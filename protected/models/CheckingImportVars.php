<?php

/**
 * This is the model class for table "{{checking_import_vars}}".
 *
 * The followings are the available columns in table '{{checking_import_vars}}':
 * @property integer $id
 * @property integer $type
 * @property integer $model_id
 * @property integer $import_var_id
 * @property integer $selected
 * @property integer $checked_id
 */
class CheckingImportVars extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckingImportVars the static model class
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
		return '{{checking_import_vars}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, model_id, import_var_id, selected, checked_id', 'required'),
			array('type, model_id, import_var_id, selected, checked_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, model_id, import_var_id, selected, checked_id', 'safe', 'on'=>'search'),
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
			'type' => 'Type',
			'model_id' => 'Model',
			'import_var_id' => 'Import Var',
			'selected' => 'Selected',
			'checked_id' => 'Checked',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('model_id',$this->model_id);
		$criteria->compare('import_var_id',$this->import_var_id);
		$criteria->compare('selected',$this->selected);
		$criteria->compare('checked_id',$this->checked_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     * проверка  - выбрана ли галочка по данной проверке, у конкретного поля в шаблоне|проекте
     * checked_id - ID проверки из таблицы проверок
     * $modelID - ID модели проекта или шаблона, зависит от типа
     * $type - тип настройки - шаблон или проект
     * $importVarId - ID внутренней переменной
     */
    static function isSelectedCheking($type, $modelID, $importVarId, $checked_id){
        $sql = 'SELECT {{checking_import_vars}}.*
                FROM {{checking_import_vars}}
                WHERE type="'.$type.'"
                    AND import_var_id="'.$importVarId.'"
                    AND checked_id="'.$checked_id.'"
                    AND model_id="'.$modelID.'"
                    ';
        //echo $sql.'<br>';
        $result = Yii::app()->db->createCommand($sql)->queryRow();
        return $result;
    }

    /*
     * формируем форму выбора или уже установленных галочек выборок по полю в выбранном проекте или шаблоне
     * т.е. форма скрыта - кликаем на ссылку - появляется диалоговое окно выбора списка проверок
     * $importVarId - ID поля для которого устанавливаем список проверок
     * $type - тип т.е. для проекта эта запись или для шаблона эта настройка
     * $modelID - ID записи в моделе(ID проекта либо ID шаблона, если ID пустой, значит данных нет по полю - НОВЫЙ список)
     */
    public static function getFormChekingByField($type='',$modelID='',$importVarId='',$column='',$chekingList){
        if(!empty($modelID) && !empty($type)){
            // проверка на существование уже выбранных позиций(выбранных галочек) по нкжному полю-столбцу
            $sql = 'SELECT *
                    FROM {{checking_import_vars}}
                    WHERE type="'.$type.'"
                        AND import_var_id="'.$importVarId.'"
                        AND model_id="'.$modelID.'"';
            $data = Yii::app()->db->createCommand($sql)->queryAll();

            // получаем список проверок и формируем по ним список чекбоксов
            //$chekingList = CheckingImportVars::getChekingList();
            $checkboxListForm = '';
            foreach($chekingList as $j=>$box){

                $selected = CheckingImportVars::isSelectedCheking($type, $modelID, $importVarId, $box['id']);

                //$row = $data[$j+1];

                if($selected['selected']==1){
                    $checked = true;
                }else{
                    $checked = false;
                }

                $checkboxListForm.=CHtml::checkBox('ChekingVarID['.$importVarId.']['.$selected['checked_id'].']',$checked).$box['title'].'<br>';
            }

            // формируем форму диалогового окна
            $id = uniqid();
            $link = CHtml::link('Проверки', '#', array('class'=>$id, 'style'=>'margin-left:20px;', 'data-toggle'=>'modal', 'data-target'=>'#'.$id));//,'onclick'=>'js:$("#'.$id.'").show();'
            $linkSelectAll = CHtml::link('Отметить все', '#', array('onclick'=>'js:$("div#'.$id.' input").attr("checked", true);'));
            $linkDeSelectAll = CHtml::link('Снять выделение', '#', array('style'=>'margin-left:40px;' ,'onclick'=>'js:$("div#'.$id.' input").attr("checked", false);'));


            $forma = '<div id="'.$id.'" class="modal fade">
                        <div class="modal-header"><a class="close" data-dismiss="modal">×</a>
                        <h4>Список проверок по полю:'.$column.'</h4>'.$checkboxListForm.'<br/>
                        '.$linkSelectAll.$linkDeSelectAll.'
                     </div>';

            $forma_ = $link.$forma;

            return $forma_;
        }else{
            // получаем список проверок и формируем по ним список чекбоксов
            //$chekingList = CheckingImportVars::getChekingList();

            // формируем форму диалогового окна
            $id = uniqid();

            $checkboxListForm = '';
            foreach($chekingList as $box){//
                $checkboxListForm.=CHtml::checkBox('ChekingVarID['.$importVarId.']['.$box['id'].']',true, array('class'=>$id)).$box['title'].'<br>';
            }


            $link = CHtml::link('Проверки', '#', array('class'=>$id,'style'=>'margin-left:20px;','data-toggle'=>'modal', 'data-target'=>'#'.$id));//,'onclick'=>'js:$("#'.$id.'").show();'

            $linkSelectAll = CHtml::link('Отметить все', '#', array('onclick'=>'js:$("div#'.$id.' input").attr("checked", true);'));
            $linkDeSelectAll = CHtml::link('Снять выделение', '#', array('style'=>'margin-left:40px;' ,'onclick'=>'js:$("div#'.$id.' input").attr("checked", false);'));

            $forma = '<div id="'.$id.'" class="modal fade">
                        <div class="modal-header"><a class="close" data-dismiss="modal">×</a>
                        <h4>Список проверок по полю:'.$column.'</h4>'.$checkboxListForm.'<br>
                        '.$linkSelectAll.$linkDeSelectAll.'
                     </div>';



            $forma_ = $link.$forma;//'<div style="display:none;" id="'.$id.'">'.$checkboxListForm.'</div>';

            return $forma_;
        }

    }

    /*
     * получаем список проверок - весь
     */
    public static function getChekingList(){
        $sql = 'SELECT {{check}}.*
                FROM {{check}}';
        return Yii::app()->db->createCommand($sql)->queryAll();
    }

    /*
     * по ID "tbl_text_data" определяем какой это тип внутренней переменной и прописаны по ней в проекте проверки
     * если прописаны проверки, то запускаем классы соотвествующих проверок и обрабатываем значение из поля
     * $key_words - список ключевиков разделён. запятой, для отправки запросов на сервер для программ проверок по правилам
     * $project - массив данных о проекте
     */
    public static function checkingFieldByRules($fieldID, $valueField, $projectID, $text_id, $key_words, $project){

        // сначала находим ID строки в таблице схемы по данному полю и проекту
        $shema = Yii::app()->db->createCommand('SELECT {{import_vars_shema}}.id,{{import_vars_shema}}.label
                                                FROM {{text_data}},{{import_vars_shema}}
                                                WHERE {{import_vars_shema}}.import_var_id={{text_data}}.import_var_id
                                                    AND {{text_data}}.id="'.$fieldID.'"
                                                    AND {{import_vars_shema}}.shema_type="1"
                                                    AND {{import_vars_shema}}.num_id="'.$projectID.'"')->queryRow();

        //$shema[id] - ID из таблицы СХЕМ-import_vars_shema, схема настроек по данному полю
        //$shema[title] - название поля - его заголовок. возможно его использовать при выводе ошибок проверки

        // получаем список классов с проверками по которым надо прогнать наше значение из поля
        $sql = 'SELECT {{checking_import_vars}}.*, {{check}}.title, {{check}}.class_name, {{check}}.id AS check_id
                FROM {{checking_import_vars}},{{check}}
                WHERE {{checking_import_vars}}.import_var_id="'.$shema['id'].'"
                    AND {{checking_import_vars}}.type="2"
                    AND {{checking_import_vars}}.selected="1"
                    AND {{check}}.id={{checking_import_vars}}.checked_id
                    AND {{checking_import_vars}}.model_id="'.$projectID.'"';
        $data = Yii::app()->db->createCommand($sql)->queryAll();

        //$data[title] - название проверки, по которой мы будем проверять содержимое поля
        //$data[class_name] - имя класса который будет запускать проверку по содержимому поля

        // флаг ошибок при проверках
        $errors = '';

        // если не указан адрес сервера значит апускаем проверки локально, под каждый тип проверки - свой класс
        if(empty(Yii::app()->params['cheking_url'])){
            foreach($data as $check){
                $class = new $check['class_name'];
                $class->sourceText = $valueField;
                $class->title = $check['title'];
                $result = $class->run();
                // если есть ошибки - запишим их в общий лог ошибок и выведим их
                if(!$result['result']){
                    $errors.=$result['msg'];
                    // обнаружена ошибка, записываем в Лог ошибок
                    $log = new LogCheking();
                    $log->import_var_id = $fieldID;
                    $log->check_id = $check['check_id'];
                    $log->import_var_value = $valueField;
                    $log->text_id = $text_id;
                    $log->error = $result['msg'];// записываем описание ошибки
                    $log->save();
                }
            }
        }else{
            // запускаем проверки НЕ локально, а через проги, которые будут отдавать ответ о результатах проверок
            foreach($data as $check){
                //для каждой проверки отправляем POST запрос на сервер для получения результатов проверки по тексту
                $curl = new Curl(Yii::app()->params['cheking_url'], $text_id, $check['check_id'], $key_words, $valueField);
                // заполняем недостающие параметры для запуска проверок
                $curl->dopysk = $project['dopysk'];
                $curl->total_num_char = $project['total_num_char'];
                $curl->unique = $project['uniqueness'];
                $curl->sickness = $project['sickness'];
                $curl->tolerance = $project['tolerance'];
                // отправляем запрос на удалённый сервер, для проверrb по тексту
                $result = $curl->post();// результат проверки
            }
        }

        return $errors;
    }

    /*
     * включены ли проверки по даному пользователю
     * если ROLE=redactor или ROLE=copywriter, смотрим по настройкам в проекте
     */
    public static function  isEnabledChekingByUser($project_id){
        // проверка по редактору
        if(Yii::app()->user->role==User::ROLE_EDITOR){
            $sql = 'SELECT id FROM {{project}} WHERE check_editor="1" AND id="'.$project_id.'"';
            $find = Yii::app()->db->createCommand($sql)->queryRow();
            if(empty($find)){
                return false;
            }else{
                return true;
            }
        }

        // проверка по копирайтору
        if(Yii::app()->user->role == User::ROLE_COPYWRITER){
            $sql = 'SELECT id FROM {{project}} WHERE check_copywriter="1" AND id="'.$project_id.'"';
            $find = Yii::app()->db->createCommand($sql)->queryRow();
            if(empty($find)){
                return false;
            }else{
                return true;
            }
        }

        return false;
    }

    /*
     * получаем список проверок по полю из ЗАДАНИЯ
     */
    static function getChekingListByFieldID($fieldID,$projectID){

        // сначала находим ID строки в таблице схемы по данному полю и проекту
        $shema = Yii::app()->db->createCommand('SELECT {{import_vars_shema}}.id,{{import_vars_shema}}.label
                                                FROM {{text_data}},{{import_vars_shema}}
                                                WHERE {{import_vars_shema}}.import_var_id={{text_data}}.import_var_id
                                                    AND {{text_data}}.id="'.$fieldID.'"
                                                    AND {{import_vars_shema}}.shema_type="1"
                                                    AND {{import_vars_shema}}.num_id="'.$projectID.'"')->queryRow();

        //$shema[id] - ID из таблицы СХЕМ-import_vars_shema, схема настроек по данному полю
        //$shema[title] - название поля - его заголовок. возможно его использовать при выводе ошибок проверки

        // получаем список классов с проверками по которым надо прогнать наше значение из поля
        $sql = 'SELECT {{checking_import_vars}}.*, {{check}}.title, {{check}}.class_name, {{check}}.id AS check_id
                FROM {{checking_import_vars}},{{check}}
                WHERE {{checking_import_vars}}.import_var_id="'.$shema['id'].'"
                    AND {{checking_import_vars}}.type="2"
                    AND {{checking_import_vars}}.selected="1"
                    AND {{check}}.id={{checking_import_vars}}.checked_id
                    AND {{checking_import_vars}}.model_id="'.$projectID.'"';

        $data = Yii::app()->db->createCommand($sql)->queryAll();

        return $data;
    }
}