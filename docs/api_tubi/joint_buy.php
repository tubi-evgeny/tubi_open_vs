<?php
    include 'connect.php';
    include 'text.php';
    include_once 'helper_classes.php';
    include 'variable.php';
	 
	mysqli_query($con,"SET NAMES 'utf8'");

    //внести в таблицу новую заявку на совместную закупку товара
    //показать совместные заказы для этого склада
    //получить список складов партнеров


    //create_new_joint_buy
    //show_this_warehouse_joint_buy
    //get_partner_warehouse_list


    //внести в таблицу новую заявку на совместную закупку товара
    if(isset($_POST['create_new_joint_buy'])){
        $partner_warehouse_id = $_POST['partner_warehouse_id'];
        $product_inventory_id = $_POST['product_inventory_id'];
        $quantity_to_order = $_POST['quantityToOrder'];
        $user_uid = $_POST['user_uid'];
        $company_tax_id = $_POST['company_tax_id'];

        //найти user_id  
        $user_id=checkUserID($con, $user_uid);
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $company_tax_id);
        
        create_new_joint_buy($con, $partner_warehouse_id, $product_inventory_id, $quantity_to_order,$user_id, $counterparty_id);
    }
    //показать совместные заказы для этого склада
    else if(isset($_POST['show_this_warehouse_joint_buy'])){
        $partner_warehouse_id = $_POST['partner_warehouse_id'];
        
        show_this_warehouse_joint_buy($con, $partner_warehouse_id);
    }
    //получить список складов партнеров
    else if(isset($_POST['get_partner_warehouse_list'])){
        $region = $_POST['my_region'];
        
        get_partner_warehouse_list($con, $region);
    }
    //получить мой counterparty_id для поиска товаров в списке которых я учавствую
    else if(isset($_POST['get_my_counterpaty_id'])){
        $company_tax_id = $_POST['company_tax_id'];
        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $company_tax_id);
        echo $counterparty_id;
    }

    //получить список складов партнеров
    function get_partner_warehouse_list($con, $region){
        $partner_warehouse_list=[];
        $query="SELECT w.warehouse_id
                    FROM t_warehouse_info win
                        JOIN t_warehous w ON w.warehouse_info_id = win.warehouse_info_id 
                                            and w.warehouse_type = 'partner'
                    WHERE win.region='$region' and win.active='1' and win.help_warehose='0'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
        while($row = mysqli_fetch_array($result)){ 
            $warehouse_id=$row[0];
        
            //получить данные(информацию) о складе и компании id
            $warehouseInfoList = warehouseInfo($con,$warehouse_id);
            $warehouseInfoString = $warehouseInfoList['warehouseInfoString'];

            echo $warehouse_id."&nbsp".$warehouseInfoString."<br>";
            //$partner_warehouse_list[]=['warehouse_id'=>$warehouse_id,'warehouseInfoString'=>$warehouseInfoString];
        }
    }
    //показать совместные заказы для этого склада
    function show_this_warehouse_joint_buy($con, $partner_warehouse_id){
        $main_warehouse = $GLOBALS['main_warehouse']; 
        $product_inventory_id_list = [];
        $count_show=0;
        //получить расположение склада 
        $warehouseInfoList = warehouseInfo($con,$partner_warehouse_id);
        $in_region = $warehouseInfoList['region'];
        $in_district = $warehouseInfoList['district'];
        $in_city = $warehouseInfoList['city'];

        //сначала собрать все товары которые учавствуют в совместной закупке
        $query="SELECT `product_inventory_id` FROM `t_joint_buy`
                     WHERE `active`='0' and `exequted`='0' and `closed`='0' and `partner_warehouse_id`='$partner_warehouse_id' 
                        and `join_buy_delete`='0' ORDER BY `joint_buy_id` DESC";
        $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
        while($row = mysqli_fetch_array($result)){ 
            $product_inventory_id=$row[0];

            $product_inventory_id_list[] = $product_inventory_id;
        }
        $product_inventory_id_list = array_unique($product_inventory_id_list, SORT_REGULAR);
        foreach($product_inventory_id_list as $k => $product_inventory_id){
            //получить склад хранения этого товара 
            $provid_warehouse_id = check_storage_warehouse($con, $product_inventory_id, $in_city,$main_warehouse);
            //получить расположение склада 
            $warehouseInfoList = warehouseInfo($con,$provid_warehouse_id);
            $out_region = $warehouseInfoList['region'];
            $out_district = $warehouseInfoList['district'];
            $out_city = $warehouseInfoList['city'];

            //получить данные(информацию) по товару
            $product_list = receive_product_info($con, $product_inventory_id);  
            $product_id=$product_list['product_id'];              
            $image_url=$product_list['image_url'];
            $price=$product_list['price']; 
            $min_sell=$product_list['min_sell'];
            $product_info=$product_list['product_info'];
            $quantity_joint=0;
            $joint_buy_list=[];
           //получить колличество заказанного товара
            $query="SELECT  `quantity`,`counterparty_id` FROM `t_joint_buy` WHERE `active`='0' and `exequted`='0' 
                        and `closed`='0' and `product_inventory_id`='$product_inventory_id' 
                        and `partner_warehouse_id`='$partner_warehouse_id' and `join_buy_delete`='0'
                        ORDER BY `joint_buy_id` DESC";
            $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
            while($row = mysqli_fetch_array($result)){ 
                $quantity = $row[0];
                $counterparty_id = $row[1];

                $quantity_joint += $quantity;
                //получить данные(информацию) о компании
                $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
                $companyInfoString_short = $companyInfoList['companyInfoString_short'];

                $joint_buy_list[] = ['counterparty_id'=> $counterparty_id,'quantity_joint'=>$quantity
                                    ,'companyInfoString_short'=>$companyInfoString_short ];
                //echo "product_inventory_id = $product_inventory_id / quantity = $quantity <br>";
            }
            if($in_region != $out_region){ 
                //получить стоимость доставки товара
                $price_of_delivery_product = 
                    price_of_delivery_product($con,$product_inventory_id,$out_city, $in_city);
                //получить стоимость обработки товара
                $price_of_processing_product = 
                    price_of_processing_product($con,$product_inventory_id,$out_city, $in_city);
                //добавить и отнять доп расходы  
                //$tubi_commission = 1.01;  
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
                //if($delivery == 1){//доставка есть
               //     $delivery_in_moscow_commission = ($price * $GLOBALS['delivery_in_moscow_percent']) - $price ;
                //}                 
                //добавить доп расходы 
                $process_price = $tubi_commission + $warehouse_processing_commission + $delivery_in_moscow_commission;
                $process_price = round($process_price, 2);//округлить
            }
            
            echo $product_id."&nbsp".$product_inventory_id."&nbsp".$image_url."&nbsp".$price."&nbsp" 
                . $process_price."&nbsp".$min_sell."&nbsp".$quantity_joint."&nbsp".$product_info."&nbsp"
                .json_encode($joint_buy_list)."<br>";
                $count_show++; 
        }

    }
    //внести в таблицу новую заявку на совместную закупку товара
    function create_new_joint_buy($con, $partner_warehouse_id, $product_inventory_id, $quantity_to_order,$user_id, $counterparty_id){
        $main_warehouse = $GLOBALS['main_warehouse'];
        //найти counterparty_id по user_id
        //$counterparty_id = check_counterparty_id_by_user_id($con, $user_id);

        //проверить нет блокировки для создания заказов у компании
        $query="SELECT `block_order` FROM `t_counterparty` WHERE `counterparty_id`='$counterparty_id'";
        $result = mysqli_query($con, $query) or die (mysql_error($con));  
        $row = mysqli_fetch_array($result);
        $block_order=$row[0];
        if($block_order =='0'){
            //компания не может делать заказы из-за блокировки
            echo "message"."&nbsp".$GLOBALS['block_order_info'];
            return;
        }
        //получить ближайшую дату доставки товара 
        $date_of_sale_mil=getFirstDateDeliveryProduct($con, $product_inventory_id, $main_warehouse,$partner_warehouse_id);
        //если поставки товара на склад не найдена дата то не создавать заказ
        if($date_of_sale_mil == 0){
            echo "message"."&nbsp".$GLOBALS['no_delivery'];
        }else{
            $query="INSERT INTO `t_joint_buy`
                    (`product_inventory_id`,     `quantity`    ,  `partner_warehouse_id`, `counterparty_id`,`user_id`) 
            VALUES ('$product_inventory_id','$quantity_to_order','$partner_warehouse_id','$counterparty_id','$user_id')";
            $result = mysqli_query($con, $query) or die (mysqli_error($con)); 
            if($result){
                echo "RESULT_OK";
                //проверь собралось совм.заявок на создание заказа
                if(checkJointBuyRequest($con, $partner_warehouse_id, $product_inventory_id,$user_id,$counterparty_id)){
                    //отметить совм.заявки => активна, и открыть заказы при необходимости и внести товар в заказы 
                    //проверить и если надо то закыть совместную закупку и создать заказ
                    checkAndMakeOrderBuyTogether($con, $partner_warehouse_id, $product_inventory_id,$user_id,$counterparty_id);
                }
                //проверить и если надо то закыть совместную закупку и создать заказ
                //checkAndMakeOrderBuyTogether_two($con, $partner_warehouse_id, $product_inventory_id,$user_id,$counterparty_id);
            }else{
                echo "NO_RESULT";
            }   
        }    

    }




mysqli_close($con);
?>