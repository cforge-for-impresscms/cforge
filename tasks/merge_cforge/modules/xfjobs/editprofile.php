<?php
	/**
	*
	* SourceForge Jobs (aka Help Wanted) Board
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: editprofile.php,v 1.7 2004/04/16 18:07:25 jcox Exp $
	*
	*/
	 
	include_once ("../../mainfile.php");
	 
	$langfile = "people.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once (ICMS_ROOT_PATH."/modules/xfaccount/account_util.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfjobs/people_utils.php");
	 
	$icmsOption['template_main'] = 'xfjobs_editprofile.html';
	 
	if (!$icmsUser)
		{
		redirect_header(ICMS_URL."/user.php", 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
	}
	 
	if ($update_profile)
	{
		/*
		update the job's description, status, etc
		*/
		if (!$resume)
		{
			//required info
			$content .= _XF_PEO_FILLINALLFIELDS;
			exit;
		}
		 
		$sql = "UPDATE ".$icmsDB->prefix("xf_user_profile")." SET people_view_skills='$people_view_skills',resume='".$ts->makeTareaData4Save($resume)."' " ."WHERE user_id='".$icmsUser->getVar("uid")."'";
		 
		$result = $icmsDB->queryF($sql);
		if (!$result)
		{
			$xoopsForgeErrorHandler->addError('User update FAILED - '. $icmsDB->error());
		}
		else
		{
			$xoopsForgeErrorHandler->addMessage(_XF_PEO_USERUPDATED);
		}
		 
	}
	else if ($add_to_skill_inventory)
	{
		/*
		add item to job inventory
		*/
		if ($skill_id == 100 || $skill_level_id == 100 || $skill_year_id == 100)
		{
			//required info
			$content .= _XF_PEO_FILLINALLFIELDS;
			exit;
		}
		people_add_to_skill_inventory($skill_id, $skill_level_id, $skill_year_id);
		 
	}
	else if ($update_skill_inventory)
	{
		/*
		Change Skill level, experience etc.
		*/
		if ($skill_level_id == 100 || $skill_year_id == 100 || !$skill_inventory_id)
		{
			//required info
			$content .= _XF_PEO_FILLINALLFIELDS;
			exit;
		}
		 
		$sql = "UPDATE ".$icmsDB->prefix("xf_people_skill_inventory")." SET " ."skill_level_id='$skill_level_id'," ."skill_year_id='$skill_year_id' " ."WHERE user_id='".$icmsUser->getVar("uid")."' " ."AND skill_inventory_id='$skill_inventory_id'";
		 
		$result = $icmsDB->queryF($sql);
		 
		if (!$result)
		{
			$xoopsForgeErrorHandler->addError('User Skill update FAILED - '. $icmsDB->error());
		}
		else
		{
			$xoopsForgeErrorHandler->addMessage(_XF_PEO_USERSKILLUPDATED);
		}
		 
	}
	else if ($delete_from_skill_inventory)
	{
		/*
		remove this skill from this job
		*/
		if (!$skill_inventory_id)
		{
			//required info
			$content .= _XF_PEO_FILLINALLFIELDS;
			exit;
		}
		 
		$sql = "DELETE FROM ".$icmsDB->prefix("xf_people_skill_inventory")." " ."WHERE user_id='".$icmsUser->getVar("uid")."' " ."AND skill_inventory_id='$skill_inventory_id'";
		 
		$result = $icmsDB->queryF($sql);
		if (!$result)
		{
			$xoopsForgeErrorHandler->addError('User Skill Delete FAILED '. $icmsDB->error());
		}
		else
		{
			$xoopsForgeErrorHandler->addMessage(_XF_PEO_USERSKILLDELETED);
		}
	}
	/*
	Fill in the info to create a job
	*/
	 
	$metaTitle = ": "._XF_MY_SKILLPROFILE;
	 
	include ("../../header.php");
	$icmsTpl->assign("account_header", account_header(_XF_MY_SKILLPROFILE));
	 
	//$xoopsForgeErrorHandler->displayFeedback();
	 
	//for security, include group_id
	$sql = "SELECT * FROM ".$icmsDB->prefix("xf_user_profile")." WHERE user_id='".$icmsUser->getVar("uid")."'";
	$result = $icmsDB->queryF($sql);
	if ($icmsDB->getRowsNum($result) < 1)
	{
		$icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_user_profile")." (user_id) VALUES (".$icmsUser->getVar("uid").")");
		 
		$people_view_skills = 1;
		$resume = "";
	}
	else
	{
		$people_view_skills = unofficial_getDBResult($result, 0, 'people_view_skills');
		$resume = unofficial_getDBResult($result, 0, 'resume');
	}
	$content .= '
		<p>'._XF_PEO_EDITYOURPROFILEINFO.'
		<p>
		<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
		<p>'._XF_PEO_EDITYOURPROFILEINFO1.'
		<p>
		<strong>'._XF_PEO_PUBLICLYVIEWABLE.':</strong><BR>
		<input type="radio" name="people_view_skills" value="0" '. (($people_view_skills == 0)?'CHECKED':'') .'> <strong>'._NO.'</strong><BR>
		<input type="radio" name="people_view_skills" value="1" '. (($people_view_skills == 1)?'CHECKED':'') .'> <strong>'._YES.'</strong><BR>
		<p>'._XF_PEO_GIVEUSSOMEINFO.'
		<p>
		<strong>'._XF_PEO_RESUME.':</strong><BR>
		<textarea name="resume" rows="15" cols="60" WRAP="SOFT">'. $ts->makeTareaData4Edit($resume) .'</textarea>
		<p>
		<input type="submit" name="update_profile" value="'._XF_PEO_UPDATEPROFILE.'">
		</form><p>';
	 
	$content .= people_edit_skill_inventory($icmsUser->uid());
	 
	$content .= '<p><form action="'.ICMS_URL.'/modules/xfaccount/" method="POST">
		<input type="submit" name="submit" value="'._XF_PEO_FINISHED.'"></form>';
	 
	$icmsTpl->assign("title", $title);
	$icmsTpl->assign("content", $content);
	 
	//CloseTable();
	//include (ICMS_ROOT_PATH."/footer.php");
	include("../../footer.php");
?>