<?php
class Messages extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Messages the static model class
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
		return '{{messages}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, create, model, model_id, msg_text,recipient_id', 'required'),
			array('author_id, create, model_id, is_new, recipient_id', 'numerical', 'integerOnly'=>true),
            array('is_new', 'default', 'value'=>1),
			array('model', 'length', 'max'=>255),
            array('create', 'default', 'value'=>time()),
            array('author_id', 'default', 'value'=>Yii::app()->user->id),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, create, model, model_id, msg_text, is_new', 'safe', 'on'=>'search'),
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
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'author_id' => 'Автор',
			'create' => 'Дата',
			'model' => 'Модель',
			'model_id' => 'ID Модель',
			'msg_text' => 'Текст сообщения',
            'recipient_id'=>'Получатель',
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
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('create',$this->create);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('model_id',$this->model_id);
		$criteria->compare('msg_text',$this->msg_text,true);
        $criteria->compare('recipient_id',Yii::app()->user->id);
        $criteria->order = " id DESC";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>Yii::app()->params['perPage'],
            ),
		));
	}

    protected function afterFind()
    {
        parent::afterFind();
        $this->create=date('d-m-Y H:i:s', $this->create);
    }

    /*
     * на основании модели и её ID формируем запись описания сообщения
     * $model - название модели, под которую подвязали сообщение
     * $model_id - ID модели под котор. подвязали личное сообщение
     * $model, $model_id - нужны, чтобы определить по какому проекту личное сообщение отправлено
     */
    public static function getHeaderMsg($model, $model_id){
        if($model=='Project'){
            return 'Проект №'.$model_id;
        }
    }

    public function onBeforeValidate($event) {
        // устанавливаем некие перменные, если они не указаны
        if(empty($this->create)){ $this->create = time(); }

        if(empty($this->author_id)){ $this->author_id = Yii::app()->user->id; }
    }


    /*
     * находим кол-во НОВЫХ сообщений для пользователя
     */
    static function counNewMsg(){
        $sql = 'SELECT COUNT(id) as count FROM {{messages}} WHERE recipient_id="'.Yii::app()->user->id.'" AND is_new="1"';
        $result = Yii::app()->db->createCommand($sql)->queryRow();

        return $result['count'];
    }
    /*
     * выделяем новые сообщения в таблице сообщений
     */
    static function getClassTbl($is_new){

        if($is_new==1){
            return 'new_msg';
        }else{
            return 'old_msg';
        }
    }

    /*
     * кнопка для отправки личного сообщения из диалогового окна
     */
    public function getLinkToForm(){
        return CHtml::ajaxSubmitButton('Отправить',
            Yii::app()->createUrl('ajax/message'),
            array(
                'type' => 'POST',
                'success'=>'js:function(data){ $("div.form-messages").html(data); }',
            ),
            array('class'=>'btn btn-primary')
        );
    }
}