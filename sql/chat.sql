
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

drop TABLE IF EXISTS messages ;
drop TABLE IF EXISTS message_type ; 
drop TABLE IF EXISTS friends ;
drop TABLE IF EXISTS users ; 

CREATE TABLE users (
user_id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
name text NOT NULL  ,
email VARCHAR(191) NOT NULL UNIQUE ,
password text NOT NULL,
token  text NOT NULL  ,
 status text NOT NULL  ,
 image text NOT NULL  ,
 user_date bigint  NOT NULL ,
 last_seen bigint  NOT NULL 
);

CREATE TABLE friends (
friends_id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
request_user_id bigint NOT NULL ,
approved_user_id bigint NOT NULL ,
type int NOT NULL DEFAULT 0  
 );

CREATE TABLE   message_type (
type_id int NOT NULL AUTO_INCREMENT PRIMARY KEY ,
type_name text NOT NULL
) ;



CREATE TABLE messages (
message_id bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
sender_id bigint NOT NULL,
receiver_id bigint NOT NULL,
message  text CHARACTER SET utf8 NOT NULL,
message_type_id int NOT NULL DEFAULT 1,
message_date bigint  NOT NULL  ,
deleted_by_sender_id int NOT NULL DEFAULT 0,
deleted_by_receiver_id int NOT NULL DEFAULT 0
);



INSERT INTO message_type (type_id , type_name) VALUES
(1,  'normal_text'),
(2,  'image_text') 
;



 



