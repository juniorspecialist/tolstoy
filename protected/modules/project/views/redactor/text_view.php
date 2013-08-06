<div class="form">
    <h3>Информация о задании:</h3>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'text-form',
	'enableAjaxValidation'=>false,
    //'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions'=>array(
   		//'validateOnSubmit'=>true,
   	),
)); ?>
    <?php echo $form->errorSummary($model); ?>
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
            //<div class="alert alert-block alert-error">

            // по каждому полю делаем запрос на выборку настроек по полю
            $sqlRule = 'SELECT {{import_vars_shema}}.edit,{{import_vars_shema}}.visible,{{import_vars_shema}}.wysiwyg
                        FROM {{import_vars_shema}}
                        WHERE import_var_id="'.$field['import_var_id'].'"
                            AND shema_type="'.ImportVarsShema::SHEMA_TYPE_PROJECT.'"
                            AND num_id="'.$model->project_id.'"';
            $rule = Yii::app()->db->createCommand($sqlRule)->queryRow();

            $htmlOptions = array();
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
    $select = CHtml::dropDownList('keywords_form','',$keyWords, array('size'=>10, 'style'=>'width:500px;'));
    echo '<div class="row"><label for="Ключевые слова">Ключевые слова</label>'.$select.'</div>';

    // если в задании есть "СВЕДЕНИЯ" - выводим их на экран в ввиде списка
    if(sizeof($reductions)>0){
        $select_reduction = CHtml::dropDownList('reductions_form','',$reductions, array('size'=>10, 'style'=>'width:500px;'));
        echo '<div class="row"><label for="сведения">Сведения</label>'.$select_reduction.'</div>';//$reductions
    }

    echo $forma;
    ?>

    <div class="row">
        <?php echo $form->hiddenField($model,'project_id'); ?>
        <?php echo $form->hiddenField($model,'status'); ?>
        <?php echo $form->hiddenField($model,'id'); ?>
    </div>

    <div class="row buttons">
   		<?php //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save');
           $this->widget('bootstrap.widgets.TbButton',array(
           	'label' => $model->isNewRecord ? 'Добавить' : 'Принять задание',
            'buttonType'=>'submit',
           	'type' => 'action',
           	'size' => 'large'
           ));

            echo CHtml::link('Отклонить задание',
                '#',
                array(
                'data-toggle'=>'modal',
                'data-target'=>'#rejectProject',
                'style'=>'margin-left:50px;'
                )
            );
        ?>
   	</div>
<?php $this->endWidget(); ?>
<?php
    $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'rejectProject')); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h4>Отклонить задание:</h4>
    </div>
    <div class="modal-body">
        <?php $this->renderPartial('reject', array('reject'=>$reject));?>
    </div>
    <?php $this->endWidget();

?>
</div><!-- form -->
<?php $this->widget('CommentsWidget',array('model_id'=>$model->id, 'model'=>get_class($model))); ?>

<?php
    // отображаем ссылку для отклонения до момента принятия задания редактором
    if(Project::getStatusInDB($model->project_id)<Project::TASK_AGREE_REDACTOR){
        $this->widget('ErrorsWidget',array('model_id'=>$model->id, 'model'=>get_class($model)));
    }

?>
<script type="text/javascript">
    $(document).ready(
        function(){
            error_form();
            $('.redactor').redactor({ lang: 'ru' });
            $('#Text_status_new').live('change',function(){
                error_form();
            });
            function error_form(){
                //выбрали ошибку - выводим поле для описания ошибки
                if($('#Text_status_new').val()=='error'){
                    $('#status_new_text_row').show();
                }else{
                    $('#status_new_text_row').hide();
                }
            }
        }
    );
</script>