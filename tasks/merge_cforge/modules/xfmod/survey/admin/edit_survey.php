<?php
	/**
	*
	* SourceForge Survey Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: edit_survey.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
	*
	*/
	include_once("../../../../mainfile.php");
	 
	$langfile = "survey.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	$icmsOption['template_main'] = 'survey/admin/xfmod_edit_survey.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$survey_id)
	{
		$icmsForgeErrorHandler->setSystemError(
		"Cannot edit a survey without a survey id");
	}
	 
	if (!$perm->isAdmin())
	{
		$icmsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
	}
	 
	if ($post_changes)
	{
		$sql = "UPDATE ".$icmsDB->prefix("xf_surveys")." " ."SET survey_title='$survey_title'," ."survey_questions='$survey_questions'," ."is_active='$is_active' " ."WHERE survey_id='$survey_id' " ."AND group_id='$group_id'";
		 
		$result = $icmsDB->queryF($sql);
		 
		if (!$result)
		{
			$icmsForgeErrorHandler->addError("Survey Update Failed - ". $icmsDB->error());
		}
		else
		{
			$msg = 'Survey "'.$survey_title.'" has been updated. ID='.$survey_id;
			 
			redirect_header(ICMS_URL."/modules/xfmod/survey/admin/". "browse_surveys.php?group_id=".$group_id. "&feedback=".urlencode($msg), 0, "");
		}
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	$header = survey_header($group, _XF_SUR_EDITSURVEYS, 'is_admin_page');
	$icmsTpl->assign("survey_header", $header);
	 
	/*
	Get this survey out of the DB
	*/
	$sql = "SELECT * FROM ".$icmsDB->prefix("xf_surveys")." " ."WHERE survey_id='$survey_id' " ."AND group_id='$group_id'";
	$result = $icmsDB->query($sql);
	$survey_title = unofficial_getDBResult($result, 0, "survey_title");
	$survey_questions = unofficial_getDBResult($result, 0, "survey_questions");
	$is_active = unofficial_getDBResult($result, 0, "is_active");
	 
	$icmsTpl->assign("survey_id", $survey_id);
	$icmsTpl->assign("survey_name", _XF_SUR_NAMEOFSURVEY);
	$icmsTpl->assign("survey_title", $ts->makeTboxData4Show($survey_title));
	$icmsTpl->assign("group_id", $group_id);
	 
	//include(ICMS_ROOT_PATH."modules/xfmod/survey/admin/quest_spec.php");
	 
	if ($is_active == '1')
	{
		$checked = '<BR><input type="radio" name="is_active" value="1" CHECKED> '._YES.'<BR><input type="radio" name="is_active" value="0"> '._NO;
		$icmsTpl->assign("checked", $checked);
	}
	else
	{
		$checked = '<BR><input type="radio" name="is_active" value="1"> '._YES.'<BR><input type="radio" name="is_active" value="0" CHECKED> '._NO;
		$icmsTpl->assign("checked", $checked);
	}
	 
	 
	$icmsTpl->assign("is_active", _XF_SUR_ISACTIVE);
	$icmsTpl->assign("submit", _XF_G_SUBMIT);
	 
	include(ICMS_ROOT_PATH."/footer.php");
?>