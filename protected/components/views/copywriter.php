<?php
$this->widget('bootstrap.widgets.TbMenu',array(
    'type'=>'pills',
//    'tabs'=>array(
//        array('label'=>'Профиль', 'url'=>array('/user/profile')),
//        //array('label'=>'Login', 'url'=>array('/user/login'), 'visible'=>Yii::app()->user->isGuest),
//        array('label'=>UserModule::t('Logout').' ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'),'visible'=>!Yii::app()->user->isGuest)
//        ),
//    ));
    'type'=>'pills',
    //'type'=>null,
    //'collapse'=>true, // requires bootstrap-responsive.css
    'items'=>array(

            array('label'=>'Профиль', 'url'=>array('/site/index')),
            //array('label'=>'Пароль', 'url'=>array('/user/profile/changepassword')),
            //array('label'=>'Пользователи', 'url'=>array('/user/admin')),
            //array('label'=>'Категории', 'url'=>array('/category/')),
            array('label'=>'Задания', 'url'=>array('/project/copywriter/')),
            array('label'=>'Сообщения('.Messages::counNewMsg().')', 'url'=>array('/messages/')),
            //array('label'=>'Шаблоны', 'url'=>array('/project/template/')),
            array('label'=>'Выход ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)

    ),
));