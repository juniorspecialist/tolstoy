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
    'template'=>'{items}{pager}',
    'enableSorting'=>false,
	'columns'=>array(
        array(
            'filter'=>false,
            'name'=>'author_id',
            'type'=>'raw',
            'value'=>'CHtml::link(UserModule::getUsernameByid($data->author_id),array("view","id"=>$data->id), array("class"=>($data->is_new ? "new_msg" :"old_msg")))',
        ),
        array(
            'name'=>'create',
            'type'=>'raw',
            'value'=>'CHtml::link($data->create,array("view","id"=>$data->id), array("class"=>($data->is_new ? "new_msg" :"old_msg")))',
            'filter'=>'',
        ),
        array(
            'name'=>'msg_text',
            'type'=>'raw',
            'value'=>'CHtml::link(MyText::lenghtWords($data->msg_text,60),array("view","id"=>$data->id), array("class"=>($data->is_new ? "new_msg" :"old_msg")))',
            'filter'=>false,
        ),
		array(
			'class'=>'CButtonColumn',
            'visible'=>false
		),
	),
)); ?>
