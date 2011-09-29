<?php
/**
  *
  * Package Monitor Page
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: filemodule_monitor.php,v 1.4 2003/12/15 18:09:21 devsupaul Exp $
  *
  */

include_once("../../../mainfile.php");
$xoopsOption['template_main'] = 'project/xfmod_filemonitor.html';

$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");

if ($xoopsUser && $filemodule_id) {
	/*
		User obviously has to be logged in to monitor
		a file module
	*/
	$sql = "SELECT group_id FROM ".$xoopsDB->prefix("xf_frs_package")." WHERE package_id=$filemodule_id";
	$result = $xoopsDB->query($sql);
	list($group_id) = $xoopsDB->fetchRow($result);
	$project =& group_get_object($group_id);
	$perm  =& $project->getPermission( $xoopsUser );
	//group is private
	if (!$project->isPublic()) {
		//if it's a private group, you must be a member of that group
		if (!$project->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
		{
			redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
			exit;
		}
	}

	include ("../../../header.php");
	
	$content .= "<H4 style='text-align:left;'>"._XF_PRJ_MONITORAPACKAGE."</H4>";

	/*
		First check to see if they are already monitoring
		this thread. If they are, say so and quit.
		If they are NOT, then insert a row into the db
	*/
	$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_filemodule_monitor")." WHERE user_id='".$xoopsUser->getVar("uid")."' AND filemodule_id='$filemodule_id'";
	$result = $xoopsDB->query($sql);
	if (!$result || $xoopsDB->getRowsNum($result) < 1) {
		/*
			User is not already monitoring this filemodule, so 
			insert a row so monitoring can begin
		*/
		$sql = "INSERT INTO ".$xoopsDB->prefix("xf_filemodule_monitor")." (filemodule_id,user_id) VALUES ('$filemodule_id','".$xoopsUser->getVar("uid")."')";
	
		$result = $xoopsDB->queryF($sql);
	
		if (!$result) {
			$content .= "<FONT COLOR='RED'>Error inserting into filemodule_monitor</FONT>";
			$content .= $xoopsDB->error();
		} else {
			$content .= "<FONT COLOR='RED'><H4 style='text-align:left;'>"._XF_PRJ_PACKAGEISMONITORED."</H4></FONT>"
		."<P>"._XF_PRJ_PACKAGEMONITOREDINFO1
		."<P>"._XF_PRJ_PACKAGEMONITOREDINFO2;
		}
	
	} else {
	
		$sql = "DELETE FROM ".$xoopsDB->prefix("xf_filemodule_monitor")." WHERE user_id='".$xoopsUser->getVar("uid")."' AND filemodule_id='$filemodule_id'";
		$result = $xoopsDB->queryF($sql);
		$content .= "<FONT COLOR='RED'><H4 style='text-align:left;'>"._XF_PRJ_MONITORINGTURNEDOFF."</H4></FONT>"
	  ."<P>"._XF_PRJ_MONITORINGTURNEDOFFINFO;
	
	}
	$xoopsTpl->assign("content",$content);
	include ("../../../footer.php");
} else {
	redirect_header($GLOBALS["HTTP_REFERER"],2,_NOPERM);
	exit;
}
?>