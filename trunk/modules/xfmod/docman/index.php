<?php
/**
  *
  * SourceForge Documentaion Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.9 2004/03/16 01:51:21 jcox Exp $
  *
  */


/*
        by Quentin Cregan, SourceForge 06/2000
*/
include_once("../../../mainfile.php");

$langfile="docman.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/docman/doc_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/mime_lookup.php");
$xoopsOption['template_main'] = 'docman/xfmod_index.html';

if (isset($_POST['docid']))
	$docid = $_POST['docid'];
elseif (isset($_GET['docid']))
	$docid = $_GET['docid'];
else
	$docid = null;

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
		$private_project = true;
	}else{
		$private_project = false;
	}
		
	if($docid){
		include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/download.php");
	}
	include(XOOPS_ROOT_PATH."/header.php");
	$xoopsTpl->assign("docman_header",docman_header($project,$group_id,_XF_DOC_PROJECTDOCUMENTATION));

	//get a list of group numbers that this project owns
	$query = "SELECT * "
	        ."FROM ".$xoopsDB->prefix("xf_doc_groups")." "
					."WHERE group_id='$group_id' "
					."ORDER BY groupname";

	$result = $xoopsDB->query($query);
	$content = "";
	//otherwise, throw up an error
	if ($xoopsDB->getRowsNum($result) < 1) {
		$content .= "<b>"._XF_DOC_NOCATEGORIZEDDATA."</b><p>";
	} else {
		// get the groupings and display them with their members.
		while ($row = $xoopsDB->fetchArray($result)) {
			$query = "SELECT description, docid, title, data, doc_group, createdate, created_by, stateid "
			        ."FROM ".$xoopsDB->prefix("xf_doc_data")
							." WHERE doc_group='".$row['doc_group']."'"
							." AND (stateid='1'";
				//state 1 == 'active'
				
				if ($xoopsUser && $perm->isMember('user_id', $xoopsUser->getVar('uid'))) {
					$query .= " OR stateid='5' ";
				} //state 5 == 'private'
				$query .= ")";
				
			$subresult = $xoopsDB->query($query);

			$content .= "<p/><b>".$ts->makeTboxData4Show($row['groupname'])."</b>\n<ul>\n";
			if ($xoopsDB->getRowsNum($subresult) > 0) {
				while ($subrow = $xoopsDB->fetchArray($subresult)) {
					$private_doc=($subrow['stateid']==1)?false:true;
					$url = "index.php?group_id=".$group_id."&docid=".$subrow['docid'];
					if($private_project || $private_doc) $url .= "&private=1";
					$tempUser =& $member_handler->getUser($subrow['created_by']);
					$name = $tempUser->getVar('name', 'E') != '' ? $tempUser->getVar('name', 'E') : $tempUser->getVar('uname', 'E');
					$content .= "<li>"
					  ."<b><a href='$url'>".$ts->makeTboxData4Show($subrow['title'])."</a></b>"
					  ."<BR />&nbsp;&nbsp;<i>"._XF_G_DESCRIPTION.":</i> ".$ts->makeTboxData4Show($subrow['description'])
					  ."<BR />&nbsp;&nbsp;<i>"._XF_DOC_CREATEDBY.":</i> ".$name;
					  if (!strstr($subrow['data'], 'http://')) {
						  $content .= "<BR />&nbsp;&nbsp;<i>"._XF_DOC_TIMESTAMP.":</i> ".date($sys_datefmt,$subrow['createdate']);
					  }  
				}
				$tempUser = null;
			}else{
				$content .= "<li>"._XF_DOC_NODOCS;
			}
			$content .= "</ul>\n\n";
		}
	}
  $xoopsTpl->assign("content",$content);	
  include (XOOPS_ROOT_PATH."/footer.php");
} else {
  redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />No Group");
  exit;
}

?>