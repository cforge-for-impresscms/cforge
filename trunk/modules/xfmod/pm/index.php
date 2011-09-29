<?php
/**
  *
  * SourceForge Project/Task Manager (PM)
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.6 2004/02/05 23:26:55 jcox Exp $
  *
  */
include_once ("../../../mainfile.php");

$langfile="pm.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/pm/pm_utils.php");
$xoopsOption['template_main'] = 'pm/xfmod_index.html';

$group_id = util_http_track_vars('group_id');

if ($group_id) {
  $group =& group_get_object($group_id);
  $perm  =& $group->getPermission( $xoopsUser );
	//group is private
	if (!$group->isPublic()) {
	  //if it's a private group, you must be a member of that group
	  if (!$group->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
		{
		  redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
		  exit;
		}
	}

  include (XOOPS_ROOT_PATH."/header.php");
  
  //meta tag information
  $metaTitle=" "._XF_PM_TASKS." - ".$group->getPublicName();
  $metaKeywords=project_getmetakeywords($group_id);
  $metaDescription=str_replace('"', "&quot;", strip_tags($group->getDescription()));

  $xoopsTpl->assign("xoops_pagetitle", $metaTitle);
  $xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
  $xoopsTpl->assign("xoops_meta_description", $metaDescription);

  // project nav information  
  $xoopsTpl->assign("project_title", project_title($group));
  $xoopsTpl->assign("project_tabs", project_tabs ('pm', $group_id));
  
	  if (isset($_POST['group_project_id']))
		$group_project_id = $_POST['group_project_id'];
	elseif (isset($_GET['group_project_id']))
		$group_project_id = $_GET['group_project_id'];
	else
		$group_project_id = null;

  $header = pm_header($group, $perm, sprintf (_XF_PM_PROJECTSFOR, $group->getPublicName()), $group_project_id);
  $xoopsTpl->assign("pm_header",$header);
  $content = '';
  
  if ( $xoopsUser && $group->isMemberOfGroup( $xoopsUser )) {
    $public_flag='0,1';
  } else {
    $public_flag='1';
  }

  $sql = "SELECT * FROM ".$xoopsDB->prefix("xf_project_group_list")." WHERE group_id='$group_id' AND is_public IN ($public_flag)";
  $result = $xoopsDB->query ($sql);
  $rows = $xoopsDB->getRowsNum($result);

  if (!$result || $rows < 1) {
    $content .= "<P>"
        ."<B>"._XF_PM_NOSUBPROJECTSFOUND." for ".$group->getPublicName()."</B>";
    $xoopsTpl->assign("content",$content);
    include (XOOPS_ROOT_PATH."/footer.php");
    exit;
  }

  $content .= '<P>'._XF_PM_CHOOSESUBPROJECT.'<P>';

	/*
		Put the result set (list of forums for this group) into a column with folders
	*/

  for ($j = 0; $j < $rows; $j++) {
    $content .= '
      <A HREF="'.XOOPS_URL.'/modules/xfmod/pm/task.php?group_project_id='.unofficial_getDBResult($result, $j, 'group_project_id')
     .'&group_id='.$group_id.'&func=browse">'
     .'<img src="'.XOOPS_URL.'/modules/xfmod/images/ic/index.png" width="24" height="24" border="0" alt="index"> &nbsp;'
     .$ts->makeTboxData4Show(unofficial_getDBResult($result, $j, 'project_name')).'</A><BR>'
     .$ts->makeTareaData4Show(unofficial_getDBResult($result, $j, 'description')).'<P>';
  }
  $xoopsTpl->assign("content",$content);
  include (XOOPS_ROOT_PATH."/footer.php");
} else {
  redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />NO group");
  exit;
}
?>