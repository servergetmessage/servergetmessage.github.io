<?php

$DBOperations_path = __DIR__ . "/../sql/DBOperations.php";
include($DBOperations_path);

class GeneralFunctionsClass
{
    public function __constructor()
    {

    }

    public function getUserInfoWithPassword($resultUser)
    {
        while ($rowUser = mysqli_fetch_array($resultUser)) {
            $user = $this->getUserAtRow($rowUser);
            $user["password"] = $rowUser["password"];
        }
        return $user;
    }

    public function getUserInfo($resultUser)
    {
        while ($rowUser = mysqli_fetch_array($resultUser)) {
            $user = $this->getUserAtRow($rowUser);
        }
        return $user;
    }

    public function getUserInfoSafety($resultUser)
    {
        while ($rowUser = mysqli_fetch_array($resultUser)) {
            $user = $this->getUserAtRowSafety($rowUser);
        }
        return $user;
    }

    public function getUserAtRow($rowUser)
    {
        $user["user_id"] = $rowUser["user_id"];
        $user["name"] = $rowUser["name"];
        $user["email"] = $rowUser["email"];
        $user["token"] = $rowUser["token"];
        $user["status"] = $rowUser["status"];
        $user["user_date"] = $rowUser["user_date"];
        $user["image"] = $rowUser["image"];
        $user["last_seen"] = $rowUser["last_seen"];
        return $user;
    }

    public function getUserAtRowSafety($rowUser)
    {
        $user["user_id"] = $rowUser["user_id"];
        $user["name"] = $rowUser["name"];
        $user["email"] = $rowUser["email"];
        $user["status"] = $rowUser["status"];
        $user["user_date"] = $rowUser["user_date"];
        $user["image"] = $rowUser["image"];
        $user["last_seen"] = $rowUser["last_seen"];
        return $user;
    }

    public function getFriendsAtRow($rowFriends)
    {
        $friends["friends_id"] = $rowFriends["friends_id"];
        $friends["request_user_id"] = $rowFriends["request_user_id"];
        $friends["approved_user_id"] = $rowFriends["approved_user_id"];
        $friends["type"] = $rowFriends["type"];
        return $friends;
    }

    public function getMessagesByUser($resultMessages)
    {
        $dbOperationsObject = new DBOperations();
        $messages = array();
        while ($rowMessage = mysqli_fetch_array($resultMessages)) {
            $message = array("message_id" => $rowMessage['message_id'],
                "sender_id" => $rowMessage['sender_id'],
                "receiver_id" => $rowMessage['receiver_id'],
                "message" => $rowMessage['message'],
                "message_type_id" => $rowMessage['message_type_id'],
                "message_date" => $rowMessage['message_date'],
                "deleted_by_sender_id" => $rowMessage['deleted_by_sender_id'],
                "deleted_by_receiver_id" => $rowMessage['deleted_by_receiver_id'],
                "name" => $rowMessage['name'],
                "email" => $rowMessage['email'],
                "token" => $rowMessage['token'],
                "status" => $rowMessage['status'],
                "image" => $rowMessage['image'],
                "user_date" => $rowMessage['user_date'],
                "last_seen" => $rowMessage['last_seen']
            );
            $resultMessageType = $dbOperationsObject->getMessageType($rowMessage["message_type_id"]);
            $messageType = $this->getMessageType($resultMessageType);
            $message["message_type"] = $messageType;
            array_push($messages, $message);
        }
        return $messages;
    }

    public function getLastMessagesByUser($resultMessages, $user_id)
    {
        $dbOperationsObject = new DBOperations();
        $messages = array();
        while ($rowMessage = mysqli_fetch_array($resultMessages)) {
            $message = array("message_id" => $rowMessage['message_id'],
                "sender_id" => $rowMessage['sender_id'],
                "receiver_id" => $rowMessage['receiver_id'],
                "message" => $rowMessage['message'],
                "message_type_id" => $rowMessage['message_type_id'],
                "message_date" => $rowMessage['message_date'],
                "deleted_by_sender_id" => $rowMessage['deleted_by_sender_id'],
                "deleted_by_receiver_id" => $rowMessage['deleted_by_receiver_id']
            );

            $resultMessageType = $dbOperationsObject->getMessageType($rowMessage["message_type_id"]);
            $messageType = $this->getMessageType($resultMessageType);
            $message["message_type"] = $messageType;
            if ($user_id != $rowMessage['sender_id']) {
                $senderUser = $this->getUserById($rowMessage['sender_id']);
                $friendInfo = $this->getFriendRelation($senderUser["user_id"], $user_id);
                $senderUser["friends_id"] = $friendInfo["friends_id"];
                $senderUser["request_user_id"] = $friendInfo["request_user_id"];
                $senderUser["approved_user_id"] = $friendInfo["approved_user_id"];
                $senderUser["type"] = $friendInfo["type"];
                $message["contact"] = $senderUser;
            } else {
                $receivedUser = $this->getUserById($rowMessage['receiver_id']);
                $friendInfo = $this->getFriendRelation($receivedUser["user_id"], $user_id);
                $receivedUser["friends_id"] = $friendInfo["friends_id"];
                $receivedUser["request_user_id"] = $friendInfo["request_user_id"];
                $receivedUser["approved_user_id"] = $friendInfo["approved_user_id"];
                $receivedUser["type"] = $friendInfo["type"];
                $message["contact"] = $receivedUser;
            }
            array_push($messages, $message);
        }
        return $messages;
    }

    public function getMessagesByUserWithContact($resultMessages)
    {
        $dbOperationsObject = new DBOperations();
        $messages = array();
        while ($rowMessage = mysqli_fetch_array($resultMessages)) {
            $message = array("message_id" => $rowMessage['message_id'],
                "sender_id" => $rowMessage['sender_id'],
                "receiver_id" => $rowMessage['receiver_id'],
                "message" => $rowMessage['message'],
                "message_type_id" => $rowMessage['message_type_id'],
                "message_date" => $rowMessage['message_date'],
                "deleted_by_sender_id" => $rowMessage['deleted_by_sender_id'],
                "deleted_by_receiver_id" => $rowMessage['deleted_by_receiver_id']
            );

            $resultMessageType = $dbOperationsObject->getMessageType($rowMessage["message_type_id"]);
            $messageType = $this->getMessageType($resultMessageType);
            $message["message_type"] = $messageType;
            array_push($messages, $message);
        }
        return $messages;
    }

    public function getMessageType($resultMessageType)
    {
        while ($rowMessageType = mysqli_fetch_array($resultMessageType)) {
            $messageType["type_id"] = $rowMessageType["type_id"];
            $messageType["type_name"] = $rowMessageType["type_name"];
        }
        return $messageType;
    }

    public function getContactsByUser($resultFriends)
    {
        $dbOperationsObject = new DBOperations();
        $friends = array();
        while ($rowFriend = mysqli_fetch_array($resultFriends)) {
            $friend = array("user_id" => $rowFriend['user_id'],
                "name" => $rowFriend['name'],
                "email" => $rowFriend['email'],
                "token" => $rowFriend['token'],
                "status" => $rowFriend['status'],
                "image" => $rowFriend['image'],
                "user_date" => $rowFriend['user_date'],
                "last_seen" => $rowFriend['last_seen'],
                "type" => $rowFriend['type'],
                "friends_id" => $rowFriend['friends_id'],
                "request_user_id" => $rowFriend['request_user_id'],
                "approved_user_id" => $rowFriend['approved_user_id']

            );
            array_push($friends, $friend);
        }
        return $friends;
    }

    public function getFriendRequestsByUser($resultFriends)
    {
        $dbOperationsObject = new DBOperations();
        $friends = array();
        while ($rowFriend = mysqli_fetch_array($resultFriends)) {
            $friend = array("user_id" => $rowFriend['user_id'],
                "name" => $rowFriend['name'],
                "email" => $rowFriend['email'],
                "token" => $rowFriend['token'],
                "status" => $rowFriend['status'],
                "image" => $rowFriend['image'],
                "user_date" => $rowFriend['user_date'],
                "last_seen" => $rowFriend['last_seen'],
                "type" => $rowFriend['type'],
                "friends_id" => $rowFriend['friends_id'],
                "request_user_id" => $rowFriend['request_user_id'],
                "approved_user_id" => $rowFriend['approved_user_id']
            );
            array_push($friends, $friend);
        }
        return $friends;
    }

    public function getUsersByFriendsId($friends_id)
    {
        $dbOperationsObject = new DBOperations();
        $result = $dbOperationsObject->getUsersByFriendsId($friends_id);
        while ($rowFriends = mysqli_fetch_array($result)) {
            $friends = $this->getFriendsAtRow($rowFriends);
        }

        return $friends;

    }

    public function getAllUsers($resultUsers)
    {
        $users = array();
        $dbOperationsObject = new DBOperations();
        while ($rowUser = mysqli_fetch_array($resultUsers)) {
            $resultUser = $dbOperationsObject->getUser($rowUser["user_id"]);
            $user = $this->getUserInfo($resultUser);
            array_push($users, $user);
        }
        return $users;
    }

    public function getFriendInfo($resultFriend)
    {
        $friend["friends_id"] = "-1";
        $friend["request_user_id"] = "0";
        $friend["approved_user_id"] = "0";
        $friend["type"] = "0";

        while ($rowFriend = mysqli_fetch_array($resultFriend)) {
            $friend = $this->getFriendAtRow($rowFriend);
            break;
        }
        return $friend;
    }

    public function getFriendAtRow($rowFriend)
    {
        $friend["friends_id"] = $rowFriend["friends_id"];
        $friend["request_user_id"] = $rowFriend["request_user_id"];
        $friend["approved_user_id"] = $rowFriend["approved_user_id"];
        $friend["type"] = $rowFriend["type"];
        return $friend;
    }

    public function getUserById($user_id)
    {
        $dbOperationsObject = new DBOperations();
        $result = $dbOperationsObject->getUser($user_id);
        $user = $this->getUserInfoSafety($result);
        return $user;
    }

    public function getUserRegestrationId($user_id)
    {
        $dbOperationsObject = new DBOperations();
        $result = $dbOperationsObject->getUser($user_id);
        $user = $this->getUserInfo($result);
        return $user["token"];
    }

    public function getFriendRelation($friend_user_id, $user_id)
    {
        $dbOperationsObject = new DBOperations();
        $resultFriend = $dbOperationsObject->getFriendRelation($friend_user_id, $user_id);
        $friend = $this->getFriendInfo($resultFriend);
        return $friend;
    }
}