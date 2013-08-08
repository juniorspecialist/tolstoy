<?php

/**
 * This is the model class for table "{{errors}}".
 *
 * The followings are the available columns in table '{{errors}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $model
 * @property string $error_text
 * @property integer $model_id
 * @property integer $create
 */
class Errors extends CActiveRecord
{

    const PUNCTUATION = 1; //ошибка Пунктуации
    const SPELLING = 2; //ошибка - Орфография
    const UNIQUE = 3; //уникальность контента
    const DENSITY_KEYS = 4; //Плотность вхождения ключей
    const ACCURACY_KEYS = 5; //Точность вхождения ключей
    const ORDER_KEYS = 6; //Порядок следования ключей
    const DISTANCE_KEYWORDS = 7; //Расстояние между словами в ключевике
    const UNIFORM_DISTRIBUTION = 8; //Равномерность распределения ключей
    const OTHER = 9;// другой тип ошибки


    /*
     * список-массив типов-ошибок
     */
    public static function getListErrors($error_id=''){

        if(!empty($error_id)){
            return Errors::getErrorDesc($error_id);
        }else{
            return array(
                self::PUNCTUATION=>'Пунктуация',
                self::SPELLING=>'Орфография',
                self::UNIQUE=>'Уникальность контента',
                self::DENSITY_KEYS=>'Плотность вхождения ключей',
                self::ACCURACY_KEYS=>'Точность вхождения ключей',
                self::ORDER_KEYS=>'Порядок следования ключей',
                self::DISTANCE_KEYWORDS=>'Расстояние между словами в ключевике',
                self::UNIFORM_DISTRIBUTION=>'Равномерность распределения ключей',
                self::OTHER=>'Другой тип ошибки',
            );
        }
    }

    static function getErrorDesc($error_type){
        $list = Errors::getListErrors();
        $find = $list[$error_type];
        return $find;
    }

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Errors the static model class
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
		return '{{errors}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, model, error_text, model_id, create', 'required'),
			array('user_id, model_id, create, type', 'numerical', 'integerOnly'=>true),
			array('model', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, model, error_text, model_id, create', 'safe', 'on'=>'search'),
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
			'user_id' => 'Автор',
			'model' => 'Model',
			'error_text' => 'Описание ошибки',
			'model_id' => 'Model',
			'create' => 'Дата',
            'type'=>'Тип',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('error_text',$this->error_text,true);
		$criteria->compare('model_id',$this->model_id);
		$criteria->compare('create',$this->create);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function onBeforeValidate($event) {
        // устанавливаем некие перменные, если они не указаны
        if(empty($this->create)){ $this->create = time(); }

        if(empty($this->user_id)){ $this->user_id = Yii::app()->user->id; }
    }

    protected function afterFind()
    {
        parent::afterFind();
        $this->create=date('d-m-Y H:i:s', $this->create);
    }
}