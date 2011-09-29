<?php
	/**
	*
	* Show Release Historical ChangeLog Page
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: showchangelog.php,v 1.1 2004/01/27 22:22:47 caitchison Exp $
	*
	*/
	 
	include_once("../../../mainfile.php");
	 
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	$icmsOption['template_main'] = 'project/xfmod_showchangelog.html';
	 
	include("../../../header.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
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
	 
	//meta tag information
	$metaTitle = ": "._XF_PRJ_CHANGELOG." - ".$project->getPublicName();
	$metaDescription = strip_tags($project->getDescription());
	$metaKeywords = project_getmetakeywords($group_id);
	 
	$icmsTpl->assign("project_title", project_title($project));
	$icmsTpl->assign("project_tabs", project_tabs('downloads', $group_id));
	 
	$query = "SELECT r.changes, r.preformatted, p.name " ." FROM ".$icmsDB->prefix("xf_frs_release") ." r LEFT JOIN " . $icmsDB->prefix("xf_frs_package") ." p ON r.package_id=p.package_id " ." WHERE  p.group_id='$group_id' " ." ORDER BY p.name";
	 
	$result = $icmsDB->query($query);
	 
	if (!$result || $icmsDB->getRowsNum($result) < 1)
	{
		$content = $icmsDB->error();
		$content .= "Error - That Release Was Not Found";
		$icmsTpl->assign("change_content", $content);
	}
	else
	{
		 
		$completeChangeLog = "";
		$packageNameCurrent = "";
		$packageNameNew = "";
		 
		 
		for($i = 0; $i < $icmsDB->getRowsNum($result); $i++)
		{
			$packageNameNew = unofficial_getDBResult($result, $i, 'name');
			if (strcmp($packageNameNew, $packageNameCurrent))
			{
				$packageNameCurrent = $packageNameNew;
				$completeChangeLog .= "<h3 style='text-align:left;'>" . $packageNameCurrent . "</h3>";
			}
			 
			$completeChangeLog .= unofficial_getDBResult($result, $i, 'changes');
		}
		 
		/*
		Show preformatted or plain notes/changes
		*/
		if (unofficial_getDBResult($result, 0, 'preformatted'))
		{
			$icmsTpl->assign("change_title", _XF_PRJ_CHANGELOG);
			$icmsTpl->assign("change_content", "<pre>".$ts->makeTareaData4Show($completeChangeLog)."</pre>");
		}
		else
		{
			$icmsTpl->assign("changelog_title", _XF_PRJ_CHANGELOG);
			$icmsTpl->assign("changelog_content", $ts->makeTareaData4Show($completeChangeLog));
		}
	}
	 
	include("../../../footer.php");
?>