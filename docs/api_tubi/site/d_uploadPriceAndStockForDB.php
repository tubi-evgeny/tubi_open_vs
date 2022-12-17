<?php

    include '../connect.php';
    include_once '../helper_classes.php';

    mysqli_query($con,"SET NAMES 'utf8'");

    //Загрузка цен и запасов из 1С

  
    if($_POST){
        $post_empty = '1';
    }else {
        $post_empty = '0';
    }
    $json = file_get_contents('php://input');
    //echo "$json\n";
    // Преобразует его в объект PHP
    $data = json_decode($json, true);


    $count_position = 0;
    $error_position = 0;
    foreach($data as $k => $string){

        $in_product_name = base64_decode($string['in_product_name']);                
        $price = base64_decode($string['price']);
        $quantity = base64_decode($string['quantity']);
        $accounting_unit = base64_decode($string['accounting_unit']);
        $warehouse_id = $string['warehouse_id'];
        $company_tax_id = $string['company_tax_id'];

        //$pr = "123 456";
        //$price = str_replace(" ", "", $pr);//убрать пробеллы в тысячах

        $unit_price = '0.00';
        $package_price = '0.00';
        if($accounting_unit == 'шт'){
            $unit_price = $price;
        }else if($accounting_unit == 'упак'){
            $package_price = $price;
            echo "package_price: $package_price\n";
        }

        try{
            $query="INSERT INTO `t_check_inventory_of_goods`
                                    ( `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number`) 
                            VALUES ('$in_product_name','$accounting_unit','$quantity','$warehouse_id','$company_tax_id')";
            mysqli_query($con,$query)or die (mysqli_error($con));

            $query="INSERT INTO `t_check_price_for_product`
                    (`in_product_name`, `package_price`, `unit_price`, `taxpayer_id_number`) 
            VALUES ('$in_product_name', '$package_price', '$unit_price', '$company_tax_id')";
            mysqli_query($con,$query)or die (mysqli_error($con)); 

            $count_position++;
        }catch(Exception $e){
            $error_position++;
        }
    }
    echo "Загружено: $count_position позиций(товаров)\n";
    echo "Ошибка при загрузке: $error_position позиций\n";

    //обновить прайс
    start_check_price($con);

    //защита от зацикливания(start_check_inventory)
    $repeat_count = 0;
    //начать исправление остатков запасов товар у поставщика
    start_check_inventory($con, $repeat_count);

    //начать исправление остатков запасов товар у поставщика
    function start_check_inventory($con, $repeat_count){
        $taxpayer_id_number = 0;
        $procent = 0.7;
        $all_products_info = array();

        //получить инн постаавщика
        $query="SELECT `taxpayer_id_number`, `warehouse_id` 
                    FROM `t_check_inventory_of_goods` WHERE `id` > '0' LIMIT 1";
        $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
        if(mysqli_num_rows($result) > 0){  
            $row = mysqli_fetch_array($result);
            $taxpayer_id_number =  $row[0]; 
            $provider_warehouse_id =  $row[1];  
            
            
        }else{
            //данных нет вернуться
            //echo 
            $str_info .= "начальные данные не получены, проверьте данные в первой строке таблицы t_check_inventory_of_goods";
            return;
        }
        //найти в каталоге tubi все товары поставщика               
        $query="SELECT  `product_inventory_id`, `in_product_name`
                FROM `t_inventory_vs_inproductname` 
            WHERE `taxpayer_id_number`='$taxpayer_id_number'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con)); 
        if(mysqli_num_rows($res) > 0){ 
            while($row = mysqli_fetch_array($res)){
                $my_product_inventory_id =  $row[0];
                $my_in_product_name =  $row[1];

                $all_products_info[] = ['my_product_inventory_id' => $my_product_inventory_id, 'my_in_product_name' => $my_in_product_name];

            }
        }else{
            //данных нет вернуться
            //echo 
            $str_info .= "в t_inventory_vs_inproductname нет товаров этого поставщика, перепроверьте данные и попробуйте еще раз";
            return;
        }
        foreach($all_products_info as $k => $product){
            //ищем товар в списке полученном то поставщика для корректировки остатков
            $product_inventory_id = $product['my_product_inventory_id'];
            $my_in_product_name = $product['my_in_product_name'];

            //получить строку из таблици сверки запасов
            $query="SELECT `id`, `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number` 
                        FROM `t_check_inventory_of_goods` 
                        WHERE `in_product_name` = '$my_in_product_name' and `taxpayer_id_number`='$taxpayer_id_number'";
            $res = mysqli_query($con, $query) or die (mysqli_error($con));
            if(mysqli_num_rows($res) > 0){
                $row = mysqli_fetch_array($res);
                $id = $row[0];
                $in_product_name = $row[1];
                $accounting_unit = $row[2];
                $quantity = $row[3];
                $provider_warehouse_id = $row[4];
                $taxpayer_id_number = $row[5];

                //товар найден ищем текущий запас и сравниваем
                //echo 
                $str_info="";
                $str_info .= "$in_product_name - ";

                //получить колличество в упаковке из t_product_inventory
                //получить данные(информацию) по товару
                $product_list = receive_product_info($con, $product_inventory_id);                        
                $quantity_package=$product_list['quantity_package'];
                $min_sell=$product_list['min_sell'];
                $multiple_of=$product_list['multiple_of'];

                //привести колличество в штуки
                if($accounting_unit == "шт"){
                    //echo "in_product_name = $in_product_name / шт <br>";
                    $quantity_finish = $quantity;
                }else{                    

                    $quantity_finish = $quantity * $quantity_package;
                }

                //получить остаток товара на складе
                $provider_stock_quantity = stock_product_to_warehouse($con, $provider_warehouse_id, $product_inventory_id);

                //проверить в какую сторону провести изменения добавить товара или уменьшить
                    //запас больше или равен минимальной продаже
                    if($quantity_finish >= $min_sell){

                        //$make_reserve = $quantity_finish;
                        // уменьшаем запас на коэфициент (на личные продажи поставщика)
                        $make_reserve = $quantity_finish * $procent;                        

                        //но меньше мин партии
                        if($make_reserve < $min_sell){
                            $make_reserve = 0;

                        }//округлить до кратного числа(упаковки, мин продажи + шаги увеличения )
                        else{
                            
                            //вычеслить сколько (продажа кратно) в колличестве товара для записи
                            $my_count = $make_reserve / $multiple_of;
                           // echo "my_count = $my_count  <br>";
                            //округлить в меньшую
                            $my_count = floor($my_count);
                           // echo "my_count floor = $my_count  <br>";
                            //теперь пересчитать
                            $my_make_reserve = $my_count * $multiple_of;

                            if($my_make_reserve < $min_sell){
                                $make_reserve = 0;
                            }else{
                                $make_reserve = $my_make_reserve;
                            }

                        }
                        
                    }else{
                        $make_reserve = 0;
                    }

                    if($make_reserve == 0){
                        //получить запас и полностью сделать возврат до результата /отсаток = 0/
                        if($provider_stock_quantity > 0){
                            //изменить знак числа на минус
                            //$quantity = -$provider_stock_quantity;

                            $quantity = $provider_stock_quantity;

                            $query="INSERT INTO `t_warehouse_inventory_in_out`
                                    ( `transaction_name`, `product_inventory_id`, `quantity`, `out_warehouse_id`, `collected`, `out_active`) 
                            VALUES ('return'      ,'$product_inventory_id','$quantity','$provider_warehouse_id',    '1'     ,     '1'     )";
                            mysqli_query($con, $query) or die (mysqli_error($con));
                            //echo 
                            $str_info .= "ЗАПАС  изменен на 0 \n";
                        }
                        else{
                            //echo 
                            $str_info .= "ОСТАТОК НА СКЛАДЕ РАВЕН = 0; ИЛИ ОТРИЦАТЕЛЬНЫЙ действие уменьшить отменено \n";
                        }
                        

                    }//добавить товар на склад . если добавить то /delivery/ 
                    else if($provider_stock_quantity < $make_reserve){
                        //добавить товар в приход 
                        $quantity_to_add = $make_reserve - $provider_stock_quantity;
                        $query="INSERT INTO `t_warehouse_inventory_in_out`
                                (`transaction_name`, `product_inventory_id`, `quantity`, `in_warehouse_id`, `in_active`) 
                        VALUES ('delivery','$product_inventory_id','$quantity_to_add','$provider_warehouse_id','1')";
                        mysqli_query($con, $query) or die (mysqli_error($con));
                        //echo 
                        $str_info .= "ТОВАР добавлен \n";

                    }//уменьшить остаток на складе. сделать возврат /return/ если уменьшить то /return/
                    else if($provider_stock_quantity > $make_reserve){
                        //$provider_stock_quantity > $make_reserve
                        //получаем результат с минусом
                        //$quantity_to_minus = $make_reserve - $provider_stock_quantity;

                        $quantity = $provider_stock_quantity - $make_reserve;
                        
                        $query="INSERT INTO `t_warehouse_inventory_in_out`
                                    ( `transaction_name`, `product_inventory_id`, `quantity`, `out_warehouse_id`  , `collected`, `out_active`) 
                            VALUES ('return'             ,'$product_inventory_id','$quantity','$provider_warehouse_id',  '1'    ,   '1'   )";
                            mysqli_query($con, $query) or die (mysqli_error($con));
                            //echo 
                            $str_info .= "ТОВАР уменньшен \n";
                    }else{
                        //echo 
                        $str_info .= "ЗАПАСЫ РАВНЫ \n";
                    }

                    //удаляем товар из таблицы
                    //удалить строку после обработки из таблицы
                    $query ="DELETE FROM `t_check_inventory_of_goods` WHERE `id`='$id'";
                    mysqli_query($con, $query) or die (mysqli_error($con));
            
            }else{
                //товар не найден в новом списке(запасы на складе) от поставщика на этом складе
                //, значит ищем у нас в запасах и делаем остаток = 0

                //получить остаток товара на складе
                $provider_stock_quantity = stock_product_to_warehouse($con, $provider_warehouse_id, $product_inventory_id);

                if($provider_stock_quantity > 0){
                    $query="INSERT INTO `t_warehouse_inventory_in_out`
                                ( `transaction_name`, `product_inventory_id`, `quantity`        , `out_warehouse_id`  , `collected`, `out_active`) 
                                VALUES ('return','$product_inventory_id','$provider_stock_quantity','$provider_warehouse_id',  '1'    ,   '1'   )";
                    mysqli_query($con, $query) or die (mysqli_error($con));
                    //echo 
                    $str_info .= "В списке(запасы на складе) поставщика этот товар отсутствовал, поэтому ТОВАР уменьшен до $my_in_product_name = 0\n";
                    
                }else{
                    //echo 
                    $str_info .= "В списке(запасы на складе) поставщика этот товар отсутствовал,  ТОВАР $my_in_product_name уже был равен = '0'\n";
                }
            }           

        }
        //очистить таблицу от товваров которые еще не внесены в туби каталог этого поставщика
        $query="DELETE FROM `t_check_inventory_of_goods` 
            WHERE `taxpayer_id_number`= '$taxpayer_id_number' and `warehouse_id`='$provider_warehouse_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        
        //проверяем таблица пуста
        $query="SELECT * FROM `t_check_inventory_of_goods` WHERE `id`> '0' LIMIT 1";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0 && $repeat_count < 10){//защита от зацикливания
            //если в таблице с новыми данными о запасах поставщиков остались данные повторить проверку
            start_check_inventory($con);
            $repeat_count++;
        }
        echo $str_info;

    }

    function start_check_price($con){
        //получить позиции из нового прайса
        $query="SELECT  `id`, `in_product_name`, `package_price`, `unit_price`, `taxpayer_id_number`
        FROM `t_check_price_for_product` WHERE `id` > '0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));   
        if(mysqli_num_rows($result) > 0){        
            while($row = mysqli_fetch_array($result)){
                $id = $row[0];
                $in_product_name = $row[1];
                $package_price = $row[2];
                $unit_price = $row[3];
                $taxpayer_id_number = $row[4];

                //найти в каталоге tubi этот товар                
                $query="SELECT `product_inventory_id` FROM `t_inventory_vs_inproductname` 
                        WHERE `taxpayer_id_number`='$taxpayer_id_number' and `in_product_name`='$in_product_name'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                if(mysqli_num_rows($res) > 0){
                    //если найдены дубликаты то в цикле исправить у всех цену  
                    while($row = mysqli_fetch_array($res)){
                        $product_inventory_id =  $row[0];

                        if($unit_price != 0){
                            chenge_price($con, $product_inventory_id, $unit_price);
                        }else if($package_price != 0){
                            $query="SELECT `quantity_package` FROM `t_product_inventory`
                                            WHERE `product_inventory_id`='$product_inventory_id'";
                            $res = mysqli_query($con, $query) or die (mysqli_error($con));
                            $row = mysqli_fetch_array($res);                            
                            $quantity_package = $row[0];

                            $unit_price = $package_price / $quantity_package;
                            $unit_price = round($unit_price, 2);

                            chenge_price($con, $product_inventory_id, $unit_price);
                        }
                      /*  $query="UPDATE `t_product_inventory` SET `price`='$unit_price'
                                WHERE `product_inventory_id`='$product_inventory_id'";
                        mysqli_query($con, $query) or die (mysqli_error($con));    */                    
                    }
                }else{
                    echo "Нет данных о --- $in_product_name \n";
                }
                //удалить строку из таблицы после обработки
                $query="DELETE FROM `t_check_price_for_product` WHERE `id` = '$id'";
                mysqli_query($con, $query) or die (mysqli_error($con));

            }
        }

    }

        function chenge_price($con, $product_inventory_id, $unit_price){
            $query="UPDATE `t_product_inventory` SET `price`='$unit_price'
                                WHERE `product_inventory_id`='$product_inventory_id'";
            mysqli_query($con, $query) or die (mysqli_error($con));
        }

    mysqli_close($con);

/*
  //начать исправление остатков запасов товар у поставщика
    function start_check_inventory($con){
        //получить строку из таблици сверки запасов
        $query="SELECT `id`, `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number` 
                FROM `t_check_inventory_of_goods` WHERE `id` > '0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        while($row = mysqli_fetch_array($result)){
            $id = $row[0];
            $in_product_name = $row[1];
            $accounting_unit = $row[2];
            $quantity = $row[3];
            $provider_warehouse_id = $row[4];
            $taxpayer_id_number = $row[5];

            //найти в каталоге tubi товар поставщика               
            $query="SELECT  `product_inventory_id` FROM `t_inventory_vs_inproductname` 
                WHERE `in_product_name`='$in_product_name' and  `taxpayer_id_number`='$taxpayer_id_number'";
            $res = mysqli_query($con, $query) or die (mysqli_error($con)); 
            if(mysqli_num_rows($res) > 0){ 
                $row = mysqli_fetch_array($res);
                $product_inventory_id =  $row[0];

                //получить колличество в упаковке из t_product_inventory
                //получить данные(информацию) по товару
                $product_list = receive_product_info($con, $product_inventory_id);                        
                $quantity_package=$product_list['quantity_package'];
                $min_sell=$product_list['min_sell'];
                $multiple_of=$product_list['multiple_of'];

                //привести колличество в штуки
                if($accounting_unit == "шт"){
                    //echo "in_product_name = $in_product_name / шт <br>";
                    $quantity_finish = $quantity;
                }else if($accounting_unit == 'упак'){                    

                    $quantity_finish = $quantity * $quantity_package;
                }else{
                    $quantity_finish = '0';
                }
                //получить остаток товара на складе
                $provider_stock_quantity = stock_product_to_warehouse($con, $provider_warehouse_id, $product_inventory_id);

                //проверить в какую сторону провести изменения добавить товара или уменьшить
                //запас больше или равен минимальной продаже
                if($quantity_finish >= $min_sell){

                    //$make_reserve = $quantity_finish;
                    // уменьшаем запас на коэфициент (на личные продажи поставщика)
                    $make_reserve = $quantity_finish * $procent;                        

                    //но меньше мин партии
                    if($make_reserve < $min_sell){
                        $make_reserve = 0;

                    }//округлить до кратного числа(упаковки, мин продажи + шаги увеличения )
                    else{
                        
                        //вычеслить сколько (продажа кратно) в колличестве товара для записи
                        $my_count = $make_reserve / $multiple_of;
                        // echo "my_count = $my_count  <br>";
                        //округлить в меньшую
                        $my_count = floor($my_count);
                        // echo "my_count floor = $my_count  <br>";
                        //теперь пересчитать
                        $my_make_reserve = $my_count * $multiple_of;

                        if($my_make_reserve < $min_sell){
                            $make_reserve = 0;
                        }else{
                            $make_reserve = $my_make_reserve;
                        }

                    }
                    
                }else{
                    $make_reserve = 0;
                }
                if($make_reserve == 0){
                    //получить запас и полностью сделать возврат до результата /отсаток = 0/
                    if($provider_stock_quantity > 0){
                        //изменить знак числа на минус
                        //$quantity = -$provider_stock_quantity;

                        $quantity = $provider_stock_quantity;

                        $query="INSERT INTO `t_warehouse_inventory_in_out`
                                ( `transaction_name`, `product_inventory_id`, `quantity`, `out_warehouse_id`, `collected`, `out_active`) 
                        VALUES ('return'      ,'$product_inventory_id','$quantity','$provider_warehouse_id',    '1'     ,     '1'     )";
                        mysqli_query($con, $query) or die (mysqli_error($con));
                        //echo 
                        $str_info .= "ЗАПАС  изменен на 0 \n";
                    }
                    else{
                        //echo 
                        $str_info .= "ОСТАТОК НА СКЛАДЕ РАВЕН = 0; ИЛИ ОТРИЦАТЕЛЬНЫЙ действие уменьшить отменено \n";
                    }  
                }
                //добавить товар на склад . если добавить то /delivery/ 
                else if($provider_stock_quantity < $make_reserve){
                    //добавить товар в приход 
                    $quantity_to_add = $make_reserve - $provider_stock_quantity;
                    $query="INSERT INTO `t_warehouse_inventory_in_out`
                            (`transaction_name`, `product_inventory_id`, `quantity`, `in_warehouse_id`, `in_active`) 
                    VALUES ('delivery','$product_inventory_id','$quantity_to_add','$provider_warehouse_id','1')";
                    mysqli_query($con, $query) or die (mysqli_error($con));
                    //echo 
                    $str_info .= "ТОВАР добавлен \n";
                }
                //уменьшить остаток на складе. сделать возврат /return/ если уменьшить то /return/
                else if($provider_stock_quantity > $make_reserve){
                    //$provider_stock_quantity > $make_reserve
                    //получаем результат с минусом
                    //$quantity_to_minus = $make_reserve - $provider_stock_quantity;

                    $quantity = $provider_stock_quantity - $make_reserve;
                    
                    $query="INSERT INTO `t_warehouse_inventory_in_out`
                                ( `transaction_name`, `product_inventory_id`, `quantity`, `out_warehouse_id`  , `collected`, `out_active`) 
                        VALUES ('return'             ,'$product_inventory_id','$quantity','$provider_warehouse_id',  '1'    ,   '1'   )";
                        mysqli_query($con, $query) or die (mysqli_error($con));
                        //echo 
                        $str_info .= "ТОВАР уменньшен \n";
                }else{
                    //echo 
                    $str_info .= "ЗАПАСЫ РАВНЫ \n";
                }


            }
        }
    }
*/
   
?>