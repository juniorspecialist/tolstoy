<?php
/* @var $this CategoryController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
    Yii::t('msg','Categories'),
);

$this->menu=array(
	array('label'=>'Create Category', 'url'=>array('create')),
	array('label'=>'Manage Category', 'url'=>array('admin')),
);
?>

<h3>Управления категориями</h3>

<?php echo CHtml::link('Создать категорию','/category/create/'); ?>
<?php echo CHtml::link('Импортировать категории из файла','/category/importtxt/',array('style'=>'margin-left:50px;')); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'category-grid',
	'dataProvider'=>$model->search(),
    'template'=>'{items}{pager}',
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
