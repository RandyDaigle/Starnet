<?php

Session_Start();
require_once('dbLibrary.php'); 
require_once('dbChat.php');
require_once('dbBlobController.php');

db_connect();

$userID = $_POST['userID'];
$friendID = $_POST['friendID'];

send_friend_request($userID, $friendID);

$suggestedFriends = get_suggested_friends($userID);

if(!empty($suggestedFriends))
{
	foreach($suggestedFriends as $friend) {
		$friendID = $friend['UserID'];
		$first_name = ucfirst($friend['First_Name']);
		$last_name = ucfirst($friend['Last_Name']);
		$full_name = $first_name . $last_name;
		$getFileID = checkUserImage($friendID);
		$userPicture = selectUserImage($getFileID['FilesID']);
		echo "<div id=\"SuggestedFriends\">";
		echo "<span id=\"UserID\" style=\"display:none;\">$userID</span>";
		echo "<span id=\"FriendID\" style=\"display:none;\">$friendID</span>";
		echo "<span id=\"$full_name\" style=\"display:none;\">$friendID</span>";
		echo 	'<img src="data:image/png;base64,' . base64_encode($userPicture["data"]) . '"/>';
		echo 	"<span>$first_name $last_name</span>";
		echo "<input type='button' id='sendFriendRequest' value='Add'>";
		echo "</div>";
	}
} 
else {
	echo "<div id=\"SuggestedFriends\">";
	echo 	"<span>No other friends to suggest!</span>";
	echo "</div>";
}	

?>