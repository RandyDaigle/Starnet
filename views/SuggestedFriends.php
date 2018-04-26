<?php

require_once('/home/zach/uploadHere/control/dbChat.php');
require_once('/home/zach/uploadHere/control/dbBlobController.php');

db_connect(); 

function display_friend_requests() {
	$user = get_user_by_Username($_SESSION["loggedin"]);
	$userID = $user["UserID"];
	
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
}


function display_suggested_friends() {
	$user = get_user_by_Username($_SESSION["loggedin"]);
	$userID = $user["UserID"];
	
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
			echo 	'<img src="data:image/png;base64,' . base64_encode($userPicture["data"]) . '"/>';
			echo 	"<span>$first_name $last_name</span>";
			echo "<input type='button' value='Add' id='sendFriendRequest'>";
			echo "</div>";
		}
	}
	else {
		echo "<div id=\"SuggestedFriends\">";
		echo 	"<span>No other friends to suggest!</span>";
		echo "</div>";
	}
}

?>