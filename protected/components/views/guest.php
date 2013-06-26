<?php
        $this->widget('zii.widgets.CMenu',array(
            'type'=>'pills',
			'tabs'=>array(
				array('label'=>'Home', 'url'=>array('/site/index')),
				array('label'=>'Login', 'url'=>array('/user/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>UserModule::t('Logout').' ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		));