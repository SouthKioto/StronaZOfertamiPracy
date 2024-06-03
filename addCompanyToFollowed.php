<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');

    @include 'connect.php';

    $not_id = $_POST['not_id'];
    $user_id = $_POST['user_id'];
    
    $query = "SELECT * FROM `category` WHERE category_name = '$newCategory'";
    $result = $conn->query($query);

    if($result->num_rows > 0){
        echo json_encode(array("status"=> "error","message"=> "Category exists already"));
    } else {
        if(!empty($newCategory)){
            $query = "INSERT INTO `category` (category_name) VALUES ('$newCategory')";
            $conn->query($query);
            echo json_encode(array("status"=> "success","message"=> "Category already added"));
        }
    }

    $conn->close();

