<?php
/**
*
* SourceForge Survey Facility
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001(c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: survey.php,v 1.4 2003/12/15 18:09:21 devsupaul Exp $
*
*/
include_once("../../../mainfile.php");
 
$langfile = "survey.php";
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
 
if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
 
// Check to make sure they're logged in.
if (!$icmsUser)
{
	$icmsForgeErrorHandler->setSystemError(_NOPERM . "called from ".__FILE__." line ".__LINE__);
}
 
if (!$survey_id || !$group_id)
{
	$icmsForgeErrorHandler->setSystemError("Error: For some reason, the Group ID or". " Survey ID did not get passed the survey page");
}
else
{
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
	 
	survey_header($group, 'Survey');
	 
	echo "<table border='0'>";
	echo show_survey($group_id, $survey_id);
	echo "</table>";
	 
	survey_footer();
}
?>