<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 24.12.12
 * Time: 15:27
 * To change this template use File | Settings | File Templates.
 */
class ErrorsWidget extends CWidget{

    public $model;
    public $model_id;

    public function run(){

        $model = new Errors();
        $model->model = $this->model;
        $model->model_id = $this->model_id;

        $criteria=new CDbCriteria;
        $criteria->compare('model',$this->model,true);
        $criteria->compare('model_id',$this->model_id);
        $criteria->order = 'id DESC';

        $dataProvider=new CActiveDataProvider('errors', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>10,
            ),
        ));

        $this->render('errors', array('model'=>$model,'dataProvider'=>$dataProvider,));
    }
}