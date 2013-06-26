<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */

$this->breadcrumbs=array(
    Yii::t('msg','Description Templates')=>array('index'),
	Yii::t('msg','Create'),
);

$this->menu=array(
	array('label'=>'List DescriptionTemplate', 'url'=>array('index')),
	array('label'=>'Manage DescriptionTemplate', 'url'=>array('admin')),
);
?>

<h2>Добавить шаблон описания</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>