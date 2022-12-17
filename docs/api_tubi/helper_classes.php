<?php
	include 'variable.php';
    //include 'connect.php';
    include 'text.php';

    //активировать заказ для начала оформления документов
    //вычислить свободные запасы на складе
    //вычислить свободные запасы для продажи
    //вести описание товара в таблицу
    //найти counterparty_id
    //найти counterparty_id по user_id  
    //найти counterparty_id по product_inventory_id 
    //найти копию продукта, или создать новый и вернуть id  
    //найти дубликат продукта в t_product и получить product_id 
    //узнать цену для склада этого региона
    //получить адрес доставки покупателю
    //получить остаток товара на складе
    //получить весь приход товара на склад
    //получить весь расход товара со склада
    //получить все склады этого партнера
    //получить данные(информацию) по товару
    //получить данные(информацию) о складе и компании counterparty_id
    //получить данные(информацию) о компании
    //получаем данные авто в строке
    //получить склад хранения этого товара
    //получить стоимость обработки товара
    //получить стоимость доставки товара     
    //получаем описание товара в строке
    //получить ближайшую дату поставки из москвы
    //получить ближайшую дату поставки в городе
    //получить колличество в укомплектованных заказах
    //получить остаток товара на складе партнера
    //получить тип склада 
    //получить список покупателей из совм.заявок для одного мин заказа на этот товар
    //Проверить ограничения по сумме или весу всех заказов для этой даты
    //проверь собралось совм.заявок на создание заказа
    //данные о складе регион или москва   
    //найти user_id  
    //описание товара для документов создать или найти копию и вернуть id
    //сделать все вчерашние заказы для поставщиков активными
    //сложить этот продукт во всех заказах
    //список складов этого поставщика
    //список id поставщиков
    //первая буква заглавная
    // Возвращает сумму прописью 
    //рейтинг поставщика
    //найти открытые заказы на совм.заявку
    //найти описание id товара
    //создать заказ совместной закупки
    //получить стоимость процессов по товару для наценки
    //получить ближайшую дату доставки товара 
    //активировать заказ, изменить статус заказа (заказ отправлен в обработку) order_active = 1 и указать склад выдачи товара заказчику
    //создать кллюч к документам (invoice_key) и сделать записи в t_warehouse_inventory_in_out 
    //создать новый продукт
    //получить counterparty_id из warehouse_id
    //сгенерировать заказ от склад партнера для склад поставщика
    //проверить если этот user заказывает первый раз то передать заказ на контроль в "t_order_from_new_buyer"
    //проветить есть ли такой каталог в таблице нет =0; есть =1;
    //проветить есть ли такой категория в таблице нет =0; есть =1; 

    ////добавить новую категорию в таблицу
    //добавить новую product_name в таблицу
    //добавить новую brand в таблицу
    //добавить новую characteristic в таблицу
    //добавить новую type_packaging в таблицу
    //добавить новую unit_measure в таблицу
    //уменьшить количество товара в заказе поставщику
    //заменить инфу о том что товар не собран       //make_no_collect_product_partner
     

    //add_this_product_in_all_orders
    //add_category
    //add_product_name
    //add_brand
    //add_characteristic
    //add_type_packaging
    //add_unit_measure
    //calculateAvailableInventoryForSale
    //mb_ucfirst
    //sortArray
    //checkUserID
    //check_inventory_004
    //check_storage_warehouse
    //check_counterparty_id_by_user_id
    //checkJointBuyBuyersListForOneMinSale
    //checkOpenOrderForJointBuy
    //go_order_partner_activation
    //get_open_order_active_not_excecute_this_warehouse
    //get_delivery_date_intercity
    //get_delivery_date_city
    //get_document_num_for_invoice_key
    //get_provider_list
    //receiveCompanyInfo
    //delivery_product_for_warehouse
    //price_of_delivery_product
    //product_info
    //warehouseInfo
    //receive_product_description_docs_id
    //receive_product_info
    //receiveProductInfoShort
    //receiveCarInfoShort
    //receive_counterparty_warehouses
    //receiveDeliveryAddress
    //receive_warehouse_type
    //receive_counterpartyId_from_product_inventoryId
    //make_every_orders_partner_yesterday_active
    //make_new_product
    //make_invoice_key
    //stock_collect_to_order
    //stock_product_to_warehouse
    //searchCounterpartyId
    //searchOrMakeProduct
    //search_product_id
    //which_city_warehouse
    //provider_rating
    //price_of_processing_product
    //addJoinOrder
    //getProcessPriceForProduct
    //getFirstDateDeliveryProduct
    //chengeOrderActive_001
    //get_counterparty_id_from_warehouse_id
    //generation_order_to_provider
    //sendOrderToModeration
    //checkAndMakeOrderBuyTogether
    //catalogCheckHave
    //categoryCheckHave
    //descriptionIdSearch
    //writeDescriptionToTable
    //reduce_the_quantity_of_goods


//заменить инфу о том что товар не собран
function make_no_collect_product_partner($con, $order_partner_id, $warehouse_inventory_id){
    $query="UPDATE `t_warehouse_inventory_in_out` SET `collected`='0' 
                    WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
    mysqli_query($con,$query)or die (mysqli_error($con));

    $query="UPDATE `t_order_product_part` SET `collected`='0' 
                    WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
    mysqli_query($con,$query)or die (mysqli_error($con));

    $query="UPDATE `t_order_partner` SET `collected`='0' 
                    WHERE `order_partner_id`='$order_partner_id'";
    mysqli_query($con,$query)or die (mysqli_error($con));
}
//уменьшить количество товара в заказе поставщику 
function reduce_the_quantity_of_goods($con, $order_partner_id, $product_inventory_id, $reduce_quantity, $order_product_part_id){
    $query="SELECT `quantity` FROM `t_order_product_part` 
                                WHERE `order_product_part_id`='$order_product_part_id'";
    $result=mysqli_query($con,$query)or die (mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $quantity= $row[0];

    $quantity -= $reduce_quantity;

    //echo "quantity = $quantity <br>";

    $query="UPDATE `t_order_product_part` SET `quantity`='$quantity'
                    WHERE `order_product_part_id`='$order_product_part_id'";
    mysqli_query($con,$query)or die (mysqli_error($con));

}
//найти копию продукта, или создать новый и вернуть id 
function searchOrMakeProduct($con, $category_id, $product_name_id ,$brand_id
                            , $characteristic_id,$type_packaging_id ,$unit_measure_id
                            , $weight_volume, $storage_conditions){

    //найти дубликат продукта в t_product и получить product_id  
    $product_id = search_product_id($con, $category_id, $product_name_id
                        ,$brand_id, $characteristic_id,$type_packaging_id
                        , $unit_measure_id, $weight_volume, $storage_conditions); 
    if($product_id == 0){
        $product_id = make_new_product($con, $category_id, $product_name_id
                            ,$brand_id, $characteristic_id,$type_packaging_id
                            , $unit_measure_id, $weight_volume, $storage_conditions);
    }
    return $product_id;
}
//создать новый продукт
function make_new_product($con, $category_id, $product_name_id
                            ,$brand_id, $characteristic_id,$type_packaging_id
                            , $unit_measure_id, $weight_volume, $storage_conditions){
    echo 'make_new_product()' .'<br>';
    $query= "INSERT INTO `t_product` 
            (`category_id`, `product_name_id`, `brand_id`, `characteristic_id`, `type_packaging_id`
            , `unit_measure_id`, `weight_volume`, `storage_conditions`)
    VALUES ('$category_id', '$product_name_id', '$brand_id', '$characteristic_id', '$type_packaging_id'
            , '$unit_measure_id','$weight_volume', '$storage_conditions')";
    $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
    $product_id = mysqli_insert_id($con);    
    echo "make_new_product() / product_id = $product_id <br>";
    return $product_id;
}
//проветить есть ли такой категория в таблице нет =0; есть =1; 
function categoryCheckHave($con,$category){ 
    $query= "SELECT category FROM t_category WHERE category = '$category'";
    $result= mysqli_query($con, $query) or die (mysql_error($link));
    $row=mysqli_fetch_array($result);
    if($row){ $res=1;
    }else { $res = 0;}
    return $res;
}
//проветить есть ли такой каталог в таблице нет =0; есть =1;     
function catalogCheckHave($con,$catalog){  
    $query= "SELECT catalog FROM t_catalog WHERE catalog = '$catalog'";
    $result= mysqli_query($con, $query) or die (mysql_error($link));
    $row=mysqli_fetch_array($result);
    if($row){ $res=1;
    }else { $res = 0;}
    return $res;
}
//вести описание товара в таблицу
function  writeDescriptionToTable($con, $description){ //-----записать описание в таблицу
    $query = "INSERT INTO `t_description` (`description`) VALUES ('$description')";
    $result = mysqli_query($con, $query) or die (mysql_error($link));
     if($result){
     } else {
           echo "Error: " . $query . "<br>" . mysqli_error($con);
     }
}
//найти описание id товара
function descriptionIdSearch($con,$description){  //найти description_id в таблице нет =0; есть = передать id;
    $query= "SELECT `description_id` FROM `t_description` WHERE `description` = '$description'";
    $result= mysqli_query($con, $query) or die (mysql_error($link));
    $row=mysqli_fetch_array($result);
    if($row){ $res=$row[0];
    }else { $res = 0;}
    return $res;
}
//найти дубликат продукта в t_product и получить product_id  
function  search_product_id($con, $category_id, $product_name_id, $brand_id, $characteristic_id, 
    $type_packaging_id, $unit_measure_id, $weight_volume, $storage_conditions){        
    $query_product="SELECT `product_id` FROM `t_product` WHERE `category_id`='$category_id' AND `product_name_id`='$product_name_id'
                        AND `brand_id`='$brand_id' AND `characteristic_id`='$characteristic_id' 
                        AND `type_packaging_id`='$type_packaging_id' AND `unit_measure_id`='$unit_measure_id'
                        AND `weight_volume`='$weight_volume' AND `storage_conditions`='$storage_conditions'";
    $result_product=mysqli_query($con,$query_product) or die (mysqli_error($con));
    if($row_product=mysqli_fetch_array($result_product)){  
        $product_id_exists = $row_product[0];  
    }else{     
        $product_id_exists = 0;           
    }
    echo "search_product_id() product_id = $product_id_exists <br>";
    return $product_id_exists;
}
function get_unit_measure_id($con, $unit_measure){
    $query="SELECT `unit_measure_id` FROM `t_unit_measure` WHERE `unit_measure`='$unit_measure'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);        
    $unit_measure_id = $row[0];
    return $unit_measure_id;
}
function get_type_packaging_id($con, $type_packaging){
    $query="SELECT `type_packaging_id` FROM `t_type_packaging` WHERE `type_packaging`='$type_packaging'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);        
    $type_packaging_id = $row[0];
    return $type_packaging_id;
}
function get_characteristic_id($con, $characteristic){
    $query="SELECT `characteristic_id` FROM `t_characteristic` WHERE `characteristic`='$characteristic'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);        
    $characteristic_id = $row[0];
    return $characteristic_id;
}
function get_brand_id($con, $brand){
    $query="SELECT `brand_id` FROM `t_brand` WHERE `brand`='$brand'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);        
    $brand_id = $row[0];
    return $brand_id;
}
function get_product_name_id($con, $product_name){
    $query="SELECT `product_name_id` FROM `t_product_name` WHERE `product_name`='$product_name'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);        
    $product_name_id = $row[0];
    return $product_name_id;
}
//получить category_id
//$category_id = getCategory_id($con, $category);
function getCategory_id($con, $category){
    $query="SELECT `category_id` FROM `t_category` WHERE `category`='$category'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);        
    $category_id = $row[0];
    return $category_id;
}    
 //добавить новую unit_measure в таблицу
 function add_unit_measure($con,$unit_measure){
    $unit_measure = mb_strtolower($unit_measure);
     $query = "SELECT unit_measure FROM t_unit_measure WHERE unit_measure = '$unit_measure'";
    $result = mysqli_query($con, $query) or die (mysql_error($link));
    $row = mysqli_fetch_array($result);
    if($row[0]){
        echo "this unit_measure exists <br>";
    }else {
        $query = "INSERT INTO t_unit_measure (unit_measure) VALUES ('$unit_measure')";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($result){
                 echo "add unit_measure<br>";
             }else echo "error" . "&nbsp" . "Error add unit_measure<br>";
    }
}
//добавить новую type_packaging в таблицу
function add_type_packaging($con, $type_packaging){
    $type_packaging = mb_strtolower($type_packaging);
    $query = "SELECT type_packaging FROM t_type_packaging WHERE type_packaging = '$type_packaging'";
    $result = mysqli_query($con, $query) or die (mysql_error($link));
    $row = mysqli_fetch_array($result);
    if($row[0]){
        echo "this type_packaging exists<br>";
    }else {
        $query = "INSERT INTO t_type_packaging (type_packaging) VALUES ('$type_packaging')";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($result){
                 echo "add type_packaging<br><br>";
             }else echo "Error add type_packaging<br>";
    }
}
//добавить новую characteristic в таблицу
function add_characteristic($con, $characteristic){
    $characteristic = mb_strtolower($characteristic);
    $query = "SELECT characteristic FROM t_characteristic WHERE characteristic = '$characteristic'";
    $result = mysqli_query($con, $query) or die (mysql_error($link));
    $row = mysqli_fetch_array($result);
    if($row[0]){
        echo "this characteristic exists<br>";
    }else {
        $query = "INSERT INTO t_characteristic (characteristic) VALUES ('$characteristic')";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($result){
                 echo "add characteristic<br>";
             }else echo "Error add characteristic<br>";
    }
}
//добавить новую brand в таблицу
function add_brand($con, $brand){
    $brand = mb_strtolower($brand);
    $query = "SELECT brand FROM t_brand WHERE brand = '$brand'";
    $result = mysqli_query($con, $query) or die (mysql_error($link));
    //$row = mysqli_fetch_array($result);
    if(mysqli_num_rows($result) > 0){
        echo "this brand exists <br>";
    }else {
        $query = "INSERT INTO t_brand (brand) VALUES ('$brand')";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($result){
                    echo "add brand<br>";
                }else echo "Error add brand<br>";
    }
}
//добавить новую product_name в таблицу
function add_product_name($con, $product_name){
    $product_name = mb_strtolower($product_name);
    $query = "SELECT `product_name` FROM `t_product_name` WHERE `product_name`='$product_name'";
    $result = mysqli_query($con, $query) or die (mysql_error($link));
    $row = mysqli_fetch_array($result);
    if($row[0]){
        echo "this product name exists<br>";
    }else {
        $query = "INSERT INTO `t_product_name`( `product_name`) VALUES ('$product_name')";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($result){
                echo "add product name<br>";
            }else echo "Error add product name<br>";
    }
}
//добавить новую категорию в таблицу
function add_category($con, $catalog, $category){
    $catalog = mb_strtolower($catalog);
    $category = mb_strtolower($category);
   //проверить на категорию дубликат 
   $query = "SELECT category FROM t_category WHERE category = '$category'";
   $result = mysqli_query($con, $query) or die (mysql_error($link));
   $row = mysqli_fetch_array($result);
   if($row[0]){
       echo "эта категория существует";
   }else {
       //получить id каталога для записи в таблицу категории
       $guery_catalog_id = "SELECT catalog_id FROM t_catalog WHERE catalog = '$catalog'";
       $result_catalog_id = mysqli_query($con, $guery_catalog_id) or die (mysql_error($link));
       if($row_catalog_id = mysqli_fetch_array($result_catalog_id)){
            $catalog_id = $row_catalog_id[0];
            
            $query = "INSERT INTO `t_category` (`category`, `catalog_id`) VALUES ('$category', '$catalog_id')";
            $result = mysqli_query($con, $query) or die (mysql_error($link));
            if($result){
                     echo "was add category<br>";
            }else echo "Error add category<br>";
       }
   }
}
    //проверить если этот user заказывает первый раз то передать заказ на контроль в "t_order_from_new_buyer"
function sendOrderToModeration($con, $order_id){
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
}
//получить данные о заказе покупателя
function getBuyerOrderDate($con, $order_id){
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
    $user_order_date = ['partner_warehouse_id' => $partner_warehouse_id
                        , 'get_order_date_millis'=>$get_order_date_millis
                        ,'category_in_order'=>$category_in_order
                        ,'partner_counterparty_id'=>$partner_counterparty_id];
    return $user_order_date;
}

//получить товары в заказе покупателя
function get_product_from_buyer_order($con, $order_id, $partner_counterparty_id, $partner_warehouse_id
                                                            , $get_order_date_millis, $category_in_order){
    $product_info_list = [];
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
    return $product_info_list;
}

   //сгенерировать заказ от склад партнера для склад поставщика
   function generation_order_to_provider($con, $order_id){
    //получить данные о заказе покупателя
    $user_order_date = getBuyerOrderDate($con, $order_id);
    $partner_warehouse_id = $user_order_date['partner_warehouse_id'];
    $get_order_date_millis = $user_order_date['get_order_date_millis'];
    $category_in_order = $user_order_date['category_in_order'];
    $partner_counterparty_id = $user_order_date['partner_counterparty_id'];
   /* $query="SELECT ord.warehouse_id,
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
    $partner_counterparty_id= $row[3];*/

    //получить товары в заказе покупателя
    $product_info_list = get_product_from_buyer_order($con, $order_id, $partner_counterparty_id, $partner_warehouse_id
                                                    , $get_order_date_millis, $category_in_order);
   /* $query="SELECT `order_product_id`,`product_inventory_id`, `quantity`, `price`, `price_process`,`provider_war_id`,`description_docs_id`
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
                    
    }*/
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
           // echo "test 1 <br>";
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
}
//создать кллюч к документам (invoice_key) и сделать записи в t_warehouse_inventory_in_out 
function make_invoice_key($con, $order_id, $out_warehouse_id, $in_counterparty_id){
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
}
 //активировать заказ, изменить статус заказа (заказ отправлен в обработку) order_active = 1 и указать склад выдачи товара заказчику
 function chengeOrderActive_001($con,$order_id, $warehouse_id,  $counterparty_id){//$getOrderMillis,
    //$get_order_date = date('Y-m-d H:i:s', $getOrderMillis / 1000);

    $query="UPDATE `t_order` SET `order_active` = '1'WHERE `order_id` = '$order_id'";
                 //, `warehouse_id`='$warehouse_id',`counterparty_id`='$counterparty_id', `get_order_date`='$get_order_date'
    $result = mysqli_query($con, $query) or die (mysqli_error($con));            
    return $result;
} 
//проверь собралось совм.заявок на создание заказа
function checkJointBuyRequest($con, $partner_warehouse_id, $product_inventory_id,$user_id,$counterparty_id){
    $quantity=0;
    $query="SELECT `joint_buy_id`, `quantity` FROM `t_joint_buy` WHERE `active`='0' 
            and `product_inventory_id`='$product_inventory_id' and `partner_warehouse_id`='$partner_warehouse_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
    while($row = mysqli_fetch_array($result)){ 
        $joint_buy_id = $row[0];
        $quantity += $row[1];
    }
    //получить данные(информацию) по товару
    $product_list = receive_product_info($con, $product_inventory_id);      
    $min_sell=$product_list['min_sell'];
     
    if($min_sell <= $quantity){    
        //echo "true <br>";   
        return true;
    }else{
        //echo "false <br>"; 
        return false;
    }
}

function checkAndMakeOrderBuyTogether($con, $partner_warehouse_id, $product_inventory_id,$user_id,$counterparty_id){
    $main_warehouse = $GLOBALS['main_warehouse']; 
    //получить данные(информацию) по товару
    //echo "hello <br>"; 
    //проверить наличие товара        
    //получить расположение склада 
    $warehouseInfoList = warehouseInfo($con,$partner_warehouse_id);
    $in_region = $warehouseInfoList['region'];
    $in_district = $warehouseInfoList['district'];
    $in_city = $warehouseInfoList['city'];
    //echo "partner_warehouse_id=$partner_warehouse_id in_city=$in_city<br>"; 
    //получить склад хранения этого товара 
    $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $in_city,$main_warehouse);
    //echo "provid_warehouse_id=$provid_warehouse_id <br>"; 
    //получить расположение склада 
  /*  $warehouseInfoList = warehouseInfo($con,$provid_warehouse_id);
    $out_region = $warehouseInfoList['region'];
    $out_district = $warehouseInfoList['district'];
    $out_city = $warehouseInfoList['city'];*/
    //-вычислить свободные запасы на складе ( кроме заказа из запроса)
    $orders_id_list=[];
    $free_inventory = check_inventory_004($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id); 
    //получить данные(информацию) по товару
    $product_list = receive_product_info($con, $product_inventory_id); 
    $min_sell=$product_list['min_sell'];
    //echo "free_inventory=$free_inventory min_sell=$min_sell <br>"; 
    if($free_inventory >= $min_sell){
        //echo "hello 2 <br>"; 
        //получить ближайшую дату доставки товара 
        $date_of_sale_mil=getFirstDateDeliveryProduct($con, $product_inventory_id, $main_warehouse,$partner_warehouse_id);
        //получить ближайшую дату доставки
       /* $date_of_sale_mil=0;
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
        }else{
            //получить id города
            $city_id=get_city_id($con, $in_city);
            //получить ближайшую дату поставки в городе
            $delivery_time_millis=get_delivery_date_city($con, $city_id);
            $date_of_sale_mil= $delivery_time_millis;
        } */
        //если поставки товара на склад не найдена дата то не создавать заказ
       // if($date_of_sale_mil == 0){
        //    echo "message"."&nbsp".$GLOBALS['no_delivery'];
       // }else{            
        //получить список покупателей из совм.заявок для одного мин заказа на этот товар
        $jointBuyersList=checkJointBuyBuyersListForOneMinSale($con, $partner_warehouse_id, $product_inventory_id);
            //echo " counterparty_id=$counterparty_id <br>";
        //найти открытые заказы на совм.заявку or открыть заказ для совм.заупки
        $jointOrdersList=checkOpenOrderForJointBuy($con, $jointBuyersList, $date_of_sale_mil,$user_id,$counterparty_id,$partner_warehouse_id);
        //получить данные(информацию) по товару
        $product_list = receive_product_info($con, $product_inventory_id);    
        $price=$product_list['price']; 

        //получить стоимость процессов по товару для наценки
        $process_price=getProcessPriceForProduct($con, $product_inventory_id, $partner_warehouse_id, $provid_warehouse_id, $price);
        //описание товара для документов создать или найти копию и вернуть id
        $description_docs_id = receive_product_description_docs_id($con, $product_inventory_id);                    

        foreach($jointOrdersList as $k => $jointOrder){
            $joint_buy_id = $jointOrder['joint_buy_id'];
            $quantity = $jointOrder['quantity'];
            $counterparty_id = $jointOrder['counterparty_id'];
            $order_id = $jointOrder['order_id'];
            //внести товар в заказ
            $order_product_id =addProductInJointOrder($con, $product_inventory_id, $quantity, $price, $process_price, $order_id
                                                        , $provid_warehouse_id,$partner_warehouse_id,$description_docs_id);

            //указать в табле t_joint_buy, order_product_id заказа, and `active`='1'
            $query="UPDATE `t_joint_buy` SET `active`='1',`order_id`='$order_id',`order_product_id`='$order_product_id'
                    WHERE `joint_buy_id`='$joint_buy_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }        
        //}
    }    
}    
//получить ближайшую дату доставки товара 
//$date_of_sale_mil=getFirstDateDeliveryProduct($con, $product_inventory_id, $main_warehouse,$partner_warehouse_id);
function getFirstDateDeliveryProduct($con, $product_inventory_id, $main_warehouse,$partner_warehouse_id){
    //получить расположение склада 
    $warehouseInfoList = warehouseInfo($con,$partner_warehouse_id);
    $in_region = $warehouseInfoList['region'];
    $in_district = $warehouseInfoList['district'];
    $in_city = $warehouseInfoList['city'];
    //получить склад хранения этого товара 
    $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $in_city,$main_warehouse);
    //echo "provid_warehouse_id=$provid_warehouse_id <br>"; 
    //получить расположение склада 
    $warehouseInfoList = warehouseInfo($con,$provid_warehouse_id);
    $out_region = $warehouseInfoList['region'];
    $out_district = $warehouseInfoList['district'];
    $out_city = $warehouseInfoList['city'];
    //получить ближайшую дату доставки
    $date_of_sale_mil=0;
    $delivery_time_millis=0;
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
    }else{
        //получить id города
        $city_id=get_city_id($con, $in_city);
        //получить ближайшую дату поставки в городе
        $delivery_time_millis=get_delivery_date_city($con, $city_id);
        $date_of_sale_mil= $delivery_time_millis;
    }
    return $date_of_sale_mil;
}
//внести товар в заказ
function addProductInJointOrder($con, $product_inventory_id, $quantity, $price, $process_price, $order_id
                                    , $provid_warehouse_id,$partner_warehouse_id, $description_docs_id){  
    
    //вносим данные в t_order_product
    $query = "INSERT INTO `t_order_product` 
            (`product_inventory_id`, `quantity`, `price`, `price_process`, `order_id`, `provider_war_id`, `description_docs_id`) 
    VALUES ('$product_inventory_id', '$quantity', '$price', '$process_price', '$order_id', '$provid_warehouse_id', '$description_docs_id')";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $order_product_id  = mysqli_insert_id($con);
    return $order_product_id ;
}
//получить стоимость процессов по товару для наценки
function getProcessPriceForProduct($con, $product_inventory_id, $partner_warehouse_id, $provid_warehouse_id,$price){
    //получить расположение склада 
    $warehouseInfoList = warehouseInfo($con,$partner_warehouse_id);
    $in_region = $warehouseInfoList['region'];
    $in_district = $warehouseInfoList['district'];
    $in_city = $warehouseInfoList['city'];
    //получить расположение склада 
    $warehouseInfoList = warehouseInfo($con,$provid_warehouse_id);
    $out_region = $warehouseInfoList['region'];
    $out_district = $warehouseInfoList['district'];
    $out_city = $warehouseInfoList['city'];


    if($in_region != $out_region){ 
        //получить стоимость доставки товара
        $price_of_delivery_product = 
            price_of_delivery_product($con,$product_inventory_id,$out_city, $in_city);
        //получить стоимость обработки товара
        $price_of_processing_product = 
            price_of_processing_product($con,$product_inventory_id,$out_city, $in_city);
        //добавить и отнять доп расходы  
        $tubi_commission = ($price * $GLOBALS['tubi_commission_percent']) - $price ;
        $process_price = $tubi_commission + $price_of_delivery_product + $price_of_processing_product;
        //$process_price = round($process_price, 2);//округлить*/
       
    }else{
        $process_price = 0;
        $tubi_commission = 0;                    
        $warehouse_processing_commission = 0;
        $delivery_in_moscow_commission = 0;            
                 
        $tubi_commission = ($price * $GLOBALS['tubi_commission_percent']) - $price ;
        $warehouse_processing_commission = ($price * $GLOBALS['warehouse_processing_percent']) - $price ;
        //if($delivery == 1){//доставка есть
       //     $delivery_in_moscow_commission = ($price * $GLOBALS['delivery_in_moscow_percent']) - $price ;
        //}                 
        //добавить доп расходы 
        $process_price = $tubi_commission + $warehouse_processing_commission + $delivery_in_moscow_commission;
        //$process_price = round($process_price, 2);//округлить
    }
    $process_price = round($process_price, 2);//округлить
    return $process_price;
}
//найти открытые заказы на совм.заявку
function checkOpenOrderForJointBuy($con, $jointBuyersList, $date_of_sale_mil,$user_id,$counterparty_id,$partner_warehouse_id){
    $jointOrdersList=[];
    foreach($jointBuyersList as $k => $buyer){
        $joint_buy_id = $buyer['joint_buy_id'];
        $quantity = $buyer['quantity'];
        $counterparty_id = $buyer['counterparty_id'];
        $query="SELECT `order_id` FROM `t_order` WHERE `order_active`='0' 
                    and `counterparty_id`='$counterparty_id' and `joint_buy`='1'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            $order_id = $row[0];
        }else{
            //создать заказ совместной закупки
            $category='все'; $delivery='0';
            $order_id=addJoinOrder($con, $user_id, $counterparty_id,  $date_of_sale_mil, $category, $delivery,$partner_warehouse_id);
        }
        $jointOrdersList[]=['joint_buy_id'=>$joint_buy_id,'quantity'=>$quantity,'counterparty_id'=>$counterparty_id,'order_id'=>$order_id];
    }
    //echo "hello 4<br>"; 
    return $jointOrdersList;
}
//создать заказ совместной закупки
function addJoinOrder($con, $user_id, $counterparty_id, $dateOfSaleMillis, $category, $delivery,$partner_warehouse_id){
  /*  //проверить нет блокировки для создания заказов у компании
    $query="SELECT `block_order` FROM `t_counterparty` WHERE `counterparty_id`='$counterparty_id'";
    $result = mysqli_query($con, $query) or die (mysql_error($con));  
    $row = mysqli_fetch_array($result);
    $block_order=$row[0];
    if($block_order == 0){
        //компания не может делать заказы из-за блокировки
        echo "message"."&nbsp".$GLOBALS['block_order_info'];
        return;
    }*/

    $query = "INSERT INTO `t_order` 
            (`user_id` , `counterparty_id`, `warehouse_id`     ,`get_order_date_millis`, `category_in_order`,`delivery`,`joint_buy`) 
    VALUES ('$user_id', '$counterparty_id','$partner_warehouse_id','$dateOfSaleMillis',   '$category'    ,  '$delivery' ,   '1')";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $order_id = mysqli_insert_id($con);
            
    return $order_id;
}
//получить список покупателей из совм.заявок для одного мин заказа на этот товар
    //$jointBuyersList=checkJointBuyBuyersListForOneMinSale($con, $partner_warehouse_id, $product_inventory_id);
function checkJointBuyBuyersListForOneMinSale($con, $partner_warehouse_id, $product_inventory_id){
    $jointBuyersList=[];
    $quantity_full=0;
    //получить данные(информацию) по товару
    $product_list = receive_product_info($con, $product_inventory_id);      
    $min_sell=$product_list['min_sell'];

    $query="SELECT `joint_buy_id`, `quantity`,`counterparty_id` FROM `t_joint_buy` WHERE `active`='0' 
            and `product_inventory_id`='$product_inventory_id' and `partner_warehouse_id`='$partner_warehouse_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
    while($row = mysqli_fetch_array($result)){ 
        $joint_buy_id = $row[0];
        $quantity = $row[1];
        $counterparty_id = $row[2];

        $quantity_full += $quantity;
        
        if($quantity_full <= $min_sell){
            $jointBuyersList[] = ['joint_buy_id'=>$joint_buy_id,'quantity'=>$quantity,'counterparty_id'=>$counterparty_id];
        }else if($quantity_full > $min_sell){  
            break;
        }
    }   
    //echo "hello 3<br>"; 
    return $jointBuyersList; 
}

/*
function checkAndMakeOrderBuyTogether_two($con, $partner_warehouse_id, $product_inventory_id,$user_id,$counterparty_id){
    $main_warehouse = $GLOBALS['main_warehouse']; 
    //получить список совместных заказов и сверить с минимальным заказом
    //получить данные(информацию) по товару
    $product_list = receive_product_info($con, $product_inventory_id);  
    $min_sell=$product_list['min_sell'];
    $quantity=0;
    $query="SELECT `quantity` FROM `t_joint_buy` WHERE `exequted`='0' and `join_buy_delete`='0' `closed`='0' 
                                    and `product_inventory_id`='$product_inventory_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
    while($row = mysqli_fetch_array($result)){ 
        $quantity += $row[0];
    }
    if($quantity >= $min_sell){
        //проверить наличие товара        
        //получить расположение склада 
        $warehouseInfoList = warehouseInfo($con,$partner_warehouse_id);
        $in_region = $warehouseInfoList['region'];
        $in_district = $warehouseInfoList['district'];
        $in_city = $warehouseInfoList['city'];
        //получить склад хранения этого товара 
        $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $in_city,$main_warehouse);
        //получить расположение склада 
        $warehouseInfoList = warehouseInfo($con,$provid_warehouse_id);
        $out_region = $warehouseInfoList['region'];
        $out_district = $warehouseInfoList['district'];
        $out_city = $warehouseInfoList['city'];
        //-вычислить свободные запасы на складе ( кроме заказа из запроса)
        $orders_id_list=[];
        $free_inventory = check_inventory_004($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id); 
        if($free_inventory >= $min_sell){
            //получить ближайшую дату доставки
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
            }else{
                //получить ближайшую дату поставки в городе
                $delivery_time_millis=get_delivery_date_city($con, $city_id);
                $date_of_sale_mil= $delivery_time_millis;
            } 
            //найти открытый заказ или создать
        // получить все активные заказы $order_list
        $order_list=search_my_active_orders_list($con, $user_id, $counterparty_id);
        if($order_list){
            //создать заказ
            $category="все";
            $delivery='0';
            $order_id=addMyOrder001($con, $user_id, $counterparty_id
                        , $partner_warehouse_id, $date_of_sale_mil, $category, $delivery);
        }else{
            //найти заказ на дату 
            foreach($order_list as $k => $order){
                $order_id=$order['order_id'];
                $date_millis=$order['date_millis'];
                $category=$order['category'];
                $delivery=$order['delivery'];
                //проверить дату в заказе
                $equals_flag=check_equals_millis($date_of_sale_mil,$date_millis);
                if($equals_flag)break;
            }

        }
           
            
        }    
    }

    //внести товар в заказ

    //отметить товары в таблице как заказано


} */

//проверить дату в заказе
function check_equals_millis($date_millis_one,$date_millis_two){
    $equals_flag=false;
    $date = explode('-', explode(' ', $date_millis_one)[0]); 
    $day_one = $date[0];
    $month_one = $date[1];
    $year_one = $date[2];

    $date = explode('-', explode(' ', $date_millis_two)[0]); 
    $day_two = $date[0];
    $month_two = $date[1];
    $year_two = $date[2];
    if($day_one==$day_two && $month_one==$month_two && $year_one==$year_two){
        $equals_flag=true;
    }
    return $equals_flag;
}
/*
//рейтинг поставщика
$providerRatingInfoList = provider_rating($con, $counterparty_id);    
$inventory_data=$providerRatingInfoList['inventory_data'];
$under_delivery=$providerRatingInfoList['under_delivery'];
*/
//рейтинг поставщика
function provider_rating($con, $counterparty_id){
    $query="SELECT `inventory_data`, `under_delivery` FROM `t_counterparty` 
            WHERE `counterparty_id`='$counterparty_id'";
    $result=mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);
    $inventory_data=$row[0];
    $under_delivery=$row[1];

    $providerRatingInfoList = array('inventory_data' => $inventory_data,'under_delivery' => $under_delivery);        

    return $providerRatingInfoList;
}

//найти counterparty_id по product_inventory_id
//$counterparty_id = receive_counterpartyId_from_product_inventoryId($con, $product_inventory_id);
function receive_counterpartyId_from_product_inventoryId($con, $product_inventory_id){
    $query="SELECT `counterparty_id` FROM `t_product_inventory` 
                WHERE `product_inventory_id`='$product_inventory_id'";
    $result=mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);
    $counterparty_id=$row[0];
    return $counterparty_id;
}
//получить тип склада 
//$warehouse_type = receive_warehouse_type($con, $warehouse_id);
function receive_warehouse_type($con, $warehouse_id){
    $warehouse_type = "";
    $query="SELECT `warehouse_type` FROM `t_warehous` WHERE `warehouse_id`='$warehouse_id'";
    $result=mysqli_query($con, $query) or die (mysqli_error($con));
    $row = mysqli_fetch_array($result);
    $warehouse_type=$row[0];
    return $warehouse_type;
}
    // Возвращает сумму прописью    
   function num2str($num) {
       $nul='ноль';
       $ten=array(
           array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
           array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
       );
       $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
       $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
       $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
       $unit=array( // Units
           array('копейка' ,'копейки' ,'копеек',	 1),
           array('рубль'   ,'рубля'   ,'рублей'    ,0),
           array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
           array('миллион' ,'миллиона','миллионов' ,0),
           array('миллиард','милиарда','миллиардов',0),
       );
       //
       list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
       $out = array();
       if (intval($rub)>0) {
           foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
               if (!intval($v)) continue;
               $uk = sizeof($unit)-$uk-1; // unit key
               $gender = $unit[$uk][3];
               list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
               // mega-logic
               $out[] = $hundred[$i1]; # 1xx-9xx
               if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
               else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
               // units without rub & kop
               if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
           } //foreach
       }
       else $out[] = $nul;
       $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
       $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
       return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
   }   
    //Склоняем словоформу    
   function morph($n, $f1, $f2, $f5) {
       $n = abs(intval($n)) % 100;
       if ($n>10 && $n<20) return $f5;
       $n = $n % 10;
       if ($n>1 && $n<5) return $f2;
       if ($n==1) return $f1;
       return $f5;
   }
    //список id поставщиков
    //$counterparty_id_list=get_provider_list($con);
    function get_provider_list($con){
        $counterparty_id_list = [];
        //получить все склады (поставщики)
        $query="SELECT win.counterparty_id
                        FROM t_warehous wt 
                            JOIN t_warehouse_info win ON win.warehouse_info_id = wt.warehouse_info_id
                        WHERE wt.warehouse_type = 'provider'";
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $counterparty_id=$row[0];
            
            $counterparty_id_list[] = $counterparty_id;                                      

        }
        return $counterparty_id_list;
    }

    //сложить этот продукт во всех заказах( кроме заказа из запроса)
    //$quantity_order = add_this_product_in_all_orders($con, $product_inventory_id);
    function add_this_product_in_all_orders($con, $product_inventory_id, $orders_id_list){
        $quantity_order = 0;
        $query_order = "SELECT op.quantity, 
                                    o.executed
                                FROM t_order_product op                                
                                    JOIN t_order o ON o.order_id = op.order_id
                                WHERE op.product_inventory_id = '$product_inventory_id' 
                                    and op.order_prod_deleted = '0'";   
        $result_order = mysqli_query($con, $query_order) or die (mysql_error($link));   
        while($row_order = mysqli_fetch_array($result_order)){
            //сложить колличество этого товара в заказах
            $executed=$row_order[1];
            if($executed == 0){
                $quantity_order += $row_order[0];
            }                
            //echo "выполнен : $executed / ";
        }
        return $quantity_order;
    }
   
    //получить адрес доставки покупателю
    //$address_for_delivery = receiveDeliveryAddress($con, $order_id);
    function receiveDeliveryAddress($con, $order_id){
        $query="SELECT `address_for_delivery` FROM `t_order_for_delivery_to_buyer`  
                                WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $address_for_delivery=$row[0];

        return $address_for_delivery;
    }
     //вычислить свободные запасы на складе ( кроме заказа из запроса)
     function check_inventory_004($con, $product_inventory_id, $orders_id_list, $storage_warehouse){
        //получить свободный остаток на складе
       
               //получить колличество поставленного товара
               $query = "SELECT `quantity` FROM `t_warehouse_inventory_in_out` 
                                WHERE  `product_inventory_id`='$product_inventory_id' 
                                AND `in_warehouse_id` ='$storage_warehouse'";
               $result = mysqli_query($con, $query) or die (mysqli_error($con));
               $delivery_quantity = '0';
             
               while($row = mysqli_fetch_array($result)){
                   $delivery_quantity += $row[0];
                   //echo "`product_inventory_id 1 `". $product_inventory_id . ' quantity: ' .$row[0] . "<br>";
               }
   
               //получить колличество реализованного товара
               $query = "SELECT `quantity` FROM `t_warehouse_inventory_in_out` 
                                WHERE  `product_inventory_id`='$product_inventory_id'
                                AND `out_warehouse_id`='$storage_warehouse'";// IS NOT NULL";
               $result = mysqli_query($con, $query) or die (mysqli_error($con));
               $sold_quantity = '0';
               
               while($row = mysqli_fetch_array($result)){
                   $sold_quantity += $row[0];
                   //echo "`product_inventory_id 2 `". $product_inventory_id . ' quantity: ' .$row[0] . "<br>";
               }
               //подсчитать свободный остаток на складе            
               $product_have_to_warehouse = $delivery_quantity - $sold_quantity; 
               //echo " || $delivery_quantity - $sold_quantity = $product_have_to_warehouse<br>";
               //echo "`product_inventory_id  `". $product_inventory_id . ' quantity: ' .$product_have_to_warehouse . "<br>";
           
            //получить колличество товара в невыполненных заказах 
            $query = "SELECT op.quantity
                            FROM t_order ord
                                JOIN t_order_product op ON op.order_id = ord.order_id 
                                    AND op.product_inventory_id='$product_inventory_id' 
                                    AND op.order_prod_deleted = '0'
                            WHERE ord.executed='0'";
            $res_1 = mysqli_query($con, $query) or die (mysqli_error($con));
            $ordered_quantity = 0;
            while($row = mysqli_fetch_array($res_1)){
                $ordered_quantity += $row[0];
            }
            //получить колличество товара в заказах с которыми сейчас работаем
            $my_orders_quantity = 0;
            foreach($orders_id_list as $k => $order_id){
                $query = "SELECT op.quantity
                            FROM t_order ord
                                JOIN t_order_product op ON op.order_id = ord.order_id 
                                    AND op.product_inventory_id='$product_inventory_id' 
                                    AND op.order_prod_deleted = '0'
                            WHERE ord.order_id = '$order_id'";
                $res_1 = mysqli_query($con, $query) or die (mysqli_error($con));
                if(mysqli_num_rows($res_1) > 0){                
                    while($row = mysqli_fetch_array($res_1)){
                        $my_orders_quantity += $row[0];
                    }
                }
            }

            $ordered_quantity -= $my_orders_quantity;
            //вычислить свободный(и незабронированный) остаток
            $free_inventory = $product_have_to_warehouse - $ordered_quantity;
   
            return $free_inventory;
       }
    //получить номер документа по ключу 
    //$document_num = get_document_num_for_invoice_key($con, $invoice_key_id, $document_name);
    function get_document_num_for_invoice_key($con, $invoice_key_id, $document_name){
        $document_num=0;
        $query="SELECT `document_num` FROM `t_document_deal` 
                    WHERE `invoice_key_id`='$invoice_key_id' and document_name='$document_name'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_array($result);
            $document_num = $row[0];
        }
        return $document_num;
    }

    //список складов этого поставщика
    //$warehouse_info_list = receive_counterparty_warehouses($con, $partner_counterparty_id, $warehouse_type);
    /* foreach($warehouse_info_list as $k => $warehouse_info){
        $warehouse_info_id = $warehouse_info['warehouse_info_id'];
        $warehouse_id = $warehouse_info['warehouse_id'];
        $warehouse_type = $warehouse_info['warehouse_type'];
        $active = $warehouse_info['active'];
        echo "warehouse_info_id: ".$warehouse_info_id." warehouse_id: ".$warehouse_id." warehouse_type: ".$warehouse_type." active: ".$active." <br>";
    }*/
    function receive_counterparty_warehouses($con, $partner_counterparty_id, $warehouse_type){
        //$warehouse_list = [];
        $query="SELECT `warehouse_info_id` FROM `t_warehouse_info` WHERE `counterparty_id`='$partner_counterparty_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $warehouse_info_id = $row[0];
            
            $warehouse_info_id_list[] = $warehouse_info_id;
        } 

        if($warehouse_type == "all"){
            foreach($warehouse_info_id_list as $k => $warehouse_info_id){
               // echo "warehouse_info_id: $warehouse_info_id <br>";
                $query="SELECT `warehouse_id`, `warehouse_type`, `active` FROM `t_warehous` 
                            WHERE `warehouse_info_id`='$warehouse_info_id'";
                $result = mysqli_query($con, $query) or die (mysqli_error($con));
                while($row=mysqli_fetch_array($result)){
                    $warehouse_id = $row[0];
                    $warehouse_type = $row[1];
                    $active = $row[2];

                    //echo "warehouse_id: $warehouse_id warehouse_type: $warehouse_type <br>";
                    
                    $warehouse_info_list[] = ['warehouse_info_id'=>$warehouse_info_id,'warehouse_id'=>$warehouse_id,'warehouse_type'=>$warehouse_type,'active'=>$active,];
                } 
            }
            

        }else{
            foreach($warehouse_info_id_list as $k => $warehouse_info_id){
                //echo "warehouse_info_id: $warehouse_info_id <br>";
                $query="SELECT `warehouse_id`, `warehouse_type`, `active` FROM `t_warehous` 
                            WHERE `warehouse_info_id`='$warehouse_info_id' and `warehouse_type`='$warehouse_type'";
                $result = mysqli_query($con, $query) or die (mysqli_error($con));
                while($row=mysqli_fetch_array($result)){
                    $warehouse_id = $row[0];
                    $warehouse_type = $row[1];
                    $active = $row[2];

                   // echo "warehouse_id: $warehouse_id warehouse_type: $warehouse_type <br>";
                    
                    $warehouse_info_list[] = ['warehouse_info_id'=>$warehouse_info_id,'warehouse_id'=>$warehouse_id,'warehouse_type'=>$warehouse_type,'active'=>$active,];
                } 
            }
        } 
       /* foreach($warehouse_info_list as $k => $warehouse_info){
            $warehouse_info_id = $warehouse_info['warehouse_info_id'];
            $warehouse_id = $warehouse_info['warehouse_id'];
            $warehouse_type = $warehouse_info['warehouse_type'];
            $active = $warehouse_info['active'];
            echo "warehouse_info_id: ".$warehouse_info_id." warehouse_id: ".$warehouse_id." warehouse_type: ".$warehouse_type." active: ".$active." <br>";
        }*/
        
        

        return $warehouse_info_list;
    }
    
    
    //получаем данные авто в строке
    //$car_info = receiveCarInfoShort($con, $car_id);
    function receiveCarInfoShort($con, $car_id){
        $car_info="";
        $query="SELECT `car_brand`, `car_model`, `registration_num`
                    FROM `t_car` WHERE `car_id`='$car_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($result ){
            $row = mysqli_fetch_array($result);
            $car_brand=$row[0]; 
            $car_model=$row[1]; 
            $registration_num=$row[2];  
            
            $car_info = $car_brand." ".$car_model." ".$registration_num;
        }
        

        return $car_info;
    }

      //получаем описание товара в строке
      //$description_docs_price=receiveProductInfoShort($con, $product_inventory_id, $invoice_key_id);
    function receiveProductInfoShort($con, $product_inventory_id, $invoice_key_id){  
         /*
 $query = "SELECT `description_docs` FROM `t_description_docs`
                    WHERE `product_inventory_id`='$product_inventory_id'";
                */
        $query = "SELECT doc.description_docs,
                            inf.price                        
                        FROM t_invoice_info inf 
                            JOIN t_description_docs doc ON doc.description_docs_id = inf.description_docs_id
                        WHERE inf.invoice_key_id='$invoice_key_id' 
                            and inf.product_inventory_id='$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($result){
            $row = mysqli_fetch_array($result);
            $description_docs=$row[0];
            $price=$row[1];
        }else{
            $description_docs ="";
            $price="0";
        }

        $description_docs_price = array('description_docs'=> $description_docs, 'price'=>$price);
        
        return $description_docs_price;
    }

    //$data = sortArray( $data, 'counterparty_id' );
    //$data = sortArray( $data, array( 'lastname', 'firstname' ) );            
    function sortArray( $data, $field ) {
        $field = (array) $field;
        uasort( $data, function($a, $b) use($field) {
            $retval = 0;
            foreach( $field as $fieldname ) {
                if( $retval == 0 ) $retval = strnatcmp( $a[$fieldname], $b[$fieldname] );
            }
            return $retval;
        } );
        return $data;
    }

    //получить колличество в укомплектованных заказах
    function stock_collect_to_order($con,$providerWarehouse_id, $product_inventory_id){
        $query="SELECT `quantity` FROM `t_warehouse_inventory_in_out` 
                    WHERE `product_inventory_id`='$product_inventory_id' and `out_warehouse_id`='$providerWarehouse_id' 
                            and `transaction_name`='sale' and `collected`='1' and `out_active`='0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $stock_collect=0;
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $stock_collect += $row[0];
            }
        }

        return $stock_collect;
    }
     //получить counterparty_id из warehouse_id
     function get_counterparty_id_from_warehouse_id($con, $warehouse_id){
        $query="SELECT win.counterparty_id
                    FROM t_warehous w 
                        JOIN t_warehouse_info win ON win.warehouse_info_id=w.warehouse_info_id
                    WHERE w.warehouse_id='$warehouse_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $counterparty_id = $row[0];

        return $counterparty_id;
     }
    //сделать все вчерашние заказы для поставщиков активными
    function make_every_orders_partner_yesterday_active($con){
        $today = date("Y-m-d"); 
    
        $query="SELECT `order_partner_id`, `created_at` 
                    FROM `t_order_partner` WHERE `order_active`='0'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $order_partner_id= $row[0];
            $created_at= $row[1];
                        
            //разобрать и проверить дату заказа если дата вчерашняя то сделать активным
            $datetime = explode(" ",$created_at);
            $date = $datetime[0];
            /*$date_list = explode("-",$date);
            $year = $date_list[0];
            $month = $date_list[1];
            $day = $date_list[2];*/
            
            //дата вчерашняя, заказ сделать активным
            if($date != $today){

                //активировать заказ
                //объединить и внести все товары в t_warehouse_inventory
                $transaction_name = "sale"; $user_id = 0;
                go_order_partner_activation($con,$order_partner_id, $transaction_name, $user_id);
              
                 /*  echo "сделать заказ активным"."<br>";
                $query="UPDATE `t_order_partner` SET `order_active`='1'
                            WHERE `order_partner_id`='$order_partner_id'";
                mysqli_query($con,$query)or die (mysqli_error($con));*/
            }
                        
        }
    }
     //активировать заказ для начала оформления документов
    //объединить и внести все товары в t_warehouse_inventory
    function go_order_partner_activation($con,$order_partner_id, $transaction_name, $user_id){
       
        try{
            $query="SELECT   `out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`
                        FROM `t_order_partner` WHERE `order_partner_id`='$order_partner_id'";
            $result=mysqli_query($con, $query) or die (mysqli_error($con));
            $row = mysqli_fetch_array($result);
            $out_counterparty_id=$row[0];  
            $out_warehouse_id=$row[1];
            $in_counterparty_id=$row[2];  
            $in_warehouse_id=$row[3];

            //создать ключ для идентификации документов t_invoice_key
            $query="INSERT INTO `t_invoice_key`
                    (`out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`) 
            VALUES ('$out_counterparty_id','$out_warehouse_id','$in_counterparty_id','$in_warehouse_id')";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $invoice_key_id = mysqli_insert_id($con);

            $query="SELECT `order_product_part_id`, `product_inventory_id`, `quantity`, `price`, `price_process`, `description_docs_id` 
                        FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id'";
            $result=mysqli_query($con, $query) or die (mysqli_error($con));
            while($row = mysqli_fetch_array($result)){
                $order_product_part_id=$row[0];
                $product_inventory_id=$row[1];
                $quantity=$row[2];
                $price=$row[3];
                $price_process=$row[4];
                $description_docs_id=$row[5];

                
                $order_product_info_list[]=array('order_product_part_id'=>$order_product_part_id,'product_inventory_id'=>$product_inventory_id
                            ,'quantity'=>$quantity,'price'=>$price,'price_process'=>$price_process,'description_docs_id'=>$description_docs_id);                            

            }
            //сортировать по product_inventory_id
          
            
            $order_product_info_list = sortArray( $order_product_info_list, 'product_inventory_id' );
            //$data = sortArray( $data, array( 'lastname', 'firstname' ) );
            //$order_product_info_list=
            //usort($order_product_info_list, make_comparer('product_inventory_id'));

            //сложить колличество, сложить стоимость, вычеслить среднюю цену, записать в новый массив
            $product_inventory_id=0;
            $description_docs_id = 0;
            $quantity = 0;
            $price = 0;
            $summ=0;
            $count = 0;
            foreach($order_product_info_list as $k => $order_product){
                $my_product_inventory_id = $order_product['product_inventory_id'];
                if($count == 0){
                    $product_inventory_id = $my_product_inventory_id;
                    $description_docs_id = $order_product['description_docs_id'];
                }
                if($product_inventory_id == $my_product_inventory_id){
                    $quantity += $order_product['quantity'];
                    //для документов поставщика расходы по доставке и др не неужны
                    $price = $order_product['price'];// + $order_product['price_process'];
                    $summ += $order_product['quantity'] * $price;


                }else{       
                    $price = $summ / $quantity;
                    $product_info_list[]=array('product_inventory_id'=>$product_inventory_id
                    ,'quantity'=>$quantity,'price'=>$price,'description_docs_id'=>$description_docs_id);
                  
                    $quantity = 0;
                    $price = 0;
                    $summ = 0;

                    $quantity += $order_product['quantity'];
                    //для документов поставщика расходы по доставке и др не неужны
                    $price = $order_product['price'] ;//+ $order_product['price_process'];
                    $summ += $order_product['quantity'] * $price;
                    

                    $product_inventory_id = $my_product_inventory_id; 
                    $description_docs_id = $order_product['description_docs_id'];                 

                }
                $count++;
            }
            $price = $summ / $quantity;
            $product_info_list[]=array('product_inventory_id'=>$product_inventory_id
                    ,'quantity'=>$quantity,'price'=>$price,'description_docs_id'=>$description_docs_id);

            //сделать две записи  t_warehouse_inventory_in_out и t_invoice_info
            foreach($product_info_list as $k => $product_info){
                $product_inventory_id = $product_info['product_inventory_id'];
                $quantity = $product_info['quantity'];
                $price = $product_info['price'];
                $description_docs_id = $product_info['description_docs_id'];

                $query="INSERT INTO `t_warehouse_inventory_in_out`
                                ( `transaction_name`, `product_inventory_id`, `quantity`, `out_warehouse_id`
                                , `in_warehouse_id`, `creator_user_id`) 
                        VALUES ('$transaction_name','$product_inventory_id','$quantity','$out_warehouse_id'
                                ,'$in_warehouse_id','$user_id')";
                mysqli_query($con, $query) or die (mysqli_error($con));
                $warehouse_inventory_id = mysqli_insert_id($con);

                $query="INSERT INTO `t_invoice_info`
                                    (`warehouse_inventory_id`, `product_inventory_id`, `quantity`, `price`, `description_docs_id`,`description_for_doc`) 
                            VALUES ('$warehouse_inventory_id','$product_inventory_id','$quantity','$price','$description_docs_id','$description_docs_id')";
                mysqli_query($con, $query) or die (mysqli_error($con));
                $invoice_info_id = mysqli_insert_id($con);

                $query="UPDATE `t_warehouse_inventory_in_out` SET `invoice_info_id`='$invoice_info_id' 
                                WHERE `warehouse_inventory_id`='$warehouse_inventory_id'";
                mysqli_query($con, $query) or die (mysqli_error($con));

                //добавить $warehouse_inventory_id в t_order_product_part
                foreach($order_product_info_list as $k => $order_product){
                    $my_product_inventory_id = $order_product['product_inventory_id'];
                    $order_product_part_id = $order_product['order_product_part_id'];
                    if($my_product_inventory_id == $product_inventory_id){
                        $query="UPDATE `t_order_product_part` SET `warehouse_inventory_id`='$warehouse_inventory_id'
                                    WHERE `order_product_part_id`='$order_product_part_id'";
                        mysqli_query($con, $query) or die (mysqli_error($con));
                    }
                }
            }
                //`invoice_key_id`='$invoice_key_id',
            $query="UPDATE `t_order_partner` SET `order_active`='1'
                        WHERE `order_partner_id`='$order_partner_id'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            if($result){
                echo "RESULT_OK";
            }else{
                echo "messege"."&nbsp". $GLOBALS['error_try_again_later_text'];
            }

        }catch(Exception $e){
            echo "messege"."&nbsp". $GLOBALS['error_try_again_later_text'];
        }
    }
    //описание товара для документов создать или найти копию и вернуть id
    function receive_product_description_docs_id($con, $product_inventory_id){
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
       $price=$product_list['price'];
       $description=$product_list['description'];

       //собрать строку описания товара 
       $product_description_for_docs =  $description;    
       
       /*$product_description_for_docs = $category." ".$product_name." ".$characteristic." "
                   .$brand.", ".$weight_volume." ".$unit_measure." ".$quantity_package." "
                   .$GLOBALS['quantity_in_package'];*/

        //найти по product_inventory_id => description_docs_id
       $query="SELECT `description_docs_id` FROM `t_description_docs` 
                    WHERE `product_inventory_id`='$product_inventory_id' 
                        and `description_docs`='$product_description_for_docs'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            $description_docs_id=$row[0];
        }//если нет записи создать и венуть id
        else{
            $query="INSERT INTO `t_description_docs` (`description_docs`, `product_inventory_id`) 
                                     VALUES ('$product_description_for_docs','$product_inventory_id')";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));
            $description_docs_id = mysqli_insert_id($con);
        }

       return $description_docs_id;
   }

    //описание товара для документов
    /*function receive_product_description_for_docs($con, $product_inventory_id){
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
        $price=$product_list['price'];

        //$product_description_for_docs = "hello 23";
        
        $product_description_for_docs = $category." ".$product_name." ".$characteristic." "
                    .$brand.", ".$weight_volume." ".$unit_measure." ".$quantity_package." "
                    .$GLOBALS['quantity_in_package'];
        

        return $product_description_for_docs;
    }*/
    //получить ближайшую дату поставки в городе
    function get_delivery_date_city($con, $city_id){
        $millis = round(microtime(true) * 1000);     
        //получить ближайший рабочий день
        $time_flag = false;
        while($time_flag == false){         
            $millis += 24*60*60*1000;
            $date = date('d-m-Y H:i:s', $millis / 1000);
            $date = explode('-', explode(' ', $date)[0]); 
            $day = $date[0];
            $month = $date[1];
            $year = $date[2];
            $query="SELECT `weekend_id` FROM `t_weekend`
                    WHERE `city_id`=$city_id and `year` = '$year' and `month` = '$month' 
                                            and `day` = '$day' ";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));   
            if(mysqli_num_rows($result) > 0){
            }else{
                $delivery_time_millis = $millis;
                $time_flag = true;
            }
        }
        //echo "hi $day-$month-$year millis: $delivery_time_millis <br>";
        return $delivery_time_millis;
    }
    //получить id города
    //$city_id=get_city_id($con, $city);
    function get_city_id($con, $city){
        $query="SELECT `city_id` FROM `t_city` WHERE `city`='$city'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $city_id=$row[0];
        return $city_id;
    }
    //получить ближайшую дату поставки из москвы (московской области)
    function get_delivery_date_intercity($con, $out_city, $in_city){
        $delivery_time_millis = 0;
        //получить id города
        $out_city_id=get_city_id($con, $out_city);
        //echo "out_city = $out_city, out_city_id = $out_city_id <br>";
        $out_city_id=1;
        //получить id города
        $in_city_id=get_city_id($con, $in_city);
        //echo "in_city_id = $in_city_id <br>";
        //$in_city_id=2;//$in_city
        $this_time_millis = round(microtime(true) * 1000);//microtime(true);        
        $query="SELECT `time_millis` FROM `t_intercity_delivery_calendar` 
                    WHERE `out_city_id`='$out_city_id' and `in_city_id`='$in_city_id' 
                                and `time_millis` > '$this_time_millis' LIMIT 2";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            //Проверить ограничения по сумме или весу всех заказов для этой даты
            //и при превышении ограничить(перенести на следующую открытую дату)
            while($row = mysqli_fetch_array($result)){ 
                $delivery_time_millis=$row[0];
                $flag = checkMaxSummOrWeightForThisDateDelivery($con, $delivery_time_millis);
                if($flag){
                    break;
                }else{
                    $delivery_time_millis = 0;
                }
            }
            
        }else{
            $delivery_time_millis = 0;
        }

        return $delivery_time_millis ;
    }

    //Проверить ограничения по сумме или весу всех заказов для этой даты
    function checkMaxSummOrWeightForThisDateDelivery($con, $delivery_time_millis){
        $allOrdesrsSumm=0;
        $allOrdersWeight=0;
        $openFlag=true;
        $delivery_time_millis += (24*60*60*1000);
        //получить все заказы на эту дату
        $query="SELECT `order_id` FROM `t_order` WHERE `get_order_date_millis`='$delivery_time_millis'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){ 
                $order_id=$row[0];
                //Посчитать сумму и вес всех заказов
                $query="SELECT  op.quantity,
                                op.price,
                                op.price_process,
                                p.weight_volume, 
                                op.product_inventory_id
                                
                            FROM t_order_product op 
                                JOIN t_product_inventory pin ON pin.product_inventory_id = op.product_inventory_id
                                JOIN t_product p             ON p.product_id             = pin.product_id
                            WHERE op.order_id ='$order_id'";
                $res=mysqli_query($con, $query) or die(mysqli_error($con));
                while($row = mysqli_fetch_array($res)){
                    $quantity=$row[0];
                    $price=$row[1];
                    $price_process=$row[2];
                    $weight_volume=$row[3];
                    $product_id = $row[4];

                    $allOrdesrsSumm += $quantity * ($price + $price_process);
                    $allOrdersWeight += $quantity * $weight_volume;
                }                           
                
            }
            $allOrdersWeight = $allOrdersWeight/1000;
            if($allOrdesrsSumm >= $GLOBALS['allOrdesrsSummVariable'] ){
                $openFlag=false;
                
                //echo "allOrdesrsSumm = $allOrdesrsSumm: false  <br>";
            }else if($allOrdersWeight >= $GLOBALS['allOrdersWeightVariable']){
                $openFlag=false;
                
               // echo "allOrdersWeight = $allOrdersWeight: false <br>";
            }        
        }
        //echo "delivery_time_millis = $delivery_time_millis : allOrdersWeight = $allOrdersWeight<br>";
        return $openFlag;
    }
    //получить ближайшую дату поставки из москвы
    /*function get_delivery_date_intercity($con, $out_city, $in_city){
        $out_city_id=1;//$out_city
        $in_city_id=2;//$in_city
        $this_time_millis = round(microtime(true) * 1000);//microtime(true);        
        $query="SELECT `time_millis` FROM `t_intercity_delivery_calendar` 
                    WHERE `out_city_id`='$out_city_id' and `in_city_id`='$in_city_id' 
                                and `time_millis` > '$this_time_millis' LIMIT 1";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            $delivery_time_millis=$row[0];
        }else{
            $delivery_time_millis = 0;
        }

        return $delivery_time_millis ;
    }*/

    //данные о складе регион или москва
    function which_city_warehouse($con,$provider_warehouse_id){
        $query="SELECT win.city
                 FROM t_warehous w
                    JOIN t_warehouse_info win ON win.warehouse_info_id = w.warehouse_info_id
                WHERE w.warehouse_id='$provider_warehouse_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $city=$row[0];

        return $city;
    }
    //получить склад хранения этого товара 
    function check_storage_warehouse($con, $product_inventory_id, $city,$main_warehouse){
        //$main_warehouse = 'Москва';
        $provider_warehouse_id_list = array();
        $storage_warehouse=0;
        //найти склады компании продавца
        $query="SELECT `counterparty_id` FROM `t_product_inventory` 
                        WHERE `product_inventory_id`='$product_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $counterparty_id=$row[0];
        //echo "counterparty_id = $counterparty_id city=$city <br>"; 

        $query="SELECT w.warehouse_id ,
                        win.city
                    FROM t_warehouse_info win
                        JOIN t_warehous w ON w.warehouse_info_id = win.warehouse_info_id
                                            and w.warehouse_type = 'provider' and w.active = '1'
                    WHERE win.counterparty_id = '$counterparty_id' and win.city='$city' 
                                                                    and win.active = '1'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        while($row = mysqli_fetch_array($result)){ 
            $provider_warehouse_id=$row[0];
            $city_warehouse = $row[1];        
            //echo" pr war: $provider_warehouse_id city_warehouse: $city_warehouse<br>";    
            $provider_warehouse_id_list[] = ['provider_warehouse_id' => $provider_warehouse_id,
                                                'city_warehouse' => $city_warehouse];
        }
        $query="SELECT w.warehouse_id ,
                        win.city
                    FROM t_warehouse_info win
                        JOIN t_warehous w ON w.warehouse_info_id = win.warehouse_info_id
                                            and w.warehouse_type = 'provider' and w.active = '1'
                    WHERE win.counterparty_id ='$counterparty_id' and win.region='$main_warehouse'
                                                                    and win.active = '1'"; //  win.city='$main_warehouse'
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        //добавляем главные склады Москва
        while($row = mysqli_fetch_array($result)){ 
            $provider_warehouse_id=$row[0];
            $city_warehouse = $row[1];    
            //echo" pr war: $provider_warehouse_id city_warehouse: $city_warehouse<br>";         
            $provider_warehouse_id_list[] = ['provider_warehouse_id' => $provider_warehouse_id,
                                                'city_warehouse' => $city_warehouse];
        }
        //echo "count: ".count($provider_warehouse_id_list)."<br>";
        //на полученных складах найти товар начиная с города из которого запрос
        foreach($provider_warehouse_id_list as $k => $provider_warehouse){
            //echo "provider_warehouse: ".$provider_warehouse['provider_warehouse_id'] . " product_inventory_id: ".$product_inventory_id."<br>";
            
            //найти склад на котором есть данные об этом товаре
            $storage_warehouse=search_warehouse_this_product($con, 
                            $provider_warehouse['provider_warehouse_id'], $product_inventory_id);
            //echo "storage_warehouse: $storage_warehouse<br>";
            if($storage_warehouse > 0){                
                break 1;
            }
        }
            
        return $storage_warehouse;
    }
    //найти склад на котором есть данные об этом товаре
    function search_warehouse_this_product($con, $provider_warehouse_id, $product_inventory_id){
        $storage_warehouse=0;
        $query="SELECT `warehouse_inventory_id`, `transaction_name`, `product_inventory_id`, `quantity`, `logistic_product`, `car_for_logistic`, `out_warehouse_id`, `collected`, `out_active`, `out_user_id`, `out_updated_at`, `in_warehouse_id`, `in_active`, `in_user_id`, `in_updated_at`, `creator_user_id`, `created_at` 
                        FROM `t_warehouse_inventory_in_out` 
                            WHERE `in_warehouse_id`='$provider_warehouse_id' 
                                and `product_inventory_id`='$product_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            //$row = mysqli_fetch_array($result);
            $storage_warehouse=$provider_warehouse_id;
        }        

        return $storage_warehouse;
    }
    //deleted --- узнать цену для этого распределительного склада
    function get_price_product_for_partner_warehouse($con,
                                 $partner_warehouse_id,$product_inventory_id,$city){
        $price = 0;
        $central_provider_warehouse = 'Москва';
        //найти склад хранения товара (provider)
        $query="SELECT `warehouse_id`
                     FROM `t_price` WHERE `product_inventory_id`='$product_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        while($row = mysqli_fetch_array($result)){ 
            $provider_warehouse_id=$row[0]; 
            $provider_warehouse_id_list[] = $provider_warehouse_id;
            echo "provider_warehouse_id: $provider_warehouse_id<br>";
        }
        //получить склад в регионе при отсутствии склад в москве
        $city_flag = false;
        foreach($provider_warehouse_id_list as $k => $provider_warehouse_id){
            $query="SELECT  win.city
                        FROM t_warehous w 
                             JOIN t_warehouse_info win ON win.warehouse_info_id = w.warehouse_info_id
                        WHERE w.warehouse_id = '$provider_warehouse_id'";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));
            while($row = mysqli_fetch_array($result)){ 
                $city_warehouse=$row[0]; 
                //$city_warehouse_list[] = $city_warehouse;
                echo "city_warehouse: $city_warehouse<br>";
                if($city_warehouse == $city){
                    $city_flag = true;
                    break 2;
                }else if($city_warehouse == $central_provider_warehouse){
                    $central_provider_warehouse_id = $provider_warehouse_id;
                }
            }           
        }
        //получить цену
        if($city_flag){
            $query="SELECT `price` FROM `t_price` 
                                            WHERE `warehouse_id`='$provider_warehouse_id' 
                                            and `product_inventory_id`='$product_inventory_id'";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));
            $row = mysqli_fetch_array($result);
            $price=$row[0];            
        }else{
            $query="SELECT `price` FROM `t_price` 
                                            WHERE `warehouse_id`='$provider_warehouse_id' 
                                            and `product_inventory_id`='$central_provider_warehouse_id'";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));
            $row = mysqli_fetch_array($result);
            $price=$row[0]; 
            //получить стоимость доставки товара
            $price_of_delivery_product = 
                price_of_delivery_product($con,$product_inventory_id,$out_city, $in_city);
            //получить стоимость обработки товара
            $price_of_processing_product = 
                price_of_processing_product($con,$product_inventory_id,$out_city, $in_city);
           //добавить и отнять доп расходы
            $price += $price_of_delivery_product + $price_of_processing_product ;
        }       

        return $price;
    }
    //получить стоимость обработки товара
    function price_of_processing_product($con,$product_inventory_id,$out_city, $in_city){
        //цена обработки товара      
        //$price_processing_gramm = 0.015;
        $price_processing_gramm = $GLOBALS['price_processing_gramm'];
        //получить вес единицы товара
        $query="SELECT p.weight_volume
                        FROM t_product_inventory pin 
                            JOIN t_product p ON p.product_id = pin.product_id
                        WHERE pin.product_inventory_id = '$product_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $weight_volume=$row[0]; 

        $price_of_processing_product = $weight_volume * $price_processing_gramm;
        return $price_of_processing_product;
    }
    //получить стоимость доставки товара
    function price_of_delivery_product($con,$product_inventory_id,$out_city, $in_city){
        //цена доставки кило за километр
        //$price_delivery_gramm_per_kilometer = 0.015;//15р/кг
        $price_delivery_gramm_per_kilometer = $GLOBALS['price_delivery_gramm_per_kilometer'];
        //получить вес единицы товара
        $query="SELECT p.weight_volume
                        FROM t_product_inventory pin 
                            JOIN t_product p ON p.product_id = pin.product_id
                        WHERE pin.product_inventory_id = '$product_inventory_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $weight_volume=$row[0]; 

        $price_of_delivery_product = $weight_volume * $price_delivery_gramm_per_kilometer;
        return $price_of_delivery_product;
    }
    //дата для mysqli
    $date = date_mysqli();
    function date_mysqli(){
        $date = date("Y-m-d H:i:s");
        return $date;
    }
    //найти user_id
    //$user_id=checkUserID($con, $user_uid);
    function checkUserID($con, $user_uid){ 
        $query="SELECT `user_id` FROM `t_user` WHERE `unique_id` = '$user_uid'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $user_id = $row[0];
        //echo "user_id " . $user_id . "<br>";
        return $user_id;
    }

    //найти counterparty_id по user_id
    //$counterparty_id = check_counterparty_id_by_user_id($con, $user_id);
    function check_counterparty_id_by_user_id($con, $user_id){ 
        $query="SELECT `counterparty_id` FROM `t_user` WHERE `user_id`='$user_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $counterparty_id = $row[0];
        return $counterparty_id;
    }


    /*
        //получить данные(информацию) о компании
            $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
            $counterparty_id = $companyInfoList['counterparty_id'];
            $abbreviation = $companyInfoList['abbreviation'];
            $counterparty = $companyInfoList['counterparty'];
            $taxpayer_id_number = $companyInfoList['taxpayer_id_number'];
            $open_full_catalog = $companyInfoList['open_full_catalog'];
            $companyInfoString_short = $companyInfoList['companyInfoString_short'];
            $companyInfoString = $companyInfoList['companyInfoString'];
    */
    //получить данные(информацию) о компании
    function receiveCompanyInfo($con, $counterparty_id){
        $query="SELECT `abbreviation`, `counterparty`, `taxpayer_id_number`,`open_full_catalog`
                    FROM `t_counterparty` WHERE `counterparty_id`='$counterparty_id'";
         $result = mysqli_query($con,$query) or die (mysqli_error($con));
         $row=mysqli_fetch_array($result);
         $abbreviation=$row[0];
         $counterparty=$row[1];
         $taxpayer_id_number=$row[2];
         $open_full_catalog=$row[3];

         $companyInfoString_short = $abbreviation." ".$counterparty;
         $companyInfoString = $abbreviation." ".$counterparty." инн ".$taxpayer_id_number;
         $companyInfoList = array('counterparty_id'=>$counterparty_id
                                ,'abbreviation' => $abbreviation,'counterparty' => $counterparty
                                ,'taxpayer_id_number' => $taxpayer_id_number
                                ,'companyInfoString' => $companyInfoString
                                ,'companyInfoString_short' => $companyInfoString_short
                                ,'open_full_catalog'=>$open_full_catalog);        

        return $companyInfoList;
    }

    /*
            //получить данные(информацию) о складе и компании id
            $warehouseInfoList = warehouseInfo($con,$warehouse_id);
            $warehouse_id = $warehouseInfoList['warehouse_id'];
            $warehouse_info_id = $warehouseInfoList['warehouse_info_id'];
            $region = $warehouseInfoList['region'];
            $district = $warehouseInfoList['district'];
            $city = $warehouseInfoList['city'];
            $street = $warehouseInfoList['street'];
            $house = $warehouseInfoList['house'];
            $building = $warehouseInfoList['building'];
            $signboard = $warehouseInfoList['signboard'];
            $counterparty_id = $warehouseInfoList['counterparty_id'];
            $warehouseInfoString = $warehouseInfoList['warehouseInfoString'];
    */    
    //получить данные(информацию) о складе и компании counterparty_id
    function warehouseInfo($con,$warehouse_id){//$warehouseInfoList;  
          $query="SELECT wi.warehouse_info_id,
                            wi.region,
                            wi.district,
                        wi.city,
                        wi.street,
                        wi.house,
                        wi.building,
                        wi.signboard,
                        wi.counterparty_id
                    FROM  t_warehous w
                        JOIN t_warehouse_info wi ON  wi.warehouse_info_id=w.warehouse_info_id                                                                  
                    WHERE w.warehouse_id = '$warehouse_id'"; 
     
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $warehouse_info_id=$row[0];
        $region=$row[1];
        $district=$row[2];
        $city=$row[3];
        $street=$row[4];
        $house=$row[5];
        $building=$row[6];
        $signboard=$row[7];
        $counterparty_id=$row[8];

        $warehouseInfoString = $GLOBALS['warehouse_text']." № ".$warehouse_info_id."/".$warehouse_id." ".$city." ".$GLOBALS['street_short_text']."."
                                .$street." ".$house;
        if($building !== ''){
            $warehouseInfoString .= " ".$GLOBALS['building_short_text']." ".$building;
        }
        
       
        $warehouseInfoList = array('warehouse_id' => $warehouse_id,'warehouse_info_id' => $warehouse_info_id,
                                    'region' => $region,'district' => $district,'city' => $city,'street' => $street,
                                    'house' => $house, 'building' => $building,
                                    'signboard' => $signboard, 'counterparty_id'=>$counterparty_id, 'warehouseInfoString'=>$warehouseInfoString);        
        
        return $warehouseInfoList;
    }
    //создать и записать полное описание товара в таблицу для поиска 
    function writeFullProductInfoToTable($con, $product_inventory_id){
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
        $price=$product_list['price'];            
        $product_name_from_provider=$product_list['product_name_from_provider'];
        $min_sell=$product_list['min_sell'];
        $multiple_of=$product_list['multiple_of'];
        $description_prod=$product_list['description_prod'];
        $product_info=$product_list['product_info'];
        
        $full_description = "id".$product_inventory_id." ".$category." ".$product_name." ".$brand." ".$characteristic." "
            .$type_packaging." ".$weight_volume." ".$unit_measure." "
            .$GLOBALS['quantity_in_package']." ".$quantity_package." ".$storage_conditions." "
            .$product_name_from_provider." ".$description_prod;

        $query="INSERT INTO `t_product_description_for_search`
                            (`product_inventory_id`, `product_description_for_search`) 
                    VALUES ('$product_inventory_id','$full_description')";
        mysqli_query($con, $query) or die (mysqli_error($con));
    }

            /*
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
           */
        //получить данные(информацию) по товару
        function receive_product_info($con, $product_inventory_id){
            //ищем товары поставщика
            $query = "SELECT pr.product_id,                                                       
                               cat.category, 
                               br.brand,
                               cr.characteristic,
                               tp.type_packaging,
                               um.unit_measure,
                               pr.weight_volume,                            
                               pi.quantity_package,
                               im.image_url,
                               pr.storage_conditions,
                               pi.price,                           
                               pn.product_name,
                               pi.min_sell,
                               pi.multiple_of,
                               inin.in_product_name,
                               ds.description,
                               c.catalog,
                               c.catalog_id
                               FROM t_product_inventory pi
                                   JOIN t_image im          ON im.image_id            = pi.image_id
                                   JOIN t_product pr        ON pr.product_id          = pi.product_id
                                   JOIN t_product_name pn   ON pn.product_name_id     = pr.product_name_id
                                   JOIN t_category cat      ON cat.category_id        = pr.category_id
                                   JOIN t_catalog c         ON c.catalog_id           = cat.catalog_id
                                   JOIN t_brand br          ON br.brand_id            = pr.brand_id
                                   JOIN t_characteristic cr ON cr.characteristic_id   = pr.characteristic_id
                                   JOIN t_type_packaging tp ON tp.type_packaging_id   = pr.type_packaging_id 
                                   JOIN t_unit_measure um   ON um.unit_measure_id     = pr.unit_measure_id 
                                   JOIN t_description ds    ON ds.description_id      = pi.description_id
                                   JOIN t_inventory_vs_inproductname inin ON inin.product_inventory_id = '$product_inventory_id'
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
           $image_url=$row[8];
           $storage_conditions=$row[9];
           $price = $row[10];
           $product_name = $row[11];
           $min_sell = $row[12];
           $multiple_of = $row[13];
           $product_name_from_provider = $row[14];
           $description_prod = $row[15];
           $catalog = $row[16];
           $catalog_id = $row[17];

           $weight_volume_for_info = $weight_volume;
           $unit_measure_for_info = $unit_measure;

           if($weight_volume_for_info >= 1000 && $unit_measure_for_info == "гр"){
                $weight_volume_for_info /= 1000;
                $unit_measure_for_info = $GLOBALS['kg_char'];
           }
           else if($weight_volume_for_info >= 1000 && $unit_measure_for_info == "мл."){
                $weight_volume_for_info /= 1000;                
                $unit_measure_for_info = $GLOBALS['l_char'];
            }
            $weight_volume_for_info = round($weight_volume_for_info, 2);

           $product_info = $product_name." ".mb_ucfirst($brand)." ".$characteristic." "
                            .$type_packaging." ".$weight_volume_for_info." ".$unit_measure_for_info.". "
                            .$GLOBALS['quantity_in_package']." ".$quantity_package;
            $description = $product_info;
            /*
                $description = $category." ".$product_name." ".$characteristic." ".$brand." "
                            .$type_packaging." ".$weight_volume." ".$unit_measure." "
                            .$GLOBALS['quantity_in_package']." ".$quantity_package;
            */
   
           $product_list = array('product_id' => $product_id,'product_inventory_id' => $product_inventory_id
                                    ,'product_name' => $product_name,'catalog_id' => $catalog_id
                                    ,'catalog' => $catalog,'category' => $category
                                    ,'brand' => $brand,'characteristic' => $characteristic
                                    , 'type_packaging' => $type_packaging,'unit_measure' => $unit_measure
                                    ,'weight_volume' => $weight_volume,'quantity_package' => $quantity_package
                                    ,'image_url' => $image_url,'storage_conditions' => $storage_conditions
                                    ,'price' => $price,'description' => $description,'product_info' => $product_info
                                    ,'product_name_from_provider' => $product_name_from_provider
                                    ,'min_sell' => $min_sell, 'multiple_of' => $multiple_of
                                    , 'description_prod' => $description_prod);        
                            
           return $product_list;          
        }

        //первая буква заглавная
    function mb_ucfirst($old_str) {
        $my_str = mb_strtoupper(mb_substr($old_str, 0, 1));
        return $my_str.mb_substr($old_str, 1);
    }
 

    //получить все открытые, активные, не выполненные заказы
    function get_open_order_active_not_excecute_this_warehouse($con,$warehouse_id){
        $query="SELECT `order_id` FROM `t_order` 
                    WHERE `warehouse_id`='$warehouse_id' 
                    and `order_active`='1' and `executed`='0' and `order_deleted`='0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $open_order_id_array[] = $row[0];
        }
    
        return $open_order_id_array;
    }
    //найти counterparty_id
    //$counterparty_id = searchCounterpartyId($con, $taxpayer_id);
    function searchCounterpartyId($con, $taxpayer_id){     
    $query = "SELECT counterparty_id FROM t_counterparty WHERE taxpayer_id_number = '$taxpayer_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    if($row = mysqli_fetch_array($result)){
        $counterparty_id = $row[0];
    }else{
        //echo "error: " . $query . "<br>" . mysqli_error($con);
        $counterparty_id = 0;
    }
    return $counterparty_id;
    }

    //получить остаток товара на складе
    //$partner_stock_quantity = stock_product_to_warehouse($con, $warehouse_id, $product_inventory_id);
    function stock_product_to_warehouse($con, $warehouse_id, $product_inventory_id){
        $delivery_quantity=0;
        $sold_quantity=0;
        //получить весь приход товара на склад
        $delivery_quantity=delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

        //получить весь расход товара со склада
        $sold_quantity=sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id);

        //if($delivery_quantity > $sold_quantity){
            //запас товара
            $partner_stock_quantity = $delivery_quantity - $sold_quantity;                  
                 
        //}
        return $partner_stock_quantity;
    }
     //получить весь приход товара на склад
     function delivery_product_for_warehouse($con, $warehouse_id, $product_inventory_id){
        $query="SELECT  `quantity` FROM `t_warehouse_inventory_in_out`
                        WHERE `in_warehouse_id`='$warehouse_id' AND `product_inventory_id`='$product_inventory_id'
                                                                AND `in_active`='1'"; 
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
            $delivery_quantity = 0;
        while($row = mysqli_fetch_array($res)){
                    //колличество прихода товара
            $delivery_quantity += $row[0];

        }
        return $delivery_quantity;
    }  
    //получить весь расход товара со склада
    function sold_product_for_warehouse($con, $warehouse_id, $product_inventory_id){//$sold_quantity
            
        $query="SELECT  `quantity` FROM `t_warehouse_inventory_in_out`
                WHERE `out_warehouse_id`='$warehouse_id' AND `product_inventory_id`='$product_inventory_id'
                                                        AND `out_active`='1'"; 
        $res = mysqli_query($con, $query) or die (mysqli_error($con));
        $sold_quantity = 0;
        while($row = mysqli_fetch_array($res)){
            //колличество расхода товара
            $sold_quantity += $row[0];
        }
        return $sold_quantity;
    }

?>