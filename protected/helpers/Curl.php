<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 18.12.12
 * Time: 13:03
 * To change this template use File | Settings | File Templates.
 */
class Curl{

    // записывать ли при ошибках, данные в лог ошибок или нет
    public $write_log = false;

    public $url; // URL адрес куда будем отправлять запрос
    public $data = array(); // массив даных котор. будем отправлять
    public $text_id; // ID задания по которому отправляем запрос на проверку
    public $field_id; // ID поля из таблицы import_vars
    public $type_check; // тип проверки
    public $key_words; // ключевики, через запятую
    public $text; // текстовое значение поля, из "import_vars"

    // параметры котор. будут передаваться при конкретных проверках, не по всем проверкам
    //при проверке на кол-во текста должен учитываться допуск,
    public $dopysk;//значение в процентах от заданого кол-ва знаков
    public $total_num_char;//кол-во символов в проекта

    //при проверке на уникальность:
    public $unique;//доп. параметры - минимально допустимая уникальность текста, в процентах, размер шингла

    // при проверке на плоность вхождения ключей:
    public $sickness; //ТОШНОТА - будет параметр - предельно допустимый процент вхождения ключа

    //при проверке на расстояние на между ключевиками:
    public $tolerance; //допуск расстояния(вручную добавленный в проекте админом)

    public $intelligence; // список сведений из задания, через запятую

    static function is_empty($value, $name){
        if(empty($value)){
            echo $name.' is empty'; die();
        }
    }

    // инициализация параметров для отправки запроса
    function __construct($url='', $textID='', $import_var_id ='', $type_check='', $key_words='', $text=''){

        // если не передали URL напрямую смотрим в гллобальных настройках
        if(empty($url)){
            if(!empty(Yii::app()->params['cheking_url'])){
                $url = Yii::app()->params['cheking_url'];
            }
        }
        // инициализация переменных класса, для формирования массива на отправку
        $this->url = $url;
        $this->text_id = $textID;
        $this->field_id = $import_var_id;
        $this->type_check = $type_check;
        $this->key_words = $key_words;
        $this->text = $text;
    }

    /*
     * отправка POST запроса с данными
     */
    public function post($array_data=''){
        if( $curl = curl_init() ) {

            // не указали данные для отправки, формируем ВСЕ данные
            if(empty($array_data)){
                $array_data = $this->create_data();
            }

            //уcтанавливаем урл, к которому обратимся
            curl_setopt($curl, CURLOPT_URL, $this->url);
            //включаем вывод заголовков
            curl_setopt($curl, CURLOPT_HEADER, 0);
            //передаем данные по методу post
            curl_setopt($curl, CURLOPT_POST, 1);
            //теперь curl вернет нам ответ, а не выведет
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            //переменные, которые будут переданные по методу post
            curl_setopt($curl, CURLOPT_POSTFIELDS, 'data='.json_encode($array_data));
            //я не скрипт, я браузер опера
            curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');
            $res = curl_exec($curl);

            //проверяем, если ошибка, то получаем номер и сообщение
            if(!$res){
                $result = curl_error($curl).'('.curl_errno($curl).')';
            }else{
                $result = json_decode($res, true);
            }

            curl_close($curl);

            return $result;
        }else{
            die('Возможно CURL не установлен на сервере');
        }
    }

    /*
     * формируем правильную структуру данных для отправки
     */
    public function create_data(){
        return array(
            'type_check'=>$this->type_check,// тип проверки
            'field_id'=>$this->field_id,// ID поля по котор. запустили проверку
            'text_id'=>$this->text_id,// ID задания
            'text'=>$this->text,// текст который проверяем
            'key_words'=>$this->key_words,// список ключевиков, через запятую
            'dopysk'=>$this->dopysk,//при проверке на кол-во текста должен учитываться допуск,значение в процентах от заданого кол-ва знаков
            'total_num_char'=>$this->total_num_char,// общее кол-во символов в проекте
            'unique'=>$this->unique,// уникальность
            'sickness'=>$this->sickness,// тошнота
            'tolerance'=>$this->tolerance,///при проверке на расстояние на между ключевиками: допуск
            'intelligence'=>$this->intelligence,// список сведений, через запятую
        );
    }
}