<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type');
    
    @include 'connect.php';

    $query = "SELECT * FROM company";

    $result = $conn->query($query);

    if($result->num_rows > 0){
        $companies = array();
        while($row = $result->fetch_assoc()){
            $companies[] = array(
                'company_id' => $row['company_id'],
                'company_name' => $row['company_name'],
                'company_address' => $row['company_address'],
                'company_location' => $row['company_location'],
                'about_company' => $row['about_company']
            );
        }
        echo json_encode(array("success" => "Data was successfully fetched", "companyData" => $companies));
    }

    $conn->close();

?>
