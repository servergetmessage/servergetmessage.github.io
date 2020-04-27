<?php
define( 'API_ACCESS_KEY', 'AAAA6ut0AOA:APA91bG0E0S9RBRR6hfZYzBxy1YainCUYyKVf5bmfBgrQJz1Nt7xTplLW0xuZcZ2h_8XVFC1kUwgBbus_FnDz5Ey_jv5h5SPmUJQS6z_TYBJ-LdU7eumiRP5Wf0_TMqGq-embfddTkeT' );
define( 'FIREBASE_SEND_URL', 'https://fcm.googleapis.com/fcm/send' );
class FirebaseNotificationClass {
	function __construct() {

	}
    /**
     * Sending Push Notification
     */
    public function send_notification($registratoin_ids, $message) {
    	$msg = array
    	(
    		'title'		=> 'Firebase Notification',
    		'message'	=> $message,
    		'type'		=> 'message'
    	);
    	$fields = array
    	(
    		'registration_ids' 	=> array($registratoin_ids) ,
    		'data'			=> $msg
    	);

    	$headers = array
    	(
    		'Authorization: key=' . API_ACCESS_KEY,
    		'Content-Type: application/json'
    	);
    	$ch = curl_init();
    	curl_setopt( $ch,CURLOPT_URL, FIREBASE_SEND_URL );
    	curl_setopt( $ch,CURLOPT_POST, true );
    	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    	$result = curl_exec($ch );
    	curl_close( $ch );
    }
}
?>