<?php
	/**
	*
	* Package Monitor Page
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: filemodule_monitor.php,v 1.4 2003/12/15 18:09:21 devsupaul Exp $
	*
	*/
	 
	include_once("../../../mainfile.php");
	$icmsOption['template_main'] = 'project/xfmod_filemonitor.html';
	 
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if ($icmsUser && $filemodule_id)
	{
		/*
		User obviously has to be logged in to monitor
		a file module
		*/
		$sql = "SELECT group_id FROM ".$icmsDB->prefix("xf_frs_package")." WHERE package_id=$filemodule_id";
		$result = $icmsDB->query($sql);
		list($group_id) = $icmsDB->fetchRow($result);
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
		}
		 
		include("../../../header.php");
		 
		$content .= "<H4 style='text-align:left;'>"._XF_PRJ_MONITORAPACKAGE."</H4>";
		 
		/*
		First check to see if they are already monitoring
		this thread. If they are, say so and quit.
		If they are NOT, then insert a row into the db
		*/
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_filemodule_monitor")." WHERE user_id='".$icmsUser->getVar("uid")."' AND filemodule_id='$filemodule_id'";
		$result = $icmsDB->query($sql);
		if (!$result || $icmsDB->getRowsNum($result) < 1)
		{
			/*
			User is not already monitoring this filemodule, so
			insert a row so monitoring can begin
			*/
			$sql = "INSERT INTO ".$icmsDB->prefix("xf_filemodule_monitor")."(filemodule_id,user_id) VALUES('$filemodule_id','".$icmsUser->getVar("uid")."')";
			 
			$result = $icmsDB->queryF($sql);
			 
			if (!$result)
			{
				$content .= "<FONT COLOR='RED'>Error inserting into filemodule_monitor</FONT>";
				$content .= $icmsDB->error();
			}
			else
			{
				$content .= "<FONT COLOR='RED'><H4 style='text-align:left;'>"._XF_PRJ_PACKAGEISMONITORED."</H4></FONT>" ."<p>"._XF_PRJ_PACKAGEMONITOREDINFO1 ."<p>"._XF_PRJ_PACKAGEMONITOREDINFO2;
			}
			 
		}
		else
		{
			 
			$sql = "DELETE FROM ".$icmsDB->prefix("xf_filemodule_monitor")." WHERE user_id='".$icmsUser->getVar("uid")."' AND filemodule_id='$filemodule_id'";
			$result = $icmsDB->queryF($sql);
			$content .= "<FONT COLOR='RED'><H4 style='text-align:left;'>"._XF_PRJ_MONITORINGTURNEDOFF."</H4></FONT>" ."<p>"._XF_PRJ_MONITORINGTURNEDOFFINFO;
			 
		}
		$icmsTpl->assign("content", $content);
		include("../../../footer.php");
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 2, _NOPERM . "called from ".__FILE__." line ".__LINE__);
		exit;
	}
?>