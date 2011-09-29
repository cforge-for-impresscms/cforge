<?php
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	 
	include_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/include/trove.php");
	include_once("admin/admin_utils.php");
	include_once("admin/trove/trove.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	//$op = util_http_track_vars('op');
	 
	// trove parametes
	//$shortname = util_http_track_vars('shortname');
	//$fullname = util_http_track_vars('fullname');
	//$description = util_http_track_vars('description');
	//$parent = util_http_track_vars('parent');
	//$trove_cat_id = util_http_track_vars('trove_cat_id');
	 
	switch($op)
	{
		case "TroveAdd":
		TroveAdd();
		break;
		 
		case "TroveInsert":
		 
		TroveInsert($shortname, $fullname, $description, $parent);
		break;
		 
		case "TroveEdit":
		TroveEdit($trove_cat_id);
		break;
		 
		case "TroveSave":
		TroveSave($trove_cat_id, $shortname, $fullname, $description, $parent);
		break;
		 
		case "TroveList":
		TroveList();
		break;
		 
		default:
		TroveList();
		break;
	}
	 
?>