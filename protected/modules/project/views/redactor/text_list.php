<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 19.11.12
 * Time: 13:41
 * To change this template use File | Settings | File Templates.
 */
?>
<h2>Список заданий:</h2>
<?php
 $this->widget('bootstrap.widgets.TbGridView', array(
     'id'=>'user-grid',
     'dataProvider'=>$dataProvider,
     //'filter'=>$model,
     'template'=>'{items}{pager}',
     'columns'=>array(
         array(
             'name' => 'id',
             'header'=>'Заголовок',
             'type'=>'raw',
             'value' => 'CHtml::link(Text::getTitleText($data["title"],$data["num"]),array("redactor/text","id"=>$data["id"]))',
         ),
         array(
             'name' => 'status',
             'header'=>'Статус',
             'type'=>'raw',
             'value' => 'Text::getStatus($data["status"])',
         ),
         array(
             'class'=>'bootstrap.widgets.TbButtonColumn',
             'visible'=>false,
         ),
     ),
 )); ?>