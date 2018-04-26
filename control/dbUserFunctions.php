<?php

require_once('dbLibrary.php');

    /* 
    	Add user function-checks for duplicate entry prior to adding to database
    	Input: $Username, $Password, $Email, $First_Name, $Last_Name, $Phone_Number
    	*** adjust the Usertype. just defaulting to 1 right now.
    	Output: True upon sucessful execute, False otherwise.
    	
    */
    
    function add_user($Username, $Password, $Email, $First_Name, $Last_Name, $Phone_Number){
    	
    	// indicates that userID or email already exists

    	if(check_username($Username))
    		return false;
    	
    	if(check_email($Email))
    		return false;
    		
    	
    	
    	// sanitize input htmlentities/strip_tags/stripcslashes
    	$Username = sanitize($Username);
    	$Password = sanitize($Password);
    	$Email = sanitize($Email);
    	$First_Name = sanitize($First_Name);
    	$Last_Name = sanitize($Last_Name);
    	$Phone_Number = sanitize($Phone_Number);

    	$Password = myencrypt($Password);
    	//uncomment for submission to check emails.
/*
    	if(!filter_var($Email, FILTER_VALIDATE_EMAIL))
    		return false;
*/
	    
	    global $db_connection_handle;
	    //$user_array = array(':userID' => $userID);
	    $sql = "INSERT INTO UserInfo (Username, Password, Email, First_Name, Last_Name, Phone_Number) 
	    			VALUES (:Username, :Password, :Email, :First_Name, :Last_Name, :Phone_Number)";
	    
	    $sql2 = "INSERT INTO UserLoginInfo (UserID, Last_Login, Status_ID)
	    			VALUES (:UserID, :Last_Login, :Status_ID)";
	    
	    try{			
	    	$add = $db_connection_handle->prepare($sql);
	    	$add->bindParam(':Username', $Username, PDO::PARAM_STR, 8);
	    	$add->bindParam(':Password', $Password, PDO::PARAM_STR, 150);
	    	$add->bindParam(':Email', $Email, PDO::PARAM_STR, 8);
	    	$add->bindParam(':First_Name', $First_Name, PDO::PARAM_STR, 8);
	    	$add->bindParam(':Last_Name', $Last_Name, PDO::PARAM_STR, 8);
	    	$add->bindParam(':Phone_Number', $Phone_Number, PDO::PARAM_STR, 20);
			$worked = $add->execute();
			
			$user = get_user_by_Username($Username);
			$date = get_myDate();
			$status = 1;
			
			$add2 = $db_connection_handle->prepare($sql2);
	    	$add2->bindParam(':UserID', $user['UserID'], PDO::PARAM_STR, 8);
	    	$add2->bindParam(':Last_Login', $date , PDO::PARAM_STR, 150);
	    	$add2->bindParam(':Status_ID', $status , PDO::PARAM_STR, 8);
			$add2->execute();

			// log the user in after registering... 
			$_SESSION['loggedin'] = $Username;
			$_SESSION['Status_ID'] = $status;
			$_SESSION['Banned'] = "No";

			// ensures name is in database after function to install.
			if(check_username($Username))
				return true;
			return false;
			}
		catch(PDOException $e){
			return "Error inputing data -> " . $e->getMessage();

		}
			
    }
    /*
		Send 
	*/
    function retrieve_password($Username, $Email){
	    
	    // indicates that userID exists
	    if(strcmp($Username, "") != 0){
	    	if(check_username($Username)){
	    		return reset_password(get_user_by_Username($Username));
	    	}
	    	else
	    		return false;
    	}
    	// indicates that Email exists
    	elseif(strcmp($Email, "") != 0){
	    	if (check_email($Email)){
	    		return reset_password(get_user_by_Email($Email));
	    	}
	    	else
	    		return false;
	    }
	    // nothing set or user does not exist.
    	else
    		return false;
    }
    
    function reset_password($User){
	    $newPassword= random_int(10000, 65000);
	    $newEncrypt= myencrypt($newPassword);

	    global $db_connection_handle;
	    $id = $User['UserID'];
	    $sql = "UPDATE UserInfo SET
	    			Password= :newPassword
	    			WHERE UserID= :UserID";

		try{			
	    	$resetPass = $db_connection_handle->prepare($sql);
	    	$resetPass->bindParam(':newPassword', $newEncrypt, PDO::PARAM_STR, 150);
	    	$resetPass->bindParam(':UserID', $id, PDO::PARAM_STR, 20);
			$resetPass->execute();
			
			$subject = "Password Reset";
			$message = "
			Your new password is \"$newPassword\". It is highly recommended you update your password upon recovery.
			
			Thanks
			The Starnet Team";
						
			return mail($User['Email'], $subject, $message);

		}
		catch(PDOException $e){
			return "Error inputing data -> " . $e->getMessage();
		}

    }
    
    /* 
    	Delete user function ** Function removes both Test information and Driver information & comments**
    	Input: UserID	
    	Output: Returns true upon success, False otherwise
    */
    
    function delete_user($userID){
    
    	//ensure user level is 1
    	if(!check_level($_SESSION['loggedin']))
    		return FALSE;
    		
    	// sanitize input
    	$userID = htmlentities($userID);
    	$userID = strip_tags($userID);
	    
	    global $db_connection_handle;
	    $user_array = array(':userID' => $userID);
	    $sql = "DELETE FROM quiz_results WHERE userID= :userID";
	    $sql1 = "DELETE FROM comments WHERE userID= :userID";
		$sql2 = "DELETE FROM user WHERE userID= :userID";
		
	    try{			
	    	$remove = $db_connection_handle->prepare($sql);
	    	$remove->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
			$remove->execute();
			$remove1 = $db_connection_handle->prepare($sql1);
			$remove1->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
			$remove1->execute();
			$remove2 = $db_connection_handle->prepare($sql2);
			$remove2->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
			$remove2->execute();
			
			if (!check_username($userID))
				return true;
			return false;
			}
		catch(PDOException $e){
			return "Error inputing data -> " . $e->getMessage();
		}
			
    }
    
    /* 
    	Update user function 
    	Input: Old userID, New userID, User level, Given Name, Surname, Trainer
    	Output: True on successful changes, False otherwise	
    */
    
    function update_user($oldPassword, $newPassword, $oldUsername, $Username, $Email, $First_Name, $Last_Name, $Phone_Number, $UserID){
    
    	
    	// if provided old password is not same as database password return false for updating info.
		if(!check_password($UserID, $oldPassword))
			return false;
		// check username not in database, if it is, return false. Also check if it is the same as before
		if(strcmp($oldUsername, $Username)!=0){
			if(check_username_againstID($Username, $UserID))
    			return false;
    	}

    	// check if user is attempting to set a new password.
    	$Password = $newPassword;
		if(strcmp($Password, "") == 0){
			$Password = $oldPassword;
		}
    		
    	// sanitize input
    	$Username = sanitize($Username);
    	$Email = sanitize($Email);
    	$First_Name = sanitize($First_Name);
    	$Last_Name = sanitize($Last_Name);
    	$Phone_Number = sanitize($Phone_Number);
    	$Password = sanitize($Password);
				
		// encrypt password
		$Password = myencrypt($Password);
					
	    global $db_connection_handle;
		
	    $sql = "UPDATE UserInfo SET
	    			First_Name= :First_Name ,
	    			Last_Name= :Last_Name ,
	    			Username= :Username ,
	    			Phone_Number= :Phone_Number, 
	    			Email= :Email ,
	    			Password= :Password
	    			WHERE UserID= :UserID";
	    			
	    // confirm changes
	    $sql2 = "SELECT * FROM UserInfo WHERE UserID= :UserID";
	    try{
	    	// execute command to change information			
	    	$add = $db_connection_handle->prepare($sql);
	    	$add->bindParam(':First_Name', $First_Name, PDO::PARAM_STR, 20);
	    	$add->bindParam(':Last_Name', $Last_Name, PDO::PARAM_STR, 20);
	    	$add->bindParam(':Username', $Username, PDO::PARAM_STR, 20);
	    	$add->bindParam(':Phone_Number', $Phone_Number, PDO::PARAM_STR, 15);
	    	$add->bindParam(':Email', $Email, PDO::PARAM_STR, 30);
	    	$add->bindParam(':UserID', $UserID, PDO::PARAM_STR, 11);
	    	$add->bindParam(':Password', $Password, PDO::PARAM_STR, 150);
			$add->execute();
			
			// check if change occured and return
			$add1 = $db_connection_handle->prepare($sql2);
			$add1->bindParam(':UserID', $UserID, PDO::PARAM_STR, 11);
			$add1->execute();
			
			$userinfo = $add1->fetch(PDO::FETCH_ASSOC);
			// **** YOU MUST RESET THE SESSION LOGGED IN PERSON TO PROPERLY DISPLAY THE LOGGED IN USER. ****// 
			$_SESSION['loggedin'] = $userinfo['Username'];
			
			// check changes took place in database.
			if ( strcmp($userinfo['First_Name'], $First_Name) == 0 &&
					strcmp($userinfo['Last_Name'], $Last_Name) == 0 &&
					strcmp($userinfo['Username'], $Username) == 0 &&
					strcmp($userinfo['Phone_Number'], $Phone_Number) == 0 &&
					strcmp($userinfo['Password'], $Password) == 0 &&
					strcmp($userinfo['Email'], $Email) == 0)
                return true;
            return false;
            
		}
		catch(PDOException $e){
			echo "Error inputing data -> " . $e->getMessage();
			exit();
			return "Error inputing data -> " . $e->getMessage();
		}
			
    }
    
    function updateUserStatus( $UserID , $Status_ID){
	    // sanitize input
    	$UserID = sanitize($UserID);
    	$newStatus_ID = sanitize($Status_ID);
    	$oldStatus = get_status($UserID);
					
	    global $db_connection_handle;
	    
	    $sql = "UPDATE UserLoginInfo SET
	    			Status_ID = :newStatus
	    			WHERE ID = :myID";
	    			
	    try{
	    	// execute command to change information			
	    	$add = $db_connection_handle->prepare($sql);
	    	$add->bindParam(':newStatus', $newStatus_ID, PDO::PARAM_STR, 11);
	    	$add->bindParam(':myID', $oldStatus['ID'], PDO::PARAM_STR, 11);
			$add->execute();
			
			return TRUE;
            
		}
		catch(PDOException $e){
			echo "Error inputing data -> " . $e->getMessage();
			exit();
			return "Error inputing data -> " . $e->getMessage();
		}
			
    }
    
    function updateUserType($UserID , $Type_Desc, $sessionUser){
	    // sanitize input
    	$UserID = sanitize($UserID);
    	$newType_Desc = sanitize($Type_Desc);
    	$oldUserType = get_user($UserID);
					
	    global $db_connection_handle;
	    
	    switch($Type_Desc){
	    	case "Administrator":
	    		$newType_Desc = 1;
	    		break;
	    	case "Moderator":
	    		$newType_Desc = 2;
	    		break;
	    	default:
	    		$newType_Desc = 3;
    	}
    	// If trying to assign someone a higher privilage level that you are, return false.
    	if($newType_Desc < $sessionUser)
    		return FALSE;

	    
	    $sql = "UPDATE UserInfo SET
	    			User_TypeID = :newType
	    			WHERE UserID = :myID";
	    			
	    try{
	    	// execute command to change information			
	    	$add = $db_connection_handle->prepare($sql);
	    	$add->bindParam(':newType', $newType_Desc, PDO::PARAM_STR, 11);
	    	$add->bindParam(':myID', $oldUserType['UserID'], PDO::PARAM_STR, 11);
			$add->execute();
			
			return TRUE;
            
		}
		catch(PDOException $e){
			echo "Error inputing data -> " . $e->getMessage();
			exit();
			return "Error inputing data -> " . $e->getMessage();
		}
			
    }
    
    
    function banUser($UserID, $Ban_Type, $DatePicker){
	    // sanitize input
    	$banType = sanitize($Ban_Type);
    	$banDate = sanitize($DatePicker);
    	date_default_timezone_set("America/Detroit");
    	if($banType == 1){
	    	$banDate = "2217-01-01 " . date("h:i:s");
    	}
    	else{
	    	$list = explode("/", $banDate);
	    	$banDate = $list[2] . "-" . $list[0] . "-" . $list[1];
	    	$banDate = $banDate . " " . date("h:i:s");
    	}

    	$UserID = sanitize($UserID);
					
	    global $db_connection_handle;
	    
	    $sql = "INSERT INTO User_Ban (User_Id, Ban_Reason_Id, Expiry_Date, Start_Date) 
			VALUES (:UserID, :Ban_Reason_Id, :Expiry_Date, :Expiry_Date)";
	    			
	    try{
	    	// execute command to change information			
	    	$add = $db_connection_handle->prepare($sql);
	    	$add->bindParam(':UserID', $UserID, PDO::PARAM_STR, 11);
	    	$add->bindParam(':Expiry_Date', $banDate, PDO::PARAM_STR, 30);
	    	$add->bindParam(':Ban_Reason_Id', $banType, PDO::PARAM_STR, 11);
			$add->execute();
			
			return TRUE;
            
		}
		catch(PDOException $e){
			echo "Error inputing data -> " . $e->getMessage();
			exit();
			return "Error inputing data -> " . $e->getMessage();
		}
			
    }
    
    function unBanUser($UserID){
	    // sanitize input
    	$banType = 2;
    	date_default_timezone_set("America/Detroit");
    	$banDate = "2017-01-01" . " " . date("h:i:s");

    	$UserID = sanitize($UserID);
					
	    global $db_connection_handle;
	    
		$ban = get_ban($UserID);

	    $sql = "UPDATE User_Ban SET
			Ban_Reason_Id = :Ban_Reason_Id,
			Expiry_Date = :Expiry_Date
			WHERE Id = :Id";
	    			
	    try{
	    	// execute command to change information			
	    	$add = $db_connection_handle->prepare($sql);
	    	$add->bindParam(':Ban_Reason_Id', $banType, PDO::PARAM_STR, 11);
	    	$add->bindParam(':Expiry_Date', $banDate, PDO::PARAM_STR, 30);
	    	$add->bindParam(':Id', $ban['Id'], PDO::PARAM_STR, 11);
			$add->execute();
			
			return TRUE;
            
		}
		catch(PDOException $e){
			echo "Error inputing data -> " . $e->getMessage();
			return "Error inputing data -> " . $e->getMessage();
		}
			
    }

    
    // returns everything about a user, except the password
    function listUsers(){
    
        
        global $db_connection_handle;

        $sql = 'SELECT UserID, First_Name, Last_Name, Username, Email, Phone_Number, UserType.Type_Desc 
        		FROM UserInfo JOIN UserType 
        		ON UserType.User_Type_ID = UserInfo.User_TypeID';
        
        try{
            $getusers=$db_connection_handle->prepare($sql);
            $getusers->execute();
            
            $userinfo = $getusers->fetchAll(PDO::FETCH_ASSOC);
            
            return $userinfo;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }
    
	    
    /*
    	Search All function
    	Input: logged in user id
    	Output: array of all users in DB with values:
    				first name
    				last name
    				trainer
    				level
    				userID
    			and ordered by last name then first name.
    */
    
    function search_all($userID){
		
		global $db_connection_handle;

		$sql = "SELECT fname, lname, trainer, level, userID FROM user ORDER BY lname, fname";
		
		try{
			$userinfo = $db_connection_handle->prepare($sql);
			$userinfo->execute();
			
			$users = $userinfo->fetchAll(PDO::FETCH_ASSOC);
			
			return $users;
		}
		catch(PDOException $e){
			return $sql . " <br/> " . $e->getMessage();
		}
		
	}
	
	
	/*
		Search all students specific to trainer value.
			
	*/
		
	function search_allTrainer($userID){
		
		global $db_connection_handle;
		
		$trainer = get_user($userID);
		$trainerID = $trainer['IDNumber'];

		$sql = "SELECT fname, lname, trainer, level, userID 
				FROM user 
				WHERE trainer= $trainerID
				ORDER BY lname, fname";
		
		try{
			$userinfo = $db_connection_handle->prepare($sql);
			$userinfo->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
			$userinfo->execute();
			
			$users = $userinfo->fetchAll(PDO::FETCH_ASSOC);
			
			return $users;
		}
		catch(PDOException $e){
			return $sql . " <br/> " . $e->getMessage();
		}
		
	}
	


	function myencrypt($Password){
		// salt & encrypt new password
		$saltbegin = "k(&76u%";
    	$saltend = "nb$430#";
    	$Password = $saltbegin.$Password.$saltend;
    	return hash('sha256', "$Password");
	}

?>