<?php
        /**
         * function addcart
         * Adds product to users shopping cart.
         */
        # connect to db and authenticate api
        include 'db/mysqli.connect.php';
        include 'db/authenticate_api.php';

        $myArray = array();
        
        // TODO: Puuttuu user kentan kasittely, jolla voidaan hakea oman ostoskorin sisalto, nyt tukee vain 1 kayttajaa (usedid=0)
        
        // 1 Get parameters
        $amount = $_REQUEST['amount'];
        $id = $_REQUEST['productid'];
        $userid = 0;
        $purchased = 'N';
        
        // 2 Check if product is already in users own basket
        $query="SELECT count(*) as cnt FROM basket WHERE pid = $id and purchased='N';";
        $result=$mysqli->query($query);
        $row = $result->fetch_object();
        $count = $row->cnt;

        // 3 Insert into basket if no records found, else update pcs of product
        if ($count == 0){
            $query3="INSERT INTO basket VALUES(NULL,?,?,?,?)";          
            $stmt=$mysqli->prepare($query3);
            $stmt->bind_param('iiis',$id, $userid, $amount,$purchased); 
            $stmt->execute();
            if ($mysqli->errno) $myArray[0]= '</br><span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
            else {
                $myArray[0]= "</br><span>Ostoskori päivitetty ".$mysqli->affected_rows.' kpl</span>';
                $myArray[1]= $id;
            }
        } else {
            $query2="UPDATE basket SET pcs = (pcs +'$amount') WHERE pid=$id;";            
            $result=$mysqli->query($query2);
            if ($mysqli->errno) $myArray[0]= '</br><span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
            else {
                $myArray[0]= "</br><span>Ostoskori päivitetty ".$mysqli->affected_rows.' kpl</span>';
                $myArray[1]= $id;
            }
        }        
        
        // 4 Update stock reservation                
        $query="UPDATE stock SET reserved = (reserved+'$amount') WHERE productid=$id;";
        // echo $query;
        $result=$mysqli->query($query);
        if ($mysqli->errno) $myArray[0].= '</br><span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        else {
            $myArray[0].= "</br><span>Varastovaraus päivitetty ".$mysqli->affected_rows.' kpl</span>';
            $myArray[1]= $id;
        }
        
              
        // 5 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>
