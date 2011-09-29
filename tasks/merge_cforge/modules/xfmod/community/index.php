<?php
	/**
	* community_home.php
	*
	* @version   $Id: index.php,v 1.12 2004/03/29 17:59:59 devsupaul Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vars.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/trove.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/maillist/maillist_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/mime_lookup.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/community/community_utils.php");
	$icmsOption['template_main'] = 'community/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	function isspace($c)
	{
		if ($c == ' ' || $c == '\n' || $c == '\r' || $c == '\t')
		{
			return true;
		}
		return false;
	}
	 
	/**
	*
	*
	*
	*/
	if (!isset($group_id) || !$group_id || !is_numeric($group_id))
	{
		$unixname = strtolower($QUERY_STRING);
		$res = $icmsDB->query("SELECT group_id FROM ".$icmsDB->prefix("xf_groups")." WHERE unix_group_name='".$unixname."'");
		if (!$res || $icmsDB->getRowsNum($res) < 1)
		{
			 
		}
		else
		{
			$group_arr = $icmsDB->fetchArray($res);
			$group_id = $group_arr['group_id'];
		}
	}
	$community = group_get_object($group_id);
	/**
	*
	*
	*
	*/
	if (!$community)
	{
		redirect_header(ICMS_URL, 4, _XF_COMM_COMMDOESNOTEXIST);
		exit;
	}
	if ($community->isInactive() && $activate == 'y')
	{
		$sql = "UPDATE " . $icmsDB->prefix("xf_groups")
		. " SET status='A', is_='1'" . " WHERE group_id='".$group_id."'";
		$result = $icmsDB->queryF($sql);
		// Refresh the community object
		$community = group_get_object($group_id);
	}
	//if(! $community)
	//{
	// redirect_header(ICMS_URL,4,_XF_COMM_PROJECTDOESNOTEXIST);
	// exit;
	//}
	$perm = $community->getPermission($icmsUser);
	 
	//group is private
	if (!$community->isPublic())
	{
		//if it's a private group, you must be a member of that group
		if (!$community->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
		{
			redirect_header(ICMS_URL."/", 4, _XF_COMM_COMMMARKEDASPRIVATE);
			exit;
		}
	}
	 
	//First, for inactive communities, you have to be a project admin or superuser
	if ($community->isInactive() && !$perm->isSuperUser() && !$perm->isAdmin())
	{
		redirect_header(ICMS_URL, 4, _XF_COMM_NOTAUTHORIZEDTOENTER);
		exit;
	}
	//for dead communities must be member of xoopsforge project
	if (!$community->isActive() && !$perm->isSuperUser())
	{
		redirect_header(ICMS_URL, 4, _XF_COMM_NOTAUTHORIZEDTOENTER);
		exit;
	}
	 
	if ($community->isProject())
	{
		redirect_header(ICMS_URL."/modules/xfmod/project/?".$community->getUnixName(), 4, "");
		exit;
	}
	include("../../../header.php");
	 
	//meta tag information
	$metaTitle = " Community - ".$community->getPublicName();
	$metaKeywords = project_getmetakeywords($group_id);
	$metaDescription = str_replace('"', "&quot;", strip_tags($community->getDescription()));
	 
	$icmsTpl->assign("icms_pagetitle", $metaTitle);
	$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
	$icmsTpl->assign("icms_meta_description", $metaDescription);
	 
	//community nav information
	$icmsTpl->assign("project_title", project_title($community));
	$icmsTpl->assign("project_tabs", project_tabs('home', $group_id));
	if ($community->isInactive() && $activate != 'y')
	{
		$icmsTpl->assign("inactive_info", "\r\n<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\" width=\"100%\">\n" . "<tr><td align=\"center\"><strong>"._XF_COMM_THISCOMMISINACTIVE."</strong></td></tr>\n" . "<form action=\"".ICMS_URL."/modules/xfmod/community/?".$community->getUnixName()."\" method=\"POST\">\n" . "<input type=\"hidden\" name=\"activate\" value=\"y\">\n" . "<tr><td align=\"center\"><input type=\"submit\" name=\"submit\" value=\""._XF_COMM_REACTIVATECOMM."\">\n" . "</td></tr></form></table><hr>\n");
	}
	 
	 
	// two column deal
	 
	// Description
	if ($community->getDescription())
	{
		$description = "<p>" . $ts->makeTareaData4Show($community->getDescription())."<BR/><BR/><BR/>";
	}
	else
	{
		$description = "<p>"._XF_COMM_NODESCRIPTION."<BR/>";
	}
	 
	$icmsTpl->assign("description", $description);
	 
	$res_admin = $icmsDB->query("SELECT u.uid,u.uname " ."FROM ".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_user_group")." ug " ."WHERE ug.user_id=u.uid " ."AND ug.group_id=$group_id " ."AND ug.admin_flags='A' " ."AND u.level>0");
	if ($res_admin && ($icmsDB->getRowsNum($res_admin) > 0))
	{
		$icmsTpl->assign("admins_title", _XF_COMM_COMMADMINS);
		$content = "";
		while ($row_admin = $icmsDB->fetchArray($res_admin))
		{
			$content .= "<A href='".ICMS_URL."/userinfo.php?uid=".$row_admin['uid']."'>".$row_admin['uname']."</a><BR>";
		}
		$icmsTpl->assign("admins_content", $content);
	}
	 
	$icmsTpl->assign("discussion_title", "Recent Discussions");
	$icmsTpl->assign("discussion_content", get_recent_discussions($group_id));
	 
	if ($community->getStatus() == 'H')
	{
		$icmsTpl->assign("maintained_label", "<p>"._XF_COMM_ISMAINTAINEDBYSTAFF);
	}
	 
	 
	// ############################## COMMUNITY NEWS
	 
	if ($community->usesNews())
	{
		$icmsTpl->assign("uses_news", true);
		$icmsTpl->assign("news_title", _XF_COMM_LATESTNEWS);
		$icmsTpl->assign("news_content", news_show_latest($group_id, 10, false));
	}
	 
	// ########################## List a featured project
	if ($community->hasFeaturedProject())
	{
		 
		$feedback = "";
		$featured_project_content = "No featured projects";
		 
		$sql = "SELECT * FROM ".$community->db->prefix("xf_foundry_featured_projects")." " ."WHERE foundry_id='".$community->getID()."'";
		$results = $community->db->queryF($sql);
		$featured_count = $community->db->getRowsNum($results);
		 
		if (!$results or $featured_count < 1)
		{
			$community->setError('No description for this freatured project!!');
			$featured_project_content = "No description for this freatured project.";
			return;
		}
		$pick_entry = rand()%$featured_count + 1;
		$pick = 1;
		while ($featured = $community->db->fetchArray($results))
		{
			if ($pick == $pick_entry)
			{
				$desc = $featured['description'];
				$featured_project = group_get_object($featured['project_id']);
				$featured_project_content = "<strong><a href='".ICMS_URL."/modules/xfmod/project/?".$featured_project->getUnixName()."'>".$featured_project->getPublicName()."</a></strong><BR><BR>";
				$featured_project_content .= $desc;
				break;
			}
			else
				$pick++;
		}
		 
		$featured_project_title = "Featured projects";
		 
		 
	}
	// ########################## List members of this community
	// ########################### Survey highlight
	$sql = "SELECT survey_id,survey_title FROM ".$icmsDB->prefix("xf_surveys")." WHERE group_id='$group_id' AND is_active='1'";
	$result = $icmsDB->query($sql);
	 
	global $icmsConfig, $icmsDB;
	$rows = $icmsDB->getRowsNum($result);
	$cols = unofficial_getNumFields($result);
	$survey_content = "";
	$survey_title = "Survey";
	$has_survey = $rows > 0;
	 
	if ($has_survey)
	{
		$pick = (rand()%$rows);
		for($j = 0; $j < $rows; $j++)
		{
			if ($pick == $j)
			{
				$survey_content .= "<table border='0'>";
				$survey_content .= show_survey_small($group_id, unofficial_getDBResult($result, $j, 'survey_id'));
				$survey_content .= "</table>";
			}
			 
		}
		 
	}
	// Community membership
	$uses_memberships = $community->usesMail();
	if ($uses_memberships)
	{
		 
		$feedback = "";
		$membership_content = "";
		$membership_title = "New Community Members";
		 
		if ($icmsUser)
		{
			if (!$community->isFoundryMember($icmsUser->uid()))
			{
				$membership_content .= "<A href='".ICMS_URL."/modules/xfmod/community/members.php?group_id=".$group_id."&membership=add'>Join Community</a><BR><BR>";
			}
			else
				{
				$membership_content .= "<A href='".ICMS_URL."/modules/xfmod/community/members.php?group_id=".$group_id."&membership=remove'>Remove My Membership</a><BR><BR>";
			}
		}
		else
		{
			$membership_content .= "<A href='http://forge.novell.com/modules/xfmod/community/members.php?group_id=".$group_id."&membership=add'>Join Community</a><BR><BR>";
		}
		 
		$users = $icmsDB->prefix("users");
		$foundry_members = $icmsDB->prefix("xf_user_foundry_groups");
		 
		$sql = "SELECT u.uname, u.uid FROM ".$users." u, ".$foundry_members." m " ."WHERE u.uid=m.user_id AND m.group_id='".$group_id."' " ."ORDER BY m.join_date DESC";
		 
		$limit = 10;
		$result = $icmsDB->query($sql, $limit);
		 
		if (!$result)
		{
			echo "Error: ".$icmsDB->error()."<br/>";
			exit;
		}
		 
		$rows = $icmsDB->getRowsNum($result);
		 
		if (!$result || $rows < 1)
		{
			$membership_content .= "No current results<br>";
		}
		else
		{
			for($i = 0; $i < $rows; $i++)
			{
				$curr_group = $icmsDB->fetchArray($result);
				$membership_content .= "<a href='".ICMS_URL."/userinfo.php?uid=".$curr_group['uid']."'>";
				$membership_content .= $curr_group['uname'];
				$membership_content .= "</a><br>";
			}
		}
		 
		$membership_content .= "<br><a href='".ICMS_URL."/modules/xfmod/community/members.php?group_id=".$group_id."'>View All</a>";
		 
		 
		 
	}
	 
	// ########################### Top ten projects in this community
	$projects_title = _XF_COMM_TOPTEN;
	$projects_content = "<br>";
	 
	$grp = $icmsDB->prefix("xf_groups");
	$metric = $icmsDB->prefix("xf_project_weekly_metric");
	$trove = $icmsDB->prefix("xf_trove_group_link");
	 
	$sql = "SELECT unix_group_name, group_name FROM $trove " ."LEFT JOIN $grp ON $trove.group_id=$grp.group_id " ."LEFT JOIN $metric ON $trove.group_id=$metric.group_id " ."WHERE trove_cat_id=$group_id " ."ORDER BY percentile DESC";
	 
	$limit = 10;
	$result = $icmsDB->query($sql, $limit);
	 
	if (!$result)
	{
		echo "Error: ".$icmsDB->error()."<br/>";
		exit;
	}
	 
	$rows = $icmsDB->getRowsNum($result);
	 
	if (!$result || $rows < 1)
	{
		$projects_content .= "No current results<br>";
	}
	else
	{
		for($i = 0; $i < $rows; $i++)
		{
			$curr_group = $icmsDB->fetchArray($result);
			$projects_content .= "<a href='".ICMS_URL."/modules/xfmod/project/?".$curr_group['unix_group_name']."'>";
			$projects_content .= $curr_group['group_name'];
			$projects_content .= "</a><br>";
		}
	}
	 
	$projects_content .= "<br><a href='".ICMS_URL."/modules/xfmod/community/project_list.php?form_cat=".$TROVE_COMMUNITY."&group_id=".$group_id."'>View All</a>";
	 
	 
	 
	 
	// ############################## COMMUNITY ARTICLES
	 
	if ($community->usesDocman())
	{
		// Are articles enabled?
		$sql = "SELECT doc_group" ." FROM ".$icmsDB->prefix("xf_doc_groups")
		." WHERE groupname='"._XF_COMM_ARTICLES_KEY."'" ." AND group_id='".$group_id."'";
		$result = $icmsDB->query($sql);
		$article_title = _XF_COMM_FEATUREDARTICLE;
		$has_article = 0 < $icmsDB->getRowsNum($result);
		$article_content = "";
		 
		if ($has_article)
		{
			 
			$document_active_state = 1;
			$document_private_state = 5;
			$sql = "SELECT dd.title,dd.data,dd.docid,dd.created_by,dd.stateid,u.uname" . " FROM " . $icmsDB->prefix("xf_doc_data") . " dd, " . $icmsDB->prefix("xf_doc_groups") . " dg, " . $icmsDB->prefix("users") . " u" . " WHERE dg.groupname='"._XF_COMM_ARTICLES_KEY."'" . " AND dg.group_id='".$group_id."'" . " AND dg.doc_group=dd.doc_group" . " AND dd.created_by=u.uid" . " ORDER BY dd.createdate DESC";
			$result = $icmsDB->query($sql);
			$numarticles = $icmsDB->getRowsNum($result);
			if ($numarticles > 0)
			{
				while ($row = $icmsDB->fetchArray($result))
				{
					if ($document_private_state == $row['stateid'])
					{
						$chkmembersql = "SELECT user_group_id" . " FROM ".$icmsDB->prefix("xf_user_group")
						. " WHERE user_id='".$icmsUser->uid()."'" . " AND group_id='".$group_id."'";
						$chkmemberresult = $icmsDB->query($chkmembersql);
						if (0 == $icmsDB->getRowsNum())
						{
							continue;
						}
					}
					else if($document_active_state != $row['stateid'])
					{
						continue;
					}
					else
						{
						$filename = trim($row['data']);
						$mime_type = mime_lookup($filename);
						if (0 == strcasecmp("text/html", $mime_type) || 0 == strcasecmp("text/plain", $mime_type))
						{
							$article_content = "<h3>".$row['title']."</h3><h5>"._XF_COMM_ARTICLEBY.":  <a href=\"http://".$_SERVER['HTTP_HOST']."/userinfo.php?uid=".$row['created_by']."\">".$row['uname']."</a></h5>";
							$size = filesize($filename);
							$fp = fopen($filename, "r");
							if (! $fp)
							{
								echo _XF_COMM_UNABLETOOPENARTICLE."  :  $php_errormsg<br>\n";
							}
							else
								{
								$fullbody = "";
								while (!feof($fp))
								{
									$fullbody .= fgets($fp, $size);
								}
								fclose($fp);
								$pseudo_max = 1024;
								if ($pseudo_max > $size)
								{
									$article_content .= $fullbody;
								}
								else
									{
									$body = substr($fullbody, 0, $pseudo_max);
									if (! isspace($fullbody {
										$pseudo_max-1 }
									))
									{
										while (! isspace($fullbody {
											$pseudo_max }
										))
										{
											$body .= $fullbody {
												$pseudo_max };
											$pseudo_max++;
										}
									}
									$body .= " ... <a href=\"".ICMS_URL."/modules/xfmod/docman/display_doc.php?docid=".$row['docid']."&group_id=".$group_id."\">("._XF_COMM_MORE.")</a>";
									$article_content .= $body;
								}
							}
						}
					}
				}
			}
			else
				{
				$article_content = "<h4>"._XF_COMM_NOARTICLES."</h4>";
			}
			$article_content .= "<p><a href=\"".ICMS_URL."/modules/xfmod/community/newarticle.php?group_id=".$group_id."\">"._XF_COMM_SUBMITARTICLE."</a>";
		}
	}
	 
	// Right side boxes:
	if (!$icmsUser || !$community->isFoundryMember($icmsUser->uid()))
	{
		$icmsTpl->assign("member", false);
	}
	else
	{
		$icmsTpl->assign("member", true);
	}
	 
	$icmsTpl->assign("projects_title", $projects_title);
	$icmsTpl->assign("projects_content", $projects_content);
	if ($uses_memberships)
	{
		$icmsTpl->assign("membership", true);
		$icmsTpl->assign("membership_title", $membership_title);
		$icmsTpl->assign("membership_content", $membership_content);
	}
	if ($community->hasFeaturedProject())
	{
		$icmsTpl->assign("featured_project", true);
		$icmsTpl->assign("featured_project_title", $featured_project_title);
		$icmsTpl->assign("featured_project_content", $featured_project_content);
	}
	if ($has_article)
	{
		$icmsTpl->assign("article", true);
		$icmsTpl->assign("article_title", $article_title);
		$icmsTpl->assign("article_content", $article_content);
	}
	if ($has_survey)
	{
		$icmsTpl->assign("survey", true);
		$icmsTpl->assign("survey_title", $survey_title);
		$icmsTpl->assign("survey_content", $survey_content);
	}
	 
	include("../../../footer.php");
?>