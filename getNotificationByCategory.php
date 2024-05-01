<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

@include 'connect.php';

$not_category = $_POST['not_category'];

if($conn->connect_error){
    echo json_encode(array("error" => "Connection error"));
    exit();
}else{
    $query = "SELECT * FROM `notification_of_work` JOIN category USING(category_id) WHERE category.category_name = '$not_category'";
    $results = $conn->query($query);

    if($results->num_rows > 0){
        $notification_by_category = array();
        while($row = $results->fetch_assoc()){
            $notification_by_category = array(
                'notification_of_work_id' => $row['notification_of_work_id'],
                'notification_title' => $row['notification_title'],
                'notification_descript' => $row['notification_descript'],
                'work_position' => $row['work_position'],
                'job_level' => $row['job_level'],
                'contract_type' => $row['contract_type'],
                'employment_dimensions' => $row['employment_dimensions'],
                //'type_of_work_id' => $row['type_of_work_id'],
                'salary_range_start' => $row['salary_range_start'],
                'salary_range_end' => $row['salary_range_end'],
                'working_days' => $row['working_days'],
                'working_hours_start' => $row['working_hours_start'],
                'working_hours_end' => $row['working_hours_end'],
                'date_of_expiry_start' => $row['date_of_expiry_start'],
                'date_of_expiry_end' => $row['date_of_expiry_end'],
                //'category_id' => $row['category_id'],
                'responsibilities' => $row['responsibilities'],
                'candidate_requirements' => $row['candidate_requirements'],
                'employer_offers' => $row['employer_offers'],
                'user_id' => $row['user_id'],
            );
        }
        //print_r($notifications_by_category);

        echo json_encode(array("success" => "Data was successfully fetched", "notificationData" => $notification_by_category));

    }else{
        echo json_encode(array("error" => "No data found"));
    }
}
$conn->close();
?>
