<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 27.08.13
 * Time: 13:43
 * To change this template use File | Settings | File Templates.
 */


/*
 *
 * ===============Если эта проверка завершилась неуспешно, то следующая проверка «близости» ключей даже не стартует.================================
 *
 $sickness = Yii::app()->sickness;
$res = $sickness->checkText($text1);

if($res===true){
    die('ok');
}else{
    die($res);
}*
 *
 * алгорит -разбиваем текст на слова, убираем из списка слов - убираем слова по стоп-списку из файла
 * далее для каждого слова - формируем слово-форму
 * подсчитаем кол-во повторений для каждого слова, анализируем
 */
class ESicknessComponent extends CApplicationComponent {

    // путь к файлу со списком стоп-слов
    public $pathToStopWords;

    //настройка предельной «тошноты», задаваемая администратором
    public $limitSicknes;

    public $error = false;
    public $errorText = array();

    //частотный словарь по тексту - храним в нём список слово-форм, чтобы в последующих проверках не формировать частный словарь по тексту
    private $frequencyDictionary = array();

    /*
     * возрвщаем массив стоп-слов, для отфильтрования
     */
    public function getStopListWords(){

        if(file_exists($this->pathToStopWords)){

            $file = strtolower(file_get_contents($this->pathToStopWords));

            $file = str_replace(' ','', $file);

            $list = explode(PHP_EOL, $file);

            return $list;
        }else{
            return array();
        }
    }

    /*
     *из текста формируем массив слов
     * и массив слов фильтруем по списку стоп-слов
     */
    public function clearStopListText($text){

        // удаляем теги
        $text = strtoupper(strip_tags($text));

        //убираем двойные и более - пробелы
        $text = preg_replace("/(\s){2,}/",' ',$text);

        $text = str_replace(".", "", $text);
        $text = str_replace(",", "", $text);
        //$text = preg_replace("/\s+/", " ", $text);

        //стоп-лист слов
        $stopListWords = $this->getStopListWords();

        $wordsList = explode(' ', $text);

        // список слово-форм для слов из текста
        $slovoFormList = array();

        //общее кол-во слов - 100%
        $total = sizeof($wordsList);

        //очищаем список по стоп-листу
        foreach($wordsList as $word){
            if(!empty($word)){
                $word = strtolower(trim($word));
                // слово не нашли в списке стоп-слов, значит определяем для него слово-форму
                //if(!in_array(strtolower($word), $stopListWords, true)){
                if(array_search($word, $stopListWords)===false){
                    //если уже ранее НЕ получали слово-форму для данного слова
                    $cleanWordsList[] = $word;
                }
            }
        }

        //echo '<pre>'; print_r($stopListWords);

        $baseFormWordsList = $this->baseFormForWord($cleanWordsList);

        $this->frequencyDictionary = $baseFormWordsList;

        $error = false;

        // подсчитаем процентное соотношение
        foreach($baseFormWordsList as  $baseFormWord=>$countReplay){
            $percent = MyText::getPercentFromNumber($total, $countReplay);
            if($percent>$this->limitSicknes && !empty($baseFormWord)){
                $this->error = true;
                $this->errorText[] = $baseFormWord.'-'.$percent.'%';
            }
        }

        //echo '<pre>'; print_r($res);
    }

    /*
     * запускаем процесс проверки текста
     */
    public function checkText($text){

        $this->pathToStopWords = Yii::getPathOfAlias('application.components.check').'/stop_words_rus.txt';

        $this->clearStopListText($text);

        // нет ошибок
        if(!$this->error){
            return true;
        }else{
            //есть ошибки, выводим описание
            return $this->getTextError();
        }
    }

    /*
     * возвращаем базовую слово-форму для слова
     */
    public function baseFormForWord($words){

        error_reporting(E_ALL | E_STRICT);

        mb_internal_encoding("UTF-8");

        require_once(Yii::getPathOfAlias('application.components.check.src').'/common.php');

        $opts = array(
            'storage' => PHPMORPHY_STORAGE_FILE,
            'with_gramtab' => false,
            'predict_by_suffix' => true,
            'predict_by_db' => true
        );

        $dir = Yii::getPathOfAlias('application.components.check.dicts');


        try {
            $morphy = new phpMorphy($dir, 'ru_RU', $opts);
        } catch(phpMorphy_Exception $e) {
            die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
        }

        try {

            $w = $morphy->getAllFormsWithGramInfo($words, true);

            $table = array();
            foreach($words as $word) {
                $word = mb_strtolower($word);
                //echo 'word='.$words[$i].'<br>';
                $form = $morphy->lemmatize(mb_strtoupper($word));
                $sw = mb_strtolower($form[0]);
                if(isset($table[$sw])) {
                    $table[$sw] += 1;
                } else {
                    $table[$sw] = 1;
                }
            }
            asort($table);
            return $table;
        } catch(phpMorphy_Exception $e) {
            die('Error occured while text processing: ' . $e->getMessage());
        }

        return false;

    }

    /*
     * текст ошибки для данной проверки
     */
    public function getTextError(){
        //список ключей с указанием текущей частотности в % из анализатора, превышающие предельно допустимый предел частотности в %.
        return 'К сожалению, ключи:
                '.implode(", ", $this->errorText).'
                превысили предельно допустимую частоту вхождения в '.$this->limitSicknes.'%. Необходимо уменьшить кол-во вхождений приведенных слов в текст. Рекомендуем Вам:
                - убрать все вхождения указанных слов, где указанное слово не является частью исходного ключа и употреблено по Вашей инициативе
                - проверить дублирование исходных ключей. Напоминаем Вам, что необходимо однократное использование предложенных ключей.
                - увеличить объем исходного текста';
    }

}