<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: add_question.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
  *
  */
include_once ("../../../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
//$survey_page = SURVEY_ADD_QUESTIONS_PAGE;

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
$xoopsOption['template_main'] = 'survey/admin/xfmod_add_question.html';

/* http_track_vars */
$group_id = util_http_track_vars('group_id');

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if(!$perm->isAdmin())
{
	$xoopsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
}

$post_changes = util_http_track_vars('post_changes');

if ($post_changes)
{
//	echo "question_type = $question_type<br/>";

	$quest = $ts->makeTboxData4Save($question);
	if(strlen($quest) < 1)
	{
		$xoopsForgeErrorHandler->addError("You must specify a question");
	}
	else
	{
		$sql = "INSERT INTO ".$xoopsDB->prefix("xf_survey_questions").
			" (group_id,question,question_type) VALUES ('$group_id',".
			"'$quest','$question_type')";

		$result = $xoopsDB->queryF($sql);
		if ($result)
		{
			$xoopsForgeErrorHandler->addMessage(_XF_SUR_QUESTIONADDED);
		}
		else
		{
			$xoopsForgeErrorHandler->addError("Failed adding question - ".
				$xoopsDB->error());
		}
	}
}

include (XOOPS_ROOT_PATH."/header.php");
$header = survey_header($group, _XF_SUR_ADDQUESTIONS, 'is_admin_page');
$xoopsTpl->assign("survey_header",$header);

$xoopsTpl->assign("group_id",$group_id);

$xoopsTpl->assign("question",_XF_SUR_QUESTION);
$xoopsTpl->assign("question_type",_XF_SUR_QUESTIONTYPE);

$sql="SELECT * FROM ".$xoopsDB->prefix("xf_survey_question_types");
$result = $xoopsDB->query($sql);
$select_box = html_build_select_box($result,'question_type','xzxz',false);

$xoopsTpl->assign("question_type_select",$select_box);
$xoopsTpl->assign("add_question",_XF_SUR_ADDTHISQUESTION);

include (XOOPS_ROOT_PATH."/footer.php");
?>
