<?php
    /**
     * APIKEY authentication 
     * The api key must be present in all requests, and it must match the key in database
     */

    $myArray = array();
    $tempArray = array();
    if (!auth(0, $mysqli)) {
        $tempArray['info'] = "<span>API key missing or mismatch!</span></br>";
        $tempArray['lkm'] = 0;
        if (!isset($_REQUEST['qshopCallback'])){
            $_REQUEST['qshopCallback'] = '';
            $tempArray['info'] .= "<span>Callback mismatch!</span></br>";            
        }   
        array_push($myArray, $tempArray);
        header('Content-Type: application/json');
        echo $_REQUEST['qshopCallback'] . '(' . json_encode($myArray) . ')';
        exit();
    }

    /**
     * function auth 
     * Api key must be present in all requests, and it must match the key in database.
     * @return true if API KEY valid, false otherwise 
     */
    function auth($userid, $mysqli){
        $headers = apache_request_headers();
        if (isset($headers["Authorization"])){
            $key = base64_decode($headers["Authorization"]);
        } else return false;
        $query = "SELECT apikey FROM users WHERE userid =".$userid.";";
        //echo $query;
        if ($result = $mysqli->query($query)){
            if ($row = $result->fetch_object()){
                //echo $row->apikey." ".$key;
                if ($row->apikey == $key) return true;
                else return false;
            } else return false;
        } else return false;
    }
?>