<?php

/* @var $this MessagesController */
/* @var $model Messages */

$this->breadcrumbs=array(
	Yii::t('msg','Messages')=>array('index'),
    Yii::t('msg','List'),
);

$this->menu=array(
	array('label'=>'List Messages', 'url'=>array('index')),
	array('label'=>'Create Messages', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('messages-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h3>Личные сообщения:</h3>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'messages-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>,
    'template'=>'{items}{pager}',
    'enableSorting'=>false,
	'columns'=>array(
		//'id',
		//'author_id',
        array(
            'filter'=>false,
            'name'=>'author_id',
            'type'=>'raw',
            'value'=>'CHtml::link(UserModule::getUsernameByid($data->author_id),array("view","id"=>$data->id))',
        ),
		//'create',
        array(
            'name'=>'create',
            'type'=>'raw',
            'value'=>'CHtml::link($data->create,array("view","id"=>$data->id))',
            'filter'=>'',
        ),
//        array(
//            'name'=>'model',
//            'header'=>'Тип',
//            'type'=>'raw',
//            'value'=>'CHtml::link(Messages::getHeaderMsg($data->model, $data->model_id),array("view","id"=>$data->id))',
//            'filter'=>false,
//        ),
        array(
            'name'=>'msg_text',
            'type'=>'raw',
            'value'=>'CHtml::link(MyText::lenghtWords($data->msg_text,60),array("view","id"=>$data->id))',
            'filter'=>false,
        ),
//		'model',
//		'model_id',
//		'msg_text',
		array(
			'class'=>'CButtonColumn',
            'visible'=>false
		),
	),
)); ?>
