<?php
// array for JSON response
$response = array();
include __DIR__ . "/GeneralFunctions.php";
include __DIR__ . "/utilities/firebaseNotification.php";
if (isset($_POST['request_user_id']) && isset($_POST['email']) && isset($_POST['token'])) {
    $request_user_id = $_POST['request_user_id'];
    $email = $_POST['email'];
    $token = $_POST['token'];
    $dbOperationsObject = new DBOperations();
    $generalFunctionsObject = new GeneralFunctionsClass();
    $firebaseNotificationObject = new FirebaseNotificationClass();
    $resultApprovedUser = $dbOperationsObject->getUserByEmail($email);

    $result_token = $dbOperationsObject->isTokenExist($request_user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        if (mysqli_num_rows($resultApprovedUser) > 0) {
            $approvedUser = $generalFunctionsObject->getUserInfoSafety($resultApprovedUser);
            $approved_user_id = $approvedUser['user_id'];
            $friendInfo = $generalFunctionsObject->getFriendRelation($approved_user_id, $request_user_id);

            if ($friendInfo["friends_id"] == -1) {
                $request_user = $generalFunctionsObject->getUserById($request_user_id);
                $approved_user_token = $generalFunctionsObject->getUserRegestrationId($approved_user_id);
                $result = $dbOperationsObject->addFriend($request_user_id, $approved_user_id);
                if (mysqli_affected_rows($result) > 0) {
                    $friends_id = mysqli_insert_id($result);
                    $obj = new stdClass();
                    $obj->firebase_json_message = array(
                        "type" => 'addFriend',
                        "friends_id" => $friends_id,
                        "request_user" => $request_user
                    );

                    $messageFirbase = json_encode($obj);
                    $firebaseNotificationObject->send_notification($approved_user_token, $messageFirbase);
                    $response['success'] = 1;
                    $response['friends_id'] = $friends_id;
                    $response['approved_user'] = $approvedUser;
                    echo json_encode($response);
                } else {
                    $response['success'] = 0;
                    echo json_encode($response);
                }
            } else {
                $response['success'] = 0;
                if ($friendInfo["type"] == 0) {
                    if ($friendInfo["request_user_id"] == $request_user_id) {
                        $response["message"] = "you requested adding this friend before";
                    } else {
                        $response["message"] = "already found in your friend requests";
                    }
                } else
                    if ($friendInfo["type"] == 1) {
                        $response["message"] = "this contact is already in your friends list";
                    }
                echo json_encode($response);
            }
        } else {
            $response["success"] = 0;
            $response["message"] = "No Contact has this email";
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