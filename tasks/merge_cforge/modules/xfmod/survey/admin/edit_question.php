<?php
	/**
	*
	* SourceForge Survey Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: edit_question.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
	*
	*/
	include_once("../../../../mainfile.php");
	 
	$langfile = "survey.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	$icmsOption['template_main'] = 'survey/admin/xfmod_edit_question.html';
	 
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
	 
	$doingerror = false;
	 
	if ($post_changes)
	{
		$qtext = $ts->makeTboxData4Save($question);
		if (strlen($qtext) < 1)
		{
			$icmsForgeErrorHandler->addError("You must specify a question");
			$doingerror = true;
		}
		else
		{
			$sql = "UPDATE ".$icmsDB->prefix("xf_survey_questions")." " ."SET question='".$ts->makeTboxData4Save($question)."'," ."question_type='$question_type' " ."WHERE question_id='$question_id' " ."AND group_id='$group_id'";
			 
			$result = $icmsDB->queryF($sql);
			 
			if (!$result)
			{
				$icmsForgeErrorHandler->addError("Update failed - ". $icmsDB->error());
			}
			else
				{
				$updtxt = "Question $question_id updated";
				redirect_header(ICMS_URL."/modules/xfmod/survey/admin". "/show_questions.php?group_id=".$group_id."&updtxt=".$updtxt, 0, "");
			}
		}
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	$header = survey_header($group, _XF_SUR_EDITAQUESTIONS, 'is_admin_page');
	$icmsTpl->assign("survey_header", $header);
	 
	if (!$doingerror)
	{
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_survey_questions")." " ."WHERE question_id='$question_id' AND group_id='$group_id'";
		$result = $icmsDB->queryF($sql);
		 
		if ($result)
		{
			$question = unofficial_getDBResult($result, 0, "question");
			$question_type = unofficial_getDBResult($result, 0, "question_type");
		}
		else
		{
			$updtxt = "Could not load question  $question_id for editing";
			redirect_header(ICMS_URL."/modules/xfmod/survey/admin". "/show_questions.php?group_id=".$group_id."&updtxt=".$updtxt, 0, "");
		}
	}
	 
	$icmsTpl->assign("group_id", $group_id);
	$icmsTpl->assign("question_id", $question_id);
	 
	$icmsTpl->assign("sur_question", _XF_SUR_QUESTION);
	$icmsTpl->assign("question", $ts->makeTboxData4Edit($question));
	$icmsTpl->assign("sur_question_type", _XF_SUR_QUESTIONTYPE);
	 
	$sql = "SELECT * FROM ".$icmsDB->prefix("xf_survey_question_types");
	$result = $icmsDB->query($sql);
	 
	$icmsTpl->assign("html_build_select_box", html_build_select_box($result, 'question_type', $question_type, false));
	$icmsTpl->assign("submit", _XF_G_SUBMIT);
	 
	include(ICMS_ROOT_PATH."/footer.php");
	 
?>