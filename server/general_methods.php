<?php

use kornrunner\Blurhash\Blurhash;
//use Kreait\Firebase\Messaging\CloudMessage;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;
use Razorpay\Api\Api;
//Common Methods
function Tw_FileSize($bytes, $decimals = 2){
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
function Tw_ToPaise($rupee){
  return $rupee * 100;
}
function Tw_OrderCode($id){    
  return substr('#ORDER0', 0, 6 - strlen($id)) . $id;
}
function Tw_ToRupee($rupee){
  return $rupee / 100;
}
function Tw_CalculateET($numberOfFood){
  return PER_VENDOR_TIME * $numberOfFood;
}
function Tw_VendorTimeDiff($close_time, $currentTime = -1){    
  $hours = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];  
  if($close_time >= $currentTime){    
    $end = $close_time;
  }else{    
    $end = $currentTime == -1 ? time() : $currentTime;
  }
  $startHr = intval(date('H', $currentTime == -1 ? time() : $currentTime));//1613930948
  $endHr = intval(date('H', $end));//1613984189
  
  $startMin = intval(date('i', $currentTime == -1 ? time() : $currentTime));
  $endMin = intval(date('i', $end));//1613984189
  $minDiff = abs(abs(($startMin - $endMin)) - 60);
  $hourDiff = 0;
  if($startHr != $endHr){
      for($z = $startHr; $z != $endHr; $z++){
          $hourDiff++;
          if($z == 23)$z = 0;
      }
      //$hourDiff++;
  }
  if($startMin > 0){
    $hourDiff--;
  }
  if($hourDiff < 0){
    $hourDiff = 0;
  }
  return ['hrs' => $hourDiff, 'mins' => $minDiff];
}
function objectKeyInArray($objects, $key, $value) {    
    return array_filter($objects, function($toCheck) use ($key, $value) { 
        return $toCheck[$key] == $value; 
    });
}

function objectIndexInArray($list, $key, $value) {
  $foundAt = -1;
  foreach($list as $i =>  $data){
    if($data[$key] == $value){
      $foundAt = $i;
      break;
    }
  }
  return $foundAt;
}

function Tw_NumToOrdinalWord($num){
    $first_word = array('eth','First','Second','Third','Fouth','Fifth','Sixth','Seventh','Eighth','Ninth','Tenth','Elevents','Twelfth','Thirteenth','Fourteenth','Fifteenth','Sixteenth','Seventeenth','Eighteenth','Nineteenth','Twentieth');
    $second_word =array('','','Twenty','Thirty','Forty','Fifty');

    if($num <= 20)
        return $first_word[$num];

    $first_num = substr($num,-1,1);
    $second_num = substr($num,-2,1);

    return $string = str_replace('y-eth','ieth',$second_word[$second_num].'-'.$first_word[$first_num]);
}
function Tw_NumToOrdinalNumeric($number){
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}
function Tw_GetSettings() {
    global $db;    
    $result = $db->settings->getOne(["lock_file" => $tw['lock_code']]);    
    return $result;
}
function nempty($val){
  if($val == 0)return false;
  return empty($val);
}
function Tw_GenerateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false) {
    $charset = '';
    if ($uselower) {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    if ($useupper) {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if ($usenumbers) {
        $charset .= "123456789";
    }
    if ($usespecial) {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    if ($minlength > $maxlength) {
        $length = mt_rand($maxlength, $minlength);
    } else {
        $length = mt_rand($minlength, $maxlength);
    }
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
    }
    return $key;
}
function Tw_GetWebHookData(){
  $rawData = file_get_contents("php://input");  
  return $rawData;
}
function Tw_GetHeaderValue($key){
  $value = 'HTTP_'.$key;
  $headerStringValue = $_SERVER[$value];
}
function Tw_TimeAgo($timestamp){    
  $current_time    = time();
  $time_difference = $current_time - $timestamp;
  $seconds         = $time_difference;
  
  $minutes = round($seconds / 60); // value 60 is seconds  
  $hours   = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec  
  $days    = round($seconds / 86400); //86400 = 24 * 60 * 60;  
  $weeks   = round($seconds / 604800); // 7*24*60*60;  
  $months  = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60  
  $years   = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60
                
  if ($seconds <= 60){

    return "Just Now";

  } else if ($minutes <= 60){

    if ($minutes == 1){

      return "one minute ago";

    } else {

      return "$minutes minutes ago";

    }

  } else if ($hours <= 24){

    if ($hours == 1){

      return "an hour ago";

    } else {

      return "$hours hrs ago";

    }

  } else if ($days <= 7){

    if ($days == 1){

      return "yesterday";

    } else {

      return "$days days ago";

    }

  } else if ($weeks <= 4.3){

    if ($weeks == 1){

      return "a week ago";

    } else {

      return "$weeks weeks ago";

    }

  } else if ($months <= 12){

    if ($months == 1){

      return "a month ago";

    } else {

      return "$months months ago";

    }

  } else {
    
    if ($years == 1){

      return "one year ago";

    } else {

      return "$years years ago";

    }
  }
}
function Tw_CalDistance($lat1, $lon1, $lat2, $lon2, $unit = "K") {
  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    return 0;
  }else {
    if(!is_numeric($lat1) || !is_numeric($lon1) || !is_numeric($lat2) || !is_numeric($lon2)){
      return 0;
    }
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
      return $miles;
    }
  }
}
function Tw_ModifyDistance($distance){
  $unit = 'KM';
  if($distance < 1){
    $unit = 'M';
    $distance = $distance * 1000;
  }
  return number_format($distance, 2, '.', '').' '.$unit;
}
function Tw_TimeReadable($timestamp){    
  $timestamp = is_string($timestamp) ? strtotime($timestamp) : $timestamp;  
  return date('d-m-Y H:i:s', $timestamp);  
}
function Tw_DateReadable($timestamp){    
  $timestamp = is_string($timestamp) ? strtotime($timestamp) : $timestamp;  
  return date('d/m/Y', $timestamp);
}
function Tw_TimeHumanType($timestamp){    
  $timestamp = is_string($timestamp) ? strtotime($timestamp) : $timestamp;
  $timeToday = time();
  $timeToday = new DateTime("@$timestamp");
  $dt = new DateTime("@$timestamp");  // convert UNIX timestamp to PHP DateTime
  $dt->setTimezone(new DateTimeZone("Asia/Kolkata"));
  $timeToday->setTimezone(new DateTimeZone("Asia/Kolkata"));
  if($dt->format('Y') == $timeToday->format('Y')){
    if($dt->format('M') == $timeToday->format('M')){
      if($dt->format('d') == $timeToday->format('d')){
        return $dt->format('h:i a');
      }else{
        return $dt->format('d h:i a');
      }
    }else{
      return $dt->format('d M h:i a');
    }    
  }else{
    return $dt->format('d M Y h:i a');
  } 
}
function Tw_TimeReadable2($timestamp){  
  $timestamp = is_string($timestamp) ? strtotime($timestamp) : $timestamp;  
  return date('d M Y H:i', $timestamp);
}
function Tw_TimeReadableObject($timestamp = -1){  
  $time = $timestamp == -1 ? time() : $timestamp;  
  $date = array(
    'date' => date('d', $time),
    'month' => date('m', $time),
    'year' => date('Y', $time),
    'hour' => date('H', $time),
    'minutes' => date('i', $time),
    'seconds' => date('s', $time)
  );


  return $date;
}

function Tw_GetWordMonth($month){  
  switch($month){
    case 1:
    return 'January';
    case 2:
    return 'February';
    case 3:
    return 'March';
    case 4:
    return 'April';
    case 5:
    return 'May';
    case 6:
    return 'June';
    case 7:
    return 'July';
    case 8:
    return 'August';
    case 9:
    return 'September';
    case 10:
    return 'October';
    case 11:
    return 'November';
    case 12:
    return 'December';
    default:
    return 'January';
  }
}

function Tw_IsMobile() {
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
        return true;
    }
    return false;
}
function Tw_MoveSingleFile($to, $file){
    $filename = $file['name'];
    $tmplocation  = $file['file'];
    $mime_type  = $file['type'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if (!file_exists('uploads/'.$to.'/' . date('Y'))) {
        mkdir('uploads/'.$to.'/' . date('Y'), 0777, true);
    }
    if (!file_exists('uploads/'.$to.'/' . date('Y') . '/' . date('m'))) {
      mkdir('uploads/'.$to.'/' . date('Y') . '/' . date('m'), 0777, true);
    }
    
    $allowed           = 'jpg,png,jpeg,hls,m3u8,mp4,pdf';
    $new_string        = pathinfo($filename, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
      return false;
    }
    $ar = array(
      'image/png',
      'image/jpeg',      
      'image/jpg',      
      'application/pdf',
      'video/mp4',
      'application/x-mpegURL',
      'video/MP2T',
      'vnd.apple.mpegURL',
      'audio/mpeg',
    );
    if (!in_array($mime_type, $ar)) {
      return false;
    }
    $dir = 'uploads/'.$to.'/' . date('Y') . '/' . date('m');
    $new_destination = $dir . '/' . Tw_GenerateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $ext;
    move_uploaded_file($tmplocation, $new_destination);
    return $new_destination;
}
function Tw_GetIPAddress() {  
  if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
      $ip = $_SERVER['HTTP_CLIENT_IP'];  
  }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
  }else{  
      $ip = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
  }  
    return $ip;  
}  
function Tw_VerifyObject($from, $to){
  $fromKeys = $from;
  $toKey = array_keys($to);
  $object = [];
  foreach ($fromKeys as $idx => $key) {   
    if(!in_array($key, $toKey)) {
        return false;
    }else{
      $object[$key] = $to[$key];
    }
  }
  return $object;
}
function Tw_IsValidPhone($phone){
  $phone = preg_replace("/[^\d]/", "", $phone);
  if (strlen($phone) <= 13) { 
      return true;
  }else{
    return false;
  }
}
function Tw_IsValidName($value){  
  if (preg_match("/^[\p{L}\p{M}]+([\p{L}\p{Pd}\p{Zs}'.]*[\p{L}\p{M}])+$|^[\p{L}\p{M}]+$/", $value)) { 
      return true;
  }else{
    return false;
  }
}
function Tw_RandomStr($length_of_string = 6) {     
    //$str_result = '0123456789abcdefghijklmnopqrstuvwxyz'; 
     $str_result = '0123456789'; 
    return substr(str_shuffle($str_result), 0, $length_of_string); 
}
function Tw_SetPage($page_url = '') {
  global $tw;    
  $page         = './themes/' . $tw['theme'] . '/layout/' . $page_url . '.phtml';
  $page_content = '';
  ob_start();
  require($page);
  $page_content = ob_get_contents();
  ob_end_clean();    
  return $page_content;
}
function Tw_SetComponent($page_url = '') {
  global $tw;    
  $page         = './themes/' . $tw['theme'] . '/components/' . $page_url . '.phtml';
  $page_content = '';
  ob_start();
  require($page);
  $page_content = ob_get_contents();
  ob_end_clean();    
  return $page_content;
}
function Tw_IsAdmin($user_id = 0) {
    global $tw;    
    if ($tw['loggedin'] == false)return false;
    $user_id = $tw['user']['id'];
    if ($tw['user']['admin'] == 1)return true;
}

function Tw_SetAdminPage($page_url = '') {
  global $tw;    
  $page         = './layout/' . $page_url . '.phtml';
  $page_content = '';
  ob_start();
  require($page);
  $page_content = ob_get_contents();
  ob_end_clean();    
  return $page_content;
}

function Tw_GetBrowser() {
      $u_agent = $_SERVER['HTTP_USER_AGENT'];
      $bname = 'Unknown';
      $platform = 'Unknown';
      $version= "";
      // First get the platform?
      if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
      } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
      } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
      } elseif (preg_match('/iphone|IPhone/i', $u_agent)) {
        $platform = 'IPhone Web';
      } elseif (preg_match('/android|Android/i', $u_agent)) {
        $platform = 'Android Web';
      } else if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $u_agent)) {
        $platform = 'Mobile';
      }
      // Next get the name of the useragent yes seperately and for good reason
      if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
      } elseif(preg_match('/Firefox/i',$u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
      } elseif(preg_match('/Chrome/i',$u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
      } elseif(preg_match('/Safari/i',$u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
      } elseif(preg_match('/Opera/i',$u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
      } elseif(preg_match('/Netscape/i',$u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
      }
      // finally get the correct version number
      $known = array('Version', $ub, 'other');
      $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
      if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
      }
      // see how many we have
      $i = count($matches['browser']);
      if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
          $version= $matches['version'][0];
        } else {
          $version= $matches['version'][1];
        }
      } else {
        $version= $matches['version'][0];
      }
      // check if we have a number
      if ($version==null || $version=="") {$version="?";}
      return array(
          'userAgent' => $u_agent,
          'name'      => $bname,
          'version'   => $version,
          'platform'  => $platform,
          'pattern'    => $pattern
      );
}
function Tw_HeaderExit($status = 200, $data){
  $json_data = array('status' => $status, 'data' => $data);
  header("Content-type: application/json");
  echo json_encode($json_data, JSON_PRETTY_PRINT);
  exit();
}
function Tw_HeaderError(){
  header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);  
  exit();
}
function Tw_Exit($json_data){
  header("Content-type: application/json");
  echo json_encode($json_data, JSON_PRETTY_PRINT);
  exit();
}
function Tw_CheckValidMime($mime,$type){
  if(empty($mime) || empty($type)){
    return false;
  }
  if($type == 'Images'){
    $MIMES = [
     'image/jpeg',
     'image/jpg',
     'image/png',
     'image/webp',
     'image/bmp',
     'image/gif'
    ];
    if(!in_array($mime, $MIMES)){
      return false;
    }
  }else if($type == 'Videos'){
    $MIMES = [
      'video/x-flv',
      'video/mp4',
      'application/x-mpegURL',
      'video/MP2T',
      'video/3gpp',
      'video/quicktime',
      'video/x-msvideo',
      'video/x-ms-wmv'
    ];
    if(!in_array($mime, $MIMES)){
      return false;
    }
  }else if($type == 'Document'){
    if(!Yd_IsFileType($mime)){
      return false;
    }
  }
  return true;
}
function Tw_CheckValidMimeFile($mime){
  if(empty($mime)){
    return false;
  }
  $MIMES = [
   'application/pdf'
  ];
  if(!in_array($mime, $MIMES)){
    return false;
  }
  return true;
}
function Tw_GetMediaType($url){
  $MIME_Img = [
   'image/jpeg',
   'image/jpg',
   'image/png',
   'image/webp',
   'image/bmp'
  ];
  $MIME_Video = [
    'video/x-flv',
    'video/mp4',
    'application/octet-stream',
    'application/x-mpegURL',
    'video/MP2T',
    'video/3gpp',
    'video/quicktime',
    'video/x-msvideo',
    'video/x-ms-wmv'
  ];
  if(file_exists($url)){
    $mime = mime_content_type($url);
  }else{
    $mime = false;
  }  
  if($mime == 'image/gif'){
      return 2;    
  }else if(in_array($mime, $MIME_Img)){
      $type = 0;
      list($width, $height, $type, $attr) = getimagesize($url);
      $diff = $height - $width;
      if($diff >= 1){
          return 3;
      }    
  }else if(in_array($mime, $MIME_Video)){
      return 1;    
  }else{
      return false;
  }
  return $type;
}
function Tw_GetTypeFromMeme($mime){
  $MIME_Img = [
   'image/jpeg',
   'image/jpg',
   'image/png',
   'image/webp',
   'image/bmp'
  ];
  $MIME_Video = [
    'video/x-flv',
    'video/mp4',
    'application/octet-stream',
    'application/x-mpegURL',
    'video/MP2T',
    'video/3gpp',
    'video/quicktime',
    'video/x-msvideo',
    'video/x-ms-wmv'
  ];  
  if($mime == 'image/gif'){
      return 'gif';    
  }else if(in_array($mime, $MIME_Img)){
      return 'image'; 
  }else if(in_array($mime, $MIME_Video)){
      return 'video';    
  }else{
      return false;
  }  
}
function Tw_DelFromIndex($limit,$myArrayInit){
    $offsetKey = $limit;
    $n = array_keys($myArrayInit);
    $count = array_search($offsetKey, $n);
    $new_arr = array_slice($myArrayInit, 0, $count + 1, true);
    return $new_arr;
}
function Tw_IsFileType($mime = '',$url = ''){
  $MIME_FILE = [
   'application/msword',
   'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
   'application/vnd.ms-excel',
   'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
   'application/vnd.ms-powerpoint',
   'application/vnd.openxmlformats-officedocument.presentationml.presentation',
   'application/pdf'
  ]; 
  if($mime == ''){
   $mime = mime_content_type($url);
  }
  if(in_array($mime, $MIME_FILE)){
      return true;
  }else{
      return false;
  }
}
function Tw_GetTypeFromExtension($ext){
  $ext = strtolower($ext);
  if($ext == 'png' || $ext == 'jpg' || $ext == 'png'|| $ext == 'jpeg' || $ext == 'webp' || $ext == 'jfif'){
      $type = 'Images';
      return $type;
    }else if($ext == 'mp4' || $ext == 'gif' || $ext == 'm3u8' || $ext == 'ts'){
      $type = 'Videos';
      return $type;
    }else if($ext == 'doc' || $ext == 'docx' || $ext == 'pdf' || $ext == 'ppt' || $ext == 'pptx' || $ext == 'txt' || $ext == 'json' || $ext == 'php'){
      $type = 'Document';
      return $type;
    }else{
      return false;
    }
}
function Tw_ReplaceExtensionWithDir($filename, $new_extension) {
    $info = pathinfo($filename);
    return $info['dirname'].'/'.$info['filename'] . '.' . $new_extension;
}
function Tw_ReplaceExtension($filename, $new_extension) {
    $info = pathinfo($filename);
    return $info['filename'] . '.' . $new_extension;
}
function Tw_MoveMedia($details, $uploadType = 'all', $compress = true){
    $filename = $details["name"];
    $tmplocation  = $details["tmp_name"];
    $mime_type  = $details['type'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if($uploadType == 'all'){
      $type = Tw_GetTypeFromExtension($ext);
      if(!$type){
        return false;
      }
      $supported = Tw_CheckValidMime($mime_type,$type);
      if(!$supported){
        return false;
      }    
    }else if($uploadType == 'ImgVid'){
      $type = Tw_GetTypeFromExtension($ext);
      if($type != 'Images' && $type != 'Videos'){
         return false;
      }
      $supported = Tw_CheckValidMime($mime_type,$type);
      if(!$supported){
        return false;
      }    
    }else{
      $supported = Tw_CheckValidMime($mime_type,$uploadType);
      $type = Tw_GetTypeFromExtension($ext);
      if(!$supported){
        return false;
      }
      if($uploadType != $type){
        return false;
      }
    }
    if (!file_exists('uploads/'.$type.'/' . date('Y'))) {
     mkdir('uploads/'.$type.'/' . date('Y'), 0777, true);
     mkdir('uploads/Images/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('uploads/'.$type.'/' . date('Y') . '/' . date('m'))) {
     mkdir('uploads/'.$type.'/' . date('Y') . '/' . date('m'), 0777, true);     
    }
    $dir = 'uploads/'.$type.'/' . date('Y') . '/' . date('m');
    $new_destination = $dir . '/' . Tw_GenerateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $ext;    
    if($type == 'Images'){
      if($compress){
       Tw_CompressImage($tmplocation, $new_destination, 96);
      }else{
        move_uploaded_file($tmplocation, $new_destination);
      }
      //$compress = Tw_CreateThumbnail($new_destination,$new_destination.'_thumb',10,10);
      //Tw_CompressImage($new_destination.'_thumb', $new_destination.'_thumb', 60);      
    }else if($type == 'Videos'){
      if(move_uploaded_file($tmplocation, $new_destination)){
        $thumb = Tw_ReplaceExtension($new_destination, 'jpg');
        $thumb = 'uploads/Images/'. date('Y') . '/' . date('m').'/'.$thumb; 
        /*
        $whitelist = array(
            '127.0.0.1',
            '192.168.43.250',
            '::1',
            ''
        );
        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            $currentDir = dirname(__FILE__);
            $currentDir = str_replace("/assets/functions", "/", $currentDir);
            //$currentDir = str_replace("\\assets\\functions", "/", $currentDir);
            $transloadit = new Transloadit([
              "key" => "03fd86f8b7cb4505a60e610d4a354b24",
              "secret" => "6d203d16fb4fb70cd04184e54eda555833e1eef9",
            ]);
            $files = [];
            array_push($files, $currentDir.$new_destination);
            $response = $transloadit->createAssembly([
              "files" => $files, 
              "params" => [
                "steps" => [
                  ":original" => [
                    "robot" => "/upload/handle",
                  ],
                  "thumbnailed" => [
                    "use" => ":original",
                    "robot" => "/video/thumbs",
                    "count" => 1,
                    "width" => 320,
                    "height" => 240,
                    "offsets" => [0],
                    "resize_strategy" => "pad",
                    "ffmpeg_stack" => "v3.3.3",
                  ],
                  "min_resized" => [
                    "use" => "thumbnailed",
                    "robot" => "/image/resize",
                    "result" => true,
                    "height" => 320,
                    "width" => 240,
                    "quality" => 66,
                    "imagemagick_stack" => "v2.0.7",        
                  ], 
                  "store_files" => [
                    "use" => ["min_resized"],
                    "robot" => "/ftp/store",
                    "path" => "domains/ydcrackers.com/public_html/".$thumb,        
                    "url_template" => "https://ydcrackers.com/domains/ydcrackers.com/public_html/",
                    "credentials" => "ydc_host"
                  ]     
                ],
              ],
            ]); 
        }else{*/
          $ffmpeg = FFMpeg\FFMpeg::create();
          $video = $ffmpeg->open($new_destination);          
          $video
              ->filters()              
              ->synchronize();
          $video
              ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0))
              ->save($thumb);
              
          //$qtfaststart = FFMpeg\QtFaststart::create();
          //$qtfaststart->process($new_destination);

          Tw_CompressImage($thumb, $thumb, 80);
          //Tw_CreateThumbnail($thumb,$thumb.'_thumb',10,10);
          //Tw_CompressImage($thumb.'_thumb', $thumb.'_thumb', 60);
        //}
      }
    }else{
      move_uploaded_file($tmplocation, $new_destination);
    }
    return $new_destination;
}
function Tw_MoveMediaSeprate($details, $uploadType = 'all', $compress = true){    
    $filename = $details["name"];
    $tmplocation  = $details["tmp_name"];
    $mime_type  = $details['type'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if($uploadType == 'all'){
      $type = Tw_GetTypeFromExtension($ext);
      if(!$type){        
        return false;
      }
      $supported = Tw_CheckValidMime($mime_type,$type);
      if(!$supported){        
        return false;
      }    
    }else if($uploadType == 'ImgVid'){
      $type = Tw_GetTypeFromExtension($ext);
      if($type != 'Images' && $type != 'Videos'){
         return false;
      }
      $supported = Tw_CheckValidMime($mime_type,$type);
      if(!$supported){
        return false;
      }    
    }else{
      $supported = Tw_CheckValidMime($mime_type,$uploadType);
      $type = Tw_GetTypeFromExtension($ext);
      if(!$supported){
        return false;
      }
      if($uploadType != $type){
        return false;
      }
    }
    if (!file_exists('uploads/'.$type.'/' . date('Y'))) {
     mkdir('uploads/'.$type.'/' . date('Y'), 0777, true);
     mkdir('uploads/Images/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('uploads/'.$type.'/' . date('Y') . '/' . date('m'))) {
     mkdir('uploads/'.$type.'/' . date('Y') . '/' . date('m'), 0777, true);     
    }
    $dir = 'uploads/'.$type.'/' . date('Y') . '/' . date('m');
    $new_destination = $dir . '/' . Tw_GenerateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $ext;
    while(file_exists($new_destination)){
      $new_destination = $dir . '/' . Tw_GenerateKey() . '_' . date('d') . '_' . md5(time()) . '.' . $ext;
    }

    if($type == 'Images'){
      if($compress){
        Tw_CompressImage($tmplocation, $new_destination, 96);
      }else{
        move_uploaded_file($tmplocation, $new_destination);
      }
      $blurHash = Tw_CreateBlurHash($new_destination);      
    }else if($type == 'Videos'){
      if(move_uploaded_file($tmplocation, $new_destination)){
        $thumb = Tw_ReplaceExtension($new_destination, 'jpg');
        $thumb = 'uploads/Images/'. date('Y') . '/' . date('m').'/'.$thumb; 
        /*
        $whitelist = array(
            '127.0.0.1',
            '192.168.43.250',
            '::1',
            ''
        );
        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            $currentDir = dirname(__FILE__);
            $currentDir = str_replace("/assets/functions", "/", $currentDir);
            //$currentDir = str_replace("\\assets\\functions", "/", $currentDir);
            $transloadit = new Transloadit([
              "key" => "03fd86f8b7cb4505a60e610d4a354b24",
              "secret" => "6d203d16fb4fb70cd04184e54eda555833e1eef9",
            ]);
            $files = [];
            array_push($files, $currentDir.$new_destination);
            $response = $transloadit->createAssembly([
              "files" => $files, 
              "params" => [
                "steps" => [
                  ":original" => [
                    "robot" => "/upload/handle",
                  ],
                  "thumbnailed" => [
                    "use" => ":original",
                    "robot" => "/video/thumbs",
                    "count" => 1,
                    "width" => 320,
                    "height" => 240,
                    "offsets" => [0],
                    "resize_strategy" => "pad",
                    "ffmpeg_stack" => "v3.3.3",
                  ],
                  "min_resized" => [
                    "use" => "thumbnailed",
                    "robot" => "/image/resize",
                    "result" => true,
                    "height" => 320,
                    "width" => 240,
                    "quality" => 66,
                    "imagemagick_stack" => "v2.0.7",        
                  ], 
                  "store_files" => [
                    "use" => ["min_resized"],
                    "robot" => "/ftp/store",
                    "path" => "domains/ydcrackers.com/public_html/".$thumb,        
                    "url_template" => "https://ydcrackers.com/domains/ydcrackers.com/public_html/",
                    "credentials" => "ydc_host"
                  ]     
                ],
              ],
            ]); 
        }else{*/
          $ffmpeg = FFMpeg\FFMpeg::create();
          $video = $ffmpeg->open($new_destination);          
          $video
              ->filters()              
              ->synchronize();
          $video
              ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0))
              ->save($thumb);
              
          $qtfaststart = FFMpeg\QtFaststart::create();
          $qtfaststart->process($new_destination);

          Tw_CompressImage($thumb, $thumb, 80);
          $blurHash = Tw_CreateBlurHash($thumb);
          Tw_CreateThumbnail($thumb,$thumb.'_thumb',10,10);
          Tw_CompressImage($thumb.'_thumb', $thumb.'_thumb', 60);
        //}
      }
    }else{
      move_uploaded_file($tmplocation, $new_destination);
    }
    return ['file' => $new_destination, 'hash' => $blurHash];
}
const IMAGE_HANDLERS = [
    IMAGETYPE_JPEG => [
        'load' => 'imagecreatefromjpeg',
        'save' => 'imagejpeg',
        'quality' => 100
    ],
    IMAGETYPE_PNG => [
        'load' => 'imagecreatefrompng',
        'save' => 'imagepng',
        'quality' => 0
    ],
    IMAGETYPE_GIF => [
        'load' => 'imagecreatefromgif',
        'save' => 'imagegif'
    ]
];
function Tw_CreateBlurHash($file){
  $image = imagecreatefromstring(file_get_contents($file));
  $image = imagescale($image, 20);  
  $width = imagesx($image);
  $height = imagesy($image);    

  $pixels = [];
  for ($y = 0; $y < $height; ++$y) {
      $row = [];
      for ($x = 0; $x < $width; ++$x) {
          $index = imagecolorat($image, $x, $y);
          $colors = imagecolorsforindex($image, $index);
          $row[] = [$colors['red'], $colors['green'], $colors['blue']];
      }
      $pixels[] = $row;
  }

  $components_x = 4;
  $components_y = 3;
  $blurhash = Blurhash::encode($pixels, $components_x, $components_y);
  return $blurhash;
}
function Tw_CreateThumbnail($src, $dest, $targetWidth, $targetHeight = null) {
    // 1. Load the image from the given $src
    // - see if the file actually exists
    // - check if it's of a valid image type
    // - load the image resource
    // get the type of the image
    // we need the type to determine the correct loader
    $type = exif_imagetype($src);
    // if no valid type or no handler found -> exit
    if (!$type || !IMAGE_HANDLERS[$type]) {
        return null;
    }
    // load the image with the correct loader
    $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);
    // no image found at supplied location -> exit
    if (!$image) {
        return null;
    }
    // 2. Create a thumbnail and resize the loaded $image
    // - get the image dimensions
    // - define the output size appropriately
    // - create a thumbnail based on that size
    // - set alpha transparency for GIFs and PNGs
    // - draw the final thumbnail
    // get original image width and height
    $width = imagesx($image);
    $height = imagesy($image);
    // maintain aspect ratio when no height set
    if ($targetHeight == null) {
        // get width to height ratio
        $ratio = $width / $height;
        // if is portrait
        // use ratio to scale height to fit in square
        if ($width > $height) {
            $targetHeight = floor($targetWidth / $ratio);
        }
        // if is landscape
        // use ratio to scale width to fit in square
        else {
            $targetHeight = $targetWidth;
            $targetWidth = floor($targetWidth * $ratio);
        }
    }
    // create duplicate image based on calculated target size
    $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);
    // set transparency options for GIFs and PNGs
    if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
        // make image transparent
        imagecolortransparent(
            $thumbnail,
            imagecolorallocate($thumbnail, 0, 0, 0)
        );
        // additional settings for PNGs
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
    }
    // copy entire source image to duplicate image and resize
    imagecopyresampled(
        $thumbnail,
        $image,
        0, 0, 0, 0,
        $targetWidth, $targetHeight,
        $width, $height
    );
    // 3. Save the $thumbnail to disk
    // - call the correct save method
    // - set the correct quality level
    // save the duplicate version of the image to disk
    return call_user_func(
        IMAGE_HANDLERS[$type]['save'],
        $thumbnail,
        $dest,
        IMAGE_HANDLERS[$type]['quality']
    );
}

function Tw_CompressImage($source, $destination, $quality){
    $info = getimagesize($source);    
    $size = filesize($source)*0.000001;
    error_reporting(0);
    if($size > 3){
      $quality = 20;
    }else if($size > 2){
      $quality -= 40;
    }else if($size > 1){
      $quality -= 30;
    }else if($size < 0.5){
      move_uploaded_file($source, $destination);
      return $destination;
    }

    if ($info['mime'] == 'image/jpeg') 
        $img = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') 
        $img = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') 
        $img = imagecreatefrompng($source);

    $exif = exif_read_data($source);
    if ($exif && isset($exif['Orientation'])) {
        $orientation = $exif['Orientation'];
        if ($orientation != 1) {
            $deg = 0;
            switch ($orientation) {
                case 3:
                    $deg = 180;
                    break;
                case 6:
                    $deg = 270;
                    break;
                case 8:
                    $deg = 90;
                    break;
            }
            if ($deg) {
                $img = imagerotate($img, $deg, 0);
            }            
        }
    }
    
    imagejpeg($img, $destination, $quality);
    return $destination;
}
function Tw_GetDrivingDistance($lat1, $long1, $lat2, $long2){
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
    $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

    return array('distance' => $dist, 'time' => $time);
}
function Tw_OpenZip($file_to_open, $target) {
    if(empty($file_to_open) || empty($target)){
        return false;
    }     
    $zip = new ZipArchive();
    $x = $zip->open($file_to_open);
    if($x === true) {
        $zip->extractTo($target);
        $zip->close();
         
        //unlink($file_to_open);
    } else {
        die("There was a problem. Please try again!");
    }
}
function Tw_DeleteFiles($target) {
    $files = glob($target.'/*'); // get all file names
    foreach($files as $file){ // iterate files
      if(is_file($file))
        unlink($file); // delete file
    }
}
function Tw_DeleteDirectory($dirname) {
         if (is_dir($dirname))
           $dir_handle = opendir($dirname);
     if (!$dir_handle)
          return false;
     while($file = readdir($dir_handle)) {
           if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                     unlink($dirname."/".$file);
                else
                     delete_directory($dirname.'/'.$file);
           }
     }
     closedir($dir_handle);
     rmdir($dirname);
     return true;
}
function startsWith ($string, $startString) { 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
}
function endsWith($string, $endString) { 
    $len = strlen($endString); 
    if ($len == 0)return false;     
    return (substr($string, -$len) === $endString); 
} 
//General Methods
function Tw_RegisterUser($details){
  global $models, $db, $tw;

  if(empty($details['first_name'])){
    return "~Please Enter Your First Name!";
  }else if(empty($details['last_name'])){
    return "~Please Enter Your Last Name!";
  }else if(empty($details['password'])){
    return "~Password is required!";
  }else if(strlen($details["password"]) <= 6){
        return "~Password too short!";
  }

  /*else if(!empty($details['gender']) && !in_array($details['gender'], ['male', 'female'])){
    return "~Gender not selected!";
  }*/

  else if(empty($details['mail']) && empty($details['phone_no'])){
    return "~Please enter phone number or email address!";
  }

  else if(!empty($details['mail'])){
    $details['mail'] = strtolower($details['mail']);
    if(Tw_CheckUserExists($details['mail'])){      
      return "~Email already registered!";
    }else if(!filter_var($details['mail'], FILTER_VALIDATE_EMAIL)){
      return "~Invalid email entered!";
    }
    $registerObject['mail'] = $details['mail'];
    $send = 1;
  }

  else if(!empty($details['phone_no'])){
    if(!startsWith($details['phone_no'], '+91'))
      $details['phone_no'] = '+91'.$details['phone_no'];  
    if(Tw_PhoneExists($details['phone_no'])){
      return "~Phone no already registered!";
    }else if(!Tw_IsValidPhone($details['phone_no'])){
      return "~Invalid Phone no Entered!";
    }      
    $registerObject['phone_no'] = $details['phone_no'];
    $send = 2;
  }

  $registerObject['code'] = Tw_RandomStr();
  while(Tw_VerificationCodeExists($registerObject['code']))
    $registerObject['code'] = Tw_RandomStr();

  $registerObject['avatar'] = 'uploads/images/default_avatar.png';    
  $registerObject['registered'] = time();
  $registerObject['first_name'] = $details['first_name'];
  $registerObject['last_name'] = $details['last_name'];
  $pf = empty($details['platform_details']) ? '' : $details['platform_details'];
  $registerObject['ip'] = Tw_GetIPAddress();
  $registerObject['password'] = md5($details['password']);
  $registerObject['status'] = 1;

  $user_id = $db->insert('users', $registerObject);
  $tw['user_id'] = $user_id;
  $tw['loggedin'] = true;

  //if($send == 1)
    //Tw_SendVrMail($details['mail'], $user_id, $registerObject['code']);
  //else if($send == 2)
    //Tw_SendVrSMS($details['phone_no'], $registerObject['code']);
  $session_id = Tw_CreateAppSession($user_id, 1);
  //return $registerObject['code'];
  return ['s' => $session_id, 'id' => $user_id];
}

function Tw_RegisterUserDirect($details){
  global $db;
  if(empty($details['first_name'])){
    return "~Please Enter Your First Name!";
  }else if(empty($details['last_name'])){
    return "~Please Enter Your Last Name!";
  }else if(empty($details['phone_no'])){
    return "~Please enter phone number or email address!";
  }else if(!empty($details['phone_no'])){
    if(!startsWith($details['phone_no'], '+91'))
      $details['phone_no'] = '+91'.$details['phone_no'];  
    if(Tw_PhoneExists($details['phone_no'])){
      
      $user_id = Tw_UserIdFromPhoneNumber($details['phone_no']);
      if($user_id){
        $userData = Tw_UserData($user_id);
        $login = Tw_LoginUser(['phone_no' => $details['phone_no']], false);
        return [
          'id' => $user_id,
          'first_name' => $userData['first_name'],
          'last_name' => $userData['last_name'],
          's' => $login
        ];
      }

    }else if(!Tw_IsValidPhone($details['phone_no'])){
      return "~Invalid Phone no Entered!";
    }      
    $registerObject['phone_no'] = $details['phone_no'];    
  }  
  $registerObject['code'] = Tw_RandomStr();
  while(Tw_VerificationCodeExists($registerObject['code'])){
    $registerObject['code'] = Tw_RandomStr();
  }
  $registerObject['avatar'] = 'uploads/images/default_avatar.png';    
  $registerObject['registered'] = time();
  $registerObject['first_name'] = $details['first_name'];
  $registerObject['last_name'] = $details['last_name'];
  $pf = empty($details['platform_details']) ? '' : $details['platform_details'];
  $registerObject['ip'] = Tw_GetIPAddress();  
  $registerObject['status'] = 1;
  $user_id = $db->insert('users', $registerObject);
  $tw['user_id'] = $user_id;
  $tw['loggedin'] = true;

  Tw_SendVrSMS($details['phone_no'], $registerObject['code']);
  $session_id = Tw_CreateAppSession($user_id, 1);  
  return [
    'id' => $user_id,
    's' => $session_id,
    'last_name' => $details['last_name'], 
    'first_name' => $details['first_name']
  ];
}


function Tw_VerificationCodeExists($code) {
  global $db;
  if(empty($code))return false;
  $result = $db->where('code', $code)->get('users');
  return $db->count != 0;
}

function Tw_LoginIfAcc($details){
  global $db;
  if(empty($details['phone_no']))return false;
  $user_id = Tw_UserIdFromPhoneNumber($details['phone_no']);
  if($user_id){
    $userData = Tw_UserData($user_id);
    $login = Tw_LoginUser($details, false);
    if($login)return [
      's' => $login,
      'first_name' => $userData['first_name'],
      'last_name' => $userData['last_name'],
      'id' => $user_id
    ];
    else return false;
  }else{
    return false;
  }
}
function Tw_LoginUser($details, $pass_rq = true){
  global $db;
  if(!isset($details['platform_details']))$details['platform_details'] = '';
  if(isset($details['platform']))
    $details['platform'] = 'mobile';
  else
    $details['platform'] = 'web';
  if(empty($details['phone_no'])){
    $db->where('mail', $details['mail']);
  }else {
    $phone = startsWith($details['phone_no'], '+91') ? $details['phone_no'] : '+91'.$details['phone_no'];
    $db->where('phone_no', $phone);
  }
  if($pass_rq){
    $db->where('password',  md5($details['password']));
  }  
  $result = $db->getOne('users');
  if(!empty($result)){
    $session_id = Tw_CreateAppSession($result->id, $result->status);
    return $session_id;
  }else{
    return false;
  }
}

function Tw_AuthorizeUser($user_id, $session_id){
  global $db;
  if (empty($session_id) || empty($user_id)) {
        return false;
    }
    $db->where('user_id', $user_id);
    $db->where('session_id', $session_id);
    $result = $db->get ('app_sessions');     
    if(!empty($result)){
      return false;
    }else{
      return true;
    }
}

function Tw_GetUserFromAppSession($session_id){
  global $db;
  if (empty($session_id)) {
        return false;
    }    
    $result = $db->where('session_id', $session_id)->getOne('app_sessions');   
    if(!empty($result)){
      return $result->user_id;
    }else{
      return false;
    }
}

function Tw_CreateAppSession($user_id = 0, $user_type = -1, $platform = 'android', $platform_details = '') {
    global $db;
    if (empty($user_id))return false;    
    if (empty($user_type) || $user_type == -1)$user_type = ACTIVE_USER;
    $hash = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
    $task = $db->where('session_id', $hash)->delete('app_sessions');
    if ($task) {
        if($platform != 'web'){
            $ua = $platform_details;
        } else {
            $ua = serialize(getBrowser());
        }        
        //$delete_same_session = $db
        //->where('user_id', $user_id)
        //->where('platform_details', $platform_details)
        //->delete('app_sessions');
        $task_two = $db->insert('app_sessions', [
          'user_id' => $user_id,
          'session_id' => $hash,
          'platform' => $platform,
          'platform_details' => $platform_details,
          'time' => time()
        ]);   
        if ($task_two) {
            return $hash;
        }
    }
}

function Tw_UpdatePassword($d){
  global $db;
  
}

function Tw_CheckUserExists($mail = 0){
  global $db;
  if(empty($mail))return false;  
  $result = $db->where('mail', $mail)->get('users'); 
  return $db->count > 0;
}

function Tw_PhoneExists($phone_no) {
    global $db;
    if(empty($phone_no))return false;  
    $result = $db->where('phone_no', $phone_no)->get('users'); 
    return $db->count > 0;
}

function Tw_MailExists($mail) {
    global $db;
    if (empty($mail)) {
        return false;
    }       
    $result = $db->where('mail', $mail)->get('users');
    if(empty($result)){
      return false;
    }else{
      return true;
    }
}

function Tw_UserIdForLogin($entity) {
    global $db;
    if (empty($entity)) {
        return false;
    }           
    $result = $db
    ->where('mail', $entity)
    ->orWhere('phone_no', $entity)
    ->getOne('users');
    if(empty($result)){
      return false;
    }else{
      return $result->id;
    }    
}

function Tw_UserIdFromPhoneNumber($phone_no) {
    global $db;
    if (empty($phone_no)) {
        return false;
    }
    $phone_no = startsWith($phone_no, '+91') ? $phone_no : '+91'.$phone_no;
    $r = $db->where('phone_no', $phone_no)->getOne('users');
    if(empty($r)){
      return false;
    }else{
      return $r->id;
    }
}

function Tw_UserIdFromEmail($mail) {
    global $db;
    if (empty($mail)) {
        return false;
    }    
    $r = $db->where('mail', $mail)->getOne('users');
    if(empty($r)){
      return false;
    }else{
      return $r->id;
    }
}

function Tw_GetSessionFromUserID($user_id, $time, $platform = 'android') {
    global $db;
    if (empty($user_id) || empty($time)) {
        return false;
    }

    $r = $db
    ->where('id', $user_id)
    ->where('time', $time)
    ->where('platform', $platform)
    ->getOne('app_sessions');

    if (empty($r)) {
        return false;
    }
    return $r->token;
}
function Tw_UserExistsById($user_id){  
  if (empty($user_id))return false;
  global $db;
  $db->where('id', $user_id)->get(USERS);
  return $db->count > 0;
}
function Tw_UserData($user_id) {
    global $db, $tw;
    if (empty($user_id)) {
        return false;
    }
    $data = array();    
    $r = $db->where('id', $user_id)->getOne('users'); 
    if($r){      
      $data['id'] = $r->id;
      $data['user_id'] = $r->id;    
      $data['avatar'] = $r->avatar;          
      $data['mail'] = $r->mail;
      $data['phone_no'] = $r->phone_no;
      $data['whats_no'] = $r->whats_no;
      $data['status'] = $r->status;
      $data['type'] = $r->type;
      $data['lastseen'] = Tw_TimeAgo($r->lastseen);
      $data['first_name'] = htmlspecialchars_decode($r->first_name);
      $data['last_name'] = htmlspecialchars_decode($r->last_name);
      $data['name'] = $data['first_name'] .' '.$data['last_name'];
      //$data['about'] = htmlspecialchars_decode($r->about);
      if(!empty($tw['logged_id']))$data['owner'] = $r->id == $tw['logged_id'] ? true : false;  
      else $data['owner'] = false;
      return $data;
    }else{
      return false;
    }    
}
function Tw_LoadUserBasic($user_id){
  if(empty($user_id))return false;
  global $db;
  $result = $db->where('id', $user_id)->getOne(USERS);  
  if(empty($result))return false;
  else return [
    'id' => $result->id,
    'name' => $result->first_name.' '.$result->last_name,
    'avatar' => $result->avatar
  ];
}
function Tw_UserPassword($user_id){
  if(empty($user_id))return false;
  global $db;
  $result = $db->where('id', $user_id)->getOne(USERS);  
  if(empty($result))return false;
  else return $result->password;
}
function Tw_BasicUserData($user_id) {
    global $db;
    if (empty($user_id)) {
        return false;
    }
    $data = array();    
    $r = $db->where('id', $user_id)->getOne('users'); 
    if($r){      
      $data['id'] = $r->id;      
      $data['name'] = $r->first_name .' '.$r->last_name;
      $data['level'] = $r->level;
      return $data;
    }else{
      return false;
    }    
}
function Tw_AddRating($details){
  global $db;
  $e = "~Incomplete Details";
  if(empty($details['order_id']) ||
     empty($details['user_id']) ||     
     empty($details['rating']))return $e;
  $object = [
    'user_id' => $details['user_id'],
    'order_id' => $details['order_id'],
    'content' => empty($details['content']) ? '' : json_encode($details['content']),
    'rating' => $details['rating'],
    'ftype' => $details['ftype']
  ];
  $id = $db->insert(ODR_REVIEW, $object);
  return $id ? 'Added Successfully' : "~Unable to Add Rating";
  
}
function Tw_UpdateUserData($details){
  $e = "~User Not Found";
  if(empty($details['user_id']) ||
    !Tw_UserExistsById($details['user_id'])){
    return $e;
  }
  global $db;
  $userData = Tw_UserData($details['user_id']);

  $first_name = empty($details['first_name']) ? $userData['first_name'] : $details['first_name'];
  $last_name = empty($details['last_name']) ? $userData['last_name'] : $details['last_name'];

  $object = [
    'first_name' => $first_name,
    'last_name' => $last_name    
  ];

  $result = $db->where('id', $details['user_id'])->update(USERS, $object);

  return $result ? "Updated Successfully" : $e;
}
//User Address
function Tw_AddressData($id){
  if(empty($id))return false;
  global $db;
  $result = $db->where('id', $id)->getOne(ADDRESS);
  if($result == null || !$result){
    $result = new stdClass();
    $result->address = 'Address Deleted!';
  }
  return $result;
}
function Tw_UserAddresses($user_id){
  if(empty($user_id))return false;
  global $db;
  $result = $db->where('user_id', $user_id)
  ->where('status', 1)
  ->orderBy("id","Desc")
  ->get(ADDRESS);
  $response = [];
  foreach($result as $res){
    $object = array(        
    'id' => $res->id,
    'lng' => $res->lng,
    'lat' => $res->lat,
    'user_id' => $res->user_id,
    'address' => $res->address,
    'cl_address' => $res->cl_address,
    'time' => Tw_TimeReadable2($res->time),
    'flat' => empty($res->flat) ? '' : $res->flat,
    'landmark' => empty($res->landmark) ? '' : $res->landmark
    );
    $response[] = $object;
  }
  return $response;
}
function Tw_AddUserAddress($details){
  if(empty($details))return "~Unable to add address";
  global $db;
  $object = array(        
    'lng' => $details['lng'],
    'lat' => $details['lat'],
    'user_id' => $details['user_id'],
    'address' => $details['address'],
    'cl_address' => $details['cl_address'],
    'flat' => empty($details['flat']) ? '' : $details['flat'],
    'landmark' => empty($details['landmark']) ? '' : $details['landmark']
  );
  
  $vendors = Tw_NearbyVendors($details['lat'], $details['lng'], RADIUS);

  if(empty($vendors)){
    return "~Glad To See You 😊, We Are Comming Soon In Your Place!";
  }

  if(!empty($details['id'])){
    $result = $db->insert(ADDRESS, $object);
    if($result){
      $object['id'] = $result;
      $object['time'] = Tw_TimeReadable2(time());
    }else {
      $object = false;
    }
  }else{
    $result = $db->where('id', $details['id'])->update(ADDRESS, $object);
    if($result){
      $object['id'] = $details['id'];
      $object['time'] = Tw_TimeReadable2(time());
    }else {
      $object = false;
    }
  }
  return $object ? $object : "~Error While Processing!";
}
function Tw_DelUserAddress($id){
  if(empty($id))return false;
  global $db;  
  $result = $db->where('id', $id)->update(ADDRESS, [
    'status' => 0
  ]);
  return $result ? $result : "~Error While Deleting Address!";
}
//User Address End
function Tw_SendOtp($details) {
    global $db;
    $flag = -1;
    
    if(!empty($details['phone_no'])){
      $flag = 1;
      $phone = startsWith($details['phone_no'], '+91') ? $details['phone_no'] : '+91'.$details['phone_no'];
      $user_id = Tw_UserIdFromPhoneNumber($phone);
    }else if(!empty($details['mail'])){
      $flag = 2;
      $mail = $details['mail'];
      $user_id = Tw_UserIdFromEmail($mail);
    }

    if($user_id == false || $flag == -1)return "~User Not Found";
    
    $code = Tw_RandomStr();
    while(Tw_VerificationCodeExists($code))
      $code = Tw_RandomStr();

    $db->where('id',$user_id)->update(USERS, [
      'code' => $code
    ]);

    if($flag == 1){
      if(Tw_SendVrSMS($phone, $code))
        return ['code' => $code, 'user_id' => $user_id];
      else 
        return "~Unalbe to Send OTP";
    }else if($flag == 2){
      Tw_SendVrMail($mail, $user_id, $code);
      return ['code' => $code, 'user_id' => $user_id];
    }
}

function Tw_SendOtpAny($phone_no, $issuer = 'core', $hash = false) {  
  $phone = startsWith($phone_no,'+91' ) ? $phone_no : '+91'.$phone_no;
  $code = Tw_RandomStr();
  Tw_SendVrSMS($phone, $code, $issuer, $hash);
  return $code;  
}

function Tw_ResetPassword($user_id, $new_password){
  global $db;
  if(empty($user_id) || empty($new_password))return false;
  if(strlen($new_password) < 6)return false;
  $result = $db
  ->where('id', $user_id)
  ->update(USERS, 
    array('password' => md5($new_password))
  );
  if($result){
    Tw_CreateAppSession($user_id);
    return true;
  }else{
    return false;
  }  
}
//Vendor Related Functions Start//
function Tw_CreateShop($details){
  $er = "~Error Creating Shop";
  $er1 = "~Error Updating Shop";
  if(empty($details))return $er;
  global $db;
  $UPDATE = !empty($details['id']); 
  if($UPDATE && !is_numeric($details['id']))return $er;
  $finalObject = [];
  if($UPDATE){
    if(!empty($details['r_name']))
      $finalObject['name'] = $details['r_name'];
    if(!empty($details['r_address']))
      $finalObject['address'] = $details['r_address'];
    if(!empty($details['r_state']))
      $finalObject['state'] = $details['r_state'];
    if(!empty($details['r_city']))
      $finalObject['city'] = $details['r_city'];
    if(!empty($details['r_pincode']))
      $finalObject['pincode'] = $details['r_pincode'];
    if(!empty($details['r_lat']))
      $finalObject['lat'] = $details['r_lat'];
    if(!empty($details['r_long']))
      $finalObject['long'] = $details['r_long'];
    if(!empty($details['r_owner']))
      $finalObject['owner_number'] = $details['r_owner'];
    if(!empty($details['r_contact']))
      $finalObject['manager_number'] = $details['r_contact'];
    $result = $db
    ->where('id', $details['id'])
    ->update(V_FAMILY, $finalObject);
    if($result)return ['id' => $details['id']];
    else return $er1;
  }else{
    $finalObject['name'] = $details['r_name'];
    $finalObject['address'] = $details['r_address'];
    $finalObject['state'] = $details['r_state'];
    $finalObject['country'] = $details['r_con'];
    $finalObject['city'] = $details['r_city'];
    $finalObject['pincode'] = $details['r_pincode'];
    $finalObject['lat'] = $details['r_lat'];
    $finalObject['long'] = $details['r_long'];
    $finalObject['owner_number'] = $details['r_owner'];
    $finalObject['owner'] = $details['user_id'];
    $finalObject['manager_number'] = $details['r_contact'];
    $finalObject['stage'] = 1;
    $result = $db->insert(V_FAMILY, $finalObject);
    if($result)return ['id' => $result];
    else return $er;
  }  
}
function Tw_ShopMetaData($details){
  $er = "~Error While Saving Data";  
  if(empty($details))return $er;
  global $db;
  if(!empty($details['v_type']))
    $finalObject['vendor_type'] = $details['v_type'];
  
  if(isset($details['v_day']))
    $finalObject['fullday'] = $details['v_day'];
  
  if(isset($details['v_mess']))
    $finalObject['has_mess'] = $details['v_mess'];
  
  if(!empty($details['o_time']))
    $finalObject['open_time'] = $details['o_time'];
  
  if(!empty($details['c_time']))
    $finalObject['close_time'] = $details['c_time'];

  if(isset($details['a_week']))
    $finalObject['allweek'] = $details['a_week'];

  if(!empty($details['wdays']))
    $finalObject['opendays'] = json_encode($details['wdays']);
  
  $vendorData = Tw_VendorData($details['id']);
  if($vendorData->stage < 2)$finalObject['stage'] = 2;

  $result = $db
  ->where('id', $details['id'])
  ->update(V_FAMILY, $finalObject);
  if($result)return ['id' => $details['id']];
  else return $er1;
}
function Tw_PrepareAgreement($details){
  $er = "~Error While Saving Data";  
  if(empty($details))return $er;
  global $db;
  if(!empty($details['pan_num']))
    $finalObject['pan_number'] = $details['pan_num'];
  
  if(!empty($details['pan_name']))
    $finalObject['ligal_entity'] = $details['pan_name'];
  
  if(!empty($details['pan_address']))
    $finalObject['ligal_entity_address'] = $details['pan_address'];
  
  if(!empty($details['fssai_num']))
    $finalObject['fssai_number'] = $details['fssai_num'];
  
  if(!empty($details['expairy']))
    $finalObject['fssai_expiry'] = $details['expairy'];

  if(!empty($details['acc_num']))
    $finalObject['baccount_number'] = $details['acc_num'];

  if(!empty($details['acc_ifsc']))
    $finalObject['baccount_ifsc'] = $details['acc_ifsc'];

  if(!empty($details['acc_type']))
    $finalObject['baccount_type'] = $details['acc_type'];

  $vendorData = Tw_VendorData($details['id']);
  if($vendorData->stage < 3)$finalObject['stage'] = 3;

  $result = $db
  ->where('id', $details['id'])
  ->update(V_FAMILY, $finalObject);
  if($result)return ['id' => $details['id']];
  else return $er1;
}
function Tw_UpdateVendorData($id, $object){
  global $db;
  $e = "~Unable to update data";
  if(empty($object) || empty($id))return ;
  $vendorData = Tw_VendorData($id);
  if(!$vendorData)return $e;
  $result = $db->where('id', $id)->update(V_FAMILY, $object);
  return $result ? "Updated successfully" : $e;
}
function Tw_GenerateCategory($val = ''){
  global $db;
  $formal = [];
  $originBased = [];
  $cats = $db->where('name', '%'.$val.'%','like')->get(FOOD_CAT);
  foreach($cats as $cat){
    $c = array('label' => $cat->name, 'value' => $cat->id);
    if($cat->country != 0)
      $originBased[] = $c;
    else
      $formal[] = $c;
  }
  return [
    array('label' => 'Formal', 'options' => $formal),
    array('label' => 'Origin Based', 'options' => $originBased)
  ];
}

function Tw_LoadFoodCategories($offset = 0, $limit = 100){
  global $db;
  $cats = $db->get(FOOD_CAT, Array($offset, $limit));
  return $cats;
}
function Tw_SearchCategories($key, $offset = 0, $limit = 100){
  global $db;
  $cats = $db->where("name", "%{$key}%", 'like')->get(FOOD_CAT, Array($offset, $limit));
  return $cats;
}
function Tw_AddVendorFood($details, $file = null){
  $er = "~Error Upload Food Item";
  $er1 = "~Error Editing Food Item";
  if(empty($details))return $er;
  global $db;
  $UPDATE = !empty($details['id']); 
  if($UPDATE && !is_numeric($details['id']))return $er;
  $finalObject = [];
  if($UPDATE){
    $foodData = Tw_VendorFoodData($details['id']);

    if(!$foodData)return '~Food Item not found';
    if(!empty($details['name']))
      $finalObject['name'] = $details['name'];
    if(!empty($details['about']))
      $finalObject['about'] = $details['about'];
    if(!empty($details['type']))
      $finalObject['type'] = $details['type'];
    //if(array_key_exists($details['veg']))
      $finalObject['veg'] = $details['veg'];
    if(!empty($details['cat']))
      $finalObject['cat'] = $details['cat'];
    if(!empty($details['price']))
      $finalObject['price'] = $details['price'];
    if(!empty($details['menu_price']))
      $finalObject['menu_price'] = $details['menu_price'];
    if(!empty($details['old_price']))
      $finalObject['old_price'] = $details['old_price'];
    if(!empty($details['code']))
      $finalObject['code'] = $details['code'];

    if($file != null){      
      $image = Tw_MoveMediaSeprate($file);
      if($image){        
        if(file_exists($foodData->image)){
          unlink($foodData->image);
        }
        $finalObject['image'] = $image['file'];
        $finalObject['hash'] = $image['hash'];        
      }    
    }    
    $result = $db
    ->where('id', $details['id'])
    ->update(FOOD, $finalObject);
    if($result)return Tw_VendorFoodData($details['id']);
    else return $er1;

  }else{

    $finalObject['name'] = $details['name'];
    $finalObject['about'] = $details['about'];
    $finalObject['type'] = $details['type'];
    $finalObject['veg'] = $details['veg'];
    $finalObject['cat'] = $details['cat'];
    $finalObject['price'] = $details['price'];
    $finalObject['code'] = empty($details['code']) ? 0 : $details['code'];
    $finalObject['menu_price'] = $details['menu_price'];
    $finalObject['old_price'] = 0;
    $finalObject['vendor_id'] = $details['v_id'];
    if($file != null){
      $image = Tw_MoveMediaSeprate($file);
      if($image){
        $finalObject['image'] = $image['file'];
        $finalObject['hash'] = $image['hash'];
      }      
    }
    $result = $db->insert(FOOD, $finalObject);
    if($result){
      $finalObject['id'] = $result;
      return $finalObject;
    } else {
      if($file != null && file_exists($finalObject['image']))unlink($finalObject['image']);
      return $er;
    }

  }
}
function Tw_VendorExistsById ($id) {
    global $db;
    if (empty($id))return false;    
    $data = array();    
    $r = $db->where('id', $id)->getOne(V_FAMILY); 
    return !empty($r);
}
function Tw_VendorData ($id) {
    global $db;
    if (empty($id))return false;    
    $r = $db->where('id', $id)->getOne(V_FAMILY); 
    return $r;
}
function Tw_VendorOrderData ($id) {
  global $db;
  if (empty($id))return false;    
  $r = $db->where('vendor_id', $id)->getOne(ORDER_VENDOR); 
  return $r;
}
function Tw_VendorMeta($details){
    if(empty($details['id'])){
      return "~Error";
    }    
    $type = empty($details['fdtype']) ? FD_BOTH : json_decode($details['fdtype']);
    $isuser = false;
    if(!empty($details['isuser'])){
      $isuser = true;
    }    
    $foodList = Tw_LoadVendorFood($details['id'], $type);
    $categories = array();
    $catList = array();
    $catIds = array();
    $lastCat = 0;
    $counter = 0;
    $impFood = empty($details['food_id']) ? 0 : $details['food_id'];
    $impCat = 0;
    if($foodList != false){
      foreach($foodList as $key => $food){
        if($food->cat != $lastCat && !in_array($food->cat, $catIds)){
          $lastCat = $food->cat;
          $catData = Tw_FoodCatData($lastCat);
          $categories[] = $catData;       
          $catObject = array();
          $catObject['id'] = $lastCat;
          $catObject['title'] = $catData->name;
          $catObject['data'] = array();
          $catObject['section'] = $counter;
          foreach($foodList as $fd){
              if($lastCat == $fd->cat){                
                  if($impFood == $fd->id){
                    $impCat = $fd->cat;
                  }else{
                    if($isuser && $fd->price > 50){
                      $fd->price = Tw_RaiseFoodPrice($fd->price);
                    }
                    if($fd->addon == 1){
                      $fd->addon = Tw_GetFoodAddons($fd->id);
                    }else{
                      $fd->addon = [];
                    }
                    $catObject['data'][] = $fd;
                  }                
              }
          }
          $catList[] = $catObject;
          $catIds[] = $food->cat;
          $counter++;
        }
      }
    }      
    $catList = Tw_SeqCatList($details['id'], $catList);
    $extraSection = Tw_GetExtraSection($details['id'], true);
    $merged = array_merge($extraSection, $catList);
    if($impCat != 0){
      $catIndex = -1;      
      foreach ($merged as $key => $m) {
        if($impCat == $m['id']){
          $catIndex = $key;
          break;
        }
      }
      $tempCat = $merged[$catIndex];
      $fd = Tw_VendorFoodData($impFood);
      if($isuser && $fd->price > 50){
        $fd->price = Tw_RaiseFoodPrice($fd->price);
      }
      if($fd->addon == 1){
        $fd->addon = Tw_GetFoodAddons($fd->id);
      }else{
        $fd->addon = [];
      }
      array_unshift($tempCat['data'], $fd);
      array_splice($merged, $catIndex, 1);
      array_unshift($merged, $tempCat);
    }
    $result = [     
      'categories' => $merged,      
      'photo_count' => 0,
      'review_count'  => 0,
      //'photo_count' => Tw_CountVendorPhotos($details['id']),
      //'review_count'  => Tw_CountVendorReview($details['id'])
    ];
    if(!empty($details['vendor_data'])) {
      $result['vendor'] = Tw_ModifiedVendorData($details);
    }
    if(!empty($details['areas'])) {
     $result['areas'] = Tw_LoadVendorAreas($details['id'], false);
    }
    if(!empty($details['visit_id'])) {
     $result['items'] = Tw_GetFoodOfVisit($details['visit_id']);
    }
    return $result;
}
function Tw_CountVendorReview($id){
    global $db;
    if (empty($id))return 0;
    $db->where('vendor_id', $id)->get(V_REVIEWS);
    return $db->count;
}
function Tw_SeqCatList($vendor_id, $catList, $stObj = false){
  global $db;
  $hasCatData = $db->where('vid', $vendor_id)->getOne(CAT_SEQ);
  if($hasCatData){
    $catSequence = $hasCatData->data;
    try {
      $catSequence = explode(',', $catSequence);      
      $finalSequence = [];
      foreach($catSequence as $seq){
          $idx = 0;
          foreach($catList as $cat){
              if($stObj == true){
                if($seq == $cat->id){
                    $finalSequence[] = $cat;
                    array_splice($catList, $idx, 1);
                    break;
                }
              }else{
                if($seq == $cat['id']){
                    $finalSequence[] = $cat;
                    array_splice($catList, $idx, 1);
                    break;
                }
              }              
              $idx++;
          }
      }
      $merged = array_merge($finalSequence, $catList);
      return $merged;
    }catch(Exception $e){
      return $catList;
    }    
  }else{
    return $catList;
  }
}
function Tw_ModifiedVendorData($details, $secure = false) {  
  if (empty($details))return false;  
  global $db;
  $vendor = $db->where('id', $details['id'])->getOne(V_FAMILY);
  if (empty($vendor))return false;
  $data = array();
  $data['id'] = $vendor->id;
  $data['name'] = $vendor->name;
  $data['about'] = $vendor->about;
  $data['address'] = $vendor->address;
  $data['manager_number'] = $vendor->manager_number;
  $data['owner_number'] = $vendor->owner_number;  
  $data['delivery'] = $vendor->delivery;
  if($vendor->onlyveg == 1){
    $data['onlyveg'] = 1;
  }  
  $data['long'] = $vendor->long;
  $data['lat'] = $vendor->lat;
  
  $lat2 = $details['user_lat'];
  $lon2 = $details['user_long'];
  $distance = Tw_CalDistance($vendor->lat, $vendor->long, $lat2, $lon2);
  $data['distance'] = $distance;
  $data['dr'] = Tw_ModifyDistance($distance);
  
  $data['rating'] = Tw_AverageVendorRating($vendor->id);
  if(empty($details['hide_reviews'])){
    $data['reviews'] = Tw_VendorReviews($vendor->id, 0, 12, RATING);
  }

  if($secure){
    $data['rcpxtra'] = $vendor->rcpxtra;
    $data['has_kot'] = $vendor->has_kot;
    $data['ocntm'] = $vendor->ocntm;    
    
    $data['baccount_number'] = $vendor->baccount_number;
    $data['baccount_ifsc'] = $vendor->baccount_ifsc;

    $data['manager_mail'] = $vendor->manager_mail;
    $data['pan_number'] = $vendor->pan_number;
    $data['ligal_entity'] = $vendor->ligal_entity;
    $data['ligal_entity_address'] = $vendor->ligal_entity_address;    
    $data['fssai_expiry'] = $vendor->fssai_expiry;
  }

  $data['gallery'] = Tw_GetVendorPhotos($vendor->id, 4, 0);
  $data['cover'] = $vendor->cover;
  $data['cover_hash'] = $vendor->cover_hash;
  $data['logo'] = $vendor->logo;
  $data['pincode'] = $vendor->pincode;
  $data['limitPeople'] = $vendor->limitPeople;
  $data['logo_hash'] = $vendor->logo_hash;
  $data['close_time'] = $vendor->close_time;
  $data['open_time'] = $vendor->open_time;
  if($vendor->id == 2){
    $data['closed'] = false;
  }else{
    $data['closed'] = Tw_HasClosed(
      $vendor->open_time,
      $vendor->close_time
    );
  }
  $data['time_diff'] = Tw_VendorTimeDiff($data['closed'] ? 
    $vendor->open_time : $vendor->close_time);
  $data['fssai_number'] = $vendor->fssai_number;
  $data['approved'] = $vendor->approved == 1;
  $data['safety'] = $vendor->safety == 1;
  $data['full_day'] = $vendor->fullday == 1;
  $data['allweek'] = $vendor->allweek == 1;
  $data['table_booking'] = $vendor->table_booking;
  $data['has_mess'] = $vendor->has_mess == 1;   
  
  if(!empty($details['tax'])){
    $data['hasTax'] = $vendor->hasTax;
    $data['taxes'] = Tw_GetClufterTaxes($vendor->id);
  }
  return $data;
}

function Tw_UpdatePeopleLimit($vendor_id, $limit){
  global $db;
  if(nempty($limit) || !is_numeric($limit) || empty($vendor_id) || $limit < 0){
    return '~Failed to Update';
  }
  $result = $db->where('id', $vendor_id)->update(V_FAMILY, [
    'limitPeople' => $limit
  ]);
  return $result ? 'Updated Successfully' : '~Failed to Update';
}

function Tw_UpdateSlotLimit($vendor_id, $limit){
  global $db;
  if(nempty($limit) || !is_numeric($limit) || empty($vendor_id) || $limit < 0){
    return '~Failed to Update';
  }
  $result = $db->where('id', $vendor_id)->update(V_FAMILY, [
    'slotLimit' => $limit
  ]);
  return $result ? 'Updated Successfully' : '~Failed to Update';
}

function Tw_UpdateVendorCover ($details, $file) {
    global $db;
    $e = "~Unable to update cover";
    if(empty($details['vendor_id']))return $e;
    $vendorData = Tw_VendorData($details['vendor_id']);
    if(file_exists($vendorData->cover)){
      unlink($vendorData->cover);
    }
    $image = Tw_MoveMediaSeprate($file);  
    if($image){    
      $object = [        
        'cover' => $image['file'],
        'cover_hash' => $image['hash']
      ];
      $result = $db
      ->where('id', $details['vendor_id'])
      ->update(V_FAMILY, $object);
      if($result){
        return ['image' => $image['file'], 'hash' => $image['hash']];
      }else{
        if(file_exists($image['file'])){
          unlink($image['file']);
        }
        return $e;
      }
    }else{
      return $e;
    }
}

function Tw_UpdateVendorLogo ($details, $file) {
    global $db;
    $e = "~Unable to update logo";
    if(empty($details['vendor_id']))return $e;
    $vendorData = Tw_VendorData($details['vendor_id']);
    if(file_exists($vendorData->logo)){
      unlink($vendorData->logo);
    }
    if(!$vendorData)return $e;
    $image = Tw_MoveMediaSeprate($file);  
    if($image){    
      $object = [        
        'logo' => $image['file'],
        'logo_hash' => $image['hash']
      ];
      $result = $db
      ->where('id', $details['vendor_id'])
      ->update(V_FAMILY, $object);
      if($result){
        return ['image' => $image['file'], 'hash' => $image['hash']];
      }else{
        if(file_exists($image['file'])){
          unlink($image['file']);
        }
        return $e;
      }
    }else{
      return $e;
    }
}

function Tw_DeliveryStatus ($vendor_id, $status) {
  global $db;
  if(empty($vendor_id) || !in_array($status, [0, 1]) || !Tw_VendorData($vendor_id)){
    return false;
  }
  $result  = $db->where('id', $vendor_id)->update(V_FAMILY, [
    'delivery' => $status
  ]);

  return $result ? "Updated Successfully" : "~Please Try Again later";
}

function Tw_TableBookingStatus ($vendor_id, $status) {
  global $db;
  if(empty($vendor_id) || !in_array($status, [1, 2]) || !Tw_VendorData($vendor_id)){
    return false;
  }
  $result  = $db->where('id', $vendor_id)->update(V_FAMILY, [
    'table_booking' => $status
  ]);

  return $result ? "Updated Successfully" : "~Please Try Again later";
}



function Tw_ModifiedVendorList ($details) {
  global $db;  
  if (empty($details['user_lat']) || empty($details['user_long'])){
  //$details['user_lat'] = Tw_GetLocation();
  //$details['user_long'] = Tw_GetLocation();
  }
  $list = array();
  $lat = $details['user_lat'];
  $long = $details['user_long'];
  $limit = empty($details['limit']) ? 15 : $details['limit'];
  $offset = empty($details['offset']) ? 0 : $details['offset'];
  $table = empty($details['table']) ? '' : "AND `table_booking` != 0";
  $table .= empty($details['meal_type']) ? '' : "AND `meal_type` = {$details['meal_type']} ";
  $table .= empty($details['delivery']) ? '' : "AND `delivery` = 1 ";
  if(!empty($details['only_veg'])){
    if($details['only_veg'] == VEG_FOOD){
      $table .=  "AND `onlyveg` = 1 ";
    }else{
      $table .=  "AND `onlyveg` = 0 ";
    }
  }  
  $key = empty($details['key']) ? '' : "AND `name` LIKE '%" . $details['key'] ."%' ";
  $radius_km = empty($details['radius']) ? 6 : $details['radius'];
  $query = "SELECT * from (
  SELECT *, 
            (
                (
                    (
                        acos(
                            sin(( {$lat} * pi() / 180))
                            *
                            sin(( `lat` * pi() / 180)) + cos(( {$lat} * pi() /180 ))
                            *
                            cos(( `lat` * pi() / 180)) * cos((( {$long} - `long`) * pi()/180)))
                    ) * 180/pi()
                ) * 60 * 1.1515 * 1.609344
            )
        as distance FROM `vendor_family`
    ) `vendor_family`
    WHERE distance <= {$radius_km} {$table} {$key}
    ORDER BY `distance` ASC    
    LIMIT {$limit}
    OFFSET {$offset}";
  $vendors = $db->rawQuery($query); 
  if(empty($vendors))return [];

  foreach($vendors as $vendor){
    $data = array();
    $data['id'] = $vendor->id;
    $data['name'] = $vendor->name;
    $data['about'] = $vendor->about;
    $data['lat'] = $vendor->lat;
    $data['long'] = $vendor->long;
    $data['limitPeople'] = $vendor->limitPeople;
    $data['full_day'] = $vendor->fullday == 1;
    $data['approved'] = $vendor->approved == 1;
    $data['allweek'] = $vendor->allweek == 1;
    $data['logo'] = $vendor->logo;
    if($vendor->onlyveg == 1){
      $data['onlyveg'] = 1;
    }
    $data['logo_hash'] = $vendor->logo_hash;
    $data['delivery'] = $vendor->delivery;    
    $data['has_mess'] = $vendor->has_mess == 1;
    if($vendor->id == 2){
      $data['closed'] = false;
    }else{
      $data['closed'] = Tw_HasClosed($vendor->open_time,$vendor->close_time);   
    }
    if(!empty($details['sltdta'])){      
      $data['sltav'] = count(Tw_GetAviliableSlots($vendor->id)['slots']);
      if($data['sltav'] == 0){
        $data['closed'] = true;
      }
    }
    $data['time_diff'] = Tw_VendorTimeDiff($data['closed'] ? 
    $vendor->open_time : $vendor->close_time);
    $data['table_booking'] = $vendor->table_booking;
    $data['safety'] = $vendor->safety == 1;
    if(
      isset($lat) && 
      isset($long)
    ){
      $lat2 = $details['user_lat'];
      $lon2 = $details['user_long'];
      $distance = Tw_CalDistance($vendor->lat, $vendor->long, $lat2, $lon2);
      $data['distance'] = $distance;
      $data['dr'] = Tw_ModifyDistance($distance);
    }    
    $data['rating'] = Tw_AverageVendorRating($vendor->id);
    if(empty($details['noreview'])){
      $data['reviews'] = Tw_VendorReviews(
        $vendor->id, 0, 7, RATING
      );
    }    
    $data['close_time'] = $vendor->close_time;
    $data['open_time'] = $vendor->open_time;
    $data['cover'] = $vendor->cover;
    $data['safety'] = $vendor->safety;
    $data['cover_hash'] = $vendor->cover_hash;
    $list[] = $data;
  }
  return $list;
}

function Tw_NotifyVendorUser($details){
  global $db, $tw;  
  $vendorData = Tw_VendorData($details['vendor_id']);
  $s = "Notified Successfully";
  $notify = false;
  if($vendorData){    
    $isToday = date('Ymd') == date('Ymd', $vendorData->nty_time);
    if($isToday){
      if($vendorData->ntfy_cnt < MAX_NOTIFY_COUNT){
        $db->where('id', $details['vendor_id'])->update(V_FAMILY, [
          'ntfy_cnt' => $vendorData->ntfy_cnt + 1
        ]);        
      }else{
        return "~All Notifications Used!";
      }
    }else{
      $db->where('id', $vendor_id)->update(V_FAMILY, [
        'ntfy_cnt' => 1,
        'nty_time' => time()
      ]);      
    }
    $picture = $tw['site_url'] . $vendorData->cover;
    $users = Tw_GetVendorCustomers($details['vendor_id']);
    $user_ids = [];
    foreach($users as $user){
      $user_ids[] = $user->user_id;
    }    
    if(count($user_ids) > 0){
      $notification = [
        'sender_type' => VENDOR,
        'sender_id' => $details['vendor_id'],
        'recipient_id' => $user_ids,
        'recipient_type' => USER,
        'notify_type' => CVHO,
        'title' => $details['title'],
        'content' => $details['content'],        
        'content_id' => $details['vendor_id'],      
        'notifyData' => [
          'title' => $details['title'],
          'content' => $details['content'],
          'user_ids' => $user_ids,
          'big_picture' => $picture,
          'data' => [
            'type' => CVHO,
            'type_data' => $details['vendor_id']
          ]
        ]
      ];
      Tw_RegisterNotification($notification);
    }    
    return $s;    
  }else{
    return "~Unable to notify users";
  }
}

function Tw_CheckNotifyVendor($vendor_id){
  global $db;  
  $vendorData = Tw_VendorData($vendor_id);
  if($vendorData){    
    $isToday = date('Ymd') == date('Ymd', $vendorData->nty_time);
    if($isToday){      
      return $vendorData->ntfy_cnt;
    }else{
      $db->where('id', $vendor_id)->update(V_FAMILY, [
        'ntfy_cnt' => 0,
        'nty_time' => time()
      ]);
      return 0;
    }
  }else{
    return MAX_NOTIFY_COUNT;
  }
}
function Tw_CountVendorCustomers($vendor_id){
  global $db;    
  $cnt = $db
  ->where('vendor_id', $vendor_id)
  ->getValue(VISITS, "count(DISTINCT user_id)");  
  return $cnt;
}
function Tw_GetVendorCustomers($vendor_id){
  global $db;    
  $query = "SELECT DISTINCT user_id FROM `visits` WHERE vendor_id = {$vendor_id}";
  $cnt = $db->rawQuery($query);  
  return $cnt;
}
function Tw_AverageVendorRating($vendor_id){
  if(empty($vendor_id))return false;
  global $db;
  $reviews = $db->where('vendor_id', $vendor_id)->get(V_REVIEWS);
  $total = $db->count;
  $sum = 0;
  foreach($reviews as $review)$sum += $review->rating;
  if($sum == 0)return 1.1;
  $average = sprintf("%.1f", $sum/$total);
  return $average;
}
function Tw_VendorReviews($vendor_id,$offset = 0,$limit = 7,$type = NUMBER){
  if(empty($vendor_id))return false;
  global $db;
  $db->where('vendor_id', $vendor_id);  
  if($type == RATING)$db->orderBy("rating","desc");
  $reviews = $db->get(V_REVIEWS, Array($offset, $limit));
  $list = array();
  foreach($reviews as $review){
    $data = array();
    $data['text'] = json_decode($review->text);
    $data['rating'] = $review->rating;
    $data['user'] = Tw_LoadUserBasic($review->user_id);
  }
  return $reviews;
}

function Tw_NearbyVendors($user_lat, $user_long, $radius = 9){
  $resturants = Tw_ModifiedVendorList([
    'radius' => $radius,'user_long' => $user_long,'user_lat' => $user_lat,
    'offset' => 0,'limit' => 100
  ]);
  $ids = [];
  foreach($resturants as $r)$ids[] = $r['id'];  
  return $ids;
}
function Tw_VendorFoodData($id){
  if(empty($id) || !is_numeric($id))return false;
  global $db;
  $result = $db->where('id', $id)->getOne(FOOD);
  if($result){
    if($result->menu_price == 0){
      $result->menu_price = $result->price;
    }
    return $result;
  }else{
    return false;
  }
}
function Tw_ClufterFoodData($id){
  if(empty($id) || !is_numeric($id))return false;
  global $db;
  $result = $db->where('id', $id)->getOne(CFOOD);
  if($result){        
    $result->menu_price = 0;    
    return $result;
  }else{
    return false;
  }
}
function Tw_UpdateFoodStatus($id, $status){
  $valid = [0, 1];
  if(empty($id) || !is_numeric($id) || !in_array($status, $valid)){
    return "~Unable to update food status";
  }
  global $db;
  $result = $db->where('id', $id)->update(FOOD, [
    'status' => $status
  ]);
  if($result){
    return "Status updated successfully";
  }else{
    return "~Unable to update food status";
  }
}
function Tw_DeleteVendorFood($id){
  if(empty($id) || !is_numeric($id))return false;
  global $db;
  $data = Tw_VendorFoodData($id);
  if($data){
    if(file_exists($data->image))unlink($data->image);
    $result = $db->where('id', $id)->delete(FOOD);
    return $result ? true : false;
  }else{
    return true;
  }
}

function Tw_ChangeFoodStatus($details){
  $e = "~Failed!";  
  if(empty($details['id']) || !is_numeric($details['id'])){
    return $e;
  }else if(!is_numeric($details['status'])){
    return $e;
  }  
  global $db;
  $valid = [FD_BOTH, FD_ONLYMNU, FD_ONLYDLV, FD_NONE];
  if(!in_array($details['status'], $valid)){
    return $e;
  }
  $result = $db
  ->where('id', $details['id'])
  ->update(FOOD, ['status' => $details['status']]);
  return $result ? "Updated" : $e;
}

function Tw_VendorFoodDataExists($id){
  if(empty($id) || !is_numeric($id))return false;
  global $db;
  $result = $db->where('id', $id)->getOne(FOOD);
  return $db->count != 0;
}
function Tw_LoadApprovedVendorFoods($key_word = '', $resturants, $offset = 0, $length = 15){
  if(empty($resturants))return false;
  global $db;
  if(is_array($resturants)){    
    $rs = implode(",", $resturants);
    $where_condition = " WHERE food.vendor_id IN ({$rs}) ";
  }else if(is_numeric($resturants) || is_string($resturants)){    
    $where_condition = " WHERE food.vendor_id = {$resturants} ";
  }
  $query = "SELECT * FROM food {$where_condition} AND
  approved = 1 AND
  name LIKE '%{$key_word}%'
  ORDER BY taste_rating OFFSET {$offset} LIMIT {$limit}";
  $result = $db->rawQuery($query);
  if(empty($result)){
    return [];
  }else{
    return $result;
  }
}
function Tw_LoadVendorFoods($params){  
  global $db;
  
  $key_word = empty($params['key_word']) ? '' : $params['key_word'];
  $approved = empty($params['approved']) ? ALL_FOOD : $params['approved'];
  $resturants = empty($params['vendors']) ? [] : $params['vendors'];
  $offset = empty($params['offset']) ? 0 : $params['offset'];
  $limit = empty($params['limit']) ? 10 : $params['limit'];
  $category = empty($params['cat']) ? -1 : $params['cat'];
  $type = empty($params['type']) ? -1 : $params['type'];
  $meal_type = empty($params['meal_type']) ? 0 : $params['meal_type'];
  $status = empty($params['status']) ? 0 : $params['status'];
  $user_id = empty($params['user_id']) ? 0 : $params['user_id'];
  $distinct = empty($params['distinct']) ? '' : 'group by vendor_id';
  $w = "";
  $meal_type = empty($meal_type) ? false : $meal_type;  
  if(is_array($resturants)){
    $rs = implode(",", $resturants);
    $w = " WHERE food.vendor_id IN ({$rs}) ";
  }else if(is_numeric($resturants)){    
    $w = " WHERE food.vendor_id = {$resturants} ";
  }
  
  if($category != -1){
    $z = empty($w) ? " WHERE " : " AND ";    
    if(is_array($category)){
      $cids = implode(",", $category);
      $w .= " {$z}food.cat IN ({$rs}) ";
    }else if(is_numeric($category)){    
      $w .= " {$z}food.cat = {$category} ";
    }
  }
  if($type != -1){
    $z = empty($w) ? " WHERE " : " AND ";
    $w .= " {$z}food.type = {$type} ";
  }
  $ap = '';
  $like = '';
  if($approved != ALL_FOOD){
    $z = empty($w) ? " WHERE " : " AND ";
    $ap = "{$z}APPROVED = {$approved} ";
  }
  if(!empty($key_word)){
    $z = empty($w) ? " WHERE " : " AND ";
    $like =  " {$z} name LIKE '%{$key_word}%' ";
  }  
  if($status != 0){
    if(empty($w)){
      $z = empty($ap) ? " WHERE " : " AND ";
    }else{
      $z = " AND ";
    }
    if(is_array($status)){
      $ss = implode(",", $status);
      $ap .= "{$z}status IN ({$ss}) ";
    }else{
      $ap .= "{$z}status = {$status} ";
    }    
  }
  if($meal_type){
    if(empty($w)){
      $z = empty($ap) ? " WHERE " : " AND ";
    }else{
      $z = " AND ";
    }
    $meal_type = $meal_type == 1 ? 0 : 1;
    $ap .= "{$z}VEG = {$meal_type} ";
  }
  $query = "SELECT food.id, food.name, food.cat, food.type, food.image, food.hash, food.veg, food.taster_id, food.vendor_id, food.taste_rating, food.about, food.price, food.old_price, food.approved, food.status FROM food      
      {$w} {$ap} {$like}
      {$distinct} 
      LIMIT {$limit}
      OFFSET {$offset}";
  //echo $query;
  $result = $db->rawQuery($query);
  if(empty($result)){
    return [];
  }else{
    $foods = [];
    foreach($result as $food){
      $vendorData = Tw_VendorData($food->vendor_id);
      $foodPrice = $food->price;      
      if($user_id != 0 && $foodPrice > 50){
        $foodPrice = Tw_RaiseFoodPrice($foodPrice);
      }
      $foods[] = [
         'id' => $food->id,
         'name' =>  $food->name,
         'cat' =>  $food->cat,
         'type' =>  $food->type,
         'image' =>  $food->image,
         'hash' =>  $food->hash,
         'veg' =>  $food->veg,                
         'about' =>  $food->about,
         'vendor_id' =>  $food->vendor_id,
         'price' =>  $foodPrice,
         'old_price' =>  $food->old_price,
         'menu_price' =>  empty($food->menu_price) ? $food->price : $food->menu_price,
         'approved' =>  $food->approved,
         'status' =>  $food->status,
         //Removed Rating For Load Reduction
         //'rating' =>  0,
         'restaurant_name' => $vendorData->name,
         'logo' => $vendorData->logo,
         'logo_hash' => $vendorData->logo_hash
      ];       
    }
    return $foods;
  }
}

function Tw_LoadVendorFoods2($vendor_id, $cat = 0, $status = -1, $food_ids = []){
  global $db;
  if(empty($vendor_id))return [];
  $vendorData = Tw_VendorData($vendor_id);
  $result = [];  
  if($vendorData){    
    if(!empty($cat)){
      $db->where('cat', $cat);
    }
    if(!empty($food_ids)){
      $db->where('id', $food_ids, 'IN');
    }else{
      $db->where('vendor_id', $vendor_id);
    }
    if($status != -1){
      $db->where('status', $status);
    }
    $db->orderBy('id', 'desc');
    $result = $db->get(FOOD);
    $final = [];
    foreach($result as $food){
      if($food->menu_price == 0){
        $food->menu_price = $food->price;
      }
      $final[] = $food;
    }
  }
  return $final;
}

function Tw_RegisterVendorRq($details){
  $e = '~Unable To Submit Request, Please Try Again';
  if(empty($details))return $e;
  global $db;  
  $object = [
    'name' => $details['name'],
    'phone_no' => $details['phone_no'],
    'address' => $details['address'],
    'pincode' => $details['pincode']
  ];
  $request = Tw_IsRequestExists($details['phone_no']);
  if($request){
    $result = $db->update(JOIN_REQ, $object);
    return $result ? 'Your Request Updated Successfully! 😊😊' : $e;
  }else{
    $result = $db->insert(JOIN_REQ, $object);
    return $result ? 
    'Thanks For Submitting Your Partner Request! 😊😊' : $e;
  }  
}

function Tw_IsRequestExists($phone_no){
  global $db;
  $result = $db->where('phone_no', $phone_no)->getOne(JOIN_REQ);
  return $db->count > 1 ? $result : false;   
}

function Tw_LoadUserRestaurants($id){
  if(empty($id) || !is_numeric($id))return "~";
  global $db;
  $db->where('owner', $id);  
  $result = $db
  ->orderBy("id","desc")
  ->get(V_FAMILY);
  return empty($result) ? [] : $result;
}

function Tw_LoadVendorFood($id, $type = 0, $food_ids = []){
  if(empty($id) || !is_numeric($id))return false;
  global $db;
  if(empty($food_ids)){
    $db->where('vendor_id', $id);
  }else{
    $db->where('id', $food_ids, 'IN');
  }
  if($type != 0){
    if(is_array($type)){      
      $db->where('status', $type, "IN");
    }else if(is_numeric($type)){
      $db->where('status', $type);
    }
  }
  $isuser = false;
  if(!empty($details['isuser']) && $details['isuser'] == true){
    $isuser = true;
  }
  $result = $db
  ->orderBy("id","desc")
  ->get(FOOD);

  $final = [];
  foreach($result as $food){
    if($food->menu_price == 0){
      $food->menu_price = $food->price;
    }
    if($isuser && $food->price > 50){
      $food->price = Tw_RaiseFoodPrice($food->price);
    }
    $final[] = $food;
  }

  return $final;
}

function Tw_LoadVendorFdSug($vendor_id, $food_id = -1, $type = 0){
  if(empty($vendor_id) || empty($food_id) || !is_numeric($vendor_id) || !is_numeric($food_id))return [];
  global $db;
  if($type != 0){
    if(is_array($type)){
      $db->where('status', $type, "IN");
    }else if(is_numeric($type)){
      $db->where('status', $type);
    }
  }
  $result = $db
  ->where('vendor_id', $vendor_id)
  ->where('id', $food_id, "!=")  
  ->orderBy("id","desc")
  ->get(FOOD, Array(0, 6));

  $final = [];
  foreach($result as $food){
    if($food->menu_price == 0){
      $food->menu_price = $food->price;
    }
    if($food->price > 50){
      $food->price = Tw_RaiseFoodPrice($food->price);
    }
    $final[] = $food;
  }

  return $final;
}
//Explore Related
function Tw_SearchClufterKeys($details){  
  $radius = empty($details['radius']) ? 15 : $details['radius'];
  $user_lat = $details['user_lat'];
  $key = $details['key'];
  $user_long = $details['user_long'];
  $categories = Tw_SearchCategories($key, 0, 4);
  $resturants = Tw_ModifiedVendorList([
    'radius' => $radius,'user_long' => $user_long,'user_lat' => $user_lat,
    'offset' => 0,'limit' => 300
  ]);
  $svendor = Tw_ModifiedVendorList(['radius' => $radius,'user_long' => $user_long,
    'user_lat' => $user_lat,'offset' => 0,'limit' => 15,'key' => $key
  ]);
  $ids = [];
  foreach($resturants as $r)$ids[] = $r['id'];
  $sFoods = Tw_LoadVendorFoods([
    'key_word' => $key, 
    'approved' => ALL_FOOD,
    'vendors' => $ids,
    'offset' => 0,
    'limit' => 7,
    'status' => [FD_BOTH,FD_ONLYDLV]
  ]);
  $keys = [];  
  foreach($sFoods as $food){
    $keys[] = [
      'key' => $food['name'], 
      'id' => $food['id'], 
      'image' => $food['image'], 
      'type' => FD
    ];
  }
  foreach($categories as $cat){
    $keys[] = [
      'id' => $cat->id, 
      'type' => CAT,
      'key' => $cat->name,
      'image' => $cat->image
    ];
  }
  foreach($svendor as $vn){    
    $keys[] = [
      'key' => $vn['name'],
      'image' => $vn['logo'],
      'id' => $vn['id'],
      'type' => VENDOR
    ];
  }
  return $keys;
}

function Tw_SearchVendorKeys($details){  
  $radius = empty($details['radius']) ? 15 : $details['radius'];
  $user_lat = $details['user_lat'];
  $key = $details['key'];
  $user_long = $details['user_long'];  
  $vendors = Tw_ModifiedVendorList(['radius' => $radius,'user_long' => $user_long,
    'user_lat' => $user_lat,'offset' => 0,'limit' => 7,'key' => $key
  ]);
  $keys = [];
  foreach($vendors as $vn){
    $keys[] = [
      'key' => $vn['name'],
      'id' => $vn['id'],
      'image' => $vn['logo'],
      'type' => VENDOR
    ];
  }
  return $keys;
}

function Tw_SearchFoods($details){  
  $radius = empty($details['radius']) ? 5 : $details['radius'];
  $user_id = 0;
  if(!empty($details['isuser']) == true && !empty($details['user_id'])){
    $user_id = $details['user_id'];
  }
  $user_lat = $details['user_lat'];
  $key = $details['key'];
  $sFoods = [];
  $foodType = empty($details['allFood']) ? FOOD_APPROVED : ($details['allFood'] ? ALL_FOOD : FOOD_APPROVED);
  $user_long = $details['user_long'];
  $meal_type = empty($details['meal_type']) ? 0 : $details['meal_type'];
  $fdtype = empty($details['fdtype']) ? 0 : json_decode($details['fdtype']);
  if(empty($details['type'])){
    $resturants = Tw_ModifiedVendorList([
      'radius' => $radius,'user_long' => $user_long,'user_lat' => $user_lat,
      'offset' => 0,'limit' => 100
    ]);
    $ids = [];
    foreach($resturants as $r)$ids[] = $r['id'];
    if(empty($details['cats'])){
      $cats = -1;
    }else{
      $cats = json_decode($details['cats']);
      if(!$cats || empty($cats))$cats = -1;      
    }   
    $sFoods = Tw_LoadVendorFoods([
      'key_word' => $key,
      'approved' => $foodType,
      'vendors' => $ids,
      'offset' => 0,
      'limit' => 19,
      'cat' => $cats,
      'user_id' => $user_id,
      'distinct' => true,
      'type' => -1,
      'user_id' => $user_id,
      'meal_type' => $meal_type,
      'status' => $fdtype
    ]);
  }else{    
    $type = $details['type'];
    $type_id = $details['type_id'];
    if($type == VENDOR){      
      $sFoods = Tw_LoadVendorFoods([
        'key_word' => $key, 
        'approved' => $foodType, 
        'vendors' => $type_id, 
        'user_id' => $user_id,
        'offset' => 0, 
        'limit' => 19, 
        'cat' => -1, 
        'type' => -1,
        'distinct' => true,
        'meal_type' => $meal_type, 
        'status' => $fdtype
      ]);
    }elseif($type == CAT){
      $resturants = Tw_ModifiedVendorList([
        'radius' => $radius,'user_long' => $user_long,'user_lat' => $user_lat,
        'offset' => 0,'limit' => 100
      ]);
      $ids = [];
      foreach($resturants as $r)$ids[] = $r['id'];  
      $sFoods = Tw_LoadVendorFoods([
        'key_word' => "",
        'approved' => $foodType, 
        'vendors' => $ids, 
        'offset' => 0,
        'user_id' => $user_id,  
        'limit' => 19, 
        'cat' => $type_id, 
        'type' => -1,
        'distinct' => true,
        'meal_type' => $meal_type, 
        'status' => $fdtype
      ]);
    }
  }
  return $sFoods;
}

function Tw_SearchVendors($details){  
  $radius = empty($details['radius']) ? 5 : $details['radius'];
  $meal_type = empty($details['meal_type']) ? 0 : $details['meal_type'];
  $user_lat = $details['user_lat'];
  $key = $details['key'];
  $user_long = $details['user_long'];
  $object = [
    'radius' => $radius,
    'user_long' => $user_long,
    'key' => $key,
    'user_lat' => $user_lat,
    'meal_type' => $meal_type,
    'offset' => 0,
    'table' => empty($details['table']) ? 0 : 1,
    'limit' => 15
  ];  
  $sVendor = Tw_ModifiedVendorList($object);  
  return $sVendor;
}
//Vendor Related Functions End//

//Vendor Food Related Categories //
function Tw_FoodCatData($id){
    if(empty($id))return false;
    global $db;    
    $check = $db->where('id', $id)->getOne(FOOD_CAT);
    if($check)return $check;
    else return false;    
}
function Tw_VendorAddFoodCats($cat_name){
    if(empty($cat_name))return "~Unable Add Category";
    global $db;
    
    $check = $db->where('name', $cat_name)->get(FOOD_CAT);
    if($db->count != 0)return "Added Successfully";

    $result = $db->insert(FOOD_CAT, array(
       'name' => $cat_name,
       'state' => 0,
       'country' => 0
    ));
    
    if(empty($result))return "~Unable Add Category";
    else return "Added Successfully";
}
function Tw_VendorDeleteFoodCat($cat_id){
  if(empty($cat_id))return "~Unable To Delete";
  global $db;
  $result = $db->where('id', $cat_id)->delete(FOOD_CAT);
  if(empty($result))return "~Unable To Delete";
  else return "Delete Successfully";
}

function Tw_GetFoodAddons($food_id){
  if(empty($food_id)){
    return [];
  }
  global $db;
  $groups = $db->where('food_id', $food_id)->where('status', 1)->get(ADN_GROUP);
  $final = [];
  foreach ($groups as $group) {
    $data = $db->where('gid', $group->id)->get(ADN_LIST);
    if(count($data) > 0){
      $group->data = $data;
      $final[] = $group;
    }    
  }
  return $final;
}

function Tw_ProcessCatAction($status, $cat_id, $vendor_id){
  global $db;
  $db
  ->where('vendor_id', $vendor_id)
  ->where('cat', $cat_id)
  ->update(FOOD, ['status' => $status]);
  return 'Updated!';
}

function Tw_FormalizeCart($food_ids, $address_id, $user_id){
  $e = "~Unable to place order";
  if(empty($food_ids) && !is_array($food_ids) || empty($address_id) && empty($user_id)){
    return $e;
  }
  $vendors = array();
  $notFound = array();
  $closedVendors = array();
  $result = array();
  $foods = array();  
  foreach($food_ids as $food){
    $foodData = Tw_VendorFoodData($food['id']);    
    if(!$foodData){
     $notFound[] = $food['id'];
     $foodData = new stdClass();
     $foodData->active = false;
    }else{      
      $foodData->active = true;
      $foodData->quantity = $food['quantity'];
      if($foodData->addon == 1){
        $foodData->addon = Tw_GetFoodAddons($foodData->id);
      }
    }
    if($foodData->status != FD_BOTH && $foodData->status != FD_ONLYDLV){
      $notFound[] = $food['id'];
    }
    $foods[] = $foodData;
    if(!in_array($foodData->vendor_id, $vendors))
      $vendors[] = $foodData->vendor_id;
  }
  $address = Tw_AddressData($address_id);
  if(!$address)return "~Adderess";  
  foreach($vendors as $vendor){
    $v = Tw_ModifiedVendorData([
      'id' => $vendor,
      'user_long' => $address->lng,
      'user_lat' => $address->lat,
    ]);    
    $hotelToCust = $v['distance'];
    $rObj = [
      'v_name' => $v['name'],
      'v_dr' => $v['dr'],      
      'data' => []
    ];
    if($v['closed'] || $v['delivery'] == 0){   
      $rObj['closed'] = true;
      $rObj['time_diff'] = $v['time_diff'];
      $rObj['delivery'] = $v['delivery'];
      $rObj['v_id'] = $v['id'];
      $v['closed'] = true;
      $closedVendors[] = $v['id'];
    }
    foreach($foods as $food){      
      if($food->vendor_id == $vendor){
        if($food->price > 50){
          $food->price = Tw_RaiseFoodPrice($food->price);
        }
        $food->closed = $v['closed'];
        $food->out_stock = in_array($food->id, $notFound);
        $rObj['data'][] = $food;
      }            
    }
    $result[] = $rObj;    
  }
  $reward = Tw_ReferCliam($user_id);
  $deliveryFee = Tw_CalFaireCost($hotelToCust);
  $response = [
    'n_fd' => $notFound,
    'm_vr' => $closedVendors,
    'cart' => $result,
    'reward' => $reward,
    'dlv_fee' => $deliveryFee
  ];
  if(HAS_COD == 0){
    $response['nocod'] = true;
  }
  return $response;
}
function Tw_CalFaireCost($distance){
  if($distance <= BASE_DISTANCE){
    return BASE_FAIRE_COST;
  }else{
    $extraDistance = $distance - BASE_DISTANCE;
    $extraCost = $extraDistance * EXTRA_KM_COST;
    return round(BASE_FAIRE_COST + $extraCost); 
  }
}
function Tw_OrderData($order_id){
  if(empty($order_id))return;
  global $db;
  $result = $db->where('id', $order_id)->getOne(ORDERS);
  return $result ? $result : false;
}

function Tw_PlaceOrder($details){
  $e = ['s' => 400, 'm' => "~Unable to place order"];
  if(empty($details['food_ids']) 
    && !is_array($details['food_ids']) 
    || empty($details['address_id']) 
    && empty($details['user_id'])
  )return $e;
  global $db;
  $vendors = array();
  $err = false;
  $notFound = array();
  $closedVendors = array();
  $foodIds = array();
  $ctime = time();
  $totalRaise = 0;
  $paid = $details['pay_method'] == COD ? 1 : 0;
  foreach($details['food_ids'] as $food){
    $isCF = empty($details['cf']) == false;
    if($isCF){
      $foodData = Tw_ClufterFoodData($food['id']);
      $foodData->price = $foodData->cprice;
    }else{
      $foodData = Tw_VendorFoodData($food['id']);
    }
    if(!$foodData){
     $err = true;
     $notFound[] = $food['id'];
    }else{
      if($foodData->status != FD_BOTH && $foodData->status != FD_ONLYDLV){
        $err = true;
        $notFound[] = $food['id'];
      }
      $foodData->ramt = 0;
      if($isCF){
        $foodData->ramt = $foodData->cprice - $foodData->vprice;
      }else if($foodData->price > 50){
        $foodData->ramt = Tw_CalRaiseAmt($foodData->price);
      }
      $idx = objectIndexInArray($vendors, 'vendor_id', $foodData->vendor_id);
      if($idx == -1){
        $ramt = $foodData->ramt * intval($food['quantity']);
        if(empty($food['adn'])){
          $foodAmount = $foodData->price * intval($food['quantity']);
        }else{
          $foodAmount = $food['fdamt'] - $ramt;
        }
        $vendors[] = [
          'vendor_id' => $foodData->vendor_id,        
          'amount' => $foodAmount,
          'ramt' => $ramt,
          'paid' => $paid,
          'time' => time()
        ];
      }else{
        $ramt = $foodData->ramt * intval($food['quantity']);
        if(empty($food['adn'])){
          $foodAmount = $foodData->price * intval($food['quantity']);          
        }else{
          $foodAmount = $food['fdamt'] - $ramt;
        }
        $vendors[$idx]['amount'] = $vendors[$idx]['amount'] + $foodAmount;
        $vendors[$idx]['ramt'] = $ramt + $vendors[$idx]['ramt'];
      }
    }    
    $dta = [       
       'quantity' => $food['quantity'],
       'amount' => $foodData->price,
       'pramt' => $foodData->ramt,
       'vendor_id' => $foodData->vendor_id,
       'adn' => empty($food['adn']) ? '[]' : $food['adn']
    ];
    if(empty($details['cf'])){
      $dta['food_id'] = $food['id'];
    }else{
      $dta['cfood_id'] = $food['id'];
    }
    $foodIds[] = $dta;    
  }
  
  if($err){
   return array('s' => 700, 'ids' => $notFound);#Food Not Found
  }

  $address = Tw_AddressData($details['address_id']);
  if(!$address)return ["s" => 400, "m" => "Address not found"];  
  foreach($vendors as $vendor){    
    $v = Tw_ModifiedVendorData([
      'id' => $vendor['vendor_id'],
      'user_long' => $address->lng,
      'user_lat' => $address->lat,
    ]);
    $hotelToCust = $v['distance'];
    $totalRaise += $vendor['ramt'];
    if($v['closed']){
      $err = true;
      $closedVendors[] = $v['id'];
    }
  }
  $deliveryFee = Tw_CalFaireCost($hotelToCust);
  if($err)return array('s' => 800, 'ids' => $closedVendors);#Vendor Closed  
  $total = $details['amount'] + $deliveryFee + $details['d_praise'];
  $txn_dta = [
    'user_id' => $details['user_id'],
    'issuer' => PAY_FOOD,
    'amount' => $total
  ];
  $total -= $totalRaise;
  if($details['pay_method'] == COD)$txn = ['order_id' => '', 'token' => ''];
  else if($details['pay_method'] == ONLINE_PAY)$txn = Tw_CreateRazorOrder($txn_dta);
  else return array('s' => 900, 'msg' => "Invalid Payment Method");#Payment Error  

  $address = Tw_AddressData($details['address_id']);  
  $multi = count($vendors) > 1;
  $center_id = 0;
  if($multi){
    $vd = Tw_VendorData($vendors[0]['vendor_id']);
    $center_id = Tw_CenterOfRoad($vd->road_id);
  }
  
  $finalOrder = array(
    'user_id' => $details['user_id'],    
    'status' => FOOD_NOT_PREPARED,
    'time' => time(), //Time When To Deliver
    'e_time' => 0, //Expected Time in Minutes
    'address_id' => $details['address_id'],
    'amount' => $details['amount'],
    'total' => $total,
    'pay_method' => $details['pay_method'],
    'paid' => $paid, 
    'ramt' => $totalRaise,
    'delivery_fee' => $deliveryFee,
    'd_praise' => $details['d_praise'],
    'multi' => $multi,
    'center_id' => $center_id
  );

  /*if(!empty($details['reward_id'])){
    $finalOrder['reward_id'] = $details['reward_id'];    
    Tw_CreateClaim($user_id, Tw_GetReferPoints($user_id));
  }
  if(!empty($details['frd_code'])){
    $sender_id = Tw_GetSenderFromCode($details['frd_code']);
    if($sender_id){
      Tw_CreateReferral($sender_id, $user_id);
    }    
  }*/
  
  if($txn['order_id'] != ''){
    $finalOrder['txn_id'] = $txn['order_id'];
  }
  if(!empty($details['anyreq'])){
    $finalOrder['anyreq'] = $details['anyreq'];
  }
  if(!empty($details['altnum'])){
    $finalOrder['altnum'] = $details['altnum'];
  }
  if(!empty($details['surge_amt'])){
    $finalOrder['surge_amt'] = $details['surge_amt'];
  }
  $result = $db->insert(ORDERS, $finalOrder);
  foreach($vendors as $key => $vendor){    
    $vendors[$key]['billno'] = Tw_GenerateBillNr($ctime, $vendor['vendor_id']);
    $vendors[$key]['order_id'] = $result;
  }

  if($details['pay_method'] == ONLINE_PAY){
    $db->where('id', $txn['order_id'])->update(PAYMENTS, ['data_id' => $result]);
  }else{
    Tw_ProcessOrderNotify($vendors, $details['user_id'], $center_id);
  }
  
  foreach($foodIds as $key => $foodId){    
    $foodIds[$key]['stamp'] = time();
    $foodIds[$key]['order_id'] = $result;
  }

  $db->insertMulti(ORDER_VENDOR, $vendors);
  $db->insertMulti(ORDER_FOOD, $foodIds);
  $txn['order_id'] = empty($txn['order_id']) ? $result : $txn['order_id'];

  if($result) return [
    'token' => empty($txn['token']) ? '' : $txn['token'],
    'order_str' => 'ODR_'.$txn['order_id'],
    'order_id' => $txn['order_id'],
    'id' => $result,
    's' => 200];
  else return $e;
}
function Tw_ProcessOrderNotify($vendors, $user_id = 0, $center_id = 0){    
  $web_ids = [];
  $app_ids = [];
  $title = 'You Have A New Order!';
  $content = 'Press Here to Accept Order';
  foreach ($vendors as $vendor){
    $vendorData = Tw_VendorData($vendor['vendor_id']);
    if($vendorData->notify == NOTIFY_WEB){
      $web_ids[] = $vendorData->id;
    }else if($vendorData->notify == NOTIFY_APP){
      $app_ids[] = $vendorData->id;
    }else{
      $app_ids[] = $vendorData->id;
      $web_ids[] = $vendorData->id;
    }    
  }  
  if(count($web_ids) > 0){
    Tw_RegisterWebPush([
      'title' => $title,
      'content' => $content,
      'vendor_ids' => $web_ids
    ]);
  }
  if(count($app_ids) > 0){
    $notification = [
      'sender_type' => USER,
      'sender_id' => $user_id,
      'recipient_id' => $app_ids,
      'recipient_type' => VENDOR,
      'notify_type' => USORV,
      'title' => $title,
      'content' => $content,    
      'content_id' => 0,
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'vendor_ids' => $app_ids,
        'data' => [
          'type' => USORV,
          'type_data' => 0
          ]
        ]
    ];    
    Tw_RegisterNotification($notification);   
  }
    
}

function Tw_UserOrders($user_id){
  if(empty($user_id))return [];
  global $db;  
  return $db
  ->where('user_id', $user_id)
  ->where('status', HAS_DELIVERED, '<')
  ->where('paid', 1)
  ->get(ORDERS);
}

//Floor & Table Management START
function Tw_CreateVendorArea($details){
  if(empty($details['vendor_id']) ||
    !Tw_VendorExistsById($details['vendor_id'])){
    return "~Partner Not Found";
  }else if(empty($details['area']) && Tw_VendorAreaExists($details['area'],
    $details['vendor_id'])){
    return "~Area Already Exists";
  }
  global $db;
  if(empty($details['id'])){
    $data = [
      'area' => $details['area'],
      'shusr' => empty($details['shusr']) ? false : true,
      'vendor_id' => $details['vendor_id']
    ];
    if(!empty($details['name']))$data['name'] = $details['name'];
    $result = $db->insert(V_AREA, $data);
    if($result)return [
      'id' => $result,
      'vendor_id' => $details['vendor_id'],
      'shusr' => empty($details['shusr']) ? false : true,
      'area' => $details['area']      
    ];
  }else{
    if(empty($details['area']))return "Updated Successfully!";
    $result = $db
    ->where('id', $details['id'])
    ->update(V_AREA, [
      'area' => $details['area'],
      'shusr' => !empty($details['shusr']) && ($details['shusr'] != 'false' || $details['shusr'] == false) ? true : false
    ]);
    if($result)return [
      'id' => $details['id'],
      'area' => $details['area'],
    ];
  }
  return "~Please Try Again Later";
}

function Tw_CreateVendorTable($details){
  if(empty($details['vendor_id']) ||
    !Tw_VendorExistsById($details['vendor_id'])){
    return "~Partner Not Found";
  }else if(!Tw_VendorAreaExistsById($details['area_id'])){
    return "~Invalid Area";
  }
  global $db;
  $db
  ->where('area_id', $details['area_id'])
  ->where('sp_idx', 0)
  ->where('tkaway', 0)
  ->get(V_TABLE);
  $data = [    
    'area_id' => $details['area_id'],
    'number' => $db->count + 1
  ];
  $result = $db->insert(V_TABLE, $data);
  if($result)return [
    'id' => $result,    
    'area_id' => $details['area_id'],
    'number' => $data['number'],
    'status' => TBL_FREE,
    'visit_id' => 0,
    'tkaway' => 0,
    'sp_idx' => 0,
    'sp_id' => 0,
  ];
  return "~Please Try Again Later";
}

function Tw_DeleteVendorArea($id){
  global $db;
  if(empty($id) || !is_numeric($id))return true;
  //Check For Bookings Pending 
  //Check For Order Pending
  //Check For Other Things
  $result = $db->where('id', $id)->delete(V_AREA);
  $db->where('area_id', $id)->delete(V_TABLE);
  return $result ? true : "~Error";
}

function Tw_VendorAreaExists($area, $v_id){  
  if(empty($area) || empty($v_id))return "~Invalid Data Provided";
  global $db;
  $db
   ->where('area', $area)
   ->where('v_id', $v_id)
   ->get(V_AREA);
  return $db->count > 0;
}

function Tw_VendorAreaExistsById($id){  
  if(!is_numeric($id) || empty($id))return false;
  global $db;
  $db
   ->where('id', $id)   
   ->get(V_AREA);  
  return $db->count > 0;
}

function Tw_LoadVendorAreas($vendor_id, $show_all = true){
  if(empty($vendor_id))return [];
  global $db;
  $db->where('vendor_id', $vendor_id);
  if($show_all == false){
    $db->where('shusr', 1);
  }
  $result = $db->get(V_AREA);
  return empty($result) ? [] : $result;
}

function Tw_DeleteVendorTable($id){
  global $db;
  if(empty($id) || !is_numeric($id))return true;
  //Check Before Delete
  $result = $db->where('id', $id)->delete(V_TABLE);  
  return $result ? "Deleted" : "~Error";
}

function Tw_LoadVendorTables($area_id, $showSplit = true, $tkaway = true){
  if(empty($area_id))return [];
  global $db;
  if(!$showSplit){
    $db->where('sp_id', 0);
    $db->where('tkaway', 0);
  }
  if(!$tkaway){    
    $db->where('tkaway', 0);
  }
  $tables = $db
  ->where('area_id', $area_id)
  ->get(V_TABLE);
  $lmtTime = strtotime("-15 minutes", time());
  $final = [];  
  foreach($tables as $table){
    $count = Tw_CountTableBookings($table->id, $lmtTime);
    if($table->visit_id != 0){
      $visitData = Tw_VisitData($table->visit_id);
      if($visitData){
        $table->byqr = $visitData->byqr;
      }      
    }
    $table->booked = $count == 0 ? false : $count;
    if($table->tkaway){
      array_unshift($final, $table);
    }else{
      $final[] = $table;
    }
  }
  return $final;
}

function Tw_LoadVendorTkAways($area_id){
  if(empty($area_id))return [];
  global $db;  
  $tables = $db
  ->where('area_id', $area_id)
  ->where('tkaway', 1)
  ->get(V_TABLE);
  $lmtTime = strtotime("-15 minutes", time());
  $final = [];  
  foreach($tables as $table){    
    $table->booked = false;
    $final[] = $table;
  }
  return $final;
}

function Tw_TableData($id){
  if(empty($id))return false;
  global $db;
  return $db->where('id', $id)->getOne(V_TABLE);  
}

function Tw_AreaData($id){
  if(empty($id))return false;
  global $db;
  return $db->where('id', $id)->getOne(V_AREA);  
}

function Tw_UpdateTableStatus($details){
  $valid = [TBL_FREE,TBL_PRESENT,TBL_UNPAID,TBL_PAID];
  $e = "~Unable to update table status";
  $billNo = 0;
  global $db;
  if(empty($details['table_id'])){
    return $e;
  }
  if(nempty($details['status']) || 
    !in_array($details['status'], $valid)
  ){
    return $e;
  }
  if($details['status'] == TBL_PRESENT && empty($details['visit_id'])){
    return $e;
  }
  if($details['status'] == TBL_PRESENT){
    $start = strtotime("-15 minutes", time());
    $end = strtotime("+15 minutes", time());
    $db
    ->where('status', ASSIGNED)
    ->where('table_id', $details['table_id'])
    ->where('from_time', Array($start, $end), "BETWEEN")
    ->get(TABLE_BOOKING);
    if($db->count > 0){
      return "~You Have Table Booking At This Time!";
    }
    $data = [
      'status' => $details['status'],
      'visit_id' => $details['visit_id'],
      'time' => time(), 'updated' => time()
    ];
  }else if($details['status'] == TBL_FREE){
    $discount = empty($details['discount']) ? 0 : $details['discount'];      
    $tableData = Tw_TableData($details['table_id']);
    $visitData = Tw_VisitData($tableData->visit_id);
    $xamt = empty($details['xamt']) ? 0 : $details['xamt'];  
    $xtitle = empty($details['xtitle']) ? '0' : $details['xtitle'];  
    if($tableData->tkaway == 1){
      Tw_TransferTkAway($tableData, $discount, [
        'xtitle' => $xtitle,
        'xamt' => $xamt
      ]);
      return "Updated";
    }
    if(!empty($details['pmy'])){
      $payMethod = $details['pmy'];      
      if($visitData->pmy == PAY_BOTH){
        if($payMethod == PAY_ONLINE){
          $db->where('id', $tableData->visit_id)->update(VISITS, ['pmy' => PAY_ONLINE]);
        }        
      }else{
        $db->where('id', $tableData->visit_id)->update(VISITS, ['pmy' => $details['pmy']]);
      }
    }
    if($tableData->sp_id != 0){
      $data = ['table_id' => $tableData->sp_id];
      if($discount > 0 || $xamt > 0){        
        $total_amt = $visitData->total_amt;
        if($xamt > 0){          
          $data['xamt'] = $xamt;
          $data['xamt'] = $xtitle;
          $total_amt += $xamt;
        }
        if($discount > 0){
          $data['discount'] = $discount;          
          $total_amt -= $discount; 
        }    
      }
      $data['total_amt'] = $total_amt;
      $db->where('id', $tableData->id)->delete(V_TABLE);
      $db->where('id', $tableData->visit_id)->update(VISITS, $data);
      return "Updated!";
    }
    if($discount > 0 || $xamt > 0){  
      $extra = array();      
      $total_amt = $visitData->total_amt;
      if($xamt > 0){          
        $extra['xamt'] = $xamt;
        $extra['xamt'] = $xtitle;
        $total_amt += $xamt;
      }
      if($discount > 0){
        $extra['discount'] = $discount;          
        $total_amt -= $discount; 
      }
      $extra['total_amt'] = $total_amt;     
      $db->where('id', $tableData->visit_id)->update(VISITS, $extra);    
    }
    $tableData = Tw_TableData($details['table_id']);
    $visitData = Tw_VisitData($tableData->visit_id);
    if($visitData->billno == 0){      
      $billNo = Tw_GenerateBillNr(time(), $visitData->vendor_id);
      $db->where('id', $visitData->id)->update(VISITS, [
        'billno' => $billNo
      ]);
    }else{
      $billNo = $visitData->billno;
    }
    $data = ['status' => TBL_FREE,'visit_id' => 0,'updated' => time()];   
  }else{
    if($details['status'] == TBL_PAID){
      $tableData = Tw_TableData($details['table_id']);
      $visitData = Tw_VisitData($tableData->visit_id);
      if($visitData->billno == 0){
        $billNo = Tw_GenerateBillNr(time(), $visitData->vendor_id);
        $db->where('id', $visitData->id)->update(VISITS, [
          'billno' => $billNo
        ]);
      }else{
        $billNo = $visitData->billno;
      }
    }  
    $data = ['status' => $details['status'], 'updated' => time()];
  } 
  $result = $db   
   ->where('id', $details['table_id'])   
   ->update(V_TABLE, $data);
  return $result ? ['bn' => $billNo] : $e;
}
function  Tw_GetTableReceipt($details, $withTax = true){  
  if(empty($details['table_id'])){
    return [];
  }
  global $db;
  $tb = Tw_TableData($details['table_id']);
  if($tb){
    $final = [];    
    $data = $db
      ->where('visit_id', $tb->visit_id)
      ->get(VISIT_FOOD);    
    foreach($data as $item){
      $food = Tw_VendorFoodData($item->food_id);
      $item->name = $food->name;
      $item->per_price = $food->menu_price;
      if(empty($item->note))unset($item->note);
      $final[] = $item;
    }
    if($withTax && $tb->visit_id != 0){
      $visitData = Tw_VisitData($tb->visit_id);
      if($visitData->tax == 1){
          $taxes = Tw_GetVisitTaxes($tb->visit_id);    
          foreach($taxes as $t){    
            $t->tax = 1;
            $final[] = $t;
          }   
      }
    }    
    return $final;
  }else{
    return [];
  }  
}
function  Tw_GetVisitReceipt($visit_id){  
  if(empty($visit_id))return [];  
  global $db;    
  $final = [];
  $data = $db->where('visit_id', $visit_id)->get(VISIT_FOOD);
  foreach($data as $item){
    $food = Tw_VendorFoodData($item->food_id);
    $item->name = $food->name;
    $item->per_price = $food->menu_price;
    if(empty($item->note))unset($item->note);
    $final[] = $item;
  }
  $visitData = Tw_VisitData($visit_id);
  if($visitData->tax == 1){
      $taxes = Tw_GetVisitTaxes($visit_id);    
      foreach($taxes as $t){    
        $t->tax = 1;
        $final[] = $t;
      }   
  }
  return $final;
}
function  Tw_GetTkAwayReceipt($ta_id){  
  if(empty($ta_id))return [];  
  global $db;    
  $final = [];
  $data = $db->where('ta_id', $ta_id)->get(TA_FOODS);
  foreach($data as $item){
    $food = Tw_VendorFoodData($item->food_id);
    $item->name = $food->name;
    $item->per_price = $food->menu_price;
    if(empty($item->note))unset($item->note);
    $final[] = $item;
  }    
  $taxes = Tw_TkAwayTaxes($ta_id);    
  foreach($taxes as $t){    
    $t->tax = 1;
    $final[] = $t;
  }      
  return $final;
}
function Tw_SplitTable($table_id){
  global $db;
  if(empty($table_id)){
    return false;
  }
  $tableData = Tw_TableData($table_id);
  $sp_index = Tw_CountSplitedTable($table_id);
  $splitData = [        
    'status' => TBL_FREE,
    'area_id' => $tableData->area_id,
    'sp_id' => $tableData->id,
    'sp_num' => $tableData->number,
    'sp_idx' => $sp_index,
    'number' => $tableData->number,
    'visit_id' => 0,
    'time' => time(),
    'updated' => time()
  ];
  $id = $db->insert(V_TABLE, $splitData);
  $splitData['id'] = $id;
  return $splitData;
}

function Tw_CountSplitedTable($table_id){
  global $db;
  if(empty($table_id)){
    return 0;
  }
  $data = $db->where('sp_id', $table_id)->orderBy('id', 'desc')->get(V_TABLE);
  if($db->count > 0){
    return $data[0]->sp_idx + 1;
  }else{
    return 1;
  }
}
//Floor & Table Management END

function Tw_SendVrSMS($mobile_number, $otp, $issuer = 'core', $hash = false){
  if($hash == false){
    if($issuer == 'core'){
      $hash = SMS_HASH_ANDROID;
    }else if($issuer == 'vendor'){
      $hash = VENDOR_HASH_ANDROID;
    }
  }
  $body = '<#> Your Clufter Verification OTP is '.$otp.' '.$hash;
  $result = Tw_SendSMS(array('to'=> $mobile_number, 'body'=> $body));  
  return $result;
}


function Tw_SendSMS ($post_body) {
  $sid = 'AC56503261de5ece351ce3c12f2aca6d0f';
  $token = '5957a5508b0a4fd8ab2c55c0d01a586f';
  $twilio = new Client($sid, $token);

  $message = $twilio->messages
              ->create(
                $post_body['to'],
                [
                  "body" => $post_body['body'],
                  "from" => "+15183207762"
                ]
              );
  return true;
}

function Tw_SendVrMail($address, $user_id, $otp){
  global $tw;    
  try {
      $link = $tw['site_url'].'verify.php?fbv='.base64_encode($otp.'-'.$user_id);
      $mail = new PHPMailer;
      $mail->isSMTP();
      $mail->SMTPDebug = 0;
      $mail->Host = $tw['smtp_host'];
      $mail->Port = $tw['smtp_port'];
      $mail->SMTPAuth = true;
      $mail->Username = $tw['smtp_user'];
      $mail->Password = $tw['smtp_pass'];
      $mail->setFrom($tw['smtp_user'], 'FoodBazzar');  
      $mail->addAddress($address);      
      $mail->isHTML(true);
      $mail->Subject = 'Account Verification';
      $mail->Body    = 'Your Verification link for food bazzar is <a href="'.$link.'">Verify</a> or copy <br /> '.$link;
      $mail->AltBody = 'Your Verification link for Clufter is '.$link;
      $mail->send();    
  } catch (Exception $e) {
      die('Error '.$e);
  }
}
//Plate Functions 
function Tw_LoadPlateModes(){
  global $db;
  return $db->get(PLT_MODES);
}

function Tw_AddPlateMode($name){
  global $db;
  $name = strtolower($name);
  $db->where('name', $name)->get(PLT_MODES);
  if($db->count > 0)return "~Already exists!";
  $result = $db->where('name', $name)->insert(PLT_MODES, ['name' => $name]);
  return $result ? $result : "~Unable to insert";
}

function Tw_UpdatePlateMode($id, $name){
  global $db;
  $name = strtolower($name);
  $db->where('id', $id)->get(PLT_MODES);
  if($db->count == 0)
    return "~Plate Mode Not Found!";  
  else
    $result = $db->where('name', $name)->update(PLT_MODES, ['name' => $name]);
  return $result ? ['id' => $id, 'name' => $name] : "~Unable to update";
}

function Tw_DeletePlateMode($id){
  global $db;  
  $db->where('mode', $id)->delete(PLATES);
  $db->where('id', $delete)->update(PLT_MODES);
  return true;
}

function Tw_GetPlateByMode($mode){
  if(empty($mode))return [];
  global $db;
  $plates = $db->where('mode_id', $mode)->get(PLATES);
  return $plates ? $plates : [];
}

function Tw_GetPlateById($id){
  if(empty($id))return false;
  global $db;
  $plate = $db->where('id', $id)->getOne(PLATES);
  return $plate ? $plate : false;
}

function Tw_FormalizePlateMode($details){  
  if(empty($details['mode_id']) || empty($details['user_lat']) || empty($details['user_long']))
    return [];
  global $db;
  $plates = Tw_GetPlateByMode($details['mode_id']);
  $final = [];

  $vendors = Tw_NearbyVendors($details['user_lat'], $details['user_long'], RADIUS);  
  if(empty($vendors)){
    $data = "Glad To See You 😊, We Are Comming Soon In Your Place!";
    Tw_HeaderExit(300, $data);
  }

  foreach($plates as $plate){
    $object = [
      'id' => $plate->id,
      'title' => $plate->name
    ];
    $foods = [];
    if(!empty($plate->food_type)){
      $foods = Tw_LoadVendorFoods([
        'key_word' => "",
        'approved' => ALL_FOOD,
        'vendors' => $vendors,
        'offset' => 0,
        'limit' => 7,
        'user_id' => $details['user_id'],
        'cat' => -1,
        'type' => $plate->food_type,
        'meal_type' => 0,
        'status' => [FD_BOTH, FD_ONLYDLV]
      ]);
    }else if(!empty($plate->cat_ids)){
      $ids = explode(',', $plate->cat_ids);
      $foods = Tw_LoadVendorFoods([
        'key_word' => "",
        'approved' => ALL_FOOD,
        'vendors' => $vendors,
        'user_id' => $details['user_id'],
        'offset' => 0,
        'limit' => 7,
        'cat' => $ids,
        'type' => -1,
        'meal_type' => 0,
        'status' => [FD_BOTH,FD_ONLYDLV]
       ]);
    }
    $object['data'] = $foods;
    $final[] = $object;
  }
  return $final;
}

function Tw_PagPlateById($details){
  global $db;
  if(empty($details['plate_id']) || empty($details['user_lat']) || empty($details['user_long']))return [];  
  $foods = [];
  $offset = empty($details['offset']) ? 0 : $details['offset'];
  $limit = empty($details['limit']) ? 9 : $details['limit'];
  $plate = Tw_GetPlateById($details['plate_id']);
  $vendors = Tw_NearbyVendors($details['user_lat'], $details['user_long']);
  if(!empty($plate->food_type)){
      $foods = Tw_LoadVendorFoods([
        'key_word' => "",
        'approved' => ALL_FOOD,
        'vendors' => $vendors,
        'offset' => 0,
        'limit' => 7,
        'cat' => -1,
        'type' => $plate->food_type,
        'meal_type' => 0,
        'status' => [FD_BOTH, FD_ONLYDLV]
      ]);
  }else if(!empty($plate->cat_ids)){
      $ids = explode(',', $plate->cat_ids);
      $foods = Tw_LoadVendorFoods([
        'key_word' => "",
        'approved' => ALL_FOOD,
        'vendors' => $vendors,
        'offset' => $offset,
        'limit' => $limit,
        'cat' => $ids,
        'type' => -1,
        'meal_type' => 0,
        'status' => [FD_BOTH,FD_ONLYDLV]
      ]);
  }
  return $foods;
}

//Orders Functions For Vendors
function Tw_GetVendorOrders($vendor_id, $type = 1, $time = 0){
  global $db;
  $db->where('vendor_id', $vendor_id);
  if($type == 1){
    $db->where('status', HAS_PICKED_F, '<');    
    $db->where('paid', 1);
  }else if($type == 2){
    $db
    ->where('status', HAS_PICKED_F, '>=')
    ->where('status', VDRFDCANCEL, '!=');
  }else if($type == 3){
    $db->where('status', VDRFDCANCEL);
  }else if($type == 4){
    $db->where('status', FOOD_NOT_PREPARED);
    $db->where('paid', 1);
  }else if($type == 5){
    $db->where('status', [FOOD_ACCEPT, DELIVERY_FN_PREPARED], "IN");
  }else if($type == 6){
    $db
    ->where('status', FOOD_ACCEPT, '>')
    ->where('status', HAS_PICKED_F, '<=')
    ->where('status', VDRFDCANCEL, '!=');
  }
  if($time != 0){
    $start = strtotime(date('Y-m-d', $time).'00:00:00');
    $end = strtotime(date('Y-m-d', $time).'23:59:59');
    $db->where('time',  Array($start, $end), "BETWEEN");
  }
  if($type == 2){
    $db->orderBy('id', 'desc');
  }
  $records = $db->get(ORDER_VENDOR);
  $finalOrders = [];  
  foreach ($records as $record){        
    $finalOrders[] = Tw_FormatOrderData($record);
  }
  return $finalOrders;
}
function Tw_GetVOrderHistory($details){
  if(empty($details['vendor_id']) || empty($details['date'])){
    return "~Invalid Request!";
  }
  global $db;
  $start = strtotime(date('Y-m-d', $details['date']).'00:00:00');
  $end = strtotime(date('Y-m-d', $details['date']).'23:59:59');  
  $limit = empty($details['limit']) ? 10 : $details['limit'];
  $offset = empty($details['offset']) ? 0 : $details['offset'];
  $records = $db
  ->where('time',  Array($start, $end), "BETWEEN")  
  ->where('vendor_id', $details['vendor_id'])
  ->where('paid', 1)
  ->get(ORDER_VENDOR, Array($offset, $limit));
  $finalOrders = [];  
  foreach ($records as $record){              
    $finalOrders[] = Tw_FormatOrderData($record);
  }
  return $finalOrders;
}
function Tw_CalVOrderHistory($details){
  if(empty($details['vendor_id']) || empty($details['date'])){
    return "~Invalid Request!";
  }
  global $db;
  $start = strtotime(date('Y-m-d', $details['date']).'00:00:00');
  $end = strtotime(date('Y-m-d', $details['date']).'23:59:59');  
  $total = 0;$cancel = 0;$accept = 0;
  $records = $db
  ->where('time',  Array($start, $end), "BETWEEN")
  ->where('vendor_id', $details['vendor_id'])
  ->get(ORDER_VENDOR);
  foreach($records as $record){
      $fts = Tw_GetFoodsByOrderId($record->vendor_id, $record->order_id);
      $amount = 0;
      foreach ($fts as $ft)$amount += $ft['price'];
      if($record->status != VDRFDCANCEL){
          $accept++;
          $total += $amount;
      }else{
          $cancel++;
      }
  }
  return ['total' => $total,'cancel' => $cancel,'accept' => $accept];
}
function Tw_FormatOrderData($record){
    $orderData = Tw_OrderData($record->order_id);    
    $userData = Tw_UserData($orderData->user_id);
    $addressData = Tw_AddressData($orderData->address_id);
    $foodItems = Tw_GetFoodsByOrderId($record->vendor_id, $record->order_id);
    $amount = 0;
    foreach ($foodItems as $foodItem)$amount += $foodItem['price'];
    $order = [
      'id' => $orderData->id,
      'vo_id' => $record->id,
      'order_code' => Tw_OrderCode($record->order_id),      
      'status' => $record->status,
      'time' =>  Tw_TimeHumanType($record->time),
      'timestamp' => $record->time,
      'time_left' => intval($record->time),
      'paid' => $record->paid,
      'multi' => $orderData->multi,
      'food_items' => $foodItems,
      'amount' => $amount,
      'customer' => [
        'first_name' => $userData['first_name'],
        'last_name' => $userData['last_name'],
        'address' => '',
        'phone' => $userData['phone_no']
      ]
    ];
    return $order;
}
function Tw_CountPendingOrders($vendor_id){
  global $db;
  $records = $db
  ->where('vendor_id', $vendor_id)
  ->where('paid', 1)
  ->where('status', HAS_PICKED_F, '<')
  ->get(ORDER_VENDOR);
  return $db->count;
}
function Tw_GetFoodsByOrderId($vendor_id, $order_id, $raise = false, $addon = false){
  if(empty($order_id))return false;
  global $db;  
  if($vendor_id != -1){
    $db->where('vendor_id', $vendor_id);
  }
  $db->where('order_id', $order_id);
  $foodIds = $db->get(ORDER_FOOD);
  $foodItems = [];
  foreach($foodIds as $food){
    if($food->food_id == 0){
      $temp = Tw_ClufterFoodData($food->cfood_id);
    }else{
      $temp = Tw_VendorFoodData($food->food_id);
    }
    if($raise){
      $price = ($food->pramt + $food->amount) * $food->quantity;
    }else{
      $price = $food->amount * $food->quantity;
    }
    $item = [
      'name' => $temp->name,
      'image' => $temp->image,
      'hash' => $temp->hash,
      'price' => $price,
      'pramt' => $food->pramt,
      'per_price' => $food->amount,
      'adn' => $food->adn,
      'quantity' => $food->quantity
    ];
    $foodItems[] = $item;
  }
  return $foodItems;
}

function Tw_GetVendorsOfOrder($order_id){
  if(empty($order_id))return false;
  global $db;
  $vendors = $db->where('order_id',$order_id)->get(ORDER_VENDOR);
  $finalVendors = [];
  foreach($vendors as $vendor){
    $object = Tw_VendorData($vendor->vendor_id);
    $object->status = $vendor->status;
    $object->ttc = $vendor->ttc;
    $object->hero_id = $vendor->hero_id;
    $object->acptm = $vendor->acptm;    
    $finalVendors[] = $object;  
  }
  return $vendors ? $finalVendors : [];
}
function Tw_UpdateVendorOrderStatus($details){
  global $db;
  $e = "~Unable to update order";
  if(
    empty($details['vo_id']) || !is_numeric($details['vo_id']) ||
    empty($details['status'])|| !is_numeric($details['status'])
  )return $e;
  $vo_id = $details['vo_id'];
  $status = $details['status'];
  $allowed = [FOOD_NOT_PREPARED, VDRFDCANCEL, DELIVERY_F_PREPARED, FOOD_ACCEPT,FOOD_PREPARED];
  $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
  $orderData = Tw_OrderData($check->order_id);
  $vendorData = Tw_VendorData($check->vendor_id);  
  if(!$vendorData)return "~Restaurant Not Found";
  if(!$orderData)return "~Order Not Found";
  if(!in_array($status, $allowed))return "~Invalid Order Status";
  $voData = $db
  ->where('id', $vo_id)  
  ->getOne(ORDER_VENDOR);

  if($voData->status == DELIVERY_FN_PREPARED && $status == FOOD_PREPARED){
    $status = DELIVERY_F_PREPARED;
  }

  $dataToUpdate = ['status' => $status]; 

  if($status == FOOD_PREPARED || $status == DELIVERY_F_PREPARED){    
    $dataToUpdate['ckt'] = round((time() - $voData->time) / 60);
  }

  if($status == FOOD_ACCEPT && !empty($details['ttc'])){
    $dataToUpdate['ttc'] = $details['ttc'];
    $dataToUpdate['acptm'] = time();
  }

  $result = $db
  ->where('id', $vo_id)  
  ->update(ORDER_VENDOR, $dataToUpdate);  
  if($status == VDRFDCANCEL){
    if($orderData->multi == 0){
      $db->where('id', $orderData->id)->update(ORDERS, [
        'status' => VDRFDCANCEL
      ]);
    }    
    $content = "{$vendorData->name} is not able deliver food now";
    $title = $orderData->pay_method == COD ? "Don't Care Trying Ordering Same, From Other Hotel!" : "😔😔 Sorry, Your Money Will Refunded Shortly, {$vendorData->name} is not able to deliver food now";
    $notification = [
      'sender_type' => VENDOR,
      'sender_id' => $check->vendor_id,
      'recipient_id' => $orderData->user_id,
      'recipient_type' => USER,
      'notify_type' => VNUSOS,
      'title' => $title,
      'content' => $content,      
      'content_id' => $check->order_id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$orderData->user_id],     
        'data' => [
          'type' => VNUSOS,
          'type_data' => $check->order_id
        ]
      ]
    ];
    Tw_RegisterNotification($notification);
    if($orderData->pay_method != COD){
     $refund = Tw_CreateRefund([
      'amount' => intval($orderData->amount) + $orderData->delivery_fee + $orderData->d_praise,      
      'is_partial' => true,
      'type' =>  VDRFDCANCEL,
      'txn_id' => $orderData->txn_id,
      'issuer' => PAY_FOOD,
      'issuer_id' => $orderData->id,
      'user_id' => $orderData->user_id,
      'reason' => ''
     ]); 
    }    
  }
  if($status == FOOD_PREPARED || $status == DELIVERY_F_PREPARED){
    $content = "Your Food At {$vendorData->name} is Prepared";
    $title = 'Food Prepared 🙂🙂';
    $notification = [
      'sender_type' => VENDOR,
      'sender_id' => $check->vendor_id,
      'recipient_id' => $orderData->user_id,
      'recipient_type' => USER,
      'notify_type' => VNUSOS,
      'title' => $title,
      'content' => $content,      
      'content_id' => $check->order_id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$orderData->user_id],     
        'data' => [
          'type' => VNUSOS,
          'type_data' => $check->order_id
        ]
      ]
    ];
    Tw_RegisterNotification($notification);
  } 
  if($status == FOOD_ACCEPT && $check->status == FOOD_NOT_PREPARED){    
    Tw_DispatchToTookan([
          'id' => $orderData->id,
          'pay_method' => $orderData->pay_method,
          'total' => $orderData->amount + $orderData->delivery_fee + $orderData->d_praise,          
          'user_id' => $orderData->user_id,
          'address_id' => $orderData->address_id,
          'vendor_id' => $check->vendor_id,
          'altnum' => $orderData->altnum,
          'delivery_fee' => $orderData->delivery_fee
    ]);
    /*$hero_ids = [];    
    foreach (Tw_HeroOfRoad($vendorData->road_id) as $hero){
      $hero_ids[] = $hero->user_id;
    }    
    if($orderData->multi == 1){
      $center_id = Tw_CenterOfRoad($vendorData->road_id);
      if($center_id != 0){
        $e = Tw_HeroOfCenter($center_id);
        foreach ($e as $d){
          $hero_ids[] = $d->user_id;
        }
      }
    }    
    if(count($hero_ids) > 0){      
      $title = "You Have New Order";
      $content = "From {$vendorData->name}";
      $notification = [
        'sender_type' => USER,
        'sender_id' => $orderData->user_id,
        'recipient_id' => $hero_ids,
        'recipient_type' => DLH,
        'notify_type' => USORV,
        'title' => $title,
        'content' => $content,
        'content_id' => 0,      
        'notifyData' => [
          'title' => $title,
          'content' => $content,
          'hero_ids' => $hero_ids,        
          'data' => [
            'type' => USORV,
            'type_data' => 0
          ]
        ]
      ];
      Tw_RegisterNotification($notification);
    }*/
  } 
  return $result ? $status : $e;
}
function Tw_DispatchToTookan($data){
  $tookan = new Tookan();
  $task = new stdClass();  
  $task->order_id = $data['id'];
  $task->hotel = new stdClass();
  $task->cust = new stdClass(); 
  $task->pay_mode = $data['pay_method'] == COD ? 'COD' : 'ONLINE';
  $task->cust_note = '';
  $task->altnum = $data['altnum'];
  $task->total = $data['total'];
  $task->delivery_fee = $data['delivery_fee'];
  $task->foods = [];
  $foodItems = Tw_GetFoodsByOrderId($data['vendor_id'], $data['id']);
  foreach($foodItems as $food){
    $item = new stdClass();    
    $item->name = $food['name'];
    $item->quantity = $food['quantity'];
    $item->price = $food['price'];
    $task->foods[] = $item;
  }
  $userData = Tw_UserData($data['user_id']);
  $addressData = Tw_AddressData($data['address_id']);
  $vendorData = Tw_VendorData($data['vendor_id']);    
  $task->hotel->name = $vendorData->name;
  $task->hotel->address = $vendorData->address;
  $task->hotel->phone = $vendorData->manager_number;
  $task->hotel->lat = $vendorData->lat;
  $task->hotel->lng = $vendorData->long; 
  $task->cust->name = $userData['name'];
  $task->cust->address = $addressData->address;
  $task->cust->cl_address = $addressData->cl_address;
  $task->cust->phone = $userData['phone_no'];
  $task->cust->lat = $addressData->lat;
  $task->cust->lng = $addressData->lng;
  $task->cust->landmark = $addressData->landmark;
  $task->cust->flat = $addressData->flat;
  $tookan->createMultiTask($task);
}
function Tw_GetVendorOrderFeedback($vendor_id, $order_id){
  return [];
}
function Tw_GetDeliveryPath($vendor_id, $order_id){
  return [];
}
function Tw_GetReasonForCancel($order_id){
  return [];
}
//Order Function For Delivery Boy
function Tw_GetOrdersForDelivery($hero_id){
  global $db;
  $road_id = Tw_DHeroData($hero_id)['road_id'];
  $vendors = Tw_VendorsOnRoad($road_id);
  if(empty($vendors))return [];  
  $hasCurrent = Tw_GetCurrentDelivery($hero_id);
  if($hasCurrent)return Tw_DeliveryOrderData($hasCurrent);
  $result = $db
  ->where('hero_id', 0)
  ->where('vendor_id', $vendors, 'IN')
  ->where('status', FOOD_NOT_PREPARED, '>')
  ->where('status', DELIVERY_F_PREPARED, '<')
  ->get(ORDER_VENDOR);  
  $final = [];
  foreach($result as $record)$final[] = Tw_DeliveryOrderData($record);
  return $result ? $final : [];
}
function Tw_GetOrdersForCenterer($hero_id){
  global $db;
  $center_id = Tw_DHeroData($hero_id)['center_id'];
  $centerData = Tw_CenterData($center_id);

  $hasCurrent = $db  
  ->where('chero_id', $hero_id)
  ->where('center_id', $center_id)  
  ->where('status', HAS_DELIVERED, '<')
  ->getOne(ORDERS);  
  if($hasCurrent){
    $dlData = Tw_CDeliveryOrderData($hasCurrent);
    $dlData['center'] = $centerData;
    return $dlData;
  }

  $result = $db  
  ->where('chero_id', 0)
  ->where('paid', 1)
  ->where('center_id', $center_id)  
  ->where('status', HAS_DELIVERED, '<')
  ->get(ORDERS);

  $final = [];
  foreach($result as $record){    
    $data = Tw_CDeliveryOrderData($record);
    $data['center'] = $centerData;
    $final[] = $data;
  }

  return $result ? $final : [];
}
function Tw_DeliveryOrderData ($record) {      
    $orderData = Tw_OrderData($record->order_id);        
    $userData = Tw_UserData($orderData->user_id);
    $addressData = Tw_AddressData($orderData->address_id);    
    $foodItems = Tw_GetFoodsByOrderId($record->vendor_id, $record->order_id);
    $vendorData = Tw_VendorData($record->vendor_id);
    $vendorPhotos = Tw_GetVendorPhotos($record->vendor_id, 4, 0);    
    $order = [
      'id' => $orderData->id,
      'vo_id' => $record->id,
      'order_code' => Tw_OrderCode($record->order_id),      
      'status' => $record->status,
      'time' =>  Tw_TimeHumanType($record->time),
      'timestamp' => $record->time,      
      'paid' => $record->paid,
      'food_items' => $foodItems,
      'multi' => $orderData->multi,
      'pay_method' => $orderData->pay_method,
      'total' => $orderData->amount + $orderData->d_praise + $orderData->delivery_fee,
      'praise' => $orderData->d_praise,
      'customer' => [
        'first_name' => $userData['first_name'],
        'last_name' => $userData['last_name'],
        'address' => $addressData->cl_address,
        'landmark' => $addressData->landmark,
        'flat' => $addressData->flat,        
        'phone' => $userData['phone_no'],
        'lat' => $addressData->lat,
        'lng' => $addressData->lng
      ],
      'vendor' => [
        'name' => $vendorData->name,
        'address' => $vendorData->address,
        'phone' => $vendorData->manager_number,
        'lat' => $vendorData->lat,
        'lng' => $vendorData->long,
        'photos' => $vendorPhotos,
      ]
    ];    
    return $order;
}
function Tw_CDeliveryOrderData ($orderData) {      
    $userData = Tw_UserData($orderData->user_id);
    $addressData = Tw_AddressData($orderData->address_id);
    $foodItems = Tw_GetFoodsByOrderId(-1, $orderData->id);
    $amount = 0;
    foreach ($foodItems as $foodItem){
      $amount += $foodItem['price'];
    }
    $order = [
      'id' => $orderData->id,      
      'order_code' => Tw_OrderCode($orderData->id),      
      'status' => $orderData->status,
      'time' =>  Tw_TimeHumanType($orderData->time),
      'timestamp' => $orderData->time,      
      'paid' => $orderData->paid,
      'food_items' => $foodItems,
      'multi' => $orderData->multi,
      'amount' => $amount,
      'customer' => [
        'first_name' => $userData['first_name'],
        'last_name' => $userData['last_name'],
        'address' => $addressData->address,
        'phone' => $userData['phone_no'],
        'lat' => $addressData->lat,
        'lng' => $addressData->lng
      ]
    ];    
    return $order;
}
function Tw_GetCurrentDelivery($hero_id){
  if(empty($hero_id))return false;
  global $db;
  $result = $db
  ->where('hero_id', $hero_id)  
  ->where('status', HAS_CENTERED, '<')
  ->getOne(ORDER_VENDOR);
  return $result ? $result : false; 
}
function Tw_GetCurrentCenterD($hero_id){
  if(empty($hero_id))return false;
  global $db;
  $result = $db
  ->where('chero_id', $hero_id)  
  ->where('status', HAS_DELIVERED, '<')
  ->getOne(ORDER_VENDOR);
  return $result ? $result : false; 
}
function Tw_DHeroData($user_id){
  if(empty($user_id))return false;
  global $db;
  $isHero = $db->where('user_id', $user_id)->getOne(HERO);
  if($isHero){
    $userData = Tw_UserData($user_id);
    if(!$userData)return false;
    $userData['road_id'] = $isHero->road_id;
    $userData['center_id'] = $isHero->center_id;
    $userData['fleet_id'] = $isHero->fleet_id;
    $userData['lat'] = $isHero->lat;
    $userData['lng'] = $isHero->long;
    return $userData;
  }else{
   return false;
  }
}
function Tw_GetDHeroFromPhone($phone_no){
  if(empty($phone_no))return false;
  global $db;
  $phone_no = startsWith($phone_no, '+91') ? $phone_no : '+91'.$phone_no;
  $isUser = $db->where('phone_no', $phone_no)->getOne(USERS);
  if($isUser){
    $data = Tw_DHeroData($isUser->id);
    if($data)return $data;
    else $data;
  }else{
   return false;
  }
}

function Tw_GetHPIN($hero_id){
  if(empty($hero_id))return false;
  global $db;  
  $data = $db->where('hero_id', $hero_id)->getOne(HPINS);
  return md5($data->pin);
}

function Tw_VendorsOnRoad($road_id){
  global $db;
  if(empty($road_id))return [];
  $result = $db->where('road_id', $road_id)->get(V_FAMILY);
  $ids = [];
  foreach($result as $vendor)$ids[] = $vendor->id;
  return $ids;
}
function Tw_RoadsOfCenter($center_id){
  global $db;
  if(empty($center_id))return [];
  $result = $db->where('center_id', $center_id)->get(ROADS);  
  return $result;
}
function Tw_HeroOfRoad($road_id){
  global $db;
  if(empty($road_id))return [];
  $result = $db->where('road_id', $road_id)->get(HERO);  
  return $result;
}
function Tw_HeroOfCenter($center_id){
  global $db;
  if(empty($center_id))return [];
  $result = $db->where('center_id', $center_id)->get(HERO);  
  return $result;
}
function Tw_CenterOfRoad($road_id){
  global $db;
  if(empty($road_id))return 0;
  $result = $db->where('id', $road_id)->getOne(ROADS);
  return $result ? $result->center_id : 0;
}
function Tw_CenterData($center_id){
  global $db;
  if(empty($road_id))return 0;
  $result = $db->where('id', $road_id)->getOne(CENTERS);
  return $result ? $result : 0;
}
function Tw_StartDelivery($vo_id, $hero_id){
  global $db;
  $e = "~Unable to update order";
  if(
    empty($vo_id) || !is_numeric($vo_id) ||
    empty($hero_id) || !is_numeric($hero_id)    
  )return $e;    
  $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
  if($check->hero_id != 0){
    return "~AA";
  }
  $orderData = Tw_OrderData($check->order_id);
  $vendorData = Tw_VendorData($check->vendor_id);
  if(!$vendorData)return "~Restaurant Not Found";
  if(!$orderData)return "~Order Not Found";
  $status = $check->status == FOOD_PREPARED ? DELIVERY_F_PREPARED : DELIVERY_FN_PREPARED;
  $result = $db
  ->where('id', $vo_id)  
  ->update(ORDER_VENDOR, ['hero_id' => $hero_id, 'status' => $status]);
  $title = "Our Hero is On Way To {$vendorData->name}";
  $content = 'Now You Can Call Now 📞 Our Delivery Hero';
  $notification = [
    'sender_type' => DLH,
    'sender_id' => $hero_id,
    'recipient_id' => $orderData->user_id,
    'recipient_type' => USER,
    'notify_type' => DHNUS,
    'title' => $title,
    'content' => $content,
    'content_id' => $vo_id,      
    'notifyData' => [
      'title' => $title,
      'content' => $content,
      'user_ids' => [$orderData->user_id],
      'data' => [
        'type' => DHNUS,
        'type_data' => $vo_id
      ]
    ]
  ];
  Tw_RegisterNotification($notification);

  return $result ? 'Order updated successfully' : $e;
}
function Tw_StartCenterDelivery($order_id, $hero_id){
  global $db;
  $e = "~Unable to update order";
  if(
    empty($order_id) || !is_numeric($order_id) ||
    empty($hero_id) || !is_numeric($hero_id)    
  )return $e;
  if(!Tw_AllOrderCentered($order_id)){
    return "~All Food Not Arrived Yet";
  }    
  $o = Tw_OrderData($order_id);
  if(!$o)return "~Order Not Found";    
  if($o->chero_id != 0){
    return "~AA";
  }
  $result = $db
  ->where('id', $order_id)  
  ->update(ORDERS, ['chero_id' => $hero_id]);  
  return $result ? 'Order updated successfully' : $e;
}
function Tw_PickDelivery($vo_id, $hero_id, $status){
  global $db;
  $e = "~Unable to update order";
  if(
    empty($vo_id) || !is_numeric($vo_id) ||
    empty($hero_id) || !is_numeric($hero_id)    
  )return $e;    
  $allowed = [HAS_PICKED_F, HAS_PICKED_C];
  #if($status == HAS_PICKED_C){
    #Notify Center Point About it
  #}
  $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
  $orderData = Tw_OrderData($check->order_id);  
  $vendorData = Tw_VendorData($check->vendor_id);
  if(!in_array($check->status, [FOOD_PREPARED,DELIVERY_F_PREPARED])){
    return "~Food Not Prepared Yet!";
  }
  if(!$vendorData)return "~Restaurant Not Found";
  if(!$orderData)return "~Order Not Found";
  if(!in_array($status, $allowed))return "~Invalid Order Status";
  $title = "Picked From {$vendorData->name}";
  $content = "Our Hero Has Picked Food From {$vendorData->name}";
  $notification = [
    'sender_type' => DLH,
    'sender_id' => $hero_id,
    'recipient_id' => $orderData->user_id,
    'recipient_type' => USER,
    'notify_type' => DLPC,
    'title' => $title,
    'content' => $content,
    'content_id' => $check->order_id,      
    'notifyData' => [
      'title' => $title,
      'content' => $content,
      'user_ids' => [$orderData->user_id],
      'data' => [
        'type' => DLPC,
        'type_data' => $check->order_id
      ]
    ]
  ];
  Tw_RegisterNotification($notification);

  $result = $db
  ->where('id', $vo_id)  
  ->update(ORDER_VENDOR, ['hero_id' => $hero_id, 'status' => $status]);  
  return $result ? 'Order updated successfully' : $e;
}
function Tw_UpdateOrderProgress($order_id, $progress){
  global $db;
  $data = Tw_OrderData($order_id);
  if($data){
    $db->where('id', $order_id)->update(ORDERS, [
      'progress' => $progress
    ]);    
  }else{
    return "~Order Does Not Exist";
  }
}
function Tw_FinalDelivery($vo_id, $hero_id, $status, $order_id){
  global $db;
  $e = "~Unable to update order";
  if(
    empty($vo_id) || !is_numeric($vo_id) ||
    empty($hero_id) || !is_numeric($hero_id)    
  )return $e;
  $allowed = [HAS_CENTERED, HAS_DELIVERED];
  #if($status == HAS_CENTERED){
    #Notify Center Point About it
  #}
  if($vo_id == -1){
    $orderData = Tw_OrderData($order_id);
    if(!$orderData)return "~Order Not Found";
    if($status != HAS_DELIVERED)return "~Invalid Order Status"; 
    $result = $db
    ->where('id', $order_id)
    ->update(ORDERS, ['status' => $status, 'paid' => 1]);
    $result = $db
    ->where('order_id', $order_id)
    ->update(ORDER_VENDOR, ['status' => $status, 'paid' => 1]);
  }else{
    $check = $result = $db->where('id', $vo_id)->getOne(ORDER_VENDOR);
    if(!Tw_VendorData($check->vendor_id)){
      return "~Restaurant Not Found";
    }
    $orderData = Tw_OrderData($check->order_id);
    if(!$orderData)return "~Order Not Found";
    if(!in_array($status, $allowed))return "~Invalid Order Status"; 
    $result = $db
    ->where('id', $vo_id)  
    ->update(ORDER_VENDOR, ['status' => $status, 'paid' => 1]);
    if(HAS_DELIVERED == $status){
      $result = $db
      ->where('id', $check->order_id)
      ->update(ORDERS, ['status' => $status]);
    }
  }
  if($status == HAS_DELIVERED){
    $title = "Food Delivered, Please Rate Our Service";
    $content = "Please Rate Our Serivce So We Can Improve In Future";
    $notification = [
      'sender_type' => DLH,
      'sender_id' => $hero_id,
      'recipient_id' => $orderData->user_id,
      'recipient_type' => USER,
      'notify_type' => FDDL,
      'title' => $title,
      'content' => $content,    
      'content_id' => $check->order_id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$orderData->user_id],
        'data' => [
          'type' => FDDL,
          'type_data' => $check->order_id
        ]
      ]
    ];
    Tw_RegisterNotification($notification);
  }
  return $result ? 'Order updated successfully' : $e;
}
function Tw_AllOrderCentered($order_id){
  $vendors = Tw_GetVendorsOfOrder($order_id);
  $centered = true;
  foreach($vendors as $vendor){
    if($vendor->status != HAS_CENTERED){
      $centered = false;
      break;
    }    
  }
  return $centered;
}
function Tw_RetriveDeliveryStatus($order_id){
  if(empty($order_id))return false;
  $orderData = Tw_OrderData($order_id);
  if(!$orderData)return false;
  $vendors = Tw_GetVendorsOfOrder($order_id);
  $tracking = [];
  $centeredCount = 0;
  foreach($vendors as $vendor){    
    $object = [
      'percent' => Tw_PercentFromStatus($vendor->status, $orderData->status),
      'name' => $vendor->name,
      'status' =>$vendor->status
    ];    
    if($vendor->status == HAS_CENTERED || $vendor->status == HAS_PICKED_F || $vendor->status == HAS_DELIVERED)
      $centeredCount++;
    $tracking[] = $object;
  }
  $dispatch['percent'] = 0;  
  if($centeredCount == count($vendors))
    $dispatch['percent'] = $orderData->progress;  

  return [
    'tracking' => $tracking,
    'dispatch' => $dispatch
  ];
}
function Tw_PercentFromStatus($status, $delivered){
  if($delivered == HAS_DELIVERED)return 100;
  switch($status){    
    case FOOD_NOT_PREPARED:            
    return 0;
    case FOOD_ACCEPT:        
    case DELIVERY_FN_PREPARED:
    return 20;
    case DELIVERY_F_PREPARED:
    case FOOD_PREPARED:
    return 30;
    case HAS_PICKED_C:
    return 50;
    case HAS_PICKED_F:
    return 70;
    case HAS_CENTERED:
    case HAS_DELIVERED:
    return 100;
  }
}

function Tw_GetHeroFromFleetId($fleet_id){
  if(empty($fleet_id)){
    return false;
  }
  global $db;
  $hero = $db->where('fleet_id', $fleet_id)->getOne(HERO);
  return $hero;
}
//Table Booking Functions
function Tw_BookTable($details){
  if(empty($details) || 
     empty($details['user_id']) ||
     empty($details['vendor_id']) ||
     empty($details['amount']) ||
     empty($details['from_slt']) ||
     empty($details['people']) ||
     empty($details['to_slt']) ||
     empty($details['from_time']))return "~Incomplete details!";
  global $db;
  $e = "~Error While Processing";
  $txn_dta = [
    'user_id' => $details['user_id'],
    'issuer' => PAY_TABLE,
    'amount' => $details['amount']
  ];
  $full = false;
  $avSlt = Tw_GetAviliableSlots($details['vendor_id'])['slots'];
  foreach($avSlt as $av){    
    if($av->id == $details['from_slt']){
      if($av->remaining <= 0){
        $full = true;
      }
      break;
    }
  }
  if($full){    
    return ['full' => true, 'sl' => Tw_GetAviliableSlots($details['vendor_id'])];
  }
  $txn = Tw_CreateRazorOrder($txn_dta);    
  $food = false;  
  if(!empty($details['items'])){
    $items = json_decode($details['items'], true);
    if(is_array($items) && count($items) > 0){ 
      $food = true;      
    }
  }
  if(!$txn)return $e;
  $result = $db->insert(TABLE_BOOKING, [
    'vendor_id' => $details['vendor_id'],
    'from_slt' => $details['from_slt'],
    'user_id' => $details['user_id'],
    'to_slt' => $details['to_slt'],
    'from_time' => $details['from_time'],
    'to_time' => $details['to_time'],
    'people' => $details['people'],
    'tbl_amt' => TB_AMOUNT,
    'amount' => TB_AMOUNT,    
    'food' => $food,  
    'paid' => UNPROCESSED,
    'status' => ACTIVE,
    'txn_id' => $txn['order_id'],
    'booked' => time()
  ]);
  
  $db->where('id', $txn['order_id'])->update(PAYMENTS, ['data_id' => $result]);
  if($food == true){
    foreach($items as $item){
      $food = Tw_VendorFoodData($item['id']);
      $foodAmount = $item['quantity'] * $food->menu_price;
      $db->insert(BKG_FOOD, [
        'bkg_id' => $result,
        'food_id' => $item['id'],
        'quantity' => $item['quantity'],
        'amount' => $foodAmount
      ]);
    }
    Tw_ProcessTax(['bkg_id' => $result], $details['vendor_id']);
  }  
  return $result ? [
    'id' => $result,
    'order_id' => $txn['order_id'],
    'order_str' => 'ORD_'.$txn['order_id'],
    'token' => $txn['token']
  ] : $e;
}

function Tw_GetFoodOfBooking ($id){
  global $db;
  if(empty($id))return [];
  $result = $db->where('bkg_id', $id)->get(BKG_FOOD);
  $final = [];
  foreach($result as $item){
    $food = Tw_VendorFoodData($item->food_id);
    $food->id = $item->id;
    $food->food_id = $item->food_id;
    $food->per_price = $item->amount/$item->quantity;
    $food->amount = $item->amount;
    $food->quantity = $item->quantity;
    $final[] = $food;
  }
  return $result ? $final : [];
}

function Tw_LoadTBooking($details, $limit = 80, $offset = 0, $payProcess = false){
  global $db;
  $isUser = false;
  if(!empty($details['vendor_id']) && Tw_VendorExistsById($details['vendor_id'])){
    $db->where('vendor_id', $details['vendor_id']);
  }else if(!empty($details['user_id']) && Tw_UserExistsById($details['user_id'])){
    $db->where('user_id', $details['user_id']);
    $isUser = true;
  }else {
    return [];
  }
  $all = empty($details['all']) ? false : true;  
  if(!$all)$db->where('status', [ACTIVE,ASSIGNED], 'IN');
  $db->where('paid', 1);

  if(!empty($details['start']) && !empty($details['end'])){      
    $start = strtotime(date('Y-m-d', $details['start']).'00:00:00');
    $end = strtotime(date('Y-m-d', $details['end']).'23:59:59');
    $db->where('from_time', Array($start, $end), "BETWEEN");
  }else if(!empty($details['time'])){      
    $start = strtotime(date('Y-m-d', $details['time']).'00:00:00');
    $end = strtotime(date('Y-m-d', $details['time']).'23:59:59');
    $db->where('from_time', Array($start, $end), "BETWEEN");
  }

  $db->orderBy('id', 'desc');  

  $result = $db->get(TABLE_BOOKING, Array($offset, $limit));
  $time = time();  
  $final = [];
  foreach($result as $booking){
    $booking = Tw_FormatBookingData($booking, $isUser, $payProcess);    
    if($all || $booking->status != INACTIVE){
      $final[] = $booking;
    }    
  }
  return $result ? $final : [];
}

function Tw_LoadCustomBooking($details, $type = 1, $limit = 80, $offset = 0){
  global $db;
  $isUser = false;
  if(!empty($details['vendor_id']) && Tw_VendorExistsById($details['vendor_id'])){
    $db->where('vendor_id', $details['vendor_id']);
  }else if(!empty($details['user_id']) && Tw_UserExistsById($details['user_id'])){
    $db->where('user_id', $details['user_id']);
    $isUser = true;
  }else {
    return [];
  }  
  if(!empty($details['time'])){        
    $start = strtotime(date('Y-m-d', $details['time']).'00:00:00');
    $end = strtotime(date('Y-m-d', $details['time']).'23:59:59');
    $db->where('from_time', Array($start, $end), "BETWEEN");
  }
  if($type == 1){
    $db->where('status', INACTIVE);    
  }else if($type == 2){
    $db->where('status', [CANCELLED, VDRCANCEL, PAYCANCEL], "IN");
  }  
  $db->orderBy('id', 'desc');
  $result = $db->get(TABLE_BOOKING, Array($offset, $limit));  
  $final = [];
  foreach($result as $booking){
    $shouldAdd = true;        
    $canCancel = false;    
    $booking = Tw_FormatBookingData($booking, $isUser);
    $final[] = $booking;
  }
  return $result ? $final : [];
}

function Tw_FormatBookingData($booking, $isUser, $processPayment = false){
  global $db;
  $canCancel = false;
  $time = time();
  if($time > $booking->from_time){
    $db->where('id', $booking->id)->update(TABLE_BOOKING, ['status' => INACTIVE]);    
    $booking->status = INACTIVE;
  }
  $booking->type = PAY_TABLE;
  if($isUser){
    $vendorData = Tw_VendorData($booking->vendor_id);
    $booking->vendor = [
      'name' => $vendorData->name,
      'image' => $vendorData->cover,
      'hash' => $vendorData->cover_hash,
      'manager_number' => $vendorData->manager_number
    ];
    if($booking->status == ASSIGNED){
      $tableData = Tw_TableData($booking->table_id);
      $ar = Tw_AreaData($tableData->area_id);
      $booking->area = $ar->area;
    }
  }else{
    $userData = Tw_UserData($booking->user_id);        
    $booking->customer = [          
      'name' => $userData['first_name'] .' '. $userData['last_name'],
      'phone' => $userData['phone_no']
    ];
  }    
  $items = Tw_GetFoodOfBooking($booking->id);
  $temp = [];      
  if($isUser){        
    $canFlag = $time < $booking->from_time;
    if($canFlag){
      //$diffDuration = count($items) > 1 ? 60 : 15;
      $diffDuration = 30;
      $timeDiff = Tw_TimeDiffMin($time, $booking->from_time);        
      $canCancel = $timeDiff > $diffDuration;        
    }else{
      $canCancel = false;
    } 
  }          
  $booking->canCancel = $canCancel;
  if(is_array($items)){
    foreach($items as $item){          
      $foodItem = Tw_VendorFoodData($item->food_id);
      $foodItem->quantity = $item->quantity;
      $foodItem->per_price = $item->per_price;
      $foodItem->price = $item->amount;
      $temp[] = $foodItem;
    }
  }
  if($booking->tax == 1){
    $booking->taxes = Tw_GetBookingTaxes($booking->id);
  }
  $booking->time = Tw_TimeReadable2($booking->booked);
  $booking->items = $temp;
  if($processPayment && $booking->paid == UNPROCESSED){
    $booking->paid = Tw_UBKGPaymentStatus2($booking->txn_id);
  }
  return $booking;
}

function Tw_CalBkgHistory($details){
  global $db;
  if(!empty($details['vendor_id'])){
    $db->where('vendor_id', $details['vendor_id']);
  }else if(!empty($details['user_id'])){
    $db->where('user_id', $details['user_id']);    
  }else {
    return ['ac' => 0,'cn' => 0,'tx' => 0,'tt' => 0];
  }  
  $accepted = 0;
  $cancelled = 0;
  $total_tax_amt = 0;
  $total_amt = 0;  
  if(!empty($details['start']) && !empty($details['end'])){      
    $start = strtotime(date('Y-m-d', $details['start']).'00:00:00');
    $end = strtotime(date('Y-m-d', $details['end']).'23:59:59');
    $db->where('from_time', Array($start, $end), "BETWEEN");
  }else if(!empty($details['time'])){      
    $start = strtotime(date('Y-m-d', $details['time']).'00:00:00');
    $end = strtotime(date('Y-m-d', $details['time']).'23:59:59');
    $db->where('from_time', Array($start, $end), "BETWEEN");
  }  
  $history = $db->where('paid', 1)->get(TABLE_BOOKING);
  foreach($history as $his){
    if($his->cancel == 0){
      $total_tax_amt += $his->tx_amt;
      $total_amt += $his->amount;
      $accepted++;
    }else{
      $cancelled++;
    }
  }
  return [
    'ac' => $accepted,
    'cn' => $cancelled,
    'tx' => $total_tax_amt,
    'tt' => $total_amt
  ];
}

function Tw_CountVendorBookings($vendor_id, $time = 0){
  global $db; 
  $time = $time == 0 ? time() : $time;
  $db
  ->where('vendor_id', $vendor_id)
  ->where('from_time', $time, '>')
  ->where('status', ACTIVE)  
  ->where('paid', 1)
  ->get(TABLE_BOOKING);
  return $db->count;
}
function Tw_CountTableBookings($table_id, $time = 0){
  global $db; 
  $time = $time == 0 ? time() : $time;
  $db
  ->where('table_id', $table_id)
  ->where('from_time', $time, '>')
  ->where('attent', 0)
  ->where('status', [ASSIGNED, ACTIVE], 'IN')  
  ->where('paid', 1)
  ->get(TABLE_BOOKING);
  return $db->count;
}
function Tw_UserTableBookings($user_id){
  global $db; 
  $db
  ->where('user_id', $user_id)
  ->where('from_time', time(), '>')
  ->where('status', [ASSIGNED, ACTIVE], 'IN')
  ->where('paid', 1)
  ->get(TABLE_BOOKING);
  return $db->count;
}
function Tw_AcceptBooking($details){
  $co = "~Try Again!";
  if(empty($details['booking_id']) || empty($details['table_id'])){
    return $co;
  }    
  global $db;
  $bookingData = Tw_BookingData($details['booking_id']);
  $vdata = Tw_VendorData($bookingData->vendor_id);
  $table = Tw_TableData($details['table_id']);
  $area = Tw_AreaData($table->area_id);
  $time = time();
  if($bookingData && $table){
    
    $db->where('from_time', time(), ">")->where('status', ASSIGNED)->where('from_slt', $bookingData->from_slt)->where('table_id', $table->id)->get(TABLE_BOOKING);
    if($db->count > 0){
      return '~You Already Have Booking For Same Table & Time';
    }

    $futureTime = Tw_TimeDiffMin($bookingData->from_time, time());    
    if($table->status != TBL_FREE){      
      if($futureTime < 30 && Tw_TimeDiffMin($table->time, time()) < 15){
        return '~You Have An Active Table';
      }
    }
    
    $db->where('id', $bookingData->id)->update(TABLE_BOOKING, [
      'status' => ASSIGNED,
      'table_id' => $table->id,
      'tableno' => $table->number
    ]);

    $title = "You Have Got {$area->area} | Table No {$table->number}!";  
    $content = "You Will We Asked For Table No At {$vdata->name}!";
    $notification = [
      'sender_type' => VENDOR,
      'sender_id' =>  $bookingData->vendor_id,
      'recipient_id' => $bookingData->user_id,
      'recipient_type' => USER,
      'notify_type' => ASTBL,
      'title' => $title,
      'content' => $content,    
      'content_id' => $bookingData->id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$bookingData->user_id],
        'data' => [
          'type' => ASTBL,
          'type_data' => $bookingData->id
        ]
      ]
    ];
    Tw_RegisterNotification($notification);
    return "Updated!";
  }
  return $co;
}

function Tw_DispatchBooking($id){
  if(empty($id)){
    return "~Error!";
  }
  global $db;
  $bookingData = Tw_BookingData($id);
  $items = Tw_GetFoodOfBooking($id);
  $status = $bookingData->food > 0 ? TBL_PAID : TBL_PRESENT;  
  $data = [
    'user_id' => $bookingData->user_id,
    'table_id' => $bookingData->table_id,
    'bkg_id' => $bookingData->id,
    'bal_amt' => $bookingData->fd_amt + $bookingData->tx_amt,        
  ];
  if($bookingData->food === 1){
    $data['pmy'] = PAY_BOTH;
  }
  $visit_id = Tw_CaptainHotelVisit($data);
  $data = ['attent' => YESATTENT];
  if($bookingData->food == 1){//Converting To Visit Profit
    $data['amount'] = $bookingData->tbl_amt;    
  }
  $db->where('id', $bookingData->id)->update(TABLE_BOOKING, $data);
  $db->where('bkg_id', $bookingData->id)->delete(TAX_APPLIED);
  if($bookingData->food == 1){    
    $foodData = array('visit_id' => $visit_id, 'items' => json_encode($items));
    Tw_AddFoodToVisit($foodData);
  }
  $db->where('id', $bookingData->table_id)->update(V_TABLE, [
    'visit_id' => $visit_id,
    'status' => $status
  ]);  
  return "Updated!";
}

function Tw_DispatchBkgData($id){
  global $db;
  if(empty($id)){
    return "~Error";
  }
  $bookingData = Tw_BookingData($id);
  $tableData = Tw_TableData($bookingData->table_id);  
  $areaData = Tw_AreaData($tableData->area_id);
  $items = Tw_GetFoodOfBooking($bookingData->id);
  $items[] = [
    'name' => 'Booking Charges',
    'amount' => TB_AMOUNT,
    'per_price' => TB_AMOUNT,    
    'quantity' => 1
  ];
  $user = Tw_UserData($bookingData->user_id);
  $taxes = [];
  $taxAmount = 0;
  if(count($items) > 0){
    $taxes = Tw_GetBookingTaxes($id);
    foreach($taxes as $t){    
      $t->tax = 1;
      $taxAmount += $t->amount;
      $items[] = $t;
    }
  }
  $cancel = time() > strtotime("+".SHW_BK_TIME." minutes", $bookingData->from_time);  
  $data = [
    'name' => $user['name'],
    'table' => $areaData->area . ' Table No. - '. $bookingData->tableno,
    'receipt' => $items,
    'total' => $bookingData->amount,
    'ftm' => $bookingData->from_time,
    'ttm' => $bookingData->to_time,
    'tax' => $taxAmount,    
    'cancel' => $cancel
  ];
  return $data;
}

function Tw_GetBookingTaxes($bkg_id){
  if(empty($bkg_id)){
    return [];
  }
  global $db;
  $taxes = $db->where('bkg_id', $bkg_id)->get(TAX_APPLIED);
  $final = [];    
  if($db->count > 0){
    foreach($taxes as $tax){      
      $data = Tw_TaxData($tax->tax_id);
      if($data){
        $obj = new stdClass();
        $obj->name = $data->name;
        $obj->percent = $tax->percent;
        $obj->amount = $tax->tax_amt;
        $final[] = $obj;
      }
    }
    return $final;
  }else{
    return [];
  }
}


function Tw_DeactiveBooking($id){
  if(empty($id)){
    return "~Unable To Deactivate!";
  }
  global $db;  
  $db->where('id', $id)->update(TABLE_BOOKING, [
    'attent' => NOTATTENT,
    'status' => INACTIVE
  ]);
  return "Updated";
}

function Tw_LoadAssignedBookings($details){
  if(empty($details['vendor_id'])){
    return [];
  }
  global $db;
  $start = time();
  if(!empty($details['timed'])){    
    $end = strtotime("+".SHW_BK_TIME." minutes", $start);
    $db->where('from_time', Array($start, $end), "BETWEEN");
  }else{
    $db->where('from_time', time(), ">");
  }
  if(!empty($details['table_id'])){    
    $db->where('table_id', $details['table_id']);
  }
  $bookings = $db->where('status', ASSIGNED)->where('attent', 0)->get(TABLE_BOOKING);
  $final = [];
  foreach($bookings as $booking){
    $canCancel = false;
    $canFlag = $start < $booking->from_time;
    if($canFlag){      
      $timeDiff = Tw_TimeDiffMin($start, $booking->from_time);
      $canCancel = $timeDiff > CANCEL_TIME;      
    }else{
      $canCancel = false;
    }
    $item = new stdClass();
    $item->id = $booking->id;
    $item->pp = $booking->people;
    $item->am = $booking->amount;
    $item->fd = $booking->food;
    $item->c =  $canCancel;
    $final[] = $item;
  }
  return $final;
}

function Tw_CancelBooking($booking_id, $reason, $frm_usr = true){
  $co = "~Sorry You Can Not Cancel Booking";
  $bookingData = Tw_BookingData($booking_id);
  $time = time();
  if($bookingData){
    $canCancel = false;
    $canFlag = $time < $bookingData->from_time;
    if($canFlag){
      //$items = Tw_GetFoodOfBooking($booking_id);
      //$diffDuration = count($items) > 1 ? 60 : 15;
      $diffDuration = CANCEL_TIME;
      $timeDiff = Tw_TimeDiffMin($time, $bookingData->from_time);
      $canCancel = $timeDiff > $diffDuration;      
    }else{
      $canCancel = false;
    }    
    if(!$canCancel) return $co;
    $refund = Tw_CreateRefund([
      'amount' => $bookingData->amount,
      'type' =>  $frm_usr ? CANCELLED : VDRCANCEL,
      'issuer' => PAY_TABLE,
      'issuer_id' => $booking_id,
      'txn_id' => $bookingData->txn_id,
      'is_partial' => false,
      'user_id' => $bookingData->user_id,
      'reason' => $reason
    ]);
    if($frm_usr){
      $title = "Your Table Booking ID {$booking_id} Has Been Cancelled";
      $content = "Press Here To View Details";
      $notification = [
        'sender_type' => USER,
        'sender_id' =>  $bookingData->user_id,
        'recipient_id' => $bookingData->vendor_id,
        'recipient_type' => VENDOR,
        'notify_type' => CNTBL,
        'title' => $title,
        'content' => $content,    
        'content_id' => $refund,      
        'notifyData' => [
          'title' => $title,
          'content' => $content,
          'vendor_ids' => [$bookingData->vendor_id],
          'data' => [
            'type' => CNTBL,
            'type_data' => $refund
          ]
        ]
      ];
      Tw_RegisterNotification($notification);
    }
    
    if($frm_usr){
      $title = "Table Cancel Request Accepted!";
      $content = "Your Money Will Be Refunded Shortly";
    }else{
      $vData = Tw_VendorData($bookingData->vendor_id);
      $title = "😔 Sorry, Table Booking Request Cancelled!";
      $content = "Your Money Will Be Refunded Shortly, Your Request Was Cancelled By {$vData->name}";      
    }    
    
    $notification = [
      'sender_type' => $frm_usr ? APP : VENDOR,
      'sender_id' =>  $frm_usr ? 0 : $bookingData->vendor_id,
      'recipient_id' => $bookingData->user_id,
      'recipient_type' => USER,
      'notify_type' => CNTBL,
      'title' => $title,
      'content' => $content,    
      'content_id' => $refund,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$bookingData->user_id],
        'data' => [
          'type' => CNTBL,
          'type_data' => $refund
        ]
      ]
    ];
    Tw_RegisterNotification($notification);
    return $refund;
  }
  return $co;
}

function Tw_BookingData($id){
  if(empty($id))return false;
  global $db;
  $result = $db->where('id', $id)->getOne(TABLE_BOOKING);
  return $result ? $result : false;
}

//User Orders
function Tw_UserFoodOrderUpdates($user_id, $limit = 15, $offset = 0, $all = false, $payment = false){
  if(empty($user_id))return false;
  global $db;
  $cooking = [FOOD_ACCEPT, DELIVERY_FN_PREPARED];
  $prepared = [FOOD_PREPARED, DELIVERY_F_PREPARED];
  $callAv = [DELIVERY_FN_PREPARED, DELIVERY_F_PREPARED];
  $onway = [HAS_PICKED_F, HAS_PICKED_F, HAS_CENTERED];    
  if($all == false)$db->where('status', HAS_DELIVERED, '<');  
  $orders = $db->where('user_id', $user_id)
  ->where('paid', 1)
  ->orderBy('id', 'desc')
  ->get(ORDERS, Array($offset, $limit));
  $updates = [];
  foreach($orders as $order){
    $proceed = true;
    if($payment && $order->paid == UNPROCESSED){
      $proceed = Tw_UODRPaymentStatus2($order->txn_id);
    }
    if($proceed){
      $vendors = Tw_GetVendorsOfOrder($order->id);
      $order->anim = null;
      $order->dh_number = [];
      $order->type = 1;
      $order->total = $order->amount + $order->d_praise + $order->delivery_fee;
      $order->order_code = Tw_OrderCode($order->id);
      $order->vendors = count($vendors);
      $order->food_items = [];
      $order->time_string = Tw_TimeHumanType($order->time);
      foreach($vendors as $vendor){      
        if($order->status == HAS_DELIVERED){        
          $order->anim = 'dl';
        }else{
          if(          
            $vendor->status == FOOD_NOT_PREPARED
            && $order->anim == null ||
            $order->anim == 'pp' ||
            $order->anim == 'ow'
          ){
            $order->anim = 'wt';
          }else if(
            in_array($vendor->status, $cooking) 
            && $order->anim == null || 
            $order->anim == 'pp' ||
            $order->anim == 'ow'
          ){
            $order->anim = 'ck';
          }else if(
            in_array($vendor->status, $prepared) 
            && $order->anim == null 
            || $order->anim == 'ow'
          ){
            $order->anim = 'pp';
          }else if(in_array($vendor->status, $onway) && $order->anim == null){
            $order->anim = 'ow';
          }else if($vendor->status == VDRFDCANCEL && $order->anim == null || $order->anim == 'cn'){
            $order->anim = 'cn';
          }
        }      
        if($vendor->status > FOOD_PREPARED){
          //$order->dh_number[] = Tw_DHeroData($vendor->hero_id)['phone_no'];
          if($vendor->hero_id != 0){
            $order->dh_number = [Tw_DHeroData($vendor->hero_id)['phone_no']];
          }          
        }
        $order->food_items = array_merge(
          $order->food_items,
          Tw_GetFoodsByOrderId($vendor->id, $order->id, true)
        );      
      }    
      $updates[] = $order; 
    }    
  }
  return $updates;
}


function Tw_UserFoodLiteUpdates($user_id, $limit = 15, $offset = 0, $all = false, $payment = false){
  if(empty($user_id))return false;
  global $db;
  $cooking = [FOOD_ACCEPT, DELIVERY_FN_PREPARED];
  $prepared = [FOOD_PREPARED, DELIVERY_F_PREPARED];
  $callAv = [DELIVERY_FN_PREPARED, DELIVERY_F_PREPARED];
  $onway = [HAS_PICKED_F, HAS_PICKED_F, HAS_CENTERED];    
  if($all == false)$db->where('status', HAS_DELIVERED, '<');  
  $orders = $db->where('user_id', $user_id)
  ->where('paid', 1)
  ->orderBy('id', 'desc')
  ->get(ORDERS, Array($offset, $limit));
  $updates = [];
  foreach($orders as $order){
    $proceed = true;
    if($payment && $order->paid == UNPROCESSED){
      $proceed = Tw_UODRPaymentStatus2($order->txn_id);
    }
    if($proceed){
      $vendors = Tw_GetVendorsOfOrder($order->id);
      $odr = new stdClass();
      $odr->id = $order->id;
      $odr->type = 1;      
      $odr->order_code = Tw_OrderCode($order->id);
      $odr->vendors = count($vendors); 
      $odr->time_string = Tw_TimeHumanType($order->time);
      $odr->total = $order->amount + $order->d_praise + $order->delivery_fee;
      $odr->pay_method = $order->pay_method;          
      $updates[] = $odr; 
    }
  }
  return $updates;
}
//Notification
function Tw_RegisterNotification($details){
  global $db;  
  $values = [
    'title' => $details['title'],
    'content' => $details['content'],
    'sender_id' => $details['sender_id'],
    'sender_type' => $details['sender_type'],
    'recipient_id' => json_encode($details['recipient_id']),
    'notify_type' => $details['notify_type'],
    'content_id' => $details['content_id'],
    'time' => time()
  ];    
  if(!empty($details['image'])){
    $values['image'] = $details['image'];
  }   
  $result = $db->insert(NOTIFY, $values);  
  if($result){
  return Tw_NotifyRecipient($details['notifyData']);
  }else{
    return false;
  }
}

function Tw_UpdateFCMKey($details){
 if(empty($details['vendor_id']) || empty($details['key'])){
   return false;
 }
 global $db;
 $vendors = $db->where('fcm', $details['key'])->get(V_FAMILY);
 $found = 0;
 foreach($vendors as $vendor){
   if($details['vendor_id'] == $vendor->id){
     $found = 1;
   }else{
    $db->where('id', $vendor->id)->update(V_FAMILY, ['fcm' => '']);
   }   
 }
 if($found == 0){
  $db->where('id', $details['vendor_id'])->update(V_FAMILY, [
    'fcm' => $details['key']
   ]);
 } 
 return true;
}

function Tw_VendorDashLogout($vendor_id){
  if(empty($vendor_id)){
    return "~Error";
  }
  global $db;
  $db->where('id', $vendor_id)->update(V_FAMILY, ['fcm' => '']);
  return "Logout";
 }

function Tw_RegisterWebPush($details){
  global $db;
  if(empty($details['vendor_ids']) || empty($details['title']) || empty($details['content'])){
    return false;
  }
  $fcmIds = [];
  $url = 'https://fcm.googleapis.com/fcm/send';
  foreach($details['vendor_ids'] as $v_id){
    $vData = Tw_VendorData($v_id);
    if(!empty($vData->fcm)){
      $fcmIds[] = $vData->fcm;
    }    
  }  
  $fields = array (
    'notification' => [
      'title' => $details['title'],
      'body' => $details['content']
    ]    
  );
  $count = count($fcmIds);
  if($count == 0){
    return false;
  }else if($count > 1){
    $fields['registration_ids'] = $fcmIds;
  }else{
    $fields['to'] = $fcmIds[0];
  }
  $fields = json_encode($fields);
  $headers = array (
    'Authorization: key=' .FMC_KEY,
    'Content-Type: application/json'
  );
  $ch = curl_init();
  curl_setopt ( $ch, CURLOPT_URL, $url );
  curl_setopt ( $ch, CURLOPT_POST, true );
  curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
  curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
  $result = curl_exec ( $ch );
  echo $result;
  curl_close ( $ch );
}

function Tw_NotifyRecipient($details){
    if(empty($details['title']) ||
       empty($details['content'])){        
       return false;
    }    
    $key = '';
    $list = [];
    $app_channel_id = null;
    if(!empty($details['vendor_ids'])){
      $key = 'vendorid';
      $list = $details['vendor_ids'];
      $app_key = OS_VENDOR_KEY;
      $app_auth = OS_VENDOR_ATH;      
      $app_channel_id = OS_VENDOR_CHID;
    }else if(!empty($details['user_ids'])){
      $key = 'userid';
      $list = $details['user_ids'];
      $app_key = OS_CORE_KEY;
      $app_auth = OS_CORE_ATH;
      $channel_id = OS_CORE_CHID;
    }else if(!empty($details['hero_ids'])){
      $key = 'heroid';
      $list = $details['hero_ids'];
      $app_key = OS_DLV_KEY;
      $app_auth = OS_DLV_ATH;
      $channel_id = OS_DLV_CHID;
    }else{
      return true;
    }
    
    if(count($list) > 1){
        $filters = array();
        $lastIdx = count($list) - 1;
        foreach($list as $idx => $id){
            $temp = array(
              "field" => "tag",
              "key" => $key,
              "relation" => "=",
              "value" => $id
            );
            $or = array('operator' =>  "OR");
            array_push($filters, $temp);
            if($lastIdx != $idx){
              array_push($filters, $or);
            }
            
        }
    }else{
      $filters = array(array("field" => "tag", "key" => $key, "relation" => "=", "value" => $list[0]));
    }
    
    $details['big_picture'] = empty($details['big_picture']) ? '' : $details['big_picture'];
    $details['large_icon'] = empty($details['large_icon']) ? '' : $details['large_icon'];
    $data = empty($details['data']) ? [] : $details['data']; 
    $fields = array(
      'app_id' => $app_key,    
      'isChrome' => false,    
      'data' => $data,
      'headings' => array('en' => $details['title']),
      'filters' => $filters,
      'android_led_color' => 'FF0000FF',
      'big_picture' => $details['big_picture'],
      'large_icon' => $details['large_icon'],
      'android_sound' => 'notify',      
      'priority' => 10,
      'contents' => array("en" => $details['content'])
    );
    if($app_channel_id == null){
      $fields['android_channel_id'] = $channel_id;
    }else{
      $fields['existing_android_channel_id'] = $app_channel_id;
    }
    if(!empty($details['send_after'])){      
      $fields['send_after'] = date('D M d Y H:i:s', $details['send_after']).' IST';      
    }
    if($fields['big_picture'] == ''){
        unset($fields['big_picture']);
    }
    if($fields['large_icon'] == ''){
        unset($fields['large_icon']);
    }
    
    return Tw_ProcessNotify(json_encode($fields), $app_auth);
}

function Tw_ProcessNotify($fields, $autho){    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, 
    array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic '.$autho));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
    $response = curl_exec($ch);    
    curl_close($ch);    
    if($response){
        return true;
    }else{        
        return false;
    }
}

function Tw_TimeDiffMin($from_time, $to_time){  
  return round(abs($to_time - $from_time) / 60,2);
}

function Tw_HasClosed($openTime, $closeTime, $c = -1){
  $timezone = "Asia/Kolkata";
  date_default_timezone_set($timezone);
  $start = strtotime(date('m/d/Y 11:00:00')); 
  $end = strtotime(date('m/d/Y 23:00:00')); 
  if(time() < $start){
    return true;
  }else if(time() > $end){
    return true;
  }
  global $db;
  $closed = $db->where('id', 1)->getOne('temp');
  if($closed->val == 1){    
    return true;
  }
  $valid = [];
  $currentTime = $c == -1 ? time() : $c;
  $openHr = intval(date('H', $openTime));
  $closeHr = intval(date('H', $closeTime));
  $currentHr = intval(date('H', $currentTime));
  $openMin = intval(date('i', $openTime));
  $closeMin = intval(date('i', $closeTime));
  $currentMin = intval(date('i', $currentTime));
  if($currentHr == $closeHr && $closeMin < $currentMin){
      return true;
  }
  for($i = $openHr; ; $i++){
      if($i == 23 && $closeHr == 23){
          break;
      }else if($i == 23){
          $i = 0;
      }
      $valid[] = $i;
      if($i == $closeHr)break;
  }  
  return array_search($currentHr, $valid) > -1 ? false : true;
}

function Tw_GetNotifications($receiver, $time){
  global $db;
  if(empty($receiver) && !Tw_UserExistsById($receiver))
    return "~Not Found";  
  $result = $db
  ->orderBy("id","desc")
  ->where('receiver', $receiver)
  ->where('time', $time, '>')
  ->get('notifications');
  return $result;
}
//Vendor Photos Related 
function Tw_AddVendorPhotos($details, $file){
  global $db;
  $image = Tw_MoveMediaSeprate($file);  
  if($image){    
    $object = [
      'vendor_id' => $details['vendor_id'],
      'media_type' => 0,
      'type' => $details['type'],
      'url' => $image['file'],
      'blurCode' => $image['hash']
    ];
    $result = $db->insert(V_GALLERY, $object);
    $object['id'] = $result;
    return $object;
  }else{
    return "~Unable to add photos";
  }  
}

function Tw_DeleteVendorPhotos($details){
  global $db;
  $s = "Photos Deleted Successfully";
  if(empty($details['ids']))return $s;
  $ids = json_decode($details['ids'], true);
  if($ids){
    foreach($ids as $id){
      $photoData = Tw_PhotoData($id);
      if($photoData){
        if(file_exists($photoData->url)){
          unlink($photoData->url);
        }
        $db->where('id', $id)->delete(V_GALLERY);
      }      
    }
  }
  return $s;
}

function Tw_PhotoData($id){
  global $db;  
  if(empty($id))return false;
  $data = $db->where('id', $id)->getOne(V_GALLERY);
  return $data;
}

function Tw_GetVendorPhotos ($id, $limit, $offset) {
    global $db;
    if (empty($id))return false;    
    $r = $db
    ->where('vendor_id', $id)
    ->orderBy('id', 'desc')
    ->get(V_GALLERY, Array($offset, $limit)); 
    return $r;
}
function Tw_GetVendorPhotos2 ($id, $type) {
    global $db;
    if (empty($id))return false;    
    $r = $db
    ->where('vendor_id', $id)
    ->where('type', $type)
    ->orderBy('id', 'desc')
    ->get(V_GALLERY); 
    return $r;
}

function Tw_CountVendorPhotos($id){
    global $db;
    if (empty($id))return 0;    
    $db->where('vendor_id', $id)->get(V_GALLERY); 
    return $db->count;
}
//KOT Related Functions
function Tw_GetVendorKOT($vendor_id){
  global $db;
  if (empty($vendor_id))return [];
  $areas = Tw_LoadVendorAreas($vendor_id);
  $aIds = [];
  foreach($areas as $area){
    $aIds[] = $area->id;
  }
  $orderTbls = $db
  ->where('area_id', $aIds, "IN")
  ->where('status', TBL_UNPAID)  
  ->get(V_TABLE);

  $orders = $db
  ->where('vendor_id', $vendor_id)
  ->where('status', [FOOD_ACCEPT, DELIVERY_FN_PREPARED], "IN")
  ->get(ORDER_VENDOR);

  $merged = array_merge($orderTbls, $orders);

  $final = [];
  foreach($merged as $common){
    if(empty($common->visit_id)){
      $foodItems = $db->where('order_id', $common->order_id)->where('quantity > pquantity')->get(ORDER_FOOD);      
      if(count($foodItems) > 0){
        $im = [];
        foreach($foodItems as $itm){
          if($itm->food_id == 0){
            $fd = Tw_ClufterFoodData($itm->cfood_id);
          }else{
            $fd = Tw_VendorFoodData($itm->food_id);
          }
          $im[] = [
            'n' => $fd->name,
            'i' => $itm->id,
            'q' => $itm->quantity - $itm->pquantity
          ];
        }
        $fdta = [
          'd' => 1,
          'oi' => $common->order_id,               
          'tm' => $common->time,
          'im' => $im
        ];        
        $final[] = $fdta;
      }      
    }else{
      $foodItems = $db->where('visit_id', $common->visit_id)->where('quantity > pquantity')->get(VISIT_FOOD);      
      $a = Tw_AreaData($common->area_id);
      if(count($foodItems) > 0){
        $im = [];
        foreach($foodItems as $itm){
          $fd = Tw_VendorFoodData($itm->food_id);
          $im[] = [
            'n' => $fd->name,
            'i' => $itm->id,
            'q' => $itm->quantity - $itm->pquantity
          ];
        }
        $fdta = [
          'oi' => $common->visit_id,
          'a' => $a->area,
          't' => $common->number,        
          'tm' => $foodItems[0]->updated,
          'im' => $im
        ]; 
        if($common->tkaway == 1){
          $fdta['ta'] = $common->tkaway;
          $fdta['ti'] = $common->id;
        }
        if($common->sp_idx > 0){
          $fdta['s'] = $common->sp_idx;
        }
        $final[] = $fdta;
      }
    }    
  }
  usort($final, 'Tw_SortByTm');
  return $final;
}

function Tw_GetVendorAutoKOT($vendor_id){
  global $db;
  if (empty($vendor_id))return [];
  $areas = Tw_LoadVendorAreas($vendor_id);
  $aIds = [];
  foreach($areas as $area){
    $aIds[] = $area->id;
  }
  $orderTbls = $db
  ->where('area_id', $aIds, "IN")
  ->where('status', [TBL_UNPAID, TBL_PAID], "IN")  
  ->get(V_TABLE);

  $orders = $db
  ->where('vendor_id', $vendor_id)
  ->where('status', [FOOD_ACCEPT, DELIVERY_FN_PREPARED], "IN")
  ->get(ORDER_VENDOR);

  $merged = array_merge($orderTbls, $orders);

  $final = [];
  foreach($merged as $common){
    if(empty($common->visit_id)){
      $foodItems = $db
      ->where('vendor_id', $common->vendor_id)
      ->where('order_id', $common->order_id)
      ->where('quantity != pquantity')->get(ORDER_FOOD);
      if(count($foodItems) > 0){
        $im = [];
        $ids = [];
        foreach($foodItems as $itm){
          if($itm->food_id == 0){
            $fd = Tw_ClufterFoodData($itm->cfood_id);
          }else{
            $fd = Tw_VendorFoodData($itm->food_id);
          }
          $fd = Tw_VendorFoodData($itm->food_id);
          if($itm->quantity == 0){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => 'c'
            ];
            $db->where('id', $itm->id)->delete(ORDER_FOOD);
          }else if($itm->quantity < $itm->pquantity){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => 'd-'.($itm->pquantity).'-'.($itm->pquantity - $itm->quantity)
            ];
            $ids[] = $itm->id;
          }else if($itm->quantity > $itm->pquantity){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => $itm->quantity - $itm->pquantity
            ];
            $ids[] = $itm->id;
          }
        }
        if(count($ids) > 0){
        $ids = implode(',', $ids);
        $db->rawQuery("UPDATE order_food set pquantity = order_food.quantity WHERE id IN ({$ids})");
        }        
        $ids = []; 
        $fdta = [
          'd' => 1,
          'oi' => $common->order_id,  
          'tm' => $common->time,
          'im' => $im,          
          'bn' => $common->billno
        ];        
        $final[] = $fdta;
      }      
    }else{
      $foodItems = $db->where('visit_id', $common->visit_id)->where('quantity != pquantity')->get(VISIT_FOOD);
      $visitData = Tw_VisitData($common->visit_id);
      $a = Tw_AreaData($common->area_id);
      if(count($foodItems) > 0){
        $kotSq = $visitData->kotsq + 1;
        $db->where('id', $visitData->id)->update(VISITS, ['kotsq' => $kotSq]);
        $im = [];
        $ids = [];
        foreach($foodItems as $itm){
          $fd = Tw_VendorFoodData($itm->food_id);
          if($itm->quantity == 0){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => 'c'
            ];
            $db->where('id', $itm->id)->delete(VISIT_FOOD);
          }else if($itm->quantity < $itm->pquantity){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => 'd-'.($itm->pquantity).'-'.($itm->pquantity - $itm->quantity)
            ];
            $ids[] = $itm->id;
          }else if($itm->quantity > $itm->pquantity){
            $dta = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => $itm->quantity - $itm->pquantity
            ];
            if(!empty($itm->note)){
              $dta['c'] = $itm->note;
            }
            $im[] = $dta;
            $ids[] = $itm->id;
          }
        }
        if(count($ids) > 0){
        $ids = implode(',', $ids);
        $db->rawQuery("UPDATE visit_food set pquantity = visit_food.quantity WHERE id IN ({$ids})");
        }        
        $fdta = [
          'oi' => $common->visit_id,
          'a' => $a->area,
          't' => $common->number,        
          'tm' => $foodItems[0]->updated,
          'im' => $im,
          'sq' => $kotSq,
          'bn' => $visitData->billno
        ]; 
        if($common->tkaway == 1){
          $fdta['ta'] = $common->tkaway;
          $fdta['ti'] = $common->id;
        }
        if($common->sp_idx > 0){
          $fdta['s'] = $common->sp_idx;
        }
        $final[] = $fdta;
      }
    }    
  }
  usort($final, 'Tw_SortByTm');
  return $final;
}

function Tw_GetVendorBillKOT($vendor_id){
  global $db;
  if (empty($vendor_id))return [];
  $areas = Tw_LoadVendorAreas($vendor_id);
  $aIds = [];
  foreach($areas as $area){
    $aIds[] = $area->id;
  }
  $orderTbls = $db
  ->where('area_id', $aIds, "IN")
  ->where('status', [TBL_UNPAID, TBL_PAID], "IN")  
  ->get(V_TABLE);

  $orders = $db
  ->where('vendor_id', $vendor_id)
  ->where('status', [FOOD_ACCEPT, DELIVERY_FN_PREPARED], "IN")
  ->get(ORDER_VENDOR);

  $merged = array_merge($orderTbls, $orders);
  $receipts = [];
  $final = [];
  foreach($merged as $common){
    if(empty($common->visit_id)){
      $foodItems = $db
      ->where('vendor_id', $common->vendor_id)
      ->where('order_id', $common->order_id)
      ->where('quantity != pquantity')->get(ORDER_FOOD);
      if(count($foodItems) > 0){
        $im = [];
        $ids = [];
        foreach($foodItems as $itm){
          if($itm->food_id == 0){
            $fd = Tw_ClufterFoodData($itm->cfood_id);
          }else{
            $fd = Tw_VendorFoodData($itm->food_id);
          }
          if($itm->quantity == 0){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => 'c'
            ];
            $db->where('id', $itm->id)->delete(ORDER_FOOD);
          }else if($itm->quantity < $itm->pquantity){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => 'd-'.($itm->pquantity).'-'.($itm->pquantity - $itm->quantity)
            ];
            $ids[] = $itm->id;
          }else if($itm->quantity > $itm->pquantity){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => $itm->quantity - $itm->pquantity
            ];
            $ids[] = $itm->id;
          }
        }
        if(count($ids) > 0){
        $ids = implode(',', $ids);
        $db->rawQuery("UPDATE order_food set pquantity = order_food.quantity WHERE id IN ({$ids})");
        }        
        $ids = []; 
        $fdta = [
          'd' => 1,
          'oi' => $common->order_id,  
          'tm' => $common->time,
          'im' => $im,
          'bn' => $common->billno
        ];        
        $final[] = $fdta;
      }      
    }else{
      $foodItems = $db->where('visit_id', $common->visit_id)->where('quantity != pquantity')->get(VISIT_FOOD);
      $visitData = Tw_VisitData($common->visit_id);
      $a = Tw_AreaData($common->area_id);
      if(count($foodItems) > 0){ 
        $taData = null;       
        if($common->tkaway == 1){
          $visitData = Tw_VisitData($common->visit_id);          
          if($visitData->billno == 0){      
            $billNo = Tw_GenerateBillNr(time(), $visitData->vendor_id);
            $db->where('id', $visitData->id)->update(VISITS, [
              'billno' => $billNo
            ]);
          }
          $taid = Tw_TransferTkAway($common);
          $taData = Tw_TkAwayData($taid);
          $receipt = Tw_GetTkAwayReceipt($taid);
          $discount = $taData->discount;
          $roleData = Tw_VendorRoleData($taData->cp_id);          
          $vendorData = Tw_VendorData($taData->vendor_id);              
          $data = [
              'i' => $taData->id,
              'ho' => $vendorData->name,//Hotel
              'ad' => $vendorData->address,//Address          
              'rp' => $receipt,//Receipt
              'tm' => $taData->time,
              'tt' => $taData->total_amt,//Total
              'tx' => $taData->tx_amt,//Tax Amount
              'bn' => $taData->billno,//Bill No
              'dc' => $taData->discount,
              'cp' => $roleData->name//Discount
          ];          
          $receipts[] = $data;      
        }
        $kotSq = $visitData->kotsq + 1;
        $db->where('id', $visitData->id)->update(VISITS, ['kotsq' => $kotSq]);
        $im = [];
        $ids = [];
        foreach($foodItems as $itm){
          $fd = Tw_VendorFoodData($itm->food_id);
          if($itm->quantity == 0){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => 'c'
            ];
            $db->where('id', $itm->id)->delete(VISIT_FOOD);
          }else if($itm->quantity < $itm->pquantity){
            $im[] = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => 'd-'.($itm->pquantity).'-'.($itm->pquantity - $itm->quantity)
            ];
            $ids[] = $itm->id;
          }else if($itm->quantity > $itm->pquantity){
            $dta = [
              'n' => $fd->name,
              'i' => $itm->id,
              'q' => $itm->quantity - $itm->pquantity
            ];
            if(!empty($itm->note)){
              $dta['c'] = $itm->note;
            }
            $im[] = $dta;
            $ids[] = $itm->id;
          }
        }
        if(count($ids) > 0){
        $ids = implode(',', $ids);
        $db->rawQuery("UPDATE visit_food set pquantity = visit_food.quantity WHERE id IN ({$ids})");
        }        
        $fdta = [
          'oi' => $taData == null ? $common->visit_id : $taData->id,
          'a' => $a->area,
          't' => $common->number,        
          'tm' => $foodItems[0]->updated,
          'im' => $im,
          'sq' => $kotSq,
          'bn' => $taData == null ? $visitData->billno : $taData->billno
        ]; 
        if($common->tkaway == 1){
          $fdta['ta'] = $common->tkaway;
          $fdta['ti'] = $common->id;
        }
        if($common->sp_idx > 0){
          $fdta['s'] = $common->sp_idx;
        }
        $final[] = $fdta;
      }
    }
  }
  usort($final, 'Tw_SortByTm');  
  return ['k' => $final, 'r' => $receipts];
}

function Tw_SortByTm($a, $b) {
  return $a['tm'] < $b['tm'];
}

function Tw_ChefOrderTick($visitFoods){
  if(empty($visitFoods)){
    return "~Error";
  }
  $visitFoods = json_decode($visitFoods, true);
  if($visitFoods && is_array($visitFoods)){    
    global $db;
    foreach($visitFoods as $id){
      $v = $db->where('id', $id)->getOne(VISIT_FOOD);      
      $db->where('id', $id)->update(VISIT_FOOD, [
        'pquantity' => $v->quantity
      ]);
    }    
    return 'Done';
  }else{
    return 'Done';
  } 
}

function Tw_ChefDeliveryTick($orderFood){
  if(empty($orderFood)){
    return "~Error";
  }
  $orderFood = json_decode($orderFood, true);
  if($orderFood && is_array($orderFood)){    
    global $db;
    foreach($orderFood as $id){
      $v = $db->where('id', $id)->getOne(ORDER_FOOD);      
      $db->where('id', $id)->update(ORDER_FOOD, [
        'pquantity' => $v->quantity
      ]);
    }    
    return 'Done';
  }else{
    return 'Done';
  } 
}
//Vendor Reviews Related
function Tw_AddVendorReview($details){
  global $db;
  $e = "~Incomplete Details";
  if(empty($details['issuer_id']) ||
     empty($details['user_id']) ||     
     empty($details['rating']))return $e;

  $object = [
    'vendor_id' => $details['issuer_id'],
    'user_id' => $details['user_id'],
    'text' => empty($details['text']) ? '' : json_encode($details['text']),
    'rating' => $details['rating']
  ];
  $id = $db->insert(V_REVIEWS, $object);
  if($id){
    $object['id'] = $id;
    $userData = Tw_BasicUserData($details['user_id']);
    $object['name'] = $userData['name'];
    $object['time'] = Tw_DateReadable(time());
    return $object;
  }
  return "~Unable to Add Review";
}
function Tw_EditVendorReview($details){
  global $db;
  $e = "~Incomplete Details";
  if(empty($details['id']) ||
     empty($details['text']) ||
     empty($details['rating']))return $e;
  $id = $db->where('id', $id)->update(V_REVIEWS, [    
    'text' => $details['text'],
    'rating' => $details['rating']
  ]);
  return $id ? $id : "~Unable to Edit Review";
}
function Tw_DeleteVendorReview($id){
  global $db;
  $e = "~Incomplete Details";  
  if(empty($id))return $e;
  $id = $db->where('id', $id)->delete(V_REVIEWS);
  return $id ? $id : "~Unable to Delete Review";
}

//Food Reviews Related
function Tw_AddFoodReview($details){
  global $db;
  $e = "~Incomplete Details";
  if(empty($details['issuer_id']) ||
     empty($details['user_id']) ||     
     empty($details['rating']))return $e;
  $object = [
    'item_id' => $details['issuer_id'],
    'user_id' => $details['user_id'],
    'text' => empty($details['text']) ? '' : json_encode($details['text']),
    'rating' => $details['rating']
  ];
  $id = $db->insert(F_REVIEWS, $object);
  if($id){
    $object['id'] = $id;
    $userData = Tw_BasicUserData($details['user_id']);
    $object['name'] = $userData['name'];
    $object['time'] = Tw_DateReadable(time());
    return $object;
  }
  return "~Unable to Add Review";
}
function Tw_EditFoodReview($details){
  global $db;
  $e = "~Incomplete Details";  
  if(empty($details['id']) ||
     empty($details['text']) ||
     empty($details['rating']))return $e;
  $id = $db->where('id', $id)->update(F_REVIEWS, [    
    'text' => json_encode($details['text']),
    'rating' => $details['rating']
  ]);
  return $id ? $id : "~Unable to Edit Review";
}
function Tw_DeleteFoodReview($id){
  global $db;
  $e = "~Incomplete Details";  
  if(empty($id))return $e;
  $id = $db->where('id', $id)->delete(F_REVIEWS);
  return $id ? $id : "~Unable to Delete Review";
}

//Booking Reviews Related
function Tw_AddBookingReview($details){
  global $db;
  $e = "~Incomplete Details";
  if(empty($details['issuer_id']) ||
     empty($details['user_id']) ||     
     empty($details['rating']))return $e;
  $object = [
    'booking_id' => $details['issuer_id'],
    'user_id' => $details['user_id'],
    'text' => empty($details['text']) ? '' : json_encode($details['text']),
    'rating' => $details['rating']
  ];
  $id = $db->insert(B_REVIEWS, $object);
  if($id){
    $object['id'] = $id;
    $userData = Tw_BasicUserData($details['user_id']);
    $object['name'] = $userData['name'];
    $object['time'] = Tw_DateReadable(time());
    return $object;
  }
  return "~Unable to Add Review";
}
function Tw_EditBookingReview($details){
  global $db;
  $e = "~Incomplete Details";  
  if(empty($details['id']) ||
     empty($details['text']) ||
     empty($details['rating']))return $e;
  $id = $db->where('id', $id)->update(B_REVIEWS, [    
    'text' => json_encode($details['text']),
    'rating' => $details['rating']
  ]);
  return $id ? $id : "~Unable to Edit Review";
}
function Tw_DeleteBookingReview($id){
  global $db;
  $e = "~Incomplete Details";  
  if(empty($id))return $e;
  $id = $db->where('id', $id)->delete(B_REVIEWS);
  return $id ? $id : "~Unable to Delete Review";
}
//Get Reviews
function Tw_GetReviews($entity = 1, $entity_id,
  $limit = 10, $offset = 0){
  global $db;
  $vendor = 1;
  $booking = 2;
  $food = 3;
  $entitis = [$vendor, $booking, $food];  
  if($entity == 1){
    $dbName = V_REVIEWS;
    $col = "vendor_id";
  }else if($entity == 2){
    $dbName = B_REVIEWS;
    $col = "booking_id";
  }else if($entity == 3){
    $dbName = F_REVIEWS;
    $col = "item_id";
  }
  if(!in_array($entity, $entitis))return [];
  $result = $db
  ->where($col, $entity_id)
  ->orderBy('id', 'desc')
  ->get($dbName, Array($offset, $limit));
  $final = [];
  foreach($result as $review){
    $userData = Tw_UserData($review->user_id);
    $final[] = [
     'id' => $review->id,
     'name' => $userData['name'],
     'time' => Tw_DateReadable($review->time),
     'text' => json_decode($review->text),
     'user_id' => $review->user_id,
     'rating' => $review->rating
    ];
  }
  return $final;
}
function Tw_GetReviewValues($issuer, $issuer_id){
  $res = Tw_GetReviews($issuer,$issuer_id,1000,0);  
  $counter = 0;
  $total = 0;
  $average = 0;
  $meta = array();
  $rv = [0, 0, 0, 0, 0];
  $totalValue = 0;
  foreach($res as $key => $r){
      $total += $r['rating'];
      if($r['rating'] > 0)$rv[$r['rating'] - 1]++;            
      $counter++;
  }
  $average = $counter == 0 ? 0 : $total / $counter;
  $meta['average'] = number_format((float)$average, 1, '.', '');
  $meta['values'] = array();
  $meta['people'] = count($res);
  $totalValue = $rv[0] + $rv[1] + $rv[2] + $rv[3] + $rv[4];
  $totalValue = $totalValue > 0 ? $totalValue : 1;
  foreach($rv as $key => $r)
    $meta['values'][] = $r / $totalValue * 100;

  return $meta;
}
function Tw_GetMyReviews($entity = 1, $user_id,
  $limit = 10, $offset = 0){
  global $db;
  $vendor = 1;
  $booking = 2;
  $food = 3;
  $entitis = [$vendor, $booking, $food];
  $e = "~Invalid Request";
  if($entity == 1)$dbName = V_REVIEWS;    
  else if($entity == 2)$dbName = B_REVIEWS;
  else if($entity == 3)$dbName = F_REVIEWS;
  if(!in_array($entity, $entitis))return $e;
  $result = $db
  ->where('user_id', $user_id)
  ->get($dbName, Array($offset, $limit));
  if(count($result) > 0){
    foreach ($result as $key => $res) {
     $result[$key]->text =  json_decode($res->text);
    }
  }
  return $result ? $result : $e;
}
function Tw_GetReviewData($entity = 1, $id){
  global $db;
  $vendor = 1;
  $booking = 2;
  $food = 3;
  $entitis = [$vendor, $booking, $food];
  $e = "~Invalid Request";
  if($entity == 1)$dbName = V_REVIEWS;    
  else if($entity == 2)$dbName = B_REVIEWS;
  else if($entity == 3)$dbName = F_REVIEWS;
  if(!in_array($entity, $entitis))return $e;
  $result = $db->where('id', $id)->get($dbName);
  return $result ? $result : $e;
}
//Payment Related
function Tw_CreateRazorOrder($data){
  $iv = "~Invalid Access";
  if(empty($data['user_id']) || empty($data['amount']))return $iv;
  if(!Tw_UserExistsById($data['user_id']))return $iv;
  global $db;
  $api = new Api(RPKY, RPSC);
  $amount = (int) filter_var($data['amount'], FILTER_SANITIZE_NUMBER_INT);
  $orderData = [    
      'amount'          => Tw_ToPaise($amount),
      //'amount'          => 100,
      'currency'        => 'INR',
      'payment_capture' => 1      
  ];
  $razorpayOrder = $api->order->create($orderData);  
  $result = $db->insert(PAYMENTS, [
    'order_id'=> $razorpayOrder['id'],
    'user_id' => $data['user_id'],    
    'amount'  => $data['amount'],
    'updated' => time(),
    'created' => time(),
    'issuer'  => $data['issuer'],    
    'status' => PAY_PENDING
  ]);
  return ['order_id' => $result, 'token' => $razorpayOrder['id']];
}

function Tw_RazorOrderTxn($order_id){
  if(empty($order_id)){
    return [];
  }
  $api = new Api(RPKY, RPSC);
  $payments = $api->order->fetch($order_id)->payments();
  return $payments;
}
/*
function Tw_PaymentSuccess($order_id){
  if(empty($order_id))return false;
  global $db;  
  $order = $db->where('order_id', $order_id)->getOne(ORDERS, $order_id);
  if($order){    
    return $order->status == CC_SUCCESS;
  }else{
    return false;
  }
}
*/
function Tw_TransactionData($txn_id){
  if(empty($txn_id)){
    return false;
  }
  global $db;
  $result = $db->where('id', $txn_id)->getOne(PAYMENTS);
  return $result;
}

function Tw_PaymentExists($id){
  if(empty($id))return false;
  global $db;  
  $db->where('id', $id)->get();
  return $db->count > 0;
}
function Tw_RazorOrderData($order_id){
  if(empty($order_id))return false;
  global $db;  
  $result = $db->where('order_id', $order_id)->getOne(PAYMENTS);
  return $result ? $result : false;
}
function Tw_OrderSuccess($order_id){
  if(empty($order_id))return false;
  global $db;
  $success = [PAY_AUTH, PAY_CAPT];
  $order = $db->where('order_id', $order_id)->getOne(ORDERS, $order_id);
  if($order){    
    return in_array($order->status, $success);
  }else{
    return false;
  }
}

function Tw_PaymentSuccess($id){
  if(empty($id))return false;
  global $db;
  $success = [PAY_AUTH, PAY_CAPT];
  $order = $db->where('id', $id)->getOne(ORDERS);
  if($order){    
    return in_array($order->status, $success);
  }else{
    return false;
  }
}

function Tw_UBKGPaymentStatus($details){
  $e = "~Unable to update";
  if(empty($details['order_id']))return $e;
  else if(empty($details['status']))return $e;
  global $db;  
  $order_id = $details['order_id'];
  $order = $db->where('id', $order_id)->getOne(PAYMENTS);  
  $success = ['authorized', 'captured'];
  if($order){
    if($details['status'] == 'paid'){      
      $payments = Tw_GetRazorOrderPayments($order->order_id);      
      if(count($payments) == 0){
        if($order->issuer == PAY_TABLE){
          $db->where('id', $order->data_id)->delete(TABLE_BOOKING);
        }
      }else{        
        $payData = $payments[0];        
        if($order->issuer == PAY_TABLE){
          if(in_array($payData['status'], $success)){
            $db
            ->where('txn_id', $order->id)
            ->update(TABLE_BOOKING, ['paid' => 1]);
            $db
            ->where('id', $order->id)
            ->update(PAYMENTS, ['payment_id' => $payData['id']]);
            $bookingData = Tw_BookingData($order->data_id);
            $title = "You Have A Table Booking";
            $content = "Press Here To View Details";
            $notification = [
              'sender_type' => USER,
              'sender_id' => $bookingData->user_id,
              'recipient_id' => $bookingData->vendor_id,
              'recipient_type' => VENDOR,
              'notify_type' => BKTBL,
              'title' => $title,
              'content' => $content,    
              'content_id' => $order->data_id,      
              'notifyData' => [
                'title' => $title,
                'content' => $content,
                'vendor_ids' => [$bookingData->vendor_id],
                'data' => [
                  'type' => BKTBL,
                  'type_data' => $order->data_id
                ]
              ]
            ];
            Tw_RegisterNotification($notification);       
            Tw_RegisterWebPush([
              'title' => $title,
              'content' => $content,
              'vendor_ids' => [$bookingData->vendor_id]
            ]);
          }else{
            $db->where('id', $order->data_id)->delete(TABLE_BOOKING);
          }    
        }else{

        }
      }
    }else{      
      if($order->issuer == PAY_TABLE){
        $db->where('id', $order->data_id)->delete(TABLE_BOOKING);
      }
    }
    return "Updated!";
  }else{    
    return $e;
  }
}

function Tw_UBKGPaymentStatus2($order_id){    
  global $db;    
  $order = $db->where('id', $order_id)->getOne(PAYMENTS);  
  $success = ['authorized', 'captured'];
  if($order){    
      $payments = Tw_GetRazorOrderPayments($order->order_id);      
      if(count($payments) == 0){
        if($order->issuer == PAY_TABLE){
          $db->where('txn_id', $order->id)->update(TABLE_BOOKING, ['paid' => NOT_PAID, 'status' => PAYCANCEL]);
          return NOT_PAID;
        }
      }else{        
        $payData = $payments[0];        
        if($order->issuer == PAY_TABLE){
          if(in_array($payData['status'], $success)){
            $db
            ->where('txn_id', $order->id)
            ->update(TABLE_BOOKING, ['paid' => 1]);
            $bookingData = Tw_BookingData($order->data_id);
            $title = "You Have A Table Booking";
            $content = "Press Here To View Details";
            $notification = [
              'sender_type' => USER,
              'sender_id' => $bookingData->user_id,
              'recipient_id' => $bookingData->vendor_id,
              'recipient_type' => VENDOR,
              'notify_type' => BKTBL,
              'title' => $title,
              'content' => $content,    
              'content_id' => $order->data_id,      
              'notifyData' => [
                'title' => $title,
                'content' => $content,
                'vendor_ids' => [$bookingData->vendor_id],
                'data' => [
                  'type' => BKTBL,
                  'type_data' => $order->data_id
                ]
              ]
            ];
            Tw_RegisterNotification($notification);       
            Tw_RegisterWebPush([
              'title' => $title,
              'content' => $content,
              'vendor_ids' => [$bookingData->vendor_id]
            ]);
          }else{
            $db->where('id', $order->data_id)->delete(TABLE_BOOKING);
          }
          return PAID;
        }else{          
          //For Order Section
        }
      }    
  }else{    
    return NOT_PAID;
  }
}

function Tw_UODRPaymentStatus($details){    
  $e = "~Unable to update";
  if(empty($details['order_id']))return $e;
  else if(empty($details['status']))return $e;
  global $db;  
  $order_id = $details['order_id'];
  $order = $db->where('order_id', $order_id)->getOne(PAYMENTS);  
  $odr_id = $order->data_id;
  $success = ['authorized', 'captured'];
  if($order){
    if($details['status'] == 'paid'){ 
      $dbodr = $db
          ->where('id', $order->data_id)
          ->getOne(ORDERS);
      if($dbodr->paid == true || $dbodr->paid == 1){
            return "Updated";
      }
      $payments = Tw_GetRazorOrderPayments($order->order_id);
      if(count($payments) == 0){
        $db
        ->where('id', $odr_id)
        ->update(ORDERS, ['paid' => 1]); 
        $db
        ->where('order_id', $order->data_id)
        ->update(ORDER_VENDOR, ['paid' => 1]);          
        $vs = [];                    
        $vdata = Tw_GetVendorsOfOrder($odr_id);          
        foreach($vdata as $v)$vs[] = ['vendor_id' => $v->id];
        Tw_ProcessOrderNotify($vs, $order->user_id);
      }else{        
        $payData = $payments[0];        
        if(in_array($payData['status'], $success)){          
          $db
          ->where('id', $odr_id)
          ->update(ORDERS, ['paid' => 1]);
          $db
            ->where('id', $order->id)
            ->update(PAYMENTS, ['payment_id' => $payData['id']]);
          $db
          ->where('order_id', $order->data_id)
          ->update(ORDER_VENDOR, ['paid' => 1]);          
          $vs = [];                    
          $vdata = Tw_GetVendorsOfOrder($odr_id);          
          foreach($vdata as $v)$vs[] = ['vendor_id' => $v->id];
          Tw_ProcessOrderNotify($vs, $order->user_id);
        }else{
          //$db->where('id', $odr_id)->delete(ORDERS);
          //$db->where('order_id', $odr_id)->delete(ORDER_VENDOR);
          //$db->where('order_id', $odr_id)->delete(ORDER_FOOD);
        }        
      }
    }else{      
      //$db->where('id', $odr_id)->delete(ORDERS);
      //$db->where('order_id', $odr_id)->delete(ORDER_VENDOR);
      //$db->where('order_id', $odr_id)->delete(ORDER_FOOD);
    }
    return "Updated!";
  }else{    
    return $e;
  }
}

function Tw_UODRPaymentStatus2($order_id){  
  if(empty($details['order_id']))return false;
  else if(empty($details['status']))return false;
  global $db;  
  $order_id = $details['order_id'];
  $order = $db->where('id', $order_id)->getOne(PAYMENTS);  
  $odr_id = $order->data_id;
  $success = ['authorized', 'captured'];
  if($order){
      $payments = Tw_GetRazorOrderPayments($order->order_id);      
      if(count($payments) == 0){
        //$db->where('id', $odr_id)->delete(ORDERS);
        //$db->where('order_id', $odr_id)->delete(ORDER_VENDOR);
        //$db->where('order_id', $odr_id)->delete(ORDER_FOOD);
        return false;
      }else{        
        $payData = $payments[0];        
        if(in_array($payData['status'], $success)){          
          $db
          ->where('id', $odr_id)
          ->update(ORDERS, ['paid' => 1]);
          $db
          ->where('order_id', $order->data_id)
          ->update(ORDER_VENDOR, ['paid' => 1]);          
          $vs = [];                    
          $vdata = Tw_GetVendorsOfOrder($odr_id);          
          foreach($vdata as $v)$vs[] = ['vendor_id' => $v->id];
          Tw_ProcessOrderNotify($vs, $order->user_id);
          return true;
        }else{
          //$db->where('id', $odr_id)->delete(ORDERS);
          //$db->where('order_id', $odr_id)->delete(ORDER_VENDOR);
          //$db->where('order_id', $odr_id)->delete(ORDER_FOOD);
          return false;
        }        
      }    
  }else{    
    return false;
  }
}

function Tw_GetRazorOrderPayments($order_id){
  $url = "https://api.razorpay.com/v1/orders/{$order_id}/payments";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_USERPWD, RPKY.':'.RPSC);
  $result = curl_exec($ch);
  $error = false;
  if (curl_errno($ch)) {
      $error = true;
  }
  curl_close ($ch);  
  if ($error) {
    return [];
  }else{
    $result = json_decode($result, true);
    if(empty($result['items'])){
      return [];
    }else{
      return $result['items'];
    }    
  }
}

function Tw_PaymentStatus($status){
  switch($status){    
    case 'captured':
    return PAY_CAPT;
    case 'authorized':
    return PAY_AUTH;
    case 'failed':
    return PAY_FAIL;
  }
}

function Tw_CreateRefund($details){  
  if(empty($details['amount'])  ||
    empty($details['issuer'])   ||    
    empty($details['user_id'])  ||
    empty($details['type'])     ||    
    empty($details['issuer_id']))return false;    
  global $db; 
  $reason = empty($details['reason']) ? '' : $details['reason'];
  $is_partial = empty($details['is_partial']) || $details['is_partial'] == false ? false : true;
  if(!Tw_UserExistsById($details['user_id']))return false;
  if($details['issuer'] == PAY_TABLE){
    $db
    ->where('id', $details['issuer_id'])
    ->update(TABLE_BOOKING,['status' => $details['type'], 'cancel' => $details['type']]);
    $payments = Tw_GetRazorOrderPayments(Tw_TransactionData($details['txn_id'])->order_id);
    $payData = $payments[0];
    $full = true;
  }else if($details['issuer'] == PAY_FOOD){
    //$db
    //->where('id', $details['issuer_id'])
    //->update(ORDERS, ['status' => VDRFDCANCEL]);
    $payments = Tw_GetRazorOrderPayments(Tw_TransactionData($details['txn_id'])->order_id);
    $payData = $payments[0];
    $full = false;
    $amount = Tw_VendorOrderData($details['issuer_id']);
  }
  $result = $db->insert(REFUNDS, [
    'amount' => $details['amount'],
    'issuer' => $details['issuer'],
    'user_id' => $details['user_id'],
    'type' => $details['type'],
    'status' => REFUND_CREATED,
    'razor_id' => $payData['id']
  ]);  
  $api = new Api(RPKY, RPSC);
  $payment = $api->payment->fetch($payData['id']);
  if($is_partial){
    $refund = $payment->refund(array('amount' => Tw_ToPaise($details['amount']), 'speed' => 'optimum', 'notes' => ['refund_id' => $result]));
  }else{
    $refund = $payment->refund(array('speed' => 'optimum', 'notes' => ['refund_id' => $result]));
  }
  return $result ? $result : false;
}
function Tw_GetRefunds($user_id, $limit, $offset){  
  if(empty($user_id))return false;
  global $db;  
  if(!Tw_UserExistsById($user_id))return false;  
  $refunds = $db
  ->where('user_id', $user_id)
  ->orderBy('id', 'desc')
  ->get(REFUNDS, Array($offset, $limit));
  $final = [];
  foreach($refunds as $refund){
    $refund->time = Tw_TimeHumanType($refund->time);
    $final[] = $refund;
  }
  return $refunds ? $final : [];
}
function Tw_UpdateRefundStatus($refund_id, $status){
  global $db;
  $refund = $db->where('id', $refund_id)->getOne(REFUNDS);
  if($refund->status == REFUND_PROCESSED){
    return true;
  }
  if($status == REFUND_PROCESSED){
    $title = "Your Refund Has Been Processed";
    $content = "You Should Able See Money In Account Now or Shortly";        
  }else if($status == REFUND_FAILED){
    $title = "Refund Failed Don't Care!";
    $content = "We Will Refund Your Amount Within 24 Hours, I problem persists we will call you!";    
  }else if($status == REFUND_SPDCHG){
    $title = "Refund Delayed!";
    $content = "Your Account Does Not Support Instant Refund, It Will Take 5-6 To Refund Your Amount!";    
  }
  if($status != REFUND_CREATED){
    $notification = [
      'sender_type' => APP,
      'sender_id' => 0,
      'recipient_id' => $refund->user_id,
      'recipient_type' => USER,
      'notify_type' => RFSCH,
      'title' => $title,
      'content' => $content,      
      'content_id' => $refund->id,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'user_ids' => [$refund->user_id],     
        'data' => [
          'type' => RFSCH,
          'type_data' => $refund->id
        ]
      ]
    ];    
    Tw_RegisterNotification($notification); 
  }  
  if($status == REFUND_SPDCHG){
    $data = ['speedc' => 1];
  }else{
    $data = ['status' => $status];
  }
  $db->where('id', $refund_id)->update(REFUNDS, $data);
  return true;
}
//Referral Funtions
function Tw_CreateReferral($sender_id, $receiver_id){
  global $db;
  if(empty($sender_id) || empty($receiver_id)){
    return false;
  }
  if(Tw_ReferralExsits($sender_id, $receiver_id)){
    return false;
  }
  $result = $db->insert(REFERS, [
    'sender_id' => $sender_id,
    'receiver_id' => $receiver_id
  ]);
  return $result ? [
    'id' => $result,
    'sender_id' => $sender_id,
    'receiver_id' => $receiver_id
  ] : false;
}

function Tw_CheckRefApplied($receiver_id){
  global $db;
  $db  
  ->where('receiver_id', $receiver_id)
  ->get(REFERS);
  return $db->count > 0;
}
function Tw_GetSenderFromCode($code){
  $code = explode('CLF', $code);
  $id = count($code) - 1;
  $id = intval($code[$id]);
  return $id == 0 ? false : $id;
}
function Tw_DeleteReferral($id){
  global $db;
  if(empty($id))return false;  
  $result = $db->where('id', $id)->delete(REFERS);
  return $result;
}

function Tw_DeleteRefBySender($sender_id){
  global $db;
  if(empty($sender_id))return false;  
  $result = $db->where('sender_id', $sender_id)->delete(REFERS);
  return $result;
}

function Tw_ReferralExsits($sender_id, $receiver_id){
  global $db;
  $db
  ->where('sender_id', $sender_id)
  ->where('receiver_id', $receiver_id)
  ->get(REFERS);
  return $db->count > 0;
}
function Tw_GetReferrals($sender_id){
  global $db;
  $result = $db
  ->where('sender_id', $sender_id)
  ->get(REFERS);  
  $u = Tw_UserData($sender_id);  
  $n = $u['first_name'];
  $code = $n[0].$n[1].$n[2].'CLF'.$u['id'];
  $applied_count = count($result);
  $giftData = Tw_GetGiftsTable();  
  $final = [
    'orders' => $applied_count,
    'points' => $applied_count * ORDER_POINT,
    'code' => strtoupper($code),
    'gifts' => $giftData['data'],
    'keys' => $giftData['keys'],
    'users' => []
  ];  
  foreach($result as $refer){  
    $final['users'][] = Tw_BasicUserData($refer->sender_id);     
  }
  return $final;
}
function Tw_GetReferPoints($sender_id){
  global $db;
  $result = $db
  ->where('sender_id', $sender_id)
  ->get(REFERS);   
  return count($result) * ORDER_POINT;
}
function Tw_GetGiftsTable(){
  global $db;
  $gifts = $db->get(GIFTS);
  $final = [];
  foreach($gifts as $gift)
    $final[$gift->level][] = $gift;  
  $keys = array_keys($final);
  $finalKys = [];
  foreach($keys as $k){
    $finalKys[] = [
      'orders' => $k / ORDER_POINT,
      'points' => $k
    ];
  }
  arsort($keys);
  arsort($final);
  $data = [
    'data' => $final,
    'keys' => $finalKys
  ];
  return $data;
}

function Tw_ReferCliam($sender_id){
  global $db;
  $result = $db
  ->where('sender_id', $sender_id)
  ->get(REFERS);
  $u = Tw_UserData($sender_id);  
  $applied_count = count($result);
  $points = $applied_count * ORDER_POINT;
  $giftData = Tw_GetGiftsTable();  
  $keys = $giftData['keys'];
  $claimable = false;
  $giftData = $giftData['data'];
  $finalGift = [];
  $l_points = 0;  
  foreach ($keys as $key){    
    
    if($points < $key['points']){
      $l_points = $key['points'] - $points;
      $points = $key['points'];
      $claimable = false;
      break;
    }else{
      if(!Tw_HasClaimed($sender_id, $key['points'])){        
        $finalGift = $giftData[$key['points']];
        $claimable = true;
        break;
      }
    }

  }  
  return [
    'claimable' => $claimable,
    'p_left' => $l_points,
    'gifts' => $finalGift,
    'points' => $points,
    'applied' => Tw_CheckRefApplied($sender_id)
  ];
}

function Tw_HasClaimed($sender_id, $points){
  global $db;
  $db
  ->where('user_id', $sender_id)
  ->where('points', $points, '>=')
  ->get(CLAIMS);
  return $db->count > 0;
}

function Tw_CreateClaim($user_id, $points){
  global $db;
  $result = $db
  ->insert(CLAIMS, [
    'user_id' => $user_id,
    'points' => $points
  ]);
  return $result;
}
//Hotel Visits
function Tw_CaptainHotelVisit($details, $byqr = false){
  global $db;
  if(empty($details['table_id']))return false;  
  if($byqr && empty($details['user_id']))return false;
  $tableData = Tw_TableData($details['table_id']);
  $areaData = Tw_AreaData($tableData->area_id);
  $bkg_id = !empty($details['bkg_id']) && is_numeric($details['bkg_id']) ? $details['bkg_id'] : 0;  
  
  $bal_amt = !empty($details['bal_amt']) && is_numeric($details['bal_amt']) ? $details['bal_amt'] : 0;  
  
  $pmy = !empty($details['pmy']) && is_numeric($details['pmy']) ? $details['pmy'] : 1;
  
  $visitData = [
    'byqr' => $byqr,
    'bkg_id' => $bkg_id,
    'user_id' => $byqr ? $details['user_id'] : 0,
    'vendor_id' => $areaData->vendor_id,    
    'tableno' => $tableData->number,
    'table_id' => $tableData->id,
    'billno' => 0,
    'stamp' => time(),
    'cp_id' => empty($details['cp_id']) ? 0 : $details['cp_id'],
    'area_id' => $areaData->id,
    'bal_amt' => $bal_amt,    
    'pmy' => $pmy,
    'area' => $areaData->area    
  ];  
  /*if($byqr){
    $title = 'You Have New Hotel Order!';
    $content = 'Press Here to View Order';
    $notification = [
      'sender_type' => USER,
      'sender_id' => $details['user_id'],
      'recipient_id' => $areaData->vendor_id,
      'recipient_type' => VENDOR,
      'notify_type' => CVHO,
      'title' => $title,
      'content' => $content,
      'content_id' => 0,      
      'notifyData' => [
        'title' => $title,
        'content' => $content,
        'vendor_ids' => [$areaData->vendor_id],
        'data' => [
          'type' => CVHO,
          'type_data' => 0
        ]
      ]
    ];
    Tw_RegisterNotification($notification);
  }*/
  $result = $db->insert(VISITS, $visitData);
  return $result;
}

function Tw_GenerateBillNr($stamp, $vendor_id){
  global $db;
  $ss = date('Y-m-d', $stamp).' 00:00:00';
  $es = date('Y-m-d', $stamp).' 23:59:59';
  $s = strtotime($ss);
  $e = strtotime($es);
  $h = $db->where('vendor_id', $vendor_id)->where('stamp', Array($s, $e), "BETWEEN")
  ->get(VISITS);
  $o = $db->where('vendor_id', $vendor_id)->where('time', Array($s, $e), "BETWEEN")
  ->get(ORDER_VENDOR);
  $t = $db->where('vendor_id', $vendor_id)->where('time', Array($s, $e), "BETWEEN")
  ->get(TKAWAY);
  $visits = [];
  $orders = [];
  $tkaways = [];  
  $a = count($h);
  $b = count($o);
  $c = count($t);
  foreach ($h as $hs) {
    $visits[] = $hs->billno;
  }
  foreach ($o as $os) {
    $orders[] = $os->billno; 
  }
  foreach ($t as $ts) {
    $tkaways[] = $ts->billno;
  }
  $a = count($visits) > 0 ? max($visits) : 0;
  $b = count($orders) > 0 ? max($orders) : 0;
  $c = count($tkaways) > 0 ? max($tkaways) : 0;
  return max(array($a,$b,$c)) + 1;
}

function Tw_TableFromAreaNumber($area_id, $number){
  if(empty($area_id) || empty($number))return false;
  global $db;
  $data = $db
  ->where('area_id', $area_id)
  ->where('number', $number)
  ->getOne(V_TABLE);
  return $data;
}

function Tw_DeleteTableVisit($details){
  global $db;
  if(empty($details['table_id']) && empty($details['visit_id'])){
    return "~Try Again";
  }
  if(empty($details['table_id'])){
    $visit = Tw_VisitData($details['visit_id']);
    $tableData = Tw_TableData($visit->table_id);
    $visit_id = $visit->id;
  }else{
    $tableData = Tw_TableData($details['table_id']);
    $visit_id = $tableData->visit_id;
  }
  if($tableData->visit_id != 0){
    if($tableData->sp_id != 0 || $tableData->tkaway == 1){
      $db
      ->where('id', $tableData->id)
      ->delete(V_TABLE);
    }else{
      $db
      ->where('id', $tableData->id)
      ->update(V_TABLE, [
        'status' => 0,
        'visit_id' => 0
      ]);
    }    
    $db->where('id', $visit_id)->delete(VISITS);
  }
  return "Updated!";
}

function Tw_AddFoodToVisit($details){
  $e = "~Try Again";
  if(empty($details['items'])){
    return $e;
  }else if(empty($details['table_id']) && empty($details['visit_id'])){
    return $e;
  }
  global $db;  
  if(empty($details['table_id'])){
    $visitData = Tw_VisitData($details['visit_id']);
    $vendor_id = $visitData->vendor_id;
    $vendor = Tw_VendorData($vendor_id);
    $table_id = $visitData->table_id;
    $tableData = Tw_TableData($table_id);
    $visit_id = $details['visit_id'];
  }else{
    $tableData = Tw_TableData($details['table_id']);
    $areaData = Tw_AreaData($tableData->area_id);    
    $table_id = $details['table_id'];
    $visit_id = $tableData->visit_id;    
    $vendor_id = $areaData->vendor_id;
    $vendor = Tw_VendorData($vendor_id);
  }  
  $captain = empty($details['cp_id']) ? false : true;
  $items = empty($details['items']) ? [] : json_decode($details['items'], true);  
  error_reporting(E_ERROR | E_PARSE);
  
  $previous = Tw_GetFoodOfVisit($visit_id);  
  $temp = json_encode($previous);
  $preCopy = json_decode($temp);

  if($tableData->status == TBL_PAID){
    return '~Can Not Change Receipt!';
  }

  foreach($items as $item){
    $notFound = true;
    foreach($previous as $key => $pre){
      if($item['food_id'] == $pre->food_id){        
        $notFound = false;        
        if($captain && $item['quantity'] < $pre->quantity){
          return '~Cancel Via Counter!';
        }else if($item['quantity'] < $pre->quantity 
          && $vendor->ocntm > 0 
          && Tw_TimeDiffMin(time(), $pre->updated) > $vendor->ocntm){
          return '~Cancel Time Exceed!';
        }else if($item['quantity'] != $pre->quantity){
          $db->where('id', $pre->id)->update(VISIT_FOOD, [
            'quantity' => $item['quantity'],
            'amount' =>  $item['amount'],
            'note' => empty($item['note']) ? '' : $item['note'],
            'updated' => time()
          ]);          
        }        
        unset($preCopy[$key]);        
      }      
    }
    if($notFound){      
      $db->insert(VISIT_FOOD, [
        'visit_id' => $visit_id,
        'food_id' => $item['food_id'],
        'quantity' => $item['quantity'],
        'note' => empty($item['note']) ? '' : $item['note'],
        'amount' => $item['amount'],
        'updated' => time()
      ]);
    }
  }
  if($captain && count($preCopy) > 0){
    return '~Cancel Via Counter!';
  }
  if(count($preCopy) > 0 && $vendor->ocntm > 0 
    && Tw_TimeDiffMin(time(), $pre->updated) > $vendor->ocntm){
    return '~Cancel Time Exceed!';
  }
  if($vendor->has_kot == false){
      foreach($preCopy as $copy){        
          $db->where('id', $copy->id)->delete(VISIT_FOOD);        
      }
  }else{
    foreach($preCopy as $copy){        
        $db->where('id', $copy->id)->update(VISIT_FOOD, [
          'quantity' => 0,
          'amount' => 0
        ]);        
    }
  }
  Tw_ProcessTax(['visit_id' => $visit_id], $vendor_id);
  Tw_UpdateTableStatus([    
   'table_id' => $table_id,
   'visit_id' => $visit_id,
   'status' => TBL_UNPAID
  ]);
  if(empty($details['receipt'])){
    return 'Updated!';
  }else{
    $receipt = Tw_GetTableReceipt(['table_id' => $table_id]);
    $tableData = Tw_TableData($table_id);
    $visit = Tw_VisitData($tableData->visit_id);
    $billNo = $visit->billno;
    $total = 0;
    foreach($receipt as $rp){        
        $total += $rp->amount;
    }
    return ['total' => $total, 'receipt' => $receipt, 'visit_id' => $visit->id, 'billno' => $billNo];
  }
}

function Tw_ProcessTax($data, $vendor_id){
  global $db;
  if(!empty($data['visit_id'])){
    $allFood = Tw_GetFoodOfVisit($data['visit_id']);
    $col = 'visit_id';
    $id = $data['visit_id'];
  } else if(!empty($data['bkg_id'])) {
    $allFood = Tw_GetFoodOfBooking($data['bkg_id']);
    $id = $data['bkg_id'];
    $col = 'bkg_id';
  }else{
    return;
  }
  if(Tw_VendorHasTax($vendor_id)){    
    $taxes = Tw_GetClufterTaxes($vendor_id);
    $amount = 0; // Amount Without Tax
    $total_tax_amt = 0; // Tax Added
    $total_amt = 0; // Amount With Tax Added
    foreach($allFood as $food){
      $amount += $food->amount;
    }
    foreach($taxes as $tax){      
      if($tax->applied){
        $data = $db
        ->where('tax_id', $tax->id)
        ->where($col, $id)
        ->getOne(TAX_APPLIED);
        $tax_per = $tax->percent;
        $tax_amt = ceil($amount * $tax->percent / 100);
        $total_tax_amt += $tax_amt;
        if($data){
          $db->where('id', $data->id)->update(TAX_APPLIED, [          
            'percent' => $tax_per,
            'tax_amt' => $tax_amt
          ]);
        }else{        
          $data = [
            'tax_id' => $tax->id,            
            'percent' => $tax_per,
            'tax_amt' => $tax_amt
          ];
          $data[$col] = $id;
          $db->insert(TAX_APPLIED, $data);
        }
      }     
    }
    $total_amt = $total_tax_amt + $amount;
    if($col == 'visit_id'){
      $db->where('id', $id)->update(VISITS, [
        'tax' => 1,
        'amount' => $amount,
        'tax_amt'  => $total_tax_amt,
        'total_amt'  => $total_amt
      ]); // Applying Tax               
    }else{
      $total_amt += TB_AMOUNT;
      $db->where('id', $id)->update(TABLE_BOOKING, [
        'tax' => 1,
        'amount' => $total_amt,
        'tx_amt'  => $total_tax_amt,
        'fd_amt'  => $amount
      ]); // Applying Tax
    }
  }else{    
    $amount = 0; // Amount Without Tax    
    foreach($allFood as $food){
      $amount += $food->amount;
    } 
    if($col == 'visit_id'){
      $db->where('id', $id)->update(VISITS, ['tax' => 0,'amount' => $amount,'tax_amt'  => 0,'total_amt'  => $amount]);
    }else{
      $total_amount = $amount + TB_AMOUNT;
      $db->where('id', $id)->update(TABLE_BOOKING, ['tax' => 0,'amount' => $total_amount,'tx_amt'  => 0,'fd_amt'  => $amount]);
    }
  }  
}

function Tw_GetVisits($details){
  global $db;
  if(empty($details['user_id']) && empty($details['vendor_id'])){
    return false;
  }
  $hasVendor = false;
  $date = empty($details['time']) ? -1 : $details['time'];
  $offset = empty($details['offset']) ? 0 : $details['offset'];
  $limit = empty($details['limit']) ? 10 : $details['limit'] ;
  if($date != -1){
    $start = date('Y-m-d 00:00:00', $date);
    $end = date('Y-m-d 23:59:59', $date);    
    $db->where('time', $start, '>=');
    $db->where('time', $end, '<=');
  }
  if(!empty($details['user_id'])){
    $db->where('user_id', $details['user_id']);
  }else if(!empty($details['vendor_id'])){
    $hasVendor = true;
    $db->where('vendor_id', $details['vendor_id']);
  }else{
    return [];
  }
  if(array_key_exists("status", $details)){
    $db->where('status', $details['status']);
  }
  
  $final = [];
  $result = $db->orderBy('id', 'desc')->get(VISITS, Array($offset, $limit));
  foreach($result as $visit){      
    $amount = 0;
    $visit->items = Tw_GetFoodOfVisit($visit->id);    
    if($visit->tax == 1){
      $visit->taxes = Tw_GetVisitTaxes($visit->id);
    }else{      
      $visit->taxes = [];
    }
    $vendor = Tw_VendorData($visit->vendor_id);
    $visit->vendor_name = $vendor->name;
    $visit->vendor_logo = $vendor->logo;
    $visit->timestamp = strtotime($visit->time);
    $visit->time = Tw_TimeHumanType($visit->stamp);    
    $visit->vendor_hash = $vendor->logo_hash;    
    if($hasVendor && $visit->byqr == 1 && $visit->user_id != 0){
      $userData = Tw_UserData($visit->user_id);
      $visit->customer = [
        'first_name' => $userData['first_name'],
        'last_name' => $userData['last_name'],        
        'phone' => $userData['phone_no']
      ];
    }
    $final[] = $visit;
  }
  return $result ? $final : [];
}

function Tw_CountUserVisits($user_id){
  if(empty($user_id)){
    return 0;
  }
  global $db;
  $db->where('user_id', $user_id)->get(VISITS);
  return $db->count;
}
function Tw_GetModifiedVisits($details){
  global $db;
  if(empty($details['id'])){
    return false;
  }  
  $start = empty($details['start']) ? time() : $details['start'];
  $end = empty($details['end']) ? time() : $details['end'];
  $start = date('Y-m-d', $start).' 00:00:00';
  $end = date('Y-m-d', $end).' 23:59:59';  
  $final = [];
  $result = $db->rawQuery("SELECT * FROM `visits` WHERE time BETWEEN '{$start}' AND '{$end}' AND vendor_id = {$details['id']}");  
  foreach($result as $visit){      
    $final[] = [
      'i' => $visit->id,
      'ty' => $visit->byqr,
      'tn' => $visit->tableno,
      'p' => $visit->pmy,
      'bn' => $visit->billno,
      'd' => $visit->discount,
      'tm' => Tw_TimeHumanType($visit->stamp),
      'ta' => $visit->tax_amt,
      'a' => $visit->total_amt
    ];    
  }
  return $result ? $final : [];
}

function Tw_GetModifiedTakeAway($details){
  global $db;
  if(empty($details['id'])){
    return false;
  }  
  $start = empty($details['start']) ? time() : $details['start'];
  $end = empty($details['end']) ? time() : $details['end'];
  $start = strtotime(date('Y-m-d', $start).' 00:00:00');
  $end = strtotime(date('Y-m-d', $end).' 23:59:59');  
  $final = [];
  $result = $db->rawQuery("SELECT * FROM `tkaway` WHERE time BETWEEN '{$start}' AND '{$end}' AND vendor_id = {$details['id']}");  
  foreach($result as $tk){      
    $final[] = [
      'i' => $tk->id,      
      'p' => $tk->pmy,
      'd' => $tk->discount,
      'bn' => $tk->billno,
      'x' => $tk->xamt,
      'tm' => Tw_TimeHumanType($tk->time),
      'ta' => $tk->tx_amt,
      'a' => $tk->total_amt
    ];    
  }
  return $result ? $final : [];
}

function Tw_GetModifiedDelivery($details){
  global $db;
  if(empty($details['id'])){
    return false;
  }  
  $start = empty($details['start']) ? time() : $details['start'];
  $end = empty($details['end']) ? time() : $details['end'];
  $start = strtotime(date('Y-m-d', $start).' 00:00:00');
  $end = strtotime(date('Y-m-d', $end).' 23:59:59');  
  $final = [];
  $result = $db->rawQuery("SELECT * FROM `order_vendor` WHERE time BETWEEN '{$start}' AND '{$end}' AND vendor_id = {$details['id']} AND paid = 1");  
  foreach($result as $dl){    
    $final[] = [
      'i' => $dl->id,      
      'tm' => Tw_TimeReadable2($dl->time),
      's' => $dl->status,
      'a' => $dl->amount
    ];    
  }
  return $result ? $final : [];
}

function Tw_GetModifiedBooking($details){
  global $db;
  if(empty($details['id'])){
    return false;
  }  
  $start = empty($details['start']) ? time() : $details['start'];
  $end = empty($details['end']) ? time() : $details['end'];
  $start = strtotime(date('Y-m-d', $start).' 00:00:00');
  $end = strtotime(date('Y-m-d', $end).' 23:59:59');  
  $final = [];
  $result = $db->rawQuery("SELECT * FROM `table_booking` WHERE booked BETWEEN '{$start}' AND '{$end}' AND vendor_id = {$details['id']} AND paid = 1");  
  foreach($result as $bk){    
    $final[] = [
      'i' => $bk->id,      
      'tm' => Tw_TimeReadable2($bk->booked),
      's' => $bk->status,
      'at' => $bk->attent,
      'ta' => $bk->tx_amt,
      'a' => $bk->amount
    ];    
  }
  return $result ? $final : [];
}

function Tw_CalVisitHistory($details){
  global $db;
  if(empty($details['vendor_id'])){
    return false;
  }
  $hasVendor = false;
  $date = empty($details['time']) ? -1 : $details['time'];  
  if($date != -1){
    //$p_date = date('Y-m-d', $date);
    //$n_date = date('Y-m-d', strtotime('+1 day', $date));
    //$db->where('time', $p_date, '>=');
    //$db->where('time', $n_date, '<');

    $startS = date('Y-m-d', $date).' 00:00:00';
    $endS = date('Y-m-d', $date).' 23:59:59';
    $start = strtotime($startS);
    $end = strtotime($endS);
    $db->where('stamp',  Array($start, $end), "BETWEEN");
  }
  $db->where('vendor_id', $details['vendor_id']);    
  $result = $db->get(VISITS);
  $qr = 0;
  $captain = 0;
  $total = 0;
  $tax = 0;
  foreach($result as $visit){          
    $total += $visit->total_amt;
    $tax += $visit->tax_amt;
    if($visit->byqr == 1){
      $qr++;
    }else{
      $captain++;
    }
  }
  return [
    'qr' => $qr,
    'tax' => $tax,
    'captain' => $captain,
    'total' => $total
  ];
}
function Tw_GetCurrentVisit($user_id){
  global $db;
  if(empty($user_id)){
    return false;
  }
  $latest = $db->where('user_id', $user_id)->orderBy('id', 'desc')->getOne(VISITS);
  if($latest){
    $table_id = $latest->table_id;
  }else{
    return false;
  }
  $table = $db
  ->where('id', $table_id)
  ->where('status', [TBL_FREE, TBL_PAID], 'NOT IN')
  ->getOne(V_TABLE);  
  if(!empty($table) && $table->visit_id != $latest->id){
    return false;
  }
  return $table ? [
    'vendor_id' => $latest->vendor_id,
    'visit_id' => $latest->id 
  ] : false;
}

function Tw_ValidateVisitEnd($visit_id){
  global $db;
  if(empty($visit_id)){
    return false;
  }
  $vd = Tw_VisitData($visit_id);
  $visit = $db
  ->where('id', $vd->table_id)
  ->getOne(V_TABLE);
  if($visit){
    if($visit->visit_id != $visit_id){
      return [
        'ended' => true        
      ];
    }else if($visit->status == TBL_FREE || $visit->status == TBL_PAID){
      return [
        'ended' => true
      ];
    }else if($visit->status == TBL_UNPAID){
      return [
        'ended' => false,
        'msg' => 'Payment Process Not Completed, Try Again!'
      ];
    }else if($visit->status == TBL_PRESENT){
      return ['ended' => 300];
    }
  }else{
    return [
      'ended' => true
    ];
  }  
}

function Tw_CountPendingVisits($details){
  global $db;
  if(empty($details['vendor_id'])){
    return false;
  }
  $date = empty($details['time']) ? -1 : $details['time'];
  $offset = empty($details['offset']) ? 0 : $details['offset'];
  $limit = empty($details['limit']) ? 10 : $details['limit'] ;
  if($date != -1){
    //$p_date = date('Y-m-d', $date);
    //$n_date = date('Y-m-d', strtotime('+1 day', $date));
    //$db->where('time', $p_date, '>=');
    //$db->where('time', $n_date, '<');
    $startS = date('Y-m-d', $date).' 00:00:00';
    $endS = date('Y-m-d', $date).' 23:59:59';
    $start = strtotime($startS);
    $end = strtotime($endS);
    $db->where('stamp',  Array($start, $end), "BETWEEN");
  }
  $db
  ->where('vendor_id', $details['vendor_id'])
  ->where('status', 0)
  ->get(VISITS);  
  return $db->count;
}

function Tw_UpdateVisitStatus($id, $status){
  global $db;
  $e = '~Unable to update!';
  $s = 'Updated Successfully!';
  if(empty($id))return $e;  
  $visitData = Tw_VisitData($id);
  $vendorData = Tw_VendorData($visitData->vendor_id);
  if(!$visitData){
    return $e;  
  }else if($visitData && $visitData->status == $status){
    return $s;  
  }
  $result = $db
  ->where('id', $id)  
  ->update(VISITS, [
    'status' => $status
  ]);

  $title = "{$vendorData->name} Order Recivied, Please Wait Few Minutes!";
  $content = "Please Keep Patience Your Food is Being Prepared!";
  $notification = [
    'sender_type' => VENDOR,
    'sender_id' => $visitData->vendor_id,
    'recipient_id' => $visitData->user_id,
    'recipient_type' => USER,
    'notify_type' => VACHO,
    'title' => $title,
    'content' => $content,
    'content_id' => $id,      
    'notifyData' => [
      'title' => $title,
      'content' => $content,
      'user_ids' => [$visitData->user_id],
      'data' => [
        'type' => VACHO,
        'type_data' => $id
      ]
    ]
  ];
  Tw_RegisterNotification($notification);

  return $result ? $s : $e;
}

function Tw_VisitData($id){
  global $db;
  if(empty($id))return false;  
  $result = $db  
  ->where('id', $id)
  ->getOne(VISITS);  
  return $result;
}

function Tw_GetFoodOfVisit ($id){
  global $db;
  if(empty($id))return [];
  $result = $db->where('visit_id', $id)->get(VISIT_FOOD);
  $final = [];
  foreach($result as $item){
    $food = Tw_VendorFoodData($item->food_id);
    $food->id = $item->id;
    $food->food_id = $item->food_id;
    $food->per_price = $item->amount/$item->quantity;
    $food->amount = $item->amount;
    $food->quantity = $item->quantity;
    $food->pquantity = $item->pquantity;
    $food->updated = $item->updated;
    $final[] = $food;
  }
  return $result ? $final : [];
}
//Slots Part

function Tw_GetVendorSlots($vendor_id){
  global $db;
  $db->where('vendor_id', $vendor_id)
  ->where('status', 1);
  $final = [];
  $slots = $db->get(HTL_SLOTS);
  foreach($slots as $slot){        
    $slot->active = true;
    $slotData = Tw_SlotData($slot->slot_id);    
    $slot->time24 = $slotData->time24;
    $slot->timemin = $slotData->timemin;
    $slot->sorter = $slotData->sorter; 
    $slot->apm = $slotData->time24 >= 12 ? 'PM' : 'AM';
    $final[] = $slot;
  }
  return $final;
}

function Tw_GetAllSlots($vendor_id = 0){
  global $db;
  $per = false;
  $ids = [0];
  if(!empty($vendor_id)){
    $vslts = Tw_GetVendorSlots($vendor_id);
    if($vslts){
      $per = true;
      foreach($vslts as $s)$ids[] = $s->slot_id;      
    }
  }
  $slots = $db->get(SLOTS);
  $final = [];  
  if($per && count($ids) != 0){          
      foreach($slots as $slot){
        $indx = array_search($slot->id, $ids);                
        $active = false;
        if($indx != '' && $vslts[$indx - 1]->status == 1){
          $active = true;
        }
        $slot->active = $active;
        $slot->apm = $slot->time24 >= 12 ? 'PM' : 'AM';
        $final[] = $slot;
      }
      return $final;
  }else{
    foreach($slots as $slot){        
      $slot->active = false;
      $slot->apm = $slot->time24 >= 12 ? 'PM' : 'AM';
      $final[] = $slot;
    }
    return $final;
  }    
}

function Tw_UpdateHotelSlot($details){
  if(empty($details['vendor_id']) || empty($details['slot_id'])){
    return "~Unable to Update Slot";
  }
  global $db;
  $slotData = Tw_HotelSlotData(
    $details['vendor_id'], $details['slot_id']
  );  
  if($slotData){    
    $object = ['status' => $slotData->status == 0 ? 1 : 0];
    $db
    ->where('id', $slotData->id)
    ->update(HTL_SLOTS, $object);    
    $str = $slotData->status == 0 ? 'Added' : 'Removed';
    $data = Tw_SlotData($details['slot_id']);
    $object['vendor_id'] = $details['vendor_id'];
    $object['slot_id'] = $details['slot_id'];
    $object['id'] = $slotData->id;
    $object['time24'] = $data->time24;
    $object['sorter'] = $data->sorter;
    $object['timemin'] = $data->timemin;
    $object['apm'] = $data->time24 >= 12 ? 'PM' : 'AM';
    $object['active'] = $slotData->status == 1;
    return $object;
  }else{
    $object = [
      'status' => 1,
      'vendor_id' => $details['vendor_id'],
      'slot_id' => $details['slot_id']
    ];
    $result = $db->insert(HTL_SLOTS, $object);    
    $data = Tw_SlotData($details['slot_id']);    
    $object['id'] = $result;
    $object['time24'] = $data->time24;
    $object['sorter'] = $data->sorter;
    $object['timemin'] = $data->timemin;
    $object['apm'] = $data->time24 >= 12 ? 'PM' : 'AM';
    $object['active'] = true;
    return $object;
  }
}

function Tw_HotelSlotData($vendor_id, $slot_id){
  if(empty($vendor_id) || !is_numeric($vendor_id) ||
     empty($slot_id) || !is_numeric($slot_id)){
    return false;
  }
  global $db;
  $result = $db
  ->where('slot_id', $slot_id)
  ->where('vendor_id', $vendor_id)
  ->getOne(HTL_SLOTS);
  if($result){
    return $result;
  }else{
    return false;
  }
}

function Tw_SlotData($id){
  if(empty($id)){
    return false;
  }
  global $db;
  $result = $db
  ->where('id', $id)  
  ->getOne(SLOTS);
  if($result){
    return $result;
  }else{
    return false;
  }
}

function Tw_GetTimedVendorSlots($vendor_id){
  if(empty($vendor_id))return [];
  global $db;  
  $final = [];
  $slots = $db->where('vendor_id', $vendor_id)->where('status', 1)->get(HTL_SLOTS);
  foreach($slots as $slot){
    $i = intval(date("i"));
    if($i >= 30){
      $crrSort = intval(date("Hi") + 40) + 30;
    }else{
      $crrSort = intval(date("Hi")) + 30;
    }    
    $slotData = Tw_SlotData($slot->slot_id);        
    if($crrSort <= $slotData->sorter){      
      $slot->active = true;
      $slot->time24 = $slotData->time24;
      $slot->sorter = $slotData->sorter;
      $slot->sorter = $slotData->sorter;
      $slot->timemin = $slotData->timemin;    
      $slot->apm = $slotData->time24 >= 12 ? 'PM' : 'AM';
      $final[] = $slot;
    }    
  }
  return $final;
}
function Tw_GetTimedBookings($vendor_id){
  global $db;
  if(empty($vendor_id)){
    return [];
  }
  $tdyStart = strtotime(date('Y-m-d').'00:00:00');
  $tdyEnd = strtotime(date('Y-m-d').'23:59:59');
  $db
  ->where('vendor_id', $vendor_id)
  ->where('from_time', Array($tdyStart, $tdyEnd), "BETWEEN");  
  return $db->get(TABLE_BOOKING);
}
function Tw_GetAviliableSlots($vendor_id){
  $vendorData = Tw_VendorData($vendor_id);
  $slots = (array) Tw_GetTimedVendorSlots($vendor_id);
  $bookings = Tw_GetTimedBookings($vendor_id);
  uasort($slots, 'cmp');
  $slots = array_values($slots);
  $maxPerSlot = $vendorData->slotLimit;
  $avSlts = [];
  foreach($slots as $slot){
    $slot->booked = 0;
    $slot->remaining = $maxPerSlot;
    $avSlts[] = $slot;
  }
  $slotIds = [];
  foreach($slots as $slot) {
    $slotIds[] = $slot->id;
  }
  foreach($slots as $slot){
    foreach($bookings as $booking){
      //if($slot->id == $booking->from_slt && $booking->paid == 1){
      if($slot->id == $booking->from_slt){
        $tempIds = $slotIds;
        $fromIndex = array_search($booking->from_slt, $slotIds);
        $toIndex = array_search($booking->to_slt, $slotIds);
        $innerSlots = array_splice($tempIds, $fromIndex, $toIndex);
        for ($i = $fromIndex; $i < $toIndex; $i++) {
          $avSlts[$i]->booked = $avSlts[$i]->booked+1;          
          $avSlts[$i]->remaining = $avSlts[$i]->remaining-1;
          $avSlts[$i]->remaining = $avSlts[$i]->remaining < 0 ? 0 : $avSlts[$i]->remaining;
        }
      }
    }
  }
  return [
    'slots' => $avSlts,
    'maxPerSlot' => $maxPerSlot
  ];
}

function cmp(object $a, object $b) {  
    return $a->sorter - $b->sorter;
}
//Complain Related
function  Tw_ComplainData($id){
  global $db;
  $e = false;
  if(empty($id))return $e;
  $result = $db->where('id', $id)->getOne(CMPLN);
  return $result ? $result : $e;
}

function  Tw_CreateComplain($data){  
  $e = "~Unable to create complain";
  if(empty($data['user_id']) || empty($data['content'])){
    return $e;
  }
  global $db;  
  $result = $db->insert(CMPLN, [
    'user_id' => $data['user_id'],
    'content' => $data['content']
  ]);
  return $result ? $result : $e;
}

function  Tw_GetComplains($data){  
  $e = "~Unable to get complains";
  if(empty($data['user_id'])){
    return $e;
  }
  global $db;
  $checked = $data['checked'] ? 1 : 0;
  $result = $db->where('user_id', $data['user_id'])
  ->where('checked', $checked)
  ->get(CMPLN);
  $final = [];
  foreach($result as $c){
    $c->time = Tw_TimeHumanType($c->time);
    if (strlen($c->content) > 50){      
     $c->content = substr($c->content, 0, 45) . '...';
    }
    $final[] = $c;
  }
  return $final;
}


//Earning Related
function Tw_GetEarningData($details){  
  if(empty($details['vendor_id']))return "~Error Generating Record";  
  global $db;
  $dlvEarning = 0;
  $bkEarning = 0;
  if(!empty($details['date'])){
    $start = strtotime(date('Y-m-d', $details['date']).'00:00:00');
    $end = strtotime(date('Y-m-d', $details['date']).'23:59:59');
  }else if(!empty($details['range'])){
    $range = json_decode($details['range'], true);
    $start = strtotime(date('Y-m-d', $range['start']).'00:00:00');
    $end = strtotime(date('Y-m-d', $range['end']).'00:00:00');
  }
  $vendorOrders = $db
  ->where('vendor_id', $details['vendor_id'])  
  ->where('time', Array($start, $end), "BETWEEN")
  ->get(ORDER_VENDOR);    
  foreach($vendorOrders as $odr){
    $fds = Tw_GetFoodsByOrderId($details['vendor_id'], $odr->id);
    foreach($fds as $fd){
      $dlvEarning += ($fds->amount * $fd->quantity);
    }
  }

  $bookings = $db
  ->where('vendor_id', $details['vendor_id'])
  ->where('booked', Array($start, $end), "BETWEEN")
  ->get(TABLE_BOOKING);
  $bkEarning = count($bookings) * TB_AMOUNT;  
  
  return [
    'total' => $bkEarning + $dlvEarning,
    'bookings' => $bkEarning,
    'orders' => $dlvEarning
  ];

}

function Tw_SettleData($vendor_id){
  if(empty($vendor_id))return $e;

  global $db;
  $settled = 0;
  $onhold = 0;

  $vendorOrders = $db
  ->where('vendor_id', $vendor_id)
  ->get(ORDER_VENDOR);

  foreach($vendorOrders as $odr){
    $fds = Tw_GetFoodsByOrderId($vendor_id, $odr->id);
    foreach($fds as $fd){
      if($odr->settled == 1){
        $settled += ($fds->amount * $fd->quantity);
      }else{
        $onhold += ($fds->amount * $fd->quantity);
      }
    }
  }

  $bookings = $db->where('vendor_id', $vendor_id)  
  ->get(TABLE_BOOKING);
  $bkEarning = count($bookings) * TB_AMOUNT;  
  foreach($bookings as $booking){
    if($booking->settled == 1)$settled += TB_AMOUNT;      
    else $onhold += TB_AMOUNT;    
  }

  return [
    'onhold' => $onhold,
    'settled' => $settled,
    'allTotal' => $onhold + $settled
  ];
}

//Tax Functions
function Tw_VendorHasTax($vendor_id){
  if(empty($vendor_id)){
    return false;
  }
  global $db;
  $result = $db->where('id', $vendor_id)->getOne(V_FAMILY);
  if($result){
    return $result->hasTax == 1;
  }else{
    return false;
  }
}

function Tw_VendorHasAppliedTax($tax_id, $vendor_id){
  if(empty($vendor_id)){
    return false;
  }
  global $db;
  $result = $db->where('tax_id', $tax_id)->where('vendor_id', $vendor_id)->get(V_TAXES);
  if($result){
    return $db->count > 0;
  }else{
    return false;
  }
}

function Tw_VendorTaxData($tax_id, $vendor_id){
  if(empty($vendor_id)){
    return false;
  }
  global $db;
  $result = $db->where('tax_id', $tax_id)->where('vendor_id', $vendor_id)->getOne(V_TAXES);
  if($result){
    return $result;
  }else{
    return false;
  }
}

function Tw_TaxData($tax_id){
  if(empty($tax_id)){
    return false;
  }
  global $db;
  $result = $db->where('id', $tax_id)->getOne(TAXES);
  if($result){
    return $result;
  }else{
    return false;
  }
}

function Tw_SetVendorTax($details){
  if(empty($details['vendor_id'])){
    return '~Error';
  }
  global $db;
  $hasTax = empty($details['tax']) ? 0 : 1;
  $result = $db->where('id', $details['vendor_id'])->update(V_FAMILY, [
    'hasTax' => $hasTax
  ]);
  if($hasTax == 1){
    $taxes = json_decode($details['taxes']);
    foreach($taxes as $tax){
      $present = $db->where('tax_id', $tax->id)->where('vendor_id', $details['vendor_id'])->getOne(V_TAXES);
      if($present){        
        if($tax->percent == 0){
          $db->where('id', $present->id)->delete(V_TAXES);
        }else{
          $db->where('id', $present->id)->update(V_TAXES, ['percent' => $tax->percent]);
        }        
      }else if($tax->percent != 0){
        $db->insert(V_TAXES, [
          'tax_id' =>  $tax->id,
          'vendor_id' => $details['vendor_id'],
          'percent' => $tax->percent
        ]);
      }
    }    
  }else{
    $present = $db->where('vendor_id', $details['vendor_id'])->delete(V_TAXES);
  }
  if($result){
    return 'Updated';
  }else{
    return '~Error';
  }
}

function Tw_GetVendorTaxes($vendor_id){
  if(empty($vendor_id)){
    return '~Error';
  }
  global $db;
  $hasTax = Tw_VendorHasTax($vendor_id);
  if($hasTax){
    $final = [];
    $taxes = $db->where('vendor_id', $vendor_id)->get(V_TAXES);
    return $taxes;
  }else{
    return [];
  }  
}

function Tw_GetClufterTaxes($vendor_id){
  if(empty($vendor_id)){
    return [];
  }
  global $db;
  $hasApplied = Tw_VendorHasTax($vendor_id);    
  $taxes = $db->get(TAXES);
  $final = [];
  if($db->count > 0){
    foreach($taxes as $tax){
      if($tax->divided == 1){
        $tax->division = Tw_GetSubTaxes($tax->id);
      }
      $data = Tw_VendorTaxData($tax->id, $vendor_id);
      if($data){
        $tax->applied = true;
        $tax->percent = $data->percent;
      }else{
        $tax->applied = false;
        $tax->percent = 0;
      }
      $final[] = $tax;
    }
    return $final;
  }else{
    return [];
  }
}

function Tw_GetClufterAppliedTaxes($vendor_id){
  if(empty($vendor_id)){
    return ['taxes' => [], 'tax_percent' => 0];
  }
  global $db;  
  $taxes = $db->get(TAXES);
  $final = [];
  $taxPercent = 0;
  if($db->count > 0){
    foreach($taxes as $tax){      
      $data = Tw_VendorTaxData($tax->id, $vendor_id);      
      if($data){      
        if($tax->divided == 1){
          $subs = Tw_GetSubTaxes($tax->id);
          $percent = $data->percent / count($subs);
          //$amount = $data->tax_amt / count($subs);
          foreach ($subs as $sub) {
            $obj = new stdClass();
            $obj->id = $sub->id;
            $obj->name = $sub->name;
            $obj->applied = true;
            $obj->percent = $percent;
            //$obj->amount = $amount;
            $final[] = $obj;
          }
        }else{
          $tax->applied = true;
          $tax->percent = $data->percent;
          $final[] = $tax;
        }
        $taxPercent += $data->percent;
      }
    }
    return ['taxes' => $final, 'tax_percent' => $taxPercent];
  }else{
    return ['taxes' => [], 'tax_percent' => 0];
  }
}

function Tw_GetVisitTaxes($visit_id){
  if(empty($visit_id)){
    return [];
  }
  global $db;
  $taxes = $db->where('visit_id', $visit_id)->get(TAX_APPLIED);
  $final = [];    
  if($db->count > 0){
    foreach($taxes as $tax){      
      $data = Tw_TaxData($tax->tax_id);
      if($data){      
        if($data->divided == 1){
          $subs = Tw_GetSubTaxes($data->id);
          $percent = $tax->percent / count($subs);
          $amount = $tax->tax_amt / count($subs);
          foreach ($subs as $sub) {
            $obj = new stdClass();
            $obj->name = $sub->name;
            $obj->percent = $percent;
            $obj->amount = $amount;
            $final[] = $obj; 
          }          
        }else{
          $obj = new stdClass();
          $obj->name = $data->name;
          $obj->percent = $tax->percent;
          $obj->amount = $tax->tax_amt;
          $final[] = $obj;
        }
      }
    }
    return $final;
  }else{
    return [];
  }
}

function Tw_TkAwayTaxes($ta_id){
  if(empty($ta_id)){
    return [];
  }
  global $db;
  $taxes = $db->where('ta_id', $ta_id)->get(TAX_APPLIED);
  $final = [];    
  if($db->count > 0){
    foreach($taxes as $tax){      
      $data = Tw_TaxData($tax->tax_id);
      if($data){      
        if($data->divided == 1){
          $subs = Tw_GetSubTaxes($data->id);
          $percent = $tax->percent / count($subs);
          $amount = $tax->tax_amt / count($subs);
          foreach ($subs as $sub) {
            $obj = new stdClass();
            $obj->name = $sub->name;
            $obj->percent = $percent;
            $obj->amount = $amount;
            $final[] = $obj; 
          }          
        }else{
          $obj = new stdClass();
          $obj->name = $data->name;
          $obj->percent = $tax->percent;
          $obj->amount = $tax->tax_amt;
          $final[] = $obj;
        }
      }
    }
    return $final;
  }else{
    return [];
  }
}

function Tw_GetSubTaxes($tax_id){
  if(empty($tax_id)){
    return [];
  }
  global $db;
  $sub = $db->where('tax_id', $tax_id)->get(TAX_SUB);
  return $sub;
}

function Tw_VendorAddRole($details){  
  if(empty($details['vendor_id']) || empty($details['pass'])){
    return "~Invalid Request";
  }
  $role = empty($details['role']) ? 0 : $details['role'];  
  global $db;
  $result = $db->insert(ROLES, [
    'name' => $details['name'],
    'pass' => md5($details['pass']),
    'role' => $role,
    'vendor_id' => $details['vendor_id']
  ]);
  return $result;
}

function Tw_VendorDelRole($role_id){  
  if(empty($role_id)){
    return "~Invalid Request";
  }  
  global $db;
  $result = $db->where('id', $role_id)->delete(ROLES);
  return $result;
}

function Tw_VendorEditRole($details){  
  if(empty($details['role_id']) || empty($details['name']) || empty($details['pass'])){
    return "~Invalid Request";
  }  
  global $db;
  $result = $db->where('id', $details['role_id'])->update(ROLES, [
    'name' => $details['name'],
    'pass' => md5($details['pass'])
  ]);
  return $result;
}

function Tw_LogUserRole($details){  
  if(empty($details['id']) || empty($details['pass'])){
    return "~Invalid Request";
  }  
  global $db;
  $result = $db
  ->where('id', $details['id'])
  ->where('pass', md5($details['pass']))
  ->getOne(ROLES);
  if($result){
    $vendor = Tw_VendorData($result->vendor_id);
    $result->vname = $vendor->name;
    return $result;
  }else{
    return "~Not Found";
  }  
}

function Tw_LoadVendorRoles($vendor_id, $type = -1){  
  if(empty($vendor_id)){
    return [];
  }  
  global $db;
  if($type != -1){
   $db->where('role', $type);
  }
  $result = $db
  ->where('vendor_id', $vendor_id)  
  ->get(ROLES);
  return $result;
}

function Tw_VendorRoleData($role_id){  
  if(empty($role_id)){
    return [];
  }  
  global $db;
  $result = $db
  ->where('id', $role_id)  
  ->getOne(ROLES);
  return $result;
}

//Take Away Functions 

function Tw_CreateTakeAway($details, $withOut = false){
  if(empty($details['vendor_id']) ||
    !Tw_VendorExistsById($details['vendor_id'])){
    return "~Partner Not Found";
  }else if(!Tw_VendorAreaExistsById($details['area_id']) && $withOut){
    return "~Invalid Area";
  }
  global $db;
  $db->where('area_id', $details['area_id'])->get(V_TABLE);  
  $cp_id = empty($details['cp_id']) ? 0 : $details['cp_id'];
  $data = [    
    'area_id' => $details['area_id'],
    'number' => $db->count + 1,
    'status' => TBL_PRESENT,    
    'time' => time(),
    'tkaway' => 1
  ];  
  $table_id = $db->insert(V_TABLE, $data);
  $visit_id = Tw_CaptainHotelVisit(['table_id' => $table_id, 'cp_id' => $cp_id]);  
  $db->where('id', $table_id)->update(V_TABLE, ['visit_id' => $visit_id]);
  return [
    'id' => $table_id,    
    'area_id' => $details['area_id'],
    'number' => $data['number'],
    'status' => TBL_PRESENT,
    'time' => time(),
    'tkaway' => 1,
    'cp_id' => $cp_id,
    'sp_idx' => 0,
    'sp_id' => 0,
    'visit_id' => $visit_id
  ];  
}

function Tw_TransferTkAway($table, $discount = 0, $x = false){
  global $db;  
  $visitData = Tw_VisitData($table->visit_id);  
  $areaData = Tw_AreaData($table->area_id);
  $foods = Tw_GetFoodOfVisit($table->visit_id);
  if($x == false){
    $xamt = 0;
    $xtitle = '';
  }else{
    $xtitle = empty($x['xtitle']) ? '' : $x['xtitle'];
    $xamt = empty($x['xamt']) ? 0 : $x['xamt'];
  }
  $data = [
    'vendor_id' => $areaData->vendor_id,
    'pmy' => $visitData->pmy,
    'tx_amt' => $visitData->tax_amt, 
    'amount' => $visitData->amount,    
    'billno' => $visitData->billno,
    'cp_id' => $visitData->cp_id,
    'time' => $table->time
  ];
  $total_amt = $visitData->total_amt + $xamt;
  $data['xamt'] = $xamt;
  $data['xtitle'] = $xtitle;
  if($discount > 0){    
    $total_amt -= $discount;
    $data['discount'] = $discount;    
  }
  $data['total_amt'] = $total_amt;
  $ta_id = $db->insert(TKAWAY, $data);
  $db->where('id', $visitData->id)->delete(VISITS);
  foreach($foods as $food){
    $db->insert(TA_FOODS, [
      'ta_id' => $ta_id,
      'food_id' => $food->food_id, 
      'amount' => $food->amount,
      'stamp' => time(),
      'per_price' => $food->amount/$food->quantity,
      'quantity' => $food->quantity
    ]);
  }
  $db->where('visit_id', $visitData->id)->delete(VISIT_FOOD);
  $db->where('id', $table->id)->delete(V_TABLE);
  $db->where('visit_id', $visitData->id)->update(TAX_APPLIED, [
    'visit_id' => 0,
    'ta_id' => $ta_id
  ]);
  return $ta_id;
}

function Tw_TkAwayData($id){
  global $db;
  if(empty($id))return false;  
  $result = $db  
  ->where('id', $id)
  ->getOne(TKAWAY);  
  return $result;
}
//Vendor Reports Methods
function Tw_GenerateVendorSummary($details){
  if(empty($details['id'])){
    return "~Error";
  }
  global $db;

  $vendor_id = $details['id'];

  $vendor = Tw_VendorData($vendor_id);
  $vars = Tw_LoadVendorAreas($vendor_id);

  $start = empty($details['start']) ? time() : $details['start'];
  $end = empty($details['end']) ? time() : $details['end'];

  $startS = date('Y-m-d', $start).' 00:00:00';
  $endS = date('Y-m-d', $end).' 23:59:59';
  $start = strtotime($startS);
  $end = strtotime($endS);
  
  $hoByQr = 0;//Hotel Orders By QR
  $hoByCp = 0;//Hotel Orders By Captain
  $hoByQrAmt = 0;//Hotel Orders By QR Amount
  $hoByCpAmt = 0;//Hotel Orders By Captain Amount
  $hoTaxAmt = 0;//Hotel Order Tax Amt

  $tkAway = 0;//Take Away Count
  $tkAwayAmt = 0;//Take Away Amount
  $tkAwayTax = 0;//Take Away Tax Amount

  $tbkg = 0;//Table Booking Count
  $tbkgAmt = 0;//Table Booking Amount  
  $uskgAmt = 0;//Unsettled Booking Amount
  $cnTbkg = 0;//Cancelled Table Booking
  $cnTbkgAmt = 0;//Cancelled Table Booking Amount
  $tbkgTaxAmt = 0;//Table Booking Tax Amount
  
  $onOdr = 0;//Online Orders Count
  $onOdrAmt = 0;//Online Orders Amount
  $usOnodrAmt = 0;//Unsettled Online Orders Amount
  $cnOnodr = 0;//Cancelled Online Orders
  $cnOnodrAmt = 0;//Cancelled Online Orders Amount
  $usCMS = 0;//Unsettled Commission
  $CMS = 0;//Commission

  $cashAmount = 0;//Paid VIA Cash
  $cashTrans = 0;//Cash Transactions
  $onlineAmount = 0;//Paid VIA ONLINE
  $onlineTrans = 0;//Online Transactions

  $totalTaxAmt = 0;//Total Tax Amount
  $totalAmount = 0;//Total Earning Amount
  $totalUnsAmt = 0;//Total Unsattled Amount

  $dscnt = 0;//Discount Count
  $dscntAmt = 0;//Discount Amount

  $xtra = 0;//Additional Count
  $xtraAmt = 0;//Additional Amount

  $cdts = 0;//Card Transactions
  $cd_amt = 0;//Card Amount  
  $coh_amt = 0;//Cash on Hold Amount
  $cohl = 0;//Cash on Hold Transactions

  //Hotel Orders Captain & QR
  $hotelOrders = $db->where('vendor_id', $vendor_id)->where('stamp',  Array($start, $end), "BETWEEN")->get(VISITS);
  foreach($hotelOrders as $htlOdr){
    if($htlOdr->pmy == PAY_CASH){
      $cashAmount += $htlOdr->total_amt;
      $cashTrans++;
    }else if($htlOdr->pmy == PAY_ONLINE){
      $onlineAmount += $htlOdr->total_amt;
      $onlineTrans++;
    }else if($htlOdr->pmy == PAY_CARD){
      $cd_amt += $htlOdr->total_amt;
      $onlineAmount += $htlOdr->total_amt;
      $cdts++;
    }
    if($htlOdr->byqr == 1){
      $hoByQr++;
      $hoByQrAmt += $htlOdr->total_amt;
    }else{
      $hoByCp++;
      $hoByCpAmt += $htlOdr->total_amt;
    }
    if($htlOdr->discount > 0){
      $dscnt++;
      $dscntAmt += $htlOdr->discount;
    }
    $hoTaxAmt += $htlOdr->tax_amt;
  }

  //Hotel Orders For Areas  
  $areas = [];
  foreach($vars as $ar){
    $e = 0;
    $ars = $db
    ->where('area_id', $ar->id)
    ->where('stamp',  Array($start, $end), "BETWEEN")
    ->get(VISITS);
    foreach ($ars as $a)$e += $a->total_amt;
    if($ar->area == 'Zomato' ){
      $coh_amt += $e;
      $cohl += count($ars);
    }
    if($ar->area == 'Swiggy'){
      $coh_amt += $e;
      $cohl += count($ars);
    }
    $areas[] = ['t' => $ar->area,'c' => $db->count,'e' => $e];
  }
  
  //Take Away
  $takeAways = $db->where('vendor_id', $vendor_id)->where('time',  Array($start, $end), "BETWEEN")->get(TKAWAY);
  $tkAway= $db->count;
  foreach($takeAways as $tkAy){
    if($tkAy->pmy == PAY_CASH){
      $cashAmount += $tkAy->total_amt;
      $cashTrans++;
    }else if($tkAy->pmy == PAY_CARD){
      $cd_amt += $tkAy->total_amt;
      $onlineAmount += $tkAy->total_amt;
      $cdts++;
    }else{
      $onlineAmount += $tkAy->total_amt;
      $onlineTrans++;
    }
    if($tkAy->discount > 0){
      $dscnt++;
      $dscntAmt += $tkAy->discount;
    }
    if($tkAy->xamt > 0){
      $xtra++;
      $xtraAmt += $tkAy->xamt;
    }
    $tkAwayAmt += $tkAy->total_amt;
    $tkAwayTax += $tkAy->tx_amt;
  }

  //Table Booking
  $bkgs = $db->where('vendor_id', $vendor_id)->where('booked',  Array($start, $end), "BETWEEN")->get(TABLE_BOOKING);  
  foreach($bkgs as $bkg){
    if($bkg->attent == 1){
      if($bkg->cancel < CANCELLED){
        if($bkg->settled != 1){
          $uskgAmt += $bkg->amount;
        }
        $tbkgAmt += $bkg->amount;
        $onlineAmount += $bkg->amount;        
        $onlineTrans++;
        $tbkgTaxAmt += $bkg->tx_amt;
        $tbkg++;
      }else{
        $cnTbkg++;
        $cnTbkgAmt += $bkg->amount;
      }
    }        
  }

  //Online Orders
  $onodrs = $db->where('vendor_id', $vendor_id)->where('time',  Array($start, $end), "BETWEEN")->get(ORDER_VENDOR);
  foreach($onodrs as $odr){
      if($odr->paid == 1){
        if($odr->status < VDRFDCANCEL){
        if($odr->settled != 1){
          $usOnodrAmt += $odr->amount;
          $usCMS += PER_ORDER_REWARD;
        }
        $CMS += PER_ORDER_REWARD;
        $onOdrAmt += $odr->amount;      
        $onlineAmount += ($odr->amount + PER_ORDER_REWARD);
        $onlineTrans++;
        $onOdr++;
      }else{
        $cnOnodr++;
        $cnOnodrAmt += $odr->amount;
      }
    }    
  }
  
  $foodList = Tw_LoadVendorFood($vendor_id, FD_BOTH);    
  $fdforDelivery = 0;
  $fdforMenu = 0;
  $fdforNo = 0;
  $lastCat = 0;
  $catCount = 0;
  $catIds = [];
  foreach($foodList as $key => $food){
    if($food->status == FD_BOTH){
      $fdforDelivery++;
      $fdforMenu++;
    }else if($food->status == FD_ONLYMNU){
      $fdforMenu++;
    }else if($food->status == FD_ONLYDLV){
      $fdforDelivery++;
    }else if($food->status == FD_NONE){
      $fdforNo++;
    }
    if($food->cat != $lastCat && !in_array($food->cat, $catIds)){        
      $catIds[] = $food->cat;
      $catCount++;
    }
  }
    
  $totalTaxAmt = $hoTaxAmt + $tkAwayTax + $tbkgTaxAmt;
  $totalAmount = $hoByQrAmt + $hoByCpAmt + $tkAwayAmt + $tbkgAmt + $onOdrAmt + $CMS;
  $totalUnsAmt = $usOnodrAmt + $uskgAmt + $usCMS;

  $result = [
    'total' => $totalAmount, 
    'totalTax' => $totalTaxAmt, 
    'totalUS' => $totalUnsAmt,     
    'chts' => $cashTrans,
    'onts' =>$onlineTrans,    
    'odr_us' => $usOnodrAmt,
    'bkg_us' => $uskgAmt,
    'tt_us' => $totalUnsAmt,
    'ch_amt' => $cashAmount,
    'on_amt' => $onlineAmount,
    
    'fditms' => count($foodList),
    'fddlv' => $fdforDelivery,
    'fdmnu' => $fdforMenu,
    'fdno' => $fdforNo,
    'cats' => $catCount,

    'hoByQr' => $hoByQr,
    'hoByCp' => $hoByCp,
    'hoByQrAmt' => $hoByQrAmt,
    'hoByCpAmt' => $hoByCpAmt,

    'tkAway' => $tkAway,
    'tkAwayAmt' => $tkAwayAmt,
  
    'tbkg' => $tbkg,
    'tbkgAmt' => $tbkgAmt,
    'cnTbkg' => $cnTbkg,
    'cnTbkgAmt' => $cnTbkgAmt,

    'onOdr' => $onOdr,
    'onOdrAmt' => $onOdrAmt,    
    'cnOnodr' => $cnOnodr,
    'cnOnodrAmt' => $cnOnodrAmt,
    
    //Commission Related
    'us_cms' => $usCMS,
    'cms' => $CMS,

    'dscnt' => $dscnt,
    'dscntAmt' => $dscntAmt,
    'xtra' => $xtra,
    'xtraAmt' => $xtraAmt,

    'areas' => $areas,
    'hotel' => $vendor->name,

    'cdts' => $cdts,
    'cd_amt' => $cd_amt,

    'cohl' => $cohl,
    'coh_amt' => $coh_amt
  ];

  return $result;
}

function Tw_VendorTransferTable($details){
  if(empty($details['from_id']) || empty(($details['to_id']))){
    return "~Try Again";
  }
  global $db;  
  $toTable = Tw_TableData($details['to_id']);
  if($toTable->status != TBL_FREE){
    return "~Table is Not Free";
  }  
  $fromTable = Tw_TableData($details['from_id']);  
  $visitId = $fromTable->visit_id;
  $fromVisit = Tw_VisitData($visitId);

  $db->where('id', $visitId)->update(VISITS, [
    'table_id' => $toTable->id,
    'tableno' => $toTable->number,
    'area_id' => $toTable->area_id
  ]);

  $db->where('id', $fromTable->id)->update(V_TABLE, [
    'status' => TBL_FREE,
    'visit_id' => 0
  ]);

  $db->where('id', $toTable->id)->update(V_TABLE, [
    'status' => $fromTable->status,
    'visit_id' => $visitId,
    'time' => $fromTable->time,
    'updated' => $fromTable->updated
  ]);

  return "Transferred";
}

function Tw_GetVendorFoodSales($details){
  if(empty($details['id'])){
    return false;
  }
  global $db;
  $foodList = Tw_LoadVendorFood($details['id']);
  $start = empty($details['start']) ? time() : $details['start'];
  $end = empty($details['end']) ? time() : $details['end'];
  $startS = date('Y-m-d', $start).' 00:00:00';
  $endS = date('Y-m-d', $end).' 23:59:59';
  $start = strtotime($startS);
  $end = strtotime($endS);
  $data = [];
  foreach ($foodList as $item) {
    $online = 0;
    $onlineAmt = 0;
    $foodData = $db->where('food_id', $item->id)->where('stamp',  Array($start, $end), "BETWEEN")->get(ORDER_FOOD);
    foreach ($foodData as $onitm) {
      $order = $db
      ->where('order_id', $onitm->order_id)
      ->where('vendor_id', $onitm->vendor_id)
      ->getOne(ORDER_VENDOR);
      if($order && $order->status < VDRFDCANCEL){
        $online += $onitm->quantity;
        $onlineAmt += ($onitm->quantity * $onitm->amount);
      }
    }

    $tkaway = 0;
    $tkawayAmt = 0;
    $foodData = $db->where('food_id', $item->id)->where('stamp',  Array($start, $end), "BETWEEN")->get(TA_FOODS);
    foreach ($foodData as $onitm) {      
      $tkaway += $onitm->quantity;
      $tkawayAmt += $onitm->amount;      
    }

    $visit = 0;
    $visitAmt = 0;
    $foodData = $db->where('food_id', $item->id)->where('updated',  Array($start, $end), "BETWEEN")->get(VISIT_FOOD);
    foreach ($foodData as $onitm) {      
      $visit += $onitm->quantity;
      $visitAmt += $onitm->amount;      
    }

    $data[] = [
      'i' => $item->id,//id
      'n' => $item->name,//name
      'v' => $visit,//Visit
      'va' => $visitAmt,//Visit Amount
      't' => $tkaway,
      'ta' => $tkawayAmt,
      'd' => $online,
      'da' => $onlineAmt,
      'ts' =>  $visit + $tkaway + $online,
      'tt' =>  $onlineAmt + $tkawayAmt + $visitAmt
    ];
  }

  return $data;
}

function Tw_GetRolesReport($details){
  if(empty($details['id'])){
    return [];
  }  
  global $db;  
  $roles = Tw_LoadVendorRoles($details['id']);
  $start = empty($details['start']) ? time() : $details['start'];
  $end = empty($details['end']) ? time() : $details['end'];
  $startS = date('Y-m-d', $start).' 00:00:00';
  $endS = date('Y-m-d', $end).' 23:59:59';
  $start = strtotime($startS);
  $end = strtotime($endS);
  $data = [];  
  $visits = $db
   ->where('byqr', 0)
   ->where('cp_id', 0)
   ->where('vendor_id', $details['id'])
   ->where('stamp',  Array($start, $end), "BETWEEN")->get(VISITS);
   $ta = $db   
   ->where('cp_id', 0)
   ->where('vendor_id', $details['id'])
   ->where('time',  Array($start, $end), "BETWEEN")->get(TKAWAY);
   $data[] = [
      'i' => 0,
      'n' => '',
      'r' => 0,
      'o' => count($visits),
      'ta' => count($ta),
      'tt' => count($ta) + count($visits)
   ];

  foreach ($roles as $role) {
    if($role->role == 0){
     $visits = $db
     ->where('cp_id', $role->id)     
     ->where('stamp',  Array($start, $end), "BETWEEN")->get(VISITS);
     $ta = $db
     ->where('cp_id', $role->id)     
     ->where('time',  Array($start, $end), "BETWEEN")->get(TKAWAY);
     $data[] = [
        'i' => $role->id,
        'n' => $role->name,
        'r' => 1,
        'o' => count($visits),
        'ta' => count($ta),
        'tt' => count($ta) + count($visits)
     ];
    }
  }
  $visits = $db
   ->where('byqr', 1)   
   ->where('stamp',  Array($start, $end), "BETWEEN")->get(VISITS);  
  $data[] = [
      'i' => 0,
      'n' => '',
      'r' => 2,
      'o' => count($visits),
      'ta' => 0,
      'tt' => count($visits)
  ];
  return $data;
}

function Tw_VoidTakeAway($details){
 if(empty($details['id'])){
  return "~Error";
 }else{
  $ta = Tw_TkAwayData($details['id']);
  $vendor = Tw_VendorData($ta->vendor_id);
  $pass = Tw_UserPassword($vendor->owner);
  if($pass != md5($details['pass'])){
    return '~Wrong Password!';
  }
 }
 global $db;
 $db->where('id', $details['id'])->update(TKAWAY, [
   'amount' => 0,
   'tx_amt' => 0,
   'total_amt' => 0
 ]);
 return 'Bill Void!';
}

function Tw_VoidHotelOrder($details){
  if(empty($details['id'])){
  return "~Error";
 }else{
  $vd = Tw_VisitData($details['id']);
  $vendor = Tw_VendorData($vd->vendor_id);  
  $pass = Tw_UserPassword($vendor->owner);
  if($pass != md5($details['pass'])){
    return '~Wrong Password!';
  }
 }
 global $db;
 $db->where('id', $details['id'])->update(VISITS, [
   'amount' => 0,
   'tax_amt' => 0,
   'total_amt' => 0
 ]);
 return 'Bill Void!';
}

function Tw_RaiseFoodPrice ($val) {
  if($val > 50 && $val <= 150){
    $val += 2;
  }else if($val > 150 && $val <= 200){
    $val += 5;
  }else if($val > 200){
    $val += 10;
  }
  return $val;
}

function Tw_CalRaiseAmt ($val) {
  if($val > 50 && $val <= 150){
    $val = 2;
  }else if($val > 150 && $val <= 200){
    $val = 5;
  }else if($val > 200){
    $val = 10;
  }else{
    $val = 0;
  }
  return $val; 
}

//Live Tracking
function Tw_LoadInitialTrack($order_id){
  global $db;
  $odr = $db->where('id', $order_id)->getOne(ORDERS);  
  $adr = Tw_AddressData($odr->address_id);
  $vendor = Tw_GetVendorsOfOrder($odr->id)[0];
  $vdata = Tw_VendorData($vendor->id);
  $order = new stdClass();
  $order->id = $odr->id;
  $order->anim = null;    
  $order->total = $odr->amount + $odr->d_praise + $odr->delivery_fee;
  $order->hotel = $vdata->name;
  $order->phone = $vdata->manager_number;
  $order->order_code = Tw_OrderCode($odr->id);
  $order->time_string = Tw_TimeHumanType($odr->time);
  $order->time = $odr->time;
  $order->pay_method = $odr->pay_method;
  $order->delivery_fee = $odr->delivery_fee;
  $order->d_praise = $odr->d_praise;
  $order->ttrd = $odr->ttrd;
  $order->ttrh = $odr->ttrh;
  $order->htdt = $odr->htdt;
  $order->ttc = $vendor->ttc;
  $order->acptm = $vendor->acptm;
  $order->route = [
    'flat' => $vdata->lat,
    'flng' => $vdata->long,
    'tlat' => $adr->lat,
    'tlng' => $adr->lng,
  ];
  if($order->ttrd == 0){    
    $distance = Tw_CalDistance($adr->lat, $adr->lng, $vdata->lat, $vdata->long);    
    $order->ttrd = round($distance * PER_KM_TIME);
    $db->where('id', $order_id)->update(ORDERS, ['htdt' => $order->ttrd, 'ttrd' => $order->ttrd]);
    $order->htdt = $order->ttrd;
  }
  if($order->htdt == 0){
    $distance = Tw_CalDistance($adr->lat, $adr->lng, $vdata->lat, $vdata->long);        
    $order->htdt = round($distance * PER_KM_TIME);
    $db->where('id', $order_id)->update(ORDERS, ['htdt' => $order->htdt]);
  }  
  $order->anim = Tw_GetTrackAnim($odr->status, $vendor->status);
  $order->food_items = [];
  if($vendor->status > FOOD_PREPARED && $vendor->status != HAS_DELIVERED && $vendor->status != VDRFDCANCEL){    
    $rider = Tw_DHeroData($vendor->hero_id);
    $tookan = new Tookan();
    $location = $tookan->getFleetLocation($rider['fleet_id']);
    $lat = 0;
    $lng = 0;    
    if($location->status == 200){
      $lat = $location->data[0]->latitude;
      $lng = $location->data[0]->longitude;
    }else{
      $lat = $rider['lat'];
      $lng = $rider['lng'];
    }
    $order->rider = [
      'name' => $rider['name'],
      'phone' => $rider['phone_no'],
      'lat' => $lat,
      'lng' => $lng
    ];
  }
  $order->food_items = Tw_GetFoodsByOrderId($vendor->id, $order->id, true, true);
  return $order;
}

function Tw_OrderLiveTrackData($order_id){
  global $db;
  $odr = $db->where('id', $order_id)->getOne(ORDERS);
  $adr = Tw_AddressData($odr->address_id);
  $vendor = Tw_GetVendorsOfOrder($odr->id)[0];  
  $order = new stdClass();
  $order->route = [
    'flat' => $vendor->lat,
    'flng' => $vendor->long,
    'tlat' => $adr->lat,
    'tlng' => $adr->lng,
  ];
  $order->anim = Tw_GetTrackAnim($odr->status, $vendor->status); 
  if($vendor->status > FOOD_PREPARED){
    $rider = Tw_DHeroData($vendor->hero_id);
    $tookan = new Tookan();
    $location = $tookan->getFleetLocation($rider['fleet_id']);
    $lat = 0;
    $lng = 0;
    if($location->status == 200){
      $lat = $location->data[0]->latitude;
      $lng = $location->data[0]->longitude;
    }else{
      $lat = $rider['lat'];
      $lng = $rider['lng'];
    }
    $order->ttrh = 0;//Time To Reach Hotel
    $order->ttrd = 0;//Time To Reach Destination
    $speed = 27;    
    if($vendor->status >= HAS_PICKED_F){    
      $distance = Tw_CalDistance($lat, $lng, $adr->lat, $adr->lng);
      $order->ttrd = round($distance * PER_KM_TIME);
    }else{
      $vdata = $db->where('id', $vendor->id)->getOne(V_FAMILY);    
      $distance = Tw_CalDistance($lat, $lng, $vdata->lat, $vdata->long);
      $order->ttrh = round($distance * PER_KM_TIME);
      $distance = Tw_CalDistance($adr->lat, $adr->lng, $vdata->lat, $vdata->long);    
      $order->ttrd = round($distance * PER_KM_TIME);
    }
    $db->where('user_id', $vendor->hero_id)->update(HERO, [
      'lat' => $lat,
      'long' => $lng
    ]);
    $db->where('id', $order_id)->update(ORDERS, [
      'ttrh' => $order->ttrh,
      'ttrd' => $order->ttrd
    ]);    
    $order->rider = [
      'name' => $rider['name'],
      'phone' => $rider['phone_no'],
      'lat' => $lat,
      'lng' => $lng
    ];
  }  
  return $order;
}

function Tw_GetTrackAnim($osts, $vsts){
  $cooking = [FOOD_ACCEPT, DELIVERY_FN_PREPARED];
  $prepared = [FOOD_PREPARED, DELIVERY_F_PREPARED];  
  $onway = [HAS_PICKED_F, HAS_PICKED_F, HAS_CENTERED]; 
  if($osts == HAS_DELIVERED){        
    return 'dl';
  }else{
    if($vsts == FOOD_NOT_PREPARED){
      return 'wt';
    }else if($vsts == FOOD_ACCEPT || $vsts == FOOD_PREPARED){
      return 'lk';
    }else if($vsts == DELIVERY_FN_PREPARED){
      return 'ck';
    }else if($vsts == DELIVERY_F_PREPARED){
      return 'pp';
    }else if(in_array($vsts, $onway)){
      return 'ow';
    }else if($vsts == VDRFDCANCEL){
      return 'cn';
    }else{
      return null;
    }
  }
}

function Tw_UpdateHeroLocation($details){
  if(empty($details['t']) || empty($details['l'])){
    return "~";
  }
  global $db;
  $order = $db->where('id', $details['v'])->getOne(ORDER_VENDOR);  
  $orderData = $db->where('id', $order->order_id)->getOne(ORDERS);
  $ttrh = 0;//Time To Reach Hotel
  $ttrd = 0;//Time To Reach Destination
  $speed = 27;
  $adr = $db->where('id', $orderData->address_id)->getOne(ADDRESS);
  if($order->status >= HAS_PICKED_F){    
    $distance = Tw_CalDistance($details['t'], $details['l'], $adr->lat, $adr->lng);
    $ttrd = round($distance * PER_KM_TIME);
  }else{
    $vdata = $db->where('id', $order->vendor_id)->getOne(V_FAMILY);    
    $distance = Tw_CalDistance($details['t'], $details['l'], $vdata->lat, $vdata->long);
    $ttrh = round($distance * PER_KM_TIME);
    $distance = Tw_CalDistance($adr->lat, $adr->lng, $vdata->lat, $vdata->long);    
    $ttrd = round($distance * PER_KM_TIME);
  }
  $db->where('user_id', $order->hero_id)->update(HERO, [
    'lat' => $details['t'],
    'long' => $details['l']
  ]);
  $db->where('id', $order->order_id)->update(ORDERS, [
    'ttrh' => $ttrh,
    'ttrd' => $ttrd
  ]);
  return 200;
}

//Food Extra Sections
function Tw_TglFoodToSection($food_id, $section, $vendor_id){
  global $db;
  if(empty($food_id) || empty($vendor_id)){
    return "~Try Again";
  }
  $already = $db->where('vendor_id', $vendor_id)->getOne(XTRA_FD);
  if($already){
    $data = explode(',', $already->data);    
    if(!in_array($food_id, $data)){      
      $data[] = $food_id;      
      $db
      ->where('section', $section)
      ->where('vendor_id', $vendor_id)
      ->update(XTRA_FD, [
        'data' => implode(',', $data)
      ]);
    }else{
      $idx = array_search($food_id, $data);
      array_splice($data, $idx, 1);
      if(empty($data)){
        $db
        ->where('section', $section)
        ->where('vendor_id', $vendor_id)
        ->delete(XTRA_FD);
      }else{
        $db
        ->where('section', $section)
        ->where('vendor_id', $vendor_id)
        ->update(XTRA_FD, [
          'data' => implode(',', $data)
        ]);
      }      
    }
  }else{
      $db->insert(XTRA_FD, [
        'data' => $food_id,
        'section' => $section,
        'vendor_id' => $vendor_id
      ]);
  }
  return true;
}

function Tw_GetExtraSection($vendor_id, $userMode = false){
  global $db;
  $sections = [OUR_SPECIAL,MUST_TRY];
  $final = []; 
  foreach($sections as $key => $section){
    $panel = $db->where('section', $section)->where('vendor_id', $vendor_id)->getOne(XTRA_FD);
    if($panel){
      $title = $section == OUR_SPECIAL ? 'Our Special 😋' : 'Must Try 🔥';
      $ids = explode(',', $panel->data);
      if($userMode){        
        $foodList = Tw_LoadVendorFood($vendor_id, [FD_BOTH,FD_ONLYDLV], $ids);
      }else{
        $foodList = Tw_LoadVendorFoods2($vendor_id, 0, -1, $ids);
      }
      $final[] = [
        'title' => $title,
        'id' => $key,
        'data' => $foodList
      ];
    }    
  }
  return $final;
}

function Tw_GetSectionData($section, $vendor_id){  
  global $db;  
  $panel = $db->where('section', $section)->where('vendor_id', $vendor_id)->getOne(XTRA_FD);
  if($panel){
    $ids = explode(',', $panel->data);
    $foodList = Tw_LoadVendorFoods2($vendor_id, 0, -1, $ids);
  }else{
    $foodList = [];
  }
  return $foodList;
}

?>