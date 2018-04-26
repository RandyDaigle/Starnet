<?php

session_start();

require_once('dbLibrary.php'); 
require_once('dbChat.php');
require_once('dbBlobController.php');

db_connect();

$user = get_user_by_Username($_SESSION["loggedin"]);
$userID = $user["UserID"];
$friends = retrieve_friend_list($userID);

foreach($friends as $friend) {
	$friendID = $friend["UserID"];
	
	$unread_count = retrieve_unread_private_msgs($userID, $friendID);
	$getFileID = checkUserImage($friendID);
	$userPicture = selectUserImage($getFileID['FilesID']);
	$status = get_friend_status($friendID);
	$statusID = $status['Status_ID'];
	$lastActivityTime = $status['Last_Activity_Time'];
	$current_Time = get_myDate();
	
	if($lastActivityTime != NULL)
	{
		$sinceLastActivity = abs(strtotime($current_Time) - strtotime($lastActivityTime));
		
		$minutes = abs(round($sinceLastActivity / 60));
		
		if($statusID == 1)
		{
			if($minutes > 3)
			{
				make_friend_offline($friendID, 4);
			}
		}
		
		$hours = abs(round($sinceLastActivity / 3600));
		$days = abs(round($sinceLastActivity / 86400));
	}
		
		
		$first_name = ucfirst($friend['First_Name']);
		$last_name = ucfirst($friend['Last_Name']);
		$friend_name = $first_name . $last_name;	
		
		echo "<span id=\"$friend_name\" style=\"display:none;\">$friendID</span>";
		echo "<div class=\"Users\">";
		echo 	'<img src="data:image/png;base64,' . base64_encode($userPicture["data"]) . '"/>';
		echo 	"<span>$first_name $last_name</span>";
		if($unread_count != 0) {
			echo "<span class=\"Unread_Notification\">$unread_count</span>";
		}
		else {
			echo "<span></span>";
		}
		echo "<span id=\"UserID\" style=\"display:none;\">$userID</span>";
		
		if($statusID == 1)
		{
			echo "<p class=\"OnlineStatus\"></p>";
		}
		else if ($statusID == 4) {
			if($sinceLastActivity < 60)
			{
				echo "<p class=\"OfflineStatus\">".$sinceLastActivity."s</p>";
			}
			else if($minutes < 60)
			{
				echo "<p class=\"OfflineStatus\">".$minutes."m</p>";
			}
			else if($hours < 24)
			{
				echo "<p class=\"OfflineStatus\">".$hours."h</p>";
			}
			else if($days < 30)
			{
				echo "<p class=\"OfflineStatus\">".$days."d</p>";
			}
		}
		echo "</div>";
}
?> 