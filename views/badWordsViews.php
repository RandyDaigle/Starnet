<?php

require_once('../control/dbUserFunctions.php');
require_once('../control/dbLibrary.php');
require_once('./blobUpload.php');
require_once('./auditViews.php');
/* 
	Views in the audit page of starnet screen.
	These will be only accesable by an admin.
*/
	
function badWordForm(){
?>
<p>
	<div id="formWrapper">
		<fieldset class="auditFieldset">
		<legend class="fieldsetLegend">Add Bad Words</legend>
		Please enter all the bad words users shouldn't use. One word per line please.
			<form action="./badWords.php" method="post">
				<div>
				<textarea name="badWords" rows=10></textarea>
				</div>
				<input type="hidden" name="audit_type" value="badWords_add" />
				<input type="submit" name="Submit">
			</form>
		</fieldset>

		<fieldset class="auditFieldset">
		<legend class="fieldsetLegend">View Bad Words</legend>
			<form action="./badWords.php" method="post">
				<table style="width:150" align="center">
					<tr>
						<th>Field</th>
						<th>Search Restriction</th>
					</tr>
					<tr>
						<th>Word</th>
						<td><input type="text" name="Word"></td>
					</tr>
				</table>

				<input type="hidden" name="audit_type" value="badWords_view" />
				<input type="submit" name="Submit">
			</form>
		</fieldset>
	</div>
<?php
}

function addBadWords($postData) {
    // Making sure we're actually removing bad words, and that this function isn't 
    // being called improperly.
    if(!isset($postData['audit_type'])) return false;
    if($postData['audit_type'] !== 'badWords_add') return false;
	//echo json_encode($postData);

    try {
		// Testing if we have anything to add to the query
		if(isset($postData['badWords'])) {
			if($postData['badWords'] != "") {
				// Sanatizing data
				foreach(explode(PHP_EOL, $postData['badWords']) as $line)
					$sanitizedData[] = trim(sanitize($line));
	
				// Building query
				$query = "INSERT INTO Words (Word) VALUES (?)";
	
	
		        // Doing the thing
		    	global $db_connection_handle;
		    	db_connect();
			    $stmt = $db_connection_handle->prepare($query);
	
				// Executing the statement for each word
				echo "Adding words";
				$i = 0;
				foreach($sanitizedData as $wordID) {
					$stmt->bindParam(1, $wordID);
			        $stmt->execute();
	
					//Printing a dot for each row added
					if($stmt->rowCount())
						$i++;
						echo ".";
				}
				echo "<br />" . $i . " words have been added.";
			} else {
			    echo "No words entered. Nothing has changed.<br />";
			}
		} else {
		    echo "Didn't recieve a submission for words to be deleted. Are you messing with our form? ;)<br />";
		}

    } catch(Exception $e) {
        echo $e;
        return false;
    } finally {
		// Display the bad word audit form
		badWordForm();
	}

	return true;
}


function printBadWordsTable($pdoStmt) {
	// Printing the word deletion form
	echo "<form action='./badWords.php' method='POST'>";

    // Printing the table
    echo "<table id='auditResults'>";
    echo "<thead>";
	echo "<th>Word ID</th>";
	echo "<th>Mark for deletion</th>";
	echo "<th>Word</th>";
    echo "</thead>";

    // Looping through results of the query
    echo "<tbody>";
    while($row = $pdoStmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
			echo "<td>" . $row['WordID'] . "</td>";
			echo "<td><input type='checkbox' name='delete[]' value='" . $row['WordID'] . "'/></td>";
			echo "<td>" . $row['Word'] . "</td>";
        echo "</tr>";
    }   

    echo "</tbody>";
    echo "</table>";

	echo '<input type="hidden" name="audit_type" value="badWords_remove" />';
	echo '<input type="submit" value="Remove Marked Words" name="Submit">';
    echo "</form>";
}

function viewBadWords($postData) {
    // Making sure we're actually performing a bad word search, and that this function isn't 
    // being called improperly.
    if(!isset($postData['audit_type'])) return false;
    if($postData['audit_type'] !== 'badWords_view') return false;
    //echo json_encode($postData);

    try {
        $query = "SELECT * FROM Words";
        $stmt = prepareAuditPDOStatement($postData, $query);

        // Doing the thing
        $stmt->execute();
        echo "Statement executed successfully. Fetched " . $stmt->rowCount() . " bad words<br />";

        // Printing the results to a jquery datatabe
        printBadWordsTable($stmt);

    } catch(Exception $e) {
        echo $e;
        return false;
    }

	return true;
}

function removeBadWords($postData) {
    // Making sure we're actually removing bad words, and that this function isn't 
    // being called improperly.
    if(!isset($postData['audit_type'])) return false;
    if($postData['audit_type'] !== 'badWords_remove') return false;
	//echo json_encode($postData);
    try {
		// Testing if we have anything to add to the query
		if(isset($postData['delete'])) {
			// Sanatizing data
			foreach($postData['delete'] as $data)
				$sanitizedData[] = sanitize($data);

			// Building query
			$query = "DELETE FROM Words WHERE WordID = ?";


	        // Doing the thing
	    	global $db_connection_handle;
	    	db_connect();
		    $stmt = $db_connection_handle->prepare($query);

			// Executing the statement for each word
			echo "Adding words";
			$i = 0;
			foreach($sanitizedData as $wordID) {
				$stmt->bindParam(1, $wordID);
		        $stmt->execute();

				//Printing a dot for each row added
				if($stmt->rowCount())
					$i++;
					echo ".";
			}
			echo "<br />" . $i . " words have been added.";

		} else {
		    echo "No words marked for deletion. Nothing has changed.<br />";
		}

    } catch(Exception $e) {
        echo $e;
        return false;
    } finally {
		// Printing the table after the fact.
		$fakePostData = array('audit_type' => 'badWords_view');
		viewBadWords($fakePostData);
	}

	return true;
}
?>
