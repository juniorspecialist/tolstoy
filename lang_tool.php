<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 25.09.13
 * Time: 17:28
 * To change this template use File | Settings | File Templates.
 */

error_reporting(E_ALL);
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $postText = trim(file_get_contents('php://input'));
//    $postText = html_entity_decode($postText, ENT_COMPAT, "UTF-8");

    $text1 = 'Потребность в экологически чистых продуктах, воде и воздухе, в последнее время встает особенно остро.
Также фирма производит монтаж вентиляции и монтаж чиллеров.
Учитывая тот факт, что деятельность человека накладывает определенный отпечаток практически на все жизненно важные потребляемые ресурсы, необходимость в их постоянной очистке растет с каждым днем.
Особенно остро вопрос экологии встаёт именно в крупных городах.
Там катастрофически не хватает не только чистой воды, но и воздуха.
Отопительные системы для отопления для офиса и дома.
Современные технологии и оборудование позволяют добиться нужных результатов в довольно короткие сроки, благодаря чему можно максимально очистить воздух помещений и совершенно спокойно позволить себе дышать,как говориться, полной грудью.
Сегодня довольно часто можно встретить объявления такого плана: «нужна бригада на вентиляцию и монтаж интересных чиллеров.';
    //$postText = $_POST['data'];

    //echo '<pre>'; print_r($_POST);
    $postText = 'language=ru-RU&text='.$text1;//.'&profile=true'
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1");
    curl_setopt($curl, CURLOPT_PORT, 8081);
    curl_setopt($curl, CURLOPT_SSLVERSION, 3);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postText);
    //теперь curl вернет нам ответ, а не выведет
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
    $result = curl_exec($curl);
    curl_close($curl);

echo 'result='.$result;

$xml = simplexml_load_string($result);
$json = json_encode($xml);
$array = simpleXMLToArray($xml);


//$t = $array['error'][0];
//echo '<pre>'; print_r($array['error']);


foreach($array['error'] as $errorWord){
    $findWord = mb_substr($text1, $errorWord['offset'], $errorWord['errorlength'],'UTF-8');
    echo $findWord.'<br>';
    echo '<pre>'; print_r($errorWord);
}


//} else {
//    print "Error: this proxy only supports POST";
//}

function simpleXMLToArray(SimpleXMLElement $xml,$attributesKey=null,$childrenKey=null,$valueKey=null){

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
        $value = simpleXMLToArray($child,$attributesKey, $childrenKey,$valueKey);
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