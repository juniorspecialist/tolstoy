<!-- цикл по полям со значениями, кроме ключевых слов, ключевики выводим отдельно с отдельном диве, для скрывания и ссылка для скачивания ключевиков -->
<?php
$formElements = '';
// список ключевиков по заданию
$keyWords = array();
// список сведений - обработанных ключевиков
$reductions = array();

//выбираем для удобного отображения копирайтору этих элементов шаблона
$h1='';
$h2='';
$h3='';
$content1='';
$content2='';
$content3='';

$forma = '';
$div_start = '<div class="row">';
$div_end = '</div>';

// перебираем в цикле все элементы формы с полями и смотрим их настройки по полям
foreach($data as $i=>$field){
    //формируем элементы формы ключевики выводим сгруппировано
    if($field['import_var_id']==Yii::app()->params['key_words']){
        $keyWords[]=$field['import_var_value'];//.PHP_EOL
    }elseif($field['import_var_id']==Yii::app()->params['reduction']){
        if(!empty($field['import_var_value'])){
            $reductions[] = $field['import_var_value'];
        }
    }else{
        // если есть значение из POST массива, то выводим его в форме, вместо того значения, чтобы есть в БД(видимо при сохранении есть ошибки, при заполнении полей)
        if(isset($_POST['ImportVarsValue'][$field['id']])){
            $field['import_var_value'] = $_POST['ImportVarsValue'][$field['id']];
        }

        // по каждому полю делаем запрос на выборку настроек по полю
        $sqlRule = 'SELECT {{import_vars_shema}}.edit,{{import_vars_shema}}.visible,{{import_vars_shema}}.wysiwyg
                        FROM {{import_vars_shema}}
                        WHERE import_var_id="'.$field['import_var_id'].'"
                            AND shema_type="'.ImportVarsShema::SHEMA_TYPE_PROJECT.'"
                            AND num_id="'.$model->project_id.'"';
        $rule = Yii::app()->db->createCommand($sqlRule)->queryRow();

        $htmlOptions = array('extraPlugins'=>'addseparator,syntaxhighlight,geckospellchecker');
        $input_element = '';

        // смотрим видимость, доступность редактирования и ВИЗИВИГ редактор по полю
        if($rule['visible']==1){

            if($rule['edit']==0){
                $htmlOptions['disabled'] = 'disabled';
                $input_element = CHtml::textField('ImportVarsValue['.$field['id'].']',$field['import_var_value'],$htmlOptions);
            }else{
                $htmlOptions['cols']=10;
                $htmlOptions['rows']=2;
                $htmlOptions['style']='width:300px;height:40px';
                $input_element = CHtml::textArea('ImportVarsValue['.$field['id'].']',$field['import_var_value'],$htmlOptions);
            }

            if($rule['wysiwyg']==1){
                $htmlOptions['class']='redactor';
                $input_element = CHtml::textArea('ImportVarsValue['.$field['id'].']',$field['import_var_value'],$htmlOptions);
            }else{
                //$input_element = CHtml::textField('ImportVarsValue['.$field['id'].']',$field['import_var_value'],$htmlOptions);
            }

            if($field['import_var_id']==Yii::app()->params['h1']){
                $h1 = $div_start.'<label for="'.$field['title'].'">'.$field['title'].'</label>'.$input_element.$div_end;
            }elseif($field['import_var_id']==Yii::app()->params['h2']){
                $h2 = $div_start.'<label for="'.$field['title'].'">'.$field['title'].'</label>'.$input_element.$div_end;
            }elseif($field['import_var_id']==Yii::app()->params['h3']){
                $h3 = $div_start.'<label for="'.$field['title'].'">'.$field['title'].'</label>'.$input_element.$div_end;
            }elseif($field['import_var_id']==Yii::app()->params['content1']){
                $content1 = $div_start.'<label for="'.$field['title'].'">'.$field['title'].'</label>'.$input_element.$div_end;
            }elseif($field['import_var_id']==Yii::app()->params['content2']){
                $content2 = $div_start.'<label for="'.$field['title'].'">'.$field['title'].'</label>'.$input_element.$div_end;
            }elseif($field['import_var_id']==Yii::app()->params['content3']){
                $content3 = $div_start.'<label for="'.$field['title'].'">'.$field['title'].'</label>'.$input_element.$div_end;
            }else{
                $forma.=$div_start.'<label for="'.$field['title'].'">'.$field['title'].'</label>'.$input_element.$div_end;
            }
        }
    }

}

// выводим в удобном виде элементы:h1-content1-h2-content2-h3-content3
echo $h1;echo $content1;
echo $h2;echo $content2;
echo $h3;echo $content3;

// выводим на экран список ключевиков, после ключевиков выводим в удобном виде -h1-content1-h2-content2-h3-content3
if(sizeof($keyWords)>0){
    $select = CHtml::dropDownList('keywords_form','',$keyWords, array('size'=>10, 'style'=>'width:500px;'));
    echo '<div class="row"><label for="Ключевые слова">Ключевые слова</label>'.$select.'</div>';
}

// если в задании есть "СВЕДЕНИЯ" - выводим их на экран в ввиде списка
if(sizeof($reductions)>0){
    $select_reduction = CHtml::dropDownList('reductions_form','',$reductions, array('size'=>10, 'style'=>'width:500px;'));
    echo '<div class="row"><label for="сведения">Сведения</label>'.$select_reduction.'</div>';//$reductions
}

echo $forma;
?>

<div id="substratre" style="display:none;">
    <div id="loading" >
        Выполняются проверки по текстам, пожалуйста подождите
        <div id="amount" style="margin-left:210px; padding:3px;">0%</div>
        <?php
        $this->widget('zii.widgets.jui.CJuiProgressBar', array(
                'id'=>'progress',
                'value'=>0,
                'htmlOptions'=>array(
                    'style'=>'width:200px; margin:0 auto; height:20px;'
                ),
            )
        );
        ?>
    </div>
</div>