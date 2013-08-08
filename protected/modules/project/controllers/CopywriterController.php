<?php
Yii::import("application.modules.user.UserModule");
class CopywriterController extends  Controller{
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
   				'actions'=>array('index','update','downloadfile','text', 'check', 'disabledfields'),
   				//'users'=>array('@'), //UserModule::getAdmins(),
                'expression' => 'isset($user->role) && ($user->role==="copywriter")',
   			),
   			array('deny',  // deny all users
   				'users'=>array('*'),
   			),
   		);
   	}

    // выводим список текстов для исполнителя
    public function actionIndex(){

        // выясняем к какому проекту подвязан текущий пользователь-копирайтер
        $projectUser = ProjectUsers::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));

        // формируем запрос на выборку списка текстов с заголовками(TITLE)
        $sql = 'SELECT {{text}}.*,{{text_data}}.import_var_value as title
                FROM {{text}}, {{text_data}}
                WHERE {{text}}.id={{text_data}}.text_id
                  AND {{text}}.project_id="'.$projectUser->project_id.'"
                  AND {{text_data}}.import_var_id='.Yii::app()->params['title'].'
                GROUP BY tbl_text.id';
           //import_var_id=3 - это у нас внутренняя переменная "TITLE"

        // получаем массив данных, для отображения в таблице
        $data = Yii::app()->db->createCommand($sql)->queryAll();

        $result = array();
        // перебираем массив для формирования ссылок на задания, чтобы копирайтер последовательно их мог выполнять
        // т.е.выполнил 2 задания перешёл на третье, а не любое на выбор
          foreach($data as $i=>$row){
            // если статус у задания НОВЫЙ, не_принят_редактором - т.е. доступный для копирайтора, тогда показываем ему ссылку на задание
            if($row['status']==Text::TEXT_NEW || $row['status']==Text::TEXT_NOT_ACCEPT_EDITOR){
                if(empty($row['title'])){
                    $row['title'] = CHtml::link(Text::getTitleText($row["title"],$row["num"]),array("copywriter/text","id"=>$row["id"]));
                }else{
                    $row['title'] = CHtml::link($row["title"],array("copywriter/text","id"=>$row["id"]));
                }
            }

            $result[] = $row;
        }

        $dataProvider=new CArrayDataProvider($result, array(
            'pagination'=>array(
                'pageSize'=>count($data),
            ),
        ));
        // модель личных сообщений
        $msg = new Messages();
        // заполняем нужными данными, для отправки сообщения
        $msg->author_id = Yii::app()->user->id;
        $msg->model = 'Project';// к какой моделе подвязано сообщение
        $msg->model_id = $projectUser->project_id;
        $msg->is_new = 1;
        $this->performAjaxValidation($msg);

        if(isset($_POST['Messages'])){
            $msg->attributes=$_POST['Messages'];
            $msg->create = time();
            if($msg->validate()){
                $msg->save();
                Yii::app()->user->setFlash('msg','Спасибо, ваше сообщение успешно отправлено');
                $this->renderPartial('msg', array('dataProvider'=>$dataProvider,'msg'=>new Messages(), 'model_id'=>$projectUser->project_id));
                Yii::app()->end();
            }else{
                $this->renderPartial('msg', array('dataProvider'=>$dataProvider,'msg'=>$msg, 'model_id'=>$projectUser->project_id));
                Yii::app()->end();
            }
        }
        $this->render('text_list',array(
            'dataProvider'=>$dataProvider,
            'model_id'=>$projectUser->project_id,
            'msg'=>$msg,
        ));
    }

    /*
     * просматриваем выбранный текст по созданному заданию копирайтору
     * $id - ID текста в таблице
     */
    public function actionText($id){
        //скрипты для инициализации ВИЗИВИГ-редактора
        Yii::app()->bootstrap->registerAssetCss('redactor.css');
      	Yii::app()->bootstrap->registerAssetJs('redactor.min.js');
        Yii::app()->bootstrap->registerAssetJs('locales/redactor.ru.js');

        Yii::app()->clientScript->registerScript('loading', '
            $("#loading").bind("ajaxSend", function(){
                $(this).show();
            }).bind("ajaxComplete", function(){
                //$(this).hide();
            });
        ', CClientScript::POS_READY);

        // используем данные из модели, для проверки соотвествия - проекта - тексту и доступов по юзеру
        $model = $this->loadModel($id);
        // sql-запрос на выборку полей с данными для выбранного текста
        $sql = 'SELECT {{text_data}}.id, {{text_data}}.import_var_value, {{import_vars}}.title,{{text_data}}.import_var_id
                FROM {{text_data}},{{import_vars}}
                WHERE {{text_data}}.text_id='.$id.'
                    AND {{import_vars}}.id={{text_data}}.import_var_id';

        $data = Yii::app()->db->createCommand($sql)->queryAll();

        // отправили POST на обновление данных по данному тексту
        if(isset($_POST['Text'])){
            //echo '<pre>'; print_r($_POST); die();
            $model->attributes = $_POST['Text'];

            // если в настройках проекта указано, что для копирайтора включены проверки по полям, тогда проверяем поля по проверкам
            if(CheckingImportVars::isEnabledChekingByUser($model->project_id)){
                $model->setScenario('checking');
            }

            // нет ошибок, всё отлично - обновим содержимое задания
            if($model->validate()){
                // цикл по полям, с обновлением значением полей
                foreach($_POST['ImportVarsValue'] as $i=>$val){
                    // SQL запрос на обновление данных
                    $sql = 'UPDATE {{text_data}} SET import_var_value="'.$val.'" WHERE id="'.(int)$i.'"';
                    Yii::app()->db->createCommand($sql)->execute();
                }

                //задание прошло все автомат. проверки, установим новый статус у задания(чтобы редактор мог его проверить)
                Text::setNewStatusText($model->id, Text::TEXT_AVTO_CHECK);

                // откроем на доступ следующее задание в списке заданий проекта
                $numNext = $model->num+1;
                Yii::app()->db->createCommand('UPDATE {{text}}
                                                SET status="'.Text::TEXT_NEW.'"
                                                WHERE num="'.$numNext.'"
                                                    AND project_id="'.$model->project_id.'"')
                                                ->execute();
                if($model->num==1){
                    //обновим статус у проекта, после сохранения изменений в проекте
                    // установим статус - ВЫПОЛНЯЕТСЯ исполнителем, т.е. прошёл автомат. проверки первое задание копирайтора
                    Project::afterChangeDataInProject($model->project_id, Project::PERFORMED, $model->num);
                }else{
                    // установим статус - ОТПРАВЛЕНО_На_ПРОВЕРКУ_ИСПОЛНИТЕЛЕМ
                    Project::afterChangeDataInProject($model->project_id, Project::POSTED_TO_PERFORMED, $model->num);
                }

                // редирект на список заданий, а текущее становится не доступным
                $this->redirect(array('index'));
            }
        }

        $this->render('text_view',array(
            'data'=>$data,
            'model'=>$model,
        ));
    }

    /*
     * подгружаем модель с проверкой прав
     */
    public function loadModel($id){

        //смотреть тексты может копирайтор если он подвязан к этому тексту и он выполнил предыдущее задание
        $projectUser = ProjectUsers::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));
        if($projectUser===null){
           throw new CHttpException(404,'The requested page does not exist.');
        }

        // находим текст с учётом, что к данному тексту подвязан именно текущий юзер-копирайтор
   		$model = Text::model()->findByPk($id,
                 'project_id=:project_id AND (status=:status OR status=:status1)',
                 array(':project_id'=>$projectUser->project_id,
                     ':status'=>Text::TEXT_NEW,
                     ':status1'=>Text::TEXT_NOT_ACCEPT_EDITOR
                 )
        );

   		if($model===null){
            throw new CHttpException(404,'The requested page does not exist.');
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

    /*
     * контроллер запуска проверок по заданию
     */
    public function actionCheck(){
        //проверка запроса и валидация данных
        if(Yii::app()->request->isAjaxRequest){

            // находим МОДЕЛЬ-задания по текущему юзеру и доступу, чтобы запукал на проверку лишь своё задание
            $text = $this->loadModel($_POST['Text']['id']);

            // запускаем очередь из проверок или дозапускаем процесс проверок
            Queue::queueStart($text->id, $text->project_id);


            //получаем общий процент выполнения проверок от общего кол-ва запущенных
            $count = Queue::getProgressByText($text->id);

            echo json_encode(array('count'=>$count));
        }else{
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }

    /*
     * проверяем доступность для редактирования поля из формы задания
     * поля прошедшие все автомат. проверки успешно делаем недоступными, для экономии запросов к ПС
     * по каждому полю из задания, котор. может править пользователь делае проверку
     * в зависимости от статуса задания и проекта, проверяем доступность для юзера по полю
     */
    public function actionDisabledfields(){
        //инициализация AJAX запроса от пользователю
        if(Yii::app()->request->isAjaxRequest){

        }
    }
}