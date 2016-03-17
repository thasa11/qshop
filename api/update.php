<?php
        /**
         * function update
         * Updates product in product catalog with given values.
         * Updates stock amount (or inserts new stock row if product not existed)
         */
        $mysqli = connect_db::init();

        $myArray = array();
        
        // 1 Get parameters
        $descr = $mysqli->real_escape_string(strip_tags($_REQUEST['descr']));
        $price = $_REQUEST['price'];
        $name = $mysqli->real_escape_string(strip_tags($_REQUEST['name']));
        $amount = $_REQUEST['amount'];
        $id = $_REQUEST['productid'];
        $reserved = 0;
        
        // 3 Update products
                
        $query="UPDATE products SET descr='$descr', name='$name', price='$price' WHERE id=$id;";
        
        //echo $query;
        $result=$mysqli->query($query);
        if ($mysqli->errno) $myArray[0]= '<span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        else {
            $myArray[0]= "<span>Tuotetiedot päivitetty ".$mysqli->affected_rows.' kpl</span>';
            $myArray[1]= $id;
        }                
        
        // 3 Update stock or insert to stock
        $query="SELECT count(*) as cnt from stock WHERE productid=$id;";
        $query2="UPDATE stock SET amount='$amount' WHERE productid=$id;";
        $query3="INSERT INTO stock VALUES(NULL,?,?,?)";
        $result=$mysqli->query($query);
        $row = $result->fetch_object();
        $count = $row->cnt;
        // Insert if no records found
        if ($count == 0){
            $stmt=$mysqli->prepare($query3);
            $stmt->bind_param('iii',$id, $amount, $reserved); 
            $stmt->execute();           
        } else {
            $result=$mysqli->query($query2);
            if ($mysqli->errno) $myArray[0].= '</br><span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
            else {
                $myArray[0].= "</br><span>Varastotiedot päivitetty ".$mysqli->affected_rows.' kpl</span>';
                $myArray[1]= $id;
            }
        }
                
        // 6 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>
