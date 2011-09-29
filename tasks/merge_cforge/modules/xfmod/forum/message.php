<?php
	/**
	*
	* SourceForge Forums Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: message.php,v 1.7 2003/12/15 18:09:17 devsupaul Exp $
	*
	*/
	 
	include_once("../../../mainfile.php");
	 
	$langfile = "forum.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
	$icmsOption['template_main'] = 'forum/xfmod_message.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if ($msg_id)
	{
		/*
		Figure out which group this message is in, for the sake of the admin links
		*/
		$result = $icmsDB->query("SELECT fgl.send_all_posts_to,fgl.group_id," ."fgl.allow_anonymous,fgl.forum_name,f.subject,f.group_forum_id,f.thread_id " ."FROM ".$icmsDB->prefix("xf_forum_group_list")." fgl,".$icmsDB->prefix("xf_forum")." f " ."WHERE fgl.group_forum_id=f.group_forum_id " ."AND f.msg_id='$msg_id'");
		 
		if (!$result || $icmsDB->getRowsNum($result) < 1)
		{
			/*
			Message not found
			*/
			redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_FRM_MESSAGENOTFOUND."<br />"._XF_FRM_MESSAGENOTEXIST);
			exit;
		}
		 
		$group_id = unofficial_getDBResult($result, 0, 'group_id');
		$forum_id = unofficial_getDBResult($result, 0, 'group_forum_id');
		$thread_id = unofficial_getDBResult($result, 0, 'thread_id');
		$forum_name = unofficial_getDBResult($result, 0, 'forum_name');
		$allow_anonymous = unofficial_getDBResult($result, 0, 'allow_anonymous');
		$send_all_posts_to = unofficial_getDBResult($result, 0, 'send_all_posts_to');
		 
		$group = group_get_object($group_id);
		$perm = $group->getPermission($icmsUser);
		 
		//group is private
		if (!$group->isPublic())
		{
			//if it's a private group, you must be a member of that group
			if (!$group->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
			{
				redirect_header(ICMS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
				exit;
			}
		}
		 
		include("../../../header.php");
		 
		$icmsTpl->assign("project_title", project_title($group));
		$icmsTpl->assign("project_tabs", project_tabs('news', $group_id));
		$content = "<B style='font-size:16px;align:left;'>".unofficial_getDBResult($result, 0, 'subject')."</strong><br />";
		$content .= forum_show_links($forum_id, $forum_name, $group_id, $perm->isAdmin());
		 
		$content .= "<p>";
		 
		$sql = "SELECT u.uname,f.group_forum_id,f.thread_id,f.subject,f.date,f.body " ."FROM ".$icmsDB->prefix("xf_forum")." f,".$icmsDB->prefix("users")." u " ."WHERE u.uid=f.posted_by " ."AND f.msg_id='$msg_id';";
		 
		$result = $icmsDB->query($sql);
		 
		if (!$result || $icmsDB->getRowsNum($result) < 1)
		{
			/*
			Message not found
			*/
			return _XF_FRM_MESSAGENOTFOUND;
		}
		 
		$content .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>"._XF_G_MESSAGE.": ".$msg_id."</strong></td>" ."</tr>";
		 
		$content .= "<th><td BGCOLOR='#E3E3E3'>";
		$content .= _XF_G_BY.": ".unofficial_getDBResult($result, 0, "uname")."<BR>";
		$content .= _XF_G_DATE.": ".date($sys_datefmt, unofficial_getDBResult($result, 0, "date"))."<BR>";
		$content .= _XF_G_SUBJECT.": ". $ts->makeTboxData4Show(htmlspecialchars(unofficial_getDBResult($result, 0, "subject")))."<p>";
		$content .= $ts->makeTareaData4Show(unofficial_getDBResult(htmlspecialchars($result, 0, 'body')));
		$content .= "</td></th></table>";
		 
		/*
		Show entire thread
		*/
		$content .= "<BR>&nbsp;<p><H4>"._XF_FRM_THREADVIEW."</H4>";
		 
		//highlight the current message in the thread list
		$current_message = $msg_id;
		$content .= show_thread(unofficial_getDBResult($result, 0, 'thread_id'));
		 
		/*
		Show post followup form
		*/
		 
		$content .= "<p>&nbsp;<p>";
		$content .= "<CENTER><h4>"._XF_FRM_POSTFOLLOWUP."</h4></CENTER>";
		 
		$content .= show_post_form(unofficial_getDBResult($result, 0, 'group_forum_id'), unofficial_getDBResult($result, 0, 'thread_id'), $msg_id, unofficial_getDBResult($result, 0, 'subject'));
		$icmsTpl->assign("content", $content);
		include("../../../footer.php");
		 
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "ERROR<br />You must choose a message first");
		exit;
	}
?>