<?php
/**
  *
  * Project Admin page to edit Trove categorization of the project
  *
  * This page is linked from index.php
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: group_trove.php,v 1.8 2003/12/09 15:04:00 devsupaul Exp $
  *
  */

include_once("../../../../mainfile.php");

$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/trove.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$xoopsOption['template_main'] = 'project/admin/xfmod_group_trove.html';

$group_id = http_get('group_id');
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isAdmin()){
	redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
	exit();
}

// Check for submission. If so, make changes and redirect

if ($submit && $root1) {
	group_add_history ('Changed Trove',$rm_id,$group_id);

	// there is at least a $root1[xxx]
	while (list($rootnode,$value) = each($root1)) {
		// check for array, then clear each root node for group
		$xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_trove_group_link")." "
		                ."WHERE group_id='$group_id' "
				."AND trove_cat_root='$rootnode'");

		for ($i=1; $i <= $TROVE_MAXPERROOT; $i++) {
			$varname = 'root'.$i;
			// check to see if exists first, then insert into DB
			if (${$varname}[$rootnode]) {
				trove_setnode($group_id,${$varname}[$rootnode],$rootnode);
			}
		}
	}
	redirect_header(XOOPS_URL."/modules/xfmod/project/admin/?group_id=".$group_id,0,_XF_PRJ_GROUPTROVEALTERED);
	exit;
}

include ("../../../../header.php");

$xoopsTpl->assign("project_title", project_title($group));
$xoopsTpl->assign("project_tabs", project_tabs('admin', $group_id));
$xoopsTpl->assign("project_admin_header", project_admin_header($group_id, $perm));
$xoopsTpl->assign("feedback", $feedback);

if ( $group->isFoundry() )
{
  $content = "<H4>"._XF_COMM_EDITTROVECATEGORIZATION."</H4>";
  $content .= "<p>"._XF_COMM_EDITTROVEEXPLAIN1."</p>"
    ."<p>"._XF_COMM_EDITTROVEEXPLAIN2."</p>"
    ."<FORM action='".$_SERVER['PHP_SELF']."' method='POST'>";
}
else
{
  $content = "<H4>"._XF_PRJ_EDITTROVECATEGORIZATION."</H4>";
  $content .= "<p>"._XF_PRJ_EDITTROVEEXPLAIN1."</p>"
    ."<p>"._XF_PRJ_EDITTROVEEXPLAIN2."</p>"
    ."<FORM action='".$_SERVER['PHP_SELF']."' method='POST'>";
}

$CATROOTS = trove_getallroots($group->isFoundry());
while (list($catroot,$fullname) = each($CATROOTS)) {
	$content .= "<HR><P><B>".$fullname."</B>";

	$res_grpcat = $xoopsDB->queryF("SELECT trove_cat_id "
	                              ."FROM ".$xoopsDB->prefix("xf_trove_group_link")." "
					."WHERE group_id='$group_id' "
					."AND trove_cat_root='$catroot'");

	for ($i=1; $i <= $GLOBALS['TROVE_MAXPERROOT']; $i++) {
		// each drop down, consisting of all cats in each root
		$name= "root$i"."[$catroot]";
		// see if we have one for selection
		if ($row_grpcat = $xoopsDB->fetchArray($res_grpcat)) {
			$selected = $row_grpcat["trove_cat_id"];
		} else {
			$selected = 0;
		}
		$content .= trove_catselectfull($catroot,$selected,$name);
	}
}

/*
//Add projects to a community if you wish
if(!$group->isFoundry()){


	echo "<br><hr><br><b>Community</b><br>";

	$res_grpcat = $xoopsDB->queryF("SELECT trove_cat_id "
	                              ."FROM ".$xoopsDB->prefix("xf_trove_group_link")." "
					."WHERE group_id='$group_id' "
					."AND trove_cat_root=".$TROVE_COMMUNITY);

					//create select boxes
	for ($i=1; $i <= $GLOBALS['TROVE_MAXPERROOT']; $i++) {
		// each drop down, consisting of all cats in each root
		$name= "root$i"."[".$TROVE_COMMUNITY."]";
		//get selected communities
		if ($row_grpcat = $xoopsDB->fetchArray($res_grpcat)) {
			$selected = $row_grpcat["trove_cat_id"];
		} else {
			$selected = 0;
		}
		trove_communityselectfull($selected,$name);
	}

}
*/

$content .= "<INPUT type='hidden' name='group_id' value='".$group_id."'>";

if ( $group->isFoundry() )
{
  $content .= "<P><INPUT type='submit' name='submit' value='". _XF_COMM_UPDATEALLCATEGORYCHANGES."'>";
}
else
{
  $content .= "<P><INPUT type='submit' name='submit' value='". _XF_PRJ_UPDATEALLCATEGORYCHANGES."'>";
}

  $content .= "</FORM>";

$xoopsTpl->assign("content", $content);
include ("../../../../footer.php");
?>