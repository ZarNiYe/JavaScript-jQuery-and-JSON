<?php
    require_once ('db.php');
    
    $stmt = $pdo->query('SELECT * FROM Profile');

    if(!isset($_SESSION['name'])) {
        if($stmt->rowCount() == 0) {
            echo "No rows found <br><br>";
        } else {
            echo "<table class= 'table'>";
                echo "<thead class = 'thead-dark'>";
                    echo "<tr>";
                        echo "<th>Name</th>";
                        echo "<th>Headline</th>";
                    echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        echo "<tr>";
                            echo "<td>";
                               echo '<a class="" href="view.php?profile_id='.$row['profile_id'].'">'. htmlentities($row['first_name'])." ".htmlentities($row['last_name']) . '</a> ';
                            echo "</td>";
                            echo "<td>";
                                echo htmlentities($row['headline']);
                            echo "</td>";
                        echo "</tr>";
                    }
                echo "</tbody>";
            echo "</table>";
            echo "<br>";
        }
    } else {
        if($stmt->rowCount() == 0) {
            echo "No rows found <br><br>";
        } else {
            echo "<table>";
                echo "<thead>";
                    echo "<tr>";
                        echo "<th>Name</th>";
                        echo "<th>Headline</th>";
                        echo "<th>Action</th>";
                    echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        echo "<tr>";
                            echo "<td>";
                                echo '<a href = "view.php?profile_id=' . $row['profile_id'] . '">' . htmlentities($row['first_name']) . " " . $row['last_name'] . "</a>";
                            echo "</td>";

                            echo "<td>";
                                echo htmlentities($row['headline']);
                            echo "</td>";

                            echo "<td>";
                                echo '<a href = "edit.php?profile_id=' . $row['profile_id'] . '"> Edit </a>';
                            echo "</td>";

                            echo "<td>";
                                echo '<a href = "delete.php?profile_id=' . $row['profile_id'] . '"> Delete </a>';
                            echo "</td>";
                        echo "</tr>";
                    }
                echo "</tbody>";
            echo "</table>";
            echo "<br>";
        }
    }
?>