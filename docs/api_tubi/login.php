<?php

//private $conn;
include 'connect.php';

mysqli_query($con,"SET NAMES 'utf8'");

if (isset($_POST['phone']) && isset($_POST['password'])) {

    // получение параметров для входа (телефон, пароль)
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // ищем наличие в BD логина(phone)
    $login = searchLogFromDB($con, $phone);
    if($login > 0){

        //получаем salt пароля
        $salt = getSaltFromDB($con, $phone); 
         
         if($salt){

            // хешируем и кодируем пароль 
            $hash = hashSSHA($password, $salt);
            // закодированный хэш пароля
            $encrypted_password = $hash["encrypted"]; 
            //echo 'salt: ' . $salt . '<br>';
            // получаем данные пользователя
            $user = getUserByPhoneAndPassword($con, $phone, $encrypted_password);
            if ($user != false) {
                // найдено применение        
                echo ($user);
            } else {
                // пользователь не найден с учетными данными
                //$response["error"] = TRUE;
                $error_info = "Учетные данные (телефон или пароль) для входа неверны. 
                                                Пожалуйста, попробуйте еще раз!";        
        
                echo "error" . "&nbsp" . $error_info ."<br>";
            }
         }
    }else {
        // пользователь не найден с учетными данными        
        $error_info = "Учетные данные (телефон или пароль) для входа неверны. 
                                        Пожалуйста, попробуйте еще раз!";        

        echo "error" . "&nbsp" . $error_info ."<br>";
    }    

} else {
    // необходимые параметры записи отсутствуют
    echo "error" . "&nbsp" . "Необходимые данные 
                    (номер телефона или пароль) отсутствуют!" ."<br>";
}

// получаем данные пользователя
function getUserByPhoneAndPassword($con, $phone, $encrypted_password){
    
    //ищем строку пользователя в DB
    $result = mysqli_query( $con, 
    "SELECT `unique_id`,`name`,`phone`,`counterparty_id`,`created_at`,`updated_at`
     FROM `t_user` 
     WHERE (`phone` = $phone AND encrypted_password = '$encrypted_password')" );
    $res=$result;
    $role = null;
    if(mysqli_num_rows($result ) > 0 ){
            //проверим к user привязана компания если нет то передадим не полные данные
        $row=mysqli_fetch_array($res);            
            $counterparty_id = $row[3];

        if($counterparty_id == 0){
            $unique_id = $row[0];
            $name = $row[1];
            $phone = $row[2];
            $abbreviation = null; 
            $counterparty = null;
            $taxpayer_id_number = null;
            $created_at = $row[4];
            $updated_at = $row[5];
            

        //получить роль пользователя или вернуть null
        $role = receiveUserRole($con, $unique_id);

        $user = $unique_id . "&nbsp" . $name . "&nbsp" . $phone . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" . 
                $taxpayer_id_number . "&nbsp" . $role .  "&nbsp" . $created_at . "&nbsp" . $updated_at . "<br>";
        }else{
        //получаем полные данные пользователя и компании       
        $query = "SELECT u.unique_id, 
                         u.name, 
                         u.phone, 
                         c.abbreviation,
                         c.counterparty,
                         c.taxpayer_id_number,
                         u.created_at, 
                         u.updated_at 
                    FROM t_user u
                        JOIN t_counterparty c ON c.counterparty_id = u.counterparty_id
                    WHERE u.phone = $phone AND u.encrypted_password = '$encrypted_password'";
        $result = mysqli_query ($con, $query) or die(mysql_error($link));
        $row = mysqli_fetch_array($result);
            $unique_id = $row[0];
            $name = $row[1];
            $phone = $row[2];
            $abbreviation = $row[3]; 
            $counterparty = $row[4];
            $taxpayer_id_number = $row[5];
            $created_at = $row[6];
            $updated_at = $row[7];

            //получить роль пользователя или вернуть null
        $role = receiveUserRole($con, $unique_id);

        $user = $unique_id . "&nbsp" . $name . "&nbsp" . $phone . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" . 
                $taxpayer_id_number . "&nbsp" . $role .  "&nbsp" . $created_at . "&nbsp" . $updated_at . "<br>";
       
      
    } 
    if ($user != false ){
        return $user;        
        }else {
            return false;
        } 
    }else {
        return false;
    }    
                 
}

//получить роль пользователя или вернуть null
function receiveUserRole($con, $unique_id){
    $query = "SELECT role    
                FROM t_user                    
                WHERE unique_id = '$unique_id'";// JOIN t_role r ON r.user_id = u.user_id
    $result = mysqli_query($con, $query) or die (mysqli_error($con));

    if(mysqli_num_rows($result ) > 0){
        $row = mysqli_fetch_array($result);
        return ($row[0]);
    }else {
        return null;
    }
}

// хешируем и кодируем пароль 
function hashSSHA($password, $salt){
    $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
    $hash = array("salt" => $salt, "encrypted" => $encrypted);
    return $hash;
}
// ищем наличие в BD логина(phone)
function searchLogFromDB($con, $phone){
$result = mysqli_query( $con, "SELECT `phone` FROM `t_user` WHERE (`phone` = $phone )" );
return mysqli_num_rows($result );

}
//получаем salt пароля
function getSaltFromDB($con, $phone){
    $query = "SELECT salt FROM t_user WHERE phone = $phone";
    $result = mysqli_query($con, $query) or die(mysql_error($link));
    $row = mysqli_fetch_array($result);
    $salt = $row[0];
    if($salt != false){
        return $salt;
    }
}
?>