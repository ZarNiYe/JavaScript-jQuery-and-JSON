<?php
require_once "pdo.php";
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>e11f40b8</title>
</head>
<body>

<h1>Zar Ni Ye's Resume Registry</h1>

<div>
<?php
// print error messages for any errors or record updates
if ( isset($_SESSION['error']) )
{
    echo '<p style="color:red">'.htmlentities($_SESSION['error'])."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) )
{
    echo '<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n";
    unset($_SESSION['success']);
}

if ( isset($_SESSION["name"]) )
{
    echo '<p style="color:green"> Welcome: '.$_SESSION['name']."</p>\n";
}

?>
</div>

<div>
<?php

if ( isset($_SESSION['name']) )
{
    echo('<table border="1">'."\n");
    echo('<tr><th>Name</th><th>Headline</th><th id = "toHide" >Action</th></tr>');
    $stmt = $pdo->query("SELECT * FROM Profile");
    // pull SQL data to put into HTML table
    // print_r($stmt->fetch(PDO::FETCH_ASSOC));
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $profname = htmlentities($row['first_name'])." ".htmlentities($row['last_name']);
        $headline = htmlentities($row['headline']);
        $profileID = htmlentities($row['profile_id']);

        echo "<tr><td>";
        echo('<a href="view.php?profile_id='.$profileID.'">'.$profname.'</a>');
        echo("</td><td>");
        echo($headline);
        echo("</td><td  id = 'toHide' >");
        echo('<a href="edit.php?profile_id='.$profileID.'">Edit</a> / ');
        echo('<a href="delete.php?profile_id='.$profileID.'">Delete</a>');
        echo("</td></tr>\n");
    }
    echo '<div>
    <p><a href="logout.php">Logout</a></p>
    <p><a href="add.php">Add New Entry</a></p>
    </div>';
}
else
{
  echo('<table border="1">'."\n");
  echo('<tr><th>Name</th><th>Headline</th></tr>');
  $stmt = $pdo->query("SELECT * FROM Profile");
  // pull SQL data to put into HTML table
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $profname = htmlentities($row['first_name'])." ".htmlentities($row['last_name']);
      $headline = htmlentities($row['headline']);
      $profileID = htmlentities($row['profile_id']);

      echo "<tr><td>";
      echo('<a href="view.php?profile_id='.$profileID.'">'.$profname.'</a>');
      echo("</td><td>");
      echo($headline);
      echo("</td></tr>\n");
  }
    echo '<div>
    <p><a href="login.php">Please log in</a></p>
    </div>';
}

?>

</div>


</body>
</html>