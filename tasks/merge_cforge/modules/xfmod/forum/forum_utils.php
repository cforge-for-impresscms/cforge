<?php
	/**
	*
	* SourceForge Forums Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: forum_utils.php,v 1.4 2004/01/26 18:57:05 devsupaul Exp $
	*
	*/
	function forum_header($group_id, $forum_id, $forum_name, $isAdmin)
	{
		global $sys_datefmt, $icmsForge, $icmsUser, $icmsDB, $ts, $icmsTheme;
		 
		$group = group_get_object($group_id);
		$content = "";
		//meta tag information
		//  $metaTitle=": "._XF_FRM_FORUMS." - ".$group->getPublicName();
		//  $metaDescription=strip_tags($group->getDescription());
		//  $metaKeywords=project_getmetakeywords($group_id);
		 
		/*
		bastardization for news
		Show icon bar unless it's a news forum
		*/
		if ($group_id == $icmsForge['sysnews'])
		{
			//this is a news item, not a regular forum
			if ($forum_id)
			{
				/*
				Show this news item at the top of the page
				*/
				$sql = "SELECT * FROM ".$icmsDB->prefix("xf_news_bytes").",".$icmsDB->prefix("users")." WHERE submitted_by=uid AND forum_id='$forum_id'";
				$result = $icmsDB->query($sql);
				 
				//backwards shim for all "generic news" that used to be submitted
				//as of may, "generic news" is not permitted - only project-specific news
				if (unofficial_getDBResult($result, 0, 'group_id') != $icmsForge['sysnews'])
				{
					$group = group_get_object(unofficial_getDBResult($result, 0, 'group_id'));
					 
					$content .= project_title($group);
					//echo "<B style='font-size:16px;align:left;'>"._XF_FRM_FORUMS."</strong><br />";
					$content .= project_tabs('news', $group->getID());
					 
				}
				else
				{
					$content .= '<H4>XoopsForge <a href="'.ICMS_URL.'/modules/xfmod/news/">'._XF_FRM_NEWS.'</a></H4><p>';
				}
				 
				$content .= '<table width="100%"><tr><td valign="top" width="65%">';
				if (!$result || $icmsDB->getRowsNum($result) < 1)
				{
					$content .= '<h4>Error - '._XF_FRM_NEWSITEMNOTFOUND.'</h4>';
				}
				else
				{
					$content .= '
						<strong>'._XF_G_POSTEDBY.':</strong> '.unofficial_getDBResult($result, 0, 'uname').'<BR>
						<strong>'._XF_G_DATE.':</strong> '. date($sys_datefmt, unofficial_getDBResult($result, 0, 'date')).'<BR>
						<strong>'._XF_G_SUMMARY.':</strong><a href="'.ICMS_URL.'/modules/xfmod/forum/forum.php?forum_id='.unofficial_getDBResult($result, 0, 'forum_id').'">'.$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'summary')).'</a>
						<p>
						'. $ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'details'));
					$content .= '<p>';
				}
				$content .= '</td><td valign="top" width="35%">';
				//      $title = _XF_FRM_LATESTNEWS;
				//      $content = news_show_latest($icmsForge['sysnews'], 5, false);
				//      themesidebox($title, $content);
				$content .= '</td></tr></table>';
			}
		}
		else
		{
			//this is just a regular forum, not a news item
			$group = group_get_object($group_id);
			 
			$content .= project_title($group);
			// echo "<B style='font-size:16px;align:left;'>"._XF_FRM_FORUMS."</strong><br />";
			$content .= project_tabs('news', $group->getID());
		}
		 
		/*
		Show horizontal forum links
		*/
		if ($forum_id && $forum_name)
		{
			$content .= "<p><H4 style='text-align:left;'>"._XF_FRM_DISCUSSIONFORUMS.": <a href='".ICMS_URL."/modules/xfmod/forum/forum.php?forum_id=".$forum_id."'>".$ts->makeTareaData4Show($forum_name)."</a></H4>";
		}
		$content .= "<p><strong>";
		 
		if ($forum_id && $icmsUser)
		{
			// Determine if this user is already monitoring this forum.
			$sql = "SELECT monitor_id FROM ".$icmsDB->prefix("xf_forum_monitored_forums")
			. " WHERE forum_id='".$forum_id."' AND user_id='".$icmsUser->uid()."'";
			$result = $icmsDB->query($sql);
			$content .= "<a href='".ICMS_URL."/modules/xfmod/forum/monitor.php?forum_id=".$forum_id."'>" . "<img width='16' height='15' border='0' src='".ICMS_URL."/modules/xfmod/images/ic/";
			if ($icmsDB->getRowsNum($result) < 1)
			{
				$content .= "check.png' alt='"._XF_FRM_MONITORFORUM."'> "._XF_FRM_MONITORFORUM."</a>";
			}
			else
				{
				$content .= "trash.png' alt='"._XF_FRM_STOPMONITORFORUM."'> "._XF_FRM_STOPMONITORFORUM."</a>";
			}
		}
		if ($isAdmin)
		{
			$content .= " | <a href='".ICMS_URL."/modules/xfmod/forum/admin/?group_id=".$group_id."'>"._XF_G_ADMIN."</a>";
		}
		$content .= "</strong></p>";
		return $content;
	}
	 
	function recursiveDeleteMessage($msg_id, $forum_id)
	{
		global $icmsDB, $ts;
		/*
		Take a message id and recurse, deleting all followups
		*/
		 
		if ($msg_id == '' || $msg_id == '0' || (strlen($msg_id) < 1))
		{
			return 0;
		}
		$sql = "SELECT msg_id " ."FROM ".$icmsDB->prefix("xf_forum")." " ."WHERE is_followup_to='$msg_id' " ."AND group_forum_id='$forum_id'";
		 
		$result = $icmsDB->queryF($sql);
		$rows = $icmsDB->getRowsNum($result);
		$count = 1;
		 
		for($i = 0; $i < $rows; $i++)
		{
			$count += recursiveDeleteMessage(unofficial_getDBResult($result, $i, 'msg_id'), $forum_id);
		}
		 
		$icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_forum")." WHERE msg_id='$msg_id' AND group_forum_id='$forum_id'");
		 
		return $count;
	}
	 
	function forum_link_external($group_id, $forum_name, $forum_server)
	{
		// This function is very Novell-specific, to create a link to twister forums
		global $icmsDB, $feedback;
		$url = "http://".$forum_server."/group/".$forum_name."/readerNoFrame.tpt/@thread@first@f@10@D-D,D@none/@article@first";
		$sql = "INSERT INTO ".$icmsDB->prefix("xf_forum_ext_group_list")."(group_id,forum_name,forum_url) " . "VALUES('$group_id', '$forum_name', '$url')";
		$result = $icmsDB->queryF($sql);
		if (! $result)
		{
			$feedback .= " "._XF_FRM_ERRORADDINGFORUM." ";
		}
		else
		{
			$feedback .= " "._XF_FRM_FORUMADDED." ";
		}
	}
	 
	function forum_create_forum($group_id, $forum_name, $is_public = 1, $create_default_message = 1, $description = '', $verbose = 1)
	{
		global $icmsDB, $ts, $feedback, $icmsUser;
		 
		/*
		Adding forums to this group
		*/
		$sql = "INSERT INTO ".$icmsDB->prefix("xf_forum_group_list")."(group_id,forum_name,is_public,description) " ."VALUES('$group_id','". $ts->makeTboxData4Save($forum_name) ."','$is_public','". $ts->makeTareaData4Save($description) ."')";
		 
		$result = $icmsDB->queryF($sql);
		if ($verbose)
		{
			if (!$result)
			{
				$feedback .= " "._XF_FRM_ERRORADDINGFORUM." ";
			}
			else
			{
				$feedback .= " "._XF_FRM_FORUMADDED." ";
			}
		}
		$forum_id = $icmsDB->getInsertId();
		 
		if ($create_default_message)
		{
			//set up a cheap default message
			$result3 = $icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_forum")." " ."(group_forum_id,posted_by,subject,body,date,is_followup_to,thread_id) " ."VALUES('$forum_id','".$icmsUser->getVar("uid")."','Welcome to ".$ts->makeTboxData4Save($forum_name)."'," ."'Welcome to ".$ts->makeTboxData4Save($forum_name)."','".time()."','0','".get_next_thread_id()."')");
		}
		return $forum_id;
	}
	 
	function forum_show_links($forum_id, $forum_name, $group_id, $isAdmin)
	{
		global $ts, $icmsUser;
		$content = "";
		if ($forum_id && $forum_name)
		{
			$content .= "<p><H4 style='text-align:left;'>"._XF_FRM_DISCUSSIONFORUMS.": <a href='".ICMS_URL."/modules/xfmod/forum/forum.php?forum_id=".$forum_id."'>".$ts->makeTboxData4Show($forum_name)."</a></H4>";
		}
		$content .= "<p><strong>";
		 
		if ($forum_id && $icmsUser)
		{
			$content .= "<a href='".ICMS_URL."/modules/xfmod/forum/monitor.php?forum_id=".$forum_id."'>" ."<img src='".ICMS_URL."/modules/xfmod/images/ic/check.png' width='16' height='15' border'0' alt='"._XF_FRM_MONITORFORUM."'>" ." "._XF_FRM_MONITORFORUM."</a>";
		}
		if ($isAdmin)
		{
			$content .= " | <a href='".ICMS_URL."/modules/xfmod/forum/admin/?group_id=".$group_id."'>"._XF_G_ADMIN."</a>";
		}
		$content .= "</strong></p>";
		return $content;
	}
	 
	function forum_show_a_nested_message(&$result)
	{
		global $icmsDB, $ts;
		/*
		 
		accepts a database result handle to display a single message
		in the format appropriate for the nested messages
		 
		*/
		global $sys_datefmt;
		/*
		See if this message is new or not
		If so, highlite it in bold
		*/
		$ret_val = "<table border='0'>" ."<tr>" ."<td BGCOLOR='#DDDDDD' NOWRAP>"._XF_G_BY.": <a href='".ICMS_URL."/userinfo.php?uid=" .$result['uid']."'>" .$result['uname']."</a>" ."(".$result['name']. ") " ."<BR><a href='".ICMS_URL."/modules/xfmod/forum/message.php?msg_id=" .$result['msg_id']."'>" ."<img src='".ICMS_URL."/modules/xfmod/images/msg.gif' width='10' height='12' border'0' alt='"._XF_G_REPLY."'>" .$ts->makeTboxData4Show(htmlspecialchars($result['subject'])) ." [ "._XF_G_REPLY." ]</a> &nbsp; " ."<BR>". date($sys_datefmt, $result['date'])
		."</td></tr>" ."<tr><td>" .$ts->makeTareaData4Show(htmlspecialchars($result['body']))."</td>" ."</tr>" ."</table>";
		return $ret_val;
	}
	 
	function forum_show_nested_messages(&$msg_arr, $msg_id)
	{
		global $total_rows, $sys_datefmt;
		 
		$rows = count($msg_arr[$msg_id]);
		$ret_val = '';
		 
		if ($msg_arr[$msg_id] && $rows > 0)
		{
			$ret_val .= "<UL>";
			 
			/*
			 
			iterate and show the messages in this result
			 
			for each message, recurse to show any submessages
			 
			*/
			for($i = ($rows - 1); $i >= 0; $i--)
			{
				//      increment the global total count
				$total_rows++;
				 
				//      show the actual nested message
				$ret_val .= forum_show_a_nested_message($msg_arr[$msg_id][$i])."<p>";
				 
				if ($msg_arr[$msg_id][$i]['has_followups'] > 0)
				{
					//      Call yourself if there are followups
					$ret_val .= forum_show_nested_messages($msg_arr, $msg_arr[$msg_id][$i]['msg_id']);
				}
			}
			$ret_val .= "</UL>";
		}
		else
		{
			$ret_val .= "<p><strong>no messages actually follow up to $msg_id</strong>";
		}
		 
		return $ret_val;
	}
	 
	function show_thread($thread_id)
	{
		global $icmsDB, $ts, $total_rows, $sys_datefmt, $is_followup_to, $subject, $forum_id, $current_message;
		 
		$sql = "SELECT f.group_forum_id,u.uname,u.name,f.has_followups, " ."u.uid,f.msg_id,f.subject,f.thread_id, " ."f.body,f.date,f.is_followup_to " ."FROM ".$icmsDB->prefix("xf_forum")." f,".$icmsDB->prefix("users")." u " ."WHERE f.thread_id='$thread_id' " ."AND u.uid=f.posted_by " ."ORDER BY msg_id ASC";
		 
		$result = $icmsDB->query($sql);
		 
		$total_rows = 0;
		 
		if (!$result || $icmsDB->getRowsNum($result) < 1)
		{
			return _XF_FRM_BROKENTHREAD;
		}
		else
		{
			/*
			Build associative array containing row information
			*/
			while ($row = $icmsDB->fetchArray($result))
			{
				$msg_arr["$row[is_followup_to]"][] = $row;
			}
			 
			/*
			Build table header row
			*/
			$ret_val .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<th><strong>"._XF_FRM_THREAD."</strong></th>" ."<th><strong>"._XF_FRM_AUTHOR."</strong></th>" ."<th><strong>"._XF_FRM_DATE."</strong></th>" ."</tr>";
			 
			reset($msg_arr["0"]);
			$thread = $msg_arr["0"][0];
			 
			$ret_val .= "<th class='".($i++%2 > 0?"bg1":"bg3")."'><td>" .(($current_message != $thread['msg_id'])?"<a href='".ICMS_URL."/modules/xfmod/forum/message.php?msg_id=".$thread['msg_id']."'>":"")
			."<img src='".ICMS_URL."/modules/xfmod/images/msg.gif' width='10' height='12' border'0' alt='message'>";
			/*
			See if this message is new or not
			*/
			$ret_val .= $ts->makeTboxData4Show($thread['subject'])."</a></td>" ."<td>".$thread['uname']."</td>" ."<td>".date($sys_datefmt, $thread['date'])."</td></tr>";
			 
			/*
			Now call the recursive function to show nested messages
			*/
			if ($thread['has_followups'] > 0)
			{
				$ret_val .= show_submessages($msg_arr, $thread['msg_id'], 1);
			}
			 
			/*
			end table
			*/
			$ret_val .= "</table>";
		}
		return $ret_val;
	}
	 
	function show_submessages($msg_arr, $msg_id, $level)
	{
		/*
		Recursive. Selects this message's id in this thread,
		then checks if any messages are nested underneath it.
		If there are, it calls itself, incrementing $level
		$level is used for indentation of the threads.
		*/
		global $total_rows, $sys_datefmt, $forum_id, $current_message, $ts;
		 
		$rows = count($msg_arr[$msg_id]);
		 
		if ($rows > 0)
		{
			for($i = ($rows-1); $i >= 0; $i--)
			{
				/*
				Is this row's background shaded or not?
				*/
				$total_rows++;
				 
				$ret_val .= "<th class='".($i%2 > 0?"bg1":"bg3")."'><td NOWRAP>";
				/*
				How far should it indent?
				*/
				for($i2 = 0; $i2 < $level; $i2++)
				{
					$ret_val .= " &nbsp; &nbsp; &nbsp; ";
				}
				 
				/*
				If it this is the message being displayed, don't show a link to it
				*/
				 
				$ret_val .= (($current_message != $msg_arr[$msg_id][$i]['msg_id'])? "<a href='".ICMS_URL."/modules/xfmod/forum/message.php?msg_id=".$msg_arr[$msg_id][$i]['msg_id']."'>":
				"")
				."<img src='".ICMS_URL."/modules/xfmod/images/msg.gif' width='10' height='12' border'0' alt='message'>";
				/*
				See if this message is new or not
				*/
				$ret_val .= $ts->makeTboxData4Show($msg_arr[$msg_id][$i]['subject'])."</a></td>" ."<td>".$msg_arr[$msg_id][$i]['uname']."</td>" ."<td>".date($sys_datefmt, $msg_arr[$msg_id][$i]['date'])."</td></tr>";
				 
				if ($msg_arr[$msg_id][$i]['has_followups'] > 0)
				{
					/*
					Call yourself, incrementing the level
					*/
					$ret_val .= show_submessages($msg_arr, $msg_arr[$msg_id][$i]['msg_id'], ($level+1));
				}
			}
		}
		return $ret_val;
	}
	 
	function get_next_thread_id()
	{
		global $icmsDB;
		 
		$result = $icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_forum_thread_id")." VALUES('')");
		 
		if (!$result)
		{
			$feedback .= " ".$icmsDB->error()." ";
			return false;
		}
		else
		{
			return $icmsDB->getInsertId();
		}
	}
	 
	/**
	* assumes $allow_anonymous var is setup correctly
	* added checks and tests to allow anonymous posting
	*/
	function post_message($thread_id, $is_followup_to, $subject, $body, $group_forum_id)
	{
		global $icmsDB, $ts, $feedback, $icmsUser;
		 
		if (!$icmsUser)
		{
			$feedback = _XF_FRM_COULDPOSTIFLOGGEDIN;
			return false;
		}
		 
		if (!$group_forum_id)
		{
			$feedback = _XF_FRM_TRYINGTOPOSTWITHOUTID;
			return false;
		}
		if (!$body || !$subject)
		{
			$feedback = _XF_FRM_MUSTINCLUDEBODYANDSUB;
			return false;
		}
		 
		if (!$thread_id)
		{
			$thread_id = get_next_thread_id();
			$is_followup_to = 0;
			if (!$thread_id)
			{
				$feedback .= " "._XF_FRM_GETTINGNEXTIDFAILED." ";
				return false;
			}
		}
		else
		{
			if ($is_followup_to)
			{
				//
				// increment the parent's followup count if necessary
				//
				$res2 = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_forum")." WHERE msg_id='$is_followup_to' AND group_forum_id='$group_forum_id'");
				 
				if ($icmsDB->getRowsNum($res2) > 0)
				{
					//
					// get thread_id from the parent's row,
					// which is more trustworthy than the HTML form
					//
					$thread_id = unofficial_getDBResult($res2, 0, 'thread_id');
					 
					//
					// now we need to update the first message in
					// this thread with the current time
					//
					$res4 = $icmsDB->queryF("UPDATE ".$icmsDB->prefix("xf_forum")." SET most_recent_date='". time() ."' " ."WHERE thread_id='$thread_id' AND is_followup_to='0'");
					 
					if (!$res4)
					{
						$feedback = _XF_FRM_COULDNOTUPDATEPARENTTIME;
						return false;
					}
					else
					{
						//
						// mark the parent with followups as an optimization later
						//
						$res3 = $icmsDB->queryF("UPDATE ".$icmsDB->prefix("xf_forum")." SET has_followups='1',most_recent_date='". time() ."' " ."WHERE msg_id='$is_followup_to'");
						if (!$res3)
						{
							$feedback = _XF_FRM_COULDNOTUPDATEPARENT;
							return false;
						}
					}
				}
				else
				{
					$feedback = _XF_FRM_TRYINGTOFOLLOWUPNOTEXIST;
					return false;
				}
			}
			else
			{
				//should never happen except with shoddy
				//browsers or mucking with the HTML form
				$feedback = _XF_FRM_NOFOLLOWUPIDPRESENT;
				return false;
			}
		}
		 
		$sql = "INSERT INTO ".$icmsDB->prefix("xf_forum")."(group_forum_id,posted_by,subject,body,date,is_followup_to,thread_id,most_recent_date) " ."VALUES('$group_forum_id', '".$icmsUser->getVar("uid")."', '". $ts->makeTboxData4Save($subject) ."', '". $ts->makeTareaData4Save($body) ."', '". time() ."','$is_followup_to','$thread_id','". time() ."')";
		$result = $icmsDB->queryF($sql);
		 
		if (!$result)
		{
			$feedback .= " "._XF_FRM_POSTINGFAILED." ".$icmsDB->error;
			return false;
		}
		else
		{
			$msg_id = $icmsDB->getInsertId();
			 
			if (!$msg_id)
			{
				$feedback .= _XF_FRM_FAILEDTOGETINSERTID;
				return false;
			}
			else
			{
				$icmsDB->queryF("UPDATE ".$icmsDB->prefix("users")." SET posts=posts+1 WHERE uname = '".$icmsUser->getVar("uname")."'");
				handle_monitoring($group_forum_id, $msg_id);
				 
				$feedback .= " "._XF_FRM_MESSAGEPOSTED." ";
				return true;
			}
		}
	}
	 
	/**
	* assumes $allow_anonymous var is set up
	* added checks and tests to allow anonymous posting
	*/
	function show_post_form($forum_id, $thread_id = 0, $is_followup_to = 0, $subject = "")
	{
		global $icmsUser, $ts;
		$content = "";
		if (!$icmsUser)
		{
			$content .= "<CENTER>";
			$content .= "<H4><FONT COLOR='RED'>"._XF_FRM_COULDPOSTIFLOGGEDIN."</FONT></H4>";
			$content .= "</CENTER>";
		}
		else
		{
			if ($subject)
			{
				//if this is a followup, put a RE: before it if needed
				if (!eregi('RE:', $subject, $test))
				{
					$subject = "RE: ".$subject;
				}
			}
			 
			$content .= "<form action='".ICMS_URL."/modules/xfmod/forum/forum.php' METHOD='POST'>";
			$content .= "<input type='hidden' name='post_message' value='y'>";
			$content .= "<input type='hidden' name='forum_id' value='$forum_id'>";
			$content .= "<input type='hidden' name='thread_id' value='$thread_id'>";
			$content .= "<input type='hidden' name='msg_id' value='$is_followup_to'>";
			$content .= "<input type='hidden' name='is_followup_to' value='$is_followup_to'>";
			$content .= "<table>";
			$content .= "<tr>";
			$content .= "<td><strong>"._XF_G_SUBJECT.":</strong><BR>";
			$content .= "<input type='text' name='subject' value='".$ts->makeTboxData4Edit($subject)."' size='45' maxlength='45'>";
			$content .= "</td></tr>";
			$content .= "<tr><td><strong>"._XF_G_MESSAGE.":</strong><BR>";
			$content .= "<textarea name='body' value='' rows='10' cols='45' wrap='soft'></textarea>";
			$content .= "</td></tr>";
			$content .= "<tr><td align='middle'>";
			$content .= "<strong style='font-color : #FF0000;'>"._XF_FRM_HTMLDISPLAYSASTEXT."</strong>";
			$content .= "<p>";
			$content .= "<input type='submit' name='submit' value='"._XF_FRM_POSTCOMMENT."'>";
			$content .= "</td></tr></table>";
			$content .= "</form>";
			 
		}
		return $content;
	}
	 
	/**
	* assumes $send_all_posts_to var is set up
	*/
	function handle_monitoring($forum_id, $msg_id)
	{
		global $icmsDB, $ts, $feedback, $send_all_posts_to, $icmsForge;
		/*
		Checks to see if anyone is monitoring this forum
		If someone is, it sends them the message in email format
		*/
		$sql = "SELECT u.email " ."FROM ".$icmsDB->prefix("xf_forum_monitored_forums")." fmf,".$icmsDB->prefix("users")." u " ."WHERE fmf.user_id=u.uid AND fmf.forum_id='$forum_id'";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		if (($result && $rows > 0) || $send_all_posts_to)
		{
			 
			$bcc_arr = util_result_column_to_array($result);
			if ($send_all_posts_to)
			{
				$bcc_arr[sizeof($bcc_arr)] = $send_all_posts_to;
			}
			 
			$sql = "SELECT g.unix_group_name,u.uname,fgl.forum_name," ."f.group_forum_id,f.thread_id,f.subject,f.date,f.body " ."FROM ".$icmsDB->prefix("xf_forum")." f,".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_forum_group_list")." fgl,".$icmsDB->prefix("xf_groups")." g " ."WHERE u.uid=f.posted_by " ."AND fgl.group_forum_id=f.group_forum_id " ."AND g.group_id=fgl.group_id " ."AND f.msg_id='$msg_id'";
			 
			$result = $icmsDB->query($sql);
			 
			if ($result && $icmsDB->getRowsNum($result) > 0)
			{
				$uname = unofficial_getDBResult($result, 0, 'uname');
				$body = unofficial_getDBResult($result, 0, 'body');
				$unix_group_name = unofficial_getDBResult($result, 0, 'unix_group_name');
				$forum_name = unofficial_getDBResult($result, 0, 'forum_name');
				$subject = unofficial_getDBResult($result, 0, 'subject');
				 
				 
				$message = frmGetMonitorMessage($msg_id, $uname, $ts->makeTareaData4Edit($body), $forum_id, $unix_group_name, $ts->makeTboxData4Show($forum_name), $ts->makeTboxData4Show($subject));
				 
				$r = xoopsForgeMail($icmsForge['noreply'], $icmsConfig['sitename'], $message['subject'], $message['body'], array($icmsForge['noreply']), $bcc_arr);
				if ($r)
				{
					$feedback .= ' '._XF_FRM_EMAILSENT.' - '._XF_FRM_PEOPLEMONITORING.' ';
				}
				else
				{
					$feedback .= ' '._XF_FRM_EMAILNOTSENT.' - '._XF_FRM_PEOPLEMONITORING.' ';
				}
			}
			else
			{
				$feedback .= ' '._XF_FRM_EMAILNOTSENT.' - '._XF_FRM_PEOPLEMONITORING.' ';
				$feedback .= $icmsDB->error();
			}
		}
		else
		{
			$feedback .= ' '._XF_FRM_EMAILNOTSENT.' - '._XF_FRM_NOONEMONITORING.' ';
			$feedback .= $icmsDB->error();
		}
	}
	 
	function validate_forum_name($name)
	{
		// no spaces
		if (strrpos($name, ' ') > 0)
		{
			$GLOBALS['register_error'] = "There cannot be any spaces in the forum name.";
			return false;
		}
		 
		// min and max length
		if (strlen($name) < 2)
		{
			$GLOBALS['register_error'] = "Name is too short. It must be at least 2 characters.";
			return false;
		}
		if (strlen($name) > 40)
		{
			$GLOBALS['register_error'] = "Name is too long. It must be less than 40 characters.";
			return false;
		}
		 
		//valid characters
		if (!ereg('^[a-z][-a-z0-9_]+$', $name))
		{
			$GLOBALS['register_error'] = "The name may only contain Letters, Numbers, Dashes, or Underscores.";
			return false;
		}
		 
		return true;
	}
	 
?>