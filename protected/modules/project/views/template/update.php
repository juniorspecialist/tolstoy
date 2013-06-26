<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */

$this->breadcrumbs=array(
    Yii::t('msg','Description Templates')=>array('index'),
	$model->id=>array('view','id'=>$model->id),
    Yii::t('msg','Update'),
);

$this->menu=array(
	array('label'=>'List DescriptionTemplate', 'url'=>array('index')),
	array('label'=>'Create DescriptionTemplate', 'url'=>array('create')),
	array('label'=>'View DescriptionTemplate', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DescriptionTemplate', 'url'=>array('admin')),
);
?>

<h2>Обновить шаблон описания <?php echo $model->id; ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>