<?php

//require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "libs", "php-ffmpeg", "vendor", "autoload.php"));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "libs", "blurhash", "vendor", "autoload.php"));

require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "libs", "PHPMailer", "vendor", "autoload.php"));

require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "libs", "twilio", "vendor", "autoload.php"));

require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "..", "libs", "razorpay", "Razorpay.php"));

require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "tookan.php"));

require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "general_methods.php"));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "config.php"));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, "types.php"));

date_default_timezone_set("Asia/Kolkata");

$tw = array();
$tw['smtp_host'] = 'smtp.hostinger.com';
$tw['smtp_port'] = 587;
$tw['smtp_user'] = 'support@ydcrackers.com';
$tw['smtp_pass'] = 'FcqWYx#v1';
$tw['socket_port'] = 2021;
$tw['loggedin'] = false;
$tw['user_id'] = -1;
$tw['theme'] = 'web';
$tw['site_name'] = 'FoodBazzar';
$tw['author'] = 'Deepak Balasaheb Rathod';
$tw['site_url'] = 'http://localhost/FoodBazzar/';
$tw['theme_url'] = 'http://localhost/FoodBazzar/themes/web/assets/';
$tw['api_url'] = 'http://localhost/FoodBazzar/';
if(isset($_GET['link']))$tw['page']   = $_GET['link'];
else $tw['page']   = 'home';
if(!empty($_SESSION['s']) && !empty($_SESSION['uid'])){
	$tw['loggedin'] = Tw_AuthorizeUser($_SESSION['uid'], $_SESSION['s']);
	$tw['user_id'] = $_SESSION['uid'];
}else if(!empty($_GET['s']) && !empty($_GET['user_id'])){
	$tw['loggedin'] = Tw_AuthorizeUser($_GET['user_id'], $_GET['s']);
	$tw['user_id'] = $_GET['user_id'];
} else if( !empty($_POST['s']) && !empty($_POST['user_id'])){	
	$tw['loggedin'] = Tw_AuthorizeUser($_POST['user_id'], $_POST['s']);
	$tw['user_id'] = $_POST['user_id'];
}

if($tw['user_id'] != -1)$tw['user'] = Tw_UserData($tw['user_id']);

?>