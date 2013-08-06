<div class="form-upload">

<?php




    if(!empty($import->result)){

    }else{



    $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'upload-file-form',
    'action'=>'uploadfile',
    //'type'=>'inline',
    //'enableAjaxValidation' => true,
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
        $this->widget('bootstrap.widgets.TbButton',array(
        	'label' => 'Загрузить файл',
            'buttonType'=>'submit',
        	'type' => 'primary',
        	'size' => 'large'
        ));
        ?>
   	</div>
<?php $this->endWidget(); ?>
   <?php } ?>
</div><!-- form -->