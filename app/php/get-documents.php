<?php
require_once('DBHandler.php');
$connection = new DBHandler();
$result = $connection->getDocuments();
die(json_encode($result));