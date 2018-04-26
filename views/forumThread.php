<?php
// Start Session
session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');
	
require_once('../control/dbLibrary.php');
require_once('./SuggestedFriends.php');
require_once('./main_menu.php');
require_once('./loginViews.php');
require_once('./privateChatView.php');
require_once('./forumDetailViews.php');
require_once('../control/dbBlobController.php');
require_once('../control/dbCommentController.php');
require_once('../control/dbForumController.php');
require_once('../control/dbChat.php');

db_connect();


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"/>
	<meta name="description" content="Starnet social network"/>
	<meta name="keywords" content="starnet, forum, messenger, awesome"/>
	<meta name="author" content="Starnet"/>
	<title> Starnet </title>
	
	<script src="http://code.jquery.com/jquery-3.2.1.min.js"
		integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
		crossorigin="anonymous"></script>
	<script src="../ChatScript.js"></script>
	<link rel="stylesheet" type="text/css" href="./styles2.css"/>
	<link rel="stylesheet" type="text/css" href="./PrivateChat.css"/>
	<link rel="icon" type="image/x-icon" href="<?php get_home(); ?>/favicon.ico" />
	
</head>

<body>		
 <div id="PageWrapper" style="padding-bottom:20px;">
 	<a id="top"></a>
 	<div id="Header">
 		<table>
 			<tr>
 				<td><img src="<?php get_view(); ?>images/star1.png" alt="star" width="125" height="100"/></td>
 				<td><h1>Starnet</h1></td> 
				<td width="920">
					<?php 
						display_login();	
					?>
 				</td>
 			</tr>
 		</table>
	</div>
		
<?php 
	if(isset($_SESSION['loggedin'])){ 
		echo "<div id=\"SidebarLeft\">"; 
		displayNavigation();
		db_display_logout_button($_SESSION['loggedin']);
		}
	else 
		echo "<div id=\"SidebarLeft\">";
		echo "</div>"; 
?>
		
<?php 
	if(isset($_SESSION['loggedin'])){ 
		echo "<div id=\"SidebarRight\">"; 
		}
	else 
		echo "<div id=\"SidebarRight\">";
		if(isset($_SESSION['loggedin'])) {
			echo "<div id=\"FriendRequestsWindow\">";
			echo 	"<span style=\"text-align:center;font-weight:bold;\">Friend Requests</span>";
					display_friend_requests();
			echo "</div>";
			
			echo "<hr/>";
			
			echo "<div id=\"SuggestedFriendsWindow\">";
			echo 	"<span style=\"text-align:center;font-weight:bold;\">Suggestions</span>";
					display_suggested_friends();
			echo "</div>";
		}
	echo "</div>"; 
?>

        <div id="MainContent">
 			<!--<h1>Starnet</h1>-->
	 			<?php forumThread(); ?>
        </div>

</div>

<div id="ChatWindow">
	<div id="ChatTop">Chat</div>
		<div id="ChatBody">
			<?php 
				if(isset($_SESSION['loggedin'])) {
					showFriends(); 
				}
				else {
					echo "<div>";
					echo "Please log in.<br />Once you logon, your friends will appear here.";
					echo "</div>";
				}
			?>
		</div>
</div>


<div id="PrivateChat" style="right:290px"></div>


</body>
</html>