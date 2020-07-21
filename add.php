<?php
require_once "pdo.php";
    session_start();
    if ( ! isset($_SESSION['name'])) {
    die('ACCESS DENIED');
	}

	if ( isset($_POST['Cancel']) ) {
    header('Location: index.php');
    return;

}


if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) && isset($_POST['model'])) {

	$_SESSION['make'] = $_POST['make'];
	$_SESSION['model'] = $_POST['model'];
	$_SESSION['year'] = $_POST['year'];
	$_SESSION['mileage'] = $_POST['mileage'];

	if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 ||strlen($_POST['year']) < 1 ||strlen($_POST['mileage']) < 1 ) {
		$_SESSION["error"] = "All fields are required";
		header('Location: add.php');
    	return;
	}

	if (is_numeric($_SESSION['year']) &&  is_numeric($_SESSION['mileage'])){
	    $sql = "INSERT INTO autos (make, model, year, mileage) 
	              VALUES (:make, :model, :year, :mileage)";
	    // echo("<pre>\n".$sql."\n</pre>\n");

	    
	    $stmt = $pdo->prepare($sql);
	    $stmt->execute(array(
	        ':make' => $_SESSION['make'],
	        ':model' => $_SESSION['model'],
	        ':year' => $_SESSION['year'],
	        ':mileage' => $_SESSION['mileage']));
	    $_SESSION['success'] = "Record added";
	    unset($_SESSION['make']);
	    unset($_SESSION['model']);
	    unset($_SESSION['year']);
	    unset($_SESSION['mileage']);
		header("Location: index.php");
		return;
	} else {
		if (is_numeric($_SESSION['year']) === 'false'){
			$_SESSION['error'] = "Year must be numeric";
			header('Location: add.php');
    		return;
		} else {
			$_SESSION['error'] = "Mileage must be numeric";
			header('Location: add.php');
	    	return;
    	}
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
	<p>Make:
	<input type="text" name="make" size="40"></p>
	<p>Model:
	<input type="text" name="model" size="40"></p>
	<p>Year:
	<input type="text" name="year" size="10"></p>
	<p>Mileage:
	<input type="text" name="mileage" size="10"></p>
	<input type="submit" name="Add" value="Add">
	<input type="submit" name="Cancel" value="Cancel">
	</form>

	
	</div>

</body>
</html>