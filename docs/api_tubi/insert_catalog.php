<?php
    include 'connect.php';

	
  mysqli_query($con,"SET NAMES 'utf8'");	
	 
	$catalog = $_POST['catalog'];
	//$catalog = $_GET['catalog'];
	
	mysqli_query($con,"SET NAMES 'utf8'");
	
	$sql = "INSERT INTO t_catalog (catalog) 
							VALUES ('$catalog')";
	if (mysqli_query($con, $sql)) {
		  echo "Создана новая категория ". $catalog;
	} else {
		  echo "Error: " . $sql . "<br>" . mysqli_error($con);
	}
	mysqli_close($con);
?>