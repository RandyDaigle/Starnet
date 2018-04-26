<?php

session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');
require_once('../control/dbLibrary.php');
require_once('../control/dbUserFunctions.php');
require_once('./messagepage.php');

db_connect();

// make sure old password set first or return back to register page.
if(strcmp($_POST['oldPassword'], "") == 0){
	top_page();
	?>
	<p>Old password was not entered. Enter your password to change information.</p>
	<form action="register.php" method="post">
		<input type="submit" value="Return"/>
	</form>
	<?php
	bottom_page();
	return;
}

// set variables for add user function.
$First_Name = $_POST['First_Name'];
if(strcmp($First_Name, "") == 0){
	$First_Name = $_POST['oldFirst_Name'];
}

$Last_Name = $_POST['Last_Name'];
if(strcmp($Last_Name, "") == 0){
	$Last_Name = $_POST['oldLast_Name'];
}

$Email = $_POST['Email'];
if(strcmp($Email, "") == 0){
	$Email = $_POST['oldEmail'];
}

$Phone_Number = $_POST['Phone_Number'];
if(strcmp($Phone_Number, "") == 0){
	$Phone_Number = $_POST['oldPhone_Number'];
}

$Username = $_POST['Username'];
if(strcmp($Username, "") == 0){
	$Username = $_POST['oldUsername'];
}
$oldUsername = $_POST['oldUsername'];
$oldPassword = $_POST['oldPassword'];
$newPassword = $_POST['newPassword'];

// try to perform update on user information
if(update_user($oldPassword, $newPassword, $oldUsername, $Username, $Email, $First_Name, $Last_Name, $Phone_Number, $_POST['UserID'])){
	top_page();
	?>
	<p>User updated successfully! Click below to return to Update portal.</p>
	<form action="register.php" method="post">
		<input type="submit" value="Return"/>
	</form>
	<?php
	bottom_page();
	return;
}
else{
	top_page();
	?>
	<p>User Information has not been entered into the system. You have entered a username that already exists or your password did not match.<br/>
		Please try again. Thank you.
	</p>
	<form action="register.php" method="post">
		<input type="submit" value="Return"/>
	</form>
	<?php
	bottom_page();
	return;
}
			


	

?>