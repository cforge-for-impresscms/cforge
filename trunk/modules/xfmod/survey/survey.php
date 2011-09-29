<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: survey.php,v 1.4 2003/12/15 18:09:21 devsupaul Exp $
  *
  */
include_once ("../../../mainfile.php");

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");

// Check to make sure they're logged in.
if (!$xoopsUser)
{
	$xoopsForgeErrorHandler->setSystemError(_NOPERM);
}

if (!$survey_id || !$group_id){
	$xoopsForgeErrorHandler->setSystemError("Error: For some reason, the Group ID or".
	" Survey ID did not get passed the survey page");
}else{
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
	
	survey_header($group, 'Survey');
	
	echo "<table border='0'>";
	echo show_survey($group_id,$survey_id);
	echo "</table>";
 
	survey_footer();
}
?>