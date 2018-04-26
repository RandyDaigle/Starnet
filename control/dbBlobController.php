<?php
/* This is the blob control module. */


function checkUserImage($UserID){
    
    	//clean input
    	$UserID = sanitize($UserID);
        
        global $db_connection_handle;
        
        //$user_array = array(':userID' => $userID);
        $sql = 'SELECT FilesID FROM Files WHERE UserID=:UserID AND IsActive = 1 AND Comment IS NULL';
        
        try{
            $image=$db_connection_handle->prepare($sql);
            $image->bindParam(':UserID', $UserID, PDO::PARAM_STR, 11);
            $image->execute();
            
            $userinfo = $image->fetch(PDO::FETCH_ASSOC);
            
            return $userinfo;
        }
        catch(PDOException $e){
            return "Error is: " . $e->getMessage();
        }
}

    
function selectUserImage($FilesID) {
	global $db_connection_handle;
					
	$sql = "SELECT mime,
	                data
	           FROM Files
	          WHERE FilesID = :FilesID;";
	          
	try{		
		//retrieve last image uploaded.
		$stmt = $db_connection_handle->prepare($sql);
		$stmt->bindColumn(1, $mime);
		$stmt->bindColumn(2, $data, PDO::PARAM_LOB);
		$stmt->execute(array(":FilesID" => $FilesID));
		
		$stmt->fetch(PDO::FETCH_BOUND);
		
		if($stmt->rowCount() == 0)
		{
			$sql_default = " SELECT mime, data
							 FROM Files
							 WHERE FilesID = :FilesID";
			
			$new_stmt=$db_connection_handle->prepare($sql_default);
			$new_stmt->bindColumn(1, $mime);
			$new_stmt->bindColumn(2, $data, PDO::PARAM_LOB);
			$new_stmt->execute(array(":FilesID" => 91));
			
			$new_stmt->fetch(PDO::FETCH_BOUND);
			
			return array("mime" => $mime,
		    "data" => $data);
		}
		
		return array("mime" => $mime,
		    "data" => $data);
    }
    catch (PDOException $e){
            return "Error" . $e->getMessage();
    }

}

function selectCommentImage($commentID) {
	global $db_connection_handle;
					
	$sql = "SELECT mime,
	                data
	           FROM Files
	          WHERE Comment = :comment;";
	          
	try{		
		//retrieve last image uploaded.
		$stmt = $db_connection_handle->prepare($sql);
		$stmt->bindColumn(1, $mime);
		$stmt->bindColumn(2, $data, PDO::PARAM_LOB);
		$stmt->execute(array(":comment" => $commentID));
		
		$stmt->fetch(PDO::FETCH_BOUND);
		
		return array("mime" => $mime,
		    "data" => $data);
    }
    catch (PDOException $e){
            return "Error" . $e->getMessage();
    }

}


function setInactivePicture($FilesID){
	global $db_connection_handle;
	$sqlOldPic = 	"UPDATE Files
					SET IsActive = 0
					WHERE FilesID = :FilesID;";
	try{
		$s1 = $db_connection_handle->prepare($sqlOldPic);
		$s1->bindParam(':FilesID', $FilesID, PDO::PARAM_STR);
		$s1->execute();	
	}
	catch (PDOException $e){
            return "Error" . $e->getMessage();
    }
}

// use this insert function. We will have to modify slightly for userID info.
function insertBlob($GFiles, $Post, $commentID){

	global $db_connection_handle;
	
	//make the original picture inactive if one was previously set for the user.	
	if($commentID == NULL){
		if(isset($Post['OldFilesID'])){
			setInactivePicture($Post['OldFilesID']);
		}
	}
	
	try{

		// set the SQL based on comment upload or user upload.
		$sql="";
		if(isset($GFiles['fileToUpload']['tmp_name']))
			$sql = "INSERT INTO Files(mime,data,UserID,IsActive,Comment) VALUES (?,?,?,?,NULL)";
		else
			$sql = "INSERT INTO Files(mime,data,UserID,IsActive,Comment) VALUES (?,?,?,?,?)";
			
		$stmt = $db_connection_handle->prepare($sql);
		
		// set the bind Values based on comment upload or user upload
		if(isset($GFiles['fileToUpload']['tmp_name'])){
			$fp = fopen($GFiles['fileToUpload']['tmp_name'], 'rb');
			$stmt->bindValue(4 , 1, PDO::PARAM_STR);
		}
		else{
			$fp = fopen($GFiles['generalFileToUpload']['tmp_name'], 'rb');
			$stmt->bindValue(4 , 0, PDO::PARAM_STR);
			$stmt->bindParam(5 , $commentID);
		}
		
		$stmt->bindValue(1 , 'image/png');
		$stmt->bindParam(2 , $fp, PDO::PARAM_LOB);
		$stmt->bindParam(3 , $Post['UserID'], PDO::PARAM_STR, 3);
		
		return $stmt->execute();

	}
	catch (PDOException $e){
            return "Error" . $e->getMessage();
    }
}


/**
 * update the files table with the new blob from the file specified
 * by the filepath
 * @param int $id
 * @param string $filePath
 * @param string $mime
 * @return bool
 */
function updateBlob($id, $filePath, $mime) {

    $blob = fopen($filePath, 'rb');

    $sql = "UPDATE Files
            SET mime = :mime,
                data = :data
            WHERE id = :id;";

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindParam(':mime', $mime);
    $stmt->bindParam(':data', $blob, PDO::PARAM_LOB);
    $stmt->bindParam(':id', $id);

    return $stmt->execute();
}




	
	
?>