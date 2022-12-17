<?php
	 include 'connect.php';
     include 'text.php';
	 include_once 'helper_classes.php';
     include 'variable.php';

	 
   mysqli_query($con,"SET NAMES 'utf8'");
   
   if(isset($_GET['show_products'])){   //------показать товары в корзине
       $order_id = $_GET['order_id'];
       $city_id = $_GET['city_id'];
       
        show_products_from_shoping_box($con, $order_id, $city_id);                                 
   } 
   
    function show_products_from_shoping_box($con, $order_id, $city_id){
        $city  = $GLOBALS['city'];
        $main_warehouse = $GLOBALS['main_warehouse'];
        $orders_id_list[] = $order_id;

       $query =  "SELECT 
                            op.product_inventory_id,
                            cp.counterparty,                            
                            op.order_product_id,
                            op.quantity,
                            op.price_process,                            
                            op.provider_war_id
                            
                    FROM t_order_product op
                        JOIN t_product_inventory pi ON pi.product_inventory_id = op.product_inventory_id                        
                        JOIN t_counterparty cp      ON cp.counterparty_id      = pi.counterparty_id
                    
                    WHERE order_id = $order_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));

        if(mysqli_num_rows($result) > 0){
            //echo "true"."<br>";
            while($row = mysqli_fetch_array($result)){
                $product_inventory_id = $row[0];
                $counterparty = $row[1];                        
                $order_product_id = $row[2];
                $quantity = $row[3];
                $price_process = $row[4];
                $provider_warehouse_id = $row[5];


                //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);
            $product_id=$product_list['product_id'];
            $category=$product_list['category'];
            $brand=$product_list['brand']; 
            $characteristic=$product_list['characteristic'];
            $unit_measure=$product_list['unit_measure'];
            $weight_volume=$product_list['weight_volume']; 
            $price=$product_list['price']; 
            $image_url=$product_list['image_url'];
            $description_prod=$product_list['description_prod'];
            $product_name=$product_list['product_name'];
            $quantity_package=$product_list['quantity_package'];

            $min_sell=$product_list['min_sell'];
            $multiple_of=$product_list['multiple_of'];
            $product_info=$product_list['product_info'];


            //получить склад хранения этого товара 
            $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $city,$main_warehouse); 
            //получить свободный остаток товара 
            //-вычислить свободные запасы на складе( кроме заказа из запроса)
           $free_inventory = check_inventory_004($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id); 
                
                
                $product = $order_product_id . "&nbsp" . $product_id . "&nbsp" . $product_inventory_id . "&nbsp" 
                            . $category . "&nbsp" .  $brand . "&nbsp" . $characteristic . "&nbsp" 
                            . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $price . "&nbsp" 
                            . $image_url . "&nbsp" . $description_prod . "&nbsp" . $counterparty . "&nbsp" 
                            . $quantity."&nbsp".$product_name."&nbsp".$quantity_package."&nbsp".$price_process."&nbsp"
                            . $provider_warehouse_id."&nbsp"
                            . $min_sell."&nbsp".$multiple_of."&nbsp".$product_info."&nbsp".$free_inventory;
                echo $product . "<br>";
            }
        }else{
            echo "NO_PRODUCT"."<br>";            
        }            
     
   }
   
   
   mysqli_close($con);
?>