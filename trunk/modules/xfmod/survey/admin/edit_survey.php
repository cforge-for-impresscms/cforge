<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: edit_survey.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
  *
  */
include_once ("../../../../mainfile.php");

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
$xoopsOption['template_main'] = 'survey/admin/xfmod_edit_survey.html';

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if(!$survey_id)
{
	$xoopsForgeErrorHandler->setSystemError(
		"Cannot edit a survey without a survey id");
}

if(!$perm->isAdmin())
{
	$xoopsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
}

if ($post_changes)
{
	$sql = "UPDATE ".$xoopsDB->prefix("xf_surveys")." "
	      ."SET survey_title='$survey_title',"
				."survey_questions='$survey_questions',"
				."is_active='$is_active' "
				."WHERE survey_id='$survey_id' "
				."AND group_id='$group_id'";
				
	$result = $xoopsDB->queryF($sql);
	
	if (!$result)
	{
		$xoopsForgeErrorHandler->addError("Survey Update Failed - ".
			$xoopsDB->error());
	}
	else
	{
		$msg = 'Survey "'.$survey_title.'" has been updated. ID='.$survey_id;
	
		redirect_header(XOOPS_URL."/modules/xfmod/survey/admin/".
			"browse_surveys.php?group_id=".$group_id.
			"&feedback=".urlencode($msg), 0, "");
	}
}

include (XOOPS_ROOT_PATH."/header.php");
$header = survey_header($group, _XF_SUR_EDITSURVEYS, 'is_admin_page');
$xoopsTpl->assign("survey_header",$header);

/*
	Get this survey out of the DB
*/
$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_surveys")." "
	  ."WHERE survey_id='$survey_id' "
			."AND group_id='$group_id'";
$result = $xoopsDB->query($sql);
$survey_title = unofficial_getDBResult($result, 0, "survey_title");
$survey_questions = unofficial_getDBResult($result, 0, "survey_questions");
$is_active = unofficial_getDBResult($result, 0, "is_active");

$xoopsTpl->assign("survey_id",$survey_id);
$xoopsTpl->assign("survey_name",_XF_SUR_NAMEOFSURVEY);
$xoopsTpl->assign("survey_title",$ts->makeTboxData4Show($survey_title));
$xoopsTpl->assign("group_id",$group_id);

//include(XOOPS_ROOT_PATH."modules/xfmod/survey/admin/quest_spec.php");

if($is_active=='1'){
	$checked = '<BR><INPUT TYPE="RADIO" NAME="is_active" VALUE="1" CHECKED> '._YES.'<BR><INPUT TYPE="RADIO" NAME="is_active" VALUE="0"> '._NO;
	$xoopsTpl->assign("checked", $checked);
}
else {
	$checked = '<BR><INPUT TYPE="RADIO" NAME="is_active" VALUE="1"> '._YES.'<BR><INPUT TYPE="RADIO" NAME="is_active" VALUE="0" CHECKED> '._NO;
	$xoopsTpl->assign("checked", $checked);
}


$xoopsTpl->assign("is_active",_XF_SUR_ISACTIVE);
$xoopsTpl->assign("submit",_XF_G_SUBMIT);

include (XOOPS_ROOT_PATH."/footer.php");
?>