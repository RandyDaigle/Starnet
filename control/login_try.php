<?php

session_start();
require_once ('./dbLibrary.php');
require_once('./dbChat.php');
	
if (count($_POST) == 2 && array_key_exists('UserName', $_POST) && array_key_exists('password', $_POST))
{

  $login = sanitize($_POST['UserName']);
  $pass = sanitize($_POST['password']);
  db_connect();
  
    if (dbCredentials($login, $pass))
	    {

	        $_SESSION['loggedin'] = $login;
			update_user_status($login, 1);
	        header('Location: http://138.197.152.148/index.php');
	    }
    else
	    {

	        unset($_SESSION['loggedin']);
	        unset($_SESSION['Status_ID']);
	        unset($_SESSION['Banned']);
			header('Location: http://138.197.152.148/index.php');
	    }
}
else
{
	unset($_SESSION['loggedin']);
	unset($_SESSION['Status_ID']); 
	unset($_SESSION['Banned']);
	header('Location: http://138.197.152.148/index.php');
}


?>