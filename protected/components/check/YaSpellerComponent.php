<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 24.09.13
 * Time: 17:36
 * To change this template use File | Settings | File Templates.
 */


$url = 'http://speller.yandex.net/services/spellservice.json/checkText';
$data = 'text='.$text1;

$speller = Yii::app()->spelller;
//post($url, $header='', $data='', $proxy='', $proxy_login='', $proxy_pass=''){
$res = $speller->curlBehavior->post($url,'',  $data);//

class YaSpellerComponent extends CApplicationComponent{



}