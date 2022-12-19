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
$req = empty($details['req']) ? 'home' : $details['req'];
if($req == 'home') {
	$closed = $db->where('id', 1)->getOne('temp');
	if($closed->val == 1){
		$order_count = count(Tw_UserOrders($details['user_id']));
		$slider = [
			[
				'title' => 'All Riders Busy!',
				'desc' => "Please Come Back After Few Minutes",
				'type' => 'link',
				'hash' => 'L24-:}I]0h$x~SR.9vxC9ds-t5WC',
				'type_data' => 'https://clufter.com',
				'image' => 'https://i.ibb.co/6t2sXrC/Food-Ordering-1-min.png'
			]
		];
		$result = [
			'category' => [],
			'resturants' => [],
			'slider' => $slider,
			'order_count' => $order_count,
			'visit' => false,
			'update' => 9,
			'sections' => []
		];
	}else{
		$radius = empty($details['radius']) ? 15 : $details['radius'];
		$addressData = Tw_AddressData($details['address_id']);
		$user_lat = $addressData->lat;
		$user_long = $addressData->lng;
		$categories = Tw_LoadFoodCategories(0, 12);
		$onlyVeg = empty($details['fdType']) ? BOTH_FOOD : $details['fdType'];
		$rpack = Tw_ModifiedVendorList([
			'radius' => $radius,
			'user_long' => $user_long,
			'user_lat' => $user_lat,
			'noreview' => true,
			'only_veg' => $onlyVeg,
			'delivery' => 1,
			'offset' => 0,
			'limit' => 10	
		]);
		$clrest = [];
		$resturants = [];
		/*$ids = [];
		foreach($rpack as $r)$ids[] = $r['id'];
		$peopleOrdering = Tw_LoadVendorFoods([
			'key_word' => "",
			'approved' => FOOD_APPROVED,
			'vendors' => $ids,
			'offset' => 0,
			'limit' => 6
		]);*/
		$slider = [
			[
				'title' => 'Best Quality Food',
				'desc' => "Order From Aamantran",
				'type' => 'vendor',
				'hash' => 'LDK04@1aTX~U0q?BR~X30dzXRPE0',
				'type_data' => Tw_ModifiedVendorData([				
					'id' => 20,
					'user_lat' => $user_long,
					'user_long' => $user_lat
				]),
				'image' => 'https://i.ibb.co/kDLvPv7/aamantran-min.png'
			],			
			[
				'title' => 'Best Veg Food In Latur',
				'desc' => 'Order From Hotel Gandharva Garden Pure Veg',
				'type' => 'vendor',
				'hash' => 'LGE.ObS7OsIU~TIrS7s9o@WAajjb',
				'type_data' => Tw_ModifiedVendorData([				
					'id' => 6,
					'user_lat' => $user_long,
					'user_long' => $user_lat,
					'hide_reviews' => true
				]),
				'image' => 'https://i.ibb.co/V36Q5tP/sda.png'
			]
		];
		if(VEG_FOOD != $onlyVeg){
			$slider[] = [
				'title' => 'Best Veg & Non Veg Resturant',
				'desc' => 'Order From Hotel Garam Masala',
				'type' => 'vendor',
				'hash' => 'LNH-u$00af%MIAXQsAR+00?^s:M{',
				'type_data' => Tw_ModifiedVendorData([				
					'id' => 9,
					'user_lat' => $user_long,
					'user_long' => $user_lat,
					'hide_reviews' => true
				]),
				'image' => 'https://i.ibb.co/CPhrT51/ce0bb160-010d-4d5d-99db-01ce1484bcbd-min.jpg'
			];
		}
		shuffle($slider);	
		foreach ($rpack as $rst) {
			if($rst['closed'] || $rst["delivery"] == 0){
				$clrest[] = $rst;
			}else{
				$resturants[] = $rst;
			}
		}
		$resturants = array_merge($resturants, $clrest);
		if($addressData->user_id == 30){
			$resturants[] = Tw_ModifiedVendorData([
				'id' => 2,
				'user_lat' => $user_lat,
				'user_long'=> $user_long,
				'hide_reviews' => true
			]);
		}
		$order_count = count(Tw_UserOrders($addressData->user_id));
		$vst = Tw_GetCurrentVisit($addressData->user_id);
		$visit = false;
		if($vst){
			$visit = [
				'vendor_id' => $vst['vendor_id'],
				'visit_id' => $vst['visit_id']
			];
		}
		$result = [
			'category' => $categories,
			'resturants' => $resturants,
			'slider' => $slider,
			'order_count' => $order_count,
			'visit' => $visit,
			'update' => 9,
			'sections' => [
				['title' => 'Approved','items' => []]
			]
		];
	}	
}else if($req == 'ldrst'){
	$radius = empty($details['radius']) ? 15 : $details['radius'];
	$onlyVeg = empty($details['fdType']) ? BOTH_FOOD : $details['fdType'];
	$addressData = Tw_AddressData($details['address_id']);
	$user_lat = $addressData->lat;
	$user_long = $addressData->lng;
	$rpack = Tw_ModifiedVendorList([
		'radius' => $radius,
		'user_long' => $user_long,
		'user_lat' => $user_lat,
		'only_veg' => $onlyVeg,
		'offset' => $details['offset'],
		'limit' => 10,
		'delivery' => 1
	]);
	$clrest = [];
	$result = [];
        foreach ($rpack as $rst) {
		if($rst['closed'] || $rst["delivery"] == 0){
			$clrest[] = $rst;
		}else{
			$result[] = $rst;
		}
	}
	$result = array_merge($result, $clrest);
}else if($req == 'fd_meta' && !empty($details['id'])) {	
		$foodData = Tw_VendorFoodData($details['id']);
		if(!$foodData)Tw_HeaderExit(400, "Item Not Found");		
		$resturant = Tw_ModifiedVendorData([
			'id' => $foodData->vendor_id,
			'user_lat' => $details['user_lat'],
			'user_long' => $details['user_long'],
		]);
		$alsoAdd = Tw_LoadVendorFdSug($foodData->vendor_id, $foodData->id, [FD_BOTH,FD_ONLYDLV]);
		$result = [			
			'vendor_id' => $resturant['id'],
			'vendor_name' => $resturant['name'],
			'vendor_image' => $resturant['cover'],
			'vendor_hash' => $resturant['cover_hash'],
			'vendor_rating' => $resturant['rating'],
			'restaurant_approved' => $resturant['approved'],
			'photo_count' => Tw_CountVendorPhotos($foodData->vendor_id),
			'review_count'	=> Tw_CountVendorReview($foodData->vendor_id),	
			'alsoAdd' => $alsoAdd
		];
}else if($req == 'odc') {
		$result = count(Tw_UserOrders($details['user_id']));
}else if($req == 'vr_meta') {
		$result = Tw_VendorMeta($details);		
}else if($req == 'htOdr') {
	$result = Tw_VendorMeta($details);
	if(!is_string($result) && !empty($details['taxes'])){
		$taxData = Tw_GetClufterAppliedTaxes($details['id']);	
		$result['taxes'] = $taxData['taxes'];
		$result['tax_percent'] = $taxData['tax_percent'];
	}	
}else if($req == 'vr_meta2') {
	    if(empty($details['id'])){
	    	$result = "~Error";
	    }else{
	    	$secure = false;
	    	if(!empty($details['security']) && $details['security'] == KISOK){
				$secure = true;
			}
	    	$details['hide_reviews'] = true;
			$vendorData = Tw_ModifiedVendorData($details, $secure);			
			$photos = Tw_GetVendorPhotos($details['id'],3,0);		
			$meta = Tw_GetReviewValues($details['issuer'],$details['id']);
			$result = [
			   'vendor' => $vendorData,'review' => $meta,
			   'photos' => $photos
			];
	    }	    
}else if($req == 'vr_meta3') {
	    $metaData = Tw_VendorMeta($details);		
	    if(!is_string($metaData)){	    	
			$taxData = Tw_GetClufterAppliedTaxes($details['id']);	   
	    	$metaData['slotData'] = Tw_GetAviliableSlots($details['id']);
			$metaData['taxes'] = $taxData['taxes'];
			$metaData['tax_percent'] = $taxData['tax_percent'];
	    }
	    $result = $metaData;	       
}else if($req == 'phts' && !empty($details['id'])) {
	$result = Tw_GetVendorPhotos2($details['id'],$details['cat_id']);		
}else if($req == 'fm_crt') {
	$f_ids = json_decode($details['f_ids'], true);
	$result = Tw_FormalizeCart($f_ids, $details['address_id'], $details['user_id']);
}else if($req == 'srh_kys'){
        $details = array(
        	'user_lat' => $details['user_lat'],			
			'user_long' => $details['user_long'],
			'key' => $details['key']
        );	    	
	    $result = Tw_SearchClufterKeys($details);
}else if($req == 'srhv_kys'){
        $details = array(
        	'user_lat' => $details['user_lat'],			
			'user_long' => $details['user_long'],
			'key' => $details['key']
        );	    	
	    $result = Tw_SearchVendorKeys($details);
}else if($req == 'srh_fd'){       
	    $result = Tw_SearchFoods($details);
	    $meal_type = false;	    
}else if($req == 'srh_vn'){
        $params = array(
		'user_long' => $details['user_long'],
		'meal_type' => empty($details['meal_type']) ? 0 : $details['meal_type'],
		'radius' => empty($details['radius']) ? RADIUS : $details['radius'],
		'user_lat' => $details['user_lat'],
		'key' => $details['key'],
		'table' => empty($details['table']) ? 0 : $details['table']
        );	    	
	$result = Tw_SearchVendors($params);
}else if($req == 'cats'){
	$result = Tw_LoadFoodCategories(0, 100);	 
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>