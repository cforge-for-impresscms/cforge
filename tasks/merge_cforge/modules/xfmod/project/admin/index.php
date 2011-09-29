<?php
	/**
	*
	* Project Admin Main Page
	*
	* This page contains administrative information for the project as well
	* as allows to manage it. This page should be accessible to all project
	* members, but only admins may perform most functions.
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.14 2004/02/12 23:55:21 devsupaul Exp $
	*
	*/
	 
	include_once("../../../../mainfile.php");
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/include/nxoopsLDAP.php");
	$icmsOption['template_main'] = 'project/admin/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	function getLDAPConnection()
	{
		global $icmsConfig, $icmsForgeErrorHandler;
		 
		$lldap = new nxoopsLDAP;
		if (!$lldap->connect())
		{
			$icmsForgeErrorHandler->setSystemError("Failed to connect to LDAP server: " . $icmsConfig['ldapserver']);
		}
		 
		if (!$lldap->bindAdmin())
		{
			$ldaperr = $lldap->lastError();
			$lldap->cleanUp();
			$icmsForgeErrorHandler->setSystemError("Failed to bind to LDAP server: " .$ldaperr);
		}
		return $lldap;
	}
	 
	if (isset($feedback) && strlen($feedback) > 0)
	{
		$icmsForgeErrorHandler->addMessage($feedback);
	}
	 
	 
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isAdmin())
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
		exit();
	}
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	// Only admin can make modifications via this page
	if ($perm->isAdmin() && $func)
	{
		 
		$doLDAPTrans = false;
		 
		//
		// updating the database
		//
		if ($func == 'adduser')
		{
			//
			// add user to this project
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
		else if($func == 'chcvs')
		{
			//
			// Change CVS access information
			//
			 
			//$use_cvs = util_http_track_vars('use_cvs');
			//$anon_cvs = util_http_track_vars('anon_cvs');
			if ($group->usesCVS())
			{
				$use_cvs = 1;
			}
			 
			if (!$group->updateCVS($use_cvs, $anon_cvs))
			{
				$icmsForgeErrorHandler->addError($group->getErrorMessage());
			}
			else
				{
				$icmsForgeErrorHandler->addMessage("CVS Access changed");
			}
		}
		else if($func == 'addcvsmon')
		{
			//Insert the email into the database
			$icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_cvs_commit_notify")." VALUES($group_id, ".time().", '$notifyemail')");
			$doLDAPTrans = true;
			 
			$icmsForgeErrorHandler->addMessage("CVS commit monitor added for $notifyemail");
		}
		else if($func == 'rmcvsmon')
		{
			//Remove the email from the database
			$icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_cvs_commit_notify")." WHERE group_id=$group_id AND email='$notifyemail'");
			$doLDAPTrans = true;
			 
			$icmsForgeErrorHandler->addMessage("CVS commit monitor removed for $notifyemail");
		}
		else if($func == 'rmcomm')
		{
			//
			// Remove association with community
			//
			$results = $icmsDB->queryF("DELETE FROM " .$icmsDB->prefix("xf_trove_group_link")
			." WHERE trove_cat_id=$comm_id" ." AND group_id=$group_id");
			 
			if (!$results)
			{
				$icmsForgeErrorHandler->addError("Failed to delete association to community $comm_name: " . $icmsDB->error());
			}
			else
			{
				$icmsForgeErrorHandler->addMessage("Association to community $comm_name removed");
			}
		}
		 
		if ($doLDAPTrans)
		{
			$emailaddrs = array();
			 
			$sql = "SELECT email from ".$icmsDB->prefix("xf_cvs_commit_notify")
			." WHERE group_id=$group_id";
			 
			$res = $icmsDB->queryF($sql);
			 
			if ($res && $icmsDB->getRowsNum($res) > 0)
			{
				while ($therow = $icmsDB->fetchArray($res))
				{
					$emailaddrs[] = $therow['email'];
				}
			}
			 
			$lldap = getLDAPConnection();
			$rc = $lldap->setProjNotify($group->getUnixName(), $emailaddrs);
			 
			$lldap->cleanUp();
			if (!$rc)
			{
				$icmsForgeErrorHandler->addError("Failed to add Project notification transaction. " ."Error: ".$lldap->lastError());
			}
		}
	}
	 
	$group->clearError();
	 
	include("../../../../header.php");
	 
	//meta tag information
	$metaTitle = " "._XF_PRJ_ADMIN." - ".$group->getPublicName();
	$metaKeywords = project_getmetakeywords($group_id);
	$metaDescription = str_replace('"', "&quot;", strip_tags($group->getDescription()));
	 
	$icmsTpl->assign("icms_pagetitle", $metaTitle);
	$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
	$icmsTpl->assign("icms_meta_description", $metaDescription);
	 
	//project nav information
	$icmsTpl->assign("project_title", project_title($group));
	$icmsTpl->assign("project_tabs", project_tabs('admin', $group_id));
	$icmsTpl->assign("feedback", $icmsForgeErrorHandler->getDisplayFeedback());
	$icmsTpl->assign("project_admin_header", get_project_admin_header($group_id, $perm, $group->isProject()));
	 
	 
	$icmsTpl->assign("misc_title", _XF_PRJ_MISCPROJECTINFO);
	$content = "<p>"._XF_G_DESCRIPTION.": ".$ts->makeTareaData4Show($group->getDescription())."<br /></p>" ._XF_G_HOMEPAGE.": ".$group->getHomepage()."<br>" ."<HR NOSHADE>" ."<p><strong>"._XF_PRJ_TROVECATEGORIZATION.":</strong> [ <a href='".ICMS_URL."/modules/xfmod/project/admin/group_trove.php?group_id=".$group->getID()."'>" ._XF_G_EDIT."</a> ]</p>";
	 
	$icmsTpl->assign("misc_content", $content);
	 
	 
	//
	// Show the members of this project
	//
	$icmsTpl->assign("members_title", _XF_PRJ_GROUPMEMBERS);
	 
	$res_memb = $icmsDB->query("SELECT u.name,u.uid,u.uname,ug.admin_flags " ."FROM ".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_user_group")." ug " ."WHERE u.uid=ug.user_id " ."AND ug.group_id='$group_id'");
	 
	$content = "<table width='100%' border='0'>";
	 
	while ($row_memb = $icmsDB->fetchArray($res_memb))
	{
		 
		if ($perm->isAdmin())
		{
			if (stristr($row_memb['admin_flags'], 'A'))
			{
				$button = "<img src='".ICMS_URL."/modules/xfmod/images/ic/trash-x.png' height=16 width=16 border=0 alt='can not remove'>";
			}
			else
			{
				$button = "<input type='IMAGE' name='DELETE' SRC='".ICMS_URL."/modules/xfmod/images/ic/trash.png' HEIGHT='16' width='16' border='0' alt='remove'>";
			}
		}
		else
		{
			$button = "&nbsp;";
		}
		$content .= "<form action='rmuser.php' METHOD='POST'>" ."<input type='hidden' name='func' value='rmuser'>" ."<input type='hidden' name='return_to' value='".$_SERVER['REQUEST_URI']."'>" ."<input type='hidden' name='rm_id' value='".$row_memb['uid']."'>" ."<input type='hidden' name='group_id' value='". $group_id ."'>" ."<th><td align='middle'>".$button."</td></form>" ."<td><A href='".ICMS_URL."/userinfo.php?uid=".$row_memb['uid']."'>".$row_memb['uname']."</a></td></th>";
	}
	$content .= "</table>";
	 
	//
	// Add member form
	//
	if ($perm->isAdmin())
	{
		 
		// After adding user, we go to the permission page for one
		$content .= "<hr NoShade size='1'>" ."<form name='adduserform' ACTION='userpermedit.php?group_id=".$group->getID()."' METHOD='POST'>" ."<input type='hidden' name='func' value='adduser'>" ."<table width='100%' border='0'>" ."<th><td align='center' ";
		//the user is an admin, we already know that so these checks are unnessesary
		/*$module_handler = icms_gethandler('module');
		$membermodule = $module_handler->getByDirname('xoopsmembers');
		$modperm_handler = icms_gethandler('groupperm');
		if ($membermodule && $modperm_handler->checkRight("module", $membermodule->mid(), $icmsUser->getGroups()))
		{*/
		$content .= "colspan='3'>"._XF_PRJ_HOWTOADDUSER."</td></th>" ."<th><td align='right' width='50%'><strong>"._XF_PRJ_USERNAME.":&nbsp;&nbsp;</strong>" ."</td><td align='center'><input type='text' name='form_unix_name' size='10' value=''>" ."</td><td align='left' width='50%'>&nbsp;&nbsp;<a href=\"#\" onClick=\"window.open('".ICMS_URL."/modules/xoopsmembers/?userlookup=yes&isprj=yes','userlookup','status,scrollbars,height=480,width=800');return false\">User Lookup</a>" ."</td></th>" ."<th><td colspan='3'";
		/*}
		else
	{
		$content .= "colspan='2'>"._XF_PRJ_HOWTOADDUSER."</td></th>"
		."<th><td align='right'><strong>"._XF_PRJ_USERNAME.":&nbsp;&nbsp;</strong></td><td><input type='text' name='form_unix_name' size='10' value=''>"
		."</td></th>"
		."<th><td colspan='2'";
		}*/
		$content .= " align='center'><input type='submit' name='submit' value='"._XF_PRJ_ADDUSER."'></td></th></form>" ."</table>"  
		."<hr NoShade size='1'>" ."<div align='center'>" ."[ <A href='".ICMS_URL."/modules/xfmod/project/admin/userperms.php?group_id=".$group->getID()."'>"._XF_PRJ_EDITMEMBERPERMS."</a> ]" ."</div>";
	}
	 
	$icmsTpl->assign("members_content", $content);
	 
	 
	 
	//
	// CVS Administration
	//
	if ($group->isProject())
	{
		$icmsTpl->assign("isProject", true);
		$icmsTpl->assign("cvs_title", "CVS Administration");
		$cvsDisabled = "";
		$usesCVS = "";
		$anonCVS = "";
		$anonDisabled = "";
		if ($group->usesCVS())
		{
			$cvsDisabled = "disabled";
			$usesCVS = "checked";
			if ($group->anonCVS())
			{
				$anonCVS = "checked";
			}
		}
		 
		if (!$perm->isAdmin())
		{
			$cvsDisabled = "disabled";
			$anonDisabled = "disabled";
		}
		 
		$content = "<form action='".$_SERVER['PHP_SELF']."'method='post'>" . "<table width='100%' border='0'>" ."<tr><td><input $cvsDisabled type='checkbox' name='use_cvs' value='1' $usesCVS>Use CVS</td></tr>" ."<tr><td><input $anonDisabled type='checkbox' name='anon_cvs' value='1' $anonCVS>Anonymous CVS Access</td></tr>";
		 
		 
		if ($perm->isAdmin())
		{
			$content .= "<tr><td colspan='2' align='center'>" ."<input type='submit' name='chgcvs' value='Change CVS'>" ."</td></tr>" ."<input type='hidden' name='func' value='chcvs'>";
		}
		 
		$content .= "<input type='hidden' name='group_id' value='" .$group->getID()."'>" ."<input type='hidden' name='return_to' value='" .$_SERVER['REQUEST_URI']."'>" . "</table></form>";
		$content .= '<SCRIPT LANGUAGE="JavaScript"><!--
			function validEmail(email){
			invalidChars = " /:,;";
			if (email =="") {
			return false;
			}
			for(i=0;i<invalidChars.length;i++){
			badChar = invalidChars.charAt(i);
			if (email.indexOf(badChar,0)!=-1){
			return false;
			}
			}
			atPos = email.indexOf("@",1);
			if (atPos == -1){
			return false;
			}
			if (email.indexOf("@",atPos+1)!=-1){
			return false;
			}
			periodPos = email.indexOf(".",atPos);
			if (periodPos == -1){
			return flase;
			}
			if (periodPos+3 > email.length){
			return false;
			}
			return true;
			}
			function validate(form){
			if (!validEmail(form.notifyemail.value)){
			alert("Invalid email address");
			form.notifyemail.focus();
			form.notifyemail.select();
			return false;
			}
			return true;
			}
			--></SCRIPT>';
		 
		$content .= "<hr><p>The form below allows you to submit any valid email address to be notified when a cvs commit happens." ."  You may want to use a development mailing list rather than your personal email address.</p>";
		$content .= "<form  method='POST' action='".$_SERVER['PHP_SELF']."'>" ."<input type='hidden' name='group_id' value='".$group_id."'>" ."<input type='hidden' name='func' value='addcvsmon'>" ."<input type='text' name='notifyemail'>" ."<input type='submit' value='Submit'>" ."</form>";
		$r = $icmsDB->query("SELECT email FROM ".$icmsDB->prefix("xf_cvs_commit_notify")." where group_id=$group_id");
		if ($r) $content .= "<hr>";
		while ($row = $icmsDB->fetchArray($r))
		{
			$content .= "<a href='".$_SERVER['PHP_SELF']."?func=rmcvsmon&group_id=$group_id&notifyemail=".urlencode($row['email'])."'>" ."<img src='".ICMS_URL."/modules/xfmod/images/ic/trash.png' alt='remove cvs monitor'>" ."</a> ".$row['email']."<br>";
		}
		 
		 
		$icmsTpl->assign("cvs_content", $content);
		 
	}
	 
	//
	// Associated Communities
	//
	$icmsTpl->assign("comm_title", "Associated Communities");
	$content = "<table width='100%' border='0'>";
	 
	$sql = "SELECT p.group_id,p.unix_group_name FROM " .$icmsDB->prefix("xf_groups")." AS p, " .$icmsDB->prefix("xf_trove_group_link")." AS c" ." WHERE c.group_id=$group_id" ." AND p.group_id=c.trove_cat_id";
	 
	$res_memb = $icmsDB->query($sql);
	while ($row = $icmsDB->fetchArray($res_memb))
	{
		 
		if ($perm->isAdmin())
		{
			$button = "<input type='image' name='delete' src='".ICMS_URL. "/modules/xfmod/images/ic/trash.png' height='16' width='16' border='0' alt='delete'>";
		}
		else
		{
			$button = "&nbsp;";
		}
		 
		$content .= "<form action=".ICMS_URL."/modules/xfmod/project/admin/?group_id=$group_id " ." method='post'" .'onSubmit="return verify(\''.$row['unix_group_name'].'\')">' ."<input type='hidden' name='func' value='rmcomm'>" ."<input type='hidden' name='comm_id' value='".$row['group_id']."'>" ."<input type='hidden' name='comm_name' value='".$row['unix_group_name']."'>" ."<tr><td align='middle'>".$button."</td></form>" ."<td><a href='".ICMS_URL."/modules/xfmod/community/?".$row['unix_group_name']."'>" .$row['unix_group_name']."</a></td></tr>";
		 
		 
	}
	$content .= "</table>";
	$icmsTpl->assign("comm_content", $content);
	 
	 
	//
	// Tool admin pages
	//
	$icmsTpl->assign("tool_title", _XF_PRJ_TOOLADMIN);
	 
	$content = "<BR>" ."<a href='".ICMS_URL."/modules/xfmod/tracker/admin/?group_id=".$group->getID()."'>"._XF_PRJ_TRACKERADMIN."</a><BR>" ."<a href='".ICMS_URL."/modules/xfmod/docman/admin/?group_id=".$group->getID()."'>"._XF_PRJ_DOCMANAGERADMIN."</a><BR>" ."<a href='".ICMS_URL."/modules/xfmod/news/admin/?group_id=".$group->getID()."'>"._XF_PRJ_NEWSADMIN."</a><BR>" ."<a href='".ICMS_URL."/modules/xfmod/pm/admin/?group_id=".$group->getID()."'>"._XF_PRJ_TASKADMIN."</a><BR>" ."<a href='".ICMS_URL."/modules/xfmod/forum/admin/?group_id=".$group->getID()."'>"._XF_PRJ_FORUMADMIN."</a><BR>";
	 
	$icmsTpl->assign("tool_content", $content);
	 
	$icmsTpl->assign("file_title", _XF_PRJ_FILERELEASES);
	 
	$content = "&nbsp;<BR>" ."<CENTER>" ."[ <A href='editpackages.php?group_id=".$group_id."'><strong>"._XF_PRJ_RELEASEEDITFILERELEASES."</strong></a> ]" ."</CENTER>" ."<HR>" ."<strong>"._XF_PRJ_PACKAGES.":</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ."<A href='".ICMS_URL."/modules/xfmod/help/projects.php#downloading_projects'><i>"._XF_PRJ_WHATISTHIS."</i></a>" ."<p>";
	 
	$res_module = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_frs_package")." WHERE group_id='$group_id'");
	while ($row_module = $icmsDB->fetchArray($res_module))
	{
		$content .= $row_module['name']."<BR>";
	}
	 
	$icmsTpl->assign("file_content", $content);
	 
	include("../../../../footer.php");
?>