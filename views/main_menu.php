<?php
/************
*   The following code displays various items that appear on multiple pages. 
*	Multiple footer functions based on file locations.
************/


// Navigation for the site.	
// Input a integer variable to extend content on page that calls function.
function displayNavigation(){
	?>
	<span style="text-align:center;">Navigation</span>
	<ul style="text-align:left;">
		<li><a href="<?php get_home();?>">Home</a></li>
		<?php
			if(isset($_SESSION['loggedin'])){
				?>
				<li><a href="<?php get_view();?>forum.php">Forum</a></li>
				<?php
			}
			?>
	</ul>
	<?php

}


// extends Menu content based on the selection made for the menu items.
function get_manage(){
	?>
	<ul>
		<li><a href="./manage_content.php">Content</a></li>
	</ul>
	<?php
}

// returns the website location.
function get_home(){
	echo "http://138.197.152.148";
}

function get_view(){
	echo "http://138.197.152.148/views/";
}

function get_control(){
	echo "http://138.197.152.148/control/";
}



function get_myfooter(){
?>	
	        <p>
        	<?php if(array_key_exists('loggedin', $_SESSION)){ db_connect(); echo "Welcome: "; db_get_user_by_id($_SESSION['loggedin']); }?>
            <a href="<?php get_home();?>/index.php">Home</a> ---
            <a href="#top">Back to Top</a> ---
            <a href="mailto:&#112;&#101;&#097;&#114;&#115;&#111;&#049;&#049;&#064;&#117;&#119;&#105;&#110;&#100;&#115;&#111;&#114;&#046;&#099;&#097;">Email Starnet</a> ---
            <span style="text-decoration:underline">Powered by</span> &copy; <a href="./copyright.php">Starnet Web Design 2015</a>
        	</p>
<?php
	
}

function get_footer(){
?>	
	        <p>
        	<?php if(array_key_exists('loggedin', $_SESSION)){ db_connect(); echo "Welcome: "; db_get_user_by_id($_SESSION['loggedin']); }?>
            <a href="<?php get_home();?>/index.php">Home</a> ---
            <a href="#top">Back to Top</a> ---
            <a href="mailto:&#112;&#101;&#097;&#114;&#115;&#111;&#049;&#049;&#064;&#117;&#119;&#105;&#110;&#100;&#115;&#111;&#114;&#046;&#099;&#097;">Email Starnet</a> ---
            <span style="text-decoration:underline">Powered by</span>&copy; <a href="./copyright.php">Starnet Web Design 2015</a>
        	</p>
<?php
	
}

?>
