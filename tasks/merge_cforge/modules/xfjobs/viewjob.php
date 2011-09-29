<?php
	/**
	*
	* SourceForge Jobs (aka Help Wanted) Board
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: viewjob.php,v 1.9 2004/01/15 22:33:19 devsupaul Exp $
	*
	*/
	include_once ("../../mainfile.php");
	 
	require_once("language/english/people.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfjobs/people_utils.php");
	$icmsOption['template_main'] = 'xfjobs_viewjob.html';
	 
	include (ICMS_ROOT_PATH."/header.php");
	 
	if ($group_id && $job_id)
		{
		// Fill in the info to create a job
		 
		//for security, include group_id
		$sql = "SELECT g.group_name,g.unix_group_name,pjc.name AS category_name,". "pjs.name AS status_name,". "pj.title,pj.description,pj.date,u.uname,u.uid ". "FROM ".$icmsDB->prefix("xf_people_job")." pj,". $icmsDB->prefix("xf_groups")." g,". $icmsDB->prefix("xf_people_job_status"). " pjs,".$icmsDB->prefix("xf_people_job_category"). " pjc,".$icmsDB->prefix("users")." u ". "WHERE pjc.category_id=pj.category_id ". "AND pjs.status_id=pj.status_id ". "AND u.uid=pj.created_by ". "AND g.group_id=pj.group_id ". "AND pj.job_id='$job_id' ". "AND pj.group_id='$group_id'";
		 
		$result = $icmsDB->query($sql);
		 
		if (!$result || $icmsDB->getRowsNum($result) < 1)
			{
			$xoopsForgeErrorHandler->addError('POSTING fetch FAILED');
			$content = people_header($group_id, $job_id);
			$content .= "<h4>"._XF_PEO_VIEWAJOB."</h4>";
			$content .= $icmsDB->error();
			$content .= '<h4>'._XF_PEO_NOSUCHPOSTING.'</h4>';
		}
		else
			{
			$content = people_header($group_id, $job_id, $deljob);
			 
			if ($deljob)
			{
				$content .= '<h4><strong><a href="'.ICMS_URL. '/modules/xfjobs/?group_id='. $group_id.'&del_job_id='.$job_id. '">Click here to delete this job</a></strong></h4>';
			}
			else
				{
				$content .= "<h4>"._XF_PEO_VIEWAJOB."</h4>";
			}
			 
			$content .= '
				<p><table border="0" width="100%"><tr><td colspan="2"><strong>'. $ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'title')).'</strong>
				</td></tr><tr><td width="50%"><strong>'._XF_PEO_CONTACTINFO.':<br>
				<a href="javascript:openWithSelfMain(\''.ICMS_URL. '/pmlite.php?send2=1&to_userid='. unofficial_getDBResult($result, 0, 'uid').'\',\'pmlite\',450,450);">'. unofficial_getDBResult($result, 0, 'uname').'</a></strong></td><td width="50%"><strong>'. _XF_PEO_STATUS.':</strong><br>'. unofficial_getDBResult($result, 0, 'status_name'). '</td></tr><tr><td width="50%"><strong>'._XF_PEO_OPENDATE.':</strong><br>'. date($sys_datefmt, unofficial_getDBResult($result, 0, 'date')). '</td><td width="50%"><strong>'. _XF_PEO_FORPROJECT.':<br>
				<a href="'.ICMS_URL.'/modules/xfmod/project/?'. unofficial_getDBResult($result, 0, 'unix_group_name').'">'. unofficial_getDBResult($result, 0, 'group_name'). '</a></strong></td></tr><tr><td colspan="2"><strong>'. _XF_PEO_LONGDESCRIPTION.':</strong><p>'. $ts->makeTareaData4Show(unofficial_getDBResult($result, 0,
				'description')).'</td></tr><tr><td colspan="2">
				<h4>'._XF_PEO_REQUIREDSKILLS.':</h4>';
			 
			//now show the list of desired skills
			$content .= '<p>'.people_show_job_inventory($job_id).'</td></tr>';
			 
			//now show the user how to apply for this job
			global $icmsUser;
			if ($icmsUser )
				{
				$content .= "<tr height=\"10\"><td colspan=\"2\">&nbsp;</td></tr>";
				if (! $apply )
					{
					$content .= "<tr><td colspan=\"2\"><h4>"._XF_PEO_APPLYFORJOB."</h4></td></tr>";
					$content .= "<tr><td colspan=\"2\">"._XF_PEO_IFYOUWANTTOAPPLY." \""._XF_PEO_APPLY."\" "._XF_PEO_BUTTONBELOW.".<br>" . _XF_PEO_APPLYDISCLAIMER."  " . $icmsConfig['sitename']." "._XF_PEO_NOTRESPONSIBLE.".<br>" . "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">" . "<input type=\"hidden\" name=\"group_id\" value=\"".$group_id."\">" . "<input type=\"hidden\" name=\"job_id\" value=\"".$job_id."\">" . "<div align=\"center\"><input type=\"submit\" name=\"apply\" value=\""._XF_PEO_APPLY."\"></div></form></td></tr>\n";
				}
				else
					{
					$skill_sql = "SELECT s.name as skill_name," . " sl.name as skill_level_name," . " sy.name as skill_year_name" . " FROM ".$icmsDB->prefix("xf_people_skill")." s, " . $icmsDB->prefix("xf_people_skill_level")." sl, " . $icmsDB->prefix("xf_people_skill_year")." sy, " . $icmsDB->prefix("xf_people_skill_inventory")." si" . " WHERE si.user_id='".$icmsUser->uid()."'" . " AND s.skill_id=si.skill_id" . " AND sl.skill_level_id=si.skill_level_id" . " AND sy.skill_year_id=si.skill_year_id" . " ORDER BY sl.skill_level_id DESC, sy.skill_year_id DESC";
					$skill_result = $icmsDB->query($skill_sql);
					$subject = $icmsConfig['sitename']." "._XF_PEO_JOBAPP;
					$mail = $subject."\r\n" . _XF_PEO_THISUSERAPPLIED.".\n\n" . _XF_PEO_USERNAME.":  ".$icmsUser->uname()."\r\n" . _XF_PEO_JOB.":  ".unofficial_getDBResult($result, 0, 'title')."\r\n" . _XF_PEO_PROJECT.":  ".unofficial_getDBResult($result, 0, 'group_name')."\r\n";
					if (0 < $icmsDB->getRowsNum($skill_result) )
						{
						$mail .= _XF_PEO_SKILLS.":\n";
						while ($skill_row = $icmsDB->fetchArray($skill_result) )
						{
							$mail .= "\t".$skill_row['skill_name']." - ".$skill_row['skill_level_name']." (".$skill_row['skill_year_name'].")\n";
						}
					}
					$mail .= "\r\n"._XF_PEO_ADDVIAPRJPAGE." (" . ICMS_URL."/modules/xfmod/project/?".unofficial_getDBResult($result, 0, 'unix_group_name').")." . "\r\n"._XF_PEO_THANKSFORUSING." ".$icmsConfig['sitename'].".\n";
					$adm_sql = "SELECT u.uid " . " FROM ".$icmsDB->prefix("users")." u, " . $icmsDB->prefix("xf_user_group")." ug" . " WHERE ug.group_id='".$group_id."'" . " AND ug.admin_flags='A'" . " AND u.uid=ug.user_id";
					$adm_result = $icmsDB->query($adm_sql);
					$xoopsMailer = getMailer();
					while ($adm_row = $icmsDB->fetchArray($adm_result))
					{
						$xoopsMailer->setToUsers(new XoopsUser($adm_row['uid']));
					}
					$xoopsMailer->setFromName("Novell Forge - Noreply");
					$xoopsMailer->setFromEmail($xoopsForge['noreply']);
					$xoopsMailer->setSubject($subject);
					$xoopsMailer->setBody($mail);
					$xoopsMailer->useMail();
					$xoopsMailer->send();
					 
					$content .= "<tr><td colspan=\"2\">" ."<strong>"._XF_PEO_APPSUBMITTED."</strong></td></tr>";
				}
			}
			$content .= "</table>";
		}
		 
		$icmsTpl->assign("content", $content);
		 
		include (ICMS_ROOT_PATH."/footer.php");
	}
	else
	{
		if (!$group_id)
			{
			redirect_header($_SERVER["HTTP_REFERER"], 2, "ERROR<br />No Group!");
			exit;
		}
		else
			{
			redirect_header($_SERVER["HTTP_REFERER"], 2,
				"ERROR<br />Posting ID not found");
			exit;
		}
	}
	 
?>