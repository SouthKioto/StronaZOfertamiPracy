<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

    @include 'connect.php';
    @include 'functions.php'; 

    $searchedText = isset($_POST['searchedText']) ? mysqli_real_escape_string($conn, $_POST['searchedText']) : '';

    $search_query = "SELECT * FROM `company` WHERE 1";

    if (!empty($searchedText)) {
        $search_query .= " AND company_name LIKE '%" . $searchedText . "%'";
    }

    $result = $conn->query($search_query);

    if ($result) {
        if ($result->num_rows > 0) {
            $searchCompany = array();

            while ($row = $result->fetch_assoc()) {
                $searchCompany[] = array(
                    'company_id' => $row['company_id'],
                    'company_name' => $row['company_name'],
                    'company_address' => $row['company_address'],
                    'company_logo' => base64_encode($row['company_logo']),
                    'lat' => $row['lat'],
                    'lng' => $row['lng'],
                    'about_company' => $row['about_company'],
                );
            }

            //var_dump($searchCompany);

            $jsonData = json_encode(utf8ize(array('status' => 'success', 'message' => 'Data was fetched successfully', 'searchCompany' => $searchCompany)));

            if ($jsonData === false) {
                echo json_encode(array('status' => 'Error', 'message' => 'Błąd podczas przetwarzania danych na format JSON.'));
            } else {
                echo $jsonData;
            }
        } else {
            echo json_encode(array('status' => 'NoData', 'message' => 'Brak wyników wyszukiwania.', 'searchCompany' => []));
        }
    } else {
        echo json_encode(array('status' => 'Error', 'message' => 'Wystąpił błąd podczas wykonania zapytania.'));
    }

    $conn->close(); 

