<?php
/**
 * Controller that routes all API requests to QSHop API handler class
 */
    require_once 'API.class.php';
    // Requests from the same server don't have a HTTP_ORIGIN header
    if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
        $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
    }
    // Process request, user (now 0) should be taken from session
    if(isset($_REQUEST['apirequest'])){
        $API = new QShopAPI($_REQUEST['apirequest'], $_SERVER['HTTP_ORIGIN'], 0);
        echo $API->processAPI();
    }
?>