<?php
# db connection select
		// LOCALHOST
//    	$mysqli=new mysqli('servername','user','password','dbname');
        $mysqli=new mysqli('localhost','root','tetramou','qvantel');  
        $mysqli->set_charset("utf8");
        $mysqli->query("SET NAMES utf8");
        /* check connection */
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
?>