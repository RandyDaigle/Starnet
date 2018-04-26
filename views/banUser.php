<?php

session_start();
require_once('../control/dbLibrary.php');
require_once('../control/dbUserFunctions.php');
require_once('./messagepage.php');

db_connect();

$adminUser= get_user_by_Username($_SESSION['loggedin']);
$user = get_user($_POST['UserID']);echo "<br/><br/>";

top_page();
if(banUser($_POST['UserID'], $_POST['Ban_Type'], $_POST['DatePicker'])){
	echo "<br/>". $user['First_Name'] . " " . $user['Last_Name'] . " has been banned ";
	echo $_POST['Ban_Type']==1 ? "permanently." : "until " . $_POST['DatePicker'] . ".";
	?>
	<br/>
	<form action="../index.php" method="post">
		<input type="submit" value="Return"/>
	</form>
	<?php
}
else{
	echo "Error banning user.";
	?>
	<br/>
	<form action="../index.php" method="post">
		<input type="submit" value="Return"/>
	</form>
	<?php
}
bottom_page();
