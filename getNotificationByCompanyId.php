<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    
    @include 'connect.php';

    $companyId = $_POST['company_id'];

    $query = "SELECT * FROM `notification_of_work` WHERE company_id = '$companyId' LIMIT 3";
    $result = $conn->query($query);

    if($result->num_rows > 0){
        $not_data = array();
        while($row = $result->fetch_assoc()){
            $not_data[] = array(
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

        echo json_encode(array('status' => 'success', 'message' => 'Data successfully fetched', 'not_data' => $not_data));
    } else {
        echo json_encode(array("error"=> "data not found"));
    }

    $conn->close();

