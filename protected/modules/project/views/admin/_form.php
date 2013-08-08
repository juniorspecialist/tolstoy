<?php
/* @var $this ProjectController */
/* @var $model Project */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'project-form',
	'enableAjaxValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions'=>array(
   		//'validateOnSubmit'=>true,
   	),
)); ?>

    <? echo Yii::t('msg','<p class="note">Fields with <span class="required">*</span> are required.</p>');?>
	<?php echo $form->errorSummary($model); ?>
    <!--  если создание новой записи, выводим поле для файла, при обновлении, не выводим --->
    <?php if($model->isNewRecord){ ?>
        <div class="row">
            <?php
                echo $form->labelEx($model,'uploadFile');
                echo $form->fileField($model, 'uploadFile');
                echo $form->error($model,'uploadFile');
            ?>
        </div>

        <div class="row">
            <?php
                echo $form->labelEx($model,'UseTemplate');

                $tpl_list = CHtml::listData(DescriptionTemplate::model()->findAll(), 'id', 'title');
                // при выборе значения из списка, автоматически подгружаем значения из выбранного шаблона в форму
                echo $form->dropDownList($model,
                    'UseTemplate',
                    $tpl_list,
                    array(
                        'empty' => 'Не выбрано значение',
                        'ajax' => array(
                            'type'=>'POST', //request type
                            'dataType'=>'json',
                            'url'=>CController::createUrl('selecttemplate'), //url to call.
                            'success'=>'function(data) {
                                 //$("#Project_category_id").html(data.category_id);
                                 $("#Project_category_id").val(data.category_id).attr("selected",true);
                                 $("#Project_title").val(data.title);
                                 $("#Project_type_job").val(data.type_job);
                                 $("#Project_description").html(data.description);
                                 $("#Project_deadline").val(data.deadline);
                                 $("#Project_price_th").val(data.price_th);
                                 $("#Project_uniqueness").val(data.uniqueness);
                              }',
                        )
                    )
                );
            ?>
        </div>
    <?php }else{

    // проверяем если уже по данному шаблону выставленные соотвествия по полям импорта из CSV
    $data = ImportVarsShema::getListFieldsByIdShema($model->id, ImportVarsShema::SHEMA_TYPE_PROJECT);
    $filels = '';
    $varsList = CHtml::listData(ImportVars::model()->findAll(), 'id', 'title');
    // обновление данных
    // получаем список проверок и формируем по ним список чекбоксов
    $chekingList = CheckingImportVars::getChekingList();
    foreach($data as $i=>$column){

        //echo '<pre>'; print_r($data); die();

        $index = $column['num'];

        //$select = CHtml::dropDownList('ImportVarsShema['.$column['id'].']',$column['import_var_id'],$varsList); //'.$column['import_var_id'].'
        $select = CHtml::dropDownList('ImportVarsShema['.$column['id'].']',$column['import_var_id'],$varsList, array('disabled'=>'disabled'));
        $hidden = CHtml::hiddenField('ImportVarsShemaID['.$column['id'].']', $index);

        // галочка для конкретного поля для редактирования, отображения и испольование редактора-ВИЗИ
        $checkBox1 = '        Отображаемый столбец'.CHtml::CheckBox('visible['.$index.']',$column['visible']==1 ? true:false);
        $checkBox2 = '        Редактируемый столбец'.CHtml::CheckBox('edit['.$index.']',$column['edit']==1 ? true:false);
        $checkBox3 = '        Визивиг редактор'.CHtml::CheckBox('wysiwyg['.$index.']',$column['wysiwyg']==1 ? true:false);


        $checkBox = $checkBox1. $checkBox2. $checkBox3;

        $forma = CheckingImportVars::getFormChekingByField(2 ,$model->id ,$column['id'],$column['label'],$chekingList);

        $row = '<div class="row"><label for="'.$column['label'].'">Столбец('.$column['label'].')</label>'.$select.$hidden.$checkBox.$forma.'</div>';

        $filels.=$row;
    }

    echo $filels;

    } ?>
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php
            echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255));
           // echo $form->label($checkbox,'title', array('style'=>'margin-left:30px;'));
           // echo CHtml::activeCheckBox($checkbox,'title', array('style'=>'margin-left:-70px;'));

        ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

    <div class="row">
    		<?php echo $form->labelEx($model,'category_id'); ?>
    		<?php
                echo $form->dropDownList($model,'category_id',CHtml::listData(Category::getArrayCategory(), 'id', 'title'),array('empty' =>'Не выбрано значение'));
            ?>
    		<?php echo $form->error($model,'category_id'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'site'); ?>
        <?php echo $form->textField($model,'site',array('size'=>60,'maxlength'=>255));?>
        <?php echo $form->error($model,'site'); ?>
    </div>
	<div class="row">
		<?php echo $form->labelEx($model,'type_job'); ?>
		<?php
            echo $form->textField($model,'type_job',array('size'=>60,'maxlength'=>255));
        ?>
		<?php echo $form->error($model,'type_job'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php
            echo $form->textArea($model,'description',array('rows'=>10, 'cols'=>50, 'style'=>'width:650px;'));
         ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php //echo $form->labelEx($model,'deadline'); ?>
		<?php
            //echo $form->textField($model,'deadline');
            echo $form->datepickerRow($model,'deadline', array('readonly'=>true));
        ?>
		<?php //echo $form->error($model,'deadline'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'price_th'); ?>
		<?php
            echo $form->textField($model,'price_th');
        ?>
		<?php echo $form->error($model,'price_th'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'total_cost'); ?>
		<?php echo $form->textField($model,'total_cost'); ?>
		<?php echo $form->error($model,'total_cost'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'total_num_char'); ?>
		<?php echo $form->textField($model,'total_num_char'); ?>
		<?php echo $form->error($model,'total_num_char'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'uniqueness'); ?>
		<?php echo $form->textField($model,'uniqueness');?>
		<?php echo $form->error($model,'uniqueness'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'dopysk'); ?>
        <?php echo $form->textField($model,'dopysk');?>
        <?php echo $form->error($model,'dopysk'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'sickness'); ?>
        <?php echo $form->textField($model,'sickness');?>
        <?php echo $form->error($model,'sickness'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'tolerance'); ?>
        <?php echo $form->textField($model,'tolerance');?>
        <?php echo $form->error($model,'tolerance'); ?>
    </div>

    <div class="row">
        <?php echo $form->checkBox($model,'check_editor'); ?>
        <?php echo $form->label($model,'check_editor', array('style'=>'width:350px;')); ?>
    </div>
    <div class="row">
        <?php echo $form->checkBox($model,'check_copywriter'); ?>
        <?php echo $form->label($model,'check_copywriter', array('style'=>'width:350px;')); ?>
    </div>

    <div class="row buttons">
   		<?php //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save');
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