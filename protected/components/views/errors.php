<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 24.12.12
 * Time: 15:30
 * To change this template use File | Settings | File Templates.
 */
// выводим список комментариев
?>
<h4>Список ошибок:</h4>
<?
$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_view_error',
    'template'=>'{items}{pager}',
    'emptyText'=>'',
));