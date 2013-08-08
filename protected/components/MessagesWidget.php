<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 11.12.12
 * Time: 10:24
 * To change this template use File | Settings | File Templates.
 */
class MessagesWidget extends CWidget{
    // объявим внутренние переменные класса, которые будут использоваться для записи новых данных и отображения
    public $model; // МОДЕЛЬ к которой подвязываем список сообщений
    public $model_id;// ID моделе к которой подвязаны сообщения
    public $recipient_id; //ID получателя сообщения

    public function run(){

        $model = new Messages();
        $model->model = $this->model;
        $model->model_id = $this->model_id;
        $model->is_new = 1;
        if(!empty($this->recipient_id)){
            $model->recipient_id = $this->recipient_id;
        }

        $this->render('messages', array('model'=>$model));
    }
}