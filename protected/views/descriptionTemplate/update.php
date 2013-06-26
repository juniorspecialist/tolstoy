<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */

$this->breadcrumbs=array(
	'Description Templates'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List DescriptionTemplate', 'url'=>array('index')),
	array('label'=>'Create DescriptionTemplate', 'url'=>array('create')),
	array('label'=>'View DescriptionTemplate', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DescriptionTemplate', 'url'=>array('admin')),
);
?>

<h1>Update DescriptionTemplate <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>