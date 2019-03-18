<?php
// Functions to do the base web services needed
// Note that all needed web services are sent from this day directory
// The functions here should throw up to their callers, just like
// the functions in model.
//
// Post day number to server
// Returns if successful, or throws if not



function place_order_server1($db,$app_path,$order){
    
    
    
   // echo 'app_path: ' . $app_path . '<br>';
$spot = strpos($app_path, 'pizza2');
$part = substr($app_path, 0, $spot);
$base_url = $_SERVER['SERVER_NAME'] . $part . 'proj2_server/rest';
//echo 'base_url: ' . $base_url . '<br>';

// Instantiate Guzzle HTTP client
$httpClient = new \GuzzleHttp\Client();


//echo  '<br>Now POST it back, but on second run, expect to see an error unless you change productCode in index.php';

$url = 'http://' . $base_url . '/orders/';

// works only once per each value



error_log('...... restclient: POST order');
try {
    // Guzzle does the json_encode for us--
    $response4 = $httpClient->request('POST', $url, ['json' => $order]);
    $location4 = $response4->getHeader('Location');
    $p=$location4[0];
    $f=explode('/',$p);
   
    $status4 = $response4->getStatusCode();
} catch (Exception $e) {
  include '../errors/error.php'; 
}
     return $f[9];
    
}



function get_server_orders($app_path)
{
//echo 'app_path: ' . $app_path . '<br>';
$spot = strpos($app_path, 'pizza2');
$part = substr($app_path, 0, $spot);
$base_url = $_SERVER['SERVER_NAME'] . $part . 'proj2_server/rest';
//echo 'base_url: ' . $base_url . '<br>';


$httpClient = new \GuzzleHttp\Client();
$url = 'http://' . $base_url . '/orders/';
//echo '<br>GET all orders ' . $url;
error_log('...... restclient: GET orders');
try {
    $response3 = $httpClient->get($url);
    $ordJson = $response3->getBody()->getContents();  // as StreamInterface, then string
   // echo '<br> Returned result of GET of all orders<br>';
   // print_r($ordJson);
   // echo '<br> After json_decode:<br>';
    $order = json_decode($ordJson, true);
   // print_r($order);
} catch (Exception $e) {
    include '../../errors/error.php'; 
}
return $order;
}




function post_day($db,$app_path)
{
    $day= get_current_day($db,$app_path);
    $d=$day;
    //echo $day;
    $spot = strpos($app_path, 'pizza2');
$part = substr($app_path, 0, $spot);
$base_url = $_SERVER['SERVER_NAME'] . $part . 'proj2_server/rest';
//echo 'base_url: ' . $base_url . '<br>';


$httpClient = new \GuzzleHttp\Client();
    
$url = 'http://' . $base_url . '/day/';
//echo 'POST day = 3 to ' . $url . '<br>';
$fp = fopen('php://temp', 'r+');   // for more debug info if desired
error_log('...... restclient: POST day = 3 to ' . $url);
try {
    $response = $httpClient->request('POST', $url, ['json' => intval($day)]);
    $status = $response->getStatusCode();
   // fseek($fp, 0);
   // var_dump(stream_get_contents($fp));  // uncomment for additional debug output

} catch (GuzzleHttp\Exception $e) {
    $status = 'POST failed, error = ' . $e;
    error_log($status);
    include '../errors/error.php';  // Note new error.echophp code that handles Exceptions
}
//echo 'Post of day result: ' .  $status;
    
    
    
}





function post_day_usrchoice($db,$app_path,$day)
{
    
    $d=$day;
    //echo $day;
    $spot = strpos($app_path, 'pizza2');
$part = substr($app_path, 0, $spot);
$base_url = $_SERVER['SERVER_NAME'] . $part . 'proj2_server/rest';
//echo 'base_url: ' . $base_url . '<br>';


$httpClient = new \GuzzleHttp\Client();
    
$url = 'http://' . $base_url . '/day/';
//echo 'POST day = 3 to ' . $url . '<br>';
$fp = fopen('php://temp', 'r+');   // for more debug info if desired
error_log('...... restclient: POST day = 3 to ' . $url);
try {
    $response = $httpClient->request('POST', $url, ['json' => intval($day)]);
    $status = $response->getStatusCode();
   // fseek($fp, 0);
   // var_dump(stream_get_contents($fp));  // uncomment for additional debug output

} catch (GuzzleHttp\Exception $e) {
    $status = 'POST failed, error = ' . $e;
    error_log($status);
    include '../errors/error.php';  // Note new error.echophp code that handles Exceptions
}
//echo 'Post of day result: ' .  $status;
    
    
    
}




function get_server_day($db,$app_path)
{
    //echo 'app_path: ' . $app_path . '<br>';
$spot = strpos($app_path, 'pizza2');
$part = substr($app_path, 0, $spot);
$base_url = $_SERVER['SERVER_NAME'] . $part . 'proj2_server/rest';
//echo 'base_url: ' . $base_url . '<br>';

// Instantiate Guzzle HTTP client
$httpClient = new \GuzzleHttp\Client();
$url = 'http://' . $base_url . '/day/';
$fp = fopen('php://temp', 'r+'); 
//echo '<br>GET of day to ' . $url;
error_log('...... restclient: GET day');
try {
    $response2 = $httpClient->get($url,['debug' => $fp]);
    //echo '<br>Back from GET: day = ' . $response2->getBody() . ' (wrong until server coded right)';
} catch (Exception $e) {
    include '../errors/error.php'; 
}
return $response2->getBody();
}


// TODO: POST order and get back location (i.e., get new id), get all orders 
// in server and/or get a specific order by orderid

