<?php

require_once('../control/dbLibrary.php');
require_once('./main_menu.php');
require_once('./loginViews.php');
require_once('./forumViews.php');
require_once('../control/dbBlobController.php');
require_once('../control/dbCommentController.php');
require_once('../control/dbForumController.php');


function forumThread(){
?>
	<?php 
		if(isset($_SESSION['loggedin'])){
			if(!isset($_POST['Convo_Id']))
		 		$Convo_Id = getLastConvoID();
		 	else
		 		$Convo_Id = $_POST['Convo_Id'];
			$thread = displayForumThread($Convo_Id);
			echo "<h1>" . $thread[0]['Name'] . "</h1>";
			threadTable($thread);
			echo "<p>Post a Comment</p>";
			postForumThreadForm(); 
		}
		
		
	?>
<?php
}


function postForumThreadForm(){	
	if(!isset($_POST['Convo_Id']))
		$Convo_Id = getLastConvoID();
	else
		$Convo_Id = $_POST['Convo_Id'];
	
?>
			<form action="./forumThreadCommentUpload.php" method="post" enctype="multipart/form-data">
				Comment:<br/><textarea name="Description" rows="5" cols="100"></textarea>
				<br/>
				Select image to upload:
				<input type="file" name="generalFileToUpload" id="generalFileToUpload">
				<input type="hidden" name="UserID" value="<?php echo $_SESSION['loggedin']; ?>">
				<input type="hidden" name="Convo_Id" value="<?php echo $_POST['Convo_Id']; ?>">
				<input type="hidden" name="IsForum" value="1">
				<input type="submit" value="Post" name="Post">
			</form>
<?php
}



function threadTable($topics){
	?>
	<div id="Forum">
		<table border="1px" width="575px" align="center">
		<?php
		foreach($topics as $topic){
			$user = get_user($topic['User_Id']);
			$commentPicture = selectCommentImage($topic['CommentID']);
		?>
		<tr>
			<td colspan="2">Posted: <?php $date = explode(" ", $topic['Date_Created']); echo $date[0] . " by ". $user['Username']; ?></td>

			<td>
				<?php echo $topic['CommentBody'] . "<br/>";
					if(strcmp($commentPicture["data"], "") != 0 ){
				?>
					<img src="data:image/png;base64,<?php echo base64_encode($commentPicture["data"]); ?>" height="250" width="250" /><br/>
					<?php } ?>
			</td>
		
		<?php
	}
	?>
	</table>
	</div>
	<?php
}
?>

