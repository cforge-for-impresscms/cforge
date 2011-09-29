<?php
	/**
	*
	* Show Release Notes/ChangeLog Page
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: shownotes.php,v 1.12 2004/04/21 23:02:50 jcox Exp $
	*
	*/
	 
	include_once("../../../mainfile.php");
	 
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	$icmsOption['template_main'] = 'project/xfmod_shownotes.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	include("../../../header.php");
	 
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
	$metaTitle = ": "._XF_PRJ_RELEASENOTESANDCHANGELOG." - ".$project->getPublicName();
	$metaDescription = strip_tags($project->getDescription());
	$metaKeywords = project_getmetakeywords($group_id);
	 
	$icmsTpl->assign("project_title", project_title($project));
	$icmsTpl->assign("project_tabs", project_tabs('downloads', $group_id));
	 
	$result = $icmsDB->query("SELECT r.notes,r.changes,r.dependencies,r.preformatted,r.name,p.group_id " ."FROM ".$icmsDB->prefix("xf_frs_release")." r,".$icmsDB->prefix("xf_frs_package")." p " ."WHERE r.package_id=p.package_id " ."AND r.release_id='$release_id'");
	 
	if (!$result || $icmsDB->getRowsNum($result) < 1)
	{
		$content = $icmsDB->error();
		$content .= "Error - That Release Was Not Found";
	}
	else
	{
		 
		$content = "<h2 style='text-align:left;'>"._XF_PRJ_RELEASENAME.": <a href='showfiles.php?group_id=".$group_id."'>".$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'name'))."</a></H3><p>";
		$icmsTpl->assign("notes_info", $content);
		/*
		Show preformatted or plain notes/changes
		*/
		if (unofficial_getDBResult($result, 0, 'preformatted'))
		{
			$notes = trim($ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'notes')));
			if ($notes != '')
			{
				$icmsTpl->assign("notes_title", _XF_PRJ_NOTES);
				$icmsTpl->assign("notes_content", "<pre>".$notes."</pre>");
			}
			$changelog = trim($ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'changes')));
			if ($changelog != '')
			{
				$icmsTpl->assign("change_title", _XF_PRJ_CHANGELOG);
				$icmsTpl->assign("change_content", "<pre>".$changelog."</pre>");
			}
			$dependencies = trim($ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'dependencies')));
			if ($dependencies != '')
			{
				$icmsTpl->assign("depend_title", _XF_PRJ_DEPENDENCIES);
				$icmsTpl->assign("depend_content", "<pre>".$dependencies."</pre>");
			}
		}
		else
		{
			$notes = trim($ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'notes')));
			if ($notes != '')
			{
				$icmsTpl->assign("notes_title", _XF_PRJ_NOTES);
				$icmsTpl->assign("notes_content", $notes);
			}
			$changelog = trim($ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'changes')));
			if ($changelog != '')
			{
				$icmsTpl->assign("change_title", _XF_PRJ_CHANGELOG);
				$icmsTpl->assign("change_content", $changelog);
			}
			$dependencies = trim($ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'dependencies')));
			if ($dependencies != '')
			{
				$icmsTpl->assign("depend_title", _XF_PRJ_DEPENDENCIES);
				$icmsTpl->assign("depend_content", $dependencies);
			}
		}
	}
	 
	include("../../../footer.php");
?>