<?php


require_once('../control/dbLibrary.php'); 
require_once('../control/dbBlobController.php');
require_once('./messagepage.php');
db_connect(); 
//print_r($_FILES);


if (isset($_FILES["fileToUpload"]["name"])){
	
	$imageFileType = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
		top_page();
	    ?>
	    <p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>
		<form action="register.php" method="post">
			<input type="submit" value="Return"/>
		</form>
		<?php
		bottom_page();
		return;
	}
	
	// Check if image file is a actual image or fake image
	if(!getimagesize($_FILES["fileToUpload"]["tmp_name"])) {
	    top_page();
	    ?>
	    <p>File is not an image.</p>
		<form action="register.php" method="post">
			<input type="submit" value="Return"/>
		</form>
		<?php
	    bottom_page();
	    return;
	}
	
	// Check file size restrict to below 300k
	if ($_FILES["fileToUpload"]["size"] > 300000) {
		top_page();
		?>
		<p>Sorry, your file is too large.</p>
		<form action="register.php" method="post">
			<input type="submit" value="Return"/>
		</form>
		<?php
		bottom_page();
		return;
	}
	// else insert the image
	else{
		insertBlob($_FILES, $_POST, NULL);
		header('Location: http://138.197.152.148/views/register.php');
	}
}

?>