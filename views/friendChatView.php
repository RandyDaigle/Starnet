<?php 
//session_start();

require_once('../control/dbLibrary.php'); 
require_once('../control/dbChat.php');
require_once('../control/dbBlobController.php');

db_connect(); 

$userID = $_POST['userID'];
$friendID = $_POST['friendID']; 
$chatID = retrieve_chat_ID($userID, $friendID);
$friendName = get_user($friendID);
$first_Name = ucfirst($friendName['First_Name']);
$last_Name = ucfirst($friendName['Last_Name']); 
$getFileID = checkUserImage($friendID);
$userPicture = selectUserImage($getFileID['FilesID']);

echo "<div id=\"PrivateTop\">";
echo 	$first_Name . " " . $last_Name;
echo 	"<span id=\"ChatID\" style=\"display:none;\">$chatID</span>";
echo 	"<div id=\"Close\">X</div>";
echo "</div>";
echo "<div id=\"MsgWrap\">";
echo 	"<div id=\"PrivateBody\">";
echo 		"<div id=\"Messages\">";
				$messages = retrieve_private_messages($chatID);
				if(!empty($messages)) {
					foreach($messages as $message) {
						$msg = htmlentities($message['Message'], ENT_NOQUOTES);
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
echo 		"</div>";
echo 	"</div>";
echo 	"<div id=\"PrivateFooter\">";
echo 		"<textarea id=\"MsgSend\" row=\"4\" placeholder=\"Hit enter to send message\"></textarea>";
echo 	"</div>";
echo "</div>"; 

?>