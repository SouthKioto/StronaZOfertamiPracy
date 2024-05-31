<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');


@include 'connect.php';

if($conn->connect_error){
    echo json_encode(array('error'=> 'Connection Error'));
    exit();
} else {

    $user_id = $_POST['user_id'];

    if(!empty($user_id)){
        $userProfileImg = "SELECT prof_image FROM users WHERE user_id = '$user_id'";
        $resultUserProf = $conn->query($userProfileImg);

        $skillQuery = "SELECT skill_name FROM `users` JOIN user_skills USING(user_id) JOIN skills USING(skills_id) WHERE user_id = '$user_id';";
        $resultSkill = $conn->query($skillQuery);

        $workExpQuery = "SELECT user_id, position, company_id, company_name, workDate_Start, workDate_End FROM `users` 
                        JOIN user_workexperience USING(user_id) 
                        JOIN work_experience USING(work_experience_id) 
                        JOIN company USING(company_id) 
                        WHERE user_id = '$user_id';";
        $resultExp = $conn->query($workExpQuery);

        $educationQuery = "SELECT user_id, school_name, country, education_level, direction, education_dateStart, education_dateEnd, school_webside FROM `users` 
                            JOIN user_education USING(user_id) 
                            JOIN education USING(education_id) 
                            WHERE user_id = '$user_id';";
        $resultEducation = $conn->query($educationQuery);

        $languageQuery = "";

        $skills = array();
        $workExpArr = array();
        $educationArr = array();
        $prof_image = null;


        if($resultUserProf->num_rows > 0){
            while($row = $resultUserProf->fetch_assoc()) {
                $prof_image = base64_encode($row['prof_image']);   
            }
        }

        if($resultSkill->num_rows > 0){
            while($row = $resultSkill->fetch_assoc()) {
                $skills[] = array(
                    'skill_name' => $row['skill_name'],
                );
            }
        }

        if($resultExp->num_rows > 0){
            while($row = $resultExp->fetch_assoc()) {
                $workExpArr[] = array(
                    'position' => $row['position'],
                    'company_name' => $row['company_name'],
                    'workDate_Start' => $row['workDate_Start'],
                    'workDate_End' => $row['workDate_End'],
                    'company_id' => $row['company_id'],
                );
            }
        }

        if($resultEducation->num_rows > 0){
            while($row = $resultEducation->fetch_assoc()) {
                $educationArr[] = array(
                    'school_name' => $row['school_name'],
                    'country'=> $row['country'],
                    'education_level'=> $row['education_level'],
                    'direction'=> $row['direction'],
                    'education_dateStart' => $row['education_dateStart'],
                    'education_dateEnd' => $row['education_dateEnd'],
                    'school_webside' => $row['school_webside'],
                );
            }
        }

        echo json_encode(array( 'status' => 'success', 
                                'skill_data' => $skills, 
                                'workExpData' => $workExpArr,
                                'educationData' => $educationArr,
                                'profileImgData' => $prof_image));
        
    }else{
        json_encode(array('error'=> 'User_id not found'));
    }
}


