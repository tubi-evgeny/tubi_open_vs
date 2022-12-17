<?php
    include 'connect.php';
    include 'text.php';
    include_once 'helper_classes.php';
    include 'variable.php';
	 
	 mysqli_query($con,"SET NAMES 'utf8'");
	 
     //show_full_price
     //chenge_user_name
     //chenge_user_phone
     //delete_order_from_database
     //receive_partner_warehouse

     //поменять имя пользователя
    if(isset($_GET['chenge_user_name'])){
        $user_uid = $_GET['user_uid'];
        $user_name = $_GET['user_name'];

        //все буквы маленькие
        $user_name = mb_strtolower($user_name);

        $result = chenge_user_name($con, $user_uid ,$user_name);
        if($result){
            echo "RESULT_OK" . "<br>";
        }else{
            echo "error" . "Запрос потерпел неудачу, повторите попытку позже" . "<br>";
        }
        
    }//поменять телефон пользователя
    else if(isset($_GET['chenge_user_phone'])){
        $user_uid = $_GET['user_uid'];
        $user_phone = $_GET['user_phone'];

        $result = chenge_user_phone($con, $user_uid, $user_phone);
        
        echo $result . "<br>";
    }//удалить заказ из БД
    else if(isset($_GET['delelte_order_from_database'])){
        $user_uid = $_GET['user_uid'];
        $order_id = $_GET['order_id'];

        //найти user_id
        $user_id=checkUserID($con, $user_uid);

        delete_order_from_database($con, $user_id, $order_id); 
        //delelte_order_from_database($con, $user_id, $order_id);  
    }
    //получить id склада партнера на который отправить товар для выдачи покупателю
    else if(isset($_GET['receive_partner_warehouse'])){        
        $order_id = $_GET['order_id'];

        receive_partner_warehouse($con, $order_id);  
    }
    //показать полный прайс
    else if(isset($_GET['show_full_price'])){  
        $my_city = $_GET['my_city']; 
        $my_region = $_GET['my_region']; 
        $taxpayer_id = $_GET['taxpayer_id']; 
        
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $taxpayer_id);

        show_full_price($con,$my_city, $my_region, $counterparty_id);  
    }
    //показать полный прайс
    function show_full_price($con,$my_city, $my_region, $counterparty_id){
        $in_region = $my_region;   
        $city  = $my_city;
        $in_city = $my_city;
        $main_warehouse = $GLOBALS['main_warehouse'];  
        $orders_id_list = array();
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

        $query="SELECT `product_inventory_id` FROM `t_product_inventory`
                 WHERE `on_off`='1' and `product_inventory_id`>'0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $product_inventory_id = $row[0];

            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);
            $catalog_id=$product_list['catalog_id'];
            $catalog=$product_list['catalog'];
            $category=$product_list['category'];
            $product_id=$product_list['product_id'];
            $price=$product_list['price'];
            $min_sell=$product_list['min_sell'];
            $product_info=$product_list['product_info'];

            //если каталог не равен закрытому каталогу то показать
            $show_catalog_flag = true;
            if($open_full_catalog == 0 && count($catalog_closed_arr) > 0){
                foreach($catalog_closed_arr as $k => $my_catalog_id){
                    if($catalog_id == $my_catalog_id) $show_catalog_flag=false;;
                }
            }
            if($show_catalog_flag){
                //получить склад хранения этого товара 
                $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $in_city,$main_warehouse); 
                //-вычислить свободные запасы на складе ( кроме заказа из запроса)
                $free_inventory = check_inventory_004($con, $product_inventory_id, $orders_id_list, $provid_warehouse_id); 
                if($free_inventory > 0 ){
                    //получить расположение склада 
                    $warehouseInfoList = warehouseInfo($con,$provid_warehouse_id);
                    $out_region = $warehouseInfoList['region'];
                    $out_district = $warehouseInfoList['district'];
                    $out_city = $warehouseInfoList['city'];

                    //$date_of_sale_mil=0;
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
                        $process_price = round($process_price, 2);//округлить*/
                    
                    }else{
                        $process_price = 0;
                        $tubi_commission = 0;                    
                        $warehouse_processing_commission = 0;
                        $delivery_in_moscow_commission = 0;            
                                
                        $tubi_commission = ($price * $GLOBALS['tubi_commission_percent']) - $price ;
                        $warehouse_processing_commission = ($price * $GLOBALS['warehouse_processing_percent']) - $price ;
                    
                        //добавить доп расходы 
                        $process_price = $tubi_commission + $warehouse_processing_commission + $delivery_in_moscow_commission;
                        $process_price = round($process_price, 2);//округлить
                    }  
                    echo $product_id ."&nbsp" . $product_inventory_id ."&nbsp" . $price . "&nbsp" 
                            .$process_price."&nbsp".$min_sell ."&nbsp".$product_info."&nbsp".$catalog."&nbsp".$category."<br>"; 
                }
            }
        }
    }
    //получить id склада партнера на который отправить товар для выдачи покупателю
    function receive_partner_warehouse($con, $order_id){
        $query="SELECT `warehouse_id` FROM `t_order` WHERE `order_id`='$order_id'";
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $warehouse_id = $row[0];

        //получить данные(информацию) о складе 
        $warehouseInfoList = warehouseInfo($con,$warehouse_id);
        $warehouse_id = $warehouseInfoList['warehouse_id'];
        $warehouse_info_id = $warehouseInfoList['warehouse_info_id'];
        $city = $warehouseInfoList['city'];
        $street = $warehouseInfoList['street'];
        $house = $warehouseInfoList['house'];
        $building = $warehouseInfoList['building'];
        $signboard = $warehouseInfoList['signboard'];

        echo $warehouse_info_id."&nbsp".$warehouse_id."&nbsp".$city."&nbsp".$street."&nbsp"
                    .$house."&nbsp".$building."<br>";
    }

    //удалить заказ из БД
    function delete_order_from_database($con, $user_id, $order_id){
        //получить данные о заказе покупателя
        $user_order_date = getBuyerOrderDate($con, $order_id);
        $partner_warehouse_id = $user_order_date['partner_warehouse_id'];
        $get_order_date_millis = $user_order_date['get_order_date_millis'];
        $category_in_order = $user_order_date['category_in_order'];
        $partner_counterparty_id = $user_order_date['partner_counterparty_id'];

        //получить товары в заказе покупателя //получить список товаров и количество
        $product_info_list = get_product_from_buyer_order($con, $order_id, $partner_counterparty_id, $partner_warehouse_id
        , $get_order_date_millis, $category_in_order);

       /* foreach($product_info_list as $k => $product_info){
            echo "description_docs_id: ".$product_info['description_docs_id'] . "<br>";
            foreach($product_info as $key => $v){
                echo $v . "&nbsp";
            }
            echo  "<br>";
        }*/

        //наити открытый заказ для этих складов        
        foreach($product_info_list as $k => $product_info){
            $partner_warehouse_id=$product_info['partner_warehouse_id'];
            $provider_warehouse_id=$product_info['provider_warehouse_id'];
            $get_order_date_millis=$product_info['get_order_date_millis'];
            $product_inventory_id=$product_info['product_inventory_id'];
            $quantity =$product_info['quantity'];
            //'product_inventory_id'=>$product_inventory_id, 'quantity'=>$quantity
            
            //найти заказ с этим товаром и в колличестве для этих складов
            $query="SELECT op.order_partner_id, 
                            opp.order_product_part_id,
                            op.order_active, 
                            op.collected,
                            opp.quantity
                            
                    FROM t_order_partner op
                        JOIN t_order_product_part opp ON opp.order_partner_id = op.order_partner_id
                                                         and opp.product_inventory_id = '$product_inventory_id'
                    WHERE op.out_warehouse_id='$provider_warehouse_id' 
                        and op.in_warehouse_id='$partner_warehouse_id' ORDER BY op.order_partner_id DESC";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            while($row=mysqli_fetch_array($result)){
                $order_partner_id=$row[0];
                $order_product_part_id =$row[1];
                $order_partner_active=$row[2];
                $order_partner_collected=$row[3];
                $order_partner_quantity=$row[4];
                
                if($order_partner_quantity == $quantity ){
                    //проверить эта позиция еще не занята?
                    $query="SELECT `deleted_goods_id` FROM `t_for_deleted_goods` 
                                WHERE `order_product_part_id`='$order_product_part_id'";
                    $res=mysqli_query($con,$query)or die (mysqli_error($con));
                    if(mysqli_num_rows($res) == '0'){
                        break;
                    }

                }
            }      
            
            echo  "order_partner_active = $order_partner_active <br>";
            //если заказ поставщику не активирован то 
            if($order_partner_active == 0){
                try{
                    //уменьшить количество товара в заказе поставщику на удаленное количество из заказа покупателя
                    reduce_the_quantity_of_goods($con, $order_partner_id, $product_inventory_id, $quantity, $order_product_part_id);
                    //сделать заказ покупателя удален, и позиции из заказа тоже удален
                    make_user_order_and_product_delete($con, $order_id);
                }catch (Exception $ex){

                }
            }else{
                //если заказ поставщику уже активирован                  //то проверить он собран
                //Внести данные об удаленном товаре в таблицу удаленных товаров из заказа
                $query="INSERT INTO `t_for_deleted_goods`
                            (`order_partner_id`, `order_product_part_id`, `product_inventory_id`, `quantity`, `order_id`, `user_id`) 
                    VALUES ('$order_partner_id', '$order_product_part_id', '$product_inventory_id','$quantity','$order_id','$user_id')";
                mysqli_query($con,$query)or die (mysqli_error($con));

                echo  "t_for_deleted_goods = true <br>";

                //сделать заказ покупателя удален, и позиции из заказа тоже удален
                make_user_order_and_product_delete($con, $order_id);

               /* //если заказ еще не собран
                if($order_partner_collected == 0){
                    //проверить если товар(позиция) из заказа еще не собран то
                    $query="SELECT `collected` FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id' 
                                            and `product_inventory_id`='$product_inventory_id'";
                    $result=mysqli_query($con,$query)or die (mysqli_error($con));
                    $row=mysqli_fetch_array($result);
                    $product_collected=$row[0];
                    //если товар(позиция) из заказа еще не собран то
                    if($product_collected == 0){




                    }else{
                        //если товар(позиция) уже собран то показать сообщение
                        //и заблокировать выполнение сборки



                    }
                }else{
                    //проверить если товар из заказа уже собран то

                }*/

            }
            

        }



      /*  try{
            $query="UPDATE `t_order` SET `order_deleted`='1' WHERE `order_id`='$order_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

            $query="UPDATE `t_order_product` SET `order_prod_deleted`='1' WHERE `order_id`='$order_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }catch(Exception $e){

        }    */
    }
    //сделать заказ покупателя удален, и позиции из заказа тоже удален
    function make_user_order_and_product_delete($con, $order_id){
        $query="UPDATE `t_order` SET `order_deleted`='1' WHERE `order_id`='$order_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

            $query="UPDATE `t_order_product` SET `order_prod_deleted`='1' WHERE `order_id`='$order_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

    }
    //уменьшить количество товара в заказе поставщику 
   /* function reduce_the_quantity_of_goods($con, $order_partner_id, $product_inventory_id, $reduce_quantity){
        $query="SELECT `order_product_part_id`, `quantity`
                        FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id' 
                                                    and `product_inventory_id`='$product_inventory_id'";
        $result=mysqli_query($con,$query)or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $order_product_part_id= $row[0];
        $quantity= $row[1];

        $quantity -= $reduce_quantity;

        //echo "quantity = $quantity <br>";

        $query="UPDATE `t_order_product_part` SET `quantity`='$quantity'
                        WHERE `order_product_part_id`='$order_product_part_id'";
        mysqli_query($con,$query)or die (mysqli_error($con));

    }*/
    //удалить заказ из БД
  /*  function delelte_order_from_database($con, $user_id, $order_id){
        try{
            $query="UPDATE `t_order` SET `order_deleted`='1' WHERE `order_id`='$order_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));

            $query="UPDATE `t_order_product` SET `order_prod_deleted`='1' WHERE `order_id`='$order_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }catch(Exception $e){

        }    
    }*/
//поменять телефон пользователя
function chenge_user_phone($con, $user_uid, $user_phone){
    $query = "SELECT `phone` FROM `t_user` WHERE `phone` = '$user_phone'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    if(mysqli_num_rows($result) > 0){
        $res_str = "messege" . "&nbsp" . "Такой номер телефона уже существует, измените номер на другой";
    }else {
        $updated = date("Y-m-d H:i:s");

        $query = "UPDATE `t_user` SET `phone`='$user_phone',`updated_at`='$updated' WHERE `unique_id`='$user_uid'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_affected_rows($con) > 0){
           // $res = true;
            $res_str ="RESULT_OK";
        }else{
            //$res = false;
            $res_str = "error";
        }
    }
   return $res_str;
}
 
//поменять имя пользователя
function chenge_user_name($con, $user_uid ,$user_name){
    $updated = date("Y-m-d H:i:s");

    $query = "UPDATE `t_user` SET `name`='$user_name',`updated_at`='$updated' WHERE `unique_id`='$user_uid'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    if(mysqli_affected_rows($con) > 0){
        $res = true;
    }else{
        $res = false;
    }
   return $res;
}

mysqli_close($con);
?>