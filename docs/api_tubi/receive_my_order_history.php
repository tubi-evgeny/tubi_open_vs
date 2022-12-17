<?php
	

    include 'connect.php';
    include 'text.php';
	include_once 'helper_classes.php';
	 
   mysqli_query($con,"SET NAMES 'utf8'");


   if(isset($_GET['receive'])){
	$user_uid = $_GET['user_uid'];
	$company_tax_id = $_GET['company_tax_id'];
	$limit = $_GET['limit'];
	

	$user_id = checkUserID($con, $user_uid);
	//найти counterparty_id
    $counterparty_id = searchCounterpartyId($con, $company_tax_id);

	receiveMyOrderHistory($con, $user_id, $limit, $counterparty_id);
}

//получить историю заказов user только company пользователя
function receiveMyOrderHistory($con,$user_id,$limit, $counterparty_id){
	//получить order_id, executed заказов пользователя, лимит 
	$query="SELECT order_id, executed, order_deleted, date_order_start, get_order_date_millis
						, get_order_date, delivery, joint_buy
			FROM t_order WHERE counterparty_id=$counterparty_id AND order_active=1 
							ORDER BY date_order_start	DESC LIMIT $limit";
	$result = mysqli_query($con,$query);

    if(mysqli_num_rows($result) <= 0){
		echo "message" . "&nbsp" . "У вас нет заказов" . "<br>";
		return;
	}
	if($result){
		while($row=mysqli_fetch_array($result)){
			
			$order_id=$row[0];
			$executed=$row[1];
			$order_deleted = $row[2];
            $date_order_start = $row[3];
			$get_order_date_millis = $row[4];
			$get_order_date = $row[5];
			$delivery = $row[6];
			$joint_buy = $row[7];

            $dateComps = date_parse($date_order_start);    
            $year = $dateComps['year'];
            $month = $dateComps['month'];
            $day = $dateComps['day'];
			$date = $day . "." .$month . "." . $year;

			$get_order_date = date('d.m.Y', $get_order_date_millis / 1000);

			/*$dateComps = date_parse($get_order_date);    
            $get_year = $dateComps['year'];
            $get_month = $dateComps['month'];
            $get_day = $dateComps['day'];
			$get_hour = $dateComps['hour'];
			$get_date = $get_day . "." .$get_month . "." . $get_year." ".$get_hour.":00";*/
            
            
			//tempOrderInfo($con,$order_id,$executed,$date, $order_deleted,$get_order_date, $delivery);
			tempOrderInfo($con,$order_id,$executed,$date, $order_deleted,$get_order_date_millis, $delivery, $joint_buy);
			 
		}
		 
	}else die ("error" . "&nbsp" . "Не верный запрос: " . mysqli_error());      
    
}
//получить по order_id колличество позиций в заказе колл. штук и        
	//цену товара, категорию, брэнд, характеристику
function tempOrderInfo($con,$order_id,$executed,$date, $order_deleted,$get_order_date, $delivery, $joint_buy){
	$query="SELECT      pi.product_id,
						op.product_inventory_id,
						c.category,
						b.brand,
						ch.characteristic,
						um.unit_measure,
						p.weight_volume,
						op.price,
						im.image_url,
						d.description,
						cp.counterparty,						
						op.order_product_id,
						op.quantity,
						op.price_process						
						
				FROM t_order_product op
					JOIN t_product_inventory pi ON pi.product_inventory_id = op.product_inventory_id
					JOIN t_product p            ON p.product_id = pi.product_id
					JOIN t_category c           ON c.category_id = p.category_id 
					JOIN t_brand b              ON b.brand_id = p.brand_id
					JOIN t_characteristic ch    ON ch.characteristic_id = p.characteristic_id 
					JOIN t_unit_measure um      ON um.unit_measure_id = p.unit_measure_id
					JOIN t_image im             ON im.image_id = pi.image_id
					JOIN t_description d        ON d.description_id = pi.description_id
					JOIN t_counterparty cp      ON cp.counterparty_id = pi.counterparty_id
				
				WHERE op.order_id = $order_id";
				$result = mysqli_query($con, $query) or die (mysql_error($link));

				if(mysqli_num_rows($result) > 0){
					//echo "true"."<br>";
					while($row = mysqli_fetch_array($result)){
						$category = $row[2];
						$brand = $row[3];
						$characteristic = $row[4];
						$weight_volume = $row[6];
						$price = $row[7];	
						//$image_url = $row[8];					
						$quantity = $row[12];
						$price_process = $row[13];
						
						
						$product =  $order_id . "&nbsp" . $category . "&nbsp" . $brand . "&nbsp" 
									. $characteristic . "&nbsp" . $weight_volume . "&nbsp"
									. $price . "&nbsp" . $quantity . "&nbsp" . $executed . "&nbsp" 
									. $date . "&nbsp" . $get_order_date . "&nbsp" . $order_deleted."&nbsp"
									.$price_process. "&nbsp" . $delivery. "&nbsp" . $joint_buy; 
						echo $product . "<br>";
					}
				}else{
					echo "NO_ORDER" . "<br>";            
				}

}
//найти user_id
/*function checkUserID($con, $user_uid){ 
    $query="SELECT user_id FROM t_user WHERE unique_id = '$user_uid'";
    $result=mysqli_query($con, $query) or die(mysqli_error($con));
    $row=mysqli_fetch_array($result);
    $user_id = $row[0];
    //echo "user_id " . $user_id . "<br>";
    return $user_id;
    }*/
   
   mysqli_close($con);
?>