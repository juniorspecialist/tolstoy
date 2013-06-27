<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 26.06.13
 * Time: 17:14
 * To change this template use File | Settings | File Templates.
 */

class CommentAction extends  CAction{


    public function run()
    {
        if(!Yii::app()->request->isAjaxRequest){
            throw new CHttpException(400,'Invalid request');
        }

        $model=new Comments;
        // отправили POST запрос на добавление нового комментария
        if(isset($_POST['Comments'])){
            $model->attributes = $_POST['Comments'];
            if($model->validate()){
                $model->save();
                Yii::app()->user->setFlash('msg','Спасибо, ваш комментарий успешно отправлен');
                $model = new Comments;
            }
        }

        $this->renderPartial('application.components.views.comments', array('model'=>$model), false, true);

        Yii::app()->end();
    }
}