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
require_once('server/main.php');
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

if ($f == 'register') {    
    if (empty($_POST['email_or_phone']) || empty($_POST['username']) || empty($_POST['password'])) {
        $errors = "Please Check your details";
    } else {
        $is_exist = Yd_IsNameExist($_POST['username'], 0);        
        if ($is_exist) {
            $errors = 'Username already Exists!';
        }        
        if (in_array($_POST['username'], $yd['site_pages'])) {
            $errors = 'Invalid Username, please choose something else!';
        }
        if (strlen($_POST['username']) < 5) {
            $errors = 'Username too short!';
        }
        if (strlen($_POST['username']) > 32) {
            $errors = 'Username too Large!';
        }
        if (!preg_match('/^(?!.*\.\.)(?!.*\.$)[^\W][\w.]{0,29}$/', $_POST['username'])) {
            $errors = 'Invalid Username !';
        }
        if (strlen($_POST['password']) < 6) {
            $errors = "Password too short";
        }        
        $gender = 'male';
        if (!empty($_POST['gender'])) {
            if ($_POST['gender'] != 'male' && $_POST['gender'] != 'female') {
                $gender = 'male';
            } else {
                $gender = $_POST['gender'];
            }
        }
        $phone_number = '';
        if (filter_var($_POST['email_or_phone'], FILTER_VALIDATE_EMAIL) && !empty($_POST['email_or_phone'])) {
            if (Yd_EmailExists($_POST['email_or_phone']) === true) {
                $errors = 'Email already used';                
            } else {
                $user_uniq_email = $_POST['email_or_phone'];  
            }
        } else if(preg_match('/^\+?\d+$/', $_POST['email_or_phone']) && !empty($_POST['email_or_phone'])){
            $str          = md5(microtime());
            $id           = substr($str, 0, 2); 
            $user_uniq_email = (Yd_UserExists($id) === false) ? $_POST['username'].$id.'@ydcrackers.com' : $_POST['username'].'u_'.$id.'@ydcrackers.com';
            if (Yd_PhoneExists($_POST['email_or_phone']) === true) {
                $errors = 'Phone number already used';                
            } else {
                $phone_number = $_POST['email_or_phone'];
            }

        }else{
          $errors = 'Inavlid Email or Phone number';
        }        
    }
    $field_data = array();
    if (empty($errors)) {        
        $activate = '1';
        $re_data  = array(
            'email' => $user_uniq_email,
            'username' => Yd_Secure($_POST['username'], 0),
            'password' => Yd_Secure($_POST['password'], 0),
            'email_code' => Yd_Secure(md5($_POST['username']), 0),            
            'gender' => Yd_Secure($gender),
            'lastseen' => time(),
            'phone_number' => $phone_number,
            'active' => Yd_Secure($activate),
            'birthday' => '0000-00-00'
        );        
        $register = Yd_RegisterUser($re_data);
        if ($register === true) {
            if ($activate == 1) {
                $data  = array(
                    'status' => 200,
                    'message' => "Successfully Registered!"
                );
                $login = Yd_Login($_POST['username'], $_POST['password']);
                if ($login === true) {
                    $session             = Yd_CreateLoginSession(Yd_UserIdFromUsername($_POST['username']));
                    $_SESSION['user_id'] = $session;
                    setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
                }                
                $data['location'] = $yd['site_url'];
            }
        }else{
           $errors = 'Unable to register currently!';
        }
        if (!empty($_SESSION['user_id'])) {
            $user_id = Yd_GetUserFromSessionID($_SESSION['user_id']);            
        }
    }
    header("Content-type: application/json");
    if (isset($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    } else {
        echo json_encode($data);
    }
    exit();
}else if ($f == 'login') {
    $data_ = array();
    $phone = 0;
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = Yd_Secure($_POST['username']);
        $password = Yd_Secure($_POST['password']);
        $result   = Yd_Login($username, $password);
        if ($result === false) {
            $errors[] = "Incorrect Password and Username/Email/Phone";
        } else if (Yd_UserInactive($_POST['username']) === true) {
            $errors[] = "Sorry, Your account is disabled.";
        } else if (Yd_UserActive($_POST['username']) === false) {
            $_SESSION['code_id'] = Yd_UserIdForLogin($username);
            $data_               = array(
                'status' => 600,
                'location' => 'logout'
            );
            $phone = 1;
        }
        if (empty($errors) && $phone == 0) {
            $userid              = Yd_UserIdForLogin($username);
            $ip                  = Yd_Secure(get_ip_address());
            $update              = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `ip_address` = '{$ip}' WHERE `user_id` = '{$userid}'");
            $session             = Yd_CreateLoginSession(Yd_UserIdForLogin($username));
            $_SESSION['user_id'] = $session;
            setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));            
            $data = array(
                'status' => 200,
                'message' => "Successfully loggedin!"
            );
            if (!empty($_POST['last_url'])) {
                $data['location'] = $_POST['last_url'];
            } else {
                $data['location'] = $yd['site_url'];
            }
        }
    }
    header("Content-type: application/json");
    if (!empty($errors)) {
        echo json_encode(array(
            'errors' => $errors
        ));
    } else if (!empty($data_)) {
        echo json_encode($data_);
    } else {
        echo json_encode($data);
    }
    exit();
} else if($f == 'create_seo_links'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
       $request = SEO_GenerateSEOLinks();           
       if($request){             
         $data = array('status' => 200);
       }else{
         $data = array('status' => 400);      
       }  
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}else if ($f == 'create_new_board') {
	  $data = array(
        'status' => 400
    );

    $location = empty($_POST['location']) ? '' : $_POST['location'];
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],
      'location' => $location,
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
              
      if(empty($_POST['name']) || empty($_POST['sname'])){
         $data = array(
          'status' => 300
         );
      }else if(Yd_CreateBoard($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
} else if ($f == 'edit_board') {
    $data = array(
        'status' => 400
    );

    $location = empty($_POST['location']) ? '' : $_POST['location'];
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'id' => $_POST['id'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],
      'location' => $location,
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about,
      'active' => $_POST['active']
    );
    if ($yd['loggedin'] == true) {
              
      if(empty($_POST['name']) || empty($_POST['sname'])){
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditBoard($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
} else if($f == 'delete_board'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteBoard($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}else if ($f == 'create_new_course') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'bid' => $_POST['bid'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],      
      'type' => $_POST['type'],
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
              
      if(empty($_POST['bid']) || empty($_POST['name']) || empty($_POST['sname'])){
         $data = array(
          'status' => 300
         );
      }else if(Yd_CreateCourse($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}else if ($f == 'edit_course') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'id' => $_POST['id'],
      'bid' => $_POST['bid'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],      
      'type' => $_POST['type'],
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about,
      'active' => $_POST['active']
    );
    if ($yd['loggedin'] == true) {
              
      if(empty($_POST['id']) || empty($_POST['bid']) || empty($_POST['name']) || empty($_POST['sname'])){
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditCourse($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}  else if($f == 'delete_course'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteCourse($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}else if ($f == 'create_new_branch') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
    $details = array(
      'crsid' => $_POST['crsid'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],
      'branch' => $_POST['branch'],
      'version' => $_POST['version'],            
      'tags' => $tags,
      'logo' => $logo,
      'about' => $about      
    ); 
    if ($yd['loggedin'] == true) {
              
      if(empty($_POST['crsid']) || empty($_POST['name']) || empty($_POST['sname']) || empty('branch') || empty('version')){
         $data = array(
          'status' => 300
         );
      }else if(Yd_CreateBranch($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
} else if($f == 'delete_branch'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteBranch($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if ($f == 'edit_branch') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
    $details = array(
      'id' => $_POST['id'],
      'crsid' => $_POST['crsid'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],
      'branch' => $_POST['branch'],
      'version' => $_POST['version'],            
      'tags' => $tags,
      'logo' => $logo,
      'about' => $about,
      'active' => $_POST['active']
    ); 
    if ($yd['loggedin'] == true) {
              
      if(Yd_EditBranch($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}else if ($f == 'add_branch_data') {
    $data = array(
        'status' => 400
    );
    
    $details = array(
      'brid' => $_POST['brid'],
      'year' => $_POST['year'],
      'semester' => $_POST['semester'],
      'content_categories' => $_POST['content_categories'],
      'code' => $_POST['code']
    );
    if ($yd['loggedin'] == true) {
              
      if(empty($_POST['brid']) || empty($_POST['year']) || empty($_POST['content_categories']) || empty($_POST['semester']) || empty($_POST['code']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddBranchData($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}else if ($f == 'edit_branch_data') {
    $data = array(
        'status' => 400
    );        
    $details = array(
      'id' => $_POST['id'],
      'brid' => $_POST['brid'],
      'year' => $_POST['year'],
      'content_categories' => $_POST['content_categories'],
      'semester' => $_POST['semester'],
      'code' => $_POST['code']
    );
    if ($yd['loggedin'] == true) {
              
      if(empty($_POST['brid']) || empty($_POST['year']) || empty($_POST['semester']) || empty($_POST['code']) || empty($_POST['content_categories']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditBranchData($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}else if ($f == 'delete_branch_data') {
    $data = array(
        'status' => 400
    );
    if ($yd['loggedin'] == true) {
              
      if(empty($_POST['id'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_DeleteBranchData($_POST['id'])){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
} else if ($f == 'create_new_class') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'crsid' => $_POST['crsid'],
      'name' => Yd_NumToOrdinalWord($_POST['cls']),
      'cls' => $_POST['cls'],      
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['crsid']) || empty($_POST['cls']) ){
         $data = array(
          'status' => 300
         );
      }else if(Yd_CreateClass($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}  else if ($f == 'edit_class') {
    $data = array(
        'status' => 400
    );    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }  
    $details = array(
      'id' => $_POST['id'],
      'crsid' => $_POST['crsid'],
      'name' => Yd_NumToOrdinalWord($_POST['cls']),
      'cls' => $_POST['cls'],      
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about,
      'active' => $_POST['active']
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['crsid']) || empty($_POST['cls']) ){
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditClass($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}  else if($f == 'delete_class'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteClass($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if ($f == 'add_new_brsubject') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'brid' => $_POST['brid'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],
      'code' => $_POST['code'],      
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['brid']) || empty($_POST['name']) || empty($_POST['sname']) || empty($_POST['code'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddBRSubject($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}  else if ($f == 'edit_br_subject') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'id' => $_POST['id'],
      'brid' => $_POST['brid'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],
      'code' => $_POST['code'],      
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about,
      'active' => $_POST['active'],       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['id']) || empty($_POST['brid']) || empty($_POST['name']) || empty($_POST['sname']) || empty($_POST['code'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditBRSubject($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}  else if($f == 'delete_br_subject'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteBRSubject($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if ($f == 'add_new_clsubject') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'clid' => $_POST['clid'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['clid']) || empty($_POST['name']) || empty($_POST['sname']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddCLSubject($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}  else if ($f == 'edit_cl_subject') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'id' => $_POST['id'],
      'clid' => $_POST['clid'],
      'name' => $_POST['name'],
      'sname' => $_POST['sname'],
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about,       
      'active' => $_POST['active']
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['id']) || empty($_POST['clid']) || empty($_POST['name']) || empty($_POST['sname']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditCLSubject($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

} else if($f == 'delete_cl_subject'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteCLSubject($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}  else if ($f == 'add_chapter') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'subid' => $_POST['subid'],
      'name' => $_POST['name'],
      'type' => $_POST['type'],
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['subid']) || empty($_POST['name']) || empty($_POST['type']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddChapter($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}   else if ($f == 'edit_chapter') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else{
      $logo = '';
    }
    
  
    $details = array(
      'id' => $_POST['id'],
      'subid' => $_POST['subid'],
      'type' => $_POST['type'],
      'name' => $_POST['name'],      
      'tags' => $tags,        
      'logo' => $logo,
      'about' => $about,
      'active' => $_POST['active'],       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['subid']) || empty($_POST['name']) || empty($_POST['type']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditChapter($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

} else if($f == 'delete_chapter'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteChapter($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}   else if ($f == 'add_topic') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['data_file'])){
      $file = array(
          'file' => $_FILES["data_file"]["tmp_name"],
          'name' => $_FILES['data_file']['name'],
          'size' => $_FILES['data_file']['size'],
          'type' => $_FILES['data_file']['type']
      );      
    } else {
      $file = $_POST['data_content'];
    }
    
  
    $details = array(
      'chid' => $_POST['chid'],
      'type' => $_POST['type'],
      'name' => $_POST['name'],
      'data' => $file,      
      'tags' => $tags,        
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['chid']) || empty($_POST['name']) || empty($_POST['type']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddTopic($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}    else if ($f == 'edit_topic') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['data_file'])){
      $file = array(
          'file' => $_FILES["data_file"]["tmp_name"],
          'name' => $_FILES['data_file']['name'],
          'type' => $_FILES['data_file']['type']
      );      
    }else if($_POST['type'] == 'article') {
      $file = $_POST['data_content'];
    }else{
      $file = '';
    }
    
  
    $details = array(
      'id' => $_POST['id'],
      'chid' => $_POST['chid'],
      'type' => $_POST['type'],
      'name' => $_POST['name'],
      'active' => $_POST['active'],
      'data' => $file,      
      'tags' => $tags,        
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['id']) || empty($_POST['chid']) || empty($_POST['name']) || empty($_POST['type']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditTopic($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

} else if($f == 'delete_topic'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteTopic($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}   else if ($f == 'add_res_cat') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else {
      $logo = '';
    }
    
  
    $details = array(            
      'name' => $_POST['name'],
      'type' => $_POST['type'],
      'logo' => $logo,      
      'tags' => $tags,        
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['name']) || empty($_POST['type']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddResCat($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}   else if ($f == 'edit_res_cat') {
    $data = array(
        'status' => 400
    );
    
    $tags = empty($_POST['tags']) ? '' : $_POST['tags'];
    $about = empty($_POST['about']) ? '' : $_POST['about'];
    if(isset($_FILES['logo'])){
      $logo = array(
          'file' => $_FILES["logo"]["tmp_name"],
          'name' => $_FILES['logo']['name'],
          'type' => $_FILES['logo']['type']
      );      
    }else {
      $logo = '';
    }
    
  
    $details = array(            
      'id' => $_POST['id'],
      'name' => $_POST['name'],
      'type' => $_POST['type'],
      'logo' => $logo,      
      'tags' => $tags,        
      'about' => $about       
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['name']) || empty($_POST['type']) ) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditResCat($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

} else if($f == 'delete_res_cat'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteResCat($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if ($f == 'add_res_data') {
    $data = array(
        'status' => 400
    );
    
    $subtype = empty($_POST['subtype']) ? '' : $_POST['subtype'];    
    if(isset($_FILES['data_file'])){
      $file = array(
          'file' => $_FILES["data_file"]["tmp_name"],
          'name' => $_FILES['data_file']['name'],
          'type' => $_FILES['data_file']['type']
      );      
    }else {
      $file = $_POST['data_content'];
    }

    
  
    $details = array(            
      'name' => $_POST['name'],
      'resid' => $_POST['resid'],
      'type' => $_POST['type'],
      'typeid' => $_POST['typeid'],
      'size' => $_FILES["data_file"]['size'],
      'data' => $file, 
      'media' => $_POST['media'], 
      'subtype' => $subtype      
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['name']) || empty($_POST['resid']) || empty($_POST['type']) || empty($_POST['typeid']) ) {
         $data = array(
          'status' => 300
         );
      }else if($_POST['type'] == 1 &&  empty($_POST['subtype'])){        
        $data = array(
          'status' => 300
        );
      } else if(Yd_AddResData($details)){        

        $data = array(
        'status' => 200
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

} else if ($f == 'edit_res_data') {
    $data = array(
        'status' => 400
    );    
    $subtype = empty($_POST['subtype']) ? '' : $_POST['subtype'];    
    if(isset($_FILES['data_file'])){
      $file = array(
          'file' => $_FILES["data_file"]["tmp_name"],
          'name' => $_FILES['data_file']['name'],
          'type' => $_FILES['data_file']['type']
      );      
    }else if($_POST['media'] == 'article' || $_POST['media'] == 'json') {
      $file = $_POST['data_content'];
    }else{
      $file = '';
    }
    $details = array(            
      'name' => $_POST['name'],
      'id' => $_POST['id'],
      'resid' => $_POST['resid'],
      'type' => $_POST['type'],
      'typeid' => $_POST['typeid'],
      'media' => $_POST['media'],      
      'data' => $file, 
      'subtype' => $subtype      
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['resid']) || empty($_POST['type']) || empty($_POST['typeid']) ) {
         $data = array(
          'status' => 300
         );
      }else if($_POST['type'] == 1 &&  empty($_POST['subtype'])){        
        $data = array(
          'status' => 100
        );
      } else if(Yd_EditResData($details)){        

        $data = array(
        'status' => 200,
        'data' => $file
        );

      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();

}  else if($f == 'delete_res_data'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteResData($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if($f == 'search_boards'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['key'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $key = YD_Secure($_POST['key']);
           $limit = empty($_POST['limit']) ? 10 : YD_Secure($_POST['limit']);
           $boards = Yd_SearchBoards($key, $limit);           
           if($boards)             
             $data = array('status' => 200, 'data' => $boards);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if($f == 'search_chapters'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['key'] ) || empty( $_POST['type'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $key = YD_Secure($_POST['key']);
           $limit = empty($_POST['limit']) ? 10 : YD_Secure($_POST['limit']);
           $type = YD_Secure($_POST['type']);
           $boards = Yd_SearchChapters($key,$type,$limit);           
           if($boards)             
             $data = array('status' => 200, 'data' => $boards);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}  else if($f == 'search_topics'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['key'] ) || empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $key = YD_Secure($_POST['key']);
           $limit = empty($_POST['limit']) ? 10 : YD_Secure($_POST['limit']);
           $type = YD_Secure($_POST['id']);
           $boards = Yd_SearchTopics($key,$type,$limit);           
           if($boards)             
             $data = array('status' => 200, 'data' => $boards);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}   else if($f == 'load_news'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {
      $limit = empty($_POST['limit']) ? 10 : YD_Secure($_POST['limit']);
      $offset = empty($_POST['offset']) ? 0 : YD_Secure($_POST['offset']);      
      $news = Yd_GetNews($limit, $offset);           
      if($news)             
        $data = array('status' => 200, 'data' => $news);
      else               
        $data = array('status' => 400);
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if($f == 'get_key_sug'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true ) {
      $key = YD_Secure($_POST['key']);
      if(!empty($key)){
        $load = YD_GetSugFromKey($key); 
        if($load)             
          $data = array('status' => 200, 'data' => $load);
        else               
          $data = array('status' => 300);
      }else{
        $data = array('status' => 400);
      }
      
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} 
//New Article
else if($f == 'add_news_article'){
    $data = array(
        'status' => 400
    );    
    if(isset($_FILES['cover'])){
      $file = array(
          'tmp_name' => $_FILES["cover"]["tmp_name"],
          'name' => $_FILES['cover']['name'],
          'type' => $_FILES['cover']['type']
      );      
    }else {
      $file = '';
    }  
    $details = array(            
      'title' => $_POST['title'],
      'sub_title' => $_POST['description'],
      'data' => $_POST['data'],
      'file' => $file,
      'time' => time(),          
      'type' => $_POST['type'], 
        
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['title'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddNewsArticle($details)){        
        $data = array(
        'status' => 200
        );
      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
else if($f == 'edit_news_article'){
    $data = array(
        'status' => 400
    );    
    if(isset($_FILES['cover'])){
      $file = array(
          'tmp_name' => $_FILES["cover"]["tmp_name"],
          'name' => $_FILES['cover']['name'],
          'type' => $_FILES['cover']['type']
      );      
      $cover = '';
    }else {
      $file = '';
      $cover = $_POST['temp_cover'];
    }  
    $details = array(            
      'id' => $_POST['id'],
      'title' => $_POST['title'],
      'sub_title' => $_POST['description'],
      'data' => $_POST['data'],
      'cover' => $cover,
      'file' => $file,
      'time' => time(),          
      'type' => $_POST['type']        
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['title'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditNewsArticle($details)){        
        $data = array(
        'status' => 200
        );
      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}  else if($f == 'delete_article'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteNews($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}
//Apps
else if($f == 'install_app'){
    $data = array(
        'status' => 400
    );
    if(isset($_FILES['zip_file'])){
      $zipFile = array(
          'tmp_name' => $_FILES["zip_file"]["tmp_name"],
          'name' => $_FILES['zip_file']['name'],
          'type' => $_FILES['zip_file']['type']
      );      
    }else {
      $zipFile = '';
    }  
    $details = array(            
      'title' => $_POST['app_title'],      
      'about' => $_POST['about'],      
      'classes' => $_POST['classes'],
      'branches' => $_POST['branches'],      
      'app_ver' => $_POST['app_ver'],
      'app_level' => $_POST['app_level'],
      'content_category' => $_POST['content_category'],
      'zip_file' => $zipFile,              
      'type' => $_POST['type']      
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['app_title'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddApp($details)){        
        $data = array(
        'status' => 200
        );
      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
else if($f == 'update_app'){
    $data = array(
        'status' => 400
    );
    if(isset($_FILES['zip_file'])){
      $zipFile = array(
          'tmp_name' => $_FILES["zip_file"]["tmp_name"],
          'name' => $_FILES['zip_file']['name'],
          'type' => $_FILES['zip_file']['type']
      );      
    }else {
      $zipFile = '';
    }  
    $details = array(            
      'id' => $_POST['id'],
      'title' => $_POST['app_title'],      
      'about' => $_POST['about'],      
      'classes' => $_POST['classes'],
      'branches' => $_POST['branches'],      
      'app_ver' => $_POST['app_ver'],      
      'content_category' => $_POST['content_category'],      
      'app_level' => $_POST['app_level'], 
      'zip_file' => $zipFile,              
      'type' => $_POST['type']      
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['app_title'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditApp($details)){        
        $data = array(
        'status' => 200
        );
      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}else if($f == 'delete_app'){
    $data = array(
        'status' => 400
    );
     
    if ($yd['loggedin'] == true) {
      if(empty($_POST['id'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_DeleteApp($_POST['id'])){        
        $data = array(
        'status' => 200
        );
      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
//New Announcement
else if($f == 'create_announcement'){
    $data = array(
        'status' => 400
    );    
    if(isset($_FILES['cover'])){
      $file = array(
          'tmp_name' => $_FILES["cover"]["tmp_name"],
          'name' => $_FILES['cover']['name'],
          'type' => $_FILES['cover']['type']
      );      
    }else {
      $file = '';
    }  
    $details = array(            
      'title' => $_POST['title'],
      'about' => $_POST['about'],
      'file' => $file,
      'type' => $_POST['type'],
      'data' => $_POST['data'],      
      'time' => time()
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['title'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_AddAnnouncement($details)){        
        $data = array(
        'status' => 200
        );
      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
else if($f == 'edit_announcement'){
    $data = array(
        'status' => 400
    );    
    if(isset($_FILES['cover'])){
      $file = array(
          'tmp_name' => $_FILES["cover"]["tmp_name"],
          'name' => $_FILES['cover']['name'],
          'type' => $_FILES['cover']['type']
      );      
      $cover = '';
    }else {
      $file = '';
      $cover = $_POST['temp_cover'];
    }  
    $details = array(            
      'id' => $_POST['id'],
      'title' => $_POST['title'],
      'about' => $_POST['about'],
      'data' => $_POST['data'],
      'cover' => $cover,
      'file' => $file,
      'time' => time(),          
      'type' => $_POST['type']        
    );
    if ($yd['loggedin'] == true) {
      if(empty($_POST['title'])) {
         $data = array(
          'status' => 300
         );
      }else if(Yd_EditAnnouncement($details)){        
        $data = array(
        'status' => 200
        );
      } else {
        $data = array(
          'status' => 400          
        );
      }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}  else if($f == 'delete_announcement'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 300
           );           
        }else {
           $id = YD_Secure($_POST['id']);
           $del = Yd_DeleteAnnouncement($id);           
           if($del)             
             $data = array('status' => 200);
           else               
             $data = array('status' => 400);
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if($f == 'get_subject_from_brcode'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['code'] )){
           $data = array(
            'status' => 400
           );           
        }else {
           $code = Yd_Secure($_POST['code']);
           $perform = Yd_GetBRSubjectsByBranchCode($code);
          if($perform == false){
             $data = array(
              'status' => '400',
             );
          } else if($perform) {
            $data = array(
              'status' => '200',
              'subjects' => $perform
            );
          }  
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
} else if($f == 'copy_subject'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['sub_ids'] ) || empty( $_POST['to_brid'] ) ){
           $data = array(
            'status' => 400
           );           
        }else {
          $sub_ids = $_POST['sub_ids'];
          $to_brid = Yd_Secure($_POST['to_brid']);
          $perform = Yd_CopyBRSubjectsById($sub_ids, $to_brid);
          if($perform == false){
             $data = array(
              'status' => '400',
             );
          } else if($perform) {
            $data = array(
              'status' => '200',
              'log' => $perform
            );
          }  
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}  else if($f == 'create_content_category'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['name'] ) || empty( $_POST['about'] ) ){
           $data = array(
            'status' => 400,
            'message' => 'Please Check Your Details'
           );           
        }else if(strlen( $_POST['name'] ) < 4 || strlen( $_POST['about'] ) < 10){
           $data = array(
            'status' => 400,
            'message' => 'Name or about section too short'
           );
        }else {
          $parameters = array(
            'name' => Yd_Secure($_POST['name']),
            'about' => Yd_Secure($_POST['about'])
          );
          $perform = Yd_AddContentCategory($parameters);
          if($perform == false){
             $data = array(
              'status' => '400',
              'message' => 'Unable to add Content Category'
             );
          } else if($perform) {
            $data = array(
              'status' => '200',
              'message' => 'Content Category Added'
            );
          }  
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}  else if($f == 'edit_content_category'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['name'] ) || empty( $_POST['about'] ) ){
           $data = array(
            'status' => 400,
            'message' => 'Please Check Your Details'
           );           
        }else if(strlen( $_POST['name'] ) < 4 || strlen( $_POST['about'] ) < 10){
           $data = array(
            'status' => 400,
            'message' => 'Name or about section too short'
           );
        }else {
          $parameters = array(
            'id' => Yd_Secure($_POST['id']),
            'name' => Yd_Secure($_POST['name']),
            'about' => Yd_Secure($_POST['about'])
          );
          $perform = Yd_EditContentCategory($parameters);
          if($perform == false){
             $data = array(
              'status' => '400',
              'message' => 'Unable to Edit Content Category'
             );
          } else if($perform) {
            $data = array(
              'status' => '200',
              'message' => 'Content Category Edit'
            );
          }  
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}  else if($f == 'delete_content_category'){
    $data = array(
        'status' => 400
    );    
    if ($yd['loggedin'] == true) {        
        if(empty( $_POST['id'] )){
           $data = array(
            'status' => 400,
            'message' => 'Please Check Your Details'
           );           
        }else if(!is_numeric( $_POST['id'] )){
           $data = array(
            'status' => 400,
            'message' => 'Invalid Id'
           );
        }else {
          $id = Yd_Secure($_POST['id']);
          $perform = Yd_DeleteContentCategory($id);
          if($perform == false){
             $data = array(
              'status' => '400',
              'message' => 'Unable to Edit Content Category'
             );
          } else if($perform) {
            $data = array(
              'status' => '200',
              'message' => 'Content Category Edit'
            );
          }  
        }       
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit(); 
}


        
      
?>