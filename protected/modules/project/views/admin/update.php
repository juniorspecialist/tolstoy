<?php
/* @var $this ProjectController */
/* @var $model Project */

$this->breadcrumbs=array(
    Yii::t('msg','Projects')=>array('index'),
	$model->title=>array('view','id'=>$model->id),
    Yii::t('msg','Update'),
);

$this->menu=array(
	array('label'=>'List Project', 'url'=>array('index')),
	array('label'=>'Create Project', 'url'=>array('create')),
	array('label'=>'View Project', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Project', 'url'=>array('admin')),
);
?>

<h2>Редактировать проект<?php echo $model->id; ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>