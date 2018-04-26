<?php

require_once('../control/dbUserFunctions.php');
require_once('../control/dbLibrary.php');
require_once('./blobUpload.php');
/* 
	views in Main body of starnet screen.
	
*/
	
function registerForm(){
?>
<p>
	User Registration, Enter your information.
	<form action="./addUser.php" method="post">
			<table style="width:150", align="center">
	 			<tr>
		 			<th>Value</th>
		 			<th>Your Info:</th>
	 			</tr>
	 			<tr>
		 			<th>First Name<font color="red">*</font></th>
		 			<th><input type="text" name="First_Name"></th>
	 			</tr>
	 			<tr>
	 				<th>Last Name<font color="red">*</font></th>
	 				<th><input type="text" name="Last_Name"></th>
		 		</tr>
	 			<tr>
		 			<th>Email<font color="red">*</font></th>
		 			<th><input type="text" name="Email"></th>
		 		</tr>
	 			<tr>
		 			<th>Phone</th>
		 			<th><input type="text" name="Phone_Number"></th>
		 		</tr>
	 			<tr>
		 			<th>UserName<font color="red">*</font></th>
		 			<th><input type="text" name="Username"></th>
		 		</tr>
	 			<tr>
		 			<th>Password<font color="red">*</font></th>
		 			<th><input type="password" name="Password"></th>
		 		</tr>
			</table>
		<input type="submit" value="Register" name="Register">
	</form>
</p>
<?php
}

function adminListUsers(){
	$users = listUsers();	
	//print_r($users);
?>
<p>
	<table style="width:150", align="center">
		<tr>
 			<th>First Name</th>
 			<th>Last Name</th>
 			<th>UserName</th>
 			<th>Level</th>
 			<th>Modify</th>
		</tr>
		<form action="./userAdmin.php" method="post">
			<?php foreach($users as $User){?>
			<tr>
		 		<td><?php echo $User['First_Name']; ?></td>
		 		<td><?php echo $User['Last_Name']; ?></td>
		 		<td><?php echo $User['Username']; ?></td>
		 		<td><?php echo $User['Type_Desc']; ?></td>
		 		<td><button name="modifyUser" type="submit" value="<?php echo $User['UserID'];?>" >Modify User</button></td>	 
			</tr>
		<?php
	 	}	//foreach loop close
	 	?>
	 	</form>
	</table>
</p>


<?php

}


function forgotYourCredentials(){
?>
<p>
	Please enter your username or email to reset your password.
	<form action="./retrieveUser.php" method="post">
		Username:<input type="text" name="Username" id="Username">
		Email:<input type="email" name="Email" id="Email">
		<input type="submit" value="Reset Password">
	</form>
</p>
<?php

}

function successfulRegister(){
	
//echo print_r($_SESSION);
$user = get_user_by_Username($_SESSION['loggedin']);
//echo print_r($user);

?>
<p>
	Update UserInfo, Enter your information.
	<p><strong>You must re-enter your old password to change information<font color="red">*</font></strong></p>
	<form action="./updateUser.php" method="post">
			<table style="width:150", align="center">
	 			<tr>
		 			<th>Value</th>
		 			<th>Your Info:</th>
		 			<th>New Info:</th>
	 			</tr>
	 			<tr>
		 			<th>First Name</th>
		 			<th><?php echo $user['First_Name']; ?></th>
		 			<th><input type="text" name="First_Name"></th>
	 			</tr>
	 			<tr>
	 				<th>Last Name</th>
	 				<th><?php echo $user['Last_Name']; ?></th>
	 				<th><input type="text" name="Last_Name"></th>
		 		</tr>
	 			<tr>
		 			<th>Email</th>
		 			<th><?php echo $user['Email']; ?></th>
		 			<th><input type="text" name="Email"></th>
		 		</tr>
	 			<tr>
		 			<th>Phone</th>
		 			<th><?php echo $user['Phone_Number']; ?></th>
		 			<th><input type="text" name="Phone_Number"></th>
		 		</tr>
	 			<tr>
		 			<th>UserName</th>
		 			<th><?php echo $user['Username']; ?></th>
		 			<th><input type="text" name="Username"></th>
		 		</tr>
	 			<tr>
		 			<th>Password(old/new)<font color="red">*</font></th>
		 			<th><input type="password" name="oldPassword"></th>
		 			<th><input type="password" name="newPassword"></th>
		 		</tr>
			</table>
			<!-- pass old variables for checking information.  in updateUser.php file.-->
			<input type="hidden" value="<?php echo $user['UserID'];?>" name="UserID">
			<input type="hidden" value="<?php echo $user['First_Name'];?>" name="oldFirst_Name">
			<input type="hidden" value="<?php echo $user['Last_Name'];?>" name="oldLast_Name">
			<input type="hidden" value="<?php echo $user['Email'];?>" name="oldEmail">
			<input type="hidden" value="<?php echo $user['Phone_Number'];?>" name="oldPhone_Number">
			<input type="hidden" value="<?php echo $user['Username'];?>" name="oldUsername">
		<input type="submit" value="Update" name="Update">
	</form>
	


</p>
<?php
	if(($FileNumber=checkUserImage($user['UserID']))!=0){
		$picture = selectUserImage($FileNumber['FilesID']);
		?>
		<img src="data:image/png;base64,<?php echo base64_encode($picture["data"]); ?>" height="100" width="100" /><br/>
		<p style="text-align: center">
			Update your file picture!
			<form action="blobUpload.php" method="post" enctype="multipart/form-data">
				Select image to upload:
				<input type="file" name="fileToUpload" id="fileToUpload">
				<input type="hidden" name="OldFilesID" value="<?php echo $FileNumber['FilesID']; ?>">
				<input type="hidden" name="UserID" value="<?php echo $user['UserID']; ?>">
				<input type="submit" value="Upload Image" name="submit">
			</form>
		</p>
		<?php
	}
	else{
		?>
		<p style="text-align: center">
			Add a picture to your file!
			<form action="blobUpload.php" method="post" enctype="multipart/form-data">
				Select image to upload:
				<input type="file" name="fileToUpload" id="fileToUpload">
				<input type="hidden" name="UserID" value="<?php echo $user['UserID']; ?>">
				<input type="submit" value="Upload Image" name="submit">
			</form>
		</p>
		<?php
	}
}
