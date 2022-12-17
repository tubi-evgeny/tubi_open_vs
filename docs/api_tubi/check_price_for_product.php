<?php
	 include 'connect.php';
     include 'text.php';
     include_once 'helper_classes.php';

	 
   mysqli_query($con,"SET NAMES 'utf8'");
   //check_price_for_product.php?check_start

   
        //найти активный заказ
    if(isset($_GET['check_start'])){     
             
        //make_trim_to_nomenklature($con);

        $taxpayer_id_number = start_check_price($con);

        //какая позиция не найдена
        echo "<br>------ ищем позиции которые есть в татлице но не найдены в прайсе -------<br><br>";
        which_position_was_not_found($con, $taxpayer_id_number);

        clear_table_t_check_price_for_product($con);
  
    }

    //какая позиция не найдена
    function which_position_was_not_found($con, $taxpayer_id_number){
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

            echo "OK - id= $price_id / my_id = $product_inventory_id name =  $in_product_name<br>";
        }
        echo "<br>----------------------------------------<br><br>";
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
                echo "В ПРАЙСЕ НЕТ ЭТОГО ТОВАРА / my_id = $product_inventory_id name =  $in_product_name<br>";                
            }         
            $my_flag = false;   
        }        
        //найдены описания схожие но с синтаксическими или др ошибками 
            echo "<br>найдены описания схожие но с синтаксическими или др ошибками 
                        <br> double product count = $double_product_count <br>";
    }

    function start_check_price($con){
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
                $query="SELECT  `in_product_name`, `package_price`, `unit_price`, `taxpayer_id_number`
                             FROM `t_check_price_for_product` WHERE `id`='$id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                $row = mysqli_fetch_array($res);
                $in_product_name = $row[0];
                $package_price = $row[1];
                $unit_price = $row[2];
                $taxpayer_id_number = $row[3];
                //найти в каталоге tubi этот товар                
                $query="SELECT `product_inventory_id` 
                            FROM `t_inventory_vs_inproductname` 
                        WHERE `taxpayer_id_number`='$taxpayer_id_number' and `in_product_name`='$in_product_name'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));   
                if(mysqli_num_rows($res) > 1){ 
                    echo "найдены дубликаты товара <br>";
                    while($row = mysqli_fetch_array($res)){
                        $product_inventory_id =  $row[0];
                        
                        echo $in_product_name . "= product_inventory_id: " . $product_inventory_id . "<br>";
                    }
                }else if(mysqli_num_rows($res) > 0){
                    //товар найден ищем текущую цену и сравниваем

                    echo "$in_product_name - ";

                    $row = mysqli_fetch_array($res);
                    $product_inventory_id =  $row[0];
                    $query="SELECT  `price`, `quantity_package` FROM `t_product_inventory`
                                 WHERE `product_inventory_id`='$product_inventory_id'";
                    $res = mysqli_query($con, $query) or die (mysqli_error($con));
                    $row = mysqli_fetch_array($res);
                    $price = $row[0];
                    $quantity_package = $row[1];
                    if($unit_price != 0){
                        if($price != $unit_price){
                            chenge_price($con, $product_inventory_id, $unit_price);
                        }else{
                            echo "ЦЕНА СООТВЕТСТВУЕТ <br>";
                        }
                    }else if($package_price != 0){
                        $unit_price = $package_price / $quantity_package;
                        $unit_price = round($unit_price, 2);
                        if($price != $unit_price){
                            chenge_price($con, $product_inventory_id, $unit_price);
                        }else{
                            echo "ЦЕНА СООТВЕТСТВУЕТ <br>";
                        }
                    }
                    echo "<br>";
                }
            }
            
        }
        return $taxpayer_id_number;
    }
    function chenge_price($con, $product_inventory_id, $unit_price){
        $query="UPDATE `t_product_inventory` SET `price`='$unit_price'
                 WHERE `product_inventory_id`='$product_inventory_id'";
        mysqli_query($con, $query) or die (mysqli_error($con));
        echo "chenge PRICE id: $product_inventory_id <br>";
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