<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: survey_resp.php,v 1.3 2003/12/15 18:09:21 devsupaul Exp $
  *
  */

include_once ("../../../mainfile.php");

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");

if (!$xoopsUser) {
  redirect_header($GLOBALS["HTTP_REFERER"],4,_NOPERM);
  exit;
}

if (!$survey_id || !$group_id) {
  redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />For some reason, the Group ID or Survey ID did not make it to this page");
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

survey_header($group, _XF_SUR_SURVEYCOMPLETE);

echo _XF_SUR_THANKYOU."<p>";

/*
	Delete this customer's responses in case they had back-arrowed
*/

$result = $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_survey_responses")." "
                          ."WHERE survey_id='" . addslashes($survey_id) . "' "
													."AND group_id='" . addslashes($group_id) . "' "
													."AND user_id='".$xoopsUser->getVar("uid")."'");

/*
	Select this survey from the database
*/

$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_surveys")." "
      ."WHERE survey_id='$survey_id'";

$result = $xoopsDB->query($sql);

/*
	Select the questions for this survey
*/
$questions = unofficial_getDBResult($result, 0, "survey_questions");
$questions = str_replace(" ", "", $questions);
$quest_array = explode(',', $questions);

$count = count($quest_array);
$now = time();

for ($i=0; $i<$count; $i++) {

	/*
		Insert each form value into the responses table
	*/

	$val = "_" . $quest_array[$i];
	// for check box responses for yes no
	if ($$val=='') $$val = "5";
	if ($$val=='on') $$val = "1";

	$sql = "INSERT INTO ".$xoopsDB->prefix("xf_survey_responses")." (user_id,group_id,survey_id,question_id,response,date) "
	      ."VALUES ('".$xoopsUser->getVar("uid")."','" . addslashes($group_id) . "','" . addslashes($survey_id) . "','" . addslashes($quest_array[$i]) . "','". addslashes($$val) . "','$now')";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		echo "<h4>Error</h4>";
	}
}

survey_footer();
?>