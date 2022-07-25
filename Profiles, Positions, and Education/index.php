<?php
    require_once ("db.php");
    session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Zar Ni Ye 09c1e450</title>
        <?php require_once ('bootstrap.php'); ?>
    </head>
    <body>
        <div class="container">
            <h1>Zar Ni Ye's Resume Registry </h1>
            <?php
                // flash messsage
                if(isset($_SESSION['error'])) {
                    echo '<p style = "color: red;">' . $_SESSION['error'] . "</p>";
                    unset ($_SESSION['error']);
                }
                if(isset($_SESSION['success'])) {
                    echo '<p style = "color: green;">' . $_SESSION['success'] . "</p>";
                    unset ($_SESSION['success']);
                }
            ?>

            <?php
                if(!isset($_SESSION['name'])){
            ?>
                    <p>
                        <a href="login.php">Please log in</a>
                    </p>

                    <?php require_once "all_data_view.php"; ?>
            <?php
                } else {
                    require_once "all_data_view.php";
            ?>
                <a href="add.php">Add New Entry</a>
                <br>
                <br>
                <a href="logout.php">Logout</a>
            <?php
                }
            ?>
        </div>
    </body>
</html>