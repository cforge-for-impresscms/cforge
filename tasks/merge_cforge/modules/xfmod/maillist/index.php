<?php
	/**
	*
	* SourceForge Mailing List Manager
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.11 2004/01/30 18:05:00 jcox Exp $
	*
	*/
	 
	 
	/*
	by Quentin Cregan, SourceForge 06/2000
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "maillist.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/maillist/maillist_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	 
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
		}
		 
		if ($project->isFoundry())
		{
			define("_LOCAL_XF_ML_MLNOTENABLED", _XF_ML_MLNOTENABLECOMM);
			define("_LOCAL_XF_G_PROJECT", _XF_G_COMM);
			define("_LOCAL_XF_ML_FULLNAME", _XF_ML_FULLNAMECOMM);
		}
		else
		{
			define("_LOCAL_XF_ML_MLNOTENABLED", _XF_ML_MLNOTENABLED);
			define("_LOCAL_XF_G_PROJECT", _XF_G_PROJECT);
			define("_LOCAL_XF_ML_FULLNAME", _XF_ML_FULLNAME);
		}
		 
		if (!$project->usesMail())
		{
			redirect_header($_SERVER["HTTP_REFERER"], 4, _LOCAL_XF_ML_MLNOTENABLED);
			exit;
		}
		 
		include("../../../header.php");
		 
		//meta tag information
		$metaTitle = ": "._XF_ML_LISTS." - ".$project->getPublicName();
		$metaKeywords = project_getmetakeywords($group_id);
		$metaDescription = str_replace('"', "&quot;", strip_tags($project->getDescription()));
		 
		$icmsTpl->assign("icms_pagetitle", $metaTitle);
		$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
		$icmsTpl->assign("icms_meta_description", $metaDescription);
		 
		//project nav information
		echo project_title($project);
		echo project_tabs('maillist', $group_id);
		if ($perm->isAdmin() || $perm->isSuperUser())
		{
			// Provide administrative link to site admins or superusers.
			echo "<p/><strong><a href='".ICMS_URL."/modules/xfmod/maillist/admin/index.php?group_id=$group_id'>"._XF_G_ADMIN."</a></strong></p>";
		}
		 
		echo "<p/>\n";
		$sql = "SELECT name, description FROM ".$icmsDB->prefix("xf_maillists")." WHERE group_id=$group_id";
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		if (!$result || $rows < 1)
		{
			echo "<b/>No mailing lists found for ".$project->getPublicName()."</strong>";
		}
		else
		{
			while (list($suffix, $desc) = $icmsDB->fetchRow($result))
			{
				echo "<strong>".$project->getUnixName() . "-" . $suffix."</strong> - ".$desc."<br>\n&nbsp;&nbsp;";
				echo "<a href=\"".ICMS_URL."/modules/xfmod/maillist/subscribe.php?group_id=".$group_id."&list=".urlencode($project->getUnixName()."-".$suffix)."\">"._XF_ML_SUBSCRIBE."</a>\n" ."&nbsp; | &nbsp; <a href=\"http://" . $_SERVER['SERVER_NAME'] . "/modules/xfmod/maillist/archbrowse.php/" . $project->getUnixName(). "-" . $suffix . "/?id=" . $group_id . "&prjname=" . $project->getUnixName() . "&mlname=" . $suffix . "\">" . _XF_ML_VIEW_ARCHIVE . "</a><br><br>\n";
			}
		}
		 
		include("../../../footer.php");
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "Error<br />No Group");
		exit;
	}
	 
?>