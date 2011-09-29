<?php
/**
  *
  * SourceForge Forums Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.14 2004/02/02 18:36:42 devsupaul Exp $
  *
  */

include_once("../../../mainfile.php");

$langfile="forum.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/newsportal/config.inc");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/newsportal/newsportal.php");
$xoopsOption['template_main'] = 'newsportal/xfmod_index.html';


if ($group_id) {
	if($xoopsForge['forum_type']!='newsportal')
		redirect_header(XOOPS_URL."/modules/xfmod/forum/?group_id=$group_id",4,"");
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
	
	include_once ("../../../header.php");

	//meta tag information
	$metaTitle=" "._XF_FRM_FORUMS." - ".$group->getPublicName();
	$metaKeywords=project_getmetakeywords($group_id);
	$metaDescription=str_replace('"', "&quot;", strip_tags($group->getDescription()));
	
	$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
	$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
	$xoopsTpl->assign("xoops_meta_description", $metaDescription);

	//project nav information
	$xoopsTpl->assign("project_title",project_title($group));
	$xoopsTpl->assign("project_tabs",project_tabs ('forums', $group_id));
	
	$xoopsTpl->assign("group_id",$group_id);
	$xoopsTpl->assign("xoops_url",XOOPS_URL);
	$xoopsTpl->assign("nntp_server",$xoopsForge['nntp_server']);
	
	$xoopsTpl->assign("isForumAdmin",$perm->isForumAdmin());
	$xoopsTpl->assign("admin_label",_XF_G_ADMIN);

	$extsql = "SELECT forum_name,forum_desc_name FROM "
				.$xoopsDB->prefix("xf_forum_nntp_list")
				." WHERE group_id='$group_id'";
	$extres = $xoopsDB->query($extsql);
	$extrows = $xoopsDB->getRowsNum($extres);

	if (!$extres || $extrows < 1){
		$xoopsTpl->assign("noforums",true);
		$xoopsTpl->assign("noforums_message",sprintf(_XF_FRM_NOFORUMSFOUNDFORGROUP, $group->getPublicName()));
		$xoopsTpl->assign("dberror",$xoopsDB->error());
	}else{
		$ns=OpenNNTPconnection($server,$port);
		$xoopsTpl->assign("choose_forum",_XF_FRM_CHOOSEFORUMTOBROWSE);
		while ( $extrow = $xoopsDB->fetchArray($extres) ){
			$extrow['num_posts']=getNumArticles($ns,$extrow['forum_name']);
			$extforums[]=$extrow;
		}
		$xoopsTpl->assign("extforums",$extforums);
	}
	include_once("../../../footer.php");
} else {
	redirect_header($GLOBALS["HTTP_REFERER"],4,"No Group<br />You must specify a group id");
	exit;
}

?>