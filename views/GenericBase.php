<?php
// Start Session
session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');
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
 <div id="PageWrapper">
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
	else{
	echo "<div id=\"SidebarLeft\">";
	echo "</div>";
	}
	if(isset($_SESSION['loggedin'])){ 
		echo "<div id=\"SidebarRight\">"; 
	}
	else{
		echo "<div id=\"SidebarRight\">";
		echo "Friends and requests";
		echo "</div>";
	} 
?>

        <div id="MainContent">
 			<h1>Starnet</h1>
 			<p>
	 			Conversations go here.
 			</p>
 			 <br/>
        <br/>
         <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/> <br/>
        <br/>
 			<?php //if((isset($_SESSION['loggedin']))) echo "<p><img src=\"./views/images/garage.png\" alt=\"Founder\" width=\"500\" height=\"313\"/></p><i>Founder Claude Pearson with operator Al Badder at CG Tilbury offices</i>"; ?>
 			<div id="LogIn">
 			<?php //if(!(isset($_SESSION['loggedin']))) display_login(); else display_newsflash($_SESSION['loggedin']); ?>
 			</div>
        </div>
        

        
        <br/>
        <br/>
        <div id="Footer" <?php //if(!(isset($_SESSION['loggedin']))) echo "style=padding-left:50px"; ?>>
        	<?php get_myfooter(); ?>
		</div>
        		
</div>

</body>
</html>