<?php
	/**
	*
	* SourceForge Project/Task Manager(PM)
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: mod_task.php,v 1.4 2004/02/06 01:42:06 jcox Exp $
	*
	*/
	 
	$icmsOption['template_main'] = 'pm/xfmod_mod_task.html';
	 
	//$project_task_id = util_http_track_vars('project_task_id');
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
	$metaTitle = " "._XF_PM_MODIFYATASK." - ".$g->getPublicName();
	$metaKeywords = project_getmetakeywords($group_id);
	$metaDescription = str_replace('"', "&quot;", strip_tags($g->getDescription()));
	 
	$icmsTpl->assign("icms_pagetitle", $metaTitle);
	$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
	$icmsTpl->assign("icms_meta_description", $metaDescription);
	 
	// project nav information
	$icmsTpl->assign("project_title", project_title($g));
	$icmsTpl->assign("project_tabs", project_tabs('pm', $group_id));
	 
	$header = pm_header($g, $perm, _XF_PM_MODIFYATASK, $group_project_id);
	$icmsTpl->assign("pm_header", $header);
	 
	$sql = "SELECT * FROM ".$icmsDB->prefix("xf_project_task")." " ."WHERE project_task_id='$project_task_id' " ."AND group_project_id='$group_project_id'";
	 
	$result = $icmsDB->query($sql);
	 
	$content = "
		 
		<form action='".$_SERVER['PHP_SELF']."' METHOD='POST'>
		<input type='hidden' name='func' value='postmodtask'>
		<input type='hidden' name='group_id' value='".$group_id."'>
		<input type='hidden' name='group_project_id' value='".$group_project_id."'>
		<input type='hidden' name='project_task_id' value='".$project_task_id."'>
		 
		<table border='0' width='100%'>
		<th>
		<td><strong>"._XF_PM_SUBPROJECT.":</strong>
		<BR>";
	$content .= pm_show_subprojects_box('new_group_project_id', $group_id, $group_project_id);
	$content .= "
		</td>
		 
		<td>
		<input type='submit' value='"._XF_G_CHANGE."' name='submit'>
		</td>
		</th>
		 
		<th>
		<td><strong>"._XF_PM_PERCENTCOMPLETE.":</strong>
		<BR>";
	$content .= pm_show_percent_complete_box('percent_complete', unofficial_getDBResult($result, 0, 'percent_complete'));
	$content .= "
		</td>
		 
		<td><strong>"._XF_G_PRIORITY.":</strong>
		<BR>";
	$content .= build_priority_select_box('priority', unofficial_getDBResult($result, 0, 'priority'));
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'><strong>"._XF_G_SUMMARY.":</strong>
		<BR>
		<input type='text' name='summary' size='40' maxlength='65' value='".$ts->makeTboxData4Edit(unofficial_getDBResult($result, 0, 'summary'))."'>
		</td>
		</th>
		 
		<th>
		<td colspan='2'>
		<strong>"._XF_PM_ORIGINALCOMMENT.":</strong>
		<p>";
	$content .= $ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'details'));
	$content .= "
		<p>
		<strong>"._XF_PM_ADDACOMMENT.":</strong>
		<BR>
		<textarea name='details' rows='5' cols='40' wrap='soft'></textarea>
		</td>
		</th>
		 
		<th>
		<td colspan='2'><strong>"._XF_PM_STARTDATE.":</strong>
		<BR>";
	$content .= pm_show_month_box('start_month', date('m', unofficial_getDBResult($result, 0, 'start_date')));
	$content .= pm_show_day_box('start_day', date('d', unofficial_getDBResult($result, 0, 'start_date')));
	$content .= pm_show_year_box('start_year', date('Y', unofficial_getDBResult($result, 0, 'start_date')));
	$content .= "
		</td>
		</th>
		 
		<th>
		<td colspan='2'><strong>"._XF_PM_ENDDATE.":</strong>
		<BR>";
	$content .= pm_show_month_box('end_month', date('m', unofficial_getDBResult($result, 0, 'end_date')));
	$content .= pm_show_day_box('end_day', date('d', unofficial_getDBResult($result, 0, 'end_date')));
	$content .= pm_show_year_box('end_year', date('Y', unofficial_getDBResult($result, 0, 'end_date')));
	$content .= "
		</td>
		</th>
		 
		<th>
		<td>
		<strong>"._XF_G_ASSIGNEDTO.":</strong>
		<BR>";
	/*
	List of possible users that this one could be assigned to
	*/
	$content .= pm_multiple_assigned_box('assigned_to[]', $group_id, $project_task_id);
	$content .= "
		</td>
		 
		<td>
		<strong>"._XF_PM_DEPENDENTONTASK.":</strong>
		<BR>";
	/*
	List of possible tasks that this one could depend on
	*/
	$content .= pm_multiple_task_depend_box('dependent_on[]', $group_project_id, $project_task_id);
	$content .= "
		</td>
		</th>
		 
		<th>
		<td>
		<strong>"._XF_PM_HOURS.":</strong>
		<BR>
		<input type='text' name='hours' size='5' value='".unofficial_getDBResult($result, 0, 'hours')."'>
		</td>
		 
		<td>
		<strong>"._XF_PM_STATUS.":</strong>
		<BR>";
	$content .= pm_status_box('status_id', unofficial_getDBResult($result, 0, 'status_id'));
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
		 
		<th>
		<td colspan='2' align='middle'>
		<input type='submit' value='"._XF_G_CHANGE."' name='submit'>
		</td>
		</form>
		</th>
		 
		</table>";
	 
	$icmsTpl->assign("content", $content);
	 
?>