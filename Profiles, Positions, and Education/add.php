<?php
    session_start();
    if (!isset($_SESSION['name'])){
        die ("ACCESS DEINED");
    }

    require_once ('db.php');
    
    if (isset($_POST['cancel'])){
        header("Location: index.php");
        return;
    }

    if (isset($_POST['first_name']) &&
        isset($_POST['last_name']) &&
        isset($_POST['email']) &&
        isset($_POST['headline']) &&
        isset($_POST['summary'])) {
            if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) <1 || strlen($_POST['headline']) <1 || strlen($_POST['summary']) < 1) {
                $_SESSION['error'] = "All fields are required";
                header("location: add.php");
                return;
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email address must contain @";
                header("location:add.php");
                return;
            }

            for($i=1; $i<= 9; $i++){
                if (!isset($_POST['year' . $i])) continue;
                if (!isset($_POST['desc' . $i])) continue;
                $year = $_POST['year' . $i];
                $desc = $_POST['desc' . $i];

                if (strlen($year) == 0 || strlen($desc) == 0) {
                    $_SESSION['error'] = "All fields are required";
                    header("location:add.pph");
                    return;
                }

                if(!is_numeric($year)) {
                    $_SESSION['error'] = "Year must be numeric";
                    header("location:add.php");
                    return;
                }
            }

            for( $i=1; $i<=9; $i++){
                if ( !isset($_POST['edu_year' . $i])) continue;
                if ( !isset($_POST['edu_school' . $i])) continue;

                $year = $_POST['edu_year' . $i];
                $school = $_POST['edu_school' . $i];

                if (strlen($year) == 0 || strlen($school) == 0){
                    $_SESSION['error'] = "All fields are required";
                    header("location:add.php");
                    return;
                }

                if( ! is_numeric($year)) {
                    $_SESSION['error'] = "Year must be numeric";
                    header("Location: add.php");
                    return;
                }
            }

            $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');

            $profInsrt = $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary']
            ));

            $profileId = $pdo->lastInsertId();

            $rank = 1;
            for ($i=1; $i<=9; $i++) {
                if( !isset($_POST['year' . $i])) continue;
                if( !isset($_POST['desc' . $i])) continue;

                $year = $_POST['year' . $i];
                $desc = $_POST['desc' . $i];

                $stmt = $pdo->prepare('INSERT INTO `Position` (`position_id`, `rank`, `year`, `description`) VALUES (:pid, :rank, :year, :desc)');
                
                $posInsrt = $stmt->execute(array(
                    ':pid' => $profileId,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc
                ));
                $rank++;
            }

            $irank = 1;
            for($i=1; $i <= 9; $i++) {
                if  ( !isset($_POST['edu_year']))continue;
                if  ( !isset($_POST['edu_school']))continue;

                $year = $_POST['year' . $i];
                $schoolName = $_POST['edu_school' . $i];

                $istmt = $pdo->prepare("SELECT * FROM Institution WHERE name =  :insname");

                $istmt->execute([
                    'insname' => $schoolName
                ]);

                $irow = $istmt->fetch(PDO::FETCH_ASSOC);

                if($irow === false) {
                    $stmt = $pdo->prepare(
                        'INSERT INTO Institution (name)
                        Values (:iid)'
                    );
                    $stmt->execute([
                        ':iid' => $schoolName
                    ]);

                    $instututionId = $pdo->lastInsertId();
                } else {
                    $instututionId = $irow['institution_id'];
                }

                $stmt = $pdo->prepare(
                    'INSERT INTO Education (profile_id, institution_id, rank, year) VALUES (:pid, :iid, :rnk, :yr)'
                );

                $posInsrt = $stmt->execute([
                    ':pid' => $profileId,
                    ':iid' => $instututionId,
                    ':rnk' => $irank,
                    ':yr' => $year
                ]);
                $irank++;
            }
            
            $_SESSION['success'] = "Profile added";
            header("location:index.php");
            return;
        }
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Zar Ni Ye 09c1e450</title>
        <?php require_once "bootstrap.php"; ?>
    </head>
    <body>
        <div class="container">
            <h1>Adding Profile for <?= $_SESSION['name']; ?></h1>
            <br>
            <?php
                if (isset($_SESSION['error'])) {
                    echo ('<p style ="color:red">' . htmlentities($_SESSION['error']) . "</p>\n");
                    unset($_SESSION['error']);
                }
            ?>
            <form method="POST">
                <label>First Name:
                    <input type="text" name="first_name" size="60" class="form-control"/>
                </label>
                <br><br>
                <label>Last Name:
                    <input type="text" name="last_name" size="60" class="form-control"/>
                </label>
                <br><br>
                <label>Email:
                    <input type="text" name="email" size="60" class="form-control"/>
                </label>
                <br><br>
                <label>Headline:
                    <input type="text" name="headline" size="60" class="form-control"/>
                </label>
                <br><br>
                <label>Summary:
                    <textarea name="summary" cols="80" rows="8"></textarea>
                </label>
                <br><br>
                <label>Education:
                    <input type="submit" class="btn btn-primary" id="addEdu" value="+">
                </label>
                <br><br>
                <div id="education_fields"></div>
                <br><br>

                <label>Position:
                    <input type="submit" class="btn btn-primary" id="addPos" value="+">
                </label>
                <br><br>
                <div id="position_fields"></div>
                <br><br>

                <input type="submit" value="Add" class="btn btn-success">
                <input type="submit" name="cancel" value="Cancel" class="btn btn-danger">
            </form>
            <script>
                countPos = 0;
                countEdu = 0;

                // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
                $(document).ready(function(){
                    window.console && console.log('Document ready called');

                    // adding position
                    $('#addPos').click(function(event){
                        // http://api.jquery.com/event.preventdefault/
                        event.preventDefault();
                        if ( countPos >= 9 ) {
                            alert("Maximum of nine position entries exceeded");
                            return;
                        }
                        countPos++;
                        window.console && console.log("Adding position "+countPos);
                        $('#position_fields').append(
                            '<div id="position'+countPos+'"> \
                                <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                                <input type="button" value="-" \
                                    onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                                <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                                <br><br> \
                            </div>');
                    });

                    // adding education
                    $('#addEdu').click(function(event){
                        // http://api.jquery.com/event.preventdefault/
                        event.preventDefault();
                        if ( countEdu >= 9 ) {
                            alert("Maximum of nine education entries exceeded");
                            return;
                        }
                        countEdu++;

                        window.console && console.log("Adding education "+countEdu);
                        $('#education_fields').append(
                            '<div id="edu' + countEdu + '"> \
                                <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
                                <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"><br>\
                                <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />\
                            </p></div>'
                        );

                        $( ".school" ).autocomplete({
                            source: "school.php"
                        });
                    });                                

                });
                
            </script>

    </body>
</html>
