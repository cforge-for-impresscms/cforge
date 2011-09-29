<?php
/**
*
* SourceForge Forums Facility
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001(c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: forum.php,v 1.7 2004/01/26 18:57:05 devsupaul Exp $
*
*/
 
/*
 
Forum written 11/99 by Tim Perdue
Massive re-write 7/2000 by Tim Perdue(nesting/multiple views/etc)
 
Massive optimization 11/00 to eliminate recursive queries
 
*/

include_once("../../../mainfile.php");

$langfile = "forum.php";
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$icmsOption['template_main'] = 'forum/xfmod_forum.html';
 
//$forum_id =(isset($_POST['forum_id'])) ? $_POST['forum_id'] : $_GET['forum_id'];
 
if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);

if ($forum_id)
{
	/*
	Set up global vars that are expected by some forum functions
	*/
	$result = $icmsDB->queryF("SELECT group_id,forum_name,is_public,allow_anonymous,send_all_posts_to " ."FROM ".$icmsDB->prefix("xf_forum_group_list")." " ."WHERE group_forum_id='$forum_id'");
	 
	if (!$result || $icmsDB->getRowsNum($result) < 1)
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "ERROR<br />"._XF_FRM_FORUMNOTFOUND." ".$icmsDB->error());
		exit;
	}
	$group_id = unofficial_getDBResult($result, 0, 'group_id');
	$forum_name = unofficial_getDBResult($result, 0, 'forum_name');
	$allow_anonymous = unofficial_getDBResult($result, 0, 'allow_anonymous');
	$send_all_posts_to = unofficial_getDBResult($result, 0, 'send_all_posts_to');
	 
	//
	// Set up local objects
	//
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
	 
	if ($icmsUser)
	{
		$perm = $group->getPermission($icmsUser);
	}
	 
	//private forum check
	if (unofficial_getDBResult($result, 0, 'is_public') != 1)
	{
		if (!$icmsUser || ($icmsUser && !$perm->isMember()))
		{
			/*
			If this is a private forum, kick 'em out
			*/
			redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_G_PERMISSIONDENIED."<br />"._XF_FRM_FORUMISRESTRICTED);
			exit;
		}
	}
	 
	/*
	if necessary, insert a new message into the forum
	*/
	if ($post_message)
	{
		if (!post_message($thread_id, $is_followup_to, $subject, $body, $forum_id))
		{
			redirect_header($_SERVER["HTTP_REFERER"], 4, "ERROR<br />".$feedback);
			exit;
		}
		else
		{
			$feedback = _XF_FRM_MESSAGEPOSTED;
			$style = "";
			$thread_id = "";
		}
	}
	 
	/*
	set up some defaults if they aren't provided
	*/
	if ((!$offset) || ($offset < 0))
	{
		$offset = 0;
	}
	if (!$max_rows || $max_rows < 5)
	{
		$max_rows = 25;
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	$icmsTpl->assign("header", forum_header($group_id, $forum_id, $forum_name, $perm->isAdmin()));
	 
	 
	/**
	*
	* Forum styles include Nested, threaded, flat, ultimate
	*
	* threaded indents and shows subjects/authors of all messages/followups
	* nested indents and shows the entirety of all messages/followups
	* flat shows entiretly of messages in date order descending
	* ultimate is based roughly on "Ultimate BB"
	*
	*/
	if (!$thread_id)
	{
		//create a pop-up select box listing the forums for this project
		//determine if this person can see private forums or not
		if ($icmsUser && $group->isMemberOfGroup($icmsUser))
		{
			$public_flag = "0,1";
		}
		else
		{
			$public_flag = "1";
		}
		if ($group_id == $icmsForge['sysnews'])
		{
			$forum_popup = '<input type="hidden" name="forum_id" value="'.$forum_id.'">';
		}
		else
		{
			$res = $icmsDB->query("SELECT group_forum_id,forum_name" ." FROM ".$icmsDB->prefix("xf_forum_group_list")
			." WHERE group_id='$group_id' AND is_public IN($public_flag)");
			 
			$vals = util_result_column_to_array($res, 0);
			$texts = util_result_column_to_array($res, 1);
			 
			$forum_popup = html_build_select_box_from_arrays($vals, $texts, 'forum_id', $forum_id, false);
		}
		 
		//create a pop-up select box showing options for max_row count
		$vals = array(25, 50, 75, 100);
		$texts = array(_XF_FRM_SHOW.' 25', _XF_FRM_SHOW.' 50', _XF_FRM_SHOW.' 75', _XF_FRM_SHOW.' 100');
		 
		$max_row_popup = html_build_select_box_from_arrays($vals, $texts, 'max_rows', $max_rows, false);
		 
		//now show the popup boxes in a form
		$ret_val .= "<table border='0' width='50%'>" ."<form action='".$_SERVER['PHP_SELF']."' METHOD='GET'>" ."<input type='hidden' name='set' value='custom'>" ."<th><td><FONT size='-1'>".$forum_popup ."</td><td><FONT size='-1'>".$max_row_popup ."</td><td><FONT size='-1'><input type='submit' name='submit' value='"._XF_FRM_CHANGEVIEW."'>" ."</td></th>" ."</table></form>";
	}
	 
	if ($thread_id)
	{
		$sql = "SELECT u.uname,u.name,f.has_followups, " ."u.uid,f.msg_id,f.subject,f.thread_id, " ."f.body,f.date,f.is_followup_to,f.most_recent_date,f.group_forum_id " ."FROM ".$icmsDB->prefix("xf_forum")." f,".$icmsDB->prefix("users")." u " ."WHERE f.group_forum_id='$forum_id' " ."AND f.thread_id='$thread_id' " ."AND u.uid=f.posted_by " ."ORDER BY f.most_recent_date DESC";
		 
		$result = $icmsDB->query($sql, ($max_rows + 25), $offset);
		 
		$feedback = $icmsDB->error();
		 
		while ($row = $icmsDB->fetchArray($result))
		{
			$msg_arr["$row[is_followup_to]"][] = $row;
		}
		 
		$rows = count($msg_arr[0]);
		if ($rows > $max_rows)
		{
			$rows = $max_rows;
		}
		$i = 0;
		while (($i < $rows) && ($total_rows < $max_rows))
		{
			$thread = $msg_arr["0"][$i];
			 
			$total_rows++;
			/*
			New slashdot-inspired nested threads,
			showing all submessages and bodies
			*/
			$ret_val .= forum_show_a_nested_message($thread)."<BR>";
			 
			if ($thread['has_followups'] > 0)
			{
				//show submessages for this message
				$ret_val .= forum_show_nested_messages($msg_arr, $thread['msg_id']);
			}
			$i++;
		}
		 
	}
	else
	{
		/*
		This is the view that is most similar to the "Ultimate BB view"
		*/
		$sql = "SELECT f.most_recent_date,u.uname,u.name,u.uid,f.msg_id,f.subject,f.thread_id," ."(COUNT(f2.thread_id)-1) AS followups, MAX(f2.date) AS recent " ."FROM ".$icmsDB->prefix("xf_forum")." f, ".$icmsDB->prefix("xf_forum")." f2, ".$icmsDB->prefix("users")." u " ."WHERE f.group_forum_id='$forum_id' " ."AND f.is_followup_to=0 " ."AND u.uid=f.posted_by " ."AND f.thread_id=f2.thread_id " ."GROUP BY f.most_recent_date,u.uname,u.name,u.uid,f.msg_id,f.subject,f.thread_id " ."ORDER BY f.most_recent_date DESC";
		 
		$result = $icmsDB->query($sql, ($max_rows + 1), $offset);
		 
		$feedback .= $icmsDB->error();
		 
		$ret_val .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>"._XF_FRM_TOPIC."</strong></td>" ."<td><strong>"._XF_FRM_TOPICSTARTER."</strong></td>" ."<td><strong>"._XF_FRM_REPLIES."</strong></td>" ."<td><strong>"._XF_FRM_LASTPOST."</strong></td>" ."</tr>";
		 
		$i = 0;
		while (($row = $icmsDB->fetchArray($result)) && ($i < $max_rows))
		{
			$ret_val .= "<th class='".($i%2 > 0?"bg1":"bg3")."'>" ."<td><a href='".ICMS_URL."/modules/xfmod/forum/forum.php?thread_id=" .$row['thread_id']."&forum_id=".$forum_id."'>" ."<img src='".ICMS_URL."/modules/xfmod/images/ic/cfolder15.png' width='15' height='13' border='0' alt='thread'> &nbsp; ";
			 
			/*
			show the subject and poster
			*/
			$ret_val .= $row['subject']."</a></td>" ."<td>".$row['uname']."</td>" ."<td>".$row['followups']."</td>" ."<td>".date($sys_datefmt, $row['recent'])."</td></th>";
			$i++;
		}
		 
		$ret_val .= "</table>";
	}
	/*
	This code puts the nice next/prev.
	*/
	$ret_val .= "<table width='100%' border='0'>" ."<th BGCOLOR='#EEEEEE'><td width='50%'>";
	 
	if ($offset != 0)
	{
		$ret_val .= "<FONT face='Arial, Helvetica' size='3' STYLE='text-decoration: none'>" ."<a href='javascript:history.back()'><strong>" ."<img src='".ICMS_URL."/modules/xfmod/images/t2.gif' width='15' height='15' border='0' align='middle' alt='"._XF_FRM_PREVIOUSMESSAGES."'> "._XF_FRM_PREVIOUSMESSAGES."</a></strong></FONT>";
	}
	else
	{
		$ret_val .= "&nbsp;";
	}
	 
	$ret_val .= "</td><td>&nbsp;</td><td align='right' width='50%'>";
	 
	if ($icmsDB->getRowsNum($result) > $max_rows)
	{
		$ret_val .= "<FONT face='Arial, Helvetica' size='3' STYLE='text-decoration: none'><strong>" ."<a href='".ICMS_URL."/modules/xfmod/forum/forum.php?max_rows=".$max_rows."&offset=".($offset+$i)."&forum_id=".$forum_id."'>" ."<strong>"._XF_FRM_NEXTMESSAGES." " ."<img src='".ICMS_URL."/modules/xfmod/images/t2.gif' width='15' height='15' border='0' align='middle' alt='"._XF_FRM_NEXTMESSAGES."'></a>";
	}
	else
	{
		$ret_val .= "&nbsp;";
	}
	$ret_val .= "</table>";
	 
	 
	$ret_val .= "<p>&nbsp;<p>";
	 
	if ($thread_id)
	{
		//
		// Viewing a particular thread in nested view
		//
		$ret_val .= "<CENTER><H4>"._XF_FRM_POSTMESSAGETOTHREAD.":</H4></CENTER>";
		$ret_val .= show_post_form($forum_id, $thread_id, $msg_arr["0"][0]['msg_id'], $msg_arr["0"][0]['subject']);
	}
	else
	{
		//
		// Viewing an entire message forum in a given format
		//
		$ret_val .= "<CENTER><H4>"._XF_FRM_STARTNEWTHREAD.":</H4></CENTER>";
		$ret_val .= show_post_form($forum_id);
	}
	$icmsTpl->assign("feedback", $feedback);
	$icmsTpl->assign("content", $ret_val);
	include(ICMS_ROOT_PATH."/footer.php");
	 
}
else
{
	redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_FRM_MUSTSPECIFYFORUM);
	exit;
}
?>