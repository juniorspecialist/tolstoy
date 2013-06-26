<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 06.12.12
 * Time: 17:57
 * To change this template use File | Settings | File Templates.
 */
class Export extends CFormModel{

    // тип экспорта, либо проект целиком=все тексты либо лишь ключев
    public $type = 'project';// по-умолчанию экспортируем проект целиком, все его тексты

    // ID проекта, который будем обрабатывать
    public $project;

    // если указано какое-то конкретное задание, экспортируем лишь это задание
    public $text;

    /*
     * на основании настроенной при добавлении проекта схемы
     * формируем массив соответств. структуры для экспорта в CSV-файл
     */
    public function preparation(){

        // результирующий массив, собираем данные в него и возвращаем
        $result = array();

        //получаем схему структуры по проекту
        $shems = Yii::app()->db->createCommand('SELECT import_var_id
                                                FROM {{import_vars_shema}}
                                                WHERE num_id="'.$this->project.'"
                                                    AND shema_type="1"
                                                    AND import_var_id!="'.Yii::app()->params['not_import'].'"
                                                ORDER BY num ASC'
                                                )
                                                ->queryAll();


        // получаем список заданий и уже по каждому заданию формируем массив с данными
        $texts = Yii::app()->db->createCommand('SELECT id FROM {{text}} WHERE {{text}}.project_id="'.$this->project.'" ORDER BY id')->queryAll();
        foreach($texts as $text){
            $str = array();

            // получаем список ключевиков по заданию
            $keyWords = Yii::app()->db->createCommand('SELECT import_var_value AS value
                                                    FROM {{text_data}}
                                                    WHERE import_var_id="'.Yii::app()->params['key_words'].'"
                                                        AND text_id="'.$text['id'].'"')
                                                    ->queryAll();

            // по каждому заданию формируем вывод данных в ПРАВИЛЬНОЙ последовательности - схеме
            foreach($shems as $shema){
                // нашли ключевое слово(столбец ключевых слов нашли)
                if($shema['import_var_id']==Yii::app()->params['key_words']){
                    $str[] = Export::toWin($keyWords[0]['value']);
                }else{
                    $sql = 'SELECT {{text_data}}.import_var_value AS value
                            FROM {{text_data}}
                            WHERE {{text_data}}.text_id="'.$text['id'].'"
                                AND {{text_data}}.import_var_id="'.$shema['import_var_id'].'"';
                    // формируем структуру результирующего массива данных
                    $data_text = Yii::app()->db->createCommand($sql)->queryRow();
                    $str[] = Export::toWin($data_text['value']);
                }
            }
            // добавляем массив с заполненными полями от копирайтора по заданию
            $result[] = $str;
            // теперь добавляем список ключевиков по той же схеме но лишь ключевки добавляем
            for($j=1;$j<sizeof($keyWords);$j++){// начали с 1, потому как первое ключевое слово УЖЕ записали в массив ранее
                $row = $keyWords[$j];
                $str = array();
                // по каждому заданию формируем вывод данных в ПРАВИЛЬНОЙ последовательности - схеме
                foreach($shems as $shema){
                    // нашли ключевое слово(столбец ключевых слов нашли)
                    if($shema['import_var_id']==Yii::app()->params['key_words']){
                        $str[] = Export::toWin($row['value']);
                    }else{
                        $str[] = '';
                    }
                }
                // после каждого добавления ключевика добавляем в общий массив данных
                $result[] = $str;
            }
            // добавим парочку строк пропуска между заданиями
            for($k=0;$k<2;$k++){
                $str = array();
                foreach($shems as $shema){
                    $str[] = '';
                }
                $result[] = $str;
            }
        }

        // возвращаем результирующий массив, с нужной структурой

        return $result;
    }

    static function toWin($str_utf_8){
        return iconv('UTF-8','windows-1251//IGNORE',$str_utf_8);
    }
    /*
     * создаём файл экспорта и записываем в нём полученный ранее массив в ввиде CSV файла
     * и возвращаем ссылку на адрес файла на диске
     * $data - массив массивов, т.е. каждый элемент маасива это массива $data[] = array("1"=>"sdfsdf","2"=>"ddddd")
     */
    public function saveToCSV($data){

        $name = time().'.csv';
        $name = 'project.csv';
        $path = Yii::getPathOfAlias('webroot.export').'/';

        $fp = fopen($path.$name, 'w');
        foreach ($data as $value) {
            fwrite($fp, implode(',',$value) . PHP_EOL);
        }
        fclose($fp);


        // ссылка на скачивание файла результата
        return $path.$name;
    }
}