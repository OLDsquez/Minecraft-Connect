<?php
/**************************************************************************\
||========================================================================||
|| Minecraft Connect ||
|| Copyright 2016 ||
|| Version 0.2 ||
|| Made by fizz on the official MyBB board ||
|| http://community.mybb.com/user-36020.html ||
|| I don't take responsibility for any errors caused by this plugin. ||
|| Always keep MyBB up to date and always keep this plugin up to date. ||
|| You may NOT redistribute this plugin, sell it, ||
|| remove copyrights, or claim it as your own in any way. ||
||========================================================================||
\*************************************************************************/

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'mctest.php');

require_once("./global.php");

#$lang->load("safelink");

// Add link in breadcrumb
add_breadcrumb('Minecraft Connect', "mctest.php");
if($mybb->settings['mcc_enabled'] != '1')
{
	$error = 'disabled';
	eval("\$mctest = \"".$templates->get("mctest")."\";");
	output_page($mctest);
	exit;
}

if($mybb->get_input('act') == 'mclogin')
{
	require('inc/plugins/MinecraftConnect/MCAuth.class.php');
	#$MCAuth = new MCAuth($mybb->settings['mcc_token']);
	$username = $db->escape_string(trim($mybb->get_input('mcusername')));
	$pass = $db->escape_string($mybb->get_input('mcpassword'));
	$mc = new MCAuth($username);
	if($mc->validateInput())
	{
		$auth = $mc->authenticate($username, $pass);
		if($auth == true)
		{
			$username = $mc->getUsername();
			$success = 'Successful login as '.$username.' ('.$mc->getClientToken().')!';
			$success .= "<br />Access Token: " . $mc->getAccessToken();
		}
		else
			$error = $mc->getErr();
	}
	else
		$error = $mc->getErr();
	/*
	* MYBB LOGIN LINK https://github.com/mybb/mybb/blob/feature/member.php#L1662
	*/
}

eval("\$mctest = \"".$templates->get("mctest")."\";");

output_page($mctest);

exit;