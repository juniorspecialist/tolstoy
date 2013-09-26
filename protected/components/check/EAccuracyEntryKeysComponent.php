<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 29.08.13
 * Time: 11:24
 * To change this template use File | Settings | File Templates.
 */

/*
 * Пример вызова и работы с компонентом
 * $keyList - массив ключевых слов(возможен вариант, что в одном ключевике будет несколько слов, обработка это варианта предусмотрена)
 * $keyList = array(
  'потребительские насосы купить',
  'насосы купить в москве',
  'отопления для дома'
);
$accuracyEntryKeys = Yii::app()->accuracyEntryKeys;
$res = $accuracyEntryKeys->checkText($text1,$keyList);
 */

class EAccuracyEntryKeysComponent extends CApplicationComponent {

    //частотный словарь по тексту - храним в нём список слово-форм, чтобы в последующих проверках не формировать частный словарь по тексту
    private $frequencyDictionary = array();

    public $error = false;
    public $error_text;

    //частотный словарь всего массива ключей
    public $frequencyDictionaryKeys = array();

    /*
     * т.е. фактически мы должны взять частотный словарь всего массива ключей и удостовериться, что все леммы из массива ключей найдены в частотном словаре анализируемого текста.
     * Если эта проверка завершилась неуспешно, то следующая проверка «близости» ключей даже не стартует.
     * $keyList - массив ключей, по которому мы составим частотный словарь и будем искать все значения в частотном массиве ключей по тексту
     */
    public function checkText($text='', $keyList=''){

        if(empty($text)){ return 'Не указан текст для проверки(Точность вхождения ключей по заданному тексту)';}

        if(empty($keyList)){ return 'Не указан массив ключей(Точность вхождения ключей по заданному тексту)';}

        // получим из предыдущей проверки список слово-форм по проверяемому тексту
        $sickness = Yii::app()->sickness;

        //формируем массив слово-форм для списка ключей
        //getOneWordInArray() - преобразовываем массив ключей к массиву слов
        $this->frequencyDictionaryKeys = $sickness->baseFormForWord($this->getOneWordInArray($keyList), false);

        // формируем массив слово-форм для текста
        $this->frequencyDictionary = $sickness->getFrequencyDictionary();

        // если предыдущая проверка не проводилась или просто результат пустой, тогда формируем заново-список слово-форм для текста, используя методы из компонента - "Тошнота"
        if(empty($this->frequencyDictionary)){
            $sickness->clearStopListText($text);
            $this->frequencyDictionary = $sickness->getFrequencyDictionary();
        }

        //сравнение списка слово-форм из массива ключей со списком созданного на основании текста
        //сами слово-формы хранятся в индексах массивов, цикл по списку ключей - ищим вхождение в массиве слово-форм на основании текста
        $notFindWords = array();// список слов, которые мы не нашли в тексте
        foreach($this->frequencyDictionaryKeys as $lemmaFromKey=>$counter){
            if (!array_key_exists($lemmaFromKey, $this->frequencyDictionary)) {
                $this->error = true;
                $notFindWords[] = $lemmaFromKey;
            }
        }

        if($this->error){
            return $this->getError($notFindWords);
        }else{
            return true;
        }

    }

    public function getError($notFindWords){

        return 'К сожалению, анализ предложенного Вами текста показал, что не все  исходные ключи употреблены корректно. Для некоторых ключевых слов не найдено вхождение в тексте:
                '.implode(",",$notFindWords).'.
                Проверьте корректность вхождения предложенного ключевого слова в текст и перезапустите проверку.
            ';
    }

    /*
     * формируем из массива ключей - массив слов
     * т.е. в одном ключе может быть несколько слов, необходимо, чтобы в одном элементе массива было - одно слово
     */
    public function getOneWordInArray($arrayKeyWords = ''){

        $resultKeyWords = array();

        if(!empty($arrayKeyWords)){
            // пробегаемся по массиву ключевиков
            foreach($arrayKeyWords as $keyWord){
                //с каждого ключевика - формируем - массив слов
                $listWords = explode(' ', $keyWord);
                foreach($listWords as $word){
                    $resultKeyWords[] = $word;
                }
            }

            return $resultKeyWords;
        }else{
            return '';
        }
    }

}