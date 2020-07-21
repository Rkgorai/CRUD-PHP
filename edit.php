<?php
require_once "pdo.php";
require_once "util.php";
session_start();
if ( ! isset($_SESSION['user_id'])) {
    die('ACCESS DENIED');
    return;
}

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
if ( isset($_POST['first_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['last_name']) && isset($_POST['summary']) && isset($_REQUEST['profile_id'])) {

    // Data validation
    $msg = validateProfile();
    if (is_string($msg)){
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }

    $msg = validatePos();
    if (is_string($msg)){
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }

    $msg = validateEdu();
    if (is_string($msg)){
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }


    $sql = ('UPDATE profile SET
                user_id = :uid,
                first_name = :fn,
                last_name = :ln,
                email = :em, 
                headline = :he,
                summary = :su WHERE profile_id = :pid');
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':he' => $_POST['headline'],
        ':em' => $_POST['email'],
        ':su' => $_POST['summary'],
        ':pid' => $_REQUEST['profile_id']));


    //Clear Out the old Positions entries
    $stmt = $pdo->prepare('DELETE FROM position
        WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id'])
    );

    $stmt = $pdo->prepare('DELETE FROM education
        WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id'])
    );

    // Insert the position entries
    insertPositions($pdo, $_REQUEST['profile_id']);
    insertEducations($pdo, $_REQUEST['profile_id']);
    


    $_SESSION['success'] = 'Profile updated';
    header( 'Location: index.php' ) ;
    return;
}

//Load Up POsitions and Educations
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$educations = loadEdu($pdo, $_REQUEST['profile_id']);
// print_r($positions);
//print_r($educations);

$pos = sizeof($positions);
$edu = sizeof($educations);

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Flash pattern


$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$headline = htmlentities($row['headline']);
$email = htmlentities($row['email']);
$summary = htmlentities($row['summary']);
$profile_id = htmlentities($_REQUEST['profile_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit</title>
    <?php require_once "head.php"; ?>
</head>
<body>
    <div class="container">
<h1>Editing Profile for
<?php
    if ( isset($_SESSION['name']) ) {
        echo $_SESSION['name'];
        echo "\n";
    }
?>
</h1>
<?php flashMessages(); ?>

<form method="post" action="edit.php">
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>"/>
    <p>First Name:
    <input type="text" name="first_name" value="<?= $first_name ?>" size="60"/></p>
    <p>Last Name:
    <input type="text" name="last_name" value="<?= $last_name ?>" size="60"/></p>
    <p>Email:
    <input type="text" name="email" value="<?= $email ?>" size="30"/></p>
    <p>Headline:<br/>
    <input type="text" name="headline" value="<?= $headline ?>" size="80"/></p>
    <p>Summary:<br/>
    <textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea></p>
    </p>
    <p>
    Education: <input type="submit" id="addEdu" value="+">
    <div id="edu_fields">
        <?php 
        for ($i=1; $i <= $edu; $i++) { 
            // $j = $i+1;
            $ye = $educations[$i-1]['year'];
            $de = $educations[$i-1]['name'];
            echo('<div id = "edu'.$i.'">');
            echo('<p>Year: <input type="text" name="edu_year'.$i.'" value="'.$ye.'">');
            echo(' <input type="button" value="-" onclick="$(\'#edu'.$i.'\').remove();return false;">');
            echo '</p>';
            echo ('<p>School: <input type="text" size="80" name="edu_school'.$i.'" class="school" value="'.$de.'" />\
            </p></div>');
        }
         ?>
    </div>
    </p>
    <p>
    Position: <input type="submit" id="addPos" value="+">
    <div id="position_fields">
        <?php 
        for ($i=1; $i <= $pos; $i++) { 
            // $j = $i+1;
            $ye = $positions[$i-1]['year'];
            $de = $positions[$i-1]['description'];
            echo('<div id = "position'.$i.'">');
            echo('<p>Year: <input type="text" name="year'.$i.'" value="'.$ye.'">');
            echo(' <input type="button" value="-" onclick="$(\'#position'.$i.'\').remove();return false;">');
            echo '</p>';
            echo('<textarea name="desc'.$i.'" rows="8" cols="80">'.$de.'</textarea></div>');
        }
         ?>

    </div>
    </p>
    <p>
    <input type="submit" value="Save"/>
    <input type="submit" name="cancel" value="cancel">
    </p>
</form>
</div>
<script>
    
countPos = <?= $pos ?>;
countEdu = <?= $pos ?>;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
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
            </div>');
    });

        $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );

        $('.school').autocomplete({
            source: "school.php"
        });
    });
});
</script>
</body>
</html>