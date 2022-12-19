<?php
 require_once('server/main.php');
 $pass = empty($_GET['pass']) ? null : $_GET['pass'];
 $id = empty($_GET['id']) ? null : $_GET['id'];
 global $db;
 if($pass == null){
 	echo "InValid Access";
 	return;
 }else if($pass != "082122"){
 	echo "InValid Access";
 	return;
 }
 $vendorData = Tw_VendorData($id);
 if($vendorData){ 	
 	$area_ids = [];
 	$tableIds = [];
 	$visitIds = [];
 	$tkawayIds = [];
 	$areas = Tw_LoadVendorAreas($id);
 	if(count($areas) == 0){
 		echo "Successfully Cleared";
 		return;
 	}
 	//Clearing Tables
 	foreach ($areas as $area) {
 		$area_ids[] = $area->id;
 	}
 	foreach ($area_ids as $id) {
 		$tables = Tw_LoadVendorTables($id);
 		foreach ($tables as $table) {
 			if($table->sp_id != 0 || $table->tkaway != 0){
 				$db->where('id', $table->id)->delete(V_TABLE);
 			}else{ 				
 				$tableIds[] = $table->id;
 			} 			
 		}
 	}
 	if(count($tableIds) != 0){ 		
	 	$db->where('id', $tableIds, 'IN')->update(V_TABLE, ['status' => 0,'visit_id' => 0,'time' => 0,'updated' => 0,'sp_id' => 0,'sp_idx' => 0,'sp_num' => 0,'tkaway' => 0
	 	]); 	
	 	//Deleting Visits
	 	$visits = $db->where('area_id', $area_ids, 'IN')->get(VISITS);
	 	if(count($visits) > 0){
	 		foreach ($visits as $visit) {
		 		$visitIds[] = $visit->id;
		 	}
		 	$db->where('area_id', $area_ids, 'IN')->delete(VISITS);
		 	$db->where('visit_id', $visitIds, 'IN')->delete(VISIT_FOOD);
	 	}	 	
	}

 	//Deleting TkAway
 	$tkaways = $db->where('vendor_id', $id)->get(TKAWAY); 	
 	if(count($tkaways) > 0){
 		foreach ($tkaways as $tkaway) {
	 		$tkawayIds[] = $tkaway->id;
	 	}
	 	$db->where('id', $tkawayIds, 'IN')->delete(TKAWAY);
	 	$db->where('ta_id', $tkawayIds, 'IN')->delete(TA_FOODS);
 	}
 	echo 'Cleared Successfully';
 }else{
 	echo 'Vendor Not Found!';
 } 
?>