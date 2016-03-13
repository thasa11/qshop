/**
 * @author Java-QShop
 * Knockout.js adds a global ko object
 */
$(function () {
    // Viewmodel for CATALOG view (name:value properties) 
    var vm = {
        // elements array is an observableArray
        elements: ko.observableArray(data.elements),
        nbElementsDB: ko.observable(elementsDB.lkm),
        idOrder: ko.observable("ascending"),
        nameOrder: ko.observable("ascending"),
        descrOrder: ko.observable("ascending"),
        amountOrder: ko.observable("ascending"),
        reservedOrder: ko.observable("ascending"),
        priceOrder: ko.observable("ascending"),
        rowtotalsumOrder: ko.observable("ascending"),
        // Page size
        pageSize: ko.observable(50),
        currentPage: ko.observable(1),
        elementsPaged: ko.observableArray(),
        pages: ko.observableArray(),
        prices: ko.observableArray(),
        pricerange: ko.observable('%'),
        rangesql: ko.observable('%'),
        originalElements: null,
        pageNumber: ko.observable(1),
        currentRow: ko.observable(1),
        searchCat: ko.observable(''),
        updateDB: ko.observable(),
        start: ko.observable(0),
        limit: ko.observable(10),

        // pageSize * pageNumber = 5 * 10 = 50 = ID of last viewable element
        // Instead of JS regular array.sort(), we use Knockout sort
        sort: function (viewmodel, e) {
            // Which th was clicked; get data-bind attribute e.g. nameOrder
            var orderProp = $(e.target).attr("data-bind").split(" ")[1],
            // viewmodel property's value getter () e.g. ascending or setter (value)
            orderVal = viewmodel[orderProp](),
            // Get e.g. name which is a property inside elements array
            comparatorProp = orderProp.split("O")[0];
            viewmodel.elements.sort(function (a, b) {
                var propA = a[comparatorProp],
                propB = b[comparatorProp];
                if (typeof (propA) !== typeof (propB)) {
                    propA = (typeof (propA) === "string") ? 0 :propA;
                    propB = (typeof (propB) === "string") ? 0 :propB;
                }
                // Compare prices as floats
                if (comparatorProp != "price" && comparatorProp != "amount" && comparatorProp != "reserved"){                    
                    // Sort comparator function, return 0, 1, or -1
                    if (orderVal === "ascending") {
                        return (propA === propB) ? 0 : (propA<propB) ? -1 : 1;
                    } else {
                        return (propA === propB) ? 0 : (propA<propB) ? 1 : -1;
                    }
                } else {
                     // Sort comparator function, return 0, 1, or -1
                    if (orderVal === "ascending") {
                        return (propA === propB) ? 0 : (parseFloat(propA)<parseFloat(propB)) ? -1 : 1;
                    } else {
                        return (propA === propB) ? 0 : (parseFloat(propA)<parseFloat(propB)) ? 1 : -1;
                    }                  
                }
            });
            orderVal = (orderVal === "ascending") ? "descending" : "ascending";
            viewmodel[orderProp](orderVal);
            // Reset order property values to ascending
            for (prop in viewmodel) {
                // Reset ..Order properties in viewmodel
                if (prop.indexOf("Order") !== -1 && prop !== orderProp) {
                    viewmodel[prop]("ascending");
                }
            }
            setRowData(1);
            scrollPos(1);
        },
        // Show data in details panel
        showDetails: function (viewmodel, e) {
            var $elm = $(e.target).parent().children();
            var $trnumber = $elm.parent().index();
            viewmodel.currentRow($trnumber + 1);
        },
        // Viewmodel methods (Functions total, next, prev, change page, manage classes)
        totalPages: function () {
            var totalPages = this.elements().length / this.pageSize() || 1;
            return Math.ceil(totalPages);
        },

        // Pages available in database
        totalPagesDB: function () {
            var totalPagesDB = this.nbElementsDB() / this.pageSize() || 1;
            return Math.ceil(totalPagesDB);
        },

        // Next page link
        goToNextPage: function () {
            var newPage;
            if (this.currentPage() < this.totalPagesDB() - 1) {
                newPage = this.currentPage() + 1;
                this.currentPage(newPage);
                this.pageNumber(this.pageNumber() + 1);
            }
            // Reset details view to 1st row
            setRowData(1);
            scrollPos(1);
            this.currentRow(1);
        },

        // Previous page link
        goToPrevPage: function () {
            var newPage;
            if (this.currentPage() > 0) {
                newPage = this.currentPage() - 1;
                this.currentPage(this.currentPage() - 1);
                this.pageNumber(this.currentPage() + 1);
            }
            setRowData(1);
            scrollPos(1);
            this.currentRow(1);
        },

        // Next row link
        goToNextRow: function () {
            var x = this.currentRow();
            var $tbl = $("#content>table");
            var $nbofrows = $tbl.find("tr").size() - 1;
            x = x % $nbofrows + 1;
            scrollPos(x);
            this.currentRow(x);
        },
       
        // Prev row link
        goToPrevRow: function () {
            var x = this.currentRow();
            var $tbl = $("#content>table");
            var $nbofrows = $tbl.find("tr").size() - 1;
            if (x === 1) x = $nbofrows;
            else x -= 1;
            setRowData(x);
            scrollPos(x);
            this.currentRow(x);
        },
       
        // Go to Page entered from input field (Note! DBPagesize 'All' is NaN checked too)
        goToPage: function (obj, e) {
            var el = $(e.target),
            newPage = parseInt(el.val(), 10) - 1;
            if (!isNaN(newPage) && newPage >= 0){
                if (newPage<this.totalPagesDB() && newPage !=-1){
                    vm.currentPage(newPage);
                }
            setRowData(1);
            this.currentRow(1);
            }
        },

        // Change Page from page links
        changePage: function (obj, e) {
            var el = $(e.target),
            newPage = parseInt(el.text(), 10) - 1;
            vm.currentPage(newPage);
            vm.pageNumber(newPage + 1);
            setRowData(1);
            vm.currentRow(1);
        },        

        // adding or removing the disabled class from the Previous and Next links && adding the active class to the
        // numbered link corresponding to the current page
        manageClasses: function () {
            var nav = $("#footer").find("#nav"),
            currentpage = this.currentPage();
            nav.find("a.active")
            .removeClass("active")
            .end()
            .find("a.disabled")
            .removeClass("disabled");
            if (currentpage === 0) {
                nav.children(":first-child").addClass("disabled");
            } else if (currentpage === this.totalPagesDB() - 1) {
                nav.children(":last-child").addClass("disabled");
            }
            $("#pages").find("a")
            .eq(currentpage)
            .addClass("active");
        },

        // RESET = select change event handler which resets the current page to 0 every time when selects value changes
        goToFirstPage: function (obj, e) {
            this.currentPage(0);
            this.pageNumber(1);
            setRowData(1);
            scrollPos(1);
            this.currentRow(1);
        },
        
        // Search products from catalog
        searchCatalog: function (obj, e) {
            var el = $(e.target);
            var searchstr = el.val();
            vm.searchCat(searchstr);
            this.currentPage(0);
            this.pageNumber(1);
            setRowData(1);
            scrollPos(1);
            this.currentRow(1);
        },

        // Filter data according to selected price range
        filterPrices: function (obj, e) {
            // First is just a label, skip it..
            var targetelm = e.originalEvent.target;
            if (targetelm.selectedIndex !== 0) {
                var vm = this;
                var tmpArr = [];
                var pricerange = targetelm.value;
                var rangesql = targetelm.options[targetelm.selectedIndex].text;
                vm.pricerange(pricerange);    // Update price observable
                vm.rangesql(rangesql);
                vm.goToFirstPage(); // Page 0
                vm.originalElements = vm.elements();
                $.each(vm.elements(), function (i, item) {
                    if (item.pricerange === pricerange) {
                        tmpArr.push(item);
                    }
                });
                vm.elements(tmpArr).currentPage(0);
                var label = $("<span/>", {
                    "class": "filter-label",
                    text: rangesql
                });
                $("<a/>", {
                    text: " X",
                    href: "#",
                    title: "Remove this filter"
                }).appendTo(label).on("click", function () {
                    $(this).parent().remove();
                    $("#paging #prices").show().prop("selectedIndex", 0);
                    vm.elements(vm.originalElements).currentPage(0);
                    vm.pricerange('%');
                    vm.rangesql('%');
                    vm.goToFirstPage();
                });
                label.insertBefore("#paging #prices").next().hide();
            }
        }
    };
    // Viewmodel for BASKET view
    var vmbasket = {
        // elements array is an observableArray
        basketelements: ko.observableArray(basket.elements),
        basketnbElementsDB: ko.observable(elementsDB.lkm),
        baskettotalsum: ko.observable(elementsDB.lkm),
        idOrder: ko.observable("ascending"),
        nameOrder: ko.observable("ascending"),
        descrOrder: ko.observable("ascending"),
        pcsOrder: ko.observable("ascending"),
        priceOrder: ko.observable("ascending"),
        rowtotalsumOrder: ko.observable("ascending"),
        // Page size
        pageSize: ko.observable(50),
        currentPage: ko.observable(1),
        elementsPaged: ko.observableArray(),
        pages: ko.observableArray(),
        prices: ko.observableArray(),
        pricerange: ko.observable('%'),
        rangesql: ko.observable('%'),
        originalElements: null,
        pageNumber: ko.observable(1),
        currentRow: ko.observable(1),
        searchCat: ko.observable(''),
        start: ko.observable(0),
        limit: ko.observable(10),

        // pageSize * pageNumber = 5 * 10 = 50 = ID of last viewable element
        // Instead of JS regular array.sort(), we use Knockout sort
        sort: function (viewmodel, e) {
            // Which th was clicked; get data-bind attribute e.g. nameOrder
            var orderProp = $(e.target).attr("data-bind").split(" ")[1],
            // viewmodel property's value getter () e.g. ascending or setter (value)
            orderVal = viewmodel[orderProp](),
            // Get e.g. name which is a property inside elements array
            comparatorProp = orderProp.split("O")[0];
            viewmodel.basketelements.sort(function (a, b) {
                var propA = a[comparatorProp],
                propB = b[comparatorProp];
                if (typeof (propA) !== typeof (propB)) {
                    propA = (typeof (propA) === "string") ? 0 :propA;
                    propB = (typeof (propB) === "string") ? 0 :propB;
                }
                // Compare prices as floats
                if (comparatorProp != "price" && comparatorProp != "pcs" && comparatorProp != "rowtotalsum"){                    
                    // Sort comparator function, return 0, 1, or -1
                    if (orderVal === "ascending") {
                        return (propA === propB) ? 0 : (propA<propB) ? -1 : 1;
                    } else {
                        return (propA === propB) ? 0 : (propA<propB) ? 1 : -1;
                    }
                } else {
                     // Sort comparator function, return 0, 1, or -1
                    if (orderVal === "ascending") {
                        return (propA === propB) ? 0 : (parseFloat(propA)<parseFloat(propB)) ? -1 : 1;
                    } else {
                        return (propA === propB) ? 0 : (parseFloat(propA)<parseFloat(propB)) ? 1 : -1;
                    }                  
                }
            });
            orderVal = (orderVal === "ascending") ? "descending" : "ascending";
            viewmodel[orderProp](orderVal);
            // Reset order property values to ascending
            for (prop in viewmodel) {
                // Reset ..Order properties in viewmodel
                if (prop.indexOf("Order") !== -1 && prop !== orderProp) {
                    viewmodel[prop]("ascending");
                }
            }
            setRowDataBasket(1);
            scrollPos(1,'basket');
        },
        // Show data in details panel
        showDetails: function (viewmodel, e) {
            var $elm = $(e.target).parent().children();
            var $trnumber = $elm.parent().index();
            viewmodel.currentRow($trnumber + 1);
        },
        // Viewmodel methods (Functions total, next, prev, change page, manage classes)
        totalPages: function () {
            var totalPages = this.basketelements().length / this.pageSize() || 1;
            return Math.ceil(totalPages);
        },

        // Pages available in database
        totalPagesDB: function () {
            var totalPagesDB = this.basketnbElementsDB() / this.pageSize() || 1;
            return Math.ceil(totalPagesDB);
        },

        // Next page link
        goToNextPage: function () {
            var newPage;
            if (this.currentPage() < this.totalPagesDB() - 1) {
                newPage = this.currentPage() + 1;
                this.currentPage(newPage);
                this.pageNumber(this.pageNumber() + 1);
            }
            // Reset details view to 1st row
            setRowDataBasket(1);
            scrollPos(1, 'basket');
            this.currentRow(1);
        },

        // Previous page link
        goToPrevPage: function () {
            var newPage;
            if (this.currentPage() > 0) {
                newPage = this.currentPage() - 1;
                this.currentPage(this.currentPage() - 1);
                this.pageNumber(this.currentPage() + 1);
            }
            setRowDataBasket(1);
            scrollPos(1, 'basket');
            this.currentRow(1);
        },

        // Next row link
        goToNextRow: function () {
            var x = this.currentRow();
            var $tbl = $("#basketcontent>table");
            var $nbofrows = $tbl.find("tr").size() - 1;
            x = x % $nbofrows + 1;
            scrollPos(x, 'basket');
            this.currentRow(x);
        },
       
        // Prev row link
        goToPrevRow: function () {
            var x = this.currentRow();
            var $tbl = $("#basketcontent>table");
            var $nbofrows = $tbl.find("tr").size() - 1;
            if (x === 1) x = $nbofrows;
            else x -= 1;
            setRowDataBasket(x);
            scrollPos(x, 'basket');
            this.currentRow(x);
        },
       
        // Go to Page entered from input field (Note! DBPagesize 'All' is NaN checked too)
        goToPage: function (obj, e) {
            var el = $(e.target),
            newPage = parseInt(el.val(), 10) - 1;
            if (!isNaN(newPage) && newPage >= 0){
                if (newPage<this.totalPagesDB() && newPage !=-1){
                    vmbasket.currentPage(newPage);
                }
            setRowDataBasket(1);
            this.currentRow(1);
            }
        },

        // Change Page from page links
        changePage: function (obj, e) {
            var el = $(e.target),
            newPage = parseInt(el.text(), 10) - 1;
            vmbasket.currentPage(newPage);
            vmbasket.pageNumber(newPage + 1);
            setRowDataBasket(1);
            vmbasket.currentRow(1);
        },        

        // adding or removing the disabled class from the Previous and Next links && adding the active class to the
        // numbered link corresponding to the current page
        manageClasses: function () {
            var nav = $("#basketfooter").find("#basketnav"),
            currentpage = this.currentPage();
            nav.find("a.active")
            .removeClass("active")
            .end()
            .find("a.disabled")
            .removeClass("disabled");
            if (currentpage === 0) {
                nav.children(":first-child").addClass("disabled");
            } else if (currentpage === this.totalPagesDB() - 1) {
                nav.children(":last-child").addClass("disabled");
            }
            $("#basketnav #pages").find("a")
            .eq(currentpage)
            .addClass("active");
        },

        // RESET = select change event handler which resets the current page to 0 every time when selects value changes
        goToFirstPage: function (obj, e) {
            this.currentPage(0);
            this.pageNumber(1);
            setRowDataBasket(1);
            scrollPos(1, 'basket');
            this.currentRow(1);
        },
        
        // Search products from catalog
        searchCatalog: function (obj, e) {
            var el = $(e.target);
            var searchstr = el.val();
            vmbasket.searchCat(searchstr);
            this.currentPage(0);
            this.pageNumber(1);
            setRowDataBasket(1);
            scrollPos(1, 'basket');
            this.currentRow(1);
        },

        // Filter data according to selected price range
        filterPrices: function (obj, e) {
            // First is just a label, skip it..
            var targetelm = e.originalEvent.target;
            if (targetelm.selectedIndex !== 0) {
                var tmpArr = [];
                var pricerange = targetelm.value;
                var rangesql = targetelm.options[targetelm.selectedIndex].text;
                this.pricerange(pricerange);    // Update price observable
                this.rangesql(rangesql);
                this.goToFirstPage(); // Page 0
                this.originalElements = vmbasket.basketelements();
                $.each(vmbasket.basketelements(), function (i, item) {
                    if (item.pricerange === pricerange) {
                        tmpArr.push(item);
                    }
                });
                this.basketelements(tmpArr).currentPage(0);
                var label = $("<span/>", {
                    "class": "filter-label",
                    text: rangesql
                });
                $("<a/>", {
                    text: " X",
                    href: "#",
                    title: "Remove this filter"
                }).appendTo(label).on("click", function () {
                    $(this).parent().remove();
                    $("#basketpaging #prices").show().prop("selectedIndex", 0);
                    vmbasket.basketelements(vmbasket.originalElements).currentPage(0);
                    vmbasket.pricerange('%');
                    vmbasket.rangesql('%');
                    vmbasket.goToFirstPage();
                });
                label.insertBefore("#basketpaging #prices").next().hide();
            }
        }
    }; // End of basket
    
    // Computed observable; function is invoked whenever the monitored Viewmoded properties change
    // VM is passed in as an argument
    vm.createPage = ko.computed(function () {
        // if (this.elements.length==0 && this.originalElements!=null) this.elements(this.originalElements);
        // because vm as passed as 2nd argument "this" is referenced as vm
        if (this.pageSize() === "all") {
            this.elementsPaged(this.elements.slice(0));
        } else {
           var pagesize = parseInt(this.pageSize(), 10);
           this.elementsPaged(this.elements.slice(0, this.pageSize())).manageClasses();
        }
        //console.log("Create page "+this.elements());
    }, vm);
    
    vmbasket.createPage = ko.computed(function () {
        // if (this.elements.length==0 && this.originalElements!=null) this.elements(this.originalElements);
        // because vm as passed as 2nd argument "this" is referenced as vm
        if (this.pageSize() === "all") {
            this.elementsPaged(this.basketelements.slice(0));
        } else {
           var pagesize = parseInt(this.pageSize(), 10);
           this.elementsPaged(this.basketelements.slice(0, this.pageSize())).manageClasses();
        }
        //console.log("Create page "+this.elements());
    }, vmbasket);

    // Computed observable 2: create page links to directly goto selected (clicked) page
    // tmp is the object array of pages (num:value) that is put to html
    // could use $index
    vm.createPages = ko.computed(function () {
        var tmp = [];
        var totalPagesDB = this.totalPagesDB();
        for (var x = 0; x < totalPagesDB; x++) {
            tmp.push({ num: x + 1 });
        }
        this.pages(tmp).manageClasses();
    }, vm);

    vmbasket.createPages = ko.computed(function () {
        var tmp = [];
        var totalPagesDB = this.totalPagesDB();
        for (var x = 0; x < totalPagesDB; x++) {
            tmp.push({ num: x + 1 });
        }
        this.pages(tmp).manageClasses();
    }, vmbasket);

    // Observable 3: Request product catalog data from DB if DB Page is changed
    vm.requestCatalogData = ko.computed(function () {
        var Page = this.pageNumber();
        var startID = (Page-1)*(this.pageSize()==="all"?0:this.pageSize());
        this.start(startID);
        var limit = (this.pageSize()==="all"?1000:this.pageSize());
        this.limit(limit);
        getCatalogData("loadCatalog",startID,limit,this.pricerange(),Page, this);
    }, vm);

    // Observable 3: Request basket data from DB if DB Page is changed
    vmbasket.requestBasketData = ko.computed(function () {
        var Page = this.pageNumber();
        var startID = (Page-1)*(this.pageSize()==="all"?0:this.pageSize());
        this.start(startID);
        var limit = (this.pageSize()==="all"?1000:this.pageSize());
        this.limit(limit);
        getBasketData("loadBasket",startID,limit,this.pricerange(),Page, this);
    }, vmbasket);

    // Observable 4: Transfer new data to details panel row is changed
    vm.detailsData = ko.computed(function () {
        setRowData(this.currentRow());
    },vm);
        
    vmbasket.detailsData = ko.computed(function () {
        setRowDataBasket(this.currentRow());
    },vmbasket);
    
    // Bind catalog and basket
    ko.applyBindings(vm, document.getElementById('catalog'));
    ko.applyBindings(vmbasket, document.getElementById('basket'));

    //add initial classes
    vm.manageClasses();
    vmbasket.manageClasses();

    // Observable 5: Populate the prices array
    vm.updatePrices = ko.computed(function () {
        var tmpArr = [];
        var refObj = {};
        tmpArr.push({ pricerange: "Filter by price range ..." , rangesql: "Filter by price range ..."});
        $.each(vm.elements(), function(i, item) {
            var pricerange = item.pricerange;
            var rangesql = item.rangesql;
            var grouptotal = item.grouptotal;
            // If ref obj not has the price, add it
            if (!refObj.hasOwnProperty(pricerange)) {
                var tmpObj = {pricerange: pricerange + ' ('+grouptotal+')', rangesql: rangesql};
                refObj[pricerange] = pricerange;
                refObj[rangesql] = rangesql;
                tmpArr.push(tmpObj);
            }
        });
        vm.prices(tmpArr);
    });
    
    vmbasket.updatePrices = ko.computed(function () {
        var tmpArr = [];
        var refObj = {};
        tmpArr.push({ pricerange: "Filter by price range ..." , rangesql: "Filter by price range ..."});
        $.each(vmbasket.basketelements(), function(i, item) {
            var pricerange = item.pricerange;
            var rangesql = item.rangesql;
            var grouptotal = item.grouptotal;
            // If ref obj not has the price, add it
            if (!refObj.hasOwnProperty(pricerange)) {
                var tmpObj = {pricerange: pricerange + ' ('+grouptotal+')', rangesql: rangesql};
                refObj[pricerange] = pricerange;
                refObj[rangesql] = rangesql;
                tmpArr.push(tmpObj);
            }
        });
        vmbasket.prices(tmpArr);
    });

    // Observable 6: Format stock total amount and reserved
    vm.showStock = ko.computed(function () {
        
    });

    // INIT: Populate the elements array and total nb of elements in DB
    getCatalogData("loadCatalog",vm.start(),vm.limit(),vm.pricerange(),vm.pageNumber(),vm);
    getBasketData("loadBasket",vmbasket.start(),vmbasket.limit(),vm.pricerange(),vmbasket.pageNumber(),vmbasket);
    
    // Make details panel draggable
    $( "#details" ).draggable();
    $( "#basketdetails" ).draggable();
    
    // CATALOG FORM
    // Submit event handler for UPDATE
    var $detailsForm = $('#details-form');
    var $basketForm = $('#basket-form');
    $detailsForm.on('click', function(event) {
        event.preventDefault();
        var t = event.target.id;
        if (t!="update" || $(this).valid() == false) return false;
        $.ajax({
            type: "PUT",
            url: 'api/update.php'+'?qshopCallback=?',
            dataType: 'jsonp',
            jsonp: 'qshopCallback',
            data: $(this).serialize(),
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('load').innerHTML = 'Loading page '+vm.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            success: function(data) {
                $('#response').html(data[0]);
                restoreRowData(data[1], false);
                // Update both views
                getCatalogData("loadCatalog",vm.start(),vm.limit(),vm.pricerange(),vm.pageNumber(), vm);
                getBasketData("loadBasket",vmbasket.start(),vmbasket.limit(),vmbasket.pricerange(),vmbasket.pageNumber(), vmbasket);
                document.getElementById("load").innerHTML = "";
            }
        });
    });
    
    // Submit event handler for NEW
    $detailsForm.on('click', function(event) {
        event.preventDefault();
        var t = event.target.id;
        if (t!="new" || $(this).valid() == false) return false;
        $.ajax({
            type: "POST",
            url: 'api/addproduct.php'+'?qshopCallback=?',
            dataType: 'jsonp',
            jsonp: 'qshopCallback',
            data: $(this).serialize(),
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('load').innerHTML = 'Loading page '+vm.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            success: function(data) {
                $('#response').html(data[0]);
                addRowData(data[1]);
                getCatalogData("loadCatalog",vm.start(),vm.limit(),vm.pricerange(),vm.pageNumber(), vm);
                document.getElementById("load").innerHTML = "";
            }
        });
    });

    // Submit event handler for REMOVE
    $detailsForm.on('click', function(event) {
        event.preventDefault();
        var t = event.target.id;
        if (t!="remove" || $(this).valid() == false) return false;
        $.ajax({
            type: "DELETE",
            url: 'api/remove.php'+'?qshopCallback=?',
            dataType: 'jsonp',
            jsonp: 'qshopCallback',
            data: $(this).serialize(),
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('load').innerHTML = 'Loading page '+vm.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            success: function(data) {
                $('#response').html(data[0]);
                removeRowData(data[1]);
                // Update both views
                getCatalogData("loadCatalog",vm.start(),vm.limit(),vm.pricerange(),vm.pageNumber(), vm);
                getBasketData("loadBasket",vmbasket.start(),vmbasket.limit(),vmbasket.pricerange(),vmbasket.pageNumber(), vmbasket);
                document.getElementById("load").innerHTML = "";
            }
        });
    });

    // Submit event handler for ADD TO CART
    $detailsForm.on('click', function(event) {
        event.preventDefault();
        var t = event.target.id;
        if (t!="addcart" || $(this).valid() == false) return false;
        $.ajax({
            type: "POST",
            url: 'api/addcart.php'+'?qshopCallback=?',
            dataType: 'jsonp',
            jsonp: 'qshopCallback',
            data: $(this).serialize(),
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('load').innerHTML = 'Loading page '+vm.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            success: function(data) {
                $('#response').html(data[0]);
                restoreRowData(data[1], true);
                // Update both views
                getCatalogData("loadCatalog",vm.start(),vm.limit(),vm.pricerange(),vm.pageNumber(), vm, false);
                getBasketData("loadBasket",vmbasket.start(),vmbasket.limit(),vmbasket.pricerange(),vmbasket.pageNumber(), vmbasket, true);
                document.getElementById("load").innerHTML = "";
            }
        });
    });

    // BASKET FORM
    // Submit event handler for UPDATE
    $basketForm.on('click', function(event) {
        event.preventDefault();
        var t = event.target.id;
        if (t!="update" || $(this).valid() == false) return false;
        $.ajax({
            type: "PUT",
            url: 'api/updatecart.php'+'?qshopCallback=?',
            dataType: 'jsonp',
            jsonp: 'qshopCallback',
            data: $(this).serialize(),
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('basketload').innerHTML = 'Loading page '+vmbasket.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            success: function(data) {
                $('#basketresponse').html(data[0]);
                restoreRowData(data[1], false);
                // Update both views
                getCatalogData("loadCatalog",vm.start(),vm.limit(),vm.pricerange(),vm.pageNumber(), vm);
                getBasketData("loadBasket",vmbasket.start(),vmbasket.limit(),vmbasket.pricerange(),vmbasket.pageNumber(), vmbasket);
                document.getElementById("basketload").innerHTML = "";
            }
        });
    });

    // Submit event handler for REMOVE
    $basketForm.on('click', function(event) {
        event.preventDefault();
        var t = event.target.id;
        if (t!="remove" || $(this).valid() == false) return false;
        $.ajax({
            type: "DELETE",
            url: 'api/removecart.php'+'?qshopCallback=?',
            dataType: 'jsonp',
            jsonp: 'qshopCallback',
            data: $(this).serialize(),
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('basketload').innerHTML = 'Loading page '+vmbasket.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            success: function(data) {
                $('#basketresponse').html(data[0]);
                restoreRowData(data[1], false);
                // Update both views
                getCatalogData("loadCatalog",vm.start(),vm.limit(),vm.pricerange(),vm.pageNumber(), vm);
                getBasketData("loadBasket",vmbasket.start(),vmbasket.limit(),vmbasket.pricerange(),vmbasket.pageNumber(), vmbasket);
                document.getElementById("basketload").innerHTML = "";
            }
        });
    });

    // Submit event handler for PURCHASE
    $basketForm.on('click', function(event) {
        event.preventDefault();
        var t = event.target.id;
        var formval = $(this).serialize();
        var obj = {start : vmbasket.start(), limit : vmbasket.limit(), price : vmbasket.pricerange(), search : vmbasket.searchCat()};
        formval = formval+'&'+$.param(obj);
        if (t!="purchase" || $(this).valid() == false) return false;
        $.ajax({
            type: "POST",
            url: 'api/purchaseorder.php'+'?qshopCallback=?',
            dataType: 'jsonp',
            jsonp: 'qshopCallback',
            data: formval,
            // Start loading indication
            beforeSend: function (xhr) {
                xhr.setRequestHeader ("Authorization", btoa(APIKEY));
                document.getElementById('basketload').innerHTML = 'Loading page '+vmbasket.pageNumber()+' from DB ... <img src="images/loading.gif">';
            },
            success: function(data) {
                $('#basketresponse').html(data[0]);
                restoreRowData(data[1], false);
                // Update both views
                getCatalogData("loadCatalog",vm.start(),vm.limit(),vm.pricerange(),vm.pageNumber(), vm);
                getBasketData("loadBasket",vmbasket.start(),vmbasket.limit(),vmbasket.pricerange(),vmbasket.pageNumber(), vmbasket);
                document.getElementById("basketload").innerHTML = "";
            }
        });
    });
    
    // Catalog form validation
    $detailsForm.validate({
        rules: {
           name: "required",
           descr: "required",
           price: {
              required: true,
              number: true,
              min: 0.00
           },
           amount: {
              required: true,
              number: true,
              min: 0
           },
           messages: {
              name: "<span>Tuotenimi puuttuu!</span>",
              descr: "<span>Tuotekuvaus puuttuu!</span>",
              price: {
                 required: "<span>Hinta puuttuu!</span>",
                 number: "<span>Hinnan on oltava numeerinen!</span>",
                 min: "<span>Hinnan on oltava positiivinen!</span>"
              },
              amount: {
                 required: "<span>Määrä puuttuu!</span>",
                 number: "<span>Määrän on oltava numeerinen!</span>",
                 min: "<span>Määrän on oltava positiivinen!</span>"
              },
          }
        }
    });

    // Cart form validation
    $basketForm.validate({
        rules: {
           name: "required",
           descr: "required",
           price: {
              required: true,
              number: true,
              min: 0.00
           },
           amount: {
              required: true,
              number: true,
              min: 0
           },
           messages: {
              name: "<span>Tuotenimi puuttuu!</span>",
              descr: "<span>Tuotekuvaus puuttuu!</span>",
              price: {
                 required: "<span>Hinta puuttuu!</span>",
                 number: "<span>Hinnan on oltava numeerinen!</span>",
                 min: "<span>Hinnan on oltava positiivinen!</span>"
              },
              amount: {
                 required: "<span>Tilattava määrä puuttuu!</span>",
                 number: "<span>Tilattavan määrän on oltava numeerinen!</span>",
                 min: "<span>Määrän on oltava positiivinen!</span>"
              },
           }
        }
    });

});