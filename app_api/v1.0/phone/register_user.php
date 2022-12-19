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

if (empty($_GET['type']) || !isset($_GET['type'])) {
    Tw_HeaderExit(400, "Bad Request!");
}
$type = $_GET['type'];
if ($type == 'register_user') {
    $details = $_POST;    
    $result = Tw_RegisterUser($details);
    if(is_string($result) && substr($result,  0, 1) == '~'){
     Tw_HeaderExit(400, substr($result,  1));
    }else{
     $userData = Tw_UserData($result['id']);
     Tw_HeaderExit(200, [
        "msg" => "Registered Successfully!", 
        'user_id' => $userData['id'],
        'status' => $userData['status'],
        's' => $result['s']
     ]);
    }
}

?>