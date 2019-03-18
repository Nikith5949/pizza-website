<?php
require('../../util/main.php');
require('../../model/database.php');
require('../../model/day_db.php');
require('../../model/initial.php');
require('../../model/inventory_db.php');
require('../../model/undelivered_db.php');
require('web_services.php');
require('day_helpers.php');
require_once('../../util/main.php');
// use Composer autoloader, so we don't have to require Guzzle PHP files
require '../../vendor/autoload.php';
// Note that you don't have to put all your code in this file.
// You can use another file day_helpers.php to hold helper functions
// and call them from here.

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'list';
    }
}
 $current_day = get_current_day($db);
       // print_r($inven);
         
         $inven=get_inventory($db);
        $und=get_undelivered($db);
                $server_orders=get_server_orders($app_path);
 $todays_orders = get_orders_for_day($db, $current_day);    
if ($action == 'list') {
    try {
        // TODO:///////////////////////////////////////done
        // Load variables for displayed info on supplies on order and inventory
        

        //get_server_orders($app_path);
        //universal($db,$app_path);
        //post_day($db,$app_path);
    } catch (Exception $e) {
        include('../../errors/error.php');
        exit();
    }
    include('day_list.php');
} else if ($action == 'next_day') {
    try {
        finish_orders_for_day($db, $current_day);
        increment_day($db);
        $current_day++;
        post_day($db,$app_path);
        //echo get_server_day($db,$app_path);
        universal($db,$app_path);
             if(150>prev_inventory($db,'cheese')||150>prev_inventory($db,'flour'))
    {//echo"11111111";
   updateserver_and_undelivered($db,$app_path);
  
    }
      
    } catch (Exception $e) {
        include('../../errors/error.php');
        exit();
    }
    universal($db,$app_path);
        $inven=get_inventory($db);
        $und=get_undelivered($db);
        $server_orders=get_server_orders($app_path);
        $todays_orders = get_orders_for_day($db, $current_day);
    // TODO: without putting a huge amount of code here: 
    //   see day_helpers.php for some starter code, add other functions there//done
    // POST the new day number to the server by calling post_day in web_services.php//done
    // Get the undelivered orders from pizza2's database//done
    // Get the supply order status from the server by calling into web_services.php
    // Determine new deliveries by analyzing undelivered orders and server status info.//done
    // Add any newly delivered order amounts to inventory//done
    // Remove processed orders from undelivered orders table//done

    // Place a new supply order if necessary, via web_services.php
    // Add any new supply order to undelivered orders table
    // Load variables for displayed info on supplies on order and inventory//done
   
    // Avoiding redirect here for easier debugging: set up needed variables for day_list
    //$todays_orders = array(); // new day: no customer orders yet
    //get_server_orders();
    include('day_list.php');
} else if ($action == 'initial_db') {
    try {
        initial_db($db);
        // TODO: 
        post_day_usrchoice($db,$app_path,0);
        $inven=get_inventory($db);
        // POST day 0 to the server. //done
        // Get the current inventory info//done
      updateserver_and_undelivered($db,$app_path);
       
       universal($db,$app_path);
       
        // Place new supply order as required by same algorithm as above
        // add order to undelivered orders table
        header("Location: .");
    } catch (Exception $e) {
        include ('../../errors/error.php');
        exit();
    } 
}
?>