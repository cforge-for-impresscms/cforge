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
  * @version   $Id: proj_list.php,v 1.7 2004/01/09 19:05:53 devsupaul Exp $
  *
  */

include_once ("../../../../mainfile.php");
$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/trove.php");
$xoopsOption['template_main'] = 'community/admin/xfmod_proj_list.html';

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

// Only admin can make modifications via this page
if ($perm->isAdmin() && isset($func)) {
	//
	// updating the database
	//
	if ($func=='disassoc') {
		$results = $xoopsDB->queryF("DELETE FROM "
			.$xoopsDB->prefix("xf_trove_group_link")
			." WHERE trove_cat_id=$group_id"
			." AND group_id=$proj_id");

		if(!$results) {
			$xoopsForgeErrorHandler->addError("Failed to delete association to project $proj_name: "
				. $xoopsDB->error());
		} else {
			$xoopsForgeErrorHandler->addMessage("Association to project $proj_name removed");
			if ($group->removeFeaturedProject($proj_id))
			{
			    $xoopsForgeErrorHandler->addMessage("$proj_name has been removed from the featured list.");

			}
			else
			{
				$xoopsForgeErrorHandler->addError("Failed to add $proj_name to featured list.");
			}
		}
	}
	if ($func=='addfeature') {
		if ($group->addFeaturedProject($proj_id, $featuredescription))
		{
			$xoopsForgeErrorHandler->addMessage("$proj_name has been added to featured list.");
		}
		else
		{
			$xoopsForgeErrorHandler->addError("Failed to add $proj_name to featured list.");
		}
	}
	if ($func=='removefeature') {

		if ($group->removeFeaturedProject($proj_id))

		{

			$xoopsForgeErrorHandler->addMessage("$proj_name has been removed from the featured list.");

		}

		else

		{

			$xoopsForgeErrorHandler->addError("Failed to add $proj_name to featured list.");

		}

	}
}

include (XOOPS_ROOT_PATH."/header.php");

$sql = "SELECT p.group_id,p.group_name,"
	."p.short_description,p.unix_group_name FROM "
	.$xoopsDB->prefix("xf_groups")." AS p,"
	.$xoopsDB->prefix("xf_trove_group_link")." AS c"
	." WHERE c.trove_cat_id=$group_id"
	." AND p.group_id=c.group_id";

$results = $xoopsDB->query($sql);

if(!$results or $xoopsDB->getRowsNum($results) < 1) {
	$msg = "There are no projects associated with this community";
	redirect_header(XOOPS_URL."/modules/xfmod/community/admin/?group_id=$group_id&feedback=".urlencode($msg),3,$msg);
	exit;
}

$xoopsTpl->assign("project_title",project_title($group));
$xoopsTpl->assign("project_tabs",project_tabs ('admin', $group_id));
$xoopsTpl->assign("feedback",$xoopsForgeErrorHandler->getDisplayFeedback());
$xoopsTpl->assign("project_admin_header",get_project_admin_header($group_id, $perm,0));
$content = "<table border=0>";
$rowcount = $xoopsDB->getRowsNum($results);
for($i = 0; $i < $rowcount; $i++) {
	$row = $xoopsDB->fetchArray($results);
	$proj_id = $row['group_id'];

	$content .= "<tr class='".($i % 2 != 0 ? "bg1" : "bg3")."'><td>"
		."<a href='".XOOPS_URL."/modules/xfmod/project/?"
		.$row['unix_group_name']."'>"
		.$row['group_name']."</a></td>"
		."<td>". strip_tags(substr($row['short_description'],0,255))."...</td>"
		."<td align='center'>"
		."<form action='".XOOPS_URL."/modules/xfmod/community/admin/proj_list.php?"
		."group_id=$group_id' method='post' "
		.'onSubmit="return verify(\''.$row['unix_group_name'].'\')">'
		."<input type='hidden' name='func' value='disassoc'>"
		."<input type='hidden' name='proj_id' value='$proj_id'>"
		."<input type='hidden' name='proj_name' value='".$row['unix_group_name']."'>"
		."<BR><input type=submit value='Remove Association'></form>"
		."</td><td align='center'>";

		if ( !$group->isFeaturedProject($proj_id))

		{
			$content .= "This project is not featured.";
			$content .= "<form action='".XOOPS_URL."/modules/xfmod/community/admin/proj_list.php?"
			."group_id=$group_id' method='post'>"
			."<input type='hidden' name='func' value='addfeature'>"
			."<input type='hidden' name='proj_id' value='$proj_id'>"
			."<input type='hidden' name='proj_name' value='".$row['unix_group_name']."'>"
			."<textarea rows='6' cols='40' name='featuredescription' value=''>".strip_tags(substr($row['short_description'],0,252))."...</textarea><BR>"
			."<input type=submit value='Add to featured list'></form>";

		}
		else
		{
			$content .= "This is a featured project.<BR>Description:<BR><BR>".$group->getFeaturedProjectDescription($proj_id);
			$content .= "<form action='".XOOPS_URL."/modules/xfmod/community/admin/proj_list.php?"
			."group_id=$group_id' method='post'>"
			."<input type='hidden' name='func' value='removefeature'>"
			."<input type='hidden' name='proj_id' value='$proj_id'>"
			."<input type='hidden' name='proj_name' value='".$row['unix_group_name']."'>"
			."<input type=submit value='Remove from featured list'></form>";
		}

		$content .= "</td></tr>";
}
$content.="</table>";
$xoopsTpl->assign("content",$content);

include (XOOPS_ROOT_PATH."/footer.php");

?>