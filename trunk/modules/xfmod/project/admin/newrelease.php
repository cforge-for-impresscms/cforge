<?php
/**
  *
  * Project Admin: Create a New Release
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: newrelease.php,v 1.6 2003/12/09 15:04:00 devsupaul Exp $
  *
  */
include_once ("../../../../mainfile.php");

$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/frs.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$xoopsOption['template_main'] = 'project/admin/xfmod_newrelease.html';

http_get
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isReleaseTechnician()) {
	redirect_header(XOOPS_URL."/",4,_XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTRELEASETECHNIC);
	exit;
}

// Create a new FRS object
$frs = new FRS($group_id);

if( $submit && $release_name && $package_id) {

	$release_id = $frs->frsAddRelease($release_name, $package_id);
	if( !$frs->isError() ) {
		redirect_header("editreleases.php?package_id=$package_id&release_id=$release_id&group_id=$group_id",2,_XF_PRJ_RELEASEADDED);
		exit;
	}else{
		$content=$frs->getErrorMessage();
	}
} else {

	include ("../../../../header.php");

	$xoopsTpl->assign("project_title", project_title($group));
	$xoopsTpl->assign("project_tabs", project_tabs('admin', $group_id));
	$xoopsTpl->assign("project_admin_header", project_admin_header($group_id, $perm));
	$xoopsTpl->assign("feedback", $feedback);

	$content = "<B style='font-size:16px;align:left;'>"._XF_PRJ_RELEASENEWFILEVERSION."</B><br />

	<p>
	<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<input type='hidden' name='group_id' value='".$group_id."'>
	<table border='0' cellpadding='2' cellspacing='2'>
	<tr>
		<td>"._XF_PRJ_NEWRELEASENAME.":</td>
		<td><input type='text' name='release_name' value='' size='20' maxlength='25'></td>
	</tr>
	<tr>
		<td>"._XF_PRJ_OFPACKAGE.":</b></td>
		<td>".frs_show_package_popup($group_id,'package_id',$package_id)."</td>
	</tr>
	<tr>
		<td colspan='2'><input type='submit' name='submit' value='"._XF_PRJ_CREATETHISRELEASE."'></td>
	</tr>
	</table>
	</form>";

}

$xoopsTpl->assign("content", $content);
include ("../../../../footer.php");
?>