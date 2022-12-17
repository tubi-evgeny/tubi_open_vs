<?php

include '../connect.php';
    

$list_str = $_POST['list_str'];
    

    $table_list = json_decode($list_str);

    
    mysqli_query($con,"SET NAMES 'utf8'");

    $count = 0;

    foreach($table_list as $key => $vol){

        /*if($vol[1] == "package_price"){

            chengePrice($con, $table_list);
            $count++;

        }*//*else if($vol[1] == "accounting_unit"){

            chengeGoodsStock($con, $table_list);            
            $count++;

        }else */
        if($vol[0] == "catalog" && $vol[1] == "category"){
            //записать новые товары в 't_input_product'
            writeNewProduct($con, $table_list);            
            $count++;

        }else{
            echo "Таблица не существует";
            $count++;
        }

        if($count > 0){
            break;
        }        
        
    }
    //записать новые товары в 't_input_product'
    function writeNewProduct($con, $table_list){
        $count = 0; $x = 0;
        $duplicate_count = 0;
        $duplicate_str = "";
        
        foreach($table_list as $key => $vol){
            $catalog  = trim($vol[0]);
            $category  = trim($vol[1]);
            $product_name  = trim($vol[2]);
            $brand  = trim($vol[3]);
            $characteristic  = trim($vol[4]);
            $type_packaging  = trim($vol[5]);
            $unit_measure  = trim($vol[6]);
            $weight_volume = trim($vol[7]);
            $quantity_package  = trim($vol[8]);
            $min_sell  = trim($vol[9]);
            $multiple_of  = trim($vol[10]);
            $storage_conditions  = trim($vol[11]);
            $in_product_name = addslashes(trim($vol[12]));
            //$in_product_name=addslashes($in_product_name);
            $description  = trim($vol[13]);
            $abbreviation  = trim($vol[14]);
            $counterparty  = trim($vol[15]);
            $taxpayer_id_number  = trim($vol[16]);
            $warehouse_id = trim($vol[17]);
            $creator_user_id = trim($vol[18]);

            //проверить, есть в таблице дубликат, если есть то не записывать
            $query="SELECT `id`, `image_url`, `description`, `abbreviation`, `counterparty`, `taxpayer_id_number`, `warehouse_id`, `creator_user_id`, `on_off`, `date_start` 
                            FROM `t_input_product` WHERE `in_product_name`='$in_product_name' and `taxpayer_id_number`='$taxpayer_id_number' and  `on_off`='1'";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            if(mysqli_num_rows($result) == 0){
                try{
                    //пропустить эти две строки
                    if($catalog != "catalog" && $catalog != "каталог" ){
                        $query="INSERT INTO `t_input_product`
                                        (`catalog`, `category`, `product_name`, `brand`, `characteristic`, `type_packaging`
                                        , `unit_measure`, `weight_volume`, `quantity_package`, `min_sell`, `multiple_of`
                                        , `storage_conditions`, `in_product_name`, `description`, `abbreviation`, `counterparty`
                                        , `taxpayer_id_number`, `warehouse_id`, `creator_user_id`) 
                                VALUES ('$catalog','$category','$product_name','$brand','$characteristic','$type_packaging'
                                        ,'$unit_measure','$weight_volume','$quantity_package','$min_sell','$multiple_of'
                                        ,'$storage_conditions','$in_product_name','$description','$abbreviation','$counterparty'
                                        ,'$taxpayer_id_number','$warehouse_id','$creator_user_id')";
                        $result=mysqli_query($con,$query)or die (mysqli_error($con)); 
    
                        $count++;
                    }else{
                        $x++;
                    }
                }catch(Exception $ex){
                    //echo "Error: $ex";
                }
            }else{
                $duplicate_count++;
                $duplicate_str .= "in_product_name = ".$in_product_name . "\n";
            }
        }
        echo "Пропущено $x строк сверху \n";
        echo "Найдено дубликатов " . $duplicate_count . "\n";
        echo $duplicate_str;
        echo "Загружено $count позиций из ".(count($table_list) - $x). "\n";

    }

 

    mysqli_close($con);

?>