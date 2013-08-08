<?php
/*
 * работа с очередью проверок на удалённом сервере
 */
class Queue extends CActiveRecord
{
    // адрес сценария, куда отправляем ID задания, чтобы получить результат проверки
    const URL_RESULT = 'http://temp/check.php';
    // адрес сценария, куда отправляем начальные данные, чтобы запустить очередь проверок для задания
    const URL_SEND_DATA = 'http://temp/post.php';

    // статус запроса, при отправке в очреедь на удалён. сервере
    const STATUS_SEND = 1;// отправили запрос на сервер, поставили в очередь на выполнение
    const STATUS_RECD = 2; // получили ответ при отправке запросе в очередь

    // статусы результатов выполнения запроса
    const RESULT_STATUS_OK = 1;// успешно прошла проверка по передаваемому тексту по выбранному типу проверки
    const RESULT_STATUS_ERROR = 2 ; // при проверке обнаружена ошибка


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Queue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{queue}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('check_id, text_id, status, response_id, user_id, import_var_id', 'required'),
			array('check_id, text_id, status, response_id, user_id, import_var_id, result', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, check_id, text_id, status, response_id, user_id, import_var_id, result', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'check_id' => 'Check',
			'text_id' => 'Text',
			'status' => 'Status',
			'response_id' => 'Response',
			'user_id' => 'User',
			'import_var_id' => 'Import Var',
			'result' => 'Result',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('check_id',$this->check_id);
		$criteria->compare('text_id',$this->text_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('response_id',$this->response_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('import_var_id',$this->import_var_id);
		$criteria->compare('result',$this->result);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     * вычисляем прогресс-бар, процент выполнения проверок от общего колва
     * определяем общее кол-во всех проверок по заданию
     * определяем кол-во проверок, что уже отработало - получили по ним результат
     * и вычисляем процент прогресса на основании цифр
     */
    static function getProgressByText($text_id){
        //определяем общее кол-во всех проверок по заданию в разрезе тек. ЮЗЕРА
        $sql_all = 'SELECT COUNT(id) AS count FROM {{queue}} WHERE user_id="'.Yii::app()->user->id.'" AND text_id="'.$text_id.'"';
        $all = Yii::app()->db->createCommand($sql_all)->queryRow();
        //file_put_contents('1.txt', $sql_all);

        //определяем кол-во проверок, что уже отработало - получили по ним результат, в разрезе тек. ЮЗЕРА
        $sql_result = 'SELECT COUNT(id) AS count FROM {{queue}} WHERE user_id="'.Yii::app()->user->id.'" AND text_id="'.$text_id.'" AND status="'.self::STATUS_RECD.'"';
        $received = Yii::app()->db->createCommand($sql_result)->queryRow();
        //file_put_contents('2.txt', $sql_result);

        // текущий процент выполнения проверок
        if($all['count']>0){
            $delta = intval(($received['count']*100)/$all['count']);
        }else{
            $delta = 0;
        }

        return $delta;
    }

    /*
     * отправляем запросы, чтобы запустить очередь проверок на серваке удалённом
     *отправляем запросы на удалён. сервер и записываем очередь - если не было ранее запущенных проверок по данному заданию в разрезе ЮЗЕРА
     */
    //TODO доделать дозапуск проверок по тем лишь полям, по которым были ошибки, а по которым не было ошибок делать недосутпными для редактирования
    //TODO удалять все данные про проверкам ранее, лишь если не было ранее проверок по заданию+юзеру, если были ошибк, то дозапускаем лишь по проверка где были ошибки
    static function queueStart($text_id, $project_id){
        // если на тек. момент все проверки отработали
        if(Queue::isFinished($text_id)){
            // запускались ли ранее проверки по данному заданию
            if(!Queue::isFirstQueue($text_id)){

                // удаляем историю запуска проверок по заданию и юзеру, чтобы он смог снова запустить автомат. проверки в задании
                Yii::app()->db->createCommand('DELETE FROM {{queue}}
                                                WHERE user_id="'.Yii::app()->user->id.'"
                                                    AND text_id="'.$text_id.'"')
                                                ->execute();
            }

            // получаем список ключевиков по данному заданию
            $key_words = TextData::getKeyWordsByComa($text_id);

            // получаем список СВЕДЕНИЙ по данному заданию
            $proposed = TextData::getProposedByComa($text_id);

            // находим данные о проекте в ввиде массива, для получ. доп. параметров при проверках
            $project = Project::findByIdDAO($project_id);

            //запускаем очередь проверок по полям на удалённом сервере
            foreach($_POST['ImportVarsValue'] as $i=>$val){// цикл по списку полей из задания

                //== находим список проверок по полю из задания=======================
                $cheking_list = CheckingImportVars::getChekingListByFieldID($i, $project_id);

                //===========запускаем проверку по полю================================
                foreach($cheking_list as $j=>$check){//$check['check_id'] - ID проверки

                    // запускаем очередь проверок по каждому полю и записываем ID задания в БД, чтобы потом узнать результат
                    Queue::sendTextData($text_id,$check['check_id'],$key_words,$i,$val,$project );
                }
            }

        }else{
            // дозапуск проверок, которые были запущенны НО не отработали до конца, т.е. не получили ОТВЕТЫ с удалён. сервака
            $sql = 'SELECT response_id FROM {{queue}} WHERE user_id="'.Yii::app()->user->id.'" AND text_id="'.$text_id.'" AND status="'.self::STATUS_SEND.'"';
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach($rows as $row){
                //по каждой проверке, по котор. нет ответа, запускаем запросы и получаем ответ-результат
                Queue::getResultFromHost($row['response_id']);
            }
        }
    }

    /*
     * отправляем запрос на сервер с ID задания, для получения результата проверки
     * формируем CURL запрос на сервер
     */
    static function getResultFromHost($request_id){

        //для каждой проверки отправляем POST запрос на сервер для получения результатов проверки по тексту
        $curl = new Curl(Queue::URL_RESULT);

        // отправляем запрос на удалённый сервер, для проверrb по тексту
        $result = $curl->post(array('id'=>$request_id));// результат проверки в JSON формате

        // обработка ответа от удалённого сервера о получении результа обработки проверки по тексту
        if($result['result']=='ok' || $result['result']=='error'){// НЕТ ошибок при проверке по тексту

            if($result['result']=='ok'){
                $result_SQL = Queue::RESULT_STATUS_OK;
            }
            if($result['result']=='error'){
                $result_SQL = Queue::RESULT_STATUS_ERROR;
            }

            //запишим результат проверки по запросу
            $sql="UPDATE {{queue}}
                  SET status='".Queue::STATUS_RECD."',result='".$result_SQL."'
                  WHERE response_id='".$request_id."'";

            // подготовка SQL запроса на добавление записи к списку очередей
            Yii::app()->db->createCommand($sql)->execute();

        }else{
            // проверка ЕЩЁ не завершена никаких действий не делаем
        }
    }

    /*
     * отправляем первоначальный запрос, для начала проверки на сервере
     *  а потом по полученному ID задания опросим позднее сервер, для получения результата проверки
     * $text_id - ID задания
     * $key_words - список ключевиков, разделённых запятой
     * $check_id - ID проверки, которую нужно запустить по данному тексту из задания
     * $valueField - текст, который необходимо проверить
     * $project - массив данных о проекте, к которому подвязаное задание, текст из которого мы отправляем на проверку
     */
    static function sendTextData($text_id, $check_id, $key_words, $import_var_id, $valueField, $project){

        //для каждой проверки отправляем POST запрос на сервер для получения результатов проверки по тексту
        $curl = new Curl(Queue::URL_SEND_DATA, $text_id, $check_id, $key_words, $valueField);

        // заполняем недостающие параметры для запуска проверок
        $curl->dopysk = $project['dopysk'];
        $curl->total_num_char = $project['total_num_char'];
        $curl->unique = $project['uniqueness'];
        $curl->sickness = $project['sickness'];
        $curl->tolerance = $project['tolerance'];

        // отправляем запрос на удалённый сервер, для проверrb по тексту
        $result = $curl->post();// результат проверки в JSON формате

        // ID запущенной проверки на удалённом сервере
        $response_id = $result['id'];

        // запишим проверку в очередь, чтобы позднее получить результат проверки
        $queue = new Queue();
        $queue->check_id = $check_id;
        $queue->text_id = $text_id;
        $queue->status = Queue::STATUS_SEND;
        $queue->response_id = $response_id;
        $queue->user_id = Yii::app()->user->id;
        $queue->import_var_id = $import_var_id;
        if($queue->validate()){
            $queue->save();
        }else{
            file_put_contents('error.txt', print_r($queue->errors));
            die();
        }

    }

    /*
     * проверяем есть ли недопроверенные проверки по полям, которые следует спросить и получить результат
     * в разрезе задания и текущего пользователя
     */
    static function isFinished($text_id){

        $sql = 'SELECT COUNT(id) AS count FROM {{queue}} WHERE user_id="'.Yii::app()->user->id.'" AND text_id="'.$text_id.'" AND status="'.self::STATUS_SEND.'"';

        $data = Yii::app()->db->createCommand($sql)->queryRow();

        if($data['count']==0){
            return true;
        }else{
            // существуют запущенные проверки, по которым нужно получить резултаты проверок
            return false;
        }
    }

    /*
     * запускались ЛИ вообще проверки по данному заданию тек. юзером
     * $text_id - ID задания
     */
    static function isFirstQueue($text_id){

        $sql = 'SELECT COUNT(id) AS count FROM {{queue}} WHERE user_id="'.Yii::app()->user->id.'" AND text_id="'.$text_id.'"';

        $data = Yii::app()->db->createCommand($sql)->queryRow();

        if($data['count']==0){
            return true;
        }else{
            // ранее запускались проверки по данному заданию юзером
            return false;
        }
    }

    /*
     * аннулировать ВСЕ проверки по заданию определён. юзера
     * т.е. когда ЗАДАНИЕ отклонил редактор - удаляем инфу об очередях на проверку по копирайтору
     * $text_id - ID задания
     * $project_id - ID проекта, к которому подвязано задание
     * $status - статус задания, отменил его либо редактор либо админ
     */
    static function cancelQueueByTextWithUser($text_id, $project_id, $status){

        //в зависимости от того, кто отменил задание стираем проверки по очереди запусков ПРОВЕРОК по юзеру в задании
        if($status==Text::TEXT_NOT_ACCEPT_EDITOR || $status==Text::TEXT_NOT_ACCEPT_ADMIN){// не принят РЕДАКТОРОМ

            // находим по заданию и роли ЮЗЕРА, котор. подвязан к заданию в проекте
            $user = ProjectUsers::getUserByProject($project_id, User::ROLE_COPYWRITER);

            // удаляем историю запуска проверок по заданию и юзеру, чтобы он смог снова запустить автомат. проверки в задании
            Yii::app()->db->createCommand('DELETE FROM {{queue}} WHERE user_id="'.$user['id'].'" AND text_id="'.$text_id.'"')->execute();
        }

        // не принял АДМИНОМ
        if($status==Text::TEXT_NOT_ACCEPT_ADMIN){

            // находим по заданию и роли ЮЗЕРА, котор. подвязан к заданию в проекте
            $user = ProjectUsers::getUserByProject($project_id, User::ROLE_ADMIN);

            // удаляем историю запуска проверок по заданию и юзеру, чтобы он смог снова запустить автомат. проверки в задании
            Yii::app()->db->createCommand('DELETE FROM {{queue}} WHERE user_id="'.$user['id'].'" AND text_id="'.$text_id.'"')->execute();
        }
    }
}