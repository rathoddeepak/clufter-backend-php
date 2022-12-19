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

if (empty($_GET['type']) || !isset($_GET['type'])) {
    Tw_HeaderExit(400, "Bad Request!");
}
$type = $_GET['type'];

if ($type == 'logout') {
	if(empty($_POST['token']) || empty($_POST['user_id'])){
	 	Tw_HeaderExit(400, "Insufficient details!");
	}else if(!Tw_AuthorizeUser($_POST['user_id'], $_POST['token'])){
		Tw_HeaderExit(400, "Authorization Error.");
	}else {
		$result = Tw_DeleteAppSession($_POST['user_id']);
		if($result){
		  Tw_HeaderExit(200, ["message" => "Logout Successfully!"]);	
		}else{
		  Tw_HeaderExit(400, "Error found, please try again later!");	
		}	    
	}    
}

?>