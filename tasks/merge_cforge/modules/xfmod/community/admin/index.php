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
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.10 2004/01/26 18:56:56 devsupaul Exp $
	*
	*/
	 
	include_once("../../../../mainfile.php");
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/trove.php");
	$icmsOption['template_main'] = 'community/admin/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isAdmin())
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
		exit();
	}
	 
	if (strlen($feedback) > 0)
	{
		$icmsForgeErrorHandler->addMessage($feedback);
		$feedback = "";
	}
	 
	// Only admin can make modifications via this page
	if ($perm->isAdmin() && $func)
	{
		//
		// updating the database
		//
		if ($func == 'adduser')
		{
			//
			// add user to this community
			//
			if (!$group->addUser($form_unix_name))
			{
				$icmsForgeErrorHandler->addError($group->getErrorMessage());
			}
			else
			{
				$icmsForgeErrorHandler->addMessage(_XF_GRP_ADDEDUSER);
			}
			 
		}
		else if($func == 'rmuser')
		{
			//
			// remove a user from this group
			//
			if (!$group->removeUser($rm_id))
			{
				$icmsForgeErrorHandler->addError($group->getErrorMessage());
			}
			else
			{
				$icmsForgeErrorHandler->addMessage(_XF_GRP_REMOVEDUSER);
			}
		}
		else if($func == 'addproj')
		{
			//
			// Associate a project with the community
			//
			$result = $icmsDB->query("SELECT group_id FROM ".$icmsDB->prefix("xf_groups")
			." WHERE unix_group_name='$form_proj_name'");
			 
			if (!$result or $icmsDB->getRowsNum($result) < 1)
			{
				$icmsForgeErrorHandler->addError("Attempting to associate invalid project");
			}
			else
			{
				$g = $icmsDB->fetchArray($result);
				$projid = $g['group_id'];
				$result = $icmsDB->query("SELECT group_id from " .$icmsDB->prefix("xf_trove_group_link")
				." WHERE trove_cat_id=$group_id" ." AND group_id=$projid");
				 
				if ($result and $icmsDB->getRowsNum($result) > 0)
				{
					$icmsForgeErrorHandler->addError("Project $form_proj_name is" ." already associated to this community");
				}
				else
				{
					trove_setnode($projid, $group_id, $TROVE_COMMUNITY);
					$icmsForgeErrorHandler->addMessage("Project " .$form_proj_name." is now associated with this community");
					$form_proj_name = "";
				}
			}
		}
	}
	 
	$group->clearError();
	 
	include(ICMS_ROOT_PATH."/header.php");
	 
	$icmsTpl->assign("project_title", project_title($group));
	$icmsTpl->assign("project_tabs", project_tabs('admin', $group_id));
	$icmsTpl->assign("feedback", $icmsForgeErrorHandler->getDisplayFeedback());
	$icmsTpl->assign("project_admin_header", get_project_admin_header($group_id, $perm, 0));
	 
	$icmsTpl->assign("misc_title", _XF_COMM_MISCCOMMINFO);
	$icmsTpl->assign("misc_content", "<p>"._XF_G_DESCRIPTION.": ".$ts->makeTareaData4Show($group->getDescription())."<br /></p>" ."<p><strong>"._XF_COMM_TROVECATEGORIZATION.":</strong> [ <a href='".ICMS_URL."/modules/xfmod/project/admin/group_trove.php?group_id=".$group->getID()."'>" ._XF_G_EDIT."</a> ]</p>");
	 
	// Show the members of this community
	$icmsTpl->assign("members_title", _XF_COMM_GROUPMEMBERS);
	$res_memb = $icmsDB->query("SELECT u.name,u.uid,u.uname,ug.admin_flags " ."FROM ".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_user_group")." ug " ."WHERE u.uid=ug.user_id " ."AND ug.group_id='$group_id'");
	 
	$content = "<table width='100%' border='0'>";
	 
	while ($row_memb = $icmsDB->fetchArray($res_memb))
	{
		 
		if (stristr($row_memb['admin_flags'], 'A'))
		{
			$img = "trash-x.png";
		}
		else
		{
			$img = "trash.png";
		}
		if ($perm->isAdmin())
		{
			$button = "<input type='IMAGE' name='DELETE' SRC='".ICMS_URL."/modules/xfmod/images/ic/".$img."' HEIGHT='16' width='16' border='0' alt='remove'>";
		}
		else
		{
			$button = "&nbsp;";
		}
		$content .= "<form action='".ICMS_URL."/modules/xfmod/project/admin/rmuser.php' METHOD='POST'>" ."<input type='hidden' name='func' value='rmuser'>" ."<input type='hidden' name='return_to' value='".$_SERVER['REQUEST_URI']."'>" ."<input type='hidden' name='rm_id' value='".$row_memb['uid']."'>" ."<input type='hidden' name='group_id' value='". $group_id ."'>" ."<tr><td align='middle'>".$button."</td></form>" ."<td><A href='".ICMS_URL."/userinfo.php?uid=".$row_memb['uid']."'>".$row_memb['uname']."</a></td></tr>";
	}
	$content .= "</table>";
	 
	/*
	Add member form
	*/
	 
	if ($perm->isAdmin())
	{
		 
		// After adding user, we go to the permission page for one
		$content .= "<hr NoShade size='1'>" ."<form name='adduserform' ACTION='".ICMS_URL."/modules/xfmod/project/admin/userpermedit.php?group_id=".$group->getID()."' METHOD='POST'>" ."<input type='hidden' name='func' value='adduser'>" ."<table border='0' width='100%'>" ."<tr><td align='center' ";
		$module_handler = icms_gethandler('module');
		$membermodule = $module_handler->getByDirname('xoopsmembers');
		$modperm_handler = icms_gethandler('groupperm');
		if ($membermodule && $modperm_handler->checkRight("module", $membermodule->mid(), $icmsUser->getGroups()))
		{
			$content .= "colspan='3'>"._XF_COMM_HOWTOADDUSER."</td></tr>" ."<tr><td align='right' width='50%'><strong>"._XF_PRJ_USERNAME.":&nbsp;&nbsp;</strong>" ."</td><td align='center'><input type='text' name='form_unix_name' size='10' value=''>" ."</td><td align='left' width='50%'>&nbsp;&nbsp;<a href=\"#\" onClick=\"window.open('".ICMS_URL."/modules/xoopsmembers/?userlookup=yes&iscomm=yes','userlookup','status,scrollbars,height=480,width=640');return false\">User Lookup</a>" ."</td></tr>" ."<tr><td colspan='3'";
		}
		else
		{
			$content .= "colspan='2'>"._XF_COMM_HOWTOADDUSER."</td></tr>" ."<tr><td align='right'><strong>"._XF_PRJ_USERNAME.":&nbsp;&nbsp;</strong></td><td><input type='text' name='form_unix_name' size='10' value=''>" ."</td></tr>" ."<tr><td colspan='2'";
		}
		$content .= " align='center'><input type='submit' name='submit' value='"._XF_PRJ_ADDUSER."'></td></tr></form>" ."</table>"  
		."<hr NoShade size='1'>" ."<div align='center'>" ."[ <A href='".ICMS_URL."/modules/xfmod/project/admin/userperms.php?group_id=".$group->getID()."'>"._XF_COMM_EDITMEMBERPERMS."</a> ]" ."</div>";
	}
	$icmsTpl->assign("members_content", $content);
	 
	// Tool admin pages
	$icmsTpl->assign("tool_title", _XF_COMM_TOOLADMIN);
	$content = "<BR>" ."<a href='".ICMS_URL."/modules/xfmod/docman/admin/?group_id=".$group->getID()."'>"._XF_COMM_DOCMANAGERADMIN."</a><BR>" ."<a href='".ICMS_URL."/modules/xfmod/news/admin/?group_id=".$group->getID()."'>"._XF_COMM_NEWSADMIN."</a><BR>" ."<a href='".ICMS_URL."/modules/xfmod/forum/admin/?group_id=".$group->getID()."'>"._XF_COMM_FORUMADMIN."</a><BR>";
	$icmsTpl->assign("tool_content", $content);
	 
	 
	/*
	Associated projects
	*/
	 
	$icmsTpl->assign("projects_title", "Projects");
	 
	$content = "<br><form name='addprojform' ACTION='".ICMS_URL."/modules/xfmod/community/admin/?group_id=".$group->getID()."' METHOD='POST'>" ."<input type='hidden' name='func' value='addproj'>" ."<table width='100%' border='0'>" ."<tr><td align='center' colspan='3'>";
	$content .= "To associate a project with this community, enter the project name below and submit</td></tr>" ."<tr><td align='right' width='50%'><strong>Project Name:&nbsp;&nbsp;</strong>" ."</td><td align='center'><input type='text' name='form_proj_name' size='10' value='$form_proj_name'>" ."</td><td align='left' width='50%'>&nbsp;&nbsp;<a href=\"#\" onClick=\"window.open('".ICMS_URL."/modules/xftrove/project_list.php?projlookup=yes&iscomm=yes','projlookup','resizable,status,scrollbars,height=480,width=640');return false\">Project Lookup</a>" ."</td></tr>";
	$content .= "<tr><td colspan='3' align='center'><input type='submit' name='submit' value='Add Project'></td></tr></form>";
	$content .= "<tr><td>&nbsp;&nbsp;</td></tr>";
	$content .= "<tr><td colspan='3' align='center'>To change the project associations or featured project list:<BR><a href='" .ICMS_URL."/modules/xfmod/community/admin/proj_list.php?group_id=".$group->getID()."'>" ."Edit Project List</a>";
	$content .= "</table>";
	 
	$icmsTpl->assign("projects_content", $content);
	 
	include(ICMS_ROOT_PATH."/footer.php");
?>