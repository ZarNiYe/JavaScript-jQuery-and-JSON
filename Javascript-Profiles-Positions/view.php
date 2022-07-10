<?php
    require_once 'db.php';
    $sql = "SELECT * FROM Profile as a WHERE  a.profile_id = :xyz";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':xyz' => $_GET['profile_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row === false) {
        $_SESSION['error'] = "Bad value for profile_id";
        header("location: index.php");
        return;
    }

    $sql = 'SELECT a.* , b.*
            FROM Profile as a
            LEFT JOIN Position as b
            ON a.profile_id = b.profile_id
            WHERE a.profile_id = :xyz';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':xyz' => $_GET['profile_id']
    ]);
    $i = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $firstname = htmlentities($row['first_name']);
        $lastname = htmlentities($row['last_name']);
        $email = htmlentities($row['email']);
        $headline = htmlentities($row['headline']);
        $summary = htmlentities($row['summary']);
        
        if(isset($row['rank'])) {
            $checkPos = true;
        } else {
            $checkPos = false;
        }
        $i++;
        
        if($i == 1 ) {
            break;
        } else {
            continue;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Zar Ni Ye d980b15d</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
    <div class="container">
        <h1>Profle information</h1>
        <p>First Name:
            <?= $firstname ?>
        </p>
        <p>Last name:
            <?= $lastname ?>
        </p>
        <p>Email:
            <?= $email ?>
        </p>
        <p>Headline:
            <?= $headline ?>
        </p>
        <p>Summary:
            <?= $summary ?>
        </p>

        <?php 
            if($checkPos) {
                $sql = 'SELECT a.* , b.* FROM Profile as a LEFT JOIN Position as b ON a.profile_id = b.profile_id WHERE a.profile_id = :xyz';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':xyz' => $_GET['profile_id']
                ]);
        ?>
        <p>Position</p>
        <?php
            echo '<ul>';
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <li>
            <?= htmlentities($row['year']) ?> : <?= htmlentities($row['description']); ?>
        </li>
        <?php
            }
            echo '</ul>';
        }
        ?>

        <a href="index.php">Done</a>
    </div>
</body>
</html>