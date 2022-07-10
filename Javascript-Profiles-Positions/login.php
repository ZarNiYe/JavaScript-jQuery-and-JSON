<?php
    require_once ('db.php');
    session_start();

    if(isset($_POST['cancel'])) {
        header("location: index.php");
        return;
    }
    $salt = "XyZzy12*_";
    $stored_hash = "1a52e17fa899cf40fb04cfc42e6352f1";

    if(isset($_POST['email']) && isset($_POST['pass'])) {
        if(strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
            $failure = "Username and password are required";
            $_SESSION['error'] = $failure;
            header("location: login.php");
            return;
        } elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
            $failure = "Email must have an at-sign(@)";
            $_SESSION['error'] = $failure;
            header("location: login.php");
            return;
        } else {
            $check = hash('md5', $salt.$_POST['pass']);
            $stored_hash = "1a52e17fa899cf40fb04cfc42e6352f1";
            $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
            $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            unset($_SESSION['name']);
            unset($_SESSION['user_id']);

            if($row !== false) {
                error_log("log in success". $_POST['email']);
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                header("location: index.php");
                return;
            } elseif (!isset($_POST['pass']) && $check != $stored_hash){
                error_log("Log in fail". $_POST['email']. "$check");
                $failure = "Incorrect password";
                $_SESSION['error'] = $failure;
                header("location: login.php");
                return;
            } else {
                error_log("Login fail ".$_POST['email']." $check");
                
                $failure = "Incorrect password";
                $_SESSION['error'] = $failure;

                header("Location: login.php");
                return;
            }
        }
    }
?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Zar Ni Ye d980b15d</title>
    <?php require_once 'bootstrap.php'; ?>
</head>
<body>
    <div class="container">
        <h1>Please Log In</h1>
        <?php
            if(isset($_SESSION['error'])) {
                echo '<p style = "color: red;">' . htmlentities($_SESSION['error']) . '</p>';
                unset($_SESSION['error']);
            }
        ?>
        <form method="POST">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" class="form-control"><br>
            <label for="id_1723">Password</label>
            <input type="text" name="pass" id="id_1723" class="form-control">
            <br>
            <input type="submit" value="Log In" class="btn btn-primary" onclick="return doValidate();">
            <a href="index.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>

    <script>
        function doValidate(){
            console.log("Validating.....");
            try{
                addr = document.getElementById("email").value;
                pw = document.getElementById("id_1723").value;

                if(addr == null || pw == null || addr == "" || pw == "") {
                    alert("Both fields must be filled out");
                    return false;
                }

                if(addr.indexOf("@") == -1) {
                    alert("Invalid email address");
                    return false;
                }
                return true;
            }
            catch(e) {
                return false;
            } 
            return true;
        }
    </script>
</body>
</html>