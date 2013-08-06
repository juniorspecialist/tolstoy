<?php
/* @var $this ProjectController */
/* @var $model Project */

$this->breadcrumbs=array(
    Yii::t('msg','Projects')=>array('index'),
    Yii::t('msg','Create'),
);

$this->menu=array(
	array('label'=>'List Project', 'url'=>array('index')),
	array('label'=>'Manage Project', 'url'=>array('admin')),
);
?>

<h2>Создать проект</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>