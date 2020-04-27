<?php
// array for JSON response
$response = array();
include(__DIR__ . "/../sql/DBOperations.php");
include __DIR__ . "/utilities/encrypt_decrypt.php";

if (isset($_POST['email'])
    && isset($_POST['name'])
    && isset($_POST['password'])
    && isset($_POST['token'])
    && isset($_POST['user_date'])) {

    $encryptDecryptSecurityObject = new EncryptDecryptSecurity();
    $dbOperationsObject = new DBOperations();
    $name = $_POST['name'];
    $email = $_POST['email'];
    $date = $_POST['user_date'];
    $token = $_POST['token'];
    $password = $encryptDecryptSecurityObject->encrypt($_POST['password']);
    $result = $dbOperationsObject->isEmailExist($email);
    if (mysqli_num_rows($result) > 0) {
        $response['success'] = 0;
        $response['message'] = "email already exists";
        echo json_encode($response);
    } else {
        $result = $dbOperationsObject->addUser($name, $email, $password
            , $token, $date);
        if (mysqli_affected_rows($result) > 0) {
            $response['success'] = 1;
            echo json_encode($response);
        } else {
            $response['success'] = 0;
            $response['message'] = "can't register new user now";
            echo json_encode($response);
        }
    }
} else {
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
    echo json_encode($response);
}
?>