<?php
	 include 'connect.php';
   
   mysqli_query($con,"SET NAMES 'utf8'");
   
   $query = "SELECT id FROM t_input_product WHERE id > 0";
   
   $result = mysqli_query($con,$query); 
	
   // $data="";
    $ip_id_arr = array();
    $i = 0;
    while ($row = mysqli_fetch_array($result)) {  
           
            $ip_id_arr [$i++] = $row[0];
	}
	
	if($ip_id_arr){
    	for($i=0;$i<count($ip_id_arr);$i++){
    	                                echo "id- ". $ip_id_arr[$i] . "<br>";
    	}
	}else "Array false";
	
	for($i=0; $i < count($ip_id_arr); $i++){
	    $query = "SELECT catalog, category FROM t_input_product WHERE id = $ip_id_arr[$i]";//получаем id продуктов в массив
	    $result = mysqli_query($con, $query) or die ( mysqli_error($link));
	    $row=mysqli_fetch_array($result);
	    $ip_catalog = $row[0];   $ip_category = $row[1];
	                                echo "id: " . $ip_catalog .",". "&nbsp" . $ip_category .";". "&nbsp;";//"<br>"
	                               
	   $query = "SELECT catalog_id, catalog FROM t_catalog WHERE catalog = '$ip_catalog'"; //ищем имя каталога на совпадение 
	   $result = mysqli_query($con, $query) or die ( mysqli_error($link));
    	   if($catalog = mysqli_fetch_array($result)){
    	       $c_catalog_id=$catalog[0]; $c_catalog = $catalog[1];
    	                             echo "Found info: id " . $c_catalog_id . "&nbsp" . $c_catalog . "&nbsp";
    	   }else echo "This catalog name not catalog product" . "<br>";  
    	                                                //ищем категорию на наличие
        $query = "SELECT category FROM t_category WHERE catalog_id = $c_catalog_id AND category = '$ip_category' ";	   
	    $result = mysqli_query($con, $query) or die (mysqli_error($link));  
	    if($category = mysqli_fetch_array($result)){
	        echo "<br>";                    // если категория не существуют вносим категорию в таблицу t_category
	    }else {
	        echo "This category $ip_category is not " . "<br>";
	    }
	    echo "<br>";
	}
	
	mysqli_close($con);
?>














