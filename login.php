<?php

header('Access-Control-Allow-Origin: *');
    
@include 'connect.php';
@include 'functions.php';

if ($conn->connect_error) {
    echo json_encode(array("error" => "Connection error"));
    exit();
} else {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";

    $results = $conn->query($query);

    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();

        $hashedPassword = $row['password_hash'];

        $userData =array(
            'user_id' => $row['user_id'],
            'name' => $row['name'],
            'surname' => $row['surname'],
            'birth_date' => $row['birth_date'],
            'email' => $row['email'],
            'tel_number' => $row['tel_number'],
            'prof_image' => $row['prof_image'],
            'curr_position' => $row['curr_position'],
            'curr_position_description' => $row['curr_position_description'],
            'career_summary' => $row['career_summary'],
            'isAdmin' => $row['isAdmin'],
        );

        if (password_verify($password, $hashedPassword)) {
            
            echo json_encode(utf8ize(array("successLog" => true, "userData" => $userData, 'message' => 'login success')));
            exit();
        } else {
            echo json_encode(array("errorPassLog" => true, 'message' => "Login denied. Incorrect password"));
        }
    } else {
        echo json_encode(array("errorUserLog" => "User not found"));
    }
}

$conn->close();

