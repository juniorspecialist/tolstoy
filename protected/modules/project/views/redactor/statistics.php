<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 09.12.12
 * Time: 20:46
 * To change this template use File | Settings | File Templates.
 */
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
            'value'=>'CHtml::link($data->title,array("redactor/view","id"=>$data->id))'
        ),
        'total_num_char',
        'total_num_char_fact',
        'site',
        'upload_project_in_system',
        'output_project_to_copy',
        'deadline_copy_to_redactor',
        'deadline_redactor_to_admin',
        'accept_project_admin',
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Исполнитель проекта',
            'value'=>'Project::getLinkUserOfProjectToProfile($data->id,User::ROLE_COPYWRITER)',
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Редактор проекта',
            'value'=>'Project::getLinkUserOfProjectToProfile($data->id,User::ROLE_EDITOR)',
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Админ проекта',
            'value'=>'Project::getLinkUserOfProjectToProfile($data->id,User::ROLE_ADMIN)',
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Комментарии к проекту',
            'value'=>'Chtml::link("Просмотреть", array("/comments/project/", "id"=>$data->id))',
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Статус проекта',
            'value'=>'Project::getStatus($data->status)',
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Степень готовности проекта',
            'value'=>'Project::percentReady($data->status)',
        ),
        array(
            'name'=>'id',
            'type'=>'raw',
            'header'=>'Степень готовности текущей стадии проекта',
            'value'=>'',
        ),
        array(
            'name'=>'deadline',
            'type'=>'raw',
            'header'=>'Требуемая дата сдачи проекта',
            'value'=>'$data->deadline',
        ),
        array(
            'name'=>'id',
            'header'=>'Стоимость проекта',
            'type'=>'raw',
            'value'=>'$data->total_cost*$data->total_num_char',
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
            //'htmlOptions' => array('style'=>'width:20px;'),
            'visible'=>false,
        ),
    ),
)); ?>