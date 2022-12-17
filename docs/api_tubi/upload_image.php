<?php

     include 'connect.php';

	 
   mysqli_query($con,"SET NAMES 'utf8'");

   $encodedImage = $_POST['EN_IMAGE'];
   $imageTitle = $_POST['EN_NAME'];

   $imageTitle = "$imageTitle.jpg";
   $imageLocation = "../images/image/$imageTitle";
   
   $result = array();

   $query = "INSERT INTO `t_image`(`image_url`) VALUES ('$imageTitle')";

   if(mysqli_query($con,$query)){
          // Сохраняем изображение в 'image/$imageTitle.jpg'
        //file_put_contents($imageLocation,base64_decode($encodedImage));

        if(file_put_contents($imageLocation,base64_decode($encodedImage)) != false){
          $imageDirectory = "../images/preview_image/$imageTitle";
          //$image = base64_decode($encodedImage);
          list($width, $height,$type) = getimagesize($imageLocation);
          $new_width = '300' ;
          if($width <= $new_width){
               $new_width = $width;
               $new_height = $height;
          }else{                
               $multiply = ($new_width / ($width / 100));
               $new_height = ($height / 100) * $multiply ;
          }
          // Load image file
          $sImage = imagecreatefromjpeg($imageLocation);
          //уменьшить размер фото
          $img = imagescale( $sImage, $new_width, $new_height );

          compressImage($img, $imageDirectory, 50);
        }

        $result["status"] = true;
        $result["remarks"] = "Изображение Успешно Загружено";//"Image Uploaded Succesfully";

   }else{
        $result["status"] = false;
        $result["remarks"] = "Не удалось Загрузить Изображение";//"Image Uploading Failed";
   }

   function compressImage($image, $imageDirectory, $quality) {
        //сохранить файл
     return imagejpeg($image, $imageDirectory, $quality);
     //return imagejpeg(imagecreatefromstring($image), $imageDirectory, $quality);
   }
   

   mysqli_close($con);
   print(json_encode($result));
   /*
   $encodedImage = $_POST['EN_IMAGE'];
   $imageTitle = $_POST['EN_NAME'];

   $imageTitle = "$imageTitle.jpg";
   $imageLocation = "image/$imageTitle";
   
   $result = array();

   $query = "INSERT INTO `t_image`(`image_url`) VALUES ('$imageTitle')";

   if(mysqli_query($con,$query)){
          // Сохраняем изображение в 'image/$imageTitle.jpg'
        //file_put_contents($imageLocation,base64_decode($encodedImage));

        if(file_put_contents($imageLocation,base64_decode($encodedImage)) != false){
          $imageDirectory = "preview_image/$imageTitle";
          //$image = base64_decode($encodedImage);
          list($width, $height,$type) = getimagesize($imageLocation);
          $new_width = '300' ;
          if($width <= $new_width){
               $new_width = $width;
               $new_height = $height;
          }else{                
               $multiply = ($new_width / ($width / 100));
               $new_height = ($height / 100) * $multiply ;
          }
          // Load image file
          $sImage = imagecreatefromjpeg($imageLocation);
          //уменьшить размер фото
          $img = imagescale( $sImage, $new_width, $new_height );

          compressImage($img, $imageDirectory, 50);
        }

        $result["status"] = true;
        $result["remarks"] = "Изображение Успешно Загружено";//"Image Uploaded Succesfully";

   }else{
        $result["status"] = false;
        $result["remarks"] = "Не удалось Загрузить Изображение";//"Image Uploading Failed";
   }

   function compressImage($image, $imageDirectory, $quality) {
        //сохранить файл
     return imagejpeg($image, $imageDirectory, $quality);
     //return imagejpeg(imagecreatefromstring($image), $imageDirectory, $quality);
   }
   

   mysqli_close($con);
   print(json_encode($result));
   */

?>