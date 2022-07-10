<?php
require_once "pdo.php";
session_start();

// print error message if no access granted
if (!isset($_SESSION['name']) )
{
      die('ACCESS DENIED: Please redirect your browser to the <a href ="logout.php">front page.</a>');
}

// check if all fields have been filed in
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))
{
    // check for missing data in any field
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1)
    {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    }

    // check if the email does not have an @ sign
    if (strlen($_POST['email']) > 1 && !preg_match('/\b@\b/',$_POST['email']) )
    {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: add.php");
        return;
    }

    $sql = "INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :lnm, :em, :he, :su)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':lnm' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );

    $_SESSION['success'] = 'Record Added';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html>
<head>
<title>e11f40b8</title>
</head>
<body>
<?php
    // print header with name of profile being edited
    if ( isset($_SESSION["name"]) )
    {
        echo('<h1> Adding Profile for '.htmlentities($_SESSION["name"])."</h1>\n");
    }
?>
<p>Please fill in the form below: </p>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size = "60"></p>
<p>Last Name:
<input type="text" name="last_name" size = "60"></p>
<p>Email:
<input type="text" name="email" size = "60"></p>
<p>Headline:
<input type="text" name="headline" size = "60"></p>
<p>Summary:<br>
<textarea name="summary" rows="8" cols="69"></textarea></p>

<p><input type="submit" value="Add"/></p>
</form>
<form  action="index.php">
<input type="submit" value="Cancel"/> </p>
</form>


</body>
</html>
