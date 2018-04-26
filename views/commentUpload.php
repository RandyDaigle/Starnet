<?php
session_start();
if($_SESSION['Status_ID'] != 1 || $_SESSION['Banned'] != FALSE)
	header('Location: http://138.197.152.148/control/log_out.php');
require_once('../control/dbLibrary.php'); 
require_once('../control/dbBlobController.php');
require_once('../control/dbCommentController.php');
require_once('./messagepage.php');
db_connect(); 
$user = get_user_by_Username($_POST['UserID']);

if (isset($_FILES["generalFileToUpload"]["name"]))
{
	if(strcmp($_FILES["generalFileToUpload"]["name"], "") == 0){
		$parent = NULL;
		addComment($_SESSION['loggedin'], $_POST['Description'], $parent);
		header('Location: http://138.197.152.148/index.php');
	}
	else{
		$imageFileType = pathinfo($_FILES["generalFileToUpload"]["name"],PATHINFO_EXTENSION);
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			top_page();
		    echo "<p>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
		    ?>
			<form action="commentRedirect.php" method="post">
				<input type="submit" value="Return"/>
			</form>
			<?php
			bottom_page();
		}
		else if (!getimagesize($_FILES["generalFileToUpload"]["tmp_name"])) { // Check if image file is a actual image or fake image
			top_page();
		    echo "<p>File is not an image.</p>";
		    ?>
			<form action="commentRedirect.php" method="post">
				<input type="submit" value="Return"/>
			</form>
			<?php
			bottom_page();
		}
		else if ($_FILES["generalFileToUpload"]["size"] > 300000) { // Check file size restrict to below 300k
			top_page();
			echo "<p>Sorry, your file is too large.</p>";
		    ?>
			<form action="commentRedirect.php" method="post">
				<input type="submit" value="Return"/>
			</form>
			<?php
			bottom_page();
		}
		// else insert the image
		else{
			$parent = NULL;
			$id = addComment($_SESSION['loggedin'], $_POST['Description'], $parent, $_POST['IsForum']);
			insertBlob($_FILES, $user, $id);
			header('Location: http://138.197.152.148/index.php');
		}
	}
}


?>
