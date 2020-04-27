<?php
$response = array();
include  __DIR__."/GeneralFunctions.php";  
include  __DIR__."/utilities/sendPHPMail.php";     
include  __DIR__."/utilities/encrypt_decrypt.php";     

if (isset($_POST['email'])) {
 $email = $_POST['email'];
 $encryptDecryptSecurityObject = new EncryptDecryptSecurity();
 $generalFunctionsObject = new GeneralFunctionsClass();
 $dbOperationsObject = new DBOperations();
 $resultUser = $dbOperationsObject->getUserByEmail($email);
 if (mysqli_num_rows($resultUser)>0)  
 {
   $user = $generalFunctionsObject->getUserInfoWithPassword($resultUser);
   $user_id = $user["user_id"];
   $userPassword = $user["password"];
   $userPassword=$encryptDecryptSecurityObject->decrypt($userPassword);
   $userPasswordTemp = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
   $userPasswordTempEncrypted=$encryptDecryptSecurityObject->encrypt($userPasswordTemp);
   $resultUpdatePassword= $dbOperationsObject->updateUserPassword($user_id,$userPasswordTempEncrypted); 

   if (mysqli_affected_rows($resultUpdatePassword)>=0) {
     $phpMailSendObject = new PHPMailSend();
     $message = $userPasswordTemp ;
     $phpMailSendResult = $phpMailSendObject->send_email($email,$message);
     if($phpMailSendResult==1)
     {
      $response["success"] = 1;
      echo json_encode($response);   
    }
    else
    {
     $response["success"] = 0;
     $response["message"] = "can't send email to you";  
     echo json_encode($response);   
   }
 } 
 else {
  $response["success"] = 0;
  $response["message"] = "Oops! An error occurred.";
  echo json_encode($response);
}
}
else
{
 $response["success"] = 0;
 $response["message"] = " this email is not registered before";  
 echo json_encode($response);
}
}
else  
{
 $response["success"] = 0;
 $response["message"] = "Required field(s) is missing";
 echo json_encode($response);
}
?>