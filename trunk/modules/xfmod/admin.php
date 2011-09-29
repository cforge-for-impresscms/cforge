<?php

$fct = (isset($_GET['fct'])) ? trim($_GET['fct']): NULL;

include("../../mainfile.php");
include(XOOPS_ROOT_PATH."/include/cp_functions.php");
if ( file_exists("language/".$xoopsConfig['language']."/admin.php") ) 
{
	include("language/".$xoopsConfig['language']."/admin.php");
}
else
{
	include("language/english/admin.php");
}

include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");
/*********************************************************/
/* Admin Authentication                                  */
/*********************************************************/
$admintest = 0;

if ( $xoopsUser ) 
{
	$xoopsModule = XoopsModule::getByDirname("xfmod");
	if ( !$xoopsUser->isAdmin($xoopsModule->mid()) ) 
	{
		redirect_header(XOOPS_URL."/",3,_NOPERM);
		exit();
	}
	$admintest=1;

}
else
{
	redirect_header(XOOPS_URL."/",3,_NOPERM);
	exit();
}
if ( $admintest == 1 )
{
	if ( $fct != "" )
	{ 
		if ( file_exists("admin/".$fct."/main.php") )
		{

			if ( file_exists("language/".$xoopsConfig['language']."/admin/".$fct.".php") )
			{
				include("language/".$xoopsConfig['language']."/admin/".$fct.".php");
			}
			elseif ( file_exists("language/english/admin/".$fct.".php") )
			{
				include("language/english/admin/".$fct.".php");
			}

			include("admin/".$fct."/main.php");
		}
		else
		{
			xoops_cp_header();
			xoopsforge_menu();
    		xoops_cp_footer();
		}
	}
	else
	{
		xoops_cp_header();
		xoopsforge_menu();
    	xoops_cp_footer();
	}
}

/*********************************************************/
/* Core Menu Functions                                   */
/*********************************************************/

function xoopsforge_menu_item($folder, $modversion) {
	global $xoopsConfig;

	echo "<a href='".XOOPS_URL."/modules/xfmod/admin.php?fct=".$folder."'><b>" .trim($modversion['name'])."</b></a>\n";
}

function xoopsforge_menu() {
	global $xoopsConfig, $xoopsUser, $xoopsModule;

	echo "<table border='0' cellpadding='1' align='center'>";
	echo "<tr>";
	$i = 0;
	$admin_dir = XOOPS_ROOT_PATH."/modules/xfmod/admin";
	$handle=opendir($admin_dir);
	$counter = 0;
	while ($file = readdir($handle)) {
		if ( !ereg('[.]',$file) ) {
			include($admin_dir."/".$file."/xoops_version.php");
			if ( $modversion['hasAdmin'] ) {
				echo "<td align='center' valign='bottom' width='19%'>";
				xoopsforge_menu_item($file, $modversion);
				echo "</td>";
				$counter++;
			}
			if ( $counter > 4 ) {
				$counter = 0;
				echo "</tr>";
				echo "<tr>";
			}
		}
		unset($modversion);
	}
		
	echo "</tr>";
	echo "</table>";
}
?>