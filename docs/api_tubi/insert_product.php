<?php
	 include 'connect.php';


   mysqli_query($con,"SET NAMES 'utf8'");   
   
   $query = "SELECT 
   
        cat.catalog_id,
        cat.catalog,
        c.category_id, 
        c.category
    
    FROM 
         t_input_product ip
    
        JOIN t_catalog cat ON cat.catalog = ip.catalog
        JOIN t_category c ON c.category = ip.category
        
    WHERE ip.id >0 ";
   
   $result = mysqli_query($con,$query); 
	
    $data="";
    while ($row = mysqli_fetch_array($result)) {    
        for($i = 0;$i < 4;$i++){//count($row)
        
            $data .= $row[$i] . "&nbsp;";
        }
        
        $data .=  "<br>";
	}
  
   if($data){
      echo $data ."<br>";
   } else echo "This login defolt...";
	
	mysqli_close($con);

?>