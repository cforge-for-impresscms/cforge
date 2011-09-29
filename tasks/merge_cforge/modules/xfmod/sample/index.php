<?php
	/**
	*
	* SourceForge Documentaion Manager
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.7 2004/03/17 17:12:15 jcox Exp $
	*
	*/
	 
	 
	/*
	by Quentin Cregan, SourceForge 06/2000
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "sample.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/sample/sample_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/mime_lookup.php");
	$icmsOption['template_main'] = 'sample/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if ($group_id)
	{
		$project = group_get_object($group_id);
		$perm = $project->getPermission($icmsUser);
		//group is private
		if (!$project->isPublic())
		{
			//if it's a private group, you must be a member of that group
			if (!$project->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
			{
				redirect_header(ICMS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
				exit;
			}
			$private_project = true;
		}
		else
		{
			$private_project = false;
		}
		 
		if (isset($_POST['sampleid']))
		$sampleid = $_POST['sampleid'];
		elseif(isset($_GET['sampleid']))
		$sampleid = $_GET['sampleid'];
		else
			$sampleid = null;
		 
		if ($sampleid)
		{
			require_once(ICMS_ROOT_PATH."/modules/xfmod/include/download.php");
		}
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($project, $group_id, _XF_SC_PROJECTDOCUMENTATION));
		 
		//get a list of group numbers that this project owns
		$query = "SELECT * " ."FROM ".$icmsDB->prefix("xf_sample_groups")." " ."WHERE group_id='$group_id' " ."ORDER BY groupname";
		 
		$result = $icmsDB->query($query);
		$content = "";
		//otherwise, throw up an error
		if ($icmsDB->getRowsNum($result) < 1)
		{
			$content .= "<strong>"._XF_SC_NOCATEGORIZEDDATA."</strong><p>";
		}
		else
		{
			// get the groupings and display them with their members.
			while ($row = $icmsDB->fetchArray($result))
			{
				$query = "SELECT description, sampleid, title, data, sample_group, createdate, created_by, stateid " ."FROM ".$icmsDB->prefix("xf_sample_data")
				." WHERE sample_group='".$row['sample_group']."'" ." AND(stateid='1'";
				//state 1 == 'active'
				 
				if ($icmsUser && $perm->isMember('user_id', $icmsUser->getVar('uid')))
				{
					$query .= " OR stateid='5' ";
				} //state 5 == 'private'
				$query .= ")";
				 
				$subresult = $icmsDB->query($query);
				 
				$content .= "<p><strong>".$ts->makeTboxData4Show($row['groupname'])."</strong>\n<ul>\n";
				if ($icmsDB->getRowsNum($subresult) > 0)
				{
					while ($subrow = $icmsDB->fetchArray($subresult))
					{
						$private_sample = ($subrow['stateid'] == 1)?false:
						true;
						$url = "index.php?group_id=".$group_id."&sampleid=".$subrow['sampleid'];
						if ($private_project || $private_sample) $url .= "&private=1";
						$tempUser = $member_handler->getUser($subrow['created_by']);
						$name = $tempUser->getVar('name', 'E') != '' ? $tempUser->getVar('name', 'E') :
						 $tempUser->getVar('uname', 'E');
						$content .= "<li>" ."<strong><a href='$url'>".$ts->makeTboxData4Show($subrow['title'])."</a></strong>" ."<BR />&nbsp;&nbsp;<i>"._XF_G_DESCRIPTION.":</i> ".$ts->makeTboxData4Show($subrow['description'])
						."<BR />&nbsp;&nbsp;<i>"._XF_SC_CREATEDBY.":</i> ".$name;
						if (!strstr($subrow['data'], 'http://'))
						{
							$content .= "<BR />&nbsp;&nbsp;<i>"._XF_SC_TIMESTAMP.":</i> ".date($sys_datefmt, $subrow['createdate']);
						}
					}
					$tempUser = null;
				}
				else
					{
					$content .= "<li>"._XF_SC_NOCODE;
				}
				$content .= "</ul>\n\n";
			}
		}
		$icmsTpl->assign("content", $content);
		include(ICMS_ROOT_PATH."/footer.php");
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "Error<br />No Group");
		exit;
	}
	 
?>