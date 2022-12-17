<?php
  
	include 'connect.php';
	 
	mysqli_query($con,"SET NAMES 'utf8'");

	if (!$con) {
		  die("Connection failed: " . mysqli_connect_error());
	}	 
//	echo "Connected successfully ; ";	
	 
	$catalog = $_POST['catalog'];
	$category = $_POST['category'];
	//$category = $_GET['category'];
//	echo "  ".$catalog."  ".$category;
	
	//mysqli_query($con,"SET NAMES 'utf8'");
	
	$query = "SELECT catalog_id FROM t_catalog WHERE catalog = '$catalog'";
   
    $result = mysqli_query($con, $query) or die (mysql_error($link));
	
   $row = mysqli_fetch_array($result);
    
    $data =  $row[0] ;
  
   if($data){
      echo $data ;
   } else echo "This login defolt...";
	
	
	$sql = "INSERT INTO t_category (category,catalog_id) VALUES ('$category','$data')";
	$result = mysqli_query($con, $sql) or die (mysql_error($link));
	if ($result) {
		  echo " Создана новая категория ". $category ;
	} else {
		  echo "Error: " . $sql . "<br>" . mysqli_error($con);
	}
	mysqli_close($con);
?>