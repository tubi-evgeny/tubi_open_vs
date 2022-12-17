<?php

//echo "test" . "<br>";
    include 'connect.php';
    include 'text.php';
    include_once 'helper_classes.php';

    mysqli_query($con,"SET NAMES 'utf8'");

    //заменить дату
    //совместные закупки, открытые заказы активировать
    //удалить не активные заказы(клиент не заказал)
    //заказы которые клиент удалил после активации сделать выполнен(executed)
    //сделай все вчерашние заказы от партнера к поставщику активными 
    //сделать все вчерашние заказы активными

    //openJointBuyOrderMakeActive
    //delete_dont_active_order
    //updateOrderDeletedToExecuted
    //make_every_orders_partner_yesterday_active
       
    date_default_timezone_set("Asia/Tbilisi");
    $new_month = date("m");
    $new_today = date("d");
    $new_time = date("G");
    $month;
    $today;
    $time = 3;
                                                            //--------показать month из таблицы
    $query = "SELECT info FROM t_general_info WHERE general_info = 'date_month'";
    $result = mysqli_query($con, $query) or die (mysql_error($link));
    $row = mysqli_fetch_array($result);
    $month = $row[0];
    
    if($month != $new_month){
        $today = 0;
                                                            //-----изменить месяц в таблице на новый
                                                            
        $query = "UPDATE t_general_info SET info = $new_month WHERE general_info = 'date_month' ";
        $result = mysqli_query($con, $query) or die (mysql_error($link));                                                    
                                                            
                                                            
                                                            
    }else{                                                      //--------показать дату из таблицы
        $query = "SELECT info FROM t_general_info WHERE general_info = 'date_day'";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        $row = mysqli_fetch_array($result);
        $today = $row[0];
    }
        //если дата дольше чем дата в таблице то заменить дату
        // и выполнить сккрипты
                                                    
    if($new_today > $today and $new_time > $time ){
            //заменить дату
        $query = "UPDATE t_general_info SET info = $new_today WHERE general_info = 'date_day' ";
        $result = mysqli_query($con, $query) or die (mysql_error($link));

            //совместные закупки, открытые заказы активировать
        openJointBuyOrderMakeActive($con);

        //удалить не активные заказы(клиент не заказал)
        delete_dont_active_order($con);
        //заказы которые клиент удалил после активации сделать выполнен(executed)
        updateOrderDeletedToExecuted($con);

        //сделай все вчерашние заказы от партнера к поставщику активными 
        //сделать все вчерашние заказы активными
        make_every_orders_partner_yesterday_active($con);

        //заявки на совместную закупку не набрали за 4 дня заказ, пора удалить
        deleteJointBuyOrWaitingLongTime($con);
        
    }       
    
//заявки на совместную закупку не набрали за 4 дня заказ, пора удалить
function deleteJointBuyOrWaitingLongTime($con){
    $today_millis = floor(microtime(true) * 1000);
    //получить время открытия заявки для проверки на долгий срок(удалить)
    $query="SELECT `joint_buy_id`,`created_at` FROM `t_joint_buy` 
                    WHERE `active`='0' and `join_buy_delete`='0' and `closed`='0'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
    while($row = mysqli_fetch_array($result)){ 
        $joint_buy_id = $row[0];
        $created_at = $row[1];

        $old_time_millis=floor(strtotime($created_at)* 1000);
        $time_limit= 4 * 24*60*60*1000;
        //если у заявки прошло времени 4 дня то закрыть эту заявку
        if($today < ($old_time_millis + $time_limit)){
            $query="UPDATE `t_joint_buy` SET`closed`='1' WHERE `joint_buy_id`='$joint_buy_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }
    }
}
//совместные закупки, открытые заказы активировать
function openJointBuyOrderMakeActive($con){
    //получить открытые совместные заказы
    $query="SELECT `order_id`, `warehouse_id`, `user_id`, `counterparty_id`, `category_in_order`, `get_order_date_millis`, `get_order_date`, `date_order_start`, `up_user_id`, `date_order_finish` 
                FROM `t_order` WHERE `order_active`='0' and `joint_buy`='1'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
    while($row = mysqli_fetch_array($result)){ 
        $order_id = $row[0];
        $partner_warehouse_id = $row[1];
        $buyer_counterparty_id = $row[2];

        //активировать заказ, изменить статус заказа (заказ отправлен в обработку) order_active = 1 и указать склад выдачи товара заказчику
        $result=chengeOrderActive_001($con,$order_id, $partner_warehouse_id, $buyer_counterparty_id);//$getOrderMillis, 
        if($result){
            // echo "RESULT_OK" . "&nbsp" . "<br>";        

            //создать кллюч к документам (invoice_key) и сделать записи в t_warehouse_inventory_in_out 
            make_invoice_key($con, $order_id, $partner_warehouse_id, $buyer_counterparty_id);
        
            //сгенерировать заказ от склад партнера для склад поставщика
            generation_order_to_provider($con, $order_id);

            //проверить если этот user заказывает первый раз то передать заказ на контроль в "t_order_from_new_buyer"
            sendOrderToModeration($con, $order_id);

        }
    }        
}
//заказы которые клиент удалил после активации сделать выполнен(executed)
function updateOrderDeletedToExecuted($con){
    $query="UPDATE `t_order` SET `executed`='1'
                        WHERE `order_deleted`='1' and `executed`='0'";
    mysqli_query($con, $query) or die (mysqli_error($con));
}
    //удалить не активные заказы(клиент не заказал)
function delete_dont_active_order($con){
    $arr_orderID = [] ;
    $i=0;
    // получить order_id всех не активных заказов
    $query="SELECT order_id FROM t_order WHERE order_active = 0";
    $result = mysqli_query($con, $query);
    if($result){                
        while($row = mysqli_fetch_array($result)){

            // echo $row[0] . "<br>";
            $arr_orderID [$i++]= $row[0];                                   
        }
    }else {                
            die('Неверный запрос: ' . mysqli_error());                
    }
    if($arr_orderID){
        for($i = 0; $i < count($arr_orderID); $i++){
            //echo $arr_orderID[$i] . "<br>";
            $order_id = $arr_orderID[$i];

            //удалить из t_order_product все товары не активного заказа
            $query = "DELETE FROM t_order_product WHERE order_id = $order_id";
            $result = mysqli_query($con, $query); 
            if($result){      }else die("Неверный запрос: "  . mysqli_error($con));                                         
                                                        
            //удалить из t_order не активный заказ
        
            $query = "DELETE FROM t_order WHERE order_id = $order_id";
            $result = mysqli_query($con, $query);   
            if($result) {     } else die("Неверный запрос: " . mysqli_error($con)) ; 
            
            //удалить из t_order_by_phone не активный заказ
        
            $query = "DELETE FROM `t_order_by_phone` WHERE `order_id` = '$order_id'";
            $result = mysqli_query($con, $query);   
            if($result) {     } else die("Неверный запрос: " . mysqli_error($con)) ; 
        }
    }
}  

?>