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
header("Access-Control-Allow-Origin: *");
require_once('assets/main.php');
$f = '';
$s = '';
if (isset($_GET['f'])) {
    $f = YD_Secure($_GET['f'], 0);
}
if (isset($_GET['s'])) {
    $s = YD_Secure($_GET['s'], 0);
}
$hash_id = '';
if (!empty($_POST['hash_id'])) {
    $hash_id = $_POST['hash_id'];
} else if (!empty($_GET['hash_id'])) {
    $hash_id = $_GET['hash_id'];
} else if (!empty($_GET['hash'])) {
    $hash_id = $_GET['hash'];
} else if (!empty($_POST['hash'])) {
    $hash_id = $_POST['hash'];
}
$data        = array();

if ($f == 'gcob') {
	$data = array(
        'status' => 400
    );    
    if(empty($_POST['bid'])){
    	$data = array('status' => 400);
    }
    
    $limit = empty($_POST['l']) ? 10 : Yd_Secure($_POST['l']);
    $offset = empty($_POST['o']) ? 0 : Yd_Secure($_POST['o']);
    $crs = Yd_GetCoursesOfBoard($_POST['bid'], $limit, $offset);        
    if($crs == false && !is_array($crs)){
       $data = array('status' => 400);
    } else {
       $yd['data'] = $crs;
       $yd['data2'] = Yd_GetNews();
       $yd['announcement'] = Yd_GetAnnouncements();
       $data = array('status' => 200, 'an' => Yd_SetPage('cards/announcement-card'),'ns' => Yd_SetPage('board/small-card'), 'html' => Yd_SetPage('cards/course-card'));
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}else if ($f == 'gcboc') {
	$data = array(
        'status' => 400
    );    
    if(empty($_POST['cid'])){
    	$data = array('status' => 400);
    }
    
    $limit = empty($_POST['l']) ? 20 : Yd_Secure($_POST['l']);
    $offset = empty($_POST['o']) ? 0 : Yd_Secure($_POST['o']);
    $crs = Yd_GetCourseDataFromId($_POST['cid']);
    $yd['crs_type'] = $crs['type'];
    if($crs['type'] == 1){
     $res = array_reverse(Yd_GetBranchesOfCourse($_POST['cid'], $limit, $offset));
    }else{
     $res = Yd_GetClassesOfCourse($_POST['cid'], $limit, $offset);		
    }
    if($res == false && !is_array($res)){
       $data = array('status' => 400);
    } else {
       $yd['data'] = $res;              
       $data = array('status' => 200, 'html' => Yd_SetPage('cards/br-cls-card'));
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}else if ($f == 'gsyob') {
	$data = array(
        'status' => 400
    );    
    if(empty($_POST['brid'])){
    	$data = array('status' => 400);
    }
    
    $limit = empty($_POST['l']) ? 10 : Yd_Secure($_POST['l']);
    $offset = empty($_POST['o']) ? 0 : Yd_Secure($_POST['o']);
    $br = Yd_GetBranchInnerDataFromBranchId($_POST['brid']);       
    if($br == false && !is_array($br)){
       $data = array('status' => 400);
    } else {
       $yd['data'] = $br;              
       $data = array('status' => 200, 'html' => Yd_SetPage('cards/subranch-card'));
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
} else if ($f == 'gsob') {
	$data = array(
        'status' => 400
    );    
    if(empty($_POST['braid'])){
    	$data = array('status' => 400);
    }
    
    $limit = empty($_POST['l']) ? 10 : Yd_Secure($_POST['l']);
    $offset = empty($_POST['o']) ? 0 : Yd_Secure($_POST['o']);
    $sub = Yd_GetBranchSubjectsFromId($_POST['braid']);       
    if($sub == false && !is_array($sub)){
       $data = array('status' => 400);
    } else {
       $yd['data'] = $sub;              
       $data = array('status' => 200, 'html' => Yd_SetPage('cards/subject-card'));
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}  else if ($f == 'gbc') {
	$data = array(
        'status' => 400
    );    
    if(empty($_POST['sub_id'])){
    	$data = array('status' => 400);
    }
    
    $limit = empty($_POST['l']) ? 10 : Yd_Secure($_POST['l']);
    $offset = empty($_POST['o']) ? 0 : Yd_Secure($_POST['o']);
    $sub = Yd_GetChaptersFromIdNType($_POST['sub_id'], 1);       
    if($sub == false && !is_array($sub)){
       $data = array('status' => 400);
    } else {
       $yd['data'] = $sub;              
       $data = array('status' => 200, 'html' => Yd_SetPage('cards/chapter-card'));
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}  else if ($f == 'gbssm') {
	$data = array(
        'status' => 400
    );    
    if(empty($_POST['ty']) || empty($_POST['tid']) || empty($_POST['tid'])){
    	$data = array('status' => 400);
    }    
    $limit = empty($_POST['l']) ? 10 : Yd_Secure($_POST['l']);
    $offset = empty($_POST['o']) ? 0 : Yd_Secure($_POST['o']);        
    $type = $_POST['ty'];
    $typeid = $_POST['tid'];       
    $resid = $_POST['rid'];
    if(empty($_POST['sty'])){
        $subtype = 1;
    }else{
        $subtype = $_POST['sty'];
    }
    if($type == 'subject'){
     $dta = Yd_GetResData(1, $typeid, $resid, $subtype);                         
    } else {
     $dta = Yd_GetResData(2, $typeid, $resid);  
    }
    if($dta == false && !is_array($dta)){
       $data = array('status' => 400);
    } else {
       $yd['resdata'] = $dta;              
       $data = array('status' => 200, 'html' => Yd_SetPage('cards/material-card'));
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
} else if ($f == 'gubp') {
    $data = array(
        'status' => 400
    );
    if(empty($_POST['pid'])){
        $data = array('status' => 400);
    }    
    $user=Yd_UserData($_POST['pid']);
    $asked=Yd_CountAnsweredQuestion($user['id']);
    $answered=Yd_CountAskedQuestion($user['id']);
    $total=($asked*5)+($answered*10);
    $yd['points'] = array(
        'asked' => $asked,
        'answered' => $answered,
        'total' => $total
    );
    $yd['crscard'] = Yd_GetCourseData($user['id']);
    $data = array('status' => 200,'brd' => Yd_SetPage('profile/brd-card'),'pts' => Yd_SetPage('profile/pts-card'));    
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
} else if ($f == 'spp') {
    $data = array(
        'status' => 400
    );
    if(empty($_POST['uid']) || empty($_POST['g']) || empty($_POST['n'])){
        $data = array('status' => 400);
    }
    global $sqlConnect;
    $avatar = 'uploads/avatars/default_avatar'.$_POST['g'].' ('.$_POST['n'].').png';
    $query = mysqli_query($sqlConnect, "UPDATE ".T_USERS." SET `avatar` = '{$avatar}' WHERE `user_id` = {$_POST['uid']} ");       
    if($query){
        $data = array('status' => 200);
    }
    
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
} else if ($f == 'gtfc') {
    $data = array(
        'status' => 400
    );    
    if(empty($_POST['ch'])){
        $data = array('status' => 400);
    }
    $topic = Yd_GetTopicsFromChapterId($_POST['ch']);
    if($topic == false && !is_array($topic)){
       $data = array('status' => 400);
    } else {
       $yd['data'] = $topic;              
       $data = array('status' => 200, 'html' => Yd_SetPage('cards/topic-card'));
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
else if($f == 'us'){
    $data = array('status' => 400);
    $check_name = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `username` = {$_POST["username"]} AND `user_id` != {$_POST['zx']} ");
    if(!empty($check_name) && Yd_Sql_Result($check_name, 0) == 1){        
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    $un = empty($_POST['username']) ? '' : " username = '".$_POST['username']."',";
    $fname = empty($_POST['first_name']) ? '' : " first_name = '".$_POST['first_name']."',";
    $lname = empty($_POST['last_name']) ? '' : " last_name = '".$_POST['last_name']."',";
    $ph = empty($_POST['phone_number']) ? '' : " phone_number = ".$_POST['phone_number'].",";
    $em = empty($_POST['email']) ? '' : " email = '".$_POST['email']."',";        
    $gen = empty($_POST['gender']) ? " gender = 'male' " : " gender = '".$_POST['gender']."' ";        
    $string = $un.$fname.$lname.$ph.$em.$gen;   
    $update = mysqli_query($sqlConnect, "UPDATE ".T_USERS." SET".$string." WHERE `user_id` = {$_POST['zx']} ");    
    if($update){
       $data = array('status' => 200); 
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}else if ($f == 'comment') {
    global $sqlConnect;
    if(!empty($_POST["name"]) && !empty($_POST["comment"])){
    $insertComments = "INSERT INTO ".T_SCOM." (page_id, comment, sender) VALUES (".$_POST["commentId"].", '".$_POST["comment"]."', '".$_POST["name"]."')";
    mysqli_query($sqlConnect, $insertComments) or die("database error: ". mysqli_error($sqlConnect));
    $message = '<label class="text-success">Comment posted Successfully.</label>';
        $status = array(
            'error' => 0,
            'message' => $message
        );
    } else {
    $message = '<label class="text-danger">Error: Comment not posted.</label>';
        $status = array(
            'error' => 1,
            'message' => $message
        );
    }
    echo json_encode($status);
}else if($f == 'show_comments'){
   global $sqlConnect;
   $commentQuery = "SELECT * FROM ".T_SCOM." WHERE page_id = {$_POST['page_id']} ORDER BY id DESC";
    $commentsResult = mysqli_query($sqlConnect, $commentQuery) or die("database error:". mysqli_error($sqlConnect));
    $commentHTML = '';
    while($comment = mysqli_fetch_assoc($commentsResult)){
    $commentHTML .= '<div class="flex row w70">
        <div class="p-5 flex itemCenter"><svg width="25"height="25" viewBox="0 0 53 53" fill="#000"><use xlink:href="#user"></svg></div><div>
            <div class="mt20"><span class="tblk br10 w100 cgr p-5">'.$comment["comment"].'</span></div>
            <span class="fsm gr p-5">'.$comment["sender"].' '.$comment["date"].'</span>          
        </div></div>';
    }        
    echo $commentHTML;
}   
      
?>