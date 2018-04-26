<?php


    /* 
    	display_login-Displays the log in fields on index screen when user is not signed in. 
    	Input-nothing
    	Output-displays login form on page when not logged in.
    	
    */	
	
function display_login(){

	if(isset($_SESSION['loggedin'])){ 
		$user = get_user_by_Username($_SESSION['loggedin']);
		?>
		<table align="right">
			<tr>
				<th>
					Welcome <?php echo $user['First_Name']; ?><br/>
					<?php
						if(strcmp($user['Type_Desc'], "Member") != 0){
							?>
							User Level<br/><?php echo $user['Type_Desc'];?>
							<form action="<?php get_view(); ?>register.php" method="post">
								<input type="submit" value="User Admin" name="Useradmin">
								<a href="<?php get_view(); ?>audit.php">Audit</a>
							</form>
							<?php
						}
						?>
					
				</th>
				<th><?php
						if(($FileNumber=checkUserImage($user['UserID']))!=0){
							$picture = selectUserImage($FileNumber['FilesID']);
						?>
						<img src="data:image/png;base64,<?php echo base64_encode($picture["data"]); ?>" height="100" width="100" /><br/>
						<?php
						}
						?>
				</th>
				<th>
					<form action="<?php get_view(); ?>register.php" method="post">
						<input type="submit" value="User Detail" name="User Detail">
					</form>
					<form action="<?php get_control(); ?>log_out.php" method="post">
						<input type="submit" value="Logout" name="Logout">
					</form>
				</th>
			</tr>
		</table>
		<?php
	}
	else{
		?>
		<form action="<?php get_view(); ?>register.php" method="post">
			Register or Login to this awesome Social Network.
				<input type="submit" value="Register" name="Register">
		</form>
		
		<form action="<?php get_control(); ?>login_try.php" id="mylogin" method="post">
				UserName:<input type="text" name="UserName" id="UserName">
				Password:<input type="password" name="password" id="password">
				<input type="submit" value="Login">
		</form>
		<form action="<?php get_view(); ?>register.php" method="post">
			<input type="submit" value="Forget Something?" name="Forgot">
		</form>

		<?php
	}
	return;
}

?>
