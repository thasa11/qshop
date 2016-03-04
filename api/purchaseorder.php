<?php
        /**
         * function purchaseorder
         * Loops through products in users shopping basket, and decreases products
         * amount in stock and releases reservation status in stock.
         * Updates products status in users basket as purchased for further order processing. 
         */
        # connect to db and authenticate api
        include 'db/mysqli.connect.php';
        include 'db/authenticate_api.php';
        
        // 1 Init parameters
        $myArray = array('');
        $count = 0;
        $mycount = 0;
        $userid = 0;
        $id = 0;
        $reserved = 0;
        $rowsum = 0;
        $totsum = 0;
        
        // 1.1 Get filtering parameters
        
        $start = $_REQUEST['start'];
        $price = $_REQUEST['price'];
        $limit = $_REQUEST['limit'];
        $search = $_REQUEST['search'];
        if ($price =="%") $price = " LIKE '%'";        
                
        // 2 Select products in cart to be purchased using filter
                
        $query="select * FROM basket 
        left join products on basket.pid = products.id WHERE userid = $userid AND purchased='N'
        AND price ".$price." AND (name LIKE '".$search."%' OR descr LIKE '".$search."%') and purchased='N' LIMIT ".$_REQUEST['start'].",".$_REQUEST['limit'].";";
        
        $result=$mysqli->query($query);
        // echo $query."</br>";
        // 3 Loop through products
        while($row = $result->fetch_object()){
            // 3.1 Get count of product reserved in users basket            
            $mycount = $row->pcs;
            $id = $row->pid;
            $rowsum = $row->pcs * $row->price;
            // 3.2 Get count of this product reserved in all baskets
            // TODO: userid not used
            $query2="SELECT sum(pcs) as summa FROM basket WHERE pid = '$id' AND purchased='N';";
            //echo $query2."</br>";
            $result2=$mysqli->query($query2);
            if ($row2 = $result2->fetch_object()){
                $count = $row2->summa;
            }
            if (!$count)$count=0;
            $reserved = $count - $mycount;

            // 3.3 Release stock reservation with product count, and decrease stock amount with product count
            $query3="UPDATE stock SET reserved='$reserved', amount = amount - $mycount WHERE productid = '$id';";
            $result3=$mysqli->query($query3);
            //echo $query3."</br>";
            if ($mysqli->errno) $myArray[0]= '<span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
            else {
                $myArray[0].= "<span>Tilattu tuote ID ".$id.' '.$mycount.' kpl, summa '.number_format($rowsum, 2).'</span></br>';                    
                $myArray[1]= $id;
            }
            // 3.4 Update basket row as purchased
            $query4="UPDATE basket SET purchased='Y' WHERE pid = '$id';";
            $result4=$mysqli->query($query4);
            $totsum+=$rowsum;
            //echo $query4."</br>";
        }
       
        $myArray[0].= '<span>Yhteensä summa: '.number_format($totsum,2).'</span>';
        
        // 4 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>
