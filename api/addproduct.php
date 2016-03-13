<?php
        /**
         * function new
         * Creates new product in the product catalog.
         * Updates stock amounts. 
         */
        $mysqli = connect_db::init();

        $myArray = array();
        
        // 1 Get parameters
        $descr = $_REQUEST['descr'];
        $price = $_REQUEST['price'];
        $name = $_REQUEST['name'];
        $amount = $_REQUEST['amount'];
        $reserved = 0;
        
        // 2 Insert product
                
        $query="INSERT INTO products VALUES (NULL,?,?,?)";
        $stmt=$mysqli->prepare($query);
        $stmt->bind_param('ssi',$name, $descr, $price); 
        $stmt->execute();
        $id = $mysqli->insert_id;
        
        //echo $query;
        if ($mysqli->errno) {
            $myArray[0]= '<span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        }
        else {
            $myArray[0]= "<span>Tuote lisätty ".$mysqli->affected_rows.' kpl</span>';
            $myArray[1]= $id;
        }                
        
        // 4 Insert to stock
        $query="INSERT INTO stock VALUES(NULL,?,?,?)";
        $stmt=$mysqli->prepare($query);
        $stmt->bind_param('iii',$id, $amount, $reserved); 
        $stmt->execute();           
        if ($mysqli->errno) $myArray[0].= '</br><span>'.$mysqli->error.':'.$mysqli->errno.'</span>';
        else $myArray[0].= "</br><span>Varastotiedot päivitetty ".$mysqli->affected_rows.' kpl</span>';
                
        // 5 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>
