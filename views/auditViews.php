<?php

require_once('../control/dbUserFunctions.php');
require_once('../control/dbLibrary.php');
require_once('./blobUpload.php');
/* 
	Views in the audit page of starnet screen.
	These will be only accesable by an admin.
*/
	
function auditForm(){
?>
<p>
	<div class="paragraph">
		<div>Also see: <a href="./badWords.php">Bad word management</a></div>
		Fill out fields you want to filter on. 
	</div>

	<div id="allAudits">
		<fieldset class="audit">
		<legend class="auditSubtitle">Users</legend>
			<form action="./audit.php" method="post">
				<table style="width:150" align="center">
			 		<tr>
						<th>Field</th>
						<th>Search restriction</th>
			 		</tr>
			 		<!--<tr>
						<th>UserID</th>
						<th><input type="number" name="UserID"></th>
			 		</tr>-->
			 		<tr>
						<th>Username</th>
						<th><input type="text" name="Username"></th>
					</tr>
			 		<tr>
						<th>First name</th>
						<th><input type="text" name="First_Name"></th>
					</tr>
			 		<tr>
						<th>Last name</th>
						<th><input type="text" name="Last_Name"></th>
					</tr>
			 		<tr>
						<th>Email</th>
						<th><input type="text" name="Email"></th>
					</tr>
			 		<tr>
						<th>Phone number</th>
						<th><input type="text" name="Phone_Number"></th>
					</tr>
			 		<tr>
						<th>User Type</th>
						<th><input type="text" name="User_Type"></th>
					</tr>
				</table>
	
				<input type="hidden" name="audit_type" value="user" />
				<input type="submit" value="Perform Audit" name="Submit">
			</form>
		</fieldset>

		<fieldset class="audit">
		<legend class="auditSubtitle">User Login History</legend>
			<form action="./audit.php" method="post">
				<table style="width:150" align="center">
			 		<tr>
						<th>Field</th>
						<th>Search restriction</th>
			 		</tr>
			 		<!--<tr>
						<th>UserID</th>
						<th><input type="number" name="UserID"></th>
			 		</tr>-->
			 		<tr>
						<th>Username</th>
						<th><input type="text" name="Username"></th>
					</tr>
			 		<tr>
						<th>First name</th>
						<th><input type="text" name="First_Name"></th>
					</tr>
			 		<tr>
						<th>Last name</th>
						<th><input type="text" name="Last_Name"></th>
					</tr>
			 		<tr>
						<th>Email</th>
						<th><input type="text" name="Email"></th>
					</tr>
			 		<tr>
						<th>Phone number</th>
						<th><input type="text" name="Phone_Number"></th>
					</tr>
			 		<tr>
						<th>User Type</th>
						<th><input type="text" name="User_Type"></th>
					</tr>
				</table>
	
				<input type="hidden" name="audit_type" value="user_login" />
				<input type="submit" value="Perform Audit" name="Submit">
			</form>
		</fieldset>

		<fieldset class="audit">
		<legend class="auditSubtitle">Forum Topic Search</legend>
			<form action="./audit.php" method="post">
				<table style="width:150" align="center">
			 		<tr>
						<th>Field</th>
						<th>Search restriction</th>
			 		</tr>
			 		<tr>
						<th>Topic Name</th>
						<th><input type="text" name="Name"></th>
					</tr>
				</table>
				<input type="hidden" name="audit_type" value="forumTopic" />
				<input type="submit" value="Perform audit" name="Submit">
			</form>
		</fieldset>

		<fieldset class="audit">
		<legend class="auditSubtitle">Bad Word Search (Public comments)</legend>
			<form action="./audit.php" method="post">
				<input type="hidden" name="audit_type" value="cussWord_pub" />
				<input type="submit" value="Perform audit" name="Submit">
			</form>
		</fieldset>
		
		<fieldset class="audit">
		<legend class="auditSubtitle">Bad Word Search (Private conversations)</legend>
			<form action="./audit.php" method="post">
				<input type="hidden" name="audit_type" value="cussWord_pm" />
				<input type="submit" value="Perform audit" name="Submit">
			</form>
		</fieldset>

	</div>
<?php
}


/*
	Given the initial query (eg: "SELECT * FROM UserInfo"), add the conditionals
	to the string query (eg: " WHERE UserID = :UserID", " AND Username = :Username")
	so that it can be later processed via PDO

	Input:
		The intial query
		An array of post data, where empty fields are removed from the assoc array.
	
	Output:
		The prepared query, filled with conditionals
			eg: SELECT * FROM Users WHERE UserID = :UserID AND Username = :Username
*/
function addConditionalsToQuery($query, $filledFormFields) {
	// Testing if we have to fill the conditional of our SQL statement
	if(count($filledFormFields) > 0) {
		$query .= " WHERE ";


		// Adding the conditionals to the query
		$i = 0;
		foreach($filledFormFields as $fieldKey => $fieldValue) {
			// Appending the new conditional
			$queryAppendage = $fieldKey . " LIKE :" . $fieldKey . " ";

			$query .= $queryAppendage;
			
			// If there's more conditionals after this, append an "AND" as well.
			if($i < count($filledFormFields) - 1) 
				$query .= " AND ";

			$i++;
		}
	}

	return $query;
}

/*
	When preparing the SQL query for the audit, we have to do some garbage
	to prepare the statement to account for all cases of how the user filled out the form.
	
	ie: They could filter on the userID field, but leave the rest blank.
	
	This approach fills each SQL condition with either the value from the HTML form,
	or with the table key if the HTML form field was empty (essentially applying no filter).


	Input:
		The post data, containing the fields we want to search on
		The initial query that we will append conditionals to
			eg: "SELECT * FROM Users"

	Output:
		The pdo statement, ready to execute
*/

function prepareAuditPDOStatement($postData, $query) {
	// Connecting to the DB
	try {
		global $db_connection_handle;
		db_connect();
	
	
		// -- Building our select query --
		/*
			We need to create an SQL query that will select everything from the users table,
			unless the admin has specified some sort of filter parameter in the audit form.
	
			This means that we have to dynamically construct our query, appending 
			conditionals in our input string as they appear.
			
			We will construct the PDO query first, then fill out the fields as they are used.
		*/

		// Figuring out which form values were left empty
		$filledFormFields = array();
		foreach($postData as $fieldKey => $fieldValue) {
			// Making sure that if someone messes with our HTML form, it doesn't break things.
			if($fieldValue != "") 
				$filledFormFields[sanitize($fieldKey)] = sanitize("%". $fieldValue ."%");
		}
	
		// Removing the submit button and the audit type
		unset($filledFormFields['Submit']);
		unset($filledFormFields['audit_type']);
		#echo "Here are the filled fields:<br />";
		#echo json_encode($filledFormFields);
	
	
		// Filling the query string with conditionals
		$query = addConditionalsToQuery($query, $filledFormFields);
		//echo "<br />Query: " . $query . "<br />";
		$stmt = $db_connection_handle->prepare($query);
		
		// Now that we have our query conditionals in place, we can start binding them.
		foreach($filledFormFields as $fieldKey => $fieldValue) {
			$stmt->bindParam(':' . $fieldKey, $fieldValue);
		}
	
	} catch(PDOException $e) {
		echo "PDO Exception: " . $e;
		return false;
	}


	return $stmt;
}

// An admin has just finished their audit and wants to print it to the page in the
// form of a nice JQuery Datatable.
// 
// This does that thing
//
// Input:
//	The PDO statement post-execution, ready to fetch results
// 
// Output:
//	The results of the SELECT query printed to a table.
function printAuditResultsToTable($pdoStmt, $tableHeaders) {
	// Printing the table
	echo "<table id='auditResults'>";
	
	// Looping through the headers
	echo "<thead>";
	foreach($tableHeaders as $th)
		echo "<th>" . $th . "</th>";
	echo "</thead>";

	// Looping through results of the query
	echo "<tbody>";
	while($row = $pdoStmt->fetch(PDO::FETCH_ASSOC)) {
		echo "<tr>";

		// Printing each value from the query
		foreach($row as $r) 
			echo "<td>" . $r . "</td>";

		echo "</tr>";
	}


	echo "</tbody>";
	echo "</table>";
}

// An admin has just finished their private chat audit and wants to print it to the page in the
// form of a nice JQuery Datatable.
// 
// This does that thing, except that we check to see which user was the sender, and which was the 
// reciever. What we get from the query result shows the user data for users 1 and 2, and also
// the user data of the sender (but not the reciever). 
//
// So, we have to figure out who the reciever is ourselves before we can print both users
// to the table.
//
// Input:
//	The PDO statement post-execution, ready to fetch results
// 
// Output:
//	The results of the user audit SELECT query printed to a table.
function printUserAuditResultsToTable($pdoStmt, $tableHeaders) {
	// Printing the table
	echo "<table id='auditResults'>";
	
	// Looping through the headers
	echo "<thead>";
	foreach($tableHeaders as $th)
		echo "<th>" . $th . "</th>";
	echo "</thead>";

	// Looping through results of the query
	echo "<tbody>";
	while($row = $pdoStmt->fetch(PDO::FETCH_ASSOC)) {
		echo "<tr>";

		// Printing each value from the query
		echo "<td>" . $row['MessageID'] . "</td>";
		echo "<td>" . $row['ChatID'] . "</td>";
		echo "<td>" . $row['Message'] . "</td>";
		echo "<td>" . $row['Time_Sent'] . "</td>";
		echo "<td>" . $row['Time_Created'] . "</td>";
		echo "<td>" . $row['Status'] . "</td>";
		//echo "<td>" . $row['SenderID'] . "</td>";

		// Since we don't know who the reciever is, we need to test who the reciever is.
		// This way, we can be certain that whoever the sender is will be printed under
		// the correct header.
		if($row['SenderID'] === $row['UserID_1']) {
			echo "<td>" . $row['UserID_1'] . "</td>";
			echo "<td>" . $row['First_Name_1'] . "</td>";
			echo "<td>" . $row['Last_Name_1'] . "</td>";
			echo "<td>" . $row['Email_1'] . "</td>";
			echo "<td>" . $row['Username_1'] . "</td>";

			echo "<td>" . $row['UserID_2'] . "</td>";
			echo "<td>" . $row['First_Name_2'] . "</td>";
			echo "<td>" . $row['Last_Name_2'] . "</td>";
			echo "<td>" . $row['Email_2'] . "</td>";
			echo "<td>" . $row['Username_2'] . "</td>";
		} else {
			echo "<td>" . $row['UserID_2'] . "</td>";
			echo "<td>" . $row['First_Name_2'] . "</td>";
			echo "<td>" . $row['Last_Name_2'] . "</td>";
			echo "<td>" . $row['Email_2'] . "</td>";
			echo "<td>" . $row['Username_2'] . "</td>";

			echo "<td>" . $row['UserID_1'] . "</td>";
			echo "<td>" . $row['First_Name_1'] . "</td>";
			echo "<td>" . $row['Last_Name_1'] . "</td>";
			echo "<td>" . $row['Email_1'] . "</td>";
			echo "<td>" . $row['Username_1'] . "</td>";
		}


		echo "</tr>";
	}


	echo "</tbody>";
	echo "</table>";

}

// An admin has just submitted the audit form for a user;
// Go through their filter specifications, and make an SQL select statement
// that will reflect their user filter requests.
//
// This audit will show users as specified by the search parameters.
//
// Input: 
//	Post data from audit request
//
// Output:
//	Results of the audit or something.
function userAudit($postData) {
	// Making sure we're actually performing a user audit, and that this function isn't 
	// being called improperly.
	if(!isset($postData['audit_type'])) return false;
	if($postData['audit_type'] !== 'user') return false;
	//echo json_encode($postData);

	try {
		$query = "SELECT UserID, First_Name, Last_Name, Email, Phone_Number, Username, User_TypeID
			FROM UserInfo";
		$stmt = prepareAuditPDOStatement($postData, $query);

		// Doing the thing
		$stmt->execute();
		echo "Statement executed successfully. Fetched " . $stmt->rowCount() . " users.<br />";

		// Printing the results to a jquery datatabe
		$headers = array();
		$headers[] = "User ID";
		$headers[] = "First Name";
		$headers[] = "Last Name";
		$headers[] = "Email";
		$headers[] = "Phone Number";
		$headers[] = "Username";
		$headers[] = "User Type";
		printAuditResultsToTable($stmt, $headers);



	} catch(Exception $e) {
		echo $e;
		return false;
	}

	return true;
}

// An admin has just submitted the audit form for a user;
// Go through their filter specifications, and make an SQL select statement
// that will reflect their user filter requests.
//
// This audit will show all the logins for a specific user
//
// Input: 
//	Post data from audit request
//
// Output:
//	Results of the audit or something.
function userLoginAudit($postData) {
	// Making sure we're actually performing a user audit, and that this function isn't 
	// being called improperly.
	if(!isset($postData['audit_type'])) return false;
	if($postData['audit_type'] !== 'user_login') return false;
	//echo json_encode($postData);

	try {
		$query = "SELECT UserLoginInfo.UserID, Username, First_Name, Last_Name, User_TypeID, Email, Phone_Number, Last_Login
			FROM UserInfo
			LEFT JOIN UserLoginInfo ON UserInfo.UserID = UserLoginInfo.UserID ";
		$stmt = prepareAuditPDOStatement($postData, $query);

		// Doing the thing
		$stmt->execute();
		echo "Statement executed successfully. Fetched : " . $stmt->rowCount() . " users. <br />";

		// Printing the results to a jquery datatabe

		$headers = array();
		$headers[] = "User ID";
		$headers[] = "Username";
		$headers[] = "First Name";
		$headers[] = "Last Name";
		$headers[] = "User Type";
		$headers[] = "Email";
		$headers[] = "Phone Number";
		$headers[] = "Last Login";
		printAuditResultsToTable($stmt, $headers);



	} catch(Exception $e) {
		echo $e;
		return false;
	}

	return true;
}

// An admin has just submitted the audit form for a user;
// Go through their filter specifications, and make an SQL select statement
// that will reflect their user filter requests.
//
// This audit searches for bad words written by users in conversations.
// This is a christian server, and we will NOT tolerate that >:(
//
// Input: 
//	Post data from audit request
//
// Output:
//	Results of the audit or something.
function cussWordPublicCommentAudit($postData) {
	// Making sure we're actually performing a user audit, and that this function isn't 
	// being called improperly.
	if(!isset($postData['audit_type'])) return false;
	if($postData['audit_type'] !== 'cussWord_pub') return false;
	//echo json_encode($postData);

	try {
		// Search for all of the private messages that contain a bad words (ie: words in the "Words" table.
		// From that, join the sender of that message with the user table to fetch info about the sender.
		// Also, join the private message with the PrivateChatInfo table to fetch information about the private chat itself.
		// Finally, join the PrivateChatInfo table with the UserInfo table to fetch information about the recieving user.
		$query = "
		select CommentID, UserInfo.UserID, User_TypeID, Username, First_Name, Last_Name, Email, Phone_Number, 
			CommentBody, DateCreated
		    FROM Comments
			    LEFT JOIN UserInfo on UserInfo.UserID = Comments.UserID
    		WHERE Comments.CommentBody IN (
    		    SELECT DISTINCT Word FROM Words
    		)";	


		$stmt = prepareAuditPDOStatement($postData, $query);

		// Doing the thing
		$stmt->execute();
		echo "Statement executed successfully. Row count: " . $stmt->rowCount() . "<br />";

		// Printing the results to a jquery datatabe
		$headers = array();
		$headers[] = "Comment ID";
		$headers[] = "User ID";
		$headers[] = "User Authority";
		$headers[] = "Username";
		$headers[] = "First Name";
		$headers[] = "Last Name";
		$headers[] = "Email";
		$headers[] = "Phone Number";
		$headers[] = "Comment";
		$headers[] = "Date of message";
		printAuditResultsToTable($stmt, $headers);

	} catch(Exception $e) {
		echo $e;
		return false;
	}

	return true;
}

// An admin has just submitted the audit form for a user;
// Go through their filter specifications, and make an SQL select statement
// that will reflect their user filter requests.
//
// This audit searches for bad words written by users in conversations.
// This is a christian server, and we will NOT tolerate that >:(
//
// Input: 
//	Post data from audit request
//
// Output:
//	Results of the audit or something.
function cussWordPrivateMessageAudit($postData) {
	// Making sure we're actually performing a user audit, and that this function isn't 
	// being called improperly.
	if(!isset($postData['audit_type'])) return false;
	if($postData['audit_type'] !== 'cussWord_pm') return false;
	//echo json_encode($postData);

	try {
		// Search for all of the private messages that contain a bad words (ie: words in the "Words" table.
		// From that, join the sender of that message with the user table to fetch info about the sender.
		// Also, join the private message with the PrivateChatInfo table to fetch information about the private chat itself.
		// Finally, join the PrivateChatInfo table with the UserInfo table to fetch information about the recieving user.
		$query = "
		SELECT PrivateChatMessages.ID as MessageID, PrivateChatMessages.ChatID, Message, Time_Sent, Time_Created, Status, PrivateChatMessages.UserID as SenderID,
			u1.UserID as UserID_1, u1.First_Name as First_Name_1, u1.Last_Name as Last_Name_1, u1.Email as Email_1, u1.Username as Username_1,
    		u2.UserID as UserID_2, u2.First_Name as First_Name_2, u2.Last_Name as Last_Name_2, u2.Email as Email_2, u2.Username as Username_2
    		    FROM (((PrivateChatMessages
    		    LEFT JOIN PrivateChatInfo ON PrivateChatMessages.ChatID = PrivateChatInfo.ChatID)
    		    LEFT JOIN UserInfo as u1 ON u1.UserID = User_One_ID)
    		    LEFT JOIN UserInfo as u2 ON u2.UserID = User_Two_ID)
    		WHERE PrivateChatMessages.Message IN (
    		    SELECT DISTINCT Word FROM Words
    		)";	


		$stmt = prepareAuditPDOStatement($postData, $query);

		// Doing the thing
		$stmt->execute();
		echo "Statement executed successfully. Row count: " . $stmt->rowCount() . "<br />";

		// Printing the results to a jquery datatabe
		$headers = array();
		$headers[] = "Message ID";
		$headers[] = "Chat ID";
		$headers[] = "Message Content";
		$headers[] = "Datetime Chat Created";
		$headers[] = "Datetime Message Sent";
		$headers[] = "Chat Status";
		//$headers[] = "tmp: senderUID";
		$headers[] = "Sender UserID";
		$headers[] = "Sender Username";
		$headers[] = "Sender First Name";
		$headers[] = "Sender Last Name";
		$headers[] = "Sender Email";
		$headers[] = "Reciever UserID";
		$headers[] = "Reciever Username";
		$headers[] = "Reciever First Name";
		$headers[] = "Reciever Last Name";
		$headers[] = "Reciever Email";
		printUserAuditResultsToTable($stmt, $headers);

	} catch(Exception $e) {
		echo $e;
		return false;
	}

	return true;
}


// An admin has just submitted the audit form for forum topics;
// Go through their filter specifications, and make an SQL select statement
// that will reflect their user filter requests.
//
// This audit will show all the forum topics
//
// Input: 
//	Post data from audit request
//
// Output:
//	Results of the audit or something.
function forumTopicAudit($postData) {
	// Making sure we're actually performing a user audit, and that this function isn't 
	// being called improperly.
	if(!isset($postData['audit_type'])) return false;
	if($postData['audit_type'] !== 'forumTopic') return false;
	//echo json_encode($postData);

	try {
		$query = "SELECT Convo_ID, Name, Date_Created FROM Conversation ";
		$stmt = prepareAuditPDOStatement($postData, $query);

		// Doing the thing
		$stmt->execute();
		echo "Statement executed successfully. Fetched : " . $stmt->rowCount() . " users. <br />";

		// Printing the results to a jquery datatabe
		$headers = array();
		$headers[] = "Conversation ID";
		$headers[] = "Topic Name";
		$headers[] = "Date Created";
		printAuditResultsToTable($stmt, $headers);



	} catch(Exception $e) {
		echo $e;
		return false;
	}

	return true;
}

?>
