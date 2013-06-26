<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'category-form',
	'enableAjaxValidation'=>false,
    'htmlOptions'=>array('enctype'=>'multipart/form-data')
)); ?>
<br/>
<div class="dogovor">

    <h2>Импорт категорий из txt-файла</h2>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php //echo $form->labelEx($model,'importtxt'); ?>
        <?php echo $form->fileFieldRow($model,'importtxt',array('size'=>40)); ?>
        <?php //echo $form->error($model,'importtxt'); ?>
    </div>

</div>

<div class="row buttons">
    <?php
          $this->widget('bootstrap.widgets.TbButton',array(
          	'label' => 'Импорт',
            'buttonType'=>'submit',
          	'type' => 'primary',
          	'size' => 'large'
          ));
          ?>
</div>

<?php $this->endWidget();?>
</div><!-- form -->