<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 20.11.12
 * Time: 18:17
 * To change this template use File | Settings | File Templates.
 */
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>uniqid(),
    'options'=>array(
        'title'=>'Modal Dialog',
        'width'=>400,
        'height'=>200,
        'autoOpen'=>false,
        'resizable'=>false,
        'modal'=>true,
        'overlay'=>array(
            'backgroundColor'=>'#000',
            'opacity'=>'0.5'
        ),
        'buttons'=>array(
            'OK'=>'js:function(){alert("OK");}',
            'Cancel'=>'js:function(){$(this).dialog("close");}',
        ),
    ),
));
echo 'Modal dialog content here ';
$this->endWidget('zii.widgets.jui.CJuiDialog');