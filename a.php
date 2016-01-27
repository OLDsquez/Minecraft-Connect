<?php
require('inc/plugins/MinecraftConnect/MCAuth.class.php');
#echo strlen('808772fc24bc4d92ba2dc48bfecb375f');
#phpinfo();

$mc = new MCAuth();
$auth = $mc->authenticate('infect', 'tesatpw');

if($auth == true)
	echo 'success!';
else
	echo $mc->getErr();