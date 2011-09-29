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
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfjobs/people_utils.php");
$xoopsOption['template_main'] = 'xfjobs_editjob.html';

if ($group_id && $group_id != '')
{
	$group =& group_get_object($group_id);
	$perm  =& $group->getPermission( $xoopsUser );

	if (!$perm->isAdmin()){
    	redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
		exit();
	}

	if ( $group->isFoundry() ){
		define("_LOCAL_XF_PEO_EDITJOBFORPROJECT",_XF_PEO_EDITJOBFORCOMM);
    }else{
    	define("_LOCAL_XF_PEO_EDITJOBFORPROJECT",_XF_PEO_EDITJOBFORPROJECT);
    }

	if ($add_job)
	{
		/*
		  create a new job
		*/
		$valid = true;
		if(strlen($title) < 1)
		{
			$xoopsForgeErrorHandler->addError("Title is a required field");
			$valid = false;
		}

		if(strlen($description) < 1)
		{
			$xoopsForgeErrorHandler->addError("Description is a required field");
			$valid = false;
		}

		if($category_id == 100)
		{
			$xoopsForgeErrorHandler->addError("Category must be specified");
			$valid = false;
		}

		if(!$valid)
		{
			$title = urlencode($title);
			$description = urlencode($description);
			$loc = XOOPS_URL."/modules/xfjobs/createjob.php".
				"?group_id=$group_id&job_id=$job_id&title=$title".
				"&category_id=$category_id&description=$description".
				"&feedback=$feedback&fromedit=1";
			redirect_header($loc, 0, "");
		}

		$sql = "INSERT INTO ".$xoopsDB->prefix("xf_people_job").
			" (group_id,created_by,title,description,date,".
			"status_id,category_id) ".
			"VALUES ('$group_id','".$xoopsUser->getVar("uid").
			"','".$ts->makeTboxData4Save($title)."','".
			$ts->makeTareaData4Save($description)."','".
			time()."','1','$category_id')";

		$result = $xoopsDB->queryF($sql);

		if (!$result)
		{
			$xoopsForgeErrorHandler->addError('Job insert FAILED: ' . $xoopsDB->error());
		}
		else
		{
			$job_id = $xoopsDB->getInsertId();
			$xoopsForgeErrorHandler->addMessage(_XF_PEO_JOBINSERTED);
		}
	}
	else if	($update_job)
	{
		/*
		  update the job's description, status, etc
		*/
		if (!$title
			|| !$description
			|| $category_id==100
			|| $status_id==100
			|| !$job_id)
		{
			//required info
			$feedback = _XF_PEO_FILLINALLFIELDS;
			exit;
		}

		$sql = "UPDATE ".$xoopsDB->prefix("xf_people_job")." SET "
			  ."title='".$ts->makeTboxData4Save($title)."',"
			  ."description='".$ts->makeTareaData4Save($description)."',"
			  ."status_id='$status_id',"
			  ."category_id='$category_id' "
			  ."WHERE job_id='$job_id' "
			  ."AND group_id='$group_id'";

		$result = $xoopsDB->queryF($sql);

		if (!$result)
		{
			$xoopsForgeErrorHandler->addError('Job update FAILED: ' . $xoopsDB->error());
    	        }
		else
		{
			$xoopsForgeErrorHandler->addMessage(_XF_PEO_JOBUPDATED);
		}

	}
	else if	($add_to_job_inventory)
	{
    	/*
      		add item to job inventory
    	*/
		$valid = true;
		if($skill_id == 'xyxy')
		{
			$xoopsForgeErrorHandler->addError("Skill must be specified");
			$valid = false;
		}
		if($skill_level_id == 'xyxy')
		{
			$xoopsForgeErrorHandler->addError("Skill Level must be specified");
			$valid = false;
		}
		if($skill_year_id == 'xyxy')
		{
			$xoopsForgeErrorHandler->addError("Experience must be specified");
			$valid = false;
		}
		if(!$job_id == 'xyxy')
		{
			$xoopsForgeErrorHandler->addError("Invalid Job Identifier");
			$valid = false;
		}
		if($valid)
		{
			if (people_verify_job_group($job_id,$group_id))
			{
				people_add_to_job_inventory($job_id,$skill_id,
					$skill_level_id,$skill_year_id);
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
    	if ($skill_level_id == 100
			|| $skill_year_id == 100
			|| !$job_id
			|| !$job_inventory_id)
		{
			//required info
			$feedback .= _XF_PEO_FILLINALLFIELDS;
			exit;
		}

		if (people_verify_job_group($job_id,$group_id))
		{
			$sql = "UPDATE ".$xoopsDB->prefix("xf_people_job_inventory").
				" SET "."skill_level_id='$skill_level_id',"
            	."skill_year_id='$skill_year_id' "
            	."WHERE job_id='$job_id' "
            	."AND job_inventory_id='$job_inventory_id'";

			$result = $xoopsDB->queryF($sql);

			if (!$result)
			{
				$xoopsForgeErrorHandler->addError('JOB skill update FAILED - ' . $xoopsDB->error());

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

		if (people_verify_job_group($job_id,$group_id))
		{
			$sql = "DELETE FROM ".$xoopsDB->prefix("xf_people_job_inventory")
				." WHERE job_id='$job_id' "
				."AND job_inventory_id='$job_inventory_id'";

			$result = $xoopsDB->queryF($sql);

			if (!$result)
			{
				$xoopsForgeErrorHandler->addError('JOB skill delete FAILED - ' . $xoopsDB->error());
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

	//$xoopsTpl->assign('header', people_header($group_id, $job_id));

	include (XOOPS_ROOT_PATH."/header.php");
	$content = people_header($group_id, $job_id);
	$content .= get_project_admin_header($group_id, $perm, $group->isProject());
	$content .= "<H4>"._LOCAL_XF_PEO_EDITJOBFORPROJECT."</H4>";

	//for security, include group_id
	$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_people_job").
		" WHERE job_id='$job_id' AND group_id='$group_id'";
	$result = $xoopsDB->query($sql);

		if (!$result)
		{
			$content .= $xoopsDB->error();
			$xoopsForgeErrorHandler->addError('POSTING fetch FAILED - ' . _XF_PEO_NOSUCHPOSTING);
		}
		else
		{
			// TODO: Add People Job calculations to the
			//  update.php cronjob (All postings are automatically
			// closed after two weeks. )
			$content .= '<P>'._XF_PEO_EDITJOBSKILLINFO;
			$content .= '<P>'._XF_PEO_POSTINGSCLOSEAUTO;
			$content .= '<P><FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">';
			$content .= '<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">';
			$content .= '<INPUT TYPE="HIDDEN" NAME="job_id" VALUE="'.$job_id.'">';
			$content .= '<INPUT TYPE="HIDDEN" NAME="feedback" VALUE="'.$feedback.'">';
			$content .= '<B>'._XF_PEO_CATEGORY.':</B><BR>';
			$content .= people_job_category_box('category_id',
				unofficial_getDBResult($result,0,'category_id'));
			$content .= '<P><B>'._XF_PEO_STATUS.':</B><BR>';
			$content .= people_job_status_box('status_id',
				unofficial_getDBResult($result,0,'status_id'));
			$content .= '<P><B>'._XF_PEO_SHORTDESCRIPTION.':</B><BR>';
			$content .= '<INPUT TYPE="TEXT" NAME="title" VALUE="'.
				$ts->makeTboxData4Show(unofficial_getDBResult($result,0,
					'title')) .'" SIZE="40" MAXLENGTH="60">';
			$content .= '<P><B>'._XF_PEO_LONGDESCRIPTION.':</B><BR>';
			$content .= '<TEXTAREA NAME="description" ROWS="10" COLS="60" WRAP="SOFT">'.
				$ts->makeTareaData4Show(unofficial_getDBResult($result,0,
					'description')) .'</TEXTAREA>';
			$content .= '<P><INPUT TYPE="SUBMIT" NAME="update_job" VALUE="'.
				_XF_PEO_UPDATEDESCRIPTIONS.'">';
			$content .= '</FORM>';

			//now show the list of desired skills
			$content .= '<P>'.people_edit_job_inventory($job_id,$group_id);
			$content .= '<P><FORM ACTION="'.XOOPS_URL.
				'/modules/xfjobs/" METHOD="POST">
				<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'
				._XF_PEO_FINISHED.'">';
			if ( isset($group_id) )
			{
				$content .= '<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">';
			}
			$content .= '</FORM>';
		}

		$xoopsTpl->assign("content",$content);

		include (XOOPS_ROOT_PATH."/footer.php");

	}
	else
	{
		redirect_header($GLOBALS["HTTP_REFERER"],4,"ERROR<br />No Group!");
		exit;
	}
?>
