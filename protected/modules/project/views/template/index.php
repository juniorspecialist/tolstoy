<?php
/* @var $this DescriptionTemplateController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Description Templates',
);

$this->menu=array(
	array('label'=>'Create DescriptionTemplate', 'url'=>array('create')),
	array('label'=>'Manage DescriptionTemplate', 'url'=>array('admin')),
);
?>

<h1>Description Templates</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
