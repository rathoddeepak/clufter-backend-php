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
use Razorpay\Api\Api;

function callback($buffer)
{  
  $fh = fopen("razor_log.txt", "a");
  fwrite($fh, $buffer);
  fclose($fh);
  return $buffer;
}
//ob_start("callback");

$validPayHooks = ['payment.authorized', 'payment.captured', 'payment.failed'];
$validRefundHooks = ['refund.created', 'refund.processed', 'refund.failed', 'refund.speed_changed'];
$phpData = Tw_GetWebHookData();
$api = new Api(RPKY, RPSC);
try{
	$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'];	
	//$verified = $api->utility->verifyWebhookSignature($phpData, $signature);	
	$phpData = json_decode($phpData, true);
	$event = $phpData['event'];
	if(in_array($event, $validPayHooks)){
		$entity = $phpData['payload']['payment']['entity'];    
		$order_id = $entity['order_id'];
		$payment_id = $entity['id'];
		$status = Tw_PaymentStatus($entity['status']);
		$paid = $status == PAY_AUTH || $status == PAY_CAPT;
		$orderData = Tw_RazorOrderData($order_id);
		if($orderData){
				$db->where('order_id', $order_id)->update(PAYMENTS, [
					'payment_id'=> $payment_id,
					'status' => $status,
					'updated' => $phpData['created_at']	        
				]);
				if($orderData->issuer == PAY_TABLE){
					$dbodr = $db
					->where('txn_id', $orderData->id)
					->getOne(TABLE_BOOKING);
					$db
					->where('txn_id', $orderData->id)
					->update(TABLE_BOOKING, ['paid' => $paid]);
					if($dbodr->paid == true || $dbodr->paid == 1){
						return true;
					}
					$bookingData = Tw_BookingData($orderData->data_id);
					if($paid){
							$title = "You Have A Table Booking";
							$content = "Press Here To View Details";
							$notification = [
								'sender_type' => USER,
								'sender_id' => $bookingData->user_id,
								'recipient_id' => $bookingData->vendor_id,
								'recipient_type' => VENDOR,
								'notify_type' => BKTBL,
								'title' => $title,
								'content' => $content,    
								'content_id' => $orderData->data_id,      
								'notifyData' => [
								'title' => $title,
								'content' => $content,
								'vendor_ids' => [$bookingData->vendor_id],
								'data' => [
									'type' => BKTBL,
									'type_data' => $orderData->data_id
								]
								]
							];
							Tw_RegisterNotification($notification);				
							Tw_RegisterWebPush([
								'title' => $title,
								'content' => $content,
								'vendor_ids' => [$bookingData->vendor_id]
							]);
						}else{
							//$db->where('id', $bookingData->id)->delete(TABLE_BOOKING);
							//$db->where('bkg_id', $bookingData->id)->delete(BKG_FOOD);
							//$db->where('bkg_id', $bookingData->id)->delete(TAX_APPLIED);
						}   	
				}else if($orderData->issuer == PAY_FOOD){			
					$dbodr = $db
					->where('txn_id', $orderData->id)
					->getOne(ORDERS);
					$db
					->where('txn_id', $orderData->id)
					->update(ORDERS, ['paid' => $paid]);
					$db
					->where('order_id', $orderData->data_id)
					->update(ORDER_VENDOR, ['paid' => $paid]);
					if($dbodr->paid == true || $dbodr->paid == 1){
						return true;				
					}
					$vs = [];
					$cnt_id = 0;	    	
					$odr_id = $orderData->data_id;
					$vdata = Tw_GetVendorsOfOrder($odr_id);
					if($paid){
						foreach($vdata as $v){						
							$vs[] = ['vendor_id' => $v->id];
						}	    		
						Tw_ProcessOrderNotify($vs, $dbodr->user_id);
					}else{
						//$db->where('id', $odr_id)->delete(ORDERS);
						//$db->where('order_id', $odr_id)->delete(ORDER_VENDOR);
						//$db->where('order_id', $odr_id)->delete(ORDER_FOOD);
					}
				}
			}
		}else if(in_array($event, $validRefundHooks)){			
			$entity = $phpData['payload']['refund']['entity'];			
			$refund_id = $entity['notes']['refund_id'];
			if($event == $validRefundHooks[0]){//Created
				Tw_UpdateRefundStatus($refund_id, REFUND_CREATED);
			}else if($event == $validRefundHooks[1]){//Created
				Tw_UpdateRefundStatus($refund_id, REFUND_PROCESSED);
			}else if($event == $validRefundHooks[2]){//Created
				Tw_UpdateRefundStatus($refund_id, REFUND_FAILED);
			}else if($event == $validRefundHooks[3]){//Created
				Tw_UpdateRefundStatus($refund_id, REFUND_SPDCHG);
			}
		}
	
}catch(Exception $e) {
	echo 'RazorPay Webhook validation Error: '.$e->getMessage();
	die;
}

//ob_end_flush();
?>