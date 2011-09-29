<?php
	/**
	*
	* SourceForge Community FAQs Manager
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.11 2004/01/30 18:09:27 jcox Exp $
	*
	*/
	 
	 
	/*
	by Quentin Cregan, SourceForge 06/2000
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "faqs.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	$icmsOption['template_main'] = 'faqs/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if ($group_id)
	{
		$project = group_get_object($group_id);
		$perm = $project->getPermission($icmsUser);
		//group is private
		if (!$project->isPublic())
		{
			//if it's a private group, you must be a member of that group
			if (!$project->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
			{
				redirect_header(ICMS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
				exit;
			}
		}
		 
		include("../../../header.php");
		 
		if ($project->isFoundry())
		{
			define("_LOCAL_XF_G_PROJECT", _XF_G_COMM);
		}
		else
		{
			define("_LOCAL_XF_G_PROJECT", _XF_G_PROJECT);
		}
		 
		//meta tag information
		$metaTitle = " FAQs - ".$project->getPublicName();
		$metaKeywords = project_getmetakeywords($group_id);
		$metaDescription = str_replace('"', "&quot;", strip_tags($project->getDescription()));
		 
		$icmsTpl->assign("icms_pagetitle", $metaTitle);
		$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
		$icmsTpl->assign("icms_meta_description", $metaDescription);
		 
		//project nav information
		$icmsTpl->assign("project_title", project_title($project));
		$icmsTpl->assign("project_tabs", project_tabs('faqs', $group_id));
		 
		$content = '';
		if ($perm->isAdmin())
		{
			$content .= '<strong>';
			$content .= "<P/><a href='admin/index.php?group_id=".$group_id."'>"._XF_G_ADMIN."</a> | <a href='admin/index.php?group_id=".$group_id."&add_faq=1'>"._XF_FAQ_ADDAFAQ."</a><BR>";
			$content .= '</strong><P/>';
		}
		 
		$sql = "SELECT ff.category_id, xf.category_title FROM " . $icmsDB->prefix("xf_foundry_faqs") . " ff, " . $icmsDB->prefix("xoopsfaq_categories") . " xf " . "WHERE ff.foundry_id='".$group_id."' " . "AND xf.category_id=ff.category_id";
		$result = $icmsDB->query($sql);
		 
		if ($icmsDB->getRowsNum($result) == 0)
		{
			$content .= "No FAQs have been added to this Project";
		}
		else
		{
			while ($row = $icmsDB->fetchArray($result))
			{
				$content .= "<A href='".ICMS_URL."/modules/xoopsfaq/?cat_id=".$row['category_id']."'>";
				$content .= "<img src='".ICMS_URL."/modules/xoopsfaq/images/folder.gif' width='14' height='14' border='0' alt='FAQ'>";
				$content .= "&nbsp;".$row['category_title']."</a><br>\n";
			}
		}
		 
		$icmsTpl->assign("content", $content);
		 
		include("../../../footer.php");
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "Error<br />No Group");
		exit;
	}
	 
?>