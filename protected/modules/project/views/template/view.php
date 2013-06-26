<?php
/* @var $this DescriptionTemplateController */
/* @var $model DescriptionTemplate */

$this->breadcrumbs=array(
    Yii::t('msg','Description Templates')=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List DescriptionTemplate', 'url'=>array('index')),
	array('label'=>'Create DescriptionTemplate', 'url'=>array('create')),
	array('label'=>'Update DescriptionTemplate', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DescriptionTemplate', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DescriptionTemplate', 'url'=>array('admin')),
);
?>

<h2>Информация о шаблоне описания #<?php echo $model->id; ?></h2>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		//'category_id',
        array(
            'name'=>'category_id',
            'value'=>$model->category->title,
        ),
		'title',
		'title_job',
		'type_job',
		//'description_order',
        array(
            'name'=>'description',
            'value'=>$model->description,
            'type'=>'html'
        ),
		'deadline',
		'price_th',
		'uniqueness',
	),
)); ?>
