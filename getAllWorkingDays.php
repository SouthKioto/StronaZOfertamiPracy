<?php
    header('Access-Control-Allow-Origin: *');        
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    
    @include 'connect.php';

    $query = "SELECT * FROM `working_days`";
    $result = $conn->query($query);

    if($result->num_rows > 0){
        $days = array();

        while($row = $result->fetch_assoc()){
            $days[] = array(
                'workingDayId' => $row['working_day_id'],
                'dayName' => $row['day_name'],
            );
        }
        echo json_encode(array("success" => "Data was successfully fetched", "daysData" => $days));
    } else{
        echo json_encode(array("error" => "Cannot fetch days"));
    }

    $conn->close();