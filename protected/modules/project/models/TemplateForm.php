<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 24.10.12
 * Time: 12:15
 * To change this template use File | Settings | File Templates.
 */
// класс для обработки выделенных чекбоксов при создании задания копирайтору
class TemplateForm extends CFormModel{

    public $category_id;
    public $title;
    public $title_job;
    public $type_job;
    public $description;
    public $deadline;
    public $price_th;
    public $uniqueness;

    public function rules()
   	{
   		return array(
   			array('category_id,title,title_job,type_job,description,deadline,price_th,uniqueness', 'boolean'),
   		);
   	}


   	public function attributeLabels()
   	{
   		return array(
   			'category_id'=>'Использовать в шаблоне',
               'title'=>'Использовать в шаблоне',
               'title_job'=>'Использовать в шаблоне',
               'type_job'=>'Использовать в шаблоне',
               'description'=>'Использовать в шаблоне',
               'deadline'=>'Использовать в шаблоне',
               'price_th'=>'Использовать в шаблоне',
               'uniqueness'=>'Использовать в шаблоне',
   		);
   	}
}