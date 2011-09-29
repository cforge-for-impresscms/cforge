<?php
	/**
	*
	* SourceForge Survey Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.6 2004/04/16 22:39:29 jcox Exp $
	*
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "survey.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	$icmsOption['template_main'] = 'survey/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if (!$group_id)
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "Error<br />No Group");
		exit;
	}
	 
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	//group is private
	if (!$group->isPublic())
	{
		//if it's a private group, you must be a member of that group
		if (!$group->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
		{
			redirect_header(ICMS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
			exit;
		}
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	 
	$icmsTpl->assign("survey_header", survey_header($group));
	 
	function ShowResultsGroupSurveys($result)
	{
		global $group_id, $icmsConfig, $icmsDB, $icmsTpl;
		$rows = $icmsDB->getRowsNum($result);
		$cols = unofficial_getNumFields($result);
		 
		$icmsTpl->assign("survey_id", _XF_SUR_SURVEYID);
		$icmsTpl->assign("survey_title", _XF_SUR_SURVEYTITLE);
		 
		for($j = 0; $j < $rows; $j++)
		{
			 
			$survey_list['survey_id'] = unofficial_getDBResult($result, $j, "survey_id");
			 
			for($i = 1; $i < $cols; $i++)
			{
				$survey_list['survey_misc'] .= "<td>".unofficial_getDBResult($result, $j, $i)."</td>\n";
			}
			 
		}
		$icmsTpl->assign("survey_list", $survey_list);
	}
	 
	$sql = "SELECT survey_id,survey_title FROM ".$icmsDB->prefix("xf_surveys")." WHERE group_id='$group_id' AND is_active='1'";
	 
	$result = $icmsDB->query($sql);
	 
	if (!$result || $icmsDB->getRowsNum($result) < 1)
	{
		$icmsTpl->assign("no_active", _XF_SUR_NOACTIVESURVEYS);
	}
	else
	{
		$icmsTpl->assign("survey_results", ShowResultsGroupSurveys($result));
	}
	 
	include(ICMS_ROOT_PATH."/footer.php");
	 
?>