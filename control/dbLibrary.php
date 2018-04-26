<?php

/*
*	dbLibrary
*	_________
*	This Library of functions is mainly dedicated to security and log in functions
*	or user related functions within the site.
*	
*	Any functions that did not have a nice fit within another library were dropped in here.
*
*/
    
    require_once('config.php');
    require_once('dbUserFunctions.php');

    $db_connection_handle = NULL;
    
    function db_connect()
    {
        global $DBUSER, $DBPASS, $DBNAME, $db_connection_handle;
        
        $db_connection_handle = new PDO("mysql:host=localhost;dbname=$DBNAME", $DBUSER, $DBPASS);
        $db_connection_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db_connection_handle->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $db_connection_handle->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        
        //echo "connected.<br/>";
        
    }
    
    /*   
    	dbCredentials-Check user for valid credentials 
    	Input: userID and password. 
    	Output: True upon sucessful credential check, false otherwise
    */
    
    function dbCredentials($userID, $pass)
    {
        global $db_connection_handle;
        
    	//clean input
    	$userID = sanitize($userID);
		$pass = sanitize($pass);
		// make sure only get last login status item to save if someone is banned or status is kept constant.
        $sql = 'SELECT UserInfo.UserID, Password, Status_ID
        		FROM UserInfo 
        		JOIN UserLoginInfo ON UserInfo.UserID = UserLoginInfo.UserID
        		WHERE Username= :user 
        		ORDER BY Last_Login DESC
        		LIMIT 1';
        $ad_pass = myencrypt($pass);

        try
        {
            $st = $db_connection_handle->prepare($sql);
            $st->bindParam(':user', $userID, PDO::PARAM_STR, 8);
            $st->execute();
            
            $user = $st->fetch(PDO::FETCH_ASSOC);
            // get the last status of the user.
            $lastStatus = get_status($user['UserID']);
            // set session status id variable
            $_SESSION['Status_ID'] = $lastStatus['Status_ID'];
			$date = get_myDate();
			// set ban
			$ban = get_ban($user['UserID']);
			
			if(isset($ban['Ban_Reason_Id'])){
				$_SESSION['Banned'] = TRUE;
				return FALSE;
			}
			else
				$_SESSION['Banned'] = FALSE;
			
            if (strcmp($user['Password'], $ad_pass) == 0){
	            //set the user last log in setting.
	            $sql2 = "INSERT INTO UserLoginInfo (UserID, Last_Login, Status_ID)
	    		VALUES (:UserID, :Last_Login, :Status_ID)";

	            $add2 = $db_connection_handle->prepare($sql2);
				$add2->bindParam(':UserID', $user['UserID'], PDO::PARAM_STR, 20);
				$add2->bindParam(':Last_Login', $date , PDO::PARAM_STR, 150);
				$add2->bindParam(':Status_ID', $user['Status_ID'], PDO::PARAM_STR, 20);
				if($add2->execute())
                	return TRUE;
            }
            else
            	return FALSE;
        }
        catch (PDOException $e)
        {
            return FALSE;
        }
    }
    
    /* 
    	Checks userID to ensure no other users with that userID in system. 
    	Input: userID value
    	Output: True if userID already exists, False if userID does not exist	
    */
    
    function check_username($Username){
    
    	//clean input
    	$Username = sanitize($Username);

        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT COUNT(Username) FROM UserInfo WHERE Username= :Username';
        
        try
        {
            $st = $db_connection_handle->prepare($sql);
            $st->bindParam(':Username', $Username, PDO::PARAM_STR, 20);
            $st->execute();
            if (($st->fetch(PDO::FETCH_NUM)[0]) == 1)
                return true;
            return false;
        }
        catch (PDOException $e)
        {
            return FALSE;
        }
    }


	/* 
    	Checks email to ensure no other users with that Email in system. 
    	Input: Email value
    	Output: True if Email already exists, False if Email does not exist	
    */

    function check_email($Email){

    	//clean input
    	$Email = sanitize($Email);

        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT COUNT(Email) FROM UserInfo WHERE Email= :Email';
        
        try
        {
            $st = $db_connection_handle->prepare($sql);
            $st->bindParam(':Email', $Email, PDO::PARAM_STR, 20);
            $st->execute();
            if (($st->fetch(PDO::FETCH_NUM)[0]) == 1){
                return true;
            }
            return false;
        }
        catch (PDOException $e)
        {
            return FALSE;
        }
    }
    
    
    function check_username_againstID($Username, $UserID){
	    //clean input
    	$Username = sanitize($Username);
    	$UserID = sanitize($Username);

        global $db_connection_handle;
        
        // Count records where username is the same but has a different UserID
        $sql = 'SELECT COUNT(Username) FROM UserInfo WHERE Username= :Username AND NOT UserID = :UserID';
        
        try
        {
            $st = $db_connection_handle->prepare($sql);
            $st->bindParam(':Username', $Username, PDO::PARAM_STR, 20);
            $st->bindParam(':UserID', $UserID, PDO::PARAM_STR);
            $st->execute();
            //echo $st->fetch(PDO::FETCH_NUM)[0];
            if (($st->fetch(PDO::FETCH_NUM)[0]) == 1)
                return true;
            return false;
        }
        catch (PDOException $e)
        {
            return FALSE;
        }
    
    }
    
    /* 
    	check to see if user level is management level 1 
		Input: $userID value
		Output: True if level is 1, False otherwise
	*/
    
    function check_level($userID){
    
    	//clean input
    	$userID = sanitize($userID);
        
        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT level FROM user WHERE userID=:userID';
        
        try
        {
            $st = $db_connection_handle->prepare($sql);
            $st->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
            $st->execute($user_array);
            
            $user_level = $st->fetch(PDO::FETCH_ASSOC);
            if($user_level['level']==1)
            	return TRUE;
			return FALSE;
			
        }
        catch (PDOException $e)
        {
            return "Error" . $e->getMessage();
        }
    }
    
        /* 
    	check to see if user level is management level 1 
		Input: $userID value
		Output: True if level is 1, False otherwise
	*/
    
    function check_level_trainer($userID){
    
    	//clean input
    	$userID = sanitize($userID);
    	//echo $userID;
        
        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT level FROM user WHERE userID=:userID';
        
        try
        {
            $st = $db_connection_handle->prepare($sql);
            $st->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
            $st->execute($user_array);
            
            $user_level = $st->fetch(PDO::FETCH_ASSOC);
            if($user_level['level']==2)
            	return TRUE;
			return FALSE;
			
        }
        catch (PDOException $e)
        {
            return "Error" . $e->getMessage();
        }
    }
    
    /* 
    	Get user information 
    	Input: userID
    	Output: Array of driver information[fname,lname,userID,trainer,level]
    */
    
    function get_user($userID){
    
    	//clean input
    	$userID = sanitize($userID);
        
        global $db_connection_handle;
        

        $sql = 'SELECT UserID, First_Name, Last_Name, Username, Email, Phone_Number, User_TypeID, UserType.Type_Desc 
		FROM UserInfo JOIN UserType 
		ON UserType.User_Type_ID = UserInfo.User_TypeID
		WHERE UserID=:userID';  
		      
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            return $userinfo;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }
    
    function get_status($userID){
    
    	//clean input
    	$userID = sanitize($userID);
        
        global $db_connection_handle;
        

        $sql = 'SELECT UserInfo.UserID, ID, UserLoginInfo.Last_Login, UserLoginInfo.Status_ID
				FROM UserInfo 
				JOIN UserLoginInfo ON UserLoginInfo.UserID = UserInfo.UserID
				WHERE UserInfo.UserID=:userID 
				ORDER BY Last_Login DESC 
				LIMIT 1';
		      
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            return $userinfo;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }
    
    function get_ban($userID){
    
    	//clean input
    	$userID = sanitize($userID);
        
        global $db_connection_handle;
        

        $sql = 'SELECT *
				FROM User_Ban 
				WHERE User_Id=:userID 
				ORDER BY Id DESC 
				LIMIT 1';
		      
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            //compare dates, if expired and only temporary ban then return NULL, otherwise return details.
            $expiryDate = strtotime($userinfo['Expiry_Date']);
            $currentDate = strtotime(get_myDate());

            if($expiryDate > $currentDate){
            	return $userinfo;
            	}
            else
            	return NULL;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }

    
    function get_type_desc(){
        
        global $db_connection_handle;
        

        $sql = 'SELECT * FROM UserType';  
		      
        try{
            $getdesc=$db_connection_handle->prepare($sql);
            $getdesc->execute();
            
            return $getdesc->fetchAll(PDO::FETCH_ASSOC);

        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }

	function get_status_desc(){
        
        global $db_connection_handle;
        

        $sql = 'SELECT * FROM Status';  
		      
        try{
            $getdesc=$db_connection_handle->prepare($sql);
            $getdesc->execute();
            
            return $getdesc->fetchAll(PDO::FETCH_ASSOC);
            
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }

    
    
    // returns everything about a user, except the password
    function get_user_by_Username($Username){
    
    	//clean input
    	$Username = sanitize($Username);
        
        global $db_connection_handle;

        $sql = 'SELECT UserInfo.UserID, First_Name, Last_Name, Username, Email, Phone_Number, User_TypeID, UserType.Type_Desc 
        		FROM UserInfo 
        		JOIN UserType ON UserType.User_Type_ID = UserInfo.User_TypeID
        		WHERE Username=:Username';
        
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->bindParam(':Username', $Username, PDO::PARAM_STR, 20);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            return $userinfo;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }
    
    
        // returns everything about a user, except the password
    function get_user_by_Email($Email){
    
    	//clean input
    	$Email = sanitize($Email);
        
        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT UserID, First_Name, Last_Name, Username, Email, Phone_Number, UserType.Type_Desc 
        		FROM UserInfo 
        		JOIN UserType ON UserType.User_Type_ID = UserInfo.User_TypeID 
        		WHERE Email=:Email';
        
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->bindParam(':Email', $Email, PDO::PARAM_STR, 20);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            return $userinfo;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }

    
    // if the old password matches the database
    function check_password($UserID, $providedPassword){
        
        global $db_connection_handle;
 
        $sql = 'SELECT Password FROM UserInfo WHERE UserID=:UserID';
        $providedPassword = myencrypt($providedPassword);
        
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->bindParam(':UserID', $UserID, PDO::PARAM_STR, 11);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            if(strcmp($userinfo['Password'],$providedPassword) == 0)
            	return true;
            return false;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }
    
    function get_trainer($fname){
	        
	    //clean input
    	$fname = sanitize($fname);
        
        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT IDNumber, fname, lname, userID, trainer, level FROM user WHERE fname=:fname and level=2';
        
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->bindParam(':fname', $fname, PDO::PARAM_STR, 8);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            return $userinfo;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }
    
        function get_trainersID(){
	        	//clean input
        
        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT IDNumber FROM user WHERE level=2 ORDER BY IDNumber';
        
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            return $userinfo;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }
    
    /* 
    	Displays user name and log out button on sidebar after log in. 
    	Input: current logged in user (session[loggedin])
    	Output: Displays welcome message and logout button.
    */
    
    function db_display_logout_button($userID){
    
    	//clean input
    	$userID = sanitize($userID);
    
        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT * FROM user WHERE userID= :userID';
        
        try{
            $getuser=$db_connection_handle->prepare($sql);
            $getuser->bindParam(':userID', $userID, PDO::PARAM_STR, 8);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            if(isset($userinfo['fname'])){
                echo "<b>Hi </b>";
                echo "<b>" . $userinfo['fname'] . "</b>";
                echo "<form method=\"post\" action=\"";
                get_home();
                echo "/control/log_out.php\"><input type=\"submit\" value=\"Logout\"/></form>";
				if(isset($userinfo['picname'])){
                	echo "<p><img src=\"";
                	get_home();
                	echo "/views/images/".$userinfo['picname']."\" alt=\"driver\"  /></p>";
				}     
                return;
            }
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
    }
    
    /* 
    	Returns the value of the User's name eg. "John Doe" 
		Input: userID (session[loggedin])
		Output: prints user first and last name, and returns array of info
    */
    
    function db_get_user_by_id($userID){
    
    	//clean input
    	$userID = sanitize($userID);
        
        global $db_connection_handle;
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT * FROM user WHERE userID=:userID';
        
        try{
            $getuser = $db_connection_handle->prepare($sql);
            $getuser->bindParam('userID', $userID, PDO::PARAM_STR, 8);
            $getuser->execute();
            
            $userinfo = $getuser->fetch(PDO::FETCH_ASSOC);
            
            if(isset($userinfo['fname'])){
                echo $userinfo['fname'] . " " . $userinfo['lname'];
                return $userinfo;
                
            }
            else{
                die("User doesn't exist");
            }
        }
        catch (PDOException $e){
            return "Error" . $e->getMessage();
    	}
    }
    
    
    
//     	get the date and time for Detroit Timezone.

	function get_myDate(){
			date_default_timezone_set("America/Detroit");
			return date("Y-m-d h:i:s");
	}
    
    /*
    	sanitize input- Clean input.
    */
    
    function sanitize($value){
    
        $value = htmlentities($value);
		$value = strip_tags($value);
		return stripcslashes($value);
	   
    }
    
    /* 
    	kickout - Function used at the top of each view to kick out unwanted visitors.
    	- redirects to unwanted page if user not logged in with proper credentials. 
    	Input: nothing
    	Output: nothing
    */
    	
    function kickout(){

    if(!(check_username($_SESSION['loggedin']))){ 	
		header('Location: invalid.php');
		return;
		}
    }
    



	
