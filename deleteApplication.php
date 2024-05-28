<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    
    @include 'connect.php';

    $user_id = $_POST['user_id'];
    $not_id = $_POST['not_id'];

    //echo '<h1>'.$user_id.' '.$not_id.'</h1>';

    $query = "SELECT user_aplication_id FROM user_aplication WHERE user_id = '$user_id' AND notification_of_work_id = '$not_id'";

    $result = $conn->query($query);
    
    if($result->num_rows > 0){
        $query = "DELETE FROM user_aplication WHERE user_id = '$user_id' AND notification_of_work_id = '$not_id'";

        $result = $conn->query($query);
        echo json_encode(array('status' => 'ApplicationAlreadyDeleted', 'message' => 'aplication already deleted'));
    } else {
        echo json_encode(array('status' => 'ApplicationDoesnExist', 'message' => 'application doest exist'));
    }

    $conn->close();
    