<?php

if($tw['loggedin'])header('location: '.$tw['site_url']);

$tw['title']       = $tw['site_name'].' | Register';
$tw['desc']        = 'Create your account on '.$tw['site_name'].' and starting buy tasty food';
$tw['keywords']    = 'Create account,signup,'.$tw['site_name'];
$tw['page']        = 'register';
echo Tw_SetPage('register');

?>