<?php
        /**
         * function load
         * Query product catalog with given filters, page and search string.
         * Returns search result grouped by given pricegroups. 
         */
        # connect to db and authenticate api
        include 'db/mysqli.connect.php';
        include 'db/authenticate_api.php';

        $myArray = array();
        $tempArray = array();
        $tempArray2 = array();
        $ranges = array();
        $total = 0;
        $grouptot = array();
        
        // 1 Generate pricegroups
        $pricegroups = $_REQUEST['pricegroups'];
        $price = $_REQUEST['price'];
        $search = trim($_REQUEST['search']);
        if ($price =="%") $price = " LIKE '%'";
        $casestruct="";
        if (is_array($pricegroups)){
            foreach ($pricegroups as $key => $value) {
                $casestruct.= " when price ".$key. " then '".$value."'";
                $ranges[$value] = $key;
            }
        }
        
        //echo $casestruct;
        
        // 2 Get price group total amount of product rows
        $query = "SELECT count(*) as lkm FROM products WHERE price ".$price.";";
        // echo $query;
        if ($result = $mysqli->query("
        SELECT *, count(*) AS grouptotal FROM ( select *
        ".($casestruct?" ,case ".$casestruct." end as pricerange":"")."
        from products left join stock on products.id = stock.productid
        WHERE 1) tbl
        WHERE price ".$price." AND (name LIKE '".$search."%' OR descr LIKE '%".$search."%')
        group by pricerange
        ;")) {
            while ($row = $result->fetch_object()) {
                //$tempArray = $row;
                //array_push($myArray, $tempArray);
                $grouptot[$row->pricerange] = $row->grouptotal;
                $total+=$row->grouptotal;
            }
        }
        
        // 3 Query products
                
        $query2 = "
        SELECT *, count(*) AS grouptotal FROM ( select *
        ".($casestruct?" , case ".$casestruct." end as pricerange":"")."
        from products left join stock on products.id = stock.productid
        WHERE price ".$price." AND name LIKE '".$search."%' LIMIT ".$_REQUEST['start'].",".$_REQUEST['limit'].") tbl
        group by pricerange, tbl.id
        order by price asc
        ;";
        
        //echo $query2;
        
        if ($result = $mysqli->query("
        SELECT *, count(*) AS grouptotal FROM ( select *
        ".($casestruct?" ,case ".$casestruct." end as pricerange":"")."
        from products left join stock on products.id = stock.productid
        WHERE 1) tbl
        WHERE price ".$price." AND (name LIKE '".$search."%' OR descr LIKE '%".$search."%')
        group by pricerange, tbl.id
        order by price asc
        LIMIT ".$_REQUEST['start'].",".$_REQUEST['limit']."
        ;")) {
        
        // 4 Fetch product rows and merge grouptotals
        //print_r($ranges);
            while ($row = $result->fetch_object()) {
                $tempArray = $row;
                $tempArray->rangesql = $ranges[$row->pricerange];
                $tempArray->grouptotal = $grouptot[$row->pricerange];
                array_push($myArray, $tempArray);
            }
        }
        
        // 5 Finally add total amount of all rows
        $tempArray2['lkm']=$total;
        array_push($myArray, $tempArray2);
        
        // 6 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_REQUEST['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        $result->close();
        $mysqli->close();
?>
