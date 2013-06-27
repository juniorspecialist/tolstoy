<?php
$this->widget('bootstrap.widgets.TbMenu',array(
    'type'=>'pills',
    //'type'=>null,
    //'collapse'=>true, // requires bootstrap-responsive.css
    'items'=>array(

            array('label'=>'Профиль', 'url'=>array('/site/index'),'active'=>$this->isActive('profile/profile'),),
            array('label'=>'Пароль', 'url'=>array('/user/profile/changepassword'), 'active'=>$this->isActive('profile/changepassword')),
            //array('label'=>'Пользователи', 'url'=>array('/user/admin')),
            array('label'=>'Категории', 'url'=>array('/category/'),'active'=>$this->isActive('category/index')),
            array('label'=>'Сообщения('.Messages::counNewMsg().')', 'url'=>array('/messages/'),'active'=>$this->isActive('messages/index')),
            array('label'=>'Мониторинг заданий', 'url'=>array('/project/admin/'),'active'=>$this->isActive('admin/index')),
            array('label'=>'Шаблоны', 'url'=>array('/project/template/'),'active'=>$this->isActive('template/index')),
            array('label'=>'Выход ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)

    ),
));
