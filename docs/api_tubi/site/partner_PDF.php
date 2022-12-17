<?php

include '../connect.php';
include_once '../helper_classes.php';

//receive_partner_invoices_list
//receive_order_buyer_this
//receive_invoice_buyer_this


//получить company data
if(isset($_POST['counterparty_info'])){   
    $taxpayer_id = $_POST['taxpayer_id'];
    
    //найти counterparty_id
    $counterparty_id = searchCounterpartyId($con, $taxpayer_id);
    //получить данные(информацию) о компании
    $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
    echo json_encode($companyInfoList);   
}
//получить список ролей компании
else if(isset($_POST['receive_counterparty_role_list'])){   
    $counterparty_id = $_POST['counterparty_id'];
    
    //получить список ролей компании
    $counterparty_role_list = receive_counterparty_role_list($con, $counterparty_id);
    echo json_encode($counterparty_role_list);   
}
//получить список заказов покупателю
else if(isset($_POST['receive_partner_orders_list'])){   
    $counterparty_id = $_POST['counterparty_id'];
    
    //echo"partner true \n";
    $order_info_arr = receive_partner_orders_list($con, $counterparty_id);
    echo json_encode($order_info_arr);   
}
//показать выбраный заказ
else if(isset($_POST['receive_order_buyer_this'])){   
    $order_id = $_POST['order_id'];
    
    //echo"partner true \n";
    $this_order_info_arr = receive_order_buyer_this($con, $order_id);
    echo json_encode($this_order_info_arr);   
}
//получить список Расходныx накладных
if(isset($_POST['receive_partner_invoices_list'])){   
    $counterparty_id = $_POST['counterparty_id'];
    $document_name = $_POST['document_name'];
    $warehouse_type = $_POST['warehouse_type'];
    
    //echo"receive_partner_invoices_list true \n";
    $invoices_arr = receive_partner_invoices_list($con, $counterparty_id, $document_name, $warehouse_type);
    echo json_encode($invoices_arr);   
}
//показать выбранную накладную
else if(isset($_POST['receive_invoice_buyer_this'])){   
    $doc_num = $_POST['doc_num'];
    $invoice_key_id= $_POST['invoice_key_id'];
    
    //echo"invoice true \n";
    $this_invoice_info_arr = receive_invoice_buyer_this($con, $doc_num, $invoice_key_id);
    echo json_encode($this_invoice_info_arr);   
}
//получить список заказов поставщика
else if(isset($_POST['receive_provider_orders_list'])){   
    $counterparty_id = $_POST['counterparty_id'];
    
    //echo"partner true \n";
    $order_info_arr = receive_provider_orders_list($con, $counterparty_id);
    echo json_encode($order_info_arr);   
}
//показать выбраный заказ для поставщика от партнера
else if(isset($_POST['receive_order_partner_this'])){   
    $order_partner_id = $_POST['order_partner_id'];
    
    //echo"partner true \n";
    $this_order_info_arr = receive_order_partner_this($con, $order_partner_id);
    echo json_encode($this_order_info_arr);   
}
//показать выбраную  накладную поставщика
else if(isset($_POST['receive_invoice_provider_this'])){   
    $doc_num = $_POST['doc_num'];
    $invoice_key_id= $_POST['invoice_key_id'];
    
    //echo"invoice true \n";
    $this_invoice_info_arr = receive_invoice_provider_this($con, $doc_num, $invoice_key_id);
    echo json_encode($this_invoice_info_arr);   
}
//показать выбраную  накладную поставщика
function receive_invoice_provider_this($con, $docNum, $invoice_key_id){
    $this_invoice_info_arr = [];
    $this_invoice_info = [];
    $docName ='товарная накладная';
    //получить данные компаний участников
    $query="SELECT `out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`
                    FROM `t_invoice_key` WHERE `invoice_key_id`='$invoice_key_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $out_counterparty_id = $row[0];
    $out_warehouse_id = $row[1];
    $in_counterparty_id = $row[2];
    $in_warehouse_id = $row[3];

    //получить данные(информацию) о компании поставщика
    $companyInfoList = receiveCompanyInfo($con, $out_counterparty_id);
    $out_companyInfoString = $companyInfoList['companyInfoString'];
    //получить данные(информацию) о складе компании поставщика
    $out_warehouseInfoList = warehouseInfo($con,$out_warehouse_id);           
    $out_warehouseInfoString = $out_warehouseInfoList['warehouseInfoString'];
    
    //получить данные(информацию) о компании партнера
    $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
    $in_companyInfoString = $companyInfoList['companyInfoString'];
    //получить данные(информацию) о складе компании партнера
    $warehouseInfoList = warehouseInfo($con,$in_warehouse_id);           
    $in_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

    //получить дату создания документа
    $query="SELECT `created_at` FROM `t_document_deal` 
                WHERE `invoice_key_id`='$invoice_key_id' and `document_name` = '$docName' and `document_num` = '$docNum'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $created_at = $row[0];

    $date = date_parse( $created_at);
    $date_created_doc = $date['day'].".".$date['month'].".".$date['year'];

    $invoice_summ = 0;
    //получить данные о товарах в документе
    $query="SELECT  ii.quantity, 
                    ii.price, 
                    dd.description_docs
                    FROM t_invoice_info ii 
                        JOIN t_description_docs dd ON dd.description_docs_id = ii.description_docs_id
                    WHERE ii.invoice_key_id='$invoice_key_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    while($row=mysqli_fetch_array($result)){
        $quantity = $row[0];
        $full_price = $row[1];
        $description_docs = $row[2];

        //получить сумму
        $position_summ = $quantity * $full_price;
        $invoice_summ += $position_summ;
        //округлить до 2х знаков
        $position_summ = round($position_summ, 2);

        $this_invoice_info_arr[] = ['description_docs'=>$description_docs, 'quantity'=>$quantity
                    , 'full_price'=>$full_price, 'position_summ'=>$position_summ];
    }
    //округлить до 2х знаков
    $invoice_summ = round($invoice_summ, 2);
    $arr1 = [['out_companyInfoString'=>$out_companyInfoString, 'in_companyInfoString'=>$in_companyInfoString
            , 'out_warehouseInfoString'=> $out_warehouseInfoString, 'in_warehouseInfoString'=>$in_warehouseInfoString
            , 'date_created_doc'=>$date_created_doc, 'docNum'=>$docNum, 'invoice_summ'=>$invoice_summ
            , 'invoice_summ_text'=>num2str($invoice_summ)]];

    array_unshift($this_invoice_info_arr, $arr1);
    return $this_invoice_info_arr;

}
/*
        //проверить есть доставка
        $query="SELECT op.order_id,
                    o.delivery                            
                FROM t_order_product op 
                    JOIN t_order o ON o.order_id = op.order_id
                WHERE  op.invoice_key_id ='$invoice_key_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $order_id = $row[0];
        $delivery = $row[1];

        // доставка есть
        if($delivery == 1){
            //получить адрес доставки покупателю
            $address_for_delivery = receiveDeliveryAddress($con, $order_id);
            $in_warehouseInfoString = $GLOBALS['deliveri'] .": ". $address_for_delivery;
        }
        else{
            //нет доставки
            $in_warehouseInfoString = $GLOBALS['from_the_warehouse'] .": ". $GLOBALS['data_is_not'];
        }                
    
*/
//получить  список заказов lkz поставщика от расп.склада
function receive_provider_orders_list($con, $counterparty_id){
    $order_info_arr = [];

    $query="SELECT `order_partner_id`, `executed`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`, `get_order_date_millis`, `created_at` 
                    FROM `t_order_partner` WHERE `out_counterparty_id`='$counterparty_id' and `order_active`='1'";
    $result=mysqli_query($con,$query)or die (mysqli_error($con));
    while($row=mysqli_fetch_array($result)){
        $order_partner_id= $row[0];
        $executed= $row[1];
        $out_warehouse_id= $row[2];
        $in_counterparty_id= $row[3];
        $in_warehouse_id = $row[4];
        $get_order_date_millis= $row[5];
        $created_at= $row[6];

        //получить данные(информацию) о складе компании ппставщика
        $warehouseInfoList = warehouseInfo($con,$out_warehouse_id);
        $out_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

        //получить данные(информацию) о компании партнера
        $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
        $in_companyInfoString_short = $companyInfoList['companyInfoString_short'];
        //получить данные(информацию) о складе компании партнера
        $warehouseInfoList = warehouseInfo($con,$in_warehouse_id);
        $in_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

        //дата из миллисекунд
        $get_date = date("d.m.Y", $get_order_date_millis/1000);        
        //дата из даты sql
        $created_date = date( 'd.m.Y', strtotime( $created_at ));
        $order_summ = 0;
        $query="SELECT `quantity`, `price`, `price_process`
                    FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id'";
        $res=mysqli_query($con,$query)or die (mysqli_error($con));
        while($row=mysqli_fetch_array($res)){
            $quantity= $row[0];
            $price= $row[1];
            $price_process= $row[2];

            //сложить все суммы
            $order_summ += $quantity * ($price + $price_process);
        }
        //округлить до 2х знаков
        $order_summ = round($order_summ, 2);  
        $order_info_arr[] = ['order_id'=>$order_partner_id, 'created_date'=>$created_date
                            , 'out_warehouseInfoString'=>$out_warehouseInfoString
                            , 'in_companyInfoString_short'=>$in_companyInfoString_short
                            , 'in_warehouseInfoString'=>$in_warehouseInfoString
                            , 'order_summ'=>$order_summ, 'get_date'=>$get_date
                            , 'executed'=>$executed];

    }
    return $order_info_arr;
}
//получить список ролей компании
function receive_counterparty_role_list($con, $counterparty_id){
    $counterparty_role_list = [];
    $query="SELECT `role_partner` FROM `t_role_partner` WHERE `counterparty_id`='$counterparty_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    while($row=mysqli_fetch_array($result)){
        $role_partner = $row[0];
        $counterparty_role_list[] = $role_partner;
    }
    return $counterparty_role_list;
}
//показать выбранную накладную
function receive_invoice_buyer_this($con, $docNum, $invoice_key_id){
    $this_invoice_info_arr = [];
    $this_invoice_info = [];
    $docName ='товарная накладная';
    //получить данные компаний участников
    $query="SELECT `out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`, `in_warehouse_id`
                    FROM `t_invoice_key` WHERE `invoice_key_id`='$invoice_key_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $out_counterparty_id = $row[0];
    $out_warehouse_id = $row[1];
    $in_counterparty_id = $row[2];
    $in_warehouse_id = $row[3];

    //получить данные(информацию) о компании
    $companyInfoList = receiveCompanyInfo($con, $out_counterparty_id);
    $out_companyInfoString = $companyInfoList['companyInfoString'];

    //получить данные(информацию) о компании
    $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
    $in_companyInfoString = $companyInfoList['companyInfoString'];

    //получить данные(информацию) о складе и компании id
    $out_warehouseInfoList = warehouseInfo($con,$out_warehouse_id);           
    $out_warehouseInfoString = $out_warehouseInfoList['warehouseInfoString'];

    //получить данные(информацию) о складе и компании id
    if($in_warehouse_id != 0){
        $warehouseInfoList = warehouseInfo($con,$in_warehouse_id);           
        $in_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];
    }
    else{
        //проверить есть доставка
        $query="SELECT op.order_id,
                    o.delivery                            
                FROM t_order_product op 
                    JOIN t_order o ON o.order_id = op.order_id
                WHERE  op.invoice_key_id ='$invoice_key_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $order_id = $row[0];
        $delivery = $row[1];

        // доставка есть
        if($delivery == 1){
            //получить адрес доставки покупателю
            $address_for_delivery = receiveDeliveryAddress($con, $order_id);
            $in_warehouseInfoString = $GLOBALS['deliveri'] .": ". $address_for_delivery;
        }
        else{
            //нет доставки
            $in_warehouseInfoString = $GLOBALS['from_the_warehouse'] .": ". $GLOBALS['data_is_not'];
        }                
    }         


    //получить дату создания документа
    $query="SELECT `created_at` FROM `t_document_deal` 
                WHERE `invoice_key_id`='$invoice_key_id' and `document_name` = '$docName' and `document_num` = '$docNum'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $created_at = $row[0];

    $date = date_parse( $created_at);
    $date_created_doc = $date['day'].".".$date['month'].".".$date['year'];

    $invoice_summ = 0;
    //получить данные о товарах в документе
    $query="SELECT  op.quantity,
                    op.price,
                    op.price_process,
                    dd.description_docs
                    FROM t_order_product op
                        JOIN t_description_docs dd ON dd.description_docs_id = op.description_docs_id
                    WHERE op.invoice_key_id='$invoice_key_id'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    while($row=mysqli_fetch_array($result)){
        $quantity = $row[0];
        $price = $row[1];
        $price_process = $row[2];
        $description_docs = $row[3];

        $full_price = $price + $price_process;

        //получить сумму
        $position_summ = $quantity * $full_price;
        $invoice_summ += $position_summ;
        //округлить до 2х знаков
        $full_price = round($full_price, 2);
        $position_summ = round($position_summ, 2);

        $this_invoice_info_arr[] = ['description_docs'=>$description_docs, 'quantity'=>$quantity
                    , 'full_price'=>$full_price, 'position_summ'=>$position_summ];
        //echo $description_docs."&nbsp".$quantity."&nbsp".$full_price."&nbsp".$date_created_doc."<br>";
    } 
    //округлить до 2х знаков
    $invoice_summ = round($invoice_summ, 2);
    $arr1 = [['out_companyInfoString'=>$out_companyInfoString, 'in_companyInfoString'=>$in_companyInfoString
            , 'out_warehouseInfoString'=> $out_warehouseInfoString, 'in_warehouseInfoString'=>$in_warehouseInfoString
            , 'date_created_doc'=>$date_created_doc, 'docNum'=>$docNum, 'invoice_summ'=>$invoice_summ
            , 'invoice_summ_text'=>num2str($invoice_summ)]];

    

    //array_push($this_invoice_info_arr['0'], $arr1);
    array_unshift($this_invoice_info_arr, $arr1);
    return $this_invoice_info_arr;
}
//получить список Расходныx накладных
function receive_partner_invoices_list($con, $counterparty_id, $document_name, $warehouse_type){  
    $invoices_arr = [];  
    $query="SELECT `document_deal_id`, `invoice_key_id`, `document_name`, `document_num`, `created_at` 
                FROM `t_document_deal` WHERE `counterparty_id`='$counterparty_id' 
                AND `document_name`='$document_name' ORDER BY `document_num` DESC";
    $result=mysqli_query($con,$query)or die (mysqli_error($con));
    while($row=mysqli_fetch_array($result)){
        $document_deal_id= $row[0];
        $invoice_key_id= $row[1];
        $document_name= $row[2];
        $document_num= $row[3];
        $created_at= $row[4];

        $query="SELECT `out_warehouse_id`, `in_counterparty_id` 
                    FROM `t_invoice_key` WHERE `invoice_key_id`='$invoice_key_id'";
        $res=mysqli_query($con,$query)or die (mysqli_error($con));
        $row=mysqli_fetch_array($res);
        $out_warehouse_id= $row[0];
        $in_counterparty_id= $row[1];

        //получить тип склада 
        $out_warehouse_type = receive_warehouse_type($con, $out_warehouse_id);
        if($out_warehouse_type === $warehouse_type){
            $invoice_summ = 0;
            //посчитать сумму накладной
            if($warehouse_type === 'partner'){
                $query="SELECT `quantity`, `price`, `price_process`FROM `t_order_product` 
                                 WHERE `invoice_key_id`='$invoice_key_id'";
                $res=mysqli_query($con,$query)or die (mysqli_error($con));
                while($row=mysqli_fetch_array($res)){
                    $quantity= $row[0];
                    $price= $row[1];
                    $price_process= $row[2];

                    $invoice_summ += $quantity * ($price + $price_process);
                }
            }else{
                //сумма накладной для поставщика
                $query="SELECT  `quantity`, `price`
                                FROM `t_invoice_info` WHERE `invoice_key_id`='$invoice_key_id'";
                $res = mysqli_query($con, $query) or die (mysqli_error($con));
                while($row=mysqli_fetch_array($res)){
                    $quantity = $row[0];
                    $full_price = $row[1];

                    //получить сумму
                    $invoice_summ += $quantity * $full_price;
                }
            }            

            //дата из даты sql
            $created_date = date( 'd.m.Y', strtotime( $created_at ));

            //получить данные(информацию) о компании покупателя
            $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
            $in_companyInfoString_short = $companyInfoList['companyInfoString_short'];
            //округлить до 2х знаков
            $invoice_summ = round($invoice_summ, 2); 
            //получить данные(информацию) о складе компании партнера
            $warehouseInfoList = warehouseInfo($con,$out_warehouse_id);
            $out_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

            $invoices_arr[] = ['created_date'=>$created_date, 'document_num'=>$document_num
                                , 'in_companyInfoString_short'=>$in_companyInfoString_short
                                , 'invoice_summ'=>$invoice_summ
                                , 'out_warehouseInfoString'=>$out_warehouseInfoString
                                , 'invoice_key_id'=>$invoice_key_id];
        }
    }
    return $invoices_arr;
}
//показать выбраный заказ для поставщика от партнера
function receive_order_partner_this($con, $order_partner_id){
    $this_order_info_arr = [];
    //получить общие данные заказа
    $query="SELECT `order_partner_id`, `executed`, `out_counterparty_id`, `out_warehouse_id`, `in_counterparty_id`,`get_order_date_millis`, `created_at`
                        FROM `t_order_partner` WHERE `order_partner_id`='$order_partner_id'";
    $result=mysqli_query($con,$query)or die (mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $order_partner_id= $row[0];
    $executed= $row[1];
    $out_counterparty_id= $row[2];
    $out_warehouse_id= $row[3];
    $in_counterparty_id= $row[4];
    $get_order_date_millis= $row[5];
    $date_order_start= $row[6];

    //получить данные(информацию) о складе компании поставщика
    $warehouseInfoList = warehouseInfo($con,$out_warehouse_id);
    $out_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];
    $out_signboard = $warehouseInfoList['signboard'];
    //получить данные(информацию) о компании поставщика
    $companyInfoList = receiveCompanyInfo($con, $out_counterparty_id);
    $out_companyInfoString_short = $companyInfoList['companyInfoString_short'];

    //получить данные(информацию) о компании патнера
    $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
    $in_companyInfoString_short = $companyInfoList['companyInfoString_short'];
    //дата из миллисекунд
    $get_date = date("d.m.Y", $get_order_date_millis/1000);        
    //дата из даты sql
    $created_date = date( "d.m.Y", strtotime( $date_order_start ));

    $order_summ = 0;
    //получить summ заказа
    $query="SELECT `quantity`, `price`, `price_process`
                FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id'";    
    $res=mysqli_query($con,$query)or die (mysqli_error($con));
    while($row=mysqli_fetch_array($res)){
        $quantity= $row[0];
        $price= $row[1];
        //$price_process= $row[2];

        //для поставщика стоимость обработки не показываем
        //сложить все суммы
        $order_summ += $quantity * $price ;//+ $price_process);
    }
    //округлить до 2х знаков
    $order_summ = round($order_summ, 2); 
    $arr_1 = ['order_id'=>$order_partner_id, 'created_date'=>$created_date
                        , 'out_companyInfoString_short'=>$out_companyInfoString_short
                        , 'out_warehouseInfoString'=>$out_warehouseInfoString
                        , 'out_signboard'=>$out_signboard
                        , 'in_companyInfoString_short'=>$in_companyInfoString_short
                        , 'order_summ'=>$order_summ, 'get_date'=>$get_date
                        , 'executed'=>$executed];
    $this_order_info_arr[] = [$arr_1];

    //получить пзиции из заказа
    $query="SELECT `product_inventory_id`, `quantity`, `price`, `price_process` 
                    FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id'";
    $res=mysqli_query($con,$query)or die (mysqli_error($con));
    while($row=mysqli_fetch_array($res)){
        $product_inventory_id= $row[0];
        $quantity= $row[1];
        $price= $row[2];
        //$price_process= $row[3];

        //для поставщика стоимость обработки не показываем
        $full_price = $price;// + $price_process;
        //получить данные(информацию) по товару
        $product_list = receive_product_info($con, $product_inventory_id);     
        $product_name_from_provider=$product_list['product_name_from_provider'];
        $product_info=$product_list['product_info'];
        //получить сумму
        $position_summ = $quantity * $full_price;
        //округлить до 2х знаков
        $full_price = round($full_price, 2);
        $position_summ = round($position_summ, 2);

        $this_order_info_arr[] = ['product_info'=>$product_info, 'product_name_from_provider'=>$product_name_from_provider
                            , 'quantity'=>$quantity, 'full_price'=>$full_price, 'position_summ'=>$position_summ];
    }
    return $this_order_info_arr;
}
//показать выбраный заказ
function receive_order_buyer_this($con, $order_id){
    $this_order_info_arr = [];
    //получить общие данные заказа
    $query="SELECT `order_id`, `executed`, `warehouse_id`, `counterparty_id`, `get_order_date_millis`, `date_order_start`
                        FROM `t_order` WHERE `order_id`='$order_id'";
    $result=mysqli_query($con,$query)or die (mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $order_id= $row[0];
    $executed= $row[1];
    $warehouse_id= $row[2];
    $in_counterparty_id= $row[3];
    $get_order_date_millis= $row[4];
    $date_order_start= $row[5];

    //получить данные(информацию) о складе компании партнера
    $warehouseInfoList = warehouseInfo($con,$warehouse_id);
    $out_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];
    $out_signboard = $warehouseInfoList['signboard'];
    $out_counterparty_id = $warehouseInfoList['counterparty_id'];

    //получить данные(информацию) о компании партнера
    $companyInfoList = receiveCompanyInfo($con, $out_counterparty_id);
    $out_companyInfoString_short = $companyInfoList['companyInfoString_short'];
    
    //получить данные(информацию) о компании покупателя
    $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
    $in_companyInfoString_short = $companyInfoList['companyInfoString_short'];

    //дата из миллисекунд
    $get_date = date("d.m.Y", $get_order_date_millis/1000);        
    //дата из даты sql
    $created_date = date( "d.m.Y", strtotime( $date_order_start ));

    $order_summ = 0;
    //получить summ заказа
    $query="SELECT `quantity`, `price`, `price_process` 
                FROM `t_order_product` WHERE `order_id`='$order_id'";
    $res=mysqli_query($con,$query)or die (mysqli_error($con));
    while($row=mysqli_fetch_array($res)){
        $quantity= $row[0];
        $price= $row[1];
        $price_process= $row[2];

        //сложить все суммы
        $order_summ += $quantity * ($price + $price_process);
    }         
    //округлить до 2х знаков
    $order_summ = round($order_summ, 2);  
    $arr_1 = ['order_id'=>$order_id, 'created_date'=>$created_date
                        , 'out_companyInfoString_short'=>$out_companyInfoString_short
                        , 'out_warehouseInfoString'=>$out_warehouseInfoString
                        , 'out_signboard'=>$out_signboard
                        , 'in_companyInfoString_short'=>$in_companyInfoString_short
                        , 'order_summ'=>$order_summ, 'get_date'=>$get_date
                        , 'executed'=>$executed];
    $this_order_info_arr[] = [$arr_1];
    //получить пзиции из заказа
    $query="SELECT  `product_inventory_id`, `quantity`, `price`, `price_process`
                    FROM `t_order_product` WHERE `order_id`='$order_id'";
    $res=mysqli_query($con,$query)or die (mysqli_error($con));
    while($row=mysqli_fetch_array($res)){
        $product_inventory_id= $row[0];
        $quantity= $row[1];
        $price= $row[2];
        $price_process= $row[3];
        
        $full_price = $price + $price_process;
        //получить данные(информацию) по товару
        $product_list = receive_product_info($con, $product_inventory_id);     
        $product_name_from_provider=$product_list['product_name_from_provider'];
        $product_info=$product_list['product_info'];
        //получить сумму
        $position_summ = $quantity * $full_price;
        //округлить до 2х знаков
        $full_price = round($full_price, 2);
        $position_summ = round($position_summ, 2);
        $this_order_info_arr[] = ['product_info'=>$product_info, 'product_name_from_provider'=>$product_name_from_provider
                            , 'quantity'=>$quantity, 'full_price'=>$full_price, 'position_summ'=>$position_summ];
    } 
    return $this_order_info_arr;
}
//получить список заказов покупателю
function receive_partner_orders_list($con, $counterparty_id){
    $order_info_arr = [];
    //список складов этого поставщика
    $warehouse_info_list = receive_counterparty_warehouses($con, $counterparty_id, "partner");
    foreach($warehouse_info_list as $k => $warehouse_info){
        $warehouse_id = $warehouse_info['warehouse_id'];
        $active = $warehouse_info['active'];
    
        //если склад активен
        if($active == 1){
            $query="SELECT `order_id`, `executed`, `order_deleted`, `counterparty_id`, `get_order_date_millis`, `date_order_start`
                        FROM `t_order` WHERE `warehouse_id`='$warehouse_id' ORDER BY `order_id` DESC";
            $result=mysqli_query($con,$query)or die (mysqli_error($con));
            while($row=mysqli_fetch_array($result)){
                $order_id= $row[0];
                $executed= $row[1];
                $order_deleted= $row[2];
                $in_counterparty_id= $row[3];
                $get_order_date_millis= $row[4];
                $date_order_start= $row[5];
                //если заказ не удален
                if($order_deleted == 0){
                    //получить данные(информацию) о складе компании партнера
                    $warehouseInfoList = warehouseInfo($con,$warehouse_id);
                    $out_warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

                    //получить данные(информацию) о компании покупателя
                    $companyInfoList = receiveCompanyInfo($con, $in_counterparty_id);
                    $in_companyInfoString_short = $companyInfoList['companyInfoString_short'];

                    //дата из миллисекунд
                    $get_date = date("d.m.Y", $get_order_date_millis/1000);        
                    //дата из даты sql
                    $created_date = date( 'd.m.Y', strtotime( $date_order_start ));

                    $order_summ = 0;

                    $query="SELECT `quantity`, `price`, `price_process` 
                                FROM `t_order_product` WHERE `order_id`='$order_id'";
                    $res=mysqli_query($con,$query)or die (mysqli_error($con));
                    while($row=mysqli_fetch_array($res)){
                        $quantity= $row[0];
                        $price= $row[1];
                        $price_process= $row[2];

                        //сложить все суммы
                        $order_summ += $quantity * ($price + $price_process);
                    }         
                    //округлить до 2х знаков
                    $order_summ = round($order_summ, 2);  
                    $order_info_arr[] = ['order_id'=>$order_id, 'created_date'=>$created_date
                                        , 'out_warehouseInfoString'=>$out_warehouseInfoString
                                        , 'in_companyInfoString_short'=>$in_companyInfoString_short
                                        , 'in_warehouseInfoString'=>'нет данных'
                                        , 'order_summ'=>$order_summ, 'get_date'=>$get_date
                                        , 'executed'=>$executed];
            
                } 
            }  
        }              
    }    
    return $order_info_arr;   
}

?>