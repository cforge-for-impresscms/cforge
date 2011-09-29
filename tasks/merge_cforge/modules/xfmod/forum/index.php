<?php
	/**
	*
	* SourceForge Forums Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.8 2003/12/15 18:09:17 devsupaul Exp $
	*
	*/
	 
	include_once("../../../mainfile.php");
	 
	$langfile = "forum.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if ($icmsForge['forum_type'] != 'forum')
	redirect_header(ICMS_URL, 4, _NOPERM . "called from ".__FILE__." line ".__LINE__);
	 
	$icmsOption['template_main'] = 'forum/xfmod_index.html';
	 
	if ($group_id)
	{
		$group = group_get_object($group_id);
		$perm = $group->getPermission($icmsUser);
		 
		//group is private
		if (!$group->isPublic())
		{
			//if it's a private group, you must be a member of that group
			if (!$group->isMemberOfGroup($icmsUser) && !$perm->isSuperUser())
			{
				redirect_header(ICMS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
				exit;
			}
		}
		 
		//meta tag information
		$metaTitle = ": "._XF_FRM_FORUMS." - ".$group->getPublicName();
		$metaDescription = strip_tags($group->getDescription());
		$metaKeywords = project_getmetakeywords($group_id);
		 
		include("../../../header.php");
		$icmsTpl->assign("project_title", project_title($group));
		$icmsTpl->assign("project_tabs", project_tabs('forums', $group_id));
		 
		$icmsTpl->assign("isForumAdmin", $perm->isForumAdmin());
		$icmsTpl->assign("group_id", $group_id);
		$icmsTpl->assign("admin_label", _XF_G_ADMIN);
		 
		if ($icmsUser && $group->isMemberOfGroup($icmsUser))
		{
			$public_flag = '<3';
		}
		else
		{
			$public_flag = '=1';
		}
		 
		$sql = "SELECT g.group_forum_id,g.forum_name,g.description, famc.count as total " ."FROM ".$icmsDB->prefix("xf_forum_group_list")." g " ."LEFT JOIN ".$icmsDB->prefix("xf_forum_agg_msg_count")." famc USING(group_forum_id) " ."WHERE g.group_id='$group_id' AND g.is_public $public_flag";
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		/* extended extended forums */
		$extsql = "SELECT forum_name,forum_url FROM " .$icmsDB->prefix("xf_forum_ext_group_list")
		." WHERE group_id='$group_id'";
		$extres = $icmsDB->query($extsql);
		$extrows = $icmsDB->getRowsNum($extres);
		 
		if ((!$result || $rows < 1) && (!$extres || $extrows < 1))
		{
			$icmsTpl->assign("noforums", true);
			$icmsTpl->assign("noforums_message", sprintf(_XF_FRM_NOFORUMSFOUNDFORGROUP, $group->getPublicName()));
			$icmsTpl->assign("dberror", $icmsDB->error());
			 
			include("../../../footer.php");
			exit;
		}
		else
		{
			$icmsTpl->assign("choose_forum", _XF_FRM_CHOOSEFORUMTOBROWSE);
			 
			/*
			Put the result set(list of forums for this group) into a column with folders
			*/
			while ($row = $icmsDB->fetchArray($result))
			{
				$forums[] = $row;
			}
			$icmsTpl->assign("forums", $forums);
			 
			/* extended forums */
			/* not used
			while ($extrow = $icmsDB->fetchArray($extres)){
			$extforums[]=$extrow;
			}
			$icmsTpl->assign("extforums",$extforums);
			*/
		}
		include("../../../footer.php");
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, "No Group<br />You must specify a group id");
		exit;
	}
	 
?>