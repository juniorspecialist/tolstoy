<?php

/**
 * This is the model class for table "{{text}}".
 *
 * The followings are the available columns in table '{{text}}':
 * @property integer $id
 * @property integer $project_id
 * @property integer $status
 */
class Text extends CActiveRecord
{
    // метка ошибок, при проверке данных по выбранным проверкам в задании, храним массив ошибок по всем полям
    public $detail_error;

    // список статуосв текста, в процессе его написнаия
    const TEXT_NEW_DISABLED_COPY = -1;// необходимая метка для последовательного открытия доступа копирайтора к текстам
    const TEXT_NEW = 1; // новый текст, только что создали, после импорта файла
    const TEXT_AVTO_CHECK = 2; // прошёл автоматические проверки и нет ошибок по тексту
    const TEXT_ACCEPT_EDITOR = 3; // принят текст редактором
    const TEXT_NOT_ACCEPT_EDITOR = 4; // задание не прияното редактором, есть ошибки
    const TEXT_ACCEPT_ADMIN = 5; // задание принято админом
    const TEXT_NOT_ACCEPT_ADMIN = 6; // задание НЕ принято админом, отклонено

    public $status_new;// для установки статуса редактором при проверке задания от копирайтора
    public $status_new_text;// описание ошибки

    /*
     * выводим текстовое соответствие относительно текущего статуса текста
     */
    public static function getStatus($status){
        // новый статус текста
        if($status==self::TEXT_NEW || $status==self::TEXT_NEW_DISABLED_COPY ){ return 'Новый'; }

        // прошёл автомат. проверки системой, после написания копирайтором всех полей
        if($status==self::TEXT_AVTO_CHECK){ return 'Проверенный'; }

        if($status==self::TEXT_ACCEPT_EDITOR){ return 'Принят редактором'; }

        if($status==self::TEXT_ACCEPT_ADMIN){ return 'Принято админом'; }

        if($status==self::TEXT_NOT_ACCEPT_ADMIN){ return 'Не принято админом'; }

        if($status==self::TEXT_NOT_ACCEPT_EDITOR){ return 'Не принято редактором'; }
    }


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Text the static model class
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
		return '{{text}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_id, status', 'required'),
            array('status_new','length', 'max'=>256),
            // проверка заполнения текста ошибки, при выборе что есть ошибки в заполненной задании копирайтором
            array('status_new_text', 'description_error'),
			array('project_id, status', 'numerical', 'integerOnly'=>true),
            array('detail_error', 'chekingFields', 'on'=>'checking'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, project_id, status', 'safe', 'on'=>'search'),
		);
	}

    /*
     * метод проверки запуска проверок по полям из задания
     * по каждому полю из POST массива
     */
    public function chekingFields(){
        if(!$this->hasErrors()){
            $errors_main = array();
            //$val - значение этого поля
            //$i - ID поля из ImportVars

            foreach($_POST['ImportVarsValue'] as $i=>$val){

                $val = trim(strip_tags($val));

                // запускаем проверку по полю и находим ошибки, если есть
                //$errors = CheckingImportVars::checkingFieldByRules($i, $val, $this->project_id, $this->id, $key_words, $project);
                // если не пустое значение ошибок, тогда записываем ошибку в общий список ошибок по проверке в задании
                if(empty($val)){
                    $this->addError('error','Обнаружены ошибки при проверке данных:');
                    $this->detail_error = array('Необходимо заполнить все поля задания');
                    break;
                    return false;
                }
            }

            // если нет ошибок, тогда запускаем очередь проверок
            if(!$this->hasErrors()){
                Queue::queueStart($this->id, $this->project_id);
            }

            return true;
        }
    }

    /*
     * если редактор выбрал статус ошибки, то должен указать описание этой ошибки для проверяемого текста - задания копирайтора
     */
    public function description_error(){
        //file_put_contents('error.txt',$this->status_new);
        // если редактор установил что есть ошибки, то должен указать описание ошибки
        if($this->status_new=='error'){

            // проверка на то, заполнил ли редактор описание ошибки
            if(empty($this->status_new_text)){
                $this->addError('status_new_text','Необходимо указать описание ошибок');
                return false;
            }
        }
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
			'project_id' => 'Project',
			'status' => 'Status',
            'status_new_text'=>'Описание ошибки',
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
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     * получаем кол-во текстов по проекту
     */
    public static function getCountTextByProject($project_id){
        $sql = 'SELECT COUNT(id) as count
                FROM {{text}}
                WHERE {{text}}.project_id="'.$project_id.'"';

        $data = Yii::app()->db->createCommand($sql)->queryRow();

        if(empty($data['count'])){
            return 0;
        }else{
            return $data['count'];
        }
    }

    /*
     * в зависимости от пользователя и настроек по проекту запускаем проверки
     *$textID - ID текста который будем проверять по настройкам из проекта
     * суть - получаем список параметров для проверки по тексту и запускаем скрипты на проверку текста на ошибки
     */
    public static function  runChekingText($textID){

    }

    /*
     * установим новый статус у задания
     * по его ID
     */
    public static function setNewStatusText($TextId, $status){
        $sql = 'UPDATE {{text}} SET status="'.$status.'" WHERE id="'.$TextId.'"';
        Yii::app()->db->createCommand($sql)->execute();
    }

    protected function afterSave()
    {
        parent::afterSave();

        // редактор принял задание от копирайтора
        if(Yii::app()->user->role==User::ROLE_EDITOR && $this->status==Text::TEXT_ACCEPT_EDITOR){
            // изменим статус проекта на "Задание проверяется редактором"
            Project::afterChangeDataInProject($this->project_id, Project::TASK_CHEKING_REDACTOR, $this->num);
        }

        // админ принимает или отклонит хотя бы одно задание, тогда установим-ЗАДАНИЕ_ПРОВЕРЯЕТСЯ_АДМИНОМ
        if(User::isAdmin() && $this->status==Text::TEXT_ACCEPT_ADMIN){
            // изменим статус проекта на "Задание проверяется админом"
            Project::afterChangeDataInProject($this->project_id, Project::TASK_CHEKING_ADMIN, $this->num);
        }
    }

    /*
     * формируем заголовок задания из TITLE параметра задания, если он есть
     * а если нет тогда ЗАДАНИЕ №по порядоку
     */
    static function getTitleText($title, $num){

        if(empty($title)){
            return 'Задание №'.$num;
        }else{
            return $title;
        }
    }
}