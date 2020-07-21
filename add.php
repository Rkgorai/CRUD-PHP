<?php
require_once "pdo.php";
    session_start();
    if ( ! isset($_SESSION['user_id'])) {
    die('ACCESS DENIED');
    return;
	}

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
	}


if ( isset($_POST['first_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['last_name']) && isset($_POST['summary'])) {

	$_SESSION['first_name'] = $_POST['first_name'];
	$_SESSION['last_name'] = $_POST['last_name'];
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['headline'] = $_POST['headline'];
	$_SESSION['summary'] = $_POST['summary'];

	if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||strlen($_POST['email']) < 1 ||strlen($_POST['headline']) < 1 ||strlen($_POST['summary']) < 1 ) {
		$_SESSION["error"] = "All fields are required";
		header('Location: add.php');
    	return;
	}

	if (strpos($_POST['email'], '@') == true){

	    $sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
  					VALUES ( :uid, :fn, :ln, :em, :he, :su)";
	    // echo("<pre>\n".$sql."\n</pre>\n");

	    
	    $stmt = $pdo->prepare($sql);
	    $stmt->execute(array(
	    	':uid' => $_SESSION['user_id'],
	        ':fn' => $_SESSION['first_name'],
	        ':ln' => $_SESSION['last_name'],
	        ':em' => $_SESSION['email'],
	        ':he' => $_SESSION['headline'],
	    	':su' => $_SESSION['summary']));
	    $_SESSION['success'] = "Record added";
	    unset($_SESSION['first_name']);
	    unset($_SESSION['last_name']);
	    unset($_SESSION['email']);
	    unset($_SESSION['headline']);
	    unset($_SESSION['summary']);
		header("Location: index.php");
		return;
	} else {
			$_SESSION['error'] = "Email must have an at-sign (@)";
			header('Location: add.php');
    		return;
		}
	
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>RAHUL KISHORE GORAI</title>
	<?php require_once "bootstrap.php"; ?>
</head>
<body>
	<div class="container">
	<h1>Tracking Automobiles for 
	<?php
		if ( isset($_SESSION['name']) ) {
		    echo $_SESSION['name'];
		    echo "\n";
		}
	?>
	</h1>
	<?php 
	if (isset($_SESSION['error'])) {
		echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
		unset($_SESSION["error"]);
	}
	?>
	<form method="post">
	<p>First Name:
	<input type="text" name="first_name" size="60"/></p>
	<p>Last Name:
	<input type="text" name="last_name" size="60"/></p>
	<p>Email:
	<input type="text" name="email" size="30"/></p>
	<p>Headline:<br/>
	<input type="text" name="headline" size="80"/></p>
	<p>Summary:<br/>
	<textarea name="summary" rows="8" cols="80"></textarea>
	<p>
	<input type="submit" value="Add">
	<input type="submit" name="cancel" value="cancel">
	</p>
	</form>
	</div>

</body>
</html>