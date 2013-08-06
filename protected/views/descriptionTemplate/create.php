<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */

$this->breadcrumbs=array(
	'Description Templates'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List DescriptionTemplate', 'url'=>array('index')),
	array('label'=>'Manage DescriptionTemplate', 'url'=>array('admin')),
);
?>

<h1>Create DescriptionTemplate</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>