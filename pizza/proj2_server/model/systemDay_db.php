<?php
function get_day()
{
    global $db;
    $query = 'SELECT * FROM `systemDay`';
        $statement = $db->prepare($query);

        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();
       
        return $result['dayNumber'];
        
}

function add_day($day1)
{
    global $db;
    
    $day2=get_day();
    $query = 'update systemDay set dayNumber=:d1 where dayNumber=:d2';
        $statement = $db->prepare($query);
$statement->bindValue(':d1', $day1);
$statement->bindValue(':d2', $day2);
        $statement->execute();
      
        $statement->closeCursor();
}


?>
