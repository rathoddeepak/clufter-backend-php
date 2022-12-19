<?php
/*->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>   
    |-----------------------------------------------------------|
    |@author        Deepak Balasaheb Rathod                     |
    |@author_url    https://twohearts.dp/about/                 |
    |@author_mail   rathoddeepak143dp@gmail.com                 |
    |@site_url      https://twohearts.dp/home                   |
    |@site_mail     twohearts@social.com                        |
    |-----------------------------------------------------------|
    | TwoHearts, Private Space For Only Two Hearts              |
    | TwoHearts 2020 Copyright all rights reserved.             |
    |-----------------------------------------------------------| 
->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
if (empty($_GET['type']) || !isset($_GET['type']))Tw_HeaderExit(400, "Bad Request!");
$type = $_GET['type'];
if ($type == 'notification_handler') {
    $result  = "~";
    $request = empty($_POST['request']) ? 'get' : $_POST['request'];
    if($request == 'get')$result = Tw_GetNotifications($_POST['user_id'], empty($_POST['last_read']) ? 0 : $_POST['last_read']);    
    if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
    else     
     Tw_HeaderExit(200, $result);    
}else{
    Tw_HeaderExit(400, "Bad Request!");
}
?>