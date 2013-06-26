<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
<!--    <link rel="stylesheet" type="text/css" href="--><?php //echo Yii::app()->request->baseUrl; ?><!--/css/style.css" />-->

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo" style="margin: 0 auto;"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

<!--	<div id="mainmenu">-->
		<?php
    if(!Yii::app()->user->isGuest){
        $this->widget('Menu');
    }

//        $this->widget('zii.widgets.CMenu',array(
//			'items'=>array(
//				array('label'=>'Home', 'url'=>array('/site/index')),
//				array('label'=>'Login', 'url'=>array('/user/login'), 'visible'=>Yii::app()->user->isGuest),
//				array('label'=>UserModule::t('Logout').' ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)
//			),
//		));
        ?>
<!--	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php
            if(!Yii::app()->user->isGuest){
                $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
        			'links'=>$this->breadcrumbs,
        		));
            }
        ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

</div><!-- page -->
</body>
</html>
