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
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfjobs/people_utils.php");
$xoopsOption['template_main'] = 'xfjobs_viewjob.html';

include (XOOPS_ROOT_PATH."/header.php");

if ($group_id && $job_id)
{
	// Fill in the info to create a job

	//for security, include group_id
	$sql = "SELECT g.group_name,g.unix_group_name,pjc.name AS category_name,".
        "pjs.name AS status_name,".
        "pj.title,pj.description,pj.date,u.uname,u.uid ".
        "FROM ".$xoopsDB->prefix("xf_people_job")." pj,".
		$xoopsDB->prefix("xf_groups")." g,".
		$xoopsDB->prefix("xf_people_job_status").
		" pjs,".$xoopsDB->prefix("xf_people_job_category").
		" pjc,".$xoopsDB->prefix("users")." u ".
        "WHERE pjc.category_id=pj.category_id ".
        "AND pjs.status_id=pj.status_id ".
        "AND u.uid=pj.created_by ".
        "AND g.group_id=pj.group_id ".
        "AND pj.job_id='$job_id' ".
        "AND pj.group_id='$group_id'";

	$result = $xoopsDB->query($sql);

	if (!$result || $xoopsDB->getRowsNum($result) < 1)
	{
		$xoopsForgeErrorHandler->addError('POSTING fetch FAILED');
		$content = people_header($group_id, $job_id);
		$content .= "<h4>"._XF_PEO_VIEWAJOB."</h4>";
		$content .= $xoopsDB->error();
		$content .= '<h4>'._XF_PEO_NOSUCHPOSTING.'</h4>';
	}
	else
	{
		$content = people_header($group_id, $job_id, $deljob);

		if($deljob)
		{
			$content .= '<h4><b><a href="'.XOOPS_URL.
			'/modules/xfjobs/?group_id='.
			$group_id.'&del_job_id='.$job_id.
			'">Click here to delete this job</a></b></h4>';
		}
		else
		{
			$content .= "<h4>"._XF_PEO_VIEWAJOB."</h4>";
		}

		$content .= '
		<p><table border="0" width="100%"><tr><td colspan="2"><b>'.
		$ts->makeTboxData4Show(unofficial_getDBResult($result,0,'title')).'</b>
		</td></tr><tr><td width="50%"><b>'._XF_PEO_CONTACTINFO.':<br>
		<a href="javascript:openWithSelfMain(\''.XOOPS_URL.
		'/pmlite.php?send2=1&to_userid='.
		unofficial_getDBResult($result,0,'uid').'\',\'pmlite\',450,450);">'.
		unofficial_getDBResult($result,0,'uname').'</a></b></td><td width="50%"><b>'.
		_XF_PEO_STATUS.':</b><br>'.
		unofficial_getDBResult($result,0,'status_name').
		'</td></tr><tr><td width="50%"><b>'._XF_PEO_OPENDATE.':</b><br>'.
		date($sys_datefmt,unofficial_getDBResult($result,0,'date')).
		'</td><td width="50%"><b>'.
		_XF_PEO_FORPROJECT.':<br>
		<a href="'.XOOPS_URL.'/modules/xfmod/project/?'.
		unofficial_getDBResult($result,0,'unix_group_name').'">'.
		unofficial_getDBResult($result,0,'group_name').
		'</a></b></td></tr><tr><td colspan="2"><b>'.
		_XF_PEO_LONGDESCRIPTION.':</b><p>'.
		$ts->makeTareaData4Show(unofficial_getDBResult($result,0,
		'description')).'</td></tr><tr><td colspan="2">
		<h4>'._XF_PEO_REQUIREDSKILLS.':</h4>';

		//now show the list of desired skills
		$content .= '<p>'.people_show_job_inventory($job_id).'</td></tr>';

		//now show the user how to apply for this job
		global $xoopsUser;
		if ( $xoopsUser )
		{
			$content .= "<tr height=\"10\"><td colspan=\"2\">&nbsp;</td></tr>";
			if ( ! $apply )
			{
				$content .= "<tr><td colspan=\"2\"><h4>"._XF_PEO_APPLYFORJOB."</h4></td></tr>";
				$content .= "<tr><td colspan=\"2\">"._XF_PEO_IFYOUWANTTOAPPLY." \""._XF_PEO_APPLY."\" "._XF_PEO_BUTTONBELOW.".<br>"
					. _XF_PEO_APPLYDISCLAIMER."  "
					. $xoopsConfig['sitename']." "._XF_PEO_NOTRESPONSIBLE.".<br>"
					. "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">"
					. "<input type=\"hidden\" name=\"group_id\" value=\"".$group_id."\">"
					. "<input type=\"hidden\" name=\"job_id\" value=\"".$job_id."\">"
					. "<div align=\"center\"><input type=\"submit\" name=\"apply\" value=\""._XF_PEO_APPLY."\"></div></form></td></tr>\n";
			}
			else
			{
				$skill_sql = "SELECT s.name as skill_name,"
					. " sl.name as skill_level_name,"
					. " sy.name as skill_year_name"
					. " FROM ".$xoopsDB->prefix("xf_people_skill")." s, "
					. $xoopsDB->prefix("xf_people_skill_level")." sl, "
					. $xoopsDB->prefix("xf_people_skill_year")." sy, "
					. $xoopsDB->prefix("xf_people_skill_inventory")." si"
					. " WHERE si.user_id='".$xoopsUser->uid()."'"
					. " AND s.skill_id=si.skill_id"
					. " AND sl.skill_level_id=si.skill_level_id"
					. " AND sy.skill_year_id=si.skill_year_id"
					. " ORDER BY sl.skill_level_id DESC, sy.skill_year_id DESC";
				$skill_result = $xoopsDB->query($skill_sql);
				$subject = $xoopsConfig['sitename']." "._XF_PEO_JOBAPP;
				$mail = $subject."\n\n"
					. _XF_PEO_THISUSERAPPLIED.".\n\n"
					. _XF_PEO_USERNAME.":  ".$xoopsUser->uname()."\n"
					. _XF_PEO_JOB.":  ".unofficial_getDBResult($result,0,'title')."\n"
					. _XF_PEO_PROJECT.":  ".unofficial_getDBResult($result,0,'group_name')."\n";
				if ( 0 < $xoopsDB->getRowsNum($skill_result) )
				{
					$mail .= _XF_PEO_SKILLS.":\n";
					while ( $skill_row = $xoopsDB->fetchArray($skill_result) )
					{
						$mail .= "\t".$skill_row['skill_name']." - ".$skill_row['skill_level_name']." (".$skill_row['skill_year_name'].")\n";
					}
				}
				$mail .= "\n\n"._XF_PEO_ADDVIAPRJPAGE." ("
					. XOOPS_URL."/modules/xfmod/project/?".unofficial_getDBResult($result,0,'unix_group_name').")."
					. "\n\n"._XF_PEO_THANKSFORUSING." ".$xoopsConfig['sitename'].".\n";
				$adm_sql = "SELECT u.uid "
					. " FROM ".$xoopsDB->prefix("users")." u, "
					. $xoopsDB->prefix("xf_user_group")." ug"
					. " WHERE ug.group_id='".$group_id."'"
					. " AND ug.admin_flags='A'"
					. " AND u.uid=ug.user_id";
				$adm_result = $xoopsDB->query($adm_sql);
				$xoopsMailer =& getMailer();
				while ($adm_row = $xoopsDB->fetchArray($adm_result)) {
					$xoopsMailer->setToUsers(new XoopsUser($adm_row['uid']));
				}
				$xoopsMailer->setFromName("Novell Forge - Noreply");
				$xoopsMailer->setFromEmail($xoopsForge['noreply']);
				$xoopsMailer->setSubject($subject);
				$xoopsMailer->setBody($mail);
				$xoopsMailer->useMail();
				$xoopsMailer->send();

				$content .= "<tr><td colspan=\"2\">"
					 ."<b>"._XF_PEO_APPSUBMITTED."</b></td></tr>";
			}
		}
		$content .= "</table>";
	}

	$xoopsTpl->assign("content",$content);

	include (XOOPS_ROOT_PATH."/footer.php");
}
else
{
	if (!$group_id)
	{
    	redirect_header($GLOBALS["HTTP_REFERER"],2,"ERROR<br />No Group!");
    	exit;
  	}
	else
	{
    	redirect_header($GLOBALS["HTTP_REFERER"],2,
			"ERROR<br />Posting ID not found");
    	exit;
  	}
}

?>
