<?php
/**
  *
  * SourceForge Generic Tracker facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: download.php,v 1.5 2004/07/20 17:24:21 jcox Exp $
  *
  */
include_once ("../../../mainfile.php");

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/Artifact.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactFile.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactType.class");

if (!$group_id) {
  redirect_header($_SERVER['HTTP_REFERER'],2,"ERROR<br />No Group");
  exit;
}
// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$group || !is_object($group) || $group->isError()) {
  redirect_header(XOOPS_URL."/",2,"ERROR<br />No Group");
  exit;
}

//group is private
if (!$group->isPublic()) {
  //if it's a private group, you must be a member of that group
  if (!$group->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
	{
	  redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
	  exit;
	}
}

//
//  Create the ArtifactType object
//
$ath = new ArtifactType($group,$atid);
if (!$ath || !is_object($ath)) {
	$feedback .= 'Error<br />ArtifactType could not be created';
        exit;
}
if ($ath->isError()) {
	$feedback .= 'Error'.$ath->getErrorMessage();
	exit;
}

$ah = new Artifact($ath,$aid);
if (!$ah || !is_object($ah)) {
	$feedback .= 'Error<br />Artifact could not be created';
  exit;
}
else if ($ah->isError()) {
	$feedback .= 'Error'.$ah->getErrorMessage();
	exit;
}
else {
	$afh = new ArtifactFile($ah,$file_id);
	if (!$afh || !is_object($afh)) {
	  $feedback .= 'Error<br />ArtifactFile could not be created';
	  exit;
	}
	else if ($afh->isError()) {
	  $feedback .= 'Error'.$afh->getErrorMessage();
	  exit;
	}
	else {
		Header ("Content-length: $afh->getSize()");
		Header ("Content-Disposition: attachment; filename=".$afh->getName());
		Header ("Content-Type: ".$afh->getType());
		echo $afh->getData();
	}
}
?>