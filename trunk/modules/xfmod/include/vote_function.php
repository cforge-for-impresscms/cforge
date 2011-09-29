<?php

/**

 * vote_function.php

 *

 * SourceForge: Breaking Down the Barriers to Open Source Development

 * Copyright 1999-2001 (c) VA Linux Systems

 * http://sourceforge.net

 *

 * @version   $Id: vote_function.php,v 1.3 2004/01/26 18:56:51 devsupaul Exp $

 */



/**

 * vote_number_to_stars() - Turns vote results into *'s

 *

 * @param		int		Raw value

 */

function vote_number_to_stars($raw) {

  global $xoopsConfig;



	$raw = intval($raw*2);



	if ($raw % 2 == 0) {

		$show_half = 0;

	} else {

		$show_half = 1;

	}

	$count = intval($raw / 2);

	for ($i = 0; $i < $count; $i++) {

		$return .= "<img src='".XOOPS_URL."/modules/xfmod/images/ic/check.png' width='15' height='16' alt=''>";

	}

	if ($show_half == 1) {

		$return .= "<img src='".XOOPS_URL."/modules/xfmod/images/ic/halfcheck.png' width='15' height='16' alt=''>";

	}

	return $return;

}



/**

 * vote_show_thumbs() - Show vote stars

 *

 * @param		int		The survey ID

 * @param		string	The rating type

 */

function vote_show_thumbs($id,$flag) {

	/*

		$flag

		project - 1

		release - 2

		forum_message - 3

		user - 4

	*/

	$rating=vote_get_rating ($id,$flag);

	if ($rating==0) {

		return "<B>(unrated)</B>";

	} else {

		return vote_number_to_stars($rating).'('.$rating.')';

	}

}



/**

 * vote_get_rating() - Get a vote rating

 *

 * @param		int		The survey ID

 * @param		string	The rating type

 */

function vote_get_rating ($id,$flag) {

	$sql="SELECT response FROM survey_rating_aggregate WHERE type='$flag' AND id='$id'";

	$result=db_query($sql);

	if (!$result || (db_numrows($result) < 1) || (db_result($result,0,0)==0)) {

		return '0';

	} else {

		return db_result($result,0,0);

	}

}



/**

 * vote_show_release_radios() - Show release radio buttons

 *

 * @param		int		Survey ID

 * @param		string	The rating type

 */

function vote_show_release_radios ($vote_on_id,$flag) {

	/*

		$flag

		project - 1

		release - 2

		forum_message - 3

		user - 4

	*/



//html_blankimage($height,$width)

	$rating=vote_get_rating ($vote_on_id,$flag);

	if ($rating==0) {

		$rating='2.5';

	}

	$rating=((16*vote_get_rating ($vote_on_id,$flag))-15);



	//global $REQUEST_URI;

	?>

	<FONT SIZE="-2">

	<FORM ACTION="/survey/rating_resp.php" METHOD="POST">

	<INPUT TYPE="HIDDEN" NAME="vote_on_id" VALUE="<?php echo $vote_on_id; ?>">

	<INPUT TYPE="HIDDEN" NAME="redirect_to" VALUE="<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">

	<INPUT TYPE="HIDDEN" NAME="flag" VALUE="<?php echo $flag; ?>">

	<CENTER>

	<?php echo html_image("images/rateit.png","100","9",array()); ?>

	<BR>

	<?php

		echo html_blankimage(1,$rating);

		echo html_image("images/ic/caret.png","9","6",array());

	?>

	<BR>

	<INPUT TYPE="RADIO" NAME="response" VALUE=1>

	<INPUT TYPE="RADIO" NAME="response" VALUE=2>

	<INPUT TYPE="RADIO" NAME="response" VALUE=3>

	<INPUT TYPE="RADIO" NAME="response" VALUE=4>

	<INPUT TYPE="RADIO" NAME="response" VALUE=5>

	<BR>

	<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Rate">

	</CENTER>

	</FORM>

	</FONT>

	<?php



}



function getSurveysFromGroup($group_id, $active)

{

  global $xoopsDB;



	$ret = array();

  $result = $xoopsDB->query("SELECT survey_id,survey_title FROM ".$xoopsDB->prefix("xf_surveys")." WHERE group_id='".$group_id."' AND is_active='".$active."'");

	while ($myrow = $xoopsDB->fetchArray($result) ) {

		$ret[$myrow['survey_id']] = $myrow['survey_title'];

	}

	return $ret;

}

/**

 * show_survey() - Select and show a specific survey from the database

 *

 * @param		int		The group ID

 * @param		int		The survey ID

 */

function show_survey ($group_id, $survey_id)

{

  global $xoopsDB, $ts;

	$return = "";

	$return .= "<FORM ACTION='".XOOPS_URL."/modules/xfmod/survey/survey_resp.php' METHOD='POST'>"

	          ."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>"

	          ."<INPUT TYPE='HIDDEN' NAME='survey_id' VALUE='".$survey_id."'>";



//	  Select this survey from the database



  $sql = "SELECT * FROM ".$xoopsDB->prefix("xf_surveys")." WHERE survey_id='$survey_id'";



  $result = $xoopsDB->query($sql);



	if ($xoopsDB->getRowsNum($result) > 0)

	{

//	  $return .= "<H4>".unofficial_getDBResult($result, 0, 'survey_title')."</H4>";

		$return .= "<B>".unofficial_getDBResult($result, 0, 'survey_title')."</B><br><br>";



	  	// Select the questions for this survey



		$questions = unofficial_getDBResult($result, 0, 'survey_questions');

		$quest_array = preg_split("/[\s,]+/", $questions);

		$count = count($quest_array);



//  	$return .= "<TABLE BORDER=0>";



		$q_num = 1;



		for ($i = 0; $i < $count; $i++)

		{

			// Build the questions on the HTML form



			$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_survey_questions")." WHERE question_id='".$quest_array[$i]."'";

		  	$result = $xoopsDB->query($sql);

		  	$question_type = unofficial_getDBResult($result, 0, 'question_type');



		  	if ($question_type == '4') {

				// Don't show question number if it's just a comment



			  $return .= "<TR><TD VALIGN=TOP>&nbsp;</TD><TD>";



		  }

		  else

	 	  {

			  $return .= "<TR><TD VALIGN=TOP><B>";

//				  If it's a 1-5 question box and first in series, move Quest

//				  number down a bit

			  if (($question_type != $last_question_type) && (($question_type == '1') || ($question_type == '3'))) {

				  $return .= "&nbsp;<BR>";

			  }



			  $return .= $q_num."&nbsp;&nbsp;&nbsp;&nbsp;<BR></TD><TD>";

			  $q_num++;

		  }



		  if ($question_type == "1") {

//				  This is a radio-button question. Values 1-5.

			  // Show the 1-5 markers only if this is the first in a series



			  if ($question_type != $last_question_type) {

				  $return .= "<b>1</b> "._XF_LOW." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>5</b> "._XF_HIGH;

				  $return .= "<BR>";

			  }



			  for ($j=1; $j<=5; $j++) {

				  $return .= "<INPUT TYPE='RADIO' NAME='_".$quest_array[$i]."' VALUE='".$j."'>";

			  }



			  $return .= "&nbsp; ".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'));



		  } else if ($question_type == '2') {

//			  	This is a text-area question.



			  $return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";

			  $return .= "<textarea name='_".$quest_array[$i]."' rows='5' cols='60' wrap='soft'></textarea>";



		  } else if ($question_type == '3') {

//				  This is a Yes/No question.

			  //Show the Yes/No only if this is the first in a series

			  if ($question_type != $last_question_type) {

				  $return .= "<B>"._YES." / "._NO." </B>(check box for "._YES.")<BR>";

		 	  }



 			  $return .= "<INPUT TYPE='checkbox' NAME='_".$quest_array[$i]."' value='on'>";



			  $return .= "&nbsp; ".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'));



		  } else if ($question_type == '4') {

//				  This is a comment only.



			  $return .= "&nbsp;<BR><B>".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."</B>";

			  $return .= "<INPUT TYPE='HIDDEN' NAME='_".$quest_array[$i]."' VALUE='-666'>";



		  } else if ($question_type == '5') {

//				  This is a text-field question.



			  $return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";

			  $return .= "<INPUT TYPE='TEXT' name='_".$quest_array[$i]."' SIZE='20' MAXLENGTH='70'>";



		  } else {

			  // no answers, just show question

			  $return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";

		  }



		  $return .= "</TD></TR>";



		  $last_question_type = $question_type;

	  }



	  $return .= "<TR><TD ALIGN='MIDDLE' COLSPAN='2'>"

	      ."<INPUT TYPE='SUBMIT' NAME='SUBMIT' VALUE='"._XF_G_SUBMIT."'>"

			  ."<BR>"

			  ."<A ONCLICK='javascript:open(\"privacy.php\",\"Privacy\",\"height=600,width=700,scrollbars=yes,resizable=yes\"); return false;' HREF='#'>"._XF_SURVEYPRIVACY."</A>"

			  ."</TD></TR>"

			  ."</FORM>";

//			  ."</TABLE>";



  } else {

	  $return .= "<TR><TD COLSPAN='2'><b>"._XF_SURVEYNOTFOUND."</b></TD></TR>";

  }

	return $return;

}
/**
 * show_survey_small() - Select and show a specific survey from the database
 *
 * @param		int		The group ID
 * @param		int		The survey ID
 */
function show_survey_small ($group_id, $survey_id)
{
  global $xoopsDB, $ts;
	$return = "";
	$return .= "<FORM ACTION='".XOOPS_URL."/modules/xfmod/survey/survey_resp.php' METHOD='POST'>"
	          ."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>"
	          ."<INPUT TYPE='HIDDEN' NAME='survey_id' VALUE='".$survey_id."'>";

//	  Select this survey from the database

  $sql = "SELECT * FROM ".$xoopsDB->prefix("xf_surveys")." WHERE survey_id='$survey_id'";

  $result = $xoopsDB->query($sql);

	if ($xoopsDB->getRowsNum($result) > 0)
	{
//	  $return .= "<H4>".unofficial_getDBResult($result, 0, 'survey_title')."</H4>";
		$return .= "<B>".unofficial_getDBResult($result, 0, 'survey_title')."</B><br><br>";

	  	// Select the questions for this survey

		$questions = unofficial_getDBResult($result, 0, 'survey_questions');
		$quest_array = preg_split("/[\s,]+/", $questions);
		$count = count($quest_array);

//  	$return .= "<TABLE BORDER=0>";

		$q_num = 1;

		for ($i = 0; $i < $count; $i++)
		{
			// Build the questions on the HTML form

			$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_survey_questions")." WHERE question_id='".$quest_array[$i]."'";
		  	$result = $xoopsDB->query($sql);
		  	$question_type = unofficial_getDBResult($result, 0, 'question_type');

		  	if ($question_type == '4') {
				// Don't show question number if it's just a comment

			  $return .= "<TR><TD VALIGN=TOP>&nbsp;</TD><TD>";

		  }
		  else
	 	  {
			  $return .= "<TR><TD VALIGN=TOP><B>";
//				  If it's a 1-5 question box and first in series, move Quest
//				  number down a bit
			  if (($question_type != $last_question_type) && (($question_type == '1') || ($question_type == '3'))) {
				  $return .= "&nbsp;<BR>";
			  }

			  $return .= $q_num."&nbsp;&nbsp;&nbsp;&nbsp;<BR></TD><TD>";
			  $q_num++;
		  }

		  if ($question_type == "1") {
//				  This is a radio-button question. Values 1-5.
			  // Show the 1-5 markers only if this is the first in a series

			  if ($question_type != $last_question_type) {
				  $return .= "<b>1</b> "._XF_LOW." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>5</b> "._XF_HIGH;
				  $return .= "<BR>";
			  }

			  for ($j=1; $j<=5; $j++) {
				  $return .= "<INPUT TYPE='RADIO' NAME='_".$quest_array[$i]."' VALUE='".$j."'>";
			  }

			  $return .= "&nbsp; ".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'));

		  } else if ($question_type == '2') {
//			  	This is a text-area question.

			  $return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
			  $return .= "<textarea name='_".$quest_array[$i]."' rows='5' cols='30' wrap='soft'></textarea>";

		  } else if ($question_type == '3') {
//				  This is a Yes/No question.
			  //Show the Yes/No only if this is the first in a series
			  if ($question_type != $last_question_type) {
				  $return .= "<B>"._YES." / "._NO." </B>(check box for "._YES.")<BR>";
		 	  }

 			  $return .= "<INPUT TYPE='checkbox' NAME='_".$quest_array[$i]."' value='on'>";
			  $return .= "&nbsp; ".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'));

		  } else if ($question_type == '4') {
//				  This is a comment only.

			  $return .= "&nbsp;<BR><B>".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."</B>";
			  $return .= "<INPUT TYPE='HIDDEN' NAME='_".$quest_array[$i]."' VALUE='-666'>";

		  } else if ($question_type == '5') {
//				  This is a text-field question.

			  $return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
			  $return .= "<INPUT TYPE='TEXT' name='_".$quest_array[$i]."' SIZE='20' MAXLENGTH='70'>";

		  } else {
			  // no answers, just show question
			  $return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
		  }

		  $return .= "</TD></TR>";

		  $last_question_type = $question_type;
	  }

	  $return .= "<TR><TD ALIGN='MIDDLE' COLSPAN='2'>"
	      ."<INPUT TYPE='SUBMIT' NAME='SUBMIT' VALUE='"._XF_G_SUBMIT."'>"
			  ."<BR>"
			  ."<A ONCLICK='javascript:open(\"".XOOPS_URL."/modules/xfmod/survey/privacy.php\",\"Privacy\",\"height=600,width=700,scrollbars=yes,resizable=yes\"); return false;' HREF='#'>"._XF_SURVEYPRIVACY."</A>"
			  ."</TD></TR>"
			  ."</FORM>";
//			  ."</TABLE>";

  } else {
	  $return .= "<TR><TD COLSPAN='2'><b>"._XF_SURVEYNOTFOUND."</b></TD></TR>";
  }
	return $return;
}


/**

 * Show a single question for the new user rating system

 *

 * @param		string	The question to show

 * @param		string	The array element

 */

function vote_show_a_question ($question,$element_name) {

	echo '

	<TR><TD COLSPAN="2" NOWRAP>

	<INPUT TYPE="RADIO" NAME="Q_'. $element_name .'" VALUE="-3">

	&nbsp; <INPUT TYPE="RADIO" NAME="Q_'. $element_name .'" VALUE="-2">

	&nbsp; <INPUT TYPE="RADIO" NAME="Q_'. $element_name .'" VALUE="-1">

	&nbsp; <INPUT TYPE="RADIO" NAME="Q_'. $element_name .'" VALUE="0.1">

	&nbsp; <INPUT TYPE="RADIO" NAME="Q_'. $element_name .'" VALUE="1">

	&nbsp; <INPUT TYPE="RADIO" NAME="Q_'. $element_name .'" VALUE="2">

	&nbsp; <INPUT TYPE="RADIO" NAME="Q_'. $element_name .'" VALUE="3">

	</TD></TR>



	<TR><TD COLSPAN=2>'.$question.'

		<BR>&nbsp;</TD></TR>';



}



/*



	The ratings system is actually flexible enough

	to let you do N number of questions, but we are just going with 5

	that apply to everyone



*/



$USER_RATING_QUESTIONS=array();

//sorry - array starts at 1 so we can test for the questions on the receiving page

$USER_RATING_QUESTIONS[1]='Teamwork / Attitude';

$USER_RATING_QUESTIONS[2]='Code (Code-Fu)';

$USER_RATING_QUESTIONS[3]='Design / Architecture';

$USER_RATING_QUESTIONS[4]='Follow-Through / Reliability';

$USER_RATING_QUESTIONS[5]='Leadership / Management';



$USER_RATING_POPUP1[]='0 - Q';

$USER_RATING_POPUP1[]='1';

$USER_RATING_POPUP1[]='2 - Ferengi';

$USER_RATING_POPUP1[]='3';

$USER_RATING_POPUP1[]='4 - Federation';

$USER_RATING_POPUP1[]='5';

$USER_RATING_POPUP1[]='6 - Borg';



$USER_RATING_POPUP2[]='0 - White Belt';

$USER_RATING_POPUP2[]='1';

$USER_RATING_POPUP2[]='2 - Orange Belt';

$USER_RATING_POPUP2[]='3';

$USER_RATING_POPUP2[]='4 - Green Belt';

$USER_RATING_POPUP2[]='5';

$USER_RATING_POPUP2[]='6 - Black Belt';



$USER_RATING_POPUP3[]='0 - Block-Stacker';

$USER_RATING_POPUP3[]='1';

$USER_RATING_POPUP3[]='2 - Lego (r) Maniac';

$USER_RATING_POPUP3[]='3';

$USER_RATING_POPUP3[]='4 - Frank Lloyd Wright';

$USER_RATING_POPUP3[]='5';

$USER_RATING_POPUP3[]='6 - Leonardo Da Vinci';



$USER_RATING_POPUP4[]='0 - None';

$USER_RATING_POPUP4[]='1';

$USER_RATING_POPUP4[]='2 - Politician';

$USER_RATING_POPUP4[]='3';

$USER_RATING_POPUP4[]='4 - Firefighter';

$USER_RATING_POPUP4[]='5';

$USER_RATING_POPUP4[]='6 - Robot';



$USER_RATING_POPUP5[]='0 - Dr. Evil';

$USER_RATING_POPUP5[]='1';

$USER_RATING_POPUP5[]='2 - Monty Burns';

$USER_RATING_POPUP5[]='3';

$USER_RATING_POPUP5[]='4 - Don Corleone';

$USER_RATING_POPUP5[]='5';

$USER_RATING_POPUP5[]='6 - Muad\'Dib';



$USER_RATING_VALUES[]='-3';

$USER_RATING_VALUES[]='-2';

$USER_RATING_VALUES[]='-1';

$USER_RATING_VALUES[]='0.1';

$USER_RATING_VALUES[]='1';

$USER_RATING_VALUES[]='2';

$USER_RATING_VALUES[]='3';



/**

 * vote_show_user_rate_box() - Show user rating box

 *

 * @param		int		The user ID

 * @param		int		The user ID of the user who is rating $user_id

 */

function vote_show_user_rate_box ($user_id, $by_id=0) {

	if ($by_id) {

		$res = db_query("

			SELECT rate_field,rating FROM user_ratings

			WHERE rated_by='$by_id'

			AND user_id='$user_id'

		");

		$prev_vote = util_result_columns_to_assoc($res);

		while (list($k,$v) = each($prev_vote)) {

			if ($v == 0) {

				$prev_vote[$k] = 0.1;

			}

		}

	}



	global $USER_RATING_VALUES,$USER_RATING_QUESTIONS,$USER_RATING_POPUP1,$USER_RATING_POPUP2,$USER_RATING_POPUP3,$USER_RATING_POPUP4,$USER_RATING_POPUP5;

	echo '

	<TABLE BORDER=0>

		<FORM ACTION="/developer/rate.php" METHOD="POST">

		<INPUT TYPE="HIDDEN" NAME="rated_user" VALUE="'.$user_id.'">';



	for ($i=1; $i<=count($USER_RATING_QUESTIONS); $i++) {

		$popup="USER_RATING_POPUP$i";

		echo '<TR>

		<TD COLSPAN=2><B>'. $USER_RATING_QUESTIONS[$i] .':</B><BR> '

		.html_build_select_box_from_arrays($USER_RATING_VALUES,$$popup,"Q_$i",$prev_vote[$i]/*'xzxz'*/,true,'Unrated').'</TD></TR>';

	}



	echo '

		<TR><TD COLSPAN="2"><INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Rate User"></TD></TR>

		</TABLE>

	</FORM>';

}



/**

 * vote_show_user_rating() - Show a user rating

 *

 * @param		int		The user ID

 */

function vote_show_user_rating($user_id) {

	global $USER_RATING_QUESTIONS;

	$sql="SELECT rate_field,(avg(rating)+3) AS avg_rating,count(*) as count ".

		"FROM user_ratings ".

		"WHERE user_id='$user_id' ".

		"GROUP BY rate_field";

	$res=db_query($sql);

	$rows=db_numrows($res);

	if (!$res || $rows < 1) {



		echo '<TR><TD COLSPAN=2><H4>Not Yet Rated</H4></TD></TR>';



	} else {

		echo '<TR><TD COLSPAN="2">

			<H4>Current Ratings</H4>

			<P>

			Includes untrusted ratings.</TD></TR>';

		for ($i=0; $i<$rows; $i++) {

			echo '

			<TR><TD>'.$USER_RATING_QUESTIONS[db_result($res,$i,'rate_field')].'</TD>

			<TD>'.db_result($res,$i,'avg_rating').' (By '. db_result($res,$i,'count') .' Users)</TD></TR>';

		}



		$res=db_query("SELECT ranking,metric,importance_factor FROM user_metric WHERE user_id='$user_id'");

		if ($res && db_numrows($res) > 0) {

			echo '<TR><TD COLSPAN=2><B>Trusted Overall Rating</B></TD></TR>';

			echo '<TR><TD>Sitewide Ranking:</TD><TD><B>'. db_result($res,0,'ranking') .'</B></TD></TR>

				<TR><TD>Aggregate Score:</TD><TD><B>'. number_format (db_result($res,0,'metric'),3) .'</B></TD></TR>

				<TR><TD>Personal Importance:</TD><TD><B>'. number_format (db_result($res,0,'importance_factor'),3) .'</B></TD></TR>';

		} else {

			echo '<TR><TD COLSPAN=2><H4>Not Yet Included In Trusted Rankings</H4></TD></TR>';

		}

	}

}



/**

 * vote_remove_all_ratings_by() - Remove all ratings by a particular user

 *

 * @param		int		The user ID

 */

function vote_remove_all_ratings_by($user_id) {

	db_query("

		DELETE FROM user_ratings

		WHERE rated_by='$user_id'

	");

}



?>