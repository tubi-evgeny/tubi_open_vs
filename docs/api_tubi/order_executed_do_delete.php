<?php

    //include 'connect.php';
       
       //echo 'hello connect' . "<br>";
       
        $new_month = date("m");
        $new_today = date("d");
        $new_time = date("G");
        $month;
        $today;
        $time = 3;
                                                                //--------показать month из таблицы
        $query = "SELECT info FROM t_general_info WHERE general_info = 'date_month'";
        $result = mysqli_query($con, $query) or die (mysql_error($link));
        $row = mysqli_fetch_array($result);
        $month = $row[0];
        
        if($month != $new_month){
            $today = 0;
                                                                //-----изменить месяц в таблице на новый
                                                                
            $query = "UPDATE t_general_info SET info = $new_month WHERE general_info = 'date_month' ";
            $result = mysqli_query($con, $query) or die (mysql_error($link));                                                    
                                                                
                                                                
                                                                
        }else{                                                      //--------показать дату из таблицы
            $query = "SELECT info FROM t_general_info WHERE general_info = 'date_day'";
            $result = mysqli_query($con, $query) or die (mysql_error($link));
            $row = mysqli_fetch_array($result);
            $today = $row[0];
        }
                                                            //-----если дата дольше чем дата в таблице то заменить дату
                                                            
            if($new_today > $today and $new_time > $time ){
                    
                $query = "UPDATE t_general_info SET info = $new_today WHERE general_info = 'date_day' ";
                $result = mysqli_query($con, $query) or die (mysql_error($link));
                                                                                       //  echo $today . '&nbsp' . 'time: ' . $new_time . "<br>";
                
                do_delete_dont_executed_order($con);     //------ удалить не обработанный  заказ                                                                    

            }                                                           //else          //  echo 'not info' . "<br>";
                
        
        
        function do_delete_dont_executed_order($con){     //------ удалить не обработанный  заказ
                                                            //----получить order_id всех незавершенных заказов
            $query = "SELECT order_id FROM t_order WHERE executed = 0";
            $result = mysqli_query($con, $query) or die (mysql_error($link));
            while($row = mysqli_fetch_array($result)){
                $order_id = $row[0];
                                //echo "order_id: " . $order_id . "<br>";
                                                            //----удалить из t_order_product все id с полученными  order_id 
                $query = "DELETE FROM t_order_product WHERE order_id = $order_id";
                $result = mysqli_query($con, $query) or die (mysql_error($link));                                            
                                                            
                                                             //----удалить из t_order незавершенный заказ
            
                $query = "DELETE FROM t_order WHERE order_id = $order_id";
                $result = mysqli_query($con, $query) or die(mysql_error($link));                                            
            }
                                                                                                          
            //echo 'finish' . "<br>";
        }
        
       
         

?>