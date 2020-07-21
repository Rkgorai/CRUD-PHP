<?php 
require_once "pdo.php";
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>RAHUL KISHORE GORAI - Automobiles Database</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Welcome to Automobiles Database</h1>

<?php 

	if ( isset($_SESSION['name']) ) {
		// echo ("<h1>Tracking Autos for");
	 //    echo $_SESSION['name'];
	 //    echo "</h1>\n";
		if (isset($_SESSION['success'])) {
		echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
		unset($_SESSION['success']);
	}
	if (isset($_SESSION['error'])) {
		echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
		unset($_SESSION['error']);
	}

	// if (empty($row['make'])) {
	// 	echo "No rows found";
	// } else {
	
	echo('<table border="1">'."\n");
	echo("<thead>");
	    echo("<tr><th>");
	    echo "Make";
	    echo "</th><th>";
	    echo "Model";
	    echo "</th><th>";
	    echo "Year";
	    echo "</th><th>";
	    echo "Mileage";
	    echo "</th><th>";
	    echo "Action";
	    echo "</th></tr>";
	    echo("</thead>");
	    echo("<tbody>");
	$stmt = $pdo->query("SELECT make, model, year, mileage, autos_id FROM autos");
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

		
	    echo "<tr><td>";
	    echo(htmlentities($row['make']));
	    echo("</td><td>");
	    echo(htmlentities($row['model']));
	    echo("</td><td>");
	    echo(htmlentities($row['year']));
	    echo("</td><td>");
	    echo(htmlentities($row['mileage']));
	    echo("</td><td>");
	    echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
	    echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
	    echo("</td></tr>");
	    
	}
	echo("</tbody>");
	echo("</table>");
// }
	    echo('<p>
	    	<a href="add.php">Add New Entry</a> <br><br>
	    	<a href="logout.php">Logout</a>
	    	</p>');

	} else {
		
echo('<p><a href="login.php">Please log in</a></p>');
echo('<p>Attempt to <a href="add.php">add data</a> without logging in</p>');
	}

 ?>

</div>
</body>
</body>

