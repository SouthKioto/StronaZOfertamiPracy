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
    //echo $name." ".$surname." ".$email." ".$birth_date." ".$phone." ".$position." ".$positionDescr." ".$career_summary;

    $workExperienceList = json_decode($_POST['workExperienceList'], true);
    $educationList = json_decode($_POST['educationList'], true);
    $languageList = json_decode($_POST['languageList'], true);
    $skillsList = json_decode($_POST['skillsList'], true);

    $courseList = json_decode($_POST['courseList'], true);
    $linkList = json_decode($_POST['linkList'], true);
    
    //elementary information

    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK){
        $profile_image = null;
        $userProfileImg = "SELECT prof_image FROM users WHERE user_id = '$user_id'";
        $resultUserProf = $conn->query($userProfileImg);

        $row = $resultUserProf->fetch_assoc();

        if(empty($row['prof_image']) || $row['prof_image'] == NULL){
            $profile_image = file_get_contents($_FILES['profile_image']['tmp_name']);
            $profile_image = mysqli_real_escape_string($conn, $profile_image);
        } else if(!empty($row['prof_image']) && $row['prof_image'] !== NULL && !isset($_FILES['profile_image'])) {
            $profile_image = base64_encode($row['prof_image']);
        } else {
            $updateUserProfileImg = "UPDATE users SET prof_image = '$profile_image' WHERE user_id = '$user_id'";
            $conn->query($updateUserProfileImg);
        }

        $updateUserProfileImg = "UPDATE users SET prof_image = '$profile_image' WHERE user_id = '$user_id'";
        $conn->query($updateUserProfileImg);
    }

    $query = "UPDATE users SET 
    name='$name', 
    surname='$surname', 
    birth_date='$birth_date', 
    email='$email', 
    tel_number='$phone',
    curr_position='$position', 
    curr_position_description='$positionDescr',
    career_summary='$career_summary'
    WHERE user_id=$user_id";

    if($conn->query($query)){
        echo json_encode(array("success" => 'User updated successfully'));
    }else{
        echo json_encode(array("error" => 'User update error: ' . $conn->error));
    }

    //skills section
    
    $currentSkillsQuery = "SELECT s.skill_name FROM skills s 
                        JOIN user_skills us ON s.skills_id = us.skills_id 
                        WHERE us.user_id = $user_id";
    $currentSkillsResult = $conn->query($currentSkillsQuery);
    $currentSkills = [];

    if ($currentSkillsResult->num_rows > 0) {
        while ($row = $currentSkillsResult->fetch_assoc()) {
            $currentSkills[] = $row['skill_name'];
        }
    }

    $deleteUserSkillsQuery = "DELETE FROM user_skills WHERE user_id = $user_id";
    $conn->query($deleteUserSkillsQuery);

    foreach ($skillsList as $skill) {
        $skillName = mysqli_real_escape_string($conn, $skill['skill_name']);

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

        $insertUserSkillQuery = "INSERT INTO user_skills (user_id, skills_id) VALUES ($user_id, $skills_id)";
        $conn->query($insertUserSkillQuery);
    }

    //work experience section
    //print_r($workExperienceList);

    $currentWorkExpQuery = "SELECT position, work_experience_id FROM work_experience 
                            JOIN user_workexperience USING(work_experience_id) 
                            WHERE user_id = '$user_id'";

    $currentWorkExpResult = $conn->query($currentWorkExpQuery);
    $currentWorkExp = [];

    if ($currentWorkExpResult->num_rows > 0) {
        while ($row = $currentWorkExpResult->fetch_assoc()) {
            $currentWorkExp[] = $row['work_experience_id'];
        }
    }

    $deleteUserWorkExpQuery = "DELETE FROM user_workexperience WHERE user_id = $user_id";
    $conn->query($deleteUserWorkExpQuery);
        
    foreach ($workExperienceList as $workExp) {
        $workExpPosition = mysqli_real_escape_string($conn, $workExp['position']);
        $workExpCompany = mysqli_real_escape_string($conn, $workExp['company_id']);
        $workExpDateStart = mysqli_real_escape_string($conn, $workExp['workDate_Start']);
        $workExpDateEnd = isset($workExp['workDate_End']) ? "'" . mysqli_real_escape_string($conn, $workExp['workDate_End']) . "'" : "NULL";
    
        $checkWorkExpQuery = "SELECT work_experience_id FROM work_experience 
                              WHERE position = '$workExpPosition' 
                              AND company_id = '$workExpCompany' 
                              AND workDate_Start = '$workExpDateStart'";
        $result = $conn->query($checkWorkExpQuery);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $workExpId = $row["work_experience_id"];
        } else {
            $insertWorkExpQuery = "INSERT INTO work_experience (position, company_id, workDate_Start, workDate_End) 
                                   VALUES ('$workExpPosition', '$workExpCompany', '$workExpDateStart', $workExpDateEnd)";
            if ($conn->query($insertWorkExpQuery)) {
                $workExpId = $conn->insert_id;
            } else {
                echo json_encode(array("error" => 'Work experience insert error: ' . $conn->error));
                exit();
            }
        }
    
        $insertUserWorkExpQuery = "INSERT INTO user_workexperience (user_id, work_experience_id) VALUES ($user_id, $workExpId)";
        $conn->query($insertUserWorkExpQuery);
    }

    //education section
    
    $currentEduQuery = "SELECT education_id, school_name FROM education 
                        JOIN user_education USING(education_id) 
                        WHERE user_id = '$user_id'";

    $currentEduResult = $conn->query($currentEduQuery);
    $currentEdu = [];

    if ($currentEduResult->num_rows > 0) {
        while ($row = $currentEduResult->fetch_assoc()) {
            $currentEdu[] = $row['education_id'];
        }
    }

    $deleteUserEduQuery = "DELETE FROM user_education WHERE user_id = $user_id";
    $conn->query($deleteUserEduQuery);

    //print_r($educationList);

    foreach ($educationList as $education) {
        $schoolName = mysqli_real_escape_string($conn, $education['school_name']);
        $location = mysqli_real_escape_string($conn, $education['country']);
        $educationLevel = mysqli_real_escape_string($conn, $education['education_level']);
        $fieldOfStudy = mysqli_real_escape_string($conn, $education['direction']);
        $startDate = mysqli_real_escape_string($conn, $education['education_dateStart']);
        $endDate = isset($education['education_dateEnd']) ? mysqli_real_escape_string($conn, $education['education_dateEnd']) : "NULL";
        $schoolWebsite = mysqli_real_escape_string($conn, $education['school_webside']);

        $checkEducationQuery = "SELECT education_id FROM education 
                                WHERE school_name = '$schoolName' 
                                AND country = '$location' 
                                AND education_level = '$educationLevel'
                                AND direction = '$fieldOfStudy'
                                AND education_dateStart = '$startDate'
                                AND education_dateEnd = '$endDate'
                                AND school_webside = '$schoolWebsite'";

        $result = $conn->query($checkEducationQuery);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $educationId = $row['education_id'];
        } else {
            $insertEducationQuery = "INSERT INTO education (school_name, country, education_level, direction, school_webside, education_dateStart, education_dateEnd) 
                                     VALUES ('$schoolName', '$location', '$educationLevel', '$fieldOfStudy', '$schoolWebsite', '$startDate', '$endDate')";
            if ($conn->query($insertEducationQuery)) {
                $educationId = $conn->insert_id;
            } else {
                echo json_encode(array("error" => 'Education insert error: ' . $conn->error));
                exit();
            }
        }
    
        $insertUserEducationQuery = "INSERT INTO user_education (user_id, education_id) VALUES ('$user_id', '$educationId')";
        $conn->query($insertUserEducationQuery);
    }

    //language section
    $currentLanguageQuery = "SELECT language_skills_id FROM language_skills 
                        JOIN user_language_skills USING(language_skills_id) 
                        WHERE user_id = '$user_id';";

    $currentLanguageResult = $conn->query($currentLanguageQuery);
    $currentLanguage = [];

    if ($currentLanguageResult->num_rows > 0) {
        while ($row = $currentLanguageResult->fetch_assoc()) {
            $currentLanguage[] = $row['language_skills_id'];
        }
    }

    $deleteUserLanguageQuery = "DELETE FROM user_language_skills WHERE user_id = $user_id";
    $conn->query($deleteUserLanguageQuery);


    foreach ($languageList as $language) {
        $languageName = mysqli_real_escape_string($conn, $language["language_name"]);
        $languageLevel = mysqli_real_escape_string($conn, $language["language_level"]);

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

        $insertUserLanguageQuery = "INSERT INTO user_language_skills (user_id, language_skills_id) VALUES ('$user_id', '$languageId')";
        $conn->query($insertUserLanguageQuery);
    }

    // courses section (do dokończenia)
    $currentCoursesQuery = "SELECT courses_id FROM courses 
                            JOIN user_course USING(courses_id) 
                            WHERE user_id = '$user_id';";

    $currentCourseResult = $conn->query($currentCoursesQuery);
    $currentCourse = [];

    if ($currentCourseResult->num_rows > 0) {
        while ($row = $currentCourseResult->fetch_assoc()) {
            $currentCourse[] = $row['courses_id'];
        }
    }

    $deleteUserCourseQuery = "DELETE FROM user_course WHERE user_id = $user_id";
    $conn->query($deleteUserCourseQuery);

    foreach ($courseList as $course) {
        $courseName = mysqli_real_escape_string($conn, $course["course_name"]);
        $courseOrganiser = mysqli_real_escape_string($conn, $course["course_organiser"]);
        $courseStartDate = mysqli_real_escape_string($conn, $course["course_startDate"]);
        $courseEndDate = isset($course['course_endDate']) ? mysqli_real_escape_string($conn, $course['course_endDate']) : "NULL";

        $checkCourseQuery = "SELECT courses_id FROM `courses` 
                            WHERE name = '$courseName' 
                            AND organiser = '$courseOrganiser'
                            AND course_startDate = '$courseStartDate'
                            AND course_endDate = '$courseEndDate'";
        $result = $conn->query($checkCourseQuery);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $courseId = $row["courses_id"];
        } else {
            $insertCourseQuery = "INSERT INTO courses (name, organiser, course_startDate, course_endDate) 
                                VALUES ('$courseName', '$courseOrganiser', '$courseStartDate', '$courseEndDate')";
            if ($conn->query($insertCourseQuery)) {
                $courseId = $conn->insert_id;
            } else {
                echo json_encode(array("error" => 'Course insert error: ' . $conn->error));
                exit();
            }
        }

        $insertUserCourseQuery = "INSERT INTO user_course (user_id, courses_id) VALUES ('$user_id', '$courseId')";
        $conn->query($insertUserCourseQuery);
    }

    // links section

    $currentLinkQuery = "SELECT links_id FROM links 
                            JOIN user_link USING(links_id) 
                            WHERE user_id = '$user_id';";

    $currentlinkResult = $conn->query($currentLinkQuery);
    $currentLink = [];

    if ($currentlinkResult->num_rows > 0) {
        while ($row = $currentlinkResult->fetch_assoc()) {
            $currentLink[] = $row['links_id'];
        }
    }

    $deleteUserLinkQuery = "DELETE FROM user_link WHERE user_id = $user_id";
    $conn->query($deleteUserLinkQuery);

    foreach ($linkList as $link) {
        $linkName = mysqli_real_escape_string($conn, $link["link_name"]);
        $source = mysqli_real_escape_string($conn, $link["link_source"]);

        $checkLinkQuery = "SELECT links_id FROM `links` 
                            WHERE link_name = '$linkName' 
                            AND link_source = '$source'";
        $result = $conn->query($checkLinkQuery);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $linkId = $row["links_id"];
        } else {
            $insertLinkQuery = "INSERT INTO links (link_name, link_source) VALUES ('$linkName', '$source')";
            if ($conn->query($insertLinkQuery)) {
                $linkId = $conn->insert_id;
            } else {
                echo json_encode(array("error" => 'Language insert error: ' . $conn->error));
                exit();
            }
        }

        $insertUserLinkQuery = "INSERT INTO user_link (user_id, links_id) VALUES ('$user_id', '$linkId')";
        $conn->query($insertUserLinkQuery);
    }
    $conn->close();    
}

