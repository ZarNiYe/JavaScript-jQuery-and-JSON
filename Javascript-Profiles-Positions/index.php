<?php
    require_once ('db.php');
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Zar Ni Ye d980b15d</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
    <div class="container">
        <h1>Zar Ni Ye's Resume Registry</h1>
        <?php
            if(isset($_SESSION['error'])) {
                echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])) {
                echo '<p style = "color:green">' . $_SESSION['success'] . "</p>\n";
                unset($_SESSION['success']);
            }
        ?>

        <?php
            if(!isset($_SESSION['name'])) {
        ?>
        <p>
            <a href="login.php">Please log in</a>
        </p>
        <?php require_once 'all_data_view.php'; ?>
        <?php 
            } else {
                require_once 'all_data_view.php';
        ?>
        <a href="add.php" class="btn btn-success">Add New Entry</a>
        <br>
        <br>
        <a href="logout.php" class="btn btn-danger">Logout</a>
        <?php
            }
        ?>
    </div>
</body>
</html>