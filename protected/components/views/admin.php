<?php
$this->widget('bootstrap.widgets.TbMenu',array(
    'type'=>'pills',
    //'type'=>null,
    //'collapse'=>true, // requires bootstrap-responsive.css
    'items'=>array(

            array('label'=>'Профиль', 'url'=>array('/site/index')),
            array('label'=>'Пароль', 'url'=>array('/user/profile/changepassword')),
            //array('label'=>'Пользователи', 'url'=>array('/user/admin')),
            array('label'=>'Категории', 'url'=>array('/category/')),
            array('label'=>'Сообщения('.Messages::counNewMsg().')', 'url'=>array('/messages/')),
            array('label'=>'Мониторинг заданий', 'url'=>array('/project/admin/')),
            array('label'=>'Шаблоны', 'url'=>array('/project/template/')),
            array('label'=>'Выход ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)

    ),
));
