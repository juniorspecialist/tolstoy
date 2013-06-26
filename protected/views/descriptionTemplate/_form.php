<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'description-template-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'category_id'); ?>
		<?php echo $form->textField($model,'category_id'); ?>
		<?php echo $form->error($model,'category_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title_template'); ?>
		<?php echo $form->textField($model,'title_template',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title_template'); ?>
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
		<?php echo $form->labelEx($model,'description_order'); ?>
		<?php echo $form->textArea($model,'description_order',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description_order'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deadline_order'); ?>
		<?php echo $form->textField($model,'deadline_order'); ?>
		<?php echo $form->error($model,'deadline_order'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'price_th'); ?>
		<?php echo $form->textField($model,'price_th'); ?>
		<?php echo $form->error($model,'price_th'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'uniqueness'); ?>
		<?php echo $form->textField($model,'uniqueness'); ?>
		<?php echo $form->error($model,'uniqueness'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->