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
$type = empty($_POST['req']) ? 'frgt_pass' : $_POST['req'];
if ($type == 'frgt_pass') {
    $result = Tw_SendOtp($_POST);
    if($result){
       $result = [
            'msg' => "OTP Sent Successfully!", 
            "otp" => $result['code'],
            "user_id" => $result['user_id'],
        ];
    }
}else if ($type == 'rst_pass') {
    $result = Tw_ResetPassword($_POST['user_id'], $_POST['password']);
    if($result)$result = "Password changed successfully!";
    else $result = "Unable to change password!";
}else if ($type == 'adrs') {
    $result = Tw_UserAddresses($_POST['user_id']);
}else if ($type == 'ad_adr'){
    $result = Tw_AddUserAddress($_POST);
}else if ($type == 'dl_adr'){
    $result = Tw_DelUserAddress($_POST['id']);
}else if ($type == 'rfds'){
    $result = Tw_GetRefunds($_POST['user_id'], $_POST['limit'], $_POST['offset']);
}else if ($type == 'updts'){
    $bookings = Tw_LoadTBooking([
        'user_id' => $_POST['user_id']
    ], 15, 0, true);
    if(!empty($_POST['code_ver']) && $_POST['code_ver'] > 8){
        $orders = Tw_UserFoodLiteUpdates($_POST['user_id'], 15, 0, false, true);
    }else{
        $orders = Tw_UserFoodOrderUpdates($_POST['user_id'], 15, 0, false, true);
    }    
    $result = [
        [            
            'title' => 'Table Bookings',
            'data' => $bookings
        ],
        [
            'title' => 'Orders',
            'data' => $orders 
        ]
    ];
}else if($type == 'upt'){
    $result = Tw_UpdateUserData($_POST);    
}else if($type == 'dta'){
    $result = Tw_UserData($_POST['user_id']);

}else if($type == 'cpmdta'){//Complain Data
    $data = Tw_ComplainData($_POST['id']);
    $result = $data ? $data : "~Unable to get complain data";
}else if($type == 'crt_cmp'){//Create Complain
    $result = Tw_CreateComplain($_POST);
}else if($type == 'cmp'){//Get Complain Checked
    $result = Tw_GetComplains([
        'user_id' => $_POST['user_id'],
        'checked' => 1
    ]);
}else if($type == 'ucmp'){//Get Complain Checked
    $result = Tw_GetComplains([
        'user_id' => $_POST['user_id'],
        'checked' => 0
    ]);
}else if($type == 'adrt'){//Get Complain Checked
    $result = Tw_AddRating($_POST);
}else if($type == 'vst'){//Get Complain Checked    
    $mv = Tw_CountUserVisits($_POST['user_id']);    
    $result = [
        'mv' => $mv,
        'mpv' => MPV,
        'me'=> MPV * $mv
    ];    
}



if(is_string($result) && startsWith($result, "~")){
    Tw_HeaderExit(400, substr($result,  1));
}else{
    $data = is_string($result) ? substr($result,  1) : $result;
    Tw_HeaderExit(200, $data);   
}

?>