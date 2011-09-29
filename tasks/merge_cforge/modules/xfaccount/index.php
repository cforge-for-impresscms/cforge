<?php
/**
* SourceForge User's Personal Page
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001 (c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: index.php,v 1.18 2004/07/19 22:48:17 jcox Exp $
*/
 
include_once ("../../mainfile.php");
 
$langfile = "my.php";
 
require_once (ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once (ICMS_ROOT_PATH."/modules/xfaccount/account_util.php");
require_once (ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once (ICMS_ROOT_PATH."/modules/xfmod/maillist/maillist_utils.php");
$icmsOption['template_main'] = 'xfaccount_index.html';
if (!$icmsUser)
{
	redirect_header(ICMS_URL."/user.php?icms_redirect=/modules/xfaccount/", 1, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
	exit;
}

$metaTitle = ": "._XF_MY_MYPERSONALPAGE;

$mhandler = icms_gethandler('module');
$icmsModule = $xoopsModule = $mhandler->getByDirname('xfaccount');
global $icmsModule;

include ("../../header.php");
$icmsTpl->assign("account_header", account_header($icmsUser->uname()."'s Account"));
 
// Determine whether this user is a project or community admin.
// This is the same query used to render the "My Projects" box.
$myprojects_sql = "SELECT g.group_name,g.group_id,g.unix_group_name,g.status,g.type,ug.admin_flags,g.is_public "." FROM ".$icmsDB->prefix("xf_groups")." g,".$icmsDB->prefix("xf_user_group")." ug "." WHERE g.group_id=ug.group_id "." AND ug.user_id='".$icmsUser->getVar("uid")."' "." AND g.status='A'"." ORDER BY g.group_name";
 
$myprojects_result = $icmsDB->query($myprojects_sql);
$myprojects_rows = $icmsDB->getRowsNum($myprojects_result);
 
/***** PROJECT LIST *****/
$status = array('I' => '(Incomplete)',
	'A' => '',
	'N' => '(Inactive)',
	'P' => '(Pending)',
	'H' => '(Holding)',
	'D' => '(Deleted)');
 
if (!$myprojects_result || $myprojects_rows < 1)
	{
	$icmsTpl->assign("no_projects", true);
	$icmsTpl->assign("prj_comm_block_title", _XF_MY_MYPROJECTS);
	$icmsTpl->assign("prj_comm_content", _XF_MY_NOPROJECTS."<br/><br/>");
}
else
{
	$has_projects = 0;
	$has_communities = 0;
	for ($i = 0; $i < $myprojects_rows; $i ++)
	{
		$pl = $icmsDB->fetchArray($myprojects_result);
		$pl['status'] = $status[$pl['status']];
		if (!$pl['is_public']) $pl['status'] = '(Private)';
		$prj_list[$i] = $pl;
		if ($prj_list[$i]['type'] == 2)
			{
			$has_communities = 1;
		}
		else
			{
			$has_projects = 1;
		}
	}
	$icmsTpl->assign("prj_list", $prj_list);
	if ($has_projects)
		{
		if ($has_communities)
			{
			$icmsTpl->assign("prj_comm_block_title", _XF_MY_MYPRJCOMM);
		}
		else
			{
			$icmsTpl->assign("prj_comm_block_title", _XF_MY_MYPROJECTS);
		}
	}
	else
	{
		$icmsTpl->assign("prj_comm_block_title", _XF_MY_MYCOMM);
	}
}
 
//This is extra information that we need later.
$is_pa = false;
$is_ca = false;
if ($myprojects_rows)
	{
	// This user might be a project or community admin.
	for ($i = 0; $i < $myprojects_rows; $i ++)
	{
		if (stristr(unofficial_getDBREsult($myprojects_result, $i, 'admin_flags'), 'A'))
			{
			if (unofficial_getDBResult($myprojects_result, $i, 'type') == 2)
				{
				$is_ca = true;
			}
			else
				{
				$is_pa = true;
			}
		}
	}
}
 
/***** ASSIGNED ITEMS *****/
$icmsTpl->assign("assigned_items_title", _XF_MY_MYASSIGNEDITEMS);
$sql = "SELECT g.group_name,agl.name,agl.group_id,a.group_artifact_id,a.assigned_to,a.summary,a.artifact_id,a.priority "."FROM ".$icmsDB->prefix("xf_artifact")." a, ".$icmsDB->prefix("xf_groups")." g, ".$icmsDB->prefix("xf_artifact_group_list")." agl "."WHERE a.group_artifact_id=agl.group_artifact_id "."AND agl.group_id=g.group_id "."AND g.status = 'A' "."AND a.assigned_to='".$icmsUser->getVar("uid")."' "."AND a.status_id='1' "."ORDER BY agl.group_id,a.group_artifact_id,a.assigned_to,a.status_id";
 
$result = $icmsDB->query($sql);
$rows = $icmsDB->getRowsNum($result);
if (!$result || $rows < 1)
	{
	$icmsTpl->assign("assigned_items_content", "<tr><td colspan='2'>"._XF_MY_NOOPENTRACKERITEMS."<br/><br/></td></tr>");
}
else
{
	$content = "";
	$last_group = 0;
	for ($i = 0; $i < $rows; $i ++)
	{
		if (unofficial_getDBResult($result, $i, 'group_artifact_id') != $last_group)
			{
			$content .= "<tr><td colspan=2><strong><a href='../xfmod/tracker/?group_id=".unofficial_getDBResult($result, $i, 'group_id')."&atid=".unofficial_getDBResult($result, $i, 'group_artifact_id')."'>".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'group_name'))." - ".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'name'))."</a></td></tr>";
		}
		$content .= "<tr bgcolor='".get_priority_color(unofficial_getDBResult($result, $i, 'priority'))."'>"."<td><a href='../xfmod/tracker/?func=detail&aid=".unofficial_getDBResult($result, $i, 'artifact_id')."&group_id=".unofficial_getDBResult($result, $i, 'group_id')."&atid=".unofficial_getDBResult($result, $i, 'group_artifact_id')."'>".unofficial_getDBResult($result, $i, 'artifact_id')."</td>"."<td width=\"99%\">".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'summary'))."</td></tr>";
		$last_group = unofficial_getDBResult($result, $i, 'group_artifact_id');
	}
	$icmsTpl->assign("assigned_items_content", $content);
}
 
/***** SUBMITTED ITEMS *****/
$icmsTpl->assign("submitted_items_title", _XF_MY_MYSUBMITTEDITEMS);
$sql = "SELECT g.group_name,agl.name,agl.group_id,a.group_artifact_id,a.assigned_to,a.summary,a.artifact_id,a.priority "."FROM ".$icmsDB->prefix("xf_artifact")." a, ".$icmsDB->prefix("xf_groups")." g, ".$icmsDB->prefix("xf_artifact_group_list")." agl "."WHERE a.group_artifact_id=agl.group_artifact_id "."AND agl.group_id=g.group_id "."AND g.status = 'A' "."AND a.submitted_by='".$icmsUser->getVar("uid")."' "."AND a.status_id='1' "."ORDER BY agl.group_id,a.group_artifact_id,a.submitted_by,a.status_id";
 
$result = $icmsDB->query($sql);
$rows = $icmsDB->getRowsNum($result);
if (!$result || $rows < 1)
	{
	$icmsTpl->assign("submitted_items_content", "<tr><td colspan='2'>"._XF_MY_NOSUBMITTEDTRACKERITEMS."<br><br></td></tr>");
}
else
{
	$content = "";
	$last_group = 0;
	for ($i = 0; $i < $rows; $i ++)
	{
		if (unofficial_getDBResult($result, $i, 'group_artifact_id') != $last_group)
			{
			$content .= "<tr><td colspan=2><strong><a href='../xfmod/tracker/?group_id=".unofficial_getDBResult($result, $i, 'group_id')."&atid=".unofficial_getDBResult($result, $i, 'group_artifact_id')."'>".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'group_name'))." - ".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'name'))."</a></td></tr>";
		}
		$content .= "<tr bgcolor='".get_priority_color(unofficial_getDBResult($result, $i, 'priority'))."'>"."<td><a href='../xfmod/tracker/?func=detail&aid=".unofficial_getDBResult($result, $i, 'artifact_id')."&group_id=".unofficial_getDBResult($result, $i, 'group_id')."&atid=".unofficial_getDBResult($result, $i, 'group_artifact_id')."'>".unofficial_getDBResult($result, $i, 'artifact_id')."</td>"."<td width=\"99%\">".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'summary'))."</td></tr>";
		$last_group = unofficial_getDBResult($result, $i, 'group_artifact_id');
	}
	$icmsTpl->assign("submitted_items_content", $content);
}
 
/***** MONITORED FORUMS *****/
/*
$icmsTpl->assign("forums_title", _XF_MY_MONITOREDFORUMS);
$sql = "SELECT g.group_name,g.group_id,fgl.group_forum_id,fgl.forum_name "."FROM ".$icmsDB->prefix("xf_groups")." g,".$icmsDB->prefix("xf_forum_group_list")." fgl,".$icmsDB->prefix("xf_forum_monitored_forums")." fmf "."WHERE g.group_id=fgl.group_id "."AND g.status='A' "."AND fgl.group_forum_id=fmf.forum_id "."AND fmf.user_id='".$icmsUser->getVar("uid")."' ORDER BY group_name DESC";
 
$result = $icmsDB->query($sql);
$rows = $icmsDB->getRowsNum($result);
if (!$result || $rows < 1)
{
$icmsTpl->assign("forums_content", "<tr><td colspan='2'>"._XF_MY_NOTMONITORFORUMS."<br><br></td></tr>");
}
else
{
$last_group = 0;
$content = "";
for ($i = 0; $i < $rows; $i ++)
{
if (unofficial_getDBResult($result, $i, 'group_id') != $last_group)
{
//class='". ($i % 2 != 0 ? "bg2" : "bg3")."'
$content.= "<tr><td colspan='2'><strong><a href='../xfmod/forum/?group_id=".unofficial_getDBResult($result, $i, 'group_id')."'>".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'group_name'))."</a></td></tr>";
}
$content.= "<tr><td align='middle'><a href='../xfmod/forum/monitor.php?forum_id=".unofficial_getDBResult($result, $i, 'group_forum_id')."'><img src='../xfmod/images/ic/trash.png' height='16' width='16' border='0' alt='remove monitor'></a></td>"."<td width='99%'><a href='../xfmod/forum/forum.php?forum_id=".unofficial_getDBResult($result, $i, 'group_forum_id')."'>".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'forum_name'))."</a></td></tr>";
$last_group = unofficial_getDBResult($result, $i, 'group_id');
}
$icmsTpl->assign("forums_content", $content);
}
*/
/***** MONITORED FILE MODULES *****/
$icmsTpl->assign("files_title", _XF_MY_MONITOREDFILES);
$sql = "SELECT g.group_name,g.unix_group_name,g.group_id,p.name,f.filemodule_id "."FROM ".$icmsDB->prefix("xf_groups")." g,".$icmsDB->prefix("xf_filemodule_monitor")." f,".$icmsDB->prefix("xf_frs_package")." p "."WHERE g.group_id=p.group_id AND g.status = 'A' "."AND p.package_id=f.filemodule_id "."AND f.user_id='".$icmsUser->getVar("uid")."' ORDER BY group_name DESC";
 
$result = $icmsDB->query($sql);
$rows = $icmsDB->getRowsNum($result);
if (!$result || $rows < 1)
	{
	$icmsTpl->assign("files_content", "<tr><td colspan='2'>"._XF_MY_NOTMONITORFILES."<br><br></td></tr>");
}
else
{
	$last_group = 0;
	$content = "";
	for ($i = 0; $i < $rows; $i ++)
	{
		if (unofficial_getDBResult($result, $i, 'group_id') != $last_group)
			{
			$content .= "<tr><td colspan='2'><strong><a href='../xfmod/project/?".unofficial_getDBResult($result, $i, 'unix_group_name')."'>".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'group_name'))."</a></td></tr>";
		}
		$content .= "<tr><td align='middle'><a href='../xfmod/project/filemodule_monitor.php?filemodule_id=".unofficial_getDBResult($result, $i, 'filemodule_id')."'><img src='../xfmod/images/ic/trash.png' height='16' width='16' border='0' alt='remove monitor'></a></td>"."<td width='99%'><a href='../xfmod/project/showfiles.php?group_id=".unofficial_getDBResult($result, $i, 'group_id')."'>".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'name'))."</a></td></tr>";
		$last_group = unofficial_getDBResult($result, $i, 'group_id');
	}
	$icmsTpl->assign("files_content", $content);
}
 
/***** MY TASKS *****/
$icmsTpl->assign("tasks_title", _XF_MY_MYTASKS);
$sql = "SELECT g.group_name,pgl.project_name,pgl.group_id,pt.group_project_id,pt.priority,pt.project_task_id,pt.summary,pt.percent_complete "."FROM ".$icmsDB->prefix("xf_groups")." g,".$icmsDB->prefix("xf_project_group_list")." pgl,".$icmsDB->prefix("xf_project_task")." pt,".$icmsDB->prefix("xf_project_assigned_to")." pat "."WHERE pt.project_task_id=pat.project_task_id "."AND pat.assigned_to_id='".$icmsUser->getVar("uid")."' "."AND pt.status_id='1' "."AND pgl.group_id=g.group_id "."AND pgl.group_project_id=pt.group_project_id "."AND g.status = 'A'"."ORDER BY group_name,project_name";
 
$result = $icmsDB->query($sql);
$rows = $icmsDB->getRowsNum($result);
if (!$result || $rows < 1)
	{
	$icmsTpl->assign("tasks_content", "<tr><td colspan='2'>"._XF_MY_NOOPENTASKS."<br><br></td></tr>");
}
else
{
	$last_group = 0;
	$content = "";
	for ($i = 0; $i < $rows; $i ++)
	{
		/* Deduce summary style */
		$style_begin = '';
		$style_end = '';
		 
		if (unofficial_getDBResult($result, $i, 'percent_complete') == 100)
			{
			$style_begin = '<u>';
			$style_end = '</u>';
		}
		if (unofficial_getDBResult($result, $i, 'group_project_id') != $last_group)
			{
			$content .= "<tr><td colspan='2'><strong><a href='../xfmod/pm/task.php?group_id=".unofficial_getDBResult($result, $i, 'group_id')."&group_project_id=".unofficial_getDBResult($result, $i, 'group_project_id')."'>".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'group_name'))." - ".$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'project_name'))."</a></td></tr>";
		}
		$content .= "<tr bgcolor='".get_priority_color(unofficial_getDBResult($result, $i, 'priority'))."'>"."<td><a href='../xfmod/pm/task.php?func=detailtask&project_task_id=".unofficial_getDBResult($result, $i, 'project_task_id')."&group_id=".unofficial_getDBResult($result, $i, 'group_id')."&group_project_id=".unofficial_getDBResult($result, $i, 'group_project_id')."'>".unofficial_getDBResult($result, $i, 'project_task_id')."</td>"."<td width=\"99%\">".$style_begin.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'summary')).$style_end."</td></tr>";
		 
		$last_group = unofficial_getDBResult($result, $i, 'group_project_id');
	}
	$icmsTpl->assign("tasks_content", $content);
}
 
/***** DEVELOPER SURVEYS *****
NOTE: This section needs to be updated manually to display any given survey. */
if (intval($icmsForge['devsurvey']) != 100)
{
	$icmsTpl->assign("display_survey", true);
	$icmsTpl->assign("survey_title", _XF_MY_QUICKSURVEY);
	 
	$sql = "SELECT * "."FROM ".$icmsDB->prefix("xf_survey_responses")." "."WHERE survey_id='".$icmsForge['devsurvey']."' "."AND user_id='".$icmsUser->getVar("uid")."' "."AND group_id='1'";
	 
	$result = $icmsDB->query($sql);
	 
	if ($icmsDB->getRowsNum($result) < 1)
		{
		/* Hasn't taken dev survery yet, so show it */
		$icmsTpl->assign("survey_content", show_survey(1, $icmsForge['devsurvey']));
	}
	else
	{
		/* User has already taken the developer survery */
		$icmsTpl->assign("survey_content", "<tr><td colspan='2'>"._XF_MY_QUICKSURVEYTAKEN."</td></tr>");
	}
}
 
/***** BOOKMARKS *****/
$icmsTpl->assign("bookmarks_title", _XF_MY_MYBOOKMARKS);
$icmsTpl->assign("bookmarks_none", _XF_MY_NOBOOKMARKS);
$icmsTpl->assign("bookmarks_add", _XF_MY_ADDBOOKMARK);
$sql = "SELECT bookmark_url,bookmark_title,bookmark_id FROM " .$icmsDB->prefix("xf_user_bookmarks")
." WHERE user_id='" .$icmsUser->getVar("uid")
."' ORDER BY bookmark_title";
 
$result = $icmsDB->query($sql);
$rowCount = $icmsDB->getRowsNum($result);
$icmsTpl->assign("bookmarks_count", $rowCount);
 
$rowList = array();
for ($i = 0; $i < $rowCount; $i++)
{
	$rowList[$i] = $icmsDB->fetchArray($result);
}
$icmsTpl->assign("bookmarks_list", $rowList);
 
 
/***** SITE MAILING LIST PROCESSING *****/
$subscribe_result = "";
 
if (isset($_POST['list_sub_form_submit']) )
	$list_sub_form_submit = $_POST['list_sub_form_submit'];
elseif (isset($_GET['list_sub_form_submit']) )
$list_sub_form_submit = $_GET['list_sub_form_submit'];
else
	$list_sub_form_submit = null;
 
if ($list_sub_form_submit == _XF_G_SUBMIT)
	{
	foreach ($_POST as $name => $value)
	{
		$len = strlen($name);
		if ($len >= 5 && 0 == strcmp(substr($name, $len -7), "_listid"))
			{
			$listname = substr($name, 0, $len -7);
			$sub = $_POST[$listname.'_subscribe'];
			$unsub = $_POST[$listname.'_unsubscribe'];
			$listid = $_POST[$listname.'_listid'];
			$pwd = $_POST[$listname.'_pwd'];
			if ($sub == "on")
				{
				$confpwd = $_POST[$listname.'_confpwd'];
				if (0 != strlen($listname) && 0 != strlen($listid) && 0 != strlen($email) && 0 != strlen($pwd) && 0 != strlen($confpwd))
					{
					if (maillist_subscribe($icmsUser, $icmsDB, $_SERVER['HTTP_HOST'], $listname, $listid, urldecode($email), $pwd))
						{
						$subscribe_result .= _XF_MY_SUB_SUCCESS."<br>\n";
					}
					else
						{
						$subscribe_result .= _XF_MY_SUB_FAIL."<br>\n";
					}
				}
				else
					{
					$subscribe_result .= _XF_MY_NOSUB_NODATA."<br>\n";
				}
			}
			if ($unsub == "on")
				{
				if (0 != strlen($listname) && 0 != strlen($listid) && 0 != strlen($email) && 0 != strlen($pwd))
					{
					if (maillist_unsubscribe($icmsUser, $icmsDB, $_SERVER['HTTP_HOST'], $listname, $listid, urldecode($email), $pwd))
						{
						$subscribe_result .= _XF_MY_UNSUB_SUCCESS."<br>\n";
					}
					else
						{
						$subscribe_result .= _XF_MY_UNSUB_FAIL."<br>\n";
					}
				}
				else
					{
					$subscribe_result .= _XF_MY_NOUNSUB_NODATA."<br>\n";
				}
			}
		}
	}
}
 
/***** SITE MAILING LISTS *****/
$icmsTpl->assign("list_title", _XF_MY_SITELISTS);
$sql = "SELECT list_name,list_id,allow_ru,allow_pa,allow_ca FROM ".$icmsDB->prefix("xf_maillist_sitelists")." WHERE allow_ru = '1'";
if ($is_pa)
	{
	$sql .= " OR allow_pa = '1'";
}
if ($is_ca)
	{
	$sql .= " OR allow_ca = '1'";
}
$result = $icmsDB->query($sql);
$rows = $icmsDB->getRowsNum($result);
$avail_lists = array();
$total_avail_lists = 0;
for ($i = 0; $i < $rows; $i ++)
{
	$list_name = unofficial_getDBResult($result, $i, 'list_name');
	$avail_lists[$list_name] = unofficial_getDBResult($result, $i, 'list_id');
	$total_avail_lists ++;
}
 
$sql = "SELECT lists.list_name FROM ".$icmsDB->prefix("xf_maillist_sitelists")." lists, ".$icmsDB->prefix("xf_maillist_site_subscriptions")." subs "."WHERE subs.uid='".$icmsUser->getVar("uid")."' AND lists.list_id=subs.list_id";
$result = $icmsDB->query($sql);
$rows = $icmsDB->getRowsNum($result);
 
$content = "<table>";
if (0 != strlen($subscribe_result))
	{
	$content .= "<tr><td colspan='2'><strong>".$subscribe_result."</strong></td></tr>";
}
 
$content .= "<form name='list_sub_form' method='POST' action='".$_SERVER['PHP_SELF']."'>";
$content .= "<tr><td colspan='2'><input type='hidden' name='email' value='".urlencode($icmsUser->getVar("email"))."'>";
if (!$result || $rows < 1)
	{
	$content .= _XF_MY_NOSUBSCRIPTIONS."<br><br></td></tr>";
}
else
{
	$content .= _XF_MY_SUBSCRIPTIONS_HDR."</td></tr><tr><td colspan='2'>";
	$content .= "<table border='0' width='100%' cellpadding='2' cellspacing='2'>";
	//$class = "";
	for ($i = 0; $i < $rows; $i ++)
	{
		//$i % 2 ? $class = "bg2" : $class = "bg3";
		$list_name = unofficial_getDBResult($result, $i, 'list_name');
		$trans_list_name = strtr($list_name, "-", "_");
		$content .= "<input type='hidden' name='".$trans_list_name."_listid' value='".$avail_lists[$list_name]."'>";
		$content .= "<tr><td width='25%' class='centercolumn'>&nbsp;".$list_name."</td>";
		$content .= "<td align='center'><input type='password' name='".$trans_list_name."_pwd'></td>";
		$avail_lists[$list_name] = -1;
		$total_avail_lists --;
		$list_name = $trans_list_name;
		$content .= "<td align='center' width='20%'><input type='checkbox' name='".$list_name."_unsubscribe' onClick=\"if(document.list_sub_form.".$list_name."_unsubscribe.checked&&(document.list_sub_form.".$list_name."_pwd.value==null||document.list_sub_form.".$list_name."_pwd.value=='')){document.list_sub_form.".$list_name."_unsubscribe.checked=false;alert('"._XF_MY_PASSWD_REQD."');document.list_sub_form.".$list_name."_pwd.focus();}\">&nbsp;"._XF_MY_UNSUBSCRIBE."</td></tr>";
	}
	$content .= "<tr height='10'><td width='100%' colspan='3' /></tr>";
	$content .= "</table></td></tr>";
}
if ($total_avail_lists)
	{
	$content .= "<tr><td colspan='2'>"._XF_MY_AVAILABLE_SUBS."</td></tr><tr><td colspan='2'>";
	$content .= "<table border='0' width='100%' cellpadding='2' cellspacing='2'>";
	$i = 0;
	foreach ($avail_lists as $list_name => $list_id)
	{
		if ($list_id != -1)
			{
			$trans_list_name = strtr($list_name, "-", "_");
			$i ++ % 2 ? $class = "bg2" :
			 $class = "bg3";
			$content .= "<input type='hidden' name='".$trans_list_name."_list' value='".$list_name."'>";
			$content .= "<input type='hidden' name='".$trans_list_name."_listid' value='".strval($list_id)."'>";
			$content .= "<tr><td width='25%' class='centercolumn'>&nbsp;".$list_name."</td>";
			$content .= "<td align='center'><input type='password' name='".$trans_list_name."_pwd'></td>";
			$content .= "<td align='center'><input type='password' name='".$trans_list_name."_confpwd'></td>";
			$list_name = $trans_list_name;
			$content .= "<td align='center' width='20%'><input type='checkbox' name='".$list_name."_subscribe' onClick=\"if(document.list_sub_form.".$list_name."_subscribe.checked&&(document.list_sub_form.".$list_name."_pwd.value==null||document.list_sub_form.".$list_name."_pwd.value=='')){document.list_sub_form.".$list_name."_subscribe.checked=false;alert('"._XF_MY_PASSWD_REQD."');document.list_sub_form.".$list_name."_pwd.focus();}else if(document.list_sub_form.".$list_name."_subscribe.checked&&document.list_sub_form.".$list_name."_pwd.value!=document.list_sub_form.".$list_name."_confpwd.value){document.list_sub_form.".$list_name."_subscribe.checked=false;alert('"._XF_MY_PASSWD_NOMATCH."');document.list_sub_form.".$list_name."_pwd.focus();}\">&nbsp;"._XF_MY_SUBSCRIBE."</td></tr>";
		}
	}
	//$i % 2 ? $class = "bg2" : $class = "bg3";
	$content .= "<tr height='10'><td width='100%' colspan='4' /></tr>";
	$content .= "</table></td></tr>";
}
 
$content .= "<tr><td colspan='2'>";
$content .= "<table border='0' width='100%' cellpadding='2' cellspacing='2'>";
$content .= "<tr><td width='100%' align='center'><input type='submit' name='list_sub_form_submit' value='"._XF_G_SUBMIT."'></td></tr>";
$content .= "</table></td></tr>";
 
$content .= "</form></table>";
 
$icmsTpl->assign("list_content", $content);
 
$icmsTpl->assign("priority_colors", get_priority_colors_key());
 
include ("../../footer.php");
?>