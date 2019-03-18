<?php


function get_undelivered($db) {
    $query = 'SELECT * FROM undelivered';
    $statement = $db->prepare($query);
  
    $statement->execute();
    $orders = $statement->fetchAll();
    $statement->closeCursor();   
    return $orders;
}

function insert_undelivered($db,$id,$cheese,$flour) {
    $query = 'insert into undelivered values(:id,:flo,:che)';
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);  
    $statement->bindValue(':flo', $flour); 
    $statement->bindValue(':che', $cheese); 
    $statement->execute();

    $statement->closeCursor();   

}


function delete_undelivered($db,$id) {
    $query = 'delete FROM undelivered where orderID=:id';
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);  
    $statement->execute();

    $statement->closeCursor();   

}
?>
