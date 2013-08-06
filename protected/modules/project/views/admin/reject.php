<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 24.12.12
 * Time: 11:16
 * To change this template use File | Settings | File Templates.
 */
    if(Yii::app()->user->hasFlash('reject')): ?>
        <div class="flash-success">
            <script type="text/javascript">
                location.reload();
                alert("Успешно отклонили");
            </script>
        </div>
    <?php endif; ?>
    <div class="form" id="reject">
    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'reject-project',
        'enableAjaxValidation'=>false,
        'clientOptions'=>array(
            //'validateOnSubmit'=>true,
        ),
    )); ?>

        <div class="row" >
            <?php
            echo CHtml::label('Тип ошибки:', 'error_type');
            echo CHtml::listBox('type_error',$_POST['type_error'] , Errors::getListErrors(), array('size'=>1));
            ?>
        </div>
        <div class="row">
            <?php echo $form->hiddenField($reject,'model'); ?>
            <?php echo $form->hiddenField($reject,'model_id'); ?>

            <?php echo $form->labelEx($reject,'msg_text').'<br>'; ?>
            <?php echo $form->textArea($reject,'msg_text',array('rows'=>6, 'cols'=>50, 'style'=>'width:500px')); ?>
            <?php echo $form->error($reject,'msg_text'); ?>
        </div>
        <div class="modal-footer">
            <?php
            echo CHtml::ajaxSubmitButton('Отклонить',
                '/project/admin/reject',
                array(
                    'type' => 'POST',
                    'success'=>'js:function(data){ $("div#reject").html(data); }',
                ),
                array('class'=>'btn btn-primary', 'id'=>uniqid())
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