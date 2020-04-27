<?php
// array for JSON response
$response = array();
include __DIR__ . "/GeneralFunctions.php";
include __DIR__ . "/utilities/firebaseNotification.php";
$uploadsFolder = "uploadsMessages/";
$file_upload_url = '../' . $uploadsFolder;
//$file_upload_url =      __DIR__.'/../' . $uploadsFolder;
if (isset($_POST['sender_id'])
    && isset($_POST['receiver_id'])
    && isset($_POST['message'])
    && isset($_POST['message_type_id'])
    && isset($_POST['message_date'])
    && isset($_POST['token'])
) {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    $message_type_id = $_POST['message_type_id'];
    $date = $_POST['message_date'];
    $token = $_POST['token'];

    $user_id = $_POST['sender_id'];
    $last_seen = $_POST['message_date'];

    $dbOperationsObject = new DBOperations();

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        $result = $dbOperationsObject->updateUserLastSeen($user_id, $last_seen);

        $firebaseNotificationObject = new FirebaseNotificationClass();
        $generalFunctionsObject = new GeneralFunctionsClass();
        $dbOperationsObject = new DBOperations();
        $senderUser = $generalFunctionsObject->getUserById($sender_id);
        $friendInfo = $generalFunctionsObject->getFriendRelation($receiver_id, $senderUser["user_id"]);
        $senderUser["friends_id"] = $friendInfo["friends_id"];
        $senderUser["request_user_id"] = $friendInfo["request_user_id"];
        $senderUser["approved_user_id"] = $friendInfo["approved_user_id"];
        $senderUser["type"] = $friendInfo["type"];
        $receivedUser = $generalFunctionsObject->getUserById($receiver_id);
        $receivedUserTokenUpdate = $receivedUser['token'];
        $error = 0;
        $result = "";

        if ($message_type_id == 1) {
            $result = $dbOperationsObject->sendMessage($sender_id, $receiver_id, $message, $message_type_id, $date);
        } else
            if ($message_type_id == 2) {
                if (isset($_POST['image_new'])) {
                    try {
                        $image_new = $_POST['image_new'];
                        $image_new_name = $_POST['image_new_name'];
                        $binary = base64_decode($image_new);
                        $file = fopen('../uploadsMessages/' . $image_new_name, 'w');
                        fwrite($file, $binary);
                        fclose($file);
                        $image_new = $file_upload_url . basename($image_new_name);
                    } catch (Exception $e) {
                        $error = 1;
                    }
                }

                if ($error == 0) {
                    $message = $image_new;
                    $result = $dbOperationsObject->sendMessage($sender_id, $receiver_id, $message, $message_type_id, $date);
                }
            }

        if ($error == 0) {
            if (mysqli_affected_rows($result) > 0) {
                $message_id = mysqli_insert_id($result);
                $obj = new stdClass();
                $obj->firebase_json_message = array(
                    "type" => 'sendMessage',
                    "senderUser" => $senderUser,
                    "senderUserId" => $sender_id,
                    "receivedUser" => $receivedUser,
                    "receivedUserId" => $receiver_id,
                    "message" => $message,
                    "message_type_id" => $message_type_id,
                    "date" => $date,
                    "message_id" => $message_id,
                );

                $registration_id = $generalFunctionsObject->getUserRegestrationId($receiver_id);
                $messageFirbase =  json_encode($obj);
                $firebaseNotificationObject->send_notification($registration_id, $messageFirbase);
                $response['success'] = 1;
                $response['msg_id'] = $message_id;
                $response['msg'] = $message;
                echo json_encode($response);
            } else {
                $response['success'] = 0;
                echo json_encode($response);
            }
        } else {
            $response["success"] = 0;
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