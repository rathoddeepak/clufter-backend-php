<?php
/*->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>   
    |-----------------------------------------------------------|
    |@author        Deepak Balasaheb Rathod                     |
    |@author_url    https://twohearts.dp/about/                 |
    |@author_mail   rathoddeepak143dp@gmail.com                 |
    |@site_url      https://twohearts.dp/home                   |
    |@site_mail     twohearts@social.com                        |
    |-----------------------------------------------------------|
    | TwoHearts, Private Space For Only Two Hearts              |
    | TwoHearts 2020 Copyright all rights reserved.             |
    |-----------------------------------------------------------| 
->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

if (empty($_GET['type']) || !isset($_GET['type']))
    Tw_HeaderExit(400, "Bad Request!");
$request = 'city';
if($request == 'city'){
 if(empty($_POST['sid']))Tw_HeaderExit(400, "not found");
 $cities = $db->where('state', $_POST['sid'])->get('city'); 
 $final = [];
 $final[] = array('id' => 0, 'name' => 'Select City');
 foreach ($cities as $city)$final[] = $city;
 Tw_HeaderExit(200, $final);
}

?>