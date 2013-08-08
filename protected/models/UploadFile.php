<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 25.10.12
 * Time: 15:38
 * To change this template use File | Settings | File Templates.
 */
class UploadFile extends CFormModel{

    public $upload_file; // сам файл который будем загружать
    public $result; // результат импорта файла

    public function rules()
   	{
   		return array(
   			// username and password are required
            //array('result', 'length', 'max'=>255),
            array('upload_file', 'file',
                   'types'=>'csv',
                   'maxSize'=>1024 * 1024 * 10, // 10MB
                   'allowEmpty'=>false,
                   //'on'=>'import',
            ),
   		);
   	}

   	/**
   	 * Declares attribute labels.
   	 */
   	public function attributeLabels()
   	{
   		return array(
   			'upload_file'=>'Файл импорта',
   		);
   	}
}