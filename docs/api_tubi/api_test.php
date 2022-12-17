<?php
	 include 'connect.php';
     include 'text.php';
	 include_once 'helper_classes.php';
     include 'variable.php';


    //delete_order_product
    //add_delivery_to_table
    //add_order_product
    //add_my_order
    //show_product_and_quantity
    //show_product_price_all_provider
    //search_product_by_text
    //receive_delivery_address
	 
	 
   mysqli_query($con,"SET NAMES 'utf8'");
   
   if(isset($_GET['check_category'])){       //(isset($_GET['category_id']))
   
       checkInputProductID($con);
    	
        	for($i=0; $i < count($ip_id_arr); $i++){
        	    
        	    $row = checkProduct($con,$ip_id_arr[$i]);
        	    
        	    $row = checkCatalog($con,$row);
        	    
        	           checkCategory($con,$row);
            	                                                
        	    echo "<br>";
        	}
	printNotCategory($category_not_arr);   // автозапуск, вывести на дисплей категории которых нет/
	
   }else if(isset($_GET['write_category'])){
       
       $category = $_GET['category'];
	    $catalog_id = $_GET['catalog_id'];
       checkWriteCategory($con, $category, $catalog_id);
       
   } else if(isset($_GET['write_product'])){    //внести продукт в таблицу t_product
       checkInputProductID($con);
       
        for($i=0;$i<count($ip_id_arr);$i++){//count($ip_id_arr)
            
            $temp = $ip_id_arr[$i];
            checkAndWriteProduct($con, $temp);//проверить данные по таблицам при отсутствии добавить
            
            goProductInTable($con, $temp);    //записать товар в таблицу базы для использования
        }         
    }
    else if(isset($_GET['search_my_open_order'])){     //<delete> найти есть ли начатый заказ
        $user_uid = $_GET['user_uid'];       
                
        $order_id = checkMyEmptyOrder($con, $user_uid);      // получить начатый order_id заказа
        if($order_id){
            echo $order_id;
        }else {echo 0;}
    
    }else if(isset($_GET['search_open_order'])){     //<delete> найти есть ли начатый заказ
        $counterparty_id = $_GET['counterparty_id'];
        
        //$buyer_id =  takeBuyer($con, $counterparty_id);  //получить buyer_id контрагента
        
        $order_id = checkEmptyOrder($con, $counterparty_id);      // получить начатый order_id заказа
        if($order_id){
            echo $order_id;
        }else {echo 0;}
    
    }else if (isset($_GET['add_my_order'] )){             //создать новый заказ/
        $user_uid = $_GET['user_uid']; 
        $company_tax_id = $_GET['company_tax_id']; 
        $warehouse_id = $_GET['warehouse_id'];  
        $dateOfSaleMillis = $_GET['dateOfSaleMillis']; 
        $category = $_GET['category'];   
        $delivery = $_GET['delivery'];         
        
        $user_id = checkUserID($con, $user_uid);//найти user_id
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $company_tax_id);

        //если начатого order_id нет, то создать новый заказ
        addMyOrder001($con, $user_id, $counterparty_id, $warehouse_id, $dateOfSaleMillis, $category, $delivery);      
        //$order_id = addMyOrder($con, $user_id, $counterparty_id, $warehouse_id);               
           

        //echo   $order_id;       
  
   } 
    //сложить товар в заказ проверить остатки 
    else if(isset($_GET['add_order_product'])){        
       $order_id = $_GET['order_id'];
       $product_inventory_id = $_GET['product_inventory_id'];
       $provider_warehouse_id = $_GET['provider_warehouse_id'];
       $quantity = $_GET['quantity'];//process_price
       $process_price = $_GET['process_price'];
       
       addOrderProduct001($con, $order_id, $product_inventory_id, $quantity, $process_price, $provider_warehouse_id);
       
   } //удалить товар из заказа 
   else if(isset($_GET['delete_order_product'])){        
       $order_product_id = $_GET['order_product_id'];
       
       $order_id = deleteOrderProduct($con, $order_product_id);
                                                            //проверить если в order_id нет ни одного товара то удалить order_id
        deleteOrder($con, $order_id);                                                    
       
   }else if(isset($_GET['show_product_price_all_provider'])){   //показать цену на товар от всех поставщиков
       $product_id = $_GET['product_id'];
       $order_id = $_GET['order_id'];
       $city_id = $_GET['city_id'];
       $my_city = $_GET['my_city']; 
       $my_region = $_GET['my_region'];
       $delivery = $_GET['delivery'];
       
       showProductPriceAllProvider002($con, $product_id, $order_id,$city_id, $my_city, $my_region, $delivery);
       
   }
   else if(isset($_GET['search_product_by_text'])){   //показать цену на товар от всех поставщиков
        $text_for_search = $_GET['text_for_search'];
        $order_id = $_GET['order_id'];
        $city_id = $_GET['city_id'];
        $my_city = $_GET['my_city']; 
        $my_region = $_GET['my_region'];
        $delivery = $_GET['delivery'];
        
        search_product_by_text($con, $text_for_search, $order_id,$city_id, $my_city, $my_region, $delivery);
        
    }
   
   else if(isset($_GET['quantity_product_from_order'])){  // показать количество одного товара в заказе
       $order_id=$_GET['order_id'];
       $product_id=$_GET['product_id'];
       
       quantityProductFromOrder($con, $order_id, $product_id);
       
   }//показать список продуктов и колличество в заказе
    else if (isset($_GET['show_product_and_quantity'])){        
       $category = $_GET['category'];
       $order_id = $_GET['order_id'];
       $city_id = $_GET['city_id'];   
       $my_city = $_GET['my_city'];   
       $my_region = $_GET['my_region'];
       $delivery = $_GET['delivery'];
              
        //показать список продуктов и колличество в заказе с учетом москвы
        showProductAndQuantity006($con, $category, $order_id, $city_id, $my_city, $my_region, $delivery);

   }
    //добавить доставку заказа в таблицу БД
    else if (isset($_GET['add_delivery_to_table'])){        
        $order_id = $_GET['order_id'];
        $city_id = $_GET['city_id'];   
        $addressForDelivery = $_GET['addressForDelivery'];   
       
            
        //добавить доставку заказа в таблицу БД
        add_delivery_to_table($con, $order_id, $addressForDelivery);

    }
    //получить адресс доставки
    else if (isset($_GET['receive_delivery_address'])){        
        $order_id = $_GET['order_id'];  
       
            
        //получить адресс доставки
        receive_delivery_address($con, $order_id);

    }
    //получить адресс доставки
    function receive_delivery_address($con, $order_id){
        $query="SELECT `address_for_delivery` FROM `t_order_for_delivery_to_buyer` 
                                                    WHERE `order_id` = '$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $address_for_delivery= $row[0];

        echo $address_for_delivery;
    }

    //добавить доставку заказа в таблицу БД
    function add_delivery_to_table($con, $order_id, $addressForDelivery){
        $query="INSERT INTO `t_order_for_delivery_to_buyer`( `order_id`, `address_for_delivery`) 
                                                    VALUES ('$order_id','$addressForDelivery')";
        mysqli_query($con, $query) or die (mysqli_error($con));
    }

   function deleteOrder($con, $order_id){
       $query = "SELECT order_product_id FROM t_order_product WHERE order_id = $order_id";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       if($row = mysqli_fetch_array($result)){
                                                    //------если есть в заказе товары
           //$row = mysqli_fetch_array($result);
           //echo 'result1: ' . $row[0] . "<br>";
       }else {
                                                //-----в заказе неосталось товаров удаляем заказ order_id
            $query = "DELETE FROM t_order WHERE order_id = $order_id";
            $result = mysqli_query($con, $query) or die (mysql_error($link));
            
           echo "order_id=0";
        }
   }
   function deleteOrderProduct($con, $order_product_id){
       $query = "SELECT order_id FROM t_order_product WHERE order_product_id = $order_product_id";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $row = mysqli_fetch_array($result);
       $order_id = $row[0];
       
       $query= "DELETE FROM t_order_product WHERE order_product_id = $order_product_id";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       
       return $order_id;
   }

      //показать список продуктов и колличество в заказе с учетом москвы
   function showProductAndQuantity006($con, $category, $order_id, $city_id, $my_city, $my_region, $delivery){        
       //$main_warehouse = 'Москва';
       //$city  = $GLOBALS['city'];
       $in_region = $my_region;
       //$in_region = "Смоленская область";
       $in_city = $my_city;
       $main_warehouse = $GLOBALS['main_warehouse'];
       $orders_id_list = explode(";", $order_id);

    $all_product_inventory_id_array = [];
    $product_inventory_id_array = [];
   //найти товары в данной категории
   $query="SELECT p.product_id
                FROM t_category c 
                    JOIN t_product p ON p.category_id = c.category_id
                WHERE c.category = '$category'";
    $result = mysqli_query($con, $query) or die (mysql_error($con));         

    //echo "count= " . mysqli_num_rows($result) . "<br>";
    while($row = mysqli_fetch_array($result)){ 
        $product_id=$row[0]; 
        //echo "product_id = $product_id   <br>";
        
        //получить массив1 all product_inventory_id для поиска товара со свободным остатком/
        $query_inventory = "SELECT product_inventory_id FROM t_product_inventory WHERE product_id = $product_id";
        $result_inventory = mysqli_query($con, $query_inventory) or die(mysqli_error($con));        
        while($row_inventory = mysqli_fetch_array($result_inventory)){
            $prod_inv_id = $row_inventory[0];
            $all_product_inventory_id_array[] = $prod_inv_id;          
            //echo "product_inventory_id= $prod_inv_id <br>";
        }
        //из массива1 all_product_inventory_id_array собрать массив2 
        //в котором будут только товары с остатком на складе
        foreach($all_product_inventory_id_array as $key => $product_inventory_id){
            //получить склад хранения этого товара 
            $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $in_city,$main_warehouse); 
            //echo "provid_warehouse: $provid_warehouse_id<br>";
            //-вычислить свободные запасы на складе( кроме заказа из запроса) собрать массив2 product_inventory_id_array
            $free_inventory = check_inventory_004($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id);
            //вычислить свободные запасы для продажи
            //$free_inventory=calculateAvailableInventoryForSale($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id);
            if($free_inventory > 0 ){      
                //данные о складе регион или москва
                //$my_city=which_city_warehouse($con,$provid_warehouse_id);
                 //получить расположение склада 
                 $warehouseInfoList = warehouseInfo($con,$provid_warehouse_id);
                 $out_region = $warehouseInfoList['region'];
                 $out_district = $warehouseInfoList['district'];
                 $out_city = $warehouseInfoList['city'];
                 
                //echo "my_city: $my_city <br>";
                //найти цену на каждый товар с остатком и добавить ее в массив
                $query="SELECT `price` FROM `t_product_inventory` 
                                        WHERE `product_inventory_id`='$product_inventory_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($res);   
                $price=$row[0];  
                //echo "price: $price <br>";
                $date_of_sale_mil=0;
                if($in_region != $out_region){    //if($in_city != $out_city){  
                    //$in_city=$in_city;
                    //получить ближайшую дату поставки из москвы
                    $delivery_time_millis=get_delivery_date_intercity($con, $out_city, $in_city);
                    //срок доставки из пункта А в пункт В
                    $delivery_time = 24*60*60*1000; 
                    if($delivery_time_millis != 0){
                        $date_of_sale_mil= $delivery_time_millis + $delivery_time;
                    }else{
                        $date_of_sale_mil= $delivery_time_millis ;
                    }
                    //$date_of_sale_mil= $delivery_time_millis + $delivery_time;

                    //получить стоимость доставки товара
                    $price_of_delivery_product = 
                        price_of_delivery_product($con,$product_inventory_id,$out_city, $in_city);
                    //получить стоимость обработки товара
                    $price_of_processing_product = 
                        price_of_processing_product($con,$product_inventory_id,$out_city, $in_city);
                    //добавить и отнять доп расходы  
                    //$tubi_commission = 1.01;  
                    $tubi_commission = ($price * $GLOBALS['tubi_commission_percent']) - $price ;
                    $process_price = $tubi_commission + $price_of_delivery_product + $price_of_processing_product;
                    $process_price = round($process_price, 2);//округлить
                   
                }else{
                    //получить ближайшую дату поставки в городе
                    $delivery_time_millis=get_delivery_date_city($con, $city_id);
                    $process_price = 0;
                    $tubi_commission = 0;                    
                    $warehouse_processing_commission = 0;
                    $delivery_in_moscow_commission = 0;            
                             
                    $tubi_commission = ($price * $GLOBALS['tubi_commission_percent']) - $price ;
                    $warehouse_processing_commission = ($price * $GLOBALS['warehouse_processing_percent']) - $price ;
                    if($delivery == 1){//доставка есть
                        $delivery_in_moscow_commission = ($price * $GLOBALS['delivery_in_moscow_percent']) - $price ;
                    }
                     
                    //добавить доп расходы 
                    $process_price = $tubi_commission + $warehouse_processing_commission + $delivery_in_moscow_commission;
                    $process_price = round($process_price, 2);//округлить
                    $date_of_sale_mil= $delivery_time_millis;
                    //echo "test 2 <br>";
                }
                
                //echo "product_inventory_id= $product_inventory_id : price= $price millis: $date_of_sale_mil<br>";         
                $product_inventory_id_array[$product_inventory_id] =  $price; 
                $process_price_array[$product_inventory_id] =  $process_price;
                $date_of_sale_arr[$product_inventory_id] = $date_of_sale_mil; 
                $provid_warehouse_id_arr[$product_inventory_id] = $provid_warehouse_id; 
                $free_inventory_arr[$product_inventory_id] = $free_inventory;
            }                
        } 
        //$category
        //отсортировать по цене и первое значение получить для самой низкой цены
        asort($product_inventory_id_array);
        $count_product_provider = count($product_inventory_id_array);
        $x=0;
        $my_product_inventory_id = 0;
        $my_min_price = 0;        
        foreach($product_inventory_id_array as $key => $v){
            if($x == 0){
                $my_product_inventory_id = $key;
                $my_min_price = $v;
                //получить стоимость обработки заказа
                $process_price = $process_price_array[$my_product_inventory_id];
                //получить дату поставки из москвы
                $date_of_sale_mil = $date_of_sale_arr[$my_product_inventory_id];
                //получить склад на котором находится товар
                $provid_warehouse_id = $provid_warehouse_id_arr[$product_inventory_id]; 
                //свободные запасы на складе
                $free_inventory = $free_inventory_arr[$product_inventory_id];
                    
                
                //получить данные(информацию) по товару
                $product_list = receive_product_info($con, $my_product_inventory_id);
                $product_id=$product_list['product_id'];
                $product_name=$product_list['product_name'];
                $brand=$product_list['brand'];
                $characteristic=$product_list['characteristic'];
                $unit_measure=$product_list['unit_measure'];
                $weight_volume=$product_list['weight_volume'];
                $image_url=$product_list['image_url'];
                $quantity_package=$product_list['quantity_package'];
                

                $description=$product_list['description'];
                $min_sell=$product_list['min_sell'];
                $multiple_of=$product_list['multiple_of'];
              
                //показать колличество в заказах
                $my_quantity_in_order = 0;
                foreach($orders_id_list as $k => $order_id){
                    if($order_id != 0){
                        $query_quantity = "SELECT quantity FROM t_order_product 
                                    WHERE product_inventory_id = $my_product_inventory_id AND order_id =$order_id";
                        $result_quantity = mysqli_query($con, $query_quantity) or die (mysql_error($link));
                        $row_quantity = mysqli_fetch_array($result_quantity);
                        if($row_quantity){
                            $my_quantity_in_order += $row_quantity[0];
                        } 
                    }
                }
                //найти counterparty_id по product_inventory_id
                $counterparty_id = receive_counterpartyId_from_product_inventoryId($con, $my_product_inventory_id);
                //рейтинг поставщика
                $providerRatingInfoList = provider_rating($con, $counterparty_id);    
                $inventory_data=$providerRatingInfoList['inventory_data'];
                $under_delivery=$providerRatingInfoList['under_delivery'];

                echo $product_id ."&nbsp" . $my_product_inventory_id ."&nbsp" . $category . "&nbsp" . $brand ."&nbsp" 
                . $characteristic . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $my_min_price . "&nbsp" 
                . $image_url . "&nbsp" 
                . $min_sell . "&nbsp" . $multiple_of . "&nbsp" . $description . "&nbsp" 
                . $my_quantity_in_order . "&nbsp" 
                . $count_product_provider . "&nbsp" . $quantity_package ."&nbsp".$product_name. "&nbsp"
                . $date_of_sale_mil."&nbsp".$process_price."&nbsp".$provid_warehouse_id."&nbsp"
                . $free_inventory."&nbsp".$inventory_data."&nbsp".$under_delivery."<br>"; 

            }
            $x++;
        }

        $all_product_inventory_id_array = [];
        $product_inventory_id_array = [];         
        
    }
}
/*
    //показать список продуктов и колличество в заказе с учетом москвы
   function showProductAndQuantity006($con, $category, $order_id, $city_id){
       //$city = 'Смоленск';//$city_id
       //$main_warehouse = 'Москва';
       $city  = $GLOBALS['city'];
       $main_warehouse = $GLOBALS['main_warehouse'];
       $orders_id_list = explode(";", $order_id);

    $all_product_inventory_id_array = [];
    $product_inventory_id_array = [];
   //найти товары в данной категории
   $query="SELECT p.product_id
                FROM t_category c 
                    JOIN t_product p ON p.category_id = c.category_id
                WHERE c.category = '$category'";
    $result = mysqli_query($con, $query) or die (mysql_error($con));         

    //echo "count= " . mysqli_num_rows($result) . "<br>";
    while($row = mysqli_fetch_array($result)){ 
        $product_id=$row[0]; 
        //echo "product_id = $product_id   <br>";
        
        //получить массив1 all product_inventory_id для поиска товара со свободным остатком/
        $query_inventory = "SELECT product_inventory_id FROM t_product_inventory WHERE product_id = $product_id";
        $result_inventory = mysqli_query($con, $query_inventory) or die(mysqli_error($con));        
        while($row_inventory = mysqli_fetch_array($result_inventory)){
            $prod_inv_id = $row_inventory[0];
            $all_product_inventory_id_array[] = $prod_inv_id;          
            //echo "product_inventory_id= $prod_inv_id <br>";
        }
        //из массива1 all_product_inventory_id_array собрать массив2 
        //в котором будут только товары с остатком на складе
        foreach($all_product_inventory_id_array as $key => $product_inventory_id){
            //получить склад хранения этого товара 
            $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $city,$main_warehouse); 
            //echo "provid_warehouse: $provid_warehouse_id<br>";
            //-вычислить свободные запасы на складе собрать массив2 product_inventory_id_array
            $free_inventory = check_inventory_004($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id);
            //$free_inventory = check_inventory_003($con, $product_inventory_id, $order_id, $provid_warehouse_id);
            //echo "free_inventory: $free_inventory<br>";
            if($free_inventory > 0 ){      
                //данные о складе регион или москва
                $my_city=which_city_warehouse($con,$provid_warehouse_id);
                //echo "my_city: $my_city <br>";
                //найти цену на каждый товар с остатком и добавить ее в массив
                $query="SELECT `price` FROM `t_product_inventory` 
                                        WHERE `product_inventory_id`='$product_inventory_id'";
                $res = mysqli_query($con, $query) or die (mysql_error($con));
                $row = mysqli_fetch_array($res);   
                $price=$row[0];  
                //echo "price: $price <br>";
                $date_of_sale_mil=0;
                if($city != $my_city){    
                    $out_city=$my_city;
                    $in_city=$city;
                    //получить ближайшую дату поставки из москвы
                    $delivery_time_millis=get_delivery_date_intercity($con, $out_city, $in_city);
                    //срок доставки из пункта А в пункт В
                    $delivery_time = 24*60*60*1000; 
                    if($delivery_time_millis != 0){
                        $date_of_sale_mil= $delivery_time_millis + $delivery_time;
                    }else{
                        $date_of_sale_mil= $delivery_time_millis ;
                    }
                    //$date_of_sale_mil= $delivery_time_millis + $delivery_time;

                    //получить стоимость доставки товара
                    $price_of_delivery_product = 
                        price_of_delivery_product($con,$product_inventory_id,$out_city, $in_city);
                    //получить стоимость обработки товара
                    $price_of_processing_product = 
                        price_of_processing_product($con,$product_inventory_id,$out_city, $in_city);
                    //добавить и отнять доп расходы  
                    //$tubi_commission = 1.01;  
                    $tubi_commission = ($price * $GLOBALS['tubi_commission']) - $price ;
                    $process_price = $tubi_commission + $price_of_delivery_product + $price_of_processing_product;
                    $process_price = round($process_price, 2);//округлить
                   
                }else{
                    //получить ближайшую дату поставки в городе
                    $delivery_time_millis=get_delivery_date_city($con, $city_id);
                    $process_price = 0;
                    $date_of_sale_mil= $delivery_time_millis;
                    //echo "test 2 <br>";
                }
                
                //echo "product_inventory_id= $product_inventory_id : price= $price millis: $date_of_sale_mil<br>";         
                $product_inventory_id_array[$product_inventory_id] =  $price; 
                $process_price_array[$product_inventory_id] =  $process_price;
                $date_of_sale_arr[$product_inventory_id] = $date_of_sale_mil; 
                $provid_warehouse_id_arr[$product_inventory_id] = $provid_warehouse_id; 
                $free_inventory_arr[$product_inventory_id] = $free_inventory;
            }                
        } 
        //$category
        //отсортировать по цене и первое значение получить для самой низкой цены
        asort($product_inventory_id_array);
        $count_product_provider = count($product_inventory_id_array);
        $x=0;
        $my_product_inventory_id = 0;
        $my_min_price = 0;        
        foreach($product_inventory_id_array as $key => $v){
            if($x == 0){
                $my_product_inventory_id = $key;
                $my_min_price = $v;
                //получить стоимость обработки заказа
                $process_price = $process_price_array[$my_product_inventory_id];
                //получить дату поставки из москвы
                $date_of_sale_mil = $date_of_sale_arr[$my_product_inventory_id];
                //получить склад на котором находится товар
                $provid_warehouse_id = $provid_warehouse_id_arr[$product_inventory_id]; 
                //свободные запасы на складе
                $free_inventory = $free_inventory_arr[$product_inventory_id];
                    
                
                //получить данные(информацию) по товару
                $product_list = receive_product_info($con, $my_product_inventory_id);
                $product_id=$product_list['product_id'];
                $product_name=$product_list['product_name'];
                $brand=$product_list['brand'];
                $characteristic=$product_list['characteristic'];
                $unit_measure=$product_list['unit_measure'];
                $weight_volume=$product_list['weight_volume'];
                $image_url=$product_list['image_url'];
                $quantity_package=$product_list['quantity_package'];
                

                $description=$product_list['description'];
                $min_sell=$product_list['min_sell'];
                $multiple_of=$product_list['multiple_of'];
              
                //показать колличество в заказах
                $my_quantity_in_order = 0;
                foreach($orders_id_list as $k => $order_id){
                    $query_quantity = "SELECT quantity FROM t_order_product 
                                WHERE product_inventory_id = $my_product_inventory_id AND order_id =$order_id";
                    $result_quantity = mysqli_query($con, $query_quantity) or die (mysql_error($link));
                    $row_quantity = mysqli_fetch_array($result_quantity);
                    if($row_quantity){
                        $my_quantity_in_order += $row_quantity[0];
                    } 
                }


                echo $product_id ."&nbsp" . $my_product_inventory_id ."&nbsp" . $category . "&nbsp" . $brand ."&nbsp" 
                . $characteristic . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $my_min_price . "&nbsp" 
                . $image_url . "&nbsp" 
                . $min_sell . "&nbsp" . $multiple_of . "&nbsp" . $description . "&nbsp" 
                . $my_quantity_in_order . "&nbsp" 
                . $count_product_provider . "&nbsp" . $quantity_package ."&nbsp".$product_name. "&nbsp"
                . $date_of_sale_mil."&nbsp".$process_price."&nbsp".$provid_warehouse_id."&nbsp"
                . $free_inventory."<br>"; 

            }
            $x++;
        }

        $all_product_inventory_id_array = [];
        $product_inventory_id_array = [];         
        
    }
}
*/

   //показать список продуктов и колличество в заказе 
   function showProductAndQuantity003($con, $category, $order_id){
        $all_product_inventory_id_array = [];
        $product_inventory_id_array = [];
       //найти товары в данной категории
       $query="SELECT p.product_id
                    FROM t_category c 
                        JOIN t_product p ON p.category_id = c.category_id
                    WHERE c.category = '$category'";
        $result = mysqli_query($con, $query) or die (mysql_error($con));         

        //echo "count= " . mysqli_num_rows($result) . "<br>";
        while($row = mysqli_fetch_array($result)){ 
            $product_id=$row[0]; 
            //echo "product_id = $product_id   <br>";
            
            //получить массив1 all product_inventory_id для поиска товара со свободным остатком/
            $query_inventory = "SELECT product_inventory_id FROM t_product_inventory WHERE product_id = $product_id";
            $result_inventory = mysqli_query($con, $query_inventory) or die(mysql_error($link));
            
            while($row_inventory = mysqli_fetch_array($result_inventory)){
                $prod_inv_id = $row_inventory[0];
                $all_product_inventory_id_array[] = $prod_inv_id;          
                //echo "product_inventory_id= $prod_inv_id <br>";
            }
            //из массива1 all_product_inventory_id_array собрать массив2 
            //в котором будут только товары с остатком на складе
            foreach($all_product_inventory_id_array as $key => $product_inventory_id){

                //-вычислить свободные запасы на складе собрать массив2 product_inventory_id_array
                $free_inventory = check_inventory_002($con, $product_inventory_id, $order_id);
                if($free_inventory > 0 ){      
                    //найти цену на каждый товар с остатком и добавить ее в массив
                    $query="SELECT `price` FROM `t_product_inventory` 
                                            WHERE `product_inventory_id`='$product_inventory_id'";
                    $res = mysqli_query($con, $query) or die (mysql_error($con));
                    $row = mysqli_fetch_array($res);   
                    $price=$row[0];      
                    //echo "product_inventory_id= $product_inventory_id : price= $price <br>";         
                    $product_inventory_id_array[$product_inventory_id] =  $price; 
                }                
            } 
            //отсортировать по цене и первое значение получить для самой низкой цены
            asort($product_inventory_id_array);
            $count_product_provider = count($product_inventory_id_array);
            $x=0;
            $my_product_inventory_id = 0;
            $my_min_price = 0;
            foreach($product_inventory_id_array as $key => $v){
                if($x == 0){
                    $my_product_inventory_id = $key;
                    $my_min_price = $v;
                    //получить данные товара по my_product_inventory_id
                    $query ="SELECT p.product_id,  
                                    b.brand,  
                                    ch.characteristic, 
                                    um.unit_measure, 
                                    p.weight_volume,
                                    im.image_url,
                                    c.abbreviation,
                                    c.counterparty,
                                    pi.quantity_package,
                                    pn.product_name               
                                 FROM t_product_inventory pi
                                    JOIN t_product p         ON p.product_id = pi.product_id
                                    JOIN t_product_name pn   ON pn.product_name_id     = p.product_name_id
                                    JOIN t_brand b           ON b.brand_id = p.brand_id
                                    JOIN t_characteristic ch ON ch.characteristic_id = p.characteristic_id
                                    JOIN t_unit_measure um   ON um.unit_measure_id = p.unit_measure_id
                                    JOIN t_image im          ON im.image_id = pi.image_id
                                    JOIN t_counterparty c    ON c.counterparty_id = pi.counterparty_id
                                 WHERE product_inventory_id='$my_product_inventory_id'";
                    $res = mysqli_query($con, $query) or die (mysql_error($link));
                    while($row = mysqli_fetch_array($res)){
                                    $product_id = $row[0];
                                    $brand = $row[1];
                                    $characteristic = $row[2];
                                    $unit_measure = $row[3];
                                    $weight_volume = $row[4];
                                    $image_url = $row[5];
                                    $abbreviation = $row[6];
                                    $counterparty = $row[7];
                                    $quantity_package = $row[8];
                                    $product_name = $row[9];
                    }              

                    //показать колличество в заказе
                $query_quantity = "SELECT quantity FROM t_order_product 
                WHERE product_inventory_id = $my_product_inventory_id AND order_id =$order_id";
                $result_quantity = mysqli_query($con, $query_quantity) or die (mysql_error($link));
                $row_quantity = mysqli_fetch_array($result_quantity);
                if($row_quantity){
                    $my_quantity_in_order = $row_quantity[0];
                }else {$my_quantity_in_order = 0;} 

                     echo $product_id ."&nbsp" . $my_product_inventory_id ."&nbsp" . $category . "&nbsp" . $brand ."&nbsp" . 
                    $characteristic . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $my_min_price . "&nbsp" . 
                    $image_url . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" . $my_quantity_in_order . 
                    "&nbsp" . $count_product_provider . "&nbsp" . $quantity_package ."&nbsp".$product_name."<br>"; 
                }
                $x++;
            }

            $all_product_inventory_id_array = [];
            $product_inventory_id_array = [];         
            
        }
   }
  
    function showProduct001($con, $category){   //---------this function will be need delete
                                    //----------показать инфу <t_product> <t_inventory> по товару из выбранной категории
       $query = "SELECT c.category_id,
                        p.product_id, 
                        p.brand_id,
                        b.brand,
                        p.characteristic_id,
                        ch.characteristic,
                        p.unit_measure_id,
                        um.unit_measure,
                        p.weight_volume
                    FROM t_category c
                        JOIN t_product p         ON p.category_id = c.category_id
                        JOIN t_brand b           ON b.brand_id = p.brand_id
                        JOIN t_characteristic ch ON ch.characteristic_id = p.characteristic_id
                        Join t_unit_measure um   ON um.unit_measure_id = p.unit_measure_id
                    WHERE c.category = '$category'";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        $data = ""; 
        while($row = mysqli_fetch_array($result)){
                        $category_id = $row[0];
                        $product_id = $row[1];
                        $brand_id = $row[2];
                        $brand = $row[3];
                        $characteristic_id = $row[4];
                        $characteristic = $row[5];
                        $unit_measure_id = $row[6];
                        $unit_measure = $row[7];
                        $weight_volume = $row[8];
            
                                                        //------------показать минимальную цену по товару
                $query_price = "SELECT * FROM t_product_inventory WHERE price = (SELECT  MIN(price) FROM t_product_inventory WHERE product_id = $product_id)
                    AND product_id = $product_id AND on_off = 1";
                $result_price = mysqli_query($con, $query_price) or die (mysql_error($link));
                if($row_price = mysqli_fetch_array($result_price)){
                
                    $product_inventory_id = $row_price[0];
                    $price=$row_price[4];
                    $quantity_package = $row_price[5];
                    $image_id = $row_price[6];
                    $description_id = $row_price[7];
                    $counterparty_id = $row_price[8];
                                                //--------показать Имя контрагента//
                                                //---------показать image_url
                                                //---------показать описание 
                    $query_info = "SELECT   i.image_url,
                                            d.description,
                                            c.counterparty
                                    FROM t_product_inventory pi
                                         JOIN t_counterparty c ON c.counterparty_id = $counterparty_id 
                                         JOIN t_image i        ON i.image_id = $image_id
                                         JOIN t_description d  ON d.description_id = $description_id ";
                   
                    $result_info = mysqli_query($con, $query_info) or die(mysql_error($link));
                    if($row_info = mysqli_fetch_array($result_info)){
                    
                    $image_url = $row_info[0];
                    $description = $row_info[1];
                    $counterparty = $row_info[2];
                        
                    echo $product_id ."&nbsp" . $product_inventory_id ."&nbsp" . $category . "&nbsp" . $brand ."&nbsp" . 
                    $characteristic . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $price . "&nbsp" . 
                    $image_url . "&nbsp" . $description . "&nbsp" . $counterparty . "<br>";
                    }else echo 'error Result_info' . "<br>";
                }
        }
                                            
               
                                                    
            
   } 

   function quantityProductFromOrder($con, $order_id, $product_id){   // показать количество одного товара в заказе
       $query = "SELECT quantity FROM t_order_product WHERE order_id = $order_id AND product_id = $product_id";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       if($row = mysqli_fetch_array($result)){
           echo $row[0];
       }else {echo 0;}
   }
   //ищем товары в БД по поисковому запросу 
   function search_product_by_text($con, $text_for_search, $order_id,$city_id, $my_city, $my_region, $delivery){
        $text_list = explode( ' ', $text_for_search );
        $count = 0;
        $query_str="";
        $query_text = "";
        foreach($text_list as $k => $v){  
            $count++;
            if(count($text_list) == $count){
                $query_text .= $v ;
            }else{
                $query_text .= $v . " ";
            }   
        }
        $query_str="(`product_inventory_id` LIKE '%$query_text%' 
                    OR `product_description_for_search` LIKE '%$query_text%')";
        //echo "query_str=$query_str<br>";
        /*
        $query_text = "(`product_description_for_search` LIKE '%";
        foreach($text_list as $k => $v){            
            $query_text .= $v ;            
            $count++;
        }
        $query_text .= "%')"; 

            $query_text = "(";
            foreach($text_list as $k => $v){
                //echo "k = $k, v = $v <br>";
                if($count > 0){
                    $query_text .= " OR ";
                }
                $query_text .= "`product_description_for_search` LIKE '%". $v . "%'";            
                $count++;
            }
            $query_text .= ")";
        */
        //echo "query_text = $query_text <br>";
        $query="SELECT  `product_inventory_id`, `product_description_for_search` 
                FROM `t_product_description_for_search` WHERE $query_str";
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($res)){
            $product_inventory_id=$row[0]; 
            $product_description=$row[1];

            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);    
            $product_id=$product_list['product_id'];
            //echo "id = $product_inventory_id, product_id = $product_id, product_description = $product_description <br>";
            showProductPriceAllProvider002($con, $product_id, $order_id,$city_id, $my_city, $my_region, $delivery);
        } 
   }
   //показать цену на товар от всех поставщиков
   function showProductPriceAllProvider002($con, $product_id, $order_id,$city_id, $my_city, $my_region, $delivery){  
        $in_region = $my_region;   
        $city  = $my_city;
        $in_city = $my_city;
        $main_warehouse = $GLOBALS['main_warehouse'];   
        $orders_id_list = explode(";", $order_id); 

         //получить данные товара для поиска цен        
         $query_product = "SELECT   pi.product_inventory_id, 
                                    cp.counterparty                       

                                FROM t_product_inventory pi
                                    JOIN t_counterparty cp   ON cp.counterparty_id   = pi.counterparty_id                               
                                WHERE  pi.product_id = $product_id ORDER BY pi.price ASC"; 
       
        $result = mysqli_query($con, $query_product) or die (mysql_error($link));
        while($row = mysqli_fetch_array($result)){
            $product_inventory_id = $row[0]; 
            $counterparty  = $row[1]; 

            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);
            $category=$product_list['category'];
            $brand=$product_list['brand']; 
            $characteristic=$product_list['characteristic'];
            $unit_measure=$product_list['unit_measure'];
            $weight_volume=$product_list['weight_volume']; 
            $price=$product_list['price'];
            $image_url=$product_list['image_url'];
            $description_prod=$product_list['description_prod'];
            $quantity_package=$product_list['quantity_package'];
            $product_name=$product_list['product_name'];

            $min_sell=$product_list['min_sell'];
            $multiple_of=$product_list['multiple_of'];
            $product_info=$product_list['product_info'];

            //echo "test 1 <br>";
            //получить склад хранения этого товара 
            $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $in_city,$main_warehouse); 
            //echo "provid_warehouse_id: $provid_warehouse_id <br>";
            //-вычислить свободные запасы на складе ( кроме заказа из запроса)
           $free_inventory = check_inventory_004($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id); 

            if($free_inventory > 0 ){
                //данные о складе регион или москва
                //$my_city=which_city_warehouse($con,$provid_warehouse_id);
                //получить расположение склада 
                $warehouseInfoList = warehouseInfo($con,$provid_warehouse_id);
                $out_region = $warehouseInfoList['region'];
                $out_district = $warehouseInfoList['district'];
                $out_city = $warehouseInfoList['city'];

                $date_of_sale_mil=0;
                if($in_region != $out_region){  
                    //получить ближайшую дату поставки из москвы
                    $delivery_time_millis=get_delivery_date_intercity($con, $out_city, $in_city);
                    //срок доставки из пункта А в пункт В
                    $delivery_time = 24*60*60*1000; 
                    if($delivery_time_millis != 0){
                        $date_of_sale_mil= $delivery_time_millis + $delivery_time;
                    }else{
                        $date_of_sale_mil= $delivery_time_millis ;
                    }

                    //получить стоимость доставки товара
                    $price_of_delivery_product = 
                        price_of_delivery_product($con,$product_inventory_id,$out_city, $in_city);
                    //получить стоимость обработки товара
                    $price_of_processing_product = 
                        price_of_processing_product($con,$product_inventory_id,$out_city, $in_city);
                    //добавить и отнять доп расходы  
                    //$tubi_commission = 1.01;  
                    $tubi_commission = ($price * $GLOBALS['tubi_commission_percent']) - $price ;
                    $process_price = $tubi_commission + $price_of_delivery_product + $price_of_processing_product;
                    $process_price = round($process_price, 2);//округлить*/
                   
                }else{
                    //получить ближайшую дату поставки в городе
                    $delivery_time_millis=get_delivery_date_city($con, $city_id);
                    $process_price = 0;
                    $tubi_commission = 0;                    
                    $warehouse_processing_commission = 0;
                    $delivery_in_moscow_commission = 0;            
                             
                    $tubi_commission = ($price * $GLOBALS['tubi_commission_percent']) - $price ;
                    $warehouse_processing_commission = ($price * $GLOBALS['warehouse_processing_percent']) - $price ;
                    if($delivery == 1){//доставка есть
                        $delivery_in_moscow_commission = ($price * $GLOBALS['delivery_in_moscow_percent']) - $price ;
                    }
                     
                    //добавить доп расходы 
                    $process_price = $tubi_commission + $warehouse_processing_commission + $delivery_in_moscow_commission;
                    $process_price = round($process_price, 2);//округлить
                    $date_of_sale_mil= $delivery_time_millis;
                }  
                    
                //показать колличество в заказах
                $quantity = 0;
                foreach($orders_id_list as $k => $order_id){
                    if($order_id != 0){
                        $query_quantity = "SELECT quantity FROM  t_order_product 
                                    WHERE product_inventory_id = $product_inventory_id AND order_id = $order_id";     
                        $result_quantity = mysqli_query($con, $query_quantity) or die (mysql_error($link));
                        $row_quantity = mysqli_fetch_array($result_quantity);
                        if($row_quantity){
                            $quantity += $row_quantity[0];
                        }
                    }
                }
                //найти counterparty_id по product_inventory_id
                $counterparty_id = receive_counterpartyId_from_product_inventoryId($con, $product_inventory_id);
                //рейтинг поставщика
                $providerRatingInfoList = provider_rating($con, $counterparty_id);    
                $inventory_data=$providerRatingInfoList['inventory_data'];
                $under_delivery=$providerRatingInfoList['under_delivery'];
               
                echo $product_id ."&nbsp" . $product_inventory_id ."&nbsp" . $category . "&nbsp" 
                        . $brand ."&nbsp" . $characteristic . "&nbsp" . $unit_measure . "&nbsp" 
                        . $weight_volume . "&nbsp" . $price . "&nbsp" . $image_url . "&nbsp" 
                        . $description_prod . "&nbsp" . $counterparty . "&nbsp" . $quantity . "&nbsp" 
                        . $quantity_package ."&nbsp" .$product_name."&nbsp".$date_of_sale_mil . "&nbsp"
                        . $process_price."&nbsp".$provid_warehouse_id."&nbsp"
                        .$min_sell."&nbsp".$multiple_of."&nbsp".$product_info."&nbsp"
                        .$free_inventory."&nbsp".$inventory_data."&nbsp".$under_delivery."<br>";
                        
            }                   
            
        }      
         
   }  
   
   
   //показать цену на товар от всех поставщиков
   function showProductPriceAllProvider001($con, $product_id, $order_id){      
         //получить данные товара для поиска цен
         $query_product = "SELECT  c.category, b.brand, ch.characteristic, um.unit_measure, p.weight_volume, 
                                    pi.product_inventory_id, pi.price, im.image_url, ds.description, 
                                    cp.counterparty, pi.quantity_package, pn.product_name
                                    
                            FROM t_product_inventory pi
                                JOIN t_product p         ON p.product_id         = $product_id
                                JOIN t_product_name pn   ON pn.product_name_id   = p.product_name_id
                                JOIN t_category c        ON c.category_id        = p.category_id
                                JOIN t_brand b           ON b.brand_id           = p.brand_id
                                JOIN t_characteristic ch ON ch.characteristic_id = p.characteristic_id 
                                JOIN t_unit_measure um   ON um.unit_measure_id   = p.unit_measure_id
                                JOIN t_image im          ON im.image_id          = pi.image_id
                                JOIN t_description ds    ON ds.description_id    = pi.description_id
                                JOIN t_counterparty cp   ON cp.counterparty_id   = pi.counterparty_id
                               
                            WHERE  pi.product_id = $product_id ORDER BY pi.price ASC"; 
       
        $result = mysqli_query($con, $query_product) or die (mysql_error($link));
        while($row = mysqli_fetch_array($result)){
                    $category = $row[0]; $brand = $row[1]; $characteristic = $row[2]; $unit_measure = $row[3]; 
                    $weight_volume  = $row[4]; $product_inventory_id = $row[5]; $price = $row[6]; 
                    $image_url = $row[7]; $description = $row[8]; $counterparty  = $row[9]; 
                    $quantity_package = $row[10]; $product_name=$row[11];

            //-вычислить свободные запасы на складе 
            $free_inventory = check_inventory_002($con, $product_inventory_id, $order_id);
            
            if($free_inventory > 0 ){
                    
                $query_quantity = "SELECT quantity FROM  t_order_product 
                            WHERE product_inventory_id = $product_inventory_id AND order_id = $order_id";     
                $result_quantity = mysqli_query($con, $query_quantity) or die (mysql_error($link));
                $row_quantity = mysqli_fetch_array($result_quantity);
                if($row_quantity){
                $quantity = $row_quantity[0];
                }else {$quantity = 0;}
               
                        echo $product_id ."&nbsp" . $product_inventory_id ."&nbsp" . $category . "&nbsp" . $brand ."&nbsp" . 
                        $characteristic . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $price . "&nbsp" . 
                        $image_url . "&nbsp" . $description . "&nbsp" . $counterparty . "&nbsp" . $quantity . "&nbsp" . 
                        $quantity_package ."&nbsp" .$product_name."<br>";
            }                   
            
        }      
         
   } 
   
  //сложить товар в заказ проверить остатки/ 
   function addOrderProduct001($con, $order_id, $product_inventory_id, $quantity, $process_price,$provider_warehouse_id){  
        //получить стоимость
        $query_price = "SELECT price FROM t_product_inventory WHERE product_inventory_id = $product_inventory_id";
        $result_price = mysqli_query($con, $query_price) or die (mysql_error($link));
        $row_price = mysqli_fetch_array($result_price);
        $price = $row_price[0];
        echo 'price = ';
        echo $price . "<br>";
          //проверить существует строка в t_order_product, есть ли такой товар в заказе
        $query_check_string = "SELECT COUNT(*) FROM t_order_product 
                            WHERE order_id = $order_id 
                              AND product_inventory_id = $product_inventory_id";
                              
       $result_check_string = mysqli_query($con, $query_check_string) or die(mysql_error($link));
        $row_check_string = mysqli_fetch_row($result_check_string);
        if ($row_check_string[0] > 0){
            echo 'есть данные' . "<br>";
            //-если такой товар в заказе есть исправить колличество, и проверить остатки на складе

            //-вычислить свободные запасы на складе       
            $free_inventory = check_inventory_002($con, $product_inventory_id, $order_id);                          
           //$free_inventory = check_inventory_001($con, $product_inventory_id, $order_id);  
            //$free_inventory = check_inventory($con, $product_inventory_id, $order_id);
            echo 'свободные запасы = ' . $free_inventory . "<br>";
            if($free_inventory > 0 ){
                //если остаток меньше заказа то кладем в заказ остаток и сообщаем об этом                                  
                if($free_inventory - $quantity < 0 ){        
                    
                    $quantity = $free_inventory;
                    $res = "RETURN_QUANTITY" . "&nbsp" . "$quantity" . "&nbsp" . "Запас товара на складе меньше запрошенного колличества" . "<br>";
                    echo $res;
                    //echo 'messege' . "&nbsp" . 'Запас товара на складе меньше запрошенного колличества' . "<br>";
                }  
                                                    
                //меняем данные в t_order_product/
                $query_chenge = "UPDATE t_order_product  SET quantity = $quantity, price = $price 
                            WHERE order_id = $order_id AND product_inventory_id = $product_inventory_id ";
                            
                $result_chenge = mysqli_query($con, $query_chenge) or die (mysql_error($link));
                    if($result_chenge ) echo  "Колличество товара обновлено";
                
            }else{
                
            }
                                           
                                            
        //если такого товара нет в заказе проверить цену при необходимости исправить                                    //если такого товара нет в заказе проверить цену при необходимости исправить
        }else {
            echo 'нет данных' . "<br>";
            //если такого товара нет в заказе проверить остатки на складе
                //вычислить свободные запасы на складе 
            $free_inventory = check_inventory_002($con, $product_inventory_id, $order_id);  
            //$free_inventory = check_inventory_001($con, $product_inventory_id, $order_id);
            //$free_inventory = check_inventory($con, $product_inventory_id, $order_id);
            echo 'свободные запасы = ' . $free_inventory . "<br>";
            if($free_inventory > 0 ){
                //если остаток меньше заказа то кладем в заказ остаток и сообщаем об этом                                     
                if($free_inventory - $quantity < 0 ){        
                    
                    $quantity = $free_inventory;
                    $res = "RETURN_QUANTITY" . "&nbsp" . "$quantity" . "&nbsp" . "Запас товара на складе меньше запрошенного колличества" . "<br>";
                    //echo 'messege' . "&nbsp" . 'Запас товара на складе меньше запрошенного колличества' . "<br>";
                    echo $res;
                }                                                  
                
               //описание товара для документов создать или найти копию и вернуть id
               $description_docs_id = receive_product_description_docs_id($con, $product_inventory_id);
                
                //вносим данные в t_order_product
                $query_add = "INSERT INTO `t_order_product` 
                        (`product_inventory_id`, `quantity`, `price`, `price_process`, `order_id`, `provider_war_id`, `description_docs_id`) 
                VALUES ('$product_inventory_id', '$quantity', '$price', '$process_price', '$order_id', '$provider_warehouse_id', '$description_docs_id')";
                $result_add = mysqli_query($con, $query_add) or die (mysqli_error($con));
                if($result_add){
                    echo 'messege' . "&nbsp" . "Товар в заказе" . "<br>";
                }else echo 'error' . "&nbsp" . "Ошибка при добавлении товара в корзину" . "<br>";
                
            }else{                               //------остатка нет
                echo 'messege' ."&nbsp" . 'Товара нет в остатках' . "<br>";
            }                                                   
             
        }                    
   }
   
   //вычислить свободные запасы на складе 
   function check_inventory_002($con, $product_inventory_id, $order_id){
     //получить свободный остаток на складе
     
            //получить колличество поставленного товара
            $query = "SELECT `quantity` FROM `t_warehouse_inventory_in_out` 
                             WHERE  `product_inventory_id`='$product_inventory_id' 
                             AND `in_warehouse_id` IS NOT NULL";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $delivery_quantity = '0';
          
            while($row = mysqli_fetch_array($result)){
                $delivery_quantity += $row[0];
                //echo "`product_inventory_id 1 `". $product_inventory_id . ' quantity: ' .$row[0] . "<br>";
            }

            //получить колличество реализованного товара
            $query = "SELECT `quantity` FROM `t_warehouse_inventory_in_out` 
                             WHERE  `product_inventory_id`='$product_inventory_id'
                             AND `out_warehouse_id` IS NOT NULL";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $sold_quantity = '0';
            
            while($row = mysqli_fetch_array($result)){
                $sold_quantity += $row[0];
                //echo "`product_inventory_id 2 `". $product_inventory_id . ' quantity: ' .$row[0] . "<br>";
            }
            //подсчитать свободный остаток на складе            
            $product_have_to_warehouse = $delivery_quantity - $sold_quantity; 
       
        //получить колличество товара в невыполненных заказах кроме заказа с которым сейчас работаем
        $query = "SELECT op.quantity
                        FROM t_order ord
                            JOIN t_order_product op ON op.order_id = ord.order_id 
                                AND op.product_inventory_id='$product_inventory_id' 
                                AND op.order_prod_deleted = '0'
                        WHERE ord.executed='0' AND ord.order_id != '$order_id'";
        $res_1 = mysqli_query($con, $query) or die (mysqli_error($con));
        $ordered_quantity = 0;
        while($row = mysqli_fetch_array($res_1)){
            $ordered_quantity += $row[0];
        }
        //echo "ordered_quantity: " . $ordered_quantity . "<br>";
    
     //вычислить свободный(и незабронированный) остаток
         $free_inventory = $product_have_to_warehouse - $ordered_quantity;

         return $free_inventory;
    }

    //вычислить свободные запасы на складе 
   function check_inventory($con, $product_inventory_id, $order_id){
        $query_inventory = "SELECT quantity FROM t_product_inventory WHERE  product_inventory_id = $product_inventory_id";
        $result_inventory = mysqli_query($con, $query_inventory) or die (mysql_error($link));
        $row_inventory = mysqli_fetch_array($result_inventory);
        $inventory = $row_inventory[0];
        
        $query_count = "SELECT quantity FROM t_order_product WHERE product_inventory_id = $product_inventory_id AND order_id != $order_id";
        $result_count = mysqli_query($con, $query_count) or die (mysql_error($link));
        $quantity_sold = 0;
        while($row_count = mysqli_fetch_array($result_count)){
            $quantity_sold += $row_count[0];
        }
        $free_inventory = $inventory - $quantity_sold;
        
        return $free_inventory;
   }
   
   function addOrderProduct($con, $order_id, $product_id, $provider_id, $quantity){  //--------удалить //сложить товар в заказ
       $query = "SELECT price FROM t_price WHERE product_id = $product_id AND  provider_id = $provider_id";// получаем стоимость
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $row = mysqli_fetch_array($result);
       $price = $row[0];
       
                                                            //проверить существует строка в таблице
       $query_check_string = "SELECT COUNT(*) FROM t_order_product 
                            WHERE order_id = $order_id 
                              AND product_id = $product_id
                              AND provider_id = $provider_id";
       $result_check_string = mysqli_query($con, $query_check_string) or die(mysql_error($link));
        $row_check_string = mysqli_fetch_row($result_check_string);
        if ($row_check_string[0] > 0)
        {
            // Есть данные
            $query_update = "UPDATE t_order_product 
                              SET quantity = $quantity, price = $price 
                            WHERE order_id = $order_id 
                              AND product_id = $product_id
                              AND provider_id = $provider_id";
        $result_update = mysqli_query($con, $query_update) or die (mysql_error($link));
            echo "product update";
        }
        else
        {
            // нет данных
             $query = "INSERT INTO t_order_product (product_id, quantity, price, provider_id, order_id) VALUES 
                                                  ($product_id, $quantity, $price, $provider_id, $order_id)";
            $result = mysqli_query($con, $query) or die (mysql_error($link));
            if($result){
                echo "product in box";
            }else echo "Error write new product";
        }
       
   }   
   
   function checkMyEmptyOrder($con, $user_uid){ //проверить есть ли начатый заказ<delete>
    //найти user_id
    $userID = checkUserID($con, $user_uid);//найти user_id
    $query = "SELECT order_id FROM t_order WHERE user_id = $userID AND executed = 0";
     $result = mysqli_query($con, $query) or die (mysql_error($link));
     $row = mysqli_fetch_array($result);
     $order_id =  $row[0];
     return $order_id;
}

   function checkEmptyOrder($con, $counterparty_id){                //<delete> проверить есть ли начатый заказ
       $query = "SELECT order_id FROM t_order WHERE counterparty_id = $counterparty_id AND executed = 0";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        $row = mysqli_fetch_array($result);
        $order_id =  $row[0];
        return $order_id;
   }
   
   function takeBuyer($con, $counterparty_id){             //weel be delete--------получить buyer_id контрагента
        $query = "SELECT  buyer_id FROM t_buyer WHERE counterparty_id = '$counterparty_id'";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $row = mysqli_fetch_array($result);
       $buyer_id = $row[0];
       //echo "buyer_id: " . $buyer_id;
       return $buyer_id;
   }
    //создать новый заказ
    function addMyOrder001($con, $user_id, $counterparty_id, $warehouse_id, $dateOfSaleMillis, $category, $delivery){   
        //проверить нет блокировки для создания заказов у компании
        $query="SELECT `block_order` FROM `t_counterparty` WHERE `counterparty_id`='$counterparty_id'";
        $result = mysqli_query($con, $query) or die (mysql_error($con));  
        $row = mysqli_fetch_array($result);
        $block_order=$row[0];
        if($block_order == 0){
            //компания не может делать заказы из-за блокировки
            echo "message"."&nbsp".$GLOBALS['block_order_info'];
            return;
        }

        $query = "INSERT INTO `t_order` 
            (`user_id` , `counterparty_id`, `warehouse_id`,`get_order_date_millis`, `category_in_order`,`delivery`) 
        VALUES ('$user_id', '$counterparty_id','$warehouse_id','$dateOfSaleMillis','$category','$delivery')";
                    $result = mysqli_query($con, $query) or die (mysqli_error($con));
                    $order_id = mysqli_insert_id($con);
                
        echo $order_id."&nbsp".$dateOfSaleMillis."&nbsp".$category."<br>";
        return $order_id;
    }
     //создать новый заказ
   /* function addMyOrder($con, $user_id, $counterparty_id, $warehouse_id){   
            $query = "INSERT INTO `t_order` (`user_id` , `counterparty_id`, `warehouse_id`) 
                                VALUES ('$user_id', '$counterparty_id','$warehouse_id')";
                        $result = mysqli_query($con, $query) or die (mysqli_error($con));
                        $order_id = mysqli_insert_id($con);
                    
            return $order_id;
    }*/
  
   //--------------------------------------------------------delete bottom chenge showProduct
   function showProductForBuyer($con, $category){
       
       //$query = "SELECT  category, brand, characteristic FROM t_product WHERE category = '$category'";
      
       $query = "SELECT p.product_id, c.category, b.brand, ch.characteristic, pr.price_id, pr.price, cp.counterparty
                    FROM t_product p  
                        JOIN t_category c ON c.category_id = p.category_id
                        JOIN t_brand b ON b.brand_id = p.brand_id
                        JOIN t_characteristic ch ON ch.characteristic_id = p.characteristic_id
                        JOIN t_price pr ON pr.product_id = p.product_id
                        JOIN t_counterparty cp ON cp.counterparty_id = pr.counterparty_id
                    WHERE p.category = '$category' ";
       
        $result = mysqli_query($con, $query) or die (mysql_error($link)); 
        $data = "";
        while($row = mysqli_fetch_array($result)){
            $data .= $row[0] . "&nbsp" . $row[1] . "&nbsp" .$row[2] . "&nbsp" . $row[3] .
            "&nbsp" . $row[4] . "&nbsp" .$row[5] . "&nbsp" .$row[6] .  "<br>" ;
        }
  
       if($data){
          echo $data ."<br>";
       } else echo "This product defolt...";
   }
   //------------------------------------------------------------------delete top
   function deleteStringInputProduct($con, $temp){  //удалить записанную в товары строку из списка поставщика
       $query = "DELETE FROM t_input_product WHERE id= $temp";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       if($result){//echo "Delete" . "<br>";
       } else {echo "Error: " . $query . "<br>" . mysqli_error($con);}
   }
   
   function goProductInTable($con, $temp){     //записать товар в таблицу базы для использования
       //-------------------------------собираем все что надо записать имена и id
       $query = "SELECT
                    c.category_id,
                    c.category,
                    b.brand_id,
                    b.brand,
                    ch.characteristic_id,
                    ch.characteristic
                FROM
                    t_input_product ip
                    JOIN t_category c ON c.category = ip.category
                    JOIN t_brand b ON b.brand = ip.brand
                    JOIN t_characteristic ch ON ch.characteristic = ip.characteristic
                WHERE ip.id = $temp";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($row = mysqli_fetch_array($result)){
            $category_id=$row[0];$category=$row[1];$brand_id=$row[2];$brand=$row[3];
            $characteristic_id=$row[4];$characteristic=$row[5];
            //echo " String prod ok ".$category_id.",".$category.",".$brand_id.",".$brand.",".$characteristic_id.",".$characteristic."<br>";
            $query = "INSERT INTO t_product (category_id, category, brand_id, brand, characteristic_id, characteristic) 
                                    VALUES ('$category_id','$category','$brand_id','$brand','$characteristic_id','$characteristic')";
            $result = mysqli_query($con, $query) or die (mysql_error($link));
            if($result){
                echo " Product write: ".$category_id.",".$category.",".$brand_id.",".$brand.",".$characteristic_id.",".$characteristic."<br>";
                
                deleteStringInputProduct($con, $temp);                        //удалить записанную в товары строку из списка поставщика
                
            }else {echo "Error: " . $query . "<br>" . mysqli_error($con);}
        }else {echo "Error: " . $query . "<br>" . mysqli_error($con);}
   }
   
   function checkAndWriteProduct($con, $temp){   //проверить данные по таблицам при отсутствии добавить
       //--------------------------------проверяем brand
       $query = "SELECT
                    b.brand_id,
                    b.brand,
                    ip.brand
                FROM
                    t_input_product ip
                    JOIN t_brand b ON b.brand = ip.brand
                WHERE ip.id = $temp ";
        $result  = mysqli_query($con, $query) or die (mysqli_error($link));  
        $row = mysqli_fetch_array($result);
        if($row){
            echo "Уже имеется " . $row[0] . "; ". $row[1] .", ";
        }
        else {
            $query = "SELECT brand FROM t_input_product WHERE id = '$temp'";
            $result = mysqli_query($con, $query) or die (mysql_error($link));
                if($row = mysqli_fetch_array($result)){
                    $query = "INSERT INTO t_brand (brand) VALUES ('$row[0]')";
                    $result = mysqli_query($con, $query) or die (mysql_error($link));
                    if($result){
                    //echo "new brand write ". $row[0] . " ok";
                    }else {
                        echo "Error: " . $query . "<br>" . mysqli_error($con); 
                    }
                }else echo "Error input_product file";
        }
        //---------------------------------проверяем характеристику
        $query = "SELECT
                        ch.characteristic_id,
                        ch.characteristic
                  FROM 
                        t_input_product ip
                  JOIN t_characteristic ch ON ch.characteristic = ip.characteristic
                  WHERE ip.id = $temp";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
            if($row = mysqli_fetch_array($result)){
                echo " Characterystic heve: id " . $row[0] . ": " . $row[1] . "<br>";
            } else {
                $query = "SELECT characteristic FROM t_input_product WHERE id = '$temp' ";
                $result = mysqli_query($con, $query) or die (mysql_error($link));
                    if($row = mysqli_fetch_array($result)){
                        $query = "INSERT INTO t_characteristic (characteristic) VALUES ('$row[0]')";
                        $result = mysqli_query($con, $query) or die (mysql_error($link));
                        if($result){
                           // echo " new characteristic write " . $row[0] . " ok"; 
                        }else{
                            echo "Error: " . $query . "<br>" . mysqli_error($con);
                        }
                    }else echo "Error input_product file characteristic";
                
            }
   }
	
	function checkWriteCategory($con, $category, $catalog_id){       //внести в таблицу категорию которой нет 
	    //$category = $category;
	   // $catalog_id = $catalog_id;
	    
	    $query = "INSERT INTO t_category (category,catalog_id) VALUES ('$category','$catalog_id')";
	    
	    $result = mysqli_query($con, $query) or die (mysql_error($link));
	    
        	if ($result) {
        		  echo " Создана новая категория ". $category ;
        	} else {
        		  echo "Error: " . $query . "<br>" . mysqli_error($con);
        	}
	    
	}
	function checkInputProductID($con){       //получаем id из табл.поставки 
	    global $ip_id_arr, $i;
                                        
       $query = "SELECT id FROM t_input_product WHERE id > 0";
       
       $result = mysqli_query($con,$query); 
    	
                                    //записываем id в массив
        while ($row = mysqli_fetch_array($result)) {  
               
                $ip_id_arr [$i++] = $row[0];
    	}
    	
    	if($ip_id_arr){                     // проверяем на не пустой id в массиве
        	for($i=0;$i<count($ip_id_arr);$i++){
        	                               // echo "id- ". $ip_id_arr[$i] . "<br>";
        	}
    	}else "Array false";
   }
	
	function printNotCategory($category_not_arr){  //показать категории которых нет в таблице
	    for($i=0;$i<count($category_not_arr);$i++){
	     echo    $category_not_arr[$i] . "<br>";
	    }
	}
	function checkCategory($con,$row){     //проверить категории на наличие в таблице
	    global $category_not_arr , $j ;
	      $c_catalog_id=$row[0];     $ip_category = $row[1];                                           
        $query = "SELECT category FROM t_category WHERE catalog_id = $c_catalog_id AND category = '$ip_category' ";	   
	    $result = mysqli_query($con, $query) or die (mysqli_error($link)); 
	    $count=0;
	    if($category = mysqli_fetch_array($result)){
	        echo "<br>";                    // если категория не существуют вносим категорию в таблицу t_category
	    }else {
	        echo "This category $ip_category is not " . "<br>";
	        $category_not_arr[$j++]="id-catalog " . $c_catalog_id . " category: " . $ip_category;
	        
	    }
	}
	function checkCatalog($con,$row){                             //ищем имя каталога на совпадение 
	       $ip_catalog = $row[0];   $ip_category = $row[1];
	                               
	   $query = "SELECT catalog_id, catalog FROM t_catalog WHERE catalog = '$ip_catalog'"; 
	   $result = mysqli_query($con, $query) or die ( mysqli_error($link));
    	   if($catalog = mysqli_fetch_array($result)){
    	       $c_catalog_id=$catalog[0]; $c_catalog = $catalog[1];
    	                             echo "Found info: id " . $c_catalog_id . "&nbsp" . $c_catalog . "&nbsp";
    	   }else echo "This catalog name not catalog product" . "<br>";
    	   $row=array($c_catalog_id,$ip_category);
    	   return $row;
	}
	function checkProduct($con,$num){                                              //получаем id продуктов в массив
	    echo "num: ". $num;
	     $query = "SELECT catalog, category FROM t_input_product WHERE id = $num";
	    $result = mysqli_query($con, $query) or die ( mysqli_error($link));
	    $row=mysqli_fetch_array($result);
	    $ip_catalog = $row[0];   $ip_category = $row[1];
	                                echo "id: " . $ip_catalog .",". "&nbsp" . $ip_category .";". "&nbsp;";//"<br>"
	   return $row;                             
	}
	
	mysqli_close($con);
?>

