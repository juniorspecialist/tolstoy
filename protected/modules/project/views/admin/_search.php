<?php
/* @var $this ProjectController */
/* @var $model Project */
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
		<?php echo $form->label($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'type_job'); ?>
		<?php echo $form->textField($model,'type_job',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'deadline'); ?>
		<?php echo $form->textField($model,'deadline'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'price_th'); ?>
		<?php echo $form->textField($model,'price_th'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'total_cost'); ?>
		<?php echo $form->textField($model,'total_cost'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'total_num_char'); ?>
		<?php echo $form->textField($model,'total_num_char'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'uniqueness'); ?>
		<?php echo $form->textField($model,'uniqueness'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'category_id'); ?>
		<?php echo $form->textField($model,'category_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->