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

if (empty($_GET['type']) || !isset($_GET['type']))Tw_HeaderExit(400, "Bad Request!");
$type = $_GET['type'];
$req = empty($_POST['req']) ? $type : $_POST['req'];
if ($req == 'send_otp') {
    if(empty($_POST['security'])){
        Tw_HeaderExit(400, ['msg' => "Invalid Attempt"]);
    }else if($_POST['security'] != KISOK){
        Tw_HeaderExit(400, ['msg' => "Invalid Attempt"]);    
    }
    $issuer = empty($_POST['issuer']) ? 'core' : $_POST['issuer'];    
    $issuers = ['core', 'vendor', 'hero'];
    $hash = empty($_POST['hash']) ? '' : $_POST['hash'];
    if(!in_array($issuer, $issuers))Tw_HeaderExit(400, ['msg' => "Invalid Attempt"]);
    $result = Tw_SendOtpAny($_POST['phone_no'], $issuer, $hash);
    if($result){
        Tw_HeaderExit(200, [
            'msg' => 'OTP Sent Successfully!',
            'otp' => $result
        ]);
    } else {
        Tw_HeaderExit(400, [
            'msg' => 'Unable to send OTP, please try again'
        ]);
    }
}else if ($req == 'hero_pin') {
    if(empty($_POST['security'])){
        Tw_HeaderExit(400, ['msg' => "Invalid Attempt"]);
    }else if($_POST['security'] != KISOK){
        Tw_HeaderExit(400, ['msg' => "Invalid Attempt"]);    
    }else if(empty($_POST['phone_no'])){
        Tw_HeaderExit(400, ['msg' => "Invalid Attempt"]);    
    }
    $result = Tw_GetDHeroFromPhone($_POST['phone_no']);
    if($result){
        $pin = Tw_GetHPIN($result['user_id']);
        Tw_HeaderExit(200, [
            'msg' => 'Enter Your HPIN!',
            'pin' => $pin
        ]);
    } else {
        Tw_HeaderExit(400, [
            'msg' => 'Delivery Hero Does Not Exists!'
        ]);
    }
}else if ($req == 'verified') {
    $result = Tw_LoginIfAcc($_POST);
    $isHero = Tw_DHeroData($result['id']);
    if($result){
        $final = [
            's' =>  $result['s'],
            'user_id' => $result['id'],
            'first_name' => $result['first_name'],
            'last_name' => $result['last_name']
        ];
        if($isHero){
            $final['center_id'] = $isHero['center_id'];
        }
        Tw_HeaderExit(200, $final);
    } else {
        Tw_HeaderExit(400, "Error!");
    }
}else if ($req == 'reg_d') {
    if(empty($_POST['security'])){
        Tw_HeaderExit(400, ['msg' => "Invalid Attempt"]);
    }else if($_POST['security'] != KISOK){
        Tw_HeaderExit(400, ['msg' => "Invalid Attempt"]);    
    }
    $result = Tw_RegisterUserDirect($_POST);    
    if($result){
        Tw_HeaderExit(200, [
            's' =>  $result['s'],
            'user_id' => $result['id'],
            'first_name' => $result['first_name'],
            'last_name' => $result['last_name']
        ]);
    } else {
        Tw_HeaderExit(400, "Error!");
    }
}else if ($req == 'login') {
    if (empty($_POST['phone_no'])) {
        Tw_HeaderExit(400, "Please Enter Phone no!");
    } else if (empty($_POST['password'])) {
        Tw_HeaderExit(400, "Please Enter Password!");
    }
    $password = $_POST['password'];
    $details = array('password' => $password);
    
    if(!empty($_POST['phone_no'])){        
        $user_id = Tw_UserIdFromPhoneNumber($_POST['phone_no']);
        $details['phone_no'] = $_POST['phone_no'];
    }else if(!empty($_POST['mail'])){        
        $user_id = Tw_UserIdFromEmail($_POST['mail']);
        $details['mail'] = $_POST['mail'];
    }else{
        Tw_HeaderExit(400, "User Not Found");
    }
    $user_login_data = Tw_UserData($user_id);
    if (empty($user_login_data)) {
        Tw_HeaderExit(400, "User Not Found");
    } else {    	
        $login = Tw_LoginUser($details);
        if (!$login) {
            Tw_HeaderExit(400, "Incorrect details!");
        } else {        	
            $json_success_data = array(
                'msg' => 'Successfully logged in, Please wait..', 
                'user_id' => $user_login_data['id'],
                'status' => $user_login_data['status'],
                's' => $login
            );
            Tw_HeaderExit(200, $json_success_data);                    
        }
    }
}

?>