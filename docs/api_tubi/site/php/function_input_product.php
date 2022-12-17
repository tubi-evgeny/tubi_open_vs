<?php
    include_once '../../helper_classes.php';
    function get_price($con) {	
        $query = "SELECT `id`, `in_product_name`, `package_price`, `unit_price`, `accounting_unit`, `taxpayer_id_number`
                         FROM `t_make_new_product` WHERE `active` ='0' and `archive`='0'";
        $result=mysqli_query($con,$query) or die (mysqli_error($con)); 
        
        $my_row = array();
       /* for($i = 0;$i < mysqli_num_rows($result);$i++) {
            $row[] = mysqli_fetch_assoc($result);
        }*/
        while($row=mysqli_fetch_array($result)){
            $id = $row[0];
            $in_product_name= $row[1];
            $package_price= $row[2];
            $unit_price= $row[3];
            $accounting_unit= $row[4];
            $taxpayer_id_number= $row[5];

            //найти counterparty_id
            $counterparty_id = searchCounterpartyId($con, $taxpayer_id_number);
            //получить данные(информацию) о компании
            $companyInfoList = receiveCompanyInfo($con, $counterparty_id);
            $abbreviation = $companyInfoList['abbreviation'];
            $counterparty = $companyInfoList['counterparty'];

            $warehouse_type = "provider";
            //список складов этого поставщика
            $warehouse_info_list = receive_counterparty_warehouses($con, $counterparty_id, $warehouse_type);
            foreach($warehouse_info_list as $k => $warehouse_info){
                $warehouse_info_id = $warehouse_info['warehouse_info_id'];
                $warehouse_id = $warehouse_info['warehouse_id'];
                $warehouse_type = $warehouse_info['warehouse_type'];
                $active = $warehouse_info['active'];

            }
            
    
            $my_row[] = ['id'=>$id,'in_product_name'=>$in_product_name
                    ,'abbreviation'=>$abbreviation,'counterparty'=>$counterparty
                    ,'taxpayer_id_number'=>$taxpayer_id_number
                    ,'warehouse_id'=>$warehouse_id];     
            /*
            $my_row[] = ['id'=>$id,'in_product_name'=>$in_product_name
                    ,'package_price'=>$package_price,'unit_price'=>$unit_price
                    ,'accounting_unit'=>$accounting_unit,'taxpayer_id_number'=>$taxpayer_id_number
                    ,'warehouse_id'=>$warehouse_id];  
            */
                        
        }
        
	    return $my_row;		
    }
?>