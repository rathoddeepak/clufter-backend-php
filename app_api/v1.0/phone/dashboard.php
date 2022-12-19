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
if(!empty($details['postData'])){    
    $details = json_decode($details['postData'], true);    
}
$req = empty($details['req']) ? 'crtara' : $details['req'];

if($req == 'crtara'){//Create Area
    $result = Tw_CreateVendorArea($details);
}else if($req == 'crttbl'){//Create Area
    $result = Tw_CreateVendorTable($details);
}else if($req == 'del_ara'){
    $result = Tw_DeleteVendorArea($details['id']);
}else if($req == 'ld_ara'){
    $result = Tw_LoadVendorAreas($details['vendor_id']);
}else if($req == 'del_tbl'){
    $result = Tw_DeleteVendorTable($details['id']);
}else if($req == 'ld_tbls'){
    $result = Tw_LoadVendorTables($details['area_id']);
}else if($req == 'v_tbl'){
    $areas = Tw_LoadVendorAreas($details['vendor_id']);
    $tables = [];
    if(count($areas) > 0){
        $tables = Tw_LoadVendorTables($areas[0]->id);
    }
    $bkngs  = Tw_LoadAssignedBookings([
        'timed' => true,
        'vendor_id' => $details['vendor_id']
    ]);
    $result = [
        'tables' => $tables,
        'areas' => $areas,
        'bkngs' => $bkngs
    ];    
    $vendorData = Tw_VendorData($details['vendor_id']);
    $result['hotel'] = $vendorData->name;
    $result['address'] = $vendorData->address;
    $result['rcpxtra'] = $vendorData->rcpxtra;   
    $result['os'] = $vendorData->delivery;
}else if($req == 'capt_in'){
    $foods = Tw_LoadVendorFood($details['vendor_id'], [FD_BOTH, FD_ONLYMNU]);
    $foodList = [];
    foreach($foods as $food){
        $food->quantity = 0;
        $foodList[] = $food;
    }
    if(empty($details['offset'])){
        $areas = Tw_LoadVendorAreas($details['vendor_id']);
    }else{
        $areas = [];
    }
    $tables = [];
    if(count($areas) > 0){
        $tables = Tw_LoadVendorTables($areas[0]->id);
    }
    $result = [
        'tables' => $tables,
        'areas' => $areas,
        'foods' => $foodList,
        'tax' => Tw_GetClufterAppliedTaxes($details['vendor_id'])
    ];
}else if($req == 'uptblsts'){
    if($details['status'] == TBL_PRESENT){
        $visit = Tw_CaptainHotelVisit($details);
        $details['visit_id'] = $visit;
    }
    $result = Tw_UpdateTableStatus($details);
}else if($req == 'dbdt'){//Dispatch Booking Data
    $result = Tw_DispatchBkgData($details['id']);
}else if($req == 'dpbk'){
    $result = Tw_DispatchBooking($details['id']);
}else if($req == 'dcftb'){//Didn't Come For Table Booking
    $result = Tw_DeactiveBooking($details['id']);
}else if($req == 'crtho'){
    $result = Tw_AddFoodToVisit($details);
}else if($req == 'gnrp'){
    $result = Tw_GetTableReceipt($details, false);
}else if($req == 'tbdt'){
    $receipt = Tw_GetTableReceipt($details);
    $cp = false;
    $tableData = Tw_TableData($details['table_id']);
    $billNo = 0;
    $visit_id = 0;
    if($tableData->status != TBL_FREE){
        $visitData = Tw_VisitData($tableData->visit_id);
        $billNo = $visitData->billno;
        if($visitData){
            $bal_amt = $visitData->bal_amt;  
            $discount = $visitData->discount;
            $visit_id = $visitData->id;
        }else{
            $bal_amt = 0;
            $discount = 0;
        }
        if($visitData->cp_id != 0){
            $roleData = Tw_VendorRoleData($visitData->cp_id);
            $cp = $roleData->name;
        }
    }else{
        $discount = 0;
        $bal_amt = 0;        
    }
    $areaData = Tw_AreaData($tableData->area_id);
    $bkngs  = Tw_LoadAssignedBookings([
        'timed' => false,
        'table_id' => $tableData->id,
        'vendor_id' => $areaData->vendor_id
    ]);
    $total = 0;
    foreach($receipt as $rp){        
        $total += $rp->amount;
    }
    $result = [
        'receipt' => $receipt,     
        'total' => $total,
        'bkgs' => $bkngs,
        'bal_amt' => $bal_amt,
        'bn' => $billNo,
        'vst' => $visit_id,
        'status' => $tableData->status
    ];
    if($cp){
        $result['cp'] = $cp;
    }
}else if($req == 'odta'){
    $receipt = Tw_GetTableReceipt($details, false);
    $tdta = Tw_GetClufterAppliedTaxes($details['vendor_id']);
    if(empty($details['food'])){
        $foods = Tw_LoadVendorFood($details['vendor_id'], [FD_BOTH, FD_ONLYMNU]);
        $foodList = [];
        foreach($foods as $food){
            $food->quantity = 0;
            $foodList[] = $food;
        }
    }else{
        $foodList = false;
    }        
    $result = ['receipt' => $receipt,'foods' => $foodList, 'taxes' => $tdta['taxes']];
}else if($req == 'cntblv'){
    $result = Tw_DeleteTableVisit($details);
}else if($req == 'ldbk'){
    $ohr = ['ars' => [], 'tbs' => []];    
    $areas = Tw_LoadVendorAreas($details['vendor_id']);
    foreach($areas as $a){
        $ohr['ars'][] = $a->area;
    }
    foreach($areas as $a){
        $tbs = [];
        $tbls = Tw_LoadVendorTables($a->id, false);
        foreach($tbls as $tbl){
            $tbl = $tbl->id.'-'.$tbl->number;
            $tbs[] = $tbl;
        }
        $ohr['tbs'][] = $tbs;
    }
    $bks = Tw_LoadTBooking($details, 25, 0);
    $result = [
        'ohr' => $ohr,
        'bks' => $bks,
    ];
}else if($req == 'ldbk2'){
    $ohr = ['ars' => [], 'tbs' => []];    
    $areas = Tw_LoadVendorAreas($details['vendor_id']);
    foreach($areas as $a){
        $ohr['ars'][] = $a->area;
    }
    foreach($areas as $a){
        $tbs = [];
        $tbls = Tw_LoadVendorTables($a->id, false);
        foreach($tbls as $tbl){
            $tbl = $tbl->id.'-'.$tbl->number;
            $tbs[] = $tbl;
        }
        $ohr['tbs'][] = $tbs;
    }    
    $result = [
        'ohr' => $ohr        
    ];
}else if($req == 'upbst'){     
    if($details['cancel'] == true){        
        $result = Tw_CancelBooking($details['booking_id'], '', false);
    }else{        
        $result = Tw_AcceptBooking($details);
    }    
}else if($req == 'udts'){        
    $tables = Tw_LoadVendorTables($details['area_id']);
    $bkngs  = Tw_LoadAssignedBookings([
        'timed' => true,
        'vendor_id' => $details['vendor_id']
    ]);
    $vendorData = Tw_VendorData($details['vendor_id']);    
    $orders = Tw_CountPendingOrders($details['vendor_id']);
    $bookings = Tw_CountVendorBookings($details['vendor_id']);
    $result = [
        'tb' => $tables,
        'oC' => $orders,
        'bC' => $bookings,
        'bk' => $bkngs,
        'os' => $vendorData->delivery
    ];
}else if($req == 'udts2'){        
    $result = Tw_LoadVendorTables($details['area_id']);
}else if($req == 'sptbl'){
    $result = Tw_SplitTable($details['table_id']);
}else if($req == 'dlspt'){
    $result = Tw_DeleteVendorTable($details['split_id']);
}else if($req == 'lodrs') {//Get Typed orders
 $result = Tw_GetVendorOrders(
    $details['vendor_id'],
    $details['type']
 );
}else if($req == 'crtka'){//Create Take Away
    $result = Tw_CreateTakeAway($details);
}else if($req == 'lgt'){
    $result = Tw_VendorDashLogout($details['vendor_id']);
}else if($req == 'trft'){
    $result = Tw_VendorTransferTable($details);
}else if($req == 'vsdt'){    
    $visitData = Tw_VisitData($details['id']);    
    $receipt = Tw_GetVisitReceipt($details['id']);
    $tableData = Tw_TableData($visitData->table_id);
    $discount = $visitData->discount;
    if($tableData == false){
        $tableData = new stdClass();
        $tableData->status = TBL_FREE;
    }
    if($visitData->cp_id != 0){
        $roleData = Tw_VendorRoleData($visitData->cp_id);
        $cp = $roleData->name;
    }
    $cp = false;
    $vendorData = Tw_VendorData($visitData->vendor_id);    
    $result = [
        'hotel' => $vendorData->name,
        'address' => $vendorData->address,
        'rcpxtra' => $vendorData->rcpxtra,
        'receipt' => $receipt,
        'xtitle' => $visitData->xtitle,
        'xamt' => $visitData->xamt,
        'total' => $visitData->total_amt,        
        'bal_amt' => $visitData->bal_amt,
        'number' => $visitData->tableno,
        'area' => $visitData->area,
        'pmy' => $visitData->pmy,
        'bn' => $visitData->billno,
        'status' => $tableData->status,
        'discount' => $visitData->discount
    ];
    if($cp)$result['cp'] = $cp;
}else if($req == 'tadt'){    
    $taData = Tw_TkAwayData($details['id']);    
    $receipt = Tw_GetTkAwayReceipt($details['id']);    
    $discount = $taData->discount;    
    if($taData->cp_id != 0){
        $roleData = Tw_VendorRoleData($taData->cp_id);
        $cp = $roleData->name;
    }
    $cp = false;
    $vendorData = Tw_VendorData($taData->vendor_id);    
    $result = [
        'hotel' => $vendorData->name,
        'address' => $vendorData->address,
        'rcpxtra' => $vendorData->rcpxtra,
        'receipt' => $receipt,
        'xtitle' => $taData->xtitle,
        'xamt' => $taData->xamt,
        'total' => $taData->total_amt,        
        'pmy' => $taData->pmy,
        'bn' => $taData->billno,        
        'discount' => $taData->discount
    ];
    if($cp)$result['cp'] = $cp;
}else if($req == 'upmy' || $req == 'utpmy'){
    if(empty($details['pmy']) || empty($details['id'])){
        $result = "~Error";
    }else{
        $result = $db
        ->where('id', $details['id'])
        ->update($req == 'upmy' ? VISITS : TKAWAY, [
            'pmy' => $details['pmy']
        ]);
        $result = $result ? 'Updated!' : '~Error';
    }
}else if($req == 'vta'){
    $result = Tw_VoidTakeAway($details);
}else if($req == 'vodr'){
    $result = Tw_VoidHotelOrder($details);
}else{
    Tw_HeaderExit(400, "Bad Request");
}


if(is_string($result) && substr($result,  0, 1) == '~')
     Tw_HeaderExit(400, substr($result,  1));
else Tw_HeaderExit(200, $result);
?>