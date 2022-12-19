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
$req = empty($details['req']) ? 'sort_cat' : $details['req'];
if($req == 'sort_cat') {
	try {
		$seq = $details['seq'];
		$vid = $details['vid'];
		$data = $db->where('vid', $vid)->getOne(CAT_SEQ);
		if($data){
			$db->where('vid', $vid)->update(CAT_SEQ, ['data' => $seq]);
		}else{
			$data = $db->insert(CAT_SEQ, ['vid' => $vid,'data' => $seq]);
		}
	}catch(Exception $e){
		$db->where('vid', $details['vid'])->delete(CAT_SEQ);
	}
	$result = 'updated';
}else{
    $result = "~Bad Request";
}

if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);

?>
