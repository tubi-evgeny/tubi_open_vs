<?php
	 include '../connect.php';
     include '../text.php';
     include_once '../helper_classes.php';

	 
   mysqli_query($con,"SET NAMES 'utf8'");

   
        //найти активный заказ
   // if(isset($_GET['check_start'])){     
             
        //make_trim_to_nomenklature($con);

        $str_info = "ИНФОРМАЦИЯ ЗАГРУЗКИ\n";

        //обновить прайс
        $arr_return = start_check_price($con, $str_info);

        $taxpayer_id_number = $arr_return['taxpayer_id_number'];
        $str_info = $arr_return['str_info'];

        //какая позиция не найдена
        $str_info .=  "\n------ ищем позиции которые есть в татлице но не найдены в прайсе -------\n";

        $str_info = which_position_was_not_found($con, $taxpayer_id_number, $str_info);

        $table_clear = clear_table_t_check_price_for_product($con);

        return $table_clear . $str_info;
  
   // }

   //очистить таблицу
   function clear_table_t_check_price_for_product($con){
        $query="TRUNCATE TABLE `t_check_price_for_product`";
        $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
        if($result){
            $table_clear= "Таблица проверки запасов очищена RESULT OK\n\n";
        }else{
            $table_clear= "ОШИБКА Таблица проверки запасов НЕ ОЧИЩЕНА,\nСООБЩИТЕ АДМИНИСТРАТОРУ\n";
        }

    return $table_clear;
   }

    //какая позиция не найдена
    function which_position_was_not_found($con, $taxpayer_id_number, $str_info){
        $id_list = array();
        $id_true_list = array();
        $id_false_list = array();
        $in_product_info_list = array();

        $query="SELECT  `in_product_name` , `product_inventory_id`
                    FROM `t_inventory_vs_inproductname` WHERE `taxpayer_id_number` = '$taxpayer_id_number'";//`id`, `product_inventory_id`,
        $result = mysqli_query($con, $query) or die (mysqli_error($con));   
        if(mysqli_num_rows($result) > 0){        
            while($row = mysqli_fetch_array($result)){
                $in_product_name =  $row[0];
                $product_inventory_id = $row[1];
                
                $in_product_info_list[] = ['in_product_name'=>$in_product_name
                                            ,'product_inventory_id'=>$product_inventory_id];
            }
        }
        foreach($in_product_info_list as $k => $in_product_info){
            $in_product_name = $in_product_info['in_product_name'];
            $product_inventory_id = $in_product_info['product_inventory_id'];

            $query="SELECT `id` FROM `t_check_price_for_product` WHERE `in_product_name`='$in_product_name'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));   
            if(mysqli_num_rows($result) > 0){        
                $row = mysqli_fetch_array($result);
                    $price_id =  $row[0];

                    $id_true_list[] = ['price_id'=>$price_id, 'product_inventory_id'=>$product_inventory_id
                                        , 'in_product_name'=>$in_product_name];
                    
                    //echo "OK - id= $price_id / my_id = $product_inventory_id name =  $in_product_name<br>";
                
            }else{

                $id_false_list[] = ['product_inventory_id'=>$product_inventory_id
                                    , 'in_product_name'=>$in_product_name];
               // echo "В ПРАЙСЕ НЕТ ЭТОГО ТОВАРА / my_id = $product_inventory_id name =  $in_product_name<br>";
            }
        }
        //echo "<br><br>----------------------------------------<br><br>";
        //покажем все успешные /найденые/ товары
        foreach($id_true_list as $k => $id_true){
            $price_id = $id_true['price_id'];
            $product_inventory_id = $id_true['product_inventory_id'];
            $in_product_name = $id_true['in_product_name'];

            $str_info .=  "OK - id= $price_id / my_id = $product_inventory_id name =  $in_product_name\n";
        }
        $str_info .=  "\n----------  В ПРАЙСЕ НЕТ ЭТОГО ТОВАРА ---------\n\n";
        $my_flag = false;
        $double_product_count = 0;
        //покажем товары которые не были найдены
        foreach($id_false_list as $k => $id_false){
            $product_inventory_id = $id_false['product_inventory_id'];
            $in_product_name = $id_false['in_product_name'];

            //псмотрим этого id нет в успешном списке, если есть то пропускаем
            foreach($id_true_list as $k => $id_true){
                $my_product_inventory_id = $id_true['product_inventory_id'];
    
                if($product_inventory_id == $my_product_inventory_id){
                    $my_flag = true;

                    $double_product_count++;
                    //echo "hi <br>";
                    break;
                }
            }
            //этого id нет в успешном списке, показываем
            if($my_flag == false){
                $str_info .=  "my_id = $product_inventory_id name =  $in_product_name\n";   //В ПРАЙСЕ НЕТ ЭТОГО ТОВАРА /              
            }         
            $my_flag = false;   
        }        
        //найдены описания схожие но с синтаксическими или др ошибками 
        $str_info .=  "\nнайдены описания схожие но с синтаксическими или др ошибками 
                        \ndouble product count = $double_product_count \n";

        return $str_info;
    }
    //найти в каталоге tubi этот товар (если надо поменять входящее имя )
    function checkProductToInventoryVsProductnameTable($con, $in_product_name, $barcode_article
                                        , $taxpayer_id_number, $package_price,$unit_price,$accounting_unit){
        $product_inventory_id=0;
        $query="SELECT `product_inventory_id` 
                            FROM `t_inventory_vs_inproductname` 
                        WHERE `taxpayer_id_number`='$taxpayer_id_number' and `in_product_name`='$in_product_name'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) == 0){
            $barcodeArticleArray = explode(';', $barcode_article);
            foreach($barcodeArticleArray as $k => $barcode){
                //получить все товары поставщика и найти совпадение штрихкода
                $query="SELECT `product_inventory_id`, `barcode_article` FROM `t_inventory_vs_inproductname` 
                            WHERE `taxpayer_id_number`='$taxpayer_id_number' and `barcode_article`!= '0'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                while($row = mysqli_fetch_array($res)){
                    $product_inventory_id = $row[0];
                    $barcode_article_str = $row[1];
                    if($barcode_article_str != 0){
                        $oldBarcodeArray=explode(';', $barcode_article_str);
                        foreach($oldBarcodeArray as $k => $oldBarcode){
                            if($barcode == $oldBarcode){
                                //заменить in_product_name
                                $query="UPDATE `t_inventory_vs_inproductname` SET `in_product_name`='$in_product_name'
                                                WHERE `product_inventory_id`='$product_inventory_id'";
                                if(mysqli_query($con, $query) or die (mysqli_error($con))){
                                    //записать информацию в t_make_new_product
                                    $query="INSERT INTO `t_make_new_product`
                                            (`in_product_name`, `package_price`, `unit_price`, `accounting_unit`, `barcode_article`, `taxpayer_id_number`,`active`) 
                                    VALUES ('$in_product_name','$package_price','$unit_price','$accounting_unit', '$barcode_article','$taxpayer_id_number','1')";
                                    mysqli_query($con, $query) or die (mysqli_error($con));
                                    break 3;
                                }  
                            }                            
                        }
                    }                    
                }
            }                        
        }else if(mysqli_num_rows($result) > 1){ 
            $str_info .=  "найдены дубликаты товара\n";
            while($row = mysqli_fetch_array($result)){
                $product_inventory_id =  $row[0];
                
                $str_info .=  "дубликат" . $in_product_name . "= product_inventory_id: " . $product_inventory_id . "\n";
            }
        }else{
            $row = mysqli_fetch_array($result);
            $product_inventory_id =  $row[0];
        }
        return $product_inventory_id;
    }
    //обновить прайс
    function start_check_price($con, $str_info){
        $taxpayer_id_number = 0;
        $query="SELECT `id` FROM `t_check_price_for_product` WHERE `id`> '0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));   
        if(mysqli_num_rows($result) > 0){        
            while($row = mysqli_fetch_array($result)){
                $id =  $row[0];
                
                $id_list[] = $id;
            }
            
            foreach($id_list as $k => $id){
                //получить строку из нового прайса
                $query="SELECT  `in_product_name`, `package_price`, `unit_price`, `accounting_unit`,`barcode_article`,`taxpayer_id_number`
                             FROM `t_check_price_for_product` WHERE `id`='$id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($res);
                $in_product_name = addslashes($row[0]);
                $package_price = $row[1];
                $unit_price = $row[2];
                $accounting_unit = $row[3];
                $barcode_article = $row[4];
                $taxpayer_id_number = $row[5];

                //найти в каталоге tubi этот товар 
                $product_inventory_id = checkProductToInventoryVsProductnameTable($con, $in_product_name, $barcode_article
                                            , $taxpayer_id_number, $package_price,$unit_price,$accounting_unit);
                //найти в каталоге tubi этот товар                
              /*  $query="SELECT `product_inventory_id` 
                            FROM `t_inventory_vs_inproductname` 
                        WHERE `taxpayer_id_number`='$taxpayer_id_number' and `in_product_name`='$in_product_name'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));   
                if(mysqli_num_rows($res) == 0){
                    //провверить по штрихкоду
                    
                }*/
                /*else if(mysqli_num_rows($res) > 1){ 
                    $str_info .=  "найдены дубликаты товара \n";
                    while($row = mysqli_fetch_array($res)){
                        $product_inventory_id =  $row[0];
                        
                        $str_info .=  $in_product_name . "= product_inventory_id: " . $product_inventory_id . "\n";
                    }
                }*/
                if($product_inventory_id > 0){ //if(mysqli_num_rows($res) > 0){
                    //товар найден ищем текущую цену и сравниваем

                    $str_info .=  "$in_product_name - ";

                    //$row = mysqli_fetch_array($res);
                   // $product_inventory_id =  $row[0];
                    $query="SELECT  `price`, `quantity_package` FROM `t_product_inventory`
                                 WHERE `product_inventory_id`='$product_inventory_id'";
                    $res = mysqli_query($con, $query) or die (mysqli_error($con));
                    $row = mysqli_fetch_array($res);
                    $price = $row[0];
                    $quantity_package = $row[1];
                   //echo "product_inventory_id = $product_inventory_id / package_price = $package_price / quantity_package = $quantity_package\n";
                    if($package_price != 0){
                        if($accounting_unit == 'шт'){
                            $unit_price = $package_price;
                        }else{
                            $unit_price = $package_price / $quantity_package;
                        }
                        
                        $unit_price = round($unit_price, 2);
                        if($price != $unit_price){
                            $str_info = chenge_price($con, $product_inventory_id, $unit_price, $str_info);
                        }else{
                            $str_info .=  "ЦЕНА СООТВЕТСТВУЕТ \n";
                        }
                    }else if($unit_price != 0){
                        if($price != $unit_price){
                            $str_info = chenge_price($con, $product_inventory_id, $unit_price, $str_info);
                        }else{
                            $str_info .=  "ЦЕНА СООТВЕТСТВУЕТ \n";
                        }
                    }                    
                    //$str_info .=  "\n";
                }//если товар из прайса не найден в каталоге то внести в таблицу (t_make_new_product)
                else if($product_inventory_id == 0){                                //if(mysqli_num_rows($res) == 0){
                    //найти товар в таблице, если нет то внести
                    $query="SELECT `id` FROM `t_make_new_product` 
                                WHERE `in_product_name`='$in_product_name' and `taxpayer_id_number`='$taxpayer_id_number'";
                    $res = mysqli_query($con, $query) or die (mysqli_error($con));   
                    if(mysqli_num_rows($res) == 0){ 
                        $query="INSERT INTO `t_make_new_product`
                                (`in_product_name`, `package_price`, `unit_price`, `accounting_unit`,`barcode_article`, `taxpayer_id_number`) 
                        VALUES ('$in_product_name','$package_price','$unit_price','$accounting_unit','$barcode_article','$taxpayer_id_number')";
                        mysqli_query($con, $query) or die (mysqli_error($con));
                    }
                }
            }            
        }
        $arr_return['taxpayer_id_number'] = $taxpayer_id_number ;
        $arr_return['str_info'] = $str_info;

        return $arr_return;
    }
    function chenge_price($con, $product_inventory_id, $unit_price, $str_info){
        $query="UPDATE `t_product_inventory` SET `price`='$unit_price'
                 WHERE `product_inventory_id`='$product_inventory_id'";
        mysqli_query($con, $query) or die (mysqli_error($con));
        $str_info .=   "chenge PRICE id: $product_inventory_id \n";

        return $str_info ;
    }

    function make_trim_to_nomenklature($con){
        $query="SELECT `id` FROM `t_check_price_for_product` WHERE `id`> '0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));   
        if(mysqli_num_rows($result) > 0){        
            while($row = mysqli_fetch_array($result)){
                $id =  $row[0];
                
                $id_list[] = $id;
            }

            
            foreach($id_list as $k => $id){
                $query="SELECT `in_product_name` FROM `t_check_price_for_product` WHERE `id`='$id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($res);
                $in_product_name = $row[0];

                //заменить запрещенные символы ,',
                $in_product_name = str_replace("'", "*", $in_product_name);
                //убрать пробелы перед текстом
                $in_product_name = trim($in_product_name);

                $query="UPDATE `t_check_price_for_product` 
                            SET `in_product_name`='$in_product_name' WHERE `id`='$id'";
                mysqli_query($con, $query) or die (mysqli_error($con));
            }
            
        }
    }


    mysqli_close($con);
?>