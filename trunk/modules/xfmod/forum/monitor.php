<?php
/**
  *
  * SourceForge Forums Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: monitor.php,v 1.6 2003/12/15 18:09:17 devsupaul Exp $
  *
  */


include_once("../../../mainfile.php");

$langfile="forum.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");

if ($xoopsUser) {
	/*
		User obviously has to be logged in to monitor
		a thread
	*/

	if ($forum_id) {
		$result = $xoopsDB->queryF("SELECT group_id FROM "
									." ".$xoopsDB->prefix("xf_forum_group_list")
									." WHERE group_forum_id='$forum_id'");
	
		if (!$result || $xoopsDB->getRowsNum($result) < 1) {
		  redirect_header($GLOBALS["HTTP_REFERER"],4,"ERROR<br />"._XF_FRM_FORUMNOTFOUND." ".$xoopsDB->error());
		  exit;
		}
		$group_id = unofficial_getDBResult($result,0,'group_id');
		$group =& group_get_object($group_id);
		$perm  =& $group->getPermission( $xoopsUser );
		
		//group is private
		if (!$group->isPublic()) {
			//if it's a private group, you must be a member of that group
			if (!$group->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser()) {
				redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
				exit;
			}
		}
		/*
			Check to see if they are already monitoring
			this thread. If they are, say so and quit.
			If they are NOT, then insert a row into the db
		*/

		$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_forum_monitored_forums")." WHERE user_id='".$xoopsUser->getVar("uid")."' AND forum_id='$forum_id'";
		$result = $xoopsDB->query($sql);

		if (!$result || $xoopsDB->getRowsNum($result) < 1) {
			/*
				User is not already monitoring thread, so 
				insert a row so monitoring can begin
			*/
			$sql = "INSERT INTO ".$xoopsDB->prefix("xf_forum_monitored_forums")." (forum_id,user_id) VALUES ('$forum_id','".$xoopsUser->getVar("uid")."')";
			$result = $xoopsDB->queryF($sql);

			if (!$result) {
			  redirect_header($GLOBALS["HTTP_REFERER"],1,"ERROR<br />"._XF_FRM_COULDNOTINSERTMONITOR);
			  exit;
			}else{
			  redirect_header(XOOPS_URL."/modules/xfmod/forum/forum.php?forum_id=$forum_id",2,_XF_FRM_FORUMISMONITORED);
			  exit;
			}
		}else{
			$sql = "DELETE FROM ".$xoopsDB->prefix("xf_forum_monitored_forums")." WHERE user_id='".$xoopsUser->getVar("uid")."' AND forum_id='$forum_id'";
			$result = $xoopsDB->queryF($sql);
			redirect_header(XOOPS_URL."/modules/xfmod/forum/forum.php?forum_id=$forum_id",2,_XF_FRM_FORUMISNOTMONITORED);
			exit;
		}
	}else{
		redirect_header($GLOBALS["HTTP_REFERER"],2,"ERROR<br />Choose a forum First");
		exit;
	} 
}else{
  redirect_header($GLOBALS["HTTP_REFERER"],2,_XF_G_PERMISSIONDENIED."<br />"._XF_FRM_MUSTLOGGEDINTOMONITOR);
  exit;
}
?>