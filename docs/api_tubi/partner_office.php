<?php
	
	include 'connect.php';
    include 'text.php';
    include_once 'helper_classes.php';
	 
	 mysqli_query($con,"SET NAMES 'utf8'");

     //receive_list_orders
     //receive_list_order_product
     //receive_list_order_product_for_issue
     //receive_list_buyers_company
     //receive_list_partner_warehouse
     //receive_list_buyers_company_for_issue 
     //receive_delivery_address    
     //receive_invoice_info_list
     //write_order_is_completed
     //write_collect_product_for_sale
     //update_out_active_in_table
     //show_document
     //make_document

     //t_invoice_key
    

    //получить список склад партнер контрагента
    if(isset($_GET['receive_list_partner_warehouse'])){
        $taxpayer_id = $_GET['company_tax_id'];     
        
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
                
        receive_list_partner_warehouse($con,$counterparty_id);
            
    }//получить список покупателей
    else  if(isset($_GET['receive_list_buyers_company'])){
        $warehouse_id = $_GET['warehouse_id'];     
                        
        receive_list_buyers_company($con,$warehouse_id);
            
    }//получить список продуктов для комплектации этому покупателю
    else  if(isset($_GET['receive_list_order_product'])){
        $order_id = $_GET['order_id']; 
        $providerWarehouse_id = $_GET['providerWarehouse_id'];    
                        
        receive_list_order_product($con,$order_id, $providerWarehouse_id);
            
    }
    //сделать запись в (t_warehouse_inventory_in_out and t_order_product) о том что товар собран 
    else  if(isset($_GET['write_collect_product_for_sale'])){
        $partner_warehouse_id = $_GET['warehouse_id']; 
        $order_id = $_GET['order_id']; 
        $product_inventory_id = $_GET['product_inventory_id']; 
        $quantity = $_GET['quantity']; 
        $transaction_name = $_GET['transaction_name']; 
        $collected = $_GET['collected']; 
        $user_uid = $_GET['user_uid'];    
        $invoice_key_id = $_GET['invoice_key_id'];
        $counterparty_tax_id = $_GET['counterparty_tax_id']; 
        $order_product_id = $_GET['order_product_id']; 

        $user_id=checkUserID($con, $user_uid);
        //найти counterparty_id
        $partner_counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);
                        
        write_collect_product_for_sale($con,$partner_warehouse_id,$order_id,$product_inventory_id,$quantity,
                                        $transaction_name,$collected,$user_id, $invoice_key_id
                                        ,$partner_counterparty_id,$order_product_id);
            
    }
    //получить список продуктов для выдачи этому покупателю
    else  if(isset($_GET['receive_list_order_product_for_issue'])){
        $order_id = $_GET['order_id']; 
                        
        receive_list_order_product_for_issue($con,$order_id);
            
    }//получить список покупателей для выдачи заказов
    else  if(isset($_GET['receive_list_buyers_company_for_issue'])){
        $warehouse_id = $_GET['warehouse_id'];     
                        
        receive_list_buyers_company_for_issue($con,$warehouse_id);
            
    }
    //изменить out_active в таблице
    else  if(isset($_GET['update_out_active_in_table'])){
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];   
        $user_uid = $_GET['user_uid'];   
        $checked = $_GET['checked']; 

        $user_id=checkUserID($con, $user_uid);
                        
        update_out_active_in_table($con,$user_id, $warehouse_inventory_id, $checked);
            
    }//сделать запись, заказ выполнен
    //и записать в (мой каталог t_catalog_is_mine) товары которые не показывают покупателю в каталоге
    else  if(isset($_GET['write_order_is_completed'])){
        $user_uid = $_GET['user_uid'];   
        $order_id = $_GET['order_id']; 

        $user_id=checkUserID($con, $user_uid);
                        
        //сделать запись, заказ выполнен
        write_order_is_completed($con,$user_id, $order_id);
        //записать в (мой каталог t_catalog_is_mine) товары которые не показывают покупателю в каталоге
        search_and_write_poduct_for_my_catalog($con, $user_id, $order_id);
            
    }
    //очистить(удалить) все записи о сборке товара
    else  if(isset($_GET['delete_all_product_collect_this_order'])){         
        $order_id = $_GET['order_id'];

        delete_all_product_collect_this_order($con, $order_id);            
    }
    //получить список заказов
    else  if(isset($_GET['receive_list_orders'])){         
        $counterparty_tax_id = $_GET['counterparty_tax_id'];
        $limit = $_GET['limit'];

         //найти counterparty_id
        $partner_counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);

        receive_list_orders($con, $partner_counterparty_id, $limit);            
    }
    //получить номер товарной накладной
    else  if(isset($_GET['receive_invoice_info_list'])){         
        $order_id = $_GET['order_id'];

        receive_invoice_info_list($con, $order_id);            
    }
    //создать документ
    else  if(isset($_GET['make_document'])){  
        $invoice_key_id = $_GET['invoice_key_id'];      
        $docName = $_GET['docName'];
        $docNum = $_GET['docNum'];

        make_document($con, $invoice_key_id,  $docName, $docNum);            
    }
    //показать документ
    else  if(isset($_GET['show_document'])){  
        $invoice_key_id = $_GET['invoice_key_id'];      
        $docName = $_GET['docName'];
        $docNum = $_GET['docNum'];

        show_document_for_PDF($con, $invoice_key_id,  $docName, $docNum);            
    }
    //получить адрес доставки заказа
    else  if(isset($_GET['receive_delivery_address'])){  
        $order_id = $_GET['order_id'];      

        receive_delivery_address($con, $order_id);            
    }

    //получить адрес доставки заказа
    function receive_delivery_address($con, $order_id){
        //получить адрес доставки покупателю
        $address_for_delivery = receiveDeliveryAddress($con, $order_id);
        
        echo $address_for_delivery;
    }
    //показать документ
    function show_document_for_PDF($con, $invoice_key_id,  $docName, $docNum){
        //получить данные компаний участников
        $query="SELECT `out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`
                        FROM `t_invoice_key` WHERE `invoice_key_id`='$invoice_key_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
            $out_counterparty_id = $row[0];
            $out_warehouse_id = $row[1];
            $in_counterparty_id = $row[2];
            $in_warehouse_id = $row[3];

            //получить данные(информацию) о компании
            $companyInfoList = receiveCompanyInfo($con, $out_counterparty_id);
                $out_companyInfoString = $companyInfoList['companyInfoString'];

            //получить данные(информацию) о компании
            $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
                $in_companyInfoString = $companyInfoList['companyInfoString'];

            //получить данные(информацию) о складе и компании id
            $out_warehouseInfoList = warehouseInfo($con,$out_warehouse_id);           
                $out_warehouseInfoString = $out_warehouseInfoList['warehouseInfoString'];

            //получить данные(информацию) о складе и компании id
            if($in_warehouse_id != 0){
                $warehouseInfoList = warehouseInfo($con,$in_warehouse_id);           
                    $in_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];
            }
            else{
                //проверить есть доставка
                $query="SELECT op.order_id,
                                o.delivery                            
                            FROM t_order_product op 
                                JOIN t_order o ON o.order_id = op.order_id
                            WHERE  op.invoice_key_id ='$invoice_key_id'";
                $result = mysqli_query($con, $query) or die (mysqli_error($con));
                $row=mysqli_fetch_array($result);
                $order_id = $row[0];
                $delivery = $row[1];

                // доставка есть
                if($delivery == 1){
                    //получить адрес доставки покупателю
                    $address_for_delivery = receiveDeliveryAddress($con, $order_id);
                    $in_warehouseInfoString = $GLOBALS['deliveri'] .": ". $address_for_delivery;
                }
                else{
                    //нет доставки
                    $in_warehouseInfoString = $GLOBALS['from_the_warehouse'] .": ". $GLOBALS['data_is_not'];
                }                
            }         
            

        //получить дату создания документа
        $query="SELECT `created_at` FROM `t_document_deal` 
                    WHERE `invoice_key_id`='$invoice_key_id' and `document_name` = '$docName' and `document_num` = '$docNum'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
            $created_at = $row[0];

            $date = date_parse( $created_at);
            $date_created_doc = $date['day'].".".$date['month'].".".$date['year'];

        //отправить данные перед списком товаров
        echo "out_companyInfoString"."&nbsp".$out_companyInfoString."<br>";
        echo "in_companyInfoString"."&nbsp".$in_companyInfoString."<br>";
        echo "out_warehouseInfoString"."&nbsp".$out_warehouseInfoString."<br>";
        echo "in_warehouseInfoString"."&nbsp".$in_warehouseInfoString."<br>";
        echo "date_created_doc"."&nbsp".$date_created_doc."<br>";

        //получить данные о товарах в документе
        $query="SELECT  op.quantity,
                        op.price,
                        op.price_process,
                        dd.description_docs
                        FROM t_order_product op
                            JOIN t_description_docs dd ON dd.description_docs_id = op.description_docs_id
                        WHERE op.invoice_key_id='$invoice_key_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $quantity = $row[0];
            $price = $row[1];
            $price_process = $row[2];
            $description_docs = $row[3];

            $full_price = $price + $price_process;

                //echo "quantity $quantity; price $price; price_process $price_process; full_price $full_price; description_docs $description_docs  <br>";
                echo $description_docs."&nbsp".$quantity."&nbsp".$full_price."&nbsp".$date_created_doc."<br>";
        }       

    }
    //создать докумен
    function make_document($con, $invoice_key_id, $docName, $docNum){
        //найти ключ
        /*$query="SELECT `invoice_key_id` FROM `t_order_product` WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $invoice_key_id = $row[0];*/

        //найти 
       /* $query="SELECT  `warehouse_id` FROM `t_order` WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $warehouse_id = $row[0];

        //получить данные(информацию) о складе и компании id
        $warehouseInfoList = warehouseInfo($con,$warehouse_id);
        $counterparty_id = $warehouseInfoList['counterparty_id'];*/

        //найти компанию поставщика
        $query="SELECT  `out_counterparty_id` FROM `t_invoice_key` WHERE `invoice_key_id`='$invoice_key_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
            $out_counterparty_id = $row[0];

        //найти последний (номер документа) для компании поставщика
        $document_num=0;
        $query="SELECT `document_num` FROM `t_document_deal` 
                    WHERE `counterparty_id`='$out_counterparty_id' and `document_name`='$docName' ORDER BY `document_num` DESC LIMIT 1";//`invoice_key_id`='$invoice_key_id' and 
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_array($result);
            $document_num = $row[0];
        }            
        $docNum = $document_num + 1;
        //создать документ (номер документа)
        $query="INSERT INTO `t_document_deal`(`invoice_key_id`   , `counterparty_id`    , `document_name`, `document_num`) 
                                        VALUES ('$invoice_key_id','$out_counterparty_id','$docName'   , '$docNum')";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
            //показываем данные по документу
        if($res){
            echo "RESULT_OK"."&nbsp". $docNum;
        }else{
            echo "messege"."&nbsp".$GLOBALS['dont_write_docs'];
        }
        //echo "<br>";
        //echo "invoice_key_id $invoice_key_id; counterparty_id  $counterparty_id; docName  $docName; docNum  $docNum <br>";
    }

        //получить номерa  накладной
        function receive_invoice_info_list($con, $order_id){
        $query="SELECT `invoice_key_id` FROM `t_order_product` WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $invoice_key_id = $row[0];

        $query="SELECT `document_name`, `document_num` FROM `t_document_deal` WHERE `invoice_key_id`='$invoice_key_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $document_name = $row[0];
                $document_num = $row[1];

                echo $document_name."&nbsp".$document_num."<br>";
            }
        }else echo "messege"."&nbsp".$GLOBALS['dont_write_docs']. "<br>"; 

    } 
    //получить список заказов
    function receive_list_orders($con, $partner_counterparty_id, $limit){
        //получить все (склад-партнер) этого партнера
        $warehouse_type = "partner";
        $warehouse_info_list  = receive_counterparty_warehouses($con, $partner_counterparty_id, $warehouse_type);
        foreach($warehouse_info_list as $k => $warehouse_info){
           $warehouse_id = $warehouse_info['warehouse_id'];

           $query="SELECT `order_id`, `executed`, `counterparty_id`, `get_order_date_millis`, `date_order_start`, `up_user_id` 
                            FROM `t_order` WHERE `warehouse_id`='$warehouse_id' and `order_active`='1' and `order_deleted`='0' ORDER BY `order_id` DESC;";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            while($row=mysqli_fetch_array($result)){
                $order_id = $row[0];
                $executed = $row[1];
                $buyer_counterparty_id = $row[2];
                $get_order_date_millis = $row[3];
                $date_order_start = $row[4];
                //$date_order_start = $row[4];

                $order_summ = 0;
                //получить сумму заказа
                $query="SELECT `quantity`, `price`, `price_process`,`invoice_key_id`
                        FROM `t_order_product` WHERE `order_id`='$order_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                while($row=mysqli_fetch_array($res)){
                    $quantity = $row[0];
                    $price = $row[1];
                    $price_process = $row[2];
                    $invoice_key_id = $row[3];
    
                    $order_summ = $order_summ + ($quantity * ($price + $price_process));
                } 
                //получить данные(информацию) о компании
                $companyInfoList = receiveCompanyInfo($con, $buyer_counterparty_id);
                $buyer_companyInfoString_short = $companyInfoList['companyInfoString_short'];

                $date = date_parse( $date_order_start);
                $date_order_start = $date['day'].".".$date['month'].".".$date['year'];

                $get_order_date = date("d.m.Y", $get_order_date_millis/1000);

                //echo "Date: $get_order_date <br>";

                //echo "day; $newDate<br>";
                echo $order_id."&nbsp".$executed."&nbsp".$buyer_companyInfoString_short."&nbsp".$get_order_date_millis."&nbsp".$date_order_start."&nbsp".$order_summ."&nbsp"
                            .$get_order_date."&nbsp".$invoice_key_id."<br>";

                //echo "order_id: ".$order_id." warehouse_id: ".$warehouse_id." get_order_date_millis: ".$get_order_date_millis." order_summ: ".$order_summ." <br>";
                
            } 
       }
    }
    //очистить(удалить) все записи о сборке товара
    function delete_all_product_collect_this_order($con, $order_id){
        $query="SELECT `warehouse_inventory_id` FROM `t_order_product` WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $warehouse_inventory_id = $row[0];
            //удалить строки sale(товар собран)
            $query="DELETE FROM `t_warehouse_inventory_in_out` 
                                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        } 
        //обнулить данные о сборке товара на складе партнера
        $query="UPDATE `t_order_product` SET `warehouse_inventory_id`='0'
                    WHERE `order_id`='$order_id'";
        mysqli_query($con, $query) or die (mysqli_error($con));
    }
    //записать в (мой каталог t_catalog_is_mine) товары которые не показывают покупателю в каталоге
    function search_and_write_poduct_for_my_catalog($con, $user_id, $order_id){
        $query="SELECT op.product_inventory_id,
                        p.product_id
                    FROM t_order_product op
                        JOIN t_product_inventory pin ON pin.product_inventory_id = op.product_inventory_id
                        JOIN t_product p  ON p.product_id = pin.product_id
                        JOIN t_category c ON c.category_id = p.category_id and c.catalog_id = '37'
                    WHERE op.order_id ='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
           
            while($row=mysqli_fetch_array($result)){
                $product_inventory_id = $row[0];
                $product_id = $row[1];                

                //echo "hi $product_id. <br>";
                $product_list[] = array("product_id"=> "$product_id", "product_inventory_id" => "$product_inventory_id");
            }            
        }

        foreach($product_list as $k => $product){
           /* foreach($product as $key => $v){
                echo "$v <br>";
            }*/
            $product_id = $product['product_id'];
            $product_inventory_id = $product['product_inventory_id'];

            //echo " $product_id => $product_inventory_id <br>";

            //проверить есть в в таблице эти товары
            $query="SELECT `catalog_is_mine`,  `active` FROM `t_catalog_is_mine` 
                        WHERE `product_id`='$product_id' and `product_inventory_id`='$product_inventory_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            if(mysqli_num_rows($result) == 0){
                //echo " hi 0 <br>";
                //получить counterparty_id владельца каталога
                //$query="SELECT  `counterparty_id` FROM `t_user` WHERE `user_id`='$user_id'";

                $query="SELECT `counterparty_id`
                                FROM `t_order` WHERE `order_id`='$order_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row=mysqli_fetch_array($res);
                $counterparty_id=$row[0];

                // записать товар в таблицу мои товары
                $query="INSERT INTO `t_catalog_is_mine`
                                    ( `product_id`, `product_inventory_id`, `counterparty_id`, `active`) 
                            VALUES ('$product_id','$product_inventory_id', '$counterparty_id',  '1'   )";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
            }
        }
    
    }
    //сделать запись, заказ выполнен
    function write_order_is_completed($con,$user_id, $order_id){
        $data = date('Y-m-d H:i:s');
        $query="UPDATE `t_order` SET `executed`='1',`up_user_id`='$user_id',`date_order_finish`='$data' 
                         WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($result){
            echo "RESULT_OK";
        }else{
            echo "error". "&nbsp".$GLOBALS['error_try_again_later_text'];
        }
    }
    //изменить out_active в таблице
    function update_out_active_in_table($con,$user_id, $warehouse_inventory_id, $checked){
        $data = date('Y-m-d H:i:s');
        $query="UPDATE `t_warehouse_inventory_in_out`
                     SET `out_active`='$checked',`out_user_id`='$user_id',`out_updated_at`='$data'
                    WHERE  `warehouse_inventory_id`='$warehouse_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($result){
            echo "RESULT_OK";
        }else{
            echo "error". "&nbsp".$GLOBALS['error_try_again_later_text'];
        }
    }
    //получить список покупателей для выдачи заказов
    function receive_list_buyers_company_for_issue($con,$warehouse_id){
        //получить список не выполненных заказов на этот склад
        $query="SELECT o.order_id,
                        c.abbreviation,
                        c.counterparty,
                        c.taxpayer_id_number
                     FROM t_order o
                        JOIN t_user u ON u.user_id = o.user_id
                        JOIN t_counterparty c ON c.counterparty_id = u.counterparty_id
                     WHERE `warehouse_id`='$warehouse_id' AND `order_active`='1' 
                                AND `executed`='0' AND `order_deleted`='0'";
         $result = mysqli_query($con, $query) or die (mysqli_error($con));
         if(mysqli_num_rows($result) > 0){
           // echo "id: ";
            while($row = mysqli_fetch_array($result)){
                $order_id=$row[0];  
                $abbreviation=$row[1];
                $counterparty=$row[2]; 
                $taxpayer_id_number = $row[3];    
                
                //найти в заказе хоть один собранный товар(если есть то показать компанию в списке выдачи)
                $flag = false;
                $query="SELECT `order_product_id` FROM `t_order_product` WHERE `order_id`='$order_id' and `collected`='1'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                if(mysqli_num_rows($res) > 0){
                   $flag = true;
                }   
                if($flag){
                    echo $order_id. "&nbsp".$abbreviation. "&nbsp".$counterparty."&nbsp".$taxpayer_id_number."<br>";
                }
                             
            }
         }else{
            echo "messege" . "&nbsp". $GLOBALS['this_warehouse_is_not_order'];
        }
    }

    //получить список продуктов для выдачи этому покупателю
    function receive_list_order_product_for_issue($con,$order_id){
        $query="SELECT `order_product_id`, `product_inventory_id`, `quantity`, `collected`, `warehouse_inventory_id` 
                    FROM `t_order_product` WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $order_product_id=$row[0];  
            $product_inventory_id=$row[1];
            $quantity_to_order=$row[2]; 
            $collected=$row[3];
            $warehouse_inventory_id=$row[4];

            //получить check (out_active) заказ выполнен или нет   
            if($warehouse_inventory_id != 0){
                $query="SELECT  `out_active` FROM `t_warehouse_inventory_in_out`
                                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($res);
                $out_active=$row[0];
                  
            }else{
                $out_active= 0;
            }
                                       
           
            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);
    
            $product_id=$product_list['product_id'];
            $category=$product_list['category'];
            $brand=$product_list['brand']; 
            $characteristic=$product_list['characteristic'];
            $type_packaging=$product_list['type_packaging']; 
            $unit_measure=$product_list['unit_measure'];
            $weight_volume=$product_list['weight_volume']; 
            $quantity_package=$product_list['quantity_package'];
            $image_url=$product_list['image_url'];
            $price=$product_list['price'];
            $description=$product_list['description'];
           
            echo $product_id ."&nbsp".$product_inventory_id."&nbsp".$category ."&nbsp".$brand ."&nbsp".$characteristic.
                        "&nbsp".$type_packaging ."&nbsp".$unit_measure ."&nbsp".$weight_volume ."&nbsp".$quantity_package .
                        "&nbsp".$image_url .
                        "&nbsp".$order_product_id ."&nbsp".$quantity_to_order ."&nbsp".$warehouse_inventory_id .
                        "&nbsp".$collected."&nbsp".$out_active."&nbsp".$price."&nbsp".$description."<br>";
        }       
    }
    //сделать запись в (t_warehouse_inventory_in_out and t_order_product) о том что товар собран
    function write_collect_product_for_sale($con,$partner_warehouse_id,$order_id,$product_inventory_id,$quantity,
                                                $transaction_name,$collected,$user_id, $invoice_key_id
                                                ,$partner_counterparty_id,$order_product_id){
        //проверить создан ключ документов
        /*if($invoice_key_id == 0){
            //создать ключ
            $query="INSERT INTO `t_invoice_key`(`invoice_key_id`, `out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`, `car_id`, `save`, `closed`, `created_at`) 
                                        VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]','[value-7]','[value-8]','[value-9]')";

        }*/
        $query="INSERT INTO `t_warehouse_inventory_in_out`
                        ( `transaction_name`, `product_inventory_id`, `quantity`, `out_warehouse_id`   , `collected`,`out_active`,`creator_user_id`) 
                 VALUES ('$transaction_name','$product_inventory_id','$quantity','$partner_warehouse_id','$collected',     '0'    ,'$user_id')";                                                                
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $warehouse_inventory_id = mysqli_insert_id($con);

        $query="UPDATE `t_order_product` SET `collected`='$collected', `warehouse_inventory_id`='$warehouse_inventory_id'
                             WHERE `order_id`='$order_id' and `product_inventory_id`='$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
    }
   
    //получить список продуктов для комплектации этому покупателю
    function receive_list_order_product($con, $order_id, $providerWarehouse_id){
        //получить список всех товаров в заказе
        $query="SELECT `order_product_id`, `product_inventory_id`, `quantity`,`collected`,`warehouse_inventory_id`
                     FROM `t_order_product` WHERE order_id='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $order_product_id=$row[0];  
            $product_inventory_id=$row[1];
            $quantity_to_order=$row[2]; 
            $collected=$row[3];
            $warehouse_inventory_id=$row[4];
    
            //получить остаток товара на складе партнера            
            $partner_stock_quantity=stock_product_to_warehouse($con, $providerWarehouse_id, $product_inventory_id);

            //получить колличество в укомплектованных заказах
            $partner_stock_collect=stock_collect_to_order($con,$providerWarehouse_id, $product_inventory_id);
            //вычесть остаток на складе из собранных заказов и показать свободное колличество
            $partner_stock_quantity -= $partner_stock_collect;
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

            //получить колличество собранного в заказе товара
            $quantity_of_colected=0;
            $invoice_key_id = 0;
            $out_active=0;
            if($warehouse_inventory_id != 0){
                $query="SELECT `quantity`, `out_active`,`invoice_key_id` FROM `t_warehouse_inventory_in_out` 
                                WHERE `warehouse_inventory_id`='$warehouse_inventory_id' 
                                and `collected`='1'";// and `out_active`='0'";
                 $res = mysqli_query($con, $query) or die (mysqli_error($con));
                 if(mysqli_num_rows($res) > 0){
                    $row = mysqli_fetch_array($res);
                    $quantity_of_colected=$row[0];
                    $out_active=$row[1];
                    $invoice_key_id=$row[2];
                 }                 
            }

           /* $address_for_delivery="";
            if($deliveryKey == 1){
                //получить адрес доставки покупателю
                $address_for_delivery = receiveDeliveryAddress($con, $order_id);
            }*/
            

            //получить описание товара
            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);

            $product_id=$product_list['product_id'];
            $category=$product_list['category'];
            $brand=$product_list['brand']; 
            $characteristic=$product_list['characteristic'];
            $type_packaging=$product_list['type_packaging']; 
            $unit_measure=$product_list['unit_measure'];
            $weight_volume=$product_list['weight_volume']; 
            $quantity_package=$product_list['quantity_package'];
            $image_url=$product_list['image_url'];
            $storage_conditions=$product_list['storage_conditions'];
            $description=$product_list['description'];

            //показать колличество на складе поставщика за вычетом товара который собран в заказ(на палет)
            //получить колличество собранного товара в заказах

            echo $product_id ."&nbsp".$product_inventory_id."&nbsp".$category ."&nbsp".$brand ."&nbsp".$characteristic.
                        "&nbsp".$type_packaging ."&nbsp".$unit_measure ."&nbsp".$weight_volume ."&nbsp".$quantity_package .
                        "&nbsp".$image_url ."&nbsp".$order_product_id .
                        "&nbsp".$quantity_to_order ."&nbsp".$partner_stock_quantity ."&nbsp".$provider_counterparty_id .
                        "&nbsp".$provider_abbreviation ."&nbsp".$provider_counterparty.
                        "&nbsp".$storage_conditions."&nbsp".$collected."&nbsp".$quantity_of_colected.
                        "&nbsp".$invoice_key_id."&nbsp".$out_active."&nbsp".$description."<br>";
        }
        

    }
  
     //получить список покупателей
    function receive_list_buyers_company($con,$warehouse_id){
        //получить список не выполненных заказов на этот склад
        $query="SELECT o.order_id,
                        c.abbreviation,
                        c.counterparty,
                        c.taxpayer_id_number,
                        o.order_deleted,
                        o.delivery
                     FROM t_order o
                        JOIN t_user u ON u.user_id = o.user_id
                        JOIN t_counterparty c ON c.counterparty_id = u.counterparty_id
                     WHERE `warehouse_id`='$warehouse_id' AND `order_active`='1' AND `executed`='0'";
         $result = mysqli_query($con, $query) or die (mysqli_error($con));
         if(mysqli_num_rows($result) > 0){
           // echo "id: ";
            while($row = mysqli_fetch_array($result)){
                $order_id=$row[0];  
                $abbreviation=$row[1];
                $counterparty=$row[2]; 
                $taxpayer_id_number = $row[3];  
                $order_deleted = $row[4];
                $delivery=$row[5];
                
                //проверить есть в этом заказе уже собранные позиции
                $query="SELECT `warehouse_inventory_id`
                         FROM `t_order_product` WHERE `order_id`='$order_id'";
                $res=mysqli_query($con, $query) or die(mysqli_error($con));
                while($row=mysqli_fetch_array($res)){
                    $collect_product_for_delete=$row[0];
                    if($collect_product_for_delete != 0){
                        break;
                    }
                }

                echo $order_id. "&nbsp".$abbreviation. "&nbsp".$counterparty."&nbsp".$taxpayer_id_number.
                            "&nbsp".$order_deleted."&nbsp".$collect_product_for_delete."&nbsp".$delivery."<br>";
            }
         }else{
            echo "messege" . "&nbsp". $GLOBALS['this_warehouse_is_not_order'];
        }
    }
    //получить список покупателей
    /*function receive_list_buyers_company($con,$warehouse_id){
        //получить список не выполненных заказов на этот склад
        $query="SELECT o.order_id,
                        c.abbreviation,
                        c.counterparty,
                        c.taxpayer_id_number,
                        o.order_deleted
                     FROM t_order o
                        JOIN t_user u ON u.user_id = o.user_id
                        JOIN t_counterparty c ON c.counterparty_id = u.counterparty_id
                     WHERE `warehouse_id`='$warehouse_id' AND `order_active`='1' AND `executed`='0'";
         $result = mysqli_query($con, $query) or die (mysqli_error($con));
         if(mysqli_num_rows($result) > 0){
           // echo "id: ";
            while($row = mysqli_fetch_array($result)){
                $order_id=$row[0];  
                $abbreviation=$row[1];
                $counterparty=$row[2]; 
                $taxpayer_id_number = $row[3];  
                $order_deleted = $row[4];
                
                //проверить есть в этом заказе уже собранные позиции
                $query="SELECT `warehouse_inventory_id`
                         FROM `t_order_product` WHERE `order_id`='$order_id'";
                $res=mysqli_query($con, $query) or die(mysqli_error($con));
                while($row=mysqli_fetch_array($res)){
                    $collect_product_for_delete=$row[0];
                    if($collect_product_for_delete != 0){
                        break;
                    }
                }

                echo $order_id. "&nbsp".$abbreviation. "&nbsp".$counterparty."&nbsp".$taxpayer_id_number.
                            "&nbsp".$order_deleted."&nbsp".$collect_product_for_delete."<br>";
            }
         }else{
            echo "messege" . "&nbsp". $GLOBALS['this_warehouse_is_not_order'];
        }
    }*/
     //получить список склад партнер контрагента
     function receive_list_partner_warehouse($con,$counterparty_id){
        $query="SELECT wi.warehouse_info_id,
                    wi.city,
                    wi.street,
                    wi.house,
                    wi.building,
                    w.warehouse_id
                FROM   t_warehouse_info wi
                    JOIN t_warehous w ON  w.warehouse_info_id=wi.warehouse_info_id AND w.warehouse_type = 'partner'                                                                 
                WHERE wi.counterparty_id ='$counterparty_id'";
       /* $query="SELECT `city`, `street`, `house`, `building`FROM `t_warehouse` 
                                    WHERE `warehouse_id`='$outWarehouse_id'";*/
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $warehouse_info_id=$row[0]; 
                $city=$row[1];
                $street=$row[2];
                $house=$row[3];
                $building=$row[4];
                $warehouse_id=$row[5];

                echo $warehouse_info_id."&nbsp".$warehouse_id ."&nbsp".$city ."&nbsp".$street ."&nbsp".$house ."&nbsp".
                        $building ."<br>";
            }
        }else{
            echo "messege" . "&nbsp". $GLOBALS['data_to_warehouse_is_not'];
        }              

    }
    mysqli_close($con);
?>