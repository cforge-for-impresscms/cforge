<?php
	/**
	*
	* SourceForge Project/Task Manager(PM)
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: pm_utils.php,v 1.8 2004/02/06 23:43:50 devsupaul Exp $
	*
	*/
	 
	 
	/*
	 
	Project/Task Manager
	By Tim Perdue, Sourceforge, 11/99
	Heavy rewrite by Tim Perdue April 2000
	 
	*/
	 
	function pm_header($group, $perm, $title = _XF_PM_TASKS, $group_project_id)
	{
		 
		global $icmsUser, $feedback, $icmsTheme;
		 
		$content = '';
		 
		if ($group)
		{
			 
			if (!$group->isProject())
			{
				$content .= _XF_PM_ONLYPROJECTSCANUSE;
				include(ICMS_ROOT_PATH."/footer.php");
				exit;
			}
			 
			if (!$group->usesPm())
			{
				$content .= _XF_PM_PROJECTSTURNEDOFF;
				include(ICMS_ROOT_PATH."/footer.php");
				exit;
			}
			 
			$content .= "<p><strong>";
			 
			if ($perm->isPMAdmin())
			{
				$content .= "<a href='".ICMS_URL."/modules/xfmod/pm/admin/?group_id=".$group->getID()."'>"._XF_G_ADMIN."</a> | ";
			}
			 
			$content .= "<a href='".ICMS_URL."/modules/xfmod/pm/?group_id=".$group->getID()."'>"._XF_PM_SUBPROJECTLIST."</a>";
			 
			if ($group_project_id)
			{
				if ($icmsUser)
				{
					$content .= " | <a href='".ICMS_URL."/modules/xfmod/pm/task.php?group_id=".$group->getID()."&group_project_id=$group_project_id&func=addtask'>"._XF_PM_ADDTASK."</a>";
					$content .= " | <a href='".ICMS_URL."/modules/xfmod/pm/task.php?group_id=".$group->getID()."&group_project_id=$group_project_id&func=browse&set=my'>"._XF_PM_MYTASKS."</a>";
				}
				$content .= " | <a href='".ICMS_URL."/modules/xfmod/pm/task.php?group_id=".$group->getID()."&group_project_id=$group_project_id&func=browse&set=open'>"._XF_PM_BROWSEOPENTASKS."</a>";
			}
			 
			$content .= "</strong><p><font color='red'>".$feedback."</font></p>";
			 
			return $content;
		}
		else
		{
			$content .= 'Error<br />Invalid Group';
			return $content;
			//include(ICMS_ROOT_PATH."/footer.php");
			//exit;
		}
	}
	 
	function pm_footer()
	{
		include(ICMS_ROOT_PATH."/footer.php");
	}
	 
	function pm_status_box($name = 'status_id', $checked = 'xyxy', $text_100 = _XF_G_NONE)
	{
		$result = pm_data_get_statuses();
		return html_build_select_box($result, $name, $checked, true, $text_100);
	}
	 
	function pm_tech_select_box($name = 'assigned_to', $group_id = false, $checked = 'xzxz')
	{
		if (!$group_id)
		{
			return 'ERROR - no group_id';
		}
		else
		{
			$result = pm_data_get_technicians($group_id);
			return html_build_select_box($result, $name, $checked);
		}
	}
	 
	function pm_multiple_task_depend_box($name = 'dependent_on[]', $group_project_id = false, $project_task_id = false)
	{
		if (!$group_project_id)
		{
			return 'ERROR - no group_project_id';
		}
		else
		{
			$result = pm_data_get_tasks($group_project_id);
			if ($project_task_id)
			{
				$result = pm_data_get_other_tasks($group_project_id, $project_task_id);
				//get the data so we can mark items as SELECTED
				$result2 = pm_data_get_dependent_tasks($project_task_id);
				return html_build_multiple_select_box($result, $name, util_result_column_to_array($result2));
			}
			else
			{
				return html_build_multiple_select_box($result, $name, array());
			}
		}
	}
	 
	function pm_show_subprojects_box($name = 'group_project_id', $group_id = false, $group_project_id = false)
	{
		if (!$group_id || !$group_project_id)
		{
			return 'ERROR - no group_id defined';
		}
		else
		{
			$result = pm_data_get_subprojects($group_id);
			return html_build_select_box($result, $name, $group_project_id, false);
		}
	}
	 
	function pm_multiple_assigned_box($name = 'assigned_to[]', $group_id = false, $project_task_id = false)
	{
		if (!$group_id)
		{
			return 'ERROR - no group_id';
		}
		else
		{
			$result = pm_data_get_technicians($group_id);
			if ($project_task_id)
			{
				//get the data so we can mark items as SELECTED
				$result2 = pm_data_get_assigned_to($project_task_id);
				return html_build_multiple_select_box($result, $name, util_result_column_to_array($result2));
			}
			else
			{
				return html_build_multiple_select_box($result, $name, array());
			}
		}
	}
	 
	function pm_show_percent_complete_box($name = 'percent_complete', $selected = 0)
	{
		$content = '
			<select name="'.$name.'">';
		$content .= '
			<option value="0">'._XF_PM_NOTSTARTED;
		for($i = 5; $i < 101; $i += 5)
		{
			$content .= '
				<option value="'.$i.'"';
			if ($i == $selected)
			{
				$content .= ' SELECTED';
			}
			$content .= '>'.$i.'%';
		}
		$content .= '
			</select>';
		return $content;
	}
	 
	function pm_show_month_box($name, $select_month = 0)
	{
		 
		$content = '
			<select name="'.$name.'" size="1">';
		$monthlist = array('1' => _XF_G_JANUARY,
			'2' => _XF_G_FEBRUARY,
			'3' => _XF_G_MARCH,
			'4' => _XF_G_APRIL,
			'5' => _XF_G_MAY,
			'6' => _XF_G_JUNE,
			'7' => _XF_G_JULY,
			'8' => _XF_G_AUGUST,
			'9' => _XF_G_SEPTEMBER,
			'10' => _XF_G_OCTOBER,
			'11' => _XF_G_NOVEMBER,
			'12' => _XF_G_DECEMBER);
		 
		for($i = 1; $i <= count($monthlist); $i++)
		{
			if ($i == $select_month)
			{
				$content .= '
					<option selected value="'.$i.'">'.$monthlist[$i];
			}
			else
			{
				$content .= '
					<option value="'.$i.'">'.$monthlist[$i];
			}
		}
		$content .= '
			</select>';
		return $content;
	}
	 
	function pm_show_day_box($name, $day = 1)
	{
		 
		$content = '';
		$content .= '
			<select name="'.$name.'" size="1">';
		for($i = 1; $i <= 31; $i++)
		{
			if ($i == $day)
			{
				$content .= '
					<option selected value="'.$i.'">'.$i;
			}
			else
			{
				$content .= '
					<option value="'.$i.'">'.$i;
			}
		}
		$content .= '
			</select>';
		return $content;
	}
	 
	function pm_show_year_box($name, $year = 1)
	{
		 
		$content = '';
		$content .= '
			<select name="'.$name.'" size="1">';
		for($i = 1999; $i <= 2013; $i++)
		{
			if ($i == $year)
			{
				$content .= '
					<option selected value="'.$i.'">'.$i;
			}
			else
			{
				$content .= '
					<option value="'.$i.'">'.$i;
			}
		}
		$content .= '
			</select>';
		return $content;
	}
	 
	function pm_show_tasklist($result, $offset, $set = 'open')
	{
		global $sys_datefmt, $group_id, $group_project_id, $icmsDB, $ts;
		/*
		Accepts a result set from the bugs table. Should include all columns from
		the table, and it should be joined to USER to get the user_name.
		*/
		 
		$rows = $icmsDB->getRowsNum($result);
		 
		$url = ICMS_URL."/modules/xfmod/pm/task.php?group_id=$group_id&group_project_id=$group_project_id&func=browse&set=$set&order=";
		 
		$content = "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<th><strong><a href='".$url."project_task_id'>"._XF_PM_TASKID."</a></strong></th>" ."<th><strong><a href='".$url."summary'>"._XF_G_SUMMARY."</a></strong></th>" ."<th><strong><a href='".$url."start_date'>"._XF_PM_STARTDATE."</a></strong></th>" ."<th><strong><a href='".$url."end_date'>"._XF_PM_ENDDATE."</a></strong></th>" ."<th><strong><a href='".$url."percent_complete'>"._XF_PM_PERCENTCOMPLETE."</a></strong></th>" ."</tr>";
		 
		$now = time();
		 
		for($i = 0; $i < $rows; $i++)
		{
			$content .= '<tr bgcolor="'.get_priority_color(unofficial_getDBResult($result, $i, 'priority')).'">' .'<td><a href="'.$_SERVER['PHP_SELF'].'?func=detailtask' .'&project_task_id='.unofficial_getDBResult($result, $i, 'project_task_id')
			.'&group_id='.$group_id .'&group_project_id='.unofficial_getDBResult($result, $i, 'group_project_id').'">' .unofficial_getDBResult($result, $i, 'project_task_id').'</a></td>' .'<td>'.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'summary')).'</td>' .'<td>'.date('Y-m-d', unofficial_getDBResult($result, $i, 'start_date')).'</td>' .'<td>'.(($now > unofficial_getDBResult($result, $i, 'end_date'))?'<strong>* ':'&nbsp; ') . date('Y-m-d', unofficial_getDBResult($result, $i, 'end_date')).'</td>' .'<td>';
			$pc = unofficial_getDBResult($result, $i, 'percent_complete');
			for($k = 5; $k <= 100; $k += 5)
			{
				if ($k <= $pc)
				$content .= '<img src="'.ICMS_URL.'/modules/xfmod/images/pb.gif" width="6" height="10" alt="'.$pc.'% complete">';
				else
					$content .= '<img src="'.ICMS_URL.'/modules/xfmod/images/pb_e.gif" width="6" height="10" alt="'.$pc.'% complete">';
			}
			$content .= '&nbsp;&nbsp;'.$pc.'%</td></tr>';
		}
		/*
		Show extra rows for <-- Prev / Next -->
		*/
		$content .= '<tr><td colspan="2">';
		if ($offset > 0)
		{
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?func=browse&group_project_id=' .$group_project_id.'&set='.$set.'&group_id='.$group_id.'&offset='.($offset-50).'"><strong><-- '._XF_G_PREVIOUS.' 50</strong></a>';
		}
		else
		{
			$content .= '&nbsp;';
		}
		$content .= '</td><td>&nbsp;</td><td colspan="2">';
		 
		if ($rows == 50)
		{
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?func=browse&group_project_id=' .$group_project_id.'&set='.$set.'&group_id='.$group_id.'&offset='.($offset+50).'"><strong>'._XF_G_NEXT.' 50 --></strong></a>';
		}
		else
		{
			$content .= '&nbsp;';
		}
		$content .= '</td></tr></table>';
		return $content;
	}
	 
	function pm_show_dependent_tasks($project_task_id, $group_id, $group_project_id)
	{
		global $icmsDB;
		 
		$content = '';
		 
		$sql = "SELECT pt.project_task_id,pt.summary " ."FROM ".$icmsDB->prefix("xf_project_task")." pt,".$icmsDB->prefix("xf_project_dependencies")." pd " ."WHERE pt.project_task_id=pd.project_task_id " ."AND pd.is_dependent_on_task_id='$project_task_id'";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		if ($rows > 0)
		{
			$content .= '<H4>'._XF_PM_TASKSTHATDEPEND.'</H4>
				<p>';
			 
			$content .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<th><strong>"._XF_PM_TASKID."</strong></th>" ."<th><strong>"._XF_G_SUMMARY."</strong></th>" ."</tr>";
			 
			for($i = 0; $i < $rows; $i++)
			{
				$content .= '<th class="'.($j%2 > 0?'bg1':'bg3').'">
					<td><a href="'.ICMS_URL.'/modules/xfmod/pm/task.php?func=detailtask&project_task_id=' .unofficial_getDBResult($result, $i, 'project_task_id')
				.'&group_id='.$group_id .'&group_project_id='.$group_project_id.'">' .unofficial_getDBResult($result, $i, 'project_task_id').'</td>
					<td>'.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'summary')).'</td></tr>';
			}
			$content .= '</table>';
		}
		else
		{
			$content .= '<H4>'._XF_PM_NOTASKSDEPENDENT.'</H4>';
			$content .= $icmsDB->error();
		}
		return $content;
	}
	 
	function pm_show_task_details($project_task_id)
	{
		global $sys_datefmt, $icmsDB, $ts;
		 
		$content = '';
		 
		$sql = "SELECT ph.field_name,ph.old_value,ph.date,u.uname " ."FROM ".$icmsDB->prefix("xf_project_history")." ph,".$icmsDB->prefix("users")." u " ."WHERE ph.mod_by=u.uid " ."AND ph.field_name='details' " ."AND project_task_id='$project_task_id' ORDER BY ph.date DESC";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		if ($rows > 0)
		{
			$content .= '<H4>'._XF_G_FOLLOWUPS.'</H4>
				<p>';
			 
			$content .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<th><strong>"._XF_G_COMMENT."</strong></th>" ."<th><strong>"._XF_G_DATE."</strong></th>" ."<th><strong>"._XF_G_BY."</strong></th>" ."</tr>";
			 
			for($i = 0; $i < $rows; $i++)
			{
				$content .= '<th class="'.($i%2 > 0?'bg1':'bg3').'">
					<td>'. $ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'old_value')).'</td>
					<td valign="top">'.date($sys_datefmt, unofficial_getDBResult($result, $i, 'date')).'</td>
					<td valign="top">'.unofficial_getDBResult($result, $i, 'uname').'</td></tr>';
			}
			$content .= '</table>';
		}
		else
		{
			$content .= '<H4>'._XF_G_NOCOMMENTSADDED.'</H4>';
		}
		return $content;
	}
	 
	function pm_show_task_history($project_task_id)
	{
		global $sys_datefmt, $icmsDB;
		 
		$content = '';
		/* test
		$content .=   '<style>
		.bg1 { background-color: #E3E4E0; }
		.bg2 { background-color: #CCCCCC; }
		.bg3 { background-color: #DDE1DE; }
		.bg4 { background-color: #F5F5F5; }
		.bg5 { background-color: #F5F5F5; }
		</style>';
		*/
		$sql = "SELECT ph.field_name,ph.old_value,ph.date,u.uname " ."FROM ".$icmsDB->prefix("xf_project_history")." ph,".$icmsDB->prefix("users")." u " ."WHERE ph.mod_by=u.uid " ."AND ph.field_name<>'details' " ."AND project_task_id='$project_task_id' ORDER BY ph.date DESC";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		if ($rows > 0)
		{
			$content .= '<H4>'._XF_PM_TASKHISTORY.'</H4>
				<p>';
			 
			$content .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<th><strong>"._XF_G_FIELD."</strong></th>" ."<th><strong>"._XF_G_OLDVALUE."</strong></th>" ."<th><strong>"._XF_G_DATE."</strong></th>" ."<th><strong>"._XF_G_BY."</strong></th>" ."</tr>";
			 
			for($i = 0; $i < $rows; $i++)
			{
				 
				$field = unofficial_getDBResult($result, $i, 'field_name');
				$content .= '<th class="'.($i%2 > 0?'bg1':'bg3').'"><td>'.$field.'</td><td>';
				 
				if ($field == 'status_id')
				{
					$content .= pm_data_get_status_name(unofficial_getDBResult($result, $i, 'old_value'));
				}
				else if($field == 'start_date')
				{
					$content .= date('Y-m-d', unofficial_getDBResult($result, $i, 'old_value'));
				}
				else if($field == 'end_date')
				{
					$content .= date('Y-m-d', unofficial_getDBResult($result, $i, 'old_value'));
				}
				else
				{
					$content .= unofficial_getDBResult($result, $i, 'old_value');
				}
				 
				$content .= '</td>
					<td>'.date($sys_datefmt, unofficial_getDBResult($result, $i, 'date')).'</td>
					<td>'.unofficial_getDBResult($result, $i, 'uname').'</td></tr>';
			}
			$content .= '</table>';
		}
		else
		{
			$content .= '<H4>'._XF_PM_NOTASKHISTORY.'</H4>';
		}
		return $content;
	}
?>