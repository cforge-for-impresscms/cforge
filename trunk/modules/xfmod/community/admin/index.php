<?php
/**
  *
  * Community Admin Main Page
  *
  * This page contains administrative information for the community as well
  * as allows to manage it. This page should be accessible to all community
  * members, but only admins may perform most functions.
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.10 2004/01/26 18:56:56 devsupaul Exp $
  *
  */

include_once ("../../../../mainfile.php");
$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/trove.php");
$xoopsOption['template_main'] = 'community/admin/xfmod_index.html';

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isAdmin()){
	redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
	exit();
}

if(strlen($feedback) > 0)
{
	$xoopsForgeErrorHandler->addMessage($feedback);
	$feedback = "";
}

// Only admin can make modifications via this page
if ($perm->isAdmin() && $func) {
	//
	// updating the database
	//
	if ($func=='adduser') {
		//
		// add user to this community
		//
		if (!$group->addUser($form_unix_name)) {
			$xoopsForgeErrorHandler->addError($group->getErrorMessage());
		} else {
			$xoopsForgeErrorHandler->addMessage(_XF_GRP_ADDEDUSER);
		}

	} else if ($func=='rmuser') {
		//
		// remove a user from this group
		//
		if (!$group->removeUser($rm_id)) {
			$xoopsForgeErrorHandler->addError($group->getErrorMessage());
		} else {
			$xoopsForgeErrorHandler->addMessage(_XF_GRP_REMOVEDUSER);
		}
	} else if ($func == 'addproj') {
		//
		// Associate a project with the community
		//
		$result = $xoopsDB->query("SELECT group_id FROM ".$xoopsDB->prefix("xf_groups")
			." WHERE unix_group_name='$form_proj_name'");

		if(!$result or $xoopsDB->getRowsNum($result) < 1) {
			$xoopsForgeErrorHandler->addError("Attempting to associate invalid project");
		} else {
			$g = $xoopsDB->fetchArray($result);
			$projid = $g['group_id'];
			$result = $xoopsDB->query("SELECT group_id from "
				.$xoopsDB->prefix("xf_trove_group_link")
				." WHERE trove_cat_id=$group_id"
				." AND group_id=$projid");

			if($result and $xoopsDB->getRowsNum($result) > 0) {
				$xoopsForgeErrorHandler->addError("Project $form_proj_name is"
					." already associated to this community");
			} else {
				trove_setnode($projid, $group_id, $TROVE_COMMUNITY);
				$xoopsForgeErrorHandler->addMessage("Project "
					.$form_proj_name." is now associated with this community");
				$form_proj_name = "";
			}
		}
	}
}

$group->clearError();

include (XOOPS_ROOT_PATH."/header.php");

$xoopsTpl->assign("project_title",project_title($group));
$xoopsTpl->assign("project_tabs",project_tabs ('admin', $group_id));
$xoopsTpl->assign("feedback",$xoopsForgeErrorHandler->getDisplayFeedback());
$xoopsTpl->assign("project_admin_header",get_project_admin_header($group_id, $perm,0));

$xoopsTpl->assign("misc_title",_XF_COMM_MISCCOMMINFO);
$xoopsTpl->assign("misc_content","<p>"._XF_G_DESCRIPTION.": ".$ts->makeTareaData4Show($group->getDescription())."<br /></p>"
		."<p><b>"._XF_COMM_TROVECATEGORIZATION.":</b> [ <a href='".XOOPS_URL."/modules/xfmod/project/admin/group_trove.php?group_id=".$group->getID()."'>"
		._XF_G_EDIT."</A> ]</p>");

//	Show the members of this community
$xoopsTpl->assign("members_title",_XF_COMM_GROUPMEMBERS);
$res_memb = $xoopsDB->query("SELECT u.name,u.uid,u.uname,ug.admin_flags "
                           ."FROM ".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_user_group")." ug "
                           ."WHERE u.uid=ug.user_id "
                           ."AND ug.group_id='$group_id'");

$content = "<TABLE WIDTH='100%' BORDER='0'>";

while ($row_memb = $xoopsDB->fetchArray($res_memb)) {

	if (stristr($row_memb['admin_flags'], 'A')) {
		$img = "trash-x.png";
	} else {
		$img = "trash.png";
	}
	if ($perm->isAdmin()) {
		$button = "<INPUT TYPE='IMAGE' NAME='DELETE' SRC='".XOOPS_URL."/modules/xfmod/images/ic/".$img."' HEIGHT='16' WIDTH='16' BORDER='0' alt='remove'>";
	} else {
		$button = "&nbsp;";
	}
	$content .= "<FORM ACTION='".XOOPS_URL."/modules/xfmod/project/admin/rmuser.php' METHOD='POST'>"
             ."<INPUT TYPE='HIDDEN' NAME='func' VALUE='rmuser'>"
             ."<INPUT TYPE='HIDDEN' NAME='return_to' VALUE='".$_SERVER['REQUEST_URI']."'>"
             ."<INPUT TYPE='HIDDEN' NAME='rm_id' VALUE='".$row_memb['uid']."'>"
             ."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='". $group_id ."'>"
             ."<TR><TD ALIGN='MIDDLE'>".$button."</TD></FORM>"
             ."<TD><A href='".XOOPS_URL."/userinfo.php?uid=".$row_memb['uid']."'>".$row_memb['uname']."</A></TD></TR>";
}
$content .= "</TABLE>";

/*
	Add member form
*/

if ($perm->isAdmin()) {

	// After adding user, we go to the permission page for one
$content .= "<HR NoShade SIZE='1'>"
           ."<FORM NAME='adduserform' ACTION='".XOOPS_URL."/modules/xfmod/project/admin/userpermedit.php?group_id=".$group->getID()."' METHOD='POST'>"
           ."<INPUT TYPE='hidden' NAME='func' VALUE='adduser'>"
           ."<TABLE WIDTH='100%' BORDER='0'>"
           ."<TR><TD ALIGN='CENTER' ";
$module_handler =& xoops_gethandler('module');
$membermodule =& $module_handler->getByDirname('xoopsmembers');
$modperm_handler =& xoops_gethandler('groupperm');
if ( $membermodule && $modperm_handler->checkRight("module", $membermodule->mid(), $xoopsUser->getGroups()))
{
	$content .= "COLSPAN='3'>"._XF_COMM_HOWTOADDUSER."</TD></TR>"
       	."<TR><TD ALIGN='RIGHT' WIDTH='50%'><B>"._XF_PRJ_USERNAME.":&nbsp;&nbsp;</B>"
       	."</TD><TD ALIGN='CENTER'><INPUT TYPE='TEXT' NAME='form_unix_name' SIZE='10' VALUE=''>"
		."</TD><TD ALIGN='LEFT' WIDTH='50%'>&nbsp;&nbsp;<a href=\"#\" onClick=\"window.open('".XOOPS_URL."/modules/xoopsmembers/?userlookup=yes&iscomm=yes','userlookup','status,scrollbars,height=480,width=640');return false\">User Lookup</a>"
		."</TD></TR>"
		."<TR><TD COLSPAN='3'";
}
else
{
	$content .= "COLSPAN='2'>"._XF_COMM_HOWTOADDUSER."</TD></TR>"
       	."<TR><TD ALIGN='RIGHT'><B>"._XF_PRJ_USERNAME.":&nbsp;&nbsp;</B></TD><TD><INPUT TYPE='TEXT' NAME='form_unix_name' SIZE='10' VALUE=''>"
		."</TD></TR>"
		."<TR><TD COLSPAN='2'";
}
$content .= " ALIGN='CENTER'><INPUT TYPE='SUBMIT' NAME='submit' VALUE='"._XF_PRJ_ADDUSER."'></TD></TR></FORM>"
           ."</TABLE>"

           ."<HR NoShade SIZE='1'>"
           ."<div align='center'>"
           ."[ <A href='".XOOPS_URL."/modules/xfmod/project/admin/userperms.php?group_id=".$group->getID()."'>"._XF_COMM_EDITMEMBERPERMS."</A> ]"
           ."</div>";
}
$xoopsTpl->assign("members_content",$content);

//	Tool admin pages
$xoopsTpl->assign("tool_title",_XF_COMM_TOOLADMIN);
$content = "<BR>"
          ."<A HREF='".XOOPS_URL."/modules/xfmod/docman/admin/?group_id=".$group->getID()."'>"._XF_COMM_DOCMANAGERADMIN."</A><BR>"
          ."<A HREF='".XOOPS_URL."/modules/xfmod/news/admin/?group_id=".$group->getID()."'>"._XF_COMM_NEWSADMIN."</A><BR>"
          ."<A HREF='".XOOPS_URL."/modules/xfmod/forum/admin/?group_id=".$group->getID()."'>"._XF_COMM_FORUMADMIN."</A><BR>";
$xoopsTpl->assign("tool_content",$content);


/*
	Associated projects
*/

$xoopsTpl->assign("projects_title","Projects");

$content ="<br><FORM NAME='addprojform' ACTION='".XOOPS_URL."/modules/xfmod/community/admin/?group_id=".$group->getID()."' METHOD='POST'>"
           ."<INPUT TYPE='hidden' NAME='func' VALUE='addproj'>"
           ."<TABLE WIDTH='100%' BORDER='0'>"
           ."<TR><TD ALIGN='CENTER' COLSPAN='3'>";
$content .= "To associate a project with this community, enter the project name below and submit</td></tr>"
       		."<TR><TD ALIGN='RIGHT' WIDTH='50%'><B>Project Name:&nbsp;&nbsp;</B>"
       		."</TD><TD ALIGN='CENTER'><INPUT TYPE='TEXT' NAME='form_proj_name' SIZE='10' VALUE='$form_proj_name'>"
			."</TD><TD ALIGN='LEFT' WIDTH='50%'>&nbsp;&nbsp;<a href=\"#\" onClick=\"window.open('".XOOPS_URL."/modules/xftrove/project_list.php?projlookup=yes&iscomm=yes','projlookup','resizable,status,scrollbars,height=480,width=640');return false\">Project Lookup</a>"
			."</TD></TR>";
$content .= "<TR><TD COLSPAN='3' ALIGN='CENTER'><INPUT TYPE='SUBMIT' NAME='submit' VALUE='Add Project'></TD></TR></FORM>";
$content .= "<TR><TD>&nbsp;&nbsp;</TD></TR>";
$content .= "<tr><td colspan='3' align='center'>To change the project associations or featured project list:<BR><a href='"
			.XOOPS_URL."/modules/xfmod/community/admin/proj_list.php?group_id=".$group->getID()."'>"
			."Edit Project List</A>";
$content .= "</TABLE>";

$xoopsTpl->assign("projects_content",$content);

include (XOOPS_ROOT_PATH."/footer.php");
?>