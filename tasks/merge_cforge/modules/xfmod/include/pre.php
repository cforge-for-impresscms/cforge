<?php
/**
* pre.php - Automatically prepend to every page.
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001(c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: pre.php,v 1.4 2003/11/18 21:50:24 devsupaul Exp $
*/
 
 
/*
redirect to proper hostname to get around certificate problem on IE 5
*/
$icmsModule = icmsModule::getByDirname("xfmod");

if (!isset($langfile) || !$langfile)
$langfile = "main.php";

// XoopsForge GLOBAL language file
if (file_exists(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/global.php"))
{
	include(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/global.php");
}
else
{
	include(ICMS_ROOT_PATH."/modules/xfmod/language/english/global.php");
}
// XoopsForge Mail Messages language file
if (file_exists(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/mailmessages.php"))
{
	include(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/mailmessages.php");
}
else
{
	include(ICMS_ROOT_PATH."/modules/xfmod/language/english/mailmessages.php");
}
// XoopsForge language file
if (file_exists(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/".$langfile))
{
	include(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/".$langfile);
}
else
{
	include(ICMS_ROOT_PATH."/modules/xfmod/language/english/".$langfile);
}
 
include_once(ICMS_ROOT_PATH."/class/module.textsanitizer.php");

$icmsForge['version'] = "XoopsForge 2.0.7";

//library to determine browser settings
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/browser.php");
 
//utils
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/utils.php");
 
//utils
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/html.php");
 
//base error library for new objects
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/Error.class.php");
 
//database abstraction
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/database-mysql.php");
 
//group functions like get_name, etc
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/Group.class.php");
 
//permission functions
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/Permission.class.php");
 
//Project extends Group and includes preference accessors
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/Project.class.php");
 
//Foundry extends Group and includes preference/data accessors
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/Foundry.class.php");
 
//load the xoopsForge config variables
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/config.php");
 
//Error handler
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/errorhandler.php");
 
$ts = MyTextSanitizer::getInstance();
$sys_datefmt = "Y-m-d H:i";
 
// Priority Colors:
$bgpri[1] = '#DADADA';
$bgpri[2] = '#DAD0D0';
$bgpri[3] = '#DACACA';
$bgpri[4] = '#DAC0C0';
$bgpri[5] = '#DABABA';
$bgpri[6] = '#DAB0B0';
$bgpri[7] = '#DAAAAA';
$bgpri[8] = '#DA9090';
$bgpri[9] = '#DA8A8A';
// include_once(ICMS_ROOT_PATH."/themes/".getTheme()."/forge.php");
 
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/logger.php");
?>