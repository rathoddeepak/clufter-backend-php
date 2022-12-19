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
$req = empty($details['req']) ? 'qrvst' : $details['req'];
if($req == 'qrvst'){
    $table = Tw_TableFromAreaNumber(
        $details['area_id'],
        $details['number']
    );
    if($table == false){
        $result = '~Table Not Found';
    }else if($table->status == TBL_FREE){
        $visit = Tw_CaptainHotelVisit([
            'user_id' => $details['user_id'],
            'table_id' => $table->id
        ], true);
        Tw_UpdateTableStatus(['table_id' => $table->id,'status' => TBL_PRESENT,'visit_id' => $visit]);
        $result = $visit ? ['visit_id' => $visit] : '~Please Try Again';
    } else {
        $result = ['table_id' => $table->id];
    }
}else if($req == 'splvst') {	
	$table = Tw_SplitTable($details['table_id']);
    $visit = Tw_CaptainHotelVisit([
        'user_id' => $details['user_id'],
        'table_id' => $table['id']
    ], true);
    Tw_UpdateTableStatus(['table_id' => $table['id'],'status' => TBL_PRESENT, 'visit_id' => $visit]);
    $result = $visit ? $visit : '~Please Try Again';
}else if($req == 'adftv') {	
	$result = Tw_AddFoodToVisit($details);
}else if($req == 'gevst'){
	$result = Tw_GetVisits($details);
} else if($req == 'chvt'){
    $result = Tw_ValidateVisitEnd($details['visit_id']);
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>