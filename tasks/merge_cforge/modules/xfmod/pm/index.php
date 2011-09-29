<?php
	/**
	*
	* SourceForge Project/Task Manager(PM)
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.6 2004/02/05 23:26:55 jcox Exp $
	*
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "pm.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/pm/pm_utils.php");
	$icmsOption['template_main'] = 'pm/xfmod_index.html';
	 
	//$group_id = util_http_track_vars('group_id');
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if ($group_id)
	{
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
		$metaTitle = " "._XF_PM_TASKS." - ".$group->getPublicName();
		$metaKeywords = project_getmetakeywords($group_id);
		$metaDescription = str_replace('"', "&quot;", strip_tags($group->getDescription()));
		 
		$icmsTpl->assign("icms_pagetitle", $metaTitle);
		$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
		$icmsTpl->assign("icms_meta_description", $metaDescription);
		 
		// project nav information
		$icmsTpl->assign("project_title", project_title($group));
		$icmsTpl->assign("project_tabs", project_tabs('pm', $group_id));
		 
		if (isset($_POST['group_project_id']))
		$group_project_id = $_POST['group_project_id'];
		elseif(isset($_GET['group_project_id']))
		$group_project_id = $_GET['group_project_id'];
		else
			$group_project_id = null;
		 
		$header = pm_header($group, $perm, sprintf(_XF_PM_PROJECTSFOR, $group->getPublicName()), $group_project_id);
		$icmsTpl->assign("pm_header", $header);
		$content = '';
		 
		if ($icmsUser && $group->isMemberOfGroup($icmsUser))
		{
			$public_flag = '0,1';
		}
		else
		{
			$public_flag = '1';
		}
		 
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_project_group_list")." WHERE group_id='$group_id' AND is_public IN($public_flag)";
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		if (!$result || $rows < 1)
		{
			$content .= "<p>" ."<strong>"._XF_PM_NOSUBPROJECTSFOUND." for ".$group->getPublicName()."</strong>";
			$icmsTpl->assign("content", $content);
			include(ICMS_ROOT_PATH."/footer.php");
			exit;
		}
		 
		$content .= '<p>'._XF_PM_CHOOSESUBPROJECT.'<p>';
		 
		/*
		Put the result set(list of forums for this group) into a column with folders
		*/
		 
		for($j = 0; $j < $rows; $j++)
		{
			$content .= '
				<a href="'.ICMS_URL.'/modules/xfmod/pm/task.php?group_project_id='.unofficial_getDBResult($result, $j, 'group_project_id')
			.'&group_id='.$group_id.'&func=browse">' .'<img src="'.ICMS_URL.'/modules/xfmod/images/ic/index.png" width="24" height="24" border="0" alt="index"> &nbsp;' .$ts->makeTboxData4Show(unofficial_getDBResult($result, $j, 'project_name')).'</a><BR>' .$ts->makeTareaData4Show(unofficial_getDBResult($result, $j, 'description')).'<p>';
		}
		$icmsTpl->assign("content", $content);
		include(ICMS_ROOT_PATH."/footer.php");
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "Error<br />NO group");
		exit;
	}
?>