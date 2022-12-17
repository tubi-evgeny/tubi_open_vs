<?php
    include 'connect.php';
    include_once 'helper_classes.php';
	 
	 mysqli_query($con,"SET NAMES 'utf8'");
     
     //add_category
     //add_product_name
     //add_brand
     //add_characteristic
     //add_type_packaging
     //add_unit_measure
     //catalog_array
     //category_array   
     //chenge_catalog  
     //chenge_brand
     //chenge_category
     //chenge_product_name
     //chenge_provider
     //chenge_unit_measure
     //chenge_type_packaging
     //chenge_characteristic
     //insert_inventory
     //input_product
     //new_counterparty
	 
	 //показать список товаров поставки проверить на ошибки
     // и передать список и ошибки в приложение
    if(isset($_GET['input_product'])){ 
       $limit_num = $_GET['limit_num'];
       $count_show = $_GET['count_show'];
       
       //показать карточки товара для записи и исправления ошибок
       showProductFromInputProduct($con, $limit_num, $count_show);
       
    }else if(isset($_POST['catalog_array'])){  // получить список каталог названий  
       
        catalog_array($con);
        
    }else if(isset($_POST['chenge_catalog'])){ //заменить catalog в таблице поставки
        $input_product_id = $_POST['input_product_id'];
        $catalog= $_POST['catalog'];
        
        chenge_catalog($con, $input_product_id, $catalog);
        
    }else if(isset($_POST['category_array'])){  // получить список брендов  
       
       category_array($con);
       
    }
    else if(isset($_POST['product_name_array'])){  // получить список брендов  
       
        product_name_array($con);
        
     }
     else if(isset($_POST['chenge_category'])){ //заменить category в таблице поставки
       $input_product_id = $_POST['input_product_id'];
       $category = $_POST['category'];
       
       chenge_category($con, $input_product_id, $category);
       
    }else if(isset($_POST['add_category'])){    // добавить новый category в таблицу бренды
       $category = $_POST['category'];
       $catalog = $_POST['catalog'];
       
       add_category($con, $catalog, $category);
       
    }else if(isset($_POST['brand_array'])){  // получить список брендов
       
       brand_array($con);
       
    }//add_product_name

    else if(isset($_POST['add_product_name'])){    // добавить новый product_name в таблицу бренды
        $product_name = $_POST['product_name'];
        
        add_product_name($con, $product_name);
        
    }
    else if(isset($_POST['chenge_product_name'])){ //заменить product_name в таблице поставки
        $input_product_id = $_POST['input_product_id'];
        $product_name = $_POST['product_name'];
        
        chenge_product_name($con, $input_product_id, $product_name);
        
     }
    else if(isset($_POST['chenge_brand'])){ //заменить бренд в таблице поставки
       $input_product_id = $_POST['input_product_id'];
       $brand = $_POST['brand'];
       
       chenge_brand($con, $input_product_id, $brand);
       
    }else if(isset($_POST['add_brand'])){    // добавить новый бренд в таблицу бренды
       $brand = $_POST['brand'];
       
       add_brand($con, $brand);
       
   }else if(isset($_POST['characteristic_array'])){  // получить список characteristic chenge_characteristic
       
       characteristic_array($con);
       
   }else if(isset($_POST['chenge_characteristic'])){ //заменить characteristic в таблице поставки
       $input_product_id = $_POST['input_product_id'];
       $characteristic = $_POST['characteristic'];
       
       chenge_characteristic($con, $input_product_id, $characteristic);
       
   }else if(isset($_POST['add_characteristic'])){    // добавить новый characteristic в таблицу characteristic
       $characteristic = $_POST['characteristic'];
       
       add_characteristic($con, $characteristic);
       
   }else if(isset($_POST['tipe_pacaging_array'])){ //получить список вид упаковки
       
       tipe_pacaging_array($con);
       
   }else if(isset($_POST['add_type_packaging'])){ // внести в таблицу новую запись тип упаковки
       $type_packaging = $_POST['type_packaging'];
       
       add_type_packaging($con, $type_packaging);
       
   }else if(isset($_POST['chenge_type_packaging'])){ //изменить тип упаковки в таблице
       $input_product_id = $_POST['input_product_id'];
       $type_packaging = $_POST['type_packaging'];
       
       chenge_type_packaging($con, $input_product_id, $type_packaging);
   
   }else if(isset($_POST['unit_measure_array'])){ // показать список единица измерения
       
       unit_measure_array($con);
       
   }else if(isset($_POST['add_unit_measure'])){ // внести в таблицу новую запись единица измерения
       $unit_measure = $_POST['unit_measure'];
       
       add_unit_measure($con,$unit_measure);
       
   }else if(isset($_POST['chenge_unit_measure'])){ //изменить единица измерения в таблице
       $input_product_id = $_POST['input_product_id'];
       $unit_measure = $_POST['unit_measure'];
       
       chenge_unit_measure($con, $input_product_id, $unit_measure);
       
       //показать список поставщиков первый поставщик из запроса
   }else if(isset($_POST['provider_array'])){ 
        $my_taxpayer_id = $_POST['taxpayer_id'];
    
       provider_array($con,$my_taxpayer_id);
       
       //меняем данные постащика
   }else if(isset($_POST['chenge_provider'])){  
       $input_product_id = $_POST['input_product_id'];       
       $taxpayer_id = $_POST['taxpayer_id'];
       
       chenge_provider($con, $input_product_id, $taxpayer_id);
       //chenge_provider($con, $input_product_id, $provider);
   }
   //внести в базу <t_inventory> <t_product> товар из таблицы поставки   
   else if(isset($_GET['insert_inventory'])){ 
                                                
       $limit_num = $_GET['limit_num'];
       $count_show = $_GET['count_show'];
       
       insert_product_and_inventory($con, $limit_num, $count_show);
                            
   }
   //меняем статус заказа на выполненный
   else if(isset($_GET['make_order_executed'])){
        $unique_id = $_GET['unique_id'];
        $order_id = $_GET['order_id'];
        //echo 'test';
        make_order_exequted($con, $unique_id, $order_id);
                    //добавляем данные о компании и вносим в t_user
   }else if(isset($_GET['new_counterparty'])){
       $unique_id = $_GET['unique_id'];
       $abbreviation = $_GET['abbreviation'];
       $counterparty = $_GET['counterparty'];
       $taxpayer_id = $_GET['taxpayer_id'];
       $agentKey = $_GET['agentKey'];
       
       new_counterparty($con, $unique_id, $abbreviation, $counterparty, $taxpayer_id, $agentKey);
        //изменить цену товара поставщика в БД
   }else if(isset($_GET['chenge_price_in_inventory'])){
        $product_inventory_id = $_GET['product_inventory_id'];
        $price = $_GET['price'];

        $result = chenge_price_in_inventory($con, $product_inventory_id, $price );
        if($result){
            echo "RESULT_OK" . "<br>";
        }else {
            echo "error" . "Не удалось внести изменения повторите попытку позже" . "<br>";
        }
    //добавить новую поставку в t_input_product и сразу записать в t_product_inventory
   }else if(isset($_GET['chenge_quantity_in_inventory'])){
    $product_inventory_id = $_GET['product_inventory_id'];
    $quantity = $_GET['quantity'];
    $transaction_name = $_GET['transaction_name'];
    $warehouse_id = $_GET['warehouse_id'];
    $user_uid = $_GET['user_uid'];

    //echo "test01 <br>";
    //найти user_id
    $user_id = checkUserID($con, $user_uid);
    //echo "test02 <br>";
    $result = chenge_quantity_in_inventory($con, $product_inventory_id, $quantity, $transaction_name,$warehouse_id,$user_id);
    echo $result . '1';
   }
   //добавить новую поставку в t_input_product и сразу записать в t_product_inventory
   function chenge_quantity_in_inventory($con, $product_inventory_id, $quantity,$transaction_name,$warehouse_id,$user_id){
        //получить данные о товаре
       // echo "test0 <br>"; 
        $query= "SELECT pr.product_id,
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
                        ct.catalog,
                        cp.abbreviation,
                        cp.counterparty,
                        cp.taxpayer_id_number                         
                    
                    FROM t_product_inventory pi
                    JOIN t_image im          ON im.image_id          = pi.image_id
                    JOIN t_description de    ON de.description_id    = pi.description_id
                    JOIN t_product pr        ON pr.product_id        = pi.product_id
                    JOIN t_category cat      ON cat.category_id      = pr.category_id
                    JOIN t_brand br          ON br.brand_id          = pr.brand_id
                    JOIN t_characteristic cr ON cr.characteristic_id = pr.characteristic_id
                    JOIN t_type_packaging tp ON tp.type_packaging_id = pr.type_packaging_id 
                    JOIN t_unit_measure um   ON um.unit_measure_id   = pr.unit_measure_id 
                    JOIN t_catalog ct        ON ct.catalog_id        = cat.catalog_id
                    JOIN t_counterparty cp   ON cp.counterparty_id   = pi.counterparty_id
                    WHERE pi.product_inventory_id = '$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            $product_id=$row[0]; $product_inventory_id=$row[1]; $category=$row[2]; $brand=$row[3]; $characteristic=$row[4]; 
            $type_packaging=$row[5]; $unit_measure=$row[6]; $weight_volume=$row[7]; //$total_quantity=$row[8]; 
            $price=$row[8]; $quantity_package= $row[9]; $image_url=$row[10]; $description=$row[11];
             $catalog = $row[12]; $abbreviation= $row[13]; $counterparty= $row[14]; $taxpayer_id_number = $row[15];
//echo "test1 <br>"; 
            $query = "INSERT INTO `t_input_product`
                    (`catalog`, `category`, `brand`, `characteristic`, `type_packaging`, `unit_measure`, `weight_volume`, 
                    `price`, `quantity`, `quantity_package`, `image_url`, `description`, `abbreviation`, `counterparty`, 
                    `taxpayer_id_number`, `warehouse_id`) 
                    VALUES ('$catalog','$category','$brand','$characteristic','$type_packaging','$unit_measure','$weight_volume', 
                        $price,'$quantity','$quantity_package','$image_url','$description', '$abbreviation','$counterparty',
                        '$taxpayer_id_number','$warehouse_id')";
            $result = mysqli_query($con,$query) or die (mysqli_error($con));
            
            if($result){
               //получаем id вставленной строки
                $input_product_id = mysqli_insert_id($con);
                //добавить приход товара в t_warehouse_inventory_in_out
                $query="INSERT INTO `t_warehouse_inventory_in_out`
                        ( `transaction_name`, `product_inventory_id`, `quantity`, `in_warehouse_id`,`in_active`, `creator_user_id`) 
                VALUES ('$transaction_name', '$product_inventory_id', '$quantity', '$warehouse_id',     '1',         '$user_id')";

                $result=mysqli_query($con,$query) or die (mysqli_error($con));
                if($result){
                    $res = "RESULT_OK" . "<br>";
                    $query="UPDATE `t_input_product` SET `on_off`='0' WHERE `id`='$input_product_id'";
                    $result = mysqli_query($con, $query) or die (mysqli_error($con));
                }                
            }

        }else $res = "error" . "error" . "<br>";

        return $res;
   }
     
   /*
//добавить новую поставку в t_input_product и сразу записать в t_product_inventory
   function chenge_quantity_in_inventory($con, $product_inventory_id, $quantity){
        //получить данные о товаре
        $query= "SELECT pr.product_id,
                        pi.product_inventory_id,                        
                        cat.category, 
                        br.brand,
                        cr.characteristic,
                        tp.type_packaging,
                        um.unit_measure,
                        pr.weight_volume,
                        pi.quantity,
                        pi.price,
                        pi.quantity_package,
                        im.image_url,
                        de.description,
                        ct.catalog,
                        cp.abbreviation,
                        cp.counterparty,
                        cp.taxpayer_id_number                         
                    
                    FROM t_product_inventory pi
                    JOIN t_image im          ON im.image_id          = pi.image_id
                    JOIN t_description de    ON de.description_id    = pi.description_id
                    JOIN t_product pr        ON pr.product_id        = pi.product_id
                    JOIN t_category cat      ON cat.category_id      = pr.category_id
                    JOIN t_brand br          ON br.brand_id          = pr.brand_id
                    JOIN t_characteristic cr ON cr.characteristic_id = pr.characteristic_id
                    JOIN t_type_packaging tp ON tp.type_packaging_id = pr.type_packaging_id 
                    JOIN t_unit_measure um   ON um.unit_measure_id   = pr.unit_measure_id 
                    JOIN t_catalog ct        ON ct.catalog_id        = cat.catalog_id
                    JOIN t_counterparty cp   ON cp.counterparty_id   = pi.counterparty_id
                    WHERE pi.product_inventory_id = '$product_inventory_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            $product_id=$row[0]; $product_inventory_id=$row[1]; $category=$row[2]; $brand=$row[3]; $characteristic=$row[4]; 
            $type_packaging=$row[5]; $unit_measure=$row[6]; $weight_volume=$row[7]; $total_quantity=$row[8]; $price=$row[9];
            $quantity_package= $row[10]; $image_url=$row[11]; $description=$row[12]; $catalog = $row[13];$abbreviation= $row[14]; 
            $counterparty= $row[15]; $taxpayer_id_number = $row[16];

            $query = "INSERT INTO `t_input_product`
                    (`catalog`, `category`, `brand`, `characteristic`, `type_packaging`, `unit_measure`, `weight_volume`, 
                    `price`, `quantity`, `quantity_package`, `image_url`, `description`, `abbreviation`, `counterparty`, `taxpayer_id_number`) 
                    VALUES ('$catalog','$category','$brand','$characteristic','$type_packaging','$unit_measure','$weight_volume', 
                        $price,'$quantity','$quantity_package','$image_url','$description', '$abbreviation','$counterparty','$taxpayer_id_number')";
            $result = mysqli_query($con,$query) or die (mysqli_error($con));
            
            if($result){
               
                $input_product_id = mysqli_insert_id($con);
                //изменить колличество товара на складе по 'product_inventory_id' в 't_product_inventory'
                $quantity_result = $total_quantity + $quantity;
                $query="UPDATE `t_product_inventory` SET `quantity`='$quantity_result' WHERE `product_inventory_id`='$product_inventory_id'";
                $result=mysqli_query($con,$query) or die (mysqli_error($con));
                if($result){
                    $res = "RESULT_OK" . "<br>";
                    $query="UPDATE `t_input_product` SET `on_off`='0' WHERE `id`='$input_product_id'";
                    $result = mysqli_query($con, $query) or die (mysqli_error($con));
                }                
            }

        }else $res = "error" . "error" . "<br>";

        return $res;
   }
   */

   //изменить цену товара поставщика в БД
   function chenge_price_in_inventory($con, $product_inventory_id, $price ){
       $updated = date("Y-m-d H:i:s");
        $query = "UPDATE t_product_inventory SET price = '$price',updated_at = '$updated' 
                                            WHERE product_inventory_id= '$product_inventory_id'";
        $result = mysqli_query($con,$query) or die (mysqli_error($con)) ;
        //if($result){
       //     return true;
        //}else return false;
        return $result;
   }
                    //добавляем данные о компании
    function new_counterparty($con, $unique_id, $abbreviation, $counterparty, $taxpayer_id, $agentKey){
        //echo "test1" . "<br>";
        //проверить ИНН компании на дубликат, если есть то отказать в регистрации  
        $query="SELECT * FROM t_counterparty WHERE taxpayer_id_number = $taxpayer_id";
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            echo "error" . "&nbsp" . "Контрагент с таким ИНН уже существует (проверьте ИНН)
                        и попробуйте еще раз,  или свяжитесь с администратором" . "<br>";
        }else{
            //все хорошо добавляем данные о компании
            $query="INSERT INTO t_counterparty (abbreviation,counterparty,taxpayer_id_number)
            VALUES ('$abbreviation','$counterparty','$taxpayer_id')";
            $result = mysqli_query($con,$query) or die(mysqli_error($con));
            //echo "test2" . "<br>";
            $counterparty_id = mysqli_insert_id($con);

            //если создательне агент то исправить counterparty_id в таблице
            if($agentKey != 1){
                $query="UPDATE t_user SET counterparty_id = '$counterparty_id' 
                                    WHERE unique_id = '$unique_id'";
                $result=mysqli_query($con, $query) or die(mysqli_error($con));
            }

            if($result){
                echo "RESULT_OK" . "<br>";
            }else{
                echo "error" . "&nbsp" . "Что то пошло не так, данные о компании не сохранились. 
                        Попробуйте позже еще раз" . "<br>";
            }
        }        
    }
    
                    //меняем статус заказа на выполненный
   function make_order_exequted($con, $unique_id, $order_id){
        // получить user_id, counterparty_id для записи в t_order
        $query = "SELECT user_id,counterparty_id FROM t_user WHERE unique_id='$unique_id'";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($row = mysqli_fetch_array($result)){
            $user_id = $row[0];
            $counterparty_id = $row[1];
            $query = "UPDATE t_order SET user_id='$user_id',
                                        counterparty_id='$counterparty_id',
                                        executed = 1,
                                        order_active = '0'
                                        WHERE order_id = '$order_id'";

            $result = mysqli_query($con,$query) or die(mysql_error($link));
            if($result){
                //вернуть информацию об успехе заказа order
                echo $order_id . "&nbsp" . $counterparty_id . "<br>";
            }else {
                echo "error" . "&nbsp" . "Заказ не удалось оформить" . "<br>";
            }
        }
   }
  

   //меняем данные постащика
   function chenge_provider($con, $input_product_id, $taxpayer_id){
       $query = "SELECT abbreviation, counterparty, taxpayer_id_number
                     FROM t_counterparty WHERE taxpayer_id_number = $taxpayer_id";
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $abbreviation = $row[0];
        $counterparty = $row[1];
        $taxpayer_id = $row[2];
        $query = "UPDATE t_input_product         
                        SET abbreviation='$abbreviation',counterparty='$counterparty', taxpayer_id_number=$taxpayer_id
                        WHERE id = $input_product_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        echo "update ok";
    }
                                     //меняем данные постащика
  /* function chenge_provider($con, $input_product_id, $provider){
       $query = "UPDATE t_input_product 
                        SET counterparty = '$provider'
                        WHERE id = $input_product_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        echo "update ok";
   }*/

   if(isset($_GET['test1'])){
       $tax_id = $_GET['1'];
       provider_array($con,$tax_id);
   }

                                    //показать список поставщиков первый поставщик из запроса
   function provider_array($con,$my_taxpayer_id){
        $query="SELECT  `abbreviation`, `counterparty`, `taxpayer_id_number` 
                        FROM `t_counterparty` WHERE `taxpayer_id_number` = $my_taxpayer_id";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
               
            
            if(mysqli_num_rows($result) > 0){
               // echo "1" . "<br>";
                $row=mysqli_fetch_array($result);
                $abbreviation=$row[0];
                $counterparty=$row[1];
                $taxpayer_id=$row[2];

                $data = $abbreviation . "&nbsp" . $counterparty . "&nbsp" . $taxpayer_id . "<br>";
                $allProvider = $data;

                $allProvider .= allProviderArray($con, $taxpayer_id);
            }else{
                //echo "0" . "<br>";
                $allProvider = allProviderArray($con, $my_taxpayer_id);
            }          
                  
              
        echo  $allProvider ;
   }
   /*
function provider_array($con,$my_taxpayer_id){
        $query="SELECT  `abbreviation`, `counterparty`, `taxpayer_id_number` 
                        FROM `t_counterparty` WHERE `taxpayer_id_number` = $my_taxpayer_id";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        
        if($result){
            
            if(mysqli_num_rows($result) > 0){
                echo "1" . "<br>";
            }else{
                echo "0" . "<br>";
            }

            $row=mysqli_fetch_array($result);
            $abbreviation=$row[0];
            $counterparty=$row[1];
            $taxpayer_id=$row[2];

            $data = $abbreviation . "&nbsp" . $counterparty . "&nbsp" . $taxpayer_id . "<br>";
            $allProvider = $data;

            $allProvider .= allProviderArray($con, $taxpayer_id);
        }else{
            $allProvider = allProviderArray($con, $taxpayer_id);
        }       
        echo  $allProvider ;
   }
   */
   //показать всех поставщиков
   function allProviderArray($con, $taxpayer_id){
        $query="SELECT  abbreviation, counterparty, taxpayer_id_number 
                     FROM t_counterparty WHERE counterparty_id > 0 ORDER BY counterparty";//ORDER BY `counterparty`
        //$query="SELECT  abbreviation, counterparty, taxpayer_id_number 
        //FROM t_counterparty WHERE counterparty_id > 0 ";//ORDER BY `counterparty`
        $result=mysqli_query($con,$query);
        if($result){
            $data = '';
            while($row = mysqli_fetch_array($result)){
                $data .= $row[0] . "&nbsp" . $row[1] . "&nbsp" . $row[2] . "<br>";
            }
        }
        return $data;
   }
                                    //показать список поставщиков
  /* function provider_array($con){
        $query= "SELECT
                    c.counterparty
                FROM
                    t_provider p
                    JOIN t_counterparty c ON c.counterparty_id = p.counterparty_id
                WHERE p.provider_id > 0";
                
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $data = '';
       while($row = mysqli_fetch_array($result)){
           $data .= $row[0] . "<br>";
       }
       echo $data;
   }*/
   function chenge_unit_measure($con, $input_product_id, $unit_measure){
        $query = "UPDATE t_input_product 
                        SET unit_measure = '$unit_measure'
                        WHERE id = $input_product_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        echo "update ok";
   }
   function unit_measure_array($con){
       $query= "SELECT unit_measure FROM t_unit_measure ORDER BY unit_measure ASC";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $data = '';
       while($row = mysqli_fetch_array($result)){
           $data .= $row[0] . "<br>";
       }
       echo $data;
   }
   //добавить новую unit_measure в таблицу
   /*function add_unit_measure($con,$unit_measure){
       $unit_measure = mb_strtolower($unit_measure);
        $query = "SELECT unit_measure FROM t_unit_measure WHERE unit_measure = '$unit_measure'";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $row = mysqli_fetch_array($result);
       if($row[0]){
           echo "this unit_measure exists";
       }else {
           $query = "INSERT INTO t_unit_measure (unit_measure) VALUES ('$unit_measure')";
           $result = mysqli_query($con, $query) or die (mysql_error($link));
           if($result){
                    echo "add unit_measure";
                }else echo "error" . "&nbsp" . "Error add unit_measure";
       }
   }*/
   function chenge_type_packaging($con, $input_product_id, $type_packaging){
       $query = "UPDATE t_input_product 
                        SET type_packaging = '$type_packaging'
                        WHERE id = $input_product_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        echo "update ok";
   }
   //добавить новую type_packaging в таблицу
  /* function add_type_packaging($con, $type_packaging){
       $type_packaging = mb_strtolower($type_packaging);
       $query = "SELECT type_packaging FROM t_type_packaging WHERE type_packaging = '$type_packaging'";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $row = mysqli_fetch_array($result);
       if($row[0]){
           echo "this type_packaging exists";
       }else {
           $query = "INSERT INTO t_type_packaging (type_packaging) VALUES ('$type_packaging')";
           $result = mysqli_query($con, $query) or die (mysql_error($link));
           if($result){
                    echo "add type_packaging";
                }else echo "Error add type_packaging";
       }
   }*/
   function tipe_pacaging_array($con){                      //получить список вид упаковки
       $query= "SELECT type_packaging FROM t_type_packaging ORDER BY type_packaging ASC";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $data = '';
       while($row = mysqli_fetch_array($result)){
           $data .= $row[0] . "<br>";
       }
       echo $data;
   }
   //добавить новую characteristic в таблицу
  /* function add_characteristic($con, $characteristic){
       $characteristic = mb_strtolower($characteristic);
       $query = "SELECT characteristic FROM t_characteristic WHERE characteristic = '$characteristic'";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $row = mysqli_fetch_array($result);
       if($row[0]){
           echo "this characteristic exists";
       }else {
           $query = "INSERT INTO t_characteristic (characteristic) VALUES ('$characteristic')";
           $result = mysqli_query($con, $query) or die (mysql_error($link));
           if($result){
                    echo "add characteristic";
                }else echo "Error add characteristic";
       }
   }*/
   function chenge_characteristic($con, $input_product_id, $characteristic){
       $query = "UPDATE t_input_product 
                        SET characteristic = '$characteristic'
                        WHERE id = $input_product_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        echo "update ok";     
   }
   function characteristic_array($con){
       $query= "SELECT characteristic FROM t_characteristic ORDER BY characteristic ASC";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $data = '';
       while($row = mysqli_fetch_array($result)){
           $data .= $row[0] . "<br>";
       }
       echo $data;
   }
   //
   //---------------------->>
   /*function add_product_name($con, $product_name){
        $product_name = mb_strtolower($product_name);
        $query = "SELECT `product_name` FROM `t_product_name` WHERE `product_name`='$product_name'";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        $row = mysqli_fetch_array($result);
        if($row[0]){
            echo "this product name exists";
        }else {
            $query = "INSERT INTO `t_product_name`( `product_name`) VALUES ('$product_name')";
            $result = mysqli_query($con, $query) or die (mysql_error($link));
            if($result){
                    echo "add product name";
                }else echo "Error add product name";
        }
    }*/
   //------------------------<<
   /*function add_brand($con, $brand){
       $brand = mb_strtolower($brand);
       $query = "SELECT brand FROM t_brand WHERE brand = '$brand'";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $row = mysqli_fetch_array($result);
       if($row[0]){
           echo "this brand exists";
       }else {
           $query = "INSERT INTO t_brand (brand) VALUES ('$brand')";
           $result = mysqli_query($con, $query) or die (mysql_error($link));
           if($result){
                    echo "add brand";
                }else echo "Error add brand";
       }
   }*/

   function chenge_product_name($con, $input_product_id, $product_name){
    $query = "UPDATE t_input_product 
                     SET product_name = '$product_name'
                     WHERE id = $input_product_id";
     $result = mysqli_query($con, $query) or die (mysql_error($link));
     echo "update ok";                
    
}
   function chenge_brand($con, $input_product_id, $brand){
       $query = "UPDATE t_input_product 
                        SET brand = '$brand'
                        WHERE id = $input_product_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        echo "update ok";                
       
   }
   
   function brand_array($con){
       $query= "SELECT brand FROM t_brand ORDER BY brand ASC";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $data = '';
       while($row = mysqli_fetch_array($result)){
           $data .= $row[0] . "<br>";
       }
       echo $data;
   }
   //добавить новую категорию в таблицу
  /* function add_category($con, $catalog, $category){
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
                
                $query = "INSERT INTO t_category (category, catalog_id) VALUES ('$category', $catalog_id)";
                $result = mysqli_query($con, $query) or die (mysql_error($link));
                if($result){
                         echo "was add category";
                }else echo "Error add category";
           }
       }
   }*/
   function chenge_catalog($con, $input_product_id, $catalog){    
     
     $query = "UPDATE t_input_product 
                    SET catalog = '$catalog'                       
                     WHERE id = $input_product_id";
     $result = mysqli_query($con, $query) or die (mysql_error($link));
     echo "update ok";     
}
   function chenge_category($con, $input_product_id, $category){
       $query="SELECT 
                        ct.catalog 
                FROM 
                        t_category c
                    JOIN t_catalog ct ON ct.catalog_id = c.catalog_id
                WHERE c.category = '$category'";
        $result = mysqli_query($con,$query) or die (mysql_error($link));
        $row = mysqli_fetch_array($result);
        $catalog = $row[0];
        
        $query = "UPDATE t_input_product 
                        SET catalog = '$catalog',
                            category = '$category'
                        WHERE id = $input_product_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        echo "update ok";     
   }
  
   function catalog_array($con){ //
        $query= "SELECT catalog FROM t_catalog WHERE catalog_id != '38' ORDER BY catalog ASC";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        $data = '';
        while($row = mysqli_fetch_array($result)){
            $data .= $row[0] . "<br>";
        }
        echo $data;
    }
   
    function product_name_array($con){ //
        $query= "SELECT `product_name` FROM `t_product_name` ORDER BY `product_name` ASC";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        $data = '';
        while($row = mysqli_fetch_array($result)){
            $data .= $row[0] . "<br>";
        }
        echo $data;
    }

   function category_array($con){ //
       $query= "SELECT category FROM t_category ORDER BY category ASC";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
       $data = '';
       while($row = mysqli_fetch_array($result)){
           $data .= $row[0] . "<br>";
       }
       echo $data;
   }

   //внести в базу <t_inventory> <t_product> товар из таблицы поставки
   function insert_product_and_inventory($con, $limit_num, $count_show){   
       $query="SELECT  `id`, `catalog`, `category`,`product_name`, `brand`, `characteristic`, `type_packaging`, `unit_measure`,
                         `weight_volume`, `price`, `quantity`, `quantity_package`, `image_url`, `description`,
                          `abbreviation`, `counterparty`, `taxpayer_id_number`, `warehouse_id`,
                          `storage_conditions`,`creator_user_id`,`in_product_name`, `min_sell`, `multiple_of`
                 FROM t_input_product WHERE on_off = 1 LIMIT $limit_num, $count_show "; 
       $result= mysqli_query($con,$query) or die (mysql_error($link));
       $count = 1;
        //получить полное описание товара в t_input_product
       while($row = mysqli_fetch_array($result)){
            $id=$row[0];
            $catalog=$row[1]; 
            $category=$row[2]; 
            $product_name=$row[3]; 
            $brand=$row[4]; 
            $characteristic=$row[5];
            $type_packaging=$row[6]; 
            $unit_measure=$row[7]; 
            $weight_volume=$row[8]; 
            $price=$row[9]; 
            $quantity=$row[10]; 
            $quantity_package=$row[11]; 
            $image=$row[12]; 
            $description=$row[13]; 
            $abbreviation=$row[14];
            $counterparty=$row[15]; 
            $taxpayer_id=$row[16]; 
            $warehouse_id=$row[17];
            $storage_conditions=$row[18]; 
            $creator_user_id=$row[19];  
            //$in_product_name=$row[20];   
            $in_product_name=addslashes(trim($row[20]));   //Экранирует строку с помощью слешей
            $min_sell=$row[21];  
            $multiple_of=$row[22];    

            //все буквы маленькие           
            $catalog = mb_strtolower($catalog);
            $category = mb_strtolower($category);
            $product_name = mb_strtolower($product_name);
            //$brand = mb_strtolower($brand);
            $characteristic = mb_strtolower($characteristic);
            $type_packaging = mb_strtolower($type_packaging);
            $unit_measure = mb_strtolower($unit_measure);
            $description = mb_strtolower($description);
            $counterparty = mb_strtolower($counterparty);           
            $abbreviation = mb_strtolower($abbreviation);       
           
           $product_id_exists = 0;

           if(catalogCheckHave($con,$catalog) == 1 and categoryCheckHave($con,$category) == 1 
                and product_nameCheckHave($con,$product_name) == 1
                and brandCheckHave($con,$brand) == 1 and typePackagingCheckHave($con,$type_packaging) == 1 
                and  unitMeasureCheckHave($con,$unit_measure) == 1 
                and  counterpartyCheckHave($con,$counterparty) == 1){//$unit_measure_flag = 
                   
                   echo $count . "<br>";
                   $count = $count +1;     
                        //-----------получить id значений столбцов товара для поиска дубликата/
                   $query_select_product = "SELECT  
                                                c.category_id,
                                                pn.product_name_id,
                                                br.brand_id,
                                                ch.characteristic_id,
                                                tp.type_packaging_id,
                                                um.unit_measure_id
                                        FROM t_input_product ip 
                                            JOIN t_category c        ON c.category = ip.category
                                            JOIN t_product_name pn   ON pn.product_name = ip.product_name
                                            JOIN t_brand br          ON br.brand = ip.brand
                                            JOIN t_characteristic ch ON ch.characteristic = ip.characteristic
                                            JOIN t_type_packaging tp ON tp.type_packaging = ip.type_packaging
                                            JOIN t_unit_measure um   ON um.unit_measure = ip.unit_measure
                                        WHERE ip.id = $id";
                    $result_select_product = mysqli_query($con, $query_select_product) or die (mysql_error($link));
                if($row_select_product=mysqli_fetch_array($result_select_product)){
                            $category_id=$row_select_product[0];
                            $product_name_id=$row_select_product[1];
                            $brand_id=$row_select_product[2]; 
                            $characteristic_id=$row_select_product[3];
                            $type_packaging_id=$row_select_product[4]; 
                            $unit_measure_id=$row_select_product[5]; 


                    //найти дубликат продукта в t_product и получить product_id  
                    $product_id_exists = search_product_id($con
                                            ,$category_id, $product_name_id,$brand_id, $characteristic_id
                                            ,$type_packaging_id, $unit_measure_id, $weight_volume, $storage_conditions);
                
                    if($product_id_exists == 0){
                        //создать новый продукт
                        $product_id_exists = make_new_product($con, $category_id, $product_name_id
                                            ,$brand_id, $characteristic_id,$type_packaging_id
                                            , $unit_measure_id, $weight_volume, $storage_conditions);
                    }

                   /* if($product_id_exists > 0){  
                        
                        echo 'product_id: ' . $product_id_exists . '&nbsp' . '<br>';
                        
                    }else{                          //--------если дубликата нет то вносим запись в t_product и получаем product_id
                        $query_p_insert= "INSERT INTO t_product 
                                    (category_id, product_name_id, brand_id, characteristic_id, type_packaging_id, unit_measure_id, 
                                    weight_volume, storage_conditions)
                            VALUES ($category_id, $product_name_id, $brand_id, $characteristic_id, $type_packaging_id, $unit_measure_id,
                                    $weight_volume, $storage_conditions )";
                        $result_p_insert  = mysqli_query($con, $query_p_insert) or die (mysqli_error($con));                        
                       //-----------------------------------заменить на получить id последней записи
                        if ($result_p_insert) {
                            $product_id_exists = mysqli_insert_id($con);
                    		  echo " Создана новая product" . '<br>';
                    		                                        //-----------найти дубликат продукта в t_product и получить product_id 
                    		  //$product_id_exists = search_product_id ($con, $category_id, 
                              //                $product_name_id, $brand_id, $characteristic_id, $type_packaging_id,
                    		   //                      $unit_measure_id, $weight_volume);

                    		  echo 'product_id from insert: ' . $product_id_exists . '&nbsp' . '<br>';

                    	} else {

                    		  echo "Error: " . $query_p_insert . "<br>" . mysqli_error($con);

                    	}
                    }*/               
                }
            }else echo 'Error product info' . '<br>';
            //если в t_product запись есть то вносим новую запись в t_product_inventory
            if($product_id_exists > 0){  
                //найти description_id в таблице нет =0; есть = передать id;
                $description_id = descriptionIdSearch($con,$description);
                //найти image_id в таблице нет =0; есть = передать id;
                $image_id = imageIdSearch($con, $image);
                if($description_id == 0){  //--если описание отсутсвует то 
                    //echo 'description_id: ' . $description_id .'<br>';
                    writeDescriptionToTable($con, $description); //-----записать описание в таблицу
                    $description_id = descriptionIdSearch($con,$description);
                }
                if($image_id == 0){    //--если картинка отсутсвует то 
                   // echo 'image_id: ' . $image_id . '<br>';
                    $image_id = writeImageToTable($con, $image);          //-----записать картинку в таблицу
                    //$image_id = imageIdSearch($con, $image);
                }
                                                //--------- ищем активный дубль
                $product_inventory_id = searchIdenticalProductInventory_t($con,$in_product_name, $product_id_exists, $counterparty,$taxpayer_id);
                echo 'product inventory_id: ' . $product_inventory_id . "<br>";
                
                if($product_inventory_id > 0 ){
                                                
                                                    //---------получаем старую цену
                    $query_price = "SELECT price FROM t_product_inventory WHERE product_inventory_id = $product_inventory_id";
                    $result_price = mysqli_query($con, $query_price) or die (mysql_error($link));
                    $row_price = mysqli_fetch_array($result_price);
                    $price_old = $row_price[0];
                    //echo 'old price: ' . $price_old . "<br>";
                    
                    if($price_old != $price){            //----------если цена отличается изменяем цену на новую
                        chengePriceProductInventory_t($con, $price, $product_inventory_id); 
                    }
                    //  добавляем товар на склад
                    $transaction_name='delivery';
                    $query="INSERT INTO `t_warehouse_inventory_in_out`
                            (`transaction_name`, `product_inventory_id`, `quantity`, `in_warehouse_id`,
                                `in_active`, `creator_user_id`) 
                        VALUES ('$transaction_name','$product_inventory_id','$quantity','$warehouse_id', 
                                    '1'   ,'$creator_user_id')";
                    $res_wr = mysqli_query($con, $query) or die (mysqli_error($con));
                                            
                }
                //если дубль отсутствует то вносим новую запись в t_product_inventory и количество в склад хранения
                else {                               
                    $counterparty_id = serchCounterpartyId_01($con, $taxpayer_id);

                    addNewProductInventory_t($con, $product_id_exists, $price, $quantity, $quantity_package,
                                            $image_id, $description_id, $counterparty_id, $warehouse_id,
                                            $creator_user_id, $in_product_name,$taxpayer_id, $min_sell, $multiple_of);  
                    //ищем сохраненную запись из t_input_product
                    $product_inventory_id = searchIdenticalProductInventory_t($con,$in_product_name, $product_id_exists, $counterparty,$taxpayer_id);
                }
                    
                     //блокируем использованную сохраненную запись из t_input_product on_off "0"
                if($product_inventory_id > 0){
                    blockLineFromInputProduct_t($con, $id);
                }   
                
            }
       }
   }
   
  //показать карточки товара для записи и исправления ошибок
   //проверить на ошибки и передать ошибки в приложение
    function showProductFromInputProduct($con,$limit_num, $count_show){

        $query = "SELECT `id`, `catalog`, `category`,`product_name`, `brand`, `characteristic`, `type_packaging`, `unit_measure`,
                        `weight_volume`, `price`, `quantity`, `quantity_package`, `image_url`, `description`,
                        `abbreviation`,`counterparty`,`taxpayer_id_number`
                        FROM `t_input_product` WHERE on_off = 1 LIMIT $limit_num, $count_show"; 
        $result= mysqli_query($con,$query) or die (mysql_error($link));
        while($row = mysqli_fetch_array($result)){
            $id=$row[0]; $catalog=$row[1]; $category=$row[2]; $product_name=$row[3];  $brand=$row[4]; 
            $characteristic=$row[5];
           $type_packaging=$row[6]; $unit_measure=$row[7]; $weight_volume=$row[8]; $price=$row[9]; 
           $quantity=$row[10]; $quantity_package=$row[11]; $image=$row[12]; $description=$row[13];
            $abbreviation=$row[14]; $counterparty=$row[15];$taxpayer_id=$row[16];
                     

            if(empty($catalog)){
                //найти каталог к этой категории
                $catalog = searchCatalogThisCategory($con, $category, $id);
            }           
            
                $catalog_flag = catalogCheckHave($con,$catalog);
                $category_flag = categoryCheckHave($con,$category);
                $product_name_flag = product_nameCheckHave($con,$product_name);
                $brand_flag = brandCheckHave($con,$brand);
                $characteristic_flag = characteristicCheckHave($con, $characteristic);
                $type_packaging_flag = typePackagingCheckHave($con,$type_packaging);
                $unit_measure_flag = unitMeasureCheckHave($con,$unit_measure);
                $image_flag = imageCheckHave($con,$image);
                //внести изменения в обработку поставщика и поиск в новой таблице
                $counterparty_flag = counterpartyHaveToTable($con,$counterparty,$taxpayer_id);
                if($counterparty_flag == 1){
                    $counterparty_flag = abbreviationHaveToTable($con,$abbreviation,$taxpayer_id);
                }
                //$counterparty_flag = counterpartyCheckHave($con,$counterparty);

                echo $id . "&nbsp" . $catalog . "&nbsp" . $catalog_flag . "&nbsp" . $category . "&nbsp" 
                . $category_flag . "&nbsp" . $product_name . "&nbsp" . $product_name_flag . "&nbsp" 
                . $brand . "&nbsp" . $brand_flag . "&nbsp" 
                . $characteristic . "&nbsp" . $characteristic_flag . "&nbsp" . $type_packaging . "&nbsp" 
                . $type_packaging_flag  . "&nbsp" . $unit_measure . "&nbsp" . $unit_measure_flag . "&nbsp" 
                . $weight_volume . "&nbsp" . $price . "&nbsp" . $quantity . "&nbsp" 
                . $quantity_package . "&nbsp" . $image . "&nbsp" . $image_flag . "&nbsp" 
                . $description . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" 
                . $counterparty_flag . "&nbsp" . $taxpayer_id . "<br>";
            
            //}else{
             //   echo "error" . "&nbsp" . "Не удалось внести новый каталог в таблицу" . "<br>"; 
            //}
           
        }     
      
    }
   
            //--------------------HELPER FUNCTIONS 
            //-----------------------------------------------------------------------------------------------------------------------------
    
    //если каталог найден изменить запись в таблице поставки _input_product
    function wryteCatalogToTable($con, $catalog, $id){

        $query = "UPDATE `t_input_product` SET `catalog`='$catalog' WHERE id = $id";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($result){
            return true;
        }else {
            return false;
        }
    }
    //найти каталог к этой категории
    function searchCatalogThisCategory($con, $category, $id){
        $query = "SELECT t_catalog.catalog 
                        FROM t_category
                            JOIN t_catalog ON t_catalog.catalog_id = t_category.catalog_id
                        WHERE t_category.category = '$category'";
        $result= mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        if($row){ 
            $catalog=$row[0];
            //если каталог найден изменить запись в таблице поставки _input_product
            wryteCatalogToTable($con, $catalog, $id);
        }else { 
            $catalog = '0';
            //$catalog = 'Каталог отсутствует';
        }
        return $catalog;

    }                                               
            
    //ищем сохраненную запись из t_input_product    
    function searchIdenticalProductInventory_t($con,$in_product_name, $product_id_exists, $counterparty,$taxpayer_id){  //--------- ищем активный дубль
        $counterparty_id = serchCounterpartyId_01($con, $taxpayer_id);
        
        //$query = "SELECT product_inventory_id FROM t_product_inventory WHERE product_id = $product_id_exists 
        //                                                    AND counterparty_id = $counterparty_id";
        $query="SELECT `product_inventory_id` FROM `t_inventory_vs_inproductname` 
                        WHERE `in_product_name`='$in_product_name' and `taxpayer_id_number`='$taxpayer_id'";
        $result = mysqli_query($con, $query) or die(mysql_error($link));
        
        if($row = mysqli_fetch_array($result)){
        
            $product_inventory_id = $row[0]; 
        }else{
            $product_inventory_id = 0;
        }
        return $product_inventory_id;
    }
  
   
    function blockLineFromInputProduct_t($con, $id){          //---------- блокируем сохраненную запись из t_input_product on_off "0"
        $query = "UPDATE t_input_product SET on_off = 0 WHERE id = $id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
    }
        //вносим новую запись в t_product_inventory и количество в склад хранения
    function addNewProductInventory_t($con, $product_id_exists, $price, $quantity, $quantity_package, $image_id,
                                         $description_id, $counterparty_id, $warehouse_id,$creator_user_id,
                                         $in_product_name,$taxpayer_id, $min_sell, $multiple_of){
        $query = "INSERT INTO t_product_inventory 
                        (product_id       , price , quantity_package, `min_sell`, `multiple_of`, image_id, description_id, counterparty_id) 
                VALUES ($product_id_exists, $price, $quantity_package, $min_sell, $multiple_of, $image_id, $description_id, $counterparty_id)";    
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $product_inventory_id = mysqli_insert_id($con);
        if($result){
            
        } else {
        	  echo "Error: " . $query . "<br>" . mysqli_error($con);
              return;
        }          
        //записать полное описание товара в таблицу для поиска 
        writeFullProductInfoToTable($con, $product_inventory_id);
        
        //внести внутреннее описание товара в таблицу t_inventory_vs_inproductname
        writeInfoInventoryVsInproductnameToTable($con, $product_inventory_id, $in_product_name, $taxpayer_id);
       /* $query="INSERT INTO `t_inventory_vs_inproductname`
                            (`product_inventory_id`, `in_product_name`,`taxpayer_id_number`) 
                    VALUES ('$product_inventory_id','$in_product_name','$taxpayer_id')";
        mysqli_query($con, $query) or die (mysqli_error($con)); */

        $transaction_name = "delivery";   
        $active = "1";    
        $query = "INSERT INTO `t_warehouse_inventory_in_out`
                ( `transaction_name`, `product_inventory_id`, `quantity`, `in_warehouse_id`, `in_active`, `creator_user_id`) 
        VALUES ('$transaction_name','$product_inventory_id','$quantity',  '$warehouse_id',   '$active'  ,'$creator_user_id')";
         $result = mysqli_query($con, $query) or die (mysqli_error($con));   
         
        //сделать добавленный продукт активен и разархивирован
        make_new_product_active($con, $in_product_name);
         
    }
    //внести внутреннее описание товара в таблицу t_inventory_vs_inproductname
    function writeInfoInventoryVsInproductnameToTable($con, $product_inventory_id, $in_product_name, $taxpayer_id){
        //получить штрихкод/артикул товара
        $barcode_article = 0;
        $query="SELECT `barcode_article` FROM `t_make_new_product` 
                        WHERE `in_product_name`='$in_product_name' and `taxpayer_id_number` = '$taxpayer_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($row = mysqli_fetch_array($result)){            
            $barcode_article = $row[0];
        }

        $query="INSERT INTO `t_inventory_vs_inproductname`
                            (`product_inventory_id`, `in_product_name`, `barcode_article`,`taxpayer_id_number`) 
                    VALUES ('$product_inventory_id','$in_product_name', '$barcode_article', '$taxpayer_id')";
        mysqli_query($con, $query) or die (mysqli_error($con)); 
    }
    //сделать добавленный продукт активен и разархивирован
    function make_new_product_active($con, $in_product_name){
        $query="UPDATE `t_make_new_product` SET `active`='1',`archive`='0'
                    WHERE `in_product_name`='$in_product_name'";
        mysqli_query($con, $query) or die (mysqli_error($con));
    }
     
    function chengePriceProductInventory_t($con, $price, $product_inventory_id){ //--------заменить цену
        $query = "UPDATE t_product_inventory SET price = $price WHERE product_inventory_id = $product_inventory_id";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
    }
      
    function serchCounterpartyId_01($con, $taxpayer_id){     //-----найти counterparty_id
        $query = "SELECT counterparty_id FROM t_counterparty WHERE taxpayer_id_number = '$taxpayer_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if($row = mysqli_fetch_array($result)){
            $counterparty_id = $row[0];
        }else{
            echo "Error: " . $query . "<br>" . mysqli_error($con);
        }
        return $counterparty_id;
    }

  /*  function serchCounterpartyId($con, $counterparty){     //-----найти counterparty_id
        $query = "SELECT counterparty_id FROM t_counterparty WHERE counterparty = '$counterparty'";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($row = mysqli_fetch_array($result)){
            $counterparty_id = $row[0];
        }else{
            echo "Error: " . $query . "<br>" . mysqli_error($con);
        }
        return $counterparty_id;
    }*/
    
   function  writeImageToTable($con, $image){ //-----записать картинку в таблицу
       $query = "INSERT INTO t_image (image_url) VALUES ('$image')";
       $result = mysqli_query($con, $query) or die(mysql_error($link));
        if($result){
            $image_id = mysqli_insert_id($con);
        } else {
        	  echo "Error: " . $query . "<br>" . mysqli_error($con);
        }
        return $image_id;
   }
  /* function  writeDescriptionToTable($con, $description){ //-----записать описание в таблицу
       $query = "INSERT INTO t_description (description) VALUES ('$description')";
       $result = mysqli_query($con, $query) or die (mysql_error($link));
        if($result){
        } else {
        	  echo "Error: " . $query . "<br>" . mysqli_error($con);
        }
   }*/
   function imageIdSearch($con, $image){  //найти image_id в таблице нет =0; есть = передать id;
       $query= "SELECT image_id FROM t_image WHERE image_url = '$image'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=$row[0];
       }else { $res = 0;}
       return $res;
   }
  /* function descriptionIdSearch($con,$description){  //найти description_id в таблице нет =0; есть = передать id;
       $query= "SELECT description_id FROM t_description WHERE description = '$description'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=$row[0];
       }else { $res = 0;}
       return $res;
   }*/
   //проветить есть ли такой counterparty с такой аббревиатурой и таким ИНН в таблице нет =0; есть =1; //
   function counterpartyHaveToTable($con,$counterparty,$taxpayer_id){
    $query= "SELECT counterparty FROM t_counterparty WHERE counterparty = '$counterparty' 
                    AND taxpayer_id_number = $taxpayer_id";
    $result= mysqli_query($con, $query) or die (mysql_error($link));
    $row=mysqli_fetch_array($result);
    if($row){ $res=1;
    }else { $res = 0;}   

    return $res;
   }
   
   //проветить есть ли такая аббревиатура с таким ИНН в таблице нет =0; есть =1; //
   function abbreviationHaveToTable($con,$abbreviation,$taxpayer_id){
        $query= "SELECT abbreviation FROM t_counterparty WHERE abbreviation = '$abbreviation' 
                        AND taxpayer_id_number = $taxpayer_id";
            $result= mysqli_query($con, $query) or die (mysql_error($link));
            if(mysqli_num_rows($result) > 0){
                $res = 1;
            }else{
                $res=0;
            }
            //$row=mysqli_fetch_array($result);
            //if(empty($row[0])){ $res=0;
            //}else { $res = 1;}
            //$row = mysqli_fetch_array($result);
           // echo "res-" . $row[0] . "<br>";
           // echo "mysqli_num_rows-" . mysqli_num_rows($result) . "<br>";
               // echo $res . "<br>";
            return $res;
   }
   /*
   //проветить есть ли такой counterparty с такой аббревиатурой и таким ИНН в таблице нет =0; есть =1; //
   function counterpartyHaveToTable($con,$counterparty,$taxpayer_id){
    $query= "SELECT counterparty FROM t_counterparty WHERE counterparty = '$counterparty' 
                    AND taxpayer_id_number = $taxpayer_id";
    $result= mysqli_query($con, $query) or die (mysql_error($link));
    $row=mysqli_fetch_array($result);
    if($row){ $res=1;
    }else { $res = 0;}
    return $res;
   }
   */
   //проветить есть ли такой counterparty в таблице нет =0; есть =1; //
   function counterpartyCheckHave($con,$counterparty){ 
       $query= "SELECT counterparty FROM t_counterparty WHERE counterparty = '$counterparty'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=1;
       }else { $res = 0;}
       return $res;
   }
   function imageCheckHave($con,$image){ //проветить есть ли такой type_packaging в таблице нет =0; есть =1; 
       $query= "SELECT image_url FROM t_image WHERE image_url = '$image'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=1;
       }else { $res = 0;}
       return $res;
   }
   function unitMeasureCheckHave($con,$unit_measure){ //проветить есть ли такой type_packaging в таблице нет =0; есть =1; 
       $query= "SELECT unit_measure FROM t_unit_measure WHERE unit_measure = '$unit_measure'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=1;
       }else { $res = 0;}
       return $res;
   }
   function typePackagingCheckHave($con,$type_packaging){ //проветить есть ли такой type_packaging в таблице нет =0; есть =1; 
       $query= "SELECT type_packaging FROM t_type_packaging WHERE type_packaging = '$type_packaging'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=1;
       }else { $res = 0;}
       return $res;
   }
   function characteristicCheckHave($con, $characteristic){//проветить есть ли такой characteristic в таблице нет =0; есть =1;
       $query= "SELECT characteristic FROM t_characteristic WHERE characteristic = '$characteristic'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=1;
       }else { $res = 0;}
       return $res;
   }
   function brandCheckHave($con,$brand){ //проветить есть ли такой brand в таблице нет =0; есть =1; 
       $query= "SELECT brand FROM t_brand WHERE brand = '$brand'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=1;
       }else { $res = 0;}
       return $res;
   }
    //product_nameCheckHave($con,$product_name)
    function product_nameCheckHave($con,$product_name){ //проветить есть ли такой категория в таблице нет =0; есть =1; 
        $query= "SELECT `product_name` FROM `t_product_name` WHERE `product_name`='$product_name'";
        $result= mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        if($row){ $res=1;
        }else { $res = 0;}
        return $res;
    }
  /* function categoryCheckHave($con,$category){ //проветить есть ли такой категория в таблице нет =0; есть =1; 
       $query= "SELECT category FROM t_category WHERE category = '$category'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=1;
       }else { $res = 0;}
       return $res;
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
  /* function catalogCheckHave($con,$catalog){ //проветить есть ли такой каталог в таблице нет =0; есть =1; 
       $query= "SELECT catalog FROM t_catalog WHERE catalog = '$catalog'";
       $result= mysqli_query($con, $query) or die (mysql_error($link));
       $row=mysqli_fetch_array($result);
       if($row){ $res=1;
       }else { $res = 0;}
       return $res;
   }*/
   mysqli_close($con);
?>