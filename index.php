<?php
error_reporting(E_ALL | E_STRICT) ;
ini_set('display_errors', 'On');
set_time_limit(0);
ini_set('memory_limit', '160M'); // 128 по-моему не хватало
ini_set('pcre.backtrack_limit', '5000000'); // 1000000 было мало
// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
require_once($yii);
$application = Yii::createWebApplication($config);
$text1 = 'Ротор циркуляционного насоса, находящийся внутри перекачиваемой жидкости, отделен от статора гильзой, изготовленной из нержавеющей стали. Бесшумная работа обеспечивается патрубками равного диаметра, которые расположены в одну линию. Чаще всего насосы устанавливаются на трубопроводах. Для минимизации тепловых потерь предусмотрена специальная оболочка, имеющая хорошие теплоизоляционные характеристики.
В одноступенчатых насосах конструкции «инлайн» спиральные корпуса обладают торцовым уплотнением вала, который не нуждается в техническом обслуживании. Процесс демонтажа головной части насоса, в которой имеется электродвигатель, верхняя часть и рабочее колесо, может осуществляться без необходимости использования трубопровода, что значительно облегчает работу по техобслуживанию и экономит время.
Под сдвоенным насосом понимается два отдельных насоса, находящихся в одном корпусе. Они отсоединены друг от друга при помощи подпружиненного перекидного шибера, который установлен со стороны нагнетания. При эксплуатации одного насоса эта деталь препятствует появлению обратного потока через неработающий насос.';
//$shingle = Yii::app()->shingle;
//$shingle->runCheck($text1);
//$parts = $shingle->get3Parts($text1);
//echo '<pre>';print_r($parts);
//die(Yii::getPathOfAlias("application.runtime").'/cookie.txt');

$unique = Yii::app()->uniqueCheck;
$unique->runCheck($text1);


//echo iconv('windows-1251', 'UTF-8',charset_x_win('ЮЕМПЧЕЛ'))."<br />";
//echo charset_x_win('юемпчел')."<br />";
//echo charset_x_win('человек')."<br />";
//echo charset_x_win('ЧЕЛОВЕК')."<br />";
//echo charset_x_win("С‡РµР»РѕРІРµРє")."<br />";
//echo charset_x_win("Р§Р•Р›РћР’Р•Рљ")."<br />";


//$str = "Р§Р•Р›РћР’Р•Рљ";

//$possible_encodings = array('windows-1251', 'koi8-r', 'iso8859-5');
/*
$data = 'Русская строка';
$encoding = 'iso8859-5';
$data = iconv('UTF-8', $encoding, 'Очень длинная русская строка');
*/

//$data = file_get_contents('http://koi8.pp.ru/');//http://www.php.su/
//$data = file_get_contents('http://webdesign.about.com/od/metatags/qt/meta-charset.htm');
//$data = file_get_contents('http://loco.ru');
//die($data);
//echo Encoding::toUTF_8($data);
