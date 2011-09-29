<?php
	/**
	*
	* SourceForge Jobs (aka Help Wanted) Board
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: editjob.php,v 1.7 2003/12/10 20:01:32 jcox Exp $
	*
	*/
	include_once ("../../mainfile.php");
	require_once("language/english/people.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfjobs/people_utils.php");
	$icmsOption['template_main'] = 'xfjobs_editjob.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) $ {
		$k }
	 = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) $ {
		$k }
	 = StopXSS($v);
	 
	if ($group_id && $group_id != '')
		{
		$group = group_get_object($group_id);
		$perm = $group->getPermission($icmsUser );
		 
		if (!$perm->isAdmin())
			{
			redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
			exit();
		}
		 
		if ($group->isFoundry() )
			{
			define("_LOCAL_XF_PEO_EDITJOBFORPROJECT", _XF_PEO_EDITJOBFORCOMM);
		}
		else
			{
			define("_LOCAL_XF_PEO_EDITJOBFORPROJECT", _XF_PEO_EDITJOBFORPROJECT);
		}
		 
		if ($add_job)
			{
			/*
			create a new job
			*/
			$valid = true;
			if (strlen($title) < 1)
			{
				$xoopsForgeErrorHandler->addError("Title is a required field");
				$valid = false;
			}
			 
			if (strlen($description) < 1)
			{
				$xoopsForgeErrorHandler->addError("Description is a required field");
				$valid = false;
			}
			 
			if ($category_id == 100)
			{
				$xoopsForgeErrorHandler->addError("Category must be specified");
				$valid = false;
			}
			 
			if (!$valid)
			{
				$title = urlencode($title);
				$description = urlencode($description);
				$loc = ICMS_URL."/modules/xfjobs/createjob.php". "?group_id=$group_id&job_id=$job_id&title=$title". "&category_id=$category_id&description=$description". "&feedback=$feedback&fromedit=1";
				redirect_header($loc, 0, "");
			}
			 
			$sql = "INSERT INTO ".$icmsDB->prefix("xf_people_job"). " (group_id,created_by,title,description,date,". "status_id,category_id) ". "VALUES ('$group_id','".$icmsUser->getVar("uid"). "','".$ts->makeTboxData4Save($title)."','". $ts->makeTareaData4Save(mysql_real_escape_string($description))."','". time()."','1','$category_id')";
			 
			$result = $icmsDB->queryF($sql);
			 
			if (!$result)
				{
				$xoopsForgeErrorHandler->addError('Job insert FAILED: ' . $icmsDB->error() . ' the SQL was '.$sql);
			}
			else
				{
				$job_id = $icmsDB->getInsertId();
				$xoopsForgeErrorHandler->addMessage(_XF_PEO_JOBINSERTED);
			}
		}
		else if ($update_job)
		{
			/*
			update the job's description, status, etc
			*/
			if (!$title || !$description || $category_id == 100 || $status_id == 100 || !$job_id)
			{
				//required info
				$feedback = _XF_PEO_FILLINALLFIELDS;
				exit;
			}
			 
			$sql = "UPDATE ".$icmsDB->prefix("xf_people_job")." SET " ."title='".$ts->makeTboxData4Save($title)."'," ."description='".$ts->makeTareaData4Save($description)."'," ."status_id='$status_id'," ."category_id='$category_id' " ."WHERE job_id='$job_id' " ."AND group_id='$group_id'";
			 
			$result = $icmsDB->queryF($sql);
			 
			if (!$result)
				{
				$xoopsForgeErrorHandler->addError('Job update FAILED: ' . $icmsDB->error());
			}
			else
				{
				$xoopsForgeErrorHandler->addMessage(_XF_PEO_JOBUPDATED);
			}
			 
		}
		else if ($add_to_job_inventory)
		{
			/*
			add item to job inventory
			*/
			$valid = true;
			if ($skill_id == 'xyxy')
			{
				$xoopsForgeErrorHandler->addError("Skill must be specified");
				$valid = false;
			}
			if ($skill_level_id == 'xyxy')
			{
				$xoopsForgeErrorHandler->addError("Skill Level must be specified");
				$valid = false;
			}
			if ($skill_year_id == 'xyxy')
			{
				$xoopsForgeErrorHandler->addError("Experience must be specified");
				$valid = false;
			}
			if (!$job_id == 'xyxy')
			{
				$xoopsForgeErrorHandler->addError("Invalid Job Identifier");
				$valid = false;
			}
			if ($valid)
			{
				if (people_verify_job_group($job_id, $group_id))
					{
					people_add_to_job_inventory($job_id, $skill_id,
						$skill_level_id, $skill_year_id);
					$xoopsForgeErrorHandler->addMessage(_XF_PEO_JOBUPDATED);
				}
				else
					{
					$xoopsForgeErrorHandler->addError("Job update failed - wrong Project identifier");
				}
			}
		}
		else if ($update_job_inventory)
		{
			/*
			Change Skill level, experience etc.
			*/
			if ($skill_level_id == 100 || $skill_year_id == 100 || !$job_id || !$job_inventory_id)
			{
				//required info
				$feedback .= _XF_PEO_FILLINALLFIELDS;
				exit;
			}
			 
			if (people_verify_job_group($job_id, $group_id))
				{
				$sql = "UPDATE ".$icmsDB->prefix("xf_people_job_inventory"). " SET "."skill_level_id='$skill_level_id'," ."skill_year_id='$skill_year_id' " ."WHERE job_id='$job_id' " ."AND job_inventory_id='$job_inventory_id'";
				 
				$result = $icmsDB->queryF($sql);
				 
				if (!$result)
					{
					$xoopsForgeErrorHandler->addError('JOB skill update FAILED - ' . $icmsDB->error());
					 
				}
				else
					{
					$xoopsForgeErrorHandler->addMessage(_XF_PEO_JOBSKILLUPDATED);
				}
			}
			else
				{
				$xoopsForgeErrorHandler->addError('JOB skill update failed - wrong project id');
			}
		}
		else if ($delete_from_job_inventory)
		{
			/*
			remove this skill from this job
			*/
			if (!$job_id)
				{
				//required info
				$feedback .= _XF_PEO_FILLINALLFIELDS;
				exit;
			}
			 
			if (people_verify_job_group($job_id, $group_id))
				{
				$sql = "DELETE FROM ".$icmsDB->prefix("xf_people_job_inventory")
				." WHERE job_id='$job_id' " ."AND job_inventory_id='$job_inventory_id'";
				 
				$result = $icmsDB->queryF($sql);
				 
				if (!$result)
					{
					$xoopsForgeErrorHandler->addError('JOB skill delete FAILED - ' . $icmsDB->error());
				}
				else
					{
					$xoopsForgeErrorHandler->addMessage(_XF_PEO_JOBSKILLDELETED);
				}
			}
			else
				{
				$xoopsForgeErrorHandler->addError('JOB skill delete failed - wrong project id');
			}
		}
		 
		/*
		Fill in the info to create a job
		*/
		 
		//$icmsTpl->assign('header', people_header($group_id, $job_id));
		 
		include (ICMS_ROOT_PATH."/header.php");
		$content = people_header($group_id, $job_id);
		$content .= get_project_admin_header($group_id, $perm, $group->isProject());
		$content .= "<H4>"._LOCAL_XF_PEO_EDITJOBFORPROJECT."</H4>";
		 
		//for security, include group_id
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_people_job"). " WHERE job_id='$job_id' AND group_id='$group_id'";
		$result = $icmsDB->query($sql);
		 
		if (!$result)
			{
			$content .= $icmsDB->error();
			$xoopsForgeErrorHandler->addError('POSTING fetch FAILED - ' . _XF_PEO_NOSUCHPOSTING);
		}
		else
			{
			// TODO: Add People Job calculations to the
			//  update.php cronjob (All postings are automatically
			// closed after two weeks. )
			$content .= '<p>'._XF_PEO_EDITJOBSKILLINFO;
			$content .= '<p>'._XF_PEO_POSTINGSCLOSEAUTO;
			$content .= '<p><form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
			$content .= '<input type="hidden" name="group_id" value="'.$group_id.'">';
			$content .= '<input type="hidden" name="job_id" value="'.$job_id.'">';
			$content .= '<input type="hidden" name="feedback" value="'.$feedback.'">';
			$content .= '<strong>'._XF_PEO_CATEGORY.':</strong><BR>';
			$content .= people_job_category_box('category_id',
				unofficial_getDBResult($result, 0, 'category_id'));
			$content .= '<p><strong>'._XF_PEO_STATUS.':</strong><BR>';
			$content .= people_job_status_box('status_id',
				unofficial_getDBResult($result, 0, 'status_id'));
			$content .= '<p><strong>'._XF_PEO_SHORTDESCRIPTION.':</strong><BR>';
			$content .= '<input type="text" name="title" value="'. $ts->makeTboxData4Show(unofficial_getDBResult($result, 0,
				'title')) .'" size="40" maxlength="60">';
			$content .= '<p><strong>'._XF_PEO_LONGDESCRIPTION.':</strong><BR>';
			$content .= '<textarea name="description" rows="10" cols="60" WRAP="SOFT">'. $ts->makeTareaData4Show(unofficial_getDBResult($result, 0,
				'description')) .'</textarea>';
			$content .= '<p><input type="submit" name="update_job" value="'. _XF_PEO_UPDATEDESCRIPTIONS.'">';
			$content .= '</form>';
			 
			//now show the list of desired skills
			$content .= '<p>'.people_edit_job_inventory($job_id, $group_id);
			$content .= '<p><form action="'.ICMS_URL. '/modules/xfjobs/" method="POST">
				<input type="submit" name="submit" value="' ._XF_PEO_FINISHED.'">';
			if (isset($group_id) )
				{
				$content .= '<input type="hidden" name="group_id" value="'.$group_id.'">';
			}
			$content .= '</form>';
		}
		 
		$icmsTpl->assign("content", $content);
		 
		include (ICMS_ROOT_PATH."/footer.php");
		 
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "ERROR<br />No Group!");
		exit;
	}
?>