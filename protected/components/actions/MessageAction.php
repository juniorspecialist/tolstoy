<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 27.06.13
 * Time: 17:06
 * To change this template use File | Settings | File Templates.
 */

class MessageAction extends CAction {

    public function run()
    {
        if(!Yii::app()->request->isAjaxRequest){
            throw new CHttpException(400,'Invalid request');
        }

        $model = new Messages();

        // отправили POST запрос на добавление нового сообщения
        if(isset($_POST['Messages'])){
            $model->attributes = $_POST['Messages'];
            if($model->validate()){
                $model->save();
                //Yii::app()->user->setFlash('msg','Спасибо, ваш комментарий успешно отправлен');
                $model = new Messages();
            }
        }

        $this->getController()->renderPartial('application.components.msg.views._form_massage', array('model'=>$model));

        Yii::app()->end();
    }
}