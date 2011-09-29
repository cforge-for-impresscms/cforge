<?php
	//
	// SourceForge: Breaking Down the Barriers to Open Source Development
	// Copyright 1999-2000(c) The SourceForge Crew
	// http://sourceforge.net
	//
	// $Id: index.php,v 1.9 2004/04/05 23:11:17 jcox Exp $
	include_once("../../../../mainfile.php");
	 
	$langfile = "tracker.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/Artifact.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactHtml.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactFile.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactFileHtml.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactType.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactTypeHtml.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactGroup.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactCategory.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactCanned.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactResolution.class.php");
	include(ICMS_ROOT_PATH."/modules/xfmod/language/english/tracker_text_1.php");
	$icmsOption['template_main'] = 'tracker/admin/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if ($group_id && $atid)
	{
		//
		//
		//  UPDATING A PARTICULAR ARTIFACT TYPE
		//
		//
		 
		if (!$perm->isArtifactAdmin())
		{
			redirect_header(ICMS_URL."/", 2, _XF_G_PERMISSIONDENIED."<br />"._XF_TRK_NOTTRACKERMANAGER);
			exit;
		}
		//
		//  Create the ArtifactType object
		//
		$ath = new ArtifactTypeHtml($group, $atid);
		if (!$ath || !is_object($ath))
		{
			redirect_header(ICMS_URL."/", 2, "ERROR<br />ArtifactType could not be created");
			exit;
		}
		if ($ath->isError())
		{
			redirect_header(ICMS_URL."/", 2, "ERROR<br />".$ath->getErrorMessage());
			exit;
		}
		 
		$feedback = '';
		if ($post_changes)
		{
			//
			//  Update the database
			//
			if ($add_cat)
			{
				 
				$ac = new ArtifactCategory($ath);
				if (!$ac || !is_object($ac))
				{
					$feedback .= 'Unable to create ArtifactCategory Object';
				}
				else
				{
					if (!$ac->create($_POST['name'], $_POST['assign_to']))
					{
						$feedback .= ' Error inserting: '.$ac->getErrorMessage();
						$ac->clearError();
					}
					else
					{
						$feedback .= ' '._XF_TRK_CATEGORYINSERTED.' ';
					}
				}
				 
			}
			elseif($add_group)
			{
				 
				$ag = new ArtifactGroup($ath);
				if (!$ag || !is_object($ag))
				{
					$feedback .= 'Unable to create ArtifactGroup Object';
				}
				else
				{
					if (!$ag->create($_POST['name']))
					{
						$feedback .= ' Error inserting: '.$ag->getErrorMessage();
						$ag->clearError();
					}
					else
					{
						$feedback .= ' '._XF_TRK_GROUPINSERTED.' ';
					}
				}
				 
			}
			elseif($add_canned)
			{
				 
				$acr = new ArtifactCanned($ath);
				if (!$acr || !is_object($acr))
				{
					$feedback .= 'Unable to create ArtifactCanned Object';
				}
				else
				{
					if (!$acr->create($title, $body))
					{
						$feedback .= ' Error inserting: '.$acr->getErrorMessage();
						$acr->clearError();
					}
					else
					{
						$feedback .= ' '._XF_TRK_CANNEDRESPONSEINSERTED.' ';
					}
				}
				 
			}
			elseif($add_users)
			{
				 
				//
				// if "add all" option, get list of group members
				// who are not already members of this ArtifactType
				//
				if ($add_all)
				{
					$sql = "SELECT u.uid " ."FROM ".$icmsDB->prefix("users")." u, ".$icmsDB->prefix("xf_user_group")." ug " ."LEFT JOIN ".$icmsDB->prefix("xf_artifact_perm")." ap ON ap.user_id=u.uid AND ap.group_artifact_id='$atid' " ."WHERE ap.user_id IS NULL " ."AND ug.group_id='$group_id' " ."AND u.uid=ug.user_id";
					 
					$addids = util_result_column_to_array($icmsDB->query($sql));
				}
				$count = count($addids);
				for($i = 0; $i < $count; $i++)
				{
					$ath->addUser($addids[$i]);
				}
				if ($ath->isError())
				{
					$feedback .= $ath->getErrorMessage();
					$ath->clearError();
				}
				else
				{
					$feedback .= ' '._XF_TRK_USERSADDED.' ';
				}
				//go to the perms page
				$add_users = false;
				$update_users = true;
				 
			}
			elseif($update_users)
			{
				 
				//
				// Handle the 2-D array of user_id/permission level
				//
				$count = count($updateids);
				for($i = 0; $i < $count; $i++)
				{
					$ath->updateUser($updateids[$i][0], $updateids[$i][1]);
				}
				if ($ath->isError())
				{
					$feedback .= $ath->getErrorMessage();
					$ath->clearError();
				}
				else
				{
					$feedback .= ' '._XF_TRK_USERSUPDATED.' ';
				}
				 
				//
				// Delete the checked ids
				//
				$count = count($deleteids);
				for($i = 0; $i < $count; $i++)
				{
					$ath->deleteUser($deleteids[$i]);
				}
				if ($ath->isError())
				{
					$feedback .= $ath->getErrorMessage();
					$ath->clearError();
				}
				else
				{
					$feedback .= ' '._XF_TRK_USERSDELETED.' ';
				}
				 
			}
			elseif($update_canned)
			{
				 
				$acr = new ArtifactCanned($ath, $id);
				if (!$acr || !is_object($acr))
				{
					$feedback .= 'Unable to create ArtifactCanned Object';
				}
				elseif($acr->isError())
				{
					$feedback .= $acr->getErrorMessage();
				}
				else
				{
					if (!$acr->update($_POST['title'], $_POST['body']))
					{
						$feedback .= ' Error updating: '.$acr->getErrorMessage();
						$acr->clearError();
					}
					else
					{
						$feedback .= ' '._XF_TRK_CANNEDRESPUPDATED.' ';
						$update_canned = false;
						$add_canned = true;
					}
				}
				 
			}
			elseif($update_cat)
			{
				 
				$ac = new ArtifactCategory($ath, $id);
				if (!$ac || !is_object($ac))
				{
					$feedback .= 'Unable to create ArtifactCategory Object';
				}
				elseif($ac->isError())
				{
					$feedback .= $ac->getErrorMessage();
				}
				else
				{
					if (!$ac->update($_POST['name'], $_POST['assign_to']))
					{
						$feedback .= ' Error updating: '.$ac->getErrorMessage();
						$ac->clearError();
					}
					else
					{
						$feedback .= ' '._XF_TRK_CATEGORYUPDATED.' ';
						$update_cat = false;
						$add_cat = true;
					}
				}
				 
			}
			elseif($update_group)
			{
				 
				$ag = new ArtifactGroup($ath, $id);
				if (!$ag || !is_object($ag))
				{
					$feedback .= 'Unable to create ArtifactGroup Object';
				}
				elseif($ag->isError())
				{
					$feedback .= $ag->getErrorMessage();
				}
				else
				{
					if (!$ag->update($_POST['name']))
					{
						$feedback .= ' Error updating: '.$ag->getErrorMessage();
						$ag->clearError();
					}
					else
					{
						$feedback .= ' '._XF_TRK_GROUPUPDATED.' ';
						$update_group = false;
						$add_group = true;
					}
				}
			}
			elseif($update_type)
			{
				if (!$ath->update($name, $description, $is_public, $allow_anon, $email_all, $email_address,
					$due_period, $status_timeout, $use_resolution, $submit_instructions, $browse_instructions))
				{
					$feedback .= ' Error updating: '.$ath->getErrorMessage();
					$ath->clearError();
				}
				else
				{
					$feedback .= ' '._XF_TRK_TRACKERUPDATED.' ';
				}
				 
			}
		}
		 
		 
		//
		// FORMS TO ADD/UPDATE DATABASE
		//
		if ($add_cat)
		{
			//
			//      FORM TO ADD CATEGORIES
			//
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$content = "";
			$content .= "<H4>".sprintf(_XF_TRK_ADDCATEGORIESTO, $ath->getName())."</H4>";
			 
			/*
			List of possible categories for this ArtifactType
			*/
			$result = $ath->getCategories();
			$content .= "<p>";
			$rows = $icmsDB->getRowsNum($result);
			if ($result && $rows > 0)
			{
				$content .= "<table border='2' width='100%' cellpadding='5' cellspacing='1'>" ."<tr class='bg2'>" ."<td align='center'><strong>"._XF_TRK_ID."</strong></td>" ."<td align='center'><strong>"._XF_TRK_TITLE."</strong></td>" ."</tr>";
				 
				for($i = 0; $i < $rows; $i++)
				{
					$content .= "<tr>" ."<td class='".($i%2 != 0?"odd":"even")."'>".unofficial_getDBResult($result, $i, 'id')."</td>" ."<td class='".($i%2 != 0?"odd":"even")."'><a href='".$_SERVER['PHP_SELF']."?update_cat=1&id=" .unofficial_getDBResult($result, $i, 'id')."&group_id=".$group_id."&atid=".$ath->getID()."'>" .$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'category_name'))."</a></td></th>";
				}
				$content .= "</table>";
			}
			else
			{
				$content .= "\r\n<H4>"._XF_TRK_NOCATEGORIESDEF."</H4>";
			}
			 
			$content .= '
				<p>
				<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
				<input type="hidden" name="add_cat" value="y">
				<strong>'._XF_TRK_NEWCATEGORYNAME.':</strong><BR>
				<input type="text" name="name" value="" size="15" maxlength="30"><BR>
				<p>
				<strong>'._XF_TRK_AUTOASSIGNTO.':</strong><BR>
				'.$ath->technicianBox('assign_to').'
				<p>
				<strong><FONT COLOR="RED">'._XF_TRK_ONCEADDCANNOTDELETE.'</FONT></strong>
				<p>
				<input type="submit" name="post_changes" value="'._XF_G_SUBMIT.'">
				</form>';
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
			 
		}
		elseif($add_group)
		{
			//
			//  FORM TO ADD GROUP
			//
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$content = "<H4>".sprintf(_XF_TRK_ADDGROUPSTO, $ath->getName())."</H4>";
			 
			/*
			List of possible groups for this ArtifactType
			*/
			$result = $ath->getGroups();
			$content .= "<p>";
			$rows = $icmsDB->getRowsNum($result);
			if ($result && $rows > 0)
			{
				$content .= "<table border='0' width='100%' cellpadding='5' cellspacing='1'>" ."<tr class='bg2'>" ."<tr align='center'><strong>"._XF_TRK_ID."</strong></th>" ."<tr align='center'><strong>"._XF_TRK_TITLE."</strong></th>" ."</tr>";
				 
				for($i = 0; $i < $rows; $i++)
				{
					$content .= "<tr class='".($i%2 != 0 ? "odd" : "even")."'>" ."<td>".unofficial_getDBResult($result, $i, 'id')."</td>" ."<td><a href='".$_SERVER['PHP_SELF']."?update_group=1&id=" .unofficial_getDBResult($result, $i, 'id')."&group_id=".$group_id."&atid=".$ath->getID()."'>" .$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'group_name'))."</a></td></th>";
				}
				$content .= "</table>";
			}
			else
			{
				$content .= "\r\n<H4>"._XF_TRK_NOGROUPSDEF."</H4>";
			}
			 
			$content .= '
				<p>
				<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
				<input type="hidden" name="add_group" value="y">
				<strong>'._XF_TRK_NEWGROUPNAME.':</strong><BR>
				<input type="text" name="name" value="" size="15" maxlength="30"><BR>
				<p>
				<strong><FONT COLOR="RED">'._XF_TRK_ONCEADDGROUPCANNOTDELETE.'</FONT></strong>
				<p>
				<input type="submit" name="post_changes" value="'._XF_G_SUBMIT.'">
				</form>';
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
			 
		}
		elseif($add_canned)
		{
			//
			//  FORM TO ADD CANNED RESPONSES
			//
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$content = "<H4>".sprintf(_XF_TRK_ADDRESPONSESTO, $ath->getName())."</H4>";
			 
			/*
			List of existing canned responses
			*/
			$result = $ath->getCannedResponses();
			$content .= "<p>";
			$rows = $icmsDB->getRowsNum($result);
			 
			if ($result && $rows > 0)
			{
				$content .= "<table border='0' width='100%' cellpadding='5' cellspacing='1'>" ."<tr class='bg2'>" ."<td align='center'><strong>"._XF_TRK_ID."</strong></td>" ."<td align='center'><strong>"._XF_TRK_TITLE."</strong></td>" ."</th>";
				 
				for($i = 0; $i < $rows; $i++)
				{
					$content .= "<tr class='".($i%2 != 0?"bg2":"bg3")."'>" ."<td>".unofficial_getDBResult($result, $i, 'id')."</td>" ."<td><a href='".$_SERVER['PHP_SELF']."?update_canned=1&id=" .unofficial_getDBResult($result, $i, 'id')."&group_id=".$group_id."&atid=".$ath->getID()."'>" .$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'title'))."</a></td></th>";
				}
				$content .= "</table>";
			}
			else
			{
				$content .= "\r\n<H4>"._XF_TRK_NORESPONSESDEF."</H4>";
			}
			 
			$content .= '
				<p>'._XF_TRK_GENERICMESSAGESISUSEFUL.'
				<p>
				<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
				<input type="hidden" name="add_canned" value="y">
				<strong>'._XF_TRK_TITLE.':</strong><BR>
				<input type="text" name="title" value="" size="50" maxlength="50">
				<p>
				<strong>'._XF_TRK_MESSAGEBODY.':</strong><BR>
				<textarea name="body" rows="30" cols="65" WRAP="HARD"></textarea>
				<p>
				<input type="submit" name="post_changes" value="'._XF_G_SUBMIT.'">
				</form>';
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
			 
		}
		elseif($update_users)
		{
			//
			//  FORM TO ADD/UPDATE USERS
			//
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$content = '';
			 
			$sql = "SELECT ap.id,ap.group_artifact_id,ap.user_id,ap.perm_level,u.uname,u.name " ."FROM ".$icmsDB->prefix("xf_artifact_perm")." ap, ".$icmsDB->prefix("users")." u " ."WHERE u.uid=ap.user_id " ."AND ap.group_artifact_id='".$ath->getID()."'";
			$res = $icmsDB->query($sql);
			 
			if (!$res || $icmsDB->getRowsNum($res) < 1)
			{
				$content = '<H4>'._XF_TRK_NODEVELOPERSFOUND.'</H4>';
			}
			else
			{
				$content = '
					<p>
					'._XF_TRK_TRACKERPERMINFO.'
					<p>
					<dt><strong>'._XF_TRK_TECHNICIANS.'</strong></dt>
					<dd>'._XF_TRK_TECHNICIANSINFO.'</dd>
					 
					<dt><strong>'._XF_TRK_ADMINS.'</strong></dt>
					<dd>'._XF_TRK_ADMINSINFO.'</dd>
					 
					<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="post">
					<input type="hidden" name="update_users" value="y">';
				 
				 
				$content .= "<table border='0' width='100%' cellpadding='5' cellspacing='1'>" ."<tr class='bg2'>" ."<td align='center'><strong>"._XF_G_DELETE."</strong></td>" ."<td align='center'><strong>"._XF_TRK_USERNAME."</strong></td>" ."<td align='center'><strong>"._XF_TRK_PERMISSION."</strong></td>" ."</th>";
				 
				$i = 0;
				//
				// PHP4 allows multi-dimensional arrays to be passed in from form elements
				//
				while ($row_dev = $icmsDB->fetchArray($res))
				{
					$content .= '
						<input type="hidden" name="updateids['.$i.'][0]" value="'.$row_dev['user_id'].'">
						<tr class="'.($i%2 != 0?'bg2':'bg3').'">
						<td><input type="CHECKBOX" name="deleteids[]" value="'.$row_dev['user_id'].'"> '._XF_G_DELETE.'</td>
						 
						<td>'.$row_dev['name'].'('. $row_dev['uname'] .')</td>
						 
						<td><FONT size="-1"><select name="updateids['.$i.'][1]">
						<option value="0"'.(($row_dev['perm_level'] == 0)?" selected":"").'>-
						<option value="1"'.(($row_dev['perm_level'] == 1)?" selected":"").'>'._XF_TRK_TECHNICIAN.'
						<option value="2"'.(($row_dev['perm_level'] == 2)?" selected":"").'>'._XF_TRK_ADMINTECH.'
						<option value="3"'.(($row_dev['perm_level'] == 3)?" selected":"").'>'._XF_TRK_ADMINONLY.'
						</select></FONT></td>
						 
						</th>';
					$i++;
				}
				$content .= '<th><td colspan=3 align=MIDDLE><input type="submit" name="post_changes" value="'._XF_TRK_UPDATEPERMISSIONS.'">
					</form></td></th>';
				$content .= '</table>';
			}
			$content .= '
				<p>
				<h3>'._XF_TRK_ADDTHESEUSERS.':</H3>
				<p>
				'._XF_TRK_ADDTHESEUSERSINFO.'
				<p>
				<CENTER>
				<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="post">
				<input type="hidden" name="add_users" value="y">';
			 
			$sql = "SELECT u.uid, u.uname " ."FROM ".$icmsDB->prefix("users")." u, ".$icmsDB->prefix("xf_user_group")." ug " ."LEFT JOIN ".$icmsDB->prefix("xf_artifact_perm")." ap ON ap.user_id=u.uid AND ap.group_artifact_id='$atid' " ."WHERE ap.user_id IS NULL " ."AND ug.group_id='$group_id' " ."AND u.uid=ug.user_id";
			 
			$res = $icmsDB->query($sql);
			$content .= $icmsDB->error();
			$content .= html_build_multiple_select_box($res, 'addids[]', array(), 8, false);
			$content .= '<p>
				<input type="submit" name="post_changes" value="'._XF_TRK_ADDUSERS.'">&nbsp;<input type="checkbox" name="add_all"> '._XF_TRK_ADDALLUSERS.'
				</form>
				</CENTER>';
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
			 
		}
		elseif($update_canned)
		{
			//
			// FORM TO UPDATE CANNED MESSAGES
			//
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$content = "<H4>".sprintf(_XF_TRK_UPDATECANNEDRESP, $ath->getName())."</H4>";
			$acr = new ArtifactCanned($ath, $id);
			if (!$acr || !is_object($acr))
			{
				$feedback .= 'Unable to create ArtifactCanned Object';
			}
			elseif($acr->isError())
			{
				$feedback .= $acr->getErrorMessage();
			}
			else
			{
				$content .= '
					<p>
					'._XF_TRK_GENERICMESSAGESISUSEFUL.'
					<p>
					<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
					<input type="hidden" name="update_canned" value="y">
					<input type="hidden" name="id" value="'.$acr->getID().'">
					<strong>'._XF_TRK_TITLE.':</strong><BR>
					<input type="text" name="title" value="'.$ts->makeTboxData4Edit($acr->getTitle()).'" size="50" maxlength="50">
					<p>
					<strong>'._XF_TRK_MESSAGEBODY.':</strong><BR>
					<textarea name="body" rows="30" cols="65" WRAP="HARD">'.$ts->makeTareaData4Edit($acr->getBody()).'</textarea>
					<p>
					<input type="submit" name="post_changes" value="'._XF_G_SUBMIT.'">
					</form>';
			}
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
			 
		}
		elseif($update_cat)
		{
			//
			//  FORM TO UPDATE CATEGORIES
			//
			/*
			Allow modification of a artifact category
			*/
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$content = '
				<H4>'.sprintf(_XF_TRK_MODIFYCATEGORYIN, $ath->getName()).'</H4>';
			 
			$ac = new ArtifactCategory($ath, $id);
			if (!$ac || !is_object($ac))
			{
				$feedback .= 'Unable to create ArtifactCategory Object';
			}
			elseif($ac->isError())
			{
				$feedback .= $ac->getErrorMessage();
			}
			else
			{
				$content = '
					<p>
					<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
					<input type="hidden" name="update_cat" value="y">
					<input type="hidden" name="id" value="'.$ac->getID().'">
					<p>
					<strong>'. _XF_TRK_CATEGORYNAME.':</strong><BR>
					<input type="text" name="name" value="'.$ts->makeTboxData4Edit($ac->getName()).'">
					<p>
					<strong><'._XF_TRK_AUTOASSIGNTO.':</strong><BR>
					'.$ath->technicianBox('assign_to', $ac->getAssignee()).'
					<p>
					<input type="submit" name="post_changes" value="'._XF_G_SUBMIT.'">
					</form>';
			}
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
			//$ath->footer();
			 
		}
		elseif($update_group)
		{
			//
			//  FORM TO UPDATE GROUPS
			//
			/*
			Allow modification of a artifact group
			*/
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$ag = new ArtifactGroup($ath, $id);
			if (!$ag || !is_object($ag))
			{
				$feedback .= 'Unable to create ArtifactGroup Object';
			}
			elseif($ag->isError())
			{
				$feedback .= $ag->getErrorMessage();
			}
			else
			{
				$content .= '
					<p>
					<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
					<input type="hidden" name="update_group" value="y">
					<input type="hidden" name="id" value="'.$ag->getID().'">
					<p>
					<strong>'._XF_TRK_GROUPNAME.':</strong><BR>
					<input type="text" name="name" value="'.$ts->makeTboxData4Edit($ag->getName()).'">
					<p>
					<input type="submit" name="post_changes" value="'._XF_G_SUBMIT.'">
					</form>';
			}
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
			 
		}
		elseif($update_type)
		{
			//
			// FORM TO UPDATE ARTIFACT TYPES
			//
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$content .= '
				<p>
				<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
				<input type="hidden" name="update_type" value="y">
				<p>
				<strong>'._XF_TRK_NAME.':</strong>('._XF_TRK_NAMEEXAMPLES.')<BR>';
			if ($ath->getDataType())
			{
				$content .= $ath->getName();
			}
			else
			{
				 
				$content .= '<input type="text" name="name" value="'.$ts->makeTboxData4Edit($ath->getName()).'">';
				 
			}
			$content .= '
				<p>
				<strong>'._XF_G_DESCRIPTION.':</strong><BR>';
			if ($ath->getDataType())
			{
				$content .= $ath->getDescription();
			}
			else
			{
				 
				$content .= '<input type="text" name="description" value="'.$ts->makeTareaData4Edit($ath->getDescription()).'" size="50">';
			}
			 
			$content .= '
				<p>
				<input type=CHECKBOX name="is_public" value="1" '.(($ath->isPublic())?'CHECKED':'').'> <strong>'._XF_TRK_PUBLICLYAVAILABLE.'</strong><BR>
				<input type=CHECKBOX name="allow_anon" value="1" '.(($ath->allowsAnon())?'CHECKED':'').'> <strong>'._XF_TRK_ALLOWNONLOGGEDINPOST.'</strong><BR>
				<input type=CHECKBOX name="use_resolution" value="1" '.(($ath->useResolution())?'CHECKED':'').'> <strong>'._XF_TRK_DISPLAYRESOLUTION.'</strong>
				<p>
				<strong>'._XF_TRK_SENDMAILONSUBMISSION.':</strong><BR>
				<input type="text" name="email_address" value="'.$ath->getEmailAddress().'">
				<p>
				<input type=CHECKBOX name="email_all" value="1" '.(($ath->emailAll())?'CHECKED':'').'> <strong>'._XF_TRK_SENDMAILONCHANGES.'</strong><BR>
				<p>
				<strong>'._XF_TRK_DAYSTILLOVERDUE.':</strong><BR>
				<input type="text" name="due_period" value="'.($ath->getDuePeriod() / 86400).'">
				<p>
				<strong>'._XF_TRK_DAYSTILLTIMEOUT.':</strong><BR>
				<input type="text" name="status_timeout"  value="'.($ath->getStatusTimeout() / 86400).'">
				<p>
				<strong>'._XF_TRK_FREEFORMSUBMIT.':</strong><BR>
				<textarea name="submit_instructions" rows="10" cols="55" WRAP="HARD">'.$ts->makeTareaData4Edit($ath->getSubmitInstructions()).'</textarea>
				<p>
				<strong>'._XF_TRK_FREEFORMBROWSE.':</strong><BR>
				<textarea name="browse_instructions" rows="10" cols="55" WRAP="HARD">'.$ts->makeTareaData4Edit($ath->getBrowseInstructions()).'</textarea>
				<p>
				<input type="submit" name="post_changes" value="'._XF_G_SUBMIT.'">
				</form>';
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
			 
		}
		else
		{
			//
			//  SHOW LINKS TO FEATURES
			//
			include("../../../../header.php");
			$adminheader = $ath->adminHeader();
			 
			$icmsTpl->assign("project_title", $adminheader['title']);
			$icmsTpl->assign("project_tabs", $adminheader['tabs']);
			$icmsTpl->assign("adminheader", $adminheader['nav']);
			 
			$icmsTpl->assign("feedback", $feedback);
			 
			$content .= '<p>
				<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&add_cat=1"><strong>'._XF_TRK_ADDUPDATECATEGORIES.'</strong></a><BR>
				'._XF_TRK_ADDUPDATECATEGORIESINFO.'<p>';
			$content .= '<p>
				<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&add_group=1"><strong>'._XF_TRK_ADDUPDATEGROUPS.'</strong></a><BR>
				'._XF_TRK_ADDUPDATEGROUPSINFO.'<p>';
			$content .= '<p>
				<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&add_canned=1"><strong>'._XF_TRK_ADDUPDATECANNEDRESP.'</strong></a><BR>
				'._XF_TRK_ADDUPDATECANNEDRESPINFO.'<p>';
			$content .= '<p>
				<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&update_users=1"><strong>'._XF_TRK_ADDUPDATEUSERS.'</strong></a><BR>
				'._XF_TRK_ADDUPDATEUSERSINFO.'<p>';
			$content .= '<p>
				<a href="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&update_type=1"><strong>'._XF_TRK_UPDATEPREFERENCES.'</strong></a><BR>
				'._XF_TRK_UPDATEPREFERENCESINFO.'<p>';
			 
			$icmsTpl->assign("content", $content);
			include("../../../../footer.php");
		}
		 
	}
	elseif($group_id)
	{
		 
		if (!$perm->isArtifactAdmin())
		{
			redirect_header(ICMS_URL."/", 2, _XF_G_PERMISSIONDENIED."<br />"._XF_TRK_NOTTRACKERMANAGER);
			exit;
		}
		 
		if ($post_changes)
		{
			//var_dump('post_changes');
			if ($add_at)
			{
				$res = new ArtifactTypeHtml($group);
				if (!$res->create($name, $description, $is_public, $allow_anon, $email_all, $email_address,
					$due_period, $use_resolution, $submit_instructions, $browse_instructions))
				{
					$feedback .= $res->getErrorMessage();
				}
				else
				{
					header("Location: ".ICMS_URL."/modules/xfmod/tracker/admin/?group_id=$group_id&atid=".$res->getID()."&update_users=1");
				}
			}
		}
		 
		include("../../../../header.php");
		 
		$icmsTpl->assign("project_title", project_title($group));
		$icmsTpl->assign("project_tabs", project_tabs('tracker', $group_id));
		$icmsTpl->assign("adminheader", $adminheader['nav']);
		 
		$icmsTpl->assign("feedback", $feedback);
		 
		if ($icmsUser->isAdmin())
		{
			$content = "<strong><a href='index.php?group_id=".$group_id."'>"._XF_G_ADMIN."</a></strong><P/>";
		}
		 
		$sql = "SELECT * FROM ".$icmsDB->prefix("xf_artifact_group_list")." WHERE group_id='$group_id' ORDER BY group_artifact_id";
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		if (!$result || $rows < 1)
		{
			$content .= "<H4>"._XF_TRK_NOTRACKERSFOUND."</H4>";
			$content .= "<p>";
		}
		else
		{
			 
			$content .= '<p>'._XF_TRK_CHOOSEDATATYPETOCHANGE.'<p>';
			 
			/*
			Put the result set(list of forums for this group) into a column with folders
			*/
			 
			for($j = 0; $j < $rows; $j++)
			{
				$content .= '
					<a href="'.ICMS_URL.'/modules/xfmod/tracker/admin/?atid='.unofficial_getDBResult($result, $j, 'group_artifact_id'). '&group_id='.$group_id.'"> <img src="'.ICMS_URL.'/modules/xfmod/images/ic/index.png" width="24" height="24" border="0" alt="index"> &nbsp;'. $ts->makeTboxData4Show(unofficial_getDBResult($result, $j, 'name')).'</a><BR>'. $ts->makeTareaData4Show(unofficial_getDBResult($result, $j, 'description')).'<p>';
			}
		}
		 
		$content .= _TRACKER_TEXT;
		 
		$content .= '
			<p>
			<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'" method="POST">
			<input type="hidden" name="add_at" value="y">
			<p>
			<strong>'._XF_TRK_NAME.':</strong>('._XF_TRK_NAMEEXAMPLES.')<BR>
			<input type="text" name="name" value="">
			<p>
			<strong>'._XF_G_DESCRIPTION.':</strong><BR>
			<input type="text" name="description" value="" size="50">
			<p>
			<input type=CHECKBOX name="is_public" value="1"> <strong>'._XF_TRK_PUBLICLYAVAILABLE.'</strong><BR>
			<input type=CHECKBOX name="allow_anon" value="1"> <strong>'._XF_TRK_ALLOWNONLOGGEDINPOST.'</strong><BR>
			<input type=CHECKBOX name="use_resolution" value="1"> <strong>'._XF_TRK_DISPLAYRESOLUTION.'</strong>
			<p>
			<strong>'._XF_TRK_SENDMAILONSUBMISSION.':</strong><BR>
			<input type="text" name="email_address" value="">
			<p>
			<input type=CHECKBOX name="email_all" value="1"> <strong>'._XF_TRK_SENDMAILONCHANGES.'</strong><BR>
			<p>
			<strong>'._XF_TRK_DAYSTILLOVERDUE.':</strong><BR>
			<input type="text" name="due_period" value="30">
			<p>
			<strong>'._XF_TRK_DAYSTILLTIMEOUT.':</strong><BR>
			<input type="text" name="status_timeout" value="14">
			<p>
			<strong>'._XF_TRK_FREEFORMSUBMIT.':</strong><BR>
			<textarea name="submit_instructions" rows="10" cols="55" WRAP="HARD"></textarea>
			<p>
			<strong>'._XF_TRK_FREEFORMBROWSE.':</strong><BR>
			<textarea name="browse_instructions" rows="10" cols="55" WRAP="HARD"></textarea>
			<p>
			<input type="submit" name="post_changes" value="'._XF_G_SUBMIT.'">
			</form>';
		 
		$icmsTpl->assign("content", $content);
		 
		include("../../../../footer.php");
		 
	}
	else
	{
		 
		//browse for group first message
		redirect_header(ICMS_URL."/", 2, "ERROR<br />No Group");
		exit;
	}
	 
?>