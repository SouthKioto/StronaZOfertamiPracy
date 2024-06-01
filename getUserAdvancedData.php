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

        $languageQuery = "SELECT user_id, language_skills.name as language_name, language_skills.level as language_level FROM `users` 
                            JOIN user_language_skills USING(user_id) 
                            JOIN language_skills USING(language_skills_id) 
                            WHERE user_id = '$user_id';";
        $resultLanguage = $conn->query($languageQuery);

        $linkQuery = "SELECT user_id, link_name, link_source FROM `users` JOIN user_link USING(user_id) JOIN links USING(links_id) WHERE user_id = '$user_id'";
        $resultLink = $conn->query($linkQuery);

        $courseQuery = "SELECT user_id, c.name course_name, c.organiser course_organiser, c.course_startDate course_startDate, c.course_endDate course_endDate FROM `users` 
                        JOIN user_course USING(user_id) 
                        JOIN courses c USING(courses_id) 
                        WHERE user_id = '$user_id'";

        $resultCourse = $conn->query($courseQuery);

        $skills = array();
        $workExpArr = array();
        $educationArr = array();
        $languageArr = array();
        $linkArr = array();
        $coursesArr = array();
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

        if($resultLanguage->num_rows > 0){
            while($row = $resultLanguage->fetch_assoc()) {
                $languageArr[] = array(
                    'language_name' => $row['language_name'],
                    'language_level' => $row['language_level'],
                );
            }
        }

        if($resultLink->num_rows > 0){
            while($row = $resultLink->fetch_assoc()) {
                $linkArr[] = array(
                    'link_name' => $row['link_name'],
                    'link_source' => $row['link_source'],
                );
            }
        }

        if($resultCourse->num_rows > 0){
            while($row = $resultCourse->fetch_assoc()) {
                $coursesArr[] = array(
                    'course_name' => $row['course_name'],
                    'course_organiser' => $row['course_organiser'],
                    'course_startDate' => $row['course_startDate'],
                    'course_endDate' => $row['course_endDate'],
                );
            }
        }

        echo json_encode(array( 'status' => 'success', 
                                'skill_data' => $skills, 
                                'workExpData' => $workExpArr,
                                'educationData' => $educationArr,
                                'languageData' => $languageArr,
                                'linkData' => $linkArr,
                                'courseData' => $coursesArr,
                                'profileImgData' => $prof_image));
        
    }else{
        json_encode(array('error'=> 'User_id not found'));
    }
}


