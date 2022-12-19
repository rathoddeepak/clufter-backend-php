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
$req = empty($post['req']) ? 'br' : $post['req'];
if($req == 'tg_fd_sc'){//Toggle Food of Section
  $food_id = $post['food_id'];
  $section = $post['section'];
  $vendor_id = $post['vendor_id'];
  Tw_TglFoodToSection($food_id, $section, $vendor_id);
  Tw_HeaderExit(200, 'Updated');
}else if($req == 'gt_xtra_sc'){//Get Extra Section Data  
  $section = $post['section'];
  $vendor_id = $post['vendor_id'];
  $data = Tw_TglFoodToSection($section, $vendor_id);
  Tw_HeaderExit(200, $data);
}else{
	$result = "Bad Request";
}


?>