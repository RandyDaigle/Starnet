<?php

Session_Start();
require_once('dbLibrary.php'); 
require_once('dbChat.php');
require_once('dbBlobController.php');

db_connect(); 

$userID = $_POST['userID'];
$friendID = $_POST['friendID'];
$requestStatus = $_POST['requestStatus'];

update_pending_friend_request($userID, $friendID, $requestStatus);

$friendRequests = retrieve_friend_requests($userID);
	
if(!empty($friendRequests))
{	
	foreach($friendRequests as $request)
	{
		$friendID = $request['UserID'];
		$first_name = ucfirst($request['First_Name']);
		$last_name = ucfirst($request['Last_Name']);
		$full_name = $first_name . $last_name;
		$getFileID = checkUserImage($friendID);
		$userPicture = selectUserImage($getFileID['FilesID']);
			
		echo "<div id=\"FriendRequests\">";
		echo "<span id=\"UserID\" style=\"display:none;\">$userID</span>";
		echo "<span id=\"FriendID\" style=\"display:none;\">$friendID</span>";
		echo "<span id=\"$full_name\" style=\"display:none;\">$friendID</span>";
		echo 	'<img src="data:image/png;base64,' . base64_encode($userPicture["data"]) . '"/>';
		echo 	"<span>$first_name $last_name</span>";
		echo "<input type=\"button\" id=\"Confirmation\" value=\"C\" />";
		echo "<input type=\"button\" id=\"Rejection\" value=\"D\" />";
		echo "</div>";
	}
}
else {
	echo "<div id=\"FriendRequests\">";
		echo "<span>You currently have no pending friend requests.</span>";
	echo "</div>";
}

