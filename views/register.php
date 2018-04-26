<?php
// Start Session
session_start();
	
require_once('./main_menu.php');
require_once('./registerViews.php');
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

</head>

<body>
 <div id="PageWrapper" style="min-height:740px;">
 	<a id="top"></a>
 	<div id="Header">
 		<table>
 			<tr>
 				<td><img src="./images/star1.png" alt="star" width="125" height="100"/></td>
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
			}
			else
			echo "<div id=\"SidebarLeft\">";
			echo "</div>";
			
			if(isset($_SESSION['loggedin'])){ 
				echo "<div id=\"SidebarRight\">"; 
			}
			else{
				echo "<div id=\"SidebarRight\">";
				echo "Friends and requests";
				echo "</div>";
			} 
			echo "</div>";
		?>

        <div id="MainContent" style="padding-left: 300px" >
 			<h1>Starnet</h1>
 			<?php 
				if(isset($_SESSION['loggedin']) && !(isset($_POST['Useradmin']))){ 
					successfulRegister();
				}
				else if(isset($_POST['Forgot'])){
					forgotYourCredentials();
				}
				else if(isset($_POST['Useradmin'])){
					adminListUsers();
				}
				else{
					registerForm();
				}
			?>
        </div>


        		
</div>

</body>
</html>