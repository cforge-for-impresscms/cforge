<?php
/**
  *
  * SourceForge Forums Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: message.php,v 1.7 2003/12/15 18:09:17 devsupaul Exp $
  *
  */

include_once("../../../mainfile.php");

$langfile="forum.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
$xoopsOption['template_main'] = 'forum/xfmod_message.html';

if ($msg_id) {
	/*
		Figure out which group this message is in, for the sake of the admin links
	*/
	$result = $xoopsDB->query("SELECT fgl.send_all_posts_to,fgl.group_id,"
	                    ."fgl.allow_anonymous,fgl.forum_name,f.subject,f.group_forum_id,f.thread_id "
											."FROM ".$xoopsDB->prefix("xf_forum_group_list")." fgl,".$xoopsDB->prefix("xf_forum")." f "
											."WHERE fgl.group_forum_id=f.group_forum_id "
											."AND f.msg_id='$msg_id'");

	if (!$result || $xoopsDB->getRowsNum($result) < 1) {
		/*
			Message not found
		*/
	  redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_FRM_MESSAGENOTFOUND."<br />"._XF_FRM_MESSAGENOTEXIST);
	  exit;
	}

	$group_id = unofficial_getDBResult($result,0,'group_id');
	$forum_id = unofficial_getDBResult($result,0,'group_forum_id');
	$thread_id = unofficial_getDBResult($result,0,'thread_id');
	$forum_name = unofficial_getDBResult($result,0,'forum_name');
	$allow_anonymous = unofficial_getDBResult($result,0,'allow_anonymous');
	$send_all_posts_to = unofficial_getDBResult($result,0,'send_all_posts_to');

	$group =& group_get_object($group_id);
	$perm  =& $group->getPermission( $xoopsUser );

	//group is private
	if (!$group->isPublic()) {
		//if it's a private group, you must be a member of that group
		if (!$group->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser()) {
			redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
			exit;
		}
	}

	include ("../../../header.php");
	
	$xoopsTpl->assign("project_title",project_title($group));
	$xoopsTpl->assign("project_tabs",project_tabs ('news', $group_id));
	$content = "<B style='font-size:16px;align:left;'>".unofficial_getDBResult($result,0,'subject')."</B><br />";
	$content .= forum_show_links ($forum_id, $forum_name, $group_id, $perm->isAdmin());

	$content .= "<P>";

	$sql = "SELECT u.uname,f.group_forum_id,f.thread_id,f.subject,f.date,f.body "
	      ."FROM ".$xoopsDB->prefix("xf_forum")." f,".$xoopsDB->prefix("users")." u "
				."WHERE u.uid=f.posted_by "
				."AND f.msg_id='$msg_id';";

	$result = $xoopsDB->query ($sql);

	if (!$result || $xoopsDB->getRowsNum($result) < 1) {
		/*
			Message not found
		*/
		return _XF_FRM_MESSAGENOTFOUND;
	}

	$content .= "<table border='0' width='100%'>"
	  ."<tr class='bg2'>"
	  ."<td><b>"._XF_G_MESSAGE.": ".$msg_id."</b></td>"
	  ."</tr>";

	$content .= "<TR><TD BGCOLOR='#E3E3E3'>";
	$content .= _XF_G_BY.": ".unofficial_getDBResult($result,0, "uname")."<BR>";
	$content .= _XF_G_DATE.": ".date($sys_datefmt,unofficial_getDBResult($result,0, "date"))."<BR>";
	$content .= _XF_G_SUBJECT.": ". $ts->makeTboxData4Show( htmlspecialchars(unofficial_getDBResult($result,0, "subject")))."<P>";
	$content .= $ts->makeTareaData4Show(unofficial_getDBResult(htmlspecialchars($result,0, 'body')));
	$content .= "</TD></TR></TABLE>";

	/*
		Show entire thread
	*/
	$content .= "<BR>&nbsp;<P><H4>"._XF_FRM_THREADVIEW."</H4>";

	//highlight the current message in the thread list
	$current_message = $msg_id;
	$content .= show_thread(unofficial_getDBResult($result,0, 'thread_id'));

	/*
		Show post followup form
	*/

	$content .= "<P>&nbsp;<P>";
	$content .= "<CENTER><h4>"._XF_FRM_POSTFOLLOWUP."</h4></CENTER>";

	$content .= show_post_form(unofficial_getDBResult($result, 0, 'group_forum_id'),unofficial_getDBResult($result, 0, 'thread_id'), $msg_id, unofficial_getDBResult($result,0, 'subject'));
	$xoopsTpl->assign("content",$content);
  include("../../../footer.php");
	
} else {
	redirect_header($GLOBALS["HTTP_REFERER"],4,"ERROR<br />You must choose a message first");
	exit;
}
?>