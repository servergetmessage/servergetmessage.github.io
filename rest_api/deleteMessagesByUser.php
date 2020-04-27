<?php
//including the database connection file
include __DIR__ . "/GeneralFunctions.php";
// array for JSON response
$response = array();
if (isset($_POST['user_id']) && isset($_POST['messages_ids']) && isset($_POST['token'])) {
    $dbOperationsObject = new DBOperations();
    $generalFunctionsObject = new GeneralFunctionsClass();
    $user_id = $_POST['user_id'];
    $token = $_POST['token'];
    $messages_ids = $_POST['messages_ids'];

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        $messages_ids = json_decode($messages_ids, true);
        $messagesIdsCount = count($messages_ids['messages']);
        $number = 0;
        foreach ($messages_ids['messages'] as $messages_ids_object) {
            $message_id = $messages_ids_object['message_id'];
            $numberUpdated = $dbOperationsObject->deleteMessageByUser($user_id, $message_id);
            if ($numberUpdated > 0) {
                $number++;
            } else {
                break;
            }
        }

        if ($number == $messagesIdsCount) {
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            $response["success"] = 0;
            $response["message"] = "error in deleting";
            echo json_encode($response);
        }

    } else {
        $response["success"] = 0;
        $response["message"] = "Invalid token";
        echo json_encode($response);
    }
}
?>