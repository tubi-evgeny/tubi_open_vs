<?php
include 'connect.php';
include 'text.php';
include_once 'helper_classes.php';
include 'variable.php';

mysqli_query($con,"SET NAMES 'utf8'");


//авторизация пользователя по звонку
if(isset($_POST['authorization_to_call'])){     
    $user_phone = $_POST['phone'];     
    
    authorization_to_call($con, $user_phone);
}

function authorization_to_call($con, $user_phone){
    $body = file_get_contents("https://smsc.ru/sys/send.php?login=kent7755@ya.ru&psw=54Klh54Ij&phones=". $user_phone."&mes=code&call=1&fmt=3&cost=3"); 
    $json = json_decode($body);
    //код для проверки 4 цифры
    echo substr($json->code, -4);


   /* print_r($json); 
    echo "Четырехзначный код (последние 4 цифры номера, с которого мы позвоним пользователю): ".substr($json->code, -4).". ";
    echo "ID звонка: ".$json->id.". ";
    echo "Стоимость звонка: ".$json->cost." руб. ";*/


}

/*
//sms.ru не  всегда срабатывает дозвон и еще ограничения по колличеству звонков на один номер
function authorization_to_call($con){
    $body = file_get_contents("https://sms.ru/code/call?phone=79851386686&ip=".$_SERVER["REMOTE_ADDR"]."&api_id=70299A76-38A7-B3BC-37C4-5F44AFC67A65"); 
    $json = json_decode($body);
    print_r($json); 
}
*/


mysqli_close($con);
?>