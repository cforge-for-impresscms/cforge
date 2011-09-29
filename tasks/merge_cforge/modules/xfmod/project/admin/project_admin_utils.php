<?php
	/**
	*
	* Project Admin: Module of common functions
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: project_admin_utils.php,v 1.9 2003/12/13 00:09:05 jcox Exp $
	*
	*/
	 
	require_once(ICMS_ROOT_PATH."/class/icmsUser.php");
	/*
	 
	Standard header to be used on all /project/admin/* pages
	 
	*/
	 
	function get_project_admin_header($group_id, &$perm, $is_project = 1)
	{
		global $feedback;
		 
		$prjhistory_caption = "";
		if ($is_project)
		{
			$prjhistory_caption = _XF_PRJ_PROJECTHISTORY;
			$type = "project";
		}
		else
		{
			$prjhistory_caption = _XF_COMM_COMMHISTORY;
			$type = "community";
		}
		$items = array();
		 
		// We should show most of the admin menu only to the admins
		if ($perm->isAdmin())
		{
			$items[] = "<a href='".ICMS_URL."/modules/xfmod/$type/admin/?group_id=".$group_id."'>"._XF_G_ADMIN."</a>";
			$items[] = "<a href='".ICMS_URL."/modules/xfmod/project/admin/userperms.php?group_id=".$group_id."'>"._XF_PRJ_USERPERMISSIONS."</a>";
			$items[] = "<a href='".ICMS_URL."/modules/xfmod/project/admin/history.php?group_id=".$group_id."'>".$prjhistory_caption."</a>";
			$items[] = "<a href='".ICMS_URL."/modules/xfmod/project/admin/editgroupinfo.php?group_id=".$group_id."'>"._XF_PRJ_EDITPUBLICINFO."</a>";
		}
		 
		if ($perm->isReleaseAdmin() || $perm->isReleaseTechnician())
		{
			$items[] = "<a href='".ICMS_URL."/modules/xfmod/project/admin/editpackages.php?group_id=".$group_id."'>"._XF_PRJ_EDITRELEASEFILES."</a>";
		}
		 
		// We should show most of the admin menu only to the admins
		if ($perm->isAdmin())
		{
			$items[] = "<a href='".ICMS_URL."/modules/xfjobs/createjob.php?group_id=".$group_id."'>"._XF_PRJ_POSTJOBS."</a>";
			$items[] = "<a href='".ICMS_URL."/modules/xfjobs/?group_id=".$group_id."'>"._XF_PRJ_EDITJOBS."</a>";
		}
		 
		$content = "<p><strong>";
		$content .= implode(" | ", $items);
		$content .= "</strong></p>";
		return $content;
	}
	 
	function project_admin_header($group_id, &$perm, $is_project = 1)
	{
		return get_project_admin_header($group_id, $perm, $is_project);
	}
	 
	/*
	The following functions are for the FRS(File Release System)
	*/
	 
	/*
	pop-up box of supported frs statuses
	*/
	 
	function frs_show_status_popup($name = 'status_id', $checked_val = "xzxz")
	{
		/*
		return a pop-up select box of statuses
		*/
		global $FRS_STATUS_RES, $icmsDB;
		if (!isset($FRS_STATUS_RES))
		{
			$FRS_STATUS_RES = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_frs_status"));
		}
		return html_build_select_box($FRS_STATUS_RES, $name, $checked_val, false);
	}
	/*
	pop-up box of supported frs statuses
	*/
	 
	function frs_get_status_name($status_id)
	{
		/*
		return a pop-up select box of statuses
		*/
		global $icmsDB;
		$result = $icmsDB->query("SELECT name FROM ".$icmsDB->prefix("xf_frs_status")." WHERE status_id=".$status_id);
		$row = $icmsDB->fetchRow($result);
		return $row[0];
	}
	 
	 
	/*
	pop-up box of supported frs filetypes
	*/
	 
	function frs_show_filetype_popup($name = 'type_id', $checked_val = "xzxz")
	{
		/*
		return a pop-up select box of the available filetypes
		*/
		global $FRS_FILETYPE_RES, $icmsDB;
		if (!isset($FRS_FILETYPE_RES))
		{
			$FRS_FILETYPE_RES = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_frs_filetype"));
		}
		return html_build_select_box($FRS_FILETYPE_RES, $name, $checked_val, true, 'Must Choose One');
	}
	 
	/*
	pop-up box of supported frs processor options
	*/
	 
	function frs_show_processor_popup($name = 'processor_id', $checked_val = "xzxz")
	{
		/*
		return a pop-up select box of the available processors
		*/
		global $FRS_PROCESSOR_RES, $icmsDB;
		if (!isset($FRS_PROCESSOR_RES))
		{
			$FRS_PROCESSOR_RES = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_frs_processor"));
		}
		return html_build_select_box($FRS_PROCESSOR_RES, $name, $checked_val, true, 'Must Choose One');
	}
	 
	/*
	pop-up box of packages:releases for this group
	*/
	 
	 
	function frs_show_release_popup($group_id, $name = 'release_id', $checked_val = "xzxz")
	{
		/*
		return a pop-up select box of releases for the project
		*/
		global $FRS_RELEASE_RES, $icmsDB;
		if (!$group_id)
		{
			return 'ERROR - GROUP ID REQUIRED';
		}
		else
		{
			if (!isset($FRS_RELEASE_RES))
			{
				$FRS_RELEASE_RES = $icmsDB->query("SELECT r.release_id,r.name " ."FROM ".$icmsDB->prefix("xf_frs_release")." r,".$icmsDB->prefix("xf_frs_package")." p " ."WHERE p.group_id='$group_id' " ."AND r.package_id=p.package_id");
				//echo $icmsDB->error();
			}
			return html_build_select_box($FRS_RELEASE_RES, $name, $checked_val, false);
		}
	}
	 
	/*
	 
	pop-up box of packages for this group
	 
	*/
	 
	function frs_show_package_popup($group_id, $name = 'package_id', $checked_val = "xzxz")
	{
		/*
		return a pop-up select box of packages for this project
		*/
		global $FRS_PACKAGE_RES, $icmsDB;
		if (!$group_id)
		{
			return 'ERROR - GROUP ID REQUIRED';
		}
		else
		{
			if (!isset($FRS_PACKAGE_RES))
			{
				$FRS_PACKAGE_RES = $icmsDB->query("SELECT package_id,name " ."FROM ".$icmsDB->prefix("xf_frs_package")." " ."WHERE group_id='$group_id'");
				//echo $icmsDB->error();
			}
			return html_build_select_box($FRS_PACKAGE_RES, $name, $checked_val, false);
		}
	}
	 
	/*
	 
	The following three functions are for group
	audit trail
	 
	When changes like adduser/rmuser/change status
	are made to a group, a row is added to audit trail
	using group_add_history()
	 
	*/
	 
	function group_get_history($group_id = false)
	{
		global $icmsDB;
		 
		$sql = "SELECT gh.field_name,gh.old_value,gh.date,u.uname " ."FROM ".$icmsDB->prefix("xf_group_history")." gh,".$icmsDB->prefix("users")." u " ."WHERE gh.mod_by=u.uid " ."AND group_id='$group_id' ORDER BY gh.date DESC";
		 
		return $icmsDB->query($sql);
	}
	 
	function group_add_history($field_name, $old_value, $group_id)
	{
		$group = group_get_object($group_id);
		$group->addHistory($field_name, $old_value);
	}
	 
	/*
	Nicely html-formatted output of this group's audit trail
	 
	*/
	function show_grouphistory($group_id, $is_project = 1)
	{
		/*
		show the group_history rows that are relevant to
		this group_id
		*/
		global $sys_datefmt, $icmsDB;
		$result = group_get_history($group_id);
		$rows = $icmsDB->getRowsNum($result);
		 
		if ($rows > 0)
		{
			 
			if ($is_project)
			{
				$content = "<H4>"._XF_PRJ_PROJECTHISTORY."</H4><p>";
			}
			else
			{
				$content = "<H4>"._XF_COMM_COMMHISTORY."</H4><p>";
			}
			 
			$content .= '<table border="0" width="95%" cellpadding="0" cellspacing="0" align="center" valign="top"><tr><td class="bg2">
				<table border="0" cellpadding="4" cellspacing="1" width="100%">
				<tr class="bg3" align="left">
				<td align="center"><span class="fg2"><strong>'._XF_G_FIELD.'</strong></span></td>
				<td align="center"><span class="fg2"><strong>'._XF_G_OLDVALUE.'</strong></span></td>
				<td align="center"><span class="fg2"><strong>'._XF_G_DATE.'</strong></span></td>
				<td align="center"><span class="fg2"><strong>'._XF_G_BY.'</strong></span></td>
				</tr>';
			 
			for($i = 0; $i < $rows; $i++)
			{
				$field = unofficial_getDBResult($result, $i, 'field_name');
				$content .= "<th class='".($i%2 > 0?"bg1":"bg3")."'>" ."<td>".$field."</td>" ."<td>";
				 
				if ($field == 'removed user')
				{
					$content .= icmsUser::getUnameFromId(unofficial_getDBResult($result, $i, 'old_value'));
				}
				else
				{
					$content .= unofficial_getDBResult($result, $i, 'old_value');
				}
				$content .= "</td>" ."<td>".date($sys_datefmt, unofficial_getDBResult($result, $i, 'date'))."</td>" ."<td>".unofficial_getDBResult($result, $i, 'uname')."</td>" ."</th>";
			}
			 
			$content .= "</table></td></tr></table>";
			 
		}
		else
		{
			$content .= "<H4>"._XF_PRJ_NOCHANGESMADETHISGROUP."</H4>";
		}
		 
		return $content;
	}
	 
?>