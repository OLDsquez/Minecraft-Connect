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
    global $db, $mybb, $lang;

    if(!$lang->mcc)
        $lang->load('minecraftconnect');
    
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

    // Add new columns to user table
    $db->add_column('users', 'mcc_username', 'varchar(65)');
    $db->add_column('users', 'mcc_accesstoken', 'varchar(40)');
    $db->add_column('users', 'mcc_id', 'varchar(40)'); // ID == Client token

    rebuild_settings();
}

function minecraftconnect_is_installed()
{
    global $db;

    $r = $db->simple_select('settings', 'name', "name='mcc_enabled'");
    if($db->num_rows($r) >= 1)
    {
        return true;
    }
    return false;

}

function minecraftconnect_activate()
{
    global $db, $mybb, $lang;

    if(!$lang->mcc)
        $lang->load('minecraftconnect');

    $templates[] = array(
        "tid" => "NULL",
        "title" => "mcc_test",
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

    // UserCP Nav menu title
    $templates[] = array(
        'tid'   => 'NULL',
        'title' => 'mcc_usercp_menu',
        'template' => $db->escape_string('
<tr>
    <td class="tcat">
        <div class="expcolimage">
            <img src="{$theme[\'imgdir\']}/collapse{$collapsedimg[\'collapse\']}.png" id="usercpmcc_img" class="expander" alt="[-]" title="[-]" />
        </div>
        <div>
            <span class="smalltext">
            <strong>{$lang->mcc_usercp_menu_title}</strong>
            </span>
        </div>
    </td>
</tr>
<tbody style="{$collapsed[\'usercpmcc_e\']}" id="usercpmcc_e">
    <tr>
        <td class="trow1 smalltext">
            <a href="usercp.php?action=minecraftconnect" class="usercp_nav_item usercp_nav_mcc">{$lang->mcc_usercpnav}</a>
        </td>
    </tr>
</tbody>'),
        'sid'   => '-1'
        );

    // UserCP main content
    $templates[] = array(
        'tid'   => 'NULL',
        'title' => 'mcc_usercp',
        'template' => $db->escape_string('
<html>
<head>
<title>{$lang->mcc_usercp_title} - {$mybb->settings[\'bbname\']}</title>
{$headerinclude}
</head>
<body>
 {$header}
<table width="100%" border="0" align="center">
<tr>
     {$usercpnav}
    <td valign="top">
         {$inlinesuccess}
        <form action="usercp.php?action=minecraftconnect" method="post">
            <input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
            <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
            <thead>
            <tr>
                <th class="thead" colspan="2">
                    <strong>{$lang->mcc_usercp_title}</strong>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="trow1" valign="top" width="25%">
                    <tr>
                        <td class="{$altbg}"><strong>{$lang->mcc_username}</strong></td>
                    </tr>
                    <tr>
                        <td class="{$altbg}"><strong>{$lang->mcc_id}</strong></td>
                    </tr>
                </td>
                <td class="trow1" valign="top">
                    <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" width="75%">
                     {$settings}
                    </table>
                </td>
            </tr>
            </tbody>
            </table>
            <div style="text-align:center;">
                <input type="submit" class="button" value="{$lang->mcc_unlink}" />
            </div>
        </form>
    </td>
</tr>
</table>
 {$footer}
</body>
</html>'),
        'sid'   => '-1'
        );

    $db->insert_query_multiple('templates', $templates);
}

function minecraftconnect_deactivate()
{
    global $db, $mybb;
    $mybb->settings['mcc_enabled'] = 0; // Disable Minecraft Connect automatically

    // Delete templates
    $db->delete_query('templates', "title LIKE 'mcc_%'");

    rebuild_settings();
}

function minecraftconnect_uninstall()
{
    global $db;

    $query = $db->write_query("SELECT `gid` FROM `".TABLE_PREFIX."settinggroups` WHERE name='mcc'");
    $g = $db->fetch_array($query);
    $db->write_query("DELETE FROM `".TABLE_PREFIX."settinggroups` WHERE gid='".$g['gid']."'");
    $db->write_query("DELETE FROM `".TABLE_PREFIX."settings` WHERE gid='".$g['gid']."'");

    // Drop MCC columns
    $db->drop_column('users', 'mcc_username');
    $db->drop_column('users', 'mcc_accesstoken');
    $db->drop_column('users', 'mcc_id');

    rebuild_settings();
}
// Do plugin hooks
global $mybb;

if($mybb->settings['mcc_enabled'])
{
    // Global
    $plugins->add_hook('global_start', 'minecraftconnect_global');

    // UserCP
    $plugins->add_hook('usercp_menu', 'minecraftconnect_usercp_menu', 40);
    $plugins->add_hook('usercp_start', 'minecraftconnect_usercp');
}

// add mcc templates to templatelist on proper pages
function minecraftconnect_global()
{
    global $mybb, $lang, $templatelist;

    if($templatelist)
        $templatelist = explode(',', $templatelist);
    else
        $templatelist = array();

    if(THIS_SCRIPT == 'usercp.php')
        $templatelist[] = 'mcc_usercp_menu';

    if(THIS_SCRIPT == 'usercp.php' AND $mybb->get_input('action') == 'minecraftconnect')
    {
        $templatelist[] = 'mcc_usercp_menu';
        $templatelist[] = 'mcc_usercp';
    }

    $templatelist = implode(',', array_filter($templatelist));
    if(!$lang->mcc)
        $lang->load('minecraftconnect');
}

// add usercp nav menu item
function minecraftconnect_usercp_menu()
{
    global $mybb, $templates, $theme, $usercpmenu, $lang, $collapsed, $collapsedimg;
    
    if(!$lang->mcc)
        $lang->load("minecraftconnect");
    
    eval("\$usercpmenu .= \"" . $templates->get('mcc_usercp_menu') . "\";");
}

function minecraftconnect_usercp()
{
    global $mybb, $lang;
    if(!$lang->mcc)
        $lang->load('minecraftconnect');

    if($mybb->get_input('action') == 'minecraftconnect')
    {
        global $db, $theme, $templates, $headerinclude, $header, $footer, $plugins, $usercpnav;

        add_breadcrumb($lang->nav_usercp, 'usercp.php');
        add_breadcrumb($lang->mcc_usercp_title, 'usercp.php?action=minecraftconnect');

        $settings = '';
        $settingsArr = array('mcc_username', 'mcc_id');

        $q = $db->simple_select('users', implode(',', $settingsArr), "uid = '{$mybb->user['uid']}'");
        $r = $db->fetch_array($q);

        foreach($settingsArr as $s)
        {
            $settings .= "<tr><td>{$r[$s]}</td></tr>";
        }

        eval("\$page = \"" . $templates->get('mcc_usercp') . "\";");
        output_page($page);
    }
}