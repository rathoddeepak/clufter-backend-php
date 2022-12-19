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
if(!empty($details['postData'])){    
    $details = json_decode($details['postData'], true);    
}
$req = empty($details['req']) ? 'create_shop' : $details['req'];

if($req == 'create_shop') {
    $result = Tw_CreateShop($details);
}else if($req == 'meta'){
    $result = Tw_ShopMetaData($details);
}else if($req == 'confirm'){
    $result = Tw_PrepareAgreement($details);
}else if($req == 'ld_rstnt'){
    $result = Tw_LoadUserRestaurants($_POST['user_id'], true);
}else if($req == 'v_data'){
    $data = Tw_VendorData($_POST['v_id']);
    $result = $data ? $data : '~Vendor Not Found';
}else if($req == 'ld_cat'){
    $result = Tw_GenerateCategory(empty($_POST['key']) ? '' : $_POST['key']);
}else if($req == 'add_food'){
    $result = Tw_AddVendorFood($details, isset($_FILES['food']) ? $_FILES['food'] : null);
}else if($req == 'del_food'){
    $result = Tw_DeleteVendorFood($_POST['id']) ? "Deleted Sucessfully" : "~Unable to Delete";
}else if($req == 'chng_stat'){
    $result = Tw_ChangeFoodStatus($_POST);
}else if($req == 'ld_food'){
    $userData = Tw_UserData($_POST['user_id']);
    $vendorData = Tw_VendorData($_POST['v_id']);
    $result = array(
        'food' => Tw_LoadVendorFood($_POST['v_id'], true), 
        'username' => $userData['name'],
        'avatar' => $userData['avatar'],
        'res_name' => $vendorData->name
    );
}else if($req == 'upld_doc'){
    if(empty($details['type']))
        $result = "~Unable to upload document";
    else 
        $result = Tw_VendorDoc($details['type'], $_FILE['file']);
}else if($req == 'ld_vendors'){
    $result = Tw_ModifiedVendorList($details);
}else if($req == 'vendor_data'){
    $secure = empty($details['srt']) ? fasle : true;
    if(empty($details['id']))$result = false;
    else $result = Tw_ModifiedVendorData($details, $secure);    
}
/*Floor & Table Management API
else if($req == 'add_flr'){
    $result = Tw_AddVendorFloor($details);
}else if($req == 'del_flr'){
    $result = Tw_DeleteVendorFloor($details['id']);
}else if($req == 'ld_flr'){
    $result = Tw_LoadVendorFloors($details['v_id']);
}else if($req == 'svtbls'){
    $result = Tw_SaveVTableConfig($details);
}else if($req == 'edt_tbl'){
    $result = Tw_EditVendorTable($details);    
}else if($req == 'del_tbl'){
    $result = Tw_DeleteVendorTable($details['id']);
}else if($req == 'ld_tbls'){
    $result = Tw_LoadVendorTables($details['f_id']);
}else if($req == 'v_tbl'){
    $floors = Tw_LoadVendorFloors($details['v_id']);
    $tables = [];
    if(count($floors) > 0)
        $tables = Tw_LoadVendorTables($floors[count($floors) - 1]->id);    
    $result = [
        'tables' => $tables,
        'floors' => $floors
    ];
}
Floor & Table Management API END*/
else if($req == 'ldbkcn'){
    $result = Tw_UserTableBookings($details['user_id']);
}else{
    Tw_HeaderExit(400, "Bad Request");
}


if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);
?>