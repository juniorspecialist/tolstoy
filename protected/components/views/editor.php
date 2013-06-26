<?php
$this->widget('zii.widgets.CMenu',array(
    'type'=>'pills',
    'items'=>array(
        //'class' => 'bootstrap.widgets.TbMenu',
        array('label'=>'Профиль', 'url'=>array('/site/index')),
        array('label'=>'Сообщения('.Messages::counNewMsg().')', 'url'=>array('/messages/')),
        array('label'=>'Проекты', 'items'=> array(
            array('label'=>'Всего проектов обработано', 'url'=>'/project/redactor/all_make/'),//('.Project::getCountProjectOfRedactorMenu(1).')
            array('label'=>'Проектов в проверке', 'url'=>'/project/redactor/check/'),//('.Project::getCountProjectOfRedactorMenu(2).')
            //array('label'=>'Проектов на проверке у администратора('.Project::getCountProjectOfRedactorMenu(3).')', 'url'=>'/project/redactor/check_admin/'),
        )),
        //array('label'=>'Статистика редактора', 'url'=>array('/project/redactor/statistics')),
        array('label'=>'Выход ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)
    ),
));