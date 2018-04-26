<?php

session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');
require_once('../control/dbUserFunctions.php');
require_once('../control/dbLibrary.php');
require_once('./messagepage.php');
db_connect();

		
if(isset($_SESSION['loggedin'])){ 
	$user = get_user($_POST['modifyUser']);
	$userStatus = get_status($user['UserID']);
	$userType = get_type_desc();
	$statusDesc = get_status_desc();
	$userBan = get_ban($user['UserID']);
	
	$ban=FALSE;
	if($userBan)
		$ban=TRUE;

	top_page();
	
	?>
<p>
	
	<form action="./updateUserAdmin.php" method="post">
		<input type="hidden" value="<?php echo $user['UserID'];?>" name="UserID">
		<input type="hidden" value="<?php echo $user['Type_Desc'];?>" name="old_Type_Desc">
		<input type="hidden" value="<?php echo $userStatus['Status_ID']; ?>" name="old_Status_ID">
		<input type="hidden" value="<?php echo $userBan['Ban_Reason_Id']; ?>" name="old_Ban">
		<input type="hidden" value="<?php echo $userBan['Expiry_Date']; ?>" name="old_Ban_Expiry_Date">
			<table style="width:150", align="center">
				
				<tr>
		 			<th>First Name</th>
		 			<th>Last Name</th>
		 			<th>UserName</th>
		 			<th>User Level</th>
		 			<th>User Status</th>
		 			<th>Ban User</th>
	 			</tr>
	 			
	 			<tr>
		 			<td><?php echo $user['First_Name']; ?></td>
		 			<td><?php echo $user['Last_Name']; ?></td>
		 			<td><?php echo $user['Username']; ?></td>
		 			<td><select name="Type_Desc" size="1">
			 				<option value="<?php echo $userType[0]['Type_Desc'];?>"<?php echo ($user['Type_Desc'] == "Administrator") ? 'selected' : "" ; ?>><?php echo $userType[0]['Type_Desc'];?></option>
			 				<option value="<?php echo $userType[1]['Type_Desc'];?>"<?php echo ($user['Type_Desc'] == "Moderator") ? 'selected' : "" ; ?>><?php echo $userType[1]['Type_Desc'];?></option>
			 				<option value="<?php echo $userType[2]['Type_Desc'];?>"<?php echo ($user['Type_Desc'] == "Member") ? 'selected' : "" ; ?>><?php echo $userType[2]['Type_Desc'];?></option>
		 				</select>
			 		</td>
			 		<td><select name="Status_ID" size="1">
			 				<option value="1"<?php echo ($userStatus['Status_ID'] == "1") ? 'selected' : "" ; ?>><?php echo $statusDesc[0]['Status_Desc'];?></option>
			 				<option value="2"<?php echo ($userStatus['Status_ID'] == "2") ? 'selected' : "" ; ?>><?php echo $statusDesc[1]['Status_Desc'];?></option>
			 				<option value="3"<?php echo ($userStatus['Status_ID'] == "3") ? 'selected' : "" ; ?>><?php echo $statusDesc[2]['Status_Desc'];?></option>
		 				</select>
			 		</td>
			 		<td><select name="banUser" size="1">
			 				<option value="yes"<?php if($ban) echo "selected";?>>Yes</option>
			 				<option value="no" <?php if(!$ban) echo "selected";?>>No</option>
			 			</select>
			 		</td>
	 			</tr>
			</table>
			
		<input type="submit" value="Update" name="Update">
	</form>
</p>
<?php


	
	bottom_page();
	return;
}

else{
	top_page();
	?>
	<p>You are not allowed to be here.</p>
	<form action="../index.php" form="post">
		<input type="submit" value="Return"/>
	</form>
	<?php
	bottom_page();
}
			
		
	

?>