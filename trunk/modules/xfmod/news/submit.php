<?php
/**
  *
  * SourceForge News Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: submit.php,v 1.5 2004/01/30 18:05:01 jcox Exp $
  *
  */
include_once ("../../../mainfile.php");

$langfile="news.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
$xoopsOption['template_main'] = 'news/xfmod_submit.html';

if (isset($_POST['post_changes']))
	$post_changes = $_POST['post_changes'];
elseif (isset($_GET['post_changes']))
	$post_changes = $_GET['post_changes'];
else
	$post_changes = null;

//news must now be submitted from a project page -

if (!$group_id) {
  redirect_header(XOOPS_URL."/", 2, "ERROR<br />No Group ID");
  exit;
}

if ($xoopsUser) {

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

  if (!$perm->isAdmin() && !$perm->isSuperUser())
  {
  	if ( $group->isFoundry() )
  	{
	  redirect_header(XOOPS_URL."/",2,_XF_G_PERMISSIONDENIED."<br />"._XF_NWS_YOUCANNOTSUBMITNEWSCOMM);
	}
	else
	{
	  redirect_header(XOOPS_URL."/",2,_XF_G_PERMISSIONDENIED."<br />"._XF_NWS_YOUCANNOTSUBMITNEWS);
	}
    exit;
  }

	if ($post_changes) {
		//check to make sure both fields are there
		if ($summary && $details) {
			/*
				Insert the row into the db if it's a generic message
				OR this person is an admin for the group involved
			*/

       			/*
       				create a new discussion forum without a default msg
       				if one isn't already there
       			*/

       			$new_id = forum_create_forum($xoopsForge['sysnews'], $summary, 1, 0,'',0);
       			$sql = "INSERT INTO ".$xoopsDB->prefix("xf_news_bytes")." "
						      ."(group_id,submitted_by,is_approved,date,forum_id,summary,details) "
									."VALUES ("
									."'$group_id',"
									."'".$xoopsUser->getVar("uid")."',"
									."'0',"
									."'".time()."',"
									."'$new_id',"
									."'".$ts->makeTboxData4Save($summary)."',"
									."'".$ts->makeTareaData4Save($details)."')";
       			$result = $xoopsDB->queryF($sql);
       			if (!$result) {
       				$feedback .= ' ERROR performing insert ';
       			} else {
       				$feedback .= ' '._XF_NWS_NEWSADDED.' ';
       			}
		} else {
			$feedback .= ' '._XF_NWS_BOTHSUBJECTANDBODY.' ';
		}
	}

	/*
		Show the submit form
	*/
	include (XOOPS_ROOT_PATH."/header.php");

	//meta tag information
	$metaTitle=" Submit "._XF_G_NEWS." - ".$group->getPublicName();
	$metaKeywords=project_getmetakeywords($group_id);
	$metaDescription=str_replace('"', "&quot;", strip_tags($group->getDescription()));

	$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
	$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
	$xoopsTpl->assign("xoops_meta_description", $metaDescription);

	//project nav information
	$xoopsTpl->assign("news_header",news_header ($group,$perm));

	if ( $group->isFoundry() ){
		define( "_LOCAL_XF_NWS_FORPROJECT",_XF_NWS_FORCOMM );
	}else{
		define( "_LOCAL_XF_NWS_FORPROJECT",_XF_NWS_FORPROJECT );
	}

	$xoopsTpl->assign("form_action",$_SERVER['PHP_SELF']);
	$xoopsTpl->assign("group_name",$group->getPublicName());
	$xoopsTpl->assign("group_id",$group_id);
	$xoopsTpl->assign("title",_LOCAL_XF_NWS_FORPROJECT);
	$xoopsTpl->assign("subject",_XF_G_SUBJECT);
	$xoopsTpl->assign("details",_XF_NWS_DETAILS);
	$xoopsTpl->assign("submit",_XF_G_SUBMIT);



  include (XOOPS_ROOT_PATH."/footer.php");

} else {
  redirect_header(XOOPS_URL."/", 2,_NOPERM);
  exit;
}

?>