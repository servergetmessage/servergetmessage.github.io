<?php

$response = array();
include(__DIR__ . "/../sql/DBOperations.php");
if (isset($_POST['user_id'])
    && isset($_POST['last_seen'])
    && isset($_POST['token'])) {
    $user_id = $_POST['user_id'];
    $token = $_POST['token'];
    $last_seen = $_POST['last_seen'];
    $dbOperationsObject = new DBOperations();

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        $result = $dbOperationsObject->updateUserLastSeen($user_id, $last_seen);
        if (mysqli_affected_rows($result) >= 0) {
            $response["success"] = 1;
            echo json_encode($response);
        } else {
            $response["success"] = 0;
            $response["message"] = "Oops! An error occurred.";
            echo json_encode($response);
        }

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