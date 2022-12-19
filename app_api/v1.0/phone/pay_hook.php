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
$data = $_POST;
unset($data["signature"]); // $data now has all the POST parameters except signature
ksort($data); // Sort the $data array based on keys
$postData = "";
foreach ($data as $key => $value){
    if (strlen($value) > 0) {
      $postData .= $value;
    }
}
$hash_hmac = hash_hmac('sha256', $postData, CC_SECRET, true);
$computedSignature = base64_encode($hash_hmac);
$stsList = ['SUCCESS', 'FLAGGED', 'PENDING', 'FAILED', 'CANCELLED'];
if ($signature == $computedSignature) {
    $order_id = (int) filter_var($data['ORDERID'], FILTER_SANITIZE_NUMBER_INT);
    $txStatus = $_POST["txStatus"];
    $intStat =  array_search($txStatus,$stsList);
    if($intStat == CC_SUCCESS){
        $db->where('id', $order_id)->update(PAYMENTS, ['status' => CC_SUCCESS]);
        $db->where('txn_id', $order_id)->update(TABLE_BOOKING, [
            'pay_stat' => CC_SUCCESS,
            'active' => ACTIVE
        ]);
    }else if(in_array($intStat, [CC_FAILED,CC_CANCELLED,CC_VALIDATION_ERROR])){
        $db->where('id', $order_id)->update(PAYMENTS, ['status' => PAY_FAIL]);
        $db->where('txn_id', $order_id)->update(TABLE_BOOKING, [
            'pay_stat' => $intStat,
            'active' => INACTIVE
        ]);
    }
}
?>