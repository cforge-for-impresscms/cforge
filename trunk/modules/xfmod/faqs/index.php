<?php
/**
  *
  * SourceForge Community FAQs Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.11 2004/01/30 18:09:27 jcox Exp $
  *
  */


/*
        by Quentin Cregan, SourceForge 06/2000
*/
include_once("../../../mainfile.php");

$langfile="faqs.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$xoopsOption['template_main'] = 'faqs/xfmod_index.html';

if ($group_id) {
	$project =& group_get_object($group_id);
	$perm =& $project->getPermission ($xoopsUser);
	//group is private
	if (!$project->isPublic()) {
	  //if it's a private group, you must be a member of that group
	  if (!$project->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
		{
		  redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
		  exit;
		}
	}

	include( "../../../header.php" );

	if ( $project->isFoundry() ){
		define("_LOCAL_XF_G_PROJECT",_XF_G_COMM);
	}
	else{
		define("_LOCAL_XF_G_PROJECT",_XF_G_PROJECT);
	}

	//meta tag information
	$metaTitle=" FAQs - ".$project->getPublicName();
	$metaKeywords=project_getmetakeywords($group_id);
	$metaDescription=str_replace('"', "&quot;", strip_tags($project->getDescription()));

	$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
	$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
	$xoopsTpl->assign("xoops_meta_description", $metaDescription);

	//project nav information
	$xoopsTpl->assign("project_title", project_title($project));
	$xoopsTpl->assign("project_tabs", project_tabs('faqs', $group_id));
	
	$content = '';
	if($perm->isAdmin()){
		$content .= '<B>';
		$content .= "<P/><a href='admin/index.php?group_id=".$group_id."'>"._XF_G_ADMIN."</a> | <A HREF='admin/index.php?group_id=".$group_id."&add_faq=1'>"._XF_FAQ_ADDAFAQ."</A><BR>";
		$content .= '</B><P/>';
	}

	$sql = "SELECT ff.category_id, xf.category_title FROM "
	. $xoopsDB->prefix("xf_foundry_faqs") . " ff, "
	. $xoopsDB->prefix("xoopsfaq_categories") . " xf "
	. "WHERE ff.foundry_id='".$group_id."' "
	. "AND xf.category_id=ff.category_id";
	$result = $xoopsDB->query($sql);
	
	if($xoopsDB->getRowsNum($result) == 0) {
		$content .= "No FAQs have been added to this Project";
	}
	else {
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$content .= "<A href='".XOOPS_URL."/modules/xoopsfaq/?cat_id=".$row['category_id']."'>";
			$content .= "<img src='".XOOPS_URL."/modules/xoopsfaq/images/folder.gif' width='14' height='14' border='0' alt='FAQ'>";
			$content .= "&nbsp;".$row['category_title']."</a><br>\n";
		}
	}

	$xoopsTpl->assign("content", $content);

  include ( "../../../footer.php" );
} else {
  redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />No Group");
  exit;
}

?>