<?php

if($tw['loggedin'])header('location: '.$tw['site_url']);

$tw['title']       = $tw['site_name'].' | Login';
$tw['desc']        = 'Login in '.$tw['site_name'].' and starting buy tasty food';
$tw['keywords']    = 'login,signin,'.$tw['site_name'];
$tw['page']        = 'login';
echo Tw_SetPage('login');

?>