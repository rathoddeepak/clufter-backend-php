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
if(empty($details['vid']) || !Tw_VendorExistsById($details['vid']))
    Tw_HeaderExit(400, 'Vendor Not Found');
$vendorData = Tw_VendorData($details['vid']);
$file = Tw_MoveMedia($_FILES['files']);
if($file){
    if(file_exists($file))unlink($vendorData->fssai_image);
    $db->where('id', $details['vid'])->update(V_FAMILY, ['fssai_image' => $file]);
    Tw_HeaderExit(200, 'FSSAI Image Uploaded');
}else{
    Tw_HeaderExit(400, 'Error While Uploading');
}
?>