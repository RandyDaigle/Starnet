<?php

session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');
require_once('../control/dbLibrary.php');
require_once('../control/dbUserFunctions.php');
require_once('./messagepage.php');

echo print_r($_POST);
db_connect();

// make sure old password set first or return back to register page.
if(isset($_POST['Username']) && isset($_POST['Email']))
if(strcmp($_POST['Username'], "") == 0 && strcmp($_POST['Email'], "") == 0){
	top_page();
	?>
	<p>No values received. You must enter a value in either username or email to reset password.</p>
	<form action="register.php" method="post">
		<input type="submit" name="Forgot" value="Forgot Something?"/>
	</form>
	<?php
	bottom_page();
	return;
}

// try to perform update on user information
if(retrieve_password($_POST['Username'], $_POST['Email'])){
	top_page();
	?>
	<p>Password reset and emailed successfully! Click below to return to Update portal and check your email for your new password.</p>
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
	<p>Password has not been reset. Username or Email did not match database records.<br/>
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