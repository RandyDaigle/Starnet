<?php

session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');
require_once('../control/dbLibrary.php');
require_once('../control/dbUserFunctions.php');
require_once('./messagepage.php');

db_connect();


$adminUser= get_user_by_Username($_SESSION['loggedin']);

// make sure old password set first or return back to register page.
if($adminUser['Type_Desc'] == "Member"){
	top_page();
	?>
	<p>You do not have proper privileges to modify users in this fashion.</p>
	<form action="../index.php" method="post">
		<input type="submit" value="Return"/>
	</form>
	<?php
	bottom_page();
	return;
}

$statusChanged=FALSE;
$typeChanged=FALSE;
$banRemoved=FALSE;


if(strcmp($_POST['old_Status_ID'], $_POST['Status_ID'])!=0){
	$statusChanged = updateUserStatus( $_POST['UserID'] , $_POST['Status_ID']);
}

if(strcmp($_POST['old_Type_Desc'], $_POST['Type_Desc']) != 0){
	$typeChanged = updateUserType($_POST['UserID'] , $_POST['Type_Desc'], $adminUser['User_TypeID']);
}

if(strcmp($_POST['banUser'], "no") == 0 && strcmp($_POST['old_Ban'], "")!= 0){
	$banRemoved = unBanUser($_POST['UserID']);
}

if(strcmp($_POST['banUser'], "yes") == 0){
	top_page();
	echo "<br/>User will be banned. Please enter if permanent or temporary. All other changes will be overridden.";
		?>
		<form action="banUser.php" method="post">
			<input type="hidden" value="<?php echo $_POST['UserID'];?>" name="UserID">
			<select name="Ban_Type" size="1">
			 	<option value="1">Permanent</option>
			 	<option value="2">Temporary</option>
			 </select>
			 <p>If Temporary, please enter a date of suspension end:<input type="text" id="datepicker" name="DatePicker"></p>
			 <input type="submit" name="Submit">
		</form>
		<?php
	bottom_page();
	
}
else{

top_page();
?>
<br/>
<?php
	if($statusChanged)
		echo "User updated successfully! User Status changed.<br/>";
	if($typeChanged)
		echo "User updated successfully! User Type changed.<br/>";
	if($banRemoved)
		echo "User ban has been removed.<br/>";
	if(!$statusChanged && !$typeChanged && !$banRemoved)
		echo "No modifications have been made.";
	?>
	<br/>
	<form action="../index.php" method="post">
		<input type="submit" value="Return"/>
	</form>
	<?php
	
bottom_page();
}
?>
