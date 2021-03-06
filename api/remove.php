<?php
        /**
         * function remove
         * Removes product from product catalog, and updates stock amounts accordingly.
         * Removes product from all users shopping cart.
         */
        $mysqli = connect_db::init();

        $myArray = array();
        
        // 1 Get parameters
        $id = $_REQUEST['productid'];
                
        // 2 Remove product
                
        $query="DELETE FROM products WHERE id=$id;";
        $result=$mysqli->query($query);
        
        //echo $query;
        if ($mysqli->errno) {
            $myArray[0]= '<span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        }
        else {
            $myArray[0]= "<span>Tuote poistettu ".$mysqli->affected_rows.' kpl</span>';
            $myArray[1]= $id;
        }                
        
        // 3 Remove from stock
        $query="DELETE FROM stock WHERE productid=$id;";
        $result=$mysqli->query($query);
        //echo $query;
        
        if ($mysqli->errno) $myArray[0].= '</br><span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        else $myArray[0].= "</br><span>Varastotiedot päivitetty ".$mysqli->affected_rows.' kpl</span>';
                
        // 4 Remove from basket
        $query="DELETE FROM basket WHERE productid=$id";
        $result=$mysqli->query($query);
        
        if ($mysqli->errno) $myArray[0].= '</br><span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        else $myArray[0].= "</br><span>Tuote poistettu ostoskorista ".$mysqli->affected_rows.' kpl</span>';
                
        // 6 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>
