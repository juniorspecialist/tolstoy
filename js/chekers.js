//run chekers for users of system
$(document).ready(function(){
    var count = 0;
    var step  = 10;
    var speed = 5000;
    function progress() {

        // проверка полей, чтобы были указаны все поля, перед отправкой на проверку
        var check = fields_is_empty();

        if(check){
            cool_msg('Ошибка при проверке полей', 'Необходимо заполнить все поля перед запуском проверок');
        }else{
            // показываем прогресс-бар
            $('#substratre').show();
            $('#errors_cheking').html('');
            $('#errors_cheking').hide();

            //запускаем цикл по всем полям из задания, запускаем проверки
            $('textarea[name^=ImportVarsValue]').each(function () {

                var readonly = $(this).attr("readonly");
                var contenteditable = $(this).parent().children('.redactor_editor').attr('contenteditable');

                // если хотя бы один из реквизитов недоступен, тогда не обрабатываем эти поля
                if(readonly=="readonly" || contenteditable=="contenteditable"){
                    // пропускаем текстовое поле, потому как оно заблокировано, а значит прошло все проверки
                }else{
                    //отправка POST запроса на проверку по полям
                    post($('#Text_project_id').val(),$('#Text_id').val(), $(this).attr("id"), $(this).val());
                }
            });

            var counter = 3;
            $('#amount').text(counter+'%');
            $('#progress').progressbar('option', 'value', counter);

            //запрашиваем результат проверок по полю
            get_result();
        }
    }

    // обработчик клика по ссылке - Запустить проверки
    $('#cheking_link').bind('click', function() {
        progress();
    });

    // опрашиваем удалённый сервер для получения результатов проверок
    function get_result(){

        // находим ID задания которое проверяем
        var text_id = $('#Text_id').val();

        //опрашиваем данные о результатах проверок
        $.ajax({
            url: '/project/copywriter/resultcheck',
            type: "POST",
            dataType: "json",
            data:'&text_id='+text_id,
            success: function(data) {
                //установим значение прогресс-баров, чтобы юзер видел степень выполнения проверок в процентах
                $('#amount').text(data.count+'%');
                $('#progress').progressbar('option', 'value', data.count);
                if(data.count < 100) {
                    setTimeout(get_result, 10000);// интервал опрашивания результатов 10 секунд
                }else{
                    $('#substratre').css("display", "none");
                    $('#substratre').hide();
                    $('#progress').progressbar('option', 'value', 0);
                    $('#amount').text(0+'%');
                    //показываем кнопку для отправки SUBMIT формы, если нет ошибок
                    if(data.errors==''){
                        // нет ошибок, сохраняем данные формы
                        $('#text-form').submit();
                    }else{//есть ошибки при проверках - выводим ИХ на экран

                        //блокируем поля, которые прошли все проверки
                        disabled_fields();

                        //отображаем ошибки, которые появились в ходе проверок
                        $('#errors_cheking').html(data.errors);
                        $('#errors_cheking').show();
                    }
                }
            }
        });
    }

    // отправляем первоначальные данные для создания очереди проверок
    function post(project_id ,text_id, field_id, field_value){
        $.ajax({
            url: '/project/copywriter/check',
            type: "POST",
            dataType: "json",
            data:'text_id='+text_id+'&field_id='+field_id+'&field_value='+field_value,
            success: function(data) {

            }
        });
    }

    // disabled fields, котор. прошли упешно проверку
    function disabled_fields(){
        // перебираем все поля, которые пользователь может заполнять
        $('textarea[name^=ImportVarsValue]').each(function () {
            if($(this).val()!=''){
                $.ajax({
                    url: '/project/copywriter/disabledfields',
                    type: "POST",
                    dataType: "json",
                    cache: false,
                    data:"field="+this.name+"&project_id="+$('#Text_project_id').val()+'&text_id='+$('#Text_id').val()+'&id='+this.id,
                    success: function(data) {
                        // если поле прошло все проверки и всё ОК, делаем его недоступным
                        if(data.result!=''){
                            $('#'+data.id).attr("readonly",true);
                            $('#'+data.id).parent().children('.redactor_editor').attr('contenteditable', false);
                        }
                    }
                });
            }
        });
    }

    // инициализация ВИЗИ_ВИГ редактора
    $('.redactor').redactor({ lang: 'ru' });

    // все поля должны быть заполнены, перед запуском проверок
    function fields_is_empty(){
        var error = false;
        // перебираем все поля, которые пользователь может заполнять
        $('textarea[name^=ImportVarsValue]').each(function () {
            if(this.value=='' || this.value==null){
                error = true;
            }
        });

        return error;
    }

    // поля прошедшие ВСЕ проверки успешно - делаем их недоступными для редактирования
    disabled_fields();

    //сообщения об ошибке или успешности действия
    function cool_msg(title, text){
        $.pnotify.defaults.history = false;
        $.pnotify({
            title: title,
            text: text,
            type: 'error',//notice
            shadow: false
        });
    }

});