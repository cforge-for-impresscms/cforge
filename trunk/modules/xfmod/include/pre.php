<?php
/**
 * pre.php - Automatically prepend to every page.
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @version   $Id: pre.php,v 1.4 2003/11/18 21:50:24 devsupaul Exp $
 */


/*
        redirect to proper hostname to get around certificate problem on IE 5
*/
if (!isset($langfile) || !$langfile)
  $langfile = "main.php";

// XoopsForge GLOBAL language file
if ( file_exists(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/global.php") ) {
        include(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/global.php");
} else {
        include(XOOPS_ROOT_PATH."/modules/xfmod/language/english/global.php");
}
// XoopsForge Mail Messages language file
if ( file_exists(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/mailmessages.php") ) {
        include(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/mailmessages.php");
} else {
        include(XOOPS_ROOT_PATH."/modules/xfmod/language/english/mailmessages.php");
}
// XoopsForge language file
if ( file_exists(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/".$langfile) ) {
        include(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/".$langfile);
} else {
        include(XOOPS_ROOT_PATH."/modules/xfmod/language/english/".$langfile);
}

include_once(XOOPS_ROOT_PATH."/class/module.textsanitizer.php");

$xoopsForge['version'] = "XoopsForge 2.0.7";

//library to determine browser settings
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/browser.php");

//utils
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/utils.php");

//utils
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/html.php");

//base error library for new objects
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/Error.class");

//database abstraction
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/database-mysql.php");

//group functions like get_name, etc
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/Group.class");

//permission functions
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/Permission.class");

//Project extends Group and includes preference accessors
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/Project.class");

//Foundry extends Group and includes preference/data accessors
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/Foundry.class");

//load the xoopsForge config variables
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/config.php");

//Error handler
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/errorhandler.php");

$ts = MyTextSanitizer::getInstance();
$sys_datefmt = "Y-m-d H:i";

// Priority Colors:
$bgpri[1] = '#dadada';
$bgpri[2] = '#dad0d0';
$bgpri[3] = '#dacaca';
$bgpri[4] = '#dac0c0';
$bgpri[5] = '#dababa';
$bgpri[6] = '#dab0b0';
$bgpri[7] = '#daaaaa';
$bgpri[8] = '#da9090';
$bgpri[9] = '#da8a8a';
// include_once(XOOPS_ROOT_PATH."/themes/".getTheme()."/forge.php");

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/logger.php");
?>