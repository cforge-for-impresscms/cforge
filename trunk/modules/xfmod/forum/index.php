<?php
/**
  *
  * SourceForge Forums Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.8 2003/12/15 18:09:17 devsupaul Exp $
  *
  */

include_once("../../../mainfile.php");

$langfile="forum.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");

if($xoopsForge['forum_type']!='forum')
	redirect_header(XOOPS_URL,4,_NOPERM);

$xoopsOption['template_main'] = 'forum/xfmod_index.html';

if ($group_id) {
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
	
	//meta tag information
	$metaTitle=": "._XF_FRM_FORUMS." - ".$group->getPublicName();
	$metaDescription=strip_tags($group->getDescription());
	$metaKeywords=project_getmetakeywords($group_id);  	
	
	include ("../../../header.php");
	$xoopsTpl->assign("project_title",project_title($group));
	$xoopsTpl->assign("project_tabs",project_tabs ('forums', $group_id));
	
	$xoopsTpl->assign("isForumAdmin",$perm->isForumAdmin());
	$xoopsTpl->assign("group_id",$group_id);
	$xoopsTpl->assign("admin_label",_XF_G_ADMIN);

	if ($xoopsUser && $group->isMemberOfGroup($xoopsUser)) {
		$public_flag='<3';
	} else {
		$public_flag='=1';
	}

	$sql = "SELECT g.group_forum_id,g.forum_name,g.description, famc.count as total "
	      	."FROM ".$xoopsDB->prefix("xf_forum_group_list")." g "
			."LEFT JOIN ".$xoopsDB->prefix("xf_forum_agg_msg_count")." famc USING (group_forum_id) "
			."WHERE g.group_id='$group_id' AND g.is_public $public_flag";
	$result = $xoopsDB->query ($sql);
	$rows = $xoopsDB->getRowsNum($result); 

    /* extended extended forums */
	$extsql = "SELECT forum_name,forum_url FROM "
				.$xoopsDB->prefix("xf_forum_ext_group_list")
				." WHERE group_id='$group_id'";
	$extres = $xoopsDB->query($extsql);
	$extrows = $xoopsDB->getRowsNum($extres);

	if ( (!$result || $rows < 1) && (!$extres || $extrows < 1) ) {
		$xoopsTpl->assign("noforums",true);
		$xoopsTpl->assign("noforums_message",sprintf(_XF_FRM_NOFORUMSFOUNDFORGROUP, $group->getPublicName()));
		$xoopsTpl->assign("dberror",$xoopsDB->error());

		include("../../../footer.php");
		exit;
	}else{
		$xoopsTpl->assign("choose_forum",_XF_FRM_CHOOSEFORUMTOBROWSE);
	
	   /*
		Put the result set (list of forums for this group) into a column with folders
	   */
		while($row=$xoopsDB->fetchArray($result)){
			$forums[]=$row;
		}
		$xoopsTpl->assign("forums",$forums);

        /* extended forums */
		/* not used 
		while ( $extrow = $xoopsDB->fetchArray($extres) ){
			$extforums[]=$extrow;
		}
		$xoopsTpl->assign("extforums",$extforums);
		*/
	}
  include("../../../footer.php");
} else {
	redirect_header($GLOBALS["HTTP_REFERER"],4,"No Group<br />You must specify a group id");
	exit;
}

?>