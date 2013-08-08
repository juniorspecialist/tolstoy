<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 08.12.12
 * Time: 9:20
 * To change this template use File | Settings | File Templates.
 */

?>
<h3>Комментарии администратора:</h3>
<?
// выводим список комментариев
$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataAdmin,
    'itemView'=>'_view_project',
    'template'=>'{items}{pager}',
    'emptyText'=>'Нет комментариев от администратора',
));
?>
<h3>Комментарии редактора:</h3>
<?

// выводим список комментариев
$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataRedactor,
    'itemView'=>'_view_project',
    'template'=>'{items}{pager}',
    'enablePagination'=>true,
    'emptyText'=>'Нет комментариев от редактора',
));
?>
<h3>Комментарии копирайтора:</h3>
<?
// выводим список комментариев
$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataCopywriter,
    'itemView'=>'_view_project',
    'template'=>'{items}{pager}',
    'emptyText'=>'Нет комментариев от копирайтора',
));
?>
