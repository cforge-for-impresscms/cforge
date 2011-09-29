<?php

/*
	Note: It is assumed the following code will be included
	within an HTML form and the form will have the
	onSubmit attribute set to "questOnSubmit". It is also
	assumed that the enclose form with have the name attibute
	set to "qform".
*/

$uparrowUrl = "".XOOPS_URL."/modules/xfmod/images/uparrow.gif";
$downarrowUrl = "".XOOPS_URL."/modules/xfmod/images/downarrow.gif";
$leftarrowUrl = "".XOOPS_URL."/modules/xfmod/images/leftarrow.gif";
$rightarrowUrl = "".XOOPS_URL."/modules/xfmod/images/rightarrow.gif";

$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_survey_question_types");

$qres = $xoopsDB->queryF($sql);
if($qres)
{
	$qr = $xoopsDB->getRowsNum($qres);

	echo '<script language="JavaScript">';
	echo "var questionTypes = new Array();";

	for($qi = 0; $qi < $qr; $qi++)
	{
		$qtid = unofficial_getDBResult($qres, $qi, "id");
		$qtype = unofficial_getDBResult($qres, $qi, "type");

		echo 'questionTypes["'.$qtid.'"] = "'.$qtype.'";';
	}

	echo "</script>";
}

?>

<input type="hidden" value="" name="survey_questions">

<table border='0' cellpadding='2' cellspacing='0' valign='top' width='20%'>
<tr>
<td>
	<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>
		<tr><td><a href="javascript:questMoveUp()">
		<img src="<?php echo $uparrowUrl;?>" border="1" width="14" height="14" alt="Up Arrow"/></a></td></tr>
		<tr><td><a href="javascript:questMoveDown()">
		<img src="<?php echo $downarrowUrl;?>" border="1" width="14" height="14" alt="Down Arrow"/></a></td></tr>
	</table>
</td>

<td>
	<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>
		<tr><td>Survey Questions</td></tr>


	<tr><td><select name="squestions" size="10" width=200 style="width: 200px"
		onFocus="sfocused()">

<?php
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
				echo "<option selected value='$qval'>$qtxt</option>";
			}
			else
			{
				echo "<option value='$qval'>$qtxt</option>";
			}
		}
	}
}

?>

</select></td></tr></table></td>

<td>
	<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>
		<tr><td><a href="javascript:questMoveLeft()">
		<img src="<?php echo $leftarrowUrl;?>" border="1" width="14" height="14" alt="Add Question"/></a></td></tr>
		<tr><td><a href="javascript:questMoveRight()">
		<img src="<?php echo $rightarrowUrl;?>" border="1" width="14" height="14" alt="Remove Question"/></a></td></tr>
	</table>
</td>

<td>
	<table border='0' cellpadding='0' cellspacing='0' valign='top' width='100%'>
		<tr><td>Available Questions</td></tr>
		<tr><td>
		<select name="aquestions" size="10" width=200 style="width: 200px"
			onFocus="afocused()">
<?php

$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_survey_questions").
	" WHERE group_id='".$group_id."'";

if(count($qra) > 0)
{
	for($qi = 0; $qi < count($qra); $qi++)
	{
		$sql .= " AND question_id!='".$qra[$qi]."'";
	}
}

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
			echo "<option selected value='$qval'>$qtxt</option>";
		}
		else
		{
			echo "<option value='$qval'>$qtxt</option>";
		}
	}
}
?>

</select></td></tr></table></td>

<td>
<input type="button" value="Question Details" onClick="questDetail()">
</td></table>

<?php?>
