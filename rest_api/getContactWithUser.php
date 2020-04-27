<?php
// array for JSON response
$response = array();
include __DIR__ . "/GeneralFunctions.php";

if (isset($_POST['user_id'])
    && isset($_POST['contact_user_id'])
    && isset($_POST['token'])
) {
    $user_id = $_POST['user_id'];
    $token = $_POST['token'];
    $contact_user_id = $_POST['contact_user_id'];
    $generalFunctionsObject = new GeneralFunctionsClass();
    $dbOperationsObject = new DBOperations();

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        $contactUser = $generalFunctionsObject->getUserById($contact_user_id);
        $friendInfo = $generalFunctionsObject->getFriendRelation($contact_user_id, $user_id);
        $contactUser["friends_id"] = $friendInfo["friends_id"];
        $contactUser["request_user_id"] = $friendInfo["request_user_id"];
        $contactUser["approved_user_id"] = $friendInfo["approved_user_id"];
        $contactUser["type"] = $friendInfo["type"];
        $response['success'] = 1;
        $response['contact'] = $contactUser;
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