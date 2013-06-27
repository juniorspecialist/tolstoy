<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 27.06.13
 * Time: 15:24
 * To change this template use File | Settings | File Templates.
 */

$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'messages-form',
    'enableAjaxValidation'=>false,
    'clientOptions'=>array(
        //'validateOnSubmit'=>true,
    ),
)); ?>
    <div class="row">
        <?php echo $form->labelEx($model,'recipient_id'); ?>
        <?php echo $form->dropDownList($model,'recipient_id', Project::listRecipientFor($model->model_id)); ?>
        <?php echo $form->error($model,'recipient_id'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'msg_text').'<br>'; ?>
        <?php echo $form->textArea($model,'msg_text',array('rows'=>6, 'cols'=>50, 'style'=>'width:500px')); ?>
        <?php echo $form->error($model,'msg_text'); ?>
    </div>
    <div class="modal-footer">
        <?php echo  $model->linktoform;?>
        <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'label'=>'Закрыть',
            'url'=>'#',
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); ?>
    </div>
<?php $this->endWidget(); ?>