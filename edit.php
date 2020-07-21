<?php
require_once "pdo.php";
session_start();

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
if ( isset($_POST['first_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['last_name']) && isset($_POST['summary']) && isset($_POST['profile_id'])) {

    // Data validation

    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||strlen($_POST['email']) < 1 ||strlen($_POST['headline']) < 1 ||strlen($_POST['summary']) < 1 ) {
        $_SESSION["error"] = "All fields are required";
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    if (strpos($_POST['email'], '@') == false){
        $_SESSION['error'] = 'Email must have an at-sign (@)';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    $sql = ('UPDATE profile SET first_name = :fn,
                last_name = :ln,
                email = :em, 
                headline = :he,
                summary = :su WHERE profile_id = :pid');
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        // ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':he' => $_POST['headline'],
        ':em' => $_POST['email'],
        ':su' => $_POST['summary'],
        ':pid' => $_POST['profile_id']));

    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: first_name sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
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

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$headline = htmlentities($row['headline']);
$email = htmlentities($row['email']);
$summary = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
?>
<h1>Editing Automobile</h1>
<form method="post">
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
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
    <p>
    <input type="submit" value="Save"/>
    <input type="submit" name="cancel" value="cancel">
    </p>
</form>