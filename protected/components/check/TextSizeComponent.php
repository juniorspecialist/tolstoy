<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 14.08.13
 * Time: 11:35
 * To change this template use File | Settings | File Templates.
 */

/*
 * Необходимо проверить кол-во текста на соответствие исходному требованию. Необходима настройка допуска,
 * позволяющая задавать % от заданного кол-ва знаков, регулирующий возможное отклонение копирайтера от заданной нормы.
 */
class TextSizeComponent extends CApplicationComponent {

    //настройка допуска,позволяющая задавать % от заданного кол-ва знаков, регулирующий возможное отклонение копирайтера от заданной нормы
    public $aberrance;

    // требование исходное по размеру файла, указывается в самом проекте
    public $requirement;


    /*
     * проверяем текст
     */
    public function checkText($text){

        //проверим указаны ли все данные для запуска проверки
        if(empty($this->aberrance)){
            echo 'Не указан допуск, позволяющий задавать % от заданного кол-ва знаков';
            return '';
        }

        if(empty($this->requirement)){
            echo 'Не указано требование по размеру текста, указывается в самом проекте';
            return '';
        }

        // определяем длину текста, без пробелов и HTML тегов
        $text = strip_tags($text);

        $sizeText = strlen(preg_replace( '#\s+#', ' ', $text));

        //минимально допустимая длина текста
        $aberrance_minus =  intval($this->requirement - MyText::getPercentFromNumber($this->requirement, $this->aberrance));

        //минимально допустимая длина текста
        $aberrance_plus = intval($this->requirement + MyText::getPercentFromNumber($this->requirement, $this->aberrance));

        //$sizeText = 100;

        if($sizeText<$aberrance_minus || $sizeText>$aberrance_plus){
            return $this->textError($sizeText);
        }else{
            return true;
        }
    }

    private function textError($sizeText){
        return
            'К сожалению, объем текста, предложенный Вами, не соответствует заданному нормативу.
        Исходное требование – '.$this->requirement.' знаков без пробелов, с допустимым отклонением от нормы в ту или иную сторону '.$this->aberrance.' знаков без пробелов.
        В то же время объем предложенного Вами текста - '.$sizeText.' знаков без пробелов.
        Приведите объем текста в соответствие с заданными требованиями и перезапустите проверку.';
    }

}