<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 04.12.12
 * Time: 17:07
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="view">

<!--	<b>--><?php //echo CHtml::encode($data->getAttributeLabel('id')); ?><!--:</b>-->
	<?php //echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
<!--	<br />-->
<!---->
<!--	<b>--><?php //echo CHtml::encode($data->getAttributeLabel('model')); ?><!--:</b>-->
	<?php //echo CHtml::encode($data->model); ?>
<!--	<br />-->
<!---->
<!--	<b>--><?php //echo CHtml::encode($data->getAttributeLabel('model_id')); ?><!--:</b>-->
<!--	--><?php //echo CHtml::encode($data->model_id); ?>
<!--	<br />-->

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode(UserModule::getUsernameByid($data->user_id)); ?>&nbsp&nbsp
<!--	<br />-->

	<b><?php echo CHtml::encode($data->getAttributeLabel('create')); ?>:</b>
	<?php echo CHtml::encode($data->create); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('text')); ?>:</b>
	<?php echo CHtml::encode($data->text); ?>
	<br />

</div>