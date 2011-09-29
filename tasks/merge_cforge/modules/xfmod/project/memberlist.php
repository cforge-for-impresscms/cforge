<?php
/**
*
* Project Members Information
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001(c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: memberlist.php,v 1.5 2004/01/26 18:57:00 devsupaul Exp $
*
*/
 
include_once("../../../mainfile.php");
 
$langfile = "project.php";
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$icmsOption['template_main'] = 'project/xfmod_memberlist.html';
 
if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
 
$project = group_get_object($group_id);
 
//group is private
if (!$project->isPublic())
{
	//if it's a private group, you must be a member of that group
	if (!$project->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
	{
		redirect_header(ICMS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
		exit;
	}
}
 
//for dead projects must be member of xoopsforge project
if (!$project->isActive() && !$perm->isSuperUser())
{
	redirect_header(ICMS_URL, 4, _XF_PRJ_NOTAUTHORIZEDTOENTER);
	exit;
}
 
include("../../../header.php");
 
$icmsTpl->assign("project_title", project_title($project));
$icmsTpl->assign("project_tabs", project_tabs('memberlist', $group_id));
 
// list members
$query = "SELECT u.uname,u.uid,u.name AS realname, ug.admin_flags, pjc.name AS role " ."FROM ".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_user_group")." ug,".$icmsDB->prefix("xf_people_job_category")." pjc " ."WHERE u.uid=ug.user_id " ."AND ug.group_id='$group_id' " ."AND ug.member_role=pjc.category_id " ."AND u.level>0 " ."ORDER BY u.uname";
 
$header[] = _XF_PRJ_USERNAME;
$header[] = _XF_PRJ_REALNAME;
$header[] = _XF_PRJ_ROLEPOSITION;
$header[] = _XF_PRJ_MESSAGE;
$header[] = _XF_PRJ_SKILLS;
$icmsTpl->assign("header", $header);
 
$content = "";
$res_memb = $icmsDB->query($query);
while ($row_memb = $icmsDB->fetchArray($res_memb))
{
	$content .= "<tr class='bg3'>";
	$content .= "<td>";
	if (trim($row_memb['admin_flags']) == 'A')
	{
		$content .= "<strong><A href='".ICMS_URL."/userinfo.php?uid=".$row_memb['uid']."'>".$row_memb['uname']."</a></strong>";
	}
	else
	{
		$content .= "<A href='".ICMS_URL."/userinfo.php?uid=".$row_memb['uid']."'>".$row_memb['uname']."</a>";
	}
	$content .= "</td>";
	$content .= "<td>".$row_memb['name']."</td>";
	$content .= "<td>".$row_memb['role']."</td>";
	$content .= "<td><a href=\"javascript:openWithSelfMain('".ICMS_URL."/pmlite.php?send2=1&to_userid=".$row_memb['uid']."','pmlite',360,290);\"><img src='".ICMS_URL."/images/icons/pm.gif' width='53' height='17' alt='Personal Message'></a></td>";
	$content .= "<td><A href='".ICMS_URL."/modules/xfjobs/viewprofile.php?user_id=".$row_memb['uid']."'>"._XF_PRJ_VIEW."</a></td>";
	$content .= "</tr>";
}
$icmsTpl->assign("content", $content);
include("../../../footer.php");
?>