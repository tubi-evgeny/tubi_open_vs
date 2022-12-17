<?php
    include 'connect.php';
    include 'text.php';
    include_once 'helper_classes.php';
	 
	 mysqli_query($con,"SET NAMES 'utf8'");

     //t_warehouse_info
     //get_product_info
     //         deleted        //get_invoice_list_for_create
     //go_order_partner_activation
     //get_product_for_redacted
     //distribution_orders_by_warehouses
     //distribution_orders_provider_to_partners
     //chenge_logistic_product_info
     //chenge_checked_provider
     //create_tipe_warehouse
     //write_collect_product
     //write_collect_product_for_sale
     //write_provider_collect_product_for_sale
     //write_image_name_to_table
     //write_this_deal_delivery_status
     //receive_list_recipient_and_product_for_them
     //receive_list_order_product
     //receive_list_product_for_collect
     //receive_list_provider_orders
     //receive_list_warehouse
     //receive_list_collect_product
     //receive_list_company_for_shipment  
     //receive_list_company_for_collect
     //receive_list_delivery_to_accept 
     //receive_my_product_int_partner_warehouses     
     //receive_warehouse_info
     //receive_all_my_warehouse    
     //receive_my_warehouse     
     //receive_invoice_key
     //receive_invoice_info_list
     //receive_out_warehouse_info
     //update_in_active_to_table
     //product_provider_array
     //product_in_partner_warehouse_array
     //my_provider_warehouses_count
     //show_document
     //search_my_product_int_partner_warehouses
     //$data
	 
//receive_product_info

     //получить список товаров на (склад поставщик, склад хранения)
    if(isset($_GET['product_provider_array'])){
        $taxpayer_id = $_GET['tax_id'];
        $warehouse_id = $_GET['warehouse_id'];

        //товары которые не прошли модерацию
        $result = product_provider_for_moderation($con, $taxpayer_id,$warehouse_id);
        
        $result .= product_provider_array_001($con, $taxpayer_id,$warehouse_id);
        //$result = product_provider_array($con, $taxpayer_id);

        if(empty($result)){
            $result = "messege" . "&nbsp" . $GLOBALS['product_is_not_this_warehouse'] . "<br>";
        }

        echo $result;
        
    }//получить список товаров на (склад партнер)
    else if(isset($_GET['product_in_partner_warehouse_array'])){
        $taxpayer_id = $_GET['tax_id'];
        $warehouse_id = $_GET['warehouse_id'];
        
        $result = product_in_partner_warehouse_array_001($con, $taxpayer_id,$warehouse_id);  
        //$result = product_in_partner_warehouse_array($con, $taxpayer_id,$warehouse_id);       

        if(empty($result)){
            $result = "messege" . "&nbsp" . $GLOBALS['product_is_not_this_warehouse'] . "<br>";
        }

        echo $result;
        
    }
    //product_in_partner_warehouse_array
    // поставщик получает все заказы не выполненные покупателей для сборки товара для агента
    else if(isset($_GET['receive_list_buyers_orders_product'])){
        $taxpayer_id = $_GET['taxpayer_id'];

        $result = receive_list_buyers_orders_product_001($con,$taxpayer_id);

        if(empty($result)){
            $result = "messege" . "&nbsp" . "Нет каталога" . "<br>";
        }

        echo $result;
    }
    //изменить состояние checkBox
    else if(isset($_GET['check_box_condition_update_or_insert'])){
        $prov_collect_prod_id = $_GET['order_product_id'];
        $processingCondition = $_GET['processingCondition'];
        $user_uid = $_GET['user_uid'];
        //$search_for_condition = 'provider_product_in_box';
            
        $user_id = search_user_id($con, $user_uid);
        
            //внести запись о изменении состояния заказа
            $result = update_check_box_condition_001($con,$prov_collect_prod_id, $processingCondition, $user_id);
    
        
        
        echo $result;
    }//добавить(создать)  склад партнера в BD
    else if(isset($_GET['create_new_warehouse'])){
        $counterparty_tax_id = $_GET['counterparty_tax_id'];
        $user_uid = $_GET['user_uid'];
        $region = $_GET['region'];
        $district = $_GET['district'];
        $city = $_GET['city'];
        $street = $_GET['street'];
        $house = $_GET['house'];
        $building = $_GET['building'];
        $signboard = $_GET['signboard'];

        create_new_warehouse($con,$user_uid,$counterparty_tax_id,$region,$district,$city,$street,$house,$building,$signboard);
        
    }//найти все склады контрагента
    else if(isset($_GET['receive_all_my_warehouse'])){
        $counterparty_tax_id = $_GET['counterparty_tax_id'];

        //receive_all_my_warehouse($con,$counterparty_tax_id);
        receive_all_my_warehouse_001($con,$counterparty_tax_id);
        
    }//редактировать склад партнер в BD
    else if(isset($_GET['edit_warehouse'])){
        $warehouse_info_id = $_GET['warehouse_info_id'];    
        $user_uid = $_GET['user_uid'];
        $region = $_GET['region'];
        $district = $_GET['district'];
        $city = $_GET['city'];
        $street = $_GET['street'];
        $house = $_GET['house'];
        $building = $_GET['building'];
        $signboard = $_GET['signboard'];
        

        edit_warehouse($con,$warehouse_info_id,$user_uid,$region,$district,$city,$street,$house,$building,$signboard);
        
    }//создать изменить тип склада
    else if(isset($_GET['create_tipe_warehouse'])){
        $warehouse_info_id = $_GET['warehouse_info_id'];
        $warehouse_tipe = $_GET['warehouse_tipe'];
        $active = $_GET['active'];

        create_tipe_warehouse($con,$warehouse_info_id,$warehouse_tipe,$active);
        
    }//получить список складов с которых надо сделать отгрузку по заказам, 
        //колличество складов на которые надо отправить товар
        //колличество позиций товара в заказе с отгрузкой с каждого склада поставщика
        //и колличество единиц товара в заказе с отгрузкой с каждого склада поставщика
    else if(isset($_GET['distribution_orders_by_warehouses'])){
        $taxpayer_id = $_GET['counterparty_tax_id'];
        
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);

        distribution_orders_by_warehouses_001($con,$counterparty_id);
        //distribution_orders_by_warehouses($con,$counterparty_id);
        
    }
        //получить список товаров для сборки для складов партнера
    else if(isset($_GET['distribution_orders_provider_to_partners'])){
        $taxpayer_id = $_GET['counterparty_tax_id'];
        $my_warehouse_id = $_GET['my_warehouse_id'];
        $warehouse_type = 'provider';
            
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
        
        //distribution_orders_provider_to_partners_001($con,$counterparty_id, $my_warehouse_id,$warehouse_type);
        distribution_orders_provider_to_partners_002($con,$counterparty_id, $my_warehouse_id,$warehouse_type);
            
    }  
    //получить список моих складов (складПартнера)
    else if(isset($_GET['my_provider_warehouses_count'])){
        $taxpayer_id = $_GET['counterparty_tax_id'];
        //$warehouse_type = 'provider';
            
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
        
        my_provider_warehouses_count_001($con,$counterparty_id);
        //my_provider_warehouses_count($con,$counterparty_id, $warehouse_type);
            
    }  //сделать запись в (warehouse_inventory_in_out) о том что товар собран
    else if(isset($_GET['write_collect_product'])){
        $my_warehouse_id = $_GET['my_warehouse_id'];
        $partner_warehouse_id = $_GET['partner_warehouse_id']; 
        $product_inventory_id = $_GET['product_inventory_id'];
        $quantity = $_GET['quantity'];
        $transaction_name = $_GET['transaction_name'];
        $collected = $_GET['collected'];  
        $user_uid = $_GET['user_uid']; 
        $logistic_product = $_GET['logistic_product'];   
        
        $user_id = checkUserID($con, $user_uid);
            
        write_collect_product_001($con,$my_warehouse_id, $partner_warehouse_id, $product_inventory_id, 
                                $quantity, $transaction_name, $collected, $user_id, $logistic_product);
        /*
        write_collect_product($con,$my_warehouse_id, $partner_warehouse_id, $product_inventory_id, 
                                $quantity, $transaction_name, $collected, $user_id, $logistic_product);
        */
        
    }
    //получить все собранные warhouse_inventory_id но не отправленные и показать их
    else if(isset($_GET['receive_list_collect_product'])){
        $my_warehouse_id = $_GET['my_warehouse_id'];
        $partner_warehouse_id = $_GET['partner_warehouse_id'];        
        
        receive_list_collect_product($con,$my_warehouse_id, $partner_warehouse_id);
            
    } 
    //переписать доставку у товарa
    else if(isset($_GET['chenge_logistic_product_info'])){
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];
        $logistic_product = $_GET['logistic_product'];
        $user_uid = $_GET['user_uid']; 
        $update=date('Y-m-d H:i:s');
        
        $user_id = checkUserID($con, $user_uid);
        
        chenge_logistic_product_info($con,$warehouse_inventory_id, $logistic_product,$user_id,$update);
            
    }//получить список получателей(перевозчиков) и все собранные товары для выдачи получателям
    else if(isset($_GET['receive_list_recipient_and_product_for_them'])){
        $warehouse_id = $_GET['warehouse_id'];               
        
        receive_list_recipient_and_product_for_them($con,$warehouse_id);
        //receive_list_recipient_and_product_for_them_001($con,$warehouse_id);
            
    }//получить информацию по (склад назначения)
    else if(isset($_GET['receive_warehouse_info'])){
        $in_warehouse_id = $_GET['in_warehouse_id'];               
        
        receive_warehouse_info($con,$in_warehouse_id);
            
    }
    //получить все доставленные warhouse_inventory_id но не принятые и показать их
    else if(isset($_GET['receive_list_delivery_to_accept'])){
        $warehouse_id = $_GET['warehouse_id'];     
                 
        receive_list_delivery_to_accept_001($con,$warehouse_id);
        //receive_list_delivery_to_accept($con,$warehouse_id);
            
    }
    //получить список складов контрагента
    else if(isset($_GET['receive_list_warehouse'])){
        $taxpayer_id = $_GET['company_tax_id'];     
        
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);   
        
        receive_list_warehouse_001($con,$counterparty_id);
        //receive_list_warehouse($con,$counterparty_id);
            
    }
    //получить данные склада
    else if(isset($_GET['receive_out_warehouse_info'])){
        $warehouse_id = $_GET['warehouse_id'];     
        
        receive_out_warehouse_info($con,$warehouse_id);
            
    }
    //получить данные авто
    else if(isset($_GET['receive_car_info'])){
        $car_id = $_GET['car_id'];     
        
        receive_car_info($con,$car_id);
            
    }
    //получить данные на товар
    else if(isset($_GET['get_product_info'])){
        $product_inventory_id = $_GET['product_inventory_id'];     
        
        get_product_info($con,$product_inventory_id);
            
    }
    //добавить ключ(документы) и установить или отменить галочку (передан товар)
    else if(isset($_GET['chenge_checked_provider'])){
        $warehouseInventory_id = $_GET['warehouseInventory_id'];
        $checked = $_GET['checked'];     
        
        chenge_checked_provider($con,$warehouseInventory_id,$checked);
            
    }
    //добавить собранный товар в warehouse_inventory_in_out и в t_logistic_product
    else if(isset($_GET['update_in_active_to_table'])){
        $warehouseInventory_id = $_GET['warehouseInventory_id'];
        $in_active = $_GET['in_active'];  
        $logistic_product  = $_GET['logistic_product'];  
        $give_out = $_GET['give_out'];  
        $user_uid = $_GET['user_uid'];
          
        $user_id = checkUserID($con, $user_uid);
        
        update_in_active_to_table($con,$warehouseInventory_id,$in_active,$logistic_product,$give_out,$user_id);
            
    }
    //загружаем имя картинки в t_image_moderation (таблицу)
    // меняем картинку в t_product_inventory
    else if(isset($_GET['write_image_name_to_table'])){
        $product_inventory_id = $_GET['product_inventory_id'];
        $imageName = $_GET['imageName']; 
        $user_uid = $_GET['user_uid'];    
        
        $user_id = checkUserID($con, $user_uid);
        
        write_image_name_to_table($con,$product_inventory_id,$imageName,$user_id);
            
    }//получить список складов хранения своего товара
    else if(isset($_GET['receive_my_warehouse'])){
        $taxpayer_id = $_GET['counterparty_tax_id'];
         
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
        
        //my_provider_warehouses_count_001($con,$counterparty_id);
        
        receive_my_warehouse($con,$counterparty_id);
            
    }//получить колличество позиций товара в заказе
    else if(isset($_GET['receive_quantity_position_to_order'])){
        $order_id = $_GET['order_id'];         
                
        receive_quantity_position_to_order($con,$order_id);
            
    }//найти мои товары на складах моих партнеров
    else if(isset($_GET['search_my_product_int_partner_warehouses'])){
        $taxpayer_id = $_GET['counterparty_tax_id']; 
        
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
                
        search_my_product_int_partner_warehouses($con,$counterparty_id);
            
    }
    //получить мои товары на складах моих партнеров
    else if(isset($_GET['receive_my_product_int_partner_warehouses'])){
        $taxpayer_id = $_GET['counterparty_tax_id']; 
        
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
                
        receive_my_product_int_partner_warehouses($con,$counterparty_id);
            
    }
    //получить список доступных к созданию документов
    /*else if(isset($_GET['get_invoice_list_for_create'])){
        $warehouseInventory_id = $_GET['warehouseInventory_id'];         
                
        get_invoice_list_for_create($con,$warehouseInventory_id);
            
    }*/
     //получить список складов поставщика
    else if(isset($_GET['receive_list_provider_warehouse'])){
        $taxpayer_id = $_GET['company_tax_id'];     
        
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
                
        receive_list_provider_warehouse($con,$counterparty_id);
            
    }
    //получить список покупателей для выдачи товара
    else  if(isset($_GET['receive_list_company_for_shipment'])){
        $warehouse_id = $_GET['warehouse_id'];     
                        
        receive_list_company_for_shipment($con,$warehouse_id);
            
    }
    //получить список покупателей для сборки товара
    else  if(isset($_GET['receive_list_company_for_collect'])){
        $warehouse_id = $_GET['warehouse_id'];     
                        
        receive_list_company_for_collect($con,$warehouse_id);
            
    }
    //получить список продуктов для комплектации этому покупателю
    else  if(isset($_GET['receive_list_order_product'])){
        $order_partner_id = $_GET['order_partner_id']; 
        $providerWarehouse_id = $_GET['providerWarehouse_id'];    
                        
        receive_list_order_product($con,$order_partner_id,$providerWarehouse_id);
            
    }
     //сделать запись в (t_warehouse_inventory_in_out and t_order_product) о том что товар собран 
     else  if(isset($_GET['write_collect_product_for_sale'])){
        $warehouse_id = $_GET['warehouse_id']; 
        $order_partner_id = $_GET['order_partner_id']; 
        $product_inventory_id = $_GET['product_inventory_id']; 
        $quantity = $_GET['quantity']; 
        $transaction_name = $_GET['transaction_name']; 
        $collected = $_GET['collected']; 
        $user_uid = $_GET['user_uid'];    

        $user_id=checkUserID($con, $user_uid);
                        
        write_collect_product_for_sale($con,$warehouse_id,$order_partner_id,$product_inventory_id,$quantity,
                                        $transaction_name,$collected,$user_id);
            
    }
    //активировать заказ для начала оформления документов
    //объединить и внести все товары в t_warehouse_inventory
    else  if(isset($_GET['go_order_partner_activation'])){
        $order_partner_id = $_GET['order_partner_id']; 
        $user_uid = $_GET['user_uid']; 
        $transaction_name = $_GET['transaction_name']; 

        //найти user_id
        $user_id=checkUserID($con, $user_uid);
        
        //перенесен в helper_classes.php
        go_order_partner_activation($con,$order_partner_id, $transaction_name, $user_id);
            
    }
    //показать список из warehouse_inventory для сборки товара
    else  if(isset($_GET['receive_list_product_for_collect'])){
        $order_partner_id = $_GET['order_partner_id']; 
        $providerWarehouse_id = $_GET['providerWarehouse_id'];  
                        
        receive_list_product_for_collect($con,$order_partner_id, $providerWarehouse_id);
            
    }
    //сделать запись в (t_warehouse_inventory_in_out and t_order_product_part) о том что товар собран 
    else  if(isset($_GET['write_provider_collect_product_for_sale'])){
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];
        $order_partner_id = $_GET['order_partner_id']; 
        $collected = $_GET['collected']; 

        //$user_id=checkUserID($con, $user_uid);
                        
        write_provider_collect_product_for_sale($con, $warehouse_inventory_id, $collected, $order_partner_id);
            
    }
    //записать статус доставки
    else  if(isset($_GET['write_this_deal_delivery_status'])){
        $order_partner_id = $_GET['order_partner_id']; 
        $logistic = $_GET['logistic']; 
                        
        write_this_deal_delivery_status($con,$order_partner_id,$logistic);
            
    }
    //получить описание товара
    else  if(isset($_GET['receive_product_info'])){
        $inventory_id_string = $_GET['inventory_id_string']; 
        $invoice_key_id = $_GET['invoice_key_id'];
                                
        receive_product_information($con, $inventory_id_string, $invoice_key_id);
            
    }
    //получить ключ документа или создать
    else  if(isset($_GET['receive_invoice_key'])){
        $out_warehouse_id = $_GET['out_warehouse_id'];
        $in_warehouse_id = $_GET['in_warehouse_id'];
        $car_id = $_GET['car_id'];
                                
        receive_invoice_key($con, $out_warehouse_id, $in_warehouse_id, $car_id);
            
    }
    //получить список заказов поставщику
    else  if(isset($_GET['receive_list_provider_orders'])){         
        $counterparty_tax_id = $_GET['counterparty_tax_id'];
        $limit = $_GET['limit'];

         //найти counterparty_id
        $provider_counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);

        receive_list_provider_orders($con, $provider_counterparty_id, $limit);            
    }
     //получить номер товарной накладной
     else  if(isset($_GET['receive_invoice_info_list'])){         
        $order_partner_id = $_GET['order_partner_id'];

        receive_invoice_info_list($con, $order_partner_id);            
    }
     //показать документ
     else  if(isset($_GET['show_document'])){  
        $order_partner_id = $_GET['order_partner_id']; 
        $invoice_key_id = $_GET['invoice_key_id'];      
        $docName = $_GET['docName'];
        $docNum = $_GET['docNum'];

        if($docName == "заказ"){
            show_order_doc_for_PDF($con, $order_partner_id, $docName, $docNum); 
        }else{
            show_document_to_PDF($con, $invoice_key_id,  $docName, $docNum); 
        }
                   
    }
    //получить товар для редактирования
    else  if(isset($_GET['get_product_for_redacted'])){         
        $product_inventory_id = $_GET['product_inventory_id'];

        get_product_for_redacted($con, $product_inventory_id);            
    }
    //получить список имен каталогов
    else  if(isset($_GET['get_catalog_name_list'])){         

        get_catalog_name_list($con);            
    }
    //получить список имен категорий
    else  if(isset($_GET['get_category_name_list'])){         
        $catalog_name = $_GET['catalog_name'];
        $all_category_list = $_GET['all_category_list'];

        get_category_name_list($con,$catalog_name,$all_category_list);            
    }
    //получить список вариантов меры веса
    else  if(isset($_GET['get_unit_measure_list'])){         

        get_unit_measure_list($con);            
    }
    //получить список типов упаковки товара
    else  if(isset($_GET['get_tipe_pacaging_list'])){         

        get_tipe_pacaging_list($con);            
    }
    //Запустить редактирование данных о продукте
    else  if(isset($_GET['go_redact_data'])){         
        $product_id = $_GET['product_id'];
        $product_inventory_id = $_GET['product_inventory_id'];
        $catalog = $_GET['catalog'];
        $category = $_GET['category'];
        $product_name = $_GET['product_name'];
        $brand = $_GET['brand'];
        $characteristic = $_GET['characteristic'];
        $type_packaging = $_GET['type_packaging'];
        $unit_measure = $_GET['unit_measure'];
        $weight_volume = $_GET['weight_volume'];
        $quantity_package = $_GET['quantity_package'];
        $storage_conditions = $_GET['storage_conditions'];
        $price = $_GET['price'];
        $min_sell = $_GET['min_sell'];
        $multiple_of = $_GET['multiple_of'];
        $description = $_GET['description'];
        go_redact_data($con,$product_id,$product_inventory_id,$catalog,$category,$product_name
                    ,$brand,$characteristic,$type_packaging,$unit_measure,$weight_volume
                    ,$quantity_package,$storage_conditions,$price,$min_sell,$multiple_of,$description);            
    }
    //Запустить редактирование данных о продукте
    function go_redact_data($con,$product_id,$product_inventory_id,$catalog,$category,$product_name
                            ,$brand,$characteristic,$type_packaging,$unit_measure,$weight_volume
                            ,$quantity_package,$storage_conditions,$price,$min_sell,$multiple_of
                            ,$description){
        try{
            //получить данные из таблицы и сравнить с переданными из приложения данными
            $query="SELECT  c.catalog,
                            cat.category,
                            pnam.product_name,
                            br.brand,
                            chr.characteristic,
                            tp.type_packaging,
                            um.unit_measure,
                            pr.weight_volume,
                            pr.storage_conditions,
                            pin.price,
                            pin.quantity_package,
                            pin.min_sell,
                            pin.multiple_of,
                            d.description

                            FROM t_product_inventory pin 
                                    JOIN t_product pr           ON pr.product_id        = pin.product_id
                                    JOIN t_category cat         ON cat.category_id      = pr.category_id
                                    JOIN t_catalog c            ON c.catalog_id         = cat.catalog_id
                                    JOIN t_product_name pnam    ON pnam.product_name_id = pr.product_name_id
                                    JOIN t_brand br             ON br.brand_id          = pr.brand_id
                                    JOIN t_characteristic chr   ON chr.characteristic_id= pr.characteristic_id
                                    JOIN t_type_packaging tp    ON tp.type_packaging_id = pr.type_packaging_id
                                    JOIN t_unit_measure um      ON um.unit_measure_id   = pr.unit_measure_id
                                    JOIN t_description d        ON d.description_id     = pin.description_id                               
                            WHERE pin.product_inventory_id='$product_inventory_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($result);        
            $my_catalog = $row[0];
            $my_category = $row[1];
            $my_product_name = $row[2];
            $my_brand = $row[3];
            $my_characteristic = $row[4];
            $my_type_packaging = $row[5];
            $my_unit_measure = $row[6];
            $my_weight_volume = $row[7];        
            $my_storage_conditions = $row[8];
            $my_price = $row[9];
            $my_quantity_package = $row[10];
            $my_min_sell = $row[11];
            $my_multiple_of = $row[12];
            $my_description = $row[13];

            //проверить наличие изменений , если изменения есть то ->
            if($category != $my_category || $product_name != $my_product_name || $brand != $my_brand
                || $characteristic != $my_characteristic || $type_packaging != $my_type_packaging
                || $unit_measure != $my_unit_measure || $weight_volume != $my_weight_volume 
                || $storage_conditions != $my_storage_conditions  ){

                //найти или создать category_id
                if($category != $my_category){
                    add_category($con, $catalog, $category);
                }
                if($product_name != $my_product_name){
                    add_product_name($con, $product_name);
                }
                if($brand != $my_brand){
                    add_brand($con, $brand);
                }
                if($characteristic != $my_characteristic){
                    add_characteristic($con, $characteristic);
                }
                if($type_packaging != $my_type_packaging){
                    add_type_packaging($con, $type_packaging);
                }
                if($unit_measure != $my_unit_measure){
                    add_unit_measure($con,$unit_measure);
                }
                //получить id 
                $category_id = getCategory_id($con, $category);
                $product_name_id = get_product_name_id($con, $product_name);
                $brand_id = get_brand_id($con, $brand);
                $characteristic_id = get_characteristic_id($con, $characteristic);
                $type_packaging_id = get_type_packaging_id($con, $type_packaging);
                $unit_measure_id = get_unit_measure_id($con, $unit_measure);
                
                //найти копию продукта, или создать новый и вернуть id 
                $product_id = searchOrMakeProduct($con, $category_id, $product_name_id ,$brand_id
                            , $characteristic_id,$type_packaging_id ,$unit_measure_id
                            , $weight_volume, $storage_conditions);
                                        
            }    
            //найти description_id в таблице нет =0; есть = передать id;
            $description_id = descriptionIdSearch($con,$description);
            //--если описание отсутсвует то 
            if($description_id == 0){  
                //записать описание в таблицу
                writeDescriptionToTable($con, $description); 
                $description_id = descriptionIdSearch($con,$description);
            }

            //заменить product_id  и другие переменные в t_product_inventory
            $query="UPDATE `t_product_inventory` SET `product_id`='$product_id',`price`='$price',`quantity_package`='$quantity_package'
                                ,`min_sell`='$min_sell',`multiple_of`='$multiple_of',`description_id`='$description_id'                             
                            WHERE `product_inventory_id`='$product_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }catch(Exception $ex){
            echo "error ex = $ex <br>";
        }
        echo "hello <br>";
    }
    //получить список типов упаковки товара
    function get_tipe_pacaging_list($con){
        $query="SELECT `type_packaging_id`, `type_packaging` FROM `t_type_packaging` WHERE 1";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $type_packaging_id = $row[0];
            $type_packaging = $row[1];

            echo $type_packaging_id . "&nbsp" . $type_packaging."<br>";
        }
    }
    //получить список вариантов меры веса
    function get_unit_measure_list($con){
        $query="SELECT `unit_measure_id`, `unit_measure` FROM `t_unit_measure` WHERE 1";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $unit_measure_id = $row[0];
            $unit_measure = $row[1];

            echo $unit_measure_id . "&nbsp" . $unit_measure."<br>";
        }
    }
    //получить список имен категорий
    function get_category_name_list($con,$catalog_name,$all_category_list){  
        $query="SELECT `catalog_id` FROM `t_catalog` WHERE `catalog`='$catalog_name'"; 
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $catalog_id=$row[0];
        //показать только категории этого каталога
        if($all_category_list == 0){
            $query="SELECT `category_id`, `category`
                    FROM `t_category` WHERE `catalog_id`='$catalog_id'";
        }
        //показать все категории
        else{
            $query="SELECT `category_id`, `category` FROM `t_category` WHERE 1";
        }
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $category_id = $row[0];
            $category = $row[1];

            echo $category_id . "&nbsp" . $category."<br>";
        }
        
    }
    //получить список имен каталогов
    function get_catalog_name_list($con){
        $query="SELECT `catalog_id`, `catalog` FROM `t_catalog` WHERE 1";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $catalog_id = $row[0];
            $catalog = $row[1];

            echo $catalog_id . "&nbsp" . $catalog."<br>";
        }
    }

    //получить товар для редактирования
    function get_product_for_redacted($con, $product_inventory_id){
        //получить данные(информацию) по товару
        $product_list = receive_product_info($con, $product_inventory_id);    
        $product_id=$product_list['product_id'];
        $catalog_id=$product_list['catalog_id'];
        $catalog=$product_list['catalog'];
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
        $price=$product_list['price'];            
        $product_name_from_provider=$product_list['product_name_from_provider'];
        $min_sell=$product_list['min_sell'];
        $multiple_of=$product_list['multiple_of'];
        $description_prod=$product_list['description_prod'];
        $product_info=$product_list['product_info'];
       // $description=$product_list['description'];

       echo $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category ."&nbsp"
            . $product_name . "&nbsp" . $brand . "&nbsp" . $characteristic. "&nbsp" . $type_packaging ."&nbsp" 
            . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" 
            . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $storage_conditions . "&nbsp" 
            . $price."&nbsp" . $product_name_from_provider."&nbsp" 
            . $min_sell ."&nbsp" . $multiple_of . "&nbsp" . $description_prod ."&nbsp" 
            . $product_info. "&nbsp" . $catalog . "<br>";

    }
    //показать документ
    function show_document_to_PDF($con, $invoice_key_id,  $docName, $docNum){
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
                $in_warehouseInfoString = $GLOBALS['data_is_not'];
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
        $query="SELECT ii.quantity,
                        ii.price,
                        dd.description_docs,
                        inin.in_product_name,
                        ii.product_inventory_id 
                    FROM t_invoice_info ii 
                        JOIN t_description_docs dd ON dd.description_docs_id = ii.description_docs_id
                        JOIN t_inventory_vs_inproductname inin ON inin.product_inventory_id = ii.product_inventory_id
                    WHERE ii.invoice_key_id = '$invoice_key_id'";        
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $quantity = $row[0];
            $price = $row[1];
            $description_docs = $row[2];
            $product_name_from_provider = $row[3];
            $product_inventory_id = $row[4];

             //получить данные(информацию) по товару
             $product_list = receive_product_info($con, $product_inventory_id);
             $quantity_package=$product_list['quantity_package'];

            //echo $description_docs."&nbsp".$quantity."&nbsp".$price."&nbsp".$product_name_from_provider."<br>";
            echo $description_docs."&nbsp".$quantity."&nbsp".$price."&nbsp".$product_name_from_provider."&nbsp".$quantity_package."<br>";
        }       

    }
    //показать документ
    function show_order_doc_for_PDF($con, $order_partner_id, $docName, $docNum){
        //получить данные компаний участников
        $query="SELECT `out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`, `created_at`
                FROM `t_order_partner` WHERE `order_partner_id`='$order_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
            $out_counterparty_id = $row[0];
            $out_warehouse_id = $row[1];
            $in_counterparty_id = $row[2];
            $in_warehouse_id = $row[3];
            $created_at = $row[4];

            $date = date_parse( $created_at);
            $date_created_doc = $date['day'].".".$date['month'].".".$date['year'];

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
            $in_warehouseInfoString = $GLOBALS['data_is_not'];
        } 

        //отправить данные перед списком товаров
        echo "out_companyInfoString"."&nbsp".$out_companyInfoString."<br>";
        echo "in_companyInfoString"."&nbsp".$in_companyInfoString."<br>";
        echo "out_warehouseInfoString"."&nbsp".$out_warehouseInfoString."<br>";
        echo "in_warehouseInfoString"."&nbsp".$in_warehouseInfoString."<br>";
        echo "date_created_doc"."&nbsp".$date_created_doc."<br>";

        //получить данные о товарах в документе
        $query="SELECT  op.quantity,
                        op.price,
                        dd.description_docs,
                        inin.in_product_name,
                        op.product_inventory_id
                    FROM t_order_product_part op 
                        JOIN t_description_docs dd ON dd.description_docs_id = op.description_docs_id 
                        JOIN t_inventory_vs_inproductname inin ON inin.product_inventory_id = op.product_inventory_id
                    WHERE op.order_partner_id='$order_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $quantity = $row[0];
            $price = $row[1];
            $description_docs = $row[2];
            $product_name_from_provider = $row[3];
            $product_inventory_id = $row[4];

            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);
            $quantity_package=$product_list['quantity_package'];


                //echo "quantity $quantity; price $price; price_process $price_process; full_price $full_price; description_docs $description_docs  <br>";
                echo $description_docs."&nbsp".$quantity."&nbsp".$price."&nbsp".$product_name_from_provider."&nbsp".$quantity_package."<br>";
        }    
    }
    //получить номерa  накладной
    function receive_invoice_info_list($con, $order_partner_id){
        $query="SELECT `invoice_key_id` FROM `t_order_product_part` 
                    WHERE `order_partner_id`='$order_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $invoice_key_id = $row[0];
            $invoice_key_id_list[] = $invoice_key_id;
           // echo "invoice_key_id=$invoice_key_id <br>";
        }

        $invoice_key_id_list = array_unique($invoice_key_id_list);

        foreach($invoice_key_id_list as $k => $invoice_key_id){
            if($invoice_key_id != 0){
                $query="SELECT `document_name`, `document_num` FROM `t_document_deal` 
                            WHERE `invoice_key_id`='$invoice_key_id'";
                $result = mysqli_query($con, $query) or die (mysqli_error($con));
                if(mysqli_num_rows($result) > 0){
                    while($row=mysqli_fetch_array($result)){
                        $document_name = $row[0];
                        $document_num = $row[1];

                        echo $document_name."&nbsp".$document_num."&nbsp".$invoice_key_id."<br>";
                    }
                }else {
                    echo "messege"."&nbsp".$GLOBALS['dont_write_docs']. "<br>";
                }
            }else{
                echo "messege"."&nbsp".$GLOBALS['dont_write_docs']. "<br>";
            }
        }
    }
    //получить список заказов поставщику
    function receive_list_provider_orders($con, $provider_counterparty_id, $limit){
        //получить все (склад-партнер) этого партнера
        $warehouse_type = "provider";
        $warehouse_info_list  = receive_counterparty_warehouses($con, $provider_counterparty_id, $warehouse_type);
        foreach($warehouse_info_list as $k => $warehouse_info){
            $warehouse_id = $warehouse_info['warehouse_id'];
            $query="SELECT `order_partner_id`, `executed`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`
                                , `get_order_date_millis`, `created_at` FROM `t_order_partner` 
                    WHERE `out_warehouse_id`='$warehouse_id' and `order_active`='1' ORDER BY `order_partner_id` DESC;";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            while($row=mysqli_fetch_array($result)){
                $order_partner_id = $row[0];
                $executed = $row[1];
                $out_warehouse_id = $row[2];
                $in_counterparty_id = $row[3];
                $in_warehouse_id = $row[4];
                $get_order_date_millis = $row[5];
                $created_at = $row[6];

                $order_summ = 0;
                //получить сумму заказа
                $query="SELECT `quantity`, `price`, `price_process`, `invoice_key_id`
                            FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                while($row=mysqli_fetch_array($res)){
                    $quantity = $row[0];
                    $price = $row[1];
                    $price_process = $row[2];
                    $invoice_key_id = $row[3];
    
                    $order_summ = $order_summ + ($quantity * $price);
                } 
                //получить данные(информацию) о компании партнера
                $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
                $in_companyInfoString_short = $companyInfoList['companyInfoString_short'];

                //получить данные(информацию) о складе партнера
                $warehouseInfoList = warehouseInfo($con,$in_warehouse_id);
                $in_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

                //получить данные(информацию) о складе поставщика 
                $warehouseInfoList = warehouseInfo($con,$out_warehouse_id);
                $out_warehouse_info_id = $warehouseInfoList['warehouse_info_id'];

                //разобрать дату от  sql (timestamp)
                $date = date_parse( $created_at);
                $date_order_start = $date['day'].".".$date['month'].".".$date['year'];

                $get_order_date = date("d.m.Y", $get_order_date_millis/1000);

                echo $order_partner_id."&nbsp".$executed."&nbsp".$out_warehouse_info_id."&nbsp"
                    .$out_warehouse_id."&nbsp"
                    .$in_companyInfoString_short."&nbsp".$in_warehouseInfoString."&nbsp"
                    .$date_order_start."&nbsp".$order_summ."&nbsp".$get_order_date."&nbsp"
                    .$invoice_key_id."<br>";
            }
            
        }
    }
    //получить ключ документа или создать
    function receive_invoice_key($con, $out_warehouse_id, $in_warehouse_id, $car_id){
        $invoice_key_id=0;
        $save=0;
        $query="SELECT `invoice_key_id`,`save` FROM `t_invoice_key` 
                    WHERE `closed`='0' and `out_warehouse_id`='$out_warehouse_id' 
                        and `in_warehouse_id`='$in_warehouse_id' and `car_id`='$car_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
                $invoice_key_id=$row[0];
                $save=$row[1];
                
        }else{
            //получить данные(информацию) о складе и компании id
            $warehouseInfoList = warehouseInfo($con,$out_warehouse_id);            
            $out_counterparty_id = $warehouseInfoList['counterparty_id'];

            //получить данные(информацию) о складе и компании id
            $warehouseInfoList = warehouseInfo($con,$in_warehouse_id);            
            $in_counterparty_id = $warehouseInfoList['counterparty_id'];
            
            //создать
            $query="INSERT INTO `t_invoice_key`(`out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`, `car_id`) 
                                        VALUES ('$out_counterparty_id','$out_warehouse_id','$in_counterparty_id','$in_warehouse_id','$car_id')";
            mysqli_query($con, $query) or die (mysqli_error($con));
            $invoice_key_id = mysqli_insert_id($con);            
        }
        if($invoice_key_id > 0){
            echo "RESULT_OK" . "&nbsp" . $invoice_key_id ."&nbsp".$save . "<br>";
        }else{
            echo "messege"."&nbsp".$GLOBALS['error_try_again_later_text'];
        }       

    }

    //получить описание товара
    function receive_product_information($con,$inventory_id_string, $invoice_key_id){
        //разобрать строку "inventory_id_string" в массив
        $product_inventory_id_list = explode(";", $inventory_id_string);

        //получить описание товара по id
        foreach($product_inventory_id_list as $k => $product_inventory_id){
            $query = "SELECT doc.description_docs                        
                            FROM t_invoice_info inf 
                                JOIN t_description_docs doc ON doc.description_docs_id = inf.description_docs_id
                            WHERE inf.invoice_key_id='$invoice_key_id' 
                                and inf.product_inventory_id='$product_inventory_id'";
             $result = mysqli_query($con, $query) or die (mysqli_error($con));
             if($result){
                $row = mysqli_fetch_array($result);
                $description_docs=$row[0];
                echo $product_inventory_id . "&nbsp" . $description_docs . "<br>";
             }else{
                 echo "messege"."&nbsp". $GLOBALS['description_is_empty_text'];
             }
            
        }       
    }
   
     //записать статус доставки
     function write_this_deal_delivery_status($con,$order_partner_id,$logistic){
        //получить из заказа warehouse_inventory_id список 
        $query="SELECT  `warehouse_inventory_id`FROM `t_order_product_part` 
                            WHERE `order_partner_id`='$order_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $warehouse_inventory_id=$row[0];

            // изменить статус доставки в 
            $query="UPDATE `t_warehouse_inventory_in_out` SET `logistic_product`='$logistic'
                                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }        
    }
    //записать статус доставки
  /*  function write_this_deal_delivery_status($con,$order_partner_id,$logistic){
        //получить ключ документа
        $query="SELECT `invoice_key_id` FROM `t_order_product_part` 
                        WHERE `order_partner_id`='$order_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $invoice_key_id=$row[0];

        //по ключу изменить статус доставки в 
        $query="UPDATE `t_warehouse_inventory_in_out` SET `logistic_product`='$logistic'
                    WHERE `invoice_key_id`='$invoice_key_id'";
        mysqli_query($con, $query) or die (mysqli_error($con));

    }*/
    //сделать запись в (t_warehouse_inventory_in_out and t_order_product_part) о том что товар собран
    function write_provider_collect_product_for_sale($con,$warehouse_inventory_id,$collected, $order_partner_id){
        try{
            $query="UPDATE `t_warehouse_inventory_in_out` SET `collected`='$collected'
                                    WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";                                                                
            mysqli_query($con, $query) or die (mysqli_error($con));
        

            $query="UPDATE `t_order_product_part` SET `collected`='$collected'
                                        WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";            
            mysqli_query($con, $query) or die (mysqli_error($con));

            //echo "hello";
        }catch(Exception $ex){
            echo "exception ";
        }
        //проверить все товары в заказе если все собранны то в заказ поставить collect=1;
        $collected = 1;
        $query="SELECT  `collected` FROM `t_order_product_part`
                        WHERE `order_partner_id`='$order_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $my_collected=$row[0];
            if($my_collected == 0){
               $collected = 0;
            }
        }
        //все товары в заказе собранны то
        if($collected == 1){
            $query="UPDATE `t_order_partner` SET `collected`='1'
                           WHERE `order_partner_id`='$order_partner_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }        
    }

    //показать список из warehouse_inventory для сборки товара
    function receive_list_product_for_collect($con,$order_partner_id, $providerWarehouse_id){
        //получить список товаров из warehouse_inventory по номеру заказа
        $query="SELECT `warehouse_inventory_id` FROM `t_order_product_part` 
                        WHERE `order_partner_id`='$order_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $warehouse_inventory_id=$row[0];
            $warehouse_inventory_id_list[] = $warehouse_inventory_id;
            //echo "warehouse_inventory_id 1: $warehouse_inventory_id <br>";
        }
        //убрать дубликаты
        $warehouse_inventory_id_list = array_unique($warehouse_inventory_id_list);

        foreach($warehouse_inventory_id_list as $k => $warehouse_inventory_id){

            //echo "warehouse_inventory_id 2: $warehouse_inventory_id <br>";

            $query="SELECT  `product_inventory_id`, `quantity`, `logistic_product`, `car_id`, `collected`
                            FROM `t_warehouse_inventory_in_out`
                            WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($result);
            $product_inventory_id=$row[0];
            $quantity_to_deal=$row[1];             
            $logistic_product = $row[2]; 
            $car_id = $row[3]; 
            $collected=$row[4];

            //получить остаток товара на складе партнера            
            $provider_stock_quantity=stock_product_to_warehouse($con, $providerWarehouse_id, $product_inventory_id);
                
           
            //получить дату отгрузки товара
            $query="SELECT `get_order_date_millis` FROM `t_order_partner` 
                            WHERE `order_partner_id`='$order_partner_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($result);
            $get_order_date_millis=$row[0];

            //узнать были ли корректоровки колличества в заказе
            $query="SELECT `corrected` FROM `t_order_product_part` WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            while($row = mysqli_fetch_array($result)){
                $corrected=$row[0];
                if($corrected == 1){
                    break;
                }
            }

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
                $description=$product_list['description'];
                $product_name_from_provider=$product_list['product_name_from_provider'];
    
                //показать колличество на складе поставщика за вычетом товара который собран в заказ(на палет)
                //получить колличество собранного товара в заказах
    
                echo $product_id ."&nbsp".$product_inventory_id."&nbsp".$category ."&nbsp".$brand ."&nbsp"
                    .$characteristic."&nbsp".$type_packaging ."&nbsp".$unit_measure ."&nbsp".$weight_volume ."&nbsp"
                    .$quantity_package."&nbsp".$image_url ."&nbsp".$storage_conditions."&nbsp"
                    .$warehouse_inventory_id."&nbsp".$quantity_to_deal."&nbsp".$logistic_product."&nbsp"
                    .$car_id."&nbsp".$provider_stock_quantity."&nbsp".$collected."&nbsp"
                    .$get_order_date_millis."&nbsp".$product_name."&nbsp".$corrected."&nbsp"
                    .$description."&nbsp".$product_name_from_provider."<br>";
        }
    }


     //сделать запись в (t_warehouse_inventory_in_out and t_order_product) о том что товар собран
     function write_collect_product_for_sale($con,$warehouse_id,$order_partner_id,$product_inventory_id,$quantity,
        $transaction_name,$collected,$user_id){
        $query="INSERT INTO `t_warehouse_inventory_in_out`
                ( `transaction_name`, `product_inventory_id`, `quantity`, `out_warehouse_id`, `collected`,`out_active`,`creator_user_id`) 
                VALUES ('$transaction_name','$product_inventory_id','$quantity',  '$warehouse_id'  ,'$collected',     '0'    ,'$user_id')";                                                                
        $result = mysqli_query($con, $query) or die (mysqli_error($con));$result = mysqli_query($con, $query) or die (mysqli_error($con));
        $warehouse_inventory_id = mysqli_insert_id($con);
        
        $query="UPDATE `t_order_product_part` SET `collected`='$collected',`warehouse_inventory_id`='$warehouse_inventory_id'
                        WHERE `order_partner_id`='$order_partner_id' and `product_inventory_id`='$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
    }

        //получить список продуктов для комплектации этому покупателю
        function receive_list_order_product($con,$order_partner_id,$providerWarehouse_id){
            //получить список всех товаров в заказе
           /* $query="SELECT `order_product_id`, `product_inventory_id`, `quantity`,`collected`,`warehouse_inventory_id`
                         FROM `t_order_product` WHERE order_id='$order_id'";*/

            $query="SELECT `order_product_part_id`, `product_inventory_id`, `quantity`,`collected`,`warehouse_inventory_id`
                            FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            while($row = mysqli_fetch_array($result)){
                $order_product_part_id=$row[0];  
                $product_inventory_id=$row[1];
                $quantity_to_order=$row[2]; 
                $collected=$row[3];
                $warehouse_inventory_id=$row[4];
        
                //получить остаток товара на складе партнера            
                $provider_stock_quantity=stock_product_to_warehouse($con, $providerWarehouse_id, $product_inventory_id);
    
                //получить колличество в укомплектованных заказах
                $provider_stock_collect=stock_collect_to_order($con,$providerWarehouse_id, $product_inventory_id);
                
                //вычесть остаток на складе из собранных заказов и показать свободное колличество
                $provider_stock_quantity -= $provider_stock_collect;
                
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
                $quantity_of_colected = 0;
               /* if($warehouse_inventory_id != 0){
                    $query="SELECT `quantity` FROM `t_warehouse_inventory_in_out` 
                                    WHERE `warehouse_inventory_id`='$warehouse_inventory_id' 
                                    and `collected`='1' and `out_active`='0'";
                     $res = mysqli_query($con, $query) or die (mysqli_error($con));
                     $row = mysqli_fetch_array($res);
                     $quantity_of_colected=$row[0];
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
    
                //показать колличество на складе поставщика за вычетом товара который собран в заказ(на палет)
                //получить колличество собранного товара в заказах
    
                echo $product_id ."&nbsp".$product_inventory_id."&nbsp".$category ."&nbsp".$brand ."&nbsp".$characteristic.
                            "&nbsp".$type_packaging ."&nbsp".$unit_measure ."&nbsp".$weight_volume ."&nbsp".$quantity_package .
                            "&nbsp".$image_url ."&nbsp".$order_product_part_id.
                            "&nbsp".$quantity_to_order ."&nbsp".$provider_stock_quantity ."&nbsp".$provider_counterparty_id .
                            "&nbsp".$provider_abbreviation ."&nbsp".$provider_counterparty.
                            "&nbsp".$storage_conditions."&nbsp".$collected."&nbsp".$quantity_of_colected."<br>";

                           // echo "quantity_of_colected:  $quantity_of_colected <br>";
            }
            
    
        }
      //получить список покупателей для выдачи товара
      function receive_list_company_for_shipment($con,$warehouse_id){
        //получить список складов на которые товар готов к отгрузке
        $query="SELECT `logistic_product`, `car_id`, `in_warehouse_id`, `out_active`
                    FROM `t_warehouse_inventory_in_out`
                     WHERE `out_warehouse_id`='$warehouse_id' and `transaction_name`='sale'
                        and `collected` ='1' and `in_active`='0'";
        /*/
$query="SELECT `logistic_product`, `car_id`, `in_warehouse_id`
                    FROM `t_warehouse_inventory_in_out`
                     WHERE `out_warehouse_id`='$warehouse_id' and `transaction_name`='sale'
                        and `collected` ='1' and `out_active`='0'";
        */
         $result=mysqli_query($con, $query) or die (mysqli_error($con));
         //echo "test " . mysqli_num_rows($result) . "<br>";
         $allDeliveriCount = mysqli_num_rows($result);
         if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $logistic_product = $row[0];
                $car_id = $row[1];
                $in_warehouse_id = $row[2];
                $out_active = $row[3];
                
                //собираем массив
                $all_shipment_list[]=array('in_warehouse_id'=>$in_warehouse_id
                                ,'logistic_product'=>$logistic_product, 'car_id'=>$car_id);   
                //собираем массив
                $all_out_active_list[]= $out_active;             

            }

            //$all_shipment_list = array_unique($all_shipment_list, SORT_REGULAR );
            /*foreach($all_shipment_list as $k => $v){
                echo $v['car_id'] . "<br>";
            }*/

            //убираем дубли 

            $all_shipment_list = array_unique($all_shipment_list, SORT_REGULAR );

           /* foreach($all_shipment_list as $k => $v){
                echo $v['car_id'] . "<br>";
            }
            echo "-----------------------<br>";*/

            //добавляем данные компании, склада и авто
            foreach($all_shipment_list as $k => $v){
                $logistic_product = $v['logistic_product'] ;
                $car_id = $v['car_id'] ;
                $in_warehouse_id = $v['in_warehouse_id'] ;

                //получить данные(информацию) о складе и компании id
                $warehouseInfoList = warehouseInfo($con,$in_warehouse_id);            
                $counterparty_id = $warehouseInfoList['counterparty_id'];
                //получить данные(информацию) о компании
                $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
                $companyInfoString = $companyInfoList['companyInfoString'];

                //получить данные(информацию) о складе и компании id
                $warehouseInfoList = warehouseInfo($con,$in_warehouse_id);             
                $warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

                //получаем данные авто в строке
                $car_info_string = receiveCarInfoShort($con, $car_id);

                //выясняем все ли товары выданы
                $out_active=1;
                foreach($all_out_active_list as $k => $v){
                    if($v == 0)$out_active = 0;
                    break;
                }

                $shipment_list[]=array('counterparty_id'=>$counterparty_id,'companyInfoString'=>$companyInfoString
                            ,'in_warehouse_id'=>$in_warehouse_id,'warehouseInfoString'=>$warehouseInfoString
                            ,'logistic_product'=>$logistic_product, 'car_id'=>$car_id, 'car_info_string'=>$car_info_string
                            , 'out_active'=>$out_active); 
                
                //echo $v['car_id'] . "<br>";
            }

          /*  echo "-----------------------<br>";
            foreach($shipment_list as $k => $v){
                echo $v['counterparty_id'] . "<br>";
            }*/

            // далее сортируем
            //$shipment_list = sortArray( $shipment_list, 'counterparty_id' );
            $shipment_list = sortArray( $shipment_list, array( 'counterparty_id', 'logistic_product' ) );

            foreach($shipment_list as $k => $v){
                echo $v['counterparty_id'] ."&nbsp". $v['companyInfoString'] ."&nbsp". $v['in_warehouse_id']."&nbsp". $v['warehouseInfoString'] ."&nbsp"
                                . $v['logistic_product'] ."&nbsp". $v['car_id'] . "&nbsp". $v['car_info_string'] ."&nbsp". $v['out_active'] . "<br>";
            }

            
            //передаем 
         }


    }    
  
       //получить список покупателей для сборки товара
       function receive_list_company_for_collect($con,$warehouse_id){
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
         
         $query="SELECT orp.order_partner_id,
                        orp.order_active,  
                        orp.get_order_date_millis,
                        orp.collected,
                        c.abbreviation,
                        c.counterparty,
                        c.taxpayer_id_number                        
                        FROM t_order_partner orp                         
                            JOIN t_counterparty c ON c.counterparty_id = orp.in_counterparty_id
                        WHERE orp.out_warehouse_id='$warehouse_id' AND orp.executed='0'";
         
         $result = mysqli_query($con, $query) or die (mysqli_error($con));
         if(mysqli_num_rows($result) > 0){
           // echo "id: ";
            while($row = mysqli_fetch_array($result)){
                $order_partner_id=$row[0];  
                $order_active=$row[1];
                $get_order_date_millis=$row[2];
                $collected  = $row[3];
                $abbreviation = $row[4];  
                $counterparty = $row[5];
                $taxpayer_id_number = $row[6];  
               

                echo $order_partner_id. "&nbsp".$abbreviation. "&nbsp".$counterparty."&nbsp".$taxpayer_id_number.
                            "&nbsp".$order_active."&nbsp".$collected."&nbsp".$get_order_date_millis."<br>";
            }
         }else{
            echo "messege" . "&nbsp". $GLOBALS['this_warehouse_is_not_order'];
        }
    }
    //получить список складов поставщика
    function receive_list_provider_warehouse($con,$counterparty_id){
        $query="SELECT wi.warehouse_info_id,
                    wi.city,
                    wi.street,
                    wi.house,
                    wi.building,
                    w.warehouse_id
                FROM   t_warehouse_info wi
                    JOIN t_warehous w ON  w.warehouse_info_id=wi.warehouse_info_id AND w.warehouse_type = 'provider'                                                                 
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

    //получить список доступных к созданию документов
   /* function get_invoice_list_for_create($con,$warehouseInventory_id){
        //получить склады от кого / кому и транспорт
        $query="SELECT  `car_id`, `out_warehouse_id`, `in_warehouse_id` FROM `t_warehouse_inventory_in_out`
                     WHERE `warehouse_inventory_id`='$warehouseInventory_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $car_id=$row[0];
        $out_warehouse_id=$row[1];
        $in_warehouse_id=$row[2];

        //наити ключ для создания документов
        $query="SELECT `invoice_key_id` FROM `t_invoice_key` 
                        WHERE `closed`='0' and `out_warehouse_id`='$out_warehouse_id' 
                            and `in_warehouse_id`='$in_warehouse_id' and `car_id`='$car_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_array($result);
            $invoice_key_id=$row[0];
            //проверить есть warehouse_inventory_id с этим ключем
            $query="SELECT `warehouse_inventory_id` FROM `t_warehouse_inventory_in_out` 
                            WHERE  `invoice_key_id`='$invoice_key_id'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            if(mysqli_num_rows($result) > 0){
                //показать список документов которые можно создать
                echo "товарная накладная" . "<br>";
                echo "транспортная накладная" . "<br>";                

            }else{
                echo "messege"."&nbsp".$GLOBALS['there_are_no_selected_products'];
            }           
        }else{
            echo "messege"."&nbsp".$GLOBALS['there_are_no_selected_products'];
        }

    }*/
     //получить мои товары на складах моих партнеров
     function receive_my_product_int_partner_warehouses($con,$counterparty_id){
        //получить мой список товаров 
        $query="SELECT `product_inventory_id`
                    FROM `t_product_inventory` WHERE `counterparty_id`='$counterparty_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $product_inventory_id=$row[0];

                $product_inventory_id_list []=$product_inventory_id;
            }
            /*foreach($product_inventory_id_list as $key => $product_inventory_id){
                echo "id: ".$product_inventory_id ."<br>";
            }*/
            //echo "--------------------------<br>";
        }else{
            return;
        }
        //получить мои склады
        $query="SELECT warehouse_id
                     FROM t_warehouse_info win 
                        JOIN t_warehous w ON w.warehouse_info_id = win.warehouse_info_id and w.active='1'
                     WHERE win.counterparty_id='$counterparty_id' and win.active='1'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0];

                $my_warehouse_id_list []=$warehouse_id;
            }
        }else{
            return;
        }
        /*foreach($my_warehouse_id_list as $key => $my_warehouse_id){
            
            //echo "my_warehouse_id: ".$my_warehouse_id ."<br>";
        }*/
        //проверить  поступления на складах партнеров
        foreach($product_inventory_id_list as $key => $product_inventory_id){
            $query="SELECT `in_warehouse_id` FROM `t_warehouse_inventory_in_out` 
                            WHERE `product_inventory_id`='$product_inventory_id' and `in_warehouse_id`  IS NOT NULL";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0];

                $all_warehouse_id_list []=$warehouse_id;
            }
        }
        $all_warehouse_id_list = array_unique($all_warehouse_id_list);
        /*foreach($all_warehouse_id_list as $key => $warehouse_id){
            
           // echo "warehouse_id: ".$warehouse_id ."<br>";
        }*/
        //echo "-------------------<br>";
        //оставить в списке только склады партнеров
        foreach($my_warehouse_id_list as $key => $my_warehouse_id){
            $index = array_search($my_warehouse_id, $all_warehouse_id_list);
            if($index !== FALSE){
                unset($all_warehouse_id_list[$index]);
            }           
           // echo "warehouse_id: ".$my_warehouse_id ."<br>";
        }
        $partner_warehouse_id_list = $all_warehouse_id_list;
        //проверить положительные остатки мойх товаров на складах партнера
        foreach($partner_warehouse_id_list as $key => $warehouse_id){
            foreach($product_inventory_id_list as $key => $product_inventory_id){
                //получить остаток товара на складе
                $partner_stock_quantity = stock_product_to_warehouse($con, $warehouse_id, $product_inventory_id);
                if($partner_stock_quantity > 0){
                    $full_product_inventory_id_list[] = ['product_inventory_id' => $product_inventory_id, 'warehouse_id'=>$warehouse_id,
                                                            'partner_stock_quantity' => $partner_stock_quantity];
                }
            }            
        }
        foreach($full_product_inventory_id_list as $key => $v){
            //echo $v['product_inventory_id'] . " war: ".$v['warehouse_id']. " stock: " . $v['partner_stock_quantity']."<br>";

            $product_inventory_id = $v['product_inventory_id'];
            $warehouse_id = $v['warehouse_id'];
            $partner_stock_quantity = $v['partner_stock_quantity'];
            //получить данные(информацию) о складе 
            $warehouseInfoList = warehouseInfo($con,$warehouse_id);

            $warehouse_info_id = $warehouseInfoList['warehouse_info_id'];
            $city = $warehouseInfoList['city'];
            $street = $warehouseInfoList['street'];
            $house = $warehouseInfoList['house'];
            $building = $warehouseInfoList['building'];

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

            echo $product_id."&nbsp".$product_inventory_id."&nbsp".$category."&nbsp".$brand."&nbsp".$characteristic."&nbsp".
                $type_packaging."&nbsp".$unit_measure."&nbsp".$weight_volume."&nbsp".$quantity_package."&nbsp".
                $image_url.   "&nbsp" . 
                $warehouse_info_id."&nbsp".$warehouse_id."&nbsp".$city."&nbsp".
                $street."&nbsp".$house."&nbsp".$building."&nbsp".
                $partner_stock_quantity."&nbsp".$product_name."<br>";
        }                
        /*
                            (product_id,
                            product_inventory_id, category, brand, characteristic, type_packaging, unit_measure,
                            weight_volume, total_quantity, price, quantity_package, image_url, description,
                            total_sale_quantity, free_balance);
        */
    }
    //найти мои товары на складах моих партнеров
    function search_my_product_int_partner_warehouses($con,$counterparty_id){
        //получить мой список товаров 
        $query="SELECT `product_inventory_id`
                    FROM `t_product_inventory` WHERE `counterparty_id`='$counterparty_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $product_inventory_id=$row[0];

                $product_inventory_id_list []=$product_inventory_id;
            }
           /*foreach($product_inventory_id_list as $key => $product_inventory_id){
                echo "id: ".$product_inventory_id ."<br>";
            }*/
        }else{
            return;
        }
        //получить мои склады
        $query="SELECT warehouse_id
                     FROM t_warehouse_info win 
                        JOIN t_warehous w ON w.warehouse_info_id = win.warehouse_info_id and w.active='1'
                     WHERE win.counterparty_id='$counterparty_id' and win.active='1'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0];

                $my_warehouse_id_list []=$warehouse_id;
            }
        }else{
            return;
        }
        /*foreach($my_warehouse_id_list as $key => $my_warehouse_id){
            
            //echo "my_warehouse_id: ".$my_warehouse_id ."<br>";
        }*/
        //проверить  поступления на складах партнеров
        foreach($product_inventory_id_list as $key => $product_inventory_id){
            $query="SELECT `in_warehouse_id` FROM `t_warehouse_inventory_in_out` 
                            WHERE `product_inventory_id`='$product_inventory_id' and `in_warehouse_id`  IS NOT NULL";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0];

                $all_warehouse_id_list []=$warehouse_id;
            }
        }
        $all_warehouse_id_list = array_unique($all_warehouse_id_list);
        /*foreach($all_warehouse_id_list as $key => $warehouse_id){
            
           // echo "warehouse_id 1: ".$warehouse_id ."<br>";
        }*/
        //echo "-------------------<br>";
        //оставить в списке только склады партнеров
        foreach($my_warehouse_id_list as $key => $my_warehouse_id){
            $index = array_search($my_warehouse_id, $all_warehouse_id_list);
            if($index !== FALSE){
                unset($all_warehouse_id_list[$index]);
            }           
           // echo "warehouse_id 2: ".$my_warehouse_id ."<br>";
        }
        $partner_warehouse_id_list = $all_warehouse_id_list;
        //проверить положительные остатки мойх товаров на складах партнера
        foreach($partner_warehouse_id_list as $key => $warehouse_id){
            foreach($product_inventory_id_list as $key => $product_inventory_id){
                //получить остаток товара на складе
                $partner_stock_quantity = stock_product_to_warehouse($con, $warehouse_id, $product_inventory_id);
                if($partner_stock_quantity > 0){
                    echo "RESULT_OK" . "<br>";
                    break 2;
                }
                //echo "id: ".$product_inventory_id ." partner_stock_quantity = $partner_stock_quantity <br>";
            }
            
            //echo "warehouse_id 3: ".$warehouse_id ."<br>";
        }
        if($partner_stock_quantity == 0){
            echo "RESULT_FALSE";
        }

        
    }
    //получить колличество позиций товара в заказе
    function receive_quantity_position_to_order($con,$order_id){
        $count = 0;
        $query="SELECT `order_product_id` FROM `t_order_product` WHERE `order_id`='$order_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        $count = mysqli_num_rows($result);
        
        echo $count;
    }

    //получить список складов хранения своего товара
    function receive_my_warehouse($con,$counterparty_id){
        $query="SELECT   wi.warehouse_info_id,
                         w.warehouse_id,                        
                        w.warehouse_type
                    FROM t_warehouse_info wi
                        JOIN t_warehous w ON (w.warehouse_info_id = wi.warehouse_info_id AND w.active= '1' 
                                                AND w.warehouse_type = 'storage') 
                                          OR (w.warehouse_info_id = wi.warehouse_info_id AND w.active= '1' 
                                                AND w.warehouse_type = 'provider')
                    WHERE wi.counterparty_id = '$counterparty_id' AND wi.active = '1'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_info_id=$row[0]; 
                $warehouse_id=$row[1];
                $warehouse_type=$row[2];
               

                echo  $warehouse_info_id."&nbsp".$warehouse_id."&nbsp".$warehouse_type . "<br>";
            }
        }else echo "messege" . $GLOBALS['data_to_warehouse_is_not'];
    }
    //загружаем имя картинки в t_image_moderation (таблицу)
    // меняем картинку в t_product_inventory
    function write_image_name_to_table($con,$product_inventory_id,$imageName,$user_id){
        $date = date("Y-m-d H:i:s");
        $image_url = $imageName . '.jpg' ;

        $query="SELECT `image_id`  FROM `t_image` WHERE `image_url`='$image_url'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($res) > 0){
            $row=mysqli_fetch_array($res);
            $image_id=$row[0];
            //echo "row = $image_id url = $image_url";
        }else{
            return;
        }        

        $query="INSERT INTO `t_image_moderation`
                        (`image_moderation_url`, `product_inventory_id`, `user_id`) 
                VALUES (     '$image_url'      ,'$product_inventory_id','$user_id')";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));       
       

        $query="UPDATE `t_product_inventory` 
                    SET `image_id`='$image_id',`up_user_id`='$user_id',`updated_at`='$date' 
                    WHERE `product_inventory_id`='$product_inventory_id'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
    }
    //добавить собранный товар в warehouse_inventory_in_out и в t_logistic_product
    function update_in_active_to_table($con,$warehouseInventory_id,$in_active,$logistic_product,$give_out,$user_id){
        $date = date("Y-m-d H:i:s");
        $query="UPDATE `t_warehouse_inventory_in_out` 
                    SET `in_active`='$in_active',`in_user_id`='$user_id',`in_updated_at`='$date'
                     WHERE `warehouse_inventory_id`='$warehouseInventory_id'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));

        if($logistic_product == 1 && $give_out == 0){
            $query="UPDATE `t_logistic_product` SET `give_out`='1'
                            WHERE `warehouse_inventory_id`='$warehouseInventory_id'";
            $res = mysqli_query($con, $query) or die (mysqli_error($con));
        }     
        
        //проверить заказ в котором этот товар, выполненн(передан паротеру)
            //плучить номер заказа этого товара
        $query="SELECT  `order_partner_id` FROM `t_order_product_part` WHERE `warehouse_inventory_id`='$warehouseInventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($result);
            $order_partner_id=$row[0]; 
        //плучить все товары(warehouse_inventory_id) в этом заказе
        $query="SELECT `warehouse_inventory_id` FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            //проверить все ли товары из заказа получены партнером
            $product_received_flag = true;
            while($row = mysqli_fetch_array($result)){
                $warehouse_inventory_id = $row[0];
                $query="SELECT  `in_active` FROM `t_warehouse_inventory_in_out` WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($res);
                $in_active=$row[0];
                if($in_active == 0){
                    $product_received_flag = false;
                    break;
                }
            }
        }
        //если все товары из заказа получены то сделать заказ выполненным
        if($product_received_flag){
            $query="UPDATE `t_order_partner` SET `executed`='1' WHERE `order_partner_id`='$order_partner_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }

    }
    //добавить собранный товар в warehouse_inventory_in_out и в t_logistic_product
    /*function update_in_active_to_table($con,$warehouseInventory_id,$in_active,$logistic_product,$give_out,$user_id){
        $date = date("Y-m-d H:i:s");
        $query="UPDATE `t_warehouse_inventory_in_out` 
                    SET `in_active`='$in_active',`in_user_id`='$user_id',`in_updated_at`='$date'
                     WHERE `warehouse_inventory_id`='$warehouseInventory_id'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));

        if($logistic_product == 1 && $give_out == 0){
            $query="UPDATE `t_logistic_product` SET `give_out`='1'
                            WHERE `warehouse_inventory_id`='$warehouseInventory_id'";
            $res = mysqli_query($con, $query) or die (mysqli_error($con));
        }        
    }*/
   //добавить ключ(документы) и установить или отменить галочку (передан товар) у поставщика
   function chenge_checked_provider($con,$warehouseInventory_id,$checked){
       if($checked == 1){
       
        //получить данные поставщика и покупателя
            $query="SELECT  `out_warehouse_id`, `in_warehouse_id`,`car_id`  FROM `t_warehouse_inventory_in_out`
                        WHERE `warehouse_inventory_id`='$warehouseInventory_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($result);
                $out_warehouse_id=$row[0]; 
                $in_warehouse_id=$row[1]; 
                $car_id=$row[2];
        
                //echo "test 1 <br>";
            //получить или создать ключ документа
            $query="SELECT `invoice_key_id` FROM `t_invoice_key` 
                        WHERE `out_warehouse_id`='$out_warehouse_id' 
                        and  `in_warehouse_id`='$in_warehouse_id' and `car_id`='$car_id' and `closed`='0'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            //echo "test 2 <br>";
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_array($result)){
                    $invoice_key_id=$row[0]; 
                }
            }else{
                $query="SELECT win.counterparty_id                    
                            FROM t_warehous w
                                JOIN t_warehouse_info win ON win.warehouse_info_id = w.warehouse_info_id
                            WHERE w.warehouse_id ='$out_warehouse_id'";
                $result = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($result);
                $out_counterparty_id = $row[0]; 

                $query="SELECT win.counterparty_id                    
                            FROM t_warehous w
                                JOIN t_warehouse_info win ON win.warehouse_info_id = w.warehouse_info_id
                            WHERE w.warehouse_id ='$in_warehouse_id'";
                $result = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($result);
                $in_counterparty_id = $row[0];

                $query="INSERT INTO `t_invoice_key`
                            (`out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`,`car_id`) 
                    VALUES ('$out_counterparty_id','$out_warehouse_id','$in_counterparty_id','$in_warehouse_id','$car_id')";
                $result = mysqli_query($con, $query) or die (mysqli_error($con));
                $invoice_key_id = mysqli_insert_id($con);
            }
            //добавить товар в  t_invoice_info для использования в документах
            //вычислить среднюю цену товара (собрать и разделить сумму на колличество товара)
           // $query="";

        }else{
            $invoice_key_id = '0';
        }


       $query="UPDATE `t_warehouse_inventory_in_out` SET `out_active`='$checked', `invoice_key_id`='$invoice_key_id'
                      WHERE `warehouse_inventory_id`='$warehouseInventory_id'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
   }
    //получить данные на товар
    function get_product_info($con,$product_inventory_id){     
        //получить данные(информацию) по товару
        $product_list = receive_product_info($con, $product_inventory_id);
    
        $category=$product_list['category'];
        $product_name=$product_list['product_name'];
        $brand=$product_list['brand']; 
        $characteristic=$product_list['characteristic'];
        $type_packaging=$product_list['type_packaging']; 
        $unit_measure=$product_list['unit_measure'];
        $weight_volume=$product_list['weight_volume']; 
        $quantity_package=$product_list['quantity_package'];
        $image_url=$product_list['image_url'];

                echo $category."&nbsp".$brand."&nbsp".$characteristic ."&nbsp".$type_packaging.
                    "&nbsp".$unit_measure."&nbsp".$weight_volume."&nbsp".$quantity_package.
                    "&nbsp".$product_name."&nbsp".$image_url."<br>";
    }
 
    //получить данные авто
    function receive_car_info($con,$car_id){
        $query="SELECT `car_brand`, `car_model`, `registration_num`
                    FROM `t_car` WHERE `car_id`='$car_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
            $car_brand=$row[0];
            $car_model=$row[1];
            $registration_num=$row[2];

            echo $car_brand ."&nbsp".$car_model ."&nbsp".$registration_num."<br>";
     
    }
    //получить данные склада
    function receive_out_warehouse_info($con,$warehouse_id){

        //получить данные(информацию) о складе и компании id
        $warehouseInfoList = warehouseInfo($con,$warehouse_id);       
        $warehouseInfoString = $warehouseInfoList['warehouseInfoString'];


        /*$warehouse_address_list = receiveWarehouseAddress($con, $warehouse_id);
            $outCity=$warehouse_address_list['city'];
            $outStreet=$warehouse_address_list['street'];
            $outHouse=$warehouse_address_list['house'];
            $outBuilding=$warehouse_address_list['building'];*/

            //echo $warehouse_id."&nbsp".$outCity."&nbsp".$outStreet."&nbsp".$outHouse."&nbsp".$outBuilding."<br>";
            echo $warehouseInfoString;
    }

    //получить список складов контрагента
    function receive_list_warehouse_001($con,$counterparty_id){
        $query="SELECT wi.warehouse_info_id,
                    wi.city,
                    wi.street,
                    wi.house,
                    wi.building,
                    w.warehouse_id,
                    w.warehouse_type
                FROM   t_warehouse_info wi
                    JOIN t_warehous w ON  w.warehouse_info_id=wi.warehouse_info_id                                                                  
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
                $warehouse_type=$row[6];

                echo $warehouse_id ."&nbsp".$city ."&nbsp".$street ."&nbsp".$house ."&nbsp".
                        $building ."&nbsp".$warehouse_info_id."&nbsp".$warehouse_type."<br>";
            }
        }else{
            echo "messege" . "&nbsp". $GLOBALS['data_to_warehouse_is_not'];
        }
               

       /* $query="SELECT `warehouse_id`, `city`, `street`, `house`, `building`
                    FROM `t_warehouse` WHERE `counterparty_id`='$counterparty_id'";*/
       /* $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $warehouse_id=$row[0];
                $city=$row[1];
                $street=$row[2];
                $house=$row[3];
                $building=$row[4];*/

             /*   echo $warehouse_id ."&nbsp".$city ."&nbsp".$street ."&nbsp".$house ."&nbsp".$building ."<br>";
            }
        }else{
            echo "messege" . "&nbsp". $GLOBALS['data_to_warehouse_is_not'];
        }*/
    }
    /*
      //получить список складов контрагента
    function receive_list_warehouse($con,$counterparty_id){
        $query="SELECT `warehouse_id`, `city`, `street`, `house`, `building`
                    FROM `t_warehouse` WHERE `counterparty_id`='$counterparty_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $warehouse_id=$row[0];
                $city=$row[1];
                $street=$row[2];
                $house=$row[3];
                $building=$row[4];

                echo $warehouse_id ."&nbsp".$city ."&nbsp".$street ."&nbsp".$house ."&nbsp".$building ."<br>";
            }
        }else{
            echo "messege" . "&nbsp". $GLOBALS['data_to_warehouse_is_not'];
        }
    }
    */
    //получить все доставленные warhouse_inventory_id но не принятые и показать их
    function receive_list_delivery_to_accept_001($con,$warehouse_id){
        //ищем  данные на какой склад компании есть доставка
        $query = "SELECT `warehouse_inventory_id`, `product_inventory_id`, `quantity`, 
                                        `logistic_product`,`car_for_logistic`, `out_warehouse_id`, 
                                        `out_active`, `in_active`, `invoice_key_id`
                    FROM `t_warehouse_inventory_in_out` 
                    WHERE `in_warehouse_id`='$warehouse_id' AND `out_active`='1' AND `in_active` ='0'
                            AND `transaction_name`='sale'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $warehouse_inventory_id=$row[0];
                $product_inventory_id=$row[1];
                $quantity=$row[2];
                $logistic_product=$row[3];
                $car_for_logistic = $row[4];
                $out_warehouse_id = $row[5];
                $out_active=$row[6];
                $in_active=$row[7];
                $invoice_key_id = $row[8];

                $colorDelivery=$out_active;
                $car_id=0;
                $take_in=0;

                //товар надо доставить на склад но авто не найден
                if($logistic_product == 1 and $car_for_logistic == 0){

                }
                //получить данные товар передан(поставщиком) для подсвечивания товара
                //получить данные авто
                else if($logistic_product == 1 && $car_for_logistic == 1){
                    $query="SELECT `car_id`, `take_in`, `give_out` FROM `t_logistic_product` 
                            WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
                    $res = mysqli_query($con, $query) or die (mysqli_error($con));
                    $row = mysqli_fetch_array($res);
                    $car_id = $row[0];
                    $take_in = $row[1];
                    $colorDelivery = $row[2];
                }
                else{                                                  
                    
                }
                //принять товар прямо со склада без доставки
                $accept_from_warehouse = false;
                if($logistic_product == 0 && $out_active == 1){
                    $accept_from_warehouse = true;
                }
                //принять товар с авто
                $accept_with_car = false;
                if($logistic_product == 1 && $car_for_logistic == 1 && $take_in == 1){
                    $accept_with_car = true;
                }
                
                //если получить без доставки, или есть доставка и авто найден то показать //если товар еще не принят авто то не показывать                
                if($accept_from_warehouse || $accept_with_car){
                    //получить номер документа по ключу 
                    $document_name = "товарная накладная";                
                    $document_num = get_document_num_for_invoice_key($con, $invoice_key_id, $document_name);
                  
                    //получить данные(информацию) по товару
                    $product_list = receive_product_info($con, $product_inventory_id);                    
                    //$product_id=$product_list['product_id'];
                    $category=$product_list['category'];
                    $product_name=$product_list['product_name'];
                    $brand=$product_list['brand']; 
                    $characteristic=$product_list['characteristic'];
                    $type_packaging=$product_list['type_packaging']; 
                    $unit_measure=$product_list['unit_measure'];
                    $weight_volume=$product_list['weight_volume']; 
                    $quantity_package=$product_list['quantity_package'];
                    $image_url=$product_list['image_url'];
                    //$storage_conditions=$product_list['storage_conditions'];
                    //$price=$product_list['price'];
                                        
                    echo  $warehouse_inventory_id."&nbsp".$product_inventory_id."&nbsp"
                        . $logistic_product."&nbsp". $in_active."&nbsp".$category ."&nbsp"
                        . $brand ."&nbsp". $characteristic ."&nbsp".$unit_measure ."&nbsp"
                        . $weight_volume ."&nbsp".$image_url."&nbsp".$quantity."&nbsp"
                        .$type_packaging."&nbsp".$quantity_package."&nbsp".$car_id."&nbsp"
                        .$out_warehouse_id."&nbsp".$colorDelivery."&nbsp".$document_num."&nbsp"
                        .$product_name."<br>"; 
                }
                
            }
        }else{
            echo "messege" . "&nbsp". $GLOBALS['product_is_not_to_accept_this_warehouse'];
        }       

    }
    /*
 //получить все доставленные warhouse_inventory_id но не принятые и показать их
    function receive_list_delivery_to_accept($con,$warehouse_id){
        //ищем  данные на какой склад компании есть доставка
        $query = "SELECT `warehouse_inventory_id`, `product_inventory_id`, `quantity`, 
                                        `logistic_product`,`car_for_logistic`, `out_warehouse_id`, 
                                        `out_active`, `in_active`
                    FROM `t_warehouse_inventory_in_out` 
                    WHERE `in_warehouse_id`='$warehouse_id' AND `out_active`='1' AND `in_active` ='0'
                            AND `transaction_name`='sale'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $warehouse_inventory_id=$row[0];
                $product_inventory_id=$row[1];
                $quantity=$row[2];
                $logistic_product=$row[3];
                $car_for_logistic = $row[4];
                $out_warehouse_id = $row[5];
                $out_active=$row[6];
                $in_active=$row[7];

                $colorDelivery=$out_active;
                $car_id=0;

                if($logistic_product == 1 and $car_for_logistic == 0){

                }else{                                                  
                    //получить данные товар передан(поставщиком) для подсвечивания товара
                    //получить данные авто
                    if($logistic_product == 1 && $car_for_logistic == 1){
                        $query="SELECT `car_id`,`give_out` FROM `t_logistic_product` 
                                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
                        $res = mysqli_query($con, $query) or die (mysqli_error($con));
                        $row = mysqli_fetch_array($res);
                        $car_id = $row[0];
                        $colorDelivery = $row[1];
                    }
                }
                    //получить данные товара
                    $product_info_list=receiveProductInfo($con,$product_inventory_id);
                    $category=$product_info_list['category'];
                    $brand=$product_info_list['brand'];
                    $characteristic=$product_info_list['characteristic'];
                    $type_packaging=$product_info_list['type_packaging'];
                    $unit_measure=$product_info_list['unit_measure'];
                    $weight_volume=$product_info_list['weight_volume'];
                    $quantity_package=$product_info_list['quantity_package'];
                    $image_url=$product_info_list['image_url'];
                                        
                    echo  $warehouse_inventory_id."&nbsp".
                        $product_inventory_id."&nbsp". $logistic_product."&nbsp". $in_active."&nbsp".
                        $category ."&nbsp". $brand ."&nbsp". $characteristic ."&nbsp".
                        $unit_measure ."&nbsp". $weight_volume ."&nbsp".$image_url."&nbsp".
                        $quantity."&nbsp".$type_packaging."&nbsp".
                        $quantity_package."&nbsp".$car_id."&nbsp".$out_warehouse_id."&nbsp".$colorDelivery."<br>"; 
                
            }
        }else{
            echo "messege" . "&nbsp". $GLOBALS['product_is_not_to_accept_this_warehouse'];
        }       

    }
    */
    //получить информацию по (склад назначения)
    function receive_warehouse_info($con,$in_warehouse_id){
        $query="SELECT wi.warehouse_info_id,
                    wi.city,
                    wi.street,
                    wi.house,
                    wi.building,
                    w.warehouse_id
                FROM  t_warehous w
                    JOIN t_warehouse_info wi ON  wi.warehouse_info_id=w.warehouse_info_id                                                                  
                WHERE w.warehouse_id ='$in_warehouse_id'"; 
         $result=mysqli_query($con,$query)or die (mysqli_error($con));
             while($row=mysqli_fetch_array($result)){
                $in_warehouse_info_id=$row[0]; 
                $inCity=$row[1];
                $inStreet=$row[2];
                $inHouse=$row[3];
                $inBuilding=$row[4];

                echo $in_warehouse_id ."&nbsp".$inCity ."&nbsp".$inStreet ."&nbsp".$inHouse ."&nbsp".
                        $inBuilding . "&nbsp" .$in_warehouse_info_id."<br>";                 
             }


      /*  $query="SELECT `city`, `street`, `house`, `building`FROM `t_warehouse` 
        WHERE `warehouse_id`='$in_warehouse_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($result);

            $inCity=$row[0];
            $inStreet=$row[1];
            $inHouse=$row[2];
            $inBuilding=$row[3];
            echo $in_warehouse_id ."&nbsp".$inCity ."&nbsp".$inStreet ."&nbsp".$inHouse ."&nbsp".$inBuilding ."<br>";*/
    }
    //получить список получателей(перевозчиков) и все собранные товары для выдачи получателям
   /* function receive_list_recipient_and_product_for_them_001($con,$warehouse_id){   
        $delivery_info_list = [];
        $count = 0;     
        $query="SELECT `warehouse_inventory_id`, `product_inventory_id`, `quantity`, `logistic_product`, 
                        `car_for_logistic`, `in_warehouse_id`, `out_active`
                    FROM `t_warehouse_inventory_in_out`  WHERE `out_warehouse_id`='$warehouse_id' 
                    and `transaction_name`='moving' and `collected` ='1' and `in_active`='0'";
                                                    //and (`out_active` IN ('0') OR `out_active` IS NULL)";
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        //echo "test " . mysqli_num_rows($result) . "<br>";
        $allDeliveriCount = mysqli_num_rows($result);
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_inventory_id = $row[0];
                $product_inventory_id = $row[1];
                $quantity = $row[2];
                $logistic_product = $row[3];
                $car_for_logistic = $row[4];
                $in_warehouse_id = $row[5];
                $out_active = $row[6];

               
                $car_id=0;
                if($car_for_logistic != 0){
                    $car_id = receiveCarId($con, $warehouse_inventory_id);
                }
                //если есть доставка то проверить принят товар в авто                 
                if($logistic_product == 1 && $car_for_logistic == 1){
                    $query="SELECT  `take_in` FROM `t_logistic_product` 
                            WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
                    $res=mysqli_query($con, $query) or die (mysqli_error($con));
                    $row=mysqli_fetch_array($res);
                    $take_in = $row[0];
                }

                //если нужна доставка а авто не указан то не передaвать данные//&& $take_in == 0
                //если нужна доставка а авто указан а товар в авто принят то не передaвать данные
                if(($logistic_product == 1 && $car_for_logistic == 1 && $take_in == 0) || ($logistic_product == 0)){
                    echo  $warehouse_inventory_id."&nbsp".$product_inventory_id."&nbsp".$quantity."&nbsp".
                         $logistic_product."&nbsp".$car_for_logistic."&nbsp".$out_active."&nbsp".
                         $in_warehouse_id. "&nbsp".$car_id."<br>";
                    $count++;

                   
                    $delivery_info_list[]=array('warehouse_inventory_id' => $warehouse_inventory_id,
                        'product_inventory_id'=>$product_inventory_id, 'quantity'=>$quantity,
                        'logistic_product'=>$logistic_product, 'car_for_logistic'=>$car_for_logistic,
                        'out_active'=>$out_active, 'in_warehouse_id'=>$in_warehouse_id, 'car_id'=>$car_id);

                    $product_inventory_id_list[] = $product_inventory_id;
                }
               
            }
            if($count < $allDeliveriCount){
                //$allDeliveriCount - $count;
                echo "messege" . "&nbsp". ($allDeliveriCount - $count)." - ".$GLOBALS['products_waiting_distribution'];
            }
        }else{
            echo "messege" . "&nbsp" . $GLOBALS['data_is_not'];
            return;
        }
        if($delivery_info_list){
            $product_inventory_id_list = array_unique($product_inventory_id_list);
            $temp = $delivery_info_list;
            $quantity = 0;
            foreach($product_inventory_id_list as $key => $product_inventory_id){
                echo $product_inventory_id . "<br>";
                foreach($temp as $key => $v){
                    if($v['product_inventory_id'] == $product_inventory_id){
                        $quantity += $v['quantity'];
                    }
                    echo "temp". $v['warehouse_inventory_id'] ."<br>";
                }
                $tempo_two[]=
            }
            foreach($delivery_info_list as $key => $delivery_info){
                echo $delivery_info['warehouse_inventory_id'] ."<br>";
            }
            
        }
        
      
    }*/
  //получить список получателей(перевозчиков) и все собранные товары для выдачи получателям
  function receive_list_recipient_and_product_for_them($con,$warehouse_id){   
    $count = 0;     
    $query="SELECT `warehouse_inventory_id`, `product_inventory_id`, `quantity`, `logistic_product`, 
                    `car_for_logistic`, `in_warehouse_id`, `out_active`, `invoice_key_id`
                FROM `t_warehouse_inventory_in_out`  WHERE `out_warehouse_id`='$warehouse_id' 
                and `transaction_name`='sale' and `collected` ='1' and `in_active`='0'";//and `transaction_name`='moving' and `collected` ='1' and `in_active`='0'";
                                                //and (`out_active` IN ('0') OR `out_active` IS NULL)";
    $result=mysqli_query($con, $query) or die (mysqli_error($con));
    //echo "test " . mysqli_num_rows($result) . "<br>";
    $allDeliveriCount = mysqli_num_rows($result);
    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_array($result)){
            $warehouse_inventory_id = $row[0];
            $product_inventory_id = $row[1];
            $quantity = $row[2];
            $logistic_product = $row[3];
            $car_for_logistic = $row[4];
            $in_warehouse_id = $row[5];
            $out_active = $row[6];
            $invoice_key_id = $row[7];

           
            $car_id=0;
            if($car_for_logistic != 0){
                $car_id = receiveCarId($con, $warehouse_inventory_id);
            }
            //если есть доставка то проверить принят товар в авто                 
            if($logistic_product == 1 && $car_for_logistic == 1){
                $query="SELECT  `take_in` FROM `t_logistic_product` 
                        WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
                $res=mysqli_query($con, $query) or die (mysqli_error($con));
                $row=mysqli_fetch_array($res);
                $take_in = $row[0];
            }

            //если нужна доставка а авто не указан то не передaвать данные//&& $take_in == 0
            //если нужна доставка а авто указан а товар в авто принят то не передaвать данные
            if(($logistic_product == 1 && $car_for_logistic == 1 && $take_in == 0) || ($logistic_product == 0)){
                
                //получаем описание товара  в строке
               
                $description_docs_price=receiveProductInfoShort($con, $product_inventory_id, $invoice_key_id);
                $description_docs  = $description_docs_price['description_docs'];
                $price = $description_docs_price['price'];
                //получаем данные авто в строке
                $car_info = receiveCarInfoShort($con, $car_id); 

                echo  $warehouse_inventory_id."&nbsp".$product_inventory_id."&nbsp".$quantity."&nbsp"
                    .$logistic_product."&nbsp".$car_for_logistic."&nbsp".$out_active."&nbsp"
                    .$in_warehouse_id. "&nbsp".$car_id."&nbsp".$invoice_key_id."&nbsp"
                    .$description_docs."&nbsp".$car_info."&nbsp".$price."<br>";
                $count++;
            }
           
        }
        if($count < $allDeliveriCount){
            //$allDeliveriCount - $count;
            echo "messege" . "&nbsp". ($allDeliveriCount - $count)." - ".$GLOBALS['products_waiting_distribution'];
        }
    }else{
        echo "messege" . "&nbsp" . $GLOBALS['data_is_not'];
        return;
    }
  
}


//переписать доставку у товарa
function chenge_logistic_product_info($con,$warehouse_inventory_id, $logistic_product,$user_id,$update){

    $query="UPDATE `t_warehouse_inventory_in_out` 
                SET `logistic_product`='$logistic_product',`out_user_id`='$user_id',`out_updated_at`='$update'
                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
    $result=mysqli_query($con, $query) or die (mysqli_error($con));
}
//получить все собранные warhouse_inventory_id но не отправленные и показать их
function receive_list_collect_product($con,$my_warehouse_id, $partner_warehouse_id){
    $collected = '1';
    //echo "test1  my_warehouse_id:  $my_warehouse_id => partner_warehouse_id: $partner_warehouse_id <br>";
    $query="SELECT  `warehouse_inventory_id`,`product_inventory_id`, `quantity`,`logistic_product`
                 FROM `t_warehouse_inventory_in_out` 
                 WHERE `out_warehouse_id`='$my_warehouse_id' AND `in_warehouse_id`='$partner_warehouse_id' 
                         AND `collected`='$collected' AND `out_active`='0'"; //AND `out_active`IS NULL AND `out_active`= null
    $result=mysqli_query($con, $query) or die (mysqli_error($con));

   // echo "count: " . mysqli_num_rows($result) . "<br>";
    if(mysqli_num_rows($result) > 0){
        //echo "test2 <br>";
        while($row_w=mysqli_fetch_array($result)){
            $warehouse_inventory_id=$row_w[0];
            $product_inventory_id = $row_w[1];
            $quantity = $row_w[2];
            $logistic_product=$row_w[3];
           
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
            //$image_url=$product_list['image_url'];
            //$storage_conditions=$product_list['storage_conditions'];


            echo $my_warehouse_id."&nbsp".$product_id."&nbsp".$product_inventory_id."&nbsp".$category.
                    "&nbsp".$brand."&nbsp".$characteristic ."&nbsp".$type_packaging."&nbsp".$unit_measure.
                    "&nbsp".$weight_volume."&nbsp".$quantity_package."&nbsp".$quantity .
                    "&nbsp".$partner_warehouse_id."&nbsp".$collected."&nbsp".$warehouse_inventory_id.
                    "&nbsp".$logistic_product."&nbsp".$product_name."<br>";
           
        }
    }
}
    //сделать запись в (warehouse_inventory_in_out) о том что товар собран
    function write_collect_product_001($con,$my_warehouse_id, $partner_warehouse_id, $product_inventory_id, 
                                $quantity, $transaction_name, $collected, $user_id, $logistic_product){
        $date=date('Y-m-d H:i:s');
        $query="SELECT `warehouse_inventory_id`, `quantity` FROM `t_warehouse_inventory_in_out` 
                            WHERE `product_inventory_id`='$product_inventory_id' and `out_warehouse_id`='$my_warehouse_id'
                            and `in_warehouse_id`='$partner_warehouse_id' and `logistic_product`='$logistic_product' 
                            and `collected`='1' and `out_active`='0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_array($result);
            $warehouse_inventory_id = $row[0];
            $quantity_in_data = $row[1];
            $quantity_in_data = $quantity_in_data + $quantity;
            $query="UPDATE `t_warehouse_inventory_in_out` SET `quantity`='$quantity_in_data'
                        WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }else{
            $query="INSERT INTO `t_warehouse_inventory_in_out`
                                (`transaction_name`,`product_inventory_id`,`quantity`, `logistic_product`, `car_for_logistic`, 
                                `out_warehouse_id`,`collected`,  `out_active`, `in_warehouse_id`, `in_active`, `creator_user_id`) 
                        VALUES ('$transaction_name','$product_inventory_id','$quantity','$logistic_product',       '0'      ,
                                '$my_warehouse_id','$collected',      '0'     ,'$partner_warehouse_id',   '0'    ,'$user_id')";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
        }

        
    }
 /*
 //сделать запись в (warehouse_inventory_in_out) о том что товар собран
 function write_collect_product($con,$my_warehouse_id, $partner_warehouse_id, $product_inventory_id, 
                                $quantity, $transaction_name, $collected, $user_id, $logistic_product){
     $date=date('Y-m-d H:i:s');
    $query="INSERT INTO `t_warehouse_inventory_in_out`
                (`transaction_name`,`product_inventory_id`,`quantity`, `logistic_product`, `car_for_logistic`, 
                `out_warehouse_id`,`collected`,  `out_active`, `in_warehouse_id`, `in_active`, `creator_user_id`) 
         VALUES ('$transaction_name','$product_inventory_id','$quantity','$logistic_product',       '0'      ,
                '$my_warehouse_id','$collected',      '0'     ,'$partner_warehouse_id',   '0'    ,'$user_id')";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
 }

 */

//получить список моих складов (складПартнера)
function my_provider_warehouses_count_001($con,$counterparty_id){
    $query="SELECT wi.warehouse_info_id,
                    wi.city,
                    wi.street,
                    wi.house,
                    wi.building,
                    w.warehouse_id,
                    w.warehouse_type
                FROM t_warehouse_info wi
                    JOIN t_warehous w ON w.warehouse_info_id = wi.warehouse_info_id 
                                            AND w.active= '1'
                WHERE wi.counterparty_id = '$counterparty_id' AND wi.active = '1'";
    $result=mysqli_query($con,$query)or die (mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_array($result)){
            $warehouse_info_id=$row[0]; 
            $city=$row[1]; 
            $street=$row[2];  
            $house=$row[3];
            $building=$row[4];
            $warehouse_id=$row[5];
            $warehouse_type=$row[6];

            echo $warehouse_info_id . "&nbsp" . $city . "&nbsp" . $street . "&nbsp" . $house . "&nbsp" . 
                        $building . "&nbsp" . $warehouse_id . "&nbsp" . $warehouse_type . "<br>";
        }
    }else{
        echo "messege"."&nbsp".$GLOBALS['data_to_warehouse_is_not'];
    }
}
 
//получить список товаров для сборки "палета" для отправки на склад партнера 
function distribution_orders_provider_to_partners_002($con,$counterparty_id,$my_warehouse_id,$warehouse_type){
    //получить весь список товаров из заказов которые активные
    $arr = receiveAllProductFromOrdersActive($con);
    //удалить дубликаты
    $array_product_inventory_id = array_unique($arr);

     //получить из этого списка только товары этого склада поставщика
     $my_warehouse_prod_inv_array = get_list_only_product_warehouse($con, $my_warehouse_id, $array_product_inventory_id);

    //найти склады партнеров на которые надо поставить товар из списка поставщика) 
    //крутим список всех товаров заказанных 
    foreach($my_warehouse_prod_inv_array as $key => $value){//($my_product_inventory_array as $key => $value){
        $product_inventory_id = $value;
        //получаем только склады партнера на которые надо отправить товар поставщика
        $query="SELECT  o.warehouse_id, op.quantity, o.order_id
                FROM t_order_product op 
                JOIN t_order o ON o.order_id = op.order_id and o.order_active='1' and executed='0'
                WHERE op.product_inventory_id='$product_inventory_id'";
        $result = mysqli_query($con,$query) or die (mysqli_error($con));

        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $partner_warehouse_id = $row[0];
                $order_id = $row[2];
                $partner_warehouse_id_list_temp[] = $partner_warehouse_id;
                 
                //собрать остальные данные по товару
            }
        }
    }
    //убрать дубликаты
    $partner_warehouse_id_list = array_unique($partner_warehouse_id_list_temp);
    //берем склад из списка и складываем колличество товара с этого склада, со всех заказов на него
    foreach($partner_warehouse_id_list as $key => $value){
        $partner_warehouse_id = $value;
        
        //получить все открытые, активные, не выполненные заказы для этого склада
        $open_order_id_array = get_open_order_active_not_excecute_this_warehouse($con,$partner_warehouse_id);
        
        //крутим список всех товаров поставщика заказанных 
        foreach($my_warehouse_prod_inv_array as $key => $value){
            $product_inventory_id = $value;
            $quantity_all_order=0;

            //крутим список всех заказов для этого склада партнера 
            foreach($open_order_id_array as $key => $value){
                $open_order_id = $value;
                
                $query="SELECT `quantity` FROM `t_order_product` 
                                WHERE  `order_id`='$open_order_id' and `product_inventory_id` = '$product_inventory_id'";
                $result = mysqli_query($con,$query) or die (mysqli_error($con));
                while($row=mysqli_fetch_array($result)){
                    $quantity_all_order += $row[0];                  
                }       
            }
            if($quantity_all_order > 0){

                //получить остаток на этом (складе )
                $stock_quantity= get_stock_product_for_warehouse($con, $my_warehouse_id, $product_inventory_id);
            
                //получить остаток на этом (складе partner)
                $partner_stock_quantity= get_stock_product_for_warehouse($con, $partner_warehouse_id, $product_inventory_id);

                 //получить данные на складе сколько товара уже собрано для поставки
                $collected = '1';
                $quantity_to_collect = 0;
                $query="SELECT  `quantity`
                        FROM `t_warehouse_inventory_in_out` 
                        WHERE `out_warehouse_id`='$my_warehouse_id' AND `in_warehouse_id`='$partner_warehouse_id' 
                                AND `product_inventory_id`='$product_inventory_id' AND `collected`='$collected' 
                                AND `out_active` = '0' ";//`out_active`IS NULL
                $res_pr=mysqli_query($con, $query) or die (mysqli_error($con));
                if(mysqli_num_rows($res_pr) > 0){
                    while($row_w=mysqli_fetch_array($res_pr)){
                        $quantity_to_collect += $row_w[0];
                    }
                }
                //получить колличество отправленного но не полученного товара
                $quantity_give_away_bad_do_not_receive = 0;
                $query="SELECT  `quantity`
                        FROM `t_warehouse_inventory_in_out` 
                        WHERE `out_warehouse_id`='$my_warehouse_id' AND `in_warehouse_id`='$partner_warehouse_id' 
                                AND `product_inventory_id`='$product_inventory_id' AND `collected`='1' 
                                AND `out_active` = '1' AND `in_active`='0'";
                $res=mysqli_query($con, $query) or die (mysqli_error($con));
                if(mysqli_num_rows($res) > 0){
                    while($row=mysqli_fetch_array($res)){
                        $quantity_give_away_bad_do_not_receive += $row[0];
                    }
                }
                //echo "quantity_give_away_bad_do_not_receive: $quantity_give_away_bad_do_not_receive <br>";
                //получить данные(информацию) по товару
                $product_list = receive_product_info($con, $product_inventory_id);

                //получить данные(информацию) о складе partner
                $partner_warehouse_List = warehouseInfo($con,$partner_warehouse_id);
          
            echo  $my_warehouse_id  . "&nbsp" . $product_list['product_id'] . "&nbsp" . $product_inventory_id . 
                                "&nbsp" . $product_list['category'] . "&nbsp" . $product_list['brand'] . 
                                "&nbsp" . $product_list['characteristic'] . "&nbsp" . $product_list['type_packaging'] . 
                                "&nbsp" . $product_list['unit_measure'] . "&nbsp" . $product_list['weight_volume'] . 
                                "&nbsp" . $product_list['quantity_package'] . 
            "&nbsp" . $stock_quantity . 
            "&nbsp". $partner_stock_quantity. 
            "&nbsp" . $quantity_all_order .
                                "&nbsp" . $partner_warehouse_id. "&nbsp" . $partner_warehouse_List['city'] . 
                                "&nbsp" . $partner_warehouse_List['street'] . "&nbsp" . $partner_warehouse_List['house'] . 
                                "&nbsp" . $partner_warehouse_List['building'] .
            "&nbsp".$quantity_to_collect .
            "&nbsp".$quantity_give_away_bad_do_not_receive.
                                "&nbsp".$partner_warehouse_List['warehouse_info_id'].
                                "&nbsp".$product_name=$product_list['product_name']."<br>";

            }
        }
        
    }
}
    //получить список складов с которых надо сделать отгрузку по заказам
    function distribution_orders_by_warehouses_001($con,$counterparty_id){

        //получить весь список товаров из заказов которые активные
    $array = receiveAllProductFromOrdersActive($con);
    //удалить дубликаты
    $array_product_inventory_id = array_unique($array);
    
    $count = 0;
    //по product_inventory_id найти товары поставщика 
    foreach($array_product_inventory_id as $row){    
        $product_inventory_id=$row;

        $query = "SELECT  `product_inventory_id`FROM `t_product_inventory` 
                  WHERE `product_inventory_id`='$product_inventory_id' AND `counterparty_id`='$counterparty_id'";
        $res = mysqli_query($con,$query) or die (mysqli_error($con));        
        if(mysqli_num_rows($res) > 0){
            while($row=mysqli_fetch_array($res)){
                //this is provider products
                $product_inventory_id=$row[0];

                //найти на каких складах поставщика хранится товар               
                $array_w = stock_storage_warehouse($con, $product_inventory_id, $counterparty_id);
                foreach($array_w as $key => $v){
                    $arr[] = $v['warehouse_id'];                   
                }              
            }
        }
    }   
    $warehouse_id_list = array_unique($arr);
    
    //вернуть данные о складах хранения
    foreach ($warehouse_id_list as $warehouse => $items) {  
        $warehouse_id=$items; 

        $query="SELECT wi.warehouse_info_id,
                    wi.city,
                    wi.street,
                    wi.house,
                    wi.building,
                    w.warehouse_id
                FROM  t_warehous w
                    JOIN t_warehouse_info wi ON  wi.warehouse_info_id=w.warehouse_info_id                                                                  
                WHERE w.warehouse_id ='$warehouse_id'"; 
         $result=mysqli_query($con,$query)or die (mysqli_error($con));
             while($row=mysqli_fetch_array($result)){
                 $warehouse_info_id=$row[0]; 
                 $city=$row[1]; 
                 $street=$row[2];  
                 $house=$row[3];
                 $building=$row[4];
                 $warehouse_id=$row[5];
     
                 echo $warehouse_info_id . "&nbsp" . $city . "&nbsp". $street . "&nbsp" . $house . "&nbsp" . 
                             $building . "&nbsp" . $warehouse_id . "<br>";
             }
       
        } 

    }

//получить весь список товаров из заказов которые активные
function receiveAllProductFromOrdersActive($con){
    $array_product_inventory_id = [];
    $count = 0;
    //получить активные заказы
    $query="SELECT `order_id` FROM `t_order` 
            WHERE `order_active`='1' AND `executed`='0' AND `order_deleted`='0'";
    $result = mysqli_query($con,$query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        //плучить все product_inventory_id по всем открытым заказам
        while($row=mysqli_fetch_array($result)){
            $order_id=$row[0];

            $query="SELECT `product_inventory_id` FROM `t_order_product` WHERE `order_id`='$order_id'";
            $res = mysqli_query($con,$query) or die (mysqli_error($con));
            while($row=mysqli_fetch_array($res)){
                $product_inventory_id=$row[0];

                $array_product_inventory_id[$count++] = $product_inventory_id;
            }            
        }
    } 
    return $array_product_inventory_id;
}
//создать изменить тип склада
function create_tipe_warehouse($con,$warehouse_info_id,$warehouse_tipe,$active){
    $date = date('Y-m-d H:i:s');
    //ищем тип склада если его создавали
    $query="SELECT `warehouse_id` FROM `t_warehous` 
                    WHERE `warehouse_info_id`='$warehouse_info_id' AND `warehouse_type`='$warehouse_tipe'"; 
     $result = mysqli_query($con,$query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        //запись есть, редактируем запись
        $query="UPDATE `t_warehous` SET `active`='$active',`updated_at`='$date'
                     WHERE `warehouse_info_id`='$warehouse_info_id' AND `warehouse_type`='$warehouse_tipe'";     
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
    }else {
        //записи нет , создаем новую запись
        $query="INSERT INTO `t_warehous`( `warehouse_type`, `warehouse_info_id`, `active`, `updated_at`) 
                                 VALUES ('$warehouse_tipe','$warehouse_info_id', '$active',  '$date'   )";       
        $result = mysqli_query($con,$query) or die (mysqli_error($con));

          /* $query = "SELECT `warehouse_type_id` FROM `t_warehouse_type` 
                    WHERE `warehouse_id`='$warehouse_id' AND `warehouse_type`='$warehouse_tipe'";*/
         /* $query = "UPDATE `t_warehouse_type` SET `active`='$active',`updated_at`='$date'
                     WHERE `warehouse_id`='$warehouse_id' AND `warehouse_type`='$warehouse_tipe'";*/
        /* $query = "INSERT INTO `t_warehouse_type`( `warehouse_type`, `warehouse_id`, `active`, `updated_at`) 
                                          VALUES ('$warehouse_tipe','$warehouse_id','$active',  '$date'   )";*/
    }

}
//редактировать склад партнер в BD
function edit_warehouse($con,$warehouse_info_id,$user_uid,$region,$district,$city,$street,$house,$building,$signboard){
    $user_id = search_user_id($con, $user_uid);
    $date = date('Y-m-d H:i:s');

    $query="UPDATE `t_warehouse_info` SET `region`='$region',`district`='$district',`city`='$city',`street`='$street'
                                            ,`house`='$house',`building`='$building',`signboard`='$signboard'
                                             ,`redact_user_id`='$user_id',`updated_at`='$date'
                                    WHERE `warehouse_info_id`='$warehouse_info_id'";


  /*  $query = "UPDATE `t_warehouse` SET `region`='$region',`district`='$district',`city`='$city',`street`='$street'
                                        ,`house`='$house',`building`='$building',`signboard`='$signboard'
                                        ,`redact_user_id`='$user_id',`updated_at`='$date' 
                                    WHERE `warehouse_id`='$warehouse_id'";*/
    $result=mysqli_query($con,$query) or die(mysqli_error($con));
}

//найти все склады контрагента
function receive_all_my_warehouse_001($con,$counterparty_tax_id){
    $counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);

    $query="SELECT `warehouse_info_id`,  `region`, `district`, `city`, `street`, `house`, `building`, 
                    `signboard`, `active`, `created_at`
                FROM `t_warehouse_info` WHERE `counterparty_id`='$counterparty_id'";
    /*$query="SELECT `warehouse_id`, `region`, `district`, `city`, `street`, `house`, `building`, `signboard`, `active`, `created_at`
                     FROM `t_warehouse` WHERE `counterparty_id`='$counterparty_id'";*/
    $result=mysqli_query($con,$query) or die(mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_array($result)){
            $warehouse_info_id=$row[0];$region=$row[1];$district=$row[2];$city=$row[3];
            $street=$row[4];$house=$row[5];$building=$row[6];$signboard=$row[7];
            $active=$row[8];$created_at=$row[9];

            $providerWarehouse = '0';
            $partnerWarehouse = '0';
            $warhouseStorage_id = 0;
            $warhouseProvider_id = 0;
            $warhousePartner_id = 0;

            $query="SELECT `warehouse_id`, `warehouse_type`,  `active`
                         FROM `t_warehous` WHERE `warehouse_info_id`='$warehouse_info_id'";           
            $res_tipe=mysqli_query($con,$query) or die(mysqli_error($con));
            if(mysqli_num_rows($res_tipe) > 0){
                while($row=mysqli_fetch_array($res_tipe)){
                  // echo "tect" . "&nbsp". $row[0] . "<br>";
                    if(strcmp($row[1], "partner" ) === 0  and $row[2] == 1){
                        $partnerWarehouse = '1';
                        $warhousePartner_id = $row[0];
                    }else if(strcmp($row[1], "provider") === 0  and $row[2] == 1)  {
                        $providerWarehouse = '1';
                        $warhouseProvider_id = $row[0];
                    } else if(strcmp($row[1], "storage") === 0  and $row[2] == 1)  {
                        //$storageWarehouse = '1';
                        $warhouseStorage_id = $row[0];
                    }               
                }
            }
                //warStorageNum,warProviderNum,warPartnerNum
            echo $warehouse_info_id . "&nbsp" . $region . "&nbsp" . $district . "&nbsp" . $city . "&nbsp" . 
            $street . "&nbsp" . $house . "&nbsp" . $building . "&nbsp" . $signboard . "&nbsp" . 
            $active . "&nbsp" . $created_at . "&nbsp" . $providerWarehouse . "&nbsp" . $partnerWarehouse . "&nbsp" . 
            $warhouseStorage_id . "&nbsp" . $warhouseProvider_id . "&nbsp" . $warhousePartner_id . "<br>";
        }
    }
}
/*
//найти все склады контрагента
function receive_all_my_warehouse($con,$counterparty_tax_id){
    $counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);
    $query="SELECT `warehouse_id`, `region`, `district`, `city`, `street`, `house`, `building`, `signboard`, `active`, `created_at`
                     FROM `t_warehouse` WHERE `counterparty_id`='$counterparty_id'";
    $result=mysqli_query($con,$query) or die(mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_array($result)){
            $warehouse_id=$row[0];$region=$row[1];$district=$row[2];$city=$row[3];
            $street=$row[4];$house=$row[5];$building=$row[6];$signboard=$row[7];
            $active=$row[8];$created_at=$row[9];

            $providerWarehouse = '0';
            $partnerWarehouse = '0';

            $query = "SELECT `warehouse_type_id`, `warehouse_type`,`active` 
                            FROM `t_warehouse_type` WHERE `warehouse_id`='$warehouse_id' ";
            $res_tipe=mysqli_query($con,$query) or die(mysqli_error($con));
            if(mysqli_num_rows($res_tipe) > 0){
                while($row=mysqli_fetch_array($res_tipe)){
                  // echo "tect" . "&nbsp". $row[0] . "<br>";
                    if(strcmp($row[1], "partner" ) === 0  and $row[2] == 1){
                        $partnerWarehouse = '1';
                    }else if(strcmp($row[1], "provider") === 0  and $row[2] == 1)  {
                        $providerWarehouse = '1';
                    }                
                }
            }

            echo $warehouse_id . "&nbsp" . $region . "&nbsp" . $district . "&nbsp" . $city . "&nbsp" . 
            $street . "&nbsp" . $house . "&nbsp" . $building . "&nbsp" . $signboard . "&nbsp" . 
            $active . "&nbsp" . $created_at . "&nbsp" . $providerWarehouse . "&nbsp" . $partnerWarehouse . "<br>";
        }
    }
}
*/
//добавить(создать) склад партнера в BD
function create_new_warehouse($con,$user_uid,$counterparty_tax_id,$region,$district,$city,$street,$house,$building,$signboard){

    $counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);
    $user_id = search_user_id($con, $user_uid);

    $query="INSERT INTO `t_warehouse_info`( `counterparty_id`, `region`, `district`, `city`, `street`, `house`, `building`, `signboard`, `user_id`)  
                                   VALUES ('$counterparty_id','$region','$district','$city','$street','$house','$building','$signboard','$user_id')";
    $result=mysqli_query($con,$query) or die(mysqli_error($con));
    $warehouse_info_id=mysqli_insert_id($con);

    //сразу создать склад хранения
    $warehouse_type = 'storage';
    $query="INSERT INTO `t_warehous`( `warehouse_type`, `warehouse_info_id`, `active`) 
                             VALUES ( '$warehouse_type','$warehouse_info_id',  '1' )";
    $result=mysqli_query($con,$query) or die(mysqli_error($con));


    //printf("ID новой записи: %d.\n", mysqli_insert_id($link));
    //$query="INSERT INTO `t_warehouse`( `counterparty_id`, `region`, `district`, `city`, `street`, `house`, `building`, `signboard`, `user_id`) 
    //                         VALUES ('$counterparty_id','$region','$district','$city','$street','$house','$building','$signboard','$user_id')";
    
    
}
//внести запись о изменении состояния заказа
function update_check_box_condition_001($con,$prov_collect_prod_id, $processingCondition, $user_id){
    $date = date("Y-m-d H:i:s");
    
    $query="UPDATE `t_provider_collect_product` SET `yes_no`='$processingCondition',
                                                    `user_id`='$user_id',`updated_at`='$date' 
            WHERE `prov_collect_prod_id`='$prov_collect_prod_id'";
    $result = mysqli_query($con,$query) or die(mysqli_error($con));

}
function search_user_id($con, $user_uid){
    $query = "SELECT `user_id` FROM `t_user` WHERE `unique_id`='$user_uid'";
    $result = mysqli_query($con,$query) or die(mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $user_id=$row[0];

    return $user_id;
}

//внести запись состояния заказа
function insert_check_box_condition_001($con,$order_product_id, $processingCondition, $user_uid){
    $query="INSERT INTO `t_order_processing`( `order_product_id`, `processing_condition`, `created_user_uid`) 
                                    VALUES ('$order_product_id','$processingCondition', '$user_uid')";
    $result=mysqli_query($con, $query) or die (mysqli_error($con));

}

//найти состояние checkBox
function search_check_box_condition_001($con,$order_product_id, $search_for_condition){
    $query="SELECT `order_processing_id` FROM `t_order_processing` 
             WHERE `order_product_id`='$order_product_id' AND `processing_condition`='$search_for_condition'";
    $result=mysqli_query($con,$query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        $row=mysqli_fetch_array($result);
        $res=$row[0];//есть запись
    }else{
        $res='0';//нет записи
    }
    return $res;
}
// поставщик получает все заказы своих складов, не выполненные покупателей для сборки товара для агента
/*function receive_list_buyers_orders_product_002($con,$counterparty_id){


}*/
// поставщик получает все заказы своих складов, не выполненные покупателей для сборки товара для агента
function receive_list_buyers_orders_product_001($con,$taxpayer_id){
    $res = "";
    $query="SELECT `counterparty_id` FROM `t_counterparty` WHERE `taxpayer_id_number`='$taxpayer_id'";
    $result=mysqli_query($con, $query) or die(mysqli_error($con));
    //echo "product in receive 1" . "<br>";
    if(mysqli_num_rows($result) > 0){
        $row=mysqli_fetch_array($result);
        $counterparty_id=$row[0];
            //получить список активных заказов покупателей
        $query="SELECT `order_id` FROM `t_order` WHERE `order_active`='1'AND `executed`='0'";
        $res_order_list=mysqli_query($con, $query) or die(mysqli_error($con));
        //echo "product in receive 2" . "<br>";
        if(mysqli_num_rows($res_order_list) > 0){
            while($row=mysqli_fetch_array($res_order_list)){
                $order_id=$row[0];
                //получить (product_inventory_id) для поиска товаров поставщика в таблице t_product_inventory
                $query="SELECT `order_product_id`, `product_inventory_id`, `quantity` FROM `t_order_product` 
                        WHERE `order_id` = '$order_id'";
                $res_prod_order=mysqli_query($con, $query) or die(mysqli_error($con));
                //echo "product in receive 3" . "<br>";
                while($row=mysqli_fetch_array($res_prod_order)){
                    $order_product_id =$row[0];
                    $product_inventory_id=$row[1];
                    $quantity=$row[2];
                    //echo "product_inventory_id: " . $product_inventory_id . "<br>";
                    //ищем товары поставщика
                    $query_pi = "SELECT pr.product_id,                            
                            cat.category, 
                            br.brand,
                            cr.characteristic,
                            tp.type_packaging,
                            um.unit_measure,
                            pr.weight_volume,                            
                            pi.price,
                            pi.quantity_package,
                            im.image_url,
                            de.description,
                            pcp.yes_no                         
                       
                    FROM t_product_inventory pi
                        JOIN t_image im          ON im.image_id            = pi.image_id
                        JOIN t_description de    ON de.description_id      = pi.description_id
                        JOIN t_product pr        ON pr.product_id          = pi.product_id
                        JOIN t_category cat      ON cat.category_id        = pr.category_id
                        JOIN t_brand br          ON br.brand_id            = pr.brand_id
                        JOIN t_characteristic cr ON cr.characteristic_id   = pr.characteristic_id
                        JOIN t_type_packaging tp ON tp.type_packaging_id   = pr.type_packaging_id 
                        JOIN t_unit_measure um   ON um.unit_measure_id     = pr.unit_measure_id 
                        JOIN t_order_product op  ON op.order_product_id    = '$order_product_id'
                        JOIN t_provider_collect_product pcp ON pcp.prov_collect_prod_id = op.prov_collect_prod_id
                                               
                    WHERE pi.product_inventory_id='$product_inventory_id' AND pi.counterparty_id='$counterparty_id'";

                    $result_pi = mysqli_query($con, $query_pi) or die (mysqli_error($con));

                        if(mysqli_num_rows($result_pi) > 0){
                        $row = mysqli_fetch_array($result_pi);
                        $product_id=$row[0];
                        $category=$row[1]; $brand=$row[2]; $characteristic=$row[3]; 
                        $type_packaging=$row[4]; $unit_measure=$row[5]; $weight_volume=$row[6]; //$quantity_1=$row[7];
                         $price=$row[7]; $quantity_package= $row[8]; $image_url=$row[9]; $description=$row[10];
                         $orderProcessing=$row[11];

                        $provider_product_in_box='provider_product_in_box';

                        $res .= $order_product_id . "&nbsp" . $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . "&nbsp" . $brand . "&nbsp" . $characteristic. 
                        "&nbsp" . $type_packaging . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $price . 
                        "&nbsp" . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $quantity . "&nbsp" . $orderProcessing 
                        . "&nbsp" . $order_id. "<br>";

                    }
                    
                }
            }
        }
    }
    return $res;
}
    //получить каталог товаров поставщика с данными остатков на складах, продаж, колличества поставки
function product_in_partner_warehouse_array_001($con, $taxpayer_id,$warehouse_id){
    $result_info = "";
    //найти counterparty_id
    $counterparty_id = searchCounterpartyId($con, $taxpayer_id);  
    //получить список товаров (product_inventory_id list) на складе в запросе
    $query="SELECT `product_inventory_id`
             FROM `t_warehouse_inventory_in_out` WHERE `in_warehouse_id`='$warehouse_id' AND `out_active`='1'";//`out_active`='1'";     
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) < 1){
        //echo "test002 <br>";
        return;
    }
    while($row = mysqli_fetch_array($result)){
        $list[] = $row[0];
    }
   
    $prod_inv_list = array_unique($list);
    /*foreach($prod_inv_list as $k => $v){
        echo $v . "<br>";
    }*/
    //убрать из списка товары компании(чтобы показать только товары сторонних компаний для дальнейшей передачи заказчику)
    //echo "count1: " . count($prod_inv_list) . "<br>";
    foreach($prod_inv_list as $k => $product_inventory_id){
        $query="SELECT `product_inventory_id` FROM `t_product_inventory` 
                    WHERE `counterparty_id`!='$counterparty_id' AND `product_inventory_id`='$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $product_inventory_id_list[]=$row[0];
        //echo "product_inventory_id: " . $product_inventory_id. "<br>";
       // echo "test2: " . $row[0] . "<br>";
    }
    //$product_inventory_id_list = array_unique($list);
    //echo "count: " . count($product_inventory_id_list) . "<br>";
    //прокрутить список товаров и получть данные о товаре и его запас на складе
    foreach($product_inventory_id_list as $key => $value){
        $product_inventory_id = $value;
        //echo "test: ". $product_inventory_id . "<br>";
        //получть данные о товаре и его запас на складе
        $query="SELECT  pr.product_id,
                        pi.product_inventory_id,
                        cat.category, 
                        br.brand,
                        cr.characteristic,
                        tp.type_packaging,
                        um.unit_measure,
                        pr.weight_volume,                        
                        pi.price,
                        pi.quantity_package,
                        im.image_url,
                        de.description,
                        pn.product_name 
                    FROM t_product_inventory pi
                        JOIN t_product pr        ON pr.product_id        = pi.product_id
                        JOIN t_category cat      ON cat.category_id      = pr.category_id
                        JOIN t_product_name pn   ON pn.product_name_id   = pr.product_name_id
                        JOIN t_brand br          ON br.brand_id          = pr.brand_id
                        JOIN t_characteristic cr ON cr.characteristic_id = pr.characteristic_id
                        JOIN t_type_packaging tp ON tp.type_packaging_id = pr.type_packaging_id
                        JOIN t_unit_measure um   ON um.unit_measure_id   = pr.unit_measure_id 
                        JOIN t_image im          ON im.image_id          = pi.image_id
                        JOIN t_description de    ON de.description_id    = pi.description_id
                    WHERE pi.product_inventory_id = '$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
         //echo "test001 <br>";
        //echo "count2: " . mysqli_num_rows($result) . "<br>";
        //while($row = mysqli_fetch_array($result)){    
        while($row = mysqli_fetch_array($result)){        
            $product_id=$row[0]; $product_inventory_id=$row[1]; $product_name=$row[12] ; 
            $category=$row[2]; $brand=$row[3];
             $characteristic=$row[4]; 
            $type_packaging=$row[5]; $unit_measure=$row[6]; $weight_volume=$row[7]; $price=$row[8];
            $quantity_package= $row[9]; $image_url=$row[10]; $description=$row[11];
            //echo "info2: " . "<br>";
            //получить запас товара на складе
            //получить весь приход товара на склад
            $delivery_quantity = delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

            //получить весь расход товара со склада
            $sold_quantity = sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

            //проверить положительный запас
            if($delivery_quantity > $sold_quantity){
                //запас товара
                $stock_quantity = $delivery_quantity - $sold_quantity;
            
            }else {
                $stock_quantity=0;
            }
            if($stock_quantity > 0){
                $result_info .= $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . 
                    "&nbsp" . $brand . 
                    "&nbsp" . $characteristic. "&nbsp" . $type_packaging . "&nbsp" . $unit_measure . 
                    "&nbsp" . $weight_volume . //"&nbsp" . $total_quantity . 
                    "&nbsp" . $price . "&nbsp" . $quantity_package . 
                    "&nbsp" . $image_url . "&nbsp" . $description . //"&nbsp" . $total_sale_quantity . 
                    "&nbsp" . $stock_quantity . "&nbsp" . $product_name."<br>";
            }

            //echo "info: ". $result_info;
            //return $result_info;
        }
    }
    return $result_info;
}
/*
    //получить каталог товаров поставщика с данными остатков на складах, продаж, колличества поставки
function product_in_partner_warehouse_array($con, $taxpayer_id,$warehouse_id){
    $result_info = "";
    //найти counterparty_id
    $counterparty_id = searchCounterpartyId($con, $taxpayer_id);  
    //получить список товаров (product_inventory_id list) на складе в запросе
    $query="SELECT `product_inventory_id`
             FROM `t_warehouse_inventory_in_out` WHERE `in_warehouse_id`='$warehouse_id' AND `out_active`='1'";//`out_active`='1'";     
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) < 1){
        //echo "test002 <br>";
        return;
    }
    while($row = mysqli_fetch_array($result)){
        $list[] = $row[0];
    }
   
    $prod_inv_list = array_unique($list);
    foreach($prod_inv_list as $k => $v){
        echo $v . "<br>";
    }
    //убрать из списка товары компании(чтобы показать только товары сторонних компаний для дальнейшей передачи заказчику)
    //echo "count1: " . count($prod_inv_list) . "<br>";
    foreach($prod_inv_list as $k => $product_inventory_id){
        $query="SELECT `product_inventory_id` FROM `t_product_inventory` 
                    WHERE `counterparty_id`!='$counterparty_id' AND `product_inventory_id`='$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $product_inventory_id_list[]=$row[0];
        //echo "product_inventory_id: " . $product_inventory_id. "<br>";
       // echo "test2: " . $row[0] . "<br>";
    }
    //$product_inventory_id_list = array_unique($list);
    //echo "count: " . count($product_inventory_id_list) . "<br>";
    //прокрутить список товаров и получть данные о товаре и его запас на складе
    foreach($product_inventory_id_list as $key => $value){
        $product_inventory_id = $value;
        echo "test: ". $product_inventory_id . "<br>";
        //получть данные о товаре и его запас на складе
        $query="SELECT  pr.product_id,
                        pi.product_inventory_id,
                        cat.category, 
                        br.brand,
                        cr.characteristic,
                        tp.type_packaging,
                        um.unit_measure,
                        pr.weight_volume,                        
                        pi.price,
                        pi.quantity_package,
                        im.image_url,
                        de.description 
                    FROM t_product_inventory pi
                        JOIN t_product pr        ON pr.product_id        = pi.product_id
                        JOIN t_category cat      ON cat.category_id      = pr.category_id
                        JOIN t_brand br          ON br.brand_id          = pr.brand_id
                        JOIN t_characteristic cr ON cr.characteristic_id = pr.characteristic_id
                        JOIN t_type_packaging tp ON tp.type_packaging_id = pr.type_packaging_id
                        JOIN t_unit_measure um   ON um.unit_measure_id   = pr.unit_measure_id 
                        JOIN t_image im          ON im.image_id          = pi.image_id
                        JOIN t_description de    ON de.description_id    = pi.description_id
                    WHERE pi.product_inventory_id = '$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
         //echo "test001 <br>";
        //echo "count2: " . mysqli_num_rows($result) . "<br>";
        //while($row = mysqli_fetch_array($result)){    
        while($row = mysqli_fetch_array($result)){        
            $product_id=$row[0]; $product_inventory_id=$row[1]; $category=$row[2]; $brand=$row[3];
             $characteristic=$row[4]; 
            $type_packaging=$row[5]; $unit_measure=$row[6]; $weight_volume=$row[7]; $price=$row[8];
            $quantity_package= $row[9]; $image_url=$row[10]; $description=$row[11];
            //echo "info2: " . "<br>";
            //получить запас товара на складе
            //получить весь приход товара на склад
            $delivery_quantity = delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

            //получить весь расход товара со склада
            $sold_quantity = sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

            //проверить положительный запас
            if($delivery_quantity > $sold_quantity){
                //запас товара
                $stock_quantity = $delivery_quantity - $sold_quantity;
            
            }else {
                $stock_quantity=0;
            }

            $result_info .= $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . "&nbsp" . $brand . 
            "&nbsp" . $characteristic. "&nbsp" . $type_packaging . "&nbsp" . $unit_measure . 
            "&nbsp" . $weight_volume . //"&nbsp" . $total_quantity . 
            "&nbsp" . $price . "&nbsp" . $quantity_package . 
            "&nbsp" . $image_url . "&nbsp" . $description . //"&nbsp" . $total_sale_quantity . 
            "&nbsp" . $stock_quantity . "<br>";

            //echo "info: ". $result_info;
            return $result_info;
        }
    }
}
*/
//товары которые не прошли модерацию
function product_provider_for_moderation($con, $taxpayer_id,$warehouse_id){
    $result_info = "";
    $query="SELECT  `category`, `brand`, `characteristic`, `type_packaging`, `unit_measure`,
                    `weight_volume`, `price`, `quantity`, `quantity_package`, `image_url`, 
                    `product_name`, `description`, `in_product_name`
                FROM `t_input_product` 
                WHERE `warehouse_id`='$warehouse_id' and `on_off`='1'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){        
            $product_id='0'; 
            $product_inventory_id='0'; 
            $category=$row[0]; 
            $brand=$row[1];
             $characteristic=$row[2]; 
            $type_packaging=$row[3]; 
            $unit_measure=$row[4]; 
            $weight_volume=$row[5]; 
            $price=$row[6];
            $stock_quantity= $row[7];
            $quantity_package= $row[8]; 
            $image_url=$row[9]; 
            $product_name=$row[10]; 
            $description=$row[11];
            $product_name_from_provider = $row[12];

            $product_info = $product_name." ".$characteristic." ".mb_ucfirst($brand)." "
                            .$type_packaging." ".$weight_volume." ".$unit_measure." "
                            .$GLOBALS['quantity_in_package']." ".$quantity_package;
    

            $result_info .= $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . 
            "&nbsp" . $brand . "&nbsp" . $characteristic. "&nbsp" . $type_packaging . 
            "&nbsp" . $unit_measure . "&nbsp" . $weight_volume .  "&nbsp" . $price . 
            "&nbsp" . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $description . 
            "&nbsp" . $stock_quantity . "&nbsp" . $product_name ."&nbsp" . $product_info.
            "&nbsp" . $product_name_from_provider."<br>";
             

           /* $result_info .= $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . 
                    "&nbsp" . $brand . "&nbsp" . $characteristic. "&nbsp" . $type_packaging . 
                    "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $price . 
                    "&nbsp" . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $description . 
                    "&nbsp" . $stock_quantity . "&nbsp" . $product_name."<br>";*/
        }
    }

    return $result_info;
}
//получить каталог товаров поставщика с данными остатков на складах, продаж, колличества поставки
function product_provider_array_001($con, $taxpayer_id,$warehouse_id){
    $result_info = "";
    //найти counterparty_id
    $counterparty_id = searchCounterpartyId($con, $taxpayer_id);  
    //получить список товаров (product_inventory_id list) на складе в запросе
    $query="SELECT `product_inventory_id`
             FROM `t_warehouse_inventory_in_out` WHERE `in_warehouse_id`='$warehouse_id'";     
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) < 1){
        //echo "test002 <br>";
        return;
    }
    while($row = mysqli_fetch_array($result)){
        $list[] = $row[0];
    }   
    $prod_inv_list = array_unique($list);
    //убрать из списка товары сторонних компаний(те что собираем в заказы и выдаем)
    foreach($prod_inv_list as $k => $product_inventory_id){
        $query="SELECT `product_inventory_id` FROM `t_product_inventory` 
                    WHERE `counterparty_id`='$counterparty_id' AND `product_inventory_id`='$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $product_inventory_id_list[]=$row[0];
    }
    //прокрутить список товаров и получть данные о товаре и его запас на складе
    foreach($product_inventory_id_list as $key => $value){
        $product_inventory_id = $value;        
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
            //$storage_conditions=$product_list['storage_conditions'];
            $price=$product_list['price'];            
            $product_name_from_provider=$product_list['product_name_from_provider'];
            //$min_sell=$product_list['min_sell'];
            //$multiple_of=$product_list['multiple_of'];
            $description_prod=$product_list['description_prod'];
            $product_info=$product_list['product_info'];

            //получить запас товара на складе
            //получить весь приход товара на склад
            $delivery_quantity = delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

            //получить весь расход товара со склада
            $sold_quantity = sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

            //проверить положительный запас
            if($delivery_quantity > $sold_quantity){
                //запас товара
                $stock_quantity = $delivery_quantity - $sold_quantity;
            
            }else {
                $stock_quantity=0;
            }

            $result_info .= $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . 
            "&nbsp" . $brand . "&nbsp" . $characteristic. "&nbsp" . $type_packaging . 
            "&nbsp" . $unit_measure . "&nbsp" . $weight_volume .  "&nbsp" . $price . 
            "&nbsp" . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $description_prod . 
            "&nbsp" . $stock_quantity . "&nbsp" . $product_name ."&nbsp" . $product_info.
            "&nbsp" . $product_name_from_provider."<br>";
    } 
    return $result_info;
}

/*
//получить каталог товаров поставщика с данными остатков на складах, продаж, колличества поставки
function product_provider_array_001($con, $taxpayer_id,$warehouse_id){
    $result_info = "";
    //найти counterparty_id
    $counterparty_id = searchCounterpartyId($con, $taxpayer_id);  
    //получить список товаров (product_inventory_id list) на складе в запросе
    $query="SELECT `product_inventory_id`
             FROM `t_warehouse_inventory_in_out` WHERE `in_warehouse_id`='$warehouse_id'";     
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) < 1){
        //echo "test002 <br>";
        return;
    }
    while($row = mysqli_fetch_array($result)){
        $list[] = $row[0];
    }
   
    $prod_inv_list = array_unique($list);
    //убрать из списка товары сторонних компаний(те что собираем в заказы и выдаем)
    foreach($prod_inv_list as $k => $product_inventory_id){
        $query="SELECT `product_inventory_id` FROM `t_product_inventory` 
                    WHERE `counterparty_id`='$counterparty_id' AND `product_inventory_id`='$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $product_inventory_id_list[]=$row[0];
    }
    //$product_inventory_id_list = array_unique($list);
    //echo "count2: " . count($prod_inv_list) . "<br>";
    //прокрутить список товаров и получть данные о товаре и его запас на складе
    foreach($product_inventory_id_list as $key => $value){
        $product_inventory_id = $value;
        //echo "test: ". $product_inventory_id . "<br>";
        //получть данные о товаре и его запас на складе
        $query="SELECT  pr.product_id,
                        pi.product_inventory_id,
                        cat.category, 
                        br.brand,
                        cr.characteristic,
                        tp.type_packaging,
                        um.unit_measure,
                        pr.weight_volume,                        
                        pi.price,
                        pi.quantity_package,
                        im.image_url,
                        de.description,
                        pn.product_name 
                    FROM t_product_inventory pi
                        JOIN t_product pr        ON pr.product_id        = pi.product_id
                        JOIN t_category cat      ON cat.category_id      = pr.category_id
                        JOIN t_product_name pn   ON pn.product_name_id   = pr.product_name_id
                        JOIN t_brand br          ON br.brand_id          = pr.brand_id
                        JOIN t_characteristic cr ON cr.characteristic_id = pr.characteristic_id
                        JOIN t_type_packaging tp ON tp.type_packaging_id = pr.type_packaging_id
                        JOIN t_unit_measure um   ON um.unit_measure_id   = pr.unit_measure_id 
                        JOIN t_image im          ON im.image_id          = pi.image_id
                        JOIN t_description de    ON de.description_id    = pi.description_id
                    WHERE pi.product_inventory_id = '$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
         //echo "test001 <br>";
        //echo "count: " . mysqli_num_rows($result) . "<br>";
        //while($row = mysqli_fetch_array($result)){    
        while($row = mysqli_fetch_array($result)){        
            $product_id=$row[0]; $product_inventory_id=$row[1]; $category=$row[2]; 
             $brand=$row[3]; $characteristic=$row[4]; 
            $type_packaging=$row[5]; $unit_measure=$row[6]; $weight_volume=$row[7]; $price=$row[8];
            $quantity_package= $row[9]; $image_url=$row[10]; $description=$row[11]; $product_name = $row[12]; 
            //echo "info2: " . "<br>";
            //получить запас товара на складе
            //получить весь приход товара на склад
            $delivery_quantity = delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

            //получить весь расход товара со склада
            $sold_quantity = sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

            //проверить положительный запас
            if($delivery_quantity > $sold_quantity){
                //запас товара
                $stock_quantity = $delivery_quantity - $sold_quantity;
            
            }else {
                $stock_quantity=0;
            }

            $result_info .= $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . 
            "&nbsp" . $brand . "&nbsp" . $characteristic. "&nbsp" . $type_packaging . 
            "&nbsp" . $unit_measure . 
            "&nbsp" . $weight_volume . //"&nbsp" . $total_quantity . 
            "&nbsp" . $price . "&nbsp" . $quantity_package . 
            "&nbsp" . $image_url . "&nbsp" . $description . //"&nbsp" . $total_sale_quantity . 
            "&nbsp" . $stock_quantity . "&nbsp" .$product_name."<br>";

            //echo "info: ". $result_info;

        }
    } 

       return $result_info;
}*/

//получить данные продаж поставщика , колличество за весь период
function saleProductThisProvider($con,$product_inventory_id){
    //найти все закaзы данного продукта
    $query="SELECT  `quantity`, `order_id` FROM `t_order_product` WHERE `product_inventory_id` = $product_inventory_id";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));

    $total_quantity = 0;
    while($row = mysqli_fetch_array($result)){
        $quantity=$row[0]; 
        $order_id=$row[1];
        //найти тлько выполненные закaзы
        $query="SELECT  `executed` FROM `t_order` WHERE `order_id` = $order_id";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row_temp = mysqli_fetch_array($result);
        if($row_temp[0] == 1){
            $total_quantity .=$quantity;
        }
    }
    
    //вернуть колличество продукта в выполненных заказах
    return $total_quantity;
}
    //получить остаток товара на этом (складе поставщика)
    function get_stock_product_for_warehouse($con, $warehouse_id, $product_inventory_id){
        //получить весь приход товара на склад
        $delivery_quantity = delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id);
                
        //получить весь расход товара со склада
        $sold_quantity = sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id);
    
        //проверить положительный запас
        if($delivery_quantity > $sold_quantity){
            //запас товара
            $stock_quantity = $delivery_quantity - $sold_quantity;
            
        }else {
            $stock_quantity=0;
        }
        return $stock_quantity; 
    }
    //найти на каких складах поставщика хранится товар
    function stock_storage_warehouse($con, $product_inventory_id, $counterparty_id){
        $warehouse_id_list = [];
        //получить список (склад поставщик) контрагента
        $query="SELECT  w.warehouse_id
                    FROM   t_warehouse_info wi
                        JOIN t_warehous w ON  w.warehouse_info_id=wi.warehouse_info_id 
                                                AND w.warehouse_type= 'provider' AND w.active = '1'
                    WHERE wi.counterparty_id = '$counterparty_id' AND wi.active = '1'";          
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0];

                //найти склады на которых есть запас товара
                //получить весь приход товара на склад
                $delivery_quantity = delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id);
                
                //получить весь расход товара со склада
                $sold_quantity = sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id);
            
                //проверить положительный запас
                if($delivery_quantity > $sold_quantity){
                    //запас товара
                    //$stock_quantity = $delivery_quantity - $sold_quantity;
                    $warehouse_id_list[] = ['warehouse_id' => $warehouse_id];
                    
                }
            }
        }    
        
            return $warehouse_id_list;        
                  
    }
    /*
     //найти на каких складах поставщика хранится товар
    function stock_storage_warehouse($con, $product_inventory_id, $counterparty_id){
        $warehouse_id_list = [];
        //получить список (склад поставщик) контрагента
        $query = "SELECT wt.warehouse_id
                        FROM t_warehouse w 
                            JOIN t_warehouse_type wt 
                                ON wt.warehouse_id=w.warehouse_id AND wt.warehouse_type='provider' AND wt.active='1'
                        WHERE w.counterparty_id='$counterparty_id' AND w.active='1'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_id=$row[0];

                //найти склады на которых есть запас товара
                //получить весь приход товара на склад
                $delivery_quantity = delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id);
                
                //получить весь расход товара со склада
                $sold_quantity = sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id);
            
                //проверить положительный запас
                if($delivery_quantity > $sold_quantity){
                    //запас товара
                    //$stock_quantity = $delivery_quantity - $sold_quantity;
                    $warehouse_id_list[] = ['warehouse_id' => $warehouse_id];
                    
                }
            }
        }    
        
            return $warehouse_id_list;        
                  
    }
    */
     //получить из этого списка только товары этого поставщика
     function get_from_list_only_product_counterparty($con, $counterparty_id, $array_product_inventory_id){
        foreach($array_product_inventory_id as $key => $v){
            $product_inventory_id = $v;
            $query = "SELECT `product_inventory_id` FROM `t_product_inventory` 
                    WHERE `counterparty_id`='$counterparty_id' AND `product_inventory_id`='$product_inventory_id'";
            $res = mysqli_query($con, $query) or die (mysqli_error($con));
            if(mysqli_num_rows($res) > 0){

                $my_product_inventory_array[] = $product_inventory_id;
            }
        }
        /*foreach($my_product_inventory_array as $key => $v){
            echo "$key : $v" . "<br>";
        }*/
        return $my_product_inventory_array;
     }
     //получить из этого списка только товары этого склада поставщика
    function get_list_only_product_warehouse($con, $my_warehouse_id, $array_product_inventory_id){
        foreach($array_product_inventory_id as $key => $v){
            $product_inventory_id = $v;

            //получить остаток на этом складе этого товара
            $partner_stock_quantity = stock_product_to_warehouse($con, $my_warehouse_id, $product_inventory_id);

            //есль остаток товара есть то кладем в массив
            if($partner_stock_quantity > 0){
                $my_warehouse_prod_inv_array[] = $product_inventory_id;
            }           
        }

        return $my_warehouse_prod_inv_array;
    }
        
    //получить carID
    function receiveCarId($con, $warehouse_inventory_id){
        $query = "SELECT `car_id`FROM `t_logistic_product` WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($res);
        $car_id = $row[0];

        return $car_id;
    }
     //получить данные(информацию) по товару
    /* function receive_product_info($con, $product_inventory_id){
         //ищем товары поставщика
         $query = "SELECT pr.product_id,                            
                            cat.category, 
                            br.brand,
                            cr.characteristic,
                            tp.type_packaging,
                            um.unit_measure,
                            pr.weight_volume,                            
                            pi.quantity_package                           
                    
                            FROM t_product_inventory pi
                                JOIN t_product pr        ON pr.product_id          = pi.product_id
                                JOIN t_category cat      ON cat.category_id        = pr.category_id
                                JOIN t_brand br          ON br.brand_id            = pr.brand_id
                                JOIN t_characteristic cr ON cr.characteristic_id   = pr.characteristic_id
                                JOIN t_type_packaging tp ON tp.type_packaging_id   = pr.type_packaging_id 
                                JOIN t_unit_measure um   ON um.unit_measure_id     = pr.unit_measure_id 
                            WHERE pi.product_inventory_id='$product_inventory_id'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($res);
        $product_id=$row[0];
        $category=$row[1];
        $brand=$row[2];
        $characteristic=$row[3];
        $type_packaging=$row[4];
        $unit_measure=$row[5];
        $weight_volume=$row[6];
        $quantity_package=$row[7];

        $product_list = array('product_id' => $product_id,'product_inventory_id' => $product_inventory_id,
                                'category' => $category,'brand' => $brand,
                            'characteristic' => $characteristic, 'type_packaging' => $type_packaging,
                            'unit_measure' => $unit_measure,'weight_volume' => $weight_volume,
                            'quantity_package' => $quantity_package);        
                         
        return $product_list;
     }*/
     //delete
     //получить список складов поставщика
     function receive_all_prowider_warehouse($con, $counterparty_id){
         $query="SELECT w.warehouse_id
                        FROM t_warehouse w
                            JOIN t_warehouse_type wt ON wt.warehouse_id = w.warehouse_id 
                                                    AND warehouse_type='provider' 
                                                    AND wt.active='1'
                        WHERE counterparty_id = '$counterparty_id' AND w.active='1'";
         $res = mysqli_query($con, $query) or die (mysqli_error($con));
         
        while($row = mysqli_fetch_array($res)){
            $provider_warehouse_list[] = $row[0];
        }               
        return $provider_warehouse_list;
     }
      //получить адрес складa
  /*  function receiveWarehouseAddress($con, $warehouse_id){   
              
        $query="SELECT `city`, `street`, `house`, `building`FROM `t_warehouse` 
                                    WHERE `warehouse_id`='$warehouse_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
                $city=$row[0];
                $street=$row[1];
                $house=$row[2];
                $building=$row[3];        

        $warehouse_address_list = 
                ['city'=>$city,'street'=>$street,'house'=>$house,'building'=>$building];
        return $warehouse_address_list;
    }*/
  
    //получить данные товара
    /*function receiveProductInfo($con,$product_inventory_id){//return $product_info_list;
        //получть данные о товаре и его запас на складе
        $query="SELECT  cat.category, 
                        br.brand,
                        cr.characteristic,
                        tp.type_packaging,
                        um.unit_measure,
                        pr.weight_volume,                    
                        pi.quantity_package,
                        im.image_url                        
                        
                    FROM t_product_inventory pi
                        JOIN t_product pr        ON pr.product_id        = pi.product_id
                        JOIN t_category cat      ON cat.category_id      = pr.category_id
                        JOIN t_brand br          ON br.brand_id          = pr.brand_id
                        JOIN t_characteristic cr ON cr.characteristic_id = pr.characteristic_id
                        JOIN t_type_packaging tp ON tp.type_packaging_id = pr.type_packaging_id
                        JOIN t_unit_measure um   ON um.unit_measure_id   = pr.unit_measure_id 
                        JOIN t_image im          ON im.image_id          = pi.image_id
                    WHERE pi.product_inventory_id = '$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);       
             $category=$row[0]; $brand=$row[1]; $characteristic=$row[2]; 
            $type_packaging=$row[3]; $unit_measure=$row[4]; $weight_volume=$row[5]; 
            $quantity_package= $row[6]; $image_url=$row[7]; 
        //собрать массив информация о товаре
        $product_info_list = ['category'=>$category,'brand'=>$brand,'characteristic'=>$characteristic,
                                'type_packaging'=>$type_packaging,'unit_measure'=>$unit_measure,
                                'weight_volume'=>$weight_volume,'quantity_package'=>$quantity_package,
                                'image_url'=>$image_url];   
        
       return $product_info_list;
    }*/
    //найти user_id
   /* function checkUserID($con, $user_uid){ 
        $query="SELECT `user_id` FROM `t_user` WHERE `unique_id` = '$user_uid'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $user_id = $row[0];
        //echo "user_id " . $user_id . "<br>";
        return $user_id;
        }*/
    //найти counterparty_id searchCounterpartyId
    /*function searchCounterpartyId($con, $taxpayer_id){     //$counterparty_id
        $query = "SELECT counterparty_id FROM t_counterparty WHERE taxpayer_id_number = '$taxpayer_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($row = mysqli_fetch_array($result)){
            $counterparty_id = $row[0];
        }else{
            //echo "error: " . $query . "<br>" . mysqli_error($con);
            $counterparty_id = 0;
        }
        return $counterparty_id;
    }*/

    mysqli_close($con);
?>