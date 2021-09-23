<?php
session_start();

$verb = $_SERVER["REQUEST_METHOD"];

if ($verb === "POST"){
    if($_POST['receiver_name'] != ""){
        $_SESSION["name"] = $_POST['receiver_name'];
    }
}
else if ($verb === "GET"){
    header('HTTP/1.1 200 OK');
    header('Content-Type: application/json');
    $dbSigning_array = array($_SESSION["name"]);
    echo json_encode($dbSigning_array);
}
?>
