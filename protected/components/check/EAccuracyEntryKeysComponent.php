<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 29.08.13
 * Time: 11:24
 * To change this template use File | Settings | File Templates.
 */

class EAccuracyEntryKeysComponent extends CApplicationComponent {

    //частотный словарь по тексту - храним в нём список слово-форм, чтобы в последующих проверках не формировать частный словарь по тексту
    private $frequencyDictionary = array();

    /*
     * т.е. фактически мы должны взять частотный словарь всего массива ключей и удостовериться, что все леммы из массива ключей найдены в частотном словаре анализируемого текста.
     * Если эта проверка завершилась неуспешно, то следующая проверка «близости» ключей даже не стартует.
     */
    public function checkText($text){

        // получим из предыдущей проверки список слово-форм по проверяемому тексту
        $sickness = Yii::app()->sickness;

        $this->frequencyDictionary = $sickness->getFrequencyDictionary();

        // если предыдущая проверка не проводилась или просто результат пустой, тогда формируем заново- список слово-форм для текста, используя методы из компонента - "Тошнота"
        if(empty($this->frequencyDictionary)){

        }
    }
}