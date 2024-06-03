<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');

    @include 'connect.php';

    $times = [];
    $iterations = 10; 

    for ($i = 0; $i < $iterations; $i++) {
        $start_time = microtime(true);

        $conn = new mysqli('localhost', 'root', '', 'bazadanychpraca');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $end_time = microtime(true);

        $conn->close();

        $response_time = ($end_time - $start_time) * 1000; 
        $times[] = $response_time;
        usleep(500000); 
    }
    
    $average_time = array_sum($times) / count($times);
   //echo "Średni czas odpowiedzi: " . $average_time . " ms\n<br>";

    $dataArr = array();

    foreach ($times as $index => $time) {
        $dataArr[$index] = [
            'Proba' => 'Próba '.($index + 1),
            'time' => $time
        ];
        //"Próba ".($index + 1).": ".$time." ms\n"
    }
    //print_r($dataArr);

    echo json_encode(array('status' => 'success', 'connTime' => $dataArr));


