<?php

require_once('dbLibrary.php');
 
	function retrieve_friend_list($UserID) {
		
		global $db_connection_handle;
		
		$sql = '(SELECT Friendship.User_One_ID AS UserID, First_Name, Last_Name
				FROM UserInfo
				JOIN Friendship
				ON UserInfo.UserID = Friendship.User_One_ID
				WHERE Friendship.User_Two_ID = :userID AND Request_Status = 1)
				UNION
				(SELECT Friendship.User_Two_ID AS UserID, First_Name, Last_Name
				FROM UserInfo
				JOIN Friendship
				ON UserInfo.UserID = Friendship.User_Two_ID
				Where Friendship.User_One_ID = :userID AND Request_Status = 1)';
				
		try {
			$getFriends=$db_connection_handle->prepare($sql);
			$getFriends->bindParam(':userID', $UserID, PDO::PARAM_INT);
			$getFriends->execute();
			
			$friendList = $getFriends->fetchAll(PDO::FETCH_ASSOC);
			
			return $friendList;
		}
		catch(PDOException $e) {
			return "Error. Cannot retrieve friend list.";
		}
	}

	function get_friend_status($FriendID) {
		
		global $db_connection_handle;
		
		$friendID = sanitize($FriendID);
		
		$sql = 'SELECT Status_ID, Last_Activity_Time 
				FROM Online
				WHERE User_ID = :friendID';
				
		try {
			$getFriendStatus=$db_connection_handle->prepare($sql);
			$getFriendStatus->bindParam(':friendID', $friendID, PDO::PARAM_INT);
			$getFriendStatus->execute();
			
			$status = $getFriendStatus->fetch(PDO::FETCH_ASSOC);
			
			return $status;
		}
		catch(PDOException $e) {
			return "Cannot retrieve friend status.";
		}
	}
	
	function make_friend_offline($friendID, $statusID) {
		
		global $db_connection_handle;
	
		$friendID = sanitize($friendID);
		$statusID = sanitize($statusID);
		
		$sql1 = 'SELECT COUNT(User_ID)
				 FROM Online
				 WHERE User_ID = :friendID';
				 
		try {
			$fetchedRecord=$db_connection_handle->prepare($sql1);
			$fetchedRecord->bindParam(':friendID', $friendID, PDO::PARAM_INT);
			$fetchedRecord->execute();
			
			if($fetchedRecord->fetch(PDO::FETCH_NUM)[0] != 0) {
				$sql2 = 'UPDATE Online
						 SET Status_ID = :statusID
						 WHERE User_ID = :friendID';
						 
				$updateUserStatus=$db_connection_handle->prepare($sql2);
				$updateUserStatus->bindParam(':friendID', $friendID, PDO::PARAM_INT);
				$updateUserStatus->bindParam(':statusID', $statusID, PDO::PARAM_INT);
				$updateUserStatus->execute();
				
				return;
			}
			else {
				$current_Time = get_myDate();
				
				$sql3 = 'INSERT INTO Online (User_ID, Status_ID, Last_Activity_Time)
					     VALUES (:friendID, :statusID, :current_Time)';
						 
				$insertUserStatus=$db_connection_handle->prepare($sql3);
				$insertUserStatus->bindParam(':friendID', $friendID, PDO::PARAM_INT);
				$insertUserStatus->bindParam(':statusID', $statusID, PDO::PARAM_INT);
				$insertUserStatus->bindParam(':current_Time', $current_Time, PDO::PARAM_STR);
				$insertUserStatus->execute();

				return;
			}
		}
		catch(PDOException $e) {
			return "Could not retrieve friend's name.";
		}
	}
	
	function update_user_status($userID, $statusID) {
	
		global $db_connection_handle;
	
		$userID = sanitize($userID);
		$statusID = sanitize($statusID);

		if(!is_numeric($userID)) {
			$user = get_user_by_Username($userID);
			$userID = $user["UserID"];
		}
		
		$current_Time = get_myDate();
		
		$sql1 = 'SELECT COUNT(User_ID)
				 FROM Online
				 WHERE User_ID = :userID';
				 
		try {
			$fetchedRecord=$db_connection_handle->prepare($sql1);
			$fetchedRecord->bindParam(':userID', $userID, PDO::PARAM_INT);
			$fetchedRecord->execute();
			
			if($fetchedRecord->fetch(PDO::FETCH_NUM)[0] != 0) {
				$sql2 = 'UPDATE Online
						 SET Status_ID = :statusID, Last_Activity_Time = :current_Time
						 WHERE User_ID = :userID';
						 
				$updateUserStatus=$db_connection_handle->prepare($sql2);
				$updateUserStatus->bindParam(':userID', $userID, PDO::PARAM_INT);
				$updateUserStatus->bindParam(':statusID', $statusID, PDO::PARAM_INT);
				$updateUserStatus->bindParam(':current_Time', $current_Time, PDO::PARAM_STR);
				$updateUserStatus->execute();
				
				return;
			}
			else {
				$sql3 = 'INSERT INTO Online (User_ID, Status_ID, Last_Activity_Time)
					     VALUES (:userID, :statusID, :current_Time)';
						 
				$insertUserStatus=$db_connection_handle->prepare($sql3);
				$insertUserStatus->bindParam(':userID', $userID, PDO::PARAM_INT);
				$insertUserStatus->bindParam(':statusID', $statusID, PDO::PARAM_INT);
				$insertUserStatus->bindParam(':current_Time', $current_Time, PDO::PARAM_STR);
				$insertUserStatus->execute();

				return;
			}
		}
		catch(PDOException $e) {
			return "Could not retrieve friend's name.";
		}
	}
	
	function display_friend_name($FriendID) {
		
		global $db_connection_handle;
		
		$sql = 'SELECT First_Name, Last_Name
				FROM UserInfo
				WHERE UserInfo.UserID = :friendID';
		
		try {
			$getFriendName=$db_connection_handle->prepare($sql);
			$getFriendName->bindParam(':friendID', $FriendID, PDO::PARAM_INT);
			$getFriendName->execute();
			
			$friendName = $getFriendName->fetchAll(PDO::FETCH_ASSOC);
			
			return $friendName;
		}
		catch(PDOException $e) {
			return "Could not retrieve friend's name.";
		}
	}
	
	function retrieve_friend_requests($userID) {
		
		global $db_connection_handle;
		
		$userID = sanitize($userID);
		
		$sql = 'SELECT UserID, First_Name, Last_Name
				FROM UserInfo
				WHERE UserID IN
					(SELECT Friendship.User_One_ID
					FROM UserInfo
					JOIN Friendship
					ON UserInfo.UserID = Friendship.User_One_ID
					WHERE Friendship.User_Two_ID = :userID AND Request_Status = 2)';
					
		try {
			$getFriendRequests=$db_connection_handle->prepare($sql);
			$getFriendRequests->bindParam(':userID', $userID, PDO::PARAM_INT);
			$getFriendRequests->execute();
			
			$friendRequests = $getFriendRequests->fetchAll(PDO::FETCH_ASSOC);
			
			return $friendRequests;
		}
		catch(PDOException $e) {
			return "Could not retrieve friend request list.";
		}
	}
	
	function update_pending_friend_request($userID, $friendID, $requestStatus) {
		
		global $db_connection_handle;
		
		$userID = sanitize($userID);
		$friendID = sanitize($friendID);
		$requestStatus = sanitize($requestStatus);		
		
		if($requestStatus == 1)
		{
			$sql_insert = 'UPDATE Friendship
					SET Request_Status = :requestStatus
					WHERE User_One_ID = :friendID
					AND User_Two_ID = :userID';
					
			try {
				$updateFriendRequest=$db_connection_handle->prepare($sql_insert);
				$updateFriendRequest->bindParam(':requestStatus', $requestStatus, PDO::PARAM_INT);
				$updateFriendRequest->bindParam(':userID', $userID, PDO::PARAM_INT);
				$updateFriendRequest->bindParam(':friendID', $friendID, PDO::PARAM_INT);
				$updateFriendRequest->execute();
				
				return;
			}
			catch(PDOException $e) {
				return "Could not update friend request confirmation.";
			}
		} 
		else if ($requestStatus == 3) 
		{
			$sql_delete = 'DELETE FROM Friendship
						   WHERE User_One_ID = :friendID
					       AND User_Two_ID = :userID
						   AND Request_Status = 2';
			
			try {
				$deleteFriendRequest=$db_connection_handle->prepare($sql_delete);
				$deleteFriendRequest->bindParam(':userID', $userID, PDO::PARAM_INT);
				$deleteFriendRequest->bindParam(':friendID', $friendID, PDO::PARAM_INT);
				$deleteFriendRequest->execute();
				
				return;
			}
			catch(PDOException $e) {
				return "Could not delete friend request from Friendship table.";
			}
		}
	}
	
	function get_suggested_friends($userID) {
		
		global $db_connection_handle;
		
		$userID = sanitize($userID);
		
		$sql = 'SELECT UserID, First_Name, Last_Name
				FROM UserInfo
				WHERE UserID NOT IN (
					SELECT Friendship.User_One_ID AS UserID
					FROM UserInfo
					JOIN Friendship
					ON UserInfo.UserID = Friendship.User_One_ID
					WHERE Friendship.User_Two_ID = :userID)
				AND UserID NOT IN(
					SELECT Friendship.User_Two_ID AS UserID
					FROM UserInfo
					JOIN Friendship	
					ON UserInfo.UserID = Friendship.User_Two_ID
					WHERE Friendship.User_One_ID = :userID)
				AND UserID != :userID';
				
		try {
			$getSuggestedFriendsList=$db_connection_handle-> prepare($sql);
			$getSuggestedFriendsList->bindParam(':userID', $userID, PDO::PARAM_INT);
			$getSuggestedFriendsList->execute();
			
			$suggestedList = $getSuggestedFriendsList->fetchAll(PDO::FETCH_ASSOC);
			
			return $suggestedList;
		}
		catch(PDOException $e) {
			return "Could not retrieve list of suggested friends.";
		}
	}
 
	function send_friend_request($userID, $friendID) {
		
		global $db_connection_handle;
		
		$userID = sanitize($userID);
		$friendID = sanitize($friendID);
		
		$sql = 'INSERT INTO Friendship (User_One_ID, User_Two_ID, Request_Status)
				VALUES (:userID, :friendID, 2)';
				
		try {
			$insertFriendRequest=$db_connection_handle->prepare($sql);
			$insertFriendRequest->bindParam(':userID', $userID, PDO::PARAM_INT);
			$insertFriendRequest->bindParam(':friendID', $friendID, PDO::PARAM_INT);
			$insertFriendRequest->execute();
			
			return $insertFriendRequest;
		}
		catch(PDOException $e) {
			return "Could not insert new friend request.";
		}
	}
 
	function retrieve_private_messages($chatID) {
		
		global $db_connection_handle;

		$messages=array();
		
		$sql = 'SELECT UserID, Message, Time_Sent
				FROM PrivateChatMessages
				WHERE ChatID = :chatID';
				
		try {
			$getMessageHistory=$db_connection_handle->prepare($sql);
			$getMessageHistory->bindParam(':chatID', $chatID, PDO::PARAM_INT);
			$getMessageHistory->execute();
			
			$messages = $getMessageHistory->fetchAll(PDO::FETCH_ASSOC);
			
			return $messages;
		}
		catch(PDOException $e) {
			return "No messages found.";
		}
	}
	
	function retrieve_chat_ID($UserID, $FriendID) {
	
		global $db_connection_handle;

		$userID = sanitize($UserID);
		$friendID = sanitize($FriendID);
	
		$sql =  'SELECT COUNT(ChatID)
				 FROM PrivateChatInfo
				 WHERE User_One_ID = :userID AND User_Two_ID = :friendID
				 OR USER_One_ID = :friendID AND User_Two_ID = :userID';
		
		try {
			$findConvoID=$db_connection_handle->prepare($sql);
			$findConvoID->bindParam(':userID', $userID, PDO::PARAM_INT);
			$findConvoID->bindParam(':friendID', $friendID, PDO::PARAM_INT);
			$findConvoID->execute();
			
			if(($findConvoID->fetch(PDO::FETCH_NUM)[0]) != 0)
			{
				$sql2 = 'SELECT ChatID
						 FROM PrivateChatInfo
						 WHERE User_One_ID = :userID AND User_Two_ID = :friendID
						 OR USER_One_ID = :friendID AND User_Two_ID = :userID';
				
				try {
					$retrieveConvoID=$db_connection_handle->prepare($sql2);
					$retrieveConvoID->bindParam(':userID', $userID, PDO::PARAM_INT);
					$retrieveConvoID->bindParam(':friendID', $friendID, PDO::PARAM_INT);
					$retrieveConvoID->execute();
					
					$result = $retrieveConvoID->fetch(PDO::FETCH_ASSOC);
					$convoID = (int) $result['ChatID'];
					
					return $convoID;
				}
				catch (PDOException $e) {
				
				}				
			} else {
				$newConvoID = create_new_convoID($userID, $friendID);
				return  $newConvoID;
			}
		}
		catch (PDOException $e) {
				
		}	
	}
	
	function retrieve_unread_private_msgs($UserID, $FriendID) 
	{
		global $db_connection_handle;	
		
		$userID = sanitize($UserID);
		$friendID = sanitize($FriendID);
		$chatID = retrieve_chat_ID($UserID, $FriendID);
		
		$Unreadquery = 'SELECT COUNT(Unread_Read) AS Count
						FROM PrivateChatMessages
						WHERE Unread_Read = "No"
						AND ChatID = :chatID AND UserID = :friendID';
		try {
			$unreadMsgCount=$db_connection_handle->prepare($Unreadquery);
			$unreadMsgCount->bindParam(':chatID', $chatID, PDO::PARAM_INT);
			$unreadMsgCount->bindParam(':friendID', $friendID, PDO::PARAM_INT);
			$unreadMsgCount->execute();
			$count = $unreadMsgCount->fetch(PDO::FETCH_ASSOC);
			
			return $count['Count'];
		}
		catch (PDOException $e) {
			
		}
	}
	
	function update_read_msgs($ChatID, $FriendID) {
		global $db_connection_handle;
		
		$chatID = sanitize($ChatID);
		$friendID = sanitize($FriendID);
		
		$readquery = 'UPDATE PrivateChatMessages
					  SET Unread_Read = "Yes"
					  WHERE ChatID = :chatID AND UserID = :friendID';
		
		try {
			$updateReadMsg=$db_connection_handle->prepare($readquery);
			$updateReadMsg->bindParam(':chatID', $chatID, PDO::PARAM_INT);
			$updateReadMsg->bindParam(':friendID', $friendID, PDO::PARAM_INT);
			$updateReadMsg->execute();
			
			return;
			
		}
		catch (PDOException $e) {
			
		}
	}
	
	function create_new_convoID($userID, $friendID)
	{
		global $db_connection_handle;	
		
		$time_created = get_myDate();
		$status = "Active";
		
		$sql = 'INSERT INTO PrivateChatInfo (User_One_ID, User_Two_ID, Time_Created, Status)
				VALUES (:userID, :friendID, :time_created, :status)';
	
		try {
			$createNewConvoID=$db_connection_handle->prepare($sql);
			$createNewConvoID->bindParam(':userID', $userID, PDO::PARAM_INT);
			$createNewConvoID->bindParam(':friendID', $friendID, PDO::PARAM_INT);
			$createNewConvoID->bindParam(':time_created', $time_created, PDO::PARAM_STR);
			$createNewConvoID->bindParam(':status', $status, PDO::PARAM_STR);
			
			$newConvoID = $createNewConvoID->execute();
			
			return $newConvoID;
		}
		catch (PDOException $e) {
			
		}
	
	}
	
	function add_Message($chatID, $userID, $msg) {
		
		global $db_connection_handle;
	
		$addResult = false;
		
		$chatID = sanitize($chatID);
		$userID = sanitize($userID);
		$msg = sanitize($msg);
		$timestamp = get_myDate();
		
		
		$sql = "INSERT INTO PrivateChatMessages (ChatID,UserID,Message,Time_Sent,Unread_Read)
					VALUES (:chatID,:userID,:msg,:timestamp,'No')";
				
		try {
			$insertMessage=$db_connection_handle->prepare($sql);
			$insertMessage->bindParam(':chatID', $chatID, PDO::PARAM_INT);
			$insertMessage->bindParam(':userID', $userID, PDO::PARAM_INT);
			$insertMessage->bindParam(':msg', $msg, PDO::PARAM_LOB);
			$insertMessage->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
			
			if($insertMessage !== false) {
				$insertMessage->execute();
				$addResult = true;
			} else {
				echo $this->db_connection_handle->error;
			}
			
			return $addResult;
		}
		catch (PDOException $e)
		{
			return FALSE;
		}
	}
?>