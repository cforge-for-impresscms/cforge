<?php
	/**
	*
	* SourceForge Documentaion Manager
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.13 2004/03/24 21:17:17 devsupaul Exp $
	*
	*/
	 
	 
	/*
	Docmentation Manager
	by Quentin Cregan, SourceForge 06/2000
	*/
	include_once("../../../../mainfile.php");
	 
	$langfile = "docman.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/docman/doc_utils.php");
	$icmsOption['template_main'] = 'docman/admin/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	// get current information
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isDocEditor())
	{
		redirect_header($_SERVER["HTTP_REFERER"], 2, _XF_G_PERMISSIONDENIED."<br />"._XF_DOC_YOUARENOTDOCMANAGER);
		exit;
	}
	 
	function main_page($project, $group_id)
	{
		global $icmsTpl;
		 
		$icmsTpl->assign("docman_header", docman_header($project, $group_id, _XF_DOC_DOCUMENTMANAGERADMIN, 'admin'));
		$content = "";
		// Allow to enable/disable articles if a foundry
		if ($project->isFoundry())
		{
			global $icmsDB;
			$content .= _XF_DOC_COMM_ARTICLES.":  <strong>";
			$sql = "SELECT doc_group" . " FROM " . $icmsDB->prefix("xf_doc_groups")
			. " WHERE group_id='" . $group_id . "'" . " AND groupname='"._XF_DOC_ARTICLES_KEY."'";
			$result = $icmsDB->query($sql);
			if (! $result || $icmsDB->getRowsNum($result) < 1)
			{
				$content .= _XF_DOC_ARTICLESDISABLED."</strong> - <a href=\"".$_SERVER['PHP_SELF']."?group_id=".$group_id."&articles=y\">"._XF_DOC_ARTICLESCLICKTOENABLE."</a><hr>\n";
			}
			else
				{
				$content .= _XF_DOC_ARTICLESENABLED."(";
				$row = $icmsDB->fetchArray($result);
				$doc_group_id = $row['doc_group'];
				$sql = "SELECT stateid" . " FROM " . $icmsDB->prefix("xf_doc_states")
				. " WHERE name='active'";
				$result = $icmsDB->query($sql);
				$row = $icmsDB->fetchArray($result);
				$id = $row['stateid'];
				$sql = "SELECT docid" . " FROM " . $icmsDB->prefix("xf_doc_data")
				. " WHERE doc_group='" . $doc_group_id . "'" . " AND stateid='".$id."'";
				$result = $icmsDB->query($sql);
				$num_articles = $icmsDB->getRowsNum($result);
				$content .= intval($num_articles) . " "._XF_DOC_ARTICLESACTIVE.")</strong> - <a href=\"".$_SERVER['PHP_SELF']."?group_id=".$group_id."&articles=n\">"._XF_DOC_ARTICLESCLICKTODISABLE."</a>";
				if ($num_articles > 0)
				{
					$content .= " ("._XF_DOC_ARTICLESWILLBEUNAVAIL.")";
				}
				$content .= "<hr>\n";
			}
		}
		$content .= "<h3>"._XF_DOC_ACTIVEDOCUMENTS.":</h3>";
		$content .= display_docs('1', $group_id);
		$content .= "<br><h3>"._XF_DOC_PENDINGDOCUMENTS.":</h3>";
		$content .= display_docs('3', $group_id);
		$content .= "<br><h3>"._XF_DOC_HIDDENDOCUMENTS.":</h3>";
		$content .= display_docs('4', $group_id);
		$content .= "<br><h3>"._XF_DOC_PRIVATEDOCUMENTS.":</h3>";
		$content .= display_docs('5', $group_id);
		$content .= "<br><h3>"._XF_DOC_DELETEDDOCUMENTS.":</h3>";
		$content .= display_docs('2', $group_id);
		return $content;
		 
	} //end function main_page($group_id);
	$content = "";
	//begin to seek out what this page has been called to do.
	if (strstr($mode, "docedit"))
	{
		 
		$query = "SELECT * " ."FROM ".$icmsDB->prefix("xf_doc_data")." dd, ".$icmsDB->prefix("xf_doc_groups")." dg " ."WHERE docid='$docid' " ."AND dg.doc_group=dd.doc_group " ."AND dg.group_id='$group_id'";
		 
		$result = $icmsDB->query($query);
		$row = $icmsDB->fetchArray($result);
		 
		include_once(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("docman_header", docman_header($group, $group_id, _XF_DOC_EDITDOC, 'admin'));
		 
		$content .= '
			 
			<form name="editdata" action="index.php?mode=docdoedit&group_id='.$group_id.'" method="POST">
			<table border="0" width="75%">
			<tr>
			<td><strong>'._XF_DOC_DOCUMENTTITLE.':</strong></td>
			<td><input type="text" name="doc_title" size="40" maxlength="255" value="'.$ts->htmlSpecialChars($row['title']).'"></td>
			</tr>
			<tr>
			<td><strong>'._XF_DOC_DESCRIPTION.':</strong></td>
			<td><input type="text" name="doc_description" size="40" maxlength="255" value="'.$ts->makeTboxData4Edit($row['description']).'"></td>
			</tr>
			<tr>';
		if (strstr($row['data'], "://"))
		{
			$content .= '<td><strong>'._XF_DOC_FILENAME.':</strong></td>
				<td><input type="text" name="data" value="'.$ts->makeTareaData4Edit($row['data']).'" size="40" maxlength="255"></td>';
		}
		else
		{
			$content .= '<td><strong>'._XF_DOC_FILENAME.':</strong></td>
				<td>'.basename($ts->makeTareaData4Edit($row['data'])).'</td>';
		}
		$content .= '</tr>
			<tr>
			<td><strong>'._XF_DOC_GROUPDOCBELONGSIN.':</strong></td>
			<td>';
		$content .= display_groups_option($group_id, $row['doc_group']);
		$content .= '
			</td>
			</tr>
			<tr>
			<td><strong>'._XF_DOC_DOCSTATE.':</strong></td>
			<td>';
		$res_states = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_doc_states"));
		$content .= html_build_select_box($res_states, 'stateid', $row['stateid'], false);
		$content .= '
			</td>
			</tr>
			</table>
			<input type="hidden" name="docid" value="'.$row['docid'].'">
			<input type="submit" value="'._XF_G_SUBMIT.'">
			</form>';
		 
		$content .= display_doc_feedback($group_id, $row['docid'], 10);
		$icmsTpl->assign("content", $content);
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	elseif(strstr($mode, "groupdelete"))
	{
		include_once(ICMS_ROOT_PATH."/header.php");
		$query = "SELECT docid" ." FROM ".$icmsDB->prefix("xf_doc_data")
		." WHERE doc_group='$doc_group'" ." AND stateid!=2";
		 
		$result = $icmsDB->query($query);
		 
		if ($icmsDB->getRowsNum($result) < 1)
		{
			$query = "DELETE FROM ".$icmsDB->prefix("xf_doc_groups")
			." WHERE doc_group='$doc_group'" ." AND group_id='$group_id'";
			$icmsDB->queryF($query);
			 
			$query = "DELETE FROM ".$icmsDB->prefix("xf_doc_data")
			." WHERE doc_group='$doc_group'" ." AND group_id='$group_id'";
			$icmsDB->queryF($query);
			 
			$pagehead = _XF_DOC_GROUPDELETE;
			$icmsTpl->assign("content", "<p><strong>"._XF_DOC_GROUPDELETED.".("._XF_DOC_GROUPID." : ".$doc_group.")</strong>");
		}
		else
		{
			$pagehead = _XF_DOC_GROUPDELETEFAILED;
			$icmsTpl->assign("content", _XF_DOC_CANNOTDELETEGROUP);
		}
		$icmsTpl->assign("docman_header", docman_header($group, $group_id, $pagehead, 'admin'));
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	elseif(strstr($mode, "groupedit"))
	{
		include_once(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("docman_header", docman_header($group, $group_id, _XF_DOC_GROUPEDIT, 'admin'));
		$query = "SELECT * " ."FROM ".$icmsDB->prefix("xf_doc_groups")." " ."WHERE doc_group='$doc_group' " ."AND group_id='$group_id'";
		 
		$result = $icmsDB->query($query);
		$row = $icmsDB->fetchArray($result);
		$content = '
			<br />
			<strong> '._XF_DOC_EDITAGROUP.':</strong>
			 
			<form name="editgroup" action="index.php?mode=groupdoedit&group_id='.$group_id.'" method="POST">
			<table>
			<tr><th>'._XF_DOC_NAME.':</th>
			<td><input type="text" name="groupname" value="'.$ts->makeTboxData4Edit($row['groupname']).'"></td></tr>
			<input type="hidden" name="doc_group" value="'.$row['doc_group'].'">
			<tr><td> <input type="submit" value="'._XF_G_SUBMIT.'"></td></tr></table>
			</form>
			';
		$icmsTpl->assign("content", $content);
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	elseif(strstr($mode, "groupdoedit"))
	{
		include_once(ICMS_ROOT_PATH."/header.php");
		 
		$query = "UPDATE ".$icmsDB->prefix("xf_doc_groups")." SET " ."groupname='".$ts->makeTboxData4Save($groupname)."' " ."WHERE doc_group='$doc_group' " ."AND group_id='$group_id'";
		 
		$icmsDB->queryF($query);
		$icmsTpl->assign("feedback", _XF_DOC_DOCUMENTGROUPEDITED);
		$icmsTpl->assign("content", main_page($group, $group_id));
		include_once(ICMS_ROOT_PATH."/footer.php");
	}
	elseif(strstr($mode, "docdoedit"))
	{
		//Page security - checks someone isnt updating a doc
		//that isnt theirs.
		include_once(ICMS_ROOT_PATH."/header.php");
		$query = "SELECT dd.docid, dd.data AS data " ."FROM ".$icmsDB->prefix("xf_doc_data")." dd, ".$icmsDB->prefix("xf_doc_groups")." dg " ."WHERE dd.doc_group=dg.doc_group " ."AND dg.group_id='$group_id' " ."AND dd.docid='$docid'";
		 
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
					if (!$frs->rmfile($group->getUnixName()."/docs/".$row['data']))
					{
						//$icmsTpl->assign("feedback",$frs->getErrorMessage());
						//$icmsTpl->assign("content",main_page($group, $group_id));
						//include_once(ICMS_ROOT_PATH."/footer.php");
						//exit();
					}
				}
				$query = "UPDATE ".$icmsDB->prefix("xf_doc_data")." SET " ."title='".$ts->makeTboxData4Save($doc_title)."',";
				$query .= "data='".$icmsUser->getVar('name')." - ".$icmsUser->getVar('uname')."',";
				$query .= "updatedate='".time()."'," ."doc_group='".$doc_group."'," ."stateid='".$stateid."'," ."description='".$ts->makeTboxData4Save($doc_description)."' " ."WHERE docid='$docid'";
			}
			else
				{
				if (!$frs) $frs = new FRS($group_id);
				if ($stateid == 1)
				{
					$frs->chmodpath($icmsForge['ftp_path']."/".$group->getUnixName()."/docs/".$row['data'], 0664);
				}
				else
					{
					$frs->chmodpath($icmsForge['ftp_path']."/".$group->getUnixName()."/docs/".$row['data'], 0660);
				}
				$query = "UPDATE ".$icmsDB->prefix("xf_doc_data")." SET " ."title='".$ts->addSlashes($doc_title)."',";
				if ($data)
				{
					$query .= "data='".$ts->addSlashes($data)."',";
				}
				else if($group->getUnixName()."/docs/" != substr($row['data'], 0, strlen($group->getUnixName()."/docs/")))
				{
					$query .= "data='".$ts->addSlashes($row['data'])."',";
				}
				$query .= "updatedate='".time()."'," ."doc_group='".$doc_group."'," ."stateid='".$stateid."'," ."description='".$ts->addSlashes($doc_description)."' " ."WHERE docid='$docid'";
			}
			$res = $icmsDB->queryF($query);
			if (!$res)
			{
				//$icmsTpl->assign("feedback",_XF_DOC_COULDNOTUPDATEDOCUMENT.'<br>');
				$feedback .= _XF_DOC_COULDNOTUPDATEDOCUMENT.'<br>';
			}
			else
			{
				//$icmsTpl->assign("feedback",sprintf(_XF_DOC_DOCUMENTTITLEUPDATED, $ts->makeTboxData4Show($title)));
				$feedback .= sprintf(_XF_DOC_DOCUMENTTITLEUPDATED, $ts->addSlashes($title));
			}
			$icmsTpl->assign("content", main_page($group, $group_id));
			$icmsTpl->assign("feedback", $feedback);
			 
		}
		else
		{
			$icmsTpl->assign("docman_header", docman_header($group, $group_id, _XF_DOC_COULDNOTUPDATEDOCUMENT, 'admin'));
			$icmsTpl->assign("content", "Unable to update - Document does not exist, or document's group not the same as that to which your account belongs.");
		}
		include(ICMS_ROOT_PATH."/footer.php");
	}
	elseif(strstr($mode, "groupadd"))
	{
		include(ICMS_ROOT_PATH."/header.php");
		$query = "INSERT INTO ".$icmsDB->prefix("xf_doc_groups")."(groupname,group_id) " ."values('" ."".$ts->makeTboxData4Save($groupname)."'," ."'$group_id')";
		 
		$icmsDB->queryF($query);
		$icmsTpl->assign("feedback", sprintf(_XF_DOC_GROUPGROUPNAMEADDED, $ts->makeTboxData4Show($groupname)));
		$icmsTpl->assign("content", main_page($group, $group_id));
		include(ICMS_ROOT_PATH."/footer.php");
	}
	elseif(strstr($mode, "editgroups"))
	{
		include_once(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("docman_header", docman_header($group, $group_id, _XF_DOC_GROUPEDIT, 'admin'));
		$content = '
			<p><strong> '._XF_DOC_ADDAGROUP.':</strong>
			<form name="addgroup" action="index.php?mode=groupadd&group_id='.$group_id.'" method="POST">
			<table>
			<tr><td><strong>'._XF_DOC_NEWGROUPNAME.':</strong></td>  <td><input type="text" name="groupname"></td><td><input type="submit" value="'._XF_G_ADD.'"></td></tr></table>
			<p>'._XF_DOC_GROUPNAMEWILLBEUSEDASTITLE.'</p>
			</form>
			';
		$content .= display_groups($group_id);
		$icmsTpl->assign("content", $content);
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	elseif(strstr($mode, "editdocs"))
	{
		include_once(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("docman_header", docman_header($group, $group_id, _XF_DOC_EDITDOCUMENTSLIST, 'admin'));
		$icmsTpl->assign("content", main_page($group, $group_id));
		include(ICMS_ROOT_PATH."/footer.php");
		 
	}
	else if($articles != "")
	{
		global $icmsDB;
		if ($articles == "y")
		{
			// Check for a refresh - don't recreate if a refresh
			$sql = "SELECT doc_group" . " FROM " . $icmsDB->prefix("xf_doc_groups")
			. " WHERE groupname='"._XF_DOC_ARTICLES_KEY."'" . " AND group_id='".$group_id."'";
			$result = $icmsDB->query($sql);
			if (1 > $icmsDB->getRowsNum($result))
			{
				$sql = "INSERT INTO " . $icmsDB->prefix("xf_doc_groups")
				. "(groupname, group_id) VALUES" . "('"._XF_DOC_ARTICLES_KEY."','".$group_id."')";
				$result = $icmsDB->queryF($sql);
			}
		}
		else if($articles == "n")
		{
			$sql = "DELETE FROM " . $icmsDB->prefix("xf_doc_groups")
			. " WHERE groupname='"._XF_DOC_ARTICLES_KEY."'" . " AND group_id='".$group_id."'";
			$result = $icmsDB->queryF($sql);
		}
		include(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("content", main_page($group, $group_id));
		include(ICMS_ROOT_PATH."/footer.php");
	}
	else
	{
		include_once(ICMS_ROOT_PATH."/header.php");
		$icmsTpl->assign("content", main_page($group, $group_id));
		include_once(ICMS_ROOT_PATH."/footer.php");
	} //end else
	 
?>