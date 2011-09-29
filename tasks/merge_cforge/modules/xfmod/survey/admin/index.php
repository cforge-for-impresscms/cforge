<?php
	/**
	*
	* SourceForge Survey Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
	*
	*/
	 
	include_once("../../../../mainfile.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
	$survey_page = SURVEY_ADMIN_PAGE;
	 
	$langfile = "survey.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	$icmsOption['template_main'] = 'survey/admin/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isAdmin())
	{
		$icmsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	 
	$header = survey_header($group, "Survey Administration", 'is_admin_page');
	$icmsTpl->assign("survey_header", $header);
	 
	$icmsTpl->assign("xoopsurl", ICMS_URL);
	$icmsTpl->assign("sur_info1", _XF_SUR_INFO1);
	$icmsTpl->assign("sur_info2", _XF_SUR_INFO2);
	$icmsTpl->assign("sur_info3", _XF_SUR_INFO3);
	$icmsTpl->assign("sur_info4", _XF_SUR_INFO4);
	$icmsTpl->assign("sur_info5", _XF_SUR_INFO5);
	$icmsTpl->assign("sur_info6", _XF_SUR_INFO6);
	$icmsTpl->assign("group_id", $group_id);
	$icmsTpl->assign("sur_edit", _XF_SUR_EDITSURVEYS);
	 
	include(ICMS_ROOT_PATH."/footer.php");
	 
?>