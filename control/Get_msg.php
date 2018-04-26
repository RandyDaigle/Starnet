<?php
session_start();

require_once('../control/dbLibrary.php'); 
require_once('../control/dbChat.php');
require_once('../control/dbBlobController.php');

db_connect(); 

$userID = $_POST['userID'];
$friendID = $_POST['friendID'];

$getFileID = checkUserImage($friendID);
$userPicture = selectUserImage($getFileID['FilesID']);

$chatID = retrieve_chat_ID($userID, $friendID);

echo "<div id=\"Messages\">";

$messages = retrieve_private_messages($chatID);

if(!empty($messages)) {
	foreach($messages as $message) {
		$msg = htmlentities($message['Message'], ENT_NOQUOTES);
		//$sent = date('F j. U, g:i a', $message['Sent_Time']);*/
		$fetchedUserID = $message['UserID'];
		if($fetchedUserID == $friendID)
		{
			echo "<div class=\"friendMsg\">";
			echo 	'<img src="data:image/png;base64,' . base64_encode($userPicture["data"]) . '"/>';
			echo 	"<div class=\"msg_sender\">$msg</div>";
			echo "</div>";
		} 
		else if ($fetchedUserID == $userID) {
			echo "<div class=\"msg_receiver\">$msg</div>";
		}
	}
}
else {
		echo "<div>No previous messages found.</div>";
}

echo "</div>";
?>