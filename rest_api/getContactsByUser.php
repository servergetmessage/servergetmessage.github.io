<?php
$response = array();

include __DIR__ . "/GeneralFunctions.php";

if (isset($_POST['user_id']) && isset($_POST['token'])) {
    $user_id = $_POST['user_id'];
    $token = $_POST['token'];
    $dbOperationsObject = new DBOperations();
    $generalFunctionsObject = new GeneralFunctionsClass();

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        $resultFriends = $dbOperationsObject->getContactsByUser($user_id);
        $friends = $generalFunctionsObject->getContactsByUser($resultFriends);
        $response["success"] = 1;
        $response["friends"] = $friends;
        echo json_encode($response);
    } else {
        $response["success"] = 0;
        $response["message"] = "Invalid token";
        echo json_encode($response);
    }

} else {
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
    echo json_encode($response);
}

?>