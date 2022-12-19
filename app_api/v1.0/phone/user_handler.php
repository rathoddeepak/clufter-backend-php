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
    | Founder - Suhas Karande                                   |
    |-----------------------------------------------------------|
    | FoodBazzar,Mordern Food Ordering and Table Booking System |
    | FoodBazzar 2020 Copyright all rights reserved.            |
    |-----------------------------------------------------------|     
->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

if (empty($_GET['type']) || !isset($_GET['type']))Tw_HeaderExit(400, "Bad Request!");
$type = $_GET['type'];

$userData = Tw_UserData($details['user_id']);
if(!$userData)Tw_HeaderExit(400, 'User not found!');
if ($type == 'user_profile') {    

    $request = empty($details['request']) ? 'update_cover' : $details['request'];

    if($request == 'update_cover')
        $result = Tw_UpdateUserCover($details['user_id'], $_FILES['file']);
    else if($request == 'update_avatar')
        $result = Tw_UpdateUserAvatar($details['user_id'], $_FILES['file']);    

    if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
    else
     Tw_HeaderExit(200, $result);
    
}else{
    Tw_HeaderExit(400, "Bad Request!");
}

?>