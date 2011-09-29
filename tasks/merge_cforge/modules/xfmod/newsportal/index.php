<?php
	/**
	*
	* SourceForge Forums Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.14 2004/02/02 18:36:42 devsupaul Exp $
	*
	*/
	 
	include_once("../../../mainfile.php");
	 
	$langfile = "forum.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/newsportal/config.inc");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/newsportal/newsportal.php");
	$icmsOption['template_main'] = 'newsportal/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if ($group_id)
	{
		if ($icmsForge['forum_type'] != 'newsportal')
		redirect_header(ICMS_URL."/modules/xfmod/forum/?group_id=$group_id", 4, "");
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
		 
		include_once("../../../header.php");
		 
		//meta tag information
		$metaTitle = " "._XF_FRM_FORUMS." - ".$group->getPublicName();
		$metaKeywords = project_getmetakeywords($group_id);
		$metaDescription = str_replace('"', "&quot;", strip_tags($group->getDescription()));
		 
		$icmsTpl->assign("icms_pagetitle", $metaTitle);
		$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
		$icmsTpl->assign("icms_meta_description", $metaDescription);
		 
		//project nav information
		$icmsTpl->assign("project_title", project_title($group));
		$icmsTpl->assign("project_tabs", project_tabs('forums', $group_id));
		 
		$icmsTpl->assign("group_id", $group_id);
		$icmsTpl->assign("xoops_url", ICMS_URL);
		$icmsTpl->assign("nntp_server", $icmsForge['nntp_server']);
		 
		$icmsTpl->assign("isForumAdmin", $perm->isForumAdmin());
		$icmsTpl->assign("admin_label", _XF_G_ADMIN);
		 
		$extsql = "SELECT forum_name,forum_desc_name FROM " .$icmsDB->prefix("xf_forum_nntp_list")
		." WHERE group_id='$group_id'";
		$extres = $icmsDB->query($extsql);
		$extrows = $icmsDB->getRowsNum($extres);
		 
		if (!$extres || $extrows < 1)
		{
			$icmsTpl->assign("noforums", true);
			$icmsTpl->assign("noforums_message", sprintf(_XF_FRM_NOFORUMSFOUNDFORGROUP, $group->getPublicName()));
			$icmsTpl->assign("dberror", $icmsDB->error());
		}
		else
		{
			$ns = OpenNNTPconnection($server, $port);
			$icmsTpl->assign("choose_forum", _XF_FRM_CHOOSEFORUMTOBROWSE);
			while ($extrow = $icmsDB->fetchArray($extres))
			{
				$extrow['num_posts'] = getNumArticles($ns, $extrow['forum_name']);
				$extforums[] = $extrow;
			}
			$icmsTpl->assign("extforums", $extforums);
		}
		include_once("../../../footer.php");
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "No Group<br />You must specify a group id");
		exit;
	}
	 
?>