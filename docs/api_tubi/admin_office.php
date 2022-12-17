<?php
    include 'connect.php';
    include 'text.php';
    include_once 'helper_classes.php';
    include 'variable.php';

    //$data
    //list_product_in_order
    //list_providers_processing_make_orders
    //show_provider_candidates
    //show_new_buyer
    //order_delete
    //check_partner_provider_role
    //check_partner_warehouse_role
	 
	mysqli_query($con,"SET NAMES 'utf8'");
	 
     //есть товары для модерации?
    if(isset($_GET['search_new_input_product'])){
        
        $result = search_new_input_product($con);
        echo $result . "<br>";
        
    }//показать кандидатов в поставщики
    else if(isset($_GET['show_provider_candidates'])){
        $role = "provider_business";

        showProviderCandidates_001($con,$role);
        //showProviderCandidates($con,$role);

       
    }//показать кандидатов в партнеры склада
    else if(isset($_GET['candidate_for_partner_warehouse'])){
        $role = "partner_warehouse";

        candidate_for_partner_warehouse($con,$role);       
       
    } //есть кандидаты в поставщки?
    else if(isset($_GET['search_new_candidate_for_provider'])){
        $role = "provider_business";

        search_new_candidate_for_provider($con,$role);
    
    }//есть кандидаты в партнер склада?
    else if(isset($_GET['search_new_candidate_for_partner_warehouse'])){
        $role = "partner_warehouse";

        search_new_candidate_for_partner_warehouse($con,$role);
    
    }
   
    //получить коментарий к договору
    else if(isset($_GET['receive_coment_for_contract'])){
        $role_partner_id = $_GET['role_partner_id'];
       // $role_for_moderation_id = $_GET['role_for_moderation_id'];

       receive_coment_for_contract_001($con, $role_partner_id);
        //receive_coment_for_contract($con, $role_for_moderation_id);

        //записать шаг подготовки договора поставщика
    }//записать шаг подготовки договора поставщика
    else if(isset($_GET['provider_contract_step'])){
        $role_partner_id = $_GET['role_partner_id'];
       // $role_for_moderation_id = $_GET['role_for_moderation_id'];
        $contract_step = $_GET['contract_step'];
        $moderator_id = $_GET['uid'];

        provider_contract_step_001($con, $role_partner_id, $contract_step, $moderator_id);
        //provider_contract_step($con, $role_for_moderation_id, $contract_step, $moderator_id);
        
    }//role_partner_id
    //получить все шаги по подготовке и подписанию договора
    else if(isset($_GET['receive_contract_step'])){
        $role_partner_id = $_GET['role_partner_id'];       

        receive_contract_step_001($con, $role_partner_id);
        
    }
    /*
//получить все шаги по подготовке и подписанию договора
    else if(isset($_GET['receive_contract_step'])){
        $role_for_moderation_id = $_GET['role_for_moderation_id'];       

        receive_contract_step($con, $role_for_moderation_id);
        
    }
    */
    //добавить роль поставщика и в список договоров
    else if(isset($_GET['add_provider'])){
        $role_partner_id = $_GET['role_partner_id'];  
        $user_uid = $_GET['moderator_uid'];
        $partner_user_id = $_GET['user_id'];
        $role_partner_admin= "partner_admin";  
        
        $moderator_id = checkUserID($con, $user_uid);
        add_provider_001($con, $moderator_id, $role_partner_id,$partner_user_id,$role_partner_admin);       
        
    }
    /*
    //добавить роль поставщика и в список договоров
    else if(isset($_GET['add_provider'])){
        $role_for_moderation_id = $_GET['role_for_moderation_id']; 
        $user_uid = $_GET['moderator_uid'];
        $contract_name=$GLOBALS['contract_name'];  
        
        $moderator_id = checkUserID($con, $user_uid);
        $user_id = checkUserIDFromRole($con, $role_for_moderation_id);
        $counterparty_id = checkCounterpartyIDFromRole($con, $role_for_moderation_id);
        if($counterparty_id > 0){

            add_provider($con,$contract_name, $moderator_id,$counterparty_id,$user_id,$role_for_moderation_id);
        }

        
    }
    */
    //есть ли покупатели которые делают покупку первый раз
    else if(isset($_GET['search_order_for_new_buyer'])){
        
        search_order_for_new_buyer($con);
    
        //показать новых покупателей
    }else if(isset($_GET['show_new_buyer'])){
        
        show_new_buyer($con);
    
        //показать список продуктов в заказе
    }else if(isset($_GET['list_product_in_order'])){
        $order_id = $_GET['order_id'];

        list_product_in_order($con,$order_id);
    
        //модерировать нового заказчика и одобрить его заказ
    }else if(isset($_GET['order_approved'])){
        $order_id = $_GET['order_id'];
        $moderator_id = $_GET['user_id'];
        $comment = $_GET['comment'];

        order_approved($con,$order_id, $moderator_id,$comment);
    //модерировать (удалить) нового заказчика и его заказ(ы)
    }else if(isset($_GET['order_delete'])){
        $order_id = $_GET['order_id'];
        $moderator_id = $_GET['user_id'];
        $comment = $_GET['comment'];

        order_delete($con,$order_id, $moderator_id,$comment);
    
    }//получить отчеты от поставщиков о сборке товара и готовности к отправке
    else if(isset($_GET['list_providers_processing_make_orders'])){        

        list_providers_processing_make_orders_001($con);  
        //list_providers_processing_make_orders($con); 

    }//Агент, список заказов для сборки товара агентом  
    else if(isset($_GET['show_all_order_for_collect'])){        

        show_all_order_for_collect($con);    

    }//получить список поставщиков заказы которых готовы к приемке на склад
    else if(isset($_GET['list_provider_which_sellect_order'])){        

        list_provider_which_sellect_order($con); 

    }// агент получает список товаров поступивших от поставщика на склад агента
    else if(isset($_GET['receive_list_product_arrived_from_provider'])){
        $taxpayer_id = $_GET['taxpayer_id'];

        $result = receive_list_product_arrived_from_provider($con,$taxpayer_id);

        if(empty($result)){
            $result = "messege" . "&nbsp" . "Нет каталога" . "<br>";
        }

        $res = receive_list_product_arrived_from_provider_001($con,$taxpayer_id);

        if(empty($res)){
            $res = "messege" . "&nbsp" . "Нет каталога" . "<br>";
        }

        echo $result;
       // echo "--------------------------------------" . "<br>";
       // echo $res;
    }//получить рыбу договора
    else if(isset($_GET['receive_sample_contract'])){
        $contractSampleName = $_GET['contract_sample_name'];

        receive_sample_contract($con,$contractSampleName);
        
    }// присвоение партнеру new роли 
    else if(isset($_GET['transfer_request_partner_role_new'])){
        $user_uid = $_GET['user_uid'];
        $role = $_GET['role'];
        $comment = $_GET['comment'];
        $counterparty_tax_id = $_GET['counterparty_tax_id'];
        $contract_sample_id = $_GET['contract_sample_id'];

        $user_id = checkUserID($con, $user_uid);
        //$counterparty_id = receiveCounterpartyID($con,$counterparty_tax_id);

        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);

        $res = transfer_request_partner_role_new($con, $role, $user_id, $comment, $counterparty_id,$contract_sample_id);
        if($res){
            echo "RESULT_OK";
        }else{
            echo "error" . "&nbsp" . "Что-то пошло не так и ваш договор не был доставлен. 
                                    Попробуйте отправить его позже." . "<br>";
        }        
    }//проверить является контрагент (партнером склада)
    else if(isset($_GET['check_partner_warehouse_role'])){
        $counterparty_tax_id = $_GET['counterparty_tax_id'];
        $role = $_GET['role'];

       // $counterparty_id = receiveCounterpartyID($con,$counterparty_tax_id);

        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);
        
        check_partner_role($con,$counterparty_id,$role);
        
    }//проверить является контрагент (поставщиком)
    else if(isset($_GET['check_partner_provider_role'])){
        $counterparty_tax_id = $_GET['counterparty_tax_id'];
        $role = $_GET['role'];

        //$counterparty_id = receiveCounterpartyID($con,$counterparty_tax_id);

        //найти counterparty_id
        $counterparty_id = searchCounterpartyId($con, $counterparty_tax_id);
        
        check_partner_role($con,$counterparty_id,$role);
        
    }

    
    
    //проверить является контрагент (партнером склада)
    function check_partner_role($con,$counterparty_id,$role){
        $query = "SELECT `active` FROM `t_role_partner` 
                                    WHERE `role_partner` = '$role' AND `counterparty_id`='$counterparty_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));        
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            $role_active = $row[0];

            echo "RESULT_OK" . "&nbsp" . $role_active . "<br>";
        }else{
            echo "RESULT_NO" . "<br>";
        }
        
    }
    
    //присвоение partner new роли 
    function transfer_request_partner_role_new($con, $role, $user_id, $comment, $counterparty_id,$contract_sample_id){
        $query = "INSERT INTO `t_comment`(`comment`) VALUES ('$comment')";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $comment_id = mysqli_insert_id($con);


        $query = "INSERT INTO `t_role_partner`(`contract_sample_id`, `role_partner`, `user_id`, `comment_id`, `counterparty_id`) 
                          VALUES ('$contract_sample_id','$role','$user_id','$comment_id','$counterparty_id')";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
       
        if ($result === FALSE) {
            die( mysql_error() );
            return false;
        }else{
            return true;
        }
    }

    //получить рыбу договора
    function receive_sample_contract($con,$contractSampleName){
        $query = "SELECT `contract_sample_id`,`contract_sample` FROM `t_contract_sample` 
                            WHERE `contract_sample_name` = '$contractSampleName' AND  `active`='1'";
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
        if($result){
            $row = mysqli_fetch_array($result);
            $contract_sample_id = $row[0];
            $contractSample = $row[1];
            echo $contract_sample_id . "&nbsp" . $contractSample . "<br>";
            //echo $contract_sample_id . "<br>";
        }else{
            echo "error" . "&nbsp" . "Договор не найден попробуйте еще раз позже" . "<br>";
        }
    }
    // агент получает список товаров поступивших от поставщика на склад агента
    function receive_list_product_arrived_from_provider($con,$taxpayer_id){
        $res = "";
        $query="SELECT `counterparty_id` FROM `t_counterparty` WHERE `taxpayer_id_number`='$taxpayer_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        //echo "product in receive 1" . "<br>";
        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_array($result);
            $counterparty_id=$row[0];
                //получить список активных заказов покупателей
            $query="SELECT `order_id` FROM `t_order` WHERE `order_active`='1'AND `executed`='0'";
            $res_order_list=mysqli_query($con, $query) or die(mysqli_error($con));
            //echo "product in receive 2" . "<br>";
            if(mysqli_num_rows($res_order_list) > 0){
                while($row=mysqli_fetch_array($res_order_list)){
                    $order_id=$row[0];
                    //получить (product_inventory_id) для поиска товаров поставщика в таблице t_product_inventory
                    $query="SELECT `order_product_id`, `product_inventory_id`, `quantity` FROM `t_order_product` 
                            WHERE `order_id` = '$order_id'";
                    $res_prod_order=mysqli_query($con, $query) or die(mysqli_error($con));
                    //echo "product in receive 3" . "<br>";
                    while($row=mysqli_fetch_array($res_prod_order)){
                        $order_product_id =$row[0];
                        $product_inventory_id=$row[1];
                        $quantity=$row[2];
                        //echo "product_inventory_id: " . $product_inventory_id . "<br>";
                        //ищем товары поставщика
                        $query_pi = "SELECT pr.product_id,                            
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
                                pcp.yes_no,
                                agcp.yes_no                         
                        
                        FROM t_product_inventory pi
                            JOIN t_image im          ON im.image_id            = pi.image_id
                            JOIN t_description de    ON de.description_id      = pi.description_id
                            JOIN t_product pr        ON pr.product_id          = pi.product_id
                            JOIN t_category cat      ON cat.category_id        = pr.category_id
                            JOIN t_brand br          ON br.brand_id            = pr.brand_id
                            JOIN t_characteristic cr ON cr.characteristic_id   = pr.characteristic_id
                            JOIN t_type_packaging tp ON tp.type_packaging_id   = pr.type_packaging_id 
                            JOIN t_unit_measure um   ON um.unit_measure_id     = pr.unit_measure_id 
                            JOIN t_order_product op  ON op.order_product_id    = '$order_product_id'
                            JOIN t_provider_collect_product pcp ON pcp.prov_collect_prod_id = op.prov_collect_prod_id
                            JOIN t_agent_collect_product agcp ON agcp.agent_collect_prod_id = op.agent_collect_prod_id

                        WHERE pi.product_inventory_id='$product_inventory_id' AND pi.counterparty_id='$counterparty_id'";
                        
                        $result_pi = mysqli_query($con, $query_pi) or die (mysqli_error($con));

                        if(mysqli_num_rows($result_pi) > 0){
                            $row = mysqli_fetch_array($result_pi);
                            $product_id=$row[0];
                            $category=$row[1]; $brand=$row[2]; $characteristic=$row[3]; 
                            $type_packaging=$row[4]; $unit_measure=$row[5]; $weight_volume=$row[6]; //$quantity_1=$row[7];
                            $price=$row[7]; $quantity_package= $row[8]; $image_url=$row[9]; $description=$row[10];
                           // $orderProcessing=$row[11];
                            $providerProcessing=$row[11]; $agentProcessing = $row[12];

                           if($providerProcessing == '1'){

                            $res .= $order_product_id . "&nbsp" . $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . "&nbsp" . $brand . "&nbsp" . $characteristic. 
                            "&nbsp" . $type_packaging . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . //"&nbsp" . $price . 
                            "&nbsp" . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $quantity . "&nbsp" . $providerProcessing 
                            . "&nbsp" . $order_id. "&nbsp" . $agentProcessing. "<br>";

                           }
                        }                        
                    }
                }
            }
        }
        return $res;
    }
   // агент получает список товаров поступивших от поставщика на склад агента
    function receive_list_product_arrived_from_provider_001($con,$taxpayer_id){
        $res = "";
        $query="SELECT `counterparty_id` FROM `t_counterparty` WHERE `taxpayer_id_number`='$taxpayer_id'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        //echo "product in receive 1" . "<br>";
        if(mysqli_num_rows($result) > 0){
            $row=mysqli_fetch_array($result);
            $counterparty_id=$row[0];
                //получить список активных заказов покупателей
            $query="SELECT `order_id` FROM `t_order` WHERE `order_active`='1'AND `executed`='0'";
            $res_order_list=mysqli_query($con, $query) or die(mysqli_error($con));
            //echo "product in receive 2" . "<br>";
            if(mysqli_num_rows($res_order_list) > 0){
                while($row=mysqli_fetch_array($res_order_list)){
                    $order_id=$row[0];
                    //получить (product_inventory_id) для поиска товаров поставщика в таблице t_product_inventory
                    $query="SELECT `order_product_id`, `product_inventory_id`, `quantity` FROM `t_order_product` 
                            WHERE `order_id` = '$order_id'";
                    $res_prod_order=mysqli_query($con, $query) or die(mysqli_error($con));
                    //echo "product in receive 3" . "<br>";
                    while($row=mysqli_fetch_array($res_prod_order)){
                        $order_product_id =$row[0];
                        $product_inventory_id=$row[1];
                        $quantity=$row[2];
                        //echo "product_inventory_id: " . $product_inventory_id . "<br>";
                        //ищем товары поставщика
                        $query_pi = "SELECT pr.product_id,                            
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
                                pcp.yes_no                         
                        
                        FROM t_product_inventory pi
                            JOIN t_image im          ON im.image_id            = pi.image_id
                            JOIN t_description de    ON de.description_id      = pi.description_id
                            JOIN t_product pr        ON pr.product_id          = pi.product_id
                            JOIN t_category cat      ON cat.category_id        = pr.category_id
                            JOIN t_brand br          ON br.brand_id            = pr.brand_id
                            JOIN t_characteristic cr ON cr.characteristic_id   = pr.characteristic_id
                            JOIN t_type_packaging tp ON tp.type_packaging_id   = pr.type_packaging_id 
                            JOIN t_unit_measure um   ON um.unit_measure_id     = pr.unit_measure_id 
                            JOIN t_order_product op  ON op.order_product_id    = '$order_product_id'
                            JOIN t_provider_collect_product pcp ON pcp.prov_collect_prod_id = op.prov_collect_prod_id
                                                
                        WHERE pi.product_inventory_id='$product_inventory_id' AND pi.counterparty_id='$counterparty_id'";

                        $result_pi = mysqli_query($con, $query_pi) or die (mysqli_error($con));

                            if(mysqli_num_rows($result_pi) > 0){
                            $row = mysqli_fetch_array($result_pi);
                            $product_id=$row[0];
                            $category=$row[1]; $brand=$row[2]; $characteristic=$row[3]; 
                            $type_packaging=$row[4]; $unit_measure=$row[5]; $weight_volume=$row[6]; //$quantity_1=$row[7];
                            $price=$row[7]; $quantity_package= $row[8]; $image_url=$row[9]; $description=$row[10];
                            $orderProcessing=$row[11];

                            $provider_product_in_box='provider_product_in_box';
                        // $orderProcessing = provider_product_in_box($con, $order_product_id,$provider_product_in_box);
    //SELECT `id`, `prov_collect_prod_id`, `yes_no`, `user_id`, `created_at`, `up_user_id`, `updated_at` FROM `t_provider_collect_product` WHERE 1

                            $res .= $order_product_id . "&nbsp" . $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . "&nbsp" . $brand . "&nbsp" . $characteristic. 
                            "&nbsp" . $type_packaging . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $price . 
                            "&nbsp" . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $quantity . "&nbsp" . $orderProcessing 
                            . "&nbsp" . $order_id. "<br>";

                        }
                        
                    }
                }
            }
        }
        return $res;
    }
    //получить список поставщиков заказы которых готовы к приемке на склад
    function list_provider_which_sellect_order($con){
        $provider_list = array();
        $provider_temp= array();
        //получить все активные и не выполненные заказы
        $query="SELECT `order_id` FROM `t_order` WHERE `order_active`='1' AND `executed`='0'";
        $result = mysqli_query($con, $query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $order_id = $row[0];
                //получить все `product_inventory_id` каждого заказа 
                $query="SELECT `product_inventory_id` FROM `t_order_product` WHERE `order_id`='$order_id'";
                $res = mysqli_query($con, $query)or die (mysqli_error($con));
                while($row=mysqli_fetch_array($res)){
                    $product_inventory_id = $row[0];
                    //получить данные о компаниях присутствующих в заказах
                    $query="SELECT counterparty_id FROM t_product_inventory                                
                            WHERE product_inventory_id = '$product_inventory_id' ";
                    $res_1 = mysqli_query($con, $query)or die (mysqli_error($con)); 
                    $row=mysqli_fetch_array($res_1);  
                    $counterparty_id=$row[0];
                    $provider_temp[] =  $counterparty_id;                    
                }
            }
        } else{
            echo "messege" . "&nbsp" . $GLOBALS['date_is_not'] . "<br>";
            return;
        }        

        //удаляем копии
        $provider_list = array_unique($provider_temp);
       
        foreach ($provider_list as  $k => $v) {
           // echo " $v" . "<br>";
            $counterparty_id = $v;
           // echo " $counterparty_id" . "<br>";
            //собираем полные данные о поставщике
            $query="SELECT `abbreviation`, `counterparty`, `taxpayer_id_number` 
                        FROM `t_counterparty` WHERE `counterparty_id`='$counterparty_id'";
            $result = mysqli_query($con, $query)or die (mysqli_error($con));
            $row=mysqli_fetch_array($result);
                $abbreviation = $row[0];
                $counterparty = $row[1];
                $taxpayer_id_number = $row[2];
                echo $counterparty_id . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" . $taxpayer_id_number . "<br>";
            
        }
        
    }
    //Агент, список заказов для сборки товара агентом 
    function show_all_order_for_collect($con){
        $query = "";
    }
    //получить отчеты от поставщиков о сборке товара и готовности к отправке
    function list_providers_processing_make_orders_001($con){
        //получить все оrder_id активные=1 и не выполненные=0
        $query="SELECT `order_partner_id`FROM `t_order_partner` WHERE  `order_active`='1' and `executed`='0'";
        $result = mysqli_query($con, $query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $order_partner_id=$row[0];
                //получить все 'order_product_id' и 'product_inventory_id' для поиска поставщиков этих товаров
                $query="SELECT opp.order_product_part_id,
                                opp.product_inventory_id, 
                                opp.collected,
                                win.out_active 
                                FROM t_order_product_part opp 
                                     JOIN t_warehouse_inventory_in_out win ON win.warehouse_inventory_id = opp.warehouse_inventory_id
                                WHERE opp.order_partner_id='$order_partner_id'";
                $res_2 = mysqli_query($con, $query)or die (mysqli_error($con));
                while($row=mysqli_fetch_array($res_2)){
                    $order_product_part_id=$row[0];
                    $product_inventory_id = $row[1];                    
                    $collected= $row[2];
                    $out_active= $row[3];

                    //если товар собран и выдан (логисту или партнеру) то пропускаем, если не выдан показываем
                    if($out_active == 0){
                        //данные о компании поставщике
                        $query="SELECT pi.counterparty_id,
                                        c.abbreviation,
                                        c.counterparty                                
                                FROM t_product_inventory pi
                                    JOIN t_counterparty c      ON c.counterparty_id = pi.counterparty_id                                
                                WHERE pi.product_inventory_id='$product_inventory_id'";
                        $res_4 = mysqli_query($con, $query)or die (mysqli_error($con));
                        $row=mysqli_fetch_array($res_4);
                        $counterparty_id=$row[0];
                        $abbreviation=$row[1];
                        $counterparty=$row[2];
                            //колличество и обьем всего товара поставщика , к отгрузке(для выбора траспорта)                   
                        $query_1="SELECT op.quantity,
                                p.weight_volume

                        FROM t_order_product_part op 
                            JOIN t_product_inventory pi ON pi.product_inventory_id = op.product_inventory_id
                            JOIN t_product p            ON p.product_id  = pi.product_id
                        WHERE op.order_product_part_id='$order_product_part_id'";
                        $res_6=mysqli_query($con, $query_1)or die (mysqli_error($con));
                        $row=mysqli_fetch_array($res_6);
                        $quantity=$row[0];
                        $weight_volume=$row[1];
                        $sum_weight_volume = $quantity * $weight_volume;

                        //получаем состояние сборки товара поставщиком
                        if($collected > 0){
                            $processing_condition = 'provider_product_in_box';
                        }else{
                            $processing_condition = '0';
                        }

                        echo $counterparty_id . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" 
                        . $processing_condition . "&nbsp" . $sum_weight_volume . "<br>";

                        //echo "order: $order_partner_id";
                                    
                    }
                }
            }           
            
        }else {
            echo "messege" . "&nbsp" . $GLOBALS['date_is_not'] . "<br>";
        }
    }
    //получить отчеты от поставщиков о сборке товара и готовности к отправке
   /* function list_providers_processing_make_orders_001($con){
        //получить все оrder_id активные=1 и не выполненные=0
        $query="SELECT `order_partner_id`FROM `t_order_partner` WHERE  `order_active`='1' and `executed`='0'";
        $result = mysqli_query($con, $query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $order_partner_id=$row[0];
                //получить все 'order_product_id' и 'product_inventory_id' для поиска поставщиков этих товаров
                $query="SELECT `order_product_part_id`, `product_inventory_id`, `collected` 
                                        FROM `t_order_product_part` WHERE `order_partner_id`='$order_partner_id'";
                $res_2 = mysqli_query($con, $query)or die (mysqli_error($con));
                while($row=mysqli_fetch_array($res_2)){
                    $order_product_part_id=$row[0];
                    $product_inventory_id = $row[1];
                    //$prov_collect_prod_id= $row[2];
                    $collected= $row[2];
                        //данные о компании поставщике
                    $query="SELECT pi.counterparty_id,
                                    c.abbreviation,
                                    c.counterparty                                
                            FROM t_product_inventory pi
                                JOIN t_counterparty c      ON c.counterparty_id = pi.counterparty_id                                
                            WHERE pi.product_inventory_id='$product_inventory_id'";
                    $res_4 = mysqli_query($con, $query)or die (mysqli_error($con));
                    $row=mysqli_fetch_array($res_4);
                    $counterparty_id=$row[0];
                    $abbreviation=$row[1];
                    $counterparty=$row[2];
                        //колличество и обьем всего товара поставщика , к отгрузке(для выбора траспорта)                   
                    $query_1="SELECT op.quantity,
                            p.weight_volume

                    FROM t_order_product_part op 
                        JOIN t_product_inventory pi ON pi.product_inventory_id = op.product_inventory_id
                        JOIN t_product p            ON p.product_id  = pi.product_id
                    WHERE op.order_product_part_id='$order_product_part_id'";
                    $res_6=mysqli_query($con, $query_1)or die (mysqli_error($con));
                    $row=mysqli_fetch_array($res_6);
                    $quantity=$row[0];
                    $weight_volume=$row[1];
                    $sum_weight_volume = $quantity * $weight_volume;

                    //получаем состояние сборки товара поставщиком
                     if($collected > 0){
                        $processing_condition = 'provider_product_in_box';
                    }else{
                        $processing_condition = '0';
                    }

                    echo $counterparty_id . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" 
                    . $processing_condition . "&nbsp" . $sum_weight_volume . "<br>";
                                 
                }
            }           
            
        }else {
            echo "messege" . "&nbsp" . $GLOBALS['date_is_not'] . "<br>";
        }
    }*/
    //получить отчеты от поставщиков о сборке товара и готовности к отправке
    /*function list_providers_processing_make_orders_001($con){
        //получить все оrder_id активные=1 и не выполненные=0
        $query="SELECT `order_id` FROM `t_order` WHERE `order_active`='1' AND `executed`='0'";
        $result = mysqli_query($con, $query)or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row=mysqli_fetch_array($result)){
                $order_id=$row[0];
                //получить все 'order_product_id' и 'product_inventory_id' для поиска поставщиков этих товаров
                $query="SELECT `order_product_id`,`product_inventory_id`,`prov_collect_prod_id` FROM `t_order_product` WHERE `order_id`='$order_id'";
                $res_2 = mysqli_query($con, $query)or die (mysqli_error($con));
                while($row=mysqli_fetch_array($res_2)){
                    $order_product_id=$row[0];
                    $product_inventory_id = $row[1];
                    $prov_collect_prod_id= $row[2];
                        //данные о компании поставщике
                    $query="SELECT pi.counterparty_id,
                                    c.abbreviation,
                                    c.counterparty                                
                            FROM t_product_inventory pi
                                JOIN t_counterparty c      ON c.counterparty_id = pi.counterparty_id                                
                            WHERE pi.product_inventory_id='$product_inventory_id'";
                    $res_4 = mysqli_query($con, $query)or die (mysqli_error($con));
                    $row=mysqli_fetch_array($res_4);
                    $counterparty_id=$row[0];
                    $abbreviation=$row[1];
                    $counterparty=$row[2];
                        //колличество и обьем всего товара поставщика , к отгрузке(для выбора траспорта)
                    $query_1="SELECT op.quantity,
                                    p.weight_volume

                            FROM t_order_product op 
                                JOIN t_product_inventory pi ON pi.product_inventory_id = op.product_inventory_id
                                JOIN t_product p            ON p.product_id  = pi.product_id
                            WHERE op.order_product_id='$order_product_id'";
                    $res_6=mysqli_query($con, $query_1)or die (mysqli_error($con));
                    $row=mysqli_fetch_array($res_6);
                    $quantity=$row[0];
                    $weight_volume=$row[1];
                    $sum_weight_volume = $quantity * $weight_volume;

                    //получаем состояние сборки товара поставщиком

                    $query="SELECT `prov_collect_prod_id` FROM `t_provider_collect_product` 
                            WHERE `prov_collect_prod_id` = '$prov_collect_prod_id'  AND `yes_no` = '1' AND `closed` = '0'";
                     $res_5 = mysqli_query($con, $query)or die (mysqli_error($con));
                     if(mysqli_num_rows($res_5) > 0){
                         $processing_condition = 'provider_product_in_box';
                     }else{
                         $processing_condition = '0';
                     }

                    echo $counterparty_id . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" 
                    . $processing_condition . "&nbsp" . $sum_weight_volume . "<br>";
                                 
                }
            }           
            
        }else {
            echo "messege" . "&nbsp" . $GLOBALS['date_is_not'] . "<br>";
        }
    }*/
   
    //модерировать (удалить) нового заказчика и его заказ(ы)
    function order_delete($con,$order_id, $moderator_id,$comment){
        //получить id пользователя оформившего заказ
        $query = "SELECT `user_id` FROM `t_order` WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            $row=mysqli_fetch_array($result);
            $user_id = $row[0];
            //получить все заказы от проверяемого пользователя
            $query = "SELECT `order_id` FROM `t_order` WHERE `user_id`='$user_id'";
            $result = mysqli_query($con, $query) or die(mysqli_error($con));
            if($result){
                while($row=mysqli_fetch_array($result)){
                    $order_id = $row[0];
                    //смодерировать (удалить) заказы этого пользователя 
                    $query="UPDATE `t_order_from_new_buyer` SET `moderator_id`='$moderator_id',`moderation`='1',
                                     `comment`='$comment' WHERE `order_id`='$order_id'";
                    $res = mysqli_query($con, $query) or die(mysqli_error($con));

                    $query="UPDATE `t_order_product` SET `order_prod_deleted`='1'
                                 WHERE `order_id`='$order_id'";
                    mysqli_query($con, $query) or die(mysqli_error($con));

                    $query="UPDATE `t_order` SET `order_deleted`='1'
                                    WHERE `order_id`='$order_id'";
                    mysqli_query($con, $query) or die(mysqli_error($con));

                   /* $query="DELETE FROM `t_order_product` WHERE `order_id`='$order_id'";
                    $res = mysqli_query($con, $query) or die(mysqli_error($con));

                    $query="DELETE FROM `t_order` WHERE `order_id`='$order_id'";
                    $res = mysqli_query($con, $query) or die(mysqli_error($con));*/
                }
            }
        }
    }

    //модерировать нового заказчика и одобрить его заказ
    function order_approved($con,$order_id, $moderator_id,$comment){
        //смодерировать заказ от модератора
        $query = "UPDATE `t_order_from_new_buyer` SET `moderator_id`='$moderator_id',`moderation`='1',
                            `comment`='$comment' WHERE `order_id`='$order_id'";
        $result=mysqli_query($con,$query) or die(mysqli_error($con));
                            
        //получить id пользователя оформившего заказ
        $query = "SELECT `user_id` FROM `t_order` WHERE `order_id`='$order_id'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            $row=mysqli_fetch_array($result);
            $user_id = $row[0];
            //получить все заказы от проверяемого пользователя
            $query = "SELECT `order_id` FROM `t_order` WHERE `user_id`='$user_id'";
            $result = mysqli_query($con, $query) or die(mysqli_error($con));
            if($result){
                while($row=mysqli_fetch_array($result)){
                    $order_id = $row[0];
                    //смодерировать остальные заказы этого пользователя если они есть
                    $query="UPDATE `t_order_from_new_buyer` SET `moderation`='1',`comment`='$comment'
                         WHERE `order_id`='$order_id' AND `moderation`='0'";
                    $res=mysqli_query($con,$query) or die(mysqli_error($con));
                }
            }
        }

    }
    //показать список продуктов в заказе
    function list_product_in_order($con,$order_id){
        $query = "SELECT pi.product_id,
                         op.product_inventory_id,
                         cat.category,
                         br.brand,
                         cr.characteristic,
                         tp.type_packaging,
                         um.unit_measure,
                         pr.weight_volume,
                         op.quantity,
                         pi.price,
                         pi.quantity_package,
                         im.image_url,
                         de.description
                          
                    FROM t_order_product  op
                        JOIN t_product_inventory pi ON pi.product_inventory_id = op.product_inventory_id
                        JOIN t_product pr           ON pr.product_id           = pi.product_id
                        JOIN t_category cat         ON cat.category_id         = pr.category_id
                        JOIN t_brand br             ON br.brand_id             = pr.brand_id
                        JOIN t_characteristic cr    ON cr.characteristic_id    = pr.characteristic_id
                        JOIN t_type_packaging tp    ON tp.type_packaging_id    = pr.type_packaging_id
                        JOIN t_unit_measure um      ON um.unit_measure_id      = pr.unit_measure_id 
                        JOIN t_description de       ON de.description_id       = pi.description_id
                        JOIN t_image im             ON im.image_id             = pi.image_id
                    WHERE order_id = '$order_id'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            while($row=mysqli_fetch_array($result)){
                $product_id=$row[0]; $product_inventory_id=$row[1]; $category=$row[2];$brand=$row[3];$characteristic=$row[4];
                $type_packaging=$row[5]; $unit_measure=$row[6]; $weight_volume=$row[7]; $total_quantity=$row[8]; $price=$row[9];
                $quantity_package= $row[10]; $image_url=$row[11]; $description=$row[12];

                
                echo $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . "&nbsp" . $brand . "&nbsp" . $characteristic .
                 "&nbsp" . $type_packaging . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $total_quantity . "&nbsp" . $price . 
                 "&nbsp" . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $description . "<br>";
            }
        }
    }
    /*
//получить каталог товаров поставщика с данными остатков, продаж, колличества поставки
function product_provider_array($con, $taxpayer_id){
    //-----найти counterparty_id
    $counterparty_id=serchCounterpartyId($con, $taxpayer_id);
    $res = "";
    if($counterparty_id != 0){
    //получить каталог товаров поставщика
         $query = "SELECT pr.product_id,
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
                            de.description                         
                       
                    FROM t_product_inventory pi
                        JOIN t_image im          ON im.image_id          = pi.image_id
                        JOIN t_description de    ON de.description_id    = pi.description_id
                        JOIN t_product pr        ON pr.product_id        = pi.product_id
                        JOIN t_category cat      ON cat.category_id      = pr.category_id
                        JOIN t_brand br          ON br.brand_id          = pr.brand_id
                        JOIN t_characteristic cr ON cr.characteristic_id = pr.characteristic_id
                        JOIN t_type_packaging tp ON tp.type_packaging_id = pr.type_packaging_id 
                        JOIN t_unit_measure um   ON um.unit_measure_id   = pr.unit_measure_id 
                    WHERE pi.counterparty_id = $counterparty_id";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));

        while($row = mysqli_fetch_array($result)){
            $product_id=$row[0]; $product_inventory_id=$row[1]; $category=$row[2]; $brand=$row[3]; $characteristic=$row[4]; 
            $type_packaging=$row[5]; $unit_measure=$row[6]; $weight_volume=$row[7]; $total_quantity=$row[8]; $price=$row[9];
            $quantity_package= $row[10]; $image_url=$row[11]; $description=$row[12];

            //получить данные продаж поставщика , колличество за весь период
            $total_sale_quantity = saleProductThisProvider($con,$product_inventory_id);

            //посчитать свободный остаток на сладе
            $free_balance = $total_quantity - $total_sale_quantity;

            $res .= $product_id . "&nbsp" . $product_inventory_id. "&nbsp" . $category . "&nbsp" . $brand . "&nbsp" . $characteristic. 
            "&nbsp" . $type_packaging . "&nbsp" . $unit_measure . "&nbsp" . $weight_volume . "&nbsp" . $total_quantity . "&nbsp" . $price . 
            "&nbsp" . $quantity_package . "&nbsp" . $image_url . "&nbsp" . $description . "&nbsp" . $total_sale_quantity . "&nbsp" . $free_balance . "<br>";

            //echo "total_sale_quantity " . $total_sale_quantity . "<br>";
            //echo "free_balance " . $free_balance . "<br>";

           // echo $row[0] . "&nbsp" . $row[1] . "&nbsp" . $row[2] . "&nbsp" . $row[3] . "&nbsp" . $row[4] . 
           //         "&nbsp" . $row[5] . "&nbsp" . $row[6] . "&nbsp" . $row[7] . "&nbsp" . $row[8] . "&nbsp" . $row[9] . 
           //         "&nbsp" . $row[10] . "&nbsp" . $row[11] . "&nbsp" . $row[12] . "<br>";
        }
    }else {
        $res = "";
    }

       return $res;
}
    */
    //показать новых покупателей
    function show_new_buyer($con){
        $query = "SELECT u.name,
                         c.abbreviation,
                         c.counterparty,
                         mb.created_at,
                         mb.order_id,
                         u.phone
                    FROM t_order_from_new_buyer mb
                        JOIN t_order o ON o.order_id = mb.order_id
                        JOIN t_user u ON u.user_id = o.user_id
                        JOIN t_counterparty c ON c.counterparty_id = u.counterparty_id
                    WHERE moderation = '0'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            
            while($row=mysqli_fetch_array($result)){
                $name = $row[0];
                $abbreviation=$row[1];
                $counterparty=$row[2];
                $created_at=$row[3];
                $order_id=$row[4];
                $phone=$row[5];
                    //получить зумму заказа
                $summ = orderSumm($con,$order_id);
                echo $name . "&nbsp" . $abbreviation . "&nbsp" . $counterparty . "&nbsp" . $created_at . 
                                "&nbsp" . $summ . "&nbsp" . $order_id . "&nbsp" . $phone . "<br>";
            }
        }
    }
    //получить cумму заказа
    function orderSumm($con,$order_id){
        $summ = 0;
        $query="SELECT `quantity`, `price` FROM `t_order_product` WHERE `order_id`='$order_id'";
        $result=mysqli_query($con,$query) or die(mysqli_error($con));
        while($row=mysqli_fetch_array($result)){
            $quantity=$row[0];
            $price=$row[1];
            $summ += $quantity * $price;
        }

        return $summ;
    }
    //есть ли покупатели которые делают покупку первый раз
    function search_order_for_new_buyer($con){
        $query = "SELECT `order_from_new_buyer_id` FROM `t_order_from_new_buyer` WHERE `moderation`='0'";
        $result = mysqli_query($con,$query) or die(mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            echo "RESULT_OK" ;
        }else {
            echo "messege" . "&nbsp" . "нет заказов от новых покупателей" . "<br>";
        }
    }
    
    //добавить роль поставщика и в список договоров
    function add_provider_001($con, $moderator_id, $role_partner_id,$partner_user_id,$role_partner_admin){
        $date = date('Y-m-d H:i:s');
        $query= "UPDATE `t_role_partner` SET `active`='1',`moderator_user_id`='$moderator_id',`updated_at`='$date' 
                        WHERE `role_partner_id`='$role_partner_id'";         
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){          
                          
            echo "RESULT_OK";                    
            
        }else{
            echo "FAILURE";
        }
        $query = "UPDATE `t_user` SET `role`='$role_partner_admin'
                    WHERE `user_id`='$partner_user_id'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
    }
    /*
//добавить роль поставщика и в список договоров
    function add_provider($con,$contract_name, $moderator_id,$counterparty_id,$user_id,$role_for_moderation_id){
        
        $query= "INSERT INTO `t_contract_counterparty`(`contract`, `counterparty_id`, `moderator_id`) 
                    VALUES ('$contract_name','$counterparty_id','$moderator_id')";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            $query="UPDATE `t_role_moderator` SET `executed`= '1' WHERE `role_for_moderation_id`='$role_for_moderation_id'";
            $result=mysqli_query($con, $query) or die(mysqli_error($con));
            if($result){
                $query= "UPDATE `t_user` SET `role`='provider_business' WHERE `user_id`='$user_id'";
                $result=mysqli_query($con, $query) or die (mysqli_error($con));
                echo "RESULT_OK";
            }else{
                echo "FAILURE";
            }          
            
        }else{
            echo "FAILURE";
        }
    }
    */
    //receive_contract_step_001($con, $role_partner_id)
    //получить все шаги по подготовке и подписанию договора
    function receive_contract_step_001($con, $role_partner_id){
        $query = "SELECT `contract_step_id` FROM `t_contract_step` WHERE `role_partner_id` = '$role_partner_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $count = mysqli_num_rows($result);
        echo "RESULT_OK" . "&nbsp" .  $count . "<br>";
    }
    /*
 //получить все шаги по подготовке и подписанию договора
    function receive_contract_step($con, $role_for_moderation_id){
        $query = "SELECT `contract_step_id` FROM `t_contract_step` WHERE `role_for_moderation_id` = '$role_for_moderation_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $count = mysqli_num_rows($result);
        echo "RESULT_OK" . "&nbsp" .  $count . "<br>";
    }
    */
    //provider_contract_step_001($con, $role_partner_id, $contract_step, $moderator_id)
    //записать шаг подготовки договора поставщика
    function provider_contract_step_001($con, $role_partner_id, $contract_step, $moderator_id){
        $query = "INSERT INTO `t_contract_step`( `role_partner_id`, `contract_step`, `moderator_id`) 
                        VALUES ('$role_partner_id','$contract_step','$moderator_id')";
        $result=mysqli_query($con,$query) or die(mysqli_error($con));
        
    }
    /*
//записать шаг подготовки договора поставщика
    function provider_contract_step($con, $role_for_moderation_id, $contract_step, $moderator_id){
        $query = "INSERT INTO `t_contract_step`( `role_for_moderation_id`, `contract_step`, `moderator_id`) 
                        VALUES ('$role_for_moderation_id','$contract_step','$moderator_id')";
        $result=mysqli_query($con,$query) or die(mysqli_error($con));
        
    }
    */    
    //получить коментарий к договору
    function receive_coment_for_contract_001($con, $role_partner_id){
        $query= "SELECT c.comment
                    FROM t_role_partner rp
                        JOIN t_comment c ON c.comment_id = rp.comment_id
                    WHERE rp.role_partner_id='$role_partner_id'";
        //"SELECT `comment_id`, `comment` FROM `t_comment` WHERE 1";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            $coment = mysqli_fetch_array($result);
            

            if(empty($coment[0])){
                echo "messege" . "&nbsp" . $GLOBALS['comment_is_empty_text'];
            }else {
                echo "RESULT_OK" . "&nbsp" . $coment[0]. "<br>";
            }
        }else{
            echo "error" . "&nbsp" . $GLOBALS['error_try_again_later_text'];
        }
        
    }
    /*
//получить коментарий к договору
    function receive_coment_for_contract($con, $role_for_moderation_id){
        $query="SELECT  `comment` FROM `t_role_moderator` WHERE `role_for_moderation_id` = '$role_for_moderation_id'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            $coment = mysqli_fetch_array($result);
            

            if(empty($coment[0])){
                echo "messege" . "&nbsp" . $GLOBALS['comment_is_empty_text'];
            }else {
                echo "RESULT_OK" . "&nbsp" . $coment[0]. "<br>";
            }
        }else{
            echo "error" . "&nbsp" . $GLOBALS['error_try_again_later_text'];
        }
        
    }
    */
        //есть кандидаты в поставщки?
    function search_new_candidate_for_provider($con,$role){
        $query = "SELECT `role_partner_id` FROM `t_role_partner` WHERE `active`='0' AND `role_partner`='$role'";
       // $query = "SELECT `role_for_moderation_id`, `user_id` FROM `t_role_moderator` 
         //                WHERE `executed` = '0' AND `role_for_moderation` = '$role'"; //
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            echo "RESULT_OK" . "<br>";
        }else{
            echo "messege" . "&nbsp" . "нет запросов на поставщика" . "<br>";
        }
    }  //есть кандидаты в партнер склада?
    function search_new_candidate_for_partner_warehouse($con,$role){       
       $query = "SELECT `role_partner_id` FROM `t_role_partner` WHERE `active`='0' AND `role_partner`='$role'";
       
       //"SELECT `role_for_moderation_id`, `user_id` FROM `t_role_moderator` 
          //               WHERE `executed` = '0' AND `role_for_moderation` = '$role'"; //
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            echo "RESULT_OK" . "<br>";
        }else{
            echo "messege" . "&nbsp" . "нет запросов на партнера склада" . "<br>";
        }
    }
    
    //показать кандидатов в партнеры склада
     function candidate_for_partner_warehouse($con,$role){
        /*   $query = "SELECT `role_for_moderation_id`, `user_id`, `created_at` FROM `t_role_moderator` 
                                        WHERE `executed` = '0' AND `role_for_moderation` = '$role'"; //*/
            $query ="SELECT `role_partner_id`, `counterparty_id`, `user_id`, `created_at`
                    FROM `t_role_partner` WHERE `active`='0' AND `role_partner`='$role'";
            $result = mysqli_query($con,$query) or die (mysqli_error($con));
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_array($result)){
                    $role_partner_id=$row[0];
                    $counterparty_id = $row[1];
                    $user_id = $row[2];
                    $created_date  = $row[3];
                    $date = date("d.m.Y", strtotime($created_date));  
                    
                    $query = "SELECT `name`, `phone` FROM `t_user` WHERE `user_id`='$user_id'";
                    $res_user = mysqli_query($con,$query) or die(mysqli_error($con));
                    if($res_user){
                        $row_user = mysqli_fetch_array($res_user);
                        $name = $row_user[0];
                        $phone = $row_user[1];
                    }
                                       
                    $query = "SELECT  `abbreviation`, `counterparty`, `taxpayer_id_number`
                                FROM `t_counterparty` WHERE `counterparty_id`= '$counterparty_id'";
                    $result_two = mysqli_query($con,$query) ;
                    if($result_two){
                        $row_two = mysqli_fetch_array($result_two);
                        $abbreviation=$row_two[0];                    
                        $counterparty=$row_two[1];
                        $taxpayer_id=$row_two[2];
                        
                        //получить все шаги по подготовке и подписанию договора                        
                        $query_step ="SELECT `contract_step_id` FROM `t_contract_step` WHERE `role_partner_id`='$role_partner_id'";
                        $result_step = mysqli_query($con, $query_step) or die (mysqli_error($con));
                        $count_step = mysqli_num_rows($result_step);
                        
    
                        echo  $counterparty_id . "&nbsp" .$abbreviation . "&nbsp" .  $counterparty . 
                        "&nbsp" . $taxpayer_id . "&nbsp" . $role_partner_id . 
                        "&nbsp" . $date . "&nbsp" . $name ."&nbsp" . $phone .
                        "&nbsp" . $count_step ."&nbsp" . $user_id ."<br>";
                    }else{
                        echo "error" . "&nbsp" . die(mysqli_error($con)) . "<br>";
                    }                    
                }
            }           
    }
    //показать кандидатов в поставщики
    function showProviderCandidates_001($con,$role){
    
        $query ="SELECT `role_partner_id`, `counterparty_id`, `user_id`, `created_at`
                FROM `t_role_partner` WHERE `active`='0' AND `role_partner`='$role'";
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $role_partner_id=$row[0];
                $counterparty_id = $row[1];
                $user_id = $row[2];
                $created_date  = $row[3];
                $date = date("d.m.Y", strtotime($created_date));              
               
                $query = "SELECT `name`, `phone` FROM `t_user` WHERE `user_id`='$user_id'";
                $res_user = mysqli_query($con,$query) or die(mysqli_error($con));
                if($res_user){
                    $row_user = mysqli_fetch_array($res_user);
                    $name = $row_user[0];
                    $phone = $row_user[1];
                }
                
                $query = "SELECT  `abbreviation`, `counterparty`, `taxpayer_id_number`
                            FROM `t_counterparty` WHERE `counterparty_id`= '$counterparty_id'";
                $result_two = mysqli_query($con,$query) ;
                if($result_two){
                    $row_two = mysqli_fetch_array($result_two);
                    $abbreviation=$row_two[0];                    
                    $counterparty=$row_two[1];
                    $taxpayer_id=$row_two[2];

                    //получить имя ответственного лица и телефон для связи
                    $query = "";
                    
                    //получить все шаги по подготовке и подписанию договора
                    //$query_step = "SELECT `contract_step_id` FROM `t_contract_step` WHERE `role_for_moderation_id` = '$role_for_moderation_id'";
                    $query_step ="SELECT `contract_step_id` FROM `t_contract_step` WHERE `role_partner_id`='$role_partner_id'";
                    $result_step = mysqli_query($con, $query_step) or die (mysqli_error($con));
                    $count_step = mysqli_num_rows($result_step);
                    

                    echo  $counterparty_id . "&nbsp" .$abbreviation . "&nbsp" .  $counterparty . 
                                "&nbsp" . $taxpayer_id . "&nbsp" . $role_partner_id . 
                                "&nbsp" . $date . "&nbsp" . $name ."&nbsp" . $phone .
                                "&nbsp" . $count_step ."&nbsp" . $user_id ."<br>";
                }else{
                    echo "error" . "&nbsp" . die(mysqli_error($con)) . "<br>";
                }
                
            }
        }

    
    }
    /*
//показать кандидатов в поставщики
    function showProviderCandidates($con,$role){
        $query = "SELECT `role_for_moderation_id`, `user_id`, `created_at` FROM `t_role_moderator` 
                                    WHERE `executed` = '0' AND `role_for_moderation` = '$role'"; //
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                $role_for_moderation_id=$row[0];
                $user_id = $row[1];
                $created_date  = $row[2];
                $date = date("d.m.Y", strtotime($created_date));                
               // echo "test" . "<br>";
                $query="SELECT u.name,
                                c.counterparty_id,
                                c.abbreviation,
                                c.counterparty,
                                c.taxpayer_id_number,
                                u.phone
                        FROM t_user u
                            JOIN t_counterparty c ON c.counterparty_id = u.counterparty_id
                        WHERE user_id = '$user_id'";
                $result_two = mysqli_query($con,$query) ;
                if($result_two){
                    $row_two = mysqli_fetch_array($result_two);
                    $name=$row_two[0];
                    $counterparty_id=$row_two[1];
                    $abbreviation=$row_two[2];
                    $counterparty=$row_two[3];
                    $taxpayer_id=$row_two[4];
                    $phone=$row_two[5];

                    
                    //получить все шаги по подготовке и подписанию договора
                    $query_step = "SELECT `contract_step_id` FROM `t_contract_step` WHERE `role_for_moderation_id` = '$role_for_moderation_id'";
                    $result_step = mysqli_query($con, $query_step) or die (mysqli_error($con));
                    $count_step = mysqli_num_rows($result_step);
                    

                    echo  $counterparty_id . "&nbsp" .$abbreviation . "&nbsp" .  $counterparty . 
                                "&nbsp" . $taxpayer_id . "&nbsp" . $name . "&nbsp" . $role_for_moderation_id . 
                                "&nbsp" . $date . "&nbsp" . $phone . "&nbsp" . $count_step ."<br>";
                }else{
                    echo "error" . "&nbsp" . die(mysqli_error($con)) . "<br>";
                }
                
            }
        }

    
    }
    */
    //есть товары для модерации?
    function search_new_input_product($con){
        $query = "SELECT `id` FROM `t_input_product` WHERE `on_off` = '1'";
        $result = mysqli_query($con,$query) or die (mysqli_error($con));
        if(mysqli_num_rows($result) > 0){
            $res = "RESULT_OK"; 
        }else{
            $res = "messege" . "&nbsp" . "Товаров для модерации нет";
        }
        return $res;
    }
    //найти ID пользователя который отправил запрос на роль
    function checkUserIDFromRole($con, $role_for_moderation_id){
        $query="SELECT `user_id` FROM `t_role_moderator` WHERE `role_for_moderation_id` = '$role_for_moderation_id'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));
        if($result){
            $row=mysqli_fetch_array($result);
            $user_id = $row[0];
        }else $user_id = 0;
        return $user_id; 
    }
    //найти ID контрагента
    function checkCounterpartyIDFromRole($con, $role_for_moderation_id){
        $query = "SELECT u.counterparty_id 
                    FROM t_role_moderator rm
                        JOIN t_user u ON u.user_id = rm.user_id
                    WHERE rm.role_for_moderation_id = '$role_for_moderation_id'";
     //SELECT `user_id`, `unique_id`, `name`, `phone`, `encrypted_password`, `salt`, `created_at`, `updated_at`, `counterparty_id`, `role` FROM `t_user` WHERE 1               
        $result=mysqli_query($con, $query) or die (mysqli_error($con));
        if($result){
            $row=mysqli_fetch_array($result);
            return  $row[0];
        }else{  return 0; }
    }
    //найти user_id
   /* function checkUserID($con, $user_uid){ 
    $query="SELECT user_id FROM t_user WHERE unique_id = '$user_uid'";
    $result=mysqli_query($con, $query) or die(mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $user_id = $row[0];
    //echo "user_id " . $user_id . "<br>";
    return $user_id;
    }*/
    //найти counterparty_id
   /* function receiveCounterpartyID($con,$counterparty_tax_id){
        $query = "SELECT `counterparty_id` FROM `t_counterparty` WHERE `taxpayer_id_number`= '$counterparty_tax_id'";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        $row = mysqli_fetch_array($result);
        $counterparty_id = $row[0];
        return $counterparty_id;
    }*/

    //найти user_id
    /*function checkUserID($con, $user_uid){ 
        $query="SELECT `user_id` FROM `t_user` WHERE `unique_id` = '$user_uid'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $user_id = $row[0];
        //echo "user_id " . $user_id . "<br>";
        return $user_id;
        }*/

    mysqli_close($con);
?>