<?php
// WIP: Zach

// Start Session
session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');

require_once('./main_menu.php');
require_once('./auditViews.php');
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

	<!-- JQuery Datatables 
	-->
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
	<?php //top_page() 
	?>
	<div id="MainContent">

	<?php
	// Testing if admin is logged in
	if(!isset($_SESSION['loggedin'])) {
		echo "You do not have permission to view this page.";
	} else {
		// Testing for admin/moderator
		$user = get_user_by_Username($_SESSION['loggedin']);
		if(strcmp($user['Type_Desc'], "Administrator") == 0 || strcmp($user['Type_Desc'], "Moderator") == 0) {
			// User is authorized to view the page
			if($_SERVER['REQUEST_METHOD'] === 'GET') {
				auditForm();

			} else {
				// Returning from the submission of an audit request.
				echo '<div id="AuditContent">';

				$userIsAdmin = True;
				if($userIsAdmin) {
		
					// Making sure that the user didn't mess with our form, or 
					// try to make some janky form submission. 
					// Or if our thing broke, but that won't happen :^)
					if(!isset($_POST['audit_type'])) {
						echo '
							<div class="error">Something went wrong. 
							No audit type recieved.</div>
						';
					} else {
						
						// Testing what audit the user requested.
						if($_POST['audit_type'] === 'user') {
							// Performing a search on the users.
							if(!userAudit($_POST)) {
								// Something went wrong.
								echo '
									<div class="error">Something went wrong. 
									User audit failed.</div>
								';
							}
						} else if($_POST['audit_type'] === 'user_login') {
							// Performing a search on the users.
							if(!userLoginAudit($_POST)) {
								// Something went wrong.
								echo '
									<div class="error">Something went wrong. 
									User login audit failed.</div>
								';
							}
						} else if($_POST['audit_type'] === 'forumTopic') {
							// Performing a search on the users.
							if(!forumTopicAudit($_POST)) {
								// Something went wrong.
								echo '
									<div class="error">Something went wrong. 
									Forum topic audit failed.</div>
								';
							}
						} else if($_POST['audit_type'] === 'cussWord_pm') {
							// Performing a search on the users.
							if(!cussWordPrivateMessageAudit($_POST)) {
								// Something went wrong.
								echo '
									<div class="error">Something went wrong. 
									Bad word (private messages) audit failed.</div>
								';
							}
						} else if($_POST['audit_type'] === 'cussWord_pub') {
							// Performing a search on the users.
							if(!cussWordPublicCommentAudit($_POST)) {
								// Something went wrong.
								echo '
									<div class="error">Something went wrong. 
									Bad word (public comments) audit failed.</div>
								';
							}
						} else {
							// Invalid type of audit. 
							// Either something heck'd up, or the user is messing with our forms.
							echo '
								<div class="error">Something went wrong. 
								Invalid audit type recieved.</div>
							';
						}
					} 
				}
				// Audit wrapper
				echo "</div>";
			}
		} else {
			// Not logged in as moderator/admin
			echo "You do not have permission to view this page.";
		}
	}
	?>
        		
	</div>
</div>

</body>
</html>
