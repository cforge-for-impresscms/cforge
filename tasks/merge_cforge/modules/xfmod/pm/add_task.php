<?php
	/**
	*
	* SourceForge Project/Task Manager(PM)
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: add_task.php,v 1.5 2004/02/06 01:42:06 jcox Exp $
	*
	*/
	 
	include_once("../../../mainfile.php");
	 
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	$icmsOption['template_main'] = 'pm/xfmod_add_task.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	/* http_track_vars */
	//$group_id = util_http_track_vars('group_id');
	 
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
	$metaTitle = " "._XF_PM_ADDATASK." - ".$g->getPublicName();
	$metaKeywords = project_getmetakeywords($group_id);
	$metaDescription = str_replace('"', "&quot;", strip_tags($g->getDescription()));
	 
	$icmsTpl->assign("icms_pagetitle", $metaTitle);
	$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
	$icmsTpl->assign("icms_meta_description", $metaDescription);
	 
	// project nav information
	$icmsTpl->assign("project_title", project_title($g));
	$icmsTpl->assign("project_tabs", project_tabs('pm', $group_id));
	 
	$header = pm_header($g, $perm, _XF_PM_ADDATASK, $group_project_id);
	$icmsTpl->assign("pm_header", $header);
	 
	$content = "
		 
		<form action='".$_SERVER['PHP_SELF']."' METHOD='POST'>
		<input type='hidden' name='func' value='postaddtask'>
		<input type='hidden' name='group_id' value='".$group_id."'>
		<input type='hidden' name='group_project_id' value='".$group_project_id."'>
		 
		<table border='0' width='100%'>
		<tr>
		<td>
		<strong>"._XF_PM_PERCENTCOMPLETE.":</strong>
		<BR>";
	$content .= pm_show_percent_complete_box();
	$content .= "
		</td>
		<td>
		<strong>"._XF_G_PRIORITY.":</strong>
		<BR>";
	$content .= build_priority_select_box();
	$content .= "
		</td>
		</tr>
		 
		<tr>
		<td colspan='2'><strong>"._XF_G_SUMMARY.":</strong>
		<BR>
		<input type='text' name='summary' size='40' maxlength='65'>
		</td>
		</tr>
		<tr>
		<td colspan='2'><strong>"._XF_PM_TASKDETAILS.":</strong>
		<BR>
		<textarea name='details' rows='5' cols='40' wrap='soft'></textarea></td>
		</tr>
		<tr>
		<td colspan='2'><strong>"._XF_PM_STARTDATE.":</strong>
		<BR>";
	$content .= pm_show_month_box('start_month', date('m', time()));
	$content .= pm_show_day_box('start_day', date('d', time()));
	$content .= pm_show_year_box('start_year', date('Y', time()));
	$content .= "
		</td>
		 
		</tr>
		<tr>
		<td colspan='2'><strong>"._XF_PM_ENDDATE.":</strong>
		<BR>";
	$content .= pm_show_month_box('end_month', date('m', time()));
	$content .= pm_show_day_box('end_day', date('d', time()));
	$content .= pm_show_year_box('end_year', date('Y', time()));
	$content .= "
		</td>
		 
		</tr>
		<tr>
		<td>
		<strong>"._XF_G_ASSIGNEDTO.":</strong>
		<BR>";
	$content .= pm_multiple_assigned_box('assigned_to[]', $group_id);
	$content .= "
		</td>
		<td>
		<strong>"._XF_PM_DEPENDENTONTASK.":</strong>
		<BR>";
	$content .= pm_multiple_task_depend_box('dependent_on[]', $group_project_id);
	$content .= "
		</td>
		</tr>
		<tr>
		<td colspan='2'><strong>"._XF_PM_HOURS.":</strong>
		<BR>
		<input type='text' name='hours' size='5'>
		</td>
		</tr>
		<tr>
		<td colspan='2'>
		<input type='submit' value='"._XF_G_SUBMIT."' name='submit'>
		</td>
		</form>
		</tr>
		</table>";
	 
	$icmsTpl->assign("content", $content);
	 
?>