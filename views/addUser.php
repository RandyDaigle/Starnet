<?php

session_start();
require_once('../control/dbUserFunctions.php');
require_once('../control/dbLibrary.php');
require_once('./messagepage.php');
db_connect();

// will work if phone not set.
if(isset($_POST['First_Name']) && (strcmp($_POST['First_Name'], "") != 0) &&
	isset($_POST['Last_Name']) && (strcmp($_POST['Last_Name'], "") != 0) &&
	isset($_POST['Username']) && (strcmp($_POST['Username'], "") != 0) &&
	isset($_POST['Password']) && (strcmp($_POST['Password'], "") != 0) &&
	isset($_POST['Email']) && (strcmp($_POST['Email'], "") != 0)){
		
		// Assign post values to variables.
		$First_Name = $_POST['First_Name'];
		$Last_Name = $_POST['Last_Name'];
		$Username = $_POST['Username'];
		$Phone_Number = $_POST['Phone_Number'];
		$Password = $_POST['Password'];
		$Email = $_POST['Email'];

		
		if(add_user($Username, $Password, $Email, $First_Name, $Last_Name, $Phone_Number)){
			top_page();
			//echo print_r($_SESSION);
			//$user = get_user_by_Username($_SESSION['loggedin']);
			//echo print_r($user);
			exit();
			?>
			<p>User added successfully! Click below to return to management portal.</p>
			<form action="register.php" form="post">
				<input type="submit" value="Return"/>
			</form>
			<?php
			bottom_page();
			return;
		}
	
		else{
			top_page();
			?>
			<p>UserID has already been entered into the system or this entry is not allowed.<br/>
				Please enter a new value in UserID field. Thank you.
			</p>
			<form action="register.php" form="post">
				<input type="submit" value="Return"/>
			</form>
			<?php
			bottom_page();
		}
			
		
}
else{
		top_page();
		?>
		<p>All fields must be completed in order to enter this data into the database.<br/>
			Please re-enter your information and fill out all fields.
		</p>
		<form action="register.php" form="post">
		<input type="submit" value="Return"/>
		</form>
		
		<?php
		bottom_page();

}
	

?>