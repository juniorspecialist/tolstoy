<?php
/**
 * список текстов на написание, по выбранному заданию
 */
?>
<h2>Список заданий:</h2>
<?php
$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h4>Отправка личного сообщения:</h4>
</div>

<div class="modal-body">
    <?php $this->renderPartial('msg', array('msg'=>$msg, 'model_id'=>$model_id)); ?>
</div>
<?php $this->endWidget();
echo CHtml::link('Отправить личное сообщение',
    '#',
    array(
        'data-toggle'=>'modal',
        'data-target'=>'#myModal',
        //'style'=>'margin-left:30px;'
    )
);

 $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$dataProvider,
	//'filter'=>$model,
    'template'=>'{items}{pager}',
	'columns'=>array(
		array(
			'name' => 'id',
            'header'=>'Заголовок',
			'type'=>'raw',
			'value' => 'Text::getTitleText($data["title"],$data["num"])',
		),
        array(
            'name' => 'status',
            'header'=>'Статус',
            'type'=>'raw',
            'value' => 'Text::getStatus($data["status"])',
        ),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'visible'=>false,
        ),
	),
)); ?>