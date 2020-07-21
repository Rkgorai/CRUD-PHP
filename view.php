<?php
require_once "pdo.php";
    session_start();
    if ( ! isset($_SESSION["name"])) {
    die('Not logged in');
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
		if ( isset($_SESSION['name']) ) {
		    echo $_SESSION['name'];
		    echo "\n";
		}
	?>
	</h1>
	<?php 
	if (isset($_SESSION['success'])) {
		echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
		unset($_SESSION['success']);
	}
	?>

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
	</p>
	</ul>
	<p>
	<a href="add.php">Add New</a> |
	<a href="logout.php">Logout</a>
	</p>
	</div>

</body>
</html>