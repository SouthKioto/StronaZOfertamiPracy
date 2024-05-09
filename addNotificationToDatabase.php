<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');

    @include 'connect.php';

    $title = $_POST['title'];
    $descript = $_POST['descript'];
    $company_id = $_POST['company_id'];
    $workPosition = $_POST['work_position'];
    $workPositionLvl = $_POST['workPositionLvl'];
    $contractType = $_POST['contractType'];
    $employmentDimension = $_POST['employmentDimension'];
    $salaryRange_start = $_POST['salaryRange_start'];
    $salaryRange_end = $_POST['salaryRange_end'];
    $workingHours_start = $_POST['workingHours_start'];
    $workingHours_end = $_POST['workingHours_end'];
    $dateOfExpiry = $_POST['dateOfExpiry'];
    $responsibilities = $_POST['responsibilities'];
    $candidate_requirements = $_POST['candidateRequirements'];
    $employerOffers = $_POST['employerOffers'];
    $userId = $_POST['userId'];
    $category_list = json_decode($_POST['category_list'], true);

    echo $_POST['category_list'];

    if (!is_array($category_list) || empty($category_list)) {
        echo json_encode(array('error' => 'No categories selected'));
        exit;
    }
    echo is_array($category_list);

    $checkCompanyQuery = "SELECT * FROM `company` WHERE `company_id` = '$company_id'";
    $companyResult = $conn->query($checkCompanyQuery);
    if ($companyResult->num_rows == 0) {
        echo json_encode(array('error' => 'Invalid company_id'));
        exit;
    }

    $dateToday = date("Y-m-d H:i:s");

    $query = "INSERT INTO notification_of_work 
            (notification_title, notification_descript, work_position, job_level, contract_type, employment_dimensions, salary_range_start, salary_range_end, working_hours_start, working_hours_end, date_of_expiry_start, date_of_expiry_end, responsibilities, candidate_requirements, user_id, company_id) 
            VALUES ('$title', '$descript', '$workPosition', '$workPositionLvl', '$contractType', '$employmentDimension', '$salaryRange_start', '$salaryRange_end', '$workingHours_start', '$workingHours_end', '$dateToday', '$dateOfExpiry', '$responsibilities', '$candidate_requirements', '$userId', '$company_id')";

    if ($conn->query($query)) {
        $notification_id = $conn->insert_id;

        echo json_encode(array('success' => 'Successfully added to database'));

        foreach ($category_list as $category_id) {
            $category_notification_query = "INSERT INTO category_notification (category_id, notification_of_work_id) VALUES ('$category_id', '$notification_id')";
            $conn->query($category_notification_query);
        }
    } else {
        echo json_encode(array('errorAddedNotification' => 'Failed to add notification to database'));
    }


