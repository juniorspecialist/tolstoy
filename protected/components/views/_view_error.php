<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 24.12.12
 * Time: 15:30
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="view">

    <b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
    <?php echo CHtml::encode(UserModule::getUsernameByid($data->user_id)); ?>&nbsp&nbsp

    <b><?php echo CHtml::encode($data->getAttributeLabel('create')); ?>:</b>
    <?php echo CHtml::encode($data->create); ?>

    <b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
    <?php echo CHtml::encode(Errors::getListErrors($data->type)); ?>

    <br />

    <b><?php echo CHtml::encode($data->getAttributeLabel('error_text')); ?>:</b>
    <?php echo CHtml::encode($data->error_text); ?>
    <br />

</div>