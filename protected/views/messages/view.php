<?php
/* @var $this MessagesController */
/* @var $model Messages */

$this->breadcrumbs=array(
    Yii::t('msg','Messages')=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Messages', 'url'=>array('index')),
	array('label'=>'Create Messages', 'url'=>array('create')),
	array('label'=>'Update Messages', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Messages', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Messages', 'url'=>array('admin')),
);
?>

<h3>Информация о личном сообщении:</h3>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		//'id',
		//'author_id',
        array(
            'name'=>'author_id',
            'type'=>'raw',
            'value'=>UserModule::getUsernameByid($model->author_id),
        ),
		'create',
		//'model',
        array(
            'name'=>'model',
            'label'=>'Тип',
            'type'=>'raw',
            'value'=>Messages::getHeaderMsg($model->model, $model->model_id),
        ),
		//'model_id',
		'msg_text',
	),
)); ?>
