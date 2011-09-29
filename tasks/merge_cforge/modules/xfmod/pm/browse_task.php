<?php
	/**
	*
	* SourceForge Project/Task Manager(PM)
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: browse_task.php,v 1.5 2004/02/06 01:42:06 jcox Exp $
	*
	*/
	$icmsOption['template_main'] = 'pm/xfmod_browse_task.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if (!isset($offset) || $offset < 0)
	{
		$offset = 0;
	}
	 
	//
	//  Set up local objects
	//
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
	$metaTitle = " "._XF_PM_BROWSETASKS." - ".$g->getPublicName();
	$metaKeywords = project_getmetakeywords($group_id);
	$metaDescription = str_replace('"', "&quot;", strip_tags($g->getDescription()));
	 
	$icmsTpl->assign("icms_pagetitle", $metaTitle);
	$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
	$icmsTpl->assign("icms_meta_description", $metaDescription);
	 
	// project nav information
	$icmsTpl->assign("project_title", project_title($g));
	$icmsTpl->assign("project_tabs", project_tabs('pm', $group_id));
	 
	$title = _XF_PM_BROWSETASKS;
	//$_assigned_to = util_http_track_vars('_assigned_to');
	 
	if ($_assigned_to)
	{
		$title .= " "._XF_G_FOR.": ".icmsUser::getUnameFromId($_assigned_to);
	}
	 
	//$_status = util_http_track_vars('_status');
	 
	if ($_status && $_status != 100)
	{
		$title .= " "._XF_PM_BYSTATUS.": ".pm_data_get_status_name($_status);
	}
	 
	$header = pm_header($g, $perm, $title, $group_project_id);
	$icmsTpl->assign("pm_header", $header);
	 
	//
	// Memorize order by field as a user preference if explicitly specified.
	// Automatically discard invalid field names.
	//
	if (!isset($order))
	{
		$order = 'project_task_id';
	}
	 
	if ($order)
	{
		//if ordering by priority, sort DESC
		$order_by = " ORDER BY pt.$order".(($order == 'priority') ? ' DESC ':' ');
	}
	else
	{
		$order_by = "";
	}
	 
	//the default is to show 'my' tasks, not 'open' as it used to be
	 
	if (!$set)
	{
		/*
		if no set is passed in, see if a preference was set
		if no preference or not logged in, use open set
		*/
		if ($icmsUser)
		{
			 
			//icms_debug('icmsuser');
			 
			$set = 'my';
			$_status = 1;
			$_assigned_to = $icmsUser->getVar("uid");
		}
		else
		{
			 
			//icms_debug('NO icmsuser');
			 
			$set = 'open';
			$_status = 1;
			$_assigned_to = 0;
		}
	}
	 
	/*
	Display tasks based on the form post - by user or status or both
	*/
	 
	//if status selected, and more to where clause
	if ($_status && ($_status != 100))
	{
		//for open tasks, add status=100 to make sure we show all
		$status_str = "AND pt.status_id IN($_status".(($_status == 1)?',100':'').")";
	}
	else
	{
		//no status was chosen, so don't add it to where clause
		$status_str = '';
	}
	 
	//if assigned to selected, and more to where clause
	if ($_assigned_to)
	{
		$assigned_str = "AND pat.assigned_to_id='$_assigned_to' AND pat.project_task_id=pt.project_task_id";
		$assigned_table = ",".$icmsDB->prefix("xf_project_assigned_to")." pat ";
		 
	}
	else
	{
		//no assigned to was chosen, so don't add it to where clause
		$assigned_str = '';
		$assigned_table = '';
	}
	 
	//build page title to make bookmarking easier
	//if a user was selected, add the user_name to the title
	//same for status
	 
	if ($set == "my")
	{
		$pagename = "pm_browse_my";
	}
	elseif($set == "open")
	{
		$pagename = "pm_browse_open";
	}
	else
	{
		$pagename = "pm_browse_custom";
	}
	 
	$sql = "SELECT pt.priority,pt.group_project_id,pt.project_task_id," ."pt.start_date,pt.end_date,pt.percent_complete,pt.summary " ."FROM ".$icmsDB->prefix("xf_project_task")." pt $assigned_table " ."WHERE pt.group_project_id='$group_project_id' " ." $assigned_str $status_str ".$order_by;
	 
	$message = _XF_PM_BROWSINGCUSTOMTASKLIST;
	 
	//icms_debug_info( 'the actual SQL', $sql );
	 
	$result = $icmsDB->query($sql, 51, $offset);
	 
	/*
	creating a custom technician box which includes "any" and "unassigned"
	*/
	 
	$res_tech = pm_data_get_technicians($group_id);
	 
	$tech_id_arr = util_result_column_to_array($res_tech, 0);
	$tech_id_arr[] = '0';
	//this will be the 'any' row
	 
	$tech_name_arr = util_result_column_to_array($res_tech, 1);
	$tech_name_arr[] = ''._XF_G_ANY;
	 
	$tech_box = html_build_select_box_from_arrays($tech_id_arr, $tech_name_arr, '_assigned_to', $_assigned_to, true, _XF_G_UNASSIGNED);
	 
	 
	$content = '';
	/*
	Show the new pop-up boxes to select assigned to and/or status
	*/
	$content .= '<table border="0" width="10%"><form action="'. $_SERVER['PHP_SELF'] .'" method="GET">
		<input type="hidden" name="group_id" value="'.$group_id.'">
		<input type="hidden" name="set" value="custom">
		<th><td colspan="4" nowrap><strong>'._XF_PM_BROWSTASKBYUSERSTATUS.':</strong></td></th>
		<th><td>'. pm_show_subprojects_box('group_project_id', $group_id, $group_project_id) .'</td>'. '<td>'. $tech_box .'</td><td>'. pm_status_box('_status', $_status, _XF_G_ANY) .'</td>'. '<td><input type="submit" name="submit" value="'._XF_G_BROWSE.'"></td></th></form></table>';
	 
	if ($icmsDB->getRowsNum($result) < 1)
	{
		 
		$content .= '<H4>'._XF_PM_NOMATCHINGTASKSFOUND.'</H4>
			<p>
			<strong>'._XF_PM_ADDTASKSUSINGLINK.'</strong>';
		$content .= $icmsDB->error();
	}
	else
	{
		 
		//create a new $set string to be used for next/prev button
		if ($set == 'custom')
		{
			$set .= '&_assigned_to='.$_assigned_to.'&_status='.$_status;
		}
		 
		/*
		Now display the tasks in a table with priority colors
		*/
		 
		$content .= '<BR />
			<H4>'.$message.' '._XF_G_IN.' '. pm_data_get_group_name($group_project_id) .'</H4>';
		 
		$content .= pm_show_tasklist($result, $offset, $set);
		 
		$content .= '<p>* '._XF_PM_DENOTESOVERDUETASKS;
		$content .= show_priority_colors_key();
		 
		$url = ICMS_URL."/modules/xfmod/pm/task.php?group_id=$group_id&group_project_id=$group_project_id&func=browse&set=$set&order=";
		 
		$content .= "<p>".sprintf(_XF_PM_CLICKCOLUMNORSORTPRIORITY, $url."priority");
		 
	}
	 
	$icmsTpl->assign("content", $content);
	 
?>