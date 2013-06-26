<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>true,
	'htmlOptions' => array('enctype'=>'multipart/form-data'),
));
?>

	<p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary(array($model,$profile)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row" id="rate">
		<?php echo $form->labelEx($model,'rate'); ?>
		<?php echo $form->textField($model,'rate',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'rate'); ?>
	</div>

<!--	<div class="row">-->
<!--		--><?php //echo $form->labelEx($model,'superuser'); ?>
		<?php //echo $form->dropDownList($model,'superuser',User::itemAlias('AdminStatus')); ?>
<!--		--><?php //echo $form->error($model,'superuser'); ?>
<!--	</div>-->


    <div class="row">
   		<?php echo $form->labelEx($model,'role'); ?>
   		<?php echo $form->dropDownList($model,'role',User::rolesList()); ?>
   		<?php echo $form->error($model,'role'); ?>
   	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status',User::itemAlias('UserStatus')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
<?php 
		$profileFields=$profile->getFields();
		if ($profileFields) {
			foreach($profileFields as $field) {
			?>
	<div class="row">
		<?php echo $form->labelEx($profile,$field->varname); ?>
		<?php 
		if ($widgetEdit = $field->widgetEdit($profile)) {
			echo $widgetEdit;
		} elseif ($field->range) {
			echo $form->dropDownList($profile,$field->varname,Profile::range($field->range));
		} elseif ($field->field_type=="TEXT") {
			echo CHtml::activeTextArea($profile,$field->varname,array('rows'=>6, 'cols'=>50));
		} else {
			echo $form->textField($profile,$field->varname,array('size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
		}
		 ?>
		<?php echo $form->error($profile,$field->varname); ?>
	</div>
			<?php
			}
		}
?>
	<div class="row buttons">
		<?php
        //echo CHtml::submitButton($model->isNewRecord ? UserModule::t('Create') : UserModule::t('Save'));
        $this->widget('bootstrap.widgets.TbButton',array(
        	'label' => $model->isNewRecord ? UserModule::t('Create') : UserModule::t('Save'),
            'buttonType'=>'submit',
        	'type' => 'action',
        	'size' => 'large'
        ));
        ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->