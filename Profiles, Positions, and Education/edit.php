<?php
    require_once ("db.php");
    session_start();

    if ( !isset($_SESSION['name'])){
        die("ACCESS DENIED");
    }

    if (isset($_POST['first_name']) &&
        isset($_POST['last_name']) &&
        isset($_POST['email']) &&
        isset($_POST['headline']) &&
        isset($_POST['summary'])) {
            if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) <1 || strlen($_POST['headline']) <1 || strlen($_POST['summary']) < 1) {
                $_SESSION['error'] = "All fields are required";
                header("location: add.php");
                return;
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email address must contain @";
                header("location:add.php");
                return;
            }

            for($i=1; $i<= 9; $i++){
                if (!isset($_POST['year' . $i])) continue;
                if (!isset($_POST['desc' . $i])) continue;
                $year = $_POST['year' . $i];
                $desc = $_POST['desc' . $i];

                if (strlen($year) == 0 || strlen($desc) == 0) {
                    $_SESSION['error'] = "All fields are required";
                    header("location:add.pph");
                    return;
                }

                if(!is_numeric($year)) {
                    $_SESSION['error'] = "Year must be numeric";
                    header("location:add.php");
                    return;
                }
            }

            for( $i=1; $i<=9; $i++){
                if ( !isset($_POST['edu_year' . $i])) continue;
                if ( !isset($_POST['edu_school' . $i])) continue;

                $year = $_POST['edu_year' . $i];
                $school = $_POST['edu_school' . $i];

                if (strlen($year) == 0 || strlen($school) == 0){
                    $_SESSION['error'] = "All fields are required";
                    header("location:add.php");
                    return;
                }

                if( ! is_numeric($year)) {
                    $_SESSION['error'] = "Year must be numeric";
                    header("Location: add.php");
                    return;
                }
            }
        

            $sql = "UPDATE `Profile` SET `user_id` = :a, `first_name` = :fn, `last_name` = :ln, `email` = :em, `headline` = :he, `summary` = :su WHERE profile_id = :tt";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':a' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'],
                ':tt' => $_POST['profile_id']
            ]);

            $checkPosStmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :pid');
            $checkPosStmt->execute([
                ':pid' => $_POST['profile_id']
            ]);

        if ($checkPosStmt->rowCount() != 0) {
            $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
            $stmt->execute([
                ':pid' => $_POST['profile_id']
            ]);


            $rank = 1;
            for($i=1; $i <=9; $i++){
                if ( !isset($_POST['year' . $i])) continue;
                if ( !isset($_POST['desc' . $i])) continue;

                $year = $_POST['year' . $i];
                $desc = $_POST['desc' . $i];

                $stmt = $pdo->prepare('INSERT INTO `Position` (`profile_id`, `rank`, `year`, `description`) VALUES (:pid, :rank, :year, :desc)');

                $stmt->execute([
                    ':pid' => $_POST['profil_id'],
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc
                ]);
                $rank++;
            };
        } else {
            $rank=1;
            for($i=1; $i <= 9; $i++){
                if ( !isset($_POST['year' . $i])) continue;
                if ( !isset($_POST['desc' . $i])) continue;

                $year = $_POST['year' . $i];
                $desc = $_POST['desc' . $i];

                $stmt = $pdo->prepare('INSERT INTO `Position` (`profile_id`, `rank`, `year`, `description`) VALUES (:pid, :rank, :year, :desc)');

                $stmt->execute([
                    ':pid' => $_POST['profil_id'],
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc
                ]);
                $rank++;
            }
        }

        $checkEduStmt = $pdo->prepare('SELECT * FROM Education WHERE profile_id = :pid');
        $checkEduStmt->execute([
            ':pid' => $_POST['profile_id']
        ]);

        if($checkEduStmt->rowCount() != 0) {
            $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
            $stmt->execute([
                ':pid' => $_POST['profile_id']
            ]);

            $rank = 1;
            for($i=1; $i <=9; $i++){
                if (!isset($_POST['edu_year' . $i])) continue;
                if (!isset($_POST['edu_school' . $i])) continue;

                $year = $_POST['year' . $i];
                $schoolName = $_POST['edu_school' . $i];

                $istmt = $pdo->prepare('SELECT * FROM Institution WHERE name = :insname');
                $istmt->execute([':insname' => $schoolName]);

                $irow = $istmt->fetch(PDO::FETCH_ASSOC);

                if ( $irow === false){
                    $stmt = $pdo->prepare('INSERT INTO Institution WHERE name= :insname');
                    $stmt->execute([':insname' => $schoolName]);
                    $institutionId = $pdo->lastInsertId();
                } else {
                    $institutionId = $irow['institution_id'];
                }

                $stmt = $pdo->prepare('INSERT INTO `Education` (`profile_id`, `institution_id`, `rank`, `year`) VALUES (:pid, :iid, :rnk, :yr)');

                $posInsrt = $stmt->execute([
                    ':pid' => $_POST['profile_id'],
                    ':iid' => $institutionId,
                    ':rank' => $rank,
                    ':year' => $year
                ]);
                $rank++;
            }
        } else {
            $irank = 1;
            for($i=1; $i <=9; $i++){
                if(!isset($_POST['edu_year' . $i])) continue;
                if(!isset($_POST['edu_school' . $i])) continue;

                $year = $_POST['edu_year' . $i];
                $schoolName = $_POST['edu_school' . $i];

                $istmt = $pdo->prepare('SELECT * FROM Institution WHERE name = :insname');

                $istmt->execute([
                    ':insname' => $schoolName
                ]);

                $irow = $istmt->fetch(PDO::FETCH_ASSOC);

                if ($irow === false){
                    $stmt = $pdo->prepare('INSERT INTO `Institution` (`name`) VALUES (:iid)');
                    $stmt->execute([':iid' => $schoolName]);
                    $institutionId = $pdo->lastInsertId();
                } else {
                    $institutionId = $irow['institutionId'];
                }

                $stmt = $pdo->prepare('INSERT INTO `Education` (`profile_id`, `institution_id`, `rank`, `year`) VALUES (:pid, :iid, :rnk, :yr)');

                $posInsrt = $stmt->execute([
                    ':pid' => $_POST['profile_id'],
                    ':iid' => $institutionId,
                    ':rnk' => $rank,
                    ':year' => $year
                ]);
                $irank++;
            }
        }
        $_SESSION['success'] = "Profile updated";
        header("location: index.php");
        return;
    }

    $sql = "SELECT * FROM Profile WHERE profile_id = :xyz";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':xyz' => $_GET['profile_id']
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row === false) {
        $_SESSION['error'] = "Bad value for profile_id";
        header("location: index.php");
        return;
    } else {
        $mainprofileId = $row['profile_id'];
    }

    $sql = 'SELECT  a.*, b.*
                FROM Profile as a
                LEFT JOIN Education as b
                ON a.profile_id = b.profile_id
                WHERE a.profile_id = :xyz';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'xyz' => $_GET['profile_id']
    ]);

    // echo "<pre>"; print_r( $row = $stmt->fetch( PDO::FETCH_ASSOC));die();
    $i = 0;
    while( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) 
    {        
        if ( isset($row['rank']) ) 
        {
            $checkEdu = true;
        }
        else 
        {
            $checkEdu = false ;
        }
        $i++;

        if ($i == 1) 
        {
            break;
        } 
        else 
        {
            continue;
        }
        
    }

    $sql = 'SELECT a.*, b.*
            FROM Profile as a
            LEFT JOIN Position as b
            ON a.profile_id = b.profile_id
            WHERE a.profile_id = :xyz';
    
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'xyz' => $_GET['profile_id']
    ]);


    $i = 0;
    while( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) 
    {        

        // print_r($row);die();
        $firstName =  htmlentities($row['first_name']);
        $lastName =  htmlentities($row['last_name']);
        $email =  htmlentities($row['email']);
        $headline =  htmlentities($row['headline']);
        $summary =  htmlentities($row['summary']);

        if ( isset($row['rank']) ) 
        {
            $checkPos = true;
        }
        else 
        {
            $checkPos = false ;
        }
        $i++;

        if ($i == 1) 
        {
            break;
        } 
        else 
        {
            continue;
        }
        
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Zar Ni Ye 09c1e450</title>
    <?php require_once('bootstrap.php'); ?>
</head>
<body>
    <div class="container">
        <h1>Editing Profile for <?= $_SESSION['name'] ?></h1>
        <?php
            if (isset($_SESSION['error'])){
                echo ('<p style="color:red">' . htmlentities($_SESSION['error']) . '</p>\n');
                unset($_SESSION['error']);
            }
        ?>

        <form method="POST">
            <input type="hidden" name="profile_id" value="<?= $mainprofileId ?>">
            <label>First Name:
                <input type="text" name="first_name" size="60" class="form-control" value="<?= $firstName ?>">
            </label>
            <br><br>
            <label>Last Name:
                <input type="text" name="last_name" size="60" class="form-control" value="<?= $lastName ?>">
            </label>
            <br><br>
            <label>Email:
                <input type="text" name="email" size="60" class="form-control" value="<?= $email ?>">
            </label>
            <br><br>
            <label>Headline:
                <input type="text" name="headline" size="60" class="form-control" value="<?= $headline ?>">
            </label>
            <br><br>
            <label>First Name:
                <textarea name="summary" cols="80" rows="8" class="form-control"><?= $summary ?></textarea>
            </label>
            <br><br>
            <label>Education:
                <input type="submit" class="btn btn-primary" id="addEdu" value="+">
            </label>
            <br><br>

            <?php
                $countEdu = 0;
                if($checkEdu) {
                    $sql = 'SELECT a.* , b.*, c.name As ins_name
                            FROM Profile as a
                            
                            LEFT JOIN Education as b
                            ON a.profile_id = b.profile_id

                            LEFT JOIN Institution as c
                            ON b.institution_id = c.institution_id

                            WHERE a.profile_id = :xyz
                            ORDER BY b.rank ASC';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'xyz' => $_GET['profile_id']
                    ]);
                
            ?>

            <?php
                echo("<ul>");
                while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                {
            ?>
                <div id="<?= 'education_fields'.$row['rank']; ?>"> 
                    <p>Year: <input type="text" name="<?= 'edu_year'.$row['rank']; ?>" value="<?= $row['year']; ?>" /> 
                    <input type="button" value="-" 
                        <?php $rmvId = "'".'#education_fields'.$row['rank']."'";?>
                        onclick="$(<?= $rmvId?>).remove();return false;"></p> 
                        <p>School: <input type="text" size="80" name="<?= 'edu_school'.$row['rank']; ?>" class="school" value="<?=$row['ins_name'];?>" />
                    <br><br> 
                </div>
                <script>                     
                    $( ".school" ).autocomplete({
                        source: "school.php"
                    });
                </script>
            <?php 
                    $countEdu++;

                    }
                    echo "</ul>";
                }
            ?> 

                <div id="education_fields">

                </div><br><br>

                <?php 
                $countPos = 0;
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
            <?php 
                    echo "<ul>";
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                    {                
            ?>  
                        <div id="<?= 'position'.$row['rank']; ?>"> 
                            <p>Year: <input type="text" name="<?= 'year'.$row['rank']; ?>" value="<?= $row['year']; ?>" /> 
                            <input type="button" value="-" 
                                <?php $rmvId = "'".'#position'.$row['rank']."'";?>
                                onclick="$(<?= $rmvId?>).remove();return false;"></p> 
                            <textarea name="<?= 'desc'.$row['rank']; ?>" rows="8" cols="80"><?= $row['description']; ?></textarea>
                            <br><br> 
                        </div>
            <?php 
                    $countPos++;

                    }
                    echo "</ul>";
                }
            ?> 
            <div id="position_fields">
            </div><br><br>

            <input type="submit" value="Update" name="Save" class="btn btn-success">
            <a href="index.php" class="btn btn-warning">Cancel</a>
            <br><br>
        </form>

        <script>
            countPos = <?= $countPos ?>;
            countEdu = <?= $countEdu ?>;

            $(document).ready(function(){
                window.cosole && console.log('Document ready called');
                $('#addPos').click(function(event){
                    event.preventDefault();
                    if ( countPos >= 9 ) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position "+countPos);
                    $('#position_fields').append(
                        '<div id="position'+countPos+'"> \
                            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                            <br><br> \
                        </div>');
                });
            });

            $('#addEdu').click(function(event){
                    // http://api.jquery.com/event.preventdefault/
                    event.preventDefault();
                    if ( countEdu >= 9 ) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEdu++;

                    window.console && console.log("Adding education "+countEdu);
                    $('#education_fields').append(
                        '<div id="edu' + countEdu + '"> \
                            <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
                            <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"><br>\
                            <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />\
                        </p></div>'
                    );

                    $( ".school" ).autocomplete({
                        source: "school.php"
                    });
                });
        </script>
    </div>
</body>
</html>