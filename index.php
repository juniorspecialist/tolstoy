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
$text1 = 'Наибольшей популярностью у потребителей пользуются циркуляционные насосы для отопления Grundfos UPS серии 100. Каждая модель данной серии обладает своими небольшими преимуществами, однако, так или иначе, во всех них оптимально сочетаются необходимые для нормальной работы параметры. Насосы этого типа обеспечивают циркуляцию жидкости в отопительных системах, системах обогрева полов и горячего водоснабжения. Они характеризуются малым потреблением электроэнергии, бесшумной работой, небольшим весом и габаритами. Кроме того, они удобны в использовании. Зависимо от потребности с помощью удобного переключателя можно  с легкостью установить нужную частоту вала двигателя.
Модель UPS 25-40 180 в комплекте имеет гайки и успешно устанавливается без специального инструмента. Устройство UPS 25-60 180 оборудовано обмоткой с повышенной устойчивостью к воздействиям тока. Насос UPS 25-80 180, который также укомплектован резьбовыми соединениями, дополнительно комплектуется керамическими подшипниками скольжения. Агрегат UPS 32-40 180 отличается практичностью и способностью работать автономно. Модель UPS 32-60 180 – это универсальный насос, потому что именно он обладает и высокой мощностью и надёжной защитой электродвигателя от перенапряжения. В устройстве UPS 32-80 180 имеется три фиксируемых скорости, обеспечивающие полноценную работу электродвигателя.';
//$shingle = Yii::app()->shingle;
//$shingle->runCheck($text1);
//$parts = $shingle->get3Parts($text1);
//echo '<pre>';print_r($parts);
//die(Yii::getPathOfAlias("application.runtime").'/cookie.txt');

//$unique = Yii::app()->uniqueCheck;$unique->runCheck($text1);

//echo phpinfo();

//$application->run();
$sickness = Yii::app()->sickness;
$res = $sickness->checkText($text1);
if($res===true){
    echo 'ok<br>';
}else{
    echo $res.'<br>';
}
echo '<br><br>';

//frequencyDictionary
$accuracyEntryKeys = Yii::app()->accuracyEntryKeys;
$res = $accuracyEntryKeys->checkText($text1);