<?php
	include_once("../../../mainfile.php");
	 
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vars.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/trove.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/maillist/maillist_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/mime_lookup.php");
	$icmsOption['template_main'] = 'community/xfmod_project_list.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	function isspace($c)
	{
		if ($c == ' ' || $c == '\n' || $c == '\r' || $c == '\t')
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
	if (!$group_id || !is_numeric($group_id))
	{
		$unixname = strtolower($QUERY_STRING);
		$res = $icmsDB->query("SELECT group_id FROM ".$icmsDB->prefix("xf_groups")." WHERE unix_group_name='".$unixname."'");
		if (!$res || $icmsDB->getRowsNum($res) < 1)
		{
			 
		}
		else
		{
			$group_arr = $icmsDB->fetchArray($res);
			$group_id = $group_arr['group_id'];
		}
	}
	$community = group_get_object($group_id);
	/**
	*
	*
	*
	*/
	if (!$community)
	{
		redirect_header(ICMS_URL, 4, _XF_COMM_COMMDOESNOTEXIST);
		exit;
	}
	if ($community->isInactive() && $activate == 'y')
	{
		$sql = "UPDATE " . $icmsDB->prefix("xf_groups")
		. " SET status='A', is_public='1'" . " WHERE group_id='".$group_id."'";
		$result = $icmsDB->queryF($sql);
		// Refresh the community object
		$community = group_get_object($group_id);
	}
	if (! $community)
	{
		redirect_header(ICMS_URL, 4, _XF_COMM_PROJECTDOESNOTEXIST);
		exit;
	}
	$perm = $community->getPermission($icmsUser);
	 
	//group is private
	if (!$community->isPublic())
	{
		//if it's a private group, you must be a member of that group
		if (!$community->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
		{
			redirect_header(ICMS_URL."/", 4, _XF_COMM_COMMMARKEDASPRIVATE);
			exit;
		}
	}
	 
	//First, for inactive communities, you have to be a project admin or superuser
	if ($community->isInactive() && !$perm->isSuperUser() && !$perm->isAdmin())
	{
		redirect_header(ICMS_URL, 4, _XF_COMM_NOTAUTHORIZEDTOENTER);
		exit;
	}
	//for dead communities must be member of xoopsforge project
	if (!$community->isActive() && !$perm->isSuperUser())
	{
		redirect_header(ICMS_URL, 4, _XF_COMM_NOTAUTHORIZEDTOENTER);
		exit;
	}
	 
	if ($community->isProject())
	{
		redirect_header(ICMS_URL."/modules/xfmod/project/?".$community->getUnixName(), 4, "");
		exit;
	}
	include("../../../header.php");
	 
	$icmsTpl->assign("project_title", project_title($community));
	$icmsTpl->assign("project_tabs", project_tabs('home', $group_id));
	if ($community->isInactive() && $activate != 'y')
	{
		$icmsTpl->assign("inactive_info", "\r\n<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\" width=\"100%\">\n" . "<tr><td align=\"center\"><strong>"._XF_COMM_THISCOMMISINACTIVE."</strong></td></tr>\n" . "<form action=\"".ICMS_URL."/modules/xfmod/community/?".$community->getUnixName()."\" method=\"POST\">\n" . "<input type=\"hidden\" name=\"activate\" value=\"y\">\n" . "<tr><td align=\"center\"><input type=\"submit\" name=\"submit\" value=\""._XF_COMM_REACTIVATECOMM."\">\n" . "</td></tr></form></table><hr>\n");
	}
	 
	$querytotalcount = $icmsDB->getRowsNum($res_grp);
	// ########################### List all projects in this communtiy
	$title = _XF_COMM_TOPTEN;
	$content = "<strong>Projects of this community</strong><br>";
	 
	$grp = $icmsDB->prefix("xf_groups");
	$metric = $icmsDB->prefix("xf_project_weekly_metric");
	$trove = $icmsDB->prefix("xf_trove_group_link");
	 
	$sql = "SELECT unix_group_name, group_name, short_description FROM $trove " ."LEFT JOIN $grp ON $trove.group_id=$grp.group_id " ."LEFT JOIN $metric ON $trove.group_id=$metric.group_id " ."WHERE trove_cat_id=$group_id " ."AND $grp.is_public=1 " ."AND $grp.status='A' " ."ORDER BY group_name ASC";
	 
	$limit = 1000;
	$result = $icmsDB->query($sql, $limit);
	 
	if (!$result)
	{
		echo "Error: ".$icmsDB->error()."<br/>";
		exit;
	}
	 
	$rows = $icmsDB->getRowsNum($result);
	 
	if (!$result || $rows < 1)
	{
		$content .= "No current results<br>";
	}
	else
	{
		for($i = 0; $i < $rows; $i++)
		{
			$curr_group = $icmsDB->fetchArray($result);
			$content .= "<BR><a href='".ICMS_URL."/modules/xfmod/project/?".$curr_group['unix_group_name']."'>";
			$content .= "<strong>".$curr_group['group_name']."</strong>";
			$content .= "</a><br>";
			$content .= strip_tags(substr($curr_group['short_description'], 0, 255))."...<BR>";
		}
	}
	 
	$icmsTpl->assign("content", $content);
	 
	include_once(ICMS_ROOT_PATH."/footer.php");
?>