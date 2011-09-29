<?php
/**
  * Author: Paul Jones, Novell, Inc. 01/2004
  * Copyright 2003 (c) Novell, Inc.
  * http://forge.novell.com
  *
  * @version   $Id: index.php,v 1.12 2004/07/22 19:51:39 danreese Exp $
  *
  */

include_once ("../../../../mainfile.php");

if (!$group_id) {
	redirect_header($GLOBALS["HTTP_REFERER"], 4, "Error<br />No Group");
	exit;
}
if (!$xoopsUser) {
	redirect_header($GLOBALS["HTTP_REFERER"], 4, "Error<br />You must be logged in to access this page.");
	exit;
}

$langfile = "maillist.php";
require_once (XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once (XOOPS_ROOT_PATH."/modules/xfmod/maillist/maillist_utils.php");
require_once (XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");


$project = & group_get_object($group_id);
$perm = & $project -> getPermission($xoopsUser);

if (!$perm->isAdmin()){
	redirect_header($GLOBALS["HTTP_REFERER"], 4, "You do not have permissions to access that page");
	exit;
}
//group is private
if (!$project -> isPublic()) {
	//if it's a private group, you must be a member of that group
	if (!$project -> isMemberOfGroup($xoopsUser) && !$perm -> isSuperUser()) {
		redirect_header(XOOPS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
		exit;
	}
}

if (isset($submit)) {

	switch ($submit) {
		case 'Add List' :
			$listname = trim($listname);
			$feedback = maillist_validate_listname($listname);
			if (!$feedback)
				$feedback = maillist_add($project, $listname, $listdesc);
			break;
		case 'Remove List' :
			$feedback = maillist_delete($project, $id);
			break;
	}

}
include (XOOPS_ROOT_PATH."/header.php");

echo project_title($project);
echo project_tabs('maillist', $group_id);
echo "<p><b><a href='".XOOPS_URL."/modules/xfmod/maillist/admin/index.php?group_id=$group_id'>"._XF_G_ADMIN."</a></b></p>";

if (isset($feedback) && $feedback)
	echo "<div class='errorMsg'>$feedback</div>";

echo "<p>Add a new mailing list"
			."<ul><li>Your mailing list name will be a combination of your project short name and a suffix separated by a dash."
			."   <b>Input only the suffix</b> when you create your list.</li>"
			."<li>You may have a maximum of ".$xoopsForge['max_maillists']." mailing lists for your project."
			."  If this is not enough, please contact the site administrator.</li></ul><br>";
if (maillist_count($group_id) < $xoopsForge['max_maillists'] || $perm -> isSuperUser()) {
	echo "<form method='post' action='$_SERVER['PHP_SELF']'>"
				."<input type='text' name='listname'> List Suffix<br>"
				."<input type='text' name='listdesc'> List Description<br>"
				."<input type='hidden' name='group_id' value='$group_id'>"
				."<input type='submit' name='submit' value='Add List'>"
				."</form></p>";
}
$sql = "SELECT id, name, description FROM ".$xoopsDB -> prefix("xf_maillists")." WHERE group_id=$group_id";
$result = $xoopsDB -> query($sql);

if($result && $xoopsDB->getRowsNum($result) > 0){
	echo "<p> Remove mailing list<br>"
				."<ul><li>Warning - This will remove your mailing list and it's archive.</li></ul><br>"
				."<form method='post' action='$_SERVER['PHP_SELF']'>"
				."<select name='id'>";
	while (list ($id, $suffix, $desc) = $xoopsDB -> fetchRow($result)) {
		echo "<option value='$id'>".$project -> getUnixName()."-".$suffix."</a>";
	}
	echo "</select>
			<input type='hidden' name='group_id' value='$group_id'>
			<input type='submit' name='submit' value='Remove List'>
			</form></p>";

	$sql = "SELECT name, description FROM ".$xoopsDB -> prefix("xf_maillists")." WHERE group_id=$group_id";
	$result = $xoopsDB -> query($sql);
	echo "<p>Click on a list below to administer the list using mailman's administrative interface.<ul>";
	while (list ($suffix, $desc) = $xoopsDB -> fetchRow($result)) {
		echo "<li><a target=\"mm_adm_wnd\" href=\"http://".$_SERVER['SERVER_NAME']."/mailman/admin/".$project -> getUnixName()."-".$suffix."\">".$project -> getUnixName()."-".$suffix."</a>";
	}
	echo "</ul><br></p>";
}
include (XOOPS_ROOT_PATH."/footer.php");

?>