<?php 
require_once "pdo.php";
require_once "util.php";
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>RAHUL KISHORE GORAI - Automobiles Database</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Chuck Severance's Resume Registry</h1>

<?php 

	if ( isset($_SESSION['name']) ) {
		flashMessages();
		echo '<p><a href = "logout.php">Log Out</a></p>';
	} else {
		echo '<p><a href="login.php">Please log in</a></p>';
	}
?>
<?php 
	$sql = "SELECT first_name, last_name, headline, profile_id, summary FROM profile";
	$stmt = $pdo->query($sql);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if	(empty($row['first_name']) === false) {
	echo('<table border="1">
			<thead>
				<tr>
					<th>Name</th>
			    	<th>Headline</th>');
	}
	else{
		echo "No Rows Found";
	}
	if ( isset($_SESSION['name'])){
		if	(empty($row['first_name']) === false) {
		echo "<th>";	
	    echo "Action";
	    echo "</th>"; 
	}
	}
?>
	    </tr>
	</thead>
	<tbody>
<?php 
	$stmt = $pdo->query($sql);
	while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

		
	    echo "<tr><td>";
	    echo '<a href="view.php?profile_id='.$row['profile_id'].'">';
	    echo(htmlentities($row['first_name']." ".$row['last_name']));
	    echo '</a>';
	    echo("</td><td>");
	    echo(htmlentities($row['headline']));
	    echo("</td><td>");

	    if ( isset($_SESSION['name'])){
		    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
		    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
		    echo("</td>");
		}
	    echo "</tr>";
	}
	echo("</tbody>");
	echo("</table>");
// }
		if ( isset($_SESSION['name'])){
	    echo('<p>
	    	<a href="add.php">Add New Entry</a>
	    	</p>');

		}
 ?>
<p>
<b>Note:</b> Your implementation should retain data across multiple
logout/login sessions.  This sample implementation clears all its
data periodically - which you should not do in your implementation.
</p>
</div>
</body>
</body>

