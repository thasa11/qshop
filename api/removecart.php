<?php
        /**
         * function removecart
         * Removes product from users shopping cart.
         * Releases reserved amount back to stock.
         */
        # connect to db and authenticate api
        include 'db/mysqli.connect.php';
        include 'db/authenticate_api.php';

        $myArray = array();
        $count = 0;
        
        // 1 Get parameters
        $id = $_REQUEST['productid'];
                
        // 2 Remove product from cart
                
        $query="DELETE FROM basket WHERE pid=$id;";
        $result=$mysqli->query($query);
        
        //echo $query;
        if ($mysqli->errno) {
            $myArray[0]= '<span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        }
        else {
            $myArray[0]= "<span>Tuote poistettu korista ".$mysqli->affected_rows.' kpl</span>';
            $myArray[1]= $id;
        }                
        
        // 3 Get count of product reserved in all baskets
        // TODO: userid not used
        $query="SELECT sum(pcs) as summa FROM basket WHERE pid = '$id' and purchased = 'N';";
        // echo $query;
        $result=$mysqli->query($query);
        if ($row = $result->fetch_object()){
            $count = $row->summa;
        }
        if (!$count)$count=0;
        // 3 Update stock reserved with sum of baskets content
        $query="UPDATE stock SET reserved='$count' WHERE productid = '$id';";
        $result=$mysqli->query($query);
        // echo $query;
        $result=$mysqli->query($query);
        if ($mysqli->errno) $myArray[0]= '<span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        else {
            $myArray[0]= "<span>Varastotiedot päivitetty ".$mysqli->affected_rows.' kpl</span>';
            $myArray[1]= $id;
        }                
                
        // 4 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>
