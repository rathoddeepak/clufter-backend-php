<?php
/*->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>   
    |-----------------------------------------------------------|
    |@author        Deepak Balasaheb Rathod                     |
    |@author_url    https://foodbazzar.dp/about-us/             |
    |@author_mail   rathoddeepak537@gmail.com                   |
    |@site_url      https://foodbazzar.store                    |
    |@site_mail     foodbazzar@store.com                        |
    |-----------------------------------------------------------|
    | Founder - Kshitij Kendre                                  |
    | Founder - Vitthal Kendre                                  |
    |-----------------------------------------------------------|
    | FoodBazzar,Mordern Food Ordering and Table Booking System |
    | FoodBazzar 2020 Copyright all rights reserved.            |
    |-----------------------------------------------------------|     
->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
require_once('server/main.php');
header('Access-Control-Allow-Origin: *'); 
date_default_timezone_set('Asia/Kolkata');
$api_version = empty($_POST['v']) ? '1.0' : $_POST['v'];
$type        = '';
$applications = array('phone', 'windows_app', 'mac_app');
$application = 'phone';
if (!empty($_GET['version'])) {
    $api_version = $_GET['version'];
}
if (!empty($_GET['application'])) {
    if (in_array($_GET['application'], $applications)) {
        $application = $_GET['application'];
    }
}
if (!empty($_GET['type'])) {
    $type = $_GET['type'];
} 

$file = 'app_api/v'.$api_version.'/'.$application.'/'.$type.'.php';
if(file_exists($file)){    
    include($file);
}

unset($tw);
?>