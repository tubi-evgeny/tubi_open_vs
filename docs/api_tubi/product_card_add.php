<?php

   include 'connect.php';
   include_once 'helper_classes.php';
	 
   mysqli_query($con,"SET NAMES 'utf8'");

   //upload_product_card

    if(isset($_GET['select_category'])){ 

       $result = selectCategory($con);
       echo $result;
    }
    else if(isset($_GET['select_product_name'])){ 

        $result = select_product_name($con);
        echo $result;
    }
    else if(isset($_GET['select_brand'])){ 

        $result = selectBrand($con);
        echo $result;
    }else if(isset($_GET['select_characteristic'])){ 

        $result = selectCharacteristic($con);
        echo $result;
    }else if(isset($_GET['select_unit_measure'])){ 

        $result = selectUnitMeasure($con);
        echo $result;
    }else if(isset($_GET['select_tipe_pacaging'])){ 

        $result = selectTipePacaging($con);
        echo $result;
    }else if(isset($_POST['upload_product_card'])){ 
        
        $in_product_name = $_POST['in_product_name'];
        $category = $_POST['category'];  
        $productName = $_POST['productName'];       
        $brand = $_POST['brand'];
        $characteristic = $_POST['characteristic'];
        $tipePacaging = $_POST['tipePacaging'];
        $unitMeasure = $_POST['unitMeasure'];
        $weightVolume = $_POST['weightVolume'];
        $price = $_POST['price'];
        $productQuantity = $_POST['productQuantity'];
        $pacagingQuantity = $_POST['pacagingQuantity'];        
        $imageName = $_POST['imageName'];
        $abbreviation = $_POST['abbreviation'];
        $companyName = $_POST['company_name'];
        $companyTaxId = $_POST['companyTaxId'];
        $description = $_POST['description'];
        $warehouse_id = $_POST['warehouse_id'];
        $user_uid =$_POST['user_uid'];
        $storageConditions =$_POST['storageConditions'];
        //все буквы маленькие
        $in_product_name = mb_strtolower($in_product_name);
        $category = mb_strtolower($category);
       // $brand = mb_strtolower($brand);
        $characteristic = mb_strtolower($characteristic);
        $tipePacaging = mb_strtolower($tipePacaging);
        $unitMeasure = mb_strtolower($unitMeasure);  
        $abbreviation = mb_strtolower($abbreviation);      
        $companyName = mb_strtolower($companyName);
        $description = mb_strtolower($description);
        $storageConditions = mb_strtolower($storageConditions);
                
        $user_id = checkUserID($con, $user_uid);

        $catalog = searchCatalogThisProduct($con,$category);
           
        $result = uploadProductCard($con,$in_product_name,$catalog,$category,$productName,$brand,$characteristic,$tipePacaging,$unitMeasure
                                ,$weightVolume,$price,$productQuantity,$pacagingQuantity,$imageName,
                                $abbreviation,$companyName,$companyTaxId,$description,$warehouse_id,
                                $user_id,$storageConditions);

        echo $result;
    }    

   function uploadProductCard($con,$in_product_name,$catalog,$category,$productName,$brand,$characteristic,$tipePacaging,$unitMeasure
                                ,$weightVolume,$price,$productQuantity,$pacagingQuantity,$imageName,
                                $abbreviation,$companyName,$companyTaxId,$description,$warehouse_id,
                                $user_id,$storageConditions){


        $query = "INSERT INTO `t_input_product`(`catalog`, `category`,`product_name`, `brand`, `characteristic`, 
        `type_packaging`, `unit_measure`, `weight_volume`, `price`, `quantity`, `quantity_package`
        , `image_url`, `description`,`in_product_name`,`abbreviation`, `counterparty`, `taxpayer_id_number`,
        `warehouse_id`,`storage_conditions`,`creator_user_id`) 
         VALUES('$catalog','$category','$productName','$brand','$characteristic','$tipePacaging','$unitMeasure'
                ,'$weightVolume','$price','$productQuantity','$pacagingQuantity','$imageName','$description',
                  '$in_product_name', '$abbreviation','$companyName','$companyTaxId',
                  '$warehouse_id','$storageConditions','$user_id')";
        $result = mysqli_query($con,$query);
       
        if($result){
            //echo "RESULT_OK" . "<br>";
            return "RESULT_OK" . "<br>";
        }else{
            return "error" . die (mysqli_error($con));
            //echo "error" . die (mysqli_error($con));
        }

   }
  
   function searchCatalogThisProduct($con,$category){    
       $query = "SELECT c.catalog
                    FROM t_category ct 
                        JOIN t_catalog c ON c.catalog_id = ct.catalog_id
                    WHERE ct.category = '$category'";
        $result = mysqli_query($con,$query);
        if($result){
            if($row = mysqli_fetch_array($result)){
                $catalog = $row[0];
                return $catalog;
            }else{
                $catalog = "";
                return $catalog;
            }                     
        }else{
            die (mysqli_error($con));            
        }        
   }

    
    function selectTipePacaging($con){
        $query = "SELECT `type_packaging` FROM `t_type_packaging` ORDER BY `type_packaging`";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        
        if($result){
            $string = "";          
            while($row = mysqli_fetch_array($result)){
                $string .= $row[0] . "&nbsp";
            }
            $string .= "<br>";
            return $string;
        }
        
    }

    function selectUnitMeasure($con){
        $query = "SELECT `unit_measure` FROM `t_unit_measure` ORDER BY `unit_measure`";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        
        if($result){
            $string = "";          
            while($row = mysqli_fetch_array($result)){
                $string .= $row[0] . "&nbsp";
            }
            $string .= "<br>";
            return $string;
        }
        
    }

    function selectCharacteristic($con){
        $query = "SELECT `characteristic` FROM `t_characteristic` ORDER BY `characteristic`";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        
        if($result){
            $string = "";          
            while($row = mysqli_fetch_array($result)){
                $string .= $row[0] . "&nbsp";
            }
            $string .= "<br>";
            return $string;
        }
        
    }

    function selectBrand($con){
        $query = "SELECT `brand` FROM `t_brand` ORDER BY `brand`";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        
        if($result){
            $string = "";          
            while($row = mysqli_fetch_array($result)){
                $string .= $row[0] . "&nbsp";
            }
            $string .= "<br>";
            return $string;
        }
        
    }
    function select_product_name($con){
        //$query = "SELECT `category` FROM `t_category` ORDER BY `category`";
        $query = "SELECT `product_name` FROM `t_product_name` ORDER BY `product_name`";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        
        if($result){
              $string = "";          
            while($row = mysqli_fetch_array($result)){
                $string .= $row[0] . "&nbsp";
            }
            $string .= "<br>";
            return $string;
        }
        
   }
   function selectCategory($con){
        $query = "SELECT `category` FROM `t_category` ORDER BY `category`";
        $result = mysqli_query($con, $query) or die (mysqli_error($con));
        
        if($result){
              $string = "";          
            while($row = mysqli_fetch_array($result)){
                $string .= $row[0] . "&nbsp";
            }
            $string .= "<br>";
            return $string;
        }
        
   }

  /* if(isset($_GET['help_temp'])){ 
    $user_uid =$_GET['user_uid'];

    $result = checkUserID($con, $user_uid);
    echo $result;
}*/

    //найти user_id
   /* function checkUserID($con, $user_uid){ 
        $query="SELECT `user_id` FROM `t_user` WHERE `unique_id` = '$user_uid'";
        $result=mysqli_query($con, $query) or die(mysqli_error($con));
        $row=mysqli_fetch_array($result);
        $user_id = $row[0];
        //echo "user_id " . $user_id . "<br>";
        return $user_id;
    }*/

 mysqli_close($con);
?>