<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 11.12.12
 * Time: 13:16
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="form-messages">
    <?php  if(Yii::app()->user->hasFlash('msg')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('msg'); ?>
        <script type="text/javascript">
            //window.location="";
            function load()
            {
                setTimeout("refresh()", 2000)
            }
            function refresh(){
                window.location.reload()
                load()
            }
            load();
        </script>
    </div>
    <?php endif; ?>
    <div class="form">
        <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'messages-form',
        'enableAjaxValidation'=>false,
    )); ?>
        <?php if(empty($model->recipient_id) && Yii::app()->user->hasFlash('msg')){ ?>
        <div class="row">
            <?php echo $form->labelEx($model,'recipient_id'); ?>
            <?php echo $form->dropDownList($model,'recipient_id', Project::listRecipientFor($model->id)); ?>
            <?php echo $form->error($model,'recipient_id'); ?>
        </div>
        <?php }else{
        echo $form->hiddenField($model,'recipient_id');
    } ?>

        <div class="row">
            <?php echo $form->hiddenField($model,'model'); ?>
            <?php echo $form->hiddenField($model,'model_id'); ?>

            <?php echo $form->labelEx($model,'msg_text').'<br>'; ?>
            <?php echo $form->textArea($model,'msg_text',array('rows'=>6, 'cols'=>50, 'style'=>'width:500px')); ?>
            <?php echo $form->error($model,'msg_text'); ?>
        </div>
        <div class="modal-footer">
            <?php
            echo CHtml::ajaxSubmitButton('Отправить',
                '/messages/create/',
                array(
                    'type' => 'POST',
                    'success'=>'js:function(data){ $("div.form-messages").html(data); }',
                ),
                array('class'=>'btn btn-primary')
            );
            ?>
            <?php $this->widget('bootstrap.widgets.TbButton', array(
            'label'=>'Закрыть',
            'url'=>'#',
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div><!-- form -->
</div>