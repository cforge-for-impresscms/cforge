<?php
//there is a variable $title available here that is the pages title if we want it
  include("../../../mainfile.php");
	require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	$group_id=$_REQUEST["group_id"];
	if(!$group_id){
	  redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />No Group");
	  exit;
	}

	if($xoopsForge['forum_type']!='newsportal')
		redirect_header(XOOPS_URL."/modules/xfmod/forum/?group_id=$group_id",4,"");

	$groupob =& group_get_object($group_id);
	$perm  =& $groupob->getPermission( $xoopsUser );
	//group is private
	if (!$groupob->isPublic()) {
		//if it's a private group, you must be a member of that group
		if (!$groupob->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser()) {
			redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
			exit;
		}
	}
	//make sure they are looking at a valid forum for the project
	if(!$group) $group=$newsgroups;
	$sql = "SELECT count(group_id) FROM ".$xoopsDB->prefix("xf_forum_nntp_list")
				." WHERE group_id=$group_id"
				." AND forum_name='$group'";
	$result = $xoopsDB->query($sql);
	list($count) = $xoopsDB->fetchRow($result);
	if($count<1){
			redirect_header(XOOPS_URL."/",4,"Invalid Forum");
			exit;
	}
				
	//meta tag information
	$metaTitle=": "._XF_FRM_FORUMS." - ".$groupob->getPublicName();
	$metaDescription=strip_tags($groupob->getDescription());
	$metaKeywords=project_getmetakeywords($group_id);  	
  include("../../../header.php");
  
//  $xoopsTpl->assign("project_title",project_title($group));
//  $xoopsTpl->assign("project_tabs",project_tabs ('docman', $group_id));
  
  echo project_title($groupob);
  echo project_tabs ('forums', $group_id);

?>