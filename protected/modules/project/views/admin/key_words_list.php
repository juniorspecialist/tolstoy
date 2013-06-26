<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 16.11.12
 * Time: 8:36
 * To change this template use File | Settings | File Templates.
 */
$formElements = '';
$keyWords = '<table border="1">';

foreach($data as $i=>$row){
    //формируем элементы формы ключевики выводим сгруппировано
    if($row['import_var_id']==Yii::app()->params['key_words']){
        //$keyWords.=$row['import_var_value'].PHP_EOL;
        $del_link = CHtml::ajaxLink('Удалить',
            Yii::app()->createUrl('/project/admin/deletekeyword', array('id'=>$row['id'])),
            array( // ajaxOptions
              'type' => 'POST',
              'beforeSend' => "function( request )
               {
                 // Set up any pre-sending stuff like initializing progress indicators
               }",
              'success' => "function(data)
                {
                  // handle return data
                  //alert( data );
                  jQuery('#tbl_key_words').html(data);
                }",
              'data' => array('id' => $row['id'],'textId'=>$textId)
            ),
            array( // самое интересное
                //'href' => Yii::app()->createUrl( '/project/admin/deletekeyword' ),// подменяет ссылку на левую
                'class' => "sadfsadfsadclass" // добавляем какой-нить класс для оформления
            )
        );
        $input = CHtml::textField('ImportVarsValue['.$row['id'].']',$row['import_var_value'], array('style'=>'width:300px'));
        $keyWords.='<tr><td><div class="row"><label for="'.$row['title'].'">Ключевое слово</label>'.$input.$del_link.'</div></td></tr>';
    }else{
        $input = CHtml::textField('ImportVarsValue['.$row['id'].']',$row['import_var_value']);
        $formElements.='<div class="row"><label for="'.$row['title'].'">'.$row['title'].'</label>'.$input.'</div>';
    }
}

$keyWords.='</table> ';

echo $keyWords;