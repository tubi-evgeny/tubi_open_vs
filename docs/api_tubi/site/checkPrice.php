<?php

include '../connect.php';
include_once '../helper_classes.php';


//получить список id поставщиков
if(isset($_POST['provider'])){   
    //echo"provider true\n";
    get_provider_info_list($con);   
}
//получить список складов этого поставщика
else if(isset($_POST['warehouse_list_this_provider'])){   
    $provider_id = $_POST['provider_id'];
    $warehouse_type = 'provider'; 

    get_warehouse_list_provider($con, $provider_id, $warehouse_type);
}
//загружаем таблицу в БД
else if(isset($_POST['upload_file'])){   
    $tableUpload = $_POST['tableUpload'];
    $counterparty_id = $_POST['counterparty_id']; 
    $warehouse_id = $_POST['warehouse_id'];
    $table_list = $_POST['excelArrCreated'];
    $stock_status = $_POST['stock_status']; 

    //загружаем таблицу price в БД
    if($tableUpload == 'upload_price_file'){
        upload_price_file($con, $counterparty_id, $warehouse_id, $table_list);
    }//загружаем таблицу запасы в БД
    else if($tableUpload == 'upload_stock_file'){
        //берем запас из таблицы
        if($stock_status == 'table'){
            upload_stock_file($con, $counterparty_id, $warehouse_id, $table_list);
        }//делаем запасы как указанно
        else if($stock_status == 'full'){
            upload_stock_full_file($con, $counterparty_id, $warehouse_id, $table_list);
        }//делаем запас = 0
        else if($stock_status == 'null'){
            upload_stock_null_file($con, $counterparty_id, $warehouse_id, $table_list);
        }

        
    }
}
//делаем запасы = 0, загружаем таблицу запасы в БД
function upload_stock_null_file($con, $counterparty_id, $warehouse_id, $table_list){
    //получить данные(информацию) о компании
    $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
    $taxpayer_id_number = $companyInfoList['taxpayer_id_number'];

    $column_1 = -1;
    //получаем номер столбцa в таблице
    foreach($table_list as $key => $vol){
        foreach($vol as $k => $v){
            if($v == 'in_product_name'){
                $column_1 = $k;
            }
        }   
    }
    $in_product_name = ""; 
    $accounting_unit = "шт";
    $quantity = 0;//сколько коробок переносим на склад
    $count = 0;
    foreach($table_list as $key => $vol){
        //записываем со 2й позиции, пропускаем 1 позицию
        if($key > 0){
            if($column_1 != -1){
                $in_product_name = addslashes(trim($vol[$column_1])); 
            }
            try{
                if($in_product_name != "in_product_name"){                    
                    $query="INSERT INTO `t_check_inventory_of_goods`
                                    ( `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number`) 
                            VALUES ('$in_product_name','$accounting_unit','$quantity','$warehouse_id','$taxpayer_id_number')";
                    $result=mysqli_query($con,$query)or die (mysqli_error($con));                    

                    $count++;
                    //записываем пару позиций и сбрасываем, остальные сами обнулятся
                    if($count > 5) break;
                }    
            }catch(Exception $ex){
                echo "Error: $ex";
            }              
        }    
    }
    echo "Загружено $count позиций из ".count($table_list). "\n";

    $str_return_info = include_once 'check_inventory_of_goods.php';

    echo $str_return_info;
}
//делаем запасы как указанно, загружаем таблицу запасы в БД
function upload_stock_full_file($con, $counterparty_id, $warehouse_id, $table_list){
    //получить данные(информацию) о компании
    $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
    $taxpayer_id_number = $companyInfoList['taxpayer_id_number'];

    $column_1 = -1;
    //получаем номер столбцa в таблице
    foreach($table_list as $key => $vol){
        foreach($vol as $k => $v){
            if($v == 'in_product_name'){
                $column_1 = $k;
            }
        }   
    }
    $in_product_name = ""; 
    $accounting_unit = "упак";
    $quantity = 12;//сколько коробок переносим на склад
    $count = 0;
    
    foreach($table_list as $key => $vol){
        //записываем со 2й позиции, пропускаем 1 позицию
        if($key > 0){
            if($column_1 != -1){
                $in_product_name = addslashes(trim($vol[$column_1])); 
            }
            try{
                if($in_product_name != "in_product_name"){  
                    //проверить мин.колич.продажи больше количества в упаковке то изменить колличество упаковок
                    //найти в каталоге tubi этот товар                
                    $query="SELECT `product_inventory_id` 
                                    FROM `t_inventory_vs_inproductname` 
                                WHERE `taxpayer_id_number`='$taxpayer_id_number' 
                                    and `in_product_name`='$in_product_name'";
                    $res = mysqli_query($con, $query) or die (mysqli_error($con));
                    $row = mysqli_fetch_array($res);
                    $product_inventory_id =  $row[0];
                    //получить колличество в упаковке из t_product_inventory
                    //получить данные(информацию) по товару
                    $product_list = receive_product_info($con, $product_inventory_id);                        
                    $quantity_package=$product_list['quantity_package'];
                    $min_sell=$product_list['min_sell'];
                    if($quantity_package < $min_sell){
                        $my_quantity = $quantity * $min_sell;
                    }else{
                        $my_quantity = $quantity;
                    }

                    $query="INSERT INTO `t_check_inventory_of_goods`
                                    ( `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number`) 
                            VALUES ('$in_product_name','$accounting_unit','$my_quantity','$warehouse_id','$taxpayer_id_number')";
                    $result=mysqli_query($con,$query)or die (mysqli_error($con));                    

                    $count++;
                }    
            }catch(Exception $ex){
                echo "Error: $ex";
            }              
        }    
    }
    echo "Загружено $count позиций из ".count($table_list). "\n";

    $str_return_info = include_once 'check_inventory_of_goods.php';

    echo $str_return_info;
}
//загружаем таблицу запасы в БД
function upload_stock_file($con, $counterparty_id, $warehouse_id, $table_list){
    //получить данные(информацию) о компании
    $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
    $taxpayer_id_number = $companyInfoList['taxpayer_id_number'];

    $column_1 = -1;
    $column_2 = -1;
    $column_3 = -1;
    //получаем номера столбцов в таблице
    foreach($table_list as $key => $vol){
        foreach($vol as $k => $v){
            if($v == 'in_product_name'){
                $column_1 = $k;
            }else if($v == 'accounting_unit'){
                $column_2 = $k;
            }else if($v == 'quantity'){
                $column_3 = $k;
            }
        }   
    }
    $in_product_name = ""; 
    $accounting_unit = "";
    $quantity = 0;
    $count = 0;
    foreach($table_list as $key => $vol){
        //записываем со 2й позиции, пропускаем 1 позицию
        if($key > 0){
            if($column_1 != -1){
                $in_product_name = addslashes(trim($vol[$column_1])); 
            }
            if($column_2 != -1){
                $accounting_unit = trim($vol[$column_2]);
            }
            if($column_3 != -1){
                $quantity = trim($vol[$column_3]);
            }
            try{
                if($in_product_name != "in_product_name"){
                    $query="INSERT INTO `t_check_inventory_of_goods`
                                    ( `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number`) 
                            VALUES ('$in_product_name','$accounting_unit','$quantity','$warehouse_id','$taxpayer_id_number')";
                    $result=mysqli_query($con,$query)or die (mysqli_error($con)); 

                    $count++;
                }    
            }catch(Exception $ex){
                echo "Error: $ex";
            }             
        }    
    }
    echo "Загружено $count позиций из ".count($table_list). "\n";

    $str_return_info = include_once 'check_inventory_of_goods.php';

    echo $str_return_info;

}
//загружаем таблицу price в БД
function upload_price_file($con, $counterparty_id, $warehouse_id, $table_list){
    //получить данные(информацию) о компании
    $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
    $taxpayer_id_number = $companyInfoList['taxpayer_id_number'];

    $column_1 = -1;
    $column_2 = -1;
    $column_3 = -1;
    $column_4 = -1;
    $column_5 = -1;
    //получаем номера столбцов в таблице
    foreach($table_list as $key => $vol){
        foreach($vol as $k => $v){
            if($v == 'in_product_name'){
                $column_1 = $k;
            }else if($v == 'package_price'){
                $column_2 = $k;
            }else if($v == 'unit_price'){
                $column_3 = $k;
            }else if($v == 'accounting_unit'){
                $column_4 = $k;
            }else if($v == 'barcode_article'){
                $column_5 = $k;
            }
        }   
    }
    $in_product_name = ""; 
    $package_price = 0.00;
    $unit_price = 0.00;
    $accounting_unit = "";
    $barcode_article = "0";
    $count = 0;
    foreach($table_list as $key => $vol){
        //записываем со 2й позиции, пропускаем 1 позицию
        if($key > 0){
            if($column_1 != -1){
                $in_product_name = addslashes(trim($vol[$column_1])); 
            }
            if($column_2 != -1){
                $package_price = trim($vol[$column_2]);
            }
            if($column_3 != -1){
                $unit_price = trim($vol[$column_3]);
            }
            if($column_4 != -1){
                $accounting_unit = trim($vol[$column_4]);
            }
            if($column_5 != -1){
                $barcode_article = trim($vol[$column_5]);
            }

            try{
                if($in_product_name != "in_product_name"){
                    $query="INSERT INTO `t_check_price_for_product`
                                    (`in_product_name`,  `package_price`,  `unit_price`, `accounting_unit`, `barcode_article`, `taxpayer_id_number`) 
                            VALUES ('$in_product_name', '$package_price', '$unit_price', '$accounting_unit','$barcode_article', '$taxpayer_id_number')";
                    $result=mysqli_query($con,$query)or die (mysqli_error($con)); 

                    $count++;
                }  
            }catch(Exception $ex){
                //echo "Error: $ex";
            }  
        }    
    }
    echo "Загружено $count позиций из ".count($table_list). "\n";

    $str_return_info = include_once 'check_price_for_product.php';

    echo $str_return_info;
}
//получить список складов этого поставщика
function get_warehouse_list_provider($con, $provider_id, $warehouse_type){
    $list = [];

    //список складов этого поставщика
    $warehouse_info_list = receive_counterparty_warehouses($con, $provider_id, $warehouse_type);
    foreach($warehouse_info_list as $k => $warehouse_info){
        $warehouse_id = $warehouse_info['warehouse_id'];

        //получить данные(информацию) о складе и компании id
        $warehouseInfoList = warehouseInfo($con,$warehouse_id);
        $warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

        $list[] = ['warehouse_id' => $warehouse_id, 'warehouseInfoString'=>$warehouseInfoString];
    }
    echo json_encode($list);
}
/*
function chengeGoodsStock($con, $table_list){
    $count = 0;
    
    foreach($table_list as $key => $vol){

        $in_product_name = $vol[0]; 
        $accounting_unit = $vol[1];
        $quantity = $vol[2];
        $warehouse_id = $vol[3];
        $taxpayer_id_number = $vol[4];

        try{
            if($in_product_name != "in_product_name"){
                $query="INSERT INTO `t_check_inventory_of_goods`
                                ( `in_product_name`, `accounting_unit`, `quantity`, `warehouse_id`, `taxpayer_id_number`) 
                        VALUES ('$in_product_name','$accounting_unit','$quantity','$warehouse_id','$taxpayer_id_number')";
                $result=mysqli_query($con,$query)or die (mysqli_error($con)); 

                $count++;
            }    
        }catch(Exception $ex){
            //echo "Error: $ex";
        }
    }       

    echo "Загружено $count позиций из ".count($table_list). "\n";

    $str_return_info = include_once 'check_inventory_of_goods.php';

    echo $str_return_info;

}*/

//получить список id поставщиков
function get_provider_info_list($con){
    $provider_info_list = [];
    //список id поставщиков
    $counterparty_id_list=get_provider_list($con);
    foreach($counterparty_id_list as $k => $counterparty_id){
        //echo "k = $k; counterparty_id = $counterparty_id \n";
    
        //получить данные(информацию) о компании
        $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
        $companyInfoString = $companyInfoList['companyInfoString'];

        $provider_info_list[] = ['counterparty_id'=>$counterparty_id
                        , 'companyInfoString'=>$companyInfoString];
    }
    echo json_encode( $provider_info_list);
}

function chengePrice($con, $table_list){
    $count = 0;

    /*  foreach($table_list as $key => $vol){

        $in_product_name = $vol[0]; 
        $package_price = $vol[1];
        $unit_price = $vol[2];
        $taxpayer_id_number = $vol[3];

        try{
            if($in_product_name != "in_product_name"){
                $query="INSERT INTO `t_check_price_for_product`
                                (`in_product_name`,  `package_price`,  `unit_price`,  `taxpayer_id_number`) 
                        VALUES ('$in_product_name', '$package_price', '$unit_price', '$taxpayer_id_number')";
                $result=mysqli_query($con,$query)or die (mysqli_error($con)); 

                $count++;
            }  
        }catch(Exception $ex){
            //echo "Error: $ex";
        }  
    }*/

    echo "Загружено $count позиций из ".count($table_list). "\n";

    $str_return_info = "test";//include_once 'check_price_for_product.php';

    echo $str_return_info;
}

    mysqli_close($con);

?>