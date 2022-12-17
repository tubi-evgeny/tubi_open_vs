<?php
	 include 'connect.php';
     include 'text.php';
	 include_once 'helper_classes.php';
     include 'variable.php';

	
	 
     mysqli_query($con,"SET NAMES 'utf8'");
   
     //получить список контрагентов
    if(isset($_GET['receive_counterparty_list'])){ 
   
       
	    receive_counterparty_list($con);
	
    }
    //получить список сгенерированных кодов этого агента
     else if(isset($_GET['receive_code_list'])){ 
        $agent_user_uid = $_GET['agent_user_uid'];

        //найти user_id
        $agent_user_id=checkUserID($con, $agent_user_uid);
       
	    receive_code_list($con, $agent_user_id);	
    }
    //сгенрировать новый код
    else if(isset($_GET['generate_new_code'])){ 
        $agent_user_uid = $_GET['agent_user_uid'];

        //найти user_id
        $agent_user_id=checkUserID($con, $agent_user_uid);
       
	    generate_new_code($con, $agent_user_id);	
    }
    //активировать код
    else if(isset($_GET['activate_code'])){ 
        $code_id = $_GET['code_id'];
       
	    activate_code($con, $code_id);	
    }
    //проверить введенный код существует, свободен
    else if(isset($_GET['check_code'])){ 
        $code = $_GET['code'];
       
	    check_code($con, $code);	
    }
    //закрыть код регистрации (использован)
    else if(isset($_GET['close_code'])){ 
        $code = $_GET['code'];
        $user_uid = $_GET['user_uid'];

        //найти user_id
        $user_id=checkUserID($con, $user_uid);

        //получить текущее время
        $time_millis = (float)sprintf('%.0f', (microtime(true) * 1000));

       /* list($milsec, $sec) = explode(' ', microtime());
        $time_millis = (float)sprintf('%.0f', (floatval($milsec) + floatval($sec)) * 1000);*/
       
	    close_code($con, $code, $user_id, $time_millis);	
    }

    //закрыть код регистрации (использован)
    function close_code($con, $code, $user_id, $time_millis){
        $query="UPDATE `t_codes_partner_activation` 
                    SET `used_user_id`='$user_id',`activation_millis`='$time_millis',`activated`='1',`code_used`='1'
                    WHERE `code`='$code'";
        mysqli_query($con, $query) or die(mysqli_error($con));
    }

    //проверить введенный код существует, свободен
    function check_code($con, $code){
        $query="SELECT `code_id`FROM `t_codes_partner_activation` WHERE `code`='$code' AND `activated`='0'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
            if(mysqli_num_rows($result) > 0){
            
                echo "RESULT_OK";
            }else {
                echo "ERROR";
            }
    }
    
    //активировать код
    function activate_code($con, $code_id){
        $query="UPDATE `t_codes_partner_activation` SET `code_used`='1' 
                    WHERE `code_id`='$code_id'";
        mysqli_query($con, $query) or die(mysqli_error($con));
    }

    //сгенрировать новый код
    function generate_new_code($con, $agent_user_id){
        //запустить цикл генерации кода
        $code_flag = true;

        while($code_flag){
            //сгенерировать число
            $new_code = rand ( 1000 , 9999 );

            //найти в таблице дубликат
            $query="SELECT `code_id` FROM `t_codes_partner_activation` WHERE  `code`='$new_code'";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));
            if(mysqli_num_rows($result) == 0){
            
                $code_flag = false;
            }
        }       

        //дубликат не найден, записываем 
        $query="INSERT INTO `t_codes_partner_activation` ( `code`, `create_user_id`) 
                                                VALUES ('$new_code','$agent_user_id')";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            echo "RESULT_OK";
        }
    }

    //получить список сгенерированных кодов этого агента
    function receive_code_list($con, $agent_user_id){

        $query="SELECT `code_id`, `code`, `used_user_id`, `creation_time`, `activation_millis`, `code_used` 
                        FROM `t_codes_partner_activation` WHERE `create_user_id`='$agent_user_id' ORDER BY `code_id` DESC LIMIT 50";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){ 
                $code_id=$row[0]; 
                $code=$row[1];                
                $used_user_id=$row[2];
                $creation_time=$row[3];
                $activation_millis=$row[4];
                $code_used=$row[5];

	            $creation_millis = (strtotime($creation_time) * 1000);
    
                echo $code_id."&nbsp".$code."&nbsp".$used_user_id."&nbsp".$creation_millis."&nbsp"
                    .$activation_millis."&nbsp".$code_used."<br>";
            }
        }else{
            echo "messege"."&nbsp".$GLOBALS['data_is_not'];
        }
    }

    //получить список контрагентов
    function receive_counterparty_list($con){
        $query="SELECT `counterparty_id` FROM `t_counterparty` WHERE 1";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){ 
                $counterparty_id=$row[0]; 
                //получить данные(информацию) о компании
                $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
                $abbreviation = $companyInfoList['abbreviation'];
                $counterparty = $companyInfoList['counterparty'];
                $taxpayer_id_number = $companyInfoList['taxpayer_id_number'];
                $companyInfoString = $companyInfoList['companyInfoString'];
    
                echo $counterparty_id."&nbsp".$abbreviation."&nbsp".$counterparty."&nbsp".$taxpayer_id_number."&nbsp".$companyInfoString."<br>";
            }
        }else{
            echo "messege"."&nbsp".$GLOBALS['data_is_not'];
        }
        
    }





    mysqli_close($con);
?>