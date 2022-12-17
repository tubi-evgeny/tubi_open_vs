<?php
	 include 'connect.php';
     include 'text.php';
     include_once 'helper_classes.php';
     include 'variable.php';

	 
   mysqli_query($con,"SET NAMES 'utf8'");

   //receiveBuyerCompanyInfo
   //receive_paretner_warehouse_info
   //receive_partner_warehouse_list
   //receive_categori_list_for_my_catalog
   //receive_buyer_info
   //receive_list_products_to_order
   //receive_weekend_list
   //receive_delivery_list
   //receive_invoice_product_info
   //receive_or_create_invoice_product_number
   //receive_my_warehouse_list
   //receive_order_summ_max
   //chenge_order_active 
   //check_delivery_open
   //add_order_id_for_buyer
   //search_my_active_order
   //search_my_active_orders_list
   //write_weekend_to_table
   //write_intercity_delivery_to_table
   //delete_weekend_from_table
   
        //найти активный заказ
    if(isset($_GET['search_my_active_order'])){     
        $user_uid = $_GET['user_uid'];     
        $counterparty_tax_id = $_GET['company_tax_id'];  
        
        //найти user_id
        $user_id = checkUserID($con, $user_uid);
        //найти counterparty_id
        $counterparty_id = counterparty_id($con, $counterparty_tax_id);

        // получить order_id активного заказа
        $order_id = checkMyActiveOrder_001($con, $user_id, $counterparty_id); 

        echo $order_id;

    }
    //найти все активные заказы
    else if(isset($_GET['search_my_active_orders_list'])){     
        $user_uid = $_GET['user_uid'];     
        $counterparty_tax_id = $_GET['company_tax_id'];  
        
        //найти user_id
        $user_id = checkUserID($con, $user_uid);
        //найти counterparty_id
        $counterparty_id = counterparty_id($con, $counterparty_tax_id);

        // получить все активные заказы $order_list
        search_my_active_orders_list($con, $user_id, $counterparty_id); 

    }
    //изменить статус заказа (заказ отправлен в обработку) order_active = 1 и указать склад выдачи товара заказчику
    //и внести id товаров в t_provider_collect_product 
    else if(isset($_GET['chenge_order_active'])){   
        $order_id = $_GET['order_id']; 
        $warehouse_id = $_GET['warehouse_id']; 
        $counterparty_tax_id = $_GET['company_tax_id']; 
        //$getOrderMillis = $_GET['getOrderMillis'];  

        $in_counterparty_id = counterparty_id($con, $counterparty_tax_id);
        //активировать заказ
        $result=chengeOrderActive_001($con,$order_id, $warehouse_id, $in_counterparty_id);//$getOrderMillis, 
        //$result=chengeOrderActive($con,$order_id, $warehouse_id, $getOrderMillis);//
        if($result){
            echo "RESULT_OK" . "&nbsp" . "<br>";        

            //создать кллюч к документам (invoice_key) и сделать записи в t_warehouse_inventory_in_out 
            make_invoice_key($con, $order_id, $warehouse_id, $in_counterparty_id);
        
            //сгенерировать заказ от склад партнера для склад поставщика
            generation_order_to_provider($con, $order_id);

            //проверить если этот user заказывает первый раз то передать заказ на контроль в "t_order_from_new_buyer"
            sendOrderToModeration($con, $order_id);

        }else{
            echo "error" .  "&nbsp" . "Что то случилось и Ваш заказ не был передан в обработку, 
                            попробуйте сделать еще раз через несколько минут!" . "<br";
        }

       
    } //записать пожелания к заказу
    else if(isset($_GET['write_message_from_order'])){ 
        $order_id = $_GET['order_id'];
        $user_uid = $_GET['user_uid'];
        $message = $_GET['message'];

        $user_id = checkUserID($con, $user_uid);
        $query="INSERT INTO t_message_order (message_order, user_id, order_id)
                                    VALUES ('$message', $user_id, $order_id)";
        $result=mysqli_query($con, $query ) or die (mysqli_error($con));

        //получить историю заказов user только пользователя
    }else if(isset($_GET['receive_my_order_history'])){
        $user_uid = $_GET['user_uid'];
        $limit = $_GET['limit'];
        //$count 

        $user_id = checkUserID($con, $user_uid);
        receiveMyOrderHistory($con,$user_id);

       
    }//запрос на присвоение поставщику роли "provider"
    else if(isset($_GET['transfer_request_new_provider'])){
        $user_uid = $_GET['user_uid'];
        $role = $_GET['role'];
        $comment = $_GET['comment'];

        $user_id = checkUserID($con, $user_uid);
        $res = transferRequestNewProvider($con, $role, $user_id, $comment);
        if($res){
            echo "RESULT_OK";
        }else{
            echo "error" . "&nbsp" . "Что-то пошло не так и ваш договор не был доставлен. 
                                    Попробуйте отправить его позже." . "<br>";
        }        
    }//получить роль пользователя
    else if(isset($_GET['user_role_receive'])){    
        $user_uid = $_GET['user_uid'];

        $user_id = checkUserID($con, $user_uid);
        $role = userRoleReceive($con, $user_id);
        if(!empty($role)){
            echo "RESULT_OK" . "&nbsp" . $role . "<br>";
        }else{
            echo "RESULT_OK" . "&nbsp" . "user" . "<br>";
        }

    }
    //получить сосотояние checkBox 0=false , 1=true
    else if(isset($_GET['provider_product_in_box'])){    
        $order_product_id = $_GET['order_product_id'];
        $provider_product_in_box='provider_product_in_box';

        $result = provider_product_in_box($con, $order_product_id,$provider_product_in_box);
        echo $result;

    } //получить список адресов складов по городу
    else if(isset($_GET['receive_partner_warehouse_list'])){    
        $city = $_GET['city'];   
        $region = $_GET['region'];  
        $warehouse_type = 'partner';      

        receive_partner_warehouse_list_001($con, $city, $region, $warehouse_type);      
        //receive_partner_warehouse_list($con, $city,$warehouse_type);      

    }//поставщик товаров получить список адресов складов
    else if(isset($_POST['receive_my_warehouse_list'])){    
        $counterparty_tax_id = $_POST['counterparty_tax_id'];   
        $warehouse_type = 'provider';    
        
        $counterparty_id = counterparty_id($con, $counterparty_tax_id);

       // receive_my_warehouse_list($con, $counterparty_id,$warehouse_type);  
        receive_my_warehouse_list_001($con, $counterparty_id,$warehouse_type);    

    }//получить список ролей партнера
    else if(isset($_GET['partner_role_receive'])){    
        $counterparty_tax_id = $_GET['counterparty_tax_id'];   

        $counterparty_id = counterparty_id($con, $counterparty_tax_id);

        partner_role_receive($con, $counterparty_id);      

    }//получить список продуктов из этого заказа
    else if(isset($_GET['receive_list_products_to_order'])){    
        $order_id = $_GET['order_id'];   

        receive_list_products_to_order($con, $order_id);      

    }//внести выходной в таблицу
    else if(isset($_GET['write_weekend_to_table'])){    
        $dateMillis = $_GET['dateMillis']; 
        $user_uid = $_GET['user_uid']; 
        $taxpayer_id = $_GET['taxpayer_id']; 
        
        $user_id=checkUserID($con, $user_uid);   
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);            

        write_weekend_to_table($con, $dateMillis, $user_id, $counterparty_id);      

    }//получить список выходных 
    else if(isset($_GET['receive_weekend_list'])){                          
        $month = $_GET['month'];
        $year = $_GET['year'];

        receive_weekend_list($con, $month, $year);      

    }//удалить выходной из таблицы 
    else if(isset($_GET['delete_weekend_from_table'])){                          
        $dateMillis = $_GET['dateMillis']; 
        $user_uid = $_GET['user_uid'];  
        $taxpayer_id = $_GET['taxpayer_id'];
        
        $user_id=checkUserID($con, $user_uid);
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);

        delete_weekend_from_table($con, $dateMillis, $user_id,$counterparty_id);      

    }
    //удалить даты доставки в города из таблицы 
    else if(isset($_GET['delete_intercity_delivery_from_table'])){                          
        $dateMillis = $_GET['dateMillis']; 
        $user_uid = $_GET['user_uid'];  
        $taxpayer_id = $_GET['taxpayer_id'];
        
        $user_id=checkUserID($con, $user_uid);
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);

        delete_intercity_delivery_from_table($con, $dateMillis, $user_id,$counterparty_id);      

    }
    //получить список междугородней доставки 
    else if(isset($_GET['receive_delivery_list'])){                          
        $outCity_id = $_GET['outCity_id'];
        $inCity_id = $_GET['inCity_id'];
        $month = $_GET['month'];
        $year = $_GET['year'];

        receive_delivery_list($con, $outCity_id, $inCity_id, $month, $year);      

    }
    //внести выходной в таблицу
    else if(isset($_GET['write_intercity_delivery_to_table'])){    
        $outCity_id = $_GET['outCity_id'];
        $inCity_id = $_GET['inCity_id'];
        $dateMillis = $_GET['dateMillis']; 
        $user_uid = $_GET['user_uid']; 
        $taxpayer_id = $_GET['taxpayer_id']; 
        
        $user_id=checkUserID($con, $user_uid);   
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);            

        write_intercity_delivery_to_table($con, $outCity_id, $inCity_id,$dateMillis, $user_id, $counterparty_id);      

    }
    //получить данные покупателя и его компании
    else if(isset($_GET['receive_buyer_info'])){                          
        $phoneNum = $_GET['phoneNum'];        

        receive_buyer_info($con, $phoneNum);      

    }//получить список городов присутствия компании
    else if(isset($_GET['receive_city_list'])){                          

        //receive_city_list($con);      
        echo "Смоленск";

    }//получить список складов партнера с которых можно забрать товар
    else if(isset($_GET['receive_partner_warehouse_info'])){                          
        $city = $_GET['city'];        

        receive_partner_warehouse_info($con, $city);      

    }// создать новый заказ для покупателя(оформление поставщиком)
    else if(isset($_GET['add_order_id_for_buyer'])){                          
        $creator_user_uid = $_GET['creator_user_uid']; 
        $taxpayer_id = $_GET['taxpayer_id']; 
        $buyer_user_id = $_GET['buyer_user_id'];
        $buyer_counterparty_id = $_GET['buyer_counterparty_id'];
        $partner_warehouse_id = $_GET['partner_warehouse_id']; 

        $creator_user_id=checkUserID($con, $creator_user_uid);
        
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);

        add_order_id_for_buyer($con, $creator_user_id,$buyer_user_id,$partner_warehouse_id,
                                    $counterparty_id, $buyer_counterparty_id);      

    }//получить все заказы с которые выписывает компания
    else if(isset($_GET['receive_all_write_off_orders'])){                          
        $taxpayer_id = $_GET['taxpayer_id']; 
        
          //найти counterparty_id
          $counterparty_id = searchCounterpartyId($con, $taxpayer_id);

        receive_all_write_off_orders($con, $counterparty_id);      

    }//получить данные склад
    else if(isset($_GET['receive_warehouse_info'])){                          
        $order_id = $_GET['order_id']; 
        
        receive_warehouse_info($con, $order_id);      

    }//изменить статус заказа (заказ отправлен в обработку) order_active = 1 и указать склад выдачи товара заказчику
    //и внести id товаров в t_provider_collect_product 
    else if(isset($_GET['chenge_order_active_execute'])){   
        $order_id = $_GET['order_id']; 
        $warehouse_id = $_GET['warehouse_id']; 
        $getOrderMillis = $_GET['getOrderMillis'];   

        $result=chengeOrderActive($con,$order_id, $warehouse_id, $getOrderMillis);
        if($result){
            echo "RESULT_OK" . "&nbsp" . "<br>";
        }else{
            echo "error" .  "&nbsp" . "Что то случилось и Ваш заказ не был передан в обработку, 
                              попробуйте сделать еще раз через несколько минут!" . "<br";
        }
        //в  t_order_to_phone изменить execute, заказ оформлен
        chengeExecuteToTable($con,$order_id);
               
    } 
    //список категорий для мой каталог
    else if(isset($_GET['receive_categori_list_for_my_catalog'])){  
        $taxpayer_id = $_GET['taxpayer_id'];
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);                        
        
        receive_categori_list_for_my_catalog($con, $counterparty_id);      

    }
    //получить данные о складе выдачи товара
    else if(isset($_GET['receive_paretner_warehouse_info'])){  
        $order_id = $_GET['order_id'];                              
        
        receive_paretner_warehouse_info($con, $order_id);     

    }
    //получить/создать номер товарной накладной
    else if(isset($_GET['receive_or_create_invoice_product_number'])){  
        $invoiceKey = $_GET['invoiceKey'];                              
        
        receive_or_create_invoice_product_number($con, $invoiceKey);     

    }
     //получить данные по товару в товарной накладной
     else if(isset($_GET['receive_invoice_product_info'])){  
        $invoiceKey = $_GET['invoiceKey'];                              
        
        receive_invoice_product_info($con, $invoiceKey);     

    }
    //получить данные по компании получателе
    else if(isset($_GET['receive_company_info'])){  
        $warehouse_id = $_GET['in_warehouse_id'];                              
        
        receive_company_info($con, $warehouse_id);     

    }   
    //получить новое сообщение
    else if(isset($_GET['check_new_messege'])){  
        $message_number = $_GET['message_number'];

        check_new_messege($con, $message_number);     

    }  
    //получить сообщения
    else if(isset($_GET['receive_message_list'])){  

        receive_message_list($con);     
    }
    //проверить доставка для клиентов открыта
    else if(isset($_POST['check_delivery_open'])){  

        echo $GLOBALS['delivery_open'];     
    }
    //получить максимальную сумму одного заказа
    else if(isset($_GET['receive_order_summ_max'])){  

        echo $GLOBALS['order_summ_max'];
    }
    //получить минимальную сумму одного заказа
    else if(isset($_POST['receive_order_summ_min'])){  

        echo $GLOBALS['order_summ_min'];        
    }

    //получить сообщения
    function receive_message_list($con){
        //получить массив сообщений
        $message_text_arr = $GLOBALS['message_text_arr_1'];

        foreach($message_text_arr as $message_text ){
            echo $message_text ."<br>";
        }
    }

    //получить новое сообщение
    function check_new_messege($con, $message_number){
        //получить массив сообщений
        $message_text_arr = $GLOBALS['message_text_arr_1'];
        //echo count($messege_text_arr)."<br>";
        //проверить есть новое сообщение
        if(count($message_text_arr) > $message_number){            
            echo "RESULT_OK";
        }
        else{
            echo "NO_RESULT";
        }       
        
    }

    //получить данные по компании получателе
    function receive_company_info($con, $warehouse_id){
        try{
            //получить данные(информацию) о складе и компании id
            $warehouseInfoList = warehouseInfo($con,$warehouse_id);        
            $counterparty_id = $warehouseInfoList['counterparty_id'];

            //получить данные(информацию) о компании
            $companyInfoList = receiveCompanyInfo($con, $counterparty_id);         
            $companyInfoString = $companyInfoList['companyInfoString'];

            echo $companyInfoString;
        }catch(Exception $ex){
            
        }
    }

    //создать кллюч к документам (invoice_key) и сделать записи в t_warehouse_inventory_in_out 
   /* function make_invoice_key($con, $order_id, $out_warehouse_id, $in_counterparty_id){
         //получить данные(информацию) о складе и компании id
         $warehouseInfoList = warehouseInfo($con,$out_warehouse_id);
         $out_counterparty_id = $warehouseInfoList['counterparty_id'];

        try{
            //создать ключ для идентификации документов t_invoice_key
            $query="INSERT INTO `t_invoice_key`
                            (`out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`) 
                    VALUES ('$out_counterparty_id','$out_warehouse_id','$in_counterparty_id')";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $invoice_key_id = mysqli_insert_id($con);

            //сделать запись в (t_order_product)
            $query="UPDATE `t_order_product` SET `invoice_key_id`='$invoice_key_id'
                        WHERE `order_id`='$order_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

        }catch(Exception $ex){

        }    
    }*/
    //получить данные по товару в товарной накладной
    function receive_invoice_product_info($con, $invoiceKey){
        $query="SELECT dd.description_docs,
                            ii.quantity,
                            ii.price                            
                    FROM t_invoice_info ii 
                        JOIN t_description_docs dd ON dd.description_docs_id = ii.description_docs_id
                    WHERE ii.invoice_key_id ='$invoiceKey'";
         $result=mysqli_query($con,$query)or die (mysqli_error($con));
         while($row=mysqli_fetch_array($result)){
             $description_docs = $row[0];
             $quantity= $row[1];
             $price= $row[2];
             
             
             echo $description_docs ."&nbsp".$quantity."&nbsp".$price."<br>";           
                         
         }
    }
    
    //получить/создать номер товарной накладной
   function receive_or_create_invoice_product_number($con, $invoiceKey){
       $document_name = "товарная накладная";
       $document_num = 0;
       $created_at = 0;
       //создать/получить номер документа
        $query="SELECT `document_num`, `created_at` 
                FROM `t_document_deal`
                WHERE  `invoice_key_id`='$invoiceKey' and `document_name`='$document_name'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_array($result);
            $document_num= $row[0];
            $created_at= $row[1];
        }
        else{
            //нет созданного документа => создать
            //узнать кто поставщик
            $query="SELECT `out_counterparty_id` FROM `t_invoice_key` 
                        WHERE `invoice_key_id`='$invoiceKey'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            $row=mysqli_fetch_array($result);
            $counterparty_id= $row[0];


            //наити последний номер документа для этого поставщика
            $query="SELECT `document_num` 
                    FROM `t_document_deal` 
                    WHERE `counterparty_id`='$counterparty_id' and `document_name`='$document_name'
                        ORDER BY `document_num` DESC LIMIT 1";
            $res=mysqli_query($con,$query)or die (mysqli_error($con));
         
            if(mysqli_num_rows($res) > 0){
                $row=mysqli_fetch_array($res);
                $document_num= $row[0] +1;

            }else{
                //у поставщика еще нет созданных документов
                $document_num = 1;
            }

            //создаем номер документа для этого поставщика
            $query="INSERT INTO `t_document_deal`( `invoice_key_id`, `counterparty_id`, `document_name`, `document_num`) 
                                                VALUES ('$invoiceKey','$counterparty_id','$document_name', '$document_num')";
            $result = mysqli_query($con,$query)or die (mysqli_error($con));
            if($result){
                //получить номер документа
                $query="SELECT `document_num`, `created_at` 
                            FROM `t_document_deal`
                            WHERE  `invoice_key_id`='$invoiceKey' and `document_name`='$document_name'";
                $result=mysqli_query($con,$query)or die (mysqli_error($con));
                $row=mysqli_fetch_array($result);
                $document_num= $row[0];
                $created_at= $row[1];
            }

        }
        echo $document_num ."&nbsp".$created_at."<br>";

   }

    //сгенерировать заказ от склад партнера для склад поставщика
   /* function generation_order_to_provider($con, $order_id){
        //получить данные о заказе
        $query="SELECT ord.warehouse_id,
                        ord.get_order_date_millis,
                        ord.category_in_order,
                        win.counterparty_id
                    FROM t_order ord
                        JOIN t_warehous w ON w.warehouse_id=ord.warehouse_id
                        JOIN t_warehouse_info win ON win.warehouse_info_id=w.warehouse_info_id
                    WHERE ord.order_id='$order_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $partner_warehouse_id= $row[0];
        $get_order_date_millis= $row[1];
        $category_in_order= $row[2];
        $partner_counterparty_id= $row[3];

        //получить товары в заказе
        $query="SELECT `order_product_id`,`product_inventory_id`, `quantity`, `price`, `price_process`,`provider_war_id`,`description_docs_id`
                    FROM `t_order_product` WHERE  `order_id`='$order_id' and `order_prod_deleted`='0'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $order_product_id = $row[0];
            $product_inventory_id= $row[1];
            $quantity= $row[2];
            $price= $row[3];
            $price_process= $row[4];
            $provider_warehouse_id= $row[5];
            $description_docs_id = $row[6];
            

            $product_info_list[] = array('order_id'=>$order_id,'order_product_id'=>$order_product_id
                                    ,'partner_counterparty_id'=>$partner_counterparty_id
                                    ,'partner_warehouse_id'=>$partner_warehouse_id
                                    ,'get_order_date_millis'=>$get_order_date_millis,'category_in_order'=>$category_in_order
                                    ,'product_inventory_id'=>$product_inventory_id, 'quantity'=>$quantity
                                    ,'price'=>$price, 'price_process'=>$price_process
                                    ,'provider_warehouse_id'=>$provider_warehouse_id, 'description_docs_id'=>$description_docs_id);            
                        
        }
        foreach($product_info_list as $k => $product_info){
            echo "description_docs_id: ".$product_info['description_docs_id'] . "<br>";
            foreach($product_info as $key => $v){
                echo $v . "&nbsp";
            }
            echo  "<br>";
        }
        //сделать все вчерашние заказы партнерам, активными
        make_every_orders_partner_yesterday_active($con);  
        
        //открыть заказы для поставщика
        foreach($product_info_list as $k => $product_info){
            $partner_warehouse_id=$product_info['partner_warehouse_id'];
            $provider_warehouse_id=$product_info['provider_warehouse_id'];
            $get_order_date_millis=$product_info['get_order_date_millis'];

            //получить counterparty_id из warehouse_id
            $provider_counterparty_id=get_counterparty_id_from_warehouse_id($con, $provider_warehouse_id);
            $partner_counterparty_id=get_counterparty_id_from_warehouse_id($con, $partner_warehouse_id);

            $query="SELECT `order_partner_id`,  `get_order_date_millis`, `created_at`
                            FROM `t_order_partner` WHERE `order_active`='0' and `executed`='0' 
                                and  `out_warehouse_id`='$provider_warehouse_id' and `in_warehouse_id`='$partner_warehouse_id'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
                echo "test 1 <br>";
            //нет заказа, открываем
            if(mysqli_num_rows($result) == 0){
                $query="INSERT INTO `t_order_partner`
                             (`out_counterparty_id`    ,`out_warehouse_id`    , `in_counterparty_id`       ,`in_warehouse_id`     , `get_order_date_millis`)
                    VALUES ('$provider_counterparty_id','$provider_warehouse_id','$partner_counterparty_id','$partner_warehouse_id','$get_order_date_millis')";
                mysqli_query($con,$query)or die (mysqli_error($con));
            }
               
        }
        //записать в заказ строку с товаром
        foreach($product_info_list as $k => $product_info){
            $partner_warehouse_id=$product_info['partner_warehouse_id'];
            $provider_warehouse_id=$product_info['provider_warehouse_id'];
            $get_order_date_millis=$product_info['get_order_date_millis'];
            
            //наити открытый заказ для этих складов
            $query="SELECT `order_partner_id`
                            FROM `t_order_partner` WHERE `order_active`='0' and `executed`='0' 
                                and  `out_warehouse_id`='$provider_warehouse_id' and `in_warehouse_id`='$partner_warehouse_id'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            $row=mysqli_fetch_array($result);
            $order_partner_id=$row[0];
            
            $product_inventory_id = $product_info['product_inventory_id'];
            $quantity = $product_info['quantity'];
            $price = $product_info['price'];
            $price_process = $product_info['price_process'];
            $order_id = $product_info['order_id'];
            $order_product_id = $product_info['order_product_id'];
            $description_docs_id = $product_info['description_docs_id'];

            //внести товар в зaказ
            $query="INSERT INTO `t_order_product_part`
                    (`product_inventory_id`, `quantity`, `price`, `price_process`, `order_partner_id`, `order_id`,`order_product_id`, `description_docs_id`) 
            VALUES ('$product_inventory_id','$quantity','$price','$price_process','$order_partner_id','$order_id','$order_product_id','$description_docs_id')";
            mysqli_query($con,$query)or die (mysqli_error($con));
        }
    }*/

    //получить данные о складе выдачи товара
    function  receive_paretner_warehouse_info($con, $order_id){
        $query="SELECT `warehouse_id` FROM `t_order` WHERE `order_id`='$order_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $warehouse_id= $row[0];
        
        //получить данные(информацию) о складе 
        $warehouseInfoList = warehouseInfo($con,$warehouse_id);
        $warehouse_id = $warehouseInfoList['warehouse_id'];
        $warehouse_info_id = $warehouseInfoList['warehouse_info_id'];
        $city = $warehouseInfoList['city'];
        $street = $warehouseInfoList['street'];
        $house = $warehouseInfoList['house'];
        $building = $warehouseInfoList['building'];

        echo  $warehouse_id."&nbsp".$warehouse_info_id."&nbsp".$city."&nbsp".$street."&nbsp"
                    .$house."&nbsp".$building."<br>";
    }
    //список категорий для мой каталог
    function receive_categori_list_for_my_catalog($con, $counterparty_id){        
        $query="SELECT   c.category
                            FROM t_catalog_is_mine cmi 
                                JOIN t_product p ON p.product_id = cmi.product_id
                                JOIN t_category c ON c.category_id=p.category_id
                            WHERE cmi.counterparty_id='$counterparty_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $category= $row[0];
            $category_list[] = $category; 
            
            //$category_id = $row[2];
            //$product_id = $row[1];
            //echo "category => $category category_id => $category_id product_id => $product_id<br>";
        }
        //удалить дубликаты категорий из листа
        $category_list = array_unique($category_list);
        foreach($category_list as $k => $category){
            echo $category . "<br>";
        }
       // echo "hi 2 <br>";

    }
    //в  t_order_to_phone изменить execute, заказ оформлен
    function chengeExecuteToTable($con,$order_id){
        $query="UPDATE `t_order_by_phone` SET `executed`='1' 
                        WHERE `order_id`='$order_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
    }
    //получить данные склад
    function receive_warehouse_info($con, $order_id){
        $query="SELECT w.warehouse_info_id,
                        w.warehouse_id,
                        win.city, 
                        win.street,
                        win.house,
                        win.building
                    FROM t_order o 
                        JOIN t_warehous w ON w.warehouse_id = o.warehouse_id
                        JOIN t_warehouse_info win ON win.warehouse_info_id = w.warehouse_info_id 
                    WHERE o.order_id='$order_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            //while($row=mysqli_fetch_array($result)){
                $row=mysqli_fetch_array($result);
                $warehouse_info_id=$row[0]; 
                $warehouse_id=$row[1];
                $city=$row[2];
                $street=$row[3];
                $house=$row[4];
                $building=$row[5];

                echo $warehouse_info_id."&nbsp".$warehouse_id."&nbsp".$city."&nbsp".$street."&nbsp".
                        $house."&nbsp".$building."<br>";

             /*  $query="SELECT `warehouse_id` FROM `t_warehous`
                         WHERE `warehouse_info_id`='$warehouse_info_id' AND `warehouse_type`='partner' AND `active`='1'";
                $res=mysqli_query($con,$query)or die (mysqli_error($con));
                if(mysqli_num_rows($res) > 0){
                    $row=mysqli_fetch_array($res);
                    $warehouse_id=$row[0]; */

                
               // }
           // }
        }


    }
    //получить все заказы которые выписывает компания
    function receive_all_write_off_orders($con, $counterparty_id){
        $query="SELECT  `order_id` FROM `t_order_by_phone` 
                    WHERE `counterparty_id`='$counterparty_id' AND `executed`='0'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $order_id=$row[0]; 
        
                $order_id_list[] = $order_id;
            }
        }else{
            echo "messege"."&nbsp".$GLOBALS['data_to_orders_is_not'];
            return;
        }

        foreach($order_id_list as $k => $order_id){
           // echo "$k => $order_id <br>"; 
            $query="SELECT `warehouse_id`, `user_id`, `counterparty_id`
                    FROM `t_order` WHERE `order_id`='$order_id'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            $row=mysqli_fetch_array($result);
            $partner_warehouse_id=$row[0]; 
            $buyer_user_id=$row[1]; 
            $buyer_counterparty_id=$row[2]; 

            $query="SELECT `name`, `phone` FROM `t_user` WHERE `user_id`='$buyer_user_id'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            $row=mysqli_fetch_array($result);
            $buyer_name=$row[0]; 
            $buyer_phone=$row[1]; 

            $query="SELECT `abbreviation`, `counterparty`, `taxpayer_id_number`
                            FROM `t_counterparty` WHERE `counterparty_id`='$buyer_counterparty_id'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            $row=mysqli_fetch_array($result);
            $abbreviation=$row[0]; 
            $counterparty=$row[1]; 
            $taxpayer_id_number=$row[2];

            $query="SELECT w.warehouse_info_id,
                            win.city,
                            win.street,
                            win.house,
                            win.building
                        FROM t_warehous w 
                            JOIN t_warehouse_info win ON win.warehouse_info_id=w.warehouse_info_id
                        WHERE w.warehouse_id = '$partner_warehouse_id'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            $row=mysqli_fetch_array($result);
            $warehouse_info_id=$row[0]; 
            $city=$row[1]; 
            $street=$row[2];
            $house=$row[3]; 
            $building=$row[4];
                      
            echo $buyer_name."&nbsp".$buyer_phone."&nbsp".$abbreviation."&nbsp".$counterparty."&nbsp".
                    $taxpayer_id_number."&nbsp".$warehouse_info_id."&nbsp".$partner_warehouse_id."&nbsp".
                    $city."&nbsp".$street."&nbsp".$house."&nbsp".$building."&nbsp".$order_id."<br>";
        }
    }
    // создать новый заказ для покупателя(оформление поставщиком)
    function add_order_id_for_buyer($con, $creator_user_id,$buyer_user_id,$partner_warehouse_id,
                                            $counterparty_id, $buyer_counterparty_id){
        $date = date_mysqli();
        $query="INSERT INTO `t_order`(  `warehouse_id`, `user_id`,  `counterparty_id`, `date_order_start`)
                          VALUES ('$partner_warehouse_id','$buyer_user_id','$buyer_counterparty_id','$date')";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        $lastInsertedId = mysqli_insert_id($con);

        $query = "INSERT INTO `t_order_by_phone`( `order_id`, `user_id`, `counterparty_id`) 
                                        VALUES ('$lastInsertedId','$creator_user_id','$counterparty_id')";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));

        echo $lastInsertedId;
    }
    //получить список складов партнера с которых можно забрать товар
    function receive_partner_warehouse_info($con, $city){
        $query="SELECT `warehouse_info_id`,`city`, `street`, `house`, `building`  FROM `t_warehouse_info` 
                        WHERE `city`='$city' and `active`='1'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_info_id=$row[0]; 
                $city=$row[1];
                $street=$row[2];
                $house=$row[3];
                $building=$row[4];
               $query="SELECT `warehouse_id` FROM `t_warehous`
                         WHERE `warehouse_info_id`='$warehouse_info_id' AND `warehouse_type`='partner' AND `active`='1'";
                $res=mysqli_query($con,$query)or die (mysqli_error($con));
                if(mysqli_num_rows($res) > 0){
                    $row=mysqli_fetch_array($res);
                    $warehouse_id=$row[0]; 

                    echo $warehouse_info_id."&nbsp".$warehouse_id."&nbsp".$city."&nbsp".$street."&nbsp".
                                        $house."&nbsp".$building."<br>";
                }
            }
        }
    }
    //получить данные покупателя и его компании
    function receive_buyer_info($con, $phoneNum){
        $query="SELECT `user_id`, `name`, `counterparty_id`
                FROM `t_user` WHERE `phone`='$phoneNum'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        if(mysqli_num_rows($result) > 0){
        $row=mysqli_fetch_array($result);
            $user_id=$row[0]; 
            $name=$row[1]; 
            $counterparty_id=$row[2]; 

        $query="SELECT  `abbreviation`, `counterparty`, `taxpayer_id_number` 
                FROM `t_counterparty` WHERE `counterparty_id`='$counterparty_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        $row=mysqli_fetch_array($result);
            $abbreviation=$row[0]; 
            $counterparty=$row[1]; 
            $taxpayer_id_number=$row[2]; 

        //найти открытый заказ для сбора этой компанией, если есть
        //$query="";

        echo $user_id."&nbsp".$name ."&nbsp".$abbreviation."&nbsp".$counterparty."&nbsp".
                    $taxpayer_id_number."&nbsp".$counterparty_id."<br>";
        }else{
            echo "messege"."&nbsp".$GLOBALS['data_is_not'];
        }

    }   
    //удалить даты доставки в города из таблицы 
    function delete_intercity_delivery_from_table($con, $dateMillis, $user_id,$counterparty_id){
        $weekend_date = date('d-m-Y H:i:s', $dateMillis / 1000);
        $date = explode('-', explode(' ', $weekend_date)[0]); 
        $day = $date[0];
        $month = $date[1];
        $year = $date[2];

        $query="DELETE FROM `t_intercity_delivery_calendar` WHERE `day`='$day' and `month`='$month' and `year`='$year'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        if(mysqli_affected_rows($con) > 0){
            $actions="Удалена дата доставки в города из календаря выходные компании $day.$month.$year";
    
            $query="INSERT INTO `t_history_actions`( `user_id`, `counterparty_id`, `actions`) 
                                            VALUES ('$user_id','$counterparty_id','$actions')";
            $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        }
    }
    //удалить выходной из таблицы 
    function delete_weekend_from_table($con, $dateMillis, $user_id, $counterparty_id){
        $weekend_date = date('d-m-Y H:i:s', $dateMillis / 1000);
        $date = explode('-', explode(' ', $weekend_date)[0]); 
        $day = $date[0];
        $month = $date[1];
        $year = $date[2];

        //echo "day $day month $month year $year dateMillis $dateMillis <br>";

        $query="DELETE FROM `t_weekend` WHERE `day`='$day' and `month`='$month' and `year`='$year'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        if(mysqli_affected_rows($con) > 0){
            $actions="Удален выходной из календаря выходные компании $day.$month.$year";
            //echo $actions;
    
            $query="INSERT INTO `t_history_actions`( `user_id`, `counterparty_id`, `actions`) 
                                            VALUES ('$user_id','$counterparty_id','$actions')";
            $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        }  
    }    
    //получить список междугородней доставки 
    function receive_delivery_list($con, $outCity_id, $inCity_id, $month, $year){
        $query="SELECT `id`, `time_millis` FROM `t_intercity_delivery_calendar` 
        WHERE  `year`='$year' and `month`='$month' and `out_city_id`='$outCity_id' and `in_city_id`='$inCity_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $intercity_delivery_id=$row[0]; 
                $time_millis=$row[1]; 
               
                echo $intercity_delivery_id . "&nbsp" . $time_millis . "<br>";
            }
        }
    }   
    // получить список выходных 
    function  receive_weekend_list($con, $month, $year){
        $query="SELECT `weekend_id`, `weekend_millis` FROM `t_weekend` WHERE `year`='$year' and `month`='$month' ";
        $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $weekend_id=$row[0]; 
                $weekend_millis=$row[1]; 
               
                echo $weekend_id . "&nbsp" . $weekend_millis . "<br>";
            }
        }
    }
    //;
     //внести даты доставки в города в таблицу
     function write_intercity_delivery_to_table(
                $con, $outCity_id, $inCity_id,$dateMillis, $user_id, $counterparty_id){    
        date_default_timezone_set("Asia/Tbilisi");
        $weekend_date = date('d-m-Y H:i:s', $dateMillis / 1000);
        $date = explode('-', explode(' ', $weekend_date)[0]); 
        $day = $date[0];
        $month = $date[1];
        $year = $date[2];
       // echo "day $day month $month year $year dateMillis $dateMillis";
        $query="INSERT INTO `t_intercity_delivery_calendar`
                            (`out_city_id`, `in_city_id`, `day`, `month`, `year`, `time_millis`) 
                     VALUES ('$outCity_id','$inCity_id' ,'$day' ,'$month','$year','$dateMillis')";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));  
        if(mysqli_affected_rows($con) > 0){
            $actions="Добавлена доставка в календарь междугородних доставок компании $day.$month.$year";
    
            $query="INSERT INTO `t_history_actions`( `user_id`, `counterparty_id`, `actions`) 
                                            VALUES ('$user_id','$counterparty_id','$actions')";
            $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        }                             
    }
    //внести выходной в таблицу
    function write_weekend_to_table($con, $dateMillis, $user_id, $counterparty_id){   
        date_default_timezone_set("Asia/Tbilisi");
        $weekend_date = date('d-m-Y H:i:s', $dateMillis / 1000);
        $date = explode('-', explode(' ', $weekend_date)[0]); 
        $day = $date[0];
        $month = $date[1];
        $year = $date[2];
       // echo "day $day month $month year $year dateMillis $dateMillis";
        $query="INSERT INTO `t_weekend`( `day`, `month`, `year`, `weekend_millis`) 
                                VALUES ('$day','$month','$year','$dateMillis')";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));  
        if(mysqli_affected_rows($con) > 0){
            $actions="Добавлен выходной в календарь выходные компании $day.$month.$year";
    
            $query="INSERT INTO `t_history_actions`( `user_id`, `counterparty_id`, `actions`) 
                                            VALUES ('$user_id','$counterparty_id','$actions')";
            $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
        }                             
    }
    //получить список продуктов из этого заказа
    function receive_list_products_to_order($con, $order_id){
        $query="SELECT `product_inventory_id`, `quantity`, `price`, `price_process`, `corrected` 
                        FROM `t_order_product` WHERE `order_id`='$order_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $product_inventory_id=$row[0]; 
            $quantity_to_order=$row[1]; 
            $price=$row[2];  
            $price_process=$row[3];
            $corrected=$row[4]; 

            //получить описание товара
            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);

            $product_id=$product_list['product_id'];
            $category=$product_list['category'];
            $product_name=$product_list['product_name'];
            $brand=$product_list['brand']; 
            $characteristic=$product_list['characteristic'];
            $type_packaging=$product_list['type_packaging']; 
            $unit_measure=$product_list['unit_measure'];
            $weight_volume=$product_list['weight_volume']; 
            $quantity_package=$product_list['quantity_package'];
            $image_url=$product_list['image_url'];
            $storage_conditions=$product_list['storage_conditions'];

             //получить данные поставщика
             $query="SELECT c.counterparty_id,
                            c.abbreviation,
                            c.counterparty
                        FROM t_product_inventory pin 
                            JOIN t_counterparty c ON c.counterparty_id = pin.counterparty_id
                        WHERE pin.product_inventory_id='$product_inventory_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($res);
                $provider_counterparty_id=$row[0];  
                $provider_abbreviation=$row[1];
                $provider_counterparty=$row[2];


            echo $product_id ."&nbsp".$product_inventory_id."&nbsp".$category ."&nbsp".$brand ."&nbsp".$characteristic.
                        "&nbsp".$type_packaging ."&nbsp".$unit_measure ."&nbsp".$weight_volume ."&nbsp".$quantity_package .
                        "&nbsp".$image_url ."&nbsp".$price ."&nbsp".$storage_conditions."&nbsp".$quantity_to_order . 
                        "&nbsp".$provider_counterparty_id ."&nbsp".$provider_abbreviation .
                        "&nbsp".$provider_counterparty."&nbsp".$product_name."&nbsp".$price_process."&nbsp".$corrected."<br>";
        }
    }
    //поставщик товаров получить список адресов складов
    function receive_my_warehouse_list_001($con, $counterparty_id,$warehouse_type){
        $query="SELECT wi.warehouse_info_id,
                        wi.city,
                        wi.street,
                        wi.house,
                        wi.building,
                        w.warehouse_id
                     FROM t_warehouse_info wi
                        JOIN t_warehous w ON w.warehouse_info_id = wi.warehouse_info_id 
                                                AND w.warehouse_type='$warehouse_type'
                                                AND w.active= '1'
                      WHERE wi.counterparty_id = '$counterparty_id' AND wi.active = '1'";

        //$query = "SELECT `warehouse_id`, `city`, `street`, `house`, `building`
         //            FROM `t_warehouse` WHERE `counterparty_id`='$counterparty_id' AND `active`='1'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_info_id=$row[0]; 
                $city=$row[1]; 
                $street=$row[2];  
                $house=$row[3];
                $building=$row[4];
                $warehouse_id=$row[5];

                echo $warehouse_info_id . "&nbsp" . $city . "&nbsp" . $street . "&nbsp" . $house . "&nbsp" . 
                        $building . "&nbsp" . $warehouse_id . "<br>";


                    //проверить склады на соответствие типу
               /* $query = "SELECT `warehouse_type_id` FROM `t_warehouse_type` 
                    WHERE `warehouse_id`='$warehouse_id' AND `warehouse_type`='$warehouse_type' AND `active`='1'";
                $res = mysqli_query($con,$query)or die (mysqli_error($con));
                if(mysqli_num_rows($res) > 0){
                    $row=mysqli_fetch_array($res);
                        echo $warehouse_info_id . "&nbsp" . $city . "&nbsp" . $street . "&nbsp" . $house . "&nbsp" . $building . "<br>";
                    
                }*/
            }
        }
    }
    /*
    //поставщик товаров получить список адресов складов
    function receive_my_warehouse_list($con, $counterparty_id,$warehouse_type){
        $query = "SELECT `warehouse_id`, `city`, `street`, `house`, `building`
                     FROM `t_warehouse` WHERE `counterparty_id`='$counterparty_id' AND `active`='1'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0]; 
                $city=$row[1]; 
                $street=$row[2];  
                $house=$row[3];
                $building=$row[4];
                    //проверить склады на соответствие типу
                $query = "SELECT `warehouse_type_id` FROM `t_warehouse_type` 
                    WHERE `warehouse_id`='$warehouse_id' AND `warehouse_type`='$warehouse_type' AND `active`='1'";
                $res = mysqli_query($con,$query)or die (mysqli_error($con));
                if(mysqli_num_rows($res) > 0){
                    $row=mysqli_fetch_array($res);
                        echo $warehouse_id . "&nbsp" . $city . "&nbsp" . $street . "&nbsp" . $house . "&nbsp" . $building . "<br>";
                    
                }
            }
        }
    }
    */

    //получить список ролей партнера
    function partner_role_receive($con, $counterparty_id){
        
        $query = "SELECT `role_partner` FROM `t_role_partner` WHERE `counterparty_id`='$counterparty_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $role_partner = $row[0];
                echo $role_partner . "<br>";
            }
        }else echo "null" . "<br>";
    }

    //получить список адресов складов по городу
    function receive_partner_warehouse_list_001($con, $city, $region, $warehouse_type){  //help_warehose
        $query="SELECT wi.warehouse_info_id,
                    wi.city,
                    wi.street,
                    wi.house,
                    wi.building,
                    w.warehouse_id,
                    wi.help_warehose
                FROM  t_warehous w
                    JOIN t_warehouse_info wi ON wi.region = '$region' AND wi.warehouse_info_id=w.warehouse_info_id
                                                                  AND wi.active = '1'
                WHERE w.warehouse_type = '$warehouse_type' AND w.active = '1'";       
    $result=mysqli_query($con,$query)or die (mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_array($result)){
            $warehouse_info_id=$row[0]; 
            $city=$row[1]; 
            $street=$row[2];  
            $house=$row[3];
            $building=$row[4];
            $warehouse_id=$row[5];
            $help_warehose=$row[6];

            echo $warehouse_info_id . "&nbsp" . $street . "&nbsp" . $house . "&nbsp" . 
                        $building . "&nbsp" . $warehouse_id . "&nbsp" .$help_warehose."<br>";
        }
    }

       /* $query = "SELECT `warehouse_id`  FROM `t_warehouse_type`
                             WHERE `warehouse_type` ='$warehouse_type' AND `active`='1'";            
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0];  

                $query = "SELECT `street`, `house`, `building` FROM `t_warehouse` 
                            WHERE `warehouse_id`=$warehouse_id AND `city` = '$city' AND `active`='1'";
                $res=mysqli_query($con,$query)or die (mysqli_error($con));
                $row_w=mysqli_fetch_array($res);
                $street=$row_w[0];    $house=$row_w[1];   $building=$row_w[2];
                echo $warehouse_id . "&nbsp" . $street . "&nbsp" . $house . "&nbsp" .$building . "<br>";
            }
        } */  
    }
    /*
    //получить список адресов складов по городу
    function receive_partner_warehouse_list($con, $city,$warehouse_type){      
        $query = "SELECT `warehouse_id`  FROM `t_warehouse_type`
                             WHERE `warehouse_type` ='$warehouse_type' AND `active`='1'";            
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0];  

                $query = "SELECT `street`, `house`, `building` FROM `t_warehouse` 
                            WHERE `warehouse_id`=$warehouse_id AND `city` = '$city' AND `active`='1'";
                $res=mysqli_query($con,$query)or die (mysqli_error($con));
                $row_w=mysqli_fetch_array($res);
                $street=$row_w[0];    $house=$row_w[1];   $building=$row_w[2];
                echo $warehouse_id . "&nbsp" . $street . "&nbsp" . $house . "&nbsp" .$building . "<br>";
            }
        }   
    }
    */
    //внести в `t_provider_collect_product` id товаров из заказа для дальнейшего конороля движения товаров
   /* function writeOrder_id_table_son($con, $order_id){
        $query = "SELECT `order_product_id` FROM `t_order_product` WHERE `order_id`=' $order_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $order_product_id = $row[0];
               // echo $order_product_id . "<br>";
            $query = "UPDATE `t_order_product` SET `prov_collect_prod_id`='$order_product_id'
                         WHERE `order_product_id`='$order_product_id'";
            $res=mysqli_query($con,$query)or die (mysqli_error($con));

            $query = "INSERT INTO `t_provider_collect_product`( `prov_collect_prod_id`) VALUES ('$order_product_id')";
            $res=mysqli_query($con,$query)or die (mysqli_error($con));
        }
    }*/
    //получить сосотояние checkBox 0=false , 1=true
    function provider_product_in_box($con, $order_product_id,$provider_product_in_box){
        $query="SELECT `order_processing_id` FROM `t_order_processing` 
                WHERE `order_product_id`='$order_product_id' AND `processing_condition`='$provider_product_in_box'";
                $result=mysqli_query($con,$query)or die (mysqli_error($con));
        $res = '0';
        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_array($result);
            if($row[0] != '0'){
                $res = '1';
            }           
        } 
        return $res;
    }

    //получить роль пользователя
    function userRoleReceive($con, $user_id){        
        $query = "SELECT  `role` FROM `t_user` WHERE `user_id` = $user_id";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($row = mysqli_fetch_array($result)){
            $role = $row[0];
            return $role;
        }else{
            return 0;
        }
        
    }
    //запрос на присвоение поставщику роли "provider"
    function transferRequestNewProvider($con, $role, $user_id, $comment){
        $query = "INSERT INTO `t_role_moderator`( `role_for_moderation`, `user_id`, `comment`) 
                    VALUES ('$role','$user_id','$comment')";
        $result = mysqli_query($con, $query);
        if ($result === FALSE) {
            die( mysql_error() );
            return false;
        }else{
            return true;
        }
    }
     //изменить статус заказа (заказ отправлен в обработку) order_active = 1 и указать склад выдачи товара заказчику
  /*   function chengeOrderActive_001($con,$order_id, $warehouse_id,  $counterparty_id){//$getOrderMillis,
        //$get_order_date = date('Y-m-d H:i:s', $getOrderMillis / 1000);

        $query="UPDATE `t_order` SET `order_active` = '1'WHERE `order_id` = '$order_id'";
                     //, `warehouse_id`='$warehouse_id',`counterparty_id`='$counterparty_id', `get_order_date`='$get_order_date'
        $result = mysqli_query($con, $query) or die (mysqli_error($con));            
        return $result;
    } */
    //изменить статус заказа (заказ отправлен в обработку) order_active = 1 и указать склад выдачи товара заказчику
    function chengeOrderActive($con,$order_id, $warehouse_id, $getOrderMillis){
        $get_order_date = date('Y-m-d H:i:s', $getOrderMillis / 1000);

        $query="UPDATE t_order SET order_active = 1 , `warehouse_id`='$warehouse_id', `get_order_date`='$get_order_date'
                     WHERE order_id = $order_id";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));            
        return $result;
    }    
    //проверить если этот user заказывает первый раз то передать заказ на контроль в "t_order_from_new_buyer"
  /*  function sendOrderToModeration($con, $order_id){
        $query = "SELECT  `user_id` FROM `t_order` WHERE `order_id` = '$order_id'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $user_id=$row[0];


        $query = "SELECT `order_id` FROM `t_order` WHERE `user_id` = '$user_id' AND `executed` = '1'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) == 0) {
            $query1 = "INSERT INTO `t_order_from_new_buyer`(`order_id`) 
                                VALUES ('$order_id')";
            $result1 = mysqli_query($con, $query1) or die (mysqli_error($con));
        }
    }*/
    // получить все активные заказы $order_list
    function search_my_active_orders_list($con, $user_id, $counterparty_id){
        $order_list = array();
        $query = "SELECT `order_id`, `get_order_date_millis`, `category_in_order`, `delivery`,`joint_buy` FROM `t_order`
                        WHERE `counterparty_id` = '$counterparty_id' 
                            AND `order_active` = '0' AND `order_deleted`='0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));   
        if(mysqli_num_rows($result) > 0){ 
            while($row = mysqli_fetch_array($result)){
                $order_id =  $row[0];
                $date_millis = $row[1];
                $category = $row[2];
                $delivery = $row[3];
                $joint_buy = $row[4];
                
                //если заказ оформляется по телефону поставщиком то его не показывать в списке заказов 
                //которые оформляет покупатель самостоятельно
                $query="SELECT `order_by_phone_id`
                            FROM `t_order_by_phone` WHERE `order_id`='$order_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                if(mysqli_num_rows($res) == 0){
                   $order_list[] = ['order_id'=>$order_id,'date_millis'=>$date_millis,'category'=>$category
                                    ,'delivery'=>$delivery,'joint_buy'=>$joint_buy];
                   echo $order_id."&nbsp".$date_millis."&nbsp".$category."&nbsp".$delivery."&nbsp".$joint_buy."<br>";
                }

            }
        }else{
            $order_id = 0; 
            $date_millis = 0;
            $category = "0";
            $delivery = "0";
            $joint_buy ="0";
            echo $order_id."&nbsp".$date_millis."&nbsp".$category."&nbsp".$delivery."&nbsp".$joint_buy."<br>";
        }    
        return $order_list;   
    }
        //проверить есть ли начатый заказ
    function checkMyActiveOrder_001($con, $user_id, $counterparty_id){         
        $order_id=0;        
        $query = "SELECT `order_id` FROM `t_order`
                        WHERE `counterparty_id` = '$counterparty_id' 
                            AND `order_active` = '0' AND `order_deleted`='0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));   
        if(mysqli_num_rows($result) > 0){        
            while($row = mysqli_fetch_array($result)){
                $order_id =  $row[0];
                
                $query="SELECT `order_by_phone_id`
                            FROM `t_order_by_phone` WHERE `order_id`='$order_id'";
                $res = mysqli_query($con, $query) or die (mysql_error($link));
                if(mysqli_num_rows($res) == 0){
                    break 1;
                }else{
                    $order_id = 0;
                }

            }
        }
         return $order_id;        
    }
    //проверить есть ли начатый заказ
    function checkMyActiveOrder($con, $user_uid){ 
        //найти user_id
        $order_id=0;
        $userID = checkUserID($con, $user_uid);//найти user_id
        $query = "SELECT `order_id` FROM `t_order` WHERE `user_id` = '$userID' AND `order_active` = '0'";
        $result = mysqli_query($con, $query) or die (mysql_error($link));   
        if(mysqli_num_rows($result) > 0){        
            while($row = mysqli_fetch_array($result)){
                $order_id =  $row[0];
                
                $query="SELECT `order_by_phone_id`
                            FROM `t_order_by_phone` WHERE `order_id`='$order_id'";
                $res = mysqli_query($con, $query) or die (mysql_error($link));
                if(mysqli_num_rows($res) == 0){
                    break 1;
                }else{
                    $order_id = 0;
                }

            }
        }
         return $order_id;        
    }
    /*
    //проверить есть ли начатый заказ
    function checkMyActiveOrder($con, $user_uid){ 
        //найти user_id
        $userID = checkUserID($con, $user_uid);//найти user_id
        $query = "SELECT `order_id` FROM `t_order` WHERE `user_id` = '$userID' AND `order_active` = '0'";
         $result = mysqli_query($con, $query) or die (mysql_error($link));
         $row = mysqli_fetch_array($result);
         $order_id =  $row[0];
         return $order_id;
    }
    */
    //найти user_id
   /* function checkUserID($con, $user_uid){ 
    $query="SELECT `user_id` FROM `t_user` WHERE `unique_id` = '$user_uid'";
    $result=mysqli_query($con, $query) or die(mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $user_id = $row[0];
    //echo "user_id " . $user_id . "<br>";
    return $user_id;
    }*/
    //получить counterparty_id
    function counterparty_id($con, $counterparty_tax_id){
        $query = "SELECT `counterparty_id` FROM `t_counterparty` WHERE  `taxpayer_id_number`='$counterparty_tax_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $counterparty_id = $row[0];
        return $counterparty_id;
    }
   
   
   mysqli_close($con);
?>