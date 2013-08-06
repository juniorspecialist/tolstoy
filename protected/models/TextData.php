<?php

/**
 * This is the model class for table "{{text_data}}".
 *
 * The followings are the available columns in table '{{text_data}}':
 * @property integer $id
 * @property integer $import_var_id
 * @property string $import_var_value
 * @property integer $text_id
 */
class TextData extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TextData the static model class
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
		return '{{text_data}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('import_var_id, text_id', 'required'),//import_var_value,
			array('import_var_id, text_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, import_var_id, import_var_value, text_id', 'safe', 'on'=>'search'),
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
			'import_var_id' => 'Import Var',
			'import_var_value' => 'Import Var Value',
			'text_id' => 'Text',
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
		$criteria->compare('import_var_id',$this->import_var_id);
		$criteria->compare('import_var_value',$this->import_var_value,true);
		$criteria->compare('text_id',$this->text_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     *  получаем список ключевиков по заданию, с разделителем запятая
     * $text_id - ID задания
     */
    static function getKeyWordsByComa($text_id){
        $sql = 'SELECT import_var_value FROM {{text_data}} WHERE text_id="'.$text_id.'" AND import_var_id="'.Yii::app()->params['key_words'].'"';
        $array = Yii::app()->db->createCommand($sql)->queryAll();
        $result = '';
        foreach($array as $i=>$row){
            if($i==(sizeof($array)-1)){
                $result.=$row['import_var_value'];
            }else{
                $result.=$row['import_var_value'].',';
            }
        }

        return $result;
    }

    /*
     * получаем список СВЕДЕНИЙ из задания через запятую
     * $text_id - ID задания
     */
    static function getProposedByComa($text_id){
        $sql = 'SELECT import_var_value FROM {{text_data}} WHERE text_id="'.$text_id.'" AND import_var_id="'.Yii::app()->params['reduction'].'"';
        $array = Yii::app()->db->createCommand($sql)->queryAll();
        $result = '';
        foreach($array as $i=>$row){
            if($i==(sizeof($array)-1)){
                $result.=$row['import_var_value'];
            }else{
                $result.=$row['import_var_value'].',';
            }
        }

        return $result;
    }
}