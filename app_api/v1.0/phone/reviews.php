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
$req = empty($details['req']) ? 'get_reviews' : $details['req'];
if($req == 'get_reviews') {
	$result = Tw_GetReviews($details['issuer'], $details['id'], $details['limit'], $details['offset']);
}else if($req == 'get_rev2') {
    if($details['offset'] == 0){
        $values = Tw_GetReviewValues(
            $details['issuer'],
            $details['issuer_id']
        );
        $reviews = Tw_GetReviews(
            $details['issuer'], 
            $details['issuer_id'],
            $details['limit'],
            0
        );
        $result = ['reviews' => $reviews, 'cal' => $values];
    }else{
        $result = Tw_GetReviews($details['issuer'], $details['issuer_id'], $details['limit'], $details['offset']);
    }    
}else if($req == 'my_reviews') {
    $result = Tw_GetMyReviews($details['issuer'], $details['user_id'], $details['limit'], $details['offset']);
}else if($req == 'add_review'){
    $is = $details['issuer'];    
    if($is == 1){
        $data = Tw_AddVendorReview($details);
    }else if($is == 2){
        $data = Tw_AddBookingReview($details);
    } else if($is == 3){
        $data = Tw_AddFoodReview($details);
    }
    $meta = Tw_GetReviewValues($details['issuer'],$details['issuer_id']);
    $result = [
        'data' => $data,
        'meta' => $meta
    ];
}else if($req == 'edit_review'){
    $is = $details['issuer'];
    if($is == 1){
        $result = Tw_EditVendorReview($details);
    }else if($is == 2){
        $result = Tw_EditBookingReview($details);
    } else if($is == 3){
        $result = Tw_EditFoodReview($details);
    }    
}else if($req == 'del_review'){
    $is = $details['issuer'];    
    if($is == 1){
        $data = Tw_DeleteVendorReview($details['id']);
    }else if($is == 2){
        $data = Tw_DeleteBookingReview($details['id']);
    } else if($is == 3){
        $data = Tw_DeleteFoodReview($details['id']);
    }
    $meta = Tw_GetReviewValues($details['issuer'],$details['vendor_id']);
    $result = [
        'data' => $data,
        'meta' => $meta
    ];
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>