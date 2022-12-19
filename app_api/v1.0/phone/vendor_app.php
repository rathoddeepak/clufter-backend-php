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
$post = $_POST;
if(!empty($post['postData'])){    
    $post = json_decode($post['postData'], true);    
}
$req = empty($post['req']) ? 'updts' : $post['req'];
if($req == 'pnrq'){
    $result = Tw_RegisterVendorRq($post);
}else if($req == 'mysh'){
    $result = Tw_LoadUserRestaurants($post['user_id']);
}else if($req == 'updts') {//Updates
	//$hotelOrders = Tw_CountPendingVisits(['vendor_id' => $post['vendor_id']]);
	$orders = Tw_CountPendingOrders($post['vendor_id']);
    $bookings = Tw_CountVendorBookings($post['vendor_id']);
    $vendorData = Tw_VendorData($post['vendor_id']);
	$result = [
		//'hotel_o' => $hotelOrders,
        'table_b' => $bookings,
		'orders_p' => $orders,
        'slt_lmt' => $vendorData->slotLimit,
        'ppl_lmt' => $vendorData->limitPeople,
        'dlv_sts' => $vendorData->delivery == 1 ? true : false,
        'tbl_bk' => $vendorData->table_booking == 1 ? true : false
	];
}else if($req == 'adph'){     
    $result = Tw_AddVendorPhotos($post, $_FILES['file']);    
}else if($req == 'dlph'){        
    $result = Tw_DeleteVendorPhotos($post);    
}else if($req == 'upcv'){
    $result = Tw_UpdateVendorCover($post, $_FILES['file']);
}else if($req == 'uplg'){
    $result = Tw_UpdateVendorLogo($post, $_FILES['file']);
}else if(startsWith($req, 'vup')){
    $updateType = $req;
    $check = true;
    $error = false;
    switch($updateType){
        case 'vups1':
        $object = [
            'name' => $post['name'],
            'about' => $post['about']
        ];
        if(!empty($post['rcpxtra'])){
            $object['rcpxtra'] = $post['rcpxtra'];
        }        
        $object['ocntm'] = empty($post['ocntm']) || !is_numeric($post['ocntm']) ? 0 : $post['ocntm'];
        $object['has_kot'] = empty($post['has_kot']) ? 0 : 1;
        $check = false;
        break;
        case 'vups2':
        $object = [
            'active' => $post['active'],
            'table_booking' => $post['table_booking'],
            'has_mess' => $post['has_mess'],            
        ];
        if(!is_numeric($post['active']) || !is_numeric($post['table_booking']) || !is_numeric($post['has_mess'])){
            $error = true;
        }
        break;
        case 'vups3':
        $object = [
            'address' => $post['address'],
            'long' => $post['long'],
            'lat' => $post['lat'],
            'pincode' => $post['pincode']
        ];
        break;
        case 'vups4':
        $object = [
            'open_time' => $post['open_time'],
            'close_time' => $post['close_time']                    
        ];
        break;
        case 'vups5':
        $object = [
            'owner_number' => $post['owner_number'],
            'manager_number' => $post['manager_number'],
            'manager_mail' => $post['manager_mail']            
        ];
        break;
        case 'vups6':
        $object = [
          'pan_number' => $post['pan_number'],
          'ligal_entity' => $post['ligal_entity'],
       'ligal_entity_address' => $post['ligal_entity_address'],            
          'fssai_number' => $post['fssai_number'],
          'fssai_expiry' => $post['fssai_expiry']
        ];
        $check = false;
        break;
        case 'vups7':
        $object = [
          'baccount_number' => $post['baccount_number'],
          'baccount_ifsc' => $post['baccount_ifsc']
        ];
        $check = false;
        break;
    }
    if($error){
        $result = "~Error While Updating Data.";
    }else if($check){
        $pendingOrders = Tw_CountPendingOrders($post['vendor_id']);
        if($pendingOrders > 0){
            $result = "~You Have {$pendingOrders} Pending Orders, please complete them to update details, changing details now will confuse our delivery hero.";
        }else{
            $result = Tw_UpdateVendorData($post['vendor_id'], $object);
        }
    }else{
        $result = Tw_UpdateVendorData($post['vendor_id'], $object);
    }    
}else if($req == 'stax'){
    $result = Tw_SetVendorTax($post);
}else if($req == 'valfd'){    
    $food = Tw_LoadVendorFoods2($post['vendor_id'], 0);    
    $cat = Tw_LoadFoodCategories();
    $result = [
        'cat' => $cat,
        'food' => $food
    ];
}else if($req == 'valfd2'){
    $catId = empty($post['cat_id']) ? 0 : $post['cat_id'];      
    $cats = array();
    $counter = 0;
    $lastCat = 0;
    $catIds = array();
    $catList = array();
    if($catId == 0){
        $foodList = Tw_LoadVendorFoods2($post['vendor_id']);        
        foreach($foodList as $key => $food){
            if($food->cat != $lastCat && !in_array($food->cat, $catIds)){
                $lastCat = $food->cat;
                $catData = Tw_FoodCatData($lastCat);
                $cats[] = $catData;               
                $catObject = array();
                $catObject['id'] = $lastCat;
                $catObject['title'] = $catData->name;
                $catObject['data'] = array();
                $catObject['section'] = $counter;
                foreach($foodList as $dakel){
                    if($lastCat == $dakel->cat){
                        $catObject['data'][] = $dakel;
                    }
                }
                $catIds[] = $food->cat;
                $counter++;
            }
        }        
        $cats = Tw_SeqCatList($post['vendor_id'], $cats, true);
        if(empty($post['isSearch']) && !empty($cats)){
            $foodList = Tw_LoadVendorFoods2(
                $post['vendor_id'],
                $cats[0]->id
            );
        }
        $result = [
            'cat' => $cats,
            'all_cat' => Tw_LoadFoodCategories(),
            'food' => $foodList
        ];
    }else{  
        $type_id = 0;
        $special = -2;     
        $mustTry = -1;
        if(!empty($post['type_id'])){
            $catId = 0;
            $type_id = $post['type_id'];
        }        
        if($type_id == $special){            
            $foodList = Tw_GetSectionData(OUR_SPECIAL, $post['vendor_id']);
        }else if($type_id == $mustTry){
            $foodList = Tw_GetSectionData(MUST_TRY, $post['vendor_id']);
        }else{
            $foodList = Tw_LoadVendorFoods2(
                $post['vendor_id'],
                $catId,
                $type_id == 0 ? -1 : $type_id
            );
        }
        $result = ['food' => $foodList];
    }
}else if($req == 'up_fsts'){
    $result = Tw_UpdateFoodStatus($post['food_id'], $post['status']);  
}else if($req == 'pdbk'){    
    $pending = Tw_LoadTBooking($post, $post['limit'], $post['offset']); 
    $upcoming  = Tw_LoadAssignedBookings([
        'timed' => true,
        'vendor_id' => $post['vendor_id']
    ]);
    $result = [
        'upcoming' => $upcoming,
        'list' => $pending
    ];
}else if($req == 'psbk'){    
    $list = Tw_LoadCustomBooking($post, 1, $post['limit'], $post['offset']);     
    $result = ['list' => $list];
}else if($req == 'cnbk'){    
    $list = Tw_LoadCustomBooking($post, 2, $post['limit'], $post['offset']);     
    $result = ['list' => $list];
}else if($req == 'usr_vst'){
    $result = Tw_GetVisits([
        'vendor_id' => $post['vendor_id'],
        'status' => $post['status']
    ]);
}else if($req == 'htorsts'){
    $result = Tw_UpdateVisitStatus($post['id'], $post['status']);

//Slots Functions
}else if($req == 'gvslots'){
    $result = Tw_GetVendorSlots($post['vendor_id']);
}else if($req == 'gaslots'){
    $result = Tw_GetAllSlots($post['vendor_id']);
}else if($req == 'addSlt'){
    $result = Tw_UpdateHotelSlot($post);
}else if($req == 'avlslt'){
    $result = Tw_GetAviliableSlots($post['vendor_id']);
}else if($req == 'ntfycst'){
    $result = Tw_NotifyVendorUser($post);
}else if($req == 'ntfydta'){
    $used = Tw_CheckNotifyVendor($post['vendor_id']);
    $count = Tw_CountVendorCustomers($post['vendor_id']);
    $data = [
        'notify_used' => $used,
        'notify_left' => MAX_NOTIFY_COUNT - $used,
        'cust_count' => $count
    ];
    $result = $data;
}else if($req == 'dlv_sts'){    
    $result = Tw_DeliveryStatus($post['vendor_id'], $post['status']);
}else if($req == 'tbl_sts'){    
    $result = Tw_TableBookingStatus($post['vendor_id'], $post['status']);
}else if($req == 'up_ppl'){
    $result = Tw_UpdatePeopleLimit($post['vendor_id'], $post['limit']);
}else if($req == 'up_sll'){
    $result = Tw_UpdateSlotLimit($post['vendor_id'], $post['limit']);
}else if($req == 'stat'){
    $object = [
        'vendor_id' => $post['vendor_id'],
    ];
    if(!empty($post['date'])){
        $object['date'] = $post['date'];
    }else if(!empty($post['range'])){
        $object['range'] = $post['range'];
    }
    $rangeEarning = Tw_GetEarningData($object);
    $settlement = Tw_SettleData($post['vendor_id']);
    $result = [
        'total' => $rangeEarning['total'],
        'bookings' => $rangeEarning['bookings'],
        'orders' => $rangeEarning['orders'],
        'onhold' => $settlement['onhold'],
        'settled' => $settlement['settled'],
        'allTotal' => $settlement['allTotal']
    ];
}else if($req == 'xtra_sc_act'){
    $result = Tw_TglFoodToSection($post['id'], $post['section'], $post['vendor_id']);
}else if($req == 'cat_all_act'){
    $result = Tw_ProcessCatAction($post['status'], $post['cat_id'], $post['vendor_id']);
}else{
    $result = '~Bad Request';
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);
?>