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
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.14 2004/02/12 23:55:21 devsupaul Exp $
  *
  */

include_once ("../../../../mainfile.php");
$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/nxoopsLDAP.php");
$xoopsOption['template_main'] = 'project/admin/xfmod_index.html';

function getLDAPConnection()
{
	global $xoopsConfig, $xoopsForgeErrorHandler;

	$lldap = new nxoopsLDAP;
	if(!$lldap->connect())
	{
		$xoopsForgeErrorHandler->setSystemError("Failed to connect to LDAP server: "
			. $xoopsConfig['ldapserver']);
	}

	if(!$lldap->bindAdmin())
	{
		$ldaperr = $lldap->lastError();
		$lldap->cleanUp();
		$xoopsForgeErrorHandler->setSystemError("Failed to bind to LDAP server: "
			.$ldaperr);
	}
	return $lldap;
}

if(isset($feedback) && strlen($feedback) > 0){
	$xoopsForgeErrorHandler->addMessage($feedback);
}

$group_id = http_get('group_id');
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isAdmin()){
	redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
	exit();
}

$func = http_get('func');

// Only admin can make modifications via this page
if ($perm->isAdmin() && $func)
{

	$doLDAPTrans = false;

	//
	// updating the database
	//
	if ($func=='adduser')
	{
		//
		// add user to this project
		//

		if (!$group->addUser($form_unix_name))
		{
			$xoopsForgeErrorHandler->addError($group->getErrorMessage());
		}
		else
		{
			$xoopsForgeErrorHandler->addMessage(_XF_GRP_ADDEDUSER);
		}

	}
	else if ($func=='rmuser')
	{
		//
		// remove a user from this group
		//
		if (!$group->removeUser($rm_id))
		{
			$xoopsForgeErrorHandler->addError($group->getErrorMessage());
		}
		else
		{
			$xoopsForgeErrorHandler->addMessage(_XF_GRP_REMOVEDUSER);
		}
	}
	else if ($func == 'chcvs')
	{
		//
		// Change CVS access information
		//

		$use_cvs = util_http_track_vars('use_cvs');
		$anon_cvs = util_http_track_vars('anon_cvs');
		if($group->usesCVS())
		{
			$use_cvs = 1;
		}

		if(!$group->updateCVS($use_cvs, $anon_cvs))
		{
			$xoopsForgeErrorHandler->addError($group->getErrorMessage());
		}
		else
		{
			$xoopsForgeErrorHandler->addMessage("CVS Access changed");
		}
	}
	else if ($func == 'addcvsmon')
	{
		//Insert the email into the database
		$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_cvs_commit_notify")." VALUES ($group_id, ".time().", '$notifyemail')");
		$doLDAPTrans = true;

		$xoopsForgeErrorHandler->addMessage("CVS commit monitor added for $notifyemail");
	}
	else if ($func == 'rmcvsmon')
	{
		//Remove the email from the database
		$xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_cvs_commit_notify")." WHERE group_id=$group_id AND email='$notifyemail'");
		$doLDAPTrans = true;

		$xoopsForgeErrorHandler->addMessage("CVS commit monitor removed for $notifyemail");
	}
	else if ($func == 'rmcomm')
	{
		//
		// Remove association with community
		//
		$results = $xoopsDB->queryF("DELETE FROM "
			.$xoopsDB->prefix("xf_trove_group_link")
			." WHERE trove_cat_id=$comm_id"
			." AND group_id=$group_id");

		if(!$results) {
			$xoopsForgeErrorHandler->addError("Failed to delete association to community $comm_name: "
				. $xoopsDB->error());
		} else {
			$xoopsForgeErrorHandler->addMessage("Association to community $comm_name removed");
		}
	}

	if($doLDAPTrans)
	{
		$emailaddrs = array();

		$sql = "SELECT email from ".$xoopsDB->prefix("xf_cvs_commit_notify")
			." WHERE group_id=$group_id";

		$res = $xoopsDB->queryF($sql);

		if($res && $xoopsDB->getRowsNum($res) > 0)
		{
			while($therow = $xoopsDB->fetchArray($res))
			{
				$emailaddrs[] = $therow['email'];
			}
		}

		$lldap = getLDAPConnection();
		$rc = $lldap->setProjNotify($group->getUnixName(), $emailaddrs);

		$lldap->cleanUp();
		if(!$rc)
		{
			$xoopsForgeErrorHandler->addError("Failed to add Project notification transaction. "
				."Error: ".$lldap->lastError());
		}
	}
}

$group->clearError();

include ("../../../../header.php");

//meta tag information
$metaTitle=" "._XF_PRJ_ADMIN." - ".$group->getPublicName();
$metaKeywords=project_getmetakeywords($group_id);
$metaDescription=str_replace('"', "&quot;", strip_tags($group->getDescription()));

$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
$xoopsTpl->assign("xoops_meta_description", $metaDescription);

//project nav information
$xoopsTpl->assign("project_title",project_title($group));
$xoopsTpl->assign("project_tabs",project_tabs ('admin', $group_id));
$xoopsTpl->assign("feedback",$xoopsForgeErrorHandler->getDisplayFeedback());
$xoopsTpl->assign("project_admin_header",get_project_admin_header($group_id, $perm, $group->isProject()));


$xoopsTpl->assign("misc_title",_XF_PRJ_MISCPROJECTINFO);
$content = "<p>"._XF_G_DESCRIPTION.": ".$ts->makeTareaData4Show($group->getDescription())."<br /></p>"
          ._XF_G_HOMEPAGE.": ".$group->getHomepage()."<br>"
		."<HR NOSHADE>"
		."<p><b>"._XF_PRJ_TROVECATEGORIZATION.":</b> [ <a href='".XOOPS_URL."/modules/xfmod/project/admin/group_trove.php?group_id=".$group->getID()."'>"
		._XF_G_EDIT."</A> ]</p>";

$xoopsTpl->assign("misc_content",$content);


//
// Show the members of this project
//
$xoopsTpl->assign("members_title",_XF_PRJ_GROUPMEMBERS);

$res_memb = $xoopsDB->query("SELECT u.name,u.uid,u.uname,ug.admin_flags "
                           ."FROM ".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_user_group")." ug "
                           ."WHERE u.uid=ug.user_id "
                           ."AND ug.group_id='$group_id'");

$content = "<TABLE WIDTH='100%' BORDER='0'>";

while ($row_memb = $xoopsDB->fetchArray($res_memb)) {

	if ($perm->isAdmin()) {
		if (stristr($row_memb['admin_flags'], 'A')) {
			$button = "<img src='".XOOPS_URL."/modules/xfmod/images/ic/trash-x.png' height=16 width=16 border=0 alt='can not remove'>";
		} else {
			$button = "<INPUT TYPE='IMAGE' NAME='DELETE' SRC='".XOOPS_URL."/modules/xfmod/images/ic/trash.png' HEIGHT='16' WIDTH='16' BORDER='0' alt='remove'>";
		}
	} else {
		$button = "&nbsp;";
	}
	$content .= "<FORM ACTION='rmuser.php' METHOD='POST'>"
             ."<INPUT TYPE='HIDDEN' NAME='func' VALUE='rmuser'>"
             ."<INPUT TYPE='HIDDEN' NAME='return_to' VALUE='".$_SERVER['REQUEST_URI']."'>"
             ."<INPUT TYPE='HIDDEN' NAME='rm_id' VALUE='".$row_memb['uid']."'>"
             ."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='". $group_id ."'>"
             ."<TR><TD ALIGN='MIDDLE'>".$button."</TD></FORM>"
             ."<TD><A href='".XOOPS_URL."/userinfo.php?uid=".$row_memb['uid']."'>".$row_memb['uname']."</A></TD></TR>";
}
$content .= "</TABLE>";

//
// Add member form
//
if ($perm->isAdmin()) {

	// After adding user, we go to the permission page for one
$content .= "<HR NoShade SIZE='1'>"
           ."<FORM NAME='adduserform' ACTION='userpermedit.php?group_id=".$group->getID()."' METHOD='POST'>"
           ."<INPUT TYPE='hidden' NAME='func' VALUE='adduser'>"
           ."<TABLE WIDTH='100%' BORDER='0'>"
           ."<TR><TD ALIGN='CENTER' ";
//the user is an admin, we already know that so these checks are unnessesary
/*$module_handler =& xoops_gethandler('module');
$membermodule =& $module_handler->getByDirname('xoopsmembers');
$modperm_handler =& xoops_gethandler('groupperm');
if ( $membermodule && $modperm_handler->checkRight("module", $membermodule->mid(), $xoopsUser->getGroups()))
{*/
	$content .= "COLSPAN='3'>"._XF_PRJ_HOWTOADDUSER."</TD></TR>"
       	."<TR><TD ALIGN='RIGHT' WIDTH='50%'><B>"._XF_PRJ_USERNAME.":&nbsp;&nbsp;</B>"
       	."</TD><TD ALIGN='CENTER'><INPUT TYPE='TEXT' NAME='form_unix_name' SIZE='10' VALUE=''>"
		."</TD><TD ALIGN='LEFT' WIDTH='50%'>&nbsp;&nbsp;<a href=\"#\" onClick=\"window.open('".XOOPS_URL."/modules/xoopsmembers/?userlookup=yes&isprj=yes','userlookup','status,scrollbars,height=480,width=800');return false\">User Lookup</a>"
		."</TD></TR>"
		."<TR><TD COLSPAN='3'";
/*}
else
{
	$content .= "COLSPAN='2'>"._XF_PRJ_HOWTOADDUSER."</TD></TR>"
       	."<TR><TD ALIGN='RIGHT'><B>"._XF_PRJ_USERNAME.":&nbsp;&nbsp;</B></TD><TD><INPUT TYPE='TEXT' NAME='form_unix_name' SIZE='10' VALUE=''>"
		."</TD></TR>"
		."<TR><TD COLSPAN='2'";
}*/
$content .= " ALIGN='CENTER'><INPUT TYPE='SUBMIT' NAME='submit' VALUE='"._XF_PRJ_ADDUSER."'></TD></TR></FORM>"
           ."</TABLE>"

           ."<HR NoShade SIZE='1'>"
           ."<div align='center'>"
           ."[ <A href='".XOOPS_URL."/modules/xfmod/project/admin/userperms.php?group_id=".$group->getID()."'>"._XF_PRJ_EDITMEMBERPERMS."</A> ]"
           ."</div>";
}

$xoopsTpl->assign("members_content",$content);



//
// CVS Administration
//
if($group->isProject())
{
	$xoopsTpl->assign("isProject",true);
	$xoopsTpl->assign("cvs_title","CVS Administration");
	$cvsDisabled = "";
	$usesCVS = "";
	$anonCVS = "";
	$anonDisabled = "";
	if($group->usesCVS())
	{
		$cvsDisabled = "disabled";
		$usesCVS = "checked";
		if($group->anonCVS())
		{
			$anonCVS = "checked";
		}
	}

	if(!$perm->isAdmin())
	{
		$cvsDisabled = "disabled";
		$anonDisabled = "disabled";
	}

	$content = "<form action='".$_SERVER['PHP_SELF']."'method='post'>"
		. "<table width='100%' border='0'>"
		."<tr><td><input $cvsDisabled type='checkbox' name='use_cvs' value='1' $usesCVS>Use CVS</td></tr>"
		."<tr><td><input $anonDisabled type='checkbox' name='anon_cvs' value='1' $anonCVS>Anonymous CVS Access</td></tr>";


        if($perm->isAdmin())
        {
            $content .= "<tr><td colspan='2' align='center'>"
                ."<input type='submit' name='chgcvs' value='Change CVS'>"
                ."</td></tr>"
                ."<input type='hidden' name='func' value='chcvs'>";
        }

        $content .="<input type='hidden' name='group_id' value='"
            .$group->getID()."'>"
            ."<input type='hidden' name='return_to' value='"
            .$_SERVER['REQUEST_URI']."'>"
            . "</table></form>";
		$content .='<SCRIPT LANGUAGE="JavaScript"><!--
						function validEmail(email){
							invalidChars = " /:,;";
							if(email =="") {
								return false;
							}
							for (i=0;i<invalidChars.length;i++){
								badChar = invalidChars.charAt(i);
								if(email.indexOf(badChar,0)!=-1){
									return false;
								}
							}
							atPos = email.indexOf("@",1);
							if(atPos == -1){
								return false;
							}
							if(email.indexOf("@",atPos+1)!=-1){
								return false;
							}
							periodPos = email.indexOf(".",atPos);
							if(periodPos == -1){
								return flase;
							}
							if(periodPos+3 > email.length){
								return false;
							}
							return true;
						}
						function validate(form){
							if(!validEmail(form.notifyemail.value)){
								alert("Invalid email address");
								form.notifyemail.focus();
								form.notifyemail.select();
								return false;
							}
							return true;
						}
						--></SCRIPT>';

		$content .="<hr><p>The form below allows you to submit any valid email address to be notified when a cvs commit happens."
				."  You may want to use a development mailing list rather than your personal email address.</p>";
		$content .="<FORM  method='POST' action='".$_SERVER['PHP_SELF']."'>"
				."<INPUT type='hidden' name='group_id' value='".$group_id."'>"
				."<INPUT type='hidden' name='func' value='addcvsmon'>"
				."<INPUT type='text' name='notifyemail'>"
				."<INPUT type='submit' value='Submit'>"
				."</FORM>";
		$r = $xoopsDB->query("SELECT email FROM ".$xoopsDB->prefix("xf_cvs_commit_notify")." where group_id=$group_id");
		if($r) $content .="<hr>";
		while($row=$xoopsDB->fetchArray($r)){
			$content .="<a href='".$_SERVER['PHP_SELF']."?func=rmcvsmon&group_id=$group_id&notifyemail=".urlencode($row['email'])."'>"
					."<img src='".XOOPS_URL."/modules/xfmod/images/ic/trash.png' alt='remove cvs monitor'>"
					."</a> ".$row['email']."<br>";
		}


	$xoopsTpl->assign("cvs_content",$content);

}

//
// Associated Communities
//
$xoopsTpl->assign("comm_title","Associated Communities");
$content = "<TABLE WIDTH='100%' BORDER='0'>";

$sql = "SELECT p.group_id,p.unix_group_name FROM "
        .$xoopsDB->prefix("xf_groups")." AS p, "
        .$xoopsDB->prefix("xf_trove_group_link")." AS c"
	." WHERE c.group_id=$group_id"
	." AND p.group_id=c.trove_cat_id";

$res_memb = $xoopsDB->query($sql);
while ($row = $xoopsDB->fetchArray($res_memb)) {

	if ($perm->isAdmin()) {
		$button = "<input type='image' name='delete' src='".XOOPS_URL.
			"/modules/xfmod/images/ic/trash.png' height='16' width='16' border='0' alt='delete'>";
	} else {
		$button = "&nbsp;";
	}

	$content .= "<form action=".XOOPS_URL."/modules/xfmod/project/admin/?group_id=$group_id "
		." method='post'"
		.'onSubmit="return verify(\''.$row['unix_group_name'].'\')">'
             	."<input type='hidden' name='func' value='rmcomm'>"
             	."<input type='hidden' name='comm_id' value='".$row['group_id']."'>"
             	."<input type='hidden' name='comm_name' value='".$row['unix_group_name']."'>"
             	."<tr><td align='middle'>".$button."</td></form>"
		."<td><a href='".XOOPS_URL."/modules/xfmod/community/?".$row['unix_group_name']."'>"
		.$row['unix_group_name']."</a></td></tr>";


}
$content .= "</TABLE>";
$xoopsTpl->assign("comm_content",$content);


//
//	Tool admin pages
//
$xoopsTpl->assign("tool_title",_XF_PRJ_TOOLADMIN);

$content = "<BR>"
          ."<A HREF='".XOOPS_URL."/modules/xfmod/tracker/admin/?group_id=".$group->getID()."'>"._XF_PRJ_TRACKERADMIN."</A><BR>"
          ."<A HREF='".XOOPS_URL."/modules/xfmod/docman/admin/?group_id=".$group->getID()."'>"._XF_PRJ_DOCMANAGERADMIN."</A><BR>"
          ."<A HREF='".XOOPS_URL."/modules/xfmod/news/admin/?group_id=".$group->getID()."'>"._XF_PRJ_NEWSADMIN."</A><BR>"
          ."<A HREF='".XOOPS_URL."/modules/xfmod/pm/admin/?group_id=".$group->getID()."'>"._XF_PRJ_TASKADMIN."</A><BR>"
          ."<A HREF='".XOOPS_URL."/modules/xfmod/forum/admin/?group_id=".$group->getID()."'>"._XF_PRJ_FORUMADMIN."</A><BR>";

$xoopsTpl->assign("tool_content",$content);

$xoopsTpl->assign("file_title",_XF_PRJ_FILERELEASES);

$content = "&nbsp;<BR>"
          ."<CENTER>"
					."[ <A href='editpackages.php?group_id=".$group_id."'><B>"._XF_PRJ_RELEASEEDITFILERELEASES."</B></A> ]"
					."</CENTER>"
					."<HR>"
					."<B>"._XF_PRJ_PACKAGES.":</B> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
					."<A href='".XOOPS_URL."/modules/xfmod/help/projects.php#downloading_projects'><i>"._XF_PRJ_WHATISTHIS."</i></A>"
					."<P>";

$res_module = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_package")." WHERE group_id='$group_id'");
while ($row_module = $xoopsDB->fetchArray($res_module)) {
	$content .= $row_module['name']."<BR>";
}

$xoopsTpl->assign("file_content",$content);

include ("../../../../footer.php");
?>