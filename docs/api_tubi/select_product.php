<?php

include 'connect.php';
	
   mysqli_query($con,"SET NAMES 'utf8'");
   
   $category = $_GET['category'];   
  
   //------------------------------------
   $query = "SELECT category_id FROM t_category WHERE category = '$category' ";
   
   $result = mysqli_query($con, $query);
   
   $row = mysqli_fetch_array($result);
   
   $category_id = $row[0];
   //-----------------------------------
   $query = "SELECT product FROM t_product WHERE category_id = '$category_id'";
   
   $result = mysqli_query($con, $query);
	
   $data="";
   while ($row = mysqli_fetch_array($result)) {
    //echo("Name " . $row[0] . " age " . $row[1] . "<br>");
    
    $data .=  $row[0] . "<br>";
    
    }
  
   if($data){
      echo $data ."<br>";
   } else echo "This login defolt...";
	
	mysqli_close($con);
?>