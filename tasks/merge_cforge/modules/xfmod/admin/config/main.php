<?php
	 
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	 
	include_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	include_once("admin/admin_utils.php");
	include_once("admin/config/config.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	//$op = util_http_track_vars('op');
	switch($op)
	{
		case "save":
		save_pref();
		break;
		 
		case "default":
		default:
		site_admin_header();
		show_pref();
		echo "<br />";
		site_admin_footer();
		break;
	}
?>