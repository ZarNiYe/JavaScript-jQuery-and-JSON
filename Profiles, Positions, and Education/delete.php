<?php
    require_once('db.php');
    session_start();
    if ( !isset($_SESSION['name']) ) 
    {
        die("ACCESS DENIED");
        
    }
    //// after form submission
    if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) 
    {
    //   echo "<pre>";
    //   print_r($_POST);

      $sql = 'DELETE FROM Profile WHERE profile_id = :zip';
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
          'zip' => $_POST['profile_id']
      ]);

      $_SESSION['success'] = 'Profile deleted';
      
      header('Location: index.php');
      return;
    }


    ////check profile_id exist or not
    $sql = 'SELECT * FROM Profile WHERE profile_id = :xyz';    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'xyz' => $_GET['profile_id']
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $firstName = $row['first_name'];
    $lastName = $row['last_name'];
    
    if ($row === false) 
    {
        $_SESSION['error'] = 'Bad value for profile_id';
        
        header('Location: index.php');
        return;
    }
?>

<!-- if exist -->
<!DOCTYPE html>
<html>
<head>
<title>Zar Ni Ye 09c1e450</title>

<?php require_once "bootstrap.php"; ?>

</head>
    <body>

    <div class="container">
        <h1> Deleteing Profile </h1>

        <p>First Name:
        <?= $firstName ?></p>

        <p>Last Name:
        <?= $lastName ?></p>
        
        <form  method="post">
            <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">

            <input type="submit" value="Delete" name="delete" class="btn btn-danger" >
            <a href="index.php" class="btn btn-warning">Cancel</a>
        </form>


    </div>