<?php
require_once "pdo.php";
    session_start();
    if ( ! isset($_SESSION['name'])) {
    die('Not logged in');
	}

	if ( isset($_POST['Cancel']) ) {
    header('Location: view.php');
    return;

}


if ( isset($_POST['make']) && isset($_POST['year']) 
     && isset($_POST['mileage'] )) {

	$_SESSION['make'] = $_POST['make'];
	$_SESSION['year'] = $_POST['year'];
	$_SESSION['mileage'] = $_POST['mileage'];

	if (strlen($_POST['make']) < 1) {
		$_SESSION["error"] = "Make is required";
		header('Location: add.php');
    	return;
	}

	if (is_numeric($_SESSION['year']) &&  is_numeric($_SESSION['mileage'])){
	    $sql = "INSERT INTO autos (make, year, mileage) 
	              VALUES (:make, :year, :mileage)";
	    // echo("<pre>\n".$sql."\n</pre>\n");

	    
	    $stmt = $pdo->prepare($sql);
	    $stmt->execute(array(
	        ':make' => $_SESSION['make'],
	        ':year' => $_SESSION['year'],
	        ':mileage' => $_SESSION['mileage']));
	    $_SESSION['success'] = "Record inserted";
	    unset($_SESSION['make']);
	    unset($_SESSION['year']);
	    unset($_SESSION['mileage']);
		header("Location: view.php");
		return;
	} else {
		$_SESSION['error'] = "Mileage and year must be numeric";
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
	<h1>Tracking Autos for 
	<?php
		if ( isset($_SESSION['name']) ) {
		    echo $_SESSION['name'];
		    echo "\n";
		}
	?>
	</h1>
	<?php 
	// if ($mess !== false) {
	// 	echo('<p style="color: green;">'.htmlentities($mess)."</p>\n");
	// }
	if (isset($_SESSION['error'])) {
		echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
		unset($_SESSION["error"]);
	}
	?>
	<form method="post">
	<p>Make:
	<input type="text" name="make" size="60"></p>
	<p>Year:
	<input type="text" name="year"></p>
	<p>Mileage:
	<input type="text" name="mileage"></p>
	<input type="submit" name="Add" value="Add">
	<input type="submit" name="Cancel" value="Cancel">
	</form>

	
	</div>

</body>
</html>