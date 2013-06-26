<?php

class CommentsController extends Controller
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
//			array('allow',  // allow all users to perform 'index' and 'view' actions
//				'actions'=>array('index','view'),
//				'users'=>array('*'),
//			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('project'),
				//'users'=>array('admin'),
                'expression' => 'isset($user->role) && ($user->role==="super_administrator"||$user->role==="administrator"||$user->role==="editor")',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    /*
     *  страницу со всеми комментариями проекта, разделенная на три части: комментарии администртора, редактора, копирайтера.
     * Каждый комментарий содержит также ссылку на урл статьи, к которой привязан комментарий или на урл проекта
     * если комментарий привязан к проекту
     */
    public function actionProject($id){

        // находим список ID от заданий по данному проекту и используим их в поиске комментариев
        $id_texts = Yii::app()->db->createCommand('SELECT id FROM {{text}} WHERE project_id="'.$id.'"')->queryAll();

        $string = '';
        foreach($id_texts as $j=>$id_t){
            if(sizeof($id_texts)-1==$j){
                $string.=$id_t['id'];
            }else{
                $string.=$id_t['id'].',';
            }

        }
        //$string = implode(",", $id_texts);
//        echo $string;
//        die();

        $where='({{comments}}.model="Project" AND {{comments}}.model_id="'.$id.'") OR';
        $where.='({{comments}}.model="Text" AND {{comments}}.model_id IN ('.$string.'))';

        $dataAdmin ='SELECT {{comments}}.*, {{users}}.username
                     FROM {{comments}},{{users}}
                     WHERE
                        ('.$where.')
                        AND {{users}}.id={{comments}}.user_id
                        AND ({{users}}.role="'.User::ROLE_SA_ADMIN.'" OR {{users}}.role="'.User::ROLE_ADMIN.'") GROUP BY id';
        $count_dataAdmin = 'SELECT COUNT({{comments}}.id)
                         FROM {{comments}},{{users}}
                         WHERE
                            ('.$where.')
                            AND {{users}}.id={{comments}}.user_id
                            AND ({{users}}.role="'.User::ROLE_SA_ADMIN.'" OR {{users}}.role="'.User::ROLE_ADMIN.'") GROUP BY id';

        $dataRedactor = 'SELECT {{comments}}.*, {{users}}.username
                         FROM {{comments}},{{users}}
                         WHERE
                            ('.$where.')
                            AND {{users}}.id={{comments}}.user_id
                            AND {{users}}.role="'.User::ROLE_EDITOR.'" GROUP BY id';
        $count_Redactor = 'SELECT COUNT({{comments}}.id)
                         FROM {{comments}},{{users}}
                         WHERE
                            ('.$where.')
                            AND {{users}}.id={{comments}}.user_id
                            AND {{users}}.role="'.User::ROLE_EDITOR.'" GROUP BY id';

        $dataCopywriter =  'SELECT {{comments}}.*, {{users}}.username
                             FROM {{comments}},{{users}}
                             WHERE
                                ('.$where.')
                                AND {{users}}.id={{comments}}.user_id
                                AND {{users}}.role="'.User::ROLE_COPYWRITER.'" GROUP BY id';
        $count_Copywriter = 'SELECT COUNT({{comments}}.id)
                             FROM {{comments}},{{users}}
                             WHERE
                                ('.$where.')
                                AND {{users}}.id={{comments}}.user_id
                                AND {{users}}.role="'.User::ROLE_COPYWRITER.'" GROUP BY id';


        $dataAdmin=new CSqlDataProvider($dataAdmin, array(
            'pagination'=>array(
                'pageSize'=>1000,
            ),
            'totalItemCount'=>$count_dataAdmin,
        ));
        $dataRedactor=new CSqlDataProvider($dataRedactor, array(
            'pagination'=>array(
                'pageSize'=>1000,
            ),
            'totalItemCount'=>$count_Redactor,
        ));
        $dataCopywriter=new CSqlDataProvider($dataCopywriter, array(
            'pagination'=>array(
                'pageSize'=>1000,
            ),
            'totalItemCount'=>$count_Copywriter,
        ));

		$this->render('project',array(
			'dataAdmin'=>$dataAdmin,
            'dataRedactor'=>$dataRedactor,
            'dataCopywriter'=>$dataCopywriter,
		));
    }
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
//	public function actionView($id)
//	{
//		$this->render('view',array(
//			'model'=>$this->loadModel($id),
//		));
//	}
//
//	/**
//	 * Creates a new model.
//	 * If creation is successful, the browser will be redirected to the 'view' page.
//	 */
	public function actionCreate(){

		$model=new Comments;
        // отправили POST запрос на добавление нового комментария
        if(isset($_POST['Comments'])){

            $model->attributes = $_POST['Comments'];

            if($model->validate()){
                $model->save();
                Yii::app()->user->setFlash('msg','Спасибо, ваш комментарий успешно отправлен');
                $this->renderPartial('comments', array('model'=>new Comments), false, true);
                Yii::app()->end();
            }else{
                $this->renderPartial('comments', array('model'=>$model), false, true);
                Yii::app()->end();
            }
        }
	}
//
//	/**
//	 * Updates a particular model.
//	 * If update is successful, the browser will be redirected to the 'view' page.
//	 * @param integer $id the ID of the model to be updated
//	 */
//	public function actionUpdate($id)
//	{
//		$model=$this->loadModel($id);
//
//		// Uncomment the following line if AJAX validation is needed
//		// $this->performAjaxValidation($model);
//
//		if(isset($_POST['Comments']))
//		{
//			$model->attributes=$_POST['Comments'];
//			if($model->save())
//				$this->redirect(array('view','id'=>$model->id));
//		}
//
//		$this->render('update',array(
//			'model'=>$model,
//		));
//	}
//
//	/**
//	 * Deletes a particular model.
//	 * If deletion is successful, the browser will be redirected to the 'admin' page.
//	 * @param integer $id the ID of the model to be deleted
//	 */
//	public function actionDelete($id)
//	{
//		$this->loadModel($id)->delete();
//
//		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
//		if(!isset($_GET['ajax']))
//			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
//	}
//
//	/**
//	 * Lists all models.
//	 */
//	public function actionIndex()
//	{
//		$dataProvider=new CActiveDataProvider('Comments');
//		$this->render('index',array(
//			'dataProvider'=>$dataProvider,
//		));
//	}
//
//	/**
//	 * Manages all models.
//	 */
//	public function actionAdmin()
//	{
//		$model=new Comments('search');
//		$model->unsetAttributes();  // clear any default values
//		if(isset($_GET['Comments']))
//			$model->attributes=$_GET['Comments'];
//
//		$this->render('admin',array(
//			'model'=>$model,
//		));
//	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Comments::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='comments-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
