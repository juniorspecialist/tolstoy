<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 19.11.12
 * Time: 12:16
 * To change this template use File | Settings | File Templates.
 */
$this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'project-grid',
    'dataProvider'=>$dataProvider,
    'template'=>'{items}{pager}',
    //'filter'=>false,
    'columns'=>array(
        array(
            'name'=>'title',
            'header'=>'Анонс',
            'type'=>'raw',
            'value'=>'CHtml::link(MyText::lenghtWords($data["description"]),array("redactor/view/","id"=>$data["id"]))'
        ),
        array(
            'name'=>'id',
            'header'=>'Копирайтор',
            'type'=>'raw',
            'value'=>'CHtml::link(Project::getLinkUserOfProjectToProfile($data["id"],User::ROLE_EDITOR),array("redactor/view/","id"=>$data["id"]))'
        ),
        array(
            'name'=>'total_num_char',
            'header'=>'Кол-во знаков',
            'type'=>'raw',
            'value'=>'CHtml::link($data["total_num_char"],array("redactor/view/","id"=>$data["id"]))'
        ),
        array(
            'name'=>'id',
            'header'=>'Кол-во текстов',
            'type'=>'raw',
            'value'=>'CHtml::link(Text::getCountTextByProject($data["id"]),array("redactor/view/","id"=>$data["id"]))'
        ),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{view}',
            'visible'=>false,
        ),
    ),
)); ?>