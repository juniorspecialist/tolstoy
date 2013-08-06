<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 05.12.12
 * Time: 10:52
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="form-comments">

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

        <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'comments-form',
    'enableAjaxValidation'=>false,
)); ?>

        <?php //echo $form->errorSummary($model); ?>

            <?php echo $form->hiddenField($model,'model',array('size'=>60,'maxlength'=>255)); ?>
            <?php echo $form->hiddenField($model,'model_id'); ?>

            <?php echo $form->labelEx($model,'text'); ?>
            <?php echo $form->textArea($model,'text',array('rows'=>6, 'cols'=>50, 'style'=>'width:500px')); ?>
            <?php echo $form->error($model,'text'); ?>

            <?php
            echo CHtml::ajaxSubmitButton('Отправить',
                '/comments/create',
                array(
                    'type' => 'POST',
                    'success'=>'js:function(data){ $("div.form-comments").html(data); }',
                ),
                array('class'=>'btn btn-primary')
            );
            ?>
        <?php $this->endWidget(); ?>

    </div><!-- form -->
