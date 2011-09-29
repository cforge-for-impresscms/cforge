<?php
	/**
	*
	* SourceForge Documentaion Manager
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.9 2004/03/08 18:25:05 devsupaul Exp $
	*
	*/
	 
	 
	/*
	Docmentation Manager
	by Quentin Cregan, SourceForge 06/2000
	*/
	include_once("../../../../mainfile.php");
	 
	$langfile = "sample.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/sample/sample_utils.php");
	$icmsOption['template_main'] = 'sample/admin/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	// get current information
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isDocEditor())
	{
		redirect_header($_SERVER["HTTP_REFERER"], 2, _XF_G_PERMISSIONDENIED."<br />"._XF_SC_YOUARENOTCODEMANAGER);
		exit;
	}
	 
	function main_page($project, $group_id)
	{
		global $icmsTpl;
		 
		$icmsTpl->assign("sampleman_header", sampleman_header($project, $group_id, _XF_SC_CODEMANAGERADMIN, 'admin'));
		$content = "";
		// Allow to enable/disable articles if a foundry
		if ($project->isFoundry())
		{
			global $icmsDB;
			$content .= _XF_SC_COMM_ARTICLES.":  <strong>";
			$sql = "SELECT sample_group" . " FROM " . $icmsDB->prefix("xf_sample_groups")
			. " WHERE group_id='" . $group_id . "'" . " AND groupname='"._XF_SC_ARTICLES_KEY."'";
			$result = $icmsDB->query($sql);
			if (! $result || $icmsDB->getRowsNum($result) < 1)
			{
				$content .= _XF_SC_ARTICLESDISABLED."</strong> - <a href=\"".$_SERVER['PHP_SELF']."?group_id=".$group_id."&articles=y\">"._XF_SC_ARTICLESCLICKTOENABLE."</a><hr>\n";
			}
			else
				{
				$content .= _XF_SC_ARTICLESENABLED."(";
				$row = $icmsDB->fetchArray($result);
				$sample_group_id = $row['sample_group'];
				$sql = "SELECT stateid" . " FROM " . $icmsDB->prefix("xf_sample_states")
				. " WHERE name='active'";
				$result = $icmsDB->query($sql);
				$row = $icmsDB->fetchArray($result);
				$id = $row['stateid'];
				$sql = "SELECT sampleid" . " FROM " . $icmsDB->prefix("xf_sample_data")
				. " WHERE sample_group='" . $sample_group_id . "'" . " AND stateid='".$id."'";
				$result = $icmsDB->query($sql);
				$num_articles = $icmsDB->getRowsNum($result);
				$content .= intval($num_articles) . " "._XF_SC_ARTICLESACTIVE.")</strong> - <a href=\"".$_SERVER['PHP_SELF']."?group_id=".$group_id."&articles=n\">"._XF_SC_ARTICLESCLICKTODISABLE."</a>";
				if ($num_articles > 0)
				{
					$content .= " ("._XF_SC_ARTICLESWILLBEUNAVAIL.")";
				}
				$content .= "<hr>\n";
			}
		}
		$content = "<h3>"._XF_SC_ACTIVECODE.":</h3>";
		$content .= display_samples('1', $group_id);
		$content .= "<br><h3>"._XF_SC_PENDINGCODE.":</h3>";
		$content .= display_samples('3', $group_id);
		$content .= "<br><h3>"._XF_SC_HIDDENCODE.":</h3>";
		$content .= display_samples('4', $group_id);
		$content .= "<br><h3>"._XF_SC_PRIVATECODE.":</h3>";
		$content .= display_samples('5', $group_id);
		$content .= "<br><h3>"._XF_SC_DELETEDCODE.":</h3>";
		$content .= display_samples('2', $group_id);
		return $content;
		 
	} //end function main_page($group_id);
	$content = "";
	//begin to seek out what this page has been called to do.
	if (strstr($mode, "sampleedit"))
	{
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_SC_EDITCODE, 'admin'));
		 
		$query = "SELECT * " ."FROM ".$icmsDB->prefix("xf_sample_data")." dd, ".$icmsDB->prefix("xf_sample_groups")." dg " ."WHERE sampleid='$sampleid' " ."AND dg.sample_group=dd.sample_group " ."AND dg.group_id='$group_id'";
		 
		$result = $icmsDB->query($query);
		$row = $icmsDB->fetchArray($result);
		 
		$content .= '
			<form name="editdata" action="index.php?mode=sampledoedit&group_id='.$group_id.'" method="POST">
			<table border="0" width="75%">
			<tr>
			<td><strong>'._XF_SC_CODETITLE.':</strong></td>
			<td><input type="text" name="title" size="40" maxlength="255" value="'.$ts->makeTboxData4Edit($row['title']).'"></td>
			</tr>
			<tr>
			<td><strong>'._XF_SC_DESCRIPTION.':</strong></td>
			<td><input type="text" name="description" size="40" maxlength="255" value="'.$ts->makeTboxData4Edit($row['description']).'"></td>
			</tr>
			<tr>';
		if (strstr($row['data'], "://"))
		{
			$content .= '
				<td><strong>'._XF_SC_FILENAME.':</strong></td>
				<td><input type="text" name="data" value="'.$ts->makeTareaData4Edit($row['data']).'" size="40" maxlength="255"></td>';
		}
		else
		{
			$content .= '
				<td><strong>'._XF_SC_FILENAME.':</strong></td>
				<td>'.basename($ts->makeTareaData4Edit($row['data'])).'</td>';
		}
		$content .= '
			</tr>
			<tr>
			<td><strong>'._XF_SC_GROUPCODEBELONGSIN.':</strong></td>
			<td>';
		$content .= display_groups_option($group_id, $row['sample_group']);
		$content .= '
			</td>
			</tr>
			<tr>
			<td><strong>'._XF_SC_CODESTATE.':</strong></td>
			<td>';
		$res_states = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_sample_states"));
		$content .= html_build_select_box($res_states, 'stateid', $row['stateid'], false);
		$content .= '
			</td>
			</tr>
			</table>
			<input type="hidden" name="sampleid" value="'.$row['sampleid'].'">
			<input type="submit" value="'._XF_G_SUBMIT.'">
			</form>';
		 
		$content .= display_sample_feedback($group_id, $row['sampleid'], 10);
		$icmsTpl->assign("content", $content);
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	elseif(strstr($mode, "groupdelete"))
	{
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_SC_EDITCODE, 'admin'));
		 
		$query = "SELECT sampleid " ."FROM ".$icmsDB->prefix("xf_sample_data")." " ."WHERE sample_group='$sample_group'";
		 
		$result = $icmsDB->query($query);
		 
		if ($icmsDB->getRowsNum($result) < 1)
		{
			$query = "DELETE FROM ".$icmsDB->prefix("xf_sample_groups")." " ."WHERE sample_group='$sample_group' " ."AND group_id='$group_id'";
			 
			$icmsDB->queryF($query);
			 
			$pagehead = _XF_SC_GROUPDELETE;
			$icmsTpl->assign("content", "<p><strong>"._XF_SC_GROUPDELETED.".("._XF_SC_GROUPID." : ".$sample_group.")</strong>");
		}
		else
		{
			$pagehead = _XF_SC_GROUPDELETEFAILED;
			$icmsTpl->assign("content", _XF_SC_CANNOTDELETEGROUP);
		}
		 
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	elseif(strstr($mode, "groupedit"))
	{
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_SC_GROUPEDIT, 'admin'));
		 
		$query = "SELECT * " ."FROM ".$icmsDB->prefix("xf_sample_groups")." " ."WHERE sample_group='$sample_group' " ."AND group_id='$group_id'";
		 
		$result = $icmsDB->query($query);
		$row = $icmsDB->fetchArray($result);
		$content = '
			<br />
			<strong> '._XF_SC_EDITAGROUP.':</strong>
			 
			<form name="editgroup" action="index.php?mode=groupdoedit&group_id='.$group_id.'" method="POST">
			<table>
			<tr><th>'._XF_SC_NAME.':</th>
			<td><input type="text" name="groupname" value="'.$ts->makeTboxData4Edit($row['groupname']).'"></td></tr>
			<input type="hidden" name="sample_group" value="'.$row['sample_group'].'">
			<tr><td> <input type="submit" value="'._XF_G_SUBMIT.'"></td></tr></table>
			</form>';
		$icmsTpl->assign("content", $content);
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	elseif(strstr($mode, "groupdoedit"))
	{
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_SC_GROUPEDIT, 'admin'));
		 
		$query = "UPDATE ".$icmsDB->prefix("xf_sample_groups")." SET " ."groupname='".$ts->makeTboxData4Save($groupname)."' " ."WHERE sample_group='$sample_group' " ."AND group_id='$group_id'";
		 
		$icmsDB->queryF($query);
		$icmsTpl->assign("feedback", _XF_SC_CODEGROUPEDITED);
		$icmsTpl->assign("content", main_page($group, $group_id));
		include(ICMS_ROOT_PATH."/footer.php");
	}
	elseif(strstr($mode, "sampledoedit"))
	{
		//Page security - checks someone isnt updating a sample
		//that isnt theirs.
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_SC_GROUPEDIT, 'admin'));
		 
		$query = "SELECT dd.sampleid, dd.data AS data " ."FROM ".$icmsDB->prefix("xf_sample_data")." dd, ".$icmsDB->prefix("xf_sample_groups")." dg " ."WHERE dd.sample_group=dg.sample_group " ."AND dg.group_id='$group_id' " ."AND dd.sampleid='$sampleid'";
		 
		$result = $icmsDB->query($query);
		 
		if ($icmsDB->getRowsNum($result) == 1)
		{
			$row = $icmsDB->fetchArray($result);
			// data in DB stored in htmlspecialchars()-encoded form
			if ($stateid == 2)
			{
				//deleting a file
				if (!strstr($row['data'], "://"))
				{
					if (!$frs) $frs = new FRS($group_id);
					if (!$frs->rmfile($group->getUnixName()."/sample/".$row['data']))
					{
						$icmsTpl->assign("feedback", $frs->getErrorMessage());
						$icmsTpl->assign("content", main_page($group, $group_id));
						include_once(ICMS_ROOT_PATH."/footer.php");
						exit();
					}
				}
				$query = "UPDATE ".$icmsDB->prefix("xf_sample_data")." SET " ."title='".$ts->makeTboxData4Save($title)."',";
				$query .= "data='".$icmsUser->getVar('name')." - ".$icmsUser->getVar('uname')."',";
				$query .= "updatedate='".time()."'," ."sample_group='".$sample_group."'," ."stateid='".$stateid."'," ."description='".$ts->makeTboxData4Save($description)."' " ."WHERE sampleid='$sampleid'";
			}
			else
				{
				if (!$frs) $frs = new FRS($group_id);
				if ($stateid == 1)
				{
					$frs->chmodpath($icmsForge['ftp_path']."/".$group->getUnixName()."/sample/".$row['data'], 0664);
				}
				else
					{
					$frs->chmodpath($icmsForge['ftp_path']."/".$group->getUnixName()."/sample/".$row['data'], 0660);
				}
				$query = "UPDATE ".$icmsDB->prefix("xf_sample_data")." SET " ."title='".$ts->makeTboxData4Save($title)."',";
				if ($data)
				{
					$query .= "data='".$ts->makeTareaData4Save($data)."',";
				}
				else if($icmsForge['ftp_path'] != substr($row['data'], 0, strlen($icmsForge['ftp_path'])))
				{
					$query .= "data='".$ts->makeTareaData4Save($row['data'])."',";
				}
				$query .= "updatedate='".time()."'," ."sample_group='".$sample_group."'," ."stateid='".$stateid."'," ."description='".$ts->makeTboxData4Save($description)."' " ."WHERE sampleid='$sampleid'";
			}
			$res = $icmsDB->queryF($query);
			if (!$res)
			{
				$icmsTpl->assign("feedback", _XF_SC_COULDNOTUPDATECODE.'<br>');
			}
			else
			{
				$icmsTpl->assign("feedback", sprintf(_XF_SC_CODETITLEUPDATED, $ts->makeTboxData4Show($title)));
			}
			$icmsTpl->assign("content", main_page($group, $group_id));
			 
		}
		else
		{
			$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_DOC_COULDNOTUPDATECODE, 'admin'));
			$icmsTpl->assign("content", "Unable to update - Sample code does not exist, or sample's group is not the same as that to which your account belongs.");
		}
		include(ICMS_ROOT_PATH."/footer.php");
	}
	elseif(strstr($mode, "groupadd"))
	{
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_SC_GROUPEDIT, 'admin'));
		 
		$query = "INSERT INTO ".$icmsDB->prefix("xf_sample_groups")."(groupname,group_id) " ."values('" ."".$ts->makeTboxData4Save($groupname)."'," ."'$group_id')";
		 
		$icmsDB->queryF($query);
		$icmsTpl->assign("feedback", sprintf(_XF_SC_GROUPGROUPNAMEADDED, $ts->makeTboxData4Show($groupname)));
		$icmsTpl->assign("content", main_page($group, $group_id));
		include(ICMS_ROOT_PATH."/footer.php");
	}
	elseif(strstr($mode, "editgroups"))
	{
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_SC_GROUPEDIT, 'admin'));
		 
		$content = '
			<p><strong> '._XF_SC_ADDAGROUP.':</strong>
			<form name="addgroup" action="index.php?mode=groupadd&group_id='.$group_id.'" method="POST">
			<table>
			<tr><td><strong>'._XF_SC_NEWGROUPNAME.':</strong></td>  <td><input type="text" name="groupname"></td><td><input type="submit" value="'._XF_G_ADD.'"></td></tr></table>
			<p>'._XF_SC_GROUPNAMEWILLBEUSEDASTITLE.'</p>
			</form>';
		$content .= display_groups($group_id);
		$icmsTpl->assign("content", $content);
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	elseif(strstr($mode, "editsamples"))
	{
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("sampleman_header", sampleman_header($group, $group_id, _XF_SC_EDITCODELIST, 'admin'));
		$icmsTpl->assign("content", main_page($group, $group_id));
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	else if($articles != "")
	{
		global $icmsDB;
		if ($articles == "y")
		{
			// Check for a refresh - don't recreate if a refresh
			$sql = "SELECT sample_group" . " FROM " . $icmsDB->prefix("xf_sample_groups")
			. " WHERE groupname='"._XF_SC_ARTICLES_KEY."'" . " AND group_id='".$group_id."'";
			$result = $icmsDB->query($sql);
			if (1 > $icmsDB->getRowsNum($result))
			{
				$sql = "INSERT INTO " . $icmsDB->prefix("xf_sample_groups")
				. "(groupname, group_id) VALUES" . "('"._XF_SC_ARTICLES_KEY."','".$group_id."')";
				$result = $icmsDB->queryF($sql);
			}
		}
		else if($articles == "n")
		{
			$sql = "DELETE FROM " . $icmsDB->prefix("xf_sample_groups")
			. " WHERE groupname='"._XF_SC_ARTICLES_KEY."'" . " AND group_id='".$group_id."'";
			$result = $icmsDB->queryF($sql);
		}
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("content", main_page($group, $group_id));
		include(ICMS_ROOT_PATH."/footer.php");
	}
	else
	{
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("content", main_page($group, $group_id));
		include(ICMS_ROOT_PATH."/footer.php");
	} //end else
	 
?>