<?php
Yii::import("application.modules.user.UserModule");
class MessagesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
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
				'actions'=>array('create','view','index'),//,'update'
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'project'),
				//'users'=>array('admin'),
                'expression' => 'isset($user->role) && ($user->role==="super_administrator"||$user->role==="administrator")',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){

		$model = new Messages;

		if(isset($_POST['Messages'])){

			$model->attributes=$_POST['Messages'];
            if($model->validate()){
                $model->save();
                Yii::app()->user->setFlash('msg','Спасибо, ваше сообщение успешно отправлено');
                $this->renderPartial('messages', array('model'=>new Messages));
                Yii::app()->end();
            }else{
                $this->renderPartial('messages', array('model'=>$model), false, true);
                Yii::app()->end();
            }
		}

//		$this->render('create',array(
//			'model'=>$model,
//		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model=new Messages('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Messages']))
			$model->attributes=$_GET['Messages'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Messages::model()->findByPk($id,'recipient_id=:recipient_id', array(':recipient_id'=>Yii::app()->user->id));

		if($model===null){
            throw new CHttpException(404,'The requested page does not exist.');
        }else{
            // если новое сообщение, сделаем его прочитанным
            if($model->is_new==1){
                Yii::app()->db->createCommand('UPDATE {{messages}} SET is_new="0" WHERE id="'.$model->id.'"')->execute();
            }
        }

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='messages-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
