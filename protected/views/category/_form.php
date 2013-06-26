<?php
/* @var $this CategoryController */
/* @var $model Category */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'category-form',
	'enableAjaxValidation'=>false,
)); ?>

    <p class="note"><?php echo Yii::t('msg','Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row buttons">
		<?php
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