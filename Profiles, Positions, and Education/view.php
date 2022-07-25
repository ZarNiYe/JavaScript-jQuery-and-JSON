<?php
    require_once "db.php";

    $sql = 'SELECT * FROM Profile as a WHERE a.profile_id = :xyz';

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array('xyz'=>$_GET['profile_id']));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row === false) {
        $_SESSION['error'] = "Bad value for profile_id";

        header("location: index.php");
        return;
    }

    $sql = 'SELECT  a.*, b.*
            FROM Profile as a
            LEFT JOIN Position as b
            ON a.profile_id = b.profile_id
            WHERE a.profile_id = :xyz';
    
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'xyz' => $_GET['profile_id']
    ]);

    $i = 0;
    while( $row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $firstName = htmlentities($row['first_name']);
        $lastName = htmlentities($row['last_name']);
        $email = htmlentities($row['email']);
        $headline = htmlentities($row['summary']);
        $summary = htmlentities($row['summary']);

        if( isset($row['rank'])){
            $checkPos = true;
        } else {
            $checkPos = false;
        }
        $i++;

        if($i == 1) {
            break;
        } else {
            continue;
        }
    }

    $sql = 'SELECT  b.*
            FROM Profile as a
            LEFT JOIN Education as b
            ON a.profile_id = b.profile_id
            WHERE a.profile_id = :xyz';
    
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'xyz' => $_GET['profile_id']
    ]);

    $i = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if ( isset($row['rank'])){
            $checkEdu = true;
        } else {
            $checkEdu = false;
        }
        $i++;

        if($i == 1) {
            break;
        } else {
            continue;
        }
    }
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Zar Ni Ye 09c1e450</title>
        <?php require_once "bootstrap.php"; ?>
    </head>
    <body>
        <div class="container">
            <h1>Profile information</h1>
            <p>First Name:
                <?= $firstName ?>
            </p>
            <p>Last Name:
                <?= $lastName ?>
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
                if($checkEdu)
                {
                    $sql = 'SELECT a.* , b.*, c.name As ins_name
                        FROM Profile as a
                        
                        LEFT JOIN Education as b
                        ON a.profile_id = b.profile_id

                        LEFT JOIN Institution as c
                        ON b.institution_id = c.institution_id

                        WHERE a.profile_id = :xyz';

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'xyz' => $_GET['profile_id']
                    ]); 
            ?>
                <p>Education</p>
            <?php
                echo "<ul>";
                while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                {
            ?>
                <li>
                    <?= htmlentities($row['year']); ?>
                    <?= htmlentities($row['ins_name']); ?>
                </li>
            <?php
                    }
                    echo "</ul>";
                }   
            ?>

            <?php
                if($checkPos)
                {
                    $sql = 'SELECT a.* , b.*
                        FROM Profile as a
                        LEFT JOIN Position as b
                        ON a.profile_id = b.profile_id
                        WHERE a.profile_id = :xyz';

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'xyz' => $_GET['profile_id']
                    ]);  
            ?>
                <p>Position</p>
            <?php
                echo "<ul>";
                while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                {
            ?>
                <li>
                    <?= htmlentities($row['year']); ?>
                    <?= htmlentities($row['description']); ?>
                </li>
            <?php 
                    }
                    echo "</ul>";
                }
            ?>
            <a href="index.php">Done</a>
        </div>
    </body>
</html>