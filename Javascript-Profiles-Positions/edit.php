<?php
    require_once('db.php');
    require_once('mr_trait.php');

    session_start();
    if ( !isset($_SESSION['name']) ) 
    {
        die("ACCESS DENIED");
        
    }
    

    //// after form submission
    if (    
            isset($_POST['first_name']) 
            &&  isset($_POST['last_name']) 
            &&  isset($_POST['email']) 
            &&  isset($_POST['headline']) 
            &&  isset($_POST['summary']) 
    
    )    
    {
        /////from mr_trait.php
        $msg = validateProfile();

        if ( is_string($msg) ) 
        {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }

        ////validate the position from mr_trait.php
        $msg = validatePos();

        if ( is_string($msg) ) 
        {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        }
        // echo"<pre>"; print_r($_REQUEST);die();
      
            // echo "<pre>";
            // print_r($_POST);
            // die();
            $sql = "UPDATE Profile SET 
                    user_id = :a,
                    first_name = :fn,
                    last_name = :ln,
                    email = :em,
                    headline = :he,
                    summary = :su

                    WHERE profile_id = :tt";


            // die($_POST['profile_id']);

            $stmt = $pdo->prepare($sql);

            $stmt->execute(array(
                    ':a' => $_SESSION['user_id'],
                    ':fn' => $_POST['first_name'],
                    ':ln' => $_POST['last_name'],
                    ':em' => $_POST['email'],
                    ':he' => $_POST['headline'],
                    ':su' => $_POST['summary'],
                    ':tt' =>  $_POST['profile_id']
                )
            );


            ////check user has previous position entry
            $checkPosStmnt = $pdo->prepare('SELECT * FROM Position WHERE profile_id =:pid');
            $checkPosStmnt->execute(array( ':pid' => $_POST['profile_id']));

            if ($checkPosStmnt->rowCount() != 0) 
            {
                // echo "if ";
                // Clear out the old position entries
               $stmt = $pdo->prepare('DELETE FROM Position
                   WHERE profile_id=:pid');
    
               $stmt->execute(array( ':pid' => $_POST['profile_id']));
               // Insert the position entries
               $rank = 1;
               for($i=1; $i<=9; $i++) 
               {
                   if ( ! isset($_POST['year'.$i]) ) continue;
                   if ( ! isset($_POST['desc'.$i]) ) continue;
    
                   $year = $_POST['year'.$i];
                   $desc = $_POST['desc'.$i];
    
                   $stmt = $pdo->prepare(
                       'INSERT INTO Position (profile_id, rank, year, description)
                       VALUES (:pid, :rank, :year, :desc)');
    
                   
                   $stmt->execute(array(
                       ':pid' => $_POST['profile_id'],
                       ':rank' => $rank,
                       ':year' => $year,
                       ':desc' => $desc)
                   );
                   $rank++;
               }
                   
            }
            else 
            {
                // echo "else ";
                // echo $profileId."<br>";

                // echo "<pre>";
                // print_r($_POST);
                // echo $_POST['profile_id']."<br>";
               // Insert the position entries
               $rank = 1;
               for($i=1; $i<=9; $i++) 
               {
                   if ( ! isset($_POST['year'.$i]) ) continue;
                   if ( ! isset($_POST['desc'.$i]) ) continue;
    
                   $year = $_POST['year'.$i];
                   $desc = $_POST['desc'.$i];
    
                   $stmt = $pdo->prepare(
                       'INSERT INTO Position (profile_id, rank, year, description)
                       VALUES (:pid, :rank, :year, :desc)');
    
                   
                   $stmt->execute(array(
                       ':pid' => $_POST['profile_id'],
                       ':rank' => $rank,
                       ':year' => $year,
                       ':desc' => $desc)
                   );
                   $rank++;
               }
            } 
            $_SESSION['success'] = "Profile updated";
            header("Location: index.php");
            return;
    } 
    
    ////check profile_id exist or not
    $sql = 'SELECT * FROM Profile WHERE profile_id = :xyz';    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'xyz' => $_GET['profile_id']
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false) 
    {
        $_SESSION['error'] = 'Bad value for profile_id';
        
        header('Location: index.php');
        return;
    }


    ////check profile_id exist or not
    $sql = 'SELECT a.* 
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

<!-- if exist -->
<!DOCTYPE html>
<html>
<head>
<title>Zar Ni Ye d980b15d</title>

<?php require_once "bootstrap.php"; ?>

</head>
    <body>

    <div class="container">
        <h1 style="text-align:center; margin-bottom: 50px"> 
                Editing Profile for <?= $_SESSION['name'] ?>
        </h1>
        <?php
            // Note triple not equals and think how badly double
            // not equals would work here...
            if ( isset($_SESSION['error'] ) ) 
            {
                // Look closely at the use of single and double quotes
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset( $_SESSION['error'] );
            }
            // else 
            // {
            //     echo "<pre>";
            //     print_r($_SESSION);
            // }

        ?>
        <form method="post">
            <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">

            <label>First Name:
                <input type="text" name="first_name" size="60" class="form-control" value="<?= $firstName ?>"/>
            </label><br>

            <label>Last Name:
                <input type="text" name="last_name" value="<?= $lastName ?>" size="60"  class="form-control"/>
            </label><br>

            <label>Email:
                <input type="text" name="email" value="<?= $email ?>" class="form-control"/>
            </label><br>

            <label>Headline:
                <input type="text" name="headline" value="<?= $headline ?>" class="form-control"/>
            </label><br>

            <label>Summary:
                <textarea name="summary"  class="form-control" rows="8" cols="80"><?= $summary ?></textarea>                    
            </label><br>
            <br><br>



            <label>
                Position: <input type="submit" class="btn btn-primary"  id="addPos" value="+" >
            </label><br><br>

            <!-- printing Position -->
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
            countPos = <?= $countPos; ?>;

            // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            $(document).ready(function(){
                window.console && console.log('Document ready called');
                $('#addPos').click(function(event){
                    // http://api.jquery.com/event.preventdefault/
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
        </script>

    
    </div>