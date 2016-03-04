# qshop
QShop, a web shop application demonstration
Demonstrates JSON serializable API to product catalog and shopping basket for a web shop.
Backend techniques used:
- PHP 5.4.4, MySQL database
- Apache 2.4, Url rewrite engine
- JSON serialized server responses

Frontend techiques:
- jQuery UI 1.10.2, Ajax
- Knockout 3.4.0Model-View-View Model (MVVM) pattern
- JSONP to allow cross-site server requests

UI shows 2 views (viewmodels): 1 - product catalog and 2- shopping basket. Users can add/remove/edit products in the catalog,
and add products in the shopping basket. In the basket view, users can edit amount and remove products, and finally make a purchase order.
In both views it is possible to search and filter products by search string, and by procing category. It is also possible to sort products
by various product properties (amount, price, name, etc). Products are categorized by predefined price groups, and viewed products are 
sorted according to pricing categories. Products share a common stock, and basket reservations are shown as reserved amounts.

The application has 2 responsive views, that also communicate with each other, e.g. if product is added to cart, both the cart and catalog views
are updated.

QSHop API customers are handed the API documentation which describes available resources, actions, and descriptors.
API key is demanded in the cross-site server requests.

I Tried to make the API RESTful, but I found out I do not know how to configure Apache rewrite rules so that
operation is well-defined. Also, I noted that I do not know the principles of REST API design well enough.
