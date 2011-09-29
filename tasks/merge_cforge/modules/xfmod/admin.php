<?php
include("../../mainfile.php");
include(ICMS_ROOT_PATH."/include/cp_functions.php");
if (file_exists("language/".$icmsConfig['language']."/admin.php"))
{
	include("language/".$icmsConfig['language']."/admin.php");
}
else
{
	include("language/english/admin.php");
}
 
ob_end_flush();
 
if (isset($_POST['fct']))
{
	$fct = trim($_POST['fct']);
}
if (isset($_GET['fct']))
{
	$fct = trim($_GET['fct']);
}
 
 
 
include_once(ICMS_ROOT_PATH."/class/icmsModule.php");
/*********************************************************/
/* Admin Authentication                                  */
/*********************************************************/
$admintest = 0;
 
if ($icmsUser)
{
	$icmsModule = icmsModule::getByDirname("xfmod");
	if (!$icmsUser->isAdmin($icmsModule->mid()))
	{
		redirect_header(ICMS_URL."/", 3, _NOPERM . "called from ".__FILE__." line ".__LINE__);
		exit();
	}
	$admintest = 1;
	 
}
else
{
	redirect_header(ICMS_URL."/", 3, _NOPERM . "called from ".__FILE__." line ".__LINE__);
	exit();
}
if ($admintest == 1)
{
	if ($fct != "")
	{
		if (file_exists("admin/".$fct."/main.php"))
		{
			 
			if (file_exists("language/".$icmsConfig['language']."/admin/".$fct.".php"))
			{
				include("language/".$icmsConfig['language']."/admin/".$fct.".php");
			}
			elseif(file_exists("language/english/admin/".$fct.".php"))
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
 
function xoopsforge_menu_item($folder, $modversion)
{
	global $icmsConfig;
	 
	echo "<a href='".ICMS_URL."/modules/xfmod/admin.php?fct=".$folder."'><strong>" .trim($modversion['name'])."</strong></a>\n";
}
 
function xoopsforge_menu()
{
	global $icmsConfig, $icmsUser, $icmsModule;
	 
	echo "<table border='0' cellpadding='1' align='center'>";
	echo "<tr>";
	$i = 0;
	$admin_dir = ICMS_ROOT_PATH."/modules/xfmod/admin";
	$handle = opendir($admin_dir);
	$counter = 0;
	while ($file = readdir($handle))
	{
		if (!ereg('[.]', $file))
		{
			include($admin_dir."/".$file."/xoops_version.php");
			if ($modversion['hasAdmin'])
			{
				echo "<td align='center' valign='bottom' width='19%'>";
				xoopsforge_menu_item($file, $modversion);
				echo "</td>";
				$counter++;
			}
			if ($counter > 4)
			{
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