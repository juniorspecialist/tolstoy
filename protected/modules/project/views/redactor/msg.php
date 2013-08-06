<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 03.12.12
 * Time: 18:05
 * To change this template use File | Settings | File Templates.
 */

?>
<?php  if(Yii::app()->user->hasFlash('msg')): ?>

<div class="flash-success">
    <?php echo Yii::app()->user->getFlash('msg'); ?>
</div>
<?php endif; ?>
<?php //else: ?>
        <div class="form" id="msg">
            <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'messages-form',
            'enableAjaxValidation'=>false,
            'clientOptions'=>array(
                //'validateOnSubmit'=>true,
            ),
        )); ?>
            <div class="row">
                <?php echo $form->labelEx($msg,'recipient_id'); ?>
                <?php echo $form->dropDownList($msg,'recipient_id', Project::listRecipientFor($model->id)); ?>
                <?php echo $form->error($msg,'recipient_id'); ?>
            </div>
            <div class="row">
                <?php echo $form->labelEx($msg,'msg_text').'<br>'; ?>
                <?php echo $form->textArea($msg,'msg_text',array('rows'=>6, 'cols'=>50, 'style'=>'width:500px')); ?>
                <?php echo $form->error($msg,'msg_text'); ?>
            </div>
            <div class="modal-footer">
                <?php
                echo CHtml::ajaxSubmitButton('Отправить',
                        '#',
                        array(
                        'type' => 'POST',
                        //'enctype'=>'multipart/form-data',
                        //'dataType'=>'json',
                        //'data'=>'js:$("#upload-file-form").serialize()',
                        //'data'=>'js:jQuery(this).parents("form").serialize()',
                        'success'=>'js:function(data){ $("div#msg").html(data); }',
                        ),
                        array('class'=>'btn btn-primary', 'id'=>uniqid())
                    );
//                $this->widget('bootstrap.widgets.TbButton', array(
//                'type'=>'primary',
//                'buttonType'=>'submit',
//                //'buttonType'=>'button',
//                'label'=>'Отправить',
//                'url'=>'#',
//                'htmlOptions'=>array(
//                        //'data-dismiss'=>'modal'
//                        'class'=>'btn btn-primary',
//                    ),
//                ));
                ?>
                <?php $this->widget('bootstrap.widgets.TbButton', array(
                'label'=>'Закрыть',
                'url'=>'#',
                'htmlOptions'=>array('data-dismiss'=>'modal'),
            )); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div><!-- form -->
