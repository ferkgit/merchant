<?php
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Snimi log (opciono)
file_put_contents("webhook-log.txt", print_r($data, true), FILE_APPEND);

// Odgovor Orderpicking App-u
http_response_code(200);
echo json_encode(["status" => "ok"]);
?>
