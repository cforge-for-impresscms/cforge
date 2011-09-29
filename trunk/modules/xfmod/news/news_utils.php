<?php
/**
  *
  * SourceForge News Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: news_utils.php,v 1.12 2004/04/16 17:49:38 jcox Exp $
  *
  */

function news_header(&$group,&$perm) {
  global $xoopsUser, $xoopsForge, $feedback, $xoopsTpl;

  $content = "";
  
  if ($group->getID() != $xoopsForge['sysnews']) {

	$xoopsTpl->assign("project_title",project_title($group));
	$xoopsTpl->assign("project_tabs",project_tabs ('news', $group->getID()));

	if($perm->isAdmin()){
		$content .= '<P><B>';
		$content .= '<A HREF="'.XOOPS_URL.'/modules/xfmod/news/admin/?group_id='.$group->getID().'">'._XF_G_ADMIN.'</A>';
		$content .= ' | <A HREF="'.XOOPS_URL.'/modules/xfmod/news/submit.php?group_id='.$group->getID().'">'._XF_G_SUBMIT.'</A>';
		$content .= '</B></P>';
	}
  } else {
    $content .= "<H4>Forge "._XF_G_NEWS."</H4>";
	if($perm->isAdmin()){
		$content .= '<P><B>';
		$content .= '<A HREF="'.XOOPS_URL.'/modules/xfmod/news/admin/?group_id=">'._XF_G_ADMIN.'</A>';
		$content .= ' | <A HREF="'.XOOPS_URL.'/modules/xfmod/news/submit.php?group_id=">'._XF_G_SUBMIT.'</A>';
		$content .= ' | <A HREF="'.XOOPS_URL.'/modules/xfmod/admin.php">'._XF_G_SITEADMIN.'</A></B></P>';
	}
  }
  if($feedback) $xoopsTpl->assign("feedback",$feedback);
  return $content;
}


function news_show_latest($group_id='',$limit=10,$show_summaries=true,$allow_submit=true,$flat=false,$tail_headlines=0)
{
	global $sys_datefmt, $xoopsForge, $xoopsDB, $ts;

	if (!$group_id) {
		$group_id = $xoopsForge['sysnews'];
	}
	/*
		Show a simple list of the latest news items with a link to the forum
	*/

	if ($group_id != $xoopsForge['sysnews']) {
		$wclause="nb.group_id='$group_id' AND nb.is_approved <> '4'";
	} else {
		$wclause="nb.is_approved=1";
	}

	$sql = "SELECT g.group_id,g.group_name,g.unix_group_name,g.type,u.uname,u.name,nb.forum_id,nb.summary,nb.date,nb.details "
	      ."FROM ".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_news_bytes")." nb,".$xoopsDB->prefix("xf_groups")." g "
				."WHERE $wclause "
				."AND u.uid=nb.submitted_by "
				."AND nb.group_id=g.group_id "
				."ORDER BY date DESC";

	$result = $xoopsDB->query($sql, $limit + $tail_headlines);
	$rows = $xoopsDB->getRowsNum($result);

	if (!$result || $rows < 1)
	{
		$return .= "<H3>"._XF_NWS_NONEWSITEMSFOUND."</H3>";
		$return .= $xoopsDB->error();
	}
	else
	{
		$return .= "<DL COMPACT>";
		for ($i = 0; $i < $rows; $i++)
		{
			if ($show_summaries && $limit)
			{
				//get the first paragraph of the story
				//$arr = explode("\n",unofficial_getDBResult($result,$i,'details'));
				//if the first paragraph is short, and so are following paragraphs, add the next paragraph on
				//if ((strlen($arr[0]) < 200) && (strlen($arr[1].$arr[2]) < 300) && (strlen($arr[2]) > 5))
				//{
				//	$summ_txt = "<BR>".$arr[0]."<BR>".$arr[1]."<BR>".$arr[2];
				//}
				//else
				//{
				//	$summ_txt = "<BR>".$arr[0];
				//}
				$summ_txt = "\n".unofficial_getDBResult($result,$i,'details');
				if(strlen($summ_txt)>200){
					$summ_txt = substr($summ_txt,0,200)."...";
				}
				//show the project name
				if (unofficial_getDBResult($result,$i,'type') == 2)
				{
					$group_type="/foundry";
				}
				else
				{
					$group_type="/project";
				}
				$proj_name = "&nbsp;&nbsp; <A HREF='"
					.XOOPS_URL."/xf".$group_type."/?group_id="
					.unofficial_getDBResult($result,$i,'group_id')
					."'>"
					.$ts->makeTboxData4Show(unofficial_getDBResult($result,$i,'group_name'))
					."</A>";
			}
			else
			{
				$proj_name = "";
				$summ_txt = "";
			}
			if (!$limit)
			{
				$return .= "<li><A HREF='".XOOPS_URL."/modules/xfmod/forum/forum.php?forum_id=".unofficial_getDBResult($result,$i,'forum_id')."'><B>".$ts->makeTboxData4Show(unofficial_getDBResult($result,$i,'summary'))."</B></A>";
				$return .= " &nbsp; <I>".date($sys_datefmt,unofficial_getDBResult($result,$i,'date'))."</I><br>";
			}
			else
			{
				if (!$flat) { $return .= "<BR>"; }
				$name=unofficial_getDBResult($result,$i,'name');
				if(!($name))
				   $name=unofficial_getDBResult($result,$i,'uname');
				$summary = unofficial_getDBResult($result,$i,'summary');
				$date = date("M d",unofficial_getDBResult($result,$i,'date'));
				
				$return .= "<table align='center' border='0' cellspacing='0' cellpadding='0' width='96%'>"
				."<tr>"
				."<td width='2%' valign='bottom'><img src='".XOOPS_URL."/modules/xfmod/images/h_arrow.gif' width='14' height='26' border='0' alt=''></td>"
				."<td align='left' valign='bottom' width='98%'><A HREF='".XOOPS_URL."/modules/xfmod/forum/forum.php?forum_id=".unofficial_getDBResult($result,$i,'forum_id')."'><span class='head3'>".unofficial_getDBResult($result,$i,'summary')."</span></A></td></tr><tr>" 
				."<td colspan='2' background='".XOOPS_URL."/modules/xfmod/images/dotlinebg_horiz.gif'><img src='".XOOPS_URL."/modules/xfmod/images/spacer.gif' width='1' height='1' border='0' alt=''></td>"
				."</tr><tr><td colspan='2'><BR/>".unofficial_getDBResult($result,$i,'details')."<BR/><BR/><I>".$name." (".$date.")</I></td></tr>" 
				."</table><BR/>";


/*	      $sql = "SELECT COUNT(f.msg_id) AS count "
	            ."FROM ".$xoopsDB->prefix("xf_forum")." f,".$xoopsDB->prefix("xf_forum_group_list")." fgl "
				      ."WHERE f.group_forum_id=fgl.group_forum_id "
				      ."AND fgl.is_public=1 "
				      ."AND fgl.group_id=$group_id "
				      ."AND f.group_forum_id=".unofficial_getDBResult($result,$i,'forum_id');
*/

	      $sql = "SELECT COUNT(f.msg_id) AS count "
	            ."FROM ".$xoopsDB->prefix("xf_forum")." f "
		    ."WHERE f.group_forum_id=".unofficial_getDBResult($result,$i,'forum_id');

				$res2 = $xoopsDB->query($sql);
				$num_comments = unofficial_getDBResult($res2, 0,'count');

				if (!$num_comments)
				{
					$num_comments = '0';
				}

				if ($num_comments == 1)
				{
					$comments_txt = " "._XF_NWS_COMMENT;
				}
				else
				{
					$comments_txt = " "._XF_NWS_COMMENTS;
				}

				$return .= "<div align='center'>(".$num_comments . $comments_txt.") [ <A HREF='".XOOPS_URL."/modules/xfmod/forum/forum.php?forum_id=".unofficial_getDBResult($result,$i,'forum_id')."'>"._XF_NWS_READMORECOMMENT."</a> ]</div><BR/><BR/>";
			}

			if ($limit == 1 && $tail_headlines)
			{
				$return .= "<ul>";
			}
			if ($limit)
			{
				$limit--;
			}
		}
	}

	if ($group_id != $xoopsForge['sysnews']) {
		  $archive_url="/news/?group_id=".$group_id;
	} else {
		  $archive_url="/news/";
	}

	if ($tail_headlines) {
		$return .= "</ul><HR width='100%' size='1' noshade>\n";
	}

	$return .= "<div align='center'><BR>"
	        ."[ <a href='".XOOPS_URL."/modules/xfmod".$archive_url."'>"._XF_NWS_NEWSARCHIVE."</a> ]</div>";

	if ($allow_submit && $group_id != $xoopsForge['sysnews']) {
		//you can only submit news from a project now
		//you used to be able to submit general news
		$return .= "<div align='center'>[ <A HREF='".XOOPS_URL."/modules/xfmod/news/submit.php?group_id=".$group_id."'><FONT SIZE='-1'>"._XF_NWS_SUBMITNEWS."</FONT></A> ]</center>";
	}

	return $return;
}

function news_foundry_latest($group_id=0,$limit=5,$show_summaries=true) {
	global $sys_datefmt;
	/*
		Show a the latest news for a portal
	*/

	$sql="SELECT groups.group_name,groups.unix_group_name,users.user_name,news_bytes.forum_id,news_bytes.summary,news_bytes.date,news_bytes.details ".
		"FROM users,news_bytes,groups,foundry_news ".
		"WHERE foundry_news.foundry_id='$group_id' ".
		"AND users.user_id=news_bytes.submitted_by ".
		"AND foundry_news.news_id=news_bytes.id ".
		"AND news_bytes.group_id=groups.group_id ".
		"AND foundry_news.is_approved=1 ".
		"ORDER BY news_bytes.date DESC";

	$result=db_query($sql,$limit);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		$return .= '<H3>No News Items Found</H3>';
		$return .= db_error();
	} else {
		for ($i=0; $i<$rows; $i++) {
			if ($show_summaries) {
				//get the first paragraph of the story
				$arr=explode("\n",db_result($result,$i,'details'));
				if ((strlen($arr[0]) < 200) && (strlen($arr[1].$arr[2]) < 300) && (strlen($arr[2]) > 5)) {
					$summ_txt=util_make_links( $arr[0].'<BR>'.$arr[1].'<BR>'.$arr[2] );
				} else {
					$summ_txt=util_make_links( $arr[0] );
				}

				//show the project name
				$proj_name=' &nbsp; - &nbsp; <A HREF="/projects/'. strtolower(db_result($result,$i,'unix_group_name')) .'/">'. db_result($result,$i,'group_name') .'</A>';
			} else {
				$proj_name='';
				$summ_txt='';
			}
			$return .= '
				<A HREF="/forum/forum.php?forum_id='. db_result($result,$i,'forum_id') .'"><B>'. db_result($result,$i,'summary') . '</B></A>
				<BR><I>'. db_result($result,$i,'user_name') .' - '.
					date($sys_datefmt,db_result($result,$i,'date')) . $proj_name . '</I>
				'. $summ_txt .'<HR WIDTH="100%" SIZE="1">';
		}
	}
	return $return;
}

function get_news_name($id) {
	/*
		Takes an ID and returns the corresponding forum name
	*/
	$sql="SELECT summary FROM news_bytes WHERE id='$id'";
	$result=db_query($sql);
	if (!$result || db_numrows($result) < 1) {
		return "Not Found";
	} else {
		return db_result($result, 0, 'summary');
	}
}

?>