/**
 * Created with JetBrains PhpStorm.
 * User: Саня
 * Date: 23.01.13
 * Time: 9:50
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
    // отлавливаем клик по кнопке - ПРИНЯТИЕ задания редактором
    $('#cheking_link').click(function (){
        $('form#text-form').submit();
    });
});
