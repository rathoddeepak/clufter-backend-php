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
$details = $_POST;
$req = empty($details['req']) ? 'create_booking' : $details['req'];
if($req == 'create_booking') {    
    $result = Tw_BookTable([
        'user_id' => $details['user_id'],
        'vendor_id' => $details['vendor_id'],
        'people' => $details['people'],
        'from_slt' => $details['from_slt'],
        'to_slt' => $details['to_slt'],
        'from_time' => $details['from_time'],        
        'to_time' => $details['to_time'],
        'items' => $details['items'],
        'amount' => $details['amount']
    ]);
}else if($req == 'ld_bkng' || $req == 'ld_his') {
    $limit = empty($details['limit']) ? 10 : $details['limit'];
    $offset = empty($details['offset']) ? 0 : $details['offset'];
    $data = ['all' => true];
    if(!empty($details['vendor_id'])){
        $data['vendor_id'] =  $details['vendor_id'];
    }else{
        $data['user_id'] =  $details['user_id'];
    }
    $result = Tw_LoadTBooking($data, $limit, $offset);
}else if($req == 'cl_bkng') {    
    $result = Tw_CancelBooking($details['booking_id'], $details['reason']);
}else if($req == 'bkng_dta') {    
    $result = Tw_BookingData($details['booking_id']);
}

//Floor & Table Management API END
else{
    Tw_HeaderExit(400, "Bad Request");
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);
?>