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
        "version"       => "0.1",
        "guid"          => "",
        "codename"      => "minecraftconnect",
        "compatibility" => "18*"
    );
}

function minecraftconnect_install()
{
    global $db, $mybb;
    
    $mcc_group = array(
        'name' => 'mcc',
        'title' => 'Minecraft Connect',
        'description' => 'Edit the settings for Minecraft Connect here.',
        'disporder' => '1',
        'isdefault' => 'no',
        );
    $db->insert_query('settinggroups', $mcc_group);
    $gid = intval($db->insert_id());
    
    $psettings[] = array(
        'name' => 'mcc_enabled',
        'title' => 'Enabled',
        'description' => 'Do you want to enable Minecraft Connect?',
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => '1',
        'gid' => $gid
        );

    foreach($psettings as $setting)
    {
        $db->insert_query('settings', $setting);
    }

    rebuild_settings();
}

function minecraftconnect_is_installed()
{
    global $mybb;

    if(isset($mybb->settings['mcc_enabled']))
        return true;
    else
        return false;
}

function minecraftconnect_activate()
{

}

function minecraftconnect_deactivate()
{

}

function minecraftconnect_uninstall()
{
    global $db;

    $query = $db->write_query("SELECT `gid` FROM `".TABLE_PREFIX."settinggroups` WHERE name='mcc'");
    $g = $db->fetch_array($query);
    $db->write_query("DELETE FROM `".TABLE_PREFIX."settinggroups` WHERE gid='".$g['gid']."'");
    $db->write_query("DELETE FROM `".TABLE_PREFIX."settings` WHERE gid='".$g['gid']."'");

    rebuild_settings();
}