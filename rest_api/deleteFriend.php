<?php
include __DIR__ . "/GeneralFunctions.php";
include __DIR__ . "/utilities/firebaseNotification.php";
$response = array();

if (isset($_POST['friends_id'])
    && isset($_POST['user_id'])
    && isset($_POST['token'])
    && isset($_POST['request_user_id'])
) {
    $friends_id = $_POST['friends_id'];
    $user_id = $_POST['user_id'];
    $request_user_id = $_POST['request_user_id'];
    $token = $_POST['token'];
    $firebaseNotificationObject = new FirebaseNotificationClass();
    $generalFunctionsObject = new GeneralFunctionsClass();
    $dbOperationsObject = new DBOperations();

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        $delete_req_user = $generalFunctionsObject->getUserById($user_id);
        $connection = $dbOperationsObject->deleteFriend($friends_id);
        if (mysqli_affected_rows($connection) > 0) {
            $obj = new stdClass();
            $obj->firebase_json_message = array(
                "type" => 'deleteFriend',
                "friends_id" => $friends_id,
                "delete_req_user" => $delete_req_user
            );

            $messageFirbase = json_encode($obj);
            $registration_id = $generalFunctionsObject->getUserRegestrationId($request_user_id);
            $firebaseNotificationObject->send_notification($request_user_id, $messageFirbase);
            $response["success"] = 1;
            $response['friends_id'] = $friends_id;
            echo json_encode($response);
        } else {
            $response["success"] = 0;
            echo json_encode($response);
        }

    } else {
        $response["success"] = 0;
        $response["message"] = "Invalid token";
        echo json_encode($response);
    }
}

?>