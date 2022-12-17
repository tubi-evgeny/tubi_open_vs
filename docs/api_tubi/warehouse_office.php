<?php
	 include 'connect.php';
     include 'text.php';
     include_once 'helper_classes.php';

	 
    mysqli_query($con,"SET NAMES 'utf8'");


    //create_invoice_key_save
    //receive_product_list
    //write_to_invoice_key
    //correct_stock_to_warehouse_to_order

        //получить список товаров для выдачи и оформления документов
    
        //получить список товаров для выдачи и оформления документов
    if(isset($_GET['receive_product_list'])){     
        $out_warehouse_id = $_GET['out_warehouse_id'];     
        $in_warehouse_id = $_GET['in_warehouse_id'];  
        $car_id = $_GET['car_id']; 
        
        receive_product_list($con, $out_warehouse_id, $in_warehouse_id, $car_id);

    }
    //указать ключ для документов в таблицах к этой записи
    else  if(isset($_GET['write_to_invoice_key'])){     
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];     
        $invoice_key = $_GET['invoice_key'];  
        
        write_to_invoice_key($con, $warehouse_inventory_id, $invoice_key);

    }
    //проверить документ сохранен или в работе close = 0 or =1;
    else  if(isset($_GET['receive_invoice_key_closed'])){     
        $invoice_key = $_GET['invoice_key'];  
        
        receive_invoice_key_closed($con, $invoice_key);

    }
    //закрыть документ(close = 1)
    else  if(isset($_GET['create_invoice_key_closed'])){     
        $invoice_key = $_GET['invoice_key'];  
        
        create_invoice_key_closed($con, $invoice_key);

    }
    //сохранить документ(save = 1)
    else  if(isset($_GET['create_invoice_key_save'])){     
        $invoice_key = $_GET['invoice_key'];  
        
        create_invoice_key_save($con, $invoice_key);

    }
    //узнать документ сохранен?
   /* else  if(isset($_GET['receive_invoice_key_save'])){     
        $invoice_key = $_GET['invoice_key'];  
        
        receive_invoice_key_save($con, $invoice_key);

    }*/
    //получить номер товарной накладной
    //(сохраненной/открытой)
    else  if(isset($_GET['receive_invoice'])){     
        $invoice_key = $_GET['invoice_key'];  
        
        receive_invoice($con, $invoice_key);

    }
    //закрыь ключ / документ
    else  if(isset($_GET['close_invoice_key'])){     
        $invoice_key = $_GET['invoice_key'];  
        
        close_invoice_key($con, $invoice_key);

    }
    //установить или отменить галочку (передан товар)
    else  if(isset($_GET['chenge_checked_provider'])){  
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];    
        $checked = $_GET['checked'];  
        
        chenge_checked_provider($con, $warehouse_inventory_id, $checked);

    }
    //поставщик откорректировал остаток в заказе(уменьшил) 
    else  if(isset($_GET['correct_stock_to_warehouse_to_order'])){  
        $provider_warehouse_id = $_GET['provider_warehouse_id']; 
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];    
        $quantity_to_deal = $_GET['quantity_to_deal'];  
        $quantity_collect = $_GET['quantity_collect']; 
        $user_uid = $_GET['user_uid'];

        //найти user_id
        $user_id=checkUserID($con, $user_uid); 
        
        correct_stock_to_warehouse_to_order($con, $provider_warehouse_id, $warehouse_inventory_id, $quantity_to_deal, $quantity_collect, $user_id);

    }
    //получить список товаров которые удалены в заказе
    else  if(isset($_GET['get_deleted_goods_flag'])){  
        $order_partner_id = $_GET['order_partner_id'];    
        
        get_deleted_goods_flag($con, $order_partner_id);

    }
    //получить список товаров которые удалены в заказе
    else  if(isset($_GET['get_deleted_goods_list'])){  
        $order_partner_id = $_GET['order_partner_id'];    
        
        get_deleted_goods_list($con, $order_partner_id);

    }
    //откоректировать количество товара из-за удаления товара
    else  if(isset($_GET['corrected_product_quantity_for_collect'])){  
        $deleted_goods_id = $_GET['deleted_goods_id'];   
        $order_partner_id = $_GET['order_partner_id']; 
        $product_inventory_id = $_GET['product_inventory_id']; 
        $warehouse_inventory_id = $_GET['warehouse_inventory_id']; 
        $quantity_deleted_product = $_GET['quantity_deleted_product']; 
        $order_product_part_id = $_GET['order_product_part_id']; 
        $correct_status = $_GET['correct_status'];  
        
        corrected_product_quantity_for_collect($con, $deleted_goods_id, $order_partner_id, $product_inventory_id
                    , $warehouse_inventory_id, $quantity_deleted_product, $order_product_part_id, $correct_status);

    }
    //получить ответ, есть удаленные товары для обработки
    else  if(isset($_GET['get_all_orders_deleted_goods'])){  
        $all_order_partner_id_str = $_GET['all_order_partner_id_str'];    
        
        get_all_orders_deleted_goods($con, $all_order_partner_id_str);

    }

    //получить ответ, есть удаленные товары для обработки
    function get_all_orders_deleted_goods($con, $all_order_partner_id_str){
        $order_partner_id_arr = explode(';', $all_order_partner_id_str);
        $new_order_partner_id_arr = [];
        foreach($order_partner_id_arr as $k => $order_partner_id){
            $query="SELECT `deleted_goods_id` FROM `t_for_deleted_goods` 
                    WHERE `order_partner_id`='$order_partner_id' and `collect_provider`='0'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            if(mysqli_num_rows($result) > 0){
                echo $order_partner_id . "<br>";
            }
            //echo "test - " .$order_partner_id . "<br>";
        }
    }
    //откоректировать количество товара из-за удаления товара
    function corrected_product_quantity_for_collect($con, $deleted_goods_id, $order_partner_id, $product_inventory_id
                        ,$warehouse_inventory_id, $quantity_deleted_product, $order_product_part_id, $correct_status){
        //если статус = отредактировать количество товара за вычетом удаленного то
        if($correct_status == 1){
            //получить инфу о перемещении товара
            $query="SELECT  `quantity`, `invoice_info_id` FROM `t_warehouse_inventory_in_out` 
                            WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            $result=mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($result);
            $quantity=$row[0];
            $invoice_info_id=$row[1];
            $quantity -= $quantity_deleted_product;
            //уменьшить количество товара в реализации а удаленное количество из заказа покупателя
            $query="UPDATE `t_warehouse_inventory_in_out` SET `quantity`='$quantity'
                            WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
            //уменьшить количество товара в `t_invoice_info`, удаленное количество из заказа покупателя
            $query="UPDATE `t_invoice_info` SET `quantity`='$quantity'
                            WHERE `invoice_info_id`='$invoice_info_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
            //уменьшить количество товара в заказе поставщику на удаленное количество из заказа покупателя
            reduce_the_quantity_of_goods($con, $order_partner_id, $product_inventory_id, $quantity_deleted_product, $order_product_part_id);
            //указать что товар обработан и закрыт closed=1
            $query="UPDATE `t_for_deleted_goods` SET `collect_provider`='$correct_status',`closed`='1'
                                WHERE `deleted_goods_id`='$deleted_goods_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
            //заменить инфу о том что товар не собран
            make_no_collect_product_partner($con, $order_partner_id, $warehouse_inventory_id);

        }
        //если пропустить и не редактировать 
        else{
            $query="UPDATE `t_for_deleted_goods` SET `collect_provider`='$correct_status'
                            WHERE `deleted_goods_id`='$deleted_goods_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }              

    }

    //получить список товаров которые удалены в заказе
    function get_deleted_goods_list($con, $order_partner_id){
        $query="SELECT dg.order_product_part_id,
                        opp.product_inventory_id,
                        opp.warehouse_inventory_id,
                        win.quantity,
                        dg.quantity,
                        dg.collect_provider,
                        dg.deleted_goods_id 
                        FROM t_for_deleted_goods dg 
                            JOIN t_order_product_part opp ON opp.order_product_part_id = dg.order_product_part_id
                            JOIN t_warehouse_inventory_in_out win ON win.warehouse_inventory_id = opp.warehouse_inventory_id
                        WHERE  dg.order_partner_id='$order_partner_id' and dg.collect_provider = '0'";
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $order_product_part_id=$row[0];
            $product_inventory_id=$row[1];
            $warehouse_inventory_id=$row[2];
            $quantity_full_orders=$row[3];
            $quantity_deleted_product=$row[4];
            $status_collect_provider=$row[5];
            $deleted_goods_id=$row[6];

            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);  
            $product_info=$product_list['product_info'];

            echo $order_product_part_id."&nbsp".$product_inventory_id."&nbsp".$warehouse_inventory_id."&nbsp"
                    .$quantity_full_orders."&nbsp".$quantity_deleted_product."&nbsp".$status_collect_provider."&nbsp"
                    .$product_info."&nbsp".$deleted_goods_id ."<br>";
        }
    }
    //получить список товаров которые удалены в заказе
    function get_deleted_goods_flag($con, $order_partner_id){
        $collect_provider='0';
        $query="SELECT `deleted_goods_id` FROM `t_for_deleted_goods` 
                    WHERE `order_partner_id`='$order_partner_id' and `collect_provider`='0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $collect_provider = '1';
        }
        echo $collect_provider;
        //return $collect_provider;
    }
    //поставщик откорректировал остаток в заказе(уменьшил) 
    function correct_stock_to_warehouse_to_order($con, $provider_warehouse_id, $warehouse_inventory_id, $quantity_to_deal, $quantity_collect, $user_id){
        //проверить, количество совпадает никто из покупателей не удалил из заказа позиции???
        $query="SELECT  `quantity` FROM `t_warehouse_inventory_in_out` WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $quantity = $row[0];
        //если в заказе поставщику меньше чем в собранном количестве то отправить сообщение для обновить активность
        if($quantity < $quantity_collect){
            echo "command" . "&nbsp" . "NO_RESULT" . "<br>";
            return;
        }        
        
        try{
            $correct_quantity = $quantity_to_deal - $quantity_collect;
            $order_product_id_list = array();
            //редактировать колличество
            $query="UPDATE `t_invoice_info` SET `quantity`='$quantity_collect'
                        WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

            $query="UPDATE `t_warehouse_inventory_in_out` SET `quantity`='$quantity_collect'
                        WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

            //получить все order_product_id и колличество товара в каждом
            //

            // получить все order_product_id  desk  
            $query="SELECT `order_product_id`, `product_inventory_id` FROM `t_order_product_part` 
                        WHERE `warehouse_inventory_id`='$warehouse_inventory_id' ORDER BY `order_product_id` DESC ";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            while($row=mysqli_fetch_array($result)){
                $order_product_id = $row[0];
                $product_inventory_id = $row[1];

                $order_product_id_list[] = $order_product_id;
            }
            
            //проверить колличество не меньше чем надо уменьшить проверять в цикле
            foreach($order_product_id_list as $k => $order_product_id){               

                $query="SELECT  `quantity` FROM `t_order_product_part` 
                            WHERE `order_product_id`='$order_product_id'";
                $result = mysqli_query($con, $query) or die (mysqli_error($con));
                $row=mysqli_fetch_array($result);
                $quantity_to_order = $row[0];

                //найти counterparty_id по user_id
                $counterparty_id = check_counterparty_id_by_user_id($con, $user_id);

                //получить данные(информацию) о компании
                $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
                $companyInfoString_short = $companyInfoList['companyInfoString_short'];

                if($quantity_to_order >= $correct_quantity){
                    $quantity_one = $quantity_to_order;

                    $quantity_to_order = $quantity_to_order - $correct_quantity;

                    //сделать запись в историю и внести в столбец corrected   
                    $history_str="Поставщик $companyInfoString_short внес изменения в количество товара с $quantity_one на $quantity_to_order / order_product_id = $order_product_id";

                    $query="INSERT INTO `t_history_actions`( `user_id`, `counterparty_id`, `actions`) 
                                                    VALUES ('$user_id','$counterparty_id','$history_str')";
                    mysqli_query($con, $query) or die (mysqli_error($con));

                    $query="UPDATE `t_order_product_part` SET `quantity`='$quantity_to_order', `corrected`='1' 
                    WHERE `order_product_id`='$order_product_id'";
                    mysqli_query($con, $query) or die (mysqli_error($con));

                    $query="UPDATE `t_order_product` SET `quantity`='$quantity_to_order', `corrected`='1'
                                WHERE `order_product_id`='$order_product_id'";
                    mysqli_query($con, $query) or die (mysqli_error($con));

                    break;
                }
                else{
                    //сделать запись в историю и внести в столбец corrected   
                    $history_str="Поставщик $companyInfoString_short внес изменения в количество товара с $quantity_to_order на = 0 / order_product_id = $order_product_id";

                    $query="INSERT INTO `t_history_actions`( `user_id`, `counterparty_id`, `actions`) 
                                                    VALUES ('$user_id','$counterparty_id','$history_str')";
                    mysqli_query($con, $query) or die (mysqli_error($con));


                    $query="UPDATE `t_order_product_part` SET `quantity`='0', `corrected`='1' 
                                WHERE `order_product_id`='$order_product_id'";
                    mysqli_query($con, $query) or die (mysqli_error($con));

                    $query="UPDATE `t_order_product` SET `quantity`='0', `corrected`='1'
                                WHERE `order_product_id`='$order_product_id'";
                    mysqli_query($con, $query) or die (mysqli_error($con));

                    $correct_quantity = $correct_quantity - $quantity_to_order;

                }        
            }

            //получить остаток товара на складе
            $partner_stock_quantity = stock_product_to_warehouse($con, $provider_warehouse_id, $product_inventory_id);

            //скоректировать запас на складе на разницу (запас - собрано)
            $quantity = $partner_stock_quantity - $quantity_collect;
                        
            $query="INSERT INTO `t_warehouse_inventory_in_out`
                        ( `transaction_name`, `product_inventory_id`, `quantity`, `out_warehouse_id`  , `collected`, `out_active`) 
                VALUES ('return'             ,'$product_inventory_id','$quantity','$provider_warehouse_id',  '1'    ,   '1'   )";
            mysqli_query($con, $query) or die (mysqli_error($con));


        }catch(Exception $ex){

        }
    }

    //установить или отменить галочку (передан товар)
    function chenge_checked_provider($con, $warehouse_inventory_id, $checked){
        $query="UPDATE `t_warehouse_inventory_in_out` SET `out_active`='$checked'
                      WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
        mysqli_query($con, $query) or die (mysqli_error($con));
    }

    //закрыь ключ / документ
    function close_invoice_key($con, $invoice_key){
        $query="UPDATE `t_invoice_key` SET `closed`='1'
                        WHERE `invoice_key_id`='$invoice_key'";
        mysqli_query($con, $query) or die (mysqli_error($con));
    }
    //получить номер товарной накладной
    //(сохраненной/открытой)
    function receive_invoice($con, $invoice_key){
        $document_num = 0;
        $query="SELECT  `document_num` FROM `t_document_deal` 
                    WHERE `invoice_key_id`='$invoice_key' and `document_name`='товарная накладная'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
                $document_num=$row[0];               
                
        }
        echo $document_num;
    }

    //узнать документ сохранен?
   /* function receive_invoice_key_save($con, $invoice_key){
        $query="SELECT `invoice_key_id`, `out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`, `car_id`, `save`, `closed`, `created_at` 
                    FROM `t_invoice_key` WHERE `invoice_key_id`='$invoice_key'";
    }*/
   
    //сохранить документ(save = 1)
    function create_invoice_key_save($con, $invoice_key){
        $query="UPDATE `t_invoice_key` SET `save`='1'
                WHERE `invoice_key_id`='$invoice_key'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con)); 

        if($result){
            echo "RESULT_OK";
        }else{
            echo "messege"."&nbsp".$GLOBALS['error_try_again_later_text']."<br>";
        }
        //echo "RESULT_OK test";
        
    }

    //закрыть документ(close = 1)
    function create_invoice_key_closed($con, $invoice_key){
        $query="UPDATE `t_invoice_key` SET `closed`='1'
                WHERE `invoice_key_id`='$invoice_key'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con)); 

        if($result){
            echo "RESULT_OK";
        }else{
            echo "messege"."&nbsp".$GLOBALS['error_try_again_later_text']."<br>";
        }
        //echo "RESULT_OK test";
        
    }

    //проверить документ закрыт или в работе close = 0 or =1;
    function receive_invoice_key_closed($con, $invoice_key){
        $query="SELECT  `closed` FROM `t_invoice_key` WHERE `invoice_key_id`='$invoice_key'";
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
            $invoice_key_closed=$row[0];

            echo $invoice_key_closed;

    }

    //указать ключ для документов в таблицах к этой записи
    function write_to_invoice_key($con, $warehouse_inventory_id, $invoice_key){
        try{
            $query="UPDATE `t_warehouse_inventory_in_out` SET `invoice_key_id`='$invoice_key'
                            WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

            $query="UPDATE `t_order_product_part` SET `invoice_key_id`='$invoice_key'
                            WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

            $query="UPDATE `t_invoice_info` SET `invoice_key_id`='$invoice_key'
                        WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

        }catch(Exception $ex){

        }
    }
    //получить список товаров для выдачи и оформления документов
    function receive_product_list($con, $out_warehouse_id, $in_warehouse_id, $car_id){
        $query="SELECT `warehouse_inventory_id`, `product_inventory_id`, `quantity`, `logistic_product`
                        ,`out_active`, `invoice_info_id`, `invoice_key_id`
                        FROM `t_warehouse_inventory_in_out` WHERE  `in_active`='0' and `collected`='1' 
                            and  `out_warehouse_id`='$out_warehouse_id' and `in_warehouse_id`='$in_warehouse_id'
                            and `car_id`='$car_id'";
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $warehouse_inventory_id=$row[0];
            $product_inventory_id=$row[1];
            $quantity=$row[2];
            $logistic_product=$row[3];
            $out_active=$row[4];
            $invoice_info_id=$row[5];
            $invoice_key_id=$row[6];
            
            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);                        
            $image_url=$product_list['image_url'];

            //получить описание товара
            $query="SELECT  dd.description_docs 
                            FROM t_invoice_info ii 
                                JOIN t_description_docs dd ON dd.description_docs_id = ii.description_docs_id 
                            WHERE ii.invoice_info_id = '$invoice_info_id'";
            $res=mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($res);
            $description_docs=$row[0];

             //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);
            $product_name_from_provider=$product_list['product_name_from_provider'];
                       
            if($logistic_product == 1 && $car_id == 0){

            }else{
                echo $warehouse_inventory_id."&nbsp".$product_inventory_id."&nbsp".$quantity."&nbsp"
                    .$invoice_key_id."&nbsp".$image_url."&nbsp".$description_docs."&nbsp"
                    .$out_active."&nbsp".$product_name_from_provider."<br>";
            }
            

                   // echo "----------------------- <br>";

        }
        //echo "hello <br>";

    }
    /*
        //получить список товаров для выдачи и оформления документов
    function receive_product_list($con, $out_warehouse_id, $in_warehouse_id, $car_id){
        $query="SELECT `warehouse_inventory_id`, `product_inventory_id`, `quantity`, `logistic_product`
                        ,`invoice_info_id`, `invoice_key_id`
                        FROM `t_warehouse_inventory_in_out` WHERE  `out_active`='0' and `collected`='1' 
                            and  `out_warehouse_id`='$out_warehouse_id' and `in_warehouse_id`='$in_warehouse_id'
                            and `car_id`='$car_id'";
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $warehouse_inventory_id=$row[0];
            $product_inventory_id=$row[1];
            $quantity=$row[2];
            $logistic_product=$row[3];
            $invoice_info_id=$row[4];
            $invoice_key_id=$row[5];
            
            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);                        
            $image_url=$product_list['image_url'];

            //получить описание товара
            $query="SELECT  dd.description_docs 
                            FROM t_invoice_info ii 
                                JOIN t_description_docs dd ON dd.description_docs_id = ii.description_docs_id 
                            WHERE ii.invoice_info_id = '$invoice_info_id'";
            $res=mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($res);
            $description_docs=$row[0];
           
            if($logistic_product == 1 && $car_id == 0){

            }else{
                echo $warehouse_inventory_id."&nbsp".$product_inventory_id."&nbsp".$quantity."&nbsp"
                    .$invoice_key_id."&nbsp".$image_url."&nbsp".$description_docs."<br>";
            }
            

                   // echo "----------------------- <br>";

        }
        //echo "hello <br>";

    }
    */

    mysqli_close($con);
?>