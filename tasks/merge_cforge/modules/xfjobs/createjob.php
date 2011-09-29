<?php
	/**
	*
	* SourceForge Jobs (aka Help Wanted) Board
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: createjob.php,v 1.7 2003/12/10 20:01:32 jcox Exp $
	*
	*/
	 
	include_once ("../../mainfile.php");
	 
	require_once("language/english/people.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfjobs/people_utils.php");
	$icmsOption['template_main'] = 'xfjobs_createjob.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) $ {
		$k }
	 = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) $ {
		$k }
	 = StopXSS($v);
	 
	if (!isset($fromedit))
	{
		$title = "";
		$category_id = 100;
		$description = "";
	}
	 
	if ($group_id && $group_id != '')
		{
		 
		$group = group_get_object($group_id);
		$perm = $group->getPermission($icmsUser );
		 
		if (!$perm->isAdmin())
			{
			redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_G_PERMISSIONDENIED."<br />"._LOCAL_XF_PRJ_NOTADMINTHISPROJECT);
			exit;
		}
		 
		if ($group->isFoundry() )
			{
			define("_LOCAL_XF_PRJ_NOTADMINTHISPROJECT", _XF_COMM_NOTADMINTHISCOMM );
			define("_LOCAL_XF_PEO_CREATEJOBFORPROJECT", _XF_PEO_CREATEJOBFORCOMM );
		}
		else
			{
			define("_LOCAL_XF_PRJ_NOTADMINTHISPROJECT", _XF_PRJ_NOTADMINTHISPROJECT );
			define("_LOCAL_XF_PEO_CREATEJOBFORPROJECT", _XF_PEO_CREATEJOBFORPROJECT );
		}
		 
		 
		include (ICMS_ROOT_PATH."/header.php");
		$content = people_header($group_id, $job_id);
		$content .= get_project_admin_header($group_id, $perm, $group->isProject());
		$content .= '
			<H4>'._LOCAL_XF_PEO_CREATEJOBFORPROJECT.'</H4>
			<p>'._XF_PEO_STARTFILLINGINFIELDSBELOW.'<p>
			<form action="'.ICMS_URL.'/modules/xfjobs/editjob.php" method="POST">
			<input type="hidden" name="group_id" value="'.$group_id.'">
			<strong>'._XF_PEO_CATEGORY.':</strong><BR>' . people_job_category_box('category_id', $category_id) . '<p><strong>'._XF_PEO_SHORTDESCRIPTION.':</strong><BR>
			<input type="text" name="title" value="'.$title. '"SIZE="40" maxlength="60">
			<p><strong>'._XF_PEO_LONGDESCRIPTION.':</strong><BR>
			<textarea name="description" rows="10" cols="60" WRAP="SOFT">'.$ts->makeTareaData4Show($description).'</textarea>
			<p><input type="submit" name="add_job" value="'. _XF_PEO_CONTINUE.'"></form>';
		 
		$icmsTpl->assign("content", $content);
		 
		include (ICMS_ROOT_PATH."/footer.php");
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "ERROR<br />No Group!");
		exit;
	}
?>