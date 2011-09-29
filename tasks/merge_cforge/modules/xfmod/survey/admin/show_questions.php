<?php
	/**
	*
	* SourceForge Survey Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: show_questions.php,v 1.5 2004/01/14 23:04:26 devsupaul Exp $
	*
	*/
	include_once("../../../../mainfile.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
	//$survey_page = SURVEY_EDIT_QUESTIONS_PAGE;
	 
	$langfile = "survey.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	$group = group_get_object($group_id);
	project_check_access($group_id);
	 
	 
	if ($popup != 1)
	{
		$icmsOption['template_main'] = 'survey/admin/xfmod_show_questions.html';
		include(ICMS_ROOT_PATH."/header.php");
		 
		// get current information
		$perm = $group->getPermission($icmsUser);
		 
		if (!$perm->isAdmin() && $popup != 1)
		{
			$icmsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
		}
		 
		if ($updtxt)
		{
			$icmsForgeErrorHandler->addMessage($updtxt);
		}
		 
		$header = survey_header($group, _XF_SUR_EDITQUESTIONS, 'is_admin_page');
		$icmsTpl->assign("survey_header", $header);
		 
		$title = "<h2>"._XF_SUR_YOUMAYUSETHESEQUESTIONS."</h2>";
		$icmsTpl->assign("survey_title", $title);
	}
	else
	{
		echo '<style>
			.bg1 { background-color: #E3E4E0; }
			.bg2 { background-color: #CCCCCC; }
			.bg3 { background-color: #DDE1DE; }
			.bg4 { background-color: #F5F5F5; }
			.bg5 { background-color: #F5F5F5; }
			</style>';
		 
		echo "<h2>Available Questions for Surveys</h2>";
	}
	 
	 
	function ShowResultsEditQuestion($result, $onlypopup = 0)
	{
		global $group_id, $icmsConfig, $icmsDB;
		$rows = $icmsDB->getRowsNum($result);
		$cols = unofficial_getNumFields($result);
		 
		if (!$onlypopup)
		{
			$content .= "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";
		}
		 
		$content .= "<table border='0' width='100%'><tr class='bg2'>";
		 
		for($i = 0; $i < $cols; $i++)
		{
			$content .= "<td><strong>".unofficial_getFieldName($result, $i)."</strong></td>\n";
		}
		$content .= "</tr>";
		 
		for($j = 0; $j < $rows; $j++)
		{
			$content .= "<tr class='".($j % 2 > 0 ? 'bg1' : 'bg3')."'><td>";
			 
			if (!$onlypopup)
			{
				$content .= "<a href='edit_question.php?group_id=$group_id&question_id=". unofficial_getDBResult($result, $j, "question_id"). "'>".unofficial_getDBResult($result, $j, "question_id")."</a>";
			}
			else
				{
				$content .= unofficial_getDBResult($result, $j, "question_id");
			}
			 
			$content .= "</td>";
			 
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
	 
	$sql = "SELECT sq.question_id,sq.question,sqt.type " ."FROM ".$icmsDB->prefix("xf_survey_questions")." sq,".$icmsDB->prefix("xf_survey_question_types")." sqt " ."WHERE sqt.id=sq.question_type " ."AND sq.group_id='$group_id' " ."ORDER BY sq.question_id DESC";
	 
	$result = $icmsDB->query($sql);
	 
	 
	if ($popup != 1)
	{
		$icmsTpl->assign("survey_content", ShowResultsEditQuestion($result, $popup));
		include(ICMS_ROOT_PATH."/footer.php");
	}
	else
	{
		echo ShowResultsEditQuestion($result, $popup);
	}
?>