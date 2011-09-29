<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: add_survey.php,v 1.6 2004/04/16 22:39:30 jcox Exp $
  *
  */
include_once ("../../../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
//$survey_page = SURVEY_ADD_SURVEYS_PAGE;

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
$xoopsOption['template_main'] = 'survey/admin/xfmod_add_survey.html';

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
$survey_title = util_http_track_vars('survey_title');
$survey_questions = util_http_track_vars('survey_questions');
$qra = util_http_track_vars('qra');

if ($post_changes)
{
	$isvalid = true;
	if(strlen($survey_title) < 1)
	{
		$xoopsForgeErrorHandler->addError("Name of survey is required");
		$isvalid = false;
	}

	$survey_questions = trim($survey_questions);

	if(strlen($survey_questions) < 1)
	{	var_dump($survey_questions);
		$xoopsForgeErrorHandler->addError("Survey questions must be specified");
		$isvalid = false;
	}

	if($isvalid)
	{
		$sql = "INSERT INTO ".$xoopsDB->prefix("xf_surveys").
			" (survey_title,group_id,survey_questions) ".
			"VALUES ('$survey_title','$group_id','$survey_questions')";

		$result = $xoopsDB->queryF($sql);
		if ($result)
		{
			$xoopsForgeErrorHandler->addMessage('Survey "'.$survey_title.'" added');
			$survey_title = "";
			$survey_questions = "";
		}
		else
		{
			$xoopsForgeErrorHandler->addError("Failed to add survey: ".
				$xoopsDB->error());
		}
	}
}

include (XOOPS_ROOT_PATH."/header.php");
$header = survey_header($group, _XF_SUR_ADDSURVEYS, 'is_admin_page');
$xoopsTpl->assign("survey_header",$header);

$xoopsTpl->assign("survey_name",_XF_SUR_NAMEOFSURVEY);
$xoopsTpl->assign("survey_title",$survey_title);
$xoopsTpl->assign("group_id",$group_id);

//include(XOOPS_ROOT_PATH."modules/xfmod/survey/admin/quest_spec.php");

$xoopsTpl->assign("uparrowUrl",XOOPS_URL."/modules/xfmod/images/uparrow.gif");
$xoopsTpl->assign("downarrowUrl",XOOPS_URL."/modules/xfmod/images/downarrow.gif");
$xoopsTpl->assign("leftarrowUrl",XOOPS_URL."/modules/xfmod/images/leftarrow.gif");
$xoopsTpl->assign("rightarrowUrl",XOOPS_URL."/modules/xfmod/images/rightarrow.gif");

$content = '';

$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_survey_question_types");
$qres = $xoopsDB->queryF($sql);

if($qres)
{
	$qr = $xoopsDB->getRowsNum($qres);

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
if(strlen(trim($survey_questions)) > 0)
{
	$qra = preg_split("/[\s,]+/", $survey_questions);

	$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_survey_questions").
		" WHERE question_id='".$qra[0]."'";

	for($qi = 1; $qi < count($qra); $qi++)
	{
		$sql .= " OR question_id='".$qra[$qi]."'";
	}

	$qres = $xoopsDB->queryF($sql);

	if($qres)
	{
		$qr = $xoopsDB->getRowsNum($qres);

		for($qi = 0; $qi < $qr; $qi++)
		{
			$qtxt = unofficial_getDBResult($qres, $qi, "question");
			$quest_id = unofficial_getDBResult($qres, $qi, "question_id");
			$quest_type = unofficial_getDBResult($qres, $qi, "question_type");
			$questra[$qi] = $quest_id;
			$qval = $quest_id.",".$quest_type;

			if(!$qi)
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

$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_survey_questions").
	" WHERE group_id='".$group_id."'";

if(count($qra) > 0)
{
	for($qi = 0; $qi < count($qra); $qi++)
	{
		$sql .= " AND question_id!='".$qra[$qi]."'";
	}
}

$content3 = '';

$qres = $xoopsDB->queryF($sql);
if($qres)
{
	$qr = $xoopsDB->getRowsNum($qres);
	for($qi = 0; $qi < $qr; $qi++)
	{
		$qval = unofficial_getDBResult($qres, $qi, "question_id");
		$qval .= ",".unofficial_getDBResult($qres, $qi, "question_type");
		$qtxt = unofficial_getDBResult($qres, $qi, "question");
		if(!$qi)
		{
			$content3 .= "<option selected value='$qval'>$qtxt</option>";
		}
		else
		{
			$content3 .= "<option value='$qval'>$qtxt</option>";
		}
	}
}

	//$xoopsForgeErrorHandler->displayFeedback();
$xoopsTpl->assign("feedback", 'feedback');
$xoopsTpl->assign("content",$content);
$xoopsTpl->assign("survey_questions",$content2);
$xoopsTpl->assign("available_questions",$content3);

$xoopsTpl->assign("is_active",_XF_SUR_ISACTIVE);
$xoopsTpl->assign("YES",_YES);
$xoopsTpl->assign("NO",_NO);
$xoopsTpl->assign("add_survey",_XF_SUR_ADDTHISSURVEY);

function  ShowResultsEditSurvey($result) {
	global $group_id, $xoopsDB;
	$rows  =  $xoopsDB->getRowsNum($result);
	$cols  =  unofficial_getNumFields($result);

	$content = "<h4>".sprintf(_XF_SUR_XFOUND, $rows)."</h4>";

        $content .= "<table border='0' width='100%'><tr class='bg2'>";
	for ($i  =  0;  $i  <  $cols;  $i++)  {
		$content .="<TD><b>".unofficial_getFieldName($result, $i)."</b></td>";
	}
	$content .= "</tr>";

	for($j  =  0;  $j  <  $rows;  $j++)  {

		$content .= "<TR class='".($j%2>0?'bg1':'bg3')."'>\n";
		$content .= "<TD><A HREF='edit_survey.php?group_id=$group_id&survey_id=".unofficial_getDBResult($result,$j,0)."'>".unofficial_getDBResult($result,$j,0)."</A></TD>";
		for ($i = 1; $i < $cols; $i++)  {
			$content .= "<TD>".unofficial_getDBResult($result,$j,$i)."</TD>";
		}

		$content .= "</tr>";
	}
	$content .= "</table>";

	return $content;
}

/*
	Select this survey from the database
*/

$sql = "SELECT survey_id,survey_title,survey_questions,is_active  FROM ".$xoopsDB->prefix("xf_surveys")." WHERE group_id='$group_id'";

$result = $xoopsDB->query($sql);

$xoopsTpl->assign("existing_surveys",_XF_SUR_EXISTINGSURVEYS);
$xoopsTpl->assign("survey_content",ShowResultsEditSurvey($result));

include (XOOPS_ROOT_PATH."/footer.php");
?>
