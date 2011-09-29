<?php
	/**
	*
	* Project Admin page to show audit trail for group
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: history.php,v 1.6 2003/12/09 15:04:00 devsupaul Exp $
	*
	*/
	include_once("../../../../mainfile.php");
	 
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	$icmsOption['template_main'] = 'project/admin/xfmod_history.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isAdmin())
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
		exit();
	}
	 
	if ($group->isFoundry())
	{
		define("_LOCAL_XF_G_PROJECT", _XF_G_COMM);
		define("_LOCAL_XF_PRJ_PROJECTHISTORY", _XF_COMM_COMMHISTORY);
		define("_LOCAL_XF_PRJ_LOGWILLSHOWCHANGES", _XF_COMM_LOGWILLSHOWCHANGES);
	}
	else
	{
		define("_LOCAL_XF_G_PROJECT", _XF_G_PROJECT);
		define("_LOCAL_XF_PRJ_PROJECTHISTORY", _XF_PRJ_PROJECTHISTORY);
		define("_LOCAL_XF_PRJ_LOGWILLSHOWCHANGES", _XF_PRJ_LOGWILLSHOWCHANGES);
	}
	 
	//include("../../../../header.php");
	//OpenTable();
	 
	//echo project_title($group);
	//echo "<B style='font-size:16px;align:left;'>"._LOCAL_XF_PRJ_PROJECTHISTORY."</strong><br />";
	//echo project_tabs('admin', $group_id);
	 
	$metaTitle = ": "._XF_PRJ_ADMIN." - ".$group->getPublicName();
	 
	include("../../../../header.php");
	 
	$icmsTpl->assign("project_title", project_title($group));
	$icmsTpl->assign("project_tabs", project_tabs('admin', $group_id));
	$icmsTpl->assign("project_admin_header", get_project_admin_header($group_id, $perm, $group->isProject()));
	 
	$content = "<p>"._LOCAL_XF_PRJ_LOGWILLSHOWCHANGES."<p>";
	$content .= show_grouphistory($group_id, $group->isProject());
	 
	$icmsTpl->assign("content", $content);
	 
	//CloseTable();
	include("../../../../footer.php");
?>