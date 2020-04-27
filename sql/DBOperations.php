<?php
include  __DIR__."/DB.php";

class DBOperations {
  public function __constructor() {

  }

  public function  getUserByEmail($email){
    $com = new DbConnect();
    $sql = "select * from users where email='$email'";
    $result = mysqli_query($com->getDb(), $sql);
    return $result;
  }

  public function addFriend($request_user_id,$approved_user_id) {
    $com = new DbConnect();
    $sql = "insert into friends(request_user_id,approved_user_id) VALUES
    ('$request_user_id','$approved_user_id' )";

    $result = mysqli_query($com->getDb(), $sql);
    return $com->getDb();
  }

  public function getContactsByUser($user_id){
    $com = new DbConnect();
    $sql = "SELECT user_id, name, email, image, last_seen, status, friends_id from friends, users WHERE ( ( '$user_id' != users.user_id and '$user_id' = friends.request_user_id  and users.user_id = friends.approved_user_id  ) or ('$user_id' != users.user_id and '$user_id'= friends.approved_user_id and users.user_id = friends.request_user_id and type = '1' ) ) ";

    $result = mysqli_query($com->getDb(), $sql);
    return $result;
  }

  public function getFriendRequestsByUser($user_id){
    $com = new DbConnect();
    $sql = "SELECT * from friends, users 
    WHERE( ('$user_id' != users.user_id and '$user_id'= friends.approved_user_id and users.user_id = friends.request_user_id and type = '0' ) ) ";

    $result = mysqli_query($com->getDb(), $sql);
    return $result;
  }

  public function addUser($name, $email, $password, $token, $date) {
    $com = new DbConnect();
    $name =  mysqli_real_escape_string($com->getDb(),$name);
    $email =  mysqli_real_escape_string($com->getDb(),$email);
    $password =  mysqli_real_escape_string($com->getDb(),$password);
    $token =  mysqli_real_escape_string($com->getDb(),$token);
    $sql = "insert into users(name,email,password ,status, token, user_date ) VALUES('$name','$email','$password','','$token','$date')";
    $result = mysqli_query($com->getDb(), $sql);
    return $com->getDb();
  }

  public function getUser($user_id) {
    $com = new DbConnect();
    $sql = "select * from users where user_id =  '$user_id' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $result;
  }

  public function getFriendRelation($friend_user_id , $user_id)
  {
    $com = new DbConnect();
    $sql = "select * from friends WHERE ( ( request_user_id =  '$friend_user_id' and approved_user_id = '$user_id'  ) or ( request_user_id =  '$user_id' and approved_user_id = '$friend_user_id'  )) ";
    $result = mysqli_query($com->getDb(), $sql);
    return $result;
  }

  public function editUser($user_id, $name, $email, $password, $status, $image) {
    $com = new DbConnect();
    $name =  mysqli_real_escape_string($com->getDb(),$name);
    $email =  mysqli_real_escape_string($com->getDb(),$email);
    $password =  mysqli_real_escape_string($com->getDb(),$password);
    $status =  mysqli_real_escape_string($com->getDb(),$status);
    $image =  mysqli_real_escape_string($com->getDb(),$image);

    $sql = "update users set name='$name',email='$email',password='$password',status='$status',image='$image' where user_id =  '$user_id' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $com->getDb();
  }

public function updateUserLastSeen($user_id, $last_seen) {
	$com = new DbConnect();
	$token =  mysqli_real_escape_string($com->getDb(),$token);
	$sql = "update users set last_seen='$last_seen' where user_id= '$user_id' ";
	$result = mysqli_query($com->getDb(), $sql);
	return $com->getDb();
}

  public function updateUserToken($user_id, $token) {
    $com = new DbConnect();
    $token =  mysqli_real_escape_string($com->getDb(),$token);
    $sql = "update users set token='$token' where user_id= '$user_id' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $com->getDb();
  }
  public function updateUserTokenByEmail($email, $token) {
    $com = new DbConnect();
    $token =  mysqli_real_escape_string($com->getDb(),$token);
    $sql = "update users set token='$token' where email= '$email' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $com->getDb();
  }

  public function updateUserPassword($user_id, $password) {
    $com = new DbConnect();
    $password =  mysqli_real_escape_string($com->getDb(),$password);
    $sql = "update users set password='$password' where user_id=  '$user_id' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $com->getDb();
  }

  public function approveFriendRequest($friends_id) {
    $com = new DbConnect();
    $sql = "update friends set type='1' where friends_id=  '$friends_id' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $com->getDb();
  }

  public function getMessageType($message_type_id)
  {
    $com = new DbConnect();
    $sql = "select * from message_type where type_id=  '$message_type_id' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $result;
  }

  public function getUsersByFriendsId($friends_id) {
    $com = new DbConnect();
    $sql = "select * from friends where friends_id =  '$friends_id' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $result;
  }

  public function deleteFriend($friends_id) {
    $com = new DbConnect();
    $sql = "delete from friends where friends_id=  '$friends_id' ";
    $result = mysqli_query($com->getDb(), $sql);
    return $com->getDb();
  }
  public function deleteMessageByUser($user_id,$message_id) {
   $number  = 0;
   $com1 = new DbConnect();
   $sql1 = "update messages set deleted_by_sender_id='1' where message_id =  '$message_id'  and sender_id = '$user_id'  " ;
   $sql2 = "update messages set deleted_by_receiver_id='1' where message_id =  '$message_id'  and receiver_id = '$user_id'  " ;
   mysqli_query($com1->getDb(), $sql1);
   if (mysqli_affected_rows($com1->getDb())>=0)
   {
     $number++;
     mysqli_query($com1->getDb(), $sql2);
     if (mysqli_affected_rows($com1->getDb())>=0)
     {
       $number++;
     }
   }
   return $number;
 }

 public function isEmailExist($email) {
  $com = new DbConnect();
  $email =  mysqli_real_escape_string($com->getDb(),$email);
  $sql = "select * from users where email = '$email' Limit 1";
  $result = mysqli_query($com->getDb(), $sql);
  return $result;
}

public function isTokenExist($user_id, $token) {
  $com = new DbConnect();
  $token =  mysqli_real_escape_string($com->getDb(),$token);
  $sql = "select * from users where token = '$token' and user_id = '$user_id' Limit 1";
  $result = mysqli_query($com->getDb(), $sql);
  return $result;
}

public function isLoginExist($email, $password) {
  $com = new DbConnect();
  $email =  mysqli_real_escape_string($com->getDb(),$email);
  $password =  mysqli_real_escape_string($com->getDb(),$password);
  $sql = "select * from users where email = '$email' and password = '$password' Limit 1";
  $result = mysqli_query($com->getDb(), $sql);
  return $result;
}

public function sendMessage($sender_id, $receiver_id,$message, $message_type_id, $date) {
  $com = new DbConnect();
  $message =  mysqli_real_escape_string($com->getDb(),$message);
  $sql = "insert into messages (sender_id,receiver_id,message,message_type_id,message_date  ) VALUES ('$sender_id','$receiver_id','$message','$message_type_id','$date')";

  $result = mysqli_query($com->getDb(), $sql);
  return $com->getDb();
}

public function getMessagesByUser($user_id){
  $com = new DbConnect();
  $sql = "select * from messages ,users where ( messages.sender_id = '$user_id' and messages.deleted_by_sender_id = '0' and users.user_id = messages.receiver_id ) or ( messages.receiver_id = '$user_id' and messages.deleted_by_receiver_id = '0'   and users.user_id = messages.sender_id) order by messages.message_date desc";
  $result = mysqli_query($com->getDb(), $sql);
  return $result;
}

public function getLastMessagesByUser($user_id)    {
  $com = new DbConnect();
  $sql = "SELECT messages1.* FROM messages AS messages1 LEFT JOIN messages AS messages2
  ON ((messages1.sender_id = messages2.sender_id AND messages1.receiver_id = messages2.receiver_id) 
  OR (messages1.sender_id = messages2.receiver_id AND messages1.receiver_id = messages2.receiver_id))
  AND messages1.message_id < messages2.message_id
  WHERE (messages1.sender_id = '$user_id' OR messages1.receiver_id = '$user_id') 
  AND messages2.message_id IS NULL 
  order by messages1.message_date desc ";
  $result = mysqli_query($com->getDb(), $sql);
  return $result;
}

public function getMessagesByUserWithContact($user_id,$contact_id) {
  $com = new DbConnect();
  $sql = "select * from messages  where ( messages.sender_id = '$user_id' and messages.receiver_id = '$contact_id' and messages.deleted_by_sender_id = '0' ) or ( messages.sender_id   = '$contact_id' and messages.receiver_id = '$user_id' and messages.deleted_by_receiver_id = '0' ) order by messages.message_date asc";

  $result = mysqli_query($com->getDb(), $sql);
  return $result;
}
}
