<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

$conn = new mysqli('localhost', 'root', '', 'bazadanychpraca');

if($conn->connect_error){
    echo json_encode(array("error" => "Connection error"));
    exit();
}else{
    $user_id = $_GET['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    //$country = mysqli_real_escape_string($conn, $_POST['country']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $positionDescr = mysqli_real_escape_string($conn, $_POST['positionDescr']);
    $career_summary = mysqli_real_escape_string($conn, $_POST['career_summary']);

    $workExperienceList = json_decode($_POST['workExperienceList'], true);
    $educationList = json_decode($_POST['educationList'], true);
    $languageList = json_decode($_POST['languageList'], true);
    $skillsList = json_decode($_POST['skillsList'], true);

    $courseList = json_decode($_POST['courseList'], true);
    $linkList = json_decode($_POST['linkList'], true);

    $query = "UPDATE users SET 
    name='$name', 
    surname='$surname', 
    birth_date='$birth_date', 
    email='$email', 
    tel_number='$phone', 
    prof_image=NULL, 
    curr_position='$position', 
    curr_position_description='$positionDescr',
    career_summary='$career_summary'
    WHERE user_id=$user_id";

    if($conn->query($query)){
        echo json_encode(array("success" => 'User updated successfully'));
    }else{
        echo json_encode(array("error" => 'User update error: ' . $conn->error));
    }

    foreach ($skillsList as $skill) {
        $skillName = mysqli_real_escape_string($conn, $skill['skillName']);
     
        $checkSkillQuery = "SELECT skills_id FROM skills WHERE skill_name = '$skillName'";
        $result = $conn->query($checkSkillQuery);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $skills_id = $row['skills_id'];
        } else {
            $insertSkillQuery = "INSERT INTO skills (skill_name) VALUES ('$skillName')";
            $conn->query($insertSkillQuery);
            $skills_id = $conn->insert_id;
        }
    
        $insertUserSkillQuery = "REPLACE INTO user_skills (user_id, skills_id) VALUES ($user_id, $skills_id)";
        $conn->query($insertUserSkillQuery);
    }
    
    foreach ($workExperienceList as $workExp) {
        $workExpPosition = mysqli_real_escape_string($conn, $workExp['position']);
        $workExpCompany = mysqli_real_escape_string($conn, $workExp['companyName']);
        $workExpDateStart = mysqli_real_escape_string($conn, $workExp['startDate']);
        $workExpDateEnd = isset($workExp['endDate']) ? "'" . mysqli_real_escape_string($conn, $workExp['endDate']) . "'" : "NULL";
        $ongoing = $workExp['isOngoing'] ? 1 : 0;
    
        $checkWorkExpQuery = "SELECT work_experience_id FROM work_experience 
                              WHERE position = '$workExpPosition' 
                              AND company_id = '$workExpCompany' 
                              AND workDate_Start = '$workExpDateStart'";
        $result = $conn->query($checkWorkExpQuery);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $workExpId = $row["work_experience_id"];
        } else {
            $insertWorkExpQuery = "INSERT INTO work_experience (position, company_id, workDate_Start, workDate_End, isOngoing) 
                                   VALUES ('$workExpPosition', '$workExpCompany', '$workExpDateStart', $workExpDateEnd, $ongoing)";
            if ($conn->query($insertWorkExpQuery)) {
                $workExpId = $conn->insert_id;
            } else {
                echo json_encode(array("error" => 'Work experience insert error: ' . $conn->error));
                exit();
            }
        }
    
        $insertUserWorkExpQuery = "REPLACE INTO user_workexperience (user_id, work_experience_id) VALUES ($user_id, $workExpId)";
        $conn->query($insertUserWorkExpQuery);
    }
    
    foreach ($educationList as $education) {
        $schoolName = mysqli_real_escape_string($conn, $education['schoolName']);
        $location = mysqli_real_escape_string($conn, $education['location']);
        $educationLevel = mysqli_real_escape_string($conn, $education['educationLevel']);
        $fieldOfStudy = mysqli_real_escape_string($conn, $education['fieldOfStudy']);
        $startDate = mysqli_real_escape_string($conn, $education['startDate']);
        $endDate = mysqli_real_escape_string($conn, $education['endDate']);
        $schoolWebsite = mysqli_real_escape_string($conn, $education['schoolWebsite']);

        $checkEducationQuery = "SELECT education_id FROM education 
                                WHERE school_name = '$schoolName' 
                                AND country = '$location' 
                                AND education_level = '$educationLevel'
                                AND direction = '$fieldOfStudy'
                                AND education_dateStart = '$startDate'
                                AND education_dateEnd = '$endDate'
                                AND school_website = '$schoolWebsite'";
        $result = $conn->query($checkEducationQuery);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $educationId = $row["education_id"];
        } else {
            $insertEducationQuery = "INSERT INTO education (school_name, country, education_level, direction, school_website, education_dateStart, education_dateEnd) 
                                     VALUES ('$schoolName', '$location', '$educationLevel', '$fieldOfStudy', '$schoolWebsite', '$startDate', '$endDate')";
            if ($conn->query($insertEducationQuery)) {
                $educationId = $conn->insert_id;
            } else {
                echo json_encode(array("error" => 'Education insert error: ' . $conn->error));
                exit();
            }
        }
    
        $insertUserEducationQuery = "REPLACE INTO user_education (user_id, education_id) VALUES ($user_id, $educationId)";
        $conn->query($insertUserEducationQuery);
    }
    
    foreach ($languageList as $language) {
        $languageName = mysqli_real_escape_string($conn, $language["language"]);
        $languageLevel = mysqli_real_escape_string($conn, $language["level"]);

        $checkLanguageQuery = "SELECT language_skills_id FROM language_skills 
                               WHERE name = '$languageName' 
                               AND level = '$languageLevel'";
        $result = $conn->query($checkLanguageQuery);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $languageId = $row["language_skills_id"];
        } else {
            $insertLanguageQuery = "INSERT INTO language_skills (name, level) VALUES ('$languageName', '$languageLevel')";
            if ($conn->query($insertLanguageQuery)) {
                $languageId = $conn->insert_id;
            } else {
                echo json_encode(array("error" => 'Language insert error: ' . $conn->error));
                exit();
            }
        }

        $insertUserLanguageQuery = "INSERT INTO user_language_skills (user_id, language_skills) VALUES ($user_id, $languageId)
                                    ON DUPLICATE KEY UPDATE user_id=user_id";
        $conn->query($insertUserLanguageQuery);
    }

    $conn->close();    
}

