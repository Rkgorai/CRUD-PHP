<?php 
require_once "pdo.php";
require_once "util.php";
session_start();
 ?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Viewing</title>
 	<?php require_once "head.php"; ?>
 </head>
 <body>
 <div class="container">
 <h1>Profile Information</h1>
<?php 
flashMessages();
$i = 0;
$stmt = $pdo->prepare("SELECT * FROM profile  where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo '<p>First Name: '.$row['first_name'];
echo '</p><p>Last Name: '.$row['last_name'];
echo '</p><p>Email: '.$row['email'];
echo '</p><p>Headline: <br>'.$row['headline'];
echo '</p><p>Summary: <br>'.$row['summary'];
echo "</p><p>";
$stmt = $pdo->prepare('SELECT * FROM position where profile_id = :abc');
$stmt->execute(array(":abc" => $_GET['profile_id']));
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
if(empty($row['year']) === false){
	if ($i<1) {
		# code...
		echo "Positions :<ul>";
		$i++;
	}
	
	
}
echo "<li>";
echo $row['year'].' '.$row['description'];
echo "</li>";
}
echo "</ul></p>";
 ?>
<a href="index.php">Done</a>
 </div>
 </body>
 </html>