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
$result = "~Bad Request";
$req = empty($details['req']) ? 'uhl' : $details['req'];
if($req == 'lit') {
	$result = Tw_LoadInitialTrack($details['order_id']);
}else if($req == 'oltd') {
    $result = Tw_OrderLiveTrackData($details['order_id']);
}else if($req == 'uhl') {    
    $result = Tw_UpdateHeroLocation($_GET);
}else{
    $result = "~Bad Request";
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>
