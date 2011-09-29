<?php
/**
  *
  * SourceForge News Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.8 2004/01/30 18:05:01 jcox Exp $
  *
  */
include_once ("../../../mainfile.php");

$langfile="news.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
$xoopsOption['template_main'] = 'news/xfmod_index.html';

if (isset($_POST['group_id']))
	$group_id = $_POST['group_id'];
elseif (isset($_GET['group_id']))
	$group_id = $_GET['group_id'];
else
	$group_id = null;

if (isset($_POST['limit']))
	$limit = $_POST['limit'];
elseif (isset($_GET['limit']))
	$limit = $_GET['limit'];
else
	$limit = null;
	
if (isset($_POST['offset']))
	$offset = $_POST['offset'];
elseif (isset($_GET['offset']))
	$offset = $_GET['offset'];
else
	$offset = null;
	
	
if (!$group_id)
{
  $group_id = $xoopsForge['sysnews'];
}

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

include (XOOPS_ROOT_PATH."/header.php");

//meta tag information
$metaTitle=" "._XF_G_NEWS." - ".$group->getPublicName();
$metaKeywords=project_getmetakeywords($group_id);
$metaDescription=str_replace('"', "&quot;", strip_tags($group->getDescription()));

$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
$xoopsTpl->assign("xoops_meta_description", $metaDescription);

//project nav information
$xoopsTpl->assign("news_header",news_header($group,$perm));
$xoopsTpl->assign("news_label",'<P>'._XF_NWS_CHOOSEITEMTOBROWSE.'<P>');

/*
	Put the result set (list of forums for this group) into a column with folders
*/
if ($group_id && ($group_id != $xoopsForge['sysnews'])) {
	$sql = "SELECT * "
	      ."FROM ".$xoopsDB->prefix("xf_news_bytes")." "
				."WHERE group_id='$group_id' "
				."AND is_approved<>'4' "
				."ORDER BY date DESC";
} else {
	$sql = "SELECT * "
	      ."FROM ".$xoopsDB->prefix("xf_news_bytes")." "
				."WHERE is_approved='1' "
				."ORDER BY date DESC";
}

if (!$limit || $limit > 50)
  $limit=50;
	
$result = $xoopsDB->query($sql, $limit+1, $offset);
$rows = $xoopsDB->getRowsNum($result);
$more=0;
if ($rows > $limit) {
	$rows = $limit;
  $more = 1;
}
$content = "";
if ($rows < 1) {
	if ($group_id) {
	  $content .= '<H4>'.sprintf(_XF_NWS_NONEWSFOUNDFOR,$group->getPublicName()).'</H4>';
	} else {
	  $content .= '<H4>'._XF_NWS_NONEWSFOUND.'</H4>';
	}
	$content .= '<P>'._XF_NWS_NOITEMSFOUND;
	$content .= $xoopsDB->error();
} else {
	$content .= '<TABLE WIDTH="100%" BORDER="0"><TR><TD VALIGN="TOP">'; 

	for ($j = 0; $j < $rows; $j++) { 
		$content .= '
		<A HREF="'.XOOPS_URL.'/modules/xfmod/forum/forum.php?forum_id='.unofficial_getDBResult($result, $j, 'forum_id').'">'.
		'<img src="'.XOOPS_URL.'/modules/xfmod/images/ic/cfolder15.png" width="15" height="13" BORDER="0" alt="news thread"> &nbsp;'.
		$ts->makeTboxData4Show(unofficial_getDBResult($result, $j, 'summary')).'</A> <BR>';
	}

  if ($more) {
    $content .= '<BR/>[ <a href="?group_id='.$group_id.'&limit='.$limit.'&offset='.(string)($offset+$limit) .'">'._XF_NWS_OLDERHEADLINES.'</a> ]';
  }

  $content .= '</TD></TR></TABLE>';
}
$xoopsTpl->assign("news_content",$content);
include (XOOPS_ROOT_PATH."/footer.php");

?>