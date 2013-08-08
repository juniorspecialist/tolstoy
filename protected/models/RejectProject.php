<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 24.12.12
 * Time: 11:25
 * To change this template use File | Settings | File Templates.
 */
class RejectProject extends CFormModel{

    public $msg_text;// причина отклонения проекта
    public $model;
    public $model_id;

    public function rules()
    {
        return array(
            // username and password are required
            array('msg_text, model, model_id', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'msg_text'=>'Причина отклонения',
            'model'=>'Имя модели',
            'model_id'=>'ID записи в моделе',
        );
    }
}