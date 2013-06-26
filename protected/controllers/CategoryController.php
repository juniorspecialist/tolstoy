<?php

class CategoryController extends Controller
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
//			array('allow', // allow authenticated user to perform 'create' and 'update' actions
//				'actions'=>array('create','update'),
//				'users'=>array('@'),
//			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','importtxt','create','update','index','view'),
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
	public function actionCreate()
	{
		$model=new Category('create');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Category']))
		{
			$model->attributes=$_POST['Category'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

        $model->setScenario('update');

		if(isset($_POST['Category']))
		{
			$model->attributes=$_POST['Category'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $model=new Category('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Category']))
            $model->attributes=$_GET['Category'];

        $this->render('index',array(
            'model'=>$model,
        ));
	}

	/**
	 * Manages all models.
	 */
//	public function actionAdmin()
//	{
//		$model=new Category('search');
//		$model->unsetAttributes();  // clear any default values
//		if(isset($_GET['Category']))
//			$model->attributes=$_GET['Category'];
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
		$model=Category::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='category-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

    /*
     * контроллер импорта категорий из TXT-файла
     */
    public function actionImporttxt(){

        $model = new Category('import');

        if(isset($_POST['Category'])){

            $model->attributes = $_POST['Category'];

            if($model->validate()){
               // upload file to sever
               $txtFile = CUploadedFile::getInstance($model,'importtxt');
                //если файл загружали
               if(!empty($txtFile)){

                    //кодировка файла импорта
                   $codePageFile = 'win-1251';

                   $content = file_get_contents($txtFile->tempName);

                   // если нужно перекодируем текст из файла импорта
                   if($codePageFile!='utf-8'){
                       $content = iconv('windows-1251', 'utf-8//IGNORE', $content);
                   }

                   // с помощью регулярки вытаскиваем список категорий
                   preg_match_all('/<option  value="(.*?)">(.*?)<\/option>/i',$content , $info);
                   //$info[2] - список категорий полученных из парсинга регуляркой

                   foreach($info[2] as $category){

                       $category = str_replace('&nbsp;', '' , $category);

                        // проверяем категорию на уникальность, дублирование исключаем
                       $findCategory = Category::model()->findBySql("select title
                        from {{category}}
                        where title
                        like '%:category%'",
                           array(':category'=>$category));

                       // если категории нет - тогда добавим новую
                       if(empty($findCategory)){
                           $catModel = new Category('create');
                           $catModel->title = $category;
                           $catModel->save();
                       }
                   }

                   $this->redirect(array('index'));
               }
            }
        }

        $this->render('import', array('model'=>$model));
    }
}
