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

$req = empty($details['req']) ? 'ldkot' : $details['req'];

if ($req == 'ldkot') {    
    $result = Tw_GetVendorKOT($details['v_id']);
}else if ($req == 'pp') {//Prepared
    $result = Tw_ChefOrderTick($details['ids']);
}else if ($req == 'dp') {//Prepared
    $result = Tw_ChefDeliveryTick($details['ids']);
}else if ($req == 'akot') {    
    $result = Tw_GetVendorAutoKOT($details['v_id']);
}else if ($req == 'abkt') {    
    $result = Tw_GetVendorBillKOT($details['v_id']);
}elseif ($req == 'cpoi') {//Captain Only Order Intialize
    $foods = Tw_LoadVendorFood($details['vendor_id'], [FD_BOTH, FD_ONLYMNU]);
    $roles = Tw_LoadVendorRoles($details['vendor_id'], 0);
    $foodList = [];
    foreach($foods as $food){
        $food->quantity = 0;
        $foodList[] = $food;
    }
    if(empty($details['offset'])){
        $areas = Tw_LoadVendorAreas($details['vendor_id']);
    }else{
        $areas = [];
    }
    $tables = [];
    if(count($areas) > 0){
        $m = $details['m'] == 1;
        if($m){
            $tables = Tw_LoadVendorTables($areas[0]->id, true, false);//Without Take Away
        }else{
            $tables = Tw_LoadVendorTkAways($areas[0]->id);//Only Take Away
        }
    }
    $result = [
        'tables' => $tables,
        'areas' => $areas,
        'foods' => $foodList,
        'roles' => $roles,
        'tax' => Tw_GetClufterAppliedTaxes($details['vendor_id'])
    ];
}else if($req == 'cpou'){
    $m = $details['m'] == 1;
    if($m){
        $result = Tw_LoadVendorTables($details['area_id'], true, false);
    }else{
        $result = Tw_LoadVendorTkAways($details['area_id']);
    } 
}else if($req == 'ctwy'){    
    $areas = Tw_LoadVendorAreas($details['vendor_id']);
    if(count($areas) > 0){
        $area_id = $areas[0]->id;
        $details['area_id'] = $area_id;
    }
    $tableTA = Tw_CreateTakeAway($details, true);
    $details['table_id'] = $tableTA['id'];
    $result = Tw_AddFoodToVisit($details);
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>