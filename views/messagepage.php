<?php
	
/*
if(!isset($_SESSION)){
	session_start();
}
*/

function top_page(){
require_once('./main_menu.php');
require_once('./registerViews.php');
require_once('./loginViews.php');
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
	<link rel="icon" type="image/x-icon" href="<?php get_home(); ?>/favicon.ico" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$( function() {
			$( "#datepicker" ).datepicker();
  		} );
  	</script>
</head>

<body>
 <div id="PageWrapper">
 	<a id="top"></a>
 	<div id="Header">
 		<table>
 			<tr>
 				<td>
					<a href="<?php get_home(); ?>/index.php">
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

        <div id="MainContent"><?php
}

function bottom_page(){
?>
</div>  		
</div>

</body>
</html>
<?php 
}
?>
