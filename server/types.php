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
    | Founder - Vitthal Kendre                                  |
    |-----------------------------------------------------------|
    | FoodBazzar,Mordern Food Ordering and Table Booking System |
    | FoodBazzar 2020 Copyright all rights reserved.            |
    |-----------------------------------------------------------|     
->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

#User Types
define('ACTIVE_USER', 1);
define('INACTIVE_USER', 2);
define('VENDOR_LOCAL', 3);
define('VENDOR_MEDIUM', 4);
define('VENDOR_LARGE', 5);
define('DELIVERY_HERO', 5);
define('ADMIN', 6);
#ORDERE BY
define('NUMBER', 1);
define('RATING', 2);
#VEG NONVEG
define('BOTH_FOOD', 0);
define('VEG_FOOD', 1);
define('NONVEG_FOOD', 2);
#GALLERY TYPES
define('EXTERIOR', 1);
define('INTERIOR', 2);
define('KITCHEN', 3);
define('DESK', 4);
define('TABLES', 5);
define('STAFF', 6);
#FOOD RELATED
define('FOOD_NOTAPPROVED', 0);
define('FOOD_APPROVED', 1);
define('ALL_FOOD', 2);
#Constants
define('BOTH', 2);
define('DELIVERY_FEE', 19);
define('ORDER_POINT', 10);
define('HAS_COD', 1);
define('YESATTENT', 1);
define('NOTATTENT', 2);
define('MPV', 5); //Money Per Visit
define('SMS_HASH_ANDROID', 'G9kShlHjGk9');
define('VENDOR_HASH_ANDROID', 'grf9ExuQC3V');
define('NOTIFY_APP', 0);
define('NOTIFY_WEB', 1);
define('NOTIFY_BOTH', 2);
define('CC_APPID', '44822aa6cc64393a1f9f266d322844');
define('CC_SECRET', 'ed5d0a72a8b5d74802d0cec09eb052a4b99b3770');
define('TOOKAN_API_URL', 'https://api.tookanapp.com/v2/');
define('TOOKAN_API_KEY', '53636087f243094745196e3041116e471ae6c7fa2fd9723c5a1b01c3');
define('SECRET', 'RJUAocjVomQAYCTd');
define('SHW_BK_TIME', 20);//Before 20 Mins
define('WAIT_TIME', 12);//Wait for booking 12 Mins
define('CANCEL_TIME', 20);//Wait for booking 12 Mins
define('FMC_KEY', 'AAAAh5fmujE:APA91bHENqrjITcf3YsvPxhm1t_JflSPo2unmeZBH5bTHk23dUFKvKMksOoqFrTl5z2_4jl8Yyf_OQrwcpeeMYPoi8CGZ9c_vUXhh_TOBiTuVDcEbxeAwyJBjk9bPxttbotrKvm7_Jjm');
//One Signal
define('OS_CORE_KEY','3310ed41-9bc6-416f-8a84-8c4661dbb5fb');
define('OS_CORE_ATH','NDk4ODM4MmQtNzE3OC00MDAyLWJiMjEtMWE5OWM2NTJmODIw');
define('OS_CORE_CHID', 'eb190b19-ce2e-493d-b978-6e6107251dde');

define('OS_VENDOR_KEY', '5edb5c9d-108a-477f-a382-3b6a527b62d9');
define('OS_VENDOR_ATH','YTBlODI1NTMtNzAwNC00NDEzLWIyM2UtMTE4YjYzYTMyMDFk');
//define('OS_VENDOR_CHID', '5aecda3e-e0db-4006-b768-28cfe04dcf6c');
define('OS_VENDOR_CHID', '1001');

define('OS_DLV_KEY', '1427e7ac-aa4a-4c0f-8d67-2a95c677c3b4');
define('OS_DLV_ATH','MTFjNTIzMzctMDAxMS00ZjNkLTlhYTItNjQwZTc5ZDQ0NGE2');
define('OS_DLV_CHID', '8fa7e40d-7683-4a0c-9a7e-fb1f63c88977');

define('RPKY', 'rzp_live_FgrxlZ7JOf9oUu');
define('RPSC', 'MrWgLFnpxGYWYYnOlMwtsS96');


//Section Types
define('OUR_SPECIAL', 0);
define('MUST_TRY', 1);

//define('RPKY', 'rzp_test_UHQ3AYpUL3P5sM');
//define('RPSC', 'mwgFkHTbqlWgrDAA5tBOS2aF');


define('PER_VENDOR_TIME', 25);
define('TB_AMOUNT', 20);//Table Booking Charges
define('FD', 0);
define('PER_ORDER_REWARD', 0);
define('APP', 0);
define('VENDOR', 1);
define('CAT', 2);
define('USER', 3);
define('DLH', 4);
#Notification Type
define('CLNT', 0);//Clufter Sends Notification to customers
define('VNNT', 1);//Vendor Sends Notification to their customers
define('USORV', 2);//User Sends Notification to Vendor of Food Orders
define('VNUSOS', 3);//Vendor Notifues User of Order Status
define('USORD', 4);//U Notifies DH About Order & Centerer if Multi Order
define('DHNUS', 5);//DH Notifies User of Starting Order Can CALL
define('DLPC', 6);//DH Notifies User of Order Picking Can CALL
define('FDDL', 7);//Food When Delivered Ask For Rating
define('BKTBL', 8);//User Sends Notification of TBK
define('CNTBL', 9);//User/Vendor Sends Notification of Cancel TBK
define('RFPS', 10);//App Refund in process notify
define('RFDN', 11);//Refund Successfully Done
define('CVHO', 12);//User Send Hotel Vendor Notification
define('VACHO', 13);//Hotel Vendor Cooking Confirm
define('ATRW', 14);//Auto Review Notification After Scan
define('ASTBL', 15);//Vendor Sends Notify of Accepting Table
define('RFSCH', 16);//Refund Status Changed
#Payment methods
define('COD', 0);
define('ONLINE_PAY', 1);
#KISOK
define('KISOK', '&42jc4$');
define('USERS', 'users');
define('RADIUS', 15);

define('INACTIVE', 0);//Time Gone
define('ACTIVE', 1);//Checking
define('ASSIGNED', 2);//Vendor Assigned
define('CANCELLED', 3);//User Cancelled
define('VDRCANCEL', 4);//Vendor Cancelled
define('PAYCANCEL', 5);//Vendor Cancelled
#Table Status Values
define('TBL_FREE',0);
define('TBL_PRESENT',1);
define('TBL_UNPAID',2);
define('TBL_PAID',3);
#Delivery And Order Constants
define('FOOD_NOT_PREPARED', 0);
define('FOOD_ACCEPT', 1);
define('FOOD_PREPARED', 2);
define('DELIVERY_FN_PREPARED', 3);
define('DELIVERY_F_PREPARED', 4);
define('HAS_PICKED_F', 5);
define('HAS_PICKED_C', 6);
define('HAS_CENTERED', 7);
define('HAS_DELIVERED', 8);
define('VDRFDCANCEL', 9);
define('USRFDCANCEL', 10);
define('PER_KM_TIME', 3);

define('MAX_NOTIFY_COUNT', 3);
#Tables
define('V_FAMILY', 'vendor_family');
define('V_GALLERY', 'vendor_gallery');
define('V_AREA', 'vendor_areas');
define('V_TABLE', 'vendor_tables');
define('SLOTS', 'slots');
define('HTL_SLOTS', 'hotel_slots');
define('ADDRESS', 'address');
define('FOOD_CAT', 'food_cats');
define('HERO', 'heroes');
define('FOOD', 'food');
define('CFOOD', 'cfood');
define('PAYMENTS', 'payments');
define('PLT_MODES', 'plate_modes');
define('REFUNDS', 'refunds');
define('PLATES', 'plates');
define('ORDERS', 'orders');
define('CMPLN', 'complains');
define('CLAIMS', 'claims');
define('ORDER_VENDOR', 'order_vendor');
define('ORDER_FOOD', 'order_food');
define('REFERS', 'referral');
define('TABLE_BOOKING', 'table_booking');
define('BKG_FOOD', 'bkg_food');
define('GIFTS', 'gifts');
define('VISITS', 'visits');
define('NOTIFY', 'notifications');
define('ODR_REVIEW', 'order_reviews');
define('VISIT_FOOD', 'visit_food');
define('V_REVIEWS', 'vendor_reviews');
define('B_REVIEWS', 'booking_reviews');
define('F_REVIEWS', 'food_reviews');
define('ROADS', 'roads');
define('CENTERS', 'centers');
define('JOIN_REQ', 'join_req');
define('HPINS', 'hpins');
define('V_TAXES', 'vendor_taxes');
define('TAX_SUB', 'tax_sub');
define('TAXES', 'taxes');
define('TAX_APPLIED', 'tax_applied');
define('ROLES', 'roles');
define('TKAWAY', 'tkaway');
define('TA_FOODS', 'ta_foods');
define('PDODR', 'pndodrs');
define('ADN_GROUP', 'adngroup');
define('ADN_LIST', 'adnlist');
define('VDR_ADN', 'vdradn');
define('CAT_SEQ', 'catseq');
define('XTRA_FD', 'extrafood');

#Issuers
define('PAY_FOOD', 1);
define('PAY_TABLE', 2);
#Payments
define('PAY_PENDING', 0);
define('PAY_AUTH', 1);
define('PAY_CAPT', 2);
define('PAY_FAIL', 3);
define('CC_SUCCESS', 0);
define('CC_FAILED', 1);
define('CC_PENDING', 2);
define('CC_CANCELLED', 3);
define('CC_FLAGGED', 4);
define('CC_VALIDATION_ERROR', 5);

define('PAY_CASH', 1);
define('PAY_ONLINE', 2);
define('PAY_CARD', 3);
define('PAY_BOTH', 4);
#Refunds Constants
define('UNPROCESSED', 0);
define('PAID', 1);
define('NOT_PAID', 2);

define('REFUND_CREATED', 0);
define('REFUND_PROCESSED', 1);
define('REFUND_FAILED', 2);
define('REFUND_SPDCHG', 3);


#food type
define('FD_BOTH', 0);
define('FD_ONLYMNU', 1);
define('FD_ONLYDLV', 2);
define('FD_NONE', 3);

#ROLES 
define('RL_CAPTAIN', 0);
define('RL_CHEF', 1);

#FIRE COST
define('BASE_DISTANCE', 4.0);
define('BASE_FAIRE_COST', 19);
define('EXTRA_KM', 1);
define('EXTRA_KM_COST', 10);

?>