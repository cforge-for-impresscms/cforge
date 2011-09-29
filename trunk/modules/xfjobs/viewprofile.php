<?php
/**
  *
  * SourceForge Jobs (aka Help Wanted) Board
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: viewprofile.php,v 1.4 2003/12/10 20:01:32 jcox Exp $
  *
  */
include_once ("../../mainfile.php");

require_once("language/english/people.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfjobs/people_utils.php");
$xoopsOption['template_main'] = 'xfjobs_viewprofile.html';

include (XOOPS_ROOT_PATH."/header.php");

if ($user_id) {

	/*
		Fill in the info to create a job
	*/

  $content = "<B style='font-size:16px;align:left;'>"._XF_PEO_VIEWUSERPROFILE."</B><br />";

	//for security, include group_id
	$sql = "SELECT user_id,uname,people_view_skills,resume FROM ".$xoopsDB->prefix("users").",".$xoopsDB->prefix("xf_user_profile")." "
        ."WHERE uid='$user_id' "
        ."AND uid=user_id";

	$result = $xoopsDB->query($sql);
	if (!$result || $xoopsDB->getRowsNum($result) < 1) {
 	  $content .= '<H4>'._XF_PEO_NOTENTEREDPROFILE.'</H4>';
	} else {

		/*
			profile set private
		*/
		if (unofficial_getDBResult($result,0,'people_view_skills') != 1) {
			$content .= '<H4>'._XF_PEO_SETPROFILETOPRIVATE.'</H4>';
			$xoopsTpl->assign("content",$content);
			include (XOOPS_ROOT_PATH."/footer.php");
			exit;
		}

		$content .= '
		<P>
		<TABLE BORDER="0" WIDTH="100%">
		<TR><TD>
			<B>'._XF_PEO_USERNAME.':</B><BR>
			'. unofficial_getDBResult($result,0,'uname') .'
		</TD></TR>
		<TR><TD>
			<B>'._XF_PEO_RESUME.':</B><BR>
			'. $ts->makeTareaData4Show(unofficial_getDBResult($result,0,'resume')) .'
		</TD></TR>
		<TR><TD>
		<H4>'._XF_PEO_SKILLINVENTORY.'</H4>';

		//now show the list of desired skills
		$content .= '<P>'.people_show_skill_inventory($user_id);
		$content .= '</TD></TR></TABLE>';
	}
	
	$xoopsTpl->assign("content",$content);
	include (XOOPS_ROOT_PATH."/footer.php");

} else {
  redirect_header($GLOBALS["HTTP_REFERER"],4,"ERROR<br />uid not found!");
  exit;
}

?>
