<?php



function get_inventory($db) {
    $query = 'SELECT * FROM inventory';
    $statement = $db->prepare($query);
  
    $statement->execute();
    $orders = $statement->fetchAll();
    $statement->closeCursor();   
    return $orders;
}
function prev_inventory($db,$n) {
    $query = 'SELECT * FROM inventory where name=:n';
    $statement = $db->prepare($query);

  $statement->bindValue(':n', $n);
    $statement->execute();
    $val = $statement->fetch();
    $statement->closeCursor();  
    //print_r($val);
    //echo $val['quantity'];
    return $val['quantity'];
}

function update_inventory($db,$n,$q) {
    
    $t=prev_inventory($db,$n);
    $q+=$t;
    $query = 'update inventory set quantity=:q where name=:n';
    $statement = $db->prepare($query);
  $statement->bindValue(':q', $q);
  $statement->bindValue(':n', $n);
    $statement->execute();

    $statement->closeCursor();   

}


function update_inventory2($db,$n,$q) {
    
   
    $q;
    $query = 'update inventory set quantity=:q where name=:n';
    $statement = $db->prepare($query);
  $statement->bindValue(':q', $q);
  $statement->bindValue(':n', $n);
    $statement->execute();

    $statement->closeCursor();   

}
function decrease_inventory($db,$n){
    //echo $n; 
    //$query = 'SELECT * FROM inventory';
    //echo $query;
    $t=get_inventory($db);
    $che=prev_inventory($db,'cheese');
    $flo=prev_inventory($db,'flour');
    echo $che."****";
    echo $flo;
    $che=$che-$n;
    $flo=$flo-$n;
    //echo $che;
update_inventory2($db,'cheese',$che);
    
 update_inventory2($db,'flour',$flo);   
}





?>
