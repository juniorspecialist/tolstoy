<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 04.12.12
 * Time: 17:03
 * To change this template use File | Settings | File Templates.
 */
?>
<h4>Список комментариев:</h4>
<?
// выводим список комментариев
$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_view_comments',
    'template'=>'{items}{pager}',
    'emptyText'=>'Нет комментариев',
    //'pager'=>array(
    //    'class'=>'pagination',
    //)
));
?>