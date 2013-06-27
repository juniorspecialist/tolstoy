<?php
/* @var $this ProjectController */
/* @var $model Project */
$this->breadcrumbs=array(
    Yii::t('msg','Projects')=>array('index'),
	$model->title,
);
?>
<h2>Информация о задании #<?php echo $model->id; ?></h2>
<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		//'id',
		'title',
		'type_job',
		'description',
		'deadline',
		'price_th',
		'total_cost',
		'total_num_char',
		'uniqueness',
        array(
            'name'=>'category_id',
            'type'=>'raw',
            'value'=>$model->category->title,
        ),
	),
)); ?>

<?php

//формируем ссылку для принятия проекта копирайтором
if($model->status==Project::TASK_CHEKING_ADMIN){
    echo CHtml::ajaxLink(
        "Принять проект",
        Yii::app()->createUrl('project/admin/agree'),
        array( // ajaxOptions
            'type' =>'POST',
            'beforeSend' => "function(request){
         }",
            'success' => "function( data ){
            alert(data);
        }",
            'data' =>'project='.$model->id,
        ),
        array( //htmlOptions
            'href' => '#',

            'class'=>'admin_links',
            'id'=>uniqid(),
        )
    );
}

// ссылка на список текстов по проекту, со статусом -Text::TEXT_AVTO_CHECK

echo CHtml::link('Перейти к редактору текстов', '/project/admin/textlist/id/'.$model->id, array('style'=>'margin-left:20px;'));

// виджет - форма со всплывающим окном для добавления комментария к проекту
$this->widget('application.components.comment.CommentLinkWidget', array('model'=>get_class($model), 'model_id'=>$model->id));


$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h4>Отправка личного сообщения:</h4>
</div>

<div class="modal-body">
    <?php $this->renderPartial('msg', array('msg'=>$msg, 'model'=>$model)); ?>
</div>
<?php $this->endWidget();

echo CHtml::link('Отклонить проект',
    '#',
    array(
        'data-toggle'=>'modal',
        'data-target'=>'#rejectProject',
        'style'=>'margin-left:30px;'
    )
);

$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'rejectProject')); ?>
<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h4>Отклонить проект:</h4>
</div>
<div class="modal-body">
    <?php $this->renderPartial('reject', array('reject'=>$reject));?>
</div>
<?php $this->endWidget();

echo CHtml::link('Отправить личное сообщение',
    '#',
    array(
        'data-toggle'=>'modal',
        'data-target'=>'#myModal',
        'style'=>'margin-left:30px;'
    )
);
echo CHtml::link("Скачать CSV-файл с проектом",
                array("admin/downloadproject","id"=>$model->id),
                array(
                    'style'=>'margin-left:30px;'
                )

    );
echo CHtml::link("Скачать ключевые слова проекта",
    array("admin/downloadkeywords","id"=>$model->id),
    array(
        'style'=>'margin-left:30px;'
    )

);

//отображаем комментарии по проекту -
$this->widget('application.components.comment.CommentsWidget',array('model_id'=>$model->id, 'model'=>get_class($model)));

// отображаем ссылку для отклонения до момента принятия задания редактором
if(Project::getStatusInDB($model->id)<Project::TASK_AGREE_ADMIN){
    $this->widget('ErrorsWidget',array('model_id'=>$model->id, 'model'=>get_class($model)));
}
?>