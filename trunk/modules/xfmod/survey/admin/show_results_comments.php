<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: show_results_comments.php,v 1.5 2004/04/16 22:39:30 jcox Exp $
  *
  */
include_once ("../../../../mainfile.php");

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/HTML_Graphs.php");
$xoopsOption['template_main'] = 'survey/admin/xfmod_show_results_comments.html';

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if(!$perm->isAdmin())
{
	$xoopsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
}

include (XOOPS_ROOT_PATH."/header.php");
$header = survey_header($group, _XF_SUR_SURVEYAGGREGATERESULTS, 'is_admin_page');
$xoopsTpl->assign("survey_header",$header);
	
$sql = "SELECT question FROM ".$xoopsDB->prefix("xf_survey_questions")." WHERE question_id='$question_id'";
$result = $xoopsDB->query($sql);
$content .= "<h4>"._XF_SUR_QUESTION.": ".$ts->makeTboxData4Show(unofficial_getDBResult($result,0,"question"))."</H4>";
$content .= "<P>";

$sql = "SELECT DISTINCT response FROM ".$xoopsDB->prefix("xf_survey_responses")." WHERE survey_id='$survey_id' AND question_id='$question_id' AND group_id='$group_id'";
$result = $xoopsDB->query($sql);

$content .= ShowResultComments($result);


function ShowResultComments($result) {
	global $survey_id, $xoopsConfig, $xoopsDB;
	$rows  =  $xoopsDB->getRowsNum($result);
	$cols  =  unofficial_getNumFields($result);

	$comments .= "<h3>".sprintf(_XF_SUR_XFOUND, $rows)."</h3>";

        $comments .= "<table border='0' width='100%'><tr class='bg2'>";
	for ($i  =  0;  $i  <  $cols;  $i++)  {
		$comments .= "<td><b>".unofficial_getFieldName($result, $i)."</b></td>\n";
	}
	$comments .= "</tr>";

	for($j  =  0;  $j  <  $rows;  $j++)  {

		$comments .= "<tr class='".($j%2>0?'bg1':'bg3')."'>\n";
		for ($i = 0; $i < $cols; $i++)  {
			$comments .= "<td>".unofficial_getDBResult($result,$j,$i)."</td>\n";
		}

	$comments .= "</tr>";
	}
	$comments .= "</table><p/><a href='javascript:history.go(-1);'>[BACK]</a>";
	
	return $comments;
}

$xoopsTpl->assign("content",$content);
include (XOOPS_ROOT_PATH."/footer.php");

?>