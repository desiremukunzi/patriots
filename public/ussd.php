<?php
require_once 'index_ussd.php';

$message = "Welcome to Patriots";    
    $data = array("action" => "FC", "message" => $message);

header('Content-type: text/plain; charset=utf-8');
//echo $response;
echo json_encode($data);
?>
