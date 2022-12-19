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
if($req == 'crt_adn_grp'){//Create Addon Group
  $e = '~Unable To Create Addon Group';
  if(empty($post['name'])){
  	Tw_HeaderExit(400, $e);
  }else if(empty($post['food_id'])){
  	Tw_HeaderExit(400, $e);
  }else if(empty($post['data'])){
  	$adnList = [];
  }else{
  	$adnList = json_decode($post['data'], true);
  }
  $name = $post['name'];
  $food_id = $post['food_id'];
  $required = empty($post['required']) ? 0 : 1;  
  $gid = $db->insert(ADN_GROUP, [
  	'name' => $name,
  	'food_id' => $food_id,
  	'status' => 0,
  	'req' => $required
  ]);
  $db->where('id', $food_id)->update(FOOD, ['addon' => 1]);
  if($gid){
  	foreach ($adnList as $adn) {
  		$db->insert(ADN_LIST, [
  			'gid' => $gid,
  			'name' => $adn['name'],
  			'cost' => $adn['cost'],
  			'status' => 1
  		]);
  	}
  	Tw_HeaderExit(200, $gid);
  }else{
  	Tw_HeaderExit(400, $e);
  }
}else if($req == 'add_adn_opt'){//Create Addon Group
  $e = '~Unable To Add Option';
  if(empty($post['name'])){
  	Tw_HeaderExit(400, $e);
  }else if(empty($post['gid']) && empty($post['id'])){
  	Tw_HeaderExit(400, $e);
  }
  $name = $post['name'];
  $cost = $post['cost'];
  $gid = empty($post['gid']) ? 0 : $post['gid'];
  $id = empty($post['id']) ? 0 : $post['id'];
  if($id == 0){
  	$id = $db->insert(ADN_LIST, [
			'gid' => $gid,
			'name' => $name,
			'cost' => $cost,
			'status' => 1
	]);
  }else{
  	$db->where('id', $id)->update(ADN_LIST, [			
  		'name' => $name,
  		'cost' => $cost			
  	]);
  }
  Tw_HeaderExit(200, $id);
}else if($req == 'ld_fd_group'){//Load Vendor Addon Group
  if(empty($post['food_id'])){
  	Tw_HeaderExit(400, $e);
  }
  $food_id = $post['food_id'];
  $data = $db->where('food_id', $food_id)->get(ADN_GROUP);
  $final = [];
  foreach ($data as $group) {
  	$data = $db->where('gid', $group->id)->get(ADN_LIST);
  	$group->data = $data;
  	$final[] = $group;
  }
  Tw_HeaderExit(200, $final);
}else if($req == 'edit_adn_group'){//Create Addon Group
  $e = '~Unable To Create Update Group';
  if(empty($post['gid'])){
  	Tw_HeaderExit(400, $e);
  }else if(empty($post['name'])){
  	Tw_HeaderExit(400, $e);
  }   
  $name = $post['name'];
  $gid = $post['gid'];
  $required = empty($post['required']) ? 0 : 1;
  $db->where('id', $gid)->update(ADN_GROUP, [
  	'name' => $name,  	
  	'req' => $required
  ]);
  Tw_HeaderExit(200, "Updated Successfully!");  
}

else if($req == 'up_adgp_status'){
  if(empty($post['gid'])){
  	Tw_HeaderExit(400, $e);
  }
  $status = empty($post['status']) ? 0 : 1;
  $db->where('id', $post['gid'])->update(ADN_GROUP, [  	
  	'status' => $status
  ]);
  Tw_HeaderExit(200, 'Updated');
}else if($req == 'up_adn_status'){
  if(empty($post['gid'])){
  	Tw_HeaderExit(400, $e);
  }
  $status = empty($post['status']) ? 0 : 1;
  $db->where('id', $post['gid'])->update(ADN_LIST, [  	
  	'status' => $status
  ]);
}

else if($req == 'up_adn_req'){
  if(empty($post['gid'])){
  	Tw_HeaderExit(400, $e);
  }
  $status = empty($post['required']) ? 0 : 1;
  $db->where('id', $post['gid'])->update(ADN_GROUP, [  	
  	'req' => $status
  ]);
  Tw_HeaderExit(200, 'Updated');
}else if($req == 'dlt_fdad_group'){
  if(empty($post['gid'])){
  	Tw_HeaderExit(400, $e);
  }
  $gid = $post['gid'];
  $db->where('id', $gid)->delete(ADN_GROUP);
  $db->where('gid', $gid)->delete(ADN_LIST);
  Tw_HeaderExit(200, "Deleted Successfully!");
}else if($req == 'dlt_opt_adn'){
  if(empty($post['id'])){
  	Tw_HeaderExit(400, $e);
  }
  $id = $post['id'];
  $db->where('id', $id)->delete(ADN_LIST);
  Tw_HeaderExit(200, "Deleted Successfully!");
}

else if($req == 'my_adn_group'){
  $e = '~Unable To Create Addon Group';
  if(empty($post['name'])){
  	Tw_HeaderExit(400, $e);
  }else if(empty($post['vendor_id'])){
  	Tw_HeaderExit(400, $e);
  }   
  $name = $post['name'];
  $vendor_id = $post['vendor_id'];
  $required = empty($post['required']) ? 0 : 1;  
  $gid = $db->insert(VDR_ADN, [
  	'name' => $name,
  	'vendor_id' => $vendor_id,  	
  	'req' => $required  	
  ]);
  if($gid){
  	Tw_HeaderExit(200, ['gid' => $gid]);
  }else{
  	Tw_HeaderExit(400, $e);
  }
}else if($req == 'edit_my_group'){//Create Addon Group
  $e = '~Unable To Create Update Group';
  if(empty($post['vgid'])){
  	Tw_HeaderExit(400, $e);
  }else if(empty($post['name'])){
  	Tw_HeaderExit(400, $e);
  }else if(empty($post['data'])){
  	Tw_HeaderExit(400, $e);
  }    
  $name = $post['name'];
  $vgid = $post['vgid'];
  $required = empty($post['required']) ? 0 : 1;
  $status = empty($post['status']) ? 0 : 1;
  $data = $post['data'];
  $db->where('id', $vgid)->update(VDR_ADN, [
  	'name' => $name,
  	'data' => $data,  	
  	'req' => $required
  ]);
  Tw_HeaderExit(200, "Updated Successfully!");
}else if($req == 'dlt_adn_group'){//Create Addon Group
  if(empty($post['vgid'])){
  	Tw_HeaderExit(400, $e);
  }
  $vgid = $post['vgid'];
  $db->where('id', $vgid)->delete(VDR_ADN);
  Tw_HeaderExit(200, "Deleted Successfully!");
}else if($req == 'ld_vadn_group'){//Load Vendor Addon Group
  if(empty($post['vendor_id'])){
  	Tw_HeaderExit(400, $e);
  }
  $vendor_id = $post['vendor_id'];
  $data = $db->where('vendor_id', $vendor_id)->get(VDR_ADN);
  Tw_HeaderExit(200, $data);
}else{
	$result = "Bad Request";
}


?>