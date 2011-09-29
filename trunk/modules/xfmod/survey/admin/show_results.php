<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: show_results.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
  *
  */
include_once ("../../../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
$survey_page = SURVEY_SHOW_RESULTS_PAGE;

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
$xoopsOption['template_main'] = 'survey/admin/xfmod_show_results.html';

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if(!$perm->isAdmin())
{
	$xoopsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
}

include (XOOPS_ROOT_PATH."/header.php");
$header = survey_header($group, _XF_SUR_SURVEYRESULTS, 'is_admin_page');
$xoopsTpl->assign("survey_header",$header);

function ShowResultsSurvey($result) {
	global $group_id, $xoopsConfig, $xoopsDB;
	$rows  =  $xoopsDB->getRowsNum($result);
	$cols  =  unofficial_getNumFields($result);

	$content = "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";

	$content .= "<table border='0' width='100%'><tr class='bg2'>";
	for ($i  =  0;  $i  <  $cols;  $i++)  {
		$content .= "<TD><b>".unofficial_getFieldName($result, $i)."</b></td>\n";
	}
	$content .= "</tr>";

	for($j  =  0;  $j  <  $rows;  $j++)  {

		$content .= "<TR class='".($j%2>0?'bg1':'bg3')."'>\n";
		$content .= "<TD><A HREF='$_SERVER['PHP_SELF']?group_id=$group_id&survey_id=".unofficial_getDBResult($result,$j,"survey_id")."'>".unofficial_getDBResult($result,$j,"survey_id")."</A></TD>";
		for ($i = 0; $i < $cols; $i++)  {
			$content .= "<TD>".unofficial_getDBResult($result,$j,$i)."</TD>\n";
		}

		$content .= "</tr>";
	}
	$content .= "</table>";
	return $content;
}

function  ShowResultsAggregate($result) {
	global $group_id, $xoopsConfig, $xoopsDB;
	$rows  =  $xoopsDB->getRowsNum($result);
	$cols  =  unofficial_getNumFields($result);

	$content = "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";

	$content .="<table border='0' width='100%'><tr class='bg2'>";
	for ($i  =  0;  $i  <  $cols;  $i++)  {
		$content .="<TD><b>".unofficial_getFieldName($result, $i)."</b></td>\n";
	}
	$content .= "</tr>";

	for($j  =  0;  $j  <  $rows;  $j++)  {

		$content .= "<TR class='".($j%2>0?'bg1':'bg3')."'>\n";
		$content .= "<TD><A HREF='show_results_aggregate.php?group_id=$group_id&survey_id=".unofficial_getDBResult($result,$j,"survey_id")."'>".unofficial_getDBResult($result,$j,"survey_id")."</A></TD>";
		for ($i = 1; $i < $cols; $i++)  {
			$content .="<TD>".unofficial_getDBResult($result,$j,$i)."</TD>\n";
		}

		$content .= "</tr>";
	}
	$content .= "</table>";
	return $content;
}

function  ShowResultsCustomer($result) {
	global $group_id, $xoopsConfig, $xoopsDB;
	$rows  =  $xoopsDB->getRowsNum($result);
	$cols  =  unofficial_getNumFields($result);

	$content .= "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";

        $content .= "<table border='0' width='100%'><tr class='bg2'>";
	for ($i  =  0;  $i  <  $cols;  $i++)  {
		$content .= "<TD><b>".unofficial_getFieldName($result, $i)."</b></td>\n";
	}
	$content .= "</tr>";

	for($j  =  0;  $j  <  $rows;  $j++)  {

		$content .= "<TR class='".($j%2>0?'bg1':'bg3')."'>\n";
		$content .= "<TD><A HREF='show_results_individual.php?group_id=$group_id&survey_id=$survey_id&customer_id=".unofficial_getDBResult($result,$j,"cust_id")."'>".unofficial_getDBResult($result,$j,"cust_id")."</A></TD>";
		for ($i = 1; $i < $cols; $i++)  {
			$content .= "<TD>".unofficial_getDBResult($result,$j,$i)."</TD>\n";
		}

		$content .= "</tr>";
	}
	$content .= "</table>";
	return $content;
}



if (!$survey_id) {

	/*
		Select a list of surveys, so they can click in and view a particular set of responses
	*/

	$sql = "SELECT survey_id,survey_title FROM ".$xoopsDB->prefix("xf_surveys")." WHERE group_id='$group_id'";

	$result = $xoopsDB->query($sql);

//	echo "\n<h2>View Individual Responses</h2>\n\n";
//	ShowResultsSurvey($result);

	$xoopsTpl->assign("view_aggregate",_XF_SUR_VIEWAGGREGATERESPONSES);
	$xoopsTpl->assign("show_aggregate",ShowResultsAggregate($result));

}
//else {
//	$xoopsTpl->assign("show_results",ShowResultsSurvey($result));
//}

include (XOOPS_ROOT_PATH."/footer.php");

?>