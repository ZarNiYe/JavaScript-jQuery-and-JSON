<?php
    require_once ('db.php');
    session_start();
    
    if(!isset($_SESSION['name'])){
        die('ACESS DINIED');
    }

    $stmt = $pdo->prepare('SELECT institution_id,name FROM Institution WHERE name LIKE :prefix');
    $stmt->execute(array(':prefix' => $_REQUEST['term']. "%"));

    $retval = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $retval[] = $row['name'];
    }
    echo(json_encode($retval, JSON_PRETTY_PRINT))
?>