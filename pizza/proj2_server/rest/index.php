<?php
$request_uri = $_SERVER['REQUEST_URI'];
$doc_root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
$dirs = explode(DIRECTORY_SEPARATOR, __DIR__);
array_pop($dirs); // remove last element
$project_root = implode('/', $dirs) . '/';
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '0'); // displayed errors would mess up response
ini_set('log_errors', 1);
// the following file needs to exist, be accessible to apache
// and writable (chmod 777 php-server-errors.log)
ini_set('error_log', $project_root . 'php-server-errors.log');
set_include_path($project_root);
// app_path is the part of $project_root past $doc_root
$app_path = substr($project_root, strlen($doc_root));
// project uri is the part of $request_uri past $app_path, not counting its last /
$project_uri = substr($request_uri, strlen($app_path) - 1);
$parts = explode('/', $project_uri);
// like  /rest/product/1 ;
//     0    1     2    3    

// tell database.php not to send HTML error page
$in_webservice_code = true;
require_once('model/database.php');
require_once('model/product_db.php');
require_once('model/systemDay_db.php');
require_once('model/order_db.php');
$server = $_SERVER['HTTP_HOST'];
$method = $_SERVER['REQUEST_METHOD'];
$proto = isset($_SERVER['HTTPS']) ? 'https:' : 'http:';
$url = $proto . '//' . $server . $request_uri;
$resource = trim($parts[2]);
$id = $parts[3];
error_log('starting REST server request, method=' . $method . ', uri = ...'. $project_uri);
error_log('haha--'.$id.'   res--'.$resource.'  url--'.$url.'   server--'.$server.' $method--   '.$method);
error_log('$app_path===='.$app_path.'  $project_uri--,'.$project_uri.'    $parts.$parts');
switch ($resource) {
    // Access the specified product
    case 'products':
        error_log('request at case product');
        switch ($method) {
            case 'GET':
                handle_get_product($id);
                break;
            case 'POST':
                handle_post_product($url);
                break;
            default:
                $error_message = 'bad HTTP method : ' . $method;
                include_once('errors/server_error.php');
                server_error(405, $error_message);
                break;
        }
        break;
    case 'day':
        error_log('request at case day');
        switch ($method) {
            case 'GET':
                //$day = 6;
                // TODO: get current day from DB    
                handle_get_day();///$day
                break;
            case 'POST':
                // TODO: set new day in DB, reinitialize orders if 0, set day 
                $new_day = handle_post_day();         
                break;
            default:
                $error_message = 'bad HTTP method : ' . $method;
                include_once('errors/server_error.php');
                server_error(405, $error_message);
                break;
        }
        break;
        case 'orders':
        error_log('request at case orders = '.$id);
        switch ($method) {
            case 'GET':
                //$day = 6;
                // TODO: get current day from DB   
                if($id!=NULL)
                {
                handle_get_orders($id);
                }
                else
                {
                handle_all_get_orders();
                }
                break;
            case 'POST':
                // TODO: set new day in DB, reinitialize orders if 0, set day 
                $new_day = handle_post_orders($url);         
                break;
            default:
                $error_message = 'bad HTTP method : ' . $method;
                include_once('errors/server_error.php');
                server_error(405, $error_message);
                break;
        }
        break;
    default:
        $error_message = 'Unknown REST resource: ' . $resource;
        include_once('errors/server_error.php');
        server_error(400, $error_message);  // blame client (but might be server's fault)
        break;
}

function handle_get_product($product_id) {
    try {
        if (!(is_numeric($product_id) && $product_id > 0)) {
           $error_message = 'Bad product_id in handle_get_product: ' . $product_id;
           include_once('errors/server_error.php');
           server_error(400, $error_message);  // bad client URL
           return; 
        }
        $product = get_product($product_id); 
        if (empty($product)) {  // no data found
            $error_message = 'failed to find product';
            include_once('errors/server_error.php');
            server_error(404, $error_message);
            return; 
        }
        $data = json_encode($product);
        error_log('in handle_get_product, $product = ' . print_r($product, true));
        if ($data === FALSE) {  // failure of json_encode
            $error_message = 'JSON encode error' . json_last_error_msg();
            include_once('errors/server_error.php');
            server_error(500, $error_message);  // server problem
            return; 
        }        
    } catch (Exception $e) {
        $error_message = 'exception trying to get product' . $e->getMessage();
        include_once('errors/server_error.php');
        server_error(500, $error_message);  // server problem
        return; 
    }
    echo $data;
}

function handle_all_get_orders()
{
    
       try {
       
        $day=get_day();
        $result = get_all_order_new($day); 
        if (empty($result)) {  // no data found
         /*   $error_message = 'failed to find product';
            include_once('errors/server_error.php');
            server_error(404, $error_message);*/
            return $result; 
        }
        $data = json_encode($result);
        //error_log('in handle_get_order, $order = ' . print_r($result, true));
        if ($data === FALSE) {  // failure of json_encode
            $error_message = 'JSON encode error' . json_last_error_msg();
            include_once('errors/server_error.php');
            server_error(500, $error_message);  // server problem
            return; 
        }        
    } catch (Exception $e) {
        $error_message = 'exception trying to get product' . $e->getMessage();
        include_once('errors/server_error.php');
        server_error(500, $error_message);  // server problem
        return; 
    }
    echo $data;
    
    
     
    
    
    
}
function handle_get_orders($id){
      try {
        if (!(is_numeric($id) && $id > 0)) {
           $error_message = 'Bad order_id in handle_get_order: ' . $id;
           include_once('errors/server_error.php');
           server_error(400, $error_message);  // bad client URL
           return; 
        }
        $day=get_day();
        $result = get_order_new($id,$day); 
        if (empty($result)) {  // no data found
            $error_message = 'failed to find product';
            include_once('errors/server_error.php');
            server_error(404, $error_message);
            return; 
        }
        $data = json_encode($result);
        error_log('in handle_get_order, $order = ' . print_r($result, true));
        if ($data === FALSE) {  // failure of json_encode
            $error_message = 'JSON encode error' . json_last_error_msg();
            include_once('errors/server_error.php');
            server_error(500, $error_message);  // server problem
            return; 
        }        
    } catch (Exception $e) {
        $error_message = 'exception trying to get product' . $e->getMessage();
        include_once('errors/server_error.php');
        server_error(500, $error_message);  // server problem
        return; 
    }
    echo $data;
    
    
    
    
}
function handle_post_product($url) {
    $bodyJson = file_get_contents('php://input');
    error_log('Server saw post data' . $bodyJson);
    $body = json_decode($bodyJson, true);
    if ($body === NULL) {  // failure of json_decode 
        $error_message = 'JSON decode error' . json_last_error_msg();
        include_once('errors/server_error.php');
        server_error(400, $error_message);  // client problem: sent bad JSON
        return;
    }
    try {
        $product_id = add_product($body['categoryID'], $body['productCode'], $body['productName'], $body['description'], $body['listPrice'], $body['discountPercent']);
        // return new URI in Location header
        $locHeader = 'Location: ' . $url . $product_id;
        header($locHeader, true, 201);  // needs 3 args to set code 201 (Created)
        error_log('hi from handle_post_product, header = ' . $locHeader);
    } catch (Exception $e) {
        $error_message = 'Insert failed: ' . $e->getMessage();
        include_once('errors/server_error.php');
        server_error(500, $error_message);  // probably server error
    }
}
function handle_post_orders($url)
{
    $bodyJson = file_get_contents('php://input');
    error_log('Server saw post data' . $bodyJson);
    $body = json_decode($bodyJson, true);
    error_log('*****Server saw post data' . print_r($body,true));
    if ($body === NULL) {  // failure of json_decode 
        $error_message = 'JSON decode error' . json_last_error_msg();
        include_once('errors/server_error.php');
        server_error(400, $error_message);  // client problem: sent bad JSON
        return;
    }//error_log('item body = ' . print_r($body['items'],true));
    try {
        
        $day=get_day();
        if(($day%2)==0)
        {
        error_log('day = ' . $day);
        $orid = add_order_new($body['customerID'],$day+2);

        }
        else
        {
         error_log('day = ' . $day);
        $orid = add_order_new($body['customerID'],$day+1);
        }
        
        foreach ($body['items'] as $value) {
            error_log('item body = ' . print_r($value,true));
           add_orders_item_new($value,$orid); 
        }
        //error_log('hi from handle_post_product, header = ' . $locHeader);
        // return new URI in Location header
        $locHeader = 'Location: ' . $url . $product_id;
        header($locHeader, true, 201);  // needs 3 args to set code 201 (Created)
        error_log('hi from handle_post_product, header = ' . $locHeader);
    } catch (Exception $e) {
        $error_message = 'Insert failed: ' . $e->getMessage();
        include_once('errors/server_error.php');
        server_error(500, $error_message);  // probably server error
    }    
    
    
    
}



function handle_get_day() {
    $day=get_day();
    echo $day;
    //$data = json_encode($day);
    //error_log('rest server in handle_get_day, day = ' . $day);
    //echo $data;
}

function handle_post_day() {
    error_log('rest server in handle_post_day');
    $day = file_get_contents('php://input'); 
    error_log('rest server in handle_post_day'.$day);
    if($day==0)
    {
       reinitialize_orders();
       add_day(1);
    }else {
        
 
     add_day($day);
 }// just a digit string
    if (!(is_numeric($day) && $day >= 0)) {
        $error_message = 'Bad day number in handle_post_day: ' . $day;
        include_once('errors/server_error.php');
        server_error(400, $error_message);  // bad client data
        return;
    }
    error_log('Server saw POSTed day = ' . $day);
    return $day;
}
