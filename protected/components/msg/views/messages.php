<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 11.12.12
 * Time: 10:46
 * To change this template use File | Settings | File Templates.
 */

$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'messagesModal'));
?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h4>Отправить личное сообщение:</h4>
</div>

<div class="modal-body">
    <div class="form-messages">
        <div class="form">
            <?php
            $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                'id'=>'messages-form',
                'enableAjaxValidation'=>false,
            ));
            ?>
            <?php
                if(empty($model->recipient_id)){
            ?>
                <div class="row">
                    <?php echo $form->labelEx($model,'recipient_id'); ?>
                    <?php echo $form->dropDownList($model,'recipient_id', Project::listRecipientFor($model->id)); ?>
                    <?php echo $form->error($model,'recipient_id'); ?>
                </div>
            <?php
            }else{
                echo $form->hiddenField($model,'recipient_id');
            }
            ?>

            <div class="row">
                <?php echo $form->hiddenField($model,'model'); ?>
                <?php echo $form->hiddenField($model,'model_id'); ?>

                <?php echo $form->labelEx($model,'msg_text').'<br>'; ?>
                <?php echo $form->textArea($model,'msg_text',array('rows'=>6, 'cols'=>50, 'style'=>'width:500px')); ?>
                <?php echo $form->error($model,'msg_text'); ?>
            </div>
            <div class="modal-footer">

                <?php echo  $model->linktoform; ?>

                <?php $this->widget('bootstrap.widgets.TbButton', array('label'=>'Закрыть','url'=>'#','htmlOptions'=>array('data-dismiss'=>'modal'),)); ?>

            </div>
            <?php $this->endWidget(); ?>
        </div><!-- form -->
    </div>
</div>
<?php $this->endWidget();
echo CHtml::link('Отправить сообщение',
    '#',
    array(
        'data-toggle'=>'modal',
        'data-target'=>'#messagesModal',
        'style'=>'margin-left:30px;'
    )
);