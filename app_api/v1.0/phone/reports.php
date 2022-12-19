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

if (empty($_GET['type']) 
|| !isset($_GET['type']))Tw_HeaderExit(400, "Bad Request!");
$details = $_POST;
$result = "~Bad Request";
$req = empty($details['req']) ? 'smry' : $details['req'];
if($req == 'smry'){
    $result = Tw_GenerateVendorSummary($details);
}else if($req == 'hodr'){
    $result = Tw_GetModifiedVisits($details);    
}else if($req == 'tkay'){
    $result = Tw_GetModifiedTakeAway($details);    
}else if($req == 'dlv'){
    $result = Tw_GetModifiedDelivery($details);    
}else if($req == 'bkg'){
    $result = Tw_GetModifiedBooking($details);    
}else if($req == 'itms'){
    $report = Tw_GetVendorFoodSales($details);
    $result = [
        'rp' => $report,
        'htl' => Tw_VendorData($details['id'])->name
    ];
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>