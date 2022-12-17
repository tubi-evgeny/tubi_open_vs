<?php

echo 'Текущая версия PHP: ' . phpversion();echo "</br>";
	echo "hello i am Evgeny ";
	echo "</br>";
	echo "this is tubi site ";
	echo "</br>";
   echo "как твои дела";
   echo "<br>";

   echo date("Y-m-d H:i:s");
   echo "<br>";

   //масссив ммассивов
   makeArrayInArray();

   function makeArrayInArray(){
	$gadgets = [
        "phones" => ["apple" => "iPhone 12", 
                    "samsumg" => "Samsung S20",
                    "nokia" => "Nokia 8.2"],
        "tablets" => ["lenovo" => "Lenovo Yoga Smart Tab", 
                        "samsung" => "Samsung Galaxy Tab S5",
                        "apple" => "Apple iPad Pro"]
		];
	foreach ($gadgets as $gadget => $items){
		echo "<h3>$gadget</h3>";
		echo "<ul>";
		foreach ($items as $key => $value)
		{
			echo "<li>$key : $value</li>";
		}
		echo "</ul>";
	}
	$nums = [];
	for($i=0;$i < 5;$i++){

		$Aa = 'Aa + '.$i ;
		$Bb = 'Bb + '.$i ;
		echo $Aa . "<br>";
		$nums ['C'.$i]= [$Aa => $Bb, $Aa => $Bb];
	}
	foreach($nums as $num => $items){
		echo $num . "<br>";
		foreach($items as $key => $v){
			//echo  $v . "<br>";
			echo  $num . "&nbsp" . $key . "&nbsp" . $v . "<br>";
		}
	}
	//for($i=0;$i < count($nums); $i++){
		echo $nums['C0']['Aa + 0']. "<br>";
	//}
	
   }


   $array_001 = make_array();

   echo "-------------------------------" . "<br>";  

   function make_array(){
		$array =[];
		for($i = 0; $i < 10; $i++){
		$array [$i]= $i;	
		}
		for($i = 0; $i < 10; $i++){
		echo $array[$i] . "&nbsp" . "test" . "<br>";
		}
		return $array;
   }
   test_show_array($array_001);
      
	function test_show_array($array){
		
		for($i = 0; $i < 10; $i++){
			echo $array[$i] . "&nbsp" . "test2" . "<br>";
		   }
	}
	echo "-------------------------------" . "<br>";

?>