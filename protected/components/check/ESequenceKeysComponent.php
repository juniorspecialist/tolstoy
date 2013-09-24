<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 05.09.13
 * Time: 17:45
 * To change this template use File | Settings | File Templates.
 */

/*
 * проверка на - Порядок следования ключей
 * … 1. разбивает текст на предложения
    2. делаем частотник каждого ключа из списка и каждого предложения ОТДЕЛЬНО
    3. находим те предложения, в которых встречается каждый ключевик
    4. если порядок следования предложений такой же как порядок следования ключей - всё ок
    5. если нет - выводим ошибку

    ===========================Использование и вызов кода==========================
    $keyList = array(
      'монтаж вентиляции и чиллеров',
      'монтаж классной вентиляции и интересных чиллеров',
      'отопления для дома'
    );

    $sequenceKeys = Yii::app()->sequenceKeys;
    $sequenceKeys->toleranceRange = 1;
    $res = $sequenceKeys->checkText($text1,$keyList);

    if($res===true){
        echo 'ok';
    }else{
        echo $res;
    }
 */

class ESequenceKeysComponent extends CApplicationComponent {

    // массив предложений из текста
    public $sentenceList = array();

    // массив предложений и составленного частотного словаря по каждому предложению
    public $sentenceListLemma = array();

    //частотный словарь для каждого ключевика составляем в формате - [ключевое слово номер один]=>array('ключь', 'слово',' номер','один')
    public $keyWordsListWithLemma = array();

    // список предложений в которых встречается все ключевые слова из ключевика
    public $proposalList = array();

    //допуск расстояния между ключевыми словами - указывает в проекте админом
    public $toleranceRange;

    public $error = false;
    public $errorText;

    /*
     * частотный словарь по предложениям из текста - храним в нём список слово-форм, по каждому предложению отдельно
     * [предложение номер первое]=>array('предложение','номер','первый');
     */
    private $frequencyDictionary = array();


    /*
     * составляем частотные словари как для самого текста с предложениями, так и для списка ключевиков
     */
    public function getLemmaFor($text='', $keyWordsList=''){

        //разбивает текст на предложения
        $this->sentenceList = explode('.', $text);

        // подключаем компонент для определения слово-форм отдельно для каждого предложения
        $sickness = Yii::app()->sickness;

        // для каждого предложения формируем список слово-форм
        foreach($this->sentenceList as $sentence){

            if(empty($sentence)){ continue;}

            $sickness->clearStopListText($sentence);

            $listLemmaWords = $sickness->getFrequencyDictionary();

            // формируем нормальный вид слово-форм для предложения из текста
            $this->sentenceListLemma[$sentence] =  ESequenceKeysComponent::indexIntoValueArray($listLemmaWords);
        }

        //делаем частотник каждого ключа из списка
        foreach($keyWordsList as $keyWord){

            //ключевое слово - разделяем на отдельные слова и по каждому слову - получаем слово-форму - ЛЕММУ
            $wordsList = explode(' ', $keyWord);

            $lemmaKeyWords = array();

            foreach($wordsList as $word){
                $sickness->clearStopListText($word);
                $lemma_key = $sickness->getFrequencyDictionary();

                //получаем значение - слово-формы для слова
                $slovoForm = array_keys($lemma_key);
                $lemmaKeyWords[] = $slovoForm[0];
            }

            $this->keyWordsListWithLemma[$keyWord] = $lemmaKeyWords;
        }

    }

    /*
     * запускаем проверку по тексту
     */
    public function checkText($text='', $keyWordsList=''){

        if(empty($this->toleranceRange)){
            return 'Не указан параметр допуска';
        }

        //формируем ЛЕММЫ для ключевиков и предложения
        $this->getLemmaFor($text='', $keyWordsList='');

        // производим сравнение вхождения ключевиков по предложениям
        $this->findProposal();

        if($this->error){
            return $this->errorText;
        }else{
            return true;
        }
    }

    /*
     * находим те предложения, в которых встречается каждый ключевик
     */
    public function findProposal(){
        //перебираем все предложения
        //$sentence - само предложение,  $listLemmaOfsentence - массив - частотный словарь по предложению
        foreach($this->sentenceListLemma as $sentence=>$listLemmaOfsentence){

            //$keyWord - ключевое слово,   $listkeyWordsWithLemma - частотный словарь по ключевому слову
            foreach($this->keyWordsListWithLemma as $keyWord=>$listkeyWordsWithLemma){

                // нашли ли мы все ключевики в предложении, если не находим хотя бы одного ключа - тогда пропускаем предложение
                $find_all_key_words = true;

                // список найденных позиций ключевого слова в списке частотника по предложению
                $positionList = array();

                //перебираем все слово-формы из частотного списка по ключевому слову и ищим вхождение по частотнику из предложения
                foreach($listkeyWordsWithLemma as $findWord){//$findWord -  ищем вхождение слова из ключевика в предложении из текста

                    // поиск позиции вхождения слова в предложении по ключевому-частотнику
                    $positionWord = array_search($findWord, $listLemmaOfsentence);

                    // не нашли вхождение из частотника - ключевика по предложению - значит пропускаем предложение
                    if(!$positionWord){ $find_all_key_words = false; break;}

                    //запоминаем найденную позицию слова в частотнике предложения, для анализа
                    $positionList[$findWord] = $positionWord;
                }

                //НАШЛИ ВСЕ ключевые слова в предложении
                if($find_all_key_words){

                    // анализируем позиции вхождения ключевых слов в предложении
                    $resultAnalyze = $this->analyzePositionWords($positionList);

                    if(!$resultAnalyze){
                        $this->error = true;
                        $this->errorText = $this->errorDesc($keyWord, $sentence);
                    }

                }else{// какое-то слово-слова не нашли в предложении из ключевика, пропускаем ключевик тогда
                    continue;
                }

            }
        }
    }

    /*
     * анализируем правильноть последовательности следования ключевых слов в найденном предложении
     */
    public function analyzePositionWords($positionList){

        $before_pos = '';
        foreach($positionList as $word=>$pos){

            $pos = intval($pos);

            if(empty($before_pos)){
                $before_pos = $pos+$this->toleranceRange+1;
                $delta = 0 ;
            }else{
                $delta = $pos-($before_pos+$this->toleranceRange);
            }

            // если расстояние между вхождениями словами из ключа больше, чем допустимый допуск
            if($delta>0){
                //echo $delta.'|more-'.$word.'<br>';
                return false;
            }
            $before_pos = $pos;
        }

        return true;
    }

    /*
     * формируем текст ошибки
     */
    public function errorDesc($keyWord, $sentence){
        return 'Для предложенного исходного ключа
                ('.$keyWord.')
                употребленного вами в предложении
                ('.$sentence.')
                отмечено некорректная последовательность употребления слов ключа в тексте по сравнению с исходным вариантом.
                Проверьте корректность употребления ключевого слова и перезапустите проверку. Напоминаем, что порядок следования слов должен совпадать с тем,
                который предложен Вам в исходном варианте. Вы можете изменять падеж и число слов в ключевых словах, а также «разбивать» слова конкретного ключевика,
                вставляя между ними не более ('.$this->toleranceRange.') слов.';
    }


    /*
     * преобразовываем индексы массива в его значения
     */
    static function indexIntoValueArray($data){
        $result = array();
        if(is_array($data)){
            foreach($data as $index=>$row){
                $result[] = $index;
            }

            return $result;
        }else{
            return $result;
        }
    }

}