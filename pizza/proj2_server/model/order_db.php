<?php

// This function calculates a shipping charge of $5 per item
// but it only charges shipping for the first 5 items
function shipping_cost() {
    $item_count = cart_item_count();
    $item_shipping = 5;   // $5 per item
    if ($item_count > 5) {
        $shipping_cost = $item_shipping * 5;
    } else {
        $shipping_cost = $item_shipping * $item_count;
    }
    return $shipping_cost;
}

// This function calcualtes the sales tax,
// but only for orders in California (CA)
function tax_amount($subtotal) {
    $shipping_address = get_address($_SESSION['user']['shipAddressID']);
    $state = $shipping_address['state'];
    $state = strtoupper($state);
    switch ($state) {
        case 'CA': $tax_rate = 0.09; break;
        default: $tax_rate = 0; break;
    }
    return round($subtotal * $tax_rate, 2);
}

function card_name($card_type) {
    switch($card_type){
        case 1: 
           return 'MasterCard';
        case 2: 
            return 'Visa';
        case 3: 
            return 'Discover';
        case 4:
            return 'American Express';
        default:
            return 'Unknown Card Type';
    }
}
// renamed for project 2 to make clear it uses notion of current user
// that does not apply in the web service case
// For project 2: note add_order at end of file that doesn't use a current user
function add_order_for_current_user($card_type, $card_number, $card_cvv, $card_expires) {
    global $db;
    $customer_id = $_SESSION['user']['customerID'];
    $billing_id = $_SESSION['user']['billingAddressID'];
    $shipping_id = $_SESSION['user']['shipAddressID'];
    $shipping_cost = shipping_cost();
    $tax = tax_amount(cart_subtotal());
    $order_date = date("Y-m-d H:i:s");

    $query = '
         INSERT INTO orders (customerID, orderDate, shipAmount, taxAmount,
                             shipAddressID, cardType, cardNumber,
                             cardExpires, billingAddressID)
         VALUES (:customer_id, :order_date, :ship_amount, :tax_amount,
                 :shipping_id, :card_type, :card_number,
                 :card_expires, :billing_id)';
    $statement = $db->prepare($query);
    $statement->bindValue(':customer_id', $customer_id);
    $statement->bindValue(':order_date', $order_date);
    $statement->bindValue(':ship_amount', $shipping_cost);
    $statement->bindValue(':tax_amount', $tax);
    $statement->bindValue(':shipping_id', $shipping_id);
    $statement->bindValue(':card_type', $card_type);
    $statement->bindValue(':card_number', $card_number);
    $statement->bindValue(':card_expires', $card_expires);
    $statement->bindValue(':billing_id', $billing_id);
    $statement->execute();
    $order_id = $db->lastInsertId();
    $statement->closeCursor();
    return $order_id;
}









function add_order_item($order_id, $product_id,
                        $item_price, $discount, $quantity) {
    global $db;
    $query = '
        INSERT INTO OrderItems (orderID, productID, itemPrice,
                                discountAmount, quantity)
        VALUES (:order_id, :product_id, :item_price, :discount, :quantity)';
    $statement = $db->prepare($query);
    $statement->bindValue(':order_id', $order_id);
    $statement->bindValue(':product_id', $product_id);
    $statement->bindValue(':item_price', $item_price);
    $statement->bindValue(':discount', $discount);
    $statement->bindValue(':quantity', $quantity);
    $statement->execute();
    $statement->closeCursor();
}

function get_order($order_id) {
    global $db;
    $query = 'SELECT * FROM orders WHERE orderID = :order_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':order_id', $order_id);
    $statement->execute();
    $order = $statement->fetch();
    $statement->closeCursor();
    return $order;
}

function get_order_new($order_id,$day) {
    global $db;
    $query = 'SELECT * FROM orders WHERE orderID = :order_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':order_id', $order_id);
    $statement->execute();
    $order = $statement->fetch();
    $statement->closeCursor();
    $order_items=get_order_items($order_id);
    if($order==NULL)
    {return NULL;}
    $items;
    $c=0;
    foreach ($order_items as $value) {
      
       $items[$c]['productID']= $value['productID']; 
       $items[$c]['quantity']=  $value['quantity']; 
       $c+=1;
    }
    
    if($order['deliveryDay']<=$day)
    {
        
    
    $finalresult=array(‘customerID’=> $order[0]['customerID'],'orderID'=> $order_id, 'delivered'=>true,'items' => $items);
        }
        else
        {
            $finalresult=array(‘customerID’=> $order[0]['customerID'],'orderID'=> $order_id, 'delivered'=>false,'items' => $items);
        }
    // error_log('in handle_get_product, $order in get order new = ***' . print_r($finalresult, true));
    return $finalresult;
}
/////////////////////////////
function get_all_order_new($day)
{
     global $db;
    $query = 'SELECT * FROM orders';
    $statement = $db->prepare($query);
    $statement->bindValue(':order_id', $order_id);
    $statement->execute();
    $order = $statement->fetchAll();
    $statement->closeCursor();///////////////////////////////////////
    
    $finalorders=array();
    $c=0;
    foreach ($order as $valueo) {
        //error_log('order = ' . print_r($order, true)); 
 $finalorders[$c]=get_order_new($valueo['orderID'],$day);    
 $c+=1;
    }
 //error_log('final order = ' . print_r($finalorders, true));
    //error_log('in handle_get_product, $order in get order new = ***' . print_r($finalorders, true));
    
    return $finalorders;
    
    
    
    
}
function add_order_new($customer_id,$del_day) {
    global $db;


    $query = '
         INSERT INTO orders (customerID,deliveryDay)
         VALUES (:customer_id,:deliv)';
    $statement = $db->prepare($query);
    $statement->bindValue(':customer_id', $customer_id);
    $statement->bindValue(':deliv', $del_day);
    $statement->execute();
    $order_id = $db->lastInsertId();
    error_log('last inserted    ' . print_r($order_id, true));
    $statement->closeCursor();
    return $order_id;
}

function add_orders_item_new($item,$orid)
{
        global $db;
        //error_log('$item '.print_r($item,true).'  $orid  = ' .$orid );
    $query = '
        INSERT INTO OrderItems (orderID, productID, quantity)
        VALUES (:order_id, :product_id, :quantity)';
    $statement = $db->prepare($query);
    $statement->bindValue(':order_id', $orid);
    $statement->bindValue(':product_id', $item['productID']);


    $statement->bindValue(':quantity', $item['quantity']);
    $statement->execute();
    $statement->closeCursor();
}



function get_order_items($order_id) {
    global $db;
    $query = 'SELECT * FROM OrderItems WHERE orderID = :order_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':order_id', $order_id);
    $statement->execute();
    $order_items = $statement->fetchAll();
    $statement->closeCursor();
    return $order_items;
}

function get_orders_by_customer_id($customer_id) {
    global $db;
    $query = 'SELECT * FROM orders WHERE customerID = :customer_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':customer_id', $customer_id);
    $statement->execute();
    $order = $statement->fetchAll();
    $statement->closeCursor();
    return $order;
}

function get_unfilled_orders() {
    global $db;
    $query = 'SELECT * FROM orders
              INNER JOIN customers
              ON customers.customerID = orders.customerID
              WHERE shipDate IS NULL ORDER BY orderDate';
    $statement = $db->prepare($query);
    $statement->execute();
    $orders = $statement->fetchAll();
    $statement->closeCursor();
    return $orders;
}

function get_filled_orders() {
    global $db;
    $query =
        'SELECT * FROM orders
         INNER JOIN customers
         ON customers.customerID = orders.customerID
         WHERE shipDate IS NOT NULL ORDER BY orderDate';
    $statement = $db->prepare($query);
    $statement->execute();
    $orders = $statement->fetchAll();
    $statement->closeCursor();
    return $orders;
}

function set_ship_date($order_id) {
    global $db;
    $ship_date = date("Y-m-d H:i:s");
    $query = '
         UPDATE orders
         SET shipDate = :ship_date
         WHERE orderID = :order_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':ship_date', $ship_date);
    $statement->bindValue(':order_id', $order_id);
    $statement->execute();
    $statement->closeCursor();
}

function delete_order($order_id) {
    global $db;
    $query = 'DELETE FROM orders WHERE orderID = :order_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':order_id', $order_id);
    $statement->execute();
    $statement->closeCursor();
}

// added for project 2: add_order that doesn't need a "current user"
// and only uses some columns
// Note that add_order_item above can be used for adding order items
function add_order($customerID, $order_date, $deliveryDay)
{
    global $db;
    $shipAmount = 5.00;
    $taxAmount = 0.00;
    $shipAddressID = 7;
    $cardType = 2;
    $cardNumber = '4111111111111111';
    $cardExpires = '08/2016';
    $billingAddressID = 7;

    $query = 'INSERT INTO ORDERS (customerID, orderDate, shipAmount, taxAmount,
                             shipAddressID, cardType, cardNumber,
                             cardExpires, billingAddressID, deliveryDay)
         VALUES (:customer_id, :order_date, :ship_amount, :tax_amount,
                 :shipping_id, :card_type, :card_number,
                 :card_expires, :billing_id, :deliveryDay)';
    $statement = $db->prepare($query);
    $statement->bindValue(':customer_id', $customerID);
    $statement->bindValue(':order_date', $order_date);
    $statement->bindValue(':ship_amount', $shipAmount);
    $statement->bindValue(':tax_amount', $taxAmount);
    $statement->bindValue(':shipping_id', $shipAddressID);
    $statement->bindValue(':card_type', $cardType);
    $statement->bindValue(':card_number', $cardNumber);
    $statement->bindValue(':card_expires', $cardExpires);
    $statement->bindValue(':billing_id', $billingAddressID);
    $statement->bindValue(':deliveryDay', $deliveryDay);
    $statement->execute();
    $order_id = $db->lastInsertId();
    $statement->closeCursor();
    return $order_id;
}

// added for project 2: delete the orders and reset auto-increment if possible
function reinitialize_orders()
{ //
//echo"00000";
    global $db;
    $query='delete from orders;';
    $query.='delete from orderItems;';
    $statement = $db->prepare($query);
    $statement->execute(); 
    $statement->closeCursor();
    mod_reset_auto_increment();
}

// Note that this doesn't work properly on mysql before v. 5.6
// In such cases, drop and recreate the table
// or live with higher orderId numbers on reruns
function reset_auto_increment()
{
    global $db;
    $query='ALTER TABLE orders AUTO_INCREMENT = 0;';
    $statement = $db->prepare($query);
    $statement->execute(); 
    $statement->closeCursor();
}
function mod_reset_auto_increment()
{
    global $db;
    $query='ALTER TABLE orders AUTO_INCREMENT = 0;';
    $statement = $db->prepare($query);
    $statement->execute(); 
    $statement->closeCursor();
        $query='ALTER TABLE orderitems AUTO_INCREMENT = 0;';
    $statement = $db->prepare($query);
    $statement->execute(); 
    $statement->closeCursor();
}
?>