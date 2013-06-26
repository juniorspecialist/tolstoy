<?php
Yii::import("application.modules.user.UserModule");

/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 22.10.12
 * Time: 17:09
 * To change this template use File | Settings | File Templates.
 */
class TemplateController extends Controller{
    public $defaultAction = 'index';
    public $layout='//layouts/column2';

    private $_model;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return CMap::mergeArray(parent::filters(),array(
            'accessControl', // perform access control for CRUD operations
        ));
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('admin','delete','create','update','view', 'index','uploadfile'),
                //'users'=>UserModule::getAdmins(),
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

   		$model = new DescriptionTemplate;

        $this->performAjaxValidation($model);

        // заполнили данные формы шаблона и выставили соотвествия полей, предварительно загрузив файл импорта
   		if(isset($_POST['DescriptionTemplate']) ){//&& isset($_POST['ImportVarsShema'])

            $listCheking = CheckingImportVars::getChekingList();

   			$model->attributes = $_POST['DescriptionTemplate'];

            // преобразование даты из формата 30/11/2012 в UNIXTIME
            if(!empty($_POST['DescriptionTemplate']['deadline']) && $_POST['DescriptionTemplate']['deadline']!=0){
               $parse_date = explode('/',$_POST['DescriptionTemplate']['deadline']);
               $model->deadline = mktime(0, 0, 0,  $parse_date[1],$parse_date[0], intval($parse_date[2]));
            }


   			if($model->validate()){

               $model->save();

                // получаем список проверок
                $listCheking = CheckingImportVars::getChekingList();

               // цикл по полям и их соотвествиям
               $cnt = 1;
               foreach($_POST['ImportVarsShema'] as $i=>$shemVar){
                   $shema = new ImportVarsShema();
                   // ID внутреннй переменной
                   $shema->import_var_id = $shemVar;
                   // ID модели, к котор. подвязываем схему импорта
                   $shema->num_id = $model->id;
                   // положение по порядку
                   $shema->num = $cnt;
                   //тип схемы - для проекта или шаблона(сейчас для ШАБЛОНА)
                   $shema->shema_type = ImportVarsShema::SHEMA_TYPE_TEMPLATE;
                   // название столбца в файле импорта
                   $shema->label = $_POST['label'][$i];
                   // галочки по полям
                   if($_POST['edit'][$i]==1){// редактирование
                        $shema->edit = 1;
                   }else{
                       $shema->edit = 0;
                   }
                   // видимость поля
                   if($_POST['visible'][$i]==1){
                        $shema->visible = 1;
                   }else{
                       $shema->visible = 0;
                   }
                   // ВИЗИВИГ - редактор
                   if($_POST['wysiwyg'][$i]==1){
                        $shema->wysiwyg = 1;
                   }else{
                       $shema->wysiwyg = 0;
                   }

                   $shema->save();

                   // сохраняем список проверок по полю
                   $rowCheking = $_POST['ChekingVarID'][$i];// массив проверок поо полю
                   for($k=0;$k<count($listCheking);$k++){

                       $row = $listCheking[$k];

                       $checking_import_vars = new CheckingImportVars();
                       $checking_import_vars->type = 1;// шаблон
                       $checking_import_vars->model_id = $model->id;
                       $checking_import_vars->import_var_id = $shema->id;
                       $checking_import_vars->checked_id = $row['id'];// ID проверки
                       // если выбрали галочкой проверку
                       if($rowCheking[$k+1]==1){//$k+1 - для корректности выбора по галочкам
                           $checking_import_vars->selected = 1;
                       }else{
                           $checking_import_vars->selected = 0;
                       }
                       $checking_import_vars->save();
                   }
                   $cnt++;
               }
               $this->redirect(array('view','id'=>$model->id));
            }
   		}

   		$this->render('create',array(
   			'model'=>$model,
   		));
   	}

    /*
     * контроллер загрузки файла импорта
     */
    public function actionUploadFile(){

        Yii::import("ext.EAjaxUpload.qqFileUploader");

        $folder=Yii::getPathOfAlias('webroot.upload').'/';// folder for uploaded files

        $allowedExtensions = array("csv");//array("jpg","jpeg","gif","exe","mov" and etc...
        $sizeLimit = 7 * 1024 * 1024;// maximum file size in bytes
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload($folder);

        $result = json_encode($result);

        echo $result;// it's array
    }

   	/**
   	 * Updates a particular model.
   	 * If update is successful, the browser will be redirected to the 'view' page.
   	 * @param integer $id the ID of the model to be updated
   	 */
   	public function actionUpdate($id)
   	{
   		$model = $this->loadModel($id);

   		// Uncomment the following line if AJAX validation is needed
   		$this->performAjaxValidation($model);

   		if(isset($_POST['DescriptionTemplate'])){

   			$model->attributes = $_POST['DescriptionTemplate'];
            // преобразование даты из формата 30/11/2012 в UNIXTIME
            if(!empty($_POST['DescriptionTemplate']['deadline']) && $_POST['DescriptionTemplate']['deadline']!=0){
               $parse_date = explode('/',$_POST['DescriptionTemplate']['deadline']);
               $model->deadline = mktime(0, 0, 0,  $parse_date[1],$parse_date[0], intval($parse_date[2]));
            }

   			if($model->validate()){

               $model->save();

               // получаем список проверок
               $listCheking = CheckingImportVars::getChekingList();

               // цикл по полям и их соотвествиям
               $cnt = 1;
               foreach($_POST['ImportVarsShema'] as $i=>$shemVar){

                   $shema = ImportVarsShema::model()->findByPk($i);
                   // ID внутреннй переменной
                   $shema->import_var_id = $shemVar;

                   // галочки по полям
                   if($_POST['edit'][$cnt]==1){
                        $shema->edit = 1;
                   }else{
                       $shema->edit = 0;
                   }

                   if($_POST['visible'][$cnt]==1){
                        $shema->visible = 1;
                   }else{
                       $shema->visible = 0;
                   }

                   if($_POST['wysiwyg'][$cnt]==1){
                        $shema->wysiwyg = 1;
                   }else{
                       $shema->wysiwyg = 0;
                   }

                   $shema->save();

                   // обновление списка проверок по каждому полю из импортируемой схемы// сохраняем список проверок по полю
                   $rowCheking = $_POST['ChekingVarID'][$i];// массив проверок поо полю
                   for($k=0;$k<count($listCheking);$k++){

                       $row = $listCheking[$k];

                       // если выбрали галочкой проверку
                       if($rowCheking[$k+1]==1){//$k+1 - для корректности выбора по галочкам
                           $selected = 1;
                       }else{
                           $selected = 0;
                       }
                       $sql = 'UPDATE {{checking_import_vars}}
                                SET selected="'.$selected.'"
                                WHERE checked_id="'.$row['id'].'"
                                    AND import_var_id="'.$i.'"
                                    AND model_id="'.$model->id.'"
                                    AND type="1"';
                       Yii::app()->db->createCommand($sql)->execute();
                   }

                   $cnt++;
               }
               $this->redirect(array('view','id'=>$model->id));
            }
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
        // удаляем список проверок по шаблону по его полям соотвествия
        $sqlDeleteChekers = 'DELETE FROM {{checking_import_vars}} WHERE type="1" AND model_id="'.$id.'"';
        Yii::app()->db->createCommand($sqlDeleteChekers)->execute();

        // удаляем схему импорта по данному проекту
        $sqlShema = 'DELETE FROM {{import_vars_shema}} WHERE num_id="'.$id.'" AND shema_type="2"';
        Yii::app()->db->createCommand($sqlShema);


   		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
   		if(!isset($_GET['ajax']))
   			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
   	}

   	/**
   	 * Lists all models.
   	 */
   	public function actionIndex()
   	{
       $model=new DescriptionTemplate('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['DescriptionTemplate']))
            $model->attributes=$_GET['DescriptionTemplate'];

        $this->render('admin',array(
            'model'=>$model,
        ));
   	}

   	/**
   	 * Manages all models.
   	 */
   	public function actionAdmin()
   	{
   		$model=new DescriptionTemplate('search');
   		$model->unsetAttributes();  // clear any default values
   		if(isset($_GET['DescriptionTemplate']))
   			$model->attributes=$_GET['DescriptionTemplate'];

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
   		$model=DescriptionTemplate::model()->findByPk($id);
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
   		if(isset($_POST['ajax']) && $_POST['ajax']==='description-template-form')
   		{
   			echo CActiveForm::validate($model);
   			Yii::app()->end();
   		}
   	}
}