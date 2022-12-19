<?php 

require_once('../server/main.php');

define('OFFLINE', 0);
define('ONLINE', 1);
define('STARTED', 2);
define('DELIVERING', 3);

$pendingOrders = $db->get(PDODR);
$heroes = $db->where('status', ONLINE)->get(HERO);
if(empty($heroes)){
	$heroes = $db->where('status', DELIVERING)->get(HERO);
}

if(empty($heroes)){
	$heroes = $db->where('status', STARTED)->get(HERO);
}

if(count($pendingOrders) > count($heroes)){
	$moreNeeded = (count($pendingOrders) - count($heroes)) + 1;
	$heroes[] = $db->where('status', DELIVERING)->get(HERO);	
}

if(count($pendingOrders) > count($heroes)){
	$moreNeeded = (count($pendingOrders) - count($heroes)) + 1;
	$heroes[] = $db->where('status', STARTED)->get(HERO);	
}

foreach ($pendingOrders as $order) {	
	$distance = [];
	$canHeroes = [];
	$already = explode(',', $order->heroes);
	if(count($already) > 0 && $already[0] == ''){
		array_splice($already, 0, 1);
	}	
	foreach ($heroes as $hero) {
		if(!in_array($hero->user_id, $already)){
			$canHeroes[] = $hero;
		}
	}
	if(empty($canHeroes)){
		$canHeroes = $heroes;
	}
	foreach ($canHeroes as $hero) {
		$distance[] = round(Tw_CalDistance($order->vlat, $order->vlng, $hero->lat, $hero->long), 4);
	}	
	$minimum = 1000;
	foreach ($distance as $key => $dis) {
		if($distance < $minimum){
			$minimum = $distance;
		}
	}
	if(empty($canHeroes)){		
		return;
	}
	$index = array_search($minimum, $distance);	
	$selectedHero = $canHeroes[$index];
	foreach ($heroes as $key => $hero) {
		if($hero->user_id == $selectedHero->user_id){
			array_splice($heroes, $key, 1);			
			break;
		}
	}

	$title = "You Have New Order";
    $content = "Press Here To Accept";
    $notification = [
	    'sender_type' => APP,
	    'sender_id' => 0,
	    'recipient_id' => [$selectedHero->user_id],
	    'recipient_type' => DLH,
	    'notify_type' => USORV,
	    'title' => $title,
	    'content' => $content,
	    'content_id' => 0,      
	    'notifyData' => [
	      'title' => $title,
	      'content' => $content,
	      'hero_ids' => [$selectedHero->user_id],        
	      'data' => [
	        'type' => USORV,
	        'type_data' => 0
	      ]
	    ]
    ];
    //Tw_RegisterNotification($notification);
    $already[] = $selectedHero->user_id;
    $db->where('id', $order->id)->update(PDODR, [
    	'heroes' => implode(',', $already),
    	'hero_id' => $selectedHero->user_id
    ]);    
}

?>