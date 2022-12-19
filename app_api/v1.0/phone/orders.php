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
$req = empty($post['req']) ? 'gtpnd' : $post['req'];
if($req == 'gtpnd') {//get pending orders
	$result = Tw_GetVendorOrders($post['vendor_id'], 1, empty($post['time']) ? 0 : $post['time']);
}else if($req == 'gtcpd') {//get Completed orders
    $result = Tw_GetVendorOrders($post['vendor_id'], 2, empty($post['time']) ? 0 : $post['time']);
}else if($req == 'gtcnl') {//get Completed orders
    $result = Tw_GetVendorOrders($post['vendor_id'], 3, empty($post['time']) ? 0 : $post['time']);
}else if($req == 'vndr_sts') {//Update Vendor Status
    $result = Tw_UpdateVendorOrderStatus($post);
}else if($req == 'gtdlo'){//Get Delivery Orders    
    $result = Tw_GetOrdersForDelivery($post['user_id']);
}else if($req == 'gtcdlo'){//Get Center Delivery Orders   
    $result = Tw_GetOrdersForCenterer($post['user_id']);
}else if($req == 'dlh_sts'){//Start Delivery
    $result = Tw_StartDelivery(
        $post['vo_id'],
        $post['user_id']        
    );
}else if($req == 'dlc_sts'){//Start Delivery For Center
    $result = Tw_StartCenterDelivery(
        $post['order_id'],
        $post['user_id']        
    );
}else if($req == 'pck_sts'){//Pick Up
    $result = Tw_PickDelivery(
        $post['vo_id'],
        $post['user_id'],
        $post['full_dispatch'] ? HAS_PICKED_F : HAS_PICKED_C
    );
}else if($req == 'up_p'){//Update Progress
    $result = Tw_UpdateOrderProgress($post['vo_id'], $post['progress']);
}else if($req == 'dl_sts'){//Bring food to center or deliver
    $result = Tw_FinalDelivery(
        $post['vo_id'],
        $post['user_id'],
        $post['hasDelivered'] ? HAS_DELIVERED : HAS_CENTERED,
        empty($post['order_id']) ? 0 : $post['order_id']
    );
}else if($req == 'trk_ord'){//Tracking
    $result = Tw_RetriveDeliveryStatus($post['order_id']);
}else if($req == 'plt_fds') {
	$result = Tw_PagPlateById($post);
}else if($req == 'fmlz_plt') {	
	$result = Tw_FormalizePlateMode($post);
}else if($req == 'ld_his'){
    $user_id = $post['user_id'];
    $limit = empty($details['limit']) ? 10 : $details['limit'];
    $offset = empty($details['offset']) ? 0 : $details['offset'];   
    $result = Tw_UserFoodOrderUpdates($user_id, $limit, $offset, true);
}
if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);
?>