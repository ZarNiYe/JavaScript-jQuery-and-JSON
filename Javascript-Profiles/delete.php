<?php
require_once "pdo.php";
session_start();

// print error message if no access granted
if (!isset($_SESSION['name']) )
{
      die('ACCESS DENIED: Please redirect your browser to the <a href ="index.php">front page.</a>');
}

// only allow records to be deleted if the user created them
$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :profile_id AND user_id = :user_id");
$stmt->execute(array(
    ':profile_id' => $_GET['profile_id'],
    ':user_id' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ( $row === false )
{
    $_SESSION['error'] = 'Only entries created by your user_id can be deleted';
    header( 'Location: index.php' ) ;
    return;
}
else
{
  if ( isset($_POST['Delete']) && isset($_GET['profile_id']) ) {
      $sql = "DELETE FROM Profile WHERE profile_id = :zip";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':zip' => $_GET['profile_id']));
      $_SESSION['success'] = 'Record deleted';
      header( 'Location: index.php' ) ;
      return;
  }
}


// Guardian: Make sure that auto_id is present
if ( ! isset($_GET['profile_id']) )
{
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

// print and redirect if profile ID does not exist
$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false )
{
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>e11f40b8</title>
</head>
<body>

<h1> Deleting Profile </h1>

<p style = "color:grey"> Are you sure you want to delete the profile below?</p>
<p>First name: <?= htmlentities($row['first_name']) ?></p>
<p>Last name: <?= htmlentities($row['last_name']) ?></p>

<form method="post">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input type="submit" value="Delete" name="Delete">
<a href="index.php">Cancel</a>
</form>

</body>
</html>