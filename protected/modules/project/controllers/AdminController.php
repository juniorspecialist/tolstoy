<?php
Yii::import("application.modules.user.UserModule");
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 22.10.12
 * Time: 16:51
 * To change this template use File | Settings | File Templates.
 */
class AdminController extends Controller{
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
   				'actions'=>array('index','delete','create','update','view','uploadfile','selecttemplate',
                                'text','deletekeyword','textlist','downloadkeywords','downloadproject',
                                'agree', 'reject'
                   ),
   				//'users'=>UserModule::getAdmins(),
                'expression' => 'isset($user->role) && ($user->role==="super_administrator"||$user->role==="administrator")',
   			),
   			array('deny',  // deny all users
   				'users'=>array('*'),
   			),
   		);
   	}
    /*
     * просматриваем
     */

    /*
     * загрузка ключевых слов в TXT файл по проекту и отдача его на скачивание
     * $id - это ID проекта, по которому хотим сохранить ключевики
     */
    public function actionDownloadkeywords($id){
        //отключить профайлеры
        $this->disableProfilers();
        // находим все ключевые слова по проекту и сохраняем данные в файл
        $sql = 'SELECT {{text_data}}.import_var_value
                FROM {{text}}, {{text_data}}
                WHERE {{text}}.project_id="'.$id.'"
                    AND {{text}}.id={{text_data}}.text_id
                    AND {{text_data}}.import_var_id="'.Yii::app()->params['key_words'].'"';

        $data = Yii::app()->db->createCommand($sql)->queryAll();

        $path = Yii::getPathOfAlias('webroot.upload').DIRECTORY_SEPARATOR.'key_words.txt';

        $fp=fopen($path ,"w+");
        foreach($data as $key => $value){
            fwrite($fp,$value['import_var_value']."\r\n");
        }
        fclose($fp);

        // отдаем файл
        Yii::app()->request->sendFile(basename($path),file_get_contents($path));
    }

    /*
     * загрузка результирующего файла с обработанными текстами
     * на основании той схемы которую настроили при создании проекта
     */
    public function actionDownloadproject($id){
        $export = new Export();
        $export->project = $id;
        $path = $export->saveToCSV($export->preparation());
        //отключить профайлеры
        $this->disableProfilers();
        // отдаем файл
        Yii::app()->request->sendFile(basename($path),file_get_contents($path));
    }

    /**
   	 *выводим список ссылок на тексты по данному заданию
     * каждая ссылка это форма для написания текста, с полями и данными для этих полей
   	 */
    public function actionTextlist($id){
        // формируем запрос на выборку списка текстов с заголовками(TITLE)
        $sql = 'SELECT {{text}}.*,{{text_data}}.import_var_value as title
                FROM {{text}}, {{text_data}}
                WHERE {{text}}.id={{text_data}}.text_id
                  AND {{text}}.project_id="'.(int)$id.'"
                  AND {{text_data}}.import_var_id='.Yii::app()->params['title'].'
                GROUP BY tbl_text.id';
        //import_var_id=3 - это у нас внутренняя переменная "TITLE"

        // получаем массив данных, для отображения в таблице
        $data = Yii::app()->db->createCommand($sql)->queryAll();

        $dataProvider=new CArrayDataProvider($data, array(
            'pagination'=>array(
                'pageSize'=>count($data),
            ),
        ));

        $this->render('text_list',array(
            'dataProvider'=>$dataProvider,
        ));
    }

   	public function actionView($id){
           // модель самого проекта
           $model = $this->loadModel($id);
           // модель личных сообщений
           $msg = new Messages();
           // заполняем нужными данными, для отправки сообщения
           //$msg->author_id = Yii::app()->user->id;
           $msg->model = get_class($model);// к какой моделе подвязано сообщение
           $msg->model_id = $model->id;
           $msg->is_new = 1;
           //-------------------------------------------------------------
           // причина отклоения проекта
           $reject = new RejectProject();
           $reject->model_id = $model->id;
           $reject->model = get_class($model);// к какой моделе подвязано
           //-------------------------------------------------------------
           $this->performAjaxValidation($msg);

           if(isset($_POST['Messages'])){
               $msg->attributes=$_POST['Messages'];
               //$msg->create = time();
               if($msg->validate()){
                   $msg->save();
                   Yii::app()->user->setFlash('msg','Спасибо, ваше сообщение успешно отправлено');
                   $this->renderPartial('msg', array('msg'=>new Messages(), 'model'=>$model));
                   Yii::app()->end();
               }else{
                   $this->renderPartial('msg', array('msg'=>$msg, 'model'=>$model));
                   Yii::app()->end();
               }
           }

           $this->render('view', array('model'=>$model, 'msg'=>$msg, 'reject'=>$reject));
   	}

    /*
     * просматриваем выбранный текст по созданному заданию копирайтору
     * $id - ID текста в таблице
     */
    public function actionText($id){

        // принимаем задание или отклоняем задание(АДМИН проекта)
        if(isset($_POST['action'])){
            //задание принимает АДМИН
            if($_POST['action']=='accept_text'){
                $text = Text::model()->findByPk($_POST['project_id']);
                $text->status = Text::TEXT_ACCEPT_ADMIN;
                $text->save();
                //$this->redirect('/project/admin/textlist/', array('id'=>$model->project_id));
                echo Yii::app()->createUrl('/project/admin/textlist/', array('id'=>$text->project_id));
                Yii::app()->end();
            }
        }

        // отправили AJAX запрос на добавление нового ключевика по тексту -
        if(Yii::app()->request->isAjaxRequest){
            if(!empty($_POST['keyWordNew'])){
                $sqlInsertKeyWord = 'INSERT INTO {{text_data}}
                                    (import_var_id,import_var_value,text_id)
                                    VALUES('.intval(Yii::app()->params['key_words']).',"'.$_POST['keyWordNew'].'",'.intval($_POST['textId']).')';
                Yii::app()->db->createCommand($sqlInsertKeyWord)->execute();
            }
            // sql-запрос на выборку полей с данными для выбранного текста
            $sql = 'SELECT {{text_data}}.id, {{text_data}}.import_var_value, {{import_vars}}.title,{{text_data}}.import_var_id
                    FROM {{text_data}},{{import_vars}}
                    WHERE {{text_data}}.text_id="'.$_POST['textId'].'"
                        AND {{import_vars}}.id={{text_data}}.import_var_id';

            $data = Yii::app()->db->createCommand($sql)->queryAll();
            // получаем список ключевиков по заданию и формируем по ним таблицу
            $this->renderPartial('key_words_list', array('data'=>$data, 'textId'=>$_POST['textId']));
            Yii::app()->end();
        }

        // используем данные из модели, для проверки соотвествия - проекта - тексту и доступов по юзеру
        $model = $this->loadModelText($id);

        // sql-запрос на выборку полей с данными для выбранного текста
        $data = $model->viewText(false);

        //-------------------------------------------------------------
        // причина отклоения проекта
        $reject = new RejectProject();
        $reject->model_id = $model->id;
        $reject->model = get_class($model);// к какой моделе подвязано
        //-------------------------------------------------------------
        // отправили POST на обновление данных по данному тексту
        if(isset($_POST['Text'])){
            $model->attributes = $_POST['Text'];
            // цикл по полям, с обновлением значением полей
            foreach($_POST['ImportVarsValue'] as $i=>$val){
                // SQL запрос на обновление данных
                $sql = 'UPDATE {{text_data}} SET import_var_value="'.$val.'" WHERE id="'.(int)$i.'"';
                Yii::app()->db->createCommand($sql)->execute();
            }

            $this->redirect(array('view','id'=>$model->project_id));
        }

        $this->render('text_view',array(
            'data'=>$data,
            'model'=>$model,
            'reject'=>$reject,
        ));
    }
    /*
     * удаляем ключевое слово из текста, при редактировании админом задания
     */
    public function actionDeletekeyword($id){
        if(Yii::app()->request->isAjaxRequest){
            // удаляем выбранное ключевое слово из указанного задания и возвращаем список ключевиков обратно - ОБНОВЛЁННЫЙ список
            $sqlDelete = 'DELETE FROM {{text_data}} WHERE id="'.intval($id).'"';
            Yii::app()->db->createCommand($sqlDelete)->execute();

            // sql-запрос на выборку полей с данными для выбранного текста
            $sql = 'SELECT {{text_data}}.id, {{text_data}}.import_var_value, {{import_vars}}.title,{{text_data}}.import_var_id
                    FROM {{text_data}},{{import_vars}}
                    WHERE {{text_data}}.text_id="'.$_POST['textId'].'"
                        AND {{import_vars}}.id={{text_data}}.import_var_id';

            $data = Yii::app()->db->createCommand($sql)->queryAll();
            // получаем список ключевиков по заданию и формируем по ним таблицу
            $this->renderPartial('key_words_list', array('data'=>$data, 'textId'=>$_POST['textId']));
            Yii::app()->end();
        }
    }

   	/**
   	 * Creates a new model.
   	 * If creation is successful, the browser will be redirected to the 'view' page.
   	 */
   	public function actionCreate(){

   		$model = new Project();
        $model->setScenario('create');
        // ajax валидация данных
        //$this->performAjaxValidation($model);

   		if(isset($_POST['Project'])){

   			$model->attributes = $_POST['Project'];
            $model->uploadFile = CUploadedFile::getInstance($model,'uploadFile');

            if($model->validate()){

                // находим схему полей соотвествия для импортируемого файла
                $shems = ImportVarsShema::getListFieldsByIdShema($model->UseTemplate, ImportVarsShema::SHEMA_TYPE_TEMPLATE);
                if(!empty($model->deadline)){
                    $parse_date = explode('/',$_POST['Project']['deadline']);
                    $model->deadline = mktime(0, 0, 0,  $parse_date[1],$parse_date[0], intval($parse_date[2]));
                }
                if($model->save()){

                    // решили распарсить файл на поля соотвествий и записать значения соотвествющим полям
                    $csvFile = new CsvImport($model->uploadFile->tempName);
                    $csvFile->processFileImport($shems, $model->id);

                    // копируем поля настроек и соотвествий из выбранного шаблона и подвязываем их к проекту, что записали
                    // $model->UseTemplate - передаём, чтобы скопировать из шаблона правила проверок по полям и записать их к проекту
                    ImportVarsShema::copyTemplate($shems, $model->id, $model->UseTemplate);

                    // поле создания проекта - генерируем логин и пасс для исполнителя и создаём копирайтора и подвязываем его к проекту
                    $access = User::createCopywriter($model->id);

                    // получаем кол-во заданий по проекту и записываем его в проект
                    $countProject = Text::getCountTextByProject($model->id);

                    //допишим логин и пароль исполнителя к проекту
                    $sqlUpdate = 'UPDATE {{project}}
                                    SET performer_login="'.$access['login'].'", performer_pass="'.$access['pass'].'", count_texts="'.$countProject.'"
                                    WHERE id="'.$model->id.'"';

                    Yii::app()->db->createCommand($sqlUpdate)->execute();

                    //====подвязываем на менее загруженного РЕДАКТОРА к проекту=========================
                    $redactor_Free = User::getFreeRedactor();
                    $relationRedactor = new ProjectUsers();
                    $relationRedactor->project_id = $model->id;
                    $relationRedactor->user_id = $redactor_Free;// ID на менее загруженного РЕДАКТОРА
                    $relationRedactor->save();
                    //=========при создании проекта - записываем связь админа с проектом=================
                    $adminRelation = new ProjectUsers();
                    $adminRelation->user_id = Yii::app()->user->id;
                    $adminRelation->project_id = $model->id;
                    $adminRelation->save();

                    $this->redirect(array('view','id'=>$model->id));
                }
            }
   		}

   		$this->render('create',array(
   			'model'=>$model,
   		));
   	}
    /*
     * ajax запрос на получение данных из шаблона
     * находим шаблон, смотрим его настройки и подгружаем выбраннёе в шаблоне настройки
     */
    public function actionSelecttemplate(){

        $data = array();

        if(isset($_POST['UseTemplate'])){

            $data = DescriptionTemplate::getByID($_POST['UseTemplate']);

            if(empty($data['deadline'])){
                // установим текущую дату
                $data['deadline']= date('d/m/Y', time());
            }else{
                $data['deadline']= date('d/m/Y',$data['deadline']);
            }

        }
        // return data (JSON formatted)
        echo CJSON::encode($data);
    }
   	/**
   	 * Updates a particular model.
   	 * If update is successful, the browser will be redirected to the 'view' page.
   	 * @param integer $id the ID of the model to be updated
   	 */
   	public function actionUpdate($id)
   	{
   		$model=$this->loadModel($id);
        $model->setScenario('update');
   		//$this->performAjaxValidation($model);

   		if(isset($_POST['Project'])){

            //echo '<pre>'; print_r($_POST); die();
            // получаем список проверок
            $listCheking = CheckingImportVars::getChekingList();

            // если пустое значение кол-ва заданий в проекте, то посчитаем и запишим кол-во заданий по проекту
            if($model->count_texts==0 || empty($model->count_texts)){
                // получаем кол-во заданий по проекту и записываем его в проект
                $model->count_texts = Text::getCountTextByProject($model->id);
            }

   			$model->attributes=$_POST['Project'];

            // преобразование даты из формата 30/11/2012 в UNIXTIME
            //if(!empty($_POST['Project']['deadline']) && $_POST['Project']['deadline']!=0){
            if(!empty($model->deadline)){
                $parse_date = explode('/',$_POST['Project']['deadline']);
                $model->deadline = mktime(0, 0, 0,  $parse_date[1],$parse_date[0], intval($parse_date[2]));
            }
            //}
   			if($model->save()){
               // цикл по полям и их соотвествиям
               $cnt = 1;
               foreach($_POST['ImportVarsShemaID'] as $i=>$shemVar){

                   $shema = ImportVarsShema::model()->findByPk($i);
                   // ID внутреннй переменной
                   //$shema->import_var_id = $shemVar;

                   // галочки по полям
                   if(isset($_POST['edit'][$cnt])){
                       if($_POST['edit'][$cnt]==1){
                           $shema->edit = 1;
                       }else{
                           $shema->edit = 0;
                       }
                   }else{
                       $shema->edit = 0;
                   }

                   if(isset($_POST['visible'][$cnt])){
                       if($_POST['visible'][$cnt]==1){
                           $shema->visible = 1;
                       }else{
                           $shema->visible = 0;
                       }
                   }else{
                       $shema->visible = 0;
                   }

                   if(isset($_POST['wysiwyg'][$cnt])){
                       if($_POST['wysiwyg'][$cnt]==1){
                           $shema->wysiwyg = 1;
                       }else{
                           $shema->wysiwyg = 0;
                       }
                   }else{
                       $shema->wysiwyg = 0;
                   }

                   $shema->save();

                   // массив проверок поо полю
                   if(isset($_POST['ChekingVarID'][$i])){
                       // обновление списка проверок по каждому полю из импортируемой схемы// сохраняем список проверок по полю
                       $rowCheking = $_POST['ChekingVarID'][$i];
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
                                    AND type="2"';
                           Yii::app()->db->createCommand($sql)->execute();
                       }
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

        // удаляем все связанные данные с проектом:исполнителя,задания,тексты, список проверок по полям текста

        $sqlDeleteChekers = 'DELETE FROM {{checking_import_vars}} WHERE type="2" AND model_id="'.$id.'"';
        Yii::app()->db->createCommand($sqlDeleteChekers)->execute();

        // удаляем схему импорта по данному проекту
        $sqlShema = 'DELETE FROM {{import_vars_shema}} WHERE num_id="'.$id.'" AND shema_type="1"';
        Yii::app()->db->createCommand($sqlShema);

        // находим список текстов по проекту и удаляем подвязанные даннные по текстам
        $textList = Yii::app()->db->createCommand('SELECT id FROM {{text}} WHERE project_id="'.$id.'"')->queryAll();
        foreach($textList as $row){
            Yii::app()->db->createCommand('DELETE FROM {{text_data}} WHERE text_id="'.$row['id'].'"')->execute();
        }
        Yii::app()->db->createCommand('DELETE FROM {{text}} WHERE project_id="'.$id.'"')->execute();

        // удаляем подвязаные к проекту настройки по проверке полей
        $delFieldsCheking = 'DELETE FROM {{checking_import_vars}} WHERE type="2" AND model_id="'.$id.'"';

        // удаляем пользователей подвязанных к проекту(исполнители)
        $sqlFindCopyWriter = 'SELECT tbl_project_users.*
                            FROM `tbl_project_users` , tbl_users
                            WHERE  tbl_project_users.user_id=tbl_users.id
                                 AND tbl_users.role="'.User::ROLE_COPYWRITER.'"
                                 AND tbl_project_users.project_id="'.$id.'"';

        // сначала находим созданного копирайтора в таблице юзеров и его удаляем, а потом подвязки юзеров к проекту
        $copyWriter = Yii::app()->db->createCommand($sqlFindCopyWriter)->queryRow();


        if(!empty($copyWriter)){
            // удаляем пользователя из таблицы ЮЗЕРОВ(удаляем копирайтора)
            //Yii::app()->db->createCommand('DELETE FROM {{users}} WHERE id="'.$copyWriter['user_id'].'"')->execute();

            // БЛОКИРУЕМ КОПИРАЙТОРА
            Yii::app()->db->createCommand('UPDATE {{users}} SET status="'.User::STATUS_BANED.'" WHERE id="'.$copyWriter['user_id'].'"')->execute();
        }

        // удаляем пользователей подвязанных к проекту(исполнители) - ПОДВЯЗКИ юзеров
        Yii::app()->db->createCommand('DELETE FROM {{project_users}} WHERE project_id="'.$id.'"')->execute();

   		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
   		if(!isset($_GET['ajax']))
   			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
   	}

   	/**
   	 * Lists all models.
   	 */
   	public function actionIndex(){

        $model = new Project('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Project'])){
            $model->attributes=$_GET['Project'];
        }

        $criteria=new CDbCriteria;
        //если пользователь НЕ_СУПЕР_АДМИН, тогда выводим лишь его проекты
        if(Yii::app()->user->role!=User::ROLE_SA_ADMIN){
            // для супер_админа выводим все проекты, за всё время
            $criteria->with = array('admin');
            $criteria->together = true;
        }
        $criteria->compare('id',$model->id);
        $criteria->compare('title',$model->title,true);
        $criteria->compare('type_job',$model->type_job,true);
        $criteria->compare('description',$model->description,true);
        $criteria->compare('deadline',$model->deadline);
        $criteria->compare('price_th',$model->price_th);
        $criteria->compare('total_cost',$model->total_cost);
        $criteria->compare('total_num_char',$model->total_num_char);
        $criteria->compare('uniqueness',$model->uniqueness);
        $criteria->compare('category_id',$model->category_id);

        $dataProvider =  new CActiveDataProvider('Project', array(
           'criteria'=>$criteria,
           'pagination'=>array(
               'pageSize'=>Yii::app()->params['perPage'],
           ),
        ));

        $this->render('admin',array(
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ));
   	}

   	/**
   	 * Manages all models.
   	 */
   	public function actionAdmin()
   	{
   		$model=new Project('search');
   		$model->unsetAttributes();  // clear any default values
   		if(isset($_GET['Project']))
   			$model->attributes=$_GET['Project'];

   		$this->render('admin',array(
   			'model'=>$model,
   		));
   	}

   	/**
   	 * Returns the data model based on the primary key given in the GET variable.
   	 * If the data model is not found, an HTTP exception will be raised.
   	 * @param integer the ID of the model to be loaded
   	 */
   	public function loadModel($id){
        $criteria = '';
        // если НЕ_СУПЕР_АДМИН тогда используем условие при поиске моделе
        if(Yii::app()->user->role!=User::ROLE_SA_ADMIN){
            $criteria=new CDbCriteria;
            // для супер_админа выводим все проекты, за всё время
            $criteria->with = array('admin');
            $criteria->together = true;
        }
   		$model=Project::model()->findByPk($id, $criteria);
   		if($model===null)
   			throw new CHttpException(404,'The requested page does not exist.');
   		return $model;
   	}

    /*
     * загрузка модели для текста
     */
    public function loadModelText($id){

        $model = Text::model()->findByPk($id);

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
   		if(isset($_POST['ajax']) && $_POST['ajax']==='project-form')
   		{
   			echo CActiveForm::validate($model);
   			Yii::app()->end();
   		}
   	}

    /*
     *  принимаем проект - от лица админа проекта
     */
    public function actionAgree(){
        if(Yii::app()->request->isPostRequest){
            // проверяем может ли админ принять проект, все ли условия выполнены для этого
            if(Project::canAgreeProject($_POST['project'])){
                Project::afterChangeDataInProject($_POST['project'], Project::TASK_AGREE_ADMIN,'');
                echo 'Проект успешно принят администратором';
            }else{
                echo 'Нет возможности принять проект, задания в проекте должны быть все приняты администратором';
            }
        }
    }

    /*
       * отклонение проекта АДМИНОМ проекта
       */
    public function actionReject(){

        // причина отклоения проекта
        $reject = new RejectProject();

        if(isset($_POST['RejectProject'])){
            $reject->attributes = $_POST['RejectProject'];
            if($reject->validate()){
                //====при отклонении задания из проекта, укажим статус задания======
                if($reject->model == 'Text'){

                    // изменили статус задания
                    $text = Text::model()->findByPk($reject->model_id);
                    $text->status = Text::TEXT_NOT_ACCEPT_ADMIN;
                    $text->save();

                    // изменим статус проекта на "Задание проверяется редактором"
                    Project::afterChangeDataInProject($text->project_id, Project::TASK_CHEKING_ADMIN, $text->num);
                }
                //==при отклонении проекта, запишим это событие и изменим статус
                if($reject->model == 'Project'){
                    // изменим статус проекта, при его отклонении
                    // изменим статус проекта на "задание отправлено на доработку АДМИНОМ"
                    Project::afterChangeDataInProject($reject->model_id, Project::TASK_CANCEL_ADMIN, '');
                }

                //=====записываем ошибку в общий лог ошибок===================
                $error = new Errors();
                $error->model = $reject->model;
                $error->type = $_POST['type_error'];
                $error->error_text = $reject->msg_text;
                $error->model_id = $reject->model_id;
                $error->save();
                Yii::app()->user->setFlash('reject','Спасибо, успешно отклонили');
                $this->renderPartial('reject', array('reject'=>new RejectProject()));
                Yii::app()->end();
            }
        }
        $this->renderPartial('reject', array('reject'=>$reject), false, true);
        Yii::app()->end();
    }
}