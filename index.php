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
$text1 = 'Потребность в экологически чистых продуктах, воде и воздухе, в последнее время встает особенно остро.
Учитывая тот факт, что деятельность человека накладывает определенный отпечаток практически на все жизненно важные потребляемые ресурсы, необходимость в их постоянной очистке растет с каждым днем.
Особенно остро вопрос экологии встает именно в крупных городах. Там катастрофически не хватает не только чистой воды, но и воздуха.
Современные технологии и оборудование позволяют добиться нужных результатов в довольно короткие сроки, благодаря чему можно максимально очистить воздух помещений и совершенно спокойно позволить себе дышать,
как говориться, полной грудью. Сегодня довольно часто можно встретить объявления такого плана: «нужна бригада на монтаж и она осуществляет вентиляцию и интересных чиллеров.';
//$shingle = Yii::app()->shingle;
//$shingle->runCheck($text1);
//$parts = $shingle->get3Parts($text1);
//echo '<pre>';print_r($parts);
//die(Yii::getPathOfAlias("application.runtime").'/cookie.txt');

//$unique = Yii::app()->uniqueCheck;$unique->runCheck($text1);

//echo phpinfo();



/*
$sickness = Yii::app()->sickness;
$res = $sickness->checkText($text1);
if($res===true){
    echo 'ok<br>';
}else{
    echo $res.'<br>';
}*/
//echo '<br><br>';
//

//die($res);
//echo '<pre>'; print_r(json_decode($res, true)); die();

$keyList = array(
  'монтаж вентиляции и монтаж чиллеров',
  'монтаж классной вентиляции и интересных чиллеров',
  'отопления для дома'
);

$distanceBetweenWords = Yii::app()->distanceBetweenWords;
$distanceBetweenWords->toleranceRange = 1;
$res = $distanceBetweenWords->checkText($text1,$keyList);

if($res===true){
    echo 'ok';
}else{
    echo $res;
}





//$application->run();