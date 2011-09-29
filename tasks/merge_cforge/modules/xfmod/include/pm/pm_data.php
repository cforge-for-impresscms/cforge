<?php
	/**
	* pm_data.php - Project Manager function library
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: pm_data.php,v 1.2 2003/12/09 15:03:54 devsupaul Exp $
	*
	*/
	 
	/**
	* pm_data_get_tasks() - Return a result set of the 100 most recent tasks in this subproject
	*
	* @param  int  The ID of the project group
	* @returns Database result set
	*
	*/
	function pm_data_get_tasks($group_project_id)
	{
		global $PM_DATA_TASKS, $icmsDB;
		 
		if (!$PM_DATA_TASKS["$group_project_id"])
		{
			$sql = "SELECT project_task_id,summary " ."FROM ".$icmsDB->prefix("xf_project_task")." " ."WHERE group_project_id='$group_project_id' " ."AND status_id <> '3' ORDER BY project_task_id DESC";
			 
			$PM_DATA_TASKS["$group_project_id"] = $icmsDB->query($sql, 100);
		}
		 
		return $PM_DATA_TASKS["$group_project_id"];
	}
	 
	/**
	* pm_data_get_subprojects() - Return a result set of subprojects for this group
	*
	* @param  int  The ID of the group
	* @returns Database result set
	*
	*/
	function pm_data_get_subprojects($group_id)
	{
		global $PM_DATA_SUBPROJECTS, $icmsDB;
		 
		if (!$PM_DATA_SUBPROJECTS["$group_id"])
		{
			$sql = "SELECT group_project_id,project_name " ."FROM ".$icmsDB->prefix("xf_project_group_list")." WHERE group_id='$group_id'";
			 
			$PM_DATA_SUBPROJECTS["$group_id"] = $icmsDB->query($sql);
		}
		 
		return $PM_DATA_SUBPROJECTS["$group_id"];
	}
	 
	/**
	* pm_data_get_other_tasks() - Return a result set of tasks in this subproject that do not equal
	* the passed in task_id
	*
	* @param  int  The ID of the project group
	* @param  int  The ID of the task
	* @returns Database result set
	*
	*/
	function pm_data_get_other_tasks($group_project_id, $project_task_id)
	{
		global $icmsDB;
		 
		$sql = "SELECT project_task_id,summary " ."FROM ".$icmsDB->prefix("xf_project_task")." " ."WHERE group_project_id='$group_project_id' " ."AND status_id <> '3' " ."AND project_task_id <> '$project_task_id' ORDER BY project_task_id DESC";
		 
		return $icmsDB->query($sql, 100);
	}
	 
	/**
	* pm_data_get_technicians() - Return a result set of pm technicians in this group
	*
	* @param  int  The ID of the group
	* @returns Datbase result set
	*
	*/
	function pm_data_get_technicians($group_id)
	{
		global $PM_DATA_TECHNICIANS, $icmsDB;
		 
		if (!$PM_DATA_TECHNICIANS["$group_id"])
		{
			$sql = "SELECT u.uid,u.uname " ."FROM ".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_user_group")." ug " ."WHERE u.uid=ug.user_id " ."AND ug.group_id='$group_id' " ."AND ug.project_flags IN(1,2) " ."ORDER BY u.uname";
			$PM_DATA_TECHNICIANS["$group_id"] = $icmsDB->query($sql);
		}
		 
		return $PM_DATA_TECHNICIANS["$group_id"];
	}
	 
	/**
	* pm_data_get_dependent_tasks() - Return result set of ids of tasks that are dependent on this task
	*
	* @param  int  The project task ID
	* @returns Database result set
	*
	*/
	function pm_data_get_dependent_tasks($project_task_id)
	{
		global $icmsDB;
		 
		$sql = "SELECT is_dependent_on_task_id " ."FROM ".$icmsDB->prefix("xf_project_dependencies")." " ."WHERE project_task_id='$project_task_id'";
		 
		return $icmsDB->query($sql);
	}
	 
	/**
	* pm_data_get_assigned_to() - Return result set of user_ids that are assigned this task
	*
	* @param  int  The project task ID
	* @returns Database result set
	*
	*/
	function pm_data_get_assigned_to($project_task_id)
	{
		global $icmsDB;
		 
		$sql = "SELECT assigned_to_id " ."FROM ".$icmsDB->prefix("xf_project_assigned_to")." " ."WHERE project_task_id='$project_task_id'";
		 
		return $icmsDB->query($sql);
	}
	 
	/**
	* pm_data_get_statuses() - Return result set of statuses
	*
	* @returns Database result set
	*
	*/
	function pm_data_get_statuses()
	{
		global $PM_DATA_STATUSES, $icmsDB;
		 
		if (!$PM_DATA_STATUSES)
		{
			$sql = "SELECT * FROM ".$icmsDB->prefix("xf_project_status");
			$PM_DATA_STATUSES = $icmsDB->query($sql);
		}
		 
		return $PM_DATA_STATUSES;
	}
	 
	/**
	* pm_data_get_status_name() - Simply return status_name from bug_status
	*
	* @param  string Status ID
	* @returns Databse result set on success/Error string on error
	*
	*/
	function pm_data_get_status_name($string)
	{
		global $icmsDB;
		 
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_project_status")." WHERE status_id='$string'";
		 
		$result = $icmsDB->query($sql);
		if ($result && $icmsDB->getRowsNum($result) > 0)
		{
			return unofficial_getDBResult($result, 0, 'status_name');
		}
		else
		{
			return 'Error - '._XF_PM_NOTFOUND;
		}
	}
	 
	/**
	* pm_data_get_group_name() - Simply return the resolution name for this id
	*
	* @param  int  The group project ID
	* @returns Database result set on success/Error string one rror
	*
	*/
	function pm_data_get_group_name($group_project_id)
	{
		global $icmsDB;
		 
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_project_group_list")." WHERE group_project_id='$group_project_id'";
		$result = $icmsDB->query($sql);
		if ($result && $icmsDB->getRowsNum($result) > 0)
		{
			return unofficial_getDBResult($result, 0, 'project_name');
		}
		else
		{
			return 'Error - '._XF_PM_NOTFOUND;
		}
	}
	 
	/**
	* pm_data_create_history() - Handle the insertion of history for these parameters
	*
	* @param  string The field name
	* @param  string The old value
	* @param  int  The project task ID
	* @returns true on success/false on error
	*
	*/
	function pm_data_create_history($field_name, $old_value, $project_task_id)
	{
		global $feedback, $icmsDB, $icmsUser;
		 
		$sql = "INSERT INTO ".$icmsDB->prefix("xf_project_history")."(project_task_id,field_name,old_value,mod_by,date) " ."VALUES('$project_task_id','$field_name','$old_value','".$icmsUser->getVar("uid")."','".time()."')";
		 
		$result = $icmsDB->queryF($sql);
		if (!$result)
		{
			$feedback .= ' ERROR IN AUDIT TRAIL - '.$icmsDB->error();
			return false;
		}
		else
		{
			return true;
		}
	}
	 
	/**
	* pm_data_insert_assigned_to() - Insert the people this task is assigned to
	*
	* @param  array An array of project ID's
	* @param  int  The project task ID
	* @returns true on success/false on error
	*
	*/
	function pm_data_insert_assigned_to($array, $project_task_id)
	{
		global $feedback, $icmsDB;
		$user_count = count($array);
		if ($user_count < 1)
		{
			//if no users selected, insert user "none"
			$sql = "INSERT INTO ".$icmsDB->prefix("xf_project_assigned_to")."(project_task_id,assigned_to_id) " ."VALUES('$project_task_id','100')";
			 
			$result = $icmsDB->queryF($sql);
			if (!$result)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			for($i = 0; $i < $user_count; $i++)
			{
				if (($user_count > 1) && ($array[$i] == 100))
				{
					//don't insert the row if there's more
					//than 1 item selected and this item is the "none user"
				}
				else
				{
					$sql = "INSERT INTO ".$icmsDB->prefix("xf_project_assigned_to")."(project_task_id,assigned_to_id) " ."VALUES('$project_task_id','$array[$i]')";
					 
					$result = $icmsDB->queryF($sql);
					if (!$result)
					{
						$feedback .= ' ERROR inserting project_assigned_to '.$icmsDB->error();
						return false;
					}
				}
			}
			return true;
		}
	}
	 
	/**
	* pm_data_update_assigned_to() - Delete then Insert the people this task is assigned to
	*
	* @param  array An array of project ID's
	* @param  int  The project task ID
	* @returns Return value of pm_data_insert_assigned_to()
	* @see pm_data_insert_assigned_do()
	*
	*/
	function pm_data_update_assigned_to($array, $project_task_id)
	{
		global $feedback, $icmsDB;
		 
		$toss = $icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_project_assigned_to")." WHERE project_task_id='$project_task_id'");
		 
		return pm_data_insert_assigned_to($array, $project_task_id);
	}
	 
	/**
	* pm_data_insert_dependent_tasks() - Insert the list of dependencies
	*
	* @param  array An array of project ID's
	* @param  int  The project task ID
	* @returns true on success/false on error
	*
	*/
	function pm_data_insert_dependent_tasks($array, $project_task_id)
	{
		global $feedback, $icmsDB;
		 
		$depend_count = count($array);
		if ($depend_count < 1)
		{
			//if no tasks selected, insert task "none"
			$sql = "INSERT INTO ".$icmsDB->prefix("xf_project_dependencies")."(project_task_id,is_dependent_on_task_id) " ."VALUES('$project_task_id','100')";
			 
			$result = $icmsDB->queryF($sql);
			if (!$result)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			for($i = 0; $i < $depend_count; $i++)
			{
				if (($depend_count > 1) && ($array[$i] == 100))
				{
					//don't insert the row if there's more
					//than 1 item selected and this item is the "none task"
				}
				else
				{
					$sql = "INSERT INTO ".$icmsDB->prefix("xf_project_dependencies")."(project_task_id,is_dependent_on_task_id) " ."VALUES('$project_task_id','$array[$i]')";
					 
					$result = $icmsDB->queryF($sql);
					if (!$result)
					{
						$feedback .= ' ERROR inserting dependent_tasks '.$icmsDB->error();
						return false;
					}
				}
			}
			return true;
		}
	}
	 
	/**
	* pm_data_update_dependend_tasks() - Delete then Insert the list of dependencies
	*
	* @param  array An array of project ID's
	* @param  int  The project task ID
	* @returns true on success/false on error
	*
	*/
	function pm_data_update_dependent_tasks($array, $project_task_id)
	{
		global $feedback, $icmsDB;
		 
		$toss = $icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_project_dependencies")." WHERE project_task_id='$project_task_id'");
		 
		return pm_data_insert_dependent_tasks($array, $project_task_id);
	}
	 
	/**
	* pm_data_create_task() - Creates a new task in the task mgr
	* NOTE: Does no handle security!!
	*
	* @param  int  The group project ID
	* @param  int  The starting month
	* @param  int  The starting day
	* @param  int  The ending month
	* @param  int  The ending day
	* @param  int  The ending year
	* @param  string The task summary
	* @param  string Details of the task
	* @param  int  The completed percentage of the task
	* @param  int  The task priority
	* @param  int  The number of hours exptected to complete this task
	* @param  int  The user ID of the person to which this task is assigned
	* @param  int  on The task ID on which this task depends
	* @returns Nnew project_task_id or false and $feedback
	*
	*/
	function pm_data_create_task($group_project_id, $start_month, $start_day, $start_year, $end_month, $end_day,
		$end_year, $summary, $details, $percent_complete, $priority, $hours, $assigned_to, $dependent_on)
	{
		global $feedback, $icmsDB, $icmsUser, $ts;
		if (!$group_project_id || !$start_month || !$start_day || !$start_year || !$end_month || !$end_day || !$end_year || !$summary || !$details || !$priority)
		{
			$feedback .= ' ERROR - '._XF_PM_MISSINGREQPARAMETERS.' ';
			return false;
		}
		if (mktime(0, 0, 0, $start_month, $start_day, $start_year) > mktime(0, 0, 0, $end_month, $end_day, $end_year))
		{
			$feedback .= 'Error<br />'._XF_PM_ENDDATEMUSTBEGREATER;
			return false;
		}
		 
		$sql = "INSERT INTO ".$icmsDB->prefix("xf_project_task")." " ."(group_project_id,summary,details,percent_complete,priority,hours,start_date,end_date,created_by,status_id) VALUES(" ."'$group_project_id'," ."'".$ts->makeTboxData4Save($summary)."'," ."'".$ts->makeTareaData4Save($details)."'," ."'$percent_complete'," ."'$priority','$hours'," ."'".mktime(0, 0, 0, $start_month, $start_day, $start_year)."'," ."'".mktime(0, 0, 0, $end_month, $end_day, $end_year)."'," ."'".$icmsUser->getVar("uid")."'," ."'1')";
		 
		$result = $icmsDB->queryF($sql);
		$project_task_id = $icmsDB->getInsertId();
		if (!$result || !$project_task_id)
		{
			$feedback .= ' ERROR INSERTING ROW '.$icmsDB->error();
			return false;
		}
		else
		{
			$feedback .= ' Successfully added task ';
			if (!pm_data_insert_assigned_to($assigned_to, $project_task_id))
			{
				$icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_project_task")." WHERE project_task_id='$project_task_id'");
				$feedback .= ' ERROR inserting assigned to ';
				return false;
			}
		}
		 
		if (!pm_data_insert_dependent_tasks($dependent_on, $project_task_id))
		{
			$icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_project_task")." WHERE project_task_id='$project_task_id'");
			$feedback .= ' ERROR inserting assigned to ';
			return false;
		}
		mail_followup($project_task_id, $group_project_id, 1);
		 
		return $project_task_id;
	}
	 
	/**
	* pm_data_update_task() - Update a task
	* NOTE: Does not handle security at this time!
	* This assumes that you have verified this $group_project_id truly belongs to this $group_id
	* AND that this user is a project_task_admin
	*
	* @param  int  The group project ID
	* @param  int  The starting month
	* @param  int  The starting day
	* @param  int  The ending month
	* @param  int  The ending day
	* @param  int  The ending year
	* @param  string The task summary
	* @param  string Details of the task
	* @param  int  The completed percentage of the task
	* @param  int  The task priority
	* @param  int  The number of hours exptected to complete this task
	* @param  int  The user ID of the person to which this task is assigned
	* @param  int  The task ID on which this task depends
	* @returns Nnew project_task_id or false and $feedback
	* @returns true/false and $feedback string
	*
	*/
	function pm_data_update_task($group_project_id, $project_task_id, $start_month, $start_day, $start_year,
		$end_month, $end_day, $end_year, $summary, $details, $percent_complete, $priority, $hours,
		$status_id, $assigned_to, $dependent_on, $new_group_project_id, $group_id)
	{
		global $feedback, $icmsDB, $ts;
		 
		if (!$group_project_id || !$project_task_id || !$status_id || !$start_month || !$start_day || !$start_year || !$end_month || !$end_day || !$end_year || !$summary || !$priority || !$new_group_project_id || !$group_id)
		{
			$feedback .= ' ERROR - '._XF_PM_MISSINGREQPARAMETERS.' ';
			return false;
		}
		 
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_project_task")." WHERE project_task_id='$project_task_id' AND group_project_id='$group_project_id'";
		$result = $icmsDB->query($sql);
		 
		if ($icmsDB->getRowsNum($result) < 1)
		{
			$feedback .= " ERROR - "._XF_PM_TASKDOESNOTEXIST." ";
			return false;
		}
		 
		/*
		Enforce start date > end date
		*/
		 
		if (mktime(0, 0, 0, $start_month, $start_day, $start_year) > mktime(0, 0, 0, $end_month, $end_day, $end_year))
		{
			$feedback .= ' ERROR - '._XF_PM_ENDDATEMUSTBEGREATER.' ';
			return false;
		}
		 
		/*
		If changing subproject, verify the new subproject belongs to this project
		*/
		if ($group_project_id != $new_group_project_id)
		{
			$sql = "SELECT group_id FROM ".$icmsDB->prefix("xf_project_group_list")." WHERE group_project_id='$new_group_project_id'";
			 
			if (unofficial_getDBResult($icmsDB->query($sql), 0, 'group_id') != $group_id)
			{
				$feedback .= ' '._XF_PM_CANNOTPUTTASKINOTHERGROUP.' ';
				return false;
			}
			else
			{
				pm_data_create_history('subproject_id', $group_project_id, $project_task_id);
			}
		}
		 
		/*
		See which fields changed during the modification
		and create audit trail
		*/
		 
		if (unofficial_getDBResult($result, 0, 'status_id') != $status_id)
		{
			pm_data_create_history('status_id', unofficial_getDBResult($result, 0, 'status_id'), $project_task_id);
		}
		 
		if (unofficial_getDBResult($result, 0, 'priority') != $priority)
		{
			pm_data_create_history('priority', unofficial_getDBResult($result, 0, 'priority'), $project_task_id);
		}
		 
		if ($ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'summary')) != $ts->makeTboxData4Preview($summary))
		{
			pm_data_create_history('summary', $ts->makeTboxData4Save(unofficial_getDBResult($result, 0, 'summary')), $project_task_id);
		}
		 
		if (unofficial_getDBResult($result, 0, 'percent_complete') != $percent_complete)
		{
			pm_data_create_history('percent_complete', unofficial_getDBResult($result, 0, 'percent_complete'), $project_task_id);
		}
		 
		if (unofficial_getDBResult($result, 0, 'hours') != $hours)
		{
			pm_data_create_history('hours', unofficial_getDBResult($result, 0, 'hours'), $project_task_id);
		}
		 
		if (unofficial_getDBResult($result, 0, 'start_date') != mktime(0, 0, 0, $start_month, $start_day, $start_year))
		{
			pm_data_create_history('start_date', unofficial_getDBResult($result, 0, 'start_date'), $project_task_id);
		}
		 
		if (unofficial_getDBResult($result, 0, 'end_date') != mktime(0, 0, 0, $end_month, $end_day, $end_year))
		{
			pm_data_create_history('end_date', unofficial_getDBResult($result, 0, 'end_date'), $project_task_id);
		}
		 
		/*
		Details field is handled a little differently
		 
		Details are comments attached to bugs
		They are still stored in the project_history(audit trail)
		system, but they are not shown in the regular audit trail
		 
		Someday, these should technically be split into their own table.
		*/
		if ($details != '')
		{
			pm_data_create_history('details', $ts->makeTboxData4Save($details), $project_task_id);
		}
		 
		if (!pm_data_update_dependent_tasks($dependent_on, $project_task_id))
		{
			$feedback .= ' ERROR updating dependent tasks ';
			return false;
		}
		 
		if (!pm_data_update_assigned_to($assigned_to, $project_task_id))
		{
			$feedback .= ' ERROR updating assigned to ';
			return false;
		}
		 
		/*
		Update the actual db record
		*/
		$sql = "UPDATE ".$icmsDB->prefix("xf_project_task")." SET " ."status_id='$status_id'," ."priority='$priority'," ."summary='".$ts->makeTboxData4Save($summary)."'," ."start_date='".mktime(0, 0, 0, $start_month, $start_day, $start_year)."'," ."end_date='".mktime(0, 0, 0, $end_month, $end_day, $end_year)."'," ."hours='$hours'," ."percent_complete='$percent_complete'," ."group_project_id='$new_group_project_id' " ."WHERE project_task_id='$project_task_id' " ."AND group_project_id='$group_project_id'";
		 
		$result = $icmsDB->queryF($sql);
		if (!$result)
		{
			$feedback .= ' ERROR - Database Update Failed '.$icmsDB->error();
			return false;
		}
		else
		{
			$feedback .= ' '._XF_PM_MODIFIEDTASK.' ';
			 
			mail_followup($project_task_id, $new_group_project_id);
			return true;
		}
	}
	 
	/**
	* mail_followup() - Send a message to the person who opened this task and the person(s) it is assigned to
	* Accepts the unique id of a task, its group project id and optionally a list of additional addresses to send to
	*
	* @param  int  The project task ID
	* @param  int  The group project ID
	* @param  string The additional addresses to send the followup
	* @param  bool The flag of whether this is a new task or not
	*
	*/
	function mail_followup($project_task_id, $group_project_id, $more_addresses = false, $new_task = 0)
	{
		global $sys_datefmt, $feedback, $icmsDB, $icmsForge, $ts;
		 
		$sql = "SELECT pt.*, pgl.*, g.group_name,g.new_task_address,g.unix_group_name, ". "g.send_all_tasks,ps.status_name,u.email, ". "u.uname AS creator_name ". "FROM ".$icmsDB->prefix("xf_project_task")." pt,".$icmsDB->prefix("xf_project_group_list")." pgl,".$icmsDB->prefix("xf_project_status")." ps,".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_groups")." g ". "WHERE project_task_id='$project_task_id' ". "AND pt.group_project_id='$group_project_id' ". "AND pt.status_id=ps.status_id ". "AND pt.group_project_id=pgl.group_project_id ". "AND g.group_id=pgl.group_id ". "AND pt.created_by=u.uid";
		 
		$result = $icmsDB->query($sql);
		if ($result && $icmsDB->getRowsNum($result) > 0)
		{
			 
			// Send a message to the task creator
			$to = unofficial_getDBResult($result, 0, 'email');
			 
			// Build the list of developers assigned this task
			$sql = "SELECT u.email AS Email,u.uname ". "FROM ".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_project_assigned_to")." pat ". "WHERE pat.project_task_id='$project_task_id' ". "AND u.level <> 0 ". "AND u.uid=pat.assigned_to_id";
			 
			$result3 = $icmsDB->query($sql);
			$rows = $icmsDB->getRowsNum($result3);
			if ($result3 && $rows > 0)
			{
				$to .= ',' . implode(',', util_result_column_to_array($result3));
				$assignees = implode(',', util_result_column_to_array($result3, 1));
			}
			 
			$body = sprintf(_XF_PM_TASKBEENUPDATED, unofficial_getDBResult($result, 0, "project_task_id"))." ". "\r\n"._XF_G_PROJECT.": ".unofficial_getDBResult($result, 0, 'group_name'). "\r\n"._XF_PM_SUBPROJECT.": ".unofficial_getDBResult($result, 0, 'project_name'). "\r\n"._XF_G_SUMMARY.": ".$ts->makeTboxData4Edit(unofficial_getDBResult($result, 0, 'summary')). "\r\n"._XF_PM_COMPLETE.": ".unofficial_getDBResult($result, 0, 'percent_complete')."%". "\r\n"._XF_TRK_ATHSTATUS.": ".unofficial_getDBResult($result, 0, 'status_name'). "\r\n"._XF_PM_AUTHORITY."  : ".unofficial_getDBResult($result, 0, 'creator_name'). "\r\n"._XF_G_ASSIGNEDTO.": ".$assignees. "\r\n"._XF_G_DESCRIPTION.": ".$ts->makeTboxData4Edit(unofficial_getDBResult($result, 0, 'details'));
			 
			/*
			Now get the followups to this task
			*/
			$sql = "SELECT ph.field_name,ph.old_value,ph.date,u.uname ". "FROM ".$icmsDB->prefix("xf_project_history")." ph,".$icmsDB->prefix("users")." u ". "WHERE ph.mod_by=u.uid ". "AND ph.field_name = 'details' ". "AND project_task_id='$project_task_id' ". "ORDER BY ph.date DESC";
			 
			$result2 = $icmsDB->query($sql);
			$rows = $icmsDB->getRowsNum($result2);
			 
			if ($result2 && $rows > 0)
			{
				$body .= "\r\n"._XF_G_FOLLOWUPS.":";
				for($i = 0; $i < $rows; $i++)
				{
					$body .= "\r\n-------------------------------------------------------";
					$body .= "\r\n"._XF_G_DATE.": ".date($sys_datefmt, unofficial_getDBResult($result2, $i, 'date'));
					$body .= "\r\n"._XF_G_BY.": ".unofficial_getDBResult($result2, $i, 'uname');
					$body .= "\r\n"._XF_G_COMMENT.":\n".$ts->makeTareaData4Edit(unofficial_getDBResult($result2, $i, 'old_value'));
				}
			}
			$body .= "\r\n-------------------------------------------------------". "\r\n"._XF_PM_FORMOREINFO. "\r\n".ICMS_URL."/modules/xfmod/pm/task.php?func=detailtask&project_task_id=". unofficial_getDBResult($result, 0, 'project_task_id')."&group_id=". unofficial_getDBResult($result, 0, 'group_id')."&group_project_id=".unofficial_getDBResult($result, 0, 'group_project_id');
			 
			$subject = "[ ".unofficial_getDBResult($result, 0, 'unix_group_name')."-"._XF_PM_TASK." #".unofficial_getDBResult($result, 0, 'project_task_id').'] '. $ts->makeTareaData4Edit(unofficial_getDBResult($result, 0, 'summary'));
			 
			 
			// Append the list of additional receiptients
			if ($more_addresses)
			{
				$to .= ',' . $more_addresses;
			}
			 
			// If this is a new task, or if send all tasks == 1,
			// append the new_task_address for the group
			if (($new_task && unofficial_getDBResult($result, 0, 'new_task_address')) || unofficial_getDBResult($result, 0, 'send_all_tasks'))
			{
				$to .= ',' . unofficial_getDBResult($result, 0, 'new_task_address');
			}
			xoopsForgeMail($icmsForge['noreply'], $icmsConfig['sitename'], $subject, $body, array_unique(explode(",", $to)));
			$feedback .= " "._XF_PM_TASKUPDATESENT." ";
			 
		}
		else
		{
			$feedback .= " "._XF_PM_TASKUPDATENOTSENT." ";
			//echo $icmsDB->error();
		}
	}
	 
?>