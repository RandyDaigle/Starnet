<?php
session_start();

require_once('dbLibrary.php'); 
require_once('dbChat.php');

db_connect(); 

if(isset($_POST['msg'])) {
	
	$chatID = $_POST['chatID'];
	$userID = $_POST['userID'];
	$msg = htmlentities($_POST['msg'], ENT_NOQUOTES);
	//$timestamp = $_POST['timestamp'];
	
	add_Message($chatID, $userID, $msg);
}
?>