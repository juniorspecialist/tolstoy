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
<?php $this->renderPartial('_comments_form', array('model'=>$model)); ?>