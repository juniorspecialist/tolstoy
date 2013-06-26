<div class="form-upload">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'upload-file-form',
    'action'=>'uploadfile',
    //'type'=>'inline',
    'enableAjaxValidation' => true,
    //'enableClientValidation' => true,
    //'clientOptions' => array('validateOnSubmit' => true),
    'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>

    <?php echo $form->errorSummary($import); ?>

    <div class="row">
        <?php echo $form->fileFieldRow($import,'upload_file'); ?>
    </div>

    <div class="row buttons">
   		<?php
            echo CHtml::ajaxSubmitButton('Загрузить файл',
                'uploadfile',
                array(
                    'type' => 'POST',
                    //'enctype'=>'multipart/form-data',
                    //'dataType'=>'json',
                    //'data'=>'js:$("#upload-file-form").serialize()',
                    //'data'=>'js:jQuery(this).parents("form").serialize()',
                    'success'=>'js:function(data){ $("div.form-upload").html(data); }',
                ),
                array('type' => 'button','class'=>'btn btn-primary btn-large')
            );
        ?>
   	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->