<?php
	/**
	*
	* SourceForge Survey Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: add_survey.php,v 1.6 2004/04/16 22:39:30 jcox Exp $
	*
	*/
	include_once("../../../../mainfile.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
	//$survey_page = SURVEY_ADD_SURVEYS_PAGE;
	 
	$langfile = "survey.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	$icmsOption['template_main'] = 'survey/admin/xfmod_add_survey.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	/* http_track_vars */
	//$group_id = util_http_track_vars('group_id');
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isAdmin())
	{
		$icmsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
	}
	 
	//$post_changes = util_http_track_vars('post_changes');
	//$survey_title = util_http_track_vars('survey_title');
	//$survey_questions = util_http_track_vars('survey_questions');
	//$qra = util_http_track_vars('qra');
	 
	if ($post_changes)
	{
		$isvalid = true;
		if (strlen($survey_title) < 1)
		{
			$icmsForgeErrorHandler->addError("Name of survey is required");
			$isvalid = false;
		}
		 
		$survey_questions = trim($survey_questions);
		 
		if (strlen($survey_questions) < 1)
		{
			 var_dump($survey_questions);
			$icmsForgeErrorHandler->addError("Survey questions must be specified");
			$isvalid = false;
		}
		 
		if ($isvalid)
		{
			$sql = "INSERT INTO ".$icmsDB->prefix("xf_surveys"). "(survey_title,group_id,survey_questions) ". "VALUES('$survey_title','$group_id','$survey_questions')";
			 
			$result = $icmsDB->queryF($sql);
			if ($result)
			{
				$icmsForgeErrorHandler->addMessage('Survey "'.$survey_title.'" added');
				$survey_title = "";
				$survey_questions = "";
			}
			else
				{
				$icmsForgeErrorHandler->addError("Failed to add survey: ". $icmsDB->error());
			}
		}
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	$header = survey_header($group, _XF_SUR_ADDSURVEYS, 'is_admin_page');
	$icmsTpl->assign("survey_header", $header);
	 
	$icmsTpl->assign("survey_name", _XF_SUR_NAMEOFSURVEY);
	$icmsTpl->assign("survey_title", $survey_title);
	$icmsTpl->assign("group_id", $group_id);
	 
	//include(ICMS_ROOT_PATH."modules/xfmod/survey/admin/quest_spec.php");
	 
	$icmsTpl->assign("uparrowUrl", ICMS_URL."/modules/xfmod/images/uparrow.gif");
	$icmsTpl->assign("downarrowUrl", ICMS_URL."/modules/xfmod/images/downarrow.gif");
	$icmsTpl->assign("leftarrowUrl", ICMS_URL."/modules/xfmod/images/leftarrow.gif");
	$icmsTpl->assign("rightarrowUrl", ICMS_URL."/modules/xfmod/images/rightarrow.gif");
	 
	$content = '';
	 
	$sql = "SELECT * FROM ".$icmsDB->prefix("xf_survey_question_types");
	$qres = $icmsDB->queryF($sql);
	 
	if ($qres)
	{
		$qr = $icmsDB->getRowsNum($qres);
		 
		$content .= '<script language="JavaScript">';
		$content .= "var questionTypes = new Array();";
		 
		for($qi = 0; $qi < $qr; $qi++)
		{
			$qtid = unofficial_getDBResult($qres, $qi, "id");
			$qtype = unofficial_getDBResult($qres, $qi, "type");
			 
			$content .= 'questionTypes["'.$qtid.'"] = "'.$qtype.'";';
		}
		 
		$content .= "</script>";
	}
	 
	$content2 = '';
	if (strlen(trim($survey_questions)) > 0)
	{
		$qra = preg_split("/[\s,]+/", $survey_questions);
		 
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_survey_questions"). " WHERE question_id='".$qra[0]."'";
		 
		for($qi = 1; $qi < count($qra); $qi++)
		{
			$sql .= " OR question_id='".$qra[$qi]."'";
		}
		 
		$qres = $icmsDB->queryF($sql);
		 
		if ($qres)
		{
			$qr = $icmsDB->getRowsNum($qres);
			 
			for($qi = 0; $qi < $qr; $qi++)
			{
				$qtxt = unofficial_getDBResult($qres, $qi, "question");
				$quest_id = unofficial_getDBResult($qres, $qi, "question_id");
				$quest_type = unofficial_getDBResult($qres, $qi, "question_type");
				$questra[$qi] = $quest_id;
				$qval = $quest_id.",".$quest_type;
				 
				if (!$qi)
				{
					$content2 .= "<option selected value='$qval'>$qtxt</option>";
				}
				else
					{
					$content2 .= "<option value='$qval'>$qtxt</option>";
				}
			}
		}
	}
	 
	$sql = "SELECT * FROM ".$icmsDB->prefix("xf_survey_questions"). " WHERE group_id='".$group_id."'";
	 
	if (count($qra) > 0)
	{
		for($qi = 0; $qi < count($qra); $qi++)
		{
			$sql .= " AND question_id!='".$qra[$qi]."'";
		}
	}
	 
	$content3 = '';
	 
	$qres = $icmsDB->queryF($sql);
	if ($qres)
	{
		$qr = $icmsDB->getRowsNum($qres);
		for($qi = 0; $qi < $qr; $qi++)
		{
			$qval = unofficial_getDBResult($qres, $qi, "question_id");
			$qval .= ",".unofficial_getDBResult($qres, $qi, "question_type");
			$qtxt = unofficial_getDBResult($qres, $qi, "question");
			if (!$qi)
			{
				$content3 .= "<option selected value='$qval'>$qtxt</option>";
			}
			else
				{
				$content3 .= "<option value='$qval'>$qtxt</option>";
			}
		}
	}
	 
	//$icmsForgeErrorHandler->displayFeedback();
	$icmsTpl->assign("feedback", 'feedback');
	$icmsTpl->assign("content", $content);
	$icmsTpl->assign("survey_questions", $content2);
	$icmsTpl->assign("available_questions", $content3);
	 
	$icmsTpl->assign("is_active", _XF_SUR_ISACTIVE);
	$icmsTpl->assign("YES", _YES);
	$icmsTpl->assign("NO", _NO);
	$icmsTpl->assign("add_survey", _XF_SUR_ADDTHISSURVEY);
	 
	function ShowResultsEditSurvey($result)
	{
		global $group_id, $icmsDB;
		$rows = $icmsDB->getRowsNum($result);
		$cols = unofficial_getNumFields($result);
		 
		$content = "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";
		 
		$content .= "<table border='0' width='100%'><tr class='bg2'>";
		for($i = 0; $i < $cols; $i++)
		{
			$content .= "<td><strong>".unofficial_getFieldName($result, $i)."</strong></td>";
		}
		$content .= "</tr>";
		 
		for($j = 0; $j < $rows; $j++)
		{
			 
			$content .= "<th class='".($j%2 > 0?'bg1':'bg3')."'>\n";
			$content .= "<td><a href='edit_survey.php?group_id=$group_id&survey_id=".unofficial_getDBResult($result, $j, 0)."'>".unofficial_getDBResult($result, $j, 0)."</a></td>";
			for($i = 1; $i < $cols; $i++)
			{
				$content .= "<td>".unofficial_getDBResult($result, $j, $i)."</td>";
			}
			 
			$content .= "</tr>";
		}
		$content .= "</table>";
		 
		return $content;
	}
	 
	/*
	Select this survey from the database
	*/
	 
	$sql = "SELECT survey_id,survey_title,survey_questions,is_active  FROM ".$icmsDB->prefix("xf_surveys")." WHERE group_id='$group_id'";
	 
	$result = $icmsDB->query($sql);
	 
	$icmsTpl->assign("existing_surveys", _XF_SUR_EXISTINGSURVEYS);
	$icmsTpl->assign("survey_content", ShowResultsEditSurvey($result));
	 
	include(ICMS_ROOT_PATH."/footer.php");
?>