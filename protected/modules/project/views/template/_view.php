<?php
/* @var $this DescriptionTemplateController */
/* @var $data DescriptionTemplate */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('category_id')); ?>:</b>
	<?php echo CHtml::encode($data->category_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('title_template')); ?>:</b>
	<?php echo CHtml::encode($data->title_template); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('title_job')); ?>:</b>
	<?php echo CHtml::encode($data->title_job); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type_job')); ?>:</b>
	<?php echo CHtml::encode($data->type_job); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description_order')); ?>:</b>
	<?php echo CHtml::encode($data->description_order); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deadline_order')); ?>:</b>
	<?php echo CHtml::encode($data->deadline_order); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('price_th')); ?>:</b>
	<?php echo CHtml::encode($data->price_th); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('uniqueness')); ?>:</b>
	<?php echo CHtml::encode($data->uniqueness); ?>
	<br />

	*/ ?>

</div>