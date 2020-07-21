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

    // Insert the position entries

        $rank = 1;
        for($i=1; $i<=9; $i++) {
          if ( ! isset($_POST['year'.$i]) ) continue;
          if ( ! isset($_POST['desc'.$i]) ) continue;

          $year = $_POST['year'.$i];
          $desc = $_POST['desc'.$i];

          $stmt = $pdo->prepare('INSERT INTO position
            (profile_id, rank, year, description)
            VALUES ( :pid, :rank, :year, :desc)');

          $stmt->execute(array(
          ':pid' => $_REQUEST['profile_id'],
          ':rank' => $rank,
          ':year' => $year,
          ':desc' => $desc)
          );

          $rank++;

        }
    

    $_SESSION['success'] = 'Profile updated';
    header( 'Location: index.php' ) ;
    return;
}

$positions = loadPos($pdo, $_REQUEST['profile_id']);
// print_r($positions);

$pos = sizeof($positions);

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
    Position: <input type="submit" id="addPos" value="+">
    <div id="position_fields">
        <?php 
        for ($i=0; $i < $pos; $i++) { 
            $j = $i+1;
            $ye = $positions[$i]['year'];
            $de = $positions[$i]['description'];
            echo('<div id = "position'.$j.'">');
            echo('<p>Year: <input type="text" name="year'.$j.'" value="'.$ye.'">');
            echo(' <input type="button" value="-" onclick="$(\'#position'.$j.'\').remove();return false;">');
            echo '</p>';
            echo('<textarea name="desc'.$j.'" rows="8" cols="80">'.$de.'</textarea></div>');
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
});
</script>
</body>
</html>