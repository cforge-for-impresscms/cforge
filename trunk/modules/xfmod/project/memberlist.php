<?php
/**
  *
  * Project Members Information
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: memberlist.php,v 1.5 2004/01/26 18:57:00 devsupaul Exp $
  *
  */

include_once("../../../mainfile.php");

$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$xoopsOption['template_main'] = 'project/xfmod_memberlist.html';

$project =& group_get_object($group_id);

//group is private
if (!$project->isPublic()) {
	//if it's a private group, you must be a member of that group
	if (!$project->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
	{
		redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
		exit;
	}
}

//for dead projects must be member of xoopsforge project
if (!$project->isActive() && !$perm->isSuperUser()) {
		redirect_header(XOOPS_URL,4,_XF_PRJ_NOTAUTHORIZEDTOENTER);
		exit;
}

include ("../../../header.php");

$xoopsTpl->assign("project_title",project_title($project));
$xoopsTpl->assign("project_tabs",project_tabs('memberlist',$group_id));

// list members
$query = "SELECT u.uname,u.uid,u.name AS realname, ug.admin_flags, pjc.name AS role "
        ."FROM ".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_user_group")." ug,".$xoopsDB->prefix("xf_people_job_category")." pjc "
				."WHERE u.uid=ug.user_id "
				."AND ug.group_id='$group_id' " 
				."AND ug.member_role=pjc.category_id " 
				."AND u.level>0 "
				."ORDER BY u.uname";

$header[] = _XF_PRJ_USERNAME;
$header[] = _XF_PRJ_REALNAME;
$header[] = _XF_PRJ_ROLEPOSITION;
$header[] = _XF_PRJ_MESSAGE;
$header[] = _XF_PRJ_SKILLS;
$xoopsTpl->assign("header",$header);

$content = "";
$res_memb = $xoopsDB->query($query);
while ($row_memb = $xoopsDB->fetchArray($res_memb)) {
	$content .= "<tr class='bg3'>";
	$content .= "<td>";
	if ( trim($row_memb['admin_flags']) == 'A' ) {
		$content .= "<b><A href='".XOOPS_URL."/userinfo.php?uid=".$row_memb['uid']."'>".$row_memb['uname']."</A></b>";
	} else {
		$content .= "<A href='".XOOPS_URL."/userinfo.php?uid=".$row_memb['uid']."'>".$row_memb['uname']."</A>";
	}
	$content .= "</td>";
	$content .= "<td>".$row_memb['name']."</td>";
	$content .= "<td>".$row_memb['role']."</td>";
	$content .= "<td><a href=\"javascript:openWithSelfMain('".XOOPS_URL."/pmlite.php?send2=1&to_userid=".$row_memb['uid']."','pmlite',360,290);\"><img src='".XOOPS_URL."/images/icons/pm.gif' width='53' height='17' alt='Personal Message'></a></td>";
	$content .= "<td><A href='".XOOPS_URL."/modules/xfjobs/viewprofile.php?user_id=".$row_memb['uid']."'>"._XF_PRJ_VIEW."</a></td>";
	$content .= "</tr>";
}
$xoopsTpl->assign("content",$content);
include ("../../../footer.php");
?>