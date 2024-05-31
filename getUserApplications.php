<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

    @include 'connect.php';

    $user_id = $_POST['user_id'];

    $query = "SELECT user_aplication_id, notification_of_work_id, notification_title, data FROM user_aplication JOIN(notification_of_work) USING(notification_of_work_id) WHERE user_aplication.user_id = '$user_id'";
    $result = $conn->query($query); 


    if($result->num_rows > 0){
        $aplicationsData = array();
        while($row = $result->fetch_assoc()){
            $aplicationsData[] = array(
                "user_aplication_id" => $row["user_aplication_id"],
                "notification_title" => $row["notification_title"],
                "notification_of_work_id" => $row["notification_of_work_id"],
                "date" => $row["data"]
            );
        }

        echo json_encode(array("status" => "success", "aplicationsData"=> $aplicationsData));
    } else {
        echo json_encode(array("status" => "error", "message" => "user dont have any applications"));
    }


    $conn->close();
