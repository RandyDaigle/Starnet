<?php
// WIP: Zach

// Start Session
session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');

require_once('./main_menu.php');
require_once('./badWordsViews.php');
require_once('./loginViews.php');
require_once('../control/dbBlobController.php');
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
	<link rel="stylesheet" type="text/css" href="./styles2.css"/>

	<!-- JQuery Datatables -->
	<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
	<script>
		$(document).ready(function(){
			$('#auditResults').DataTable({
			"pageLength": 25
			});
		});
	</script>

</head>

<body>
 <div id="PageWrapper">
 	<a id="top"></a>
 	<div id="Header">
 		<table> <tr>
 				<td>
					<a href="<?php get_home(); ?>/index.php"</a>
						<img src="./images/star1.png" alt="star" width="125" height="100"/>
					</a>
				</td>
 				<td width="920">
					<?php 
						display_login();	
					?>
 				</td>
 			</tr>
 		</table>
	</div>
	<div id="MainContent">

	<?php
	// Testing if admin is logged in
	if(!isset($_SESSION['loggedin'])) {
		echo "You do not have permission to view this page.";
	} else {
		$user = get_user_by_Username($_SESSION['loggedin']);
		if(strcmp($user['Type_Desc'], "Administrator") == 0 || strcmp($user['Type_Desc'], "Moderator") == 0) {
			if($_SERVER['REQUEST_METHOD'] === 'GET') {
				echo '<h2 id="title">Bad Words</h2>';
				echo '<div>Since this is a christian forum that does NOT allow swearing of any sort, you can add naughty words to
				the "bad words" list. <a href="./audit.php">Audits</a> can be performed to find users who have sinned by using these words</div>';
		
				// This page should only be viewable by admins.
				// TODO: Check for user admin status.
				// Session var? Fetch User_TypeID from DB?
				$userIsAdmin = True;
				if($userIsAdmin) {
					badWordForm();
				}
		
			} else {
				echo '<div id="AuditContent">';
				// Returning from the submission of an audit request.
				$userIsAdmin = True;
				if($userIsAdmin) {
		
					// Making sure that the user didn't mess with our form, or 
					// try to make some janky form submission. 
					// Or if our thing broke, but that won't happen :^)
					if(!isset($_POST['audit_type'])) {
						echo '<div class="error">Something went wrong. 
							No form type recieved.</div>';
					} else {
						// Testing what form the user used.
						if($_POST['audit_type'] === 'badWords_add') {
							// Adding new bad words
							if(!addBadWords($_POST)) {
								// Something went wrong.
								echo '<div class="error">Something went wrong. 
									Adding bad words failed.</div>';
							}
		
						} else if($_POST['audit_type'] === 'badWords_view') {
							// Viewing existing bad words
							if(!viewBadWords($_POST)) {
								// Something went wrong.
								echo '<div class="error">Something went wrong. 
									Viewing bad words failed.</div>';
							}
		
						} else if($_POST['audit_type'] === 'badWords_remove') {
							// Removing the selected bad words
							if(!removeBadWords($_POST)) {
								// Something went wrong.
								echo '<div class="error">Something went wrong. 
									Deleting bad words failed.</div>';
							}
						} else {
							// Invalid type of audit. 
							// Either something heck'd up, or the user is messing with our forms.
							echo ' <div class="error">Something went wrong. 
								Invalid form type recieved.</div>';
						}
					} 
				}
			}
		} else {
			// Not logged in as admin/moderator
			echo "You do not have permission to view this page.";
		}
	}
	?>
    </div>
</div>

</body>
</html>
