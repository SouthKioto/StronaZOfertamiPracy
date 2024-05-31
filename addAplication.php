<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    
    @include 'connect.php';

    $user_id = $_POST['user_id'];
    $not_id = $_POST['not_id'];
    $dateNow = date('Y-m-d H:i:s');

    $query = "SELECT * FROM user_aplication WHERE user_id = '$user_id' AND notification_of_work_id = '$not_id'";

    $result = $conn->query($query);

    if($result->num_rows > 0){
        echo json_encode(array('error' => 'user already applies'));
    } else {
        $query = "INSERT INTO user_aplication (user_id, notification_of_work_id, data) VALUES ('$user_id', '$not_id', '$dateNow')";
        
        if($conn->query($query)){
            echo json_encode(array('success' => 'application added'));
        }
        //echo json_encode(array('success' => 'application added'));
        
    }

    $conn->close();
    