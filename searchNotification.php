<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

    @include 'connect.php';

    $searchedText = isset($_POST['searchedText']) ? $_POST['searchedText'] : '';
    $selectedCategoryIds = isset($_POST['selectedCategoryCheckbox']) ? $_POST['selectedCategoryCheckbox'] : [];
    $selectedCompanyIds = isset($_POST['selectedCompanyCheckbox']) ? $_POST['selectedCompanyCheckbox'] : [];

    $search_query = "SELECT * FROM `notification_of_work` 
        JOIN category_notification USING(notification_of_work_id) JOIN category USING(category_id) 
        WHERE 1";

    if (!empty($searchedText)) {
        $search_query .= " AND (notification_title LIKE CONCAT('%', ?, '%') OR 
            notification_title LIKE CONCAT('%', ?, '') OR
            notification_title LIKE CONCAT('', ?, '%'))";
    }

    if (!empty($selectedCategoryIds)) {
        $categoryIds = implode(',', $selectedCategoryIds);
        $search_query .= " AND category_id IN ($categoryIds)";
    }

    if (!empty($selectedCompanyIds)) {
        $companyIds = implode(',', $selectedCompanyIds);
        $search_query .= " AND company_id IN ($companyIds)";
    }

    $stmt = $conn->prepare($search_query);

    if (!empty($searchedText)) {
        $stmt->bind_param('sss', $searchedText, $searchedText, $searchedText);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $notification_with_filters = array();
            
        while($row = $result->fetch_assoc()) {
            $notification_with_filters[] = $row;
        }
        echo json_encode(array('status' => 'success', 'message' => 'Data was fetched successfully', 'filteredData' => $notification_with_filters));
    } else {
        echo json_encode(array('status' => 'NoData', 'message' => 'Brak wyników wyszukiwania.', 'filteredData' => []));
    }

    $stmt->close();
    $conn->close();
