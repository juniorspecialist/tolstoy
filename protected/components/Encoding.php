<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 07.08.13
 * Time: 17:03
 * To change this template use File | Settings | File Templates.
 */

/*
класс для определения кодировки текста и перекодирования его в кодировку UTF-8

*/
class EncodingException extends Exception { }

class Encoding {

    /*
     * определяем  случайно кодировка не ли UTF-8
     */
    static function is_utf8($string) {

        // From http://w3.org/International/questions/qa-forms-utf-8.html
        /*
        try {
            $result = preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
             )*$%xs', $string);
        } catch (Exception $e) {
            return false;
        }final{
            echo "First finally.\n";
        }*/

        /*

                $result = preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
             )*$%xs', $string);
                return $result;
        */
        return mb_check_encoding($string, 'UTF-8');
        /*
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )+%xs', $string);*/


        //return $result;

    } // function is_utf8

    /*
     * возвращаем текст в кодировке UTF-8
     */
    static function toUTF_8($text, $link=''){

        $possible_encodings = array('windows-1251', 'koi8-r', 'iso8859-5');

        $is_utf_8  = Encoding::is_utf8($text);
        // если у текста кодировка UTF-8, тогда перекодирование и дальнейшие варианты кодировок исключаем
        if($is_utf_8){
            file_put_contents('log/link='.time().'.txt',$link.'|code page=utf-8');
            return $text;
        }else{
            // текст не в  UTF-8 кодировке, определяем кодировку текста - долго и нудно ))
            $weights = array();
            $specters = array();
            //echo 'encode_page<br>';die();
            foreach ($possible_encodings as $encoding)
            {
                $weights[$encoding] = 0;
                $specters[$encoding] = require 'specters/'.$encoding.'.php';
            }

            if(preg_match_all("#(?<let>.{2})#",$text,$matches))
            {
                foreach($matches['let'] as $key)
                {
                    foreach ($possible_encodings as $encoding)
                    {
                        if (isset($specters[$encoding][$key]))
                        {
                            $weights[$encoding] += $specters[$encoding][$key];
                        }
                    }
                }
            }
            $sum_weight = array_sum($weights);

            $encode_page = ''; $max = 0;

            foreach ($weights as $encoding => $weight)
            {
                $weights[$encoding] = $weight / $sum_weight;
                if($max<$weight / $sum_weight){
                    $encode_page = $encoding;
                    $max = $weight / $sum_weight;
                }
            }

            file_put_contents('log/link='.time().'.txt',$link.'|code page='.$encode_page);

            return iconv($encode_page, 'utf-8//TRANSLIT',$text);
        }
    }
}