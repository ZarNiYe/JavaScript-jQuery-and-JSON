

<?php
require_once "pdo.php";
session_start();


// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fname = htmlentities($row['first_name']);
$lname = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$summary = htmlentities($row['summary']);
$profileID = htmlentities($row['profile_id']);
?>

<!DOCTYPE html>
<html>
<head>
<title>e11f40b8</title>
</head>
<body>

<?php echo('<h1> Profile information for '.$fname."</h1>\n");?>

<p style = "border: 1px">First Name : <?= $fname ?></p>
<p>Last Name : <?= $lname ?></p>
<p>Email : <?= $email ?></p>
<p>Headline : <?= $headline ?></p>
<p>Summary : <?= $summary ?></p>
<p> <a href = "index.php"> Done </a></p>

</body>
</html>