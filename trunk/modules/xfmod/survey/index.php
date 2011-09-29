<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.6 2004/04/16 22:39:29 jcox Exp $
  *
  */
include_once ("../../../mainfile.php");

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
$xoopsOption['template_main'] = 'survey/xfmod_index.html';

if (!$group_id) {
	redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />No Group");
	exit;
}

$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );
//group is private
if (!$group->isPublic()) {
  //if it's a private group, you must be a member of that group
  if (!$group->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
	{
	  redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
	  exit;
	}
}

include (XOOPS_ROOT_PATH."/header.php");

$xoopsTpl->assign("survey_header",survey_header($group));

function  ShowResultsGroupSurveys($result) {
	global $group_id, $xoopsConfig, $xoopsDB, $xoopsTpl;
	$rows  =  $xoopsDB->getRowsNum($result);
	$cols  =  unofficial_getNumFields($result);

	$xoopsTpl->assign("survey_id",_XF_SUR_SURVEYID);
	$xoopsTpl->assign("survey_title",_XF_SUR_SURVEYTITLE);

	for($j=0; $j<$rows; $j++)  {

		$survey_list['survey_id'] = unofficial_getDBResult($result,$j,"survey_id");

		for ($i=1; $i<$cols; $i++)  {
			$survey_list['survey_misc'] .= "<TD>".unofficial_getDBResult($result,$j,$i)."</TD>\n";
		}

	}
	$xoopsTpl->assign("survey_list",$survey_list);
}

$sql = "SELECT survey_id,survey_title FROM ".$xoopsDB->prefix("xf_surveys")." WHERE group_id='$group_id' AND is_active='1'";

$result = $xoopsDB->query($sql);

if (!$result || $xoopsDB->getRowsNum($result) < 1) {
	$xoopsTpl->assign("no_active",_XF_SUR_NOACTIVESURVEYS);
} 
else {
	$xoopsTpl->assign("survey_results",ShowResultsGroupSurveys($result));
}

include (XOOPS_ROOT_PATH."/footer.php");

?>