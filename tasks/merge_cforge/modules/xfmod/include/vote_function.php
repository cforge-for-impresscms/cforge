<?php
	 
	/**
	 
	* vote_function.php
	 
	*
	 
	* SourceForge: Breaking Down the Barriers to Open Source Development
	 
	* Copyright 1999-2001(c) VA Linux Systems
	 
	* http://sourceforge.net
	 
	*
	 
	* @version   $Id: vote_function.php,v 1.3 2004/01/26 18:56:51 devsupaul Exp $
	 
	*/
	 
	 
	 
	/**
	 
	* vote_number_to_stars() - Turns vote results into *'s
	 
	*
	 
	* @param  int  Raw value
	 
	*/
	 
	function vote_number_to_stars($raw)
	{
		 
		global $icmsConfig;
		 
		 
		 
		$raw = intval($raw * 2);
		 
		 
		 
		if ($raw % 2 == 0)
		{
			 
			$show_half = 0;
			 
		}
		else
		{
			 
			$show_half = 1;
			 
		}
		 
		$count = intval($raw / 2);
		 
		for($i = 0; $i < $count; $i++)
		{
			 
			$return .= "<img src='".ICMS_URL."/modules/xfmod/images/ic/check.png' width='15' height='16' alt=''>";
			 
		}
		 
		if ($show_half == 1)
		{
			 
			$return .= "<img src='".ICMS_URL."/modules/xfmod/images/ic/halfcheck.png' width='15' height='16' alt=''>";
			 
		}
		 
		return $return;
		 
	}
	 
	 
	 
	/**
	 
	* vote_show_thumbs() - Show vote stars
	 
	*
	 
	* @param  int  The survey ID
	 
	* @param  string The rating type
	 
	*/
	 
	function vote_show_thumbs($id, $flag)
	{
		 
		/*
		 
		$flag
		 
		project - 1
		 
		release - 2
		 
		forum_message - 3
		 
		user - 4
		 
		*/
		 
		$rating = vote_get_rating($id, $flag);
		 
		if ($rating == 0)
		{
			 
			return "<strong>(unrated)</strong>";
			 
		}
		else
		{
			 
			return vote_number_to_stars($rating).'('.$rating.')';
			 
		}
		 
	}
	 
	 
	 
	/**
	 
	* vote_get_rating() - Get a vote rating
	 
	*
	 
	* @param  int  The survey ID
	 
	* @param  string The rating type
	 
	*/
	 
	function vote_get_rating($id, $flag)
	{
		 
		$sql = "SELECT response FROM survey_rating_aggregate WHERE type='$flag' AND id='$id'";
		 
		$result = db_query($sql);
		 
		if (!$result || (db_numrows($result) < 1) || (db_result($result, 0, 0) == 0))
		{
			 
			return '0';
			 
		}
		else
		{
			 
			return db_result($result, 0, 0);
			 
		}
		 
	}
	 
	 
	 
	/**
	 
	* vote_show_release_radios() - Show release radio buttons
	 
	*
	 
	* @param  int  Survey ID
	 
	* @param  string The rating type
	 
	*/
	 
	function vote_show_release_radios($vote_on_id, $flag)
	{
		 
		/*
		 
		$flag
		 
		project - 1
		 
		release - 2
		 
		forum_message - 3
		 
		user - 4
		 
		*/
		 
		 
		 
		//html_blankimage($height,$width)
		 
		$rating = vote_get_rating($vote_on_id, $flag);
		 
		if ($rating == 0)
		{
			 
			$rating = '2.5';
			 
		}
		 
		$rating = ((16 * vote_get_rating($vote_on_id, $flag))-15);
		 
		 
		 
		//global $REQUEST_URI;
		 
	?>

<FONT size="-2">

<form action="/survey/rating_resp.php" method="POST">

<input type="hidden" name="vote_on_id" value="<?php echo $vote_on_id; ?>">

<input type="hidden" name="redirect_to" value="<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">

<input type="hidden" name="flag" value="<?php echo $flag; ?>">

<CENTER>

<?php echo html_image("images/rateit.png","100","9",array()); ?>

<BR>

	<?php
		 
		echo html_blankimage(1, $rating);
		 
		echo html_image("images/ic/caret.png", "9", "6", array());
		 
	?>

<BR>

<input type="radio" name="response" value=1>

<input type="radio" name="response" value=2>

<input type="radio" name="response" value=3>

<input type="radio" name="response" value=4>

<input type="radio" name="response" value=5>

<BR>

<input type="submit" name="submit" value="Rate">

</CENTER>

</form>

</FONT>

	<?php
		 
		 
		 
	}
	 
	 
	 
	function getSurveysFromGroup($group_id, $active)
	 
	{
		 
		global $icmsDB;
		 
		 
		 
		$ret = array();
		 
		$result = $icmsDB->query("SELECT survey_id,survey_title FROM ".$icmsDB->prefix("xf_surveys")." WHERE group_id='".$group_id."' AND is_active='".$active."'");
		 
		while ($myrow = $icmsDB->fetchArray($result))
		{
			 
			$ret[$myrow['survey_id']] = $myrow['survey_title'];
			 
		}
		 
		return $ret;
		 
	}
	 
	/**
	 
	* show_survey() - Select and show a specific survey from the database
	 
	*
	 
	* @param  int  The group ID
	 
	* @param  int  The survey ID
	 
	*/
	 
	function show_survey($group_id, $survey_id)
	 
	{
		 
		global $icmsDB, $ts;
		 
		$return = "";
		 
		$return .= "<form action='".ICMS_URL."/modules/xfmod/survey/survey_resp.php' METHOD='POST'>"  
		."<input type='hidden' name='group_id' value='".$group_id."'>"  
		."<input type='hidden' name='survey_id' value='".$survey_id."'>";
		 
		 
		 
		//   Select this survey from the database
		 
		 
		 
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_surveys")." WHERE survey_id='$survey_id'";
		 
		 
		 
		$result = $icmsDB->query($sql);
		 
		 
		 
		if ($icmsDB->getRowsNum($result) > 0)
		 
		{
			 
			//   $return .= "<H4>".unofficial_getDBResult($result, 0, 'survey_title')."</H4>";
			 
			$return .= "<strong>".unofficial_getDBResult($result, 0, 'survey_title')."</strong><br><br>";
			 
			 
			 
			// Select the questions for this survey
			 
			 
			 
			$questions = unofficial_getDBResult($result, 0, 'survey_questions');
			 
			$quest_array = preg_split("/[\s,]+/", $questions);
			 
			$count = count($quest_array);
			 
			 
			 
			//   $return .= "<table border=0>";
			 
			 
			 
			$q_num = 1;
			 
			 
			 
			for($i = 0; $i < $count; $i++)
			 
			{
				 
				// Build the questions on the HTML form
				 
				 
				 
				$sql = "SELECT * FROM ".$icmsDB->prefix("xf_survey_questions")." WHERE question_id='".$quest_array[$i]."'";
				 
				$result = $icmsDB->query($sql);
				 
				$question_type = unofficial_getDBResult($result, 0, 'question_type');
				 
				 
				 
				if ($question_type == '4')
				{
					 
					// Don't show question number if it's just a comment
					 
					 
					 
					$return .= "<th><td valign=top>&nbsp;</td><td>";
					 
					 
					 
				}
				 
				else
					 
				{
					 
					$return .= "<th><td valign=top><strong>";
					 
					//      If it's a 1-5 question box and first in series, move Quest
					 
					//      number down a bit
					 
					if (($question_type != $last_question_type) && (($question_type == '1') || ($question_type == '3')))
					{
						 
						$return .= "&nbsp;<BR>";
						 
					}
					 
					 
					 
					$return .= $q_num."&nbsp;&nbsp;&nbsp;&nbsp;<BR></td><td>";
					 
					$q_num++;
					 
				}
				 
				 
				 
				if ($question_type == "1")
				{
					 
					//      This is a radio-button question. Values 1-5.
					 
					// Show the 1-5 markers only if this is the first in a series
					 
					 
					 
					if ($question_type != $last_question_type)
					{
						 
						$return .= "<strong>1</strong> "._XF_LOW." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>5</strong> "._XF_HIGH;
						 
						$return .= "<BR>";
						 
					}
					 
					 
					 
					for($j = 1; $j <= 5; $j++)
					{
						 
						$return .= "<input type='RADIO' name='_".$quest_array[$i]."' value='".$j."'>";
						 
					}
					 
					 
					 
					$return .= "&nbsp; ".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'));
					 
					 
					 
				}
				else if($question_type == '2')
				{
					 
					//      This is a text-area question.
					 
					 
					 
					$return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
					 
					$return .= "<textarea name='_".$quest_array[$i]."' rows='5' cols='60' wrap='soft'></textarea>";
					 
					 
					 
				}
				else if($question_type == '3')
				{
					 
					//      This is a Yes/No question.
					 
					//Show the Yes/No only if this is the first in a series
					 
					if ($question_type != $last_question_type)
					{
						 
						$return .= "<strong>"._YES." / "._NO." </strong>(check box for "._YES.")<BR>";
						 
					}
					 
					 
					 
					$return .= "<input type='checkbox' name='_".$quest_array[$i]."' value='on'>";
					 
					 
					 
					$return .= "&nbsp; ".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'));
					 
					 
					 
				}
				else if($question_type == '4')
				{
					 
					//      This is a comment only.
					 
					 
					 
					$return .= "&nbsp;<BR><strong>".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."</strong>";
					 
					$return .= "<input type='hidden' name='_".$quest_array[$i]."' value='-666'>";
					 
					 
					 
				}
				else if($question_type == '5')
				{
					 
					//      This is a text-field question.
					 
					 
					 
					$return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
					 
					$return .= "<input type='text' name='_".$quest_array[$i]."' size='20' maxlength='70'>";
					 
					 
					 
				}
				else
				{
					 
					// no answers, just show question
					 
					$return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
					 
				}
				 
				 
				 
				$return .= "</td></th>";
				 
				 
				 
				$last_question_type = $question_type;
				 
			}
			 
			 
			 
			$return .= "<th><td align='middle' colspan='2'>"  
			."<input type='submit' name='submit' value='"._XF_G_SUBMIT."'>"  
			."<BR>"  
			."<A ONCLICK='javascript:open(\"privacy.php\",\"Privacy\",\"height=600,width=700,scrollbars=yes,resizable=yes\"); return false;' HREF='#'>"._XF_SURVEYPRIVACY."</a>"  
			."</td></th>"  
			."</form>";
			 
			//     ."</table>";
			 
			 
			 
		}
		else
		{
			 
			$return .= "<th><td colspan='2'><strong>"._XF_SURVEYNOTFOUND."</strong></td></th>";
			 
		}
		 
		return $return;
		 
	}
	/**
	* show_survey_small() - Select and show a specific survey from the database
	*
	* @param  int  The group ID
	* @param  int  The survey ID
	*/
	function show_survey_small($group_id, $survey_id)
	{
		global $icmsDB, $ts;
		$return = "";
		$return .= "<form action='".ICMS_URL."/modules/xfmod/survey/survey_resp.php' METHOD='POST'>" ."<input type='hidden' name='group_id' value='".$group_id."'>" ."<input type='hidden' name='survey_id' value='".$survey_id."'>";
		 
		//   Select this survey from the database
		 
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_surveys")." WHERE survey_id='$survey_id'";
		 
		$result = $icmsDB->query($sql);
		 
		if ($icmsDB->getRowsNum($result) > 0)
		{
			//   $return .= "<H4>".unofficial_getDBResult($result, 0, 'survey_title')."</H4>";
			$return .= "<strong>".unofficial_getDBResult($result, 0, 'survey_title')."</strong><br><br>";
			 
			// Select the questions for this survey
			 
			$questions = unofficial_getDBResult($result, 0, 'survey_questions');
			$quest_array = preg_split("/[\s,]+/", $questions);
			$count = count($quest_array);
			 
			//   $return .= "<table border=0>";
			 
			$q_num = 1;
			 
			for($i = 0; $i < $count; $i++)
			{
				// Build the questions on the HTML form
				 
				$sql = "SELECT * FROM ".$icmsDB->prefix("xf_survey_questions")." WHERE question_id='".$quest_array[$i]."'";
				$result = $icmsDB->query($sql);
				$question_type = unofficial_getDBResult($result, 0, 'question_type');
				 
				if ($question_type == '4')
				{
					// Don't show question number if it's just a comment
					 
					$return .= "<th><td valign=top>&nbsp;</td><td>";
					 
				}
				else
					{
					$return .= "<th><td valign=top><strong>";
					//      If it's a 1-5 question box and first in series, move Quest
					//      number down a bit
					if (($question_type != $last_question_type) && (($question_type == '1') || ($question_type == '3')))
					{
						$return .= "&nbsp;<BR>";
					}
					 
					$return .= $q_num."&nbsp;&nbsp;&nbsp;&nbsp;<BR></td><td>";
					$q_num++;
				}
				 
				if ($question_type == "1")
				{
					//      This is a radio-button question. Values 1-5.
					// Show the 1-5 markers only if this is the first in a series
					 
					if ($question_type != $last_question_type)
					{
						$return .= "<strong>1</strong> "._XF_LOW." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>5</strong> "._XF_HIGH;
						$return .= "<BR>";
					}
					 
					for($j = 1; $j <= 5; $j++)
					{
						$return .= "<input type='RADIO' name='_".$quest_array[$i]."' value='".$j."'>";
					}
					 
					$return .= "&nbsp; ".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'));
					 
				}
				else if($question_type == '2')
				{
					//      This is a text-area question.
					 
					$return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
					$return .= "<textarea name='_".$quest_array[$i]."' rows='5' cols='30' wrap='soft'></textarea>";
					 
				}
				else if($question_type == '3')
				{
					//      This is a Yes/No question.
					//Show the Yes/No only if this is the first in a series
					if ($question_type != $last_question_type)
					{
						$return .= "<strong>"._YES." / "._NO." </strong>(check box for "._YES.")<BR>";
					}
					 
					$return .= "<input type='checkbox' name='_".$quest_array[$i]."' value='on'>";
					$return .= "&nbsp; ".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'));
					 
				}
				else if($question_type == '4')
				{
					//      This is a comment only.
					 
					$return .= "&nbsp;<BR><strong>".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."</strong>";
					$return .= "<input type='hidden' name='_".$quest_array[$i]."' value='-666'>";
					 
				}
				else if($question_type == '5')
				{
					//      This is a text-field question.
					 
					$return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
					$return .= "<input type='text' name='_".$quest_array[$i]."' size='20' maxlength='70'>";
					 
				}
				else
				{
					// no answers, just show question
					$return .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'question'))."<BR>";
				}
				 
				$return .= "</td></th>";
				 
				$last_question_type = $question_type;
			}
			 
			$return .= "<th><td align='middle' colspan='2'>" ."<input type='submit' name='submit' value='"._XF_G_SUBMIT."'>" ."<BR>" ."<A ONCLICK='javascript:open(\"".ICMS_URL."/modules/xfmod/survey/privacy.php\",\"Privacy\",\"height=600,width=700,scrollbars=yes,resizable=yes\"); return false;' HREF='#'>"._XF_SURVEYPRIVACY."</a>" ."</td></th>" ."</form>";
			//     ."</table>";
			 
		}
		else
		{
			$return .= "<th><td colspan='2'><strong>"._XF_SURVEYNOTFOUND."</strong></td></th>";
		}
		return $return;
	}
	 
	 
	/**
	 
	* Show a single question for the new user rating system
	 
	*
	 
	* @param  string The question to show
	 
	* @param  string The array element
	 
	*/
	 
	function vote_show_a_question($question, $element_name)
	{
		 
		echo '
			 
			<th><td colspan="2" NOWRAP>
			 
			<input type="radio" name="Q_'. $element_name .'" value="-3">
			 
			&nbsp; <input type="radio" name="Q_'. $element_name .'" value="-2">
			 
			&nbsp; <input type="radio" name="Q_'. $element_name .'" value="-1">
			 
			&nbsp; <input type="radio" name="Q_'. $element_name .'" value="0.1">
			 
			&nbsp; <input type="radio" name="Q_'. $element_name .'" value="1">
			 
			&nbsp; <input type="radio" name="Q_'. $element_name .'" value="2">
			 
			&nbsp; <input type="radio" name="Q_'. $element_name .'" value="3">
			 
			</td></th>
			 
			 
			 
			<th><td colspan=2>'.$question.'
			 
			<BR>&nbsp;</td></th>';
		 
		 
		 
	}
	 
	 
	 
	/*
	 
	 
	 
	The ratings system is actually flexible enough
	 
	to let you do N number of questions, but we are just going with 5
	 
	that apply to everyone
	 
	 
	 
	*/
	 
	 
	 
	$USER_RATING_QUESTIONS = array();
	 
	//sorry - array starts at 1 so we can test for the questions on the receiving page
	 
	$USER_RATING_QUESTIONS[1] = 'Teamwork / Attitude';
	 
	$USER_RATING_QUESTIONS[2] = 'Code(Code-Fu)';
	 
	$USER_RATING_QUESTIONS[3] = 'Design / Architecture';
	 
	$USER_RATING_QUESTIONS[4] = 'Follow-Through / Reliability';
	 
	$USER_RATING_QUESTIONS[5] = 'Leadership / Management';
	 
	 
	 
	$USER_RATING_POPUP1[] = '0 - Q';
	 
	$USER_RATING_POPUP1[] = '1';
	 
	$USER_RATING_POPUP1[] = '2 - Ferengi';
	 
	$USER_RATING_POPUP1[] = '3';
	 
	$USER_RATING_POPUP1[] = '4 - Federation';
	 
	$USER_RATING_POPUP1[] = '5';
	 
	$USER_RATING_POPUP1[] = '6 - Borg';
	 
	 
	 
	$USER_RATING_POPUP2[] = '0 - White Belt';
	 
	$USER_RATING_POPUP2[] = '1';
	 
	$USER_RATING_POPUP2[] = '2 - Orange Belt';
	 
	$USER_RATING_POPUP2[] = '3';
	 
	$USER_RATING_POPUP2[] = '4 - Green Belt';
	 
	$USER_RATING_POPUP2[] = '5';
	 
	$USER_RATING_POPUP2[] = '6 - Black Belt';
	 
	 
	 
	$USER_RATING_POPUP3[] = '0 - Block-Stacker';
	 
	$USER_RATING_POPUP3[] = '1';
	 
	$USER_RATING_POPUP3[] = '2 - Lego(r) Maniac';
	 
	$USER_RATING_POPUP3[] = '3';
	 
	$USER_RATING_POPUP3[] = '4 - Frank Lloyd Wright';
	 
	$USER_RATING_POPUP3[] = '5';
	 
	$USER_RATING_POPUP3[] = '6 - Leonardo Da Vinci';
	 
	 
	 
	$USER_RATING_POPUP4[] = '0 - None';
	 
	$USER_RATING_POPUP4[] = '1';
	 
	$USER_RATING_POPUP4[] = '2 - Politician';
	 
	$USER_RATING_POPUP4[] = '3';
	 
	$USER_RATING_POPUP4[] = '4 - Firefighter';
	 
	$USER_RATING_POPUP4[] = '5';
	 
	$USER_RATING_POPUP4[] = '6 - Robot';
	 
	 
	 
	$USER_RATING_POPUP5[] = '0 - Dr. Evil';
	 
	$USER_RATING_POPUP5[] = '1';
	 
	$USER_RATING_POPUP5[] = '2 - Monty Burns';
	 
	$USER_RATING_POPUP5[] = '3';
	 
	$USER_RATING_POPUP5[] = '4 - Don Corleone';
	 
	$USER_RATING_POPUP5[] = '5';
	 
	$USER_RATING_POPUP5[] = '6 - Muad\'Dib';
	 
	 
	 
	$USER_RATING_VALUES[] = '-3';
	 
	$USER_RATING_VALUES[] = '-2';
	 
	$USER_RATING_VALUES[] = '-1';
	 
	$USER_RATING_VALUES[] = '0.1';
	 
	$USER_RATING_VALUES[] = '1';
	 
	$USER_RATING_VALUES[] = '2';
	 
	$USER_RATING_VALUES[] = '3';
	 
	 
	 
	/**
	 
	* vote_show_user_rate_box() - Show user rating box
	 
	*
	 
	* @param  int  The user ID
	 
	* @param  int  The user ID of the user who is rating $user_id
	 
	*/
	 
	function vote_show_user_rate_box($user_id, $by_id = 0)
	{
		 
		if ($by_id)
		{
			 
			$res = db_query("
				 
				SELECT rate_field,rating FROM user_ratings
				 
				WHERE rated_by='$by_id'
				 
				AND user_id='$user_id'
				 
				");
			 
			$prev_vote = util_result_columns_to_assoc($res);
			 
			while (list($k, $v) = each($prev_vote))
			{
				 
				if ($v == 0)
				{
					 
					$prev_vote[$k] = 0.1;
					 
				}
				 
			}
			 
		}
		 
		 
		 
		global $USER_RATING_VALUES, $USER_RATING_QUESTIONS, $USER_RATING_POPUP1, $USER_RATING_POPUP2, $USER_RATING_POPUP3, $USER_RATING_POPUP4, $USER_RATING_POPUP5;
		 
		echo '
			 
			<table border=0>
			 
			<form action="/developer/rate.php" method="POST">
			 
			<input type="hidden" name="rated_user" value="'.$user_id.'">';
		 
		 
		 
		for($i = 1; $i <= count($USER_RATING_QUESTIONS); $i++)
		{
			 
			$popup = "USER_RATING_POPUP$i";
			 
			echo '<th>
				 
				<td colspan=2><strong>'. $USER_RATING_QUESTIONS[$i] .':</strong><BR> '  
			.html_build_select_box_from_arrays($USER_RATING_VALUES, $$popup, "Q_$i", $prev_vote[$i]/*'xzxz'*/,true,'Unrated').'</td></th>';
			 
		}
		 
		 
		 
		echo '
			 
			<th><td colspan="2"><input type="submit" name="submit" value="Rate User"></td></th>
			 
			</table>
			 
			</form>';
		 
	}
	 
	 
	 
	/**
	 
	* vote_show_user_rating() - Show a user rating
	 
	*
	 
	* @param  int  The user ID
	 
	*/
	 
	function vote_show_user_rating($user_id)
	{
		 
		global $USER_RATING_QUESTIONS;
		 
		$sql = "SELECT rate_field,(avg(rating)+3) AS avg_rating,count(*) as count ".  
		"FROM user_ratings ".  
		"WHERE user_id='$user_id' ".  
		"GROUP BY rate_field";
		 
		$res = db_query($sql);
		 
		$rows = db_numrows($res);
		 
		if (!$res || $rows < 1)
		{
			 
			 
			 
			echo '<th><td colspan=2><H4>Not Yet Rated</H4></td></th>';
			 
			 
			 
		}
		else
		{
			 
			echo '<th><td colspan="2">
				 
				<H4>Current Ratings</H4>
				 
				<p>
				 
				Includes untrusted ratings.</td></th>';
			 
			for($i = 0; $i < $rows; $i++)
			{
				 
				echo '
					 
					<th><td>'.$USER_RATING_QUESTIONS[db_result($res, $i, 'rate_field')].'</td>
					 
					<td>'.db_result($res, $i, 'avg_rating').'(By '. db_result($res, $i, 'count') .' Users)</td></th>';
				 
			}
			 
			 
			 
			$res = db_query("SELECT ranking,metric,importance_factor FROM user_metric WHERE user_id='$user_id'");
			 
			if ($res && db_numrows($res) > 0)
			{
				 
				echo '<th><td colspan=2><strong>Trusted Overall Rating</strong></td></th>';
				 
				echo '<th><td>Sitewide Ranking:</td><td><strong>'. db_result($res, 0, 'ranking') .'</strong></td></th>
					 
					<th><td>Aggregate Score:</td><td><strong>'. number_format(db_result($res, 0, 'metric'), 3) .'</strong></td></th>
					 
					<th><td>Personal Importance:</td><td><strong>'. number_format(db_result($res, 0, 'importance_factor'), 3) .'</strong></td></th>';
				 
			}
			else
			{
				 
				echo '<th><td colspan=2><H4>Not Yet Included In Trusted Rankings</H4></td></th>';
				 
			}
			 
		}
		 
	}
	 
	 
	 
	/**
	 
	* vote_remove_all_ratings_by() - Remove all ratings by a particular user
	 
	*
	 
	* @param  int  The user ID
	 
	*/
	 
	function vote_remove_all_ratings_by($user_id)
	{
		 
		db_query("
			 
			DELETE FROM user_ratings
			 
			WHERE rated_by='$user_id'
			 
			");
		 
	}
	 
	 
	 
?>