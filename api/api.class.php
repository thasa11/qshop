<?php
/**
 * Abstract class API 
 * - handles parsing of the request URI (depending on request method)
 * - parse endpoint and arguments of the URI
 * - cleaning input parameters
 * - error handling
 * - returns the HTTP response (with data or error)
 */
abstract class API
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
    protected $file = Null;
    
    /**
     * API version
     */
    protected $version = '1.0'; 

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        $this->args = explode('/', rtrim($request, '/'));
        //print_r($this->args);
        $this->endpoint = array_shift($this->args);
        $this->endpoint = explode('.', $this->endpoint)[0];
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
        // Set API version
        $_REQUEST['apiversion'] = $this->version;
        
        // Detect and handle HTTP method
        switch($this->method) {
        case 'DELETE':
            $this->request = $this->_cleanInputs($_GET);
            // Parse data from PUT request to an array named PUT, and merge it to REQUEST
            parse_str(file_get_contents("php://input"), $_DELETE);            
            foreach ($_DELETE as $key => $value)
                {
                    unset($_DELETE[$key]);            
                    $_DELETE[str_replace('amp;', '', $key)] = $value;
                }            
            $_REQUEST = array_merge($_REQUEST, $_DELETE);
            break;
        case 'POST':
            $this->request = $this->_cleanInputs($_POST);
            break;
        case 'GET':
            $this->request = $this->_cleanInputs($_GET);
            break;
        case 'PUT':
            $this->request = $this->_cleanInputs($_GET);
            // Parse data from PUT request to an array named PUT, and merge it to REQUEST
            parse_str(file_get_contents("php://input"), $_PUT);            
            foreach ($_PUT as $key => $value)
                {
                    unset($_PUT[$key]);            
                    $_PUT[str_replace('amp;', '', $key)] = $value;
                }            
            $_REQUEST = array_merge($_REQUEST, $_PUT);
            break;
        default:
            $this->_response('Invalid Method', 405);
            break;
        }
    }
    /**
     * Either return json response from endpoint, or error header
     */
    public function processAPI() {
        if (method_exists($this, $this->endpoint)) {
            return $this->{$this->endpoint}($this->args);
        } else {
            return $this->_response("No Endpoint: $this->endpoint", 404);            
        }
    }
    
    private function _response($data, $status = 200) {
      header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
      return $data;
    }

    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }

}
/**
 * APIKEY authentication class
 * Users api key must be present in all requests, and it must match the key in database
 */
class ApiKey {

    private $myArray = array();
    private $tempArray = array();
    private $key;
    private $userid = 0;
    private $query;
    private $mysqli;
    
    public function __construct($u) {
        $this->userid = $u;
        $this->mysqli = connect_db::init(); 
    }
    
    /**
     * function authorize
     * Api key must be present in all requests, and it must match the key in database.
     * @return true if API KEY valid, false otherwise 
     */
    private function authorize($userid){
        global $mysqli;
        $headers = apache_request_headers();
        if (isset($headers["Authorization"])){
            $this->key = base64_decode($headers["Authorization"]);
        } else return false;
        $this->query = "SELECT apikey FROM users WHERE userid =".$userid.";";
        //echo $query;
        if ($result = $this->mysqli->query($this->query)){
            if ($row = $result->fetch_object()){
                //echo $row->apikey." ".$key;
                if ($row->apikey == $this->key) return true;
                else return false;
            } else return false;
        } else return false;
    }
    public function verifyKey(){
        if (!$this->authorize($this->userid)) {
            $this->tempArray['info'] = "<span>API key missing or mismatch!</span></br>";
            $this->tempArray['lkm'] = 0;
            if (!isset($_REQUEST['qshopCallback'])){
                $_REQUEST['qshopCallback'] = '';
                $this->tempArray['info'] .= "<span>Callback mismatch!</span></br>";            
            }   
            array_push($this->myArray, $this->tempArray);
            array_push($this->myArray, array('authstatus'=>false));
        } else {
            array_push($this->myArray, array('authstatus'=>true));
        }
        return $this->myArray;
    }    

}
/**
  * Database connection class
  */
class connect_db{
    private static $sarr = array();
    private static $mysqli_conn;
    
    static function init(){ 
        self::$mysqli_conn=new mysqli('localhost','root','tetramou','qvantel');  
        self::$mysqli_conn->set_charset("utf8");
        self::$mysqli_conn->query("SET NAMES utf8");
        /* check connection */
        if (self::$mysqli_conn->connect_errno) {
        self::$sarr[0] = "<span>Connect to DB failed: ".self::$mysqli_conn->connect_errno."</span>";
            exit();
        }
        return self::$mysqli_conn;
    }
}
/**
 * QShop API class implements the concrete endpoints of the abstract API class
 * This is the real API class handling all endpoints
 */
class QShopAPI extends API
{
    protected $User;
    protected $authstatus = array('authstatus'=>false);
    protected $retarr = array();
    // APIKEY verification in constructor
    public function __construct($request, $origin, $user) {
        parent::__construct($request);
        // user should become e.g. from session user
        $this->User = $user;
        $apikey = new ApiKey($this->User);
        $this->retarr=$apikey->verifyKey();
        $this->authstatus = array_pop($this->retarr);
    }

    /**
     * Endpoint for load catalog (GET)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function load($args=null) {
        if ($this->method == 'GET' && $this->authstatus['authstatus']==true) {
            require_once 'load.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
    /**
     * Endpoint for load shopping cart (GET)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function loadcart($args=null) {
        if ($this->method == 'GET' && $this->authstatus['authstatus']==true) {
            require_once 'loadcart.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
    /**
     * Endpoint for adding products to catalog (POST)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function addproduct($args=null) {
        if ($this->method == 'POST' && $this->authstatus['authstatus']==true) {
            require_once 'addproduct.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
    /**
     * Endpoint for adding products to cart (POST)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function addcart($args=null) {
        if ($this->method == 'POST' && $this->authstatus['authstatus']==true) {
            require_once 'addcart.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
    /**
     * Endpoint for updating products in cart (PUT)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function updatecart($args=null) {
        if ($this->method == 'PUT' && $this->authstatus['authstatus']==true) {
            require_once 'updatecart.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
    /**
     * Endpoint for updating products in catalog (PUT)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function update($args=null) {
        if ($this->method == 'PUT' && $this->authstatus['authstatus']==true) {
            require_once 'update.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
    /**
     * Endpoint for removing products in catalog (DELETE)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function remove($args=null) {
        if ($this->method == 'DELETE' && $this->authstatus['authstatus']==true) {
            require_once 'remove.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
    /**
     * Endpoint for removing products in cart (DELETE)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function removecart($args=null) {
        if ($this->method == 'DELETE' && $this->authstatus['authstatus']==true) {
            require_once 'removecart.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
    /**
     * Endpoint for adding purchaseorder from cart (POST)
     * Return endpoint response if user is authorized
     * If not authorized return status array with error message
     */
     protected function purchaseorder($args=null) {
        if ($this->method == 'POST' && $this->authstatus['authstatus']==true) {
            require_once 'purchaseorder.php';
        } else {
            header('Content-Type: application/json');
            return $_REQUEST['qshopCallback'] . '(' . json_encode($this->retarr) . ')';
        }
     }
 }

?>