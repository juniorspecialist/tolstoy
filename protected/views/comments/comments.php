<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 04.12.12
 * Time: 17:03
 * To change this template use File | Settings | File Templates.
 */
    // форма для добавления комментариев
//$this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'commentsModal'));
?>
<!--<div class="modal-header">-->
<!--    <a class="close" data-dismiss="modal">×</a>-->
<!--    <h4>Отправка комментария:</h4>-->
<!--</div>-->

<!--<div class="modal-body">-->
    <?php $this->renderPartial('_comments_form', array('msg'=>$msg, 'model'=>$model)); ?>
<!--</div>-->
<?php //$this->endWidget();
//echo CHtml::link('Оставить комментарий',
//    '#',
//    array(
//        'data-toggle'=>'modal',
//        'data-target'=>'#commentsModal',
//        'style'=>'margin-left:30px;'
//    )
//);
//// выводим список комментариев
//$this->widget('zii.widgets.CListView', array(
//    'dataProvider'=>$dataProvider,
//    'itemView'=>'_view_comments',
//    'emptyText'=>'',
//));
?>