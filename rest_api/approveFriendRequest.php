<?php

$response = array();

include __DIR__ . "/GeneralFunctions.php";
include __DIR__ . "/utilities/firebaseNotification.php";

if (isset($_POST['friends_id']) && isset($_POST['request_user_id']) && isset($_POST['user_id']) && isset($_POST['token'])) {
    $user_id = $_POST['user_id'];
    $token = $_POST['token'];
    $friends_id = $_POST['friends_id'];
    $request_user_id = $_POST['request_user_id'];

    $firebaseNotificationObject = new FirebaseNotificationClass();
    $generalFunctionsObject = new GeneralFunctionsClass();
    $dbOperationsObject = new DBOperations();
    $request_user = $generalFunctionsObject->getUserById($request_user_id);
    $approvedUser = $generalFunctionsObject->getUserById($user_id);
    $friendInfo = $generalFunctionsObject->getFriendRelation($request_user_id, $user_id);

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        if ($friendInfo["friends_id"] == -1) {
            $response['success'] = 0;
            $response["message"] = "this contact cancelled this friend request ";
            echo json_encode($response);
        } else {
            if ($friendInfo["type"] == 1) {
                $response['success'] = 0;
                $response["message"] = "this contact is already in your friends list";
                echo json_encode($response);
            } else
                if ($friendInfo["type"] == 0) {
                    $request_user_token = $request_user["token"];
                    $result = $dbOperationsObject->approveFriendRequest($friends_id);
                    if (mysqli_affected_rows($result) > 0) {
                        $obj = new stdClass();
                        $obj->firebase_json_message = array(
                            "type" => 'approveFriendRequest',
                            "friends_id" => $friends_id,
                            "approved_user" => $approvedUser,
                        );
                        $registration_id = $generalFunctionsObject->getUserRegestrationId($request_user_id);
                        $messageFirbase = json_encode($obj);
                        $firebaseNotificationObject->send_notification($registration_id, $messageFirbase);
                        $response["success"] = 1;
                        $response['friends_id'] = $friends_id;
                        $response['request_user'] = $request_user;
                        echo json_encode($response);
                    } else {
                        $response["success"] = 0;
                        $response["message"] = "Oops! An error occurred.";
                        echo json_encode($response);
                    }
                }
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

