<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    @include 'connect.php';

    $query = 'SELECT * FROM `type_of_work`';

    $results = $conn->query($query);

    if($results->num_rows > 0){
        $typeOfWork = array();
        while($row = $results->fetch_assoc()){
            $typeOfWork[] = array(
                'TypeOfWorkId' => $row['type_of_work_id'],
                'TypeOfWorkType' => $row['type_of_work_type'],
            );
        }
        echo json_encode(array("success" => "Data was successfully fetched", "typeOfWorkData" => $typeOfWork));
    }

    $conn->close();

