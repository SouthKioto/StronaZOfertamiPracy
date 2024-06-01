<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');

    @include 'connect.php';

    if(isset($_POST['companyName']) && isset($_POST['companyAddress']) && isset($_POST['companyDescription']) && isset($_POST['lat']) && isset($_POST['lng'])) {
        $companyName = $_POST['companyName'];
        $companyAddress = $_POST['companyAddress'];
        $companyDescription = $_POST['companyDescription'];
        $companyLogo = null;
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];

        if(!empty($companyName) && !empty($companyAddress) && !empty($companyDescription) && !empty($lat) && !empty($lng)) {
            $query = "SELECT * FROM `company` WHERE `company_name` = '$companyName' AND `company_address` = '$companyAddress'";
            $result = $conn->query($query);

            if($result->num_rows > 0){
                echo json_encode(array("status"=> "error","message"=> "Firma o tej nazwie i adresie już istnieje"));
            } else {
                $companyLogo = file_get_contents($_FILES['companyImg']['tmp_name']);
                $companyLogo = mysqli_real_escape_string($conn, $companyLogo);

                $query = "INSERT INTO `company` (company_name, company_address, company_logo, lat, lng, about_company) 
                VALUES('$companyName', '$companyAddress', '$companyLogo', '$lat', '$lng','$companyDescription')";
                
                if($conn->query($query)){
                    echo json_encode(array("status"=> "success","message"=> "Twoja firma została dodana"));
                }
            }
        } else {

            echo json_encode(array("status"=> "error","message"=> "Wszystkie pola są wymagane"));
        }
    } else {
        echo json_encode(array("status"=> "error","message"=> "Brak przekazanych danych"));
    }

    $conn->close();










