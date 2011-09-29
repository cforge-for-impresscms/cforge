<?php
	/**
	*
	* SourceForge Survey Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: browse_surveys.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
	*
	*/
	include_once("../../../../mainfile.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
	//$survey_page = SURVEY_BROWSE_SURVEYS_PAGE;
	 
	$langfile = "survey.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	$icmsOption['template_main'] = 'survey/admin/xfmod_browse_surveys.html';
	 
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
	 
	if (isset($feedback) && $feedback)
	{
		$feedback = str_replace('\\"', '"', $feedback);
		$icmsForgeErrorHandler->addMessage(urldecode($feedback));
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	$header = survey_header($group, _XF_SUR_EDITSURVEYS, 'is_admin_page');
	$icmsTpl->assign("survey_header", $header);
	 
	function ShowResultsEditSurvey($result)
	{
		global $group_id, $icmsDB, $_SERVER['PHP_SELF'];
		$rows = $icmsDB->getRowsNum($result);
		$cols = unofficial_getNumFields($result);
		 
		$content = "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";
		 
		$content .= "<table border='0' width='100%'><tr class='bg2'>";
		for($i = 0; $i < $cols; $i++)
		{
			$content .= "<td><strong>".unofficial_getFieldName($result, $i)."</strong></td>";
		}
		$content .= "</tr>";
		 
		for($j = 0; $j < $rows; $j++)
		{
			$content .= "<tr class='".($j%2 > 0?'bg1':'bg3')."'>";
			 
			$content .= "<td><a href='". ICMS_URL."/modules/xfmod/survey/admin/edit_survey.php". "?group_id=$group_id&survey_id=". unofficial_getDBResult($result, $j, 0)."'>". unofficial_getDBResult($result, $j, 0)."</a></td>";
			 
			for($i = 1; $i < $cols; $i++)
			{
				$content .= "<td>".unofficial_getDBResult($result, $j, $i)."</td>";
			}
			 
			$content .= "</tr>";
		}
		$content .= "</table>";
		 
		return $content;
	}
	 
	/*
	Select all surveys from the database
	*/
	 
	$sql = "SELECT survey_id,survey_title,survey_questions,is_active FROM ". $icmsDB->prefix("xf_surveys")." WHERE group_id='$group_id'";
	 
	$result = $icmsDB->query($sql);
	 
	$icmsTpl->assign("existing_surveys", _XF_SUR_EXISTINGSURVEYS);
	$icmsTpl->assign("survey_content", ShowResultsEditSurvey($result));
	 
	include(ICMS_ROOT_PATH."/footer.php");
?>