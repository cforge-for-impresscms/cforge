<?php
	/**
	* project_summary.php
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: project_summary.php,v 1.19 2004/02/04 16:49:38 jcox Exp $
	*/
	$tabcount = 0;
	$numtabs = 0;
	$project_agg_arr = array();
	/**
	* project_get_mail_list_count() - Get the number of mailing lists for a project.
	*
	* @param  int  The group ID
	*/
	function project_get_mail_list_count($group_id)
	{
		return project_getaggvalue($group_id, 'mail');
	}
	 
	/**
	* project_get_survey_count() - Get the number of surveys for a project.
	*
	* @param  int  The group ID
	*/
	function project_get_survey_count($group_id)
	{
		global $icmsDB;
		 
		$sql = "SELECT COUNT(*) AS count " ."FROM ".$icmsDB->prefix("xf_surveys")." " ."WHERE is_active=1 " ."AND group_id=$group_id";
		 
		return unofficial_getDBResult($icmsDB->query($sql), 0, 'count');
	}
	 
	/**
	* project_get_public_forum_count() - Get the number of public forums for a project.
	*
	* @param  int  The group ID
	*/
	function project_get_public_forum_count($group_id)
	{
		global $icmsDB, $icmsForge;
		 
		if ($icmsForge['forum_type'] == "forum")
		{
			$sql = "SELECT COUNT(*) AS count" ." FROM ".$icmsDB->prefix("xf_forum_group_list")
			." WHERE is_public=1" ." AND group_id=$group_id";
		}
		else
		{
			$sql = "SELECT COUNT(*) AS count" ." FROM ".$icmsDB->prefix("xf_forum_nntp_list")
			." WHERE group_id=$group_id";
		}
		return unofficial_getDBResult($icmsDB->query($sql), 0, 'count');
	}
	 
	/**
	* project_get_public_forum_message_count() - Get the number of messages within public forums for a project.
	*
	* @param  int  The group ID
	*/
	include_once(ICMS_ROOT_PATH."/modules/xfmod/newsportal/config.inc");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/newsportal/newsportal.php");
	function project_get_public_forum_message_count($group_id)
	{
		global $icmsDB;
		 
		if ($icmsForge['forum_type'] == "forum")
		{
			$sql = "SELECT COUNT(f.msg_id) AS count " . "FROM ".$icmsDB->prefix("xf_forum") . " f,".$icmsDB->prefix("xf_forum_group_list") . " fgl " ."WHERE f.group_forum_id=fgl.group_forum_id " ."AND fgl.is_public=1 " ."AND fgl.group_id=$group_id";
			return unofficial_getDBResult($icmsDB->query($sql), 0, 'count');
		}
		else
		{
			$sql = "SELECT forum_name from ".$icmsDB->prefix("xf_forum_nntp_list")
			." WHERE group_id=$group_id";
			$result = $icmsDB->query($sql);
			if (!$result) return "err";
			$count = 0;
			$ns = OpenNNTPconnection($server, $port);
			while (list($forum_name) = $icmsDB->fetchRow($result))
			{
				$messages += getNumArticles($ns, $forum_name);
			}
			if ($messages >= 1)
			{
				return $messages;
			}
			else
			{
				return '0';
			}
		}
	}
	 
	/**
	* tab_entry() - Prints out the a themed tab, used by project_tabs
	*
	* @param string Is the URL to link to
	* @param string Us the image to use(if the theme uses it)
	* @param string Is the title to use in the link tags
	* @param bool Is a boolean to test if the tab is 'selected'
	*/
	function tab_entry($url = 'http://localhost/', $icon = '', $title = 'Home', $selected = 0, $first = false)
	{
		global $icmsConfig, $icmsTheme, $tabcount, $numtabs;
		 
		$content = "";
		$tabcount++;
		$themeUrl = ICMS_URL."/modules/xfmod/images/";
		 
		if (!$first)
		{
			$content .= "<img src='".$themeUrl."dotline_vert13px.gif' width='1' height='13' alt=''>";
		}
		 
		$content .= "<a href='".$url."'>";
		 
		if ($selected)
		{
			$content .= "<strong> <span style='white-space: nowrap; color: #CC0000;'><img src='".$themeUrl."n_arrows_grey.gif' width='7' height='7' alt=''> ".$title." </span></strong>";
		}
		else
		{
			$content .= "<span style='white-space: nowrap;'> ".$title." </span>";
		}
		 
		$content .= "</a>";
		 
		return $content;
	}
	 
	/**
	* project_tabs() - Prints out the project tabs, contained here in case
	*  we want to allow it to be overriden
	*
	* @param string Is the tab currently selected
	* @param string Is the group we should look up get title info
	*  @param string Any extra text to print out
	*/
	function project_tabs($toptab, $group, $extra_text = '')
	{
		global $icmsDB, $icmsUser, $icmsConfig, $icmsTheme, $tabcount, $numtabs, $icmsForge;
		 
		$themeUrl = XOOPS_THEME_URL."/".$icmsConfig['theme_set']."/";
		 
		// get group info using the common result set
		$project = group_get_object($group);
		$perm = $project->getPermission($icmsUser);
		 
		if ($project->isError())
		{
			//wasn't found or some other problem
			return;
		}
		// count the tabs:
		// Summary
		$numtabs++;
		// Project Admin
		if ($perm->isAdmin()) $numtabs++;
		// Homepage
		if ($project->isProject()) $numtabs++;
		// community members
		if ($project->isFoundry()) $numtabs++;
		//newsbytes
		if ($project->usesNews()) $numtabs++;
		// Forums
		if ($project->usesForum()) $numtabs++;
		// FAQ  - use following to show on just communities $project->isFoundry() &&
		if ($project->usesFAQ()) $numtabs++;
		 
		$content = "";
		 
		// Artifact Tracking
		if ($project->isProject())
		{
			if ($project->usesTracker())
			{
				$numtabs++;
				$res = $icmsDB->query("SELECT * " ."FROM ".$icmsDB->prefix("xf_artifact_group_list")." " ."WHERE group_id='$group' " ."AND is_public='1' " ."AND datatype > 0 " ."ORDER BY datatype ASC");
				$rows = $icmsDB->getRowsNum($res);
				 
				//
				// Iterate through the public pre-defined trackers and add them to nav bar
				//
				for($i = 0; $i < $rows; $i++)
				{
					if (unofficial_getDBResult($res, $i, 'datatype') == 1) $numtabs++;
					elseif(unofficial_getDBResult($res, $i, 'datatype') == 2) $numtabs++;
					elseif(unofficial_getDBResult($res, $i, 'datatype') == 3) $numtabs++;
					elseif(unofficial_getDBResult($res, $i, 'datatype') == 4) $numtabs++;
				}
			}
			// Project Manager
			if ($project->usesPm()) $numtabs++;
		}
		 
		// Sample Code
		if ($project->usesSamples()) $numtabs++;
		// Doc Manager
		if ($project->usesDocman()) $numtabs++;
		// Mailing Lists
		if ($project->usesMail()) $numtabs++;
		// Surveys
		if ($project->usesSurvey()) $numtabs++;
		// Downloads
		if ($project->isProject())
		{
			$numtabs++;
			// CVS
			//if($project->usesCVS())
			if ($project->usesCVS() && ($project->anonCVS() || ($icmsUser && $perm->isMember("user_id", $icmsUser->getVar('uid'))))) $numtabs++;
		}
		 
		// Our communities do allow some menu items.
		$type = "project";
		if (! $project->isProject())
		{
			$type = "community";
		}
		 
		$urlpath = ICMS_URL."/modules/xfmod/".$type."/";
		 
		$content = "<BR/><table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td height='1' style='background-image:url(".XOOPS_THEME_URL."/".$icmsConfig['theme_set']."/dotlinebg_horiz.gif)'><img src='".XOOPS_THEME_URL."/".$icmsConfig['theme_set']."/spacer.gif' width='100%' height='1' border='0' alt=''></td><td width='20'></td></tr>";
		$content .= "<tr><td class='leadCopy'><span id='projectnav'>";
		 
		// Summary
		$content .= tab_entry($urlpath."?".$project->getUnixName(), '', _XF_G_SUMMARY, $toptab == "home", true);
		 
		// Project Admin
		if ($perm->isAdmin())
		{
			$content .= tab_entry($urlpath.'admin/?group_id='.$group, '', _XF_G_ADMIN, $toptab == 'admin');
		}
		 
		// Homepage
		if ($project->isProject())
		{
			$homepage = $project->getHomePage();
			if (0 != strcasecmp(ICMS_URL."/modules/xfmod/project/?".$project->getUnixName(), $homepage))
			{
				$content .= tab_entry($project->getHomePage(), '', _XF_G_HOMEPAGE);
			}
		}
		 
		// community members
		if ($project->isFoundry())
		{
			$content .= tab_entry(ICMS_URL.'/modules/xfmod/community/members.php?group_id='. $group, '', _XF_G_MEMBERS, $toptab == 'members');
		}
		 
		//newsbytes
		if ($project->usesNews())
		{
			$content .= tab_entry(ICMS_URL.'/modules/xfmod/news/?group_id='. $group, '', _XF_G_NEWS, $toptab == 'news');
		}
		 
		// Forums
		if ($project->usesForum())
		{
			$content .= tab_entry(ICMS_URL.'/modules/xfmod/'.$icmsForge['forum_type'].'/?group_id='.$group,
				'', _XF_G_FORUMS, $toptab == 'forums');
		}
		 
		// FAQ - use following to show on just communities $project->isFoundry() &&
		if ($project->usesFAQ())
		{
			$content .= tab_entry(ICMS_URL.'/modules/xfmod/faqs/?group_id='.$group,
				'', _XF_G_FAQS, $toptab == 'faqs');
		}
		 
		// Artifact Tracking
		if ($project->isProject())
		{
			if ($project->usesTracker())
			{
				$content .= tab_entry(ICMS_URL.'/modules/xfmod/tracker/?group_id='.$group,
					'', _XF_G_TRACKERS, $toptab == 'tracker');
				 
				$res = $icmsDB->query("SELECT * " ."FROM ".$icmsDB->prefix("xf_artifact_group_list")." " ."WHERE group_id='$group' " ."AND is_public='1' " ."AND datatype > 0 " ."ORDER BY datatype ASC");
				$rows = $icmsDB->getRowsNum($res);
				 
				//
				// Iterate through the public pre-defined trackers and add them to nav bar
				//
				for($i = 0; $i < $rows; $i++)
				{
					if (unofficial_getDBResult($res, $i, 'datatype') == 1)
					{
						//bug Tracker
						$content .= tab_entry(ICMS_URL.'/modules/xfmod/tracker/?group_id=' . $group.'&atid='.unofficial_getDBResult($res, $i, 'group_artifact_id'),
							'', _XF_G_BUGS, $toptab == 'bugs');
					}
					elseif(unofficial_getDBResult($res, $i, 'datatype') == 2)
					{
						//support Tracker
						$content .= tab_entry(ICMS_URL.'/modules/xfmod/tracker/?group_id=' . $group.'&atid='.unofficial_getDBResult($res, $i, 'group_artifact_id'),
							'', _XF_G_SUPPORT, $toptab == 'support');
					}
					elseif(unofficial_getDBResult($res, $i, 'datatype') == 3)
					{
						//patch Tracker
						$content .= tab_entry(ICMS_URL.'/modules/xfmod/tracker/?group_id='. $group.'&atid='.unofficial_getDBResult($res, $i, 'group_artifact_id'),
							'', _XF_G_PATCHES, $toptab == 'patch');
					}
					elseif(unofficial_getDBResult($res, $i, 'datatype') == 4)
					{
						//enhancement Tracker
						$content .= tab_entry(ICMS_URL.'/modules/xfmod/tracker/?group_id='. $group.'&atid='.unofficial_getDBResult($res, $i, 'group_artifact_id'),
							'', 'Features', $toptab == 'feature');
					}
				}
			}
			 
			// Project Manager
			if ($project->usesPm())
			{
				$content .= tab_entry(ICMS_URL.'/modules/xfmod/pm/?group_id='.$group,
					'', _XF_G_TASKS, $toptab == 'pm');
			}
		}
		 
		// Sample Code
		if ($project->usesSamples())
		{
			$content .= tab_entry(ICMS_URL.'/modules/xfmod/sample/?group_id='. $group, '', _XF_G_SAMPLE, $toptab == 'sample');
		}
		 
		// Doc Manager
		if ($project->usesDocman())
		{
			$content .= tab_entry(ICMS_URL.'/modules/xfmod/docman/?group_id='. $group, '', _XF_G_DOCS, $toptab == 'docman');
		}
		 
		// Mailing Lists
		if ($project->usesMail())
		{
			$content .= tab_entry(ICMS_URL.'/modules/xfmod/maillist/?group_id='. $group, '', _XF_G_LISTS, $toptab == 'maillist');
		}
		 
		// Surveys
		if ($project->usesSurvey())
		{
			$content .= tab_entry(ICMS_URL.'/modules/xfmod/survey/?group_id=' .$group, '', _XF_G_SURVEYS, $toptab == 'surveys');
		}
		 
		// Downloads
		if ($project->isProject())
		{
			$content .= tab_entry($urlpath.'showfiles.php?group_id='. $group, '', _XF_G_FILES, $toptab == 'downloads');
			 
			// CVS
			//if($project->usesCVS())
			if ($project->usesCVS() && ($project->anonCVS() || ($icmsUser && $perm->isMember("user_id", $icmsUser->getVar('uid')))))
			{
				/*
				tab_entry(ICMS_URL.'/modules/xfmod/cvs/cvsbrowse.php/' .
				$project->getUnixName().'/', '', _XF_G_CVS,
				$toptab == 'CVS');
				*/
				 
				$content .= tab_entry(ICMS_URL.'/modules/xfmod/cvs/cvspage.php/' . $project->getUnixName().'/', '', _XF_G_CVS,
					$toptab == 'CVS');
			}
		}
		 
		$content .= "</span></td></tr>";
		$content .= "<tr><td colspan='4' height='1'></td></tr><tr><td height='1' style='background-image:url(".XOOPS_THEME_URL."/".$icmsConfig['theme_set']."/dotlinebg_horiz.gif)'><img src='".XOOPS_THEME_URL."/".$icmsConfig['theme_set']."/spacer.gif' width='173' height='1' border='0' alt=''></td><td width='20'></td></tr></table>";
		$content .= "<p/>";
		return $content;
	}
	 
	function project_check_access($group_id, $require_membership = 1)
	{
		global $icmsUser;
		 
		if (!$group_id || $group_id == "" || !$group_id > 0)
		{
			redirect_header(ICMS_URL, 4, _NOPERM . "called from ".__FILE__." line ".__LINE__);
			exit;
		}
		 
		if (!$icmsUser)
		{
			redirect_header(ICMS_URL, 4, _NOPERM . "called from ".__FILE__." line ".__LINE__);
			exit;
		}
		 
		$group = group_get_object($group_id);
		$perm = $group->getPermission($icmsUser);
		 
		if (!$group->isActive() && !$perm->isSuperUser())
		{
			redirect_header(ICMS_URL, 4, _XF_PRJ_NOTAUTHORIZEDTOENTER);
			exit;
		}
		 
		if ($require_membership && !$perm->isMember('user_id', $icmsUser->getVar('uid')))
		{
			redirect_header(ICMS_URL, 4, _XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTMEMBEROFPROJECT);
			exit;
		}
	}
	 
	/**
	* project_getmetakeywords() - Returns trove keywords for a group as a string
	*
	* @param  int  The group ID
	*/
	function project_getmetakeywords($group_id)
	{
		global $discrim_url;
		global $expl_discrim;
		global $form_cat;
		global $icmsDB;
		$meta;
		$group_obj = group_get_object($group_id);
		 
		$res_trovecat = $icmsDB->query("SELECT tc.fullpath AS fullpath,tc.fullpath_ids AS fullpath_ids,tgl.trove_cat_id AS trove_cat_id, tc.trove_cat_id AS nullisacommunity" ." FROM ".$icmsDB->prefix("xf_trove_group_link")." tgl left join ".$icmsDB->prefix("xf_trove_cat")." tc " ."ON tgl.trove_cat_id=tc.trove_cat_id " ."WHERE tgl.group_id='$group_id' " ."ORDER BY tc.fullpath");
		 
		if ($icmsDB->getRowsNum($res_trovecat) < 1)
		{
			$meta = "";
			if ($group_obj->isProject())
			{
				$meta .= _XF_TRV_NONYETCATEGORIZED.' ';
			}
			else
				{
				$meta .= _XF_TRV_NONYETCATEGORIZEDCOMM.' ';
			}
		}
		else
		{
			// first unset the vars were using here
			$proj_discrim_used = '';
			$isfirstdiscrim = 1;
			$myfirsttime = 1;
			$meta = "Categorization";
			while ($row_trovecat = $icmsDB->fetchArray($res_trovecat))
			{
				while (is_null($row_trovecat['nullisacommunity']) && $row_trovecat)
				{
					if ($myfirsttime) $meta .= 'Community: ';
					$res_comm = $icmsDB->query("SELECT group_name FROM ".$icmsDB->prefix('xf_groups')." WHERE group_id=".$row_trovecat['trove_cat_id']);
					$row_comm = $icmsDB->fetchArray($res_comm);
					if (!$myfirsttime) $meta .= ", ";
					$meta .= $row_comm['group_name'];
					if ($a_filter)
					{
						if (in_array($row_trovecat['trove_cat_id'], $expl_discrim))
						{
							$meta .= '('._XF_TRV_NOWFILTERING.') ';
						}
						else
						{
							if (!$nofilter)
							{
								$meta .= '['._XF_TRV_FILTER.'] ';
							}
						}
					}
					$row_trovecat = $icmsDB->fetchArray($res_trovecat);
					if (!$row_trovecat)
					{
						return $meta;
					}
					unset($myfirsttime);
				}
				$folders = explode(" :: ", $row_trovecat['fullpath']);
				$folders_ids = explode(" :: ", $row_trovecat['fullpath_ids']);
				$folders_len = count($folders);
				// if first in discrim print root category
				if (isset($proj_discrim_used[$folders_ids[0]]) && !$proj_discrim_used[$folders_ids[0]])
				{
					$meta .= (', '.$folders[0].': ');
				}
				 
				// filter links, to add discriminators
				// first check to see if filter is already applied
				$filterisalreadyapplied = 0;
				for($i = 0; $i < sizeof($expl_discrim); $i++)
				{
					if ($folders_ids[$folders_len-1] == $expl_discrim[$i]) $filterisalreadyapplied = 1;
				}
				// then print the stuff
				if (isset($proj_discrim_used[$folders_ids[0]]) && $proj_discrim_used[$folders_ids[0]])
				$meta .= ', ';
				 
				$meta .= ($folders[$folders_len-1]);
				 
				if (isset($a_filter) && $a_filter)
				{
					if ($filterisalreadyapplied)
					{
						$meta .= '('._XF_TRV_NOWFILTERING.') ';
					}
					else
					{
						if ($discrim_url)
						{
							$meta .= $discrim_url.','.$folders_ids[$folders_len-1];
						}
						else
						{
							$meta .= '&discrim='.$folders_ids[$folders_len-1];
						}
						 
						if (!$nofilter)
						{
							$meta .= '['._XF_TRV_FILTER.'] ';
						}
					}
				}
				$proj_discrim_used[$folders_ids[0]] = 1;
				$isfirstdiscrim = 0;
			}
			return $meta;
		}
	}
	 
	/**
	* project_title() - Formats the title of the project/community page.
	*
	* @param  group        The group class to display the title of.
	*/
	function project_title($group)
	{
		$content = "";
		if ($group->isFoundry())
		{
			$content .= "<table width='100%' cellspacing='0' cellpadding='0'><tr><td><span class='title'>".$group->getPublicName()." "._XF_G_COMM." </span></td>";
			$content .= "<td align='left' width='148'><img src='".ICMS_URL."/modules/xfmod/images/community/".$group->getUnixName()."_comm_red.gif' alt='community logo'></td></tr></table>";
		}
		else
		{
			$content .= "<span class='title'>"._XF_G_PROJECT;
			$content .= ": ".$group->getPublicName()."</span><BR/>";
		}
		return $content;
	}
	 
?>