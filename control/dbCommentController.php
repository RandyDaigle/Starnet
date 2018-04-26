<?php	
 require_once('dbLibrary.php');

	/* 
		News display function, displays information and returns true upon success
		Input: userID
		Output: 
	*/
	
	function display_newsflash(){

			global $db_connection_handle;
			
			$sql = "SELECT * FROM Comments
					JOIN UserInfo
					ON UserInfo.UserID = Comments.UserID
					WHERE IsForum = 0
					ORDER BY DateCreated DESC";
			
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
	
	function addComment($Username, $comment, $parent, $IsForum){
	    //clean input
    	$comment = sanitize($comment);
    	$user = get_user_by_Username($Username);
    	$date = get_myDate();
		
		global $db_connection_handle;

		$sql = "INSERT INTO Comments(UserID, CommentBody, DateCreated, Parent, IsForum) VALUES (:userID, :comment, :Date, :Parent, :IsForum)";

 		try{
			$incom=$db_connection_handle->prepare($sql);
			$incom->bindParam(':userID', $user['UserID'], PDO::PARAM_STR, 11);
			$incom->bindParam(':comment', $comment, PDO::PARAM_STR, 512);
			$incom->bindParam(':Date', $date, PDO::PARAM_STR, 50);
			$incom->bindParam(':Parent', $parent, PDO::PARAM_STR, 11);
			$incom->bindParam(':IsForum', $IsForum, PDO::PARAM_STR, 2);
			$incom->execute();

            if (($incom->rowCount()) == 1){
                return $db_connection_handle->lastInsertId('CommentID');
            }
            return false;
		}
		catch(PDOException $e){
			return $sql . "<br/>" . $e->getMessage();
		}
	}

?>