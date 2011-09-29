<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: show_results_aggregate.php,v 1.5 2004/04/16 22:39:30 jcox Exp $
  *
  */
include_once ("../../../../mainfile.php");

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/HTML_Graphs.php");
$xoopsOption['template_main'] = 'survey/admin/xfmod_show_results_aggregates.html';

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if(!$perm->isAdmin())
{
	$xoopsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
}

include (XOOPS_ROOT_PATH."/header.php");

$header = survey_header($group, _XF_SUR_SURVEYAGGREGATERESULTS, 'is_admin_page');
$xoopsTpl->assign("survey_header",$header);

// Select this survey from the database
$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_surveys")." WHERE survey_id='$survey_id' AND group_id='$group_id'";
$result = $xoopsDB->query($sql);

$content = "<H4>".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, "survey_title"))."</H4><P>";

// Select the questions for this survey
$questions = unofficial_getDBResult($result, 0, "survey_questions");
$questions = str_replace(" ", "", $questions);
$quest_array = explode(',', $questions);
$count = count($quest_array);

$content .= "\n\n<TABLE>";

$q_num=1;

for ($i=0; $i<$count; $i++) {

	// Build the questions on the HTML form
	$sql = "SELECT question_type,question,question_id FROM ".$xoopsDB->prefix("xf_survey_questions")." WHERE question_id='".$quest_array[$i]."' AND group_id='$group_id'";
	$result = $xoopsDB->query($sql);

	$question_type = unofficial_getDBResult($result, 0, "question_type");

	if ($question_type == "4") {

		// Don't show question number if it's just a comment
		$content .= "\n<TR><TD VALIGN='TOP'>&nbsp;</TD>\n<TD>"; 

	} else {

		$content .= "\n<TR><TD VALIGN='TOP'><B>";

		// If it's a 1-5 question box and first in series, move Quest
		// number down a bit
		//if (($question_type != $last_question_type) && (($question_type == "1") || ($question_type == "3"))) {
		//	$content .= "&nbsp;";
		//}

		$content .= $q_num."&nbsp;&nbsp;&nbsp;&nbsp;<BR></TD>\n<TD>";
		$q_num++;

	}

	if ($question_type == "1") {

		// This is a radio-button question. Values 1-5.	
		// Show the 1-5 markers only if this is the first in a series
		if ($question_type != $last_question_type) {
			$content .= "\n<B>1 &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; 5</B>\n";
			$content .= "<BR>";

		}

		//Select the number of responses to this question
		$sql = "SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_survey_responses")." WHERE survey_id='$survey_id' AND question_id='$quest_array[$i]' AND response IN (1,2,3,4,5) AND group_id='$group_id'";

		$result2 = $xoopsDB->query($sql);
		if (!$result2 || $xoopsDB->getRowsNum($result2) < 1) {
			$content .= "error";
			$content .= $xoopsDB->error();
		} else {
			$response_count = unofficial_getDBResult($result2, 0, 'count');
			$content .= "<B>" . $response_count . "</B> "._XF_SUR_RESPONSES." ";
		}
		
		// average
		if ($response_count > 0){
			$sql = "SELECT AVG(response) AS avg FROM ".$xoopsDB->prefix("xf_survey_responses")." WHERE survey_id='$survey_id' AND question_id='$quest_array[$i]' AND group_id='$group_id'";
		  $result2 = $xoopsDB->query($sql);
			if (!$result2 || $xoopsDB->getRowsNum($result2) < 1) {
				$content .= "error";
				$content .= $xoopsDB->error();
			} else {
				$content .= "<B>".unofficial_getDBResult($result2, 0, 'avg')."</B> "._XF_SUR_AVERAGE;
			}
			
			$sql = "SELECT response,COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_survey_responses")." WHERE survey_id='$survey_id' AND question_id='$quest_array[$i]' AND group_id='$group_id' GROUP BY response";
		  $result2 = $xoopsDB->query($sql);
			if (!$result2 || $xoopsDB->getRowsNum($result2) < 1) {
				$content .= "error";
				$content .= $xoopsDB->error();
			} else {
				//$content .= GraphResult($result2,$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, "question")));
			}
		}// end if (responses to survey question present)
	} else if ($question_type == "2") {

		// This is a text-area question.
		$content .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, "question"))."<BR>\n";

		$content .= "<A HREF='show_results_comments.php?survey_id=$survey_id&question_id=$quest_array[$i]&group_id=$group_id'>"._XF_SUR_VIEWCOMMENTS."</A>";

	} else if ($question_type == "3") {

		// This is a Yes/No question.
		// Show the Yes/No only if this is the first in a series
		if ($question_type != $last_question_type) {
			//$content .= "<B>Yes / No</B><BR>\n";
			$content .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, "question"))."<BR>\n";
		}

		// Select the count and average of responses to this question
		$sql = "SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_survey_responses")." WHERE survey_id='$survey_id' AND question_id='$quest_array[$i]' AND group_id='$group_id' AND response IN (1,5)";
	  $result2 = $xoopsDB->query($sql);
		if (!$result2 || $xoopsDB->getRowsNum($result2) < 1) {
			$content .= "error";
			$content .= $xoopsDB->error();
		} else {
			$content .= "<B>".unofficial_getDBResult($result2, 0, 0)."</B> "._XF_SUR_RESPONSES." ";
		}

		// average
		$sql = "SELECT AVG(response) AS avg FROM ".$xoopsDB->prefix("xf_survey_responses")." WHERE survey_id='$survey_id' AND question_id='$quest_array[$i]' AND group_id='$group_id'";
	  $result2 = $xoopsDB->query($sql);
		if (!$result2 || $xoopsDB->getRowsNum($result2) < 1) {
			$content .= "error";
			$content .= $xoopsDB->error();
		} else {
			$content .= "<B>".unofficial_getDBResult($result2, 0, 0)."</B> ";
		}

		// Get the YES responses
		$sql = "SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_survey_responses")." WHERE survey_id='$survey_id' AND question_id='$quest_array[$i]' AND group_id='$group_id' AND response='1'";
	  $result2 = $xoopsDB->query($sql);

		$name_array[0] = "Yes";

		if (!$result2 || $xoopsDB->getRowsNum($result2) < 1) {
			$value_array[0] = 0;
		} else {
			$value_array[0] = unofficial_getDBResult($result2, 0, "count");
		}
		$content .= " (".$value_array[0]." = YES";

		// Get the NO responses
		$sql = "SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_survey_responses")." WHERE survey_id='$survey_id' AND question_id='$quest_array[$i]' AND group_id='$group_id' AND response='5'";
	  $result2 = $xoopsDB->query($sql);

		$name_array[1]="No";

		if (!$result2 || $xoopsDB->getRowsNum($result2) < 1) {
			$value_array[1] = 0;
		} else {
			$value_array[1] = unofficial_getDBResult($result2, 0, "count");
		}
		$content .= ", ".$value_array[0]." = No)";

		//$content .= GraphIt($name_array,$value_array,$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, "question")));

	} else if ($question_type == "4") {

		// This is a comment only.
		$content .= "&nbsp;<P><B>".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, "question"))."</B>\n";
		$content .= "<INPUT TYPE='HIDDEN' NAME='_".$quest_array[$i]."' VALUE='-666'>";

	} else if ($question_type == "5") {

		// This is a text-field question.
		$content .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, "question"))."<BR>\n";

		$content .= "<A HREF='show_results_comments.php?survey_id=$survey_id&question_id=$quest_array[$i]&group_id=$group_id'>"._XF_SUR_VIEWCOMMENTS."</A>";

	}

	$content .= "</TD></TR>";

	$last_question_type = $question_type;

}

$content .= "\n\n</TABLE>";

$xoopsTpl->assign("content",$content);

include (XOOPS_ROOT_PATH."/footer.php");

?>