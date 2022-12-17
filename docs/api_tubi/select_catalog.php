<?php	

    include 'connect.php';
    include_once 'helper_classes.php';
    include 'variable.php';
    include 'text.php';
	
	// Устанавливаем соединение sold_product_for_warehouse

/*	$con = mysqli_connect($servername, $username, $password, $database);

     if (mysqli_connect_errno($con)) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }*/
   
    mysqli_query($con,"SET NAMES 'utf8'");

    if(isset($_POST['receive_catalog'])){     
        /*$taxpayer_id = $_GET['taxpayer_id'];
        $my_city = $_GET['my_city'];   
        $my_region = $_GET['my_region'];*/
        $taxpayer_id = $_POST['taxpayer_id'];
        $my_city = $_POST['my_city'];   
        $my_region = $_POST['my_region'];

        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
        //получить данные(информацию) о компании
        $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
        $open_full_catalog = $companyInfoList['open_full_catalog'];
        //если нет разрешения на весь каталог
        $catalog_closed_arr = [];
        if($open_full_catalog == 0){
            $query="SELECT `info` FROM `t_general_info` WHERE `general_info`='catalog_closed'";
            $result = mysqli_query($con,$query) or die (mysqli_error($con));
            $row=mysqli_fetch_array($result);
            $catalog_closed=$row[0];
            $catalog_closed_arr = explode(";", $catalog_closed);
        }   
        //получить весь список catalog_id
        $query_catalog = "SELECT `catalog_id`, `catalog`, `catalog_image` FROM `t_catalog` WHERE `catalog_id` > '0'";    
        $result_catalog = mysqli_query($con, $query_catalog) or die (mysql_error($link));
        while ($row_catalog = mysqli_fetch_array($result_catalog)) {  
            $catalog_id = $row_catalog[0];
            $catalog = $row_catalog[1];
            $catalog_image = $row_catalog[2];
            
            //если каталог не равен закрытому каталогу то показать
            $show_catalog_flag = true;
            if($open_full_catalog == 0 && count($catalog_closed_arr) > 0){
                foreach($catalog_closed_arr as $k => $my_catalog_id){
                    if($catalog_id == $my_catalog_id) $show_catalog_flag=false;;
                }
            }
            if($show_catalog_flag){
                //получить category_id по catalog_id
                $query_category = "SELECT category_id FROM t_category WHERE catalog_id = $catalog_id";
                $result_category = mysqli_query($con, $query_category) or die (mysql_error($link));        
                while($row_category = mysqli_fetch_array($result_category)){       
                    $category_id = $row_category[0];

                    //получить весь список product_id по category_id                                    
                    $query_product = "SELECT product_id FROM t_product WHERE category_id = $category_id";
                    $result_product = mysqli_query($con, $query_product) or die (mysql_error($link));
                    $product_have=0;
                    while($row_product = mysqli_fetch_array($result_product)){ 
                        $product_id = $row_product[0];

                        //найти остаток запаса у product_id
                        $product_have = productHave($con, $product_id, $my_city, $my_region);

                        //echo "product_id = $product_id ";
                        //echo "/ запас = $product_have <br>";
                        //echo "------------------------------------<br>";

                        //если хоть у одного product_id есть запас в t_product_inventory то показать каталог и остановить
                        if($product_have > 0 ){
                            //echo 'show: ' . $catalog . "<br>";
                            break;
                        }                
                    }
                    //если  product_id есть запас в t_product_inventory то показать каталог
                    if($product_have > 0 ){
                        //if($catalog_id != '37'){//это табак (этот каталог не показывать)
                            echo $catalog ."&nbsp". $catalog_id. "&nbsp" . $catalog_image . "<br>";
                        
                            break;
                    //  }
                    }
                }
            }
        }
        //проверить наличие товаров в "мой каталог"
        $query="SELECT `catalog_is_mine` FROM `t_catalog_is_mine`
                                 WHERE `counterparty_id`='$counterparty_id'";
        $result = mysqli_query($con, $query) or die (mysql_error($con));
        if(mysqli_num_rows($result) > 0){
            $query="SELECT  `catalog`, `catalog_image` FROM `t_catalog` 
                                                WHERE `catalog_id`='38'";//38 это мой каталог
            $result = mysqli_query($con, $query) or die (mysql_error($con));
            $row= mysqli_fetch_array($result);
            $catalog=$row[0];
            $catalog_image=$row[1];

            echo $catalog . "&nbsp" . $catalog_image . "<br>";
        }

       // echo "counterparty_id: $counterparty_id <br> ";
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
            /*$quantity_order = add_this_product_in_all_orders($con, $product_inventory_id);
           
            //вычесть из остатка на складе - колличество товара в заказах
            $product_have = $free_inventory - $quantity_order;  */

            //вычислить свободные запасы для продажи
            //$product_have=calculateAvailableInventoryForSale($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id);
            $product_have=$free_inventory;
            
                                                                                                                                       
            if($product_have != 0){
                break;
            }                                                     
        }
       //передать остаток
        return $product_have;                                                           
    }                                    
 
	
	mysqli_close($con);
?>