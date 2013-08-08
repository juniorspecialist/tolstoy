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

}
