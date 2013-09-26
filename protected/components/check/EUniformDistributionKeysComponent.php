<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 25.09.13
 * Time: 11:51
 * To change this template use File | Settings | File Templates.
 */

/*
 * компонент для определения равномерности распределения ключей
 *
 *
 * использование кода:
 * $keyList = array(
  'монтаж вентиляции и монтаж чиллеров',
  'монтаж классной вентиляции и интересных чиллеров',
  'отопления для дома'
);

$distributionKeys = Yii::app()->uniformDistributionKeys;
$distributionKeys->toleranceLimit = 16;// допустимая погрешность в большую или меньшу сторону - указывается админом в проекте
$res = $distributionKeys->checkText($text1,$keyList);

if($res===true){
    echo 'ok';
}else{
    echo $res;
}

 */

class EUniformDistributionKeysComponent extends CApplicationComponent {
    // массив предложений из текста
    public $sentenceList = array();

    // массив предложений и составленного частотного словаря по каждому предложению
    public $sentenceListLemma = array();

    //частотный словарь для каждого ключевика составляем в формате - [ключевое слово номер один]=>array('ключь', 'слово',' номер','один')
    public $keyWordsListWithLemma = array();

    // список предложений в которых встречается все ключевые слова из ключевика
    public $proposalList = array();


    //допуск настройка предельного допуска перекоса в ту или иную сторону - указывает в проекте админом
    public $toleranceLimit;

    public $error = false;
    public $errorText;

    /*
        * основной метод проверки текста, он запускаем все необходимые этапы
        */
    public function checkText($text='', $keyWordsList=''){

        //Проверка стартует только если в исходном задании более 1 ключа, так как если ключ 1 проверка смысла не имеет.
        if(sizeof($keyWordsList)==1 || empty($keyWordsList)){
            return true;
        }

        if(empty($this->toleranceLimit)){
            return 'Не указан параметр допуска';
        }

        //компонент для формирования частотников по предложениям и ключевикам
        $sequenceKeys = Yii::app()->sequenceKeys;

        //формируем словари слово-форм для предложений и ключевиков(если не передали эти данные из предыдущих проверок, тогда формируим их заново)
        if(empty($this->sentenceListLemma) || empty($this->keyWordsListWithLemma)){

            // формируем необходимые массивы с данными для дальнейшей проверки
            $sequenceKeys->getLemmaFor($text, $keyWordsList);

            $this->sentenceListLemma = $sequenceKeys->sentenceListLemma;
            $this->keyWordsListWithLemma  = $sequenceKeys->keyWordsListWithLemma ;
            $this->proposalList = $sequenceKeys->proposalList;
            $this->sentenceList = $sequenceKeys->sentenceList;
        }

        $this->analyzeText();

        if($this->error){
            return $this->errorText;
        }else{
            return true;
        }
    }

    /*
     * разбиваем текст на 2 части и анализируем по предложения - расположение ключевых словв частях текста
     */
    public function analyzeText(){

        //массив предложений первой части из текста
        $arr_part_1 = array();

        //массив предложений второй части из текста
        $arr_part_2 = array();

        //сколько предложений будем относить к первой части текста, а остальные - ко второй
        $index_part_1 = floor(sizeof($this->sentenceList)/2);

        //die(sizeof($this->sentenceList).'|part_1='.$index_part_1);

        //перебираем все предложения и разделяем текст на 2 части -кол-во предложений/2, остаток от деления прибавляется ко второй части
        //$sentence - само предложение,  $listLemmaOfsentence - массив - частотный словарь по предложению
        $count_sentence = 0;
        foreach($this->sentenceListLemma as $sentence=>$listLemmaOfsentence){

            $not_find_key_in_all_text = false;

            //$keyWord - ключевое слово,   $listkeyWordsWithLemma - частотный словарь по ключевому слову
            foreach($this->keyWordsListWithLemma as $keyWord=>$listkeyWordsWithLemma){

                // нашли ли мы все ключевики в предложении, если не находим хотя бы одного ключа - тогда пропускаем предложение
                $find_all_key_words = true;

                // список найденных позиций ключевого слова в списке частотника по предложению
                $positionList = array();

                //перебираем все слово-формы из частотного списка по ключевому слову и ищим вхождение по частотнику из предложения
                foreach($listkeyWordsWithLemma as $keyWordStarter=>$findWord){//$findWord -  ищем вхождение слова из ключевика в предложении из текста

                    // поиск позиции вхождения слова в предложении по ключевому-частотнику
                    $positionWord = array_search($findWord, $listLemmaOfsentence);

                    // не нашли вхождение из частотника - ключевика по предложению - значит пропускаем предложение
                    if(!$positionWord){ $find_all_key_words = false; break;}

                    //запоминаем найденную позицию слова в частотнике предложения, для анализа
                    $positionList[$findWord] = $positionWord;
                }

                //НАШЛИ ВСЕ ключевые слова в предложении
                if($find_all_key_words){

                    //определяем к какой части текста относится предложение
                    if($count_sentence<=$index_part_1){// предложение принадлежит к первой части - разделённого текста
                        //echo 'part_1:'.$sentence.'<br>';
                        $arr_part_1[] = $sentence;
                    }else{// предложение принадлежит ко второй части текста
                        //echo 'part_2:'.$sentence.'<br>';
                        $arr_part_2[] = $sentence;
                    }

                }else{// какое-то слово-слова не нашли в предложении из ключевика, пропускаем ключевик тогда
                    continue;
                }
            }

            $count_sentence++;
        }

        //подчсчитаем процент вхождения ключевиков для первой части текста и для второй

        $total_size = sizeof($arr_part_1)+sizeof($arr_part_2);

        $percent_part_1 = MyText::percentFromNumber($total_size, sizeof($arr_part_1));
        $percent_part_2 = MyText::percentFromNumber($total_size, sizeof($arr_part_2));

        //определяем превышен ли лимит в рамках процентов вхождения ключевиков+ допуска установленного админом в проекте
        if($percent_part_1>50 && $percent_part_1>(50+$this->toleranceLimit)){// Превышено вхождение ключевых слов в первой части текста
            $this->error = true;
            $this->errorText = $this->errorDesc(implode('. ',$arr_part_1), $percent_part_1, implode('. ',$arr_part_2), $percent_part_2);
        }
        //если слишком малое вхождение ключевых слов в первой части текста
        if($percent_part_1<50 && $percent_part_1<(50-$this->toleranceLimit)){
            $this->error = true;
            $this->errorText = $this->errorDesc(implode('. ',$arr_part_1), $percent_part_1, implode('. ',$arr_part_2), $percent_part_2);
        }

    }

    public function errorDesc($part_1, $percent_1, $part_2, $percent_2){
        return 'К сожалению, система обнаружила что ключевики распределены недостаточно равномерно. Система разделила текст на 2 части
                ('.$part_1.')
                в которой употреблено '.$percent_1.'% исходных ключей  и
                ('.$part_2.')
                в которой употреблено '.$percent_2.'% исходных ключей, что является неравномерным распределением исходных ключей по тексту.
                Пожалуйста, распределите ключи более равномерно между частями текста. Желательно стремится к тому, чтобы в каждой половине текста была использована примерно половина исходных ключей. ';
    }
}