<?php
	/**
	*
	* SourceForge Jobs (aka Help Wanted) Board
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: helpwanted-latest.php,v 1.5 2003/12/10 20:01:32 jcox Exp $
	*
	*/
	include_once ("../../mainfile.php");
	 
	require_once("language/english/people.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfjobs/people_utils.php");
	$icmsOption['template_main'] = 'xfjobs_helpwanted-latest.html';
	 
	include (ICMS_ROOT_PATH."/header.php");
	 
	$icmsTpl->assign('header', people_header($group_id, $job_id));
	 
	$content = '<H4>'._XF_PEO_HELPWANTEDLATEST.'</H4>';
	 
	$sql = "SELECT pj.group_id,pj.job_id,g.group_name,g.unix_group_name,pj.title,pj.date,pjc.name AS category_name " ."FROM ".$icmsDB->prefix("xf_people_job")." pj,".$icmsDB->prefix("xf_people_job_category")." pjc,".$icmsDB->prefix("xf_groups")." g " ."WHERE pj.group_id=g.group_id " ."AND pj.category_id=pjc.category_id " ."AND pj.status_id=1 " ."ORDER BY date DESC";
	 
	$result = $icmsDB->query($sql, 30);
	 
	$content .= people_show_job_list($result);
	$icmsTpl->assign("content", $content);
	 
	include (ICMS_ROOT_PATH."/footer.php");
	 
?>