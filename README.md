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
API key is demanded in the cross-site server requests. API key can be tied to the domain name to make them as a pair.

There is now an abstract API class that implements RESTful API using appropriate HTTP methods.
CORS requests are allowed, and site using this API must have an API key tied to the domain name.
API key must be present in all requests, and it is verified before any request processing. 
The concrete QSHop API class implements the needed endpoints to process the requests.

For mapping of the URI to suitable API requests (endpoint, verb, arguments), there are rewrite rules written for Apache to do
proper URL rewriting. All API requests are forwarded to an API controller api/api.php, which routes the requests to concrete REST
API handler class.