<?php
$response = array();
include(__DIR__ . "/../sql/DBOperations.php");
if (isset($_POST['user_id']) && isset($_POST['token']) && isset($_POST['old_token'])) {
    $user_id = $_POST['user_id'];
    $token = $_POST['token'];
    $old_token = $_POST['old_token'];
    $dbOperationsObject = new DBOperations();

    $result_token = $dbOperationsObject->isTokenExist($user_id, $old_token);
    if (mysqli_num_rows($result_token) > 0) {
        $result = $dbOperationsObject->updateUserToken($user_id, $token);
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
        $response["message"] = "Token is invalid";
        echo json_encode($response);
    }
} else {
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
    echo json_encode($response);
}
?>