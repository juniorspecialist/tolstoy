<?php

/**
 * This is the model class for table "{{log_cheking}}".
 *
 * The followings are the available columns in table '{{log_cheking}}':
 * @property integer $id
 * @property integer $text_id
 * @property integer $import_var_id
 * @property string $import_var_value
 * @property integer $create
 * @property integer $author
 * @property string $error
 */
class LogCheking extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogCheking the static model class
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
		return '{{log_cheking}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('text_id, import_var_id, import_var_value, create, author, error,check_id', 'required'),
			array('text_id, import_var_id, create, author,check_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, text_id, import_var_id, import_var_value, create, check_id, author, error', 'safe', 'on'=>'search'),
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
			'text_id' => 'Text',
			'import_var_id' => 'Import Var',
			'import_var_value' => 'Import Var Value',
			'create' => 'Create',
			'author' => 'Author',
			'error' => 'Error',
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
		$criteria->compare('text_id',$this->text_id);
		$criteria->compare('import_var_id',$this->import_var_id);
		$criteria->compare('import_var_value',$this->import_var_value,true);
		$criteria->compare('create',$this->create);
		$criteria->compare('author',$this->author);
		$criteria->compare('error',$this->error,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function onBeforeValidate($event) {
        // устанавливаем некие перменные, если они не указаны
        if(empty($this->create)){ $this->create = time(); }

        if(empty($this->author)){ $this->author = Yii::app()->user->id; }
    }
}