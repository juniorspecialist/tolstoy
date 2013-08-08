<?php

/**
 * This is the model class for table "{{comments}}".
 *
 * The followings are the available columns in table '{{comments}}':
 * @property integer $id
 * @property string $model
 * @property integer $model_id
 * @property integer $user_id
 * @property integer $create
 * @property string $text
 */
class Comments extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comments the static model class
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
		return '{{comments}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model, model_id, user_id, create, text', 'required'),
			array('model_id, user_id, create', 'numerical', 'integerOnly'=>true),
			array('model', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, model, model_id, user_id, create, text', 'safe', 'on'=>'search'),
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
			'model' => 'ID модели',
			'model_id' => 'Модель',
			'user_id' => 'Автор',
			'create' => 'Дата',
			'text' => 'Текст комментария',
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
		$criteria->compare('model',$this->model,true);
		$criteria->compare('model_id',$this->model_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('create',$this->create);
		$criteria->compare('text',$this->text,true);

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
        $this->create = date('d-m-Y H:i:s' ,$this->create);
    }

    public function scopes()
    {
        return array(
            'admin'=>array(
                'condition'=>'user.role="'.User::ROLE_ADMIN.'" OR user.role="'.User::ROLE_SA_ADMIN.'"',//
                //'order'=>'t.id DESC',
                'limit'=>Yii::app()->params['perPage'],
            ),
            'redactor'=>array(
                'condition'=>'user.role="'.User::ROLE_EDITOR.'"',
                //'order'=>'id DESC',
                'limit'=>Yii::app()->params['perPage'],
            ),
            'copywriter'=>array(
                'condition'=>'user.role="'.User::ROLE_COPYWRITER.'"',
                //'order'=>'id DESC',
                'limit'=>Yii::app()->params['perPage'],
            ),
        );
    }
}