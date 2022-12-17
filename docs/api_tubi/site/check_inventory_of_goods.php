<?php
	 include '../connect.php';
     include '../text.php';
     include_once '../helper_classes.php';

	 
   mysqli_query($con,"SET NAMES 'utf8'");
   //check_inventory_of_goods.php?check_inventory_start

   
        //найти активный заказ
    //if(isset($_GET['check_inventory_start'])){    
        
        $str_info = "ИНФОРМАЦИЯ ЗАГРУЗКИ\n";
             
        //начать исправление остатков запасов товар у поставщика
        $arr_return = start_check_inventory_002($con, $str_info);

        $taxpayer_id_number = $arr_return['taxpayer_id_number'];
        $str_info = $arr_return['str_info'];
        
        //$taxpayer_id_number = start_check_inventory($con);

        //какая позиция не найдена
        //echo 
        $str_info .= "\n------ ищем позиции которые есть в таблице но не найдены в (запасах поставщика) -------\n\n";
        
        $str_info = which_position_was_not_found($con, $taxpayer_id_number, $str_info);
              

        $table_clear = clear_table_t_check_inventory_of_goods($con);//TRUNCATE TABLE table_name

        return $table_clear . $str_info;
        
   // }

   //очистить таблицу
   function clear_table_t_check_inventory_of_goods($con){
        $query="TRUNCATE TABLE `t_check_inventory_of_goods`";
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

            $query="SELECT `id`,`warehouse_id` FROM `t_check_inventory_of_goods` WHERE `in_product_name`='$in_product_name'";
            $result = mysqli_query($con, $query) or die (mysqli_error($con));   
            if(mysqli_num_rows($result) > 0){        
                $row = mysqli_fetch_array($result);
                    $price_id =  $row[0];
                    $warehouse_id = $row[1];

                    $id_true_list[] = ['price_id'=>$price_id, 'product_inventory_id'=>$product_inventory_id
                                        , 'in_product_name'=>$in_product_name];
                    
                   // echo "OK - id= $id / my_id = $product_inventory_id name =  $in_product_name<br>";
                
            }else{
                $warehouse_id = '0';
                $id_false_list[] = ['product_inventory_id'=>$product_inventory_id
                ,'in_product_name'=>$in_product_name, 'warehouse_id'=>$warehouse_id];
                //echo "В ПРАЙСЕ НЕТ ЭТОГО ТОВАРА / my_id = $product_inventory_id name =  $in_product_name<br>";
            }
        }
        //покажем все успешные /найденые/ товары
        foreach($id_true_list as $k => $id_true){
            $price_id = $id_true['price_id'];
            $product_inventory_id = $id_true['product_inventory_id'];
            $in_product_name = $id_true['in_product_name'];

            //echo 
            $str_info .= "OK - id= $price_id / my_id = $product_inventory_id name =  $in_product_name\n";
        }
       // echo 
        $str_info .= "\n---------  В ЛИСТЕ ЗАПАСЫ НЕТ ЭТОГО ТОВАРА  ----------\n\n";
        $my_flag = false;
        $double_product_count = 0;
        //покажем товары которые не были найдены
        foreach($id_false_list as $k => $id_false){
            $product_inventory_id = $id_false['product_inventory_id'];
            $in_product_name = $id_false['in_product_name'];
            $provider_warehouse_id = $id_false['warehouse_id'];

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
                //echo 
                $str_info .= "my_id = $product_inventory_id name =  $in_product_name\n";    //В ЛИСТЕ ЗАПАСЫ НЕТ ЭТОГО ТОВАРА /             
                //сделать на складе запас товара = 0
                
                //получить остаток товара на складе
                $provider_stock_quantity = stock_product_to_warehouse($con, $provider_warehouse_id, $product_inventory_id);
            
            }         
            $my_flag = false;   
        }        
        //найдены описания схожие но с синтаксическими или др ошибками 
            //echo
            $str_info .= "\nнайдены описания схожие но с синтаксическими или др ошибками, 
                            \ndouble product count = $double_product_count \n";

        return $str_info;
    }

    //начать исправление запасов товарa у поставщика
    function start_check_inventory_002($con, $str_info){
        $taxpayer_id_number = 0;
        $procent = 0.7;
        $all_products_info = array();

        //получить инн постаавщика
        $query="SELECT `taxpayer_id_number`, `warehouse_id` 
                    FROM `t_check_inventory_of_goods` WHERE `id` = '1'";
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
            WHERE `taxpayer_id_number`='$taxpayer_id_number'";// and  `taxpayer_id_number`='$taxpayer_id_number'";
        $res = mysqli_query($con, $query) or die (mysqli_error($con)); 
        if(mysqli_num_rows($res) > 0){ 
            while($row = mysqli_fetch_array($res)){
                $my_product_inventory_id =  $row[0];
                $my_in_product_name =  addslashes($row[1]);

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
            $query="SELECT `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number` 
                        FROM `t_check_inventory_of_goods` WHERE `in_product_name` = '$my_in_product_name'";
            $res = mysqli_query($con, $query) or die (mysqli_error($con));
            if(mysqli_num_rows($res) > 0){
                $row = mysqli_fetch_array($res);
                $in_product_name = $row[0];
                $accounting_unit = $row[1];
                $quantity = $row[2];
                $provider_warehouse_id = $row[3];
                $taxpayer_id_number = $row[4];

                //товар найден ищем текущий запас и сравниваем
                //echo 
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
            
            
            }else{
                //товар не найден в новом списке(запасы на складе) от поставщика
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

        $arr_return['taxpayer_id_number'] = $taxpayer_id_number ;
        $arr_return['str_info'] = $str_info;
        return $arr_return;
    }

    //начать исправление запасов товарa у поставщика
   /* function start_check_inventory($con){
        $taxpayer_id_number = 0;
        $procent = 0.7;
     

        $query="SELECT `id`, `in_product_name`, `accounting_unit`, `quantity`, `taxpayer_id_number` 
                    FROM `t_check_inventory_of_goods` WHERE `id` > '0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
        if(mysqli_num_rows($result) > 0){        
            while($row = mysqli_fetch_array($result)){
                $id =  $row[0];
                
                $id_list[] = $id;
            }
            foreach($id_list as $k => $id){
                //получить строку из таблици сверки запасов
                $query="SELECT `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number` 
                            FROM `t_check_inventory_of_goods` WHERE `id`='$id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($res);
                $in_product_name = $row[0];
                $accounting_unit = $row[1];
                $quantity = $row[2];
                $provider_warehouse_id = $row[3];
                $taxpayer_id_number = $row[4];
               

                //найти в каталоге tubi этот товар                
                $query="SELECT `id`, `product_inventory_id` 
                            FROM `t_inventory_vs_inproductname` 
                        WHERE `taxpayer_id_number`='$taxpayer_id_number' and `in_product_name`='$in_product_name'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));   
                if(mysqli_num_rows($res) > 1){ 
                    //echo 
                    $str_info .= "найдены дубликаты товара <br>";
                    while($row = mysqli_fetch_array($res)){
                        $id =  $row[0];
                        $product_inventory_id =  $row[1];
                        
                        //echo 
                        $str_info .= $in_product_name . "id= $id / product_inventory_id= " . $product_inventory_id . "<br>";
                    }
                }else if(mysqli_num_rows($res) > 0){
                    //товар найден ищем текущий запас и сравниваем
                    //echo 
                    $str_info .= "$in_product_name - ";

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
                    }else{
                        

                        $quantity_finish = $quantity * $quantity_package;
    
                    }
                    //$multiple_of = 1;
                   // $min_sell = 1;
                    //получить остаток товара на складе
                    $provider_stock_quantity = stock_product_to_warehouse($con, $provider_warehouse_id, $product_inventory_id);

                    //проверить в какую сторону провести изменения добавить товара или уменьшить
                    //запас больше или равен минимальной продаже
                    if($quantity_finish >= $min_sell){

                        // уменьшаем запас на коэфициент (на личные продажи поставщика)
                        //$make_reserve = $quantity_finish * $procent;

                        $make_reserve = $quantity_finish;

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
                            $str_info .= "ЗАПАС  изменен на 0 <br>";
                        }
                        else{
                            //echo 
                            $str_info .= "ОСТАТОК НА СКЛАДЕ РАВЕН = 0; ИЛИ ОТРИЦАТЕЛЬНЫЙ действие уменьшить отменено <br>";
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
                        $str_info .= "ТОВАР добавлен <br>";

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
                            $str_info .= "ТОВАР уменньшен <br>";
                    }else{
                        //echo 
                        $str_info .= "ЗАПАСЫ РАВНЫ <br>";
                    }
                    
                }
            }
        }
        else{
            //echo 
            $str_info .= "нет информации в таблице <br>";
        }
        return $taxpayer_id_number;
    }*/


    mysqli_close($con);

  
?>