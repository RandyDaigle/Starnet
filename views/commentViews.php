<?php
/* 
Main news feed comments, last 3 weeks of info.
*/
	
function commentForm(){
?>
	<?php 
		// display recent comments.
		$comments= display_newsflash();
		commentTable($comments);
		if(isset($_SESSION['loggedin'])){
			//echo "<p>Post a Comment</p>";
			postForm(); 
		}
	?>
<?php
}


function postForm(){	
?>
			<form action="./views/commentUpload.php" method="post" enctype="multipart/form-data">
				Comment:<textarea name="Description" rows="5" cols="100"></textarea>
				<br/>
				Select image to upload:
				<input type="file" name="generalFileToUpload" id="generalFileToUpload">
				<input type="hidden" name="UserID" value="<?php echo $_SESSION['loggedin']; ?>">
				<input type="hidden" name="IsForum" value="0">
				<input type="submit" value="Post" name="Post">
			</form>
<?php
}


function commentTable($comments){
	?>
	<div id="Forum">
	<table border="1px" width="575px" align="center">
	<?php
	foreach($comments as $comment){
		$user = get_user($comment['UserID']);
		$commentPicture = selectCommentImage($comment['CommentID']);
		
		?>
		<tr>
			<td colspan="2">Date Logged: <?php  echo $comment['DateCreated']; ?></td>
		</tr>
		<tr>
			
			<th>User</th>
			<th>Comment</th>
		</tr>
		<tr>
			<td><?php echo $user['Username'] . "<br/>";						
						if(($FileNumber=checkUserImage($user['UserID']))!=0){
							$picture = selectUserImage($FileNumber['FilesID']);
							?>
							<img src="data:image/png;base64,<?php echo base64_encode($picture["data"]); ?>" height="35" width="35" /><br/>
							<?php
							}
				?>
			</td>
			<td>
				<?php echo $comment['CommentBody'] . "<br/>";
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