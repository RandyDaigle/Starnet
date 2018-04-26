<?php
/* 
Main news feed comments, last 3 weeks of info.
*/
	
function forumForm(){
	if(isset($_SESSION['loggedin'])){
		echo "<p>Post a Comment</p>";
		postForumForm(); 
	}
	// display recent comments.
	$topics= display_forumTopics();
	commentTable($topics);
}


function postForumForm(){	
?>
	<form action="./forumTopicUpload.php" method="post" enctype="multipart/form-data">
		<p>Topic of interest: <input type="text" name="Name" size="90"><br/></p>
		<p>Comment: <textarea name="Description" rows="5" cols="100"></textarea></p>
		<p>
		Select image to upload:
		<input type="file" name="generalFileToUpload" id="generalFileToUpload">
		<input type="hidden" name="UserID" value="<?php echo $_SESSION['loggedin']; ?>">
		<input type="hidden" name="IsForum" value="1">
		<input type="submit" value="Post" name="Post">
		</p>
	</form>
<?php
}


function commentTable($topics){
	?><table border="1px" width="575px" align="center"><?php
	foreach($topics as $topic){
		$participator = getConvoParticipator($topic['Convo_Id']);
		$user = get_user($participator[0]['User_Id']);
		?>
		<tr>
			<td colspan="2">Posted: <?php $date = explode(" ", $topic['Date_Created']); echo $date[0] . " by ". $user['Username']; ?></td>
			<td>
				<form action="./forumThread.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="UserID" value="<?php echo $_SESSION['loggedin']; ?>">
					<input type="hidden" name="Convo_Id" value="<?php echo $topic['Convo_Id']; ?>">
					<input type="hidden" name="Name" value="<?php echo $topic['Name']; ?>">
					<input type="submit" value="<?php echo $topic['Name']; ?>" name="TopicName">
				</form>
			</td>
		<?php
	}
	?>
	</table>
	<?php
}
?>
