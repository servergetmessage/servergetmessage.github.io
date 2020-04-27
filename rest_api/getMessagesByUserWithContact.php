<?php
$response = array();
include __DIR__ . "/GeneralFunctions.php";
if (isset($_POST['user_id'])
    && isset($_POST['contact_id'])
    && isset($_POST['token'])
) {

    $user_id = $_POST['user_id'];
    $token = $_POST['token'];
    $contact_id = $_POST['contact_id'];
    $dbOperationsObject = new DBOperations();
    $generalFunctionsObject = new GeneralFunctionsClass();

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        $resultMessages = $dbOperationsObject->getMessagesByUserWithContact($user_id, $contact_id);

        $messages = $generalFunctionsObject->getMessagesByUserWithContact($resultMessages);
        $response["success"] = 1;
        $response["messages"] = $messages;
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