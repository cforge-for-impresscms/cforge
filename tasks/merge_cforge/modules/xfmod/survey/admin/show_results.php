<?php
	/**
	*
	* SourceForge Survey Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: show_results.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
	*
	*/
	include_once("../../../../mainfile.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
	$survey_page = SURVEY_SHOW_RESULTS_PAGE;
	 
	$langfile = "survey.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	$icmsOption['template_main'] = 'survey/admin/xfmod_show_results.html';
	 
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
	$header = survey_header($group, _XF_SUR_SURVEYRESULTS, 'is_admin_page');
	$icmsTpl->assign("survey_header", $header);
	 
	function ShowResultsSurvey($result)
	{
		global $group_id, $icmsConfig, $icmsDB;
		$rows = $icmsDB->getRowsNum($result);
		$cols = unofficial_getNumFields($result);
		 
		$content = "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";
		 
		$content .= "<table border='0' width='100%'><tr class='bg2'>";
		for($i = 0; $i < $cols; $i++)
		{
			$content .= "<td><strong>".unofficial_getFieldName($result, $i)."</strong></td>\n";
		}
		$content .= "</tr>";
		 
		for($j = 0; $j < $rows; $j++)
		{
			 
			$content .= "<th class='".($j%2 > 0?'bg1':'bg3')."'>\n";
			$content .= "<td><a href='$_SERVER['PHP_SELF']?group_id=$group_id&survey_id=".unofficial_getDBResult($result, $j, "survey_id")."'>".unofficial_getDBResult($result, $j, "survey_id")."</a></td>";
			for($i = 0; $i < $cols; $i++)
			{
				$content .= "<td>".unofficial_getDBResult($result, $j, $i)."</td>\n";
			}
			 
			$content .= "</tr>";
		}
		$content .= "</table>";
		return $content;
	}
	 
	function ShowResultsAggregate($result)
	{
		global $group_id, $icmsConfig, $icmsDB;
		$rows = $icmsDB->getRowsNum($result);
		$cols = unofficial_getNumFields($result);
		 
		$content = "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";
		 
		$content .= "<table border='0' width='100%'><tr class='bg2'>";
		for($i = 0; $i < $cols; $i++)
		{
			$content .= "<td><strong>".unofficial_getFieldName($result, $i)."</strong></td>\n";
		}
		$content .= "</tr>";
		 
		for($j = 0; $j < $rows; $j++)
		{
			 
			$content .= "<th class='".($j%2 > 0?'bg1':'bg3')."'>\n";
			$content .= "<td><a href='show_results_aggregate.php?group_id=$group_id&survey_id=".unofficial_getDBResult($result, $j, "survey_id")."'>".unofficial_getDBResult($result, $j, "survey_id")."</a></td>";
			for($i = 1; $i < $cols; $i++)
			{
				$content .= "<td>".unofficial_getDBResult($result, $j, $i)."</td>\n";
			}
			 
			$content .= "</tr>";
		}
		$content .= "</table>";
		return $content;
	}
	 
	function ShowResultsCustomer($result)
	{
		global $group_id, $icmsConfig, $icmsDB;
		$rows = $icmsDB->getRowsNum($result);
		$cols = unofficial_getNumFields($result);
		 
		$content .= "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";
		 
		$content .= "<table border='0' width='100%'><tr class='bg2'>";
		for($i = 0; $i < $cols; $i++)
		{
			$content .= "<td><strong>".unofficial_getFieldName($result, $i)."</strong></td>\n";
		}
		$content .= "</tr>";
		 
		for($j = 0; $j < $rows; $j++)
		{
			 
			$content .= "<th class='".($j%2 > 0?'bg1':'bg3')."'>\n";
			$content .= "<td><a href='show_results_individual.php?group_id=$group_id&survey_id=$survey_id&customer_id=".unofficial_getDBResult($result, $j, "cust_id")."'>".unofficial_getDBResult($result, $j, "cust_id")."</a></td>";
			for($i = 1; $i < $cols; $i++)
			{
				$content .= "<td>".unofficial_getDBResult($result, $j, $i)."</td>\n";
			}
			 
			$content .= "</tr>";
		}
		$content .= "</table>";
		return $content;
	}
	 
	 
	 
	if (!$survey_id)
	{
		 
		/*
		Select a list of surveys, so they can click in and view a particular set of responses
		*/
		 
		$sql = "SELECT survey_id,survey_title FROM ".$icmsDB->prefix("xf_surveys")." WHERE group_id='$group_id'";
		 
		$result = $icmsDB->query($sql);
		 
		// echo "\r\n<h2>View Individual Responses</h2>\n\n";
		// ShowResultsSurvey($result);
		 
		$icmsTpl->assign("view_aggregate", _XF_SUR_VIEWAGGREGATERESPONSES);
		$icmsTpl->assign("show_aggregate", ShowResultsAggregate($result));
		 
	}
	//else {
	// $icmsTpl->assign("show_results",ShowResultsSurvey($result));
	//}
	 
	include(ICMS_ROOT_PATH."/footer.php");
	 
?>