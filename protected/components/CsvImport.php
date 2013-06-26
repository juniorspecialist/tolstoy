<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 25.10.12
 * Time: 10:58
 * To change this template use File | Settings | File Templates.
 */
/*
 * класс обработки CSV файла и считывания с него данных
 */
class CsvImport{

    public $file; // путь к файлу
    public $delimiter=';'; // разделитель строк в файле

    /*
     * функция проверки существования файла для чтения
     */
    public function existFile(){
        if(!file_exists($this->file)){
            //die('Not exist file in - '.$this->file);
            throw new Exception('Not exist file in - '.$this->file);
        }
    }

    // инициализация файла и проверка существования файла
    function __construct($file){
        $this->file = $file;
        $this->existFile();
    }

    //считываем файл в массив и возращаем массив содержимого файла
    function get2DArrayFromCsv() {
        if (($handle = fopen($this->file, "r")) !== FALSE) {
            $i = 0;
            while (($lineArray = fgetcsv($handle, 4000, $this->delimiter)) !== FALSE) {
                for ($j=0; $j<count($lineArray); $j++) {
                    $data = iconv('windows-1251','utf-8//IGNORE',$lineArray[$j]);
                    $data2DArray[$i][$j] = $data;//$lineArray[$j];
                }
                $i++;
            }
            fclose($handle);
        }
        return $data2DArray;
    }
    /*
     * получаем массив столбцов из импорта из МегаЛемы
     * на основании обработанного CSV файла
     */
    public function getColumnLema(){

        // распарсили файл на массив
        $arrayFromCsv = $this->get2DArrayFromCsv();

        //возращаем массив со структурой столбцов
        return array('0'=>$arrayFromCsv[0],'1'=>$arrayFromCsv[1]);
    }


    /*
     * обработываем файл импорта с ключевиками и заданияем и создаём
     * заданием для копирайтора с входными параметрами для создания текстов
     * $shemaImport - схема импорта файла, т.е. поля и их соотвествия внутренним переменным системы
     */
    public function processFileImport($shemaImport, $project_id){
        // для обработки файла импорта, при записи данных используем транзакции

        // сперва преобразуем файл в массив
        $arrayData = $this->get2DArrayFromCsv();

        $last_text_id = 0;
        $counterText = 1;

        //echo '<pre>'; print_r($arrayData); die();

        $can_create_text = true;

        //на основании полученной схемы преобразоваем массив из файла в соотвествие внутренним-переменным системы
        foreach($arrayData as $i=>$row){// цикл по строчкам файла импорта

            // пропускаем заголовки столбцов, для файла импорта
            if($i==0){ continue; }
            //$row - массив столбцов - 1 строка из файла импорта

            // если массив пустой, не указано в строке ни одного значения - ПРОПУСКАЕМ
            $sizeRow = CsvImport::plenumArray($row);
            // если одна строка - значит это ключевое слово, если 0 - значит пропускаем
            if($sizeRow == 0){
                $can_create_text = true;
                continue;
            }elseif($sizeRow>0 && $can_create_text==false){
                // обработка ключевого слова по тексту
                // цикл по столбцам строки из файла
                for($j=0;$j<count($row);$j++){

                    // столбцы пустые и  не нужные для импортирования - пропускаем
                    if(empty($row[$j]) || $shemaImport[$j]['title']=='Не импортировать'){ continue; }

                    // массив настроек для текущего столбца данных из файла импорта
                    $rowShema = $shemaImport[$j];

                    $column = $row[$j];

                    // массив настроек и значений для текущ. обрабатываемого поля
                    $shemaField = $shemaImport[$j];

                    // сохраняем импортируемые значения атрибутов к заданию на написание текста
                    $dataText = new TextData();
                    $dataText->import_var_id = $shemaField['import_var_id'];
                    $dataText->text_id = $last_text_id;
                    $dataText->import_var_value = $column;
                    $dataText->save();
                }
            }elseif($sizeRow>0 && $can_create_text==true){
                // создание нового текста - на задание
                // каждая строка - это новый текст, поэтому создаём текст и подвязываем к нему заполненные переменные
                $textTask = new Text();
                $textTask->project_id = $project_id;
                // указываем статус, чтобы копирайтор, не имел доступа ко всем текста из проекта
                $textTask->status = Text::TEXT_NEW_DISABLED_COPY;
                $textTask->num = $counterText;// записываем тексты по порядку, как добавляем
                $textTask->save();

                // запрет на добавление текстов
                $can_create_text = false;

                $last_text_id = $textTask->id;

                $counterText++;

                // цикл по столбцам строки из файла
                for($j=0;$j<count($row);$j++){

                    // столбцы не нужные для импортирования - пропускаем
                    // не пропускаем ПУСТЫЕ столбцы, это важно!
                    if($shemaImport[$j]['title']='' || $shemaImport[$j]['title']=='Не импортировать'){ continue; }

                    // массив настроек для текущего столбца данных из файла импорта
                    $rowShema = $shemaImport[$j];

                    $column = $row[$j];

                    // массив настроек и значений для текущ. обрабатываемого поля
                    $shemaField = $shemaImport[$j];

                    // сохраняем импортируемые значения атрибутов к заданию на написание текста
                    $dataText = new TextData();
                    $dataText->import_var_id = $shemaField['import_var_id'];
                    $dataText->text_id = $textTask->id;
                    $dataText->import_var_value = $column;
                    $dataText->save();
                }
            }

        }

        // если добавляли задания по проекту, тогда обновим статус первого задания - сделаем его досутпным для копирайтора
        if($counterText!=1){
            Yii::app()->db->createCommand('UPDATE {{text}} SET status="'.Text::TEXT_NEW.'" WHERE project_id="'.$project_id.'" AND num="1"')->execute();
        }
    }

    /*
     * функция првоерки заполнения массива
     * т.е. смотрим указанный массив заполнен ли какими-то значениями или просто он пустой
     */
    public static function 	plenumArray($array){
        // проверяем только значения, ключи не трогаем вообще
        $counter = 0;
        foreach($array as $i=>$val){
            if(!empty($val)){
                $counter++;
            }
        }

        return $counter;
    }
}