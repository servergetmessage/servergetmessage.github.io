<?php
$response = array();
include __DIR__ . "/GeneralFunctions.php";
include __DIR__ . "/utilities/encrypt_decrypt.php";

if (isset($_POST['email'])
    && isset($_POST['password'])
    && isset($_POST['token'])
) {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $encryptDecryptSecurityObject = new EncryptDecryptSecurity();
    $password = $encryptDecryptSecurityObject->encrypt($_POST['password']);
    $dbOperationsObject = new DBOperations();
    $generalFunctionsObject = new GeneralFunctionsClass();
    $result = $dbOperationsObject->isLoginExist($email, $password);

    if (mysqli_num_rows($result) > 0) {
        $result_token = $dbOperationsObject->updateUserTokenByEmail($email, $token);
        if (mysqli_affected_rows($result_token) >= 0) {
            $resultUser = $dbOperationsObject->getUserByEmail($email);
            $user = $generalFunctionsObject->getUserInfo($resultUser);
            $response["success"] = 1;
            $response["user"] = $user;
            echo json_encode($response);

        } else {
            $response["success"] = 0;
            $response["message"] = "Oops! An error occurred.";
            echo json_encode($response);
        }
    } else {
        $response["success"] = 0;
        $response["message"] = "error in email or password";
        echo json_encode($response);
    }
} else {
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
    echo json_encode($response);
}
?>