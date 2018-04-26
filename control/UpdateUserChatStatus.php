<?php

Session_Start();
require_once('dbLibrary.php'); 
require_once('dbChat.php');

db_connect();

if($_SESSION['loggedin']) {
	$user = get_user_by_UserName($_SESSION['loggedin']);
	$userID = $user['UserID'];
	update_user_status($userID, 1);
}
	
?>