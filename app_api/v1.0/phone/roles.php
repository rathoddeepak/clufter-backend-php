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
$req = empty($details['req']) ? 'adrl' : $details['req'];
if($req == 'adrl') {
	$result = Tw_VendorAddRole($details);
}else if($req == 'dlrl') {
    $result = Tw_VendorDelRole($details['role_id']);
}else if($req == 'edrl') {
    $result = Tw_VendorEditRole($details);
}else if($req == 'lgrl'){
    $result = Tw_LogUserRole($details);
}else if($req == 'ldrls'){
    $result = Tw_LoadVendorRoles($details['vendor_id']);    
}else if($req == 'rpt'){
    $result = Tw_GetRolesReport($details);    
}else{
    $result = "~Bad Request";
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>
