<?php
/**
  *
  * SourceForge Project/Task Manager (PM)
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: task.php,v 1.5 2004/02/06 01:42:06 jcox Exp $
  *
  */
include_once ("../../../mainfile.php");

require_once(XOOPS_ROOT_PATH."/class/xoopsuser.php");

$langfile="pm.php";
include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pm/pm_data.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/pm/pm_utils.php");

$group_id = util_http_track_vars('group_id');
$group_project_id = util_http_track_vars('group_project_id');

/* post add task */
/*
$group_project_id = util_http_track_vars('group_project_id');
$start_month = util_http_track_vars('start_month,');
$start_day = util_http_track_vars('start_day');
$start_year = util_http_track_vars('start_year');
$end_month = util_http_track_vars('end_month');
$end_day = util_http_track_vars('end_day');
$end_year = util_http_track_vars('end_year');
$summary = util_http_track_vars('summary');
$details = util_http_track_vars('details');
$percent_complete = util_http_track_vars('percent_complete');
$priority = util_http_track_vars('priority');
$hours = util_http_track_vars('hours');
$assigned_to = util_http_track_vars('assigned_to');
$dependent_on = util_http_track_vars('dependent_on');
*/
/* update task */
//$group_project_id
//$project_task_id  = util_http_track_vars('project_task_id ');
//$start_month,$start_day,$start_year,
//$end_month,$end_day,$end_year,
//$summary,$details,$percent_complete,$priority,$hours,
//$status_id = util_http_track_vars('status_id');
//$assigned_to,$dependent_on,
//$new_group_project_id = util_http_track_vars('new_group_project_id');
//$group_id

if ($group_id && $group_project_id)
{
	include (XOOPS_ROOT_PATH."/header.php");

	$project = &group_get_object($group_id);
	$perm =& $project->getPermission( $xoopsUser );

	//group is private
	if (!$project->isPublic()) {
	  //if it's a private group, you must be a member of that group
	  if (!$project->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
		{
		  redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
		  exit;
		}
	}
    // Verify that this group_project_id falls under this group
	// can this person view these tasks? they may have hacked past
	// the /pm/index.php page
	if ($xoopsUser && ($perm->isMember() || $perm->isSuperUser()))
	{
		$public_flag='0,1';
	}
	else
	{
		$public_flag='1';
	}

	// Verify that this subproject belongs to this project
	$result = $xoopsDB->query("SELECT * FROM ".
		$xoopsDB->prefix("xf_project_group_list")." "
        ."WHERE group_project_id='$group_project_id' "
        ."AND group_id='$group_id' "
        ."AND is_public IN ($public_flag)");

	if ($xoopsDB->getRowsNum($result) < 1)
	{
    	redirect_header($GLOBALS["HTTP_REFERER"],4,
			_XF_G_PERMISSIONDENIED."<br />"._XF_PM_SUBPROJECTNOTPROJECT);
    	exit;
	}

	$func = util_http_track_vars('func','browse');
/*	
	if (isset($_POST['func']))
		$func = $_POST['func'];
	elseif (isset($_GET['func']))
		$func = $_GET['func'];
	else
		$func = 'browse'; // default value : browse
*/		
/*
	if (!$func)
	{
		$func='browse';
	}
*/
    // Figure out which function we're dealing with here

	switch ($func)
	{
		case 'addtask' :
		{
			if ($xoopsUser && ($perm->isPMAdmin() || $perm->isSuperUser()))
			{
				include 'add_task.php';
			}
			else
			{
				redirect_header($GLOBALS["HTTP_REFERER"],4,
					_XF_G_PERMISSIONDENIED."<br />"._XF_PM_YOUNOTASKMANAGER);
				exit;
			}
		}
		break;

		case 'postaddtask' :
		{
			if ($xoopsUser && ($perm->isPMAdmin() || $perm->isSuperUser()))
			{
				if (pm_data_create_task ($group_project_id,$start_month,
						$start_day,$start_year,$end_month,$end_day,
						$end_year,$summary,$details,$percent_complete,
						$priority,$hours,$assigned_to,$dependent_on))
				{
					$feedback=""._XF_PM_TASKCREATED;

					redirect_header($_SERVER['PHP_SELF']."?group_id=$group_id".
						"&group_project_id=$group_project_id".
						"&func=browse&set=open&feedback=$feedback", 0, "");
					exit;
					//include 'browse_task.php';
				}
				else
				{
					echo 'ERROR<br />'.$feedback;
					exit;
				}
			}
			else
			{
				redirect_header($GLOBALS["HTTP_REFERER"],4,
					_XF_G_PERMISSIONDENIED."<br />"._XF_G_YOUNOTASKMANAGER);
				exit;
			}
		}
		break;

		case 'postmodtask' :
		{
			if ($xoopsUser && ($perm->isPMAdmin() || $perm->isSuperUser()))
			{
				$dependent_on = util_http_track_vars('dependent_on');
				if (pm_data_update_task($group_project_id,$project_task_id,
						$start_month,$start_day,$start_year,$end_month,
						$end_day,$end_year,$summary,$details,
						$percent_complete,$priority,$hours,$status_id,
						$assigned_to,$dependent_on,$new_group_project_id,
						$group_id))
				{
			  		$feedback=""._XF_PM_TASKUPDATED;

					include 'browse_task.php';
				}
				else
				{
			  		echo 'ERROR<br />'.$feedback;
			  		exit;
				}
			}
			else
			{
				redirect_header($GLOBALS["HTTP_REFERER"],4,
					_XF_G_PERMISSIONDENIED."<br />"._XF_PM_YOUNOTASKMANAGER);
				exit;
		  	}
		}
		break;

		case 'detailtask' :
		{
		  	if ($xoopsUser && ($perm->isPMAdmin() || $perm->isSuperUser()))
			{
				include 'mod_task.php';
		  	}
			else
			{
				include 'detail_task.php';
		  	}
		}
		break;

		case 'browse' :
		default:
		{
		  include 'browse_task.php';
		}
		break;
	  }

	  include (XOOPS_ROOT_PATH."/footer.php");
}
else
{
	redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />No Group");
  	exit;
}

?>