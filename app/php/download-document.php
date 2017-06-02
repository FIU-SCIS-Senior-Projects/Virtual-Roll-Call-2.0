<?php
$fileName = $_GET['upload_name'];
header('Content-Type: force-download');
//header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Disposition: inline; filename="' . $fileName . '"');
readfile('uploads/' . $fileName);
