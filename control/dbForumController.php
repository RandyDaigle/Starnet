<?php	

 require_once('dbLibrary.php');
 

	/* 
		News display function, displays information and returns true upon success
		Input: userID
		Output: 
	*/
	
	function display_forumTopics(){

			global $db_connection_handle;
			// user_id needs to come out.
			$sql = "SELECT DISTINCT Conversation.Convo_Id, Name, Date_Created
					FROM (((Conversation
					LEFT JOIN Conversation_Participators ON Conversation_Participators.Convo_Id = Conversation.Convo_Id)
					LEFT JOIN Comments ON Comments.CommentID = Conversation_Participators.CommentID)
					LEFT JOIN Conversation_Ban ON Conversation_Ban.Convo_Participator_ID = Conversation_Participators.Convo_Id)
					WHERE Conversation_Participators.Convo_Id IN ( SELECT DISTINCT Convo_Id FROM Conversation_Participators )
					ORDER BY Date_Created DESC";
			
			try{
				$news= $db_connection_handle->prepare($sql);
				$news->execute();
				
				$newsflash=$news->fetchAll(PDO::FETCH_ASSOC);
				
				return $newsflash;
			}
			catch(PDOExeception $e){
				return $sql . "<br/>" . $e->getMessage();
			}
	}

	
	function displayForumThread($convo_id){
		
			global $db_connection_handle;
			
			$sql = "SELECT * FROM (((Conversation
					  LEFT JOIN Conversation_Participators
					  ON Conversation_Participators.Convo_Id = Conversation.Convo_Id)
					  LEFT JOIN Comments ON Comments.CommentID = Conversation_Participators.CommentID)
					  LEFT JOIN Conversation_Ban ON Conversation_Ban.Convo_Participator_ID = Conversation_Participators.Convo_Id)
					  WHERE Conversation.Convo_Id = :Convo_Id
					  ORDER BY Date_Created DESC";
			
 			try{
				$news=$db_connection_handle->prepare($sql);
				$news->bindParam(':Convo_Id', $convo_id, PDO::PARAM_STR, 11);
				$news->execute();
				
				$newsflash=$news->fetchAll(PDO::FETCH_ASSOC);
				
				return $newsflash;
			}
			catch(PDOExeception $e){
				return $sql . "<br/>" . $e->getMessage();
			}
	}

	
	function addTopic($Username, $commentId, $parent, $name){
	    //clean input
    	$commentId = sanitize($commentId);
    	$name = sanitize($name);
    	$user = get_user_by_Username($Username);
    	$status = get_status($user['UserID']);
    	$date = get_myDate();

		global $db_connection_handle;
		
		$test = "START TRANSACTION;
				INSERT INTO Conversation (Name, Date_Created) VALUES  (:name, :Date);
				SELECT @SID := Convo_Id FROM Conversation WHERE Name = :name AND Date_Created = :Date;
				INSERT INTO Conversation_Participators (Convo_Id, User_Id, Last_Msg_Read, Status_Id, Authentication_Level, CommentID) 
				VALUES ( @SID, :userID, :Date, :statusID, :aLevel, :commentId);
				COMMIT;
				";
				
 		try{

			// tried to use 1 transaction but could not get this to work otherwise.
			
			$another = $db_connection_handle->prepare($test);
			$another->bindParam(':Date', $date, PDO::PARAM_STR, 50);
			$another->bindParam(':name', $name, PDO::PARAM_STR, 45);
			$another->bindParam(':userID', $user['UserID'], PDO::PARAM_STR, 11);
			$another->bindParam(':statusID', $status['Status_ID'], PDO::PARAM_STR, 11);
			$another->bindParam(':aLevel', $user['User_TypeID'], PDO::PARAM_STR, 11);
			$another->bindParam(':commentId', $commentId, PDO::PARAM_STR, 512);
			return $another->execute();
			
		}
		catch(PDOException $e){
			return $test . "<br/>" . $e->getMessage();
		}
		
	}
	
		function addCommentToThread($Username, $commentId, $parent, $Convo_Id){
	    //clean input
    	$commentId = sanitize($commentId);
    	$user = get_user_by_Username($Username);
    	$status = get_status($user['UserID']);
    	$date = get_myDate();

		global $db_connection_handle;
		
		$sql = 'INSERT INTO Conversation_Participators (Convo_Id, User_Id, Last_Msg_Read, Status_Id, Authentication_Level, CommentID) VALUES ( :id, :userID, :Date, :statusID, :aLevel, :commentId)';
				
 		try{

			$another2 = $db_connection_handle->prepare($sql);
			$another2->bindParam(':userID', $user['UserID'], PDO::PARAM_STR, 11);
			$another2->bindParam(':Date', $date, PDO::PARAM_STR, 50);
			$another2->bindParam(':statusID', $status['Status_ID'], PDO::PARAM_STR, 11);
			$another2->bindParam(':aLevel', $user['User_TypeID'], PDO::PARAM_STR, 11);
			$another2->bindParam(':commentId', $commentId, PDO::PARAM_STR, 512);
			$another2->bindParam(':id', $Convo_Id, PDO::PARAM_STR, 11);
			$another2->execute();

            return true;
		}
		catch(PDOException $e){
			return $sql . "<br/>" . $e->getMessage();
		}
		
	}
	
	
	function getLastConvoID(){

		global $db_connection_handle;
		
		$sql = 'SELECT Convo_Id FROM Conversation_Participators ORDER BY CommentID DESC LIMIT 1';

				
 		try{
			
			$another = $db_connection_handle->prepare($sql);
			$another->execute();
			
			$ID = $another->fetch(PDO::FETCH_ASSOC);
			return $ID['Convo_Id'];

		}
		catch(PDOException $e){
			return $sql . "<br/>" . $e->getMessage();
		}
		
		
	}
	
	function getConvoParticipator($Convo_Id){

			global $db_connection_handle;
			// user_id needs to come out.
			$sql = "SELECT * FROM Conversation_Participators WHERE Conversation_Participators.Convo_Id = :Convo_Id ORDER BY Id ASC LIMIT 1";
			
			try{
				$participator = $db_connection_handle->prepare($sql);
				$participator->bindParam(':Convo_Id', $Convo_Id, PDO::PARAM_STR, 11);
				$participator->execute();
				
				return $participator->fetchAll(PDO::FETCH_ASSOC);
				
			}
			catch(PDOExeception $e){
				return $sql . "<br/>" . $e->getMessage();
			}
	}
	
	


?>