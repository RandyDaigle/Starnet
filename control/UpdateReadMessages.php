<?php

require_once('dbLibrary.php');
require_once('dbChat.php');

db_connect();

$userID = $_POST['userID'];
$friendID = $_POST['friendID'];

$chatID = retrieve_chat_ID($userID, $friendID);

update_read_msgs($chatID, $friendID);

?>