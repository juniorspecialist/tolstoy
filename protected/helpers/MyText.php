<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Администратор
 * Date: 24.04.12
 * Time: 21:25
 * To change this template use File | Settings | File Templates.
 */
class MyText
{
    static public function lenghtWords($str){

        $lenght = 150;// кол-во символов максимально
        $array_words = explode(' ',$str);

        $result = '';

        foreach($array_words as $word){

            if(strlen($result)>$lenght){
                return $result;
            }else{
                $result.=$word.' ';
            }
        }

        return $result;
    }

    /*
     * строка нужной длины со случайными символами
     * $symvols - какие символы будем использовать при формировании строки(только цифры, только буквы или все вместе)
     * $lenght - длина результирующей строки
     */
    static function rndString($lenght=4, $symvols = 'all'){

        if($symvols == 'all'){
            $source = 'qwertyuiopasdfghjklzxcvbnm1234567890';
        }

        if($symvols == 'int'){
            $source = '1234567890';
        }

        if($symvols == 'letters'){
            $source = 'qwertyuiopasdfghjklzxcvbnm';
        }

        $result = '';// результирующая строка

        for($i=0;$i<$lenght;$i++){
            $rnd = rand(0, strlen($source));
            $result.=$source[$rnd];
        }

        return $result;
    }
    /*
     * объединяем несколько массивов в один
     */            
    static function array_merge_recursive_new() {

            $arrays = func_get_args();
            $base = array_shift($arrays);

            foreach ($arrays as $array) {
                reset($base); //important
                while (list($key, $value) = @each($array)) {
                    if (is_array($value) && @is_array($base[$key])) {
                        $base[$key] = Text::array_merge_recursive_new($base[$key], $value);
                    } else {
                        $base[$key] = $value;
                    }
                }
            }

            return $base;
    }

    /*
     * подсчитаем процент от числа
     */
    static function getPercentFromNumber($number='', $percent=''){

        if(empty($number) || empty($percent)){
            return 'Empty value number OR percent';
        }

        $result = ($number*$percent)/100;

        return (int)$result;
    }

    /*
     * сколько процентов составляет число от общего кол-ва
     */
    static function percentFromNumber($ful_number, $part_number){

//        if(empty($ful_number) || empty($part_number)){
//            return 'Empty value number OR percent';
//        }

        $result = (100*$part_number)/$ful_number;

        return (int)$result;
    }

    /*
     * получаем первый индекс из массива
     */
    static function getFirstIndexOfArray($array){
        if(is_array($array)){
            foreach($array as $index=>$value){
                return $index;
            }
        }else{
            return '';
        }
    }

    /*
     * удаляем пустые элементы в массиве
     */
    static function delEmptyValInArray($array){

        $result_array = array();

        foreach($array as $arr_value){
            if(!empty($arr_value)){
                $result_array[] = $arr_value;
            }
        }

        return $result_array;
    }

    /*
     * преобразовываем XML документ в массив
     */
    static function simpleXMLToArray(SimpleXMLElement $xml,$attributesKey=null,$childrenKey=null,$valueKey=null){

        if($childrenKey && !is_string($childrenKey)){$childrenKey = '@children';}
        if($attributesKey && !is_string($attributesKey)){$attributesKey = '@attributes';}
        if($valueKey && !is_string($valueKey)){$valueKey = '@values';}

        $return = array();
        $name = $xml->getName();
        $_value = trim((string)$xml);
        if(!strlen($_value)){$_value = null;};

        if($_value!==null){
            if($valueKey){$return[$valueKey] = $_value;}
            else{$return = $_value;}
        }

        $children = array();
        $first = true;
        foreach($xml->children() as $elementName => $child){
            $value = MyText::simpleXMLToArray($child,$attributesKey, $childrenKey,$valueKey);
            if(isset($children[$elementName])){
                if(is_array($children[$elementName])){
                    if($first){
                        $temp = $children[$elementName];
                        unset($children[$elementName]);
                        $children[$elementName][] = $temp;
                        $first=false;
                    }
                    $children[$elementName][] = $value;
                }else{
                    $children[$elementName] = array($children[$elementName],$value);
                }
            }
            else{
                $children[$elementName] = $value;
            }
        }
        if($children){
            if($childrenKey){$return[$childrenKey] = $children;}
            else{$return = array_merge($return,$children);}
        }

        $attributes = array();
        foreach($xml->attributes() as $name=>$value){
            $attributes[$name] = trim($value);
        }
        if($attributes){
            if($attributesKey){$return[$attributesKey] = $attributes;}
            else{$return = array_merge($return, $attributes);}
        }

        return $return;
    }
}
