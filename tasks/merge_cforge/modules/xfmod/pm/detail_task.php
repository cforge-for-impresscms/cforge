<?php
	/**
	*
	* SourceForge Project/Task Manager(PM)
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: detail_task.php,v 1.4 2004/02/06 01:42:06 jcox Exp $
	*
	*/
	 
	$icmsOption['template_main'] = 'pm/xfmod_detail_task.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	$g = group_get_object($group_id);
	$perm = $g->getPermission($icmsUser);
	 
	//group is private
	if (!$g->isPublic())
	{
		//if it's a private group, you must be a member of that group
		if (!$g->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
		{
			redirect_header(ICMS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
			exit;
		}
	}
	 
	//meta tag information
	$metaTitle = " "._XF_PM_VIEWATASK." - ".$g->getPublicName();
	$metaKeywords = project_getmetakeywords($group_id);
	$metaDescription = str_replace('"', "&quot;", strip_tags($g->getDescription()));
	 
	$icmsTpl->assign("icms_pagetitle", $metaTitle);
	$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
	$icmsTpl->assign("icms_meta_description", $metaDescription);
	 
	// project nav information
	$icmsTpl->assign("project_title", project_title($g));
	$icmsTpl->assign("project_tabs", project_tabs('pm', $group_id));
	 
	$header = pm_header($g, $perm, _XF_PM_VIEWATASK, $group_project_id);
	$icmsTpl->assign("pm_header", $header);
	 
	$sql = "SELECT * FROM ".$icmsDB->prefix("xf_project_task")." " ."WHERE project_task_id='$project_task_id' " ."AND group_project_id='$group_project_id'";
	 
	$result = $icmsDB->query($sql);
	 
	$content .= "
		 
		<table border='0' width='100%'>
		<th>
		<td><strong>"._XF_PM_PERCENTCOMPLETE.":</strong>
		<BR>";
	$content .= unofficial_getDBResult($result, 0, 'percent_complete');
	$content .= " %
		</td>
		 
		<td><strong>"._XF_G_PRIORITY.":</strong>
		<BR>";
	$content .= unofficial_getDBResult($result, 0, 'priority');
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'><strong>"._XF_G_SUMMARY.":</strong>
		<BR>";
	$content .= $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'summary'));
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'>
		<strong>"._XF_PM_ORIGINALCOMMENT.":</strong>
		<p>";
	$content .= $ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'details'));
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'><strong>"._XF_PM_STARTDATE.":</strong>
		<BR>";
	$content .= date('Y-m-d', unofficial_getDBResult($result, 0, 'start_date'));
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'><strong>"._XF_PM_ENDDATE.":</strong>
		<BR>";
	$content .= date('Y-m-d', unofficial_getDBResult($result, 0, 'end_date'));
	$content .= "
		</td>
		</th>
		 
		<th>
		<td valign='TOP'>";
	 
	/*
	Get the list of ids this is assigned to and convert to array
	to pass into multiple select box
	*/
	 
	$result2 = $icmsDB->query("SELECT u.uname AS User_Name " ."FROM ".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_project_assigned_to")." pat " ."WHERE u.uid=pat.assigned_to_id " ."AND project_task_id='$project_task_id'");
	 
	$content .= ShowResultSet($result2, _XF_G_ASSIGNEDTO);
	$content .= "
		</td>
		<td valign='TOP'>";
	 
	/*
	Get the list of ids this is dependent on and convert to array
	to pass into multiple select box
	*/
	$result2 = $icmsDB->query("SELECT pt.summary " ."FROM ".$icmsDB->prefix("xf_project_dependencies")." pd,".$icmsDB->prefix("xf_project_task")." pt " ."WHERE is_dependent_on_task_id=pt.project_task_id " ."AND pd.project_task_id='$project_task_id'");
	 
	$content .= ShowResultSet($result2, _XF_PM_DEPENDENTONTASK);
	$content .= "
		</td>
		</th>
		 
		<th>
		<td>
		<strong>"._XF_PM_HOURS.":</strong>
		<BR>";
	$content .= unofficial_getDBResult($result, 0, 'hours');
	$content .= "
		</td>
		 
		<td>
		<strong>"._XF_PM_STATUS.":</strong>
		<BR>";
	 
	$content .= pm_data_get_status_name(unofficial_getDBResult($result, 0, 'status_id'));
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'>";
	$content .= pm_show_dependent_tasks($project_task_id, $group_id, $group_project_id);
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'>";
	$content .= pm_show_task_details($project_task_id);
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'>";
	$content .= pm_show_task_history($project_task_id);
	$content .= "
		</td>
		</th>
		 
		</table>";
	 
	$icmsTpl->assign("content", $content);
	 
?>