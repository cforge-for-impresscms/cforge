<?php
	/**
	*
	* SourceForge News Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.8 2004/01/30 18:05:01 jcox Exp $
	*
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "news.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
	$icmsOption['template_main'] = 'news/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	 
	if (isset($_POST['group_id']))
	$group_id = $_POST['group_id'];
	elseif(isset($_GET['group_id']))
	$group_id = $_GET['group_id'];
	else
		$group_id = null;
	 
	if (isset($_POST['limit']))
	$limit = $_POST['limit'];
	elseif(isset($_GET['limit']))
	$limit = $_GET['limit'];
	else
		$limit = null;
	 
	if (isset($_POST['offset']))
	$offset = $_POST['offset'];
	elseif(isset($_GET['offset']))
	$offset = $_GET['offset'];
	else
		$offset = null;
	 
	 
	if (!$group_id)
	{
		$group_id = $icmsForge['sysnews'];
	}
	 
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	//group is private
	if (!$group->isPublic())
	{
		//if it's a private group, you must be a member of that group
		if (!$group->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
		{
			redirect_header(ICMS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
			exit;
		}
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	 
	//meta tag information
	$metaTitle = " "._XF_G_NEWS." - ".$group->getPublicName();
	$metaKeywords = project_getmetakeywords($group_id);
	$metaDescription = str_replace('"', "&quot;", strip_tags($group->getDescription()));
	 
	$icmsTpl->assign("icms_pagetitle", $metaTitle);
	$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
	$icmsTpl->assign("icms_meta_description", $metaDescription);
	 
	//project nav information
	$icmsTpl->assign("news_header", news_header($group, $perm));
	$icmsTpl->assign("news_label", '<p>'._XF_NWS_CHOOSEITEMTOBROWSE.'<p>');
	 
	/*
	Put the result set(list of forums for this group) into a column with folders
	*/
	if ($group_id && ($group_id != $icmsForge['sysnews']))
	{
		$sql = "SELECT * " ."FROM ".$icmsDB->prefix("xf_news_bytes")." " ."WHERE group_id='$group_id' " ."AND is_approved<>'4' " ."ORDER BY date DESC";
	}
	else
	{
		$sql = "SELECT * " ."FROM ".$icmsDB->prefix("xf_news_bytes")." " ."WHERE is_approved='1' " ."ORDER BY date DESC";
	}
	 
	if (!$limit || $limit > 50)
	$limit = 50;
	 
	$result = $icmsDB->query($sql, $limit+1, $offset);
	$rows = $icmsDB->getRowsNum($result);
	$more = 0;
	if ($rows > $limit)
	{
		$rows = $limit;
		$more = 1;
	}
	$content = "";
	if ($rows < 1)
	{
		if ($group_id)
		{
			$content .= '<H4>'.sprintf(_XF_NWS_NONEWSFOUNDFOR, $group->getPublicName()).'</H4>';
		}
		else
		{
			$content .= '<H4>'._XF_NWS_NONEWSFOUND.'</H4>';
		}
		$content .= '<p>'._XF_NWS_NOITEMSFOUND;
		$content .= $icmsDB->error();
	}
	else
	{
		$content .= '<table width="100%" border="0"><th><td valign="top">';
		 
		for($j = 0; $j < $rows; $j++)
		{
			$content .= '
				<a href="'.ICMS_URL.'/modules/xfmod/forum/forum.php?forum_id='.unofficial_getDBResult($result, $j, 'forum_id').'">'. '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/cfolder15.png" width="15" height="13" border="0" alt="news thread"> &nbsp;'. $ts->makeTboxData4Show(unofficial_getDBResult($result, $j, 'summary')).'</a> <BR>';
		}
		 
		if ($more)
		{
			$content .= '<BR/>[ <a href="?group_id='.$group_id.'&limit='.$limit.'&offset='.(string)($offset+$limit) .'">'._XF_NWS_OLDERHEADLINES.'</a> ]';
		}
		 
		$content .= '</td></th></table>';
	}
	$icmsTpl->assign("news_content", $content);
	include(ICMS_ROOT_PATH."/footer.php");
	 
?>