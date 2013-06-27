<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 27.06.13
 * Time: 10:22
 * To change this template use File | Settings | File Templates.
 */
// форма для добавления комментариев
$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'commentsModal'));
?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h4>Отправка комментария:</h4>
</div>

<div class="modal-body">
    <div class="form-comments">

        <?php  if(Yii::app()->user->hasFlash('msg')): ?>
            <div class="flash-success">
                <?php echo Yii::app()->user->getFlash('msg'); ?>
            </div>
        <?php endif; ?>

        <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'comments-form',
            'enableAjaxValidation'=>false,
        )); ?>

        <?php //echo $form->errorSummary($model); ?>
        <?php echo $form->hiddenField($model,'model',array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->hiddenField($model,'model_id'); ?>

        <div class="row1">
            <?php echo $form->labelEx($model,'text'); ?>
            <?php echo $form->textArea($model,'text',array('rows'=>6, 'cols'=>50, 'style'=>'width:500px')); ?>
            <?php echo $form->error($model,'text'); ?>
        </div>

        <?php echo $model->getlinkToForm('#comments-form');?>

        <?php $this->endWidget(); ?>
    </div><!-- form -->
</div>
<?php
$this->endWidget();
    echo CHtml::link('Оставить комментарий',
        '#',
        array(
            'data-toggle'=>'modal',
            'data-target'=>'#commentsModal',
            'style'=>'margin-left:30px;'
        )
    );
?>