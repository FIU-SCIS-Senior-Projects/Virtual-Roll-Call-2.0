<?php
require_once('DBHandler.php');
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$archived = $request->archived;
$connection = new DBHandler();
$result = $connection->getDocuments($archived);
die(json_encode($result));