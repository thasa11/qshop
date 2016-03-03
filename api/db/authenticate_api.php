<?php
    $myArray = array();
    $tempArray = array();
    if (!auth(0, $mysqli)) {
        $tempArray['info'] = "<span>API key missing or mismatch!</span>";
        $tempArray['lkm'] = 0;
        array_push($myArray, $tempArray);
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
        exit();
    }

    // Authenticate apikey
    function auth($userid, $mysqli){
        $verify = false;
        $headers = apache_request_headers();
        $key = base64_decode($headers["Authorization"]);
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