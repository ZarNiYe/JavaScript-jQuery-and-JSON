<?php
    session_start();
    if(!isset($_SESSION['name'])) {
        die ("ACCESS DIENED");
    }
    require_once 'db.php';
    require_once 'mr_trait.php';

    if(isset($_POST['cancel'])){
        header("location: index.php");
        return;
    }

    if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
        if(strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline'] < 1) || strlen($_POST['summary']) < 1) {
            $_SESSION['error'] = "All fields are required";
            header("location: add.php");
            return;
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $_SESSION['error'] = "Email address must contain @";
            header("location: add.php");
            return;
        }

        for($i=1; $i <= 9; $i++){
            if(!isset($_POST['year'.$i])) continue;
            if(!isset($_POST['desc'.$i])) continue;
    
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
    
            if(strlen($year) == 0 || strlen($desc) == 0) {
                $_SESSION['error'] = "All fields are required";
                header("location: add.php");
                return;
            }
    
            if(!is_numeric($year)){
                $_SESSION['error'] = "Position year must be numeric";
                header("location: add.php");
                return;
            }
        }

        $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');

        $profInsrt = $stmt->execute(array(
            ":uid" => $_SESSION['user_id'],
            ":fn" => $_POST['first_name'],
            ":ln" => $_POST['last_name'],
            ":em" => $_POST['email'],
            ":he" => $_POST['headline'],
            ":su" => $_POST['summary']
        ));

        $profileId = $pdo->lastInsertId();

        $rank = 1;
        for($i=1; $i<=9; $i++) 
        {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;

            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];

            $stmt = $pdo->prepare(
                'INSERT INTO `Position` (`profile_id`, `rank`, `year`, `description`)
                VALUES (:pid, :rank, :year, :desc)');

            $posInsrt = $stmt->execute(array(
                ':pid' => $profileId,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
            );

            $rank ++;
        
        }
        $_SESSION['success'] = "Profile added";
        header("Location: index.php");
        return;
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
        <h1>Adding Profile for <?= $_SESSION['name'] ?></h1>
        <?php
            if(isset($_SESSION['error'])) {
                echo '<p style = "color:red;">' . htmlentities($_SESSION['error']) . '</p>';
                unset($_SESSION['error']);
            }
        ?>

        <form method="POST">
            <p>First Name:
                <input type="text" name="first_name" size="60">
            </p>
            <p>Last Name:
                <input type="text" name="last_name" size="60">
            </p>
            <p>Email:
                <input type="text" name="email" size="60">
            </p>
            <p>Headline:
                <input type="text" name="headline" size="60">
            </p>
            <p>Summary:
                <textarea name="summary" cols="80" rows="8"></textarea>
            </p>
            <label>Position:
                <input type="submit" class="btn btn-primary" id="addPos" value="+">
            </label>
            <div id="position_fields">  
            </div>
            <br>
            <br>
            <input type="submit" value="Add" class="btn btn-success">
            <input type="submit" value="Cancel" name="cancel" class="btn btn-warning">
        </form>
    </div>
    <script>
        countPos = 0;

        $(document).ready(function(){
            window.console && console.log('Document ready called');
            $('#addPos').click(function(event){
                event.preventDefault();
                if(countPos >= 9) {
                    alert("Maximun of nine position entries exceeded");
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
    </script>
</body>
</html>
