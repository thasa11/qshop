<?php
/**
  * Database connection class
  */
class connects{
    private $sarr = array();
    private $mysqli_conn;
    
    static function init(){ 
        $this->mysqli_conn=new mysqli('localhost','root','tetramoud','qvantel');  
        $this->mysqli_conn->set_charset("utf8");
        $this->mysqli_conn->query("SET NAMES utf8");
        /* check connection */
        if ($this->mysqli_conn->connect_errno) {
        $sarr[0] = "<span>Connect to DB failed: ".$mysqli->connect_errno."</span>";
            exit();
        }
        return $this->mysqli_conn;
    }
}
?>