<?php
	/**
	*
	* SourceForge Jobs (aka Help Wanted) Board
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: viewprofile.php,v 1.4 2003/12/10 20:01:32 jcox Exp $
	*
	*/
	include_once ("../../mainfile.php");
	 
	require_once("language/english/people.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfjobs/people_utils.php");
	$icmsOption['template_main'] = 'xfjobs_viewprofile.html';
	 
	include (ICMS_ROOT_PATH."/header.php");
	 
	if ($user_id)
	{
		 
		/*
		Fill in the info to create a job
		*/
		 
		$content = "<B style='font-size:16px;align:left;'>"._XF_PEO_VIEWUSERPROFILE."</strong><br />";
		 
		//for security, include group_id
		$sql = "SELECT user_id,uname,people_view_skills,resume FROM ".$icmsDB->prefix("users").",".$icmsDB->prefix("xf_user_profile")." " ."WHERE uid='$user_id' " ."AND uid=user_id";
		 
		$result = $icmsDB->query($sql);
		if (!$result || $icmsDB->getRowsNum($result) < 1)
		{
			$content .= '<H4>'._XF_PEO_NOTENTEREDPROFILE.'</H4>';
		}
		else
		{
			 
			/*
			profile set private
			*/
			if (unofficial_getDBResult($result, 0, 'people_view_skills') != 1)
			{
				$content .= '<H4>'._XF_PEO_SETPROFILETOPRIVATE.'</H4>';
				$icmsTpl->assign("content", $content);
				include (ICMS_ROOT_PATH."/footer.php");
				exit;
			}
			 
			$content .= '
				<p>
				<table border="0" width="100%">
				<th><td>
				<strong>'._XF_PEO_USERNAME.':</strong><BR>
				'. unofficial_getDBResult($result, 0, 'uname') .'
				</td></th>
				<th><td>
				<strong>'._XF_PEO_RESUME.':</strong><BR>
				'. $ts->makeTareaData4Show(unofficial_getDBResult($result, 0, 'resume')) .'
				</td></th>
				<th><td>
				<H4>'._XF_PEO_SKILLINVENTORY.'</H4>';
			 
			//now show the list of desired skills
			$content .= '<p>'.people_show_skill_inventory($user_id);
			$content .= '</td></th></table>';
		}
		 
		$icmsTpl->assign("content", $content);
		include (ICMS_ROOT_PATH."/footer.php");
		 
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "ERROR<br />uid not found!");
		exit;
	}
	 
?>