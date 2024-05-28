<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');

    @include 'connect.php';

    $user_id = $_POST['user_id'];
    if(empty($user_id)) {
        echo json_encode(array("error" => "Category not provided"));
        exit();
    }

    if($conn->connect_error) {
        echo json_encode(array("error" => "Connection error"));
        exit();
    } else {
        $query = "SELECT * FROM `notification_of_work` WHERE user_id = '$user_id'";
        $results = $conn->query($query);

        if($results->num_rows > 0) {
            $user_notification = array();
            
            while($row = $results->fetch_assoc()) {
                $user_notification[] = array(
                    'notification_of_work_id' => $row['notification_of_work_id'],
                    'notification_title' => $row['notification_title'],
                    'notification_descript' => $row['notification_descript'],
                    'work_position' => $row['work_position'],
                    'job_level' => $row['job_level'],
                    'contract_type' => $row['contract_type'],
                    'employment_dimensions' => $row['employment_dimensions'],
                    'salary_range_start' => $row['salary_range_start'],
                    'salary_range_end' => $row['salary_range_end'],
                    'working_days' => $row['working_days'],
                    'working_hours_start' => $row['working_hours_start'],
                    'working_hours_end' => $row['working_hours_end'],
                    'date_of_expiry_start' => $row['date_of_expiry_start'],
                    'date_of_expiry_end' => $row['date_of_expiry_end'],
                    'responsibilities' => $row['responsibilities'],
                    'candidate_requirements' => $row['candidate_requirements'],
                    'employer_offers' => $row['employer_offers'],
                    'user_id' => $row['user_id'],
                );
            }
            echo json_encode(array('status' => 'success', "notificationData" => $user_notification));
        } else {
            echo json_encode(array("status" => "No data found"));
        }
        $conn->close();
    }
