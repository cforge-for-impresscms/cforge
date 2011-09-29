<?php
/**
  *
  * SourceForge Forums Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: forum_utils.php,v 1.4 2004/01/26 18:57:05 devsupaul Exp $
  *
  */
function forum_header($group_id, $forum_id, $forum_name, $isAdmin) {
  global $sys_datefmt,$xoopsForge, $xoopsUser, $xoopsDB, $ts, $xoopsTheme;
  
  $group = &group_get_object ($group_id);
  $content = "";
  //meta tag information
//  $metaTitle=": "._XF_FRM_FORUMS." - ".$group->getPublicName();
//  $metaDescription=strip_tags($group->getDescription());
//  $metaKeywords=project_getmetakeywords($group_id);  	  
  
  /*
    bastardization for news
    Show icon bar unless it's a news forum
  */
  if ($group_id == $xoopsForge['sysnews']) {
    //this is a news item, not a regular forum
    if ($forum_id) {
      /*
	      Show this news item at the top of the page
      */
      $sql = "SELECT * FROM ".$xoopsDB->prefix("xf_news_bytes").",".$xoopsDB->prefix("users")." WHERE submitted_by=uid AND forum_id='$forum_id'";
      $result = $xoopsDB->query($sql);

      //backwards shim for all "generic news" that used to be submitted
      //as of may, "generic news" is not permitted - only project-specific news
      if (unofficial_getDBResult($result,0,'group_id') != $xoopsForge['sysnews']) {
        $group =& group_get_object(unofficial_getDBResult($result,0,'group_id'));

        $content .= project_title($group);
	//echo "<B style='font-size:16px;align:left;'>"._XF_FRM_FORUMS."</B><br />";
        $content .= project_tabs ('news', $group->getID());

      } else {
        $content .= '<H4>XoopsForge <A HREF="'.XOOPS_URL.'/modules/xfmod/news/">'._XF_FRM_NEWS.'</A></H4><P>';
      }

      $content .= '<TABLE width="100%"><TR><TD VALIGN="TOP" WIDTH="65%">';
      if (!$result || $xoopsDB->getRowsNum($result) < 1) {
        $content .= '<h4>Error - '._XF_FRM_NEWSITEMNOTFOUND.'</h4>';
      } else {
        $content .= '
          <B>'._XF_G_POSTEDBY.':</B> '.unofficial_getDBResult($result,0,'uname').'<BR>
	        <B>'._XF_G_DATE.':</B> '. date($sys_datefmt, unofficial_getDBResult($result,0,'date')).'<BR>
          <B>'._XF_G_SUMMARY.':</B><A HREF="'.XOOPS_URL.'/modules/xfmod/forum/forum.php?forum_id='.unofficial_getDBResult($result,0,'forum_id').'">'.$ts->makeTboxData4Show( unofficial_getDBResult($result,0,'summary') ).'</A>
          <P>
          '. $ts->makeTareaData4Show( unofficial_getDBResult($result,0,'details'));
        $content .= '<P>';
      }
      $content .= '</TD><TD VALIGN="TOP" WIDTH="35%">';
//      $title = _XF_FRM_LATESTNEWS;
//      $content = news_show_latest($xoopsForge['sysnews'], 5, false);
//      themesidebox($title, $content);
     $content .= '</TD></TR></TABLE>';
    }
  } else {
    //this is just a regular forum, not a news item
    $group =& group_get_object($group_id);

    $content .= project_title($group);
   // echo "<B style='font-size:16px;align:left;'>"._XF_FRM_FORUMS."</B><br />";
    $content .= project_tabs ('news', $group->getID());
  }

  /*
    Show horizontal forum links
  */
  if ($forum_id && $forum_name) {
    $content .= "<P><H4 style='text-align:left;'>"._XF_FRM_DISCUSSIONFORUMS.": <A HREF='".XOOPS_URL."/modules/xfmod/forum/forum.php?forum_id=".$forum_id."'>".$ts->makeTareaData4Show( $forum_name )."</A></H4>";
  }
  $content .= "<P><B>";

  if ($forum_id && $xoopsUser ) {
  	// Determine if this user is already monitoring this forum.
  	$sql = "SELECT monitor_id FROM ".$xoopsDB->prefix("xf_forum_monitored_forums")
  		. " WHERE forum_id='".$forum_id."' AND user_id='".$xoopsUser->uid()."'";
  	$result = $xoopsDB->query($sql);
    $content .= "<A HREF='".XOOPS_URL."/modules/xfmod/forum/monitor.php?forum_id=".$forum_id."'>"
    	. "<img width='16' height='15' border='0' src='".XOOPS_URL."/modules/xfmod/images/ic/";
  	if ( $xoopsDB->getRowsNum($result) < 1 )
  	{
  		$content .= "check.png' alt='"._XF_FRM_MONITORFORUM."'> "._XF_FRM_MONITORFORUM."</A>";
	}
	else
	{
		$content .= "trash.png' alt='"._XF_FRM_STOPMONITORFORUM."'> "._XF_FRM_STOPMONITORFORUM."</a>";
	}
  }
  if($isAdmin){
	  $content .= " | <A HREF='".XOOPS_URL."/modules/xfmod/forum/admin/?group_id=".$group_id."'>"._XF_G_ADMIN."</A>";
  }
  $content .= "</B></P>";
  return $content;
}

function recursiveDeleteMessage($msg_id, $forum_id) {
  global $xoopsDB, $ts;
	/*
		Take a message id and recurse, deleting all followups
	*/

	if ($msg_id == '' || $msg_id == '0' || (strlen($msg_id) < 1)) {
		return 0;
	}
	$sql = "SELECT msg_id "
	      ."FROM ".$xoopsDB->prefix("xf_forum")." "
		    ."WHERE is_followup_to='$msg_id' "
		    ."AND group_forum_id='$forum_id'";

	$result = $xoopsDB->queryF($sql);
	$rows = $xoopsDB->getRowsNum($result);
	$count = 1;

	for ($i = 0; $i < $rows; $i++) {
		$count += recursiveDeleteMessage(unofficial_getDBResult($result,$i,'msg_id'), $forum_id);
	}

	$xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_forum")." WHERE msg_id='$msg_id' AND group_forum_id='$forum_id'");

	return $count;
}

function forum_link_external($group_id,$forum_name,$forum_server)
{
  // This function is very Novell-specific, to create a link to twister forums
  global $xoopsDB, $feedback;
  $url = "http://".$forum_server."/group/".$forum_name."/readerNoFrame.tpt/@thread@first@f@10@D-D,D@none/@article@first";
  $sql = "INSERT INTO ".$xoopsDB->prefix("xf_forum_ext_group_list")." (group_id,forum_name,forum_url) "
     . "VALUES ('$group_id', '$forum_name', '$url')";
  $result = $xoopsDB->queryF($sql);
  if ( ! $result )
    {
      $feedback .= " "._XF_FRM_ERRORADDINGFORUM." ";
    }
  else
    {
      $feedback .= " "._XF_FRM_FORUMADDED." ";
    }
}

function forum_create_forum($group_id,$forum_name,$is_public=1,$create_default_message=1,$description='',$verbose=1) {
  global $xoopsDB, $ts, $feedback, $xoopsUser;

	/*
		Adding forums to this group
	*/
	$sql = "INSERT INTO ".$xoopsDB->prefix("xf_forum_group_list")." (group_id,forum_name,is_public,description) "
	      ."VALUES ('$group_id','". $ts->makeTboxData4Save($forum_name) ."','$is_public','". $ts->makeTareaData4Save($description) ."')";

	$result = $xoopsDB->queryF($sql);
	if ( $verbose )
	{
		if (!$result) {
			$feedback .= " "._XF_FRM_ERRORADDINGFORUM." ";
		} else {
			$feedback .= " "._XF_FRM_FORUMADDED." ";
		}
	}
	$forum_id = $xoopsDB->getInsertId();

	if ($create_default_message) {
		//set up a cheap default message
		$result3 = $xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_forum")." "
		                      ."(group_forum_id,posted_by,subject,body,date,is_followup_to,thread_id) "
						              ."VALUES ('$forum_id','".$xoopsUser->getVar("uid")."','Welcome to ".$ts->makeTboxData4Save($forum_name)."',"
						              ."'Welcome to ".$ts->makeTboxData4Save($forum_name)."','".time()."','0','".get_next_thread_id()."')");
	}
	return $forum_id;
}

function forum_show_links ($forum_id, $forum_name, $group_id, $isAdmin)
{
	global $ts, $xoopsUser;
	$content = "";
	if ($forum_id && $forum_name) {
		$content .= "<P><H4 style='text-align:left;'>"._XF_FRM_DISCUSSIONFORUMS.": <A HREF='".XOOPS_URL."/modules/xfmod/forum/forum.php?forum_id=".$forum_id."'>".$ts->makeTboxData4Show($forum_name)."</A></H4>";
	}
	$content .= "<P><B>";

	if ($forum_id && $xoopsUser ) {
		$content .= "<A HREF='".XOOPS_URL."/modules/xfmod/forum/monitor.php?forum_id=".$forum_id."'>"
		    ."<img src='".XOOPS_URL."/modules/xfmod/images/ic/check.png' width='16' height='15' border'0' alt='"._XF_FRM_MONITORFORUM."'>"
				." "._XF_FRM_MONITORFORUM."</A>";
	}
	if($isAdmin){
		$content .= " | <A HREF='".XOOPS_URL."/modules/xfmod/forum/admin/?group_id=".$group_id."'>"._XF_G_ADMIN."</A>";
	}
	$content .= "</B></P>";
	return $content;
}

function forum_show_a_nested_message ( &$result ) {
  global $xoopsDB, $ts;
	/*

		accepts a database result handle to display a single message
		in the format appropriate for the nested messages

	*/
	global $sys_datefmt;
	/*
		See if this message is new or not
		If so, highlite it in bold
	*/
	$ret_val = "<TABLE BORDER='0'>"
	          ."<TR>"
						."<TD BGCOLOR='#DDDDDD' NOWRAP>"._XF_G_BY.": <A HREF='".XOOPS_URL."/userinfo.php?uid="
						.$result['uid']."'>"
						.$result['uname']."</A>"
						." ( ".$result['name']. " ) "
						."<BR><A HREF='".XOOPS_URL."/modules/xfmod/forum/message.php?msg_id="
						.$result['msg_id']."'>"
						."<img src='".XOOPS_URL."/modules/xfmod/images/msg.gif' width='10' height='12' border'0' alt='"._XF_G_REPLY."'>"
						.$ts->makeTboxData4Show(htmlspecialchars($result['subject'])) ." [ "._XF_G_REPLY." ]</A> &nbsp; "
						."<BR>". date($sys_datefmt,$result['date'])
						."</TD></TR>"
						."<TR><TD>"
						.$ts->makeTareaData4Show ( htmlspecialchars($result['body'] ))."</TD>"
						."</TR>"
						."</TABLE>";
	return $ret_val;
}

function forum_show_nested_messages ( &$msg_arr, $msg_id ) {
	global $total_rows,$sys_datefmt;

	$rows = count($msg_arr[$msg_id]);
	$ret_val='';

	if ($msg_arr[$msg_id] && $rows > 0) {
		$ret_val .= "<UL>";

		/*

			iterate and show the messages in this result

			for each message, recurse to show any submessages

		*/
		for ($i = ($rows - 1); $i >= 0; $i--) {
			//      increment the global total count
			$total_rows++;

			//      show the actual nested message
			$ret_val .= forum_show_a_nested_message ($msg_arr[$msg_id][$i])."<P>";

			if ($msg_arr[$msg_id][$i]['has_followups'] > 0) {
				//      Call yourself if there are followups
				$ret_val .= forum_show_nested_messages ( $msg_arr,$msg_arr[$msg_id][$i]['msg_id'] );
			}
		}
		$ret_val .= "</UL>";
	} else {
		$ret_val .= "<P><B>no messages actually follow up to $msg_id</B>";
	}

	return $ret_val;
}

function show_thread( $thread_id ) {
  global $xoopsDB, $ts, $total_rows,$sys_datefmt,$is_followup_to,$subject,$forum_id,$current_message;

	$sql = "SELECT f.group_forum_id,u.uname,u.name,f.has_followups, "
	      ."u.uid,f.msg_id,f.subject,f.thread_id, "
				."f.body,f.date,f.is_followup_to "
				."FROM ".$xoopsDB->prefix("xf_forum")." f,".$xoopsDB->prefix("users")." u "
				."WHERE f.thread_id='$thread_id' "
				."AND u.uid=f.posted_by "
				."ORDER BY msg_id ASC";

	$result = $xoopsDB->query($sql);

	$total_rows=0;

	if (!$result || $xoopsDB->getRowsNum($result) < 1) {
		return _XF_FRM_BROKENTHREAD;
	} else {
		/*
			Build associative array containing row information
		*/
		while ($row = $xoopsDB->fetchArray($result)) {
			$msg_arr["$row[is_followup_to]"][] = $row;
		}

		/*
			Build table header row
		*/
    $ret_val .= "<table border='0' width='100%'>"
               ."<tr class='bg2'>"
               ."<td><b>"._XF_FRM_THREAD."</b></td>"
               ."<td><b>"._XF_FRM_AUTHOR."</b></td>"
               ."<td><b>"._XF_FRM_DATE."</b></td>"
  	           ."</tr>";

		reset($msg_arr["0"]);
		$thread =& $msg_arr["0"][0];

		$ret_val .= "<TR class='".($i++%2>0?"bg1":"bg3")."'><TD>"
		           .(($current_message != $thread['msg_id'])?"<A HREF='".XOOPS_URL."/modules/xfmod/forum/message.php?msg_id=".$thread['msg_id']."'>":"")
							 ."<img src='".XOOPS_URL."/modules/xfmod/images/msg.gif' width='10' height='12' border'0' alt='message'>";
		/*
			See if this message is new or not
		*/
		$ret_val .= $ts->makeTboxData4Show($thread['subject'])."</A></TD>"
		         ."<TD>".$thread['uname']."</TD>"
						 ."<TD>".date($sys_datefmt, $thread['date'] )."</TD></TR>";

		/*
			Now call the recursive function to show nested messages
		*/
		if ( $thread['has_followups'] > 0) {
			$ret_val .= show_submessages($msg_arr, $thread['msg_id'], 1);
		}

		/*
			end table
		*/
		$ret_val .= "</TABLE>";
	}
	return $ret_val;
}

function show_submessages($msg_arr, $msg_id, $level) {
	/*
		Recursive. Selects this message's id in this thread,
		then checks if any messages are nested underneath it.
		If there are, it calls itself, incrementing $level
		$level is used for indentation of the threads.
	*/
	global $total_rows,$sys_datefmt,$forum_id,$current_message, $ts;

	$rows = count($msg_arr[$msg_id]);

	if ($rows > 0) {
		for ($i = ($rows-1); $i >= 0; $i--) {
			/*
				Is this row's background shaded or not?
			*/
			$total_rows++;

			$ret_val .= "<TR class='".($i%2>0?"bg1":"bg3")."'><TD NOWRAP>";
			/*
				How far should it indent?
			*/
			for ($i2 = 0; $i2 < $level; $i2++) {
				$ret_val .= " &nbsp; &nbsp; &nbsp; ";
			}

			/*
				If it this is the message being displayed, don't show a link to it
			*/

			$ret_val .= (($current_message != $msg_arr[$msg_id][$i]['msg_id'])?
				"<A HREF='".XOOPS_URL."/modules/xfmod/forum/message.php?msg_id=".$msg_arr[$msg_id][$i]['msg_id']."'>":"")
				."<img src='".XOOPS_URL."/modules/xfmod/images/msg.gif' width='10' height='12' border'0' alt='message'>";
			/*
				See if this message is new or not
			*/
			$ret_val .= $ts->makeTboxData4Show($msg_arr[$msg_id][$i]['subject'])."</A></TD>"
			         ."<TD>".$msg_arr[$msg_id][$i]['uname']."</TD>"
							 ."<TD>".date($sys_datefmt, $msg_arr[$msg_id][$i]['date'] )."</TD></TR>";

			if ($msg_arr[$msg_id][$i]['has_followups'] > 0) {
				/*
					Call yourself, incrementing the level
				*/
				$ret_val .= show_submessages($msg_arr,$msg_arr[$msg_id][$i]['msg_id'],($level+1));
			}
		}
	}
	return $ret_val;
}

function get_next_thread_id() {
  global $xoopsDB;

  $result = $xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_forum_thread_id")." VALUES ('')");

	if (!$result) {
		$feedback .= " ".$xoopsDB->error()." ";
		return false;
	} else {
		return $xoopsDB->getInsertId();
	}
}

/**
 *	assumes $allow_anonymous var is setup correctly
 *	added checks and tests to allow anonymous posting
 */
function post_message($thread_id, $is_followup_to, $subject, $body, $group_forum_id) {
  global $xoopsDB, $ts, $feedback, $xoopsUser;

	if (!$xoopsUser) {
	  $feedback = _XF_FRM_COULDPOSTIFLOGGEDIN;
		return false;
	}

	if (!$group_forum_id) {
		$feedback = _XF_FRM_TRYINGTOPOSTWITHOUTID;
		return false;
	}
	if (!$body || !$subject) {
		$feedback = _XF_FRM_MUSTINCLUDEBODYANDSUB;
		return false;
	}

	if (!$thread_id) {
		$thread_id = get_next_thread_id();
		$is_followup_to = 0;
		if (!$thread_id) {
			$feedback .= " "._XF_FRM_GETTINGNEXTIDFAILED." ";
			return false;
		}
	} else {
		if ($is_followup_to) {
			//
			//	increment the parent's followup count if necessary
			//
			$res2 = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_forum")." WHERE msg_id='$is_followup_to' AND group_forum_id='$group_forum_id'");

			if ($xoopsDB->getRowsNum($res2) > 0) {
			//
			//	get thread_id from the parent's row,
			//	which is more trustworthy than the HTML form
			//
				$thread_id = unofficial_getDBResult($res2,0,'thread_id');

				//
				//	now we need to update the first message in
				//	this thread with the current time
				//
				$res4 = $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_forum")." SET most_recent_date='". time() ."' "
					                 ."WHERE thread_id='$thread_id' AND is_followup_to='0'");

				if (!$res4) {
					$feedback = _XF_FRM_COULDNOTUPDATEPARENTTIME;
					return false;
				} else {
					//
					//	mark the parent with followups as an optimization later
					//
					$res3 = $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_forum")." SET has_followups='1',most_recent_date='". time() ."' "
					                   ."WHERE msg_id='$is_followup_to'");
					if (!$res3) {
						$feedback = _XF_FRM_COULDNOTUPDATEPARENT;
						return false;
					}
				}
			} else {
				$feedback = _XF_FRM_TRYINGTOFOLLOWUPNOTEXIST;
				return false;
			}
		} else {
			//should never happen except with shoddy
			//browsers or mucking with the HTML form
			$feedback = _XF_FRM_NOFOLLOWUPIDPRESENT;
			return false;
		}
	}

	$sql = "INSERT INTO ".$xoopsDB->prefix("xf_forum")." (group_forum_id,posted_by,subject,body,date,is_followup_to,thread_id,most_recent_date) "
		    ."VALUES ('$group_forum_id', '".$xoopsUser->getVar("uid")."', '". $ts->makeTboxData4Save($subject) ."', '". $ts->makeTareaData4Save($body) ."', '". time() ."','$is_followup_to','$thread_id','". time() ."')";
	$result = $xoopsDB->queryF($sql);

	if (!$result) {
		$feedback .= " "._XF_FRM_POSTINGFAILED." ".$xoopsDB->error;
		return false;
	} else {
		$msg_id = $xoopsDB->getInsertId();
 
		if (!$msg_id) {
 		  $feedback .= _XF_FRM_FAILEDTOGETINSERTID;
			return false;
		} else {
			$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("users")." SET posts=posts+1 WHERE uname = '".$xoopsUser->getVar("uname")."'");
			handle_monitoring($group_forum_id,$msg_id);

			$feedback .= " "._XF_FRM_MESSAGEPOSTED." ";
			return true;
		}
	}
}

/**
 *	assumes $allow_anonymous var is set up
 *	added checks and tests to allow anonymous posting
 */
function show_post_form($forum_id, $thread_id=0, $is_followup_to=0, $subject="") {
	global $xoopsUser, $ts;
	$content = "";
	if (!$xoopsUser) {
		$content .= "<CENTER>";
		$content .= "<H4><FONT COLOR='RED'>"._XF_FRM_COULDPOSTIFLOGGEDIN."</FONT></H4>";
		$content .= "</CENTER>";
	}
	else
	{
		if ($subject) {
			//if this is a followup, put a RE: before it if needed
			if (!eregi('RE:',$subject,$test)) {
				$subject = "RE: ".$subject;
			}
		}

		
		$content .= "<CENTER>";
		$content .= "<FORM ACTION='".XOOPS_URL."/modules/xfmod/forum/forum.php' METHOD='POST'>";
		$content .= "<INPUT TYPE='HIDDEN' NAME='post_message' VALUE='y'>";
		$content .= "<INPUT TYPE='HIDDEN' NAME='forum_id' VALUE='$forum_id'>";
		$content .= "<INPUT TYPE='HIDDEN' NAME='thread_id' VALUE='$thread_id'>";
		$content .= "<INPUT TYPE='HIDDEN' NAME='msg_id' VALUE='$is_followup_to'>";
		$content .= "<INPUT TYPE='HIDDEN' NAME='is_followup_to' VALUE='$is_followup_to'>";
		$content .= "<TABLE>";
		$content .= "<TR>";
		$content .= "<TD><B>"._XF_G_SUBJECT.":</B><BR>";
		$content .= "<INPUT TYPE='TEXT' NAME='subject' VALUE='".$ts->makeTboxData4Edit($subject)."' SIZE='45' MAXLENGTH='45'>";
		$content .= "</TD></TR>";
		$content .= "<TR><TD><B>"._XF_G_MESSAGE.":</B><BR>";
		$content .= "<TEXTAREA NAME='body' VALUE='' ROWS='10' COLS='85' WRAP='SOFT'></TEXTAREA>";
		$content .= "</TD></TR>";
		$content .= "<TR><TD ALIGN='MIDDLE'>";
		$content .= "<B><FONT COLOR='RED'>"._XF_FRM_HTMLDISPLAYSASTEXT."</FONT></B>";
		$content .= "<P>";
		$content .= "<INPUT TYPE='SUBMIT' NAME='SUBMIT' VALUE='"._XF_FRM_POSTCOMMENT."'>";
		$content .= "</TD></TR></TABLE>";
		$content .= "</FORM>";
		$content .= "</CENTER>";

	}
	return $content;
}

/**
 *	assumes $send_all_posts_to var is set up
 */
function handle_monitoring($forum_id,$msg_id) {
  global $xoopsDB, $ts, $feedback,$send_all_posts_to,$xoopsForge;
	/*
		Checks to see if anyone is monitoring this forum
		If someone is, it sends them the message in email format
	*/
	$sql = "SELECT u.email "
	      ."FROM ".$xoopsDB->prefix("xf_forum_monitored_forums")." fmf,".$xoopsDB->prefix("users")." u "
	      ."WHERE fmf.user_id=u.uid AND fmf.forum_id='$forum_id'";

	$result = $xoopsDB->query($sql);
	$rows = $xoopsDB->getRowsNum($result);

	if (($result && $rows > 0) || $send_all_posts_to) {

		$bcc_arr = util_result_column_to_array($result);
		if ($send_all_posts_to){
			$bcc_arr[sizeof($bcc_arr)] = $send_all_posts_to;
		}

		$sql = "SELECT g.unix_group_name,u.uname,fgl.forum_name,"
		      	."f.group_forum_id,f.thread_id,f.subject,f.date,f.body "
				."FROM ".$xoopsDB->prefix("xf_forum")." f,".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_forum_group_list")." fgl,".$xoopsDB->prefix("xf_groups")." g "
				."WHERE u.uid=f.posted_by "
				."AND fgl.group_forum_id=f.group_forum_id "
				."AND g.group_id=fgl.group_id "
				."AND f.msg_id='$msg_id'";

		$result = $xoopsDB->query ($sql);

		if ($result && $xoopsDB->getRowsNum($result) > 0) {
			$uname = unofficial_getDBResult($result,0, 'uname');
			$body = unofficial_getDBResult($result,0, 'body');
			$unix_group_name = unofficial_getDBResult($result,0,'unix_group_name');
			$forum_name = unofficial_getDBResult($result,0,'forum_name');
			$subject = unofficial_getDBResult($result,0,'subject');
			
			
			$message = frmGetMonitorMessage($msg_id, $uname, $ts->makeTareaData4Edit($body), $forum_id, $unix_group_name, $ts->makeTboxData4Show($forum_name), $ts->makeTboxData4Show($subject));
			
			$r = xoopsForgeMail ($xoopsForge['noreply'], $xoopsConfig['sitename'], $message['subject'], $message['body'], array($xoopsForge['noreply']), $bcc_arr);
			if ($r){
			  $feedback .= ' '._XF_FRM_EMAILSENT.' - '._XF_FRM_PEOPLEMONITORING.' ';
			} else {
			  $feedback .= ' '._XF_FRM_EMAILNOTSENT.' - '._XF_FRM_PEOPLEMONITORING.' ';
			}
		} else {
			$feedback .= ' '._XF_FRM_EMAILNOTSENT.' - '._XF_FRM_PEOPLEMONITORING.' ';
			$feedback .= $xoopsDB->error();
		}
	} else {
		$feedback .= ' '._XF_FRM_EMAILNOTSENT.' - '._XF_FRM_NOONEMONITORING.' ';
		$feedback .= $xoopsDB->error();
	}
}

function validate_forum_name($name) {
	// no spaces
	if (strrpos($name,' ') > 0) {
		$GLOBALS['register_error'] = "There cannot be any spaces in the forum name.";	
		return false;
	}

	// min and max length
	if (strlen($name) < 2) {
		$GLOBALS['register_error'] = "Name is too short. It must be at least 2 characters.";
		return false;
	}
	if (strlen($name) > 40) {
		$GLOBALS['register_error'] = "Name is too long. It must be less than 40 characters.";
		return false;
	}
	
	//valid characters
	if (!ereg('^[a-z][-a-z0-9_]+$', $name)) {
		$GLOBALS['register_error'] = "The name may only contain Letters, Numbers, Dashes, or Underscores.";
		return false;
	}
		
	return true;
}

?>