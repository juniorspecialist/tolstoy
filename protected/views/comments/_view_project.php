<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 08.12.12
 * Time: 9:23
 * To change this template use File | Settings | File Templates.
 */
 ?>
<div class="view">

<b><?php echo CHtml::encode("Пользователь"); ?>:</b>
<?php echo CHtml::encode($data['username']); ?>&nbsp&nbsp
<!--	<br />-->

<b><?php echo "Дата"; ?>:</b>
<?php
    echo CHtml::encode(date('d-m-Y H:i:s' ,$data['create']));

    // определяем ссылку на проект для редактора и админа
    if(Yii::app()->user->role==User::ROLE_EDITOR){
        $project_link = '/project/redactor/';
    }else{
        $project_link = '/project/admin/';
    }

    //определяем на что был сделан комментарий и выводим ссылку
    if($data['model']=='Project'){// ссылка на проект
        // ссылка на проекта
        echo CHtml::link('Перейти к проекту', array($project_link.'view','id'=>$data['model_id']), array('style'=>'margin-left:20px;'));
    }
    if($data['model']=='Text'){// ссылка на задание
        // ссылка на проекта
        echo CHtml::link('Перейти к заданию', array($project_link.'text','id'=>$data['model_id']), array('style'=>'margin-left:20px;'));
    }

?>
<br />

<!--<b>--><?php //echo CHtml::encode(""); ?><!--:</b>-->
<?php echo CHtml::encode($data['text']); ?>
<!--<br />-->

</div>