<?php
/**
  *
  * Group Admin page to confirm removal of user from group
  *
  * This page is called from Project/Foundry Admins when admin requests
  * removal of a developer. This page checks whether it is possible
  * to remove one, if no, shows decription why not, else presents
  * admin with the confirmation form. Results of this form are submitted
  * back to calling Project/Foundry Admin page (i.e. very removal is
  * performed there). Since Project/Foundry Admins use slightly different
  * parameter passing interface, there's a bit of dirty magic here.
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: rmuser.php,v 1.7 2004/02/09 20:10:33 jcox Exp $
  *
  */

include_once ("../../../../mainfile.php");

$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/class/xoopsuser.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$xoopsOption['template_main'] = 'project/admin/xfmod_rmuser.html';  

$group_id = http_get('group_id');
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

// Do some text substitutions below
if ($group->getType() == 2) {
	$type = "community";
	// foundries hate explicit group_id
	$passed_group_id = "";
} else {
	$type = "project";
	$passed_group_id = "<input type='hidden' name='group_id' value='".$group_id."'>";
}

// Need to check if user being removed is admin
$rm_user = new XoopsUser($rm_id);
$perm = $group->getPermission($rm_user);

if ($perm->isProjectAdmin()) {
	redirect_header($return_to, 2, sprintf(_XF_PRJ_CANNOTREMOVEXXXADMIN, $type));
	exit;
}

include ("../../../../header.php");

$xoopsTpl->assign("project_title", project_title($group));
$xoopsTpl->assign("project_tabs", project_tabs('admin', $group_id));
$xoopsTpl->assign("admin_header", project_admin_header($group_id, $perm));
$xoopsTpl->assign("feedback", $feedback);

$content = "<B style='font-size:16px;align:left;'>"._XF_PRJ_MEMBERADMINISTRATION."</B><br /><p>";
$content .= _XF_PRJ_CONFIRMREMOVE." ".$type.". "._XF_PRJ_PLEASECONFIRM;
$content .= "</p>

<table>
<tr><td>

<form action='". $return_to."' method='POST'>
<input type='hidden' name='func' value='rmuser'>
".$passed_group_id."
<input type='hidden' name='rm_id' value='".$rm_id."'>
<input type='submit' value='"._XF_G_REMOVE."'>
</form>

</td><td>

<form action='".$return_to."' method='GET'>
".$passed_group_id."
<input type='submit' value='"._XF_G_CANCEL."'>
</form>

</td></tr>
</table>";

$xoopsTpl->assign("content", $content);
include ("../../../../footer.php");
?>