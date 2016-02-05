<?php
/**************************************************************************\
||========================================================================||
|| Minecraft Connect ||
|| Copyright 2016 ||
|| Version 0.4 ||
|| Made by fizz on the official MyBB board ||
|| http://community.mybb.com/user-36020.html ||
|| https://github.com/squez/Minecraft-Connect/ ||
|| I don't take responsibility for any errors caused by this plugin. ||
|| Always keep MyBB up to date and always keep this plugin up to date. ||
|| You may NOT redistribute this plugin, sell it, ||
|| remove copyrights, or claim it as your own in any way. ||
||========================================================================||
\*************************************************************************/

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'minecraftconnect.php');

require_once("./global.php");

if(!$lang->mcc)
	$lang->load("minecraftconnect");

// Add link in breadcrumb
add_breadcrumb($lang->mcc, "minecraftconnect.php");

// Redirect user to board index if Minecraft Connect is disabled
if($mybb->settings['mcc_enabled'] != 1)
{
	header("Location: index.php");
	exit;
}

$content = $lang->mcc_login_header;
if($mybb->get_input('act') == 'login')
{
	$content = $lang->mcc_login_header;

	if($mybb->request_method == 'post')
	{
		verify_post_check($mybb->get_input('my_post_key'));

		require('inc/plugins/MinecraftConnect/MCAuth.class.php');

		$username = $db->escape_string(trim($mybb->get_input('mccusername')));
		$pass = $db->escape_string($mybb->get_input('mccpassword'));
		$mc = new MCAuth($username);
		if($mc->validateInput())
		{
			// Authenticate the user with Mojang's API
			$auth = $mc->authenticate($username, $pass);
			if($auth == true)
			{
				$mcuser = $mc->getUsername();
				// if user authenticated, log them in to MyBB
				if($mc->login($mcuser))
					redirect('index.php', $lang->sprintf($lang->mcc_login_success, $mcuser));
				else
					redirect('minecraftconnect.php?act=login', $lang->mcc_login_fail);
			}
			else
				$content = $mc->getErr();
		}
		else
			$content = $mc->getErr();
	}
}

eval("\$minecraftconnect = \"".$templates->get("mcc_main")."\";");

output_page($minecraftconnect);

exit;