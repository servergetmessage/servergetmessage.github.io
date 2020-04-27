<?php
// array for JSON response
$response = array();
include __DIR__ . "/GeneralFunctions.php";

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $dbOperationsObject = new DBOperations();
    $generalFunctionsObject = new GeneralFunctionsClass();


    $result = $dbOperationsObject->getUser($user_id);
    if (mysqli_num_rows($result) > 0) {
        $user = $generalFunctionsObject->getUserInfoSafety($result);
        $response["success"] = 1;
        $response["user"] = $user;
        echo json_encode($response);
    } else {
        $response["success"] = 0;
        $response["message"] = "error in user_id";
        echo json_encode($response);
    }


} else {
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
    echo json_encode($response);
}
?>