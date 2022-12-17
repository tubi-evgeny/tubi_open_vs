<?php
    include 'connect.php';
    include_once 'helper_classes.php';

    mysqli_query($con,"SET NAMES 'utf8'");

    /* $getOrderMillis = 1641157200248;

    $get_order_date = date('Y-m-d H:i:s', $getOrderMillis / 1000);
    echo "date $get_order_date <br>";*/

    //получить текущее время
    $time_millis = (float)sprintf('%.0f', (microtime(true) * 1000));
    echo " текущее time_millis $time_millis <br>";
    $get_order_date = date('Y-m-d H:i:s', $time_millis / 1000);
    echo "date $get_order_date <br><br>";

    list($msec, $sec) = explode(' ', microtime());
    $time_millis = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    echo " текущее time_millis $time_millis <br>";
    $get_order_date = date('Y-m-d H:i:s', $time_millis / 1000);
    echo "date $get_order_date <br><br>";

    $result = 305.11 / 24;
    echo "result: $result <br>";
    echo "1000 / 12: ". 5 / (1000 / 12)  ." <br>";



    echo "<br><br> проверка ветки develop <br>";
    //---------------------------

    $str = 'Альпен Гольд ОРЕО 1\90гр.(19)384';
    echo "str 1 = $str <br>";
    
    echo "str 4 = ". stripslashes($str)."<br>";
    echo "<br>";
    echo "today --- ".floor(microtime(true) * 1000)."<br>";
    echo "old day - ".floor(strtotime("2022-11-09 14:15:59")* 1000)."<br>";
  
    echo "<br>";
    echo "<br>";
//------------------------------------

echo 'Текущая версия PHP: ' . phpversion();

/*

    $check_count=0;
    $this_image_isnot=0;
    $count=0;
    $true_count=0;
    $x=0;
    $start_image_list = [];
    $in_location = 'https://h102582557.nichost.ru/images/image/';
    $out_location = 'https://h102582557.nichost.ru/images/out_image/';
    $query="SELECT `image_id`, `image_url` FROM `t_image` WHERE `image_id` > '2699' and `image_id` < '2924'";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    while($row = mysqli_fetch_array($result)){
        $image_id = $row[0];
        $image_url = $row[1];

        //найти фото в рабочей папке
        $in_location_str = $in_location . $image_url;
        if(@getimagesize( $in_location_str)){
            $true_count++;
            echo "нашли в рабочей image_url = $in_location_str <br>";
        }else{
            $start_image_list[] = $image_url;
        }
        $x++;
        
    }
    $image_for_write_list = [];
    //нет вайла в рабочей папке ищем в старой папке
    foreach($start_image_list as $k => $image_url){        
        $out_location_str = $out_location . $image_url;

        if(@getimagesize($out_location_str)){
            $image_for_write_list[] = $image_url;
            echo "нашли в старой image_url = $out_location_str <br>";
        }else{
            //нет файла и в старой папке
            echo "нет такого файла нигде = $out_location_str <br>";
            $this_image_isnot++;           
        }


    }

    foreach($image_for_write_list as $k => $image_url){        
        $out_location_str = $out_location . $image_url;
        //файл найден в старой папке, берем его
        $content = file_get_contents($out_location_str);

        //записываем его в рабочую папку
        file_put_contents("../images/image/$image_url", $content);
        $count++;
    }


    $check_count = count($start_image_list);

    echo "нет такого файла в рабочей папке / start_image_list count = ". count($start_image_list) ." <br>";
    echo "нашли в старой папке / image_for_write_list count = ". count($image_for_write_list) ." <br>";

    echo "нет столько фото = $check_count / записано фото = $count / нет такого фото нигде = $this_image_isnot / есть в раб папке = $true_count / всего проверено = $x<br>";
*/

  /*  //найти фото в рабочей папке
        $in_location .= $image_url;
        if(@getimagesize( $in_location)){
            $true_count++;
            echo "/ есть в раб папке = 1 ";
        }else{
            //нет вайла в рабочей папке ищем в старой папке
            $out_location .= $image_url;
            if(@getimagesize( $out_location)){
                //файл найден в старой папке, берем его
                $content = file_get_contents($out_location);

                //записываем его в рабочую папку
                file_put_contents("../images/image/$image_url", $content);

                $count++;
                echo "/ записано фото = 1 ";
            }else{
                //нет файла и в старой папке
                $this_image_isnot++;
                echo "/ нет такого фото нигде = 1 ";
            }
            $check_count++;
            echo "/ url в работе = 1 ";
        }
        $x++;
        sleep(3); // пауза
        echo " / image_id = $image_id / url - $image_url <br>";*/




  /*  $query="SELECT `id`, `in_product_name`, `barcode_article` FROM `z` WHERE 1";
    $result = mysqli_query($con, $query) or die (mysqli_error($con));
    while($row = mysqli_fetch_array($result)){
        $id =  $row[0];
        $in_product_name =  addslashes(trim($row[1])); 
        $barcode_article =  $row[2];

        $query="UPDATE `t_inventory_vs_inproductname` SET `barcode_article`='$barcode_article'
                    WHERE `in_product_name`='$in_product_name'";
        if(mysqli_query($con, $query) or die (mysqli_error($con))){
            echo "in_product_name = $in_product_name  <br>";
        }
        
    }*/

//-----------------

    mysqli_close($con);

?>