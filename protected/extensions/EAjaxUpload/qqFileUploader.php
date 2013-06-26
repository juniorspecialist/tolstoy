<?php
/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings(){
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }

        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){


            $content = file_get_contents($uploadDirectory . $filename . '.' . $ext);
            // parse CSV
            $csv = new CsvImport($uploadDirectory . $filename . '.' . $ext);
            $listColums_all = $csv->getColumnLema();
            $listColums = $listColums_all[0];// список заголовков столбцов
            $list_data = $listColums_all[1];// данные первого столбца из файла импорта


            $filels = '';
            $div_checks = '<div style="width: 50%;  padding-left: 543px;">';
            $div_checks.= 'Отметить все'.CHtml::CheckBox('visibleall', false, array('id'=>'visible','style'=>'margin-right:75px;','onclick'=>'js:if($(this).is(":checked")){$(".visible").attr("checked", true)}else{$(".visible").attr("checked", false)}')).'';
            $div_checks.='Отметить все'.CHtml::CheckBox('edit_all', false, array('id'=>'edit','style'=>'margin-right:30px;','onclick'=>'js:if($(this).is(":checked")){$(".edit").attr("checked", true)}else{$(".edit").attr("checked", false)}')).'';
            $div_checks.='Отметить все'.CHtml::CheckBox('wiziwig_all', false, array('id'=>'wiziwig','onclick'=>'js:if($(this).is(":checked")){$(".wiziwig").attr("checked", true)}else{$(".wiziwig").attr("checked", false)}')).'';
            $div_checks.= '</div>';
            $filels = $div_checks;

            $varsList = CHtml::listData(ImportVars::model()->findAll(), 'id', 'title');
            // получаем список проверок и формируем по ним список чекбоксов
            $chekingList = CheckingImportVars::getChekingList();
            foreach($listColums as $index=>$column){


                if(empty($list_data[$index])){
                    $checked = false;
                    $chekingList_ = true;
                    $visible = false;
                }else{
                    $checked = true;
                    $visible = true;
                    $chekingList_ = false;
                }


                //$shema = new ImportVarsShema();
                $select = CHtml::dropDownList('ImportVarsShema['.$index.']','',$varsList);
                $hidden = CHtml::hiddenField('label['.$index.']', $column);

                // галочка для конкретного поля для редактирования, отображения и испольование редактора-ВИЗИ
                $checkBox1 = '        Отображаемый столбец'.CHtml::CheckBox('visible['.$index.']',$visible, array('class'=>'visible'));
                $checkBox2 = '        Редактируемый столбец'.CHtml::CheckBox('edit['.$index.']',$checked, array('class'=>'edit'));
                $checkBox3 = '        Визивиг редактор'.CHtml::CheckBox('wysiwyg['.$index.']',$checked, array('class'=>'wiziwig'));


                $checkBox = $checkBox1. $checkBox2. $checkBox3;
                $forma = CheckingImportVars::getFormChekingByField('', '', $index, $column, $chekingList, $chekingList_);

                $row = '<div class="row"><label for="'.$column.'">Столбец('.$column.')</label>'.$select.$hidden.$checkBox.$forma.'</div>';

                $filels.=$row;
            }

            // удаляем загруженный файл с сервера
            unlink($uploadDirectory . $filename . '.' . $ext);

            return array('success'=>true,'filename'=>$filels);
        } else {

            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }

    }
}
