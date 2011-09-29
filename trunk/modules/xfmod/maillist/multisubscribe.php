<?php
/**
  *
  * SourceForge Mailing List Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: multisubscribe.php,v 1.6 2004/01/07 22:20:13 devsupaul Exp $
  *
  */


/*
        by Quentin Cregan, SourceForge 06/2000
*/
include_once("../../../mainfile.php");

$langfile="maillist.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/maillist/maillist_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");

if ($group_id) {
  $project =& group_get_object($group_id);
  $perm =& $project->getPermission ($xoopsUser);
	//group is private
	if (!$project->isPublic()) {
		//if it's a private group, you must be a member of that group
		if (!$project->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser()) {
			redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
			exit;
		}
	}
  
  if ( $project->isFoundry() )
  {
  	define( "_LOCAL_XF_ML_MLNOTENABLED",_XF_ML_MLNOTENABLECOMM);
  	define( "_LOCAL_XF_G_PROJECT",_XF_G_COMM);
  	define( "_LOCAL_XF_ML_FULLNAME",_XF_ML_FULLNAMECOMM);
  	define( "_LOCAL_XF_ML_RETURNTOPRJPAGE",_XF_ML_RETURNTOCOMMPAGE);
	$type = "community";
  }
  else
  {
  	define( "_LOCAL_XF_ML_MLNOTENABLED",_XF_ML_MLNOTENABLED);
  	define( "_LOCAL_XF_G_PROJECT",_XF_G_PROJECT);
  	define( "_LOCAL_XF_ML_FULLNAME",_XF_ML_FULLNAME);
  	define( "_LOCAL_XF_ML_RETURNTOPRJPAGE",_XF_ML_RETURNTOPRJPAGE);
	$type = "project";
  }

  if ( !$project->usesMail() ) {
    redirect_header($GLOBALS["HTTP_REFERER"],4,_LOCAL_XF_ML_MLNOTENABLED);
    exit;
  }

  //meta tag information
  $metaTitle=": "._XF_ML_LISTS." - ".$project->getPublicName()."";
  $metaDescription=strip_tags($project->getDescription());
  $metaKeywords=project_getmetakeywords($group_id); 
  
  include( "../../../header.php" );

  echo project_title($project);
// echo "<b style='font-size:16px;align:left;'>" ._XF_ML_LISTS. "</b><br />\n";
  echo project_tabs( 'maillist', $group_id );

  echo "<p>\n";


if ( $maillistcount >= 1 )
{
	$lists = "";
	for ($x =1; $x <= $maillistcount; $x++)
	{
		$email = $_POST["email".$x];
		$subscribeme = $_POST["subscribeme".$x];
		$list = $_POST["maillistname".$x];
		$pw=rand();
		
		if ($subscribeme =="y")
		{
			$lists = $lists."<BR>".$list;
		
			if ( $email == "" )
			{
				$feedback = _XF_ML_EMAILREQD;
			}
			else if ( ! maillist_subscribe( 0, 0, $_SERVER['SERVER_NAME'], $list, 0, $email, $pw ) )
			{
				$feedback = _XF_ML_SUBFAILED;
			}
		}
	}
	echo "<h5>"._XF_ML_NOWSUBDTO . " " . $lists . "</h5>"
			. "<br><br>"
			. "<a href=\"".XOOPS_URL."/modules/xfmod/$type/?".$project->getUnixName()."\">"
			. _LOCAL_XF_ML_RETURNTOPRJPAGE . "</a><BR>";
	echo "<h5>".$feedback."</h5>";
}  
  
  include ( "../../../footer.php" );
} else {
  redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />No Group");
  exit;
}

?>