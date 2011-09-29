<?php
include_once("../../../mainfile.php");

$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vars.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/trove.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/maillist/maillist_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/mime_lookup.php");
$xoopsOption['template_main'] = 'community/xfmod_project_list.html';

function isspace( $c )
{
  if ( $c == ' ' || $c == '\n' || $c == '\r' || $c == '\t' )
    {
      return true;
    }
  return false;
}

/**
 *
 *
 *
 */
if (!$group_id || !is_numeric($group_id)) {
  $unixname = strtolower($QUERY_STRING);
  $res = $xoopsDB->query("SELECT group_id FROM ".$xoopsDB->prefix("xf_groups")." WHERE unix_group_name='".$unixname."'");
  if (!$res || $xoopsDB->getRowsNum($res) < 1) {
    
  } else {
    $group_arr = $xoopsDB->fetchArray($res);
    $group_id = $group_arr['group_id'];
  }
}
$community =& group_get_object($group_id);
/**
 *
 *
 *
 */
if (!$community) {
  redirect_header(XOOPS_URL,4,_XF_COMM_COMMDOESNOTEXIST);
  exit;
}
if ( $community->isInactive() && $activate=='y')
{
	$sql = "UPDATE " . $xoopsDB->prefix("xf_groups")
	. " SET status='A', is_public='1'"
	. " WHERE group_id='".$group_id."'";
	$result = $xoopsDB->queryF($sql);
	// Refresh the community object
	$community =& group_get_object($group_id);
}
if ( ! $community )
{
	redirect_header(XOOPS_URL,4,_XF_COMM_PROJECTDOESNOTEXIST);
	exit;
}
$perm  =& $community->getPermission( $xoopsUser );

//group is private
if (!$community->isPublic()) {
  //if it's a private group, you must be a member of that group
  if (!$community->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
    {
      redirect_header(XOOPS_URL."/",4,_XF_COMM_COMMMARKEDASPRIVATE);
      exit;
    }
}

//First, for inactive communities, you have to be a project admin or superuser
if ( $community->isInactive() && !$perm->isSuperUser() && !$perm->isAdmin())
{
	redirect_header(XOOPS_URL,4,_XF_COMM_NOTAUTHORIZEDTOENTER);
	exit;
}
//for dead communities must be member of xoopsforge project
if (!$community->isActive() && !$perm->isSuperUser()) {
  redirect_header(XOOPS_URL,4,_XF_COMM_NOTAUTHORIZEDTOENTER);
  exit;
}

if ($community->isProject())
{    
   redirect_header(XOOPS_URL."/modules/xfmod/project/?".$community->getUnixName(),4,"");
   exit;   
}
include ("../../../header.php");

$xoopsTpl->assign("project_title",project_title($community));
$xoopsTpl->assign("project_tabs",project_tabs ('home', $group_id));
if ( $community->isInactive() && $activate!='y' )
{
	$xoopsTpl->assign("inactive_info","\n<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\" width=\"100%\">\n"
	. "<tr><td align=\"center\"><b>"._XF_COMM_THISCOMMISINACTIVE."</b></td></tr>\n"
	. "<form action=\"".XOOPS_URL."/modules/xfmod/community/?".$community->getUnixName()."\" method=\"POST\">\n"
	. "<input type=\"hidden\" name=\"activate\" value=\"y\">\n"
	. "<tr><td align=\"center\"><input type=\"submit\" name=\"submit\" value=\""._XF_COMM_REACTIVATECOMM."\">\n"
	. "</td></tr></form></table><hr>\n");
}

$querytotalcount = $xoopsDB->getRowsNum($res_grp);
// ########################### List all projects in this communtiy
	$title = _XF_COMM_TOPTEN;
	$content = "<b>Projects of this community</b><br>";

	$grp = $xoopsDB->prefix("xf_groups");
	$metric = $xoopsDB->prefix("xf_project_weekly_metric");
	$trove = $xoopsDB->prefix("xf_trove_group_link");

	$sql = "SELECT unix_group_name, group_name, short_description FROM $trove "
		."LEFT JOIN $grp ON $trove.group_id=$grp.group_id "
		."LEFT JOIN $metric ON $trove.group_id=$metric.group_id "
		."WHERE trove_cat_id=$group_id "
		."AND $grp.is_public=1 "
		."AND $grp.status='A' "
		."ORDER BY group_name ASC";

	$limit = 1000;
	$result = $xoopsDB->query($sql,$limit);

	if(!$result)
	{
		echo "Error: ".$xoopsDB->error()."<br/>";
		exit;
	}

	$rows = $xoopsDB->getRowsNum($result);

	if(!$result || $rows < 1){
		$content .= "No current results<br>";
	}else{
		for($i=0;$i<$rows;$i++){
			$curr_group = $xoopsDB->fetchArray($result);
			$content .= "<BR><a href='".XOOPS_URL."/modules/xfmod/project/?".$curr_group['unix_group_name']."'>";
			$content .= "<b>".$curr_group['group_name']."</b>";
			$content .= "</a><br>";
			$content .= strip_tags(substr($curr_group['short_description'],0,255))."...<BR>";
		}
	}
	 
 	$xoopsTpl->assign("content",$content);

include_once(XOOPS_ROOT_PATH."/footer.php");
?>