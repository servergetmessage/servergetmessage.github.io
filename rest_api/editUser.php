<?php

$response = array();
include __DIR__ . "/GeneralFunctions.php";
include __DIR__ . "/utilities/encrypt_decrypt.php";
$uploadsFolder = "uploadsProfiles/";
$file_upload_url = '../' . $uploadsFolder;
if (isset($_POST['user_id']) && isset($_POST['name']) && isset($_POST['email'])
    && isset($_POST['password'])
    && isset($_POST['status'])
    && isset($_POST['token'])
    && isset($_POST['image_uploaded'])
    || isset($_POST['image_new'])
) {
    $generalFunctionsObject = new GeneralFunctionsClass();
    $dbOperationsObject = new DBOperations();
    $encryptDecryptSecurityObject = new EncryptDecryptSecurity();
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $encryptDecryptSecurityObject->encrypt($_POST['password']);
    $status = $_POST['status'];
    $token = $_POST['token'];
    $image_uploaded = $_POST['image_uploaded'];
    $edit = 0;

    $result_token = $dbOperationsObject->isTokenExist($user_id, $token);
    if (mysqli_num_rows($result_token) > 0) {

        $resultEmailFound = $dbOperationsObject->isEmailExist($email);
        if (mysqli_num_rows($resultEmailFound) > 0) {
            $user = $generalFunctionsObject->getUserInfo($resultEmailFound);
            if ($user["user_id"] != $user_id) {
                $edit = 0;
            } else {
                $edit = 1;
            }
        } else {
            $edit = 1;
        }

        if ($edit == 1) {
            $errorImage = 0;
            if (isset($_POST['image_new'])) {
                try {
                    $image_new = $_POST['image_new'];
                    $image_new_name = $_POST['image_new_name'];
                    $binary = base64_decode($image_new);
                    $file = fopen('../uploadsProfiles/' . $image_new_name, 'w');
                    fwrite($file, $binary);
                    fclose($file);
                    $image_new = $file_upload_url . basename($image_new_name);
                } catch (Exception $e) {
                    $errorImage = 1;
                }
            }

            if ($errorImage == 0) {
                if (isset($_POST['image_new'])) {
                    $result = $dbOperationsObject->editUser($user_id, $name, $email, $password, $status, $image_new);
                } else {
                    $result = $dbOperationsObject->editUser($user_id, $name, $email, $password, $status, $image_uploaded);
                }

                if (mysqli_affected_rows($result) >= 0) {
                    $resultUser = $dbOperationsObject->getUser($user_id);
                    if (mysqli_num_rows($resultUser) > 0) {
                        $user = $generalFunctionsObject->getUserInfo($resultUser);
                        $response["success"] = 1;
                        $response["user"] = $user;
                        echo json_encode($response);
                    } else {
                        $response['success'] = 0;
                        $response["message"] = "Oops! An error occurred.";
                        echo json_encode($response);
                    }
                } else {
                    $response['success'] = 0;
                    $response["message"] = "Oops! An error occurred.";
                    echo json_encode($response);
                }
            } else {
                $response["success"] = 0;
                $response["message"] = "Oops! An error occurred.";
                echo json_encode($response);
            }
        } else {
            $response["success"] = 0;
            $response["message"] = "there is a user has this email";
            echo json_encode($response);
        }

    } else {
        $response["success"] = 0;
        $response["message"] = "Invalid token";
        echo json_encode($response);
    }

} else {
    $response["success"] = 0;
    $response["message"] = "Missing required fields";
    echo json_encode($response);
}
?>