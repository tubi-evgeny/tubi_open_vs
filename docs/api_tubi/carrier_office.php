<?php
    include 'connect.php';
    include 'text.php';
    include_once 'helper_classes.php';
	 
	mysqli_query($con,"SET NAMES 'utf8'");

    //receive_list_delivery
    //receive_list_cars
    //receive_list_cars_user
    //receive_list_delivery_for_car
    //receive_list_warehouse_and_product_for_delivery
    //receive_list_hand_over_product
    //receive_product_weight_for_car
    //record_goods_and_car_for_delivery
    //write_check_to_logistic_table
    //write_check_give_out_to_logistic_table
    //update_and_delete_logistic_product
    //check_invoice_key_to_write_close
    //$data
    
	 
     //получит список доставок товаров
    if(isset($_GET['receive_list_delivery'])){
        
        receive_list_delivery_01($con);  
        //receive_list_delivery($con);      
        
    }
    //получить список всех авто для доставки
    else if(isset($_GET['receive_list_cars'])){
        
        receive_list_cars($con);        
        
    }
     //записать товар и авто в t_logistic_product
     else if(isset($_GET['record_goods_and_car_for_delivery'])){        
        $user_uid = $_GET['user_uid'];
        $car_id = $_GET['car_id'];
        $warehouse_inventory_id = $_GET['logistic_product_id'];

        //$warehouse_inventory_id=$logistic_product_id;    
       
        //найти user_id
        $user_id = checkUserID($con, $user_uid);
        
        record_goods_and_car_for_delivery($con, $warehouse_inventory_id, $user_id, $car_id);       
       
    } //получить список товаров для транспорта которые добавлены в доставку t_logistic_product
    else  if(isset($_GET['receive_list_delivery_for_car'])){
        $car_id = $_GET['car_id'];
         
        receive_list_delivery_for_car($con, $car_id);     
        
    }//удалить все исправленные на check=0; брони товаров из БД
    else  if(isset($_GET['update_and_delete_logistic_product'])){
        $warehouseInventory_id = $_GET['warehouseInventory_id'];
         
        update_and_delete_logistic_product($con, $warehouseInventory_id);     
        
    }//получить список автомобилей user
    else  if(isset($_GET['receive_list_cars_user'])){
        //$user_uid = $_GET['user_uid'];
        $taxpayer_id = $_GET['taxpayer_id'];

        //найти user_id
        //$user_id = checkUserID($con, $user_uid);
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
         
        receive_list_cars_user($con, $counterparty_id);     
        
    }//получить все склады и собранные товары warhouse_inventory_id дла погрузки и доставки
    else  if(isset($_GET['receive_list_warehouse_and_product_for_delivery'])){
        $car_id = $_GET['car_id'];
         
        receive_list_warehouse_and_product_for_delivery_01($con, $car_id);   
        //receive_list_warehouse_and_product_for_delivery($con, $car_id);   
        
    }
    //получить все товары которые загружены в авто и готовы к выдачи на склад
    else  if(isset($_GET['receive_list_hand_over_product'])){
        $car_id = $_GET['car_id'];
         
        receive_list_hand_over_product($con, $car_id);   
        
    }//записать галочки товаров в таблицу логистики и склада
    else  if(isset($_GET['write_check_to_logistic_table'])){
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];
        $check = $_GET['check'];
         
        write_check_to_logistic_table($con, $warehouse_inventory_id, $check);     
        
    }//получить данные автомобиля
    else  if(isset($_GET['get_car_info'])){
        $car_id = $_GET['car_id'];
         
        get_car_info($con, $car_id);     
        
    }//записать галочки передачи товаров в таблицу логистики и склада
    else  if(isset($_GET['write_check_give_out_to_logistic_table'])){
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];
        $check = $_GET['check'];
         
        write_check_give_out_to_logistic_table($con, $warehouse_inventory_id, $check);     
        
    }
    //получить список товаров для транспорта которые добавлены в доставку t_logistic_product
   /* else  if(isset($_GET['receive_list_delivery_for_car'])){
        $warehouse_inventory_id = $_GET['warehouse_inventory_id'];
        $car_id = $_GET['car_id'];
         
        receive_list_delivery_for_car($con, $car_id);     
        
    }*/
    //получить вес товаров для транспорта которые уже добавлены в доставку t_logistic_product
    else  if(isset($_GET['receive_product_weight_for_car'])){
        $car_id = $_GET['car_id'];
        
        receive_product_weight_for_car($con, $car_id);     
        
    }
    //получить ключ документов проверить все ли товары получены,
    //если все то закрыть ключ к документам
    else  if(isset($_GET['check_invoice_key_to_write_close'])){
        $invoice_key_id = $_GET['invoice_key_id'];
        
        check_invoice_key_to_write_close($con, $invoice_key_id);     
        
    }
    //получить ключ документов проверить все ли товары получены,
    //если все то закрыть ключ к документам
    function check_invoice_key_to_write_close($con, $invoice_key_id){
        try{
            $flag = false;
            $out_active_product = 1;
            //получить все товары с этим ключем
            $query="SELECT  lp.take_in 
                        FROM t_warehouse_inventory_in_out win 
                            JOIN t_logistic_product lp ON lp.warehouse_inventory_id = win.warehouse_inventory_id
                        WHERE win.invoice_key_id='$invoice_key_id'";
            $res=mysqli_query($con, $query) or die(mysqli_error($con));
                while($row=mysqli_fetch_array($res)){
                    $take_in=$row[0];
                    if($take_in == 0){
                        //проверить все ли товары получены контракентом поставщика
                        $flag = true;
                        //$out_active_product = 0;
                    }
                }               

            //если все то закрыть ключ к изменениям в документе
            if($flag == false){
                $query="UPDATE `t_invoice_key` SET `closed`='1' WHERE `invoice_key_id`='$invoice_key_id'";
                mysqli_query($con, $query) or die(mysqli_error($con));            
            }
        }catch(Exception $ex){

        }

    }
  
    //получить вес товаров для транспорта которые уже добавлены в доставку t_logistic_product
    function receive_product_weight_for_car($con, $car_id){
        $general_weight_all_product = 0;
        //получить товары которые авто должно везти(определены в авто)
        $query="SELECT  `warehouse_inventory_id`
                    FROM `t_logistic_product` WHERE `car_id` = '$car_id' and `take_in`='0'";
        $res=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($res) > 0){
            while($row=mysqli_fetch_array($res)){
                $warehouse_inventory_id=$row[0];
                //получить колличество товара
                $query="SELECT `product_inventory_id`, `quantity` FROM `t_warehouse_inventory_in_out` 
                                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
                $result=mysqli_query($con, $query) or die(mysqli_error($con));
                $row=mysqli_fetch_array($result);
                    $product_inventory_id=$row[0];
                    $quantity=$row[1];

                //получить вес единицы товара
                $query="SELECT pr.weight_volume
                                FROM t_product_inventory pin
                                    JOIN t_product pr ON pr.product_id=pin.product_id
                                WHERE product_inventory_id='$product_inventory_id'";
                $result=mysqli_query($con, $query) or die(mysqli_error($con));
                $row=mysqli_fetch_array($result);
                    $weight_volume=$row[0];                              

                //получить общий вес товара в позиции
                $general_weight = $quantity * $weight_volume;
                //echo "general_weight: $general_weight <br>";
                //сложить и показать общий вес всего товара
                $general_weight_all_product += $general_weight;

            }
        }
        echo $general_weight_all_product / 1000;


    }
     //записать галочки передачи товаров в таблицу логистики и склада
     function write_check_give_out_to_logistic_table($con, $warehouse_inventory_id, $check){
        $query="UPDATE `t_logistic_product` SET `give_out`='$check' 
                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));

       /* $query="UPDATE `t_warehouse_inventory_in_out` SET `out_active`='$check'
                     WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));*/
    }
    //получить данные автомобиля
    function get_car_info($con, $car_id){
        $query="SELECT  `car_brand`, `car_model`, `registration_num`
                     FROM `t_car` WHERE `car_id` = '$car_id'";
                $result=mysqli_query($con, $query) or die(mysqli_error($con));        
                $row=mysqli_fetch_array($result);
                
                $car_brand=$row[0];
                $car_model=$row[1];
                $registration_num=$row[2];

            echo $car_brand."&nbsp".$car_model."&nbsp".$registration_num."<br>";

    }
    //записать галочки товаров в таблицу логистики и склада
    function write_check_to_logistic_table($con, $warehouse_inventory_id, $check){
        $query="UPDATE `t_logistic_product` SET `take_in`='$check' 
                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));

        $query="UPDATE `t_warehouse_inventory_in_out` SET `out_active`='$check'
                     WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
    }

       //получить все товары которые загружены в авто и готовы к выдачи на склад
       function receive_list_hand_over_product($con, $car_id){
        //получить все заказы приписанные этому авто
        //получить номер товарной накладной
        $query="SELECT win.warehouse_inventory_id,
                        win.product_inventory_id,
                        win.quantity,                        
                        win.in_warehouse_id,                        
                        lp.give_out,                        
                        dd.document_num,
                        ik.closed,                        
                        ii.invoice_key_id
        
                     FROM t_logistic_product lp
                        JOIN t_warehouse_inventory_in_out win ON win.warehouse_inventory_id = lp.warehouse_inventory_id 
                                                                and in_active = '0'
                        JOIN t_invoice_info ii ON ii.warehouse_inventory_id = win.warehouse_inventory_id
                        JOIN t_document_deal dd ON dd.invoice_key_id = ii.invoice_key_id
                        JOIN t_invoice_key ik ON ik.invoice_key_id = ii.invoice_key_id
                     WHERE lp.car_id ='$car_id' and lp.take_in = '1'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));//win.out_active,lp.take_in,win.out_warehouse_id,ik.save,
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_inventory_id=$row[0];
                $product_inventory_id=$row[1];
                $quantity=$row[2];
                $inWarehouse_id=$row[3];  
                $give_out=$row[4];   
                $document_num = $row[5];   
                $document_closed = $row[6];    
                //$save = $row[7]; 
                $invoice_key_id = $row[7];

                //получить номер транспортной накладной
                $query="";
                
                //собрать массив склад-товар
                $warehouse_product_list[] = ['warehouse_inventory_id' => $warehouse_inventory_id,
                                            'product_inventory_id'=>$product_inventory_id, 
                                             'quantity'=>$quantity, 
                                             'inWarehouse_id'=>$inWarehouse_id,
                                             'give_out'=>$give_out,
                                             'document_num'=>$document_num,
                                             'document_closed'=>$document_closed,
                                             //'save'=>$save,
                                              'invoice_key_id'=>$invoice_key_id]; 
            }       
        
        //получить адреса складов и передать данные
        foreach($warehouse_product_list as $key => $split_warehouse_product){

            $warehouse_inventory_id = $split_warehouse_product['warehouse_inventory_id'];
            $product_inventory_id = $split_warehouse_product['product_inventory_id'];
            $inWarehouse_id = $split_warehouse_product['inWarehouse_id'];
            $quantity = $split_warehouse_product['quantity'];   
            $give_out=$split_warehouse_product['give_out'];    
            $document_num=$split_warehouse_product['document_num'];   
            $document_closed=$split_warehouse_product['document_closed'];
            //$save=$split_warehouse_product['save'];
            $invoice_key_id = $split_warehouse_product['invoice_key_id'];
            
            $warehouse_address_list = receiveOneWarehouseAddress($con, $inWarehouse_id);
            $inWarehouse_info_id=$warehouse_address_list['warehouse_info_id'];
            $inCity=$warehouse_address_list['city'];
            $inStreet=$warehouse_address_list['street'];
            $inHouse=$warehouse_address_list['house'];
            $inBuilding=$warehouse_address_list['building'];           
          
            $product_info_list = receiveProductInfo($con,$product_inventory_id);
            $category = $product_info_list['category'];
            $brand = $product_info_list['brand'];
            $characteristic = $product_info_list['characteristic'];
            $type_packaging = $product_info_list['type_packaging'];
            $unit_measure = $product_info_list['unit_measure'];
            $weight_volume = $product_info_list['weight_volume'];
            $quantity_package = $product_info_list['quantity_package'];
            $image_url = $product_info_list['image_url'];       
            
                
            echo $inWarehouse_id."&nbsp".$inCity."&nbsp".$inStreet."&nbsp".$inHouse."&nbsp".$inBuilding."&nbsp".
                 $warehouse_inventory_id."&nbsp". $category ."&nbsp". $brand ."&nbsp". $characteristic ."&nbsp".
                 $unit_measure ."&nbsp". $weight_volume ."&nbsp".$image_url."&nbsp".
                 $quantity."&nbsp".$type_packaging."&nbsp".$quantity_package."&nbsp".
                 $give_out."&nbsp".$inWarehouse_info_id."&nbsp"
                 .$document_num."&nbsp". $document_closed."&nbsp".$invoice_key_id."<br>";                   
        } 
        }else{
            echo "messege"."&nbsp".$GLOBALS['delivery_is_not'];
        }        
    
    }
    //получить все склады и собранные товары warhouse_inventory_id дла погрузки и доставки
    function receive_list_warehouse_and_product_for_delivery_01($con, $car_id){
        //получить все заказы приписанные этому авто
        //получить номер товарной накладной
        $query="SELECT win.warehouse_inventory_id,
                        win.product_inventory_id,
                        win.quantity,
                        win.out_warehouse_id,
                        win.in_warehouse_id,
                        lp.take_in,
                        lp.give_out,
                        win.out_active,
                        dd.document_num,
                        ik.closed,
                        ik.save,
                        ii.invoice_key_id
        
                     FROM t_logistic_product lp
                        JOIN t_warehouse_inventory_in_out win ON win.warehouse_inventory_id = lp.warehouse_inventory_id 
                                                                and in_active = '0'
                        JOIN t_invoice_info ii ON ii.warehouse_inventory_id = win.warehouse_inventory_id
                        JOIN t_document_deal dd ON dd.invoice_key_id = ii.invoice_key_id 
                                                    and dd.document_name='товарная накладная'
                        JOIN t_invoice_key ik ON ik.invoice_key_id = ii.invoice_key_id
                     WHERE lp.car_id ='$car_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_inventory_id=$row[0];
                $product_inventory_id=$row[1];
                $quantity=$row[2];
                $outWarehouse_id=$row[3];
                $inWarehouse_id=$row[4];  
                $take_in=$row[5];
                $give_out=$row[6];   
                $out_active=$row[7];   
                $document_num = $row[8];   
                $document_closed = $row[9];    
                $save = $row[10]; 
                $invoice_key_id = $row[11];

                //получить номер транспортной накладной
                $query="";
                
                //собрать массив склад-товар
                $warehouse_product_list[] = ['warehouse_inventory_id' => $warehouse_inventory_id,
                                            'product_inventory_id'=>$product_inventory_id, 
                                             'quantity'=>$quantity, 
                                             'outWarehouse_id'=>$outWarehouse_id,
                                             'inWarehouse_id'=>$inWarehouse_id,
                                             'take_in'=>$take_in,
                                             'give_out'=>$give_out,
                                             'out_active'=>$out_active,
                                             'document_num'=>$document_num,
                                             'document_closed'=>$document_closed,
                                             'save'=>$save, 'invoice_key_id'=>$invoice_key_id]; 
            }       
        
        //получить адреса складов и передать данные
        foreach($warehouse_product_list as $key => $split_warehouse_product){

            $warehouse_inventory_id = $split_warehouse_product['warehouse_inventory_id'];
            $product_inventory_id = $split_warehouse_product['product_inventory_id'];
            $outWarehouse_id = $split_warehouse_product['outWarehouse_id'];
            $inWarehouse_id = $split_warehouse_product['inWarehouse_id'];
            $quantity = $split_warehouse_product['quantity'];   
            $take_in=$split_warehouse_product['take_in'];
            $give_out=$split_warehouse_product['give_out'];    
            $out_active=$split_warehouse_product['out_active'];  
            $document_num=$split_warehouse_product['document_num'];   
            $document_closed=$split_warehouse_product['document_closed'];
            $save=$split_warehouse_product['save'];
            $invoice_key_id = $split_warehouse_product['invoice_key_id'];
            
            $out_warehouse_address_list = receiveOneWarehouseAddress($con, $outWarehouse_id);
            //$warehouse_address_list = receiveWarehouseAddress($con, $outWarehouse_id, $inWarehouse_id);
            $outWarehouse_info_id=$out_warehouse_address_list['warehouse_info_id'];
            $outCity=$out_warehouse_address_list['city'];
            $outStreet=$out_warehouse_address_list['street'];
            $outHouse=$out_warehouse_address_list['house'];
            $outBuilding=$out_warehouse_address_list['building'];
            
            $warehouse_address_list = receiveOneWarehouseAddress($con, $inWarehouse_id);
            $inWarehouse_info_id=$warehouse_address_list['warehouse_info_id'];
            $inCity=$warehouse_address_list['city'];
            $inStreet=$warehouse_address_list['street'];
            $inHouse=$warehouse_address_list['house'];
            $inBuilding=$warehouse_address_list['building'];           
          
           /* $product_info_list = receiveProductInfo($con,$product_inventory_id);
            $category = $product_info_list['category'];
            $brand = $product_info_list['brand'];
            $characteristic = $product_info_list['characteristic'];
            $type_packaging = $product_info_list['type_packaging'];
            $unit_measure = $product_info_list['unit_measure'];
            $weight_volume = $product_info_list['weight_volume'];
            $quantity_package = $product_info_list['quantity_package'];
            $image_url = $product_info_list['image_url'];     */
            
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
             $description=$product_list['description'];
 
             
            echo $outWarehouse_id."&nbsp".$outCity."&nbsp".$outStreet."&nbsp".$outHouse."&nbsp".$outBuilding."&nbsp".
                 $inWarehouse_id."&nbsp".$inCity."&nbsp".$inStreet."&nbsp".$inHouse."&nbsp".$inBuilding."&nbsp".
                 $warehouse_inventory_id."&nbsp". $category ."&nbsp". $brand ."&nbsp". $characteristic ."&nbsp".
                 $unit_measure ."&nbsp". $weight_volume ."&nbsp".$image_url."&nbsp".
                 $quantity."&nbsp".$type_packaging."&nbsp".$quantity_package."&nbsp".$take_in."&nbsp".
                 $give_out."&nbsp".$out_active."&nbsp".$outWarehouse_info_id."&nbsp".$inWarehouse_info_id."&nbsp"
                 .$document_num."&nbsp". $document_closed."&nbsp".$save."&nbsp".$invoice_key_id."&nbsp"
                 .$product_name."&nbsp".$description."<br>";               
        } 
        }else{
            echo "messege"."&nbsp".$GLOBALS['delivery_is_not'];
        }        
    
    }
    /*
       //получить все склады и собранные товары warhouse_inventory_id дла погрузки и доставки
    function receive_list_warehouse_and_product_for_delivery($con, $car_id){
        //получить все заказы приписанные этому авто
        $query="SELECT win.warehouse_inventory_id,
                        win.product_inventory_id,
                        win.quantity,
                        win.out_warehouse_id,
                        win.in_warehouse_id,
                        lp.take_in,
                        lp.give_out,
                        win.out_active
        
                     FROM t_logistic_product lp
                        JOIN t_warehouse_inventory_in_out win ON win.warehouse_inventory_id = lp.warehouse_inventory_id 
                                                                and in_active = '0'
                     WHERE lp.car_id ='$car_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $warehouse_inventory_id=$row[0];
                $product_inventory_id=$row[1];
                $quantity=$row[2];
                $outWarehouse_id=$row[3];
                $inWarehouse_id=$row[4];  
                $take_in=$row[5];
                $give_out=$row[6];   
                $out_active=$row[7];          
                
                //собрать массив склад-товар
                $warehouse_product_list[] = ['warehouse_inventory_id' => $warehouse_inventory_id,
                                            'product_inventory_id'=>$product_inventory_id, 
                                             'quantity'=>$quantity, 
                                             'outWarehouse_id'=>$outWarehouse_id,
                                             'inWarehouse_id'=>$inWarehouse_id,
                                             'take_in'=>$take_in,
                                             'give_out'=>$give_out,
                                             'out_active'=>$out_active]; 
            }       
        
        //получить адреса складов и передать данные
        foreach($warehouse_product_list as $key => $split_warehouse_product){

            $warehouse_inventory_id = $split_warehouse_product['warehouse_inventory_id'];
            $product_inventory_id = $split_warehouse_product['product_inventory_id'];
            $outWarehouse_id = $split_warehouse_product['outWarehouse_id'];
            $inWarehouse_id = $split_warehouse_product['inWarehouse_id'];
            $quantity = $split_warehouse_product['quantity'];   
            $take_in=$split_warehouse_product['take_in'];
            $give_out=$split_warehouse_product['give_out'];    
            $out_active=$split_warehouse_product['out_active'];     
            
            $out_warehouse_address_list = receiveOneWarehouseAddress($con, $outWarehouse_id);
            //$warehouse_address_list = receiveWarehouseAddress($con, $outWarehouse_id, $inWarehouse_id);
            $outWarehouse_info_id=$out_warehouse_address_list['warehouse_info_id'];
            $outCity=$out_warehouse_address_list['city'];
            $outStreet=$out_warehouse_address_list['street'];
            $outHouse=$out_warehouse_address_list['house'];
            $outBuilding=$out_warehouse_address_list['building'];
            
            $warehouse_address_list = receiveOneWarehouseAddress($con, $inWarehouse_id);
            $inWarehouse_info_id=$warehouse_address_list['warehouse_info_id'];
            $inCity=$warehouse_address_list['city'];
            $inStreet=$warehouse_address_list['street'];
            $inHouse=$warehouse_address_list['house'];
            $inBuilding=$warehouse_address_list['building'];           
          
            $product_info_list = receiveProductInfo($con,$product_inventory_id);
            $category = $product_info_list['category'];
            $brand = $product_info_list['brand'];
            $characteristic = $product_info_list['characteristic'];
            $type_packaging = $product_info_list['type_packaging'];
            $unit_measure = $product_info_list['unit_measure'];
            $weight_volume = $product_info_list['weight_volume'];
            $quantity_package = $product_info_list['quantity_package'];
            $image_url = $product_info_list['image_url'];          
            
            echo $outWarehouse_id."&nbsp".$outCity."&nbsp".$outStreet."&nbsp".$outHouse."&nbsp".$outBuilding."&nbsp".
                 $inWarehouse_id."&nbsp".$inCity."&nbsp".$inStreet."&nbsp".$inHouse."&nbsp".$inBuilding."&nbsp".
                 $warehouse_inventory_id."&nbsp". $category ."&nbsp". $brand ."&nbsp". $characteristic ."&nbsp".
                 $unit_measure ."&nbsp". $weight_volume ."&nbsp".$image_url."&nbsp".
                 $quantity."&nbsp".$type_packaging."&nbsp".$quantity_package."&nbsp".$take_in."&nbsp".
                 $give_out."&nbsp".$out_active."&nbsp".$outWarehouse_info_id."&nbsp".$inWarehouse_info_id."<br>";               
        } 
        }else{
            echo "messege"."&nbsp".$GLOBALS['delivery_is_not'];
        }        
    
    }
    */
    //получить список автомобилей user
    function  receive_list_cars_user($con, $counterparty_id){
        $query="SELECT `car_id`, `car_brand`, `car_model`, `registration_num`
                    FROM `t_car` WHERE `owner_id` = '$counterparty_id' and `active`='1'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));  
        if(mysqli_num_rows($result) > 0){      
            while($row=mysqli_fetch_array($result)){
                $car_id=$row[0];
                $car_brand=$row[1];
                $car_model=$row[2];
                $registration_num=$row[3];

                echo $car_id."&nbsp".$car_brand."&nbsp".$car_model."&nbsp".$registration_num."<br>";
            }
        }else{
            echo "messege" ."&nbsp". $GLOBALS['cars_is_not'];
        }
    }
   //удалить все исправленные на check=0; брони товаров из БД
    function update_and_delete_logistic_product($con, $warehouseInventory_id){
        $query="SELECT `warehouse_inventory_id` FROM `t_warehouse_inventory_in_out` 
                         WHERE `warehouse_inventory_id`='$warehouseInventory_id'
                          and `car_id` != '0' AND `car_for_logistic`='1'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $query="UPDATE `t_warehouse_inventory_in_out` SET `car_for_logistic`= '0', `car_id` = '0'
                        WHERE `warehouse_inventory_id`='$warehouseInventory_id'";//`car_for_logistic`= null
            $res=mysqli_query($con, $query) or die(mysqli_error($con));

            $query="DELETE FROM `t_logistic_product` WHERE  `warehouse_inventory_id`='$warehouseInventory_id'";
            $res=mysqli_query($con, $query) or die(mysqli_error($con));
        }
    }
    //получить список товаров для транспорта которые добавлены в доставку t_logistic_product
    function receive_list_delivery_for_car($con, $car_id){
        $query="SELECT  win.product_inventory_id, 
                        win.quantity,
                        win.out_warehouse_id,
                        win.in_warehouse_id,
                        p.weight_volume,
                        p.storage_conditions, 
                        win.warehouse_inventory_id         
                    FROM t_logistic_product lp
                        JOIN t_warehouse_inventory_in_out win ON win.warehouse_inventory_id = lp.warehouse_inventory_id
                        JOIN t_product_inventory pi ON pi.product_inventory_id = win.product_inventory_id
                        JOIN t_product p         ON p.product_id = pi.product_id
                    WHERE lp.car_id = '$car_id' AND lp.take_in = '0'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            while($row=mysqli_fetch_array($result)){
                $product_inventory_id=$row[0];
                $quantity=$row[1];
                $outWarehouse_id=$row[2];
                $inWarehouse_id=$row[3];
                $productWeight=$row[4];
                $storageTemperature=$row[5];
                $warehouse_inventory_id=$row[6];

                $warehouse_address_list = receiveWarehouseAddress($con, $outWarehouse_id, $inWarehouse_id);
                $outCity=$warehouse_address_list['outCity'];
                $outStreet=$warehouse_address_list['outStreet'];
                $outHouse=$warehouse_address_list['outHouse'];
                $outBuilding=$warehouse_address_list['outBuilding'];
                
                $inCity=$warehouse_address_list['inCity'];
                $inStreet=$warehouse_address_list['inStreet'];
                $inHouse=$warehouse_address_list['inHouse'];
                $inBuilding=$warehouse_address_list['inBuilding'];

                
                echo $outWarehouse_id."&nbsp".$outCity."&nbsp".$outStreet."&nbsp".$outHouse."&nbsp".$outBuilding."&nbsp".
                     $inWarehouse_id."&nbsp".$inCity."&nbsp".$inStreet."&nbsp".$inHouse."&nbsp".
                     $inBuilding."&nbsp".$quantity."&nbsp".$productWeight."&nbsp".$storageTemperature.
                     "&nbsp".$warehouse_inventory_id."<br>"; 

                //собрать массив склад-товар
               /* $warehouse_product_list[] = ['warehouse_inventory_id' => $warehouse_inventory_id, 
                        'quantity'=>$quantity, 'outWarehouse_id'=>$outWarehouse_id,
                        'inWarehouse_id'=>$inWarehouse_id, 'productWeight'=>$productWeight,
                        'storageTemperature'=>$storageTemperature]; */
                         //'product_inventory_id' => $product_inventory_id,
            }         
             //получить адреса складов и передать данные
            /* foreach($warehouse_product_list as $key => $split_warehouse_product){

                $warehouse_inventory_id = $split_warehouse_product['warehouse_inventory_id'];

                $outWarehouse_id = $split_warehouse_product['outWarehouse_id'];
                $inWarehouse_id = $split_warehouse_product['inWarehouse_id'];
                $quantity = $split_warehouse_product['quantity'];               
                $productWeight = $split_warehouse_product['productWeight'];
                $storageTemperature = $split_warehouse_product['storageTemperature'];

                $warehouse_address_list = receiveWarehouseAddress($con, $outWarehouse_id, $inWarehouse_id);
                $outCity=$warehouse_address_list['outCity'];
                $outStreet=$warehouse_address_list['outStreet'];
                $outHouse=$warehouse_address_list['outHouse'];
                $outBuilding=$warehouse_address_list['outBuilding'];
                
                $inCity=$warehouse_address_list['inCity'];
                $inStreet=$warehouse_address_list['inStreet'];
                $inHouse=$warehouse_address_list['inHouse'];
                $inBuilding=$warehouse_address_list['inBuilding'];

                
                echo $outWarehouse_id."&nbsp".$outCity."&nbsp".$outStreet."&nbsp".$outHouse."&nbsp".$outBuilding."&nbsp".
                     $inWarehouse_id."&nbsp".$inCity."&nbsp".$inStreet."&nbsp".$inHouse."&nbsp".
                     $inBuilding."&nbsp".$quantity."&nbsp".$productWeight."&nbsp".$storageTemperature.
                     "&nbsp".$warehouse_inventory_id."<br>";               
            }*/             
                       

        }else{
            echo "messege"."&nbsp". $GLOBALS['delivery_is_not'] . "<br>";
        }
    }
    //записать товар и авто в t_logistic_product
    function record_goods_and_car_for_delivery($con,  $warehouse_inventory_id, $user_id, $car_id){
        $query="SELECT `logistic_prod_id` FROM `t_logistic_product` WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) == 0){           
        
            $query="INSERT INTO `t_logistic_product`
                                (`warehouse_inventory_id`, `user_id`, `car_id`) 
                        VALUES ('$warehouse_inventory_id','$user_id','$car_id')";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));

            $query="UPDATE `t_warehouse_inventory_in_out` 
                    SET `car_for_logistic`='1', `car_id`='$car_id' WHERE `warehouse_inventory_id`=$warehouse_inventory_id";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));
      }
    }
    //получить список всех авто для доставки
    function receive_list_cars($con){
        $query="SELECT `car_id`, `car_brand`, `car_model`, `registration_num`, `max_cargo_weght` 
                    FROM `t_car` WHERE `car_id` > '0' and `active`='1'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));        
        while($row=mysqli_fetch_array($result)){
            $car_id=$row[0];
            $car_brand=$row[1];
            $car_model=$row[2];
            $registration_num=$row[3];
            $max_cargo_weght=$row[4];

            echo $car_id."&nbsp".$car_brand."&nbsp".$car_model."&nbsp".$registration_num."&nbsp".$max_cargo_weght."<br>";
        }
    }
    //получит список доставок товаров
    function receive_list_delivery_01($con){
        $warehouse_product_list = array();
        //получить список доставок c товарами из warehouse_inventory         
        //получить вес-товара и условия-хранения
        $query="SELECT   win.product_inventory_id, 
                            win.quantity,
                            win.out_warehouse_id,
                            win.in_warehouse_id,
                            p.weight_volume,
                            p.storage_conditions, 
                            win.warehouse_inventory_id
                    FROM t_warehouse_inventory_in_out win                         
                        JOIN t_product_inventory pi ON pi.product_inventory_id = win.product_inventory_id
                        JOIN t_product p         ON p.product_id = pi.product_id
                    WHERE win.logistic_product='1' AND car_for_logistic ='0' 
                                                    AND win.collected='1' 
                                                    AND win.out_active = '0' and win.in_warehouse_id IS NOT NULL";       
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            while($row=mysqli_fetch_array($result)){
                $product_inventory_id=$row[0];
                $quantity=$row[1];
                $outWarehouse_id=$row[2];
                $inWarehouse_id=$row[3];
                $productWeight=$row[4];
                $storageTemperature=$row[5];
                $warehouse_inventory_id=$row[6];

                //собрать массив склад-товар
                $warehouse_product_list[] = ['warehouse_inventory_id' => $warehouse_inventory_id, 
                        'quantity'=>$quantity, 'outWarehouse_id'=>$outWarehouse_id,
                        'inWarehouse_id'=>$inWarehouse_id, 'productWeight'=>$productWeight,
                        'storageTemperature'=>$storageTemperature]; 
                         //'product_inventory_id' => $product_inventory_id,
            }         
             //получить адреса складов и передать данные
             foreach($warehouse_product_list as $key => $split_warehouse_product){

                $warehouse_inventory_id = $split_warehouse_product['warehouse_inventory_id'];

                $outWarehouse_id = $split_warehouse_product['outWarehouse_id'];
                $inWarehouse_id = $split_warehouse_product['inWarehouse_id'];
                $quantity = $split_warehouse_product['quantity'];               
                $productWeight = $split_warehouse_product['productWeight'];
                $storageTemperature = $split_warehouse_product['storageTemperature'];

                $out_warehouse_address_list = receiveOneWarehouseAddress($con, $outWarehouse_id);
                //$warehouse_address_list = receiveWarehouseAddress($con, $outWarehouse_id, $inWarehouse_id);
                $outWarehouse_info_id=$out_warehouse_address_list['warehouse_info_id'];
                $outCity=$out_warehouse_address_list['city'];
                $outStreet=$out_warehouse_address_list['street'];
                $outHouse=$out_warehouse_address_list['house'];
                $outBuilding=$out_warehouse_address_list['building'];
                
                $warehouse_address_list = receiveOneWarehouseAddress($con, $inWarehouse_id);
                $inWarehouse_info_id=$warehouse_address_list['warehouse_info_id'];
                $inCity=$warehouse_address_list['city'];
                $inStreet=$warehouse_address_list['street'];
                $inHouse=$warehouse_address_list['house'];
                $inBuilding=$warehouse_address_list['building'];
                //['warehouse_info_id'=>$warehouse_info_id,'city'=>$city,
               // 'street'=>$street,'house'=>$house,'building'=>$building];

                
                echo $outWarehouse_id."&nbsp".$outCity."&nbsp".$outStreet."&nbsp".$outHouse."&nbsp".$outBuilding."&nbsp".
                     $inWarehouse_id."&nbsp".$inCity."&nbsp".$inStreet."&nbsp".$inHouse."&nbsp".
                     $inBuilding."&nbsp".$quantity."&nbsp".$productWeight."&nbsp".$storageTemperature.
                     "&nbsp".$warehouse_inventory_id."&nbsp".$outWarehouse_info_id."&nbsp".$inWarehouse_info_id."<br>";               
            }    
        }else{
            echo "messege"."&nbsp". $GLOBALS['delivery_is_not'] . "<br>";
        }       

    }
    /*
    //получит список доставок товаров
    function receive_list_delivery($con){
        //получить список доставок c товарами из warehouse_inventory         
        //получить вес-товара и условия-хранения
        $query="SELECT   win.product_inventory_id, 
                            win.quantity,
                            win.out_warehouse_id,
                            win.in_warehouse_id,
                            p.weight_volume,
                            p.storage_conditions 
                    FROM t_warehouse_inventory_in_out win                         
                        JOIN t_product_inventory pi ON pi.product_inventory_id = win.product_inventory_id
                        JOIN t_product p         ON p.product_id = pi.product_id
                    WHERE win.logistic_product='1' AND win.collected='1' AND win.out_active IS NULL";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            while($row=mysqli_fetch_array($result)){
                $product_inventory_id=$row[0];
                $quantity=$row[1];
                $outWarehouse_id=$row[2];
                $inWarehouse_id=$row[3];
                $productWeight=$row[4];
                $storageTemperature=$row[5];

                //собрать массив склад-товар
                $warehouse_product_list[] = ['product_inventory_id' => $product_inventory_id,'quantity'=>$quantity,
                        'outWarehouse_id'=>$outWarehouse_id,'inWarehouse_id'=>$inWarehouse_id,
                        'productWeight'=>$productWeight,'storageTemperature'=>$storageTemperature]; 
                
            }                      
            //разобрать массив по склад-склад, условия-хранения
            $count=0;  $step_flag = true;
            foreach($warehouse_product_list as $k => $warehouse_product){
                $outWarehouse_id = $warehouse_product['outWarehouse_id'];
                $inWarehouse_id = $warehouse_product['inWarehouse_id'];
                $storageTemperature = $warehouse_product['storageTemperature'];
                
                if($count ==0) {
                    $split_warehouse_product_list[] = $warehouse_product_list[$k];
                    $count++;
                }else{
                    //сложить колличество отсортированных товаров (удалив лишние строки с одинаковыми складами) 
                    //в одну строку и передать 
                    foreach( $split_warehouse_product_list as $key => $v){
                        if(     $outWarehouse_id == $v['outWarehouse_id'] 
                            AND $inWarehouse_id == $v['inWarehouse_id']
                            AND $storageTemperature == $v['storageTemperature']){

                                $quantity = $v['quantity'] + $warehouse_product['quantity'];
                                $split_warehouse_product_list[$key]['quantity'] = $quantity;
                                $step_flag=false;
                        }
                    }
                    if($step_flag){
                        $split_warehouse_product_list[] = $warehouse_product_list[$k];
                        
                    }                   
                }
                $step_flag = true;               
            }            

            //получить адреса складов и передать данные
            foreach($split_warehouse_product_list as $key => $split_warehouse_product){

                $outWarehouse_id = $split_warehouse_product['outWarehouse_id'];
                $inWarehouse_id = $split_warehouse_product['inWarehouse_id'];
                $quantity = $split_warehouse_product['quantity'];               
                $productWeight = $split_warehouse_product['productWeight'];
                $storageTemperature = $split_warehouse_product['storageTemperature'];

                $warehouse_address_list = receiveWarehouseAddress($con, $outWarehouse_id, $inWarehouse_id);
                $outCity=$warehouse_address_list['outCity'];
                $outStreet=$warehouse_address_list['outStreet'];
                $outHouse=$warehouse_address_list['outHouse'];
                $outBuilding=$warehouse_address_list['outBuilding'];
                
                $inCity=$warehouse_address_list['inCity'];
                $inStreet=$warehouse_address_list['inStreet'];
                $inHouse=$warehouse_address_list['inHouse'];
                $inBuilding=$warehouse_address_list['inBuilding'];

                echo $outWarehouse_id."&nbsp".$outCity."&nbsp".$outStreet."&nbsp".$outHouse."&nbsp".$outBuilding."&nbsp".
                $inWarehouse_id."&nbsp".$inCity."&nbsp".$inStreet."&nbsp".$inHouse."&nbsp".
                $inBuilding."&nbsp".$quantity."&nbsp".$productWeight."&nbsp".$storageTemperature."<br>";

                /*foreach($split_warehouse_product as $k => $v){
                  
                    echo "$k : $v => ";
                }
                echo "<br>";*/
            /*}
            

        }else{
            echo "messege"."&nbsp". $GLOBALS['delivery_is_not'] . "<br>";
        }
        /*
                $outCity=$row[];
                $outStreet=$row[];
                $outHouse=$row[];
                $outBuilding=$row[];
                
                $inCity=$row[];
                $inStreet=$row[];
                $inHouse=$row[];
                $inBuilding=$row[];
                $productVolume=$row[];
                $productWeight=$row[];
                $storageTemperature=$row[];
                $checked=$row[];
        

   // }
    */
    //получить данные товара
    function receiveProductInfo($con,$product_inventory_id){
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
    }
    /*
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
        //echo "count: " . mysqli_num_rows($result) . "<br>";
        //while($row = mysqli_fetch_array($result)){    
        while($row = mysqli_fetch_array($result)){        
            $product_id=$row[0]; $product_inventory_id=$row[1]; $category=$row[2]; $brand=$row[3];
             $characteristic=$row[4]; 
            $type_packaging=$row[5]; $unit_measure=$row[6]; $weight_volume=$row[7]; $price=$row[8];
            $quantity_package= $row[9]; $image_url=$row[10]; $description=$row[11];
    */
    //получить адреса складов
    function receiveOneWarehouseAddress($con, $warehouse_id){   
        $query="SELECT wi.warehouse_info_id,
                    wi.city,
                    wi.street,
                    wi.house,
                    wi.building,
                    w.warehouse_id
                FROM  t_warehous w
                    JOIN t_warehouse_info wi ON  wi.warehouse_info_id=w.warehouse_info_id                                                                  
                WHERE w.warehouse_id ='$warehouse_id'";
       /* $query="SELECT `city`, `street`, `house`, `building`FROM `t_warehouse` 
                                    WHERE `warehouse_id`='$outWarehouse_id'";*/
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
                $warehouse_info_id=$row[0]; 
                $city=$row[1];
                $street=$row[2];
                $house=$row[3];
                $building=$row[4];

        $warehouse_address_list = 
                ['warehouse_info_id'=>$warehouse_info_id,'city'=>$city,
                'street'=>$street,'house'=>$house,'building'=>$building];

       /* $query="SELECT `city`, `street`, `house`, `building`FROM `t_warehouse` 
                                        WHERE `warehouse_id`='$inWarehouse_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
                
                $inCity=$row[0];
                $inStreet=$row[1];
                $inHouse=$row[2];
                $inBuilding=$row[3];

        $warehouse_address_list = 
                ['outCity'=>$outCity,'outStreet'=>$outStreet,'outHouse'=>$outHouse,'outBuilding'=>$outBuilding,
                 'inCity'=>$inCity,  'inStreet'=>$inStreet,  'inHouse'=>$inHouse,  'inBuilding'=>$inBuilding];*/
           // echo "test 3: " . $warehouse_address_list['outStreet'] . "<br>";
        return $warehouse_address_list;
    }
    //получить адреса складов
    function receiveWarehouseAddress($con, $outWarehouse_id, $inWarehouse_id){   
              
        $query="SELECT `city`, `street`, `house`, `building`FROM `t_warehouse` 
                                    WHERE `warehouse_id`='$outWarehouse_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
                $outCity=$row[0];
                $outStreet=$row[1];
                $outHouse=$row[2];
                $outBuilding=$row[3];

        $query="SELECT `city`, `street`, `house`, `building`FROM `t_warehouse` 
                                        WHERE `warehouse_id`='$inWarehouse_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
                
                $inCity=$row[0];
                $inStreet=$row[1];
                $inHouse=$row[2];
                $inBuilding=$row[3];

        $warehouse_address_list = 
                ['outCity'=>$outCity,'outStreet'=>$outStreet,'outHouse'=>$outHouse,'outBuilding'=>$outBuilding,
                 'inCity'=>$inCity,  'inStreet'=>$inStreet,  'inHouse'=>$inHouse,  'inBuilding'=>$inBuilding];
           // echo "test 3: " . $warehouse_address_list['outStreet'] . "<br>";
        return $warehouse_address_list;
    }
    //найти user_id
  /*  function checkUserID($con, $user_uid){ 
        $query="SELECT user_id FROM t_user WHERE unique_id = '$user_uid'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $user_id = $row[0];
        //echo "user_id " . $user_id . "<br>";
        return $user_id;
        }*/
     //найти counterparty_id
     function receiveCounterpartyID($con,$counterparty_tax_id){
        $query = "SELECT `counterparty_id` FROM `t_counterparty` WHERE `taxpayer_id_number`= '$counterparty_tax_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $counterparty_id = $row[0];
        return $counterparty_id;
    }   

    mysqli_close($con);
?>
