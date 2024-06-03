<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

@include 'connect.php';

if ($conn->connect_error) {
    echo json_encode(array("error" => "Connection error"));
    exit();
} else {
    if (isset($_GET['not_id'])) {
        $not_id = $_GET['not_id'];

        $deleteQuery = "DELETE FROM notification_of_work WHERE notification_of_work_id = '$not_id'";
        $deleteResult = $conn->query($deleteQuery);

        if ($deleteResult) {
            echo json_encode(array("success" => "notification deleted successfully"));
        } else {
            echo json_encode(array("error" => "Error deleting notification", "sql_error" => $conn->error));
        }
    } else {
        echo json_encode(array("error" => "Missing user_id parameter"));
    }
}

$conn->close();

