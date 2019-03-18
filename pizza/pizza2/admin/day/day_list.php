<?php include '../../view/header.php'; ?>
<main>
    <section>
        <h1>Today is day <?php echo $current_day; ?></h1>
        <form action="index.php" method="post">
            <input type="hidden" name="action" value="next_day">
            <input type="submit" value="Advance to day <?php echo $current_day + 1; ?>" />
        </form>

        <form  action="index.php" method="post">
            <input type="hidden" name="action" value="initial_db">           
            <input type="submit" value="Initialize DB (making day = 1)" />
            <br>
        </form>
        <br>
        <h2>Today's Orders</h2>
        <?php if (count($todays_orders) > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Room No</th>
                    <th>Status</th>
                </tr>

                <?php foreach ($todays_orders as $todays_order) : ?>
                    <tr>
                        <td><?php echo $todays_order['id']; ?> </td>
                        <td><?php echo $todays_order['room_number']; ?> </td>  
                        <td><?php echo $todays_order['status']; ?> </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No Orders Today </p>
        <?php endif; ?>
            
            
                <h2> CURRENT INVENTORY</h2>
        <?php if (count($inven) > 0): ?>
            <table>
                <tr>
                    <th>Product id</th>
                    <th>product Name</th>
                    <th>Quantity</th>
                    
                </tr>

                <?php foreach ($inven as $i) : ?>
                    <tr><td><?php if ($i['name']=='flour'):
                        echo "11";
                        else:echo "12";
                        endif;?> </td>
                        <td><?php echo $i['name']; ?> </td>
                        <td><?php echo $i['quantity']; ?> </td>  
                
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No inventory </p>
        <?php endif; ?>
            
            
            
            
                       
                <h2>UNDELIVERED</h2>
        <?php if (count($und) > 0): ?>
            <table>
                <tr>
                    <th>Order id</th>
                     <th>cheese qty</th>
                     <th>flour qty</th>
                    
                </tr>

                <?php foreach ($und as $i) : ?>
                    <tr>
                        <td><?php echo $i['orderID']; ?> </td>
                        <td><?php echo $i['flour_qty']; ?> </td>
                        <td><?php echo $i['cheese_qty']; ?> </td>
                
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>every orders are delivered </p>
        <?php endif; ?>
            
            
            
             <h2>supply Orders(only for understanding)</h2>
        <?php if (count($server_orders) > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>flour qty</th>
                    <th>cheese qty</th>
                    <th>delivered</th>
                </tr>

                <?php for($l=0;$l<count($server_orders);$l++): ?>

                    <tr>
                        <td><?php echo $server_orders[$l]['orderID']; ?> </td>
                        <td><?php  if ($server_orders[$l]['items'][0]['productID']==11):
                        echo $server_orders[$l]['items'][0]['quantity']; 
                        else:
                        echo $server_orders[$l]['items'][1]['quantity']; 
                        endif;?></td>
                        <td> <?php  if ($server_orders[$l]['items'][1]['productID']==12):
                        echo $server_orders[$l]['items'][1]['quantity'];
                        else:
                        echo $server_orders[$l]['items'][0]['quantity']; 
                        endif;?></td>
               
                        <td><?php if ($server_orders[$l]['delivered']==1):
                        echo "true";
                        else:
                        echo "false"; 
                        endif;?>  </td>

                    </tr>
                <?php endfor; ?>
            </table>
        <?php else: ?>
            <p>No Orders Today </p>
        <?php endif; ?>
            
            
            
            
            
    </section>

</main>
<?php include '../../view/footer.php'; ?>