<?php
        # connect to db and authenticate api
        include 'db/mysqli.connect.php';
        include 'db/authenticate_api.php';

        $myArray = array();
        
        // 1 Get parameters
        $newcount = $_POST['amount'];
        $id = $_POST['productid'];
        $userid = 0;
        $count = 0;

        // 2 Update cart with new amount
                
        $query="UPDATE basket SET pcs='$newcount' WHERE pid=$id;";
        
        //echo $query;
        $result=$mysqli->query($query);
        if ($mysqli->errno) $myArray[0]= '<span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        else {
            $myArray[0]= "<span>Ostoskori päivitetty ".$mysqli->affected_rows.' kpl</span>';
            $myArray[1]= $id;
        }
        
        // 2 Get count of product reserved in all baskets
        // TODO: userid not used
        $query="SELECT sum(pcs) as summa FROM basket WHERE pid = $id and purchased = 'N';";
        // echo $query;
        $result=$mysqli->query($query);
        if ($row = $result->fetch_object()){
            $count = $row->summa;
        }

        // 3 Update stock reserved with sum of basket content
        $query="UPDATE stock SET reserved=$count WHERE productid=$id;";
        $result=$mysqli->query($query);
        // echo $query;
        $result=$mysqli->query($query);
        if ($mysqli->errno) $myArray[0]= '<span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        else {
            $myArray[0]= "<span>Varastotiedot päivitetty ".$mysqli->affected_rows.' kpl</span>';
            $myArray[1]= $id;
        }                
                
        // 6 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>