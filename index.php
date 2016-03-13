<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8" />
    <base href="//127.0.0.1/qvantel/" />
    <title>QShop API Demo</title>
    <link rel="stylesheet" href="css/qshop.css" type="text/css" />
    <link rel="stylesheet" href="css/common.css" />
    <link rel="stylesheet" href="css/sortable-table.css" />
</head>
    <body>
    <div id="schedulePane" class="ui-widget-content">
    <div id="glow">
    <div id="navigation">
        <h2>QShop Product Catalog</h2>
    </div>
    <div id="catalog">
        <div id="content">
       
        <table>
            <thead>
                <tr data-bind="click: sort">
                    <th title="Sort by Id" data-bind="css: idOrder">Id</th>
                    <th title="Sort by Product name" data-bind="css: nameOrder">Name</th>
                    <th title="Sort by Description" data-bind="css: descrOrder">Description</th>
                    <th title="Sort by Price" data-bind="css: priceOrder">Price (€)</th>
                    <th title="Sort by Amount" data-bind="css: amountOrder">Amount</th>
                    <th title="Sort by Reserved" data-bind="css: reservedOrder">Reserved</th>
                </tr>
            </thead>
            <tbody data-bind="foreach: elementsPaged, event: {click: showDetails}">
                <tr>
                    <td data-bind="text: id"></td>
                    <td data-bind="text: name"></td>
                    <td data-bind="text: descr"></td>
                    <td data-bind="text: price"></td>
                    <td data-bind="text: amount"></td>
                    <td data-bind="text: reserved"></td>
                </tr>
            </tbody>
        </table>
        </div>
        <div id="details">
            <form id="details-form">
                <table>
                    <caption title="Draggable">Product details</caption>
                    <thead title="Drag me around">
                    <tr>
                        <th id="rownb" width="50%"><span id="pid">ID:</span><input type="hidden" name="productid" id="productid" value="1"></th>
                        <th width="50%"><a href="#" title="Previous name" data-bind="click: goToPrevRow">&laquo;</a>
                            <a href="#" title="Next name" data-bind="click: goToNextRow">&raquo;</a></th>
                            <input type="hidden" name="rowindex" id="rowindex" value="0">
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Product name:</td>
                        <td colspan="1"><input type="text" name="name" id="name" value=""></td>
                    </tr>                    
                    <tr>
                        <td>Description:</td>
                        <td colspan="1"><input type="text" name="descr" id="descr" value=""></td>
                    </tr>
                    <tr>
                        <td>Price:</td>
                        <td colspan="1"><input type="text" name="price" id="price" value=""></td>
                    </tr>
                    <tr>
                        <td>Amount:</td>
                        <td colspan="1"><input type="text" name="amount" id="amount" value=""></td>
                    </tr>
                    <tr>
                        <td>Update:</td>
                        <td colspan="1"><input type="button" name="update" id="update" value="Update"></td>
                    </tr>
                    <tr>
                        <td>New:</td>
                        <td colspan="1"><input type="button" name="new" id="new" value="New"></td>
                    </tr>
                    <tr>
                        <td>Remove:</td>
                        <td colspan="1"><input type="button" name="remove" id="remove" value="Remove"></td>
                    </tr>
                    <tr>
                        <td>Cart:</td>
                        <td colspan="1"><input type="button" name="addcart" id="addcart" value="Add to cart"></td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <div id="response"></div>
        </div>
        <div id="footer">
        <div id="paging" class="clearfix">
              <table>
                  <tr>
                        <th>Results per page:</th>
                        <th>Go to page:</th>
                        <th>Search product:</th>
                        <th>Price range:</th>
                  </tr>
                  <tr>
                        <td><select id="perPage" title="Page size" data-bind="value: pageSize, event: {change: goToFirstPage}">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="all">All</option>
                        </select></td> 
                        
                        <td><input id="gotoPage" title="Go to page" data-bind="value: pageNumber, text: pageNumber, event: {keyup: goToPage}" size="2"></input></td>
                        <td><input id="search" title="Search from catalog" data-bind="value: searchCat, text: searchCat, event: {keyup: searchCatalog}" size="20"></input></td>
                            
                        <td><select id="prices" title="Filter by prices" data-bind="foreach: prices, event: { change: filterPrices }">
                            <option data-bind="value: rangesql, text: pricerange"></option>
                        </select></td>
                  </tr>
              </table>              
        </div>
        <div id="nav">
                      <table>
                          <tr>
                        <td style="text-align: left;"><a href="#" title="Previous page" data-bind="click: goToPrevPage">&laquo;</a></td>
                        <td><ul id="pages" data-bind="foreach: pages">
                            <li><a href="#" title="Go to page" data-bind="text: num, click: $parent.changePage"></a></li>
                        </ul></td>
                        <td style="text-align: right;"><a href="#" title="Next page" data-bind="click: goToNextPage">&raquo;&nbsp;&nbsp;</a></td>
                        </tr> 
                        </table>                       
        </div>
        <div id="load"></div>   
        </div>
    </div>
<!-- Catalog end, Basket start -->
    <div id="basket">
        <div id="basketnavigation">
        <h2>QShop Basket</h2>
        </div>
        <div id="basketcontent">
       
        <table>
            <thead>
                <tr data-bind="click: sort">
                    <th title="Sort by Id" data-bind="css: idOrder">Id</th>
                    <th title="Sort by Product name" data-bind="css: nameOrder">Name</th>
                    <th title="Sort by Description" data-bind="css: descrOrder">Description</th>
                    <th title="Sort by Price" data-bind="css: priceOrder">Price (€)</th>
                    <th title="Sort by Pieces" data-bind="css: pcsOrder">Pcs</th>
                    <th title="Sort by Total" data-bind="css: rowtotalsumOrder">Total (€)</th>
                </tr>
            </thead>
            <tbody data-bind="foreach: elementsPaged, event: {click: showDetails}">
                <tr>
                    <td data-bind="text: id"></td>
                    <td data-bind="text: name"></td>
                    <td data-bind="text: descr"></td>
                    <td data-bind="text: price"></td>
                    <td data-bind="text: pcs"></td>
                    <td data-bind="text: rowtotalsum"></td>
                </tr>
            </tbody>
        </table>
        </div>
        <div id="basketdetails">
            <form id="basket-form">
                <table>
                    <caption title="Draggable">Basket details</caption>
                    <thead title="Drag me around">
                    <tr>
                        <th id="rownb" width="50%"><span id="pid">ID:</span><input type="hidden" name="productid" id="basketproductid" value="1"></th>
                        <th width="50%"><a href="#" title="Previous name" data-bind="click: goToPrevRow">&laquo;</a>
                            <a href="#" title="Next name" data-bind="click: goToNextRow">&raquo;</a></th>
                            <input type="hidden" name="rowindex" id="basketrowindex" value="0">
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Product name:</td>
                        <td colspan="1"><input disabled type="text" name="name" id="name" value=""></td>
                    </tr>                    
                    <tr>
                        <td>Description:</td>
                        <td colspan="1"><input disabled type="text" name="descr" id="descr" value=""></td>
                    </tr>
                    <tr>
                        <td>Price:</td>
                        <td colspan="1"><input disabled type="text" name="price" id="price" value=""></td>
                    </tr>
                    <tr>
                        <td>Ordered:</td>
                        <td colspan="1"><input type="text" name="amount" id="amount" value=""></td>
                    </tr>
                    <tr>
                        <td>Update:</td>
                        <td colspan="1"><input type="button" name="update" id="update" value="Update"></td>
                    </tr>
                    <tr>
                        <td>New:</td>
                        <td colspan="1"><input disabled type="button" name="new" id="new" value="New"></td>
                    </tr>
                    <tr>
                        <td>Remove:</td>
                        <td colspan="1"><input type="button" name="remove" id="remove" value="Remove"></td>
                    </tr>
                    <tr>
                        <td><b>Total €:</b></td>
                        <td colspan="1" id="totalsum" data-bind="text: baskettotalsum"></td>
                    </tr>
                    <tr>
                        <td>Purchase:</td>
                        <td colspan="1"><input type="button" name="purchase" id="purchase" value="Purchase"></td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <div id="basketresponse"></div>
        </div>
        <div id="basketfooter">
        <div id="basketpaging" class="clearfix">
              <table>
                  <tr>
                        <th>Results per page:</th>
                        <th>Go to page:</th>
                        <th>Search product:</th>
                        <th>Price range:</th>
                  </tr>
                  <tr>
                        <td><select id="perPage" title="Page size" data-bind="value: pageSize, event: {change: goToFirstPage}">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="all">All</option>
                        </select></td> 
                        
                        <td><input id="gotoPage" title="Go to page" data-bind="value: pageNumber, text: pageNumber, event: {keyup: goToPage}" size="2"></input></td>
                        <td><input id="search" title="Search from catalog" data-bind="value: searchCat, text: searchCat, event: {keyup: searchCatalog}" size="20"></input></td>
                            
                        <td><select id="prices" title="Filter by prices" data-bind="foreach: prices, event: { change: filterPrices }">
                            <option data-bind="value: rangesql, text: pricerange"></option>
                        </select></td>
                  </tr>
              </table>              
        </div>
        <div id="basketnav">
                      <table>
                          <tr>
                        <td style="text-align: left;"><a href="#" title="Previous page" data-bind="click: goToPrevPage">&laquo;</a></td>
                        <td><ul id="pages" data-bind="foreach: pages">
                            <li><a href="#" title="Go to page" data-bind="text: num, click: $parent.changePage"></a></li>
                        </ul></td>
                        <td style="text-align: right;"><a href="#" title="Next page" data-bind="click: goToNextPage">&raquo;&nbsp;&nbsp;</a></td>
                        </tr> 
                        </table>                       
        </div>
        <div id="basketload"></div>   
        </div>

    </div>

        </div>
        </div>
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery-ui-1.10.3.js"></script>
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/knockout-3.4.0.js"></script>
        <script src="js/table-data.js"></script>
        <script src="js/sortable-table.js"></script>
    </body>
</html>
