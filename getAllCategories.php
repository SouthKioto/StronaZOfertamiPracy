<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');

    @include 'connect.php';

    $query = 'SELECT * FROM `category`';
    $results = $conn->query($query);

    if($results->num_rows > 0){
        $categories = array();
        while($row = $results->fetch_assoc()) {
            $categories[] = array(
                'category_id' => $row['category_id'],
                'category_name' => $row['category_name'],
            );
        }
        echo json_encode(array("success" => "Data was successfully fetched", "categoryData" => $categories));
    } else {
        echo json_encode(array("error" => "Cannot fetch categories"));
    }

    

    $conn->close();    

?>