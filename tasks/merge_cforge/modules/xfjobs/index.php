<?php
	/**
	*
	* SourceForge Jobs (aka Help Wanted) Board
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.8 2003/12/10 20:01:32 jcox Exp $
	*
	*/
	 
	include_once ("../../mainfile.php");
	 
	//$langfile="people.php";
	require_once("language/english/people.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfjobs/people_utils.php");
	$icmsOption['template_main'] = 'xfjobs_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) $ {
		$k }
	 = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) $ {
		$k }
	 = StopXSS($v);
	 
	include (ICMS_ROOT_PATH."/header.php");
	 
	function deleteJob($jobId)
	{
		global $xoopsForgeErrorHandler, $icmsDB;
		$result = $icmsDB->queryF("DELETE FROM " . $icmsDB->prefix("xf_people_job") . " WHERE job_id='$jobId'");
		 
		$cc = true;
		 
		if (!$result)
		{
			$xoopsForgeErrorHandler->addError("Failed deleting job $jobId: ". "Does not exist");
			$cc = false;
		}
		 
		$icmsDB->queryF("DELETE FROM " . $icmsDB->prefix("xf_people_job_inventory") . " WHERE job_id='$jobId'");
		 
		if ($cc)
		{
			$xoopsForgeErrorHandler->addError("Deleted job $jobId");
		}
		 
		return $cc;
	}
	 
	if ($group_id)
		{
		$group = group_get_object($group_id);
		$perm = $group->getPermission($icmsUser );
		$content = people_header($group_id, $job_id);
		$content .= get_project_admin_header($group_id, $perm, $group->isProject());
		 
		if ($del_job_id)
		{
			deleteJob($del_job_id);
		}
		 
		if ($group->isFoundry() )
			{
			$content .= '<p>'._XF_PEO_LISTOFPOSITIONSCOMM.'<p>';
		}
		else
			{
			$content .= '<p>'._XF_PEO_LISTOFPOSITIONS.'<p>';
		}
		 
		$content .= people_show_project_jobs($group_id);
	}
	else if ($category_id)
	{
		 
		$content .= people_header($group_id, $job_id);
		$content .= '<p>'._XF_PEO_CLICKJOBTITLESFORDETAIL.'<p>';
		$content .= people_show_category_jobs($category_id);
		 
	}
	else
	{
		$content .= people_header($group_id, $job_id);
		$content .= people_show_category_table();
		$content .= '<h4>'._XF_PEO_LASTPOSTS.'</h4>';
		 
		$sql = "SELECT pj.group_id,pj.job_id,g.group_name,g.unix_group_name,". "pj.title,pj.date,pjc.name AS category_name ". "FROM ".$icmsDB->prefix("xf_people_job"). " pj,".$icmsDB->prefix("xf_people_job_category"). " pjc,".$icmsDB->prefix("xf_groups")." g ". "WHERE pj.group_id=g.group_id ". "AND pj.category_id=pjc.category_id ". "AND pj.status_id=1 ". "AND g.is_public=1 ". "AND g.status='A' ". "ORDER BY date DESC";
		 
		$result = $icmsDB->query($sql, 5);
		 
		$content .= people_show_job_list($result);
		$content .= '<p>[ <a href="helpwanted-latest.php">'. _XF_PEO_MORELATESTPOSTS.'</a> ]</p>';
	}
	 
	include (ICMS_ROOT_PATH."/header.php");
	$icmsTpl->assign("content", $content);
	include (ICMS_ROOT_PATH."/footer.php");
	 
?>