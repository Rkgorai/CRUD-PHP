<?php 
require_once "pdo.php";
// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

$mess = false; 
$fail = false;

if ( isset($_POST['make']) && isset($_POST['year']) 
     && isset($_POST['mileage'])) {

	if (strlen($_POST['make']) < 1) {
		$fail = "Make is required";
	}

	if (is_numeric($_POST['year']) &&  is_numeric($_POST['mileage'])){
		$mess = "Record Inserted";
	    $sql = "INSERT INTO autos (make, year, mileage) 
	              VALUES (:make, :year, :mileage)";
	    // echo("<pre>\n".$sql."\n</pre>\n");
	    
	    $stmt = $pdo->prepare($sql);
	    $stmt->execute(array(
	        ':make' => $_POST['make'],
	        ':year' => $_POST['year'],
	        ':mileage' => $_POST['mileage']));
	} else {

		$fail = "Mileage and year must be numeric";
	}
	
}

	$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
		if ( isset($_REQUEST['name']) ) {
		    echo htmlentities($_REQUEST['name']);
		    echo "\n";
		}
	?>
	</h1>
	<?php 
	if ($mess !== false) {
		echo('<p style="color: green;">'.htmlentities($mess)."</p>\n");
	}
	if ($fail !== false) {
		echo('<p style="color: red;">'.htmlentities($fail)."</p>\n");
	}
	?>
	<form method="post">
	<p>Make:
	<input type="text" name="make" size="60"></p>
	<p>Year:
	<input type="text" name="year"></p>
	<p>Mileage:
	<input type="text" name="mileage"></p>
	<input type="submit" value="Add">
	<input type="submit" name="logout" value="Logout">
	</form>

	<h2>Automobiles</h2>
	<ul>
	<p>
	<?php
	foreach ( $rows as $row ) {
    echo "<li>";
    echo(htmlentities($row['year'])); 
    echo(" ");
    echo(htmlentities($row['make']));
    echo(" / ");
    echo(htmlentities($row['mileage']));
    echo("\n");
	}
	?>
	</ul>

	
	</div>

</body>
</html>



<!-- <?php  ?> -->