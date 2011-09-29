<?php

include_once("../../mainfile.php");

define( "XFTROVE_DIRNAME", 'xftrove');
define( "XFTROVE_URL", ICMS_URL . '/modules/' . XFTROVE_DIRNAME . '/');
define( "XFTROVE_ROOT_PATH", ICMS_ROOT_PATH . '/modules/' . XFTROVE_DIRNAME . '/');
define( "XFTROVE_IMAGES_URL", XFTROVE_URL . 'images/');

$mhandler = icms_gethandler('module');
$icmsModule = $xoopsModule = $mhandler->getByDirname('xftrove');

//icms_Debug_info( 'module', $icmsModule );

if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);

$projlookup = (isset($_GET['projlookup'])) ? trim(StopXSS($_GET['projlookup'])) : ((isset($_POST['projlookup'])) ? trim(StopXSS($_POST['projlookup'])) : '');
$startswith = (isset($_GET['startswith'])) ? trim(StopXSS($_GET['startswith'])) : ((isset($_POST['startswith'])) ? trim(StopXSS($_POST['startswith'])) : '');
$contains = (isset($_GET['contains'])) ? trim(StopXSS($_GET['contains'])) : ((isset($_POST['contains'])) ? trim(StopXSS($_POST['contains'])) : '');
$trovedetail = (isset($_GET['trovedetail'])) ? trim(StopXSS($_GET['trovedetail'])) : ((isset($_POST['trovedetail'])) ? trim(StopXSS($_POST['trovedetail'])) : '');

if ($projlookup == 'yes')
{
	$projlookup = true;
	include_once("../../mainfile.php");
}
else
{
	$projlookup = false;
	include ("header.php");
}

require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vars.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/trove.php");
require_once(ICMS_ROOT_PATH."/modules/xftrove/include/listlib.php");
$icmsOption['template_main'] = 'xftrove_project_list.html';
 
if (!$projlookup)
{
	$metaTitle = ": Project Search";
	include_once(ICMS_ROOT_PATH."/header.php");
}
else
{
	// Include the right headers, without drawing the entire theme.
	xoops_header(false);
	$currenttheme = getTheme();
	include(ICMS_ROOT_PATH."/themes/".$currenttheme."/theme.php");
	if (file_exists(ICMS_ROOT_PATH."/themes/".$currenttheme."/language/lang-".$icmsConfig['language'].".php") )
	{
		include(ICMS_ROOT_PATH."/themes/".$currenttheme."/language/lang-".$icmsConfig['language'].".php");
	}
	elseif (file_exists(ICMS_ROOT_PATH."/themes/".$currenttheme."/language/lang-english.php") )
	{
		include(ICMS_ROOT_PATH."/themes/".$currenttheme."/language/lang-english.php");
	}
}
 
 
if ("^" == substr($startswith, 0, 1))
{
	$startswith = substr($startswith, 1);
}
 
if (!isset($range)) $range = 10;
 
if ($range > 100) $range = 100;
 
if (!isset($offset)) $offset = 0;
 
if (!isset($firsttimeonpage))
{
	$descriptiondetail = "on";
	$firsttimeonpage = "";
}
 
if (!$projlookup)
{
	$icmsTpl->assign("displayProjectListHeader",
		displayProjectListHeader($startswith, $contains, $range, $offset, $projlookup, $trovedetail, $descriptiondetail, $firsttimeonpage));
}
else
{
	echo displayProjectListHeader($startswith, $contains, $range, $offset, $projlookup, $trovedetail, $descriptiondetail, $firsttimeonpage);
}
if ($startswith)
{
	$startswith = "^".$startswith;
}
else if($contains)
{
	$startswith = $contains;
}
 
if (!$projlookup)
{
	$icmsTpl->assign("displayProjectListNav",
		displayProjectListNav($startswith, $offset, $range, $projlookup, $trovedetail, $descriptiondetail));
	$icmsTpl->assign("displayProjectList",
		displayProjectList($startswith, $offset, $range, $projlookup, $trovedetail, $descriptiondetail));
	include_once(ICMS_ROOT_PATH."/footer.php");
}
else
{
	echo displayProjectListNav($startswith, $offset, $range, $projlookup, $trovedetail, $descriptiondetail);
	echo displayProjectList($startswith, $offset, $range, $projlookup, $trovedetail, $descriptiondetail);
}
?>