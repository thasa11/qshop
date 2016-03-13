<?php
        /**
         * updatecart
         * Updates users cart with new amount of product.
         * Updates stock reserved status with new reserved amount.
         */
        $mysqli = connect_db::init();

        $myArray = array();
        
        // 1 Get parameters
        $newcount = $_REQUEST['amount'];
        $id = $_REQUEST['productid'];
        $userid = 0;
        $count = 0;
        $saldo = 0;

        // 2 Check product availability
        $query="SELECT amount - reserved as saldo FROM stock WHERE productid = $id;";
        $result=$mysqli->query($query);
        $row = $result->fetch_object();
        $saldo = $row->saldo;

        // 2.1 Notice about stock availability
        if ($newcount > $saldo){
                $myArray[0]= "</br><span>Tuotteen varastosaldo ".$saldo." ei riitä!</span>";
                $myArray[1]= $id;
        } else {

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
        }
                
        // 6 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>
