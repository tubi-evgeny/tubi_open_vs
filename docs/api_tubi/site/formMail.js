
//файл запущен из .html кнопкой
$("#sendMail").on("click", function(){

    //получаем переменную из файла .html
    var list_str = $("#list_str").val();

    //$("#text").val("hello");
    

    $.ajax({
        //создаем url для отправки в .php
        url: 'mail.php',
        type: 'POST',
        cache: false,
        data: { 'list_str': list_str },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            $("#sendMail").prop("disabled", true);
        },
        success: function(data) {
            //функция выполнена ответ получен
            //ответ из .php
            alert(data);

            $("#php_ansver").val(data);

          /* if(!data){
                allert("Были ошибки, загрузка таблицы не выполнена!");
            }else{
                allert("Загрузка таблицы выполнена!");
                //$("#mailForm").trigger("reset");
            }*/
            $("#sendMail").prop("disabled", false);
        }

    });

});