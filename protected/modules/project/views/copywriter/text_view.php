<?php
// всплывающие сообщения об ошибках
Yii::app()->clientScript->registerScriptFile('/js/jquery.pnotify.min.js',CClientScript::POS_END);
// AJAX проверка по полям
Yii::app()->clientScript->registerScriptFile('/js/chekers.js',CClientScript::POS_END);
?>
<div class="form">
    <h3>Информация о задании:</h3>
    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'text-form',
    'enableAjaxValidation'=>false,
    //'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions'=>array(
        //'validateOnSubmit'=>true,
    ),
)); ?>
    <div class="alert alert-block alert-error" id="errors_cheking" style="display: none;"></div>
    <?php
        $this->renderPartial('_text_form', array('data'=>$data, 'model'=>$model));
    ?>
    <div class="row">
        <?php echo $form->hiddenField($model,'project_id'); ?>
        <?php echo $form->hiddenField($model,'status'); ?>
        <?php echo $form->hiddenField($model,'id'); ?>
    </div>
    <div class="row buttons">
        <?php
            echo CHtml::link('Проверить задание', '#',array('id'=>'cheking_link', 'style'=>'margin-left:20px;'));//'href' => '',
        ?>
    </div>
    <?php $this->endWidget();?>

</div><!-- form -->
<?php $this->widget('ErrorsWidget',array('model_id'=>$model->id, 'model'=>get_class($model))); ?>
<?php $this->widget('CommentsWidget',array('model_id'=>$model->id, 'model'=>get_class($model))); ?>