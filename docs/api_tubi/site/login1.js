console.log("test login start 2 \n");



$("#check_user").on("click", function(){

    let phone_number = document.getElementById('phone_number').value.trim();
    let password = document.getElementById('password').value.trim();
    //alert("login hello");  
    if( password == ""){
        alert("Введите пароль");
        return;
    }
    if(phone_number == ""){
        alert("Введите номер телефона");
        return;
    }
    
    $.ajax({    
        //создаем url для отправки в .php
        url: '../login.php',
        type: 'POST',
        cache: false,
        data: {'phone':phone_number
                , 'password':password },
        dataType: 'html',
        beforeSend: function() {
            //ожидание пока функция выполняется кнопка не активна
            $("#check_user").prop("disabled", true);
            localStorage.setItem('user_info_json', "");
        },
        success: function(data) {
            console.log(data);
            //функция выполнена ответ получен

            $("#check_user").prop("disabled", false);

            let arr = data.split('<br>');            
            arr = arr[0].split('&nbsp');
            let user_info_json = "";
            
            if(arr[0] == "error"){
                alert(arr[1]);
            }else{
                console.table(arr);
                 user_info_json = JSON.stringify(arr);
                localStorage.setItem('user_info_json', user_info_json);
                //window.location.href = 'https://h102582557.nichost.ru/api_tubi/site/partner_PDF.html';
                window.location.href = localStorage.removeItem('ADMIN_PANEL_URL') + 'site/partner_PDF.html';
               
            }
            console.log(user_info_json);
        }    
    });
});
/*
let arr = data.split('<br>');            
            let user_info_arr = arr[0].split('&nbsp');
            
            if(user_info_arr[0] == "error"){
                alert(user_info_arr[1]);
            }else{
                console.table(user_info_arr);
                let user_info_json = JSON.stringify(user_info_arr);
                localStorage.setItem('user_info_json', user_info_json);
                window.location.href = 'https://h102582557.nichost.ru/test_tubi/site/partner_PDF.html';
            }
            console.log(user_info_arr);
*/