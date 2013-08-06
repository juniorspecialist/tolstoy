<?php
/* @var $this ProjectController */
/* @var $model Project */
$this->breadcrumbs=array(
    Yii::t('msg','Projects')=>array(''),
    Yii::t('msg','Manage'),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('project-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2>Управление проектами</h2>

<?php
//echo CHtml::link('Advanced Search','#',array('class'=>'search-button'));

?>

<?php echo CHtml::link('Создать проект',array('create')); ?>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'project-grid',
	'dataProvider'=>$dataProvider,
    'template'=>'{items}{pager}',
	'filter'=>$model,
	'columns'=>array(
		//'id',
		//'title',
        array(
            'name'=>'title',
            'type'=>'raw',
            'value'=>'CHtml::link($data->title,array("admin/view","id"=>$data->id))',
            'filter' => false,
        ),
        array(
            'name'=>'zipArchive',
            'type'=>'raw',
            'value'=>'CHtml::link("Скачать",array("admin/downloadproject","id"=>$data->id))',
            'filter' => false,
        ),
        array(
            'name'=>'keyWordsProject',
            'type'=>'raw',
            'value'=>'CHtml::link("Скачать",array("admin/downloadkeywords","id"=>$data->id))',
            'filter' => false,
        ),

        array('name'=>'total_num_char','value'=>'$data->total_num_char','filter' => false,),
        array('name'=>'total_num_char_fact','value'=>'$data->total_num_char_fact','filter' => false,),
        array('name'=>'count_texts','value'=>'$data->count_texts','filter' => false,),

        array('name'=>'site','value'=>'$data->site','filter' => false,),
        array('name'=>'upload_project_in_system','value'=>'$data->upload_project_in_system','filter' => false,),
        array('name'=>'output_project_to_copy','value'=>'$data->output_project_to_copy','filter' => false,),
        array('name'=>'deadline_copy_to_redactor','value'=>'$data->deadline_copy_to_redactor','filter' => false,),
        array('name'=>'deadline_redactor_to_admin','value'=>'$data->deadline_redactor_to_admin','filter' => false,),
        array('name'=>'accept_project_admin','value'=>'$data->accept_project_admin','filter' => false,),

//        'total_num_char',
//        'total_num_char_fact',
//        'site',
//
//        'upload_project_in_system',
//        'output_project_to_copy',
//        'deadline_copy_to_redactor',
//        'deadline_redactor_to_admin',
//        'accept_project_admin',
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Исполнитель проекта',
            'value'=>'Project::getLinkUserOfProjectToProfile($data->id,User::ROLE_COPYWRITER)',
            'filter' => false,
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Редактор проекта',
            'value'=>'Project::getLinkUserOfProjectToProfile($data->id,User::ROLE_EDITOR)',
            'filter' => false,
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Админ проекта',
            'value'=>'Project::getLinkUserOfProjectToProfile($data->id,User::ROLE_ADMIN)',
            'filter' => false,
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Комментарии к проекту',
            'value'=>'Chtml::link("Просмотреть", array("/comments/project/", "id"=>$data->id))',
            'filter' => false,
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Статус проекта',
            'value'=>'Project::getStatus($data->status)',
            'filter' => false,
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Степень готовности проекта',
            'value'=>'Project::percentReady($data->status)',
            'filter' => false,
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Степень готовности текущей стадии проекта',
            'value'=>'',
            'filter' => false,
        ),
        array(
            'name'=>'deadline',
            'type'=>'raw',
            'header'=>'Требуемая дата сдачи проекта',
            'value'=>'$data->deadline',
            'filter' => false,
        ),
        array(
            'name'=>'id',
            'header'=>'Стоимость проекта',
            'type'=>'raw',
            'value'=>'$data->total_cost*$data->total_num_char',
            'filter' => false,
        ),
        array(
            'name'=>'performer_login',
            'value'=>'$data->performer_login',
            'filter' => false,
        ),
        array(
            'name'=>'performer_pass',
            'value'=>'$data->performer_pass',
            'filter' => false,
        ),
//        'performer_login',
//        'performer_pass',
		/*
		'total_cost',
		'total_num_char',
		'uniqueness',
		'category_id',
		*/
		array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'htmlOptions' => array('style'=>'width:20px;'),
            'template'=>'{update}'
		),
	),
)); ?>
