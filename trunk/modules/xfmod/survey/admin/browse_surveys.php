<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: browse_surveys.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
  *
  */
include_once ("../../../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
//$survey_page = SURVEY_BROWSE_SURVEYS_PAGE;

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
$xoopsOption['template_main'] = 'survey/admin/xfmod_browse_surveys.html';

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if(!$perm->isAdmin())
{
	$xoopsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
}

if(isset($feedback) && $feedback)
{
	$feedback = str_replace('\\"', '"', $feedback);
	$xoopsForgeErrorHandler->addMessage(urldecode($feedback));
}

include (XOOPS_ROOT_PATH."/header.php");
$header = survey_header($group, _XF_SUR_EDITSURVEYS, 'is_admin_page');
$xoopsTpl->assign("survey_header",$header);

function  ShowResultsEditSurvey($result)
{
	global $group_id, $xoopsDB, $_SERVER['PHP_SELF'];
	$rows  =  $xoopsDB->getRowsNum($result);
	$cols  =  unofficial_getNumFields($result);

	$content = "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";

	$content .= "<table border='0' width='100%'><tr class='bg2'>";
	for ($i  =  0;  $i  <  $cols;  $i++)
	{
		$content .="<TD><b>".unofficial_getFieldName($result, $i)."</b></td>";
	}
	$content .= "</tr>";

	for($j  =  0;  $j  <  $rows;  $j++)
	{
		$content .= "<tr class='".($j%2>0?'bg1':'bg3')."'>";

		$content .= "<td><a href='".
			XOOPS_URL."/modules/xfmod/survey/admin/edit_survey.php".
			"?group_id=$group_id&survey_id=".
			unofficial_getDBResult($result,$j,0)."'>".
			unofficial_getDBResult($result,$j,0)."</a></td>";

		for ($i = 1; $i < $cols; $i++)
		{
			$content .= "<td>".unofficial_getDBResult($result,$j,$i)."</td>";
		}

		$content .= "</tr>";
	}
	$content .= "</table>";

	return $content;
}

/*
	Select all surveys from the database
*/

$sql = "SELECT survey_id,survey_title,survey_questions,is_active FROM ".
	$xoopsDB->prefix("xf_surveys")." WHERE group_id='$group_id'";

$result = $xoopsDB->query($sql);

$xoopsTpl->assign("existing_surveys",_XF_SUR_EXISTINGSURVEYS);
$xoopsTpl->assign("survey_content",ShowResultsEditSurvey($result));

include (XOOPS_ROOT_PATH."/footer.php");
?>