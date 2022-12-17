<?php

//private $conn;
include 'connect.php';

mysqli_query($con,"SET NAMES 'utf8'");



if (isset($_POST['name']) && isset($_POST['phone']) && isset($_POST['password'])){
                        //----получаем начальные данные
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];  

    //сделать все буквы маленькими
    $name = mb_strtolower($name);
                //---проверьте, существует ли пользователь с тем же 
                                    //номером телефона
    if ($db = isUserExisted($con, $phone)) {
                        // пользователь уже существовал
        
        echo "message" . "&nbsp" . "Этот пользователь уже существует " . $phone ."<br>";
        
    }else{
        //создайте нового пользователя
        $user = storeUser($con, $name, $phone, $password);
        if($user){
            echo $user;
        }else{
            //пользователя не удалось сохранить
            echo "error" . "&nbsp" . "Произошла неизвестная ошибка при регистрации!" ."<br>";
        }
        
    }
}else {
    echo "error" . "&nbsp" . "Необходимые данные 
                    (имя, номер телефона или пароль) отсутствуют!" ."<br>";
}
function storeUser($con, $name, $phone, $password){   

            //генерируем и проверяем на наличие в BD uid
    $uuid = myUniqIdReal($con);
    //$uuid = uniqid();
    $hash = hashSSHA($password); // хешируем пароль
    $encrypted_password = $hash["encrypted"]; // закодированный хэш пароля
    $salt = $hash["salt"]; // salt

    //echo 'phone Insert: ' . $phone . "<br>";

    $query = "INSERT INTO t_user (unique_id, name, phone, encrypted_password, salt)
                    VALUES('$uuid', '$name', '$phone', '$encrypted_password', '$salt')";
    $result = mysqli_query($con, $query) or die (mysql_error($link));

    //проверьте наличие успешной регистрации
    if($result){
        $query = "SELECT unique_id, name, phone, created_at, updated_at, role 
                                        FROM t_user WHERE phone = $phone";
        $result = mysqli_query ($con, $query) or die(mysql_error($link));
        $row = mysqli_fetch_array($result);
        $user = $row[0] . "&nbsp" . $row[1] . "&nbsp" . $row[2] . "&nbsp" . $row[3] 
                    . "&nbsp" . $row[4] . "&nbsp" . $row[5] . "<br>";
        return $user;
    }else {
        return false;
    }    
}
// генерируем соль и хешируем пароль
function hashSSHA($password){
    //генерируем соль
    $salt = sha1(rand());
    $salt = substr($salt, 0, 10);
    //хешируем и кодируем пароль
    $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
    $hash = array("salt" => $salt, "encrypted" => $encrypted);
    return $hash;
}
//генерируем и проверяем на наличие в BD uid
function myUniqIdReal($con){
    $flag = true;
    while($flag == true){
        $uuid = uniqid();
        //echo 'uuid: ' . $uuid . "<br>";
        $query = "SELECT unique_id FROM t_user WHERE unique_id = '$uuid'";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        //$row = mysqli_fetch_array($result);
        //echo 'row: ' . $row[0] . "<br>";
        if($row = mysqli_fetch_array($result)){
            //echo 'row true: ' . $row[0] . "<br>";
            $flag = true ;
        }else{
            //echo 'row false: ' . $row[0] . "<br>";
            $flag =false;
        }
    }
    return $uuid;
}
    //---проверьте, существует ли пользователь с тем же 
                                    //номером телефона
function isUserExisted($con, $phone){
    $query = "SELECT phone from t_user WHERE phone = $phone";
    $result = mysqli_query($con, $query) or die (mysql_error($link));
    if($row = mysqli_fetch_array($result)){
        return true;
    }else{
        return false;
    }
}   

   
    mysqli_close($con);

?>