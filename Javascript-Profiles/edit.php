<?php
require_once "pdo.php";
session_start();

// print error message if no access granted
if (!isset($_SESSION['name']) )
{
      die('ACCESS DENIED: Please redirect your browser to the <a href ="logout.php">front page.</a>');
}

// only allow records to be edited if the user created them
$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :profile_id AND user_id = :user_id");
$stmt->execute(array(
    ':profile_id' => $_GET['profile_id'],
    ':user_id' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ( $row === false )
{
    $_SESSION['error'] = 'Only entries created by your user_id can be edited';
    header( 'Location: index.php' ) ;
    return;
}
else
{
    // check if all fields have been filed in
    if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))
    {
        // check for missing data in any field
        if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1)
        {
            $_SESSION['error'] = 'All fields are required';
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }

        // check if the email does not have an @ sign
        if (strlen($_POST['email']) > 1 && !preg_match('/\b@\b/',$_POST['email']) )
        {
            $_SESSION['error'] = "Email must have an at-sign (@)";
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }

        $sql = "UPDATE Profile SET first_name = :first_name,
                last_name = :last_name, email = :email, headline = :headline, summary = :summary
                WHERE profile_id = :profile_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':email' => $_POST['email'],
            ':headline' => $_POST['headline'],
            ':summary' => $_POST['summary'],
            ':profile_id' => $_GET['profile_id']));

        $_SESSION['success'] = 'Record updated';
        header( 'Location: index.php' ) ;
        return;
    }
}



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
<script src="validations.js"></script>
</head>
<body>

<?php
    // print header with name of profile being edited
    if ( isset($_SESSION["name"]) )
    {
        echo('<h1> Editing Profile for '.$fname."</h1>\n");
    }
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size= "60" id='fn' value="<?= $fname ?>"></p>
<p>Last Name:
<input type="text" name="last_name" size= "60" id='ln' value="<?= $lname ?>"></p>
<p>Email:
<input type="text" name="email" size= "60" id='ema'  value="<?= $email ?>"></p>
<p>Headline:
<input type="text" name="headline" size= "60" id='hd' value="<?= $headline ?>"></p>
<p>Summary:<br>
<textarea name="summary" rows="8" cols="69" id='sum' ><?= $summary ?></textarea></p>

<p><input type="submit" value="Save" onclick="return ValidateEdit();"></p>
</form>
<form  action="index.php">
<input type="submit" value="Cancel"> </p>
</form>

</body>
</html>