<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'category_id'); ?>
		<?php echo $form->textField($model,'category_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'title_template'); ?>
		<?php echo $form->textField($model,'title_template',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'title_job'); ?>
		<?php echo $form->textField($model,'title_job',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'type_job'); ?>
		<?php echo $form->textField($model,'type_job',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'description_order'); ?>
		<?php echo $form->textArea($model,'description_order',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'deadline_order'); ?>
		<?php echo $form->textField($model,'deadline_order'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'price_th'); ?>
		<?php echo $form->textField($model,'price_th'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'uniqueness'); ?>
		<?php echo $form->textField($model,'uniqueness'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->