<?php
/*****
* COPYRIGHT SHIT
*****/

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}


function minecraftconnect_info()
{
    return array(
        "name"          => "Minecraft Connect",
        "description"   => "Login to MyBB with your Minecraft account",
        "website"       => "http://community.mybb.com/user-36020.html", #CHANGE TO FORUM RELEASE THREAD URL
        "author"        => "fizz",
        "authorsite"    => "http://community.mybb.com/user-36020.html",
        "version"       => "0.2", //0.5 when login is done, 1.0 when registrate w/ minecraft is done?
        "guid"          => "",
        "codename"      => "minecraftconnect",
        "compatibility" => "18*"
    );
}

function minecraftconnect_install()
{
    global $db, $mybb;
    
    $mcc_group = array(
        'name'          => 'mcc',
        'title'         => 'Minecraft Connect',
        'description'   => 'Edit the settings for Minecraft Connect here.',
        'disporder'     => '1',
        'isdefault'     => 'no',
        );
    $db->insert_query('settinggroups', $mcc_group);
    $gid = intval($db->insert_id());
    
    $psettings[] = array(
        'name'          => 'mcc_enabled',
        'title'         => 'Enabled',
        'description'   => 'Do you want to enable Minecraft Connect?',
        'optionscode'   => 'yesno',
        'value'         => '1',
        'disporder'     => '1',
        'gid'           => $gid
        );

    foreach($psettings as $setting)
    {
        $db->insert_query('settings', $setting);
    }

    // TO DO (1/27):Add new mc connect fields to users table
    // string(32) client token, string mcusername, string account token
    // BE SURE TO EDIT _uninstall() TO REMOVE THEM PROPERLY

    rebuild_settings();
}

function minecraftconnect_is_installed()
{
    global $db, $mybb;

    /*if(isset($mybb->settings['mcc_enabled']))
        return true;
    else
        return false;*/
    $r = $db->simple_select('settings', 'name', "name='mcc_enabled'");
    if($db->num_rows($r) >= 1)
    {
        return true;
    }
    return false;

}

function minecraftconnect_activate()
{
    global $db, $mybb;

    $template = array(
        "tid" => "NULL",
        "title" => "mctest",
        "template" => $db->escape_string('
        <html>
<head>
<title>Minecraft Connect</title>
{$headerinclude}
</head>
<body>
{$header}
<br />
<table width="100%" border="0" align="center">
<tr>
{$cpnav}
<td valign="top">
{$cptable}
</td>
<td valign="top" style="background-color:#f0f0f0;border:1px dotted #7eb6ff;padding:4px;">
<span><strong><font color="red">{$error}</font></strong></span>
<br />
<span><strong><font color="green">{$success}</font></strong></span>
<br /><br />
<form action="mctest.php?act=mclogin" method="POST">
Username: <input type="text" name="mcusername"> Password: <input type="password" name="mcpassword">
<br />
<input type="submit" name="mcSubmit" value="Login">
</form>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
    "sid" => "-1"
    );
    $db->insert_query("templates", $template);
}

function minecraftconnect_deactivate()
{
    global $mybb;
    $mybb->settings['mcc_enabled'] = 0; // Disable Minecraft Connect automatically

    rebuild_settings();
}

function minecraftconnect_uninstall()
{
    global $db;

    $query = $db->write_query("SELECT `gid` FROM `".TABLE_PREFIX."settinggroups` WHERE name='mcc'");
    $g = $db->fetch_array($query);
    $db->write_query("DELETE FROM `".TABLE_PREFIX."settinggroups` WHERE gid='".$g['gid']."'");
    $db->write_query("DELETE FROM `".TABLE_PREFIX."settings` WHERE gid='".$g['gid']."'");

    // Delete templates
    $db->delete_query('templates', 'title = \'mctest\'');

    rebuild_settings();
}