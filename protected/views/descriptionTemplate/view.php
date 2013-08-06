<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */

$this->breadcrumbs=array(
	'Description Templates'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List DescriptionTemplate', 'url'=>array('index')),
	array('label'=>'Create DescriptionTemplate', 'url'=>array('create')),
	array('label'=>'Update DescriptionTemplate', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DescriptionTemplate', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DescriptionTemplate', 'url'=>array('admin')),
);
?>

<h1>View DescriptionTemplate #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'category_id',
		'title_template',
		'title_job',
		'type_job',
		'description_order',
		'deadline_order',
		'price_th',
		'uniqueness',
	),
)); ?>
