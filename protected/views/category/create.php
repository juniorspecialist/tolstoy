<?php
/* @var $this CategoryController */
/* @var $model Category */

$this->breadcrumbs=array(
    Yii::t('msg','Categories')=>array('index'),
    Yii::t('msg','Create'),
);

$this->menu=array(
	array('label'=>'List Category', 'url'=>array('index')),
	array('label'=>'Manage Category', 'url'=>array('admin')),
);
?>

<h1>Создать категорию</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>