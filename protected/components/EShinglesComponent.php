<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Саня
 * Date: 17.07.13
 * Time: 10:36
 * To change this template use File | Settings | File Templates.
 */

/*
# Copyright (coffee) 2013, CFutures.ru and Ermola Dmitry, Email - list.eyes@gmail.com
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#
*/


/**
 * Component allows you to determine the percentage of similarity of texts,
 * the comparison is made with the help of technology "shingling" - @see http://en.wikipedia.org/wiki/W-shingling
  */
class EShinglesComponent extends CApplicationComponent
{
    /**
     * Sets the length (number of words) for one Shingle
     * @var int
     */
    public $shinglePrice = 4;

    /**
     * Component init
     * @throws CException
     */
    public function init()
    {
        $this->shinglePrice = intval($this->shinglePrice);
        if($this->shinglePrice <= 0){
            throw new CException(Yii::t('ext.ShinglesTextCompare', 'Wrong shingle price: {price}.', array(
                    '{price}' => $this->shinglePrice,
            )));
        }
        parent::init();
    }

    /**
     * Creates an array of "Shingles" from the entered text
     *
     * @param string $text Text
     * @param int $price length (number of words) for one Shingle
     * @return array
     */
    public function getShingle($text, $price = NULL)
    {
        $price = ($price === NULL) ? $this->shinglePrice : intval($price);

        if($price <= 0)
        {
            throw new CException(Yii::t('ext.ShinglesTextCompare', 'Wrong shingle price: {price}.', array(
                    '{price}' => $price,
            )));
        }

        $text = $this->cleanText($text);
        $elements = $this->terminateShortElement(explode(" ",$text));
        $shingles = array();

        for ($i = 0; $i < (count($elements) - $price + 1); $i++)
        {
            $shingle = array();
            for ($j = 0; $j < $price; $j++) {
                    $shingle[] = $elements[$i + $j];
            }
            $shingles[implode(' ', $shingle)] = 0;
        }

        //unset($elements);

        return array_keys($shingles);
    }

    /**
     * Removes the word length is shorter than four characters,
     * and changes to lowercase
     *
     * @param array $elements
     * @return array
     */
    private function terminateShortElement($elements)
    {
        $res = array();

        foreach($elements as $item)
        {
            $item = trim($item);
            $strlen = mb_strlen($item, 'UTF-8');
            if($strlen > 3)
            {
                $res[] = mb_strtolower($item, 'UTF-8');
            }
        }
        return $res;
    }

    /**
     * Remove from the text of the special characters, double spaces, line breaks and html tags.
     *
     * @param string $text
     * @return string text
     */
    public function cleanText($text)
    {
        $newText = strip_tags($text);
        $newText = preg_replace("[\,|\.|\'|\"|\\|\/|\:|\;|\(|\)|\{|\}|\$|\#|\*|\-|\+|\%|\=|\?|\!|\&|\^|\`|\~|\№]","",$newText);
        $newText = preg_replace("[\n|\t]"," ",$newText);
        $newText = preg_replace('/(\s\s+)/', ' ', trim($newText));

        //unset($text);

        return $newText;
    }

    /**
     * Compares text-based shingles, returns the percentage of similarity.
     *
     * @param string|array $firstShingles
     * @param string|array $secondShingles
     * @param int $price length (number of words) for one Shingle
     * @return float
     */
    public function checkText($firstShingles, $secondShingles, $price = NULL)
    {
        if(!is_array($firstShingles))
        {
            $firstShingles = $this->getShingle($firstShingles, $price);
        }

        if(!is_array($secondShingles))
        {
            $secondShingles = $this->getShingle($secondShingles, $price);
        }

        $intersect = array_intersect($firstShingles, $secondShingles);
        $merge = array_unique(array_merge($firstShingles, $secondShingles));
        $countMerge = count($merge);

        if($countMerge == 0)
        {
            return 100;
        }

        $diff = floatval(count($intersect)/$countMerge)*100;

        //unset($firstShingles);
        //unset($secondShingles);

        return $diff;
    }
    /*
     * разбиваем текст на части по 500 символов
     * из части берём 3 разных куска  размером в Х шинглов
     * чистим от всего, кроме точек и вбиваем в яндекс по 3 куска шинглов, чтобы в одном запросе было 3 куска через ИЛИ
     */
    public function get3Parts($text){

        $text = $this->cleanText($text);

        // массив частей по 500 примерно символов - делим по словам
        $parts_list = array();

        // разделяем текст на слова
        $exp_text = explode(' ', $text);

        // длина куска текста
        $len_part = 0;

        // часть текста разделенная по примерно 500 символов
        $part = '';

        foreach($exp_text as $j=>$word){

            $part.=$word.' ';

            // последнее слово в тексте
            if((sizeof($exp_text)-1)==$j){
                $parts_list[] = $part;
            }else{
                if(strlen($part)>1000){
                    // формируем массив кусков текста, нужной длины, по которым будем позднее строить списки ШИНГЛОВ
                    $parts_list[] = $part;
                    $part = '';
                }
            }
        }

        //var_dump($parts_list);
        // по каждой части выбранного текст проходимся и выбираем 3 разных куска текста
        $part_text_with_shingles = array();// массив кусков текста со списком шинглов к каждому куску текста
        // указанного размера ШИНГЛОВ
        foreach($parts_list as $part_text){
            $list_shingle = $this->getShingle($part_text, NULL);
            $part_text_with_shingles[] = array('part_text'=>$part_text, 'shingles_list'=>$list_shingle);
        }

        // список кусков текста+ по каждому куску - массив шинглов
        return $part_text_with_shingles;
    }

    /*
     * из массива шинглов - получаем 3 случайных значения
     * и возвращаем их в нужном формате, для запроса к Яндексу
     */
    public function getRandomShigles($shingle_list, $rnd_number=3){

        $rand_keys = array_rand($shingle_list, $rnd_number);

        $result_str = '';

        if($rnd_number==3){
            return '('.@$shingle_list[$rand_keys[0]].')|('.@$shingle_list[$rand_keys[1]].')|('.@$shingle_list[$rand_keys[2]].')';
        }
        if($rnd_number==2){
            return '('.@$shingle_list[$rand_keys[0]].')|('.@$shingle_list[$rand_keys[1]].')';
        }
        if($rnd_number==4){
            return '('.@$shingle_list[$rand_keys[0]].')|('.@$shingle_list[$rand_keys[1]].')|('.@$shingle_list[$rand_keys[2]].')|('.@$shingle_list[$rand_keys[3]].')';
        }
    }
}
//http://yandex.ru/yandsearch?text=%28%22%D0%BA%D0%B0%D0%BA%20%D1%82%D0%BE%20%D1%81%D0%BE%D0%BE%D0%B1%D1%89%D0%B0%D1%82%D1%8C%20%D0%BE%D0%B1%22%29%20%7C%20%28%22%D0%BA%D1%80%D0%B0%D1%85%22%20%22%D0%AD%D1%82%D0%BE%20%D0%B6%D0%B5%20%D0%BA%D0%B0%D1%81%D0%B0%D0%B5%D1%82%D1%81%D1%8F%22%29&lr=225&rd=0
//("шингл 2") | ("шингл 2")