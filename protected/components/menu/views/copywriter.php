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
            array('label'=>'Профиль', 'url'=>array('/site/index'),'active'=>$this->isActive('profile/profile')),
            array('label'=>'Задания', 'url'=>array('/project/copywriter/'), 'active'=>$this->isActive('copywriter/index')),
            array('label'=>'Сообщения('.Messages::counNewMsg().')', 'url'=>array('/messages/'),'active'=>$this->isActive('messages/index')),
            array('label'=>'Выход ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)
    ),
));