<?php

session_start();
require_once "pdo.php";

// if the 'cancel' button is pressed, then go back to log in page
if ( isset($_POST['Cancel'] ) )
{
    // Redirect the browser to game.php
    header("Location: logout.php");
    return;
}

if ( isset($_POST['email']) && isset($_POST['pass'])  )
{
    // Logout current user
    unset($_SESSION['name']);
    unset($_SESSION['user_id']);

    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 && preg_match('/\b@\b/',$_POST['email']) )
    {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    }
    elseif (strlen($_POST['email']) > 1 && !preg_match('/\b@\b/',$_POST['email']) )
    {
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;
    }
    elseif(strlen($_POST['email']) > 0 && strlen($_POST['pass']) > 0 && preg_match('/\b@\b/',$_POST['email']))
    {
         // check the stored hash for autograder
         $salt = 'XyZzy12*_'; // Pw is php123, email is umsi@umich.edu
         $check = hash('md5', $salt.$_POST['pass']); // calculate user input pw hash

         $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
         $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
         // if this is a valid row based on the password
         if ( $row !== false )
         {
           error_log("Login success ".$_POST['email']);

           $_SESSION['name'] = $row['name'];
           $_SESSION['user_id'] = $row['user_id'];
           $_SESSION["success"] = "Logged in.";
           // Redirect the browser to index.php
           header("Location: index.php");
           return;
         }
         else
         {
             error_log("Login fail ".$_POST['email']." $check");
             // update current session and user message
             $_SESSION["error"] = "Incorrect password.";

             // Redirect the browser to login.php
             header( 'Location: login.php' ) ;
             return;
         }
     }
}
// Fall through into the View
?>

<!DOCTYPE html>
<html>
<head>
<title>e11f40b8</title>
<script src="validations.js"></script>
</head>
<body>

<h1>Please Log In</h1>
<?php
    // print a red error message if not successful
    if ( isset($_SESSION["error"]) )
    {
        echo('<p style="color:red">'.htmlentities($_SESSION["error"])."</p>\n");
        unset($_SESSION["error"]);
    }
    if ( isset($_SESSION["success"]) )
    {
        echo('<p style="color:green">'.htmlentities($_SESSION["success"])."</p>\n");
        unset($_SESSION["success"]);
    }
?>

<form method="post">
<label for="email">User Name</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In" onclick="return validateLogIn();">
<input type="submit" name="Cancel" value="Cancel">
</form>
<p>
Enjoy making your profile more visible online
</p>

</body>