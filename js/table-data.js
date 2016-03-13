// Global variables
// Data is an object (property:value) where the value is of an array of objects (property:name)
// Catalog data
var data = {
    elements: [
        { id: 0, name: "", descr: "", price: '0.00', amount: 0, pcs: 0, rowtotalsum: 0, reserved: 0 }
    ]
};
// Data is an object (property:value) where the value is of an array of objects (property:name)
// Basket data
var basket = {
    elements: [
        { id: 0, name: "", descr: "", price: '0.00', amount: 0, pcs: 0, rowtotalsum: 0, reserved: 0  }
    ]
};
var data4 = {elements: [{}]};
// Count of rows in DB
var elementsDB=0;
// APIKEY authentication
var APIKEY = 1234;
// Load data from server
$(document).ready(function(){
    // All doc ready code is in IIFE wrapper
});
// Get product data ajax function
function getCatalogData(command,startID,limit,filter,DBPage,vm, selectrow) {
    if (typeof selectrow == "undefined") selectrow = true;
    // Remove highlight classes
    setRowData(1);
    $.ajax({
            type: "GET",
            url: "api/load.php"+'?qshopCallback=?',
            dataType: "jsonp",
            async: true,
            jsonp: 'qshopCallback',
            data: { command : command,
                start : startID,
                limit : limit,
                price : filter,
                pricegroups: {
                    '<100': 'Cheaper than 100€',
                    'between 100 and 200': 'Between 100€ and 200€',
                    'between 200 and 300': 'Between 200€ and 300€',
                    'between 300 and 400': 'Between 300€ and 400€',
                    'between 400 and 500': 'Between 400€ and 500€', 
                    '>500': 'More that 500€',
                },
                search : vm.searchCat(),
                apikey: APIKEY
                },
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('load').innerHTML = 'Loading page '+vm.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            // Save response to viewmodel elements
            success: function( resp ){
               var info = resp.pop();
               var lkm = info.lkm;
               var $response = $('#response');
               $response.html(info.info?info.info:'');
               data.elements = resp;
               data.elements.forEach(function(entry) {
                  entry.id = parseInt(entry.id);
                  entry.totalsum = 0;
               });
               document.getElementById("load").innerHTML = "";
               vm.nbElementsDB(lkm);
               vm.elements(data.elements);
               setRowData(1);
               scrollPos(1);
            }
    });
    //return data.elements;
}
// Get basket data ajax functioin
function getBasketData(command,startID,limit,filter,DBPage,vmbasket, selectrow) {
    if (typeof selectrow == "undefined") selectrow = true;
    // Remove highlight classes
    setRowDataBasket(1);
    $.ajax({
            type: "GET",
            url: "api/loadcart.php"+'?qshopCallback=?',
            dataType: "jsonp",
            async: true,
            jsonp: 'qshopCallback',
            data: { command : command,
                start : startID,
                limit : limit,
                price : filter,
                pricegroups: {
                    '<100': 'Cheaper than 100€',
                    'between 100 and 200': 'Between 100€ and 200€',
                    'between 200 and 300': 'Between 200€ and 300€',
                    'between 300 and 400': 'Between 300€ and 400€',
                    'between 400 and 500': 'Between 400€ and 500€', 
                    '>500': 'More that 500€',
                },
                search : vmbasket.searchCat()
                },
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('basketload').innerHTML = 'Loading page '+vmbasket.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            // Save response to viewmodel elements
            success: function( resp ){
               var totals = resp.pop();
               var $response = $('#basketresponse');
               basket.elements = resp;
               basket.elements.forEach(function(entry) {
                  entry.id = parseInt(entry.id);
                  //entry.amount = entry.pcs;
               });
               document.getElementById("basketload").innerHTML = "";
               vmbasket.basketnbElementsDB(totals.lkm?totals.lkm:0);
               vmbasket.baskettotalsum(totals.totalsum?totals.totalsum:'0.00');
               vmbasket.basketelements(basket.elements);
               if (totals.info) $response.html(totals.info);
               setRowDataBasket(1);
               scrollPos(1,'basket');
            }
    });
}
// Get tr according to its index (within tbody) and update Details div inputs with row data
function setRowData(rowindex){
            var x = rowindex;
            var $tbl = $("#content>table");
            // Save content row index
            $("#rowindex").val(rowindex);
            var $nbofrows = $tbl.find("tr").size() - 1;
            var $elm = $tbl.find("tr").eq(x).children();
            var $detthinp = $("#details tr th:nth-child(1)").find("input:hidden");
            var $detth = $("#details tr th:nth-child(1)").find("span");
            var $dettdinp = $("#details tr td:nth-child(2)").find("input:text");
            // elm0 does not always exist (timing...)
            if ($elm[0]){
                $detthinp.val($elm[0].innerHTML);
                $detth.text("ID:"+$($elm[0]).text());
                $dettdinp.each(function(index){
                    $(this).val($elm[index+1].innerHTML);
                });
            }
            // Highlight selected row
            var $rows = $tbl.find("tr");
            $rows.removeClass("highlight");
            $rows.eq(x).addClass("highlight");
}
// Get tr according to its index (within tbody) and update Details div inputs with row data
function setRowDataBasket(rowindex){
            var x = rowindex;
            var $tbl = $("#basketcontent>table");
            // Save content row index
            $("#basketrowindex").val(rowindex);
            var $nbofrows = $tbl.find("tr").size() - 1;
            var $elm = $tbl.find("tr").eq(x).children();
            var $detthinp = $("#basketdetails tr th:nth-child(1)").find("input:hidden");
            var $detth = $("#basketdetails tr th:nth-child(1)").find("span");
            var $dettdinp = $("#basketdetails tr td:nth-child(2)").find("input:text");
            // elm0 does not always exist (timing...)
            if ($elm[0]){
                $detthinp.val($elm[0].innerHTML);
                $detth.text("ID:"+$($elm[0]).text());
                $dettdinp.each(function(index){
                    $(this).val($elm[index+1].innerHTML);
                });
            }
            // Highlight selected row
            var $rows = $tbl.find("tr");
            $rows.removeClass("highlight");
            $rows.eq(x).addClass("highlight");
}
// Restore content row data upon editing details
function restoreRowData(id, cart){
    var x = $("#rowindex").val();
    var $tbl = $("#content>table");
    var $elm = $tbl.find("tr").eq(x).children();
    var $dettdinp = $("#details tr td:nth-child(2)").find("input:text");
    if ($elm[0]){
         $dettdinp.each(function(index){
             if (this.id =='amount' && cart){
                $elm[index+1].innerHTML = parseInt($elm[index+1].innerHTML) - parseInt($(this).val());                 
             } else {
                $elm[index+1].innerHTML = $(this).val();
             }
         });
    }
}
// Add row data upon editing details
function addRowData(id){
    var x = $("#rowindex").val();
    var $tbl = $("#content>table");
    var $tr = $tbl.find("tr").eq(x);
    var $newtr = $tr.clone();
    var $elm = $newtr.children();
    var $dettdinp = $("#details tr td:nth-child(2)").find("input:text");
    if ($elm[0]){
        $elm[0].innerHTML = id;
        $dettdinp.each(function(index){
           $elm[index+1].innerHTML = $(this).val();
        });
    }
    // Add new row (cloned from original) to content table, and update product ID and row index
    $newtr.insertAfter($tr);
    $tr.removeClass('highlight');
    $("#details #productid").val(id);
    $("#rowindex").val(parseInt(x)+1);
}
// Add row data upon editing details
function removeRowData(id){
    var x = $("#rowindex").val();
    var $tbl = $("#content>table");
    var $tr = $tbl.find("tr").eq(x);
    $tr.remove();
    setRowData(parseInt(x)-1);
}
// Div Scroll position is set so that current row is in the top
function scrollPos(rowindex, prefix){
            if (typeof prefix == "undefined") prefix='';
            var x = rowindex;
            var $tbl = $("#"+prefix+"content>table");
            $content = $("#"+prefix+"content");
            $contentOffset = $content.offset().top;
            $divScrollTop = $content.scrollTop();
            $contentHeight = $content.height();
            $tblHeight = $tbl.height();
            // Check if animation is already in progress - avoin undesirable queuing of animations
            if ($tbl.find("tr").eq(x).offset() && !$content.is(':animated')){
                var $rowOffset = $tbl.find("tr").eq(x).offset().top;
                var $rowHeight = $tbl.find("tr").eq(x).height();
                var $diff = $contentOffset - $rowOffset;
                var d = $content.scrollTop() - ($diff - 2*$rowHeight);
                // Row is above #content, set scrollTop to 2nd row. DO not re-animate if scrollTop has not changed within 10px
                if ($diff >= 0 && (Math.abs(d) > 5)){
//                    console.log("Animate down ...");
                    $content.animate({
                        scrollTop: $divScrollTop - $diff - $rowHeight
                        }, 800, function () {
                    });
                } else if ($diff < 0 && (Math.abs(d) > 5) && !($divScrollTop > ($tblHeight - $contentHeight))){
//                    console.log("Animate up ...");
                    $content.animate({
                        scrollTop: $divScrollTop + ($rowOffset - $contentOffset) - $rowHeight
                        }, 800, function () {
                    });
                }
            }
}
