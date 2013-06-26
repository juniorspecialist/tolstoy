<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'description-template-form',
    'enableAjaxValidation'=>true,
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
   	'clientOptions'=>array(
   		'validateOnSubmit'=>true,
           //'enctype' => 'multipart/form-data'
   	),
)); ?>

	<? echo Yii::t('msg','<p class="note">Fields with <span class="required">*</span> are required.</p>');?>

	<?php echo $form->errorSummary($model); ?>

    <?php

    if(!$model->isNewRecord){
        // проверяем если уже по данному шаблону выставленные соотвествия по полям импорта из CSV
        $data = ImportVarsShema::getListFieldsByIdShema($model->id);
        $div_checks = '<div style="width: 50%; padding-left: 543px;">';
        $div_checks.= 'Отметить все'.CHtml::CheckBox('visibleall', false, array('id'=>'visible','style'=>'margin-right:75px;','onclick'=>'js:if($(this).is(":checked")){$(".visible").attr("checked", true)}else{$(".visible").attr("checked", false)}')).'';
        $div_checks.='Отметить все'.CHtml::CheckBox('edit_all', false, array('id'=>'edit','style'=>'margin-right:30px;','onclick'=>'js:if($(this).is(":checked")){$(".edit").attr("checked", true)}else{$(".edit").attr("checked", false)}')).'';
        $div_checks.='Отметить все'.CHtml::CheckBox('wiziwig_all', false, array('id'=>'wiziwig','onclick'=>'js:if($(this).is(":checked")){$(".wiziwig").attr("checked", true)}else{$(".wiziwig").attr("checked", false)}')).'';
        $div_checks.= '</div>';
        $filels = $div_checks;
        $varsList = CHtml::listData(ImportVars::model()->findAll(), 'id', 'title');
        // обновление данных
        // получаем список проверок и формируем по ним список чекбоксов
        $chekingList = CheckingImportVars::getChekingList();

        foreach($data as $i=>$column){

            $index = $column['num'];

            $select = CHtml::dropDownList('ImportVarsShema['.$column['id'].']',$column['import_var_id'],$varsList); //'.$column['import_var_id'].'
            $hidden = CHtml::hiddenField('ImportVarsShemaID['.$column['label'].']', $index);

            // галочка для конкретного поля для редактирования, отображения и испольование редактора-ВИЗИ
            $checkBox1 = '        Отображаемый столбец'.CHtml::CheckBox('visible['.$index.']',$column['visible']==1 ? true:false, array('class'=>'visible'));
            $checkBox2 = '        Редактируемый столбец'.CHtml::CheckBox('edit['.$index.']',$column['edit']==1 ? true:false, array('class'=>'edit'));
            $checkBox3 = '        Визивиг редактор'.CHtml::CheckBox('wysiwyg['.$index.']',$column['wysiwyg']==1 ? true:false, array('class'=>'wiziwig'));


            $checkBox = $checkBox1. $checkBox2. $checkBox3;

            $forma = CheckingImportVars::getFormChekingByField(1 ,$model->id ,$column['id'],$column['label'], $chekingList);

            $row = '<div class="row"><label for="'.$column['label'].'">Столбец('.$column['label'].')</label>'.$select.$hidden.$checkBox.$forma.'</div>';

            $filels.=$row;
        }

        echo $filels;

    }else{
        $this->widget('ext.EAjaxUpload.EAjaxUpload',
          array(
            'id'=>'uploadFile',
            'config'=>array(
                'action'=>'/project/template/uploadfile',
                'allowedExtensions'=>array("csv"),//array("jpg","jpeg","gif","exe","mov" and etc...
                'sizeLimit'=>7*1024*1024,// maximum file size in bytes
                //'minSizeLimit'=>1*1024*1024,// minimum file size in bytes
                'onComplete'=>"js:function(id, fileName, responseJSON){
                      //alert('fileName='+fileName);
                      //alert(responseJSON.filename);
                      $('#uploadFile').html(responseJSON.filename);
                }",
                'messages'=>array(
                  'typeError'=>"{file} недопустимое расширение у файла. Только {extensions} разрешено.",
                  'sizeError'=>"{file} слишком большой, максимальный размер файла {sizeLimit}.",
                  //'minSizeError'=>"{file} is too small, minimum file size is {minSizeLimit}.",
                  'emptyError'=>"{file} пустой, пожалуйста выберите файл снова.",
                  //'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
                 ),
                'showMessage'=>"js:function(message){
                      alert('message='+message);
                }"
               )
           ));
    }
    ?>

	<div class="row">
		<?php echo $form->labelEx($model,'category_id'); ?>
		<?php
            echo $form->dropDownList($model,'category_id',CHtml::listData(Category::getArrayCategory(), 'id', 'title'),array('empty'=> 'Не выбрано значение'));
        ?>
		<?php echo $form->error($model,'category_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title_job'); ?>
		<?php echo $form->textField($model,'title_job',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title_job'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type_job'); ?>
		<?php echo $form->textField($model,'type_job',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'type_job'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>10, 'cols'=>50, 'style'=>'width:650px;')); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php //echo $form->labelEx($model,'deadline_order'); ?>
		<?php
        echo $form->datepickerRow($model,'deadline', array('readonly'=>true,'format'=>'dd/mm/yyyy'));
        ?>
		<?php //echo $form->error($model,'deadline_order'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'price_th'); ?>
		<?php echo $form->textField($model,'price_th').' руб.'; ?>
		<?php echo $form->error($model,'price_th'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'uniqueness'); ?>
		<?php echo $form->textField($model,'uniqueness').'%'; ?>
		<?php echo $form->error($model,'uniqueness'); ?>
	</div>

	<div class="row buttons">
		<?php
        //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save');
        $this->widget('bootstrap.widgets.TbButton',array(
        	'label' => $model->isNewRecord ? 'Добавить' : 'Сохранить',
            'buttonType'=>'submit',
        	'type' => 'action',
        	'size' => 'large'
        ));
        ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $(document).ready(function() {
//        $('a').click(function (){
//           alert('sdfsdf');
//        });
    });
</script>