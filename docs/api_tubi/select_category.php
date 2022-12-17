<?php
    include 'connect.php';
    include_once 'helper_classes.php';
    include 'variable.php';
    include 'text.php';
	
	   
    mysqli_query($con,"SET NAMES 'utf8'");


    $catalog = $_GET['catalog'];
    $my_city = $_GET['my_city'];   
    $my_region = $_GET['my_region'];


    $query = "SELECT `catalog_id` FROM `t_catalog` WHERE `catalog` = '$catalog'";    
    $result = mysqli_query($con, $query) or die (mysqli_error($con));   
    $row = mysqli_fetch_array($result);
    
    $catalog_id = $row[0];
    //-----------------------------------
    $query_category = "SELECT category_id, category FROM t_category WHERE catalog_id = $catalog_id";
    $result_category = mysqli_query($con, $query_category) or die (mysql_error($link));
    //получить весь список product_id по category_id
    while($row_category = mysqli_fetch_array($result_category)){        
    
        $category_id = $row_category[0];
        $category = $row_category[1];

        //КОСТЫЛЬ временно не показывать электронные испарители
        //if($category_id != '156'){  // это   /электронные испарители/     
                                     
            $query_product="SELECT `product_id` FROM `t_product` WHERE `category_id`='$category_id'";
            $result_product = mysqli_query($con, $query_product) or die (mysql_error($con));
            //найти остаток запаса у product_id
            while($row_product = mysqli_fetch_array($result_product)){ 
                $product_id = $row_product[0];
                //echo "------------------------product_id = $product_id <br>";
                $product_have = productHave($con, $product_id, $my_city, $my_region);
                //если хоть у одного product_id есть запас в t_product_inventory то показать каталог и остановить
                if($product_have > 0 ){
                    echo $category . "<br>";
                    break;
                }            
            } 
       // }          
    }

    //найти запас у product_id           
    function productHave($con, $product_id, $my_city, $my_region){
        $product_have = 0;
        //$in_region = "Московская область";//$my_region;
        //$in_city = 'Мытищи';//$my_city;
        $in_region = $my_region;
        $in_city = $my_city;
        $main_warehouse = $GLOBALS['main_warehouse'];
        $orders_id_list = array();
        
        //получить product_inventory_id 
        $query_id = "SELECT product_inventory_id FROM t_product_inventory WHERE product_id = $product_id";
        $result_id = mysqli_query($con, $query_id) or die (mysql_error($link));
        while($row_id = mysqli_fetch_array($result_id)){ 
            $product_inventory_id = $row_id[0];

            //получить склад хранения этого товара 
            $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $in_city,$main_warehouse); 

            //-вычислить свободные запасы на складе( кроме заказа из запроса) собрать массив2 product_inventory_id_array
            $free_inventory = check_inventory_004($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id);

            //сложить этот продукт во всех заказах
           /* $quantity_order = add_this_product_in_all_orders($con, $product_inventory_id);
           
            //вычесть из остатка на складе - колличество товара в заказах
            $product_have = $free_inventory - $quantity_order;      */
            
            //вычислить свободные запасы для продажи
            //$product_have=calculateAvailableInventoryForSale($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id);
            $product_have=$free_inventory; 
            //echo "++++++++++   product_have = $product_have <br>";                                                                                                       
            if($product_have != 0){
                break;
            }                                                     
        }
       //передать остаток
        return $product_have;                                                           
    } 
    //найти запас у product_id           
   /* function productHave($con, $product_id, $my_city, $my_region){
        $product_have = 0;
        
        //получить product_inventory_id 
        $query_id = "SELECT product_inventory_id FROM t_product_inventory WHERE product_id = $product_id";
        $result_id = mysqli_query($con, $query_id) or die (mysql_error($link));
        if($row_id = mysqli_fetch_array($result_id)){   
            $product_inventory_id = $row_id[0];
            //echo 'product_inventory_id ' . $product_inventory_id . "<br>";
            //получить колличество поставленного товара
            $query = "SELECT `quantity` FROM `t_warehouse_inventory_in_out` 
                             WHERE  `product_inventory_id`='$product_inventory_id' AND `in_warehouse_id` IS NOT NULL";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $delivery_quantity = '0';
          
            while($row = mysqli_fetch_array($result)){
                $delivery_quantity += $row[0];
                //echo "`product_inventory_id 1 `". $product_inventory_id . ' quantity: ' .$row[0] . "<br>";
            }

            //получить колличество реализованного товара
            $query = "SELECT `quantity` FROM `t_warehouse_inventory_in_out` 
                             WHERE  `product_inventory_id`='$product_inventory_id' AND `out_warehouse_id` IS NOT NULL";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));
            $sold_quantity = '0';
            
            while($row = mysqli_fetch_array($result)){
                $sold_quantity += $row[0];
                //echo "`product_inventory_id 2 `". $product_inventory_id . ' quantity: ' .$row[0] . "<br>";
            }
            //подсчитать свободный остаток на складе
            
            $product_have_to_warehouse = $delivery_quantity - $sold_quantity; 
            
            
          
            // в order_product посчитать колличество этого товара в открытых заказах (product_inventory_id) 
            $quantity_order = 0;    
            $query_order = "SELECT op.quantity, 
                                    o.executed
                                FROM t_order_product op                                
                                JOIN t_order o ON o.order_id = op.order_id
                                WHERE op.product_inventory_id = '$product_inventory_id' and op.order_prod_deleted = '0'";   
            $result_order = mysqli_query($con, $query_order) or die (mysql_error($link));   
            while($row_order = mysqli_fetch_array($result_order)){
                //сложить колличество этого товара в заказах
                $executed=$row_order[1];
                if($executed == 0){
                    $quantity_order += $row_order[0];
                }                
                //echo "выполнен : $executed / ";
            }           
            //вычесть из остатка на складе - колличество товара в заказах
            $product_have = $product_have_to_warehouse - $quantity_order;                                                       
                                                                    //--------подсчитать остаток/
                                                                    
                                                                    
        }
       //передать остаток
        return $product_have;                                                           
    }   */
	mysqli_close($con);
?>