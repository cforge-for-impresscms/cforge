<?php
/**
  *
  * SourceForge Mailing List Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: subscribe.php,v 1.9 2004/02/05 22:59:43 devsupaul Exp $
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
  }
  else
  {
  	define( "_LOCAL_XF_ML_MLNOTENABLED",_XF_ML_MLNOTENABLED);
  	define( "_LOCAL_XF_G_PROJECT",_XF_G_PROJECT);
  	define( "_LOCAL_XF_ML_FULLNAME",_XF_ML_FULLNAME);
  	define( "_LOCAL_XF_ML_RETURNTOPRJPAGE",_XF_ML_RETURNTOPRJPAGE);
  }

  if ( !$project->usesMail() ) {
    redirect_header($GLOBALS["HTTP_REFERER"],4,_LOCAL_XF_ML_MLNOTENABLED);
    exit;
  }

  //meta tag information
  $metaTitle=": "._XF_ML_LISTS." - ".$project->getPublicName()."";
  $metaDescription=strip_tags($project->getDescription());
  $metaKeywords=project_getmetakeywords($group_id); 
  
  include( XOOPS_ROOT_PATH."/header.php" );

  echo project_title($project);
// echo "<b style='font-size:16px;align:left;'>" ._XF_ML_LISTS. "</b><br />\n";
  echo project_tabs( 'maillist', $group_id );

  echo "<p>\n";

$feedback = "";
if ( $dosub == "y" )
{
	if ( $email == "" )
	{
		$feedback = _XF_ML_EMAILREQD;
	}
	else if ( $pw == "" )
	{
		$feedback = _XF_ML_PWDREQD;
	}
	else if ( $pw != $pw_conf )
	{
		$feedback = _XF_ML_PWDSNOMATCH;
	}
	else if ( ! maillist_subscribe( 0, 0, $_SERVER['SERVER_NAME'], $list, 0, $email, $pw, $digest ) )
	{
		$feedback = _XF_ML_SUBFAILED;
	}
	else
	{
		echo "<h5>"._XF_ML_NOWSUBDTO . " " . $list . "</h5>"
		. "<br><br>"
		. "<a href=\"".XOOPS_URL."/modules/xfmod/project/?".$project->getUnixName()."\">"
		. _LOCAL_XF_ML_RETURNTOPRJPAGE . "</a>";
	}
}
if ( $dosub != "y" || $feedback != "" )
{
	if ( $feedback != "" )
	{
		echo "<h5>".$feedback."</h5>";
	}
	echo "<h3>"._XF_ML_SUBSCRIBINGTO." ".$list."</h3>\n\n";

	echo "<form action='".XOOPS_URL."/modules/xfmod/maillist/subscribe.php' method='POST'>\n"
	. "<table border='0' cellpadding='4' cellspacing='0' width='100%'>\n"
	. "<input type='hidden' name='dosub' value='y'>\n"
	. "<input type='hidden' name='group_id' value='".$group_id."'>\n"
	. "<input type='hidden' name='list' value='".urlencode($list)."'>\n"
	. "<tr class='bg3'><td colspan='2' align='left'>". sprintf(_XF_ML_SUB_INSTRUCTIONS, $list) ."</td></tr>\n"
	. "<tr class='bg2'><td>"._XF_ML_ENTER_EMAIL.":</td>\n"
	. "<td width='60%' align='left'><input type='text' name='email' size='30'></td></tr>\n"
	. "<tr class='bg3'><td colspan='2' align'left'>"._XF_ML_PWD_INSTRUCTIONS."<p/> <b>** Please note</b>, in order to see the mailing list show up on your account page, please make sure that you are <a href='".XOOPS_URL."/user.php?xoops_redirect=".$_SERVER['PHP_SELF']."?".urlencode($_SERVER['QUERY_STRING'])."'>logged in</a> when you subscribe.</td></tr>\n"
	. "<tr class='bg2'><td>"._XF_ML_ENTER_PWD.":</td>\n"
	. "<td width='60%' align='left'><input type='password' name='pw' size='15'></td></tr>\n"
	. "<tr class='bg2'><td>"._XF_ML_VERIFY_PWD.":</td>\n"
	. "<td width='60%' align='left'><input type='password' name='pw_conf' size='15'></td></tr>\n"
	. "<tr class='bg2'><td>"._XF_ML_DIGESTMAIL."?</td>\n"
	. "<td width='60%' align='left'><input type='radio' name='digest' value='0' checked> "._XF_ML_NO
	. "<input type='radio' name='digest' value='1'> "._XF_ML_YES."</td></tr>\n"
	. "<tr class='bg2'><td colspan='2' align='left'><input type='submit' name='submit' value='"._XF_ML_SUBSCRIBE."'></td></tr>\n"
	. "</table></form>\n";
}

	include ( XOOPS_ROOT_PATH."/footer.php" );
} else {
  redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />No Group");
  exit;
}

?>