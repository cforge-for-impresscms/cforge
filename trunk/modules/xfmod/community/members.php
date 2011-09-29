<?php
/**
  *
  * SourceForge Mailing List Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: members.php,v 1.4 2003/12/15 18:09:15 devsupaul Exp $
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
$xoopsOption['template_main'] = 'community/xfmod_members.html';

if ($group_id) {
  $project =& group_get_object($group_id);
  $perm =& $project->getPermission ($xoopsUser);
	//group is private
	if (!$project->isPublic()) {
	  //if it's a private group, you must be a member of that group
	  if (!$project->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
		{
		  redirect_header(XOOPS_URL."/",4,_XF_COMM_COMMMARKEDASPRIVATE);
		  exit;
		}
	}
  
  if ( $project->isFoundry() )
  {
  	define( "_LOCAL_XF_ML_MLNOTENABLED",_XF_ML_MLNOTENABLECOMM);
  	define( "_LOCAL_XF_G_PROJECT",_XF_G_COMM);
  	define( "_LOCAL_XF_ML_FULLNAME",_XF_ML_FULLNAMECOMM);
  }
  else
  {
  	define( "_LOCAL_XF_ML_MLNOTENABLED",_XF_ML_MLNOTENABLED);
  	define( "_LOCAL_XF_G_PROJECT",_XF_G_PROJECT);
  	define( "_LOCAL_XF_ML_FULLNAME",_XF_ML_FULLNAME);
  }

  //meta tag information
  $metaTitle=": "._XF_ML_LISTS." - ".$project->getPublicName()."";
  $metaDescription=strip_tags($project->getDescription());
  $metaKeywords=project_getmetakeywords($group_id); 


  $content = "";
  if ($membership=="add")
  {
	$project->addFoundryMembership($xoopsUser->UID());
	$content .= "<b>You have been added as a member of this community.</b><BR><BR>";
	  
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

	$maillists = maillist_get_suffixes($project->isProject());

	$content .= "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
	$content .= "<tr class='bg3'><td colspan='6' align=\"left\">Quick subscribe to mailing lists</td></tr>\n";
 	$content .= "<form action=\"".XOOPS_URL."/modules/xfmod/maillist/multisubscribe.php\" method=\"POST\">\n";

	$maillistcount = 0;
	foreach ( $maillists as $suffix => $desc )
        {	
	$maillistcount ++;
	$content .= "<input type=\"hidden\" name=\"group_id".$maillistcount."\" value=\"".$group_id."\">\n"
	. "<input type=\"hidden\" name=\"email".$maillistcount."\" size=\"30\" value=\"".$xoopsUser->getVar("email")."\"></td></tr>\n"
	. "<input type=\"hidden\" name=\"maillistname".$maillistcount."\" value=\"".urlencode($project->getUnixName() . "-" . $suffix)."\">"
	. "<tr><td class'bg31'><input type=\"checkbox\" name=\"subscribeme".$maillistcount."\" value=\"y\"><b>".$project->getUnixName() . "-" . $suffix."</b> - ".$desc."</td>";

	}
	$content .= "<input type=\"hidden\" name=\"group_id\" value=\"".$group_id."\">\n";
	$content .= "<input type=\"hidden\" name=\"maillistcount\" value=\"".$maillistcount."\">\n";
	$content .= "<tr class='bg3'><td colspan='6' align=\"left\"><input type=\"submit\" name=\"submit\" value=\""._XF_ML_SUBSCRIBE."\"></td></tr>\n";
	$content .= "</table></form>\n";
  }
  	
  else if ($membership=="remove")
  {
	$project->removeFoundryMembership($xoopsUser->UID());
	$content .= "<b>Your membership to this community has been removed.</b><BR><BR><BR><BR>";	  
  }
  
  include( "../../../header.php" );

  $xoopsTpl->assign("project_title",project_title($project));
  $xoopsTpl->assign("project_tabs",project_tabs( 'members', $group_id ));
  $xoopsTpl->assign("content",$content);

    // List members.
  	$content = "<b>Current Members</b><BR><hr>";	 
	$users = $xoopsDB->prefix("users");
	$foundry_members = $xoopsDB->prefix("xf_user_foundry_groups");
	
	$sql = "SELECT u.uname, u.uid FROM ".$users." u, ".$foundry_members." m "
		."WHERE u.uid=m.user_id AND m.group_id='".$group_id."' "
		."ORDER BY u.uname";


	$result = $xoopsDB->query($sql);

	if(!$result)
	{
		echo "Error: ".$xoopsDB->error()."<br/>";
		exit;
	}

	$rows = $xoopsDB->getRowsNum($result);

	if(!$result || $rows < 1){
		$content .= "No members<br>";
	}else{
		for($i=0;$i<$rows;$i++){
			$curr_group = $xoopsDB->fetchArray($result);
			$content .= "<a href='".XOOPS_URL."/userinfo.php?uid=".$curr_group['uid']."'>";
			$content .= $curr_group['uname'];
			$content .= "</a><br>";
		}
	}
	 
	$content .= "<br>";
	$xoopsTpl->assign("member_list",$content);
	
  include ( "../../../footer.php" );
} else {
  redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />No Group");
  exit;
}

?>