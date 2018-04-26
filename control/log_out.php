<?php

session_start(); 

require_once ('./dbLibrary.php');
require_once('./dbChat.php');

db_connect();

$user = get_user_by_UserName($_SESSION['loggedin']);
$userID = $user['UserID'];

update_user_status($userID, 4);

session_unset();
session_destroy();
header('Location: http://138.197.152.148/index.php');


?>