<?php
	/**
	*
	* Project Admin page to edit project information(like description,
	* active facilities, etc.)
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: editgroupinfo.php,v 1.8 2003/12/10 20:06:22 jcox Exp $
	*
	*/
	 
	include_once("../../../../mainfile.php");
	 
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vars.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	$icmsOption['template_main'] = 'project/admin/xfmod_editgroupinfo.html';
	 
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
		define("_LOCAL_XF_PRJ_DESCGROUPNAME", _XF_COMM_DESCGROUPNAME);
		define("_LOCAL_XF_PRJ_USEPROJECTMAN", _XF_COMM_USECOMMMAN);
	}
	else
	{
		define("_LOCAL_XF_G_PROJECT", _XF_G_PROJECT);
		define("_LOCAL_XF_PRJ_DESCGROUPNAME", _XF_PRJ_DESCGROUPNAME);
		define("_LOCAL_XF_PRJ_USEPROJECTMAN", _XF_PRJ_USEPROJECTMAN);
	}
	 
	// If this was a submission, make updates
	include(ICMS_ROOT_PATH."/header.php");
	 
	if ($submit)
	{
		$res = $group->update(
		$icmsUser,
			$form_group_name,
			$form_homepage,
			$form_shortdesc,
			$use_mail,
			$use_survey,
			$use_forum,
			$use_faq,
			$use_pm,
			$use_pm_depend,
			$group->usesCVS(),
			$use_news,
			$use_docman,
			$use_sample,
			$use_tracker,
			$new_task_address,
			$send_all_tasks,
			100, //$logo_image_id
		$group->anonCVS()
		);
		 
		if (!res)
		{
			$icmsTpl->assign("feedback", $group->getErrorMessage());
		}
		else
		{
			$icmsTpl->assign("feedback", 'Group information updated');
		}
	}
	 
	$icmsTpl->assign("project_title", project_title($group));
	$icmsTpl->assign("project_tabs", project_tabs('admin', $group_id));
	$icmsTpl->assign("project_admin_header", get_project_admin_header($group_id, $perm, $group->isProject()));
	 
	$icmsTpl->assign("php_self", $_SERVER['PHP_SELF']);
	$icmsTpl->assign("group_id", $group->getID());
	$icmsTpl->assign("group_name_label", _LOCAL_XF_PRJ_DESCGROUPNAME);
	$icmsTpl->assign("group_name", $group->getPublicName());
	$icmsTpl->assign("group_desc_label", _XF_PRJ_SHORTDESCRIPTION255MAX);
	$icmsTpl->assign("group_desc", $ts->makeTareaData4Edit($group->getDescription()));
	 
	if ($group->isProject())
	{
		$icmsTpl->assign("isProject", true);
		$icmsTpl->assign("group_homepage_label", _XF_G_HOMEPAGE);
		$icmsTpl->assign("group_homepage", $group->getHomePage());
	}
	 
	$icmsTpl->assign("activefeatures_label", _XF_PRJ_ACTIVEFEATURES);
	 
	// This function is used to render checkboxes below
	function c($v)
	{
		if ($v)
		{
			return 'CHECKED';
		}
		else
		{
			return '';
		}
	}
	 
	/*
	Show the options that this project is using
	*/
	$use[] = array('use_mail', "Use Mailing Lists", c($group->usesMail()));
	$use[] = array('use_survey', _XF_PRJ_USESURVEYS, c($group->usesSurvey()));
	$use[] = array('use_forum', _XF_PRJ_USEFORUMS, c($group->usesForum()));
	if ($group->isProject())
	{
		$use[] = array('use_pm', _LOCAL_XF_PRJ_USEPROJECTMAN, c($group->usesPM()));
		$use[] = array('use_pm_depend', _XF_PRJ_USETASKDEP, c($group->usesPMDependencies()));
		//}else{
		$use[] = array('use_faq', "Use FAQs", c($group->usesFAQ()));
	}
	$use[] = array('use_news', _XF_PRJ_USENEWS, c($group->usesNews()));
	$use[] = array('use_docman', _XF_PRJ_USEDOCMGR, c($group->usesDocman()));
	$use[] = array('use_sample', _XF_PRJ_USESAMPLEMGR, c($group->usesSamples()));
	$use[] = array('use_tracker', _XF_PRJ_USETRACKER, c($group->usesTracker()));
	$icmsTpl->assign("use", $use);
	 
	$icmsTpl->assign("email_label", _XF_PRJ_WISHDEFAULTEMAILADDRESS);
	$icmsTpl->assign("email", $group->PMEmailAddress());
	$icmsTpl->assign("sendonallupdates_label", _XF_PRJ_SENDONALLUPDATES);
	$icmsTpl->assign("sendonallupdates", c($group->PMEmailAll()));
	$icmsTpl->assign("tasks_label", _XF_PRJ_NEWTASKASSIGNMENTS);
	$icmsTpl->assign("update_button_label", _XF_G_UPDATE);
	 
	include(ICMS_ROOT_PATH."/footer.php");
?>