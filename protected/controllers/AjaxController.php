<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 27.06.13
 * Time: 9:38
 * To change this template use File | Settings | File Templates.
 */
/*
 * контроллер для ajax запросов системы
 */
class AjaxController extends  Controller{

    public function actions(){
        return array(
            /* ajax- запрос для отправки комментария*/
            'comment'=>array(
                'class'=>'application.components.actions.CommentAction',
            ),
        );
    }

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'ajaxOnly',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
}