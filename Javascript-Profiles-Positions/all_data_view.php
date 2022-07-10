<?php
require_once 'db.php';

$stmt = $pdo->query('SELECT * from Profile');

if(!isset($_SESSION['name'])) {
    if($stmt->rowCount() == 0) {
        echo 'No rows found';
    } else {
        echo ("<table border='1'>");
        echo ('<thead class = "thead-dark">');
        echo ('<tr>' . "\n");
            echo ("<th scope = 'col'> Name </th>");
            echo ("<th scope = 'col'> Headline </th>");
        echo ('</tr>');
        echo ('</thead>');
        echo ('<tbody>');
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo  ('<tr scope = "row">');
            echo '<td>';
            echo '<a href="view.php?profile_id=' . $row['profile_id'] . '">' . htmlentities($row['first_name']). " " . htmlentities($row['last_name']) . '</a>';
            echo '</td>';
            echo '<td>';
            echo htmlentities($row['headline']);
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '<br>';
    }
} else {
    if($stmt->rowCount() == 0) {
        echo "No rows found";
    } else {
        echo ("<table border='1'>");
        echo ('<thead class = "thead-dark">');
        echo ('<tr>' . "\n");
            echo ("<th scope = 'col'> Name </th>");
            echo ("<th scope = 'col'> Headline </th>");
            echo ("<th scope = 'col'> Action </th>");
        echo ('</tr>');
        echo ('</thead>');
        echo ('<tbody>');
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo  ('<tr scope = "row">');
            echo '<td>';
            echo '<a href="view.php?profile_id=' . $row['profile_id'] . '">' . htmlentities($row['first_name']). " " . htmlentities($row['last_name']) . '</a>';
            echo '</td>';
            echo '<td>';
            echo htmlentities($row['headline']);
            echo '</td>';
            echo '<td>';
            echo '<a href="edit.php?profile_id=' . $row['profile_id'] . '"> Edit </a>';
            echo '<a href="delete.php?profile_id=' . $row['profile_id'] . '"> Delete </a>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '<br>';
    }
}