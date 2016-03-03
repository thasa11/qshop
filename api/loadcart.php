<?php
        # connect to db and authenticate api
        include 'db/mysqli.connect.php';
        include 'db/authenticate_api.php';

        $myArray = array();
        $tempArray = array();
        $tempArray2 = array();
        $ranges = array();
        $total = 0;
        $totalsum = 0;
        $grouptot = array();
        
        // 1 Generate pricegroups
        $pricegroups = $_POST['pricegroups'];
        $price = $_POST['price'];
        $search = $_POST['search'];
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
        from basket left join products on basket.pid = products.id
        left join stock on products.id = stock.productid
        WHERE 1) tbl
        WHERE price ".$price." AND (name LIKE '".$search."%' OR descr LIKE '%".$search."%') and purchased='N'
        group by pricerange
        ;")) {
            while ($row = $result->fetch_object()) {
                //$tempArray = $row;
                //array_push($myArray, $tempArray);
                $grouptot[$row->pricerange] = $row->grouptotal;
                $total+=$row->grouptotal;
            }
        }
        
        // 3 Query products from basket
                
        $query2 = "
        SELECT *, count(*) AS grouptotal FROM ( select *
        ".($casestruct?" , case ".$casestruct." end as pricerange":"")."
        from basket left join products on basket.pid = products.id
        left join stock on products.id = stock.productid
        WHERE price ".$price." AND name LIKE '".$search."%' and purchased='N' LIMIT ".$_POST['start'].",".$_POST['limit'].") tbl
        group by pricerange, tbl.id
        order by price asc
        ;";
        
        // echo $query2;
        
        if ($result = $mysqli->query("
        SELECT *, count(*) AS grouptotal FROM ( select *
        ".($casestruct?" ,case ".$casestruct." end as pricerange":"")."
        from basket left join products on basket.pid = products.id
        left join stock on products.id = stock.productid
        WHERE 1) tbl
        WHERE price ".$price." AND (name LIKE '".$search."%' OR descr LIKE '%".$search."%') and purchased='N'
        group by pricerange, tbl.id
        order by price asc
        LIMIT ".$_POST['start'].",".$_POST['limit']."
        ;")) {
        
        // 4 Fetch product rows and merge grouptotals
        //print_r($ranges);
            while ($row = $result->fetch_object()) {
                $tempArray = $row;
                $tempArray->rangesql = $ranges[$row->pricerange];
                $tempArray->grouptotal = $grouptot[$row->pricerange];
                $tempArray->rowtotalsum = number_format(round($row->pcs * $row->price, 2), 2,'.','');
                $totalsum += $row->pcs * $row->price;
                array_push($myArray, $tempArray);
            }
        }
        
        // 5 Finally add total amount and total sum of all rows
        $tempArray2['lkm']=$total;
        $tempArray2['totalsum']=number_format(round($totalsum, 2), 2,'.','');
        array_push($myArray, $tempArray2);
        
        // 6 Json encode -- allow cross site script loading content
        header('Content-Type: application/json');
        echo $_GET['qshopCallback'] . '(' . json_encode($myArray) . ')';
    
        //$result->close();
        $mysqli->close();
?>
