<?php
//error_reporting(E_ALL | E_STRICT) ;
$start = microtime(true);
//ini_set('display_errors', 'On');
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
Также фирма производит монтаж вентиляции и монтаж чиллеров.
Учитывая тот факт, что деятельность человека накладывает определенный отпечаток практически на все жизненно важные потребляемые ресурсы, необходимость в их постоянной очистке растет с каждым днем.
Особенно остро вопрос экологии встает именно в крупных городах.
Там катастрофически не хватает не только чистой воды, но и воздуха.
Отопительные системы для отопления для офиса и дома.
Современные технологии и оборудование позволяют добиться нужных результатов в довольно короткие сроки, благодаря чему можно максимально очистить воздух помещений и совершенно спокойно позволить себе дышать,как говориться, полной грудью.
Сегодня довольно часто можно встретить объявления такого плана: «нужна бригада на вентиляцию и монтаж интересных чиллеров и кодеигнайтеров.';

$keyList = array(
  'монтаж вентиляции и монтаж чиллеров',
  'монтаж классной вентиляции и интересных чиллеров',
  'отопления для дома'
);

//$punctuation = Yii::app()->punctuation;
//$punctuation->serverStart();
//$punctuation->checkText($text1,array('чиллеры','кодеигнайтеры'));
$application->run();