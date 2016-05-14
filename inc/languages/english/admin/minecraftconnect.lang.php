<?php
/**************************************************************************\
||========================================================================||
|| Minecraft Connect English Language File (Admin) ||
|| Copyright 2016 ||
|| Version 0.7 ||
|| Made by fizz on the official MyBB board ||
|| http://community.mybb.com/user-36020.html ||
|| https://github.com/squez/Minecraft-Connect/ ||
|| I don't take responsibility for any errors caused by this plugin. ||
|| Always keep MyBB up to date and always keep this plugin up to date. ||
|| You may NOT redistribute this plugin, sell it, ||
|| remove copyrights, or claim it as your own in any way. ||
||========================================================================||
\*************************************************************************/
$l['mcc'] = 'Minecraft Connect';

// Plugin Install
$l['mcc_curl_disabled'] = 'cURL is disabled on your PHP installation. Please enable cURL to install Minecraft Connect.';
$l['mcc_curl_ssl'] = 'cURL is not configured to use HTTPS properly.';
$l['mcc_curl_ca'] = 'cURL is not set up with a proper Certificate Authority root certificate bundle.
 Please view <a href="http://community.mybb.com/thread-188755.html" target="_NEW">THIS GUIDE</a> on how to add one so that you can install Minecraft Connect';

// UserCP
$l['mcc_usercpnav'] = 'Minecraft Connect'; // UserCP usercp nav list 
$l['mcc_usercp_menu_title'] = 'Minecraft Connect';
$l['mcc_usercp_title'] = 'Minecraft Connect';
$l['mcc_link'] = 'Link Account';
$l['mcc_unlink'] = 'Unlink Account';
$l['mcc_username'] = 'Minecraft Username/Email';
$l['mcc_id'] = 'Minecraft ID';
$l['mcc_password'] = 'Minecraft Password';
$l['mcc_usercp_err'] = 'Invalid link/unlink request.';
$l['mcc_usercp_link_title'] = 'Minecraft Connect - Link Account';
$l['mcc_usercp_link_err'] = 'Invalid account link request.';
$l['mcc_already_linked'] = 'You already have a Minecraft account connected to your MyBB profile.';
$l['mcc_link_heading'] = 'Enter your Minecraft account information to authenticate with Mojang servers. Your password will never be stored on this site.';
$l['mcc_no_link'] = 'No Minecraft account linked to your MyBB profile.';
$l['mcc_usercp_unlink_title'] = 'Minecraft Connect - Unlink Account';
$l['mcc_unlink_heading'] = 'Delete your Minecraft account information from your MyBB profile? You cannot undo this.';
$l['mcc_unlink_confirm'] = 'Are you sure?';
$l['mcc_link_success'] = 'Successfully linked Minecraft username <strong>{1}</strong>!';
$l['mcc_unlink_success'] = 'Successfully unlinked Minecraft account <strong>{1}</strong>!';
$l['mcc_unlink_fail'] = 'Failed to unlink Minecraft account.';
$l['mcc_name_taken'] = 'That Minecraft name is already taken!';

// minecraftconnect.php (Main Page)
$l['mcc_login_success'] = 'Successfully logged in via Minecraft Connect, <strong>{1}</strong>(MC username: <strong>{2}</strong>)! Redirecting to home page...';
$l['mcc_login_fail'] = 'Failed to login with Minecraft credentials.';
$l['mcc_login_header'] = 'Login to MyBB with your Minecraft account info! Powered by <a href="https://github.com/squez/Minecraft-Connect/" target="_NEW">Minecraft Connect</a>';
$l['mcc_already_loggedin'] = 'Already logged in! Redirecting to forum index...';

// Login/Register template edit
$l['mcc_login'] = 'Login with Minecraft';

// WOL
$l['mcc_viewing_login'] = 'Logging in with Minecraft Connect';

// MCAuth
$l['mcc_invalid_username'] = 'Invalid username.';