<?php
	/**
	*
	* SourceForge Project/Task Manager(PM)
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.4 2004/02/05 23:26:56 jcox Exp $
	*
	*/
	include_once("../../../../mainfile.php");
	 
	$langfile = "pm.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/pm/pm_utils.php");
	$icmsOption['template_main'] = 'pm/admin/xfmod_index.html';
	 
	/* http_track_vars */
	//$group_id = util_http_track_vars('group_id');
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	/*
	Project / Task Manager Admin
	By Tim Perdue Nov. 1999
	*/
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isPMAdmin())
	{
		redirect_header($_SERVER["HTTP_REFERER"], 2, _XF_G_PERMISSIONDENIED."<br />"._XF_PM_YOUNOTASKMANAGER);
		exit;
	}
	 
	if ($post_changes)
	{
		/*
		Update the database
		*/
		 
		if ($projects)
		{
			/*
			Insert a new project
			*/
			 
			$sql = "INSERT INTO ".$icmsDB->prefix("xf_project_group_list")."(group_id,project_name,is_public,description) " ."VALUES('$group_id','". $ts->makeTboxData4Save($project_name) ."','$is_public','". $ts->makeTboxData4Save($description) ."')";
			 
			$result = $icmsDB->queryF($sql);
			if (!$result)
			{
				$feedback .= " Error inserting value ";
				$feedback .= $icmsDB->error();
			}
			 
			$feedback .= " "._XF_PM_SUBPROJECTINSERTED." ";
		}
		else if($change_status)
		{
			/*
			Change a project to public/private
			*/
			$sql = "UPDATE ".$icmsDB->prefix("xf_project_group_list")." SET " ."is_public='$is_public'," ."project_name='". $ts->makeTboxData4Save($project_name) ."'," ."description='". $ts->makeTboxData4Save($description) ."' " ."WHERE group_id='$group_id' " ."AND group_project_id='$group_project_id'";
			 
			$result = $icmsDB->queryF($sql);
			if (!$result)
			{
				$feedback .= " "._XF_PM_ERRORUPDATESTATUS." ";
				$feedback .= $icmsDB->error();
			}
			else
			{
				$feedback .= " "._XF_PM_STATUSUPDATED." ";
			}
		}
	}
	 
	include(ICMS_ROOT_PATH."/header.php");
	 
	//meta tag information
	$metaTitle = " "._XF_PM_TASKS." - ".$group->getPublicName();
	$metaKeywords = project_getmetakeywords($group_id);
	$metaDescription = str_replace('"', "&quot;", strip_tags($group->getDescription()));
	 
	$icmsTpl->assign("icms_pagetitle", $metaTitle);
	$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
	$icmsTpl->assign("icms_meta_description", $metaDescription);
	 
	// project nav information
	$icmsTpl->assign("project_title", project_title($group));
	$icmsTpl->assign("project_tabs", project_tabs('pm', $group_id));
	 
	/*
	Show UI forms
	*/
	 
	if ($projects)
	{
		/*
		Show categories and blank row
		*/
		 
		$header = pm_header($group, $perm, _XF_PM_ADDPROJECTS, $group_project_id);
		$icmsTpl->assign("pm_header", $header);
		/*
		List of possible categories for this group
		*/
		$sql = "SELECT group_project_id,project_name FROM ".$icmsDB->prefix("xf_project_group_list")." WHERE group_id='$group_id'";
		$result = $icmsDB->query($sql);
		$content .= "<p>";
		if ($result && $icmsDB->getRowsNum($result) > 0)
		{
			ShowResultSet($result, _XF_PM_EXISTINGPROJECTS);
		}
		else
		{
			//$content .= "\r\n<H4>"._XF_PM_NOSUBPROJECTSFOUND."</H4>";
		}
		 
		$content .= "
			<p>
			"._XF_PM_ADDPROJECTINFO."
			</p>
			<form action='".$_SERVER['PHP_SELF']."' METHOD='POST'>
			<input type='hidden' name='projects' value='y'>
			<input type='hidden' name='group_id' value='".$group_id."'>
			<input type='hidden' name='post_changes' value='y'>
			<p>
			<strong>"._XF_G_ISPUBLIC."</strong><BR>
			<input type='RADIO' name='is_public' value='1' CHECKED> "._YES."<BR>
			<input type='RADIO' name='is_public' value='0'> "._NO."<p>
			<p>
			<H4 style='text-align:left;'>"._XF_PM_NEWPROJECTNAME." :</H4>
			<p>
			<input type='text' name='project_name' value='' size='15' maxlength='30'>
			<p>
			<strong>"._XF_G_DESCRIPTION." :</strong><BR>
			<input type='text' name='description' value='' size='40' maxlength='80'>
			<p>
			<input type='submit' name='submit' value='"._XF_G_SUBMIT."'>
			</form>";
		 
	}
	else if($change_status)
	{
		/*
		Change a project to public/private
		*/
		 
		$header = pm_header($group, $perm, _XF_PM_CHANGEPROJECT, $group_project_id);
		$icmsTpl->assign("pm_header", $header);
		 
		$sql = "SELECT project_name,group_project_id,is_public,description " ."FROM ".$icmsDB->prefix("xf_project_group_list")." " ."WHERE group_id='$group_id'";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		if (!$result || $rows < 1)
		{
			$content .= '
				<H4>'._XF_PM_NOSUBPROJECTSFOUND.'</H4>
				<p>';
			$content .= $icmsDB->error();
		}
		else
		{
			$content .= '
				<p>'._XF_PM_MAKEPRIVATEINFO.'<p>';
			 
			$content .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>"._XF_PM_STATUS."</strong></td>" ."<td><strong>"._XF_PM_NAME."</strong></td>" ."<td><strong>"._XF_G_UPDATE."</strong></td>" ."</tr>";
			 
			for($i = 0; $i < $rows; $i++)
			{
				$content .= '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
					<input type="hidden" name="post_changes" value="y">
					<input type="hidden" name="change_status" value="y">
					<input type="hidden" name="group_project_id" value="'.unofficial_getDBResult($result, $i, 'group_project_id').'">
					<input type="hidden" name="group_id" value="'.$group_id.'">';
				 
				$content .= '<th class="'.($j%2 > 0?'bg1':'bg3').'"><td>
					 
					<strong>'._XF_G_ISPUBLIC.'</strong><BR>
					<input type="radio" name="is_public" value="1"'.((unofficial_getDBResult($result, $i, 'is_public') == '1')?' CHECKED':'').'> '._YES.'<BR>
					<input type="radio" name="is_public" value="0"'.((unofficial_getDBResult($result, $i, 'is_public') == '0')?' CHECKED':'').'> '._NO.'<BR>
					<input type="radio" name="is_public" value="9"'.((unofficial_getDBResult($result, $i, 'is_public') == '9')?' CHECKED':'').'> '._XF_G_DELETED.'<BR>
					</td><td>
					<input type="text" name="project_name" value="'. $ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'project_name')) .'">
					</td><td>
					 
					<input type="submit" name="submit" value="'._XF_G_UPDATE.'">
					</td></th>
					<th class="'.($j%2 > 0?'bg1':'bg3').'"><td colspan="3">
					<strong>'._XF_G_DESCRIPTION.':</strong><BR>
					<input type="text" name="description" value="'.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'description')) .'" size="40" maxlength="80"><BR>
					</td></th>
					</form>';
			}
			$content .= '</table>';
		}
		 
	}
	else
	{
		/*
		Show main page
		*/
		$header = pm_header($group, $perm, 'Project/Task Manager Administration', $group_project_id);
		$icmsTpl->assign("pm_header", $header);
		 
		$content .= '<p>
			<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&projects=1"><strong>'._XF_PM_ADDASUBPROJECT.'</strong></a>
			<BR>'._XF_PM_ADDASUBPROJECTINFO.'
			<P/>
			<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&change_status=1"><strong>'._XF_PM_UPDATEINFORMATION.'</strong></a>
			<BR>'._XF_PM_UPDATEINFORMATIONINFO;
	}
	 
	$icmsTpl->assign("content", $content);
	include(ICMS_ROOT_PATH."/footer.php");
	 
?>