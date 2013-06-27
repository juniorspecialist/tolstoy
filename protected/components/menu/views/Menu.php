<?php
$this->widget('zii.widgets.CMenu',array(
    'items'=>array(
        array('label'=>'Профиль', 'url'=>array('/site/index')),
        array('label'=>'Пользователи', 'url'=>array('/user/admin')),
        array('label'=>'Категории', 'url'=>array('/user/admin')),
        array('label'=>UserModule::t('Logout').' ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)
    ),
));