<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */

$this->breadcrumbs=array(
    Yii::t('msg','Description Templates')=>array('index'),
    Yii::t('msg','Manage'),
);

$this->menu=array(
	array('label'=>'List DescriptionTemplate', 'url'=>array('index')),
	array('label'=>'Create DescriptionTemplate', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('description-template-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2>Управление шаблонами на задание копирайтору</h2>

<?php echo CHtml::link('Создать шаблон',array('create')); ?>



<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'description-template-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'template'=>'{items}{pager}',
	'columns'=>array(
		'id',
        //'title',
        array(
            'name'=>'title',
            'type'=>'raw',
            'value'=>'CHtml::link($data->title,array("template/view","id"=>$data->id))'
        ),
        array(
            'name'=>'category_id',
            'type'=>'raw',
            'value'=>'CHtml::link($data->category->title,array("template/view","id"=>$data->id))',
            'filter'=>CHtml::listData(Category::model()->findAll(), 'id', 'title'),
        ),
		//'title_job',
		//'type_job',
        //'deadline_order',
        //'price_th',
        'uniqueness',
		//'description_order',
		/*
		*/
		array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
