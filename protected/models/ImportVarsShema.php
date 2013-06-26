<?php

/**
 * This is the model class for table "{{import_vars_shema}}".
 *
 * The followings are the available columns in table '{{import_vars_shema}}':
 * @property integer $id
 * @property integer $import_var_id
 * @property integer $shema_type
 * @property integer $num_id
 * @property integer $num
 */
class ImportVarsShema extends CActiveRecord
{

    // существует 2 типа схем - для проекта и для шаблона
    const SHEMA_TYPE_PROJECT = 1; // подвязка к тому, что запись подвязана к проекту
    const SHEMA_TYPE_TEMPLATE = 2; // подвязка к тому, что запись подвязана к шаблону

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ImportVarsShema the static model class
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
		return '{{import_vars_shema}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('import_var_id, shema_type, num_id, num, edit, visible, wysiwyg,label', 'required'),
			array('import_var_id, shema_type, num_id, num, edit, visible, wysiwyg', 'numerical', 'integerOnly'=>true),
            array('label', 'length', 'max'=>255),
            //array('', 'boolean'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, import_var_id, shema_type, num_id, num, edit, visible, wysiwyg, label', 'safe', 'on'=>'search'),
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
			'shema_type' => 'Shema Type',
			'num_id' => 'Num',
			'num' => 'Num',
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
		$criteria->compare('shema_type',$this->shema_type);
		$criteria->compare('num_id',$this->num_id);
		$criteria->compare('num',$this->num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     * получаем список названий столбцов для импорта со значениями
     * $type_shema - тип схемы или для шаблона или для проекта
     */
    public static function getListFieldsByIdShema($shema_id, $type_shema=2){
        //$shema_id
        $sql = 'SELECT {{import_vars_shema}}.*, {{import_vars}}.title,{{import_vars}}.name
                FROM {{import_vars_shema}},{{import_vars}}
                WHERE {{import_vars_shema}}.shema_type='.$type_shema.'
                    AND {{import_vars_shema}}.num_id='.$shema_id.'
                    AND {{import_vars_shema}}.import_var_id={{import_vars}}.id
                ORDER BY {{import_vars_shema}}.num ASC
                ';
        $data = Yii::app()->db->createCommand($sql)->queryAll();
        //$data = ImportVarsShema::model()->findAllByAttributes(array('num_id'=>$shema_id,'shema_type'=>$type_shema));

        return $data;
    }

    /*
     * копируем настройки полей шаблона по полям соотвествия
     * в настройки полей соотвествия по проекту
     * $num_id - id проекта или шаблона к которому подвязаны поля соотвествий
     * $shema_type - тип настроек или схемы(шаблон или проект)
     * $model_id - новая подвязка к ID шаблона или проекта(поле "num_id" в таблице)
     * $templateId - ID шаблона к которому подвязан список проверок по полям(будем их копировать из этого шаблона)
     */
    public static function copyTemplate($shems, $model_id, $templateId){

        // перебираем поля соотвествий и сохраняем схему, для задания
        foreach($shems as $shema){
            $shemaProject = new ImportVarsShema();
            $shemaProject->import_var_id = $shema['import_var_id'];
            $shemaProject->num_id = $model_id;
            $shemaProject->shema_type = ImportVarsShema::SHEMA_TYPE_PROJECT;
            $shemaProject->num = $shema['num'];
            $shemaProject->edit = $shema['edit'];
            $shemaProject->visible = $shema['visible'];
            $shemaProject->wysiwyg = $shema['wysiwyg'];
            $shemaProject->label = $shema['label'];
            $shemaProject->save();
            //находим настройки по данному полю относ. списка проверок из шаблона и записываем их к проекту
            $sql = 'SELECT *
                FROM {{checking_import_vars}}
                WHERE model_id="'.$templateId.'"
                    AND import_var_id="'.$shema['id'].'"
                    AND type="1"';

            $infoCheking = Yii::app()->db->createCommand($sql)->queryAll();
            // записываем настройк НВОЫЕ для проекта через DAO
            foreach($infoCheking as $row){
                $insert = 'INSERT INTO {{checking_import_vars}}
                        (type,model_id,import_var_id,selected,checked_id)
                        VALUES("2","'.$model_id.'","'.$shemaProject->id.'","'.$row['selected'].'","'.$row['checked_id'].'")';

                Yii::app()->db->createCommand($insert)->execute();
            }
        }
    }

    /*
     * формируем на основании настроек видимости, редактирования и визивиг редактора код для HTML элемента
     * для форма при редактировании текста, при выполнении задания
     * $dataFields - массив ID элементов, для которых прописаны правила
     * $project_id - ID проекта, к которому подвязаны правила по элементам
     * суть - находим все правила по данному проекту и применяем их при создании HTML кода для формы пользователя
     */
//    public static function createHtmlElement($project_id, $dataFields, $forma){
//        // результирую форма, HTML код финальной формы юзера
//        $form = '';
//        $div_start = '<div class="row">';
//        $div_end = '</div>';
//
//        // перебираем в цикле все элементы формы с полями и смотрим их настройки по полям
//        foreach($dataFields as $i=>$field){
//            // по каждому полю делаем запрос на выборку настроек по полю
//            $sqlRule = 'SELECT {{import_vars_shema}}.edit,{{import_vars_shema}}.visible,{{import_vars_shema}}.wysiwyg
//                        FROM {{import_vars_shema}}
//                        WHERE import_var_id';
//            $rule = Yii::app()->db->createCommand($sqlRule)->queryRow();
//
//            $htmlOptions = array();
//            $input_element = '';
//
//            // смотрим видимость, доступность редактирования и ВИЗИВИГ редактор по полю
//            if($rule['visible']==0){
//                $htmlOptions['visible'] = false;
//            }
//
//            if($rule['edit']==0){
//                $htmlOptions['disabled'] = true;
//            }
//
//            if($rule['wysiwyg']==1){
//                //$input_element = $forma->redactorRow('ImportVarsValue['.$field['id'].']',$field['import_var_value']);
//                $input_element = $this->widget('bootstrap.widgets.TbRedactorJs', array(
//                                'name' => 'text',
//                                'value' => 'blah',
//                                'lang' => 'ru',
//                                'editorOptions' => array(
//                                   //тут все опции редактора
//                                    //'buttons'=>array('formatting'),
//                                )
//                            ));
//            }else{
//                $input_element = CHtml::textField('ImportVarsValue['.$field['id'].']',$field['import_var_value']);
//            }
//
//            $form.=$div_start.'<label for="'.$field['title'].'">'.$field['title'].'</label>'.$input_element.$div_end;
//        }
//
//        return $form;
//    }

}