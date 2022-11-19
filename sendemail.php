<?php

session_start();

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
$from = "tokomasenamitc2@enamitc2.com";
$to = $_SESSION['email'];
$subject = "Password Reset";
$message = $_SESSION['message'];
$headers = "From:" . $from;
if(mail($to,$subject,$message, $headers)) {
    echo "The email message was sent.";
} else {
    echo "The email message was not sent.";
}
?>