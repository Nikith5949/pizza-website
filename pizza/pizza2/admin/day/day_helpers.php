<?php

// Use $server_orders vs. $undelivered_orders to find newly delivered orders
// Credit their newly delivered orders to inventory and delete such orders from 
// the undelivered_orders table
// $server_orders: array of orders from server
// $undelivered orders: array of orders from undelivered orders table
// Client-side REST requests: example code for pizza2 project
//require_once('../../util/main.php');
// use Composer autoloader, so we don't have to require Guzzle PHP files
//require '../../vendor/autoload.php';
// So drop "pizza2" from $app_path, add /proj2_server/rest
require_once('../../model/undelivered_db.php');
require_once('../../model/inventory_db.php');
require_once('../../model/day_db.php');
require('../../model/database.php');
//require('web_services.php');
function universal($db,$app_path)
{
    post_day($db,$app_path);
$r=get_undelivered($db);
$r1=get_server_orders($app_path);

//print_r($r);
//echo "\n******** ";
//print_r($r1);
if(count($r1)>0)
{
record_deliveries($db, $r1, $r);
}


///////call function
}


//create a function



function updateserver_and_undelivered($db,$app_path)
{
    
    $che=prev_inventory($db,'cheese');
    $flo=prev_inventory($db,'flour');
    //echo $flo;echo "**". $che;
     $order['customerID'] = 1; 
    if($flo<150)
    {   $item0['productID']=11;
$item0['quantity']=100;         }
else {
     $item0['productID']=11;
$item0['quantity']=0;  
}
if($che<150)
    {   $item1['productID']=12;
$item1['quantity']=150-$che;         }
else {
     $item1['productID']=12;
$item1['quantity']=0;  
}
   




$items=array($item0,$item1);
$order['items']= $items;

    place_order_server1($db,$app_path,$order);
     $server_orders=get_server_orders($app_path);
     $l=count($server_orders)-1;
     insert_undelivered($db, $server_orders[$l]['orderID'],$item0['quantity'],$item1['quantity']);
     //print_r($server_orders);
}







function record_deliveries($db, $server_orders, $undelivered_orders) {
    $delivered_orders = array();  // build set of delivered orders
    for ($i = 0; $i < count($server_orders); $i++) {
        $orderid = $server_orders[$i]['orderID'];
        $delivered = $server_orders[$i]['delivered'];
        if ($delivered) {
            $delivered_orders[$orderid] = $server_orders[$i];  // remember order by id
        }
    }//print_r($delivered_orders);
    error_log('server orders: ' . print_r($server_orders, true));
    error_log('delivered: ' . print_r($delivered_orders, true));
    //print_r($undelivered_orders);
    // match delivered server order with previously undelivered order
    for ($j = 0; $j < count($undelivered_orders); $j++) {
        //echo $j;
        $orderID = $undelivered_orders[$j]['orderID'];
        error_log('looking at undel order ' . print_r($undelivered_orders[$j], true));
        if (array_key_exists($orderID, $delivered_orders)) {
            error_log("found newly delivered order $orderID");
            $order = $delivered_orders[$orderID];  // the full order info
            //print_r($order);
            // TODO//////////////////////////////////////////////done
            //foreach ($order as $value) {
                
            //print_r($order['orderID']);
             delete_undelivered($db,$order['orderID']);
             //print_r($server_orders);
             $items= $delivered_orders[$order['orderID']]['items'];
             //print_r($items);
             for ($l=0;$l<count($items);$l++) {
                 if($items[$l]['productID']==11)
                 {//echo "999999";
                 //print_r($name);
                 //echo "*****";
                 //print_r($quan);
                 update_inventory($db,"flour",$items[$l]['quantity']);
             }
             else if($items[$l]['productID']==12)
             {
                 update_inventory($db,"cheese",$items[$l]['quantity']);
             }
             
                 }
            //}
            // delete $orderID from undelivered orders table//////done
            // get the quantities of flour and cheese in this order//done
            // and add them to the inventory table//done
        }
    }
}
// Put other helpers here, to keep only top-level code in index.php