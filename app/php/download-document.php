<?php
$fileName = $_GET['upload_name'];
header('Content-Type: force-download');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Disposition: inline; filename="' . $fileName . '"');
header('HTTP/1.0 200 OK', true, 200);
readfile('uploads/' . $fileName);
