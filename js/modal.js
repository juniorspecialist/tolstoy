//jQuery(function($){
$(document).ready(function(){

    // opening and closing modal is just adding a class to body or
    // removing it

    function openModal() {
        var el = 'body';

        // IE < 8
        if(document.all && !document.querySelector) {
            el = 'html';
        }

        $(el).addClass('lock');
    }

    function closeModal() {
        var el = 'body';

        // IE < 8
        if(document.all && !document.querySelector) {
            el = 'html';
        }

        $(el).removeClass('lock');
    }

    // bind event handlers to modal triggers
    $('body').on('click', '.trigger', function(e){
        //alert("sdfsdf");
        e.preventDefault();
        openModal();
    });

    // attach modal close handler
    $('#loading .close').on('click', function(e){
        e.preventDefault();
        closeModal();
    });

    // below isn't important (demo-specific things)
//    $('#loading .more-toggle').on('click', function(e){
//        e.stopPropagation();
//        $('#loading .more').toggle();
//    });
});
/**
 * Created with JetBrains PhpStorm.
 * User: Саня
 * Date: 21.01.13
 * Time: 11:09
 * To change this template use File | Settings | File Templates.
 */
