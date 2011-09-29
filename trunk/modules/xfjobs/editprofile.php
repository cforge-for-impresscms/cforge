<?php
/**
  *
  * SourceForge Jobs (aka Help Wanted) Board
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: editprofile.php,v 1.7 2004/04/16 18:07:25 jcox Exp $
  *
  */

include_once ("../../mainfile.php");

$langfile="people.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once (XOOPS_ROOT_PATH."/modules/xfaccount/account_util.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfjobs/people_utils.php");

$xoopsOption['template_main'] = 'xfjobs_editprofile.html';

if (!$xoopsUser)
{
	redirect_header(XOOPS_URL."/user.php",2,_NOPERM);
	exit;
}

if ($update_profile) {
	/*
		update the job's description, status, etc
	*/
	if (!$resume) {
		//required info
		$content .=  _XF_PEO_FILLINALLFIELDS;
		exit;
	}

	$sql = "UPDATE ".$xoopsDB->prefix("xf_user_profile")." SET people_view_skills='$people_view_skills',resume='".$ts->makeTareaData4Save($resume)."' "
	      ."WHERE user_id='".$xoopsUser->getVar("uid")."'";

	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$xoopsForgeErrorHandler->addError('User update FAILED - '.
			$xoopsDB->error());
	} else {
		$xoopsForgeErrorHandler->addMessage(_XF_PEO_USERUPDATED);
	}

} else if ($add_to_skill_inventory) {
	/*
		add item to job inventory
	*/
	if ($skill_id==100 || $skill_level_id==100 || $skill_year_id==100) {
		//required info
		$content .=  _XF_PEO_FILLINALLFIELDS;
		exit;
	}
	people_add_to_skill_inventory($skill_id,$skill_level_id,$skill_year_id);

} else if ($update_skill_inventory) {
	/*
		Change Skill level, experience etc.
	*/
	if ($skill_level_id==100 || $skill_year_id==100  || !$skill_inventory_id) {
		//required info
		$content .= _XF_PEO_FILLINALLFIELDS;
		exit;
	}

	$sql = "UPDATE ".$xoopsDB->prefix("xf_people_skill_inventory")." SET "
	      ."skill_level_id='$skill_level_id',"
				."skill_year_id='$skill_year_id' "
				."WHERE user_id='".$xoopsUser->getVar("uid")."' "
				."AND skill_inventory_id='$skill_inventory_id'";

	$result = $xoopsDB->queryF($sql);

	if (!$result) {
		$xoopsForgeErrorHandler->addError('User Skill update FAILED - '.
			$xoopsDB->error());
	} else {
		$xoopsForgeErrorHandler->addMessage(_XF_PEO_USERSKILLUPDATED);
	}

} else if ($delete_from_skill_inventory) {
	/*
		remove this skill from this job
	*/
	if (!$skill_inventory_id) {
		//required info
		$content .=  _XF_PEO_FILLINALLFIELDS;
		exit;
	}

	$sql = "DELETE FROM ".$xoopsDB->prefix("xf_people_skill_inventory")." "
	      ."WHERE user_id='".$xoopsUser->getVar("uid")."' "
				."AND skill_inventory_id='$skill_inventory_id'";

	$result = $xoopsDB->queryF($sql);
	if (!$result) {
			$xoopsForgeErrorHandler->addError('User Skill Delete FAILED '.
				$xoopsDB->error());
	} else {
		$xoopsForgeErrorHandler->addMessage(_XF_PEO_USERSKILLDELETED);
	}
}
/*
	Fill in the info to create a job
*/

$metaTitle=": "._XF_MY_SKILLPROFILE;

include ("../../header.php");
$xoopsTpl->assign("account_header", account_header(_XF_MY_SKILLPROFILE));

//$xoopsForgeErrorHandler->displayFeedback();

//for security, include group_id
$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_user_profile")." WHERE user_id='".$xoopsUser->getVar("uid")."'";
$result = $xoopsDB->queryF($sql);
if ($xoopsDB->getRowsNum($result) < 1) {
  $xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_user_profile")." (user_id) VALUES (".$xoopsUser->getVar("uid").")");

	$people_view_skills = 1;
	$resume = "";
} else {
	$people_view_skills = unofficial_getDBResult($result,0,'people_view_skills');
	$resume = unofficial_getDBResult($result,0,'resume');
}
$content .=  '
<P>'._XF_PEO_EDITYOURPROFILEINFO.'
<P>
<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
<P>'._XF_PEO_EDITYOURPROFILEINFO1.'
<P>
<B>'._XF_PEO_PUBLICLYVIEWABLE.':</B><BR>
<INPUT TYPE="RADIO" NAME="people_view_skills" VALUE="0" '. (($people_view_skills==0)?'CHECKED':'') .'> <B>'._NO.'</B><BR>
<INPUT TYPE="RADIO" NAME="people_view_skills" VALUE="1" '. (($people_view_skills==1)?'CHECKED':'') .'> <B>'._YES.'</B><BR>
<P>'._XF_PEO_GIVEUSSOMEINFO.'
<P>
<B>'._XF_PEO_RESUME.':</B><BR>
<TEXTAREA NAME="resume" ROWS="15" COLS="60" WRAP="SOFT">'. $ts->makeTareaData4Edit($resume) .'</TEXTAREA>
<P>
<INPUT TYPE="SUBMIT" NAME="update_profile" VALUE="'._XF_PEO_UPDATEPROFILE.'">
</FORM><P>';

$content .= people_edit_skill_inventory( $xoopsUser->uid());

$content .='<P><FORM ACTION="'.XOOPS_URL.'/modules/xfaccount/" METHOD="POST">
<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_PEO_FINISHED.'"></FORM>';

      $xoopsTpl->assign("title",$title);
      $xoopsTpl->assign("content",$content);

//CloseTable();
//include (XOOPS_ROOT_PATH."/footer.php");
  include("../../footer.php");
?>
