<?php
	/**
	*
	* SourceForge Generic Tracker facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.12 2004/01/30 18:05:06 jcox Exp $
	*
	*/
	include_once("../../../mainfile.php");
	 
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
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	// get current information
	$group = group_get_object($group_id);
	if (!$group || !is_object($group) || $group->isError())
	{
		redirect_header(ICMS_URL."/", 2, "ERROR<br />No Group");
		exit;
	}
	 
	$perm = $group->getPermission($icmsUser);
	$feedback = '';
	 
	if (isset($_POST['atid']))
	$atid = $_POST['atid'];
	elseif(isset($_GET['atid']))
	$atid = $_GET['atid'];
	else
		$atid = null;
	 
	if (isset($_POST['func']))
	$func = $_POST['func'];
	elseif(isset($_GET['func']))
	$func = $_GET['func'];
	else
		$func = null;
	 
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
	 
	 
	if ($group_id && $atid)
	{
		//
		// Create the ArtifactType object
		//
		$ath = new ArtifactTypeHtml($group, $atid);
		if (!$ath || !is_object($ath))
		{
			redirect_header($_SERVER['HTTP_REFERER'], 2, "ERROR<br />ArtifactType could not be created");
			exit;
		}
		if ($ath->isError())
		{
			redirect_header($_SERVER['HTTP_REFERER'], 2, "ERROR<br />".$ath->getErrorMessage());
			exit;
		}
		switch($func)
		{
			case 'add' :
			{
				include(ICMS_ROOT_PATH."/modules/xfmod/tracker/add.php");
				break;
			}
			case 'postadd' :
			{
				/*
				Create a new Artifact
				*/
				$ah = new ArtifactHtml($ath);
				if (!$ah || !is_object($ah))
				{
					redirect_header($_SERVER['HTTP_REFERER'], 2, "ERROR<br />Artifact could not be created");
					exit;
				}
				else
				{
					if (empty($user_email))
					{
						$user_email = false;
					}
					else
					{
						if (!checkEmail($user_email))
						{
							redirect_header($_SERVER["HTTP_REFERER"], 2, _XF_TRK_INVALIDMAIL);
							exit;
						}
					}
					if (!$ah->create($category_id, $artifact_group_id, $summary, $details, $assigned_to, $priority, $user_email))
					{
						redirect_header($_SERVER["HTTP_REFERER"], 2, $ah->getErrorMessage());
						exit;
					}
					else
					{
						//
						// Attach file to this Artifact.
						//
						if ($add_file)
						{
							$afh = new ArtifactFileHtml($ah);
							if (!$afh || !is_object($afh))
							{
								$feedback .= 'Could Not Create File Object';
							}
							else
							{
								if (!$afh->upload($input_file, $input_file_name, $input_file_type, $file_description))
								{
									$feedback .= ' Could Not Attach File to Item: '.$afh->getErrorMessage().'<br />';
								}
							}
						}
						$feedback .= ' '._XF_TRK_ITEMCREATED.' ';
						include(ICMS_ROOT_PATH."/modules/xfmod/tracker/browse.php");
					}
				}
				break;
			}
			case 'massupdate' :
			{
				$count = count($artifact_id_list);
				 
				$artifact_type_id = $ath->getID();
				 
				for($i = 0; $i < $count; $i++)
				{
					$ah = new Artifact($ath, $artifact_id_list[$i]);
					if (!$ah || !is_object($ah))
					{
						$feedback .= ' ID: '.$artifact_id_list[$i].'::Artifact Could Not Be Created';
					}
					else if($ah->isError())
					{
						$feedback .= ' ID: '.$artifact_id_list[$i].'::'.$ah->getErrorMessage();
					}
					else
					{
						 
						$_priority = (($priority != 100) ? $priority : $ah->getPriority());
						$_status_id = (($status_id != 100) ? $status_id : $ah->getStatusID());
						$_category_id = (($category_id != 100) ? $category_id : $ah->getCategoryID());
						$_artifact_group_id = (($artifact_group_id != 100) ? $artifact_group_id : $ah->getArtifactGroupID());
						$_resolution_id = (($resolution_id != 100) ? $resolution_id : $ah->getResolutionID());
						$_assigned_to = (($assigned_to != 100) ? $assigned_to : $ah->getAssignedTo());
						$_summary = $ah->getSummary();
						 
						if (!$ah->update($_priority, $_status_id, $_category_id, $_artifact_group_id, $_resolution_id, $_assigned_to, $_summary, $canned_response, '', $artifact_type_id))
						{
							$was_error = true;
							$feedback .= ' ID: '.$artifact_id_list[$i].'::'.$ah->getErrorMessage();
						}
					}
					unset($ah);
				}
				if (!$was_error)
				{
					$feedback = _XF_TRK_ITEMUPDATED.' ';
				}
				include(ICMS_ROOT_PATH."/modules/xfmod/tracker/browse.php");
				break;
			}
			case 'postmod' :
			{
				/*
				Modify an Artifact
				*/
				$ah = new ArtifactHtml($ath, $artifact_id);
				if (!$ah || !is_object($ah))
				{
					$feedback .= 'ERROR, Artifact Could Not Be Created';
					exit;
				}
				else if($ah->isError())
				{
					$feedback .= 'ERROR, '.$ah->getErrorMessage();
					exit;
				}
				else
				{
					if (!$ah->update($priority, $status_id, $category_id, $artifact_group_id, $resolution_id,
						$assigned_to, $summary, $canned_response, $details, $new_artfact_type_id))
					{
						$feedback .= 'Tracker Item: '.$ah->getErrorMessage();
						$ah->clearError();
						$was_error = true;
					}
					 
					//
					//  Attach file to this Artifact.
					//
					if ($add_file)
					{
						$afh = new ArtifactFileHtml($ah);
						if (!$afh || !is_object($afh))
						{
							$feedback .= 'Could Not Create File Object';
						}
						else
						{
							if (!$afh->upload($input_file, $input_file_name, $input_file_type, $file_description))
							{
								$feedback .= ' <BR>'._XF_TRK_FILEUPLOAD.': '.$afh->getErrorMessage();
								$was_error = true;
							}
							else
							{
								$feedback .= ' <BR>'._XF_TRK_FILEUPLOAD.': '._XF_TRK_SUCCESSFUL.' ';
							}
						}
					}
					 
					//
					// Delete list of files from this artifact
					//
					if ($delete_file)
					{
						$count = count($delete_file);
						for($i = 0; $i < $count; $i++)
						{
							$afh = new ArtifactFileHtml($ah, $delete_file[$i]);
							if (!$afh || !is_object($afh))
							{
								$feedback .= 'Could Not Create File Object::'.$delete_file[$i];
							}
							elseif($afh->isError())
							{
								$feedback .= $afh->getErrorMessage().'::'.$delete_file[$i];
							}
							else
							{
								if (!$afh->delete())
								{
									$feedback .= ' <BR>'._XF_TRK_FILEDELETE.': '.$afh->getErrorMessage();
									$was_error = true;
								}
								else
								{
									$feedback .= ' <BR>'._XF_TRK_FILEDELETE.': '._XF_TRK_SUCCESSFUL.' ';
								}
							}
						}
					}
					//
					// Show just one feedback entry if no errors
					//
					if (!$was_error)
					{
						$feedback = _XF_TRK_ITEMUPDATED;
					}
					include(ICMS_ROOT_PATH."/modules/xfmod/tracker/browse.php");
				}
				break;
			}
			case 'postaddcomment' :
			{
				/*
				Attach a comment to an artifact
				 
				Used by non-admins
				*/
				$ah = new ArtifactHtml($ath, $artifact_id);
				if (!$ah || !is_object($ah))
				{
					$feedback .= 'ERROR, Artifact Could Not Be Created';
					exit;
				}
				else if($ah->isError())
				{
					$feedback .= 'ERROR, '.$ah->getErrorMessage();
					exit;
				}
				else
				{
					if ($ah->addMessage($details, $user_email, true))
					{
						$feedback .= _XF_TRK_COMMENTADDED;
						include(ICMS_ROOT_PATH."/modules/xfmod/tracker/browse.php");
					}
					else
					{
						//some kind of error in creation
						$feedback .= 'ERROR '.$feedback;
					}
				}
				break;
			}
			case 'monitor' :
			{
				$ah = new ArtifactHtml($ath, $artifact_id);
				if (!$ah || !is_object($ah))
				{
					$feedback .= 'ERROR, Artifact Could Not Be Created';
					exit;
				}
				else if($ah->isError())
				{
					$feedback .= 'ERROR, '.$ah->getErrorMessage();
					exit;
				}
				else
				{
					$ah->setMonitor($user_email);
					$feedback = $ah->getErrorMessage();
					include(ICMS_ROOT_PATH."/modules/xfmod/tracker/browse.php");
				}
				break;
			}
			case 'browse' :
			{
				include(ICMS_ROOT_PATH."/modules/xfmod/tracker/browse.php");
				 
				// The line below was for the remedy integration
				//   include(ICMS_ROOT_PATH."/modules/xfmod/tracker/Tracking.php");
				 
				break;
			}
			case 'detail' :
			{
				//
				// users can modify their own tickets if they submitted them
				// even if they are not artifact admins
				//
				 
				$ah = new ArtifactHtml($ath, $aid);
				if (!$ah || !is_object($ah))
				{
					$feedback .= 'ERROR<br />Artifact Could Not Be Created';
					//exit;
				}
				else if($ah->isError())
				{
					$feedback .= 'ERROR<br />'.$ah->getErrorMessage();
					//exit;
				}
				else
				{
					if ($ath->userIsAdmin() || ($icmsUser && ($ah->getSubmittedBy() == $icmsUser->getVar("uid"))))
					{
						include(ICMS_ROOT_PATH."/modules/xfmod/tracker/mod.php");
					}
					else
					{
						include(ICMS_ROOT_PATH."/modules/xfmod/tracker/detail.php");
					}
				}
				break;
			}
			default :
			{
				include(ICMS_ROOT_PATH."/modules/xfmod/tracker/browse.php");
				break;
			}
		}
		 
		$icmsTpl->assign("feedback", $feedback);
		 
	}
	elseif($group_id)
	{
		 
		$icmsOption['template_main'] = 'tracker/xfmod_index.html';
		include("../../../header.php");
		 
		//meta tag information
		$metaTitle = " Trackers - ".$group->getPublicName();
		$metaKeywords = project_getmetakeywords($group_id);
		$metaDescription = str_replace('"', "&quot;", strip_tags($group->getDescription()));
		 
		$icmsTpl->assign("icms_pagetitle", $metaTitle);
		$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
		$icmsTpl->assign("icms_meta_description", $metaDescription);
		 
		//project nav information
		$icmsTpl->assign("project_title", project_title($group));
		$icmsTpl->assign("project_tabs", project_tabs('tracker', $group_id));
		 
		if ($perm->isAdmin())
		{
			$content = "<p><strong><a href='admin/index.php?group_id=".$group_id."'>"._XF_G_ADMIN."</a></strong><P/>";
		}
		 
		//
		// get a list of artifact types they have defined
		//
		if ($icmsUser && $group->isMemberOfGroup($icmsUser))
		{
			$public_flag = '0,1';
		}
		else
		{
			$public_flag = '1';
		}
		 
		$sql = "SELECT agl.*,aca.count,aca.open_count " ."FROM ".$icmsDB->prefix("xf_artifact_group_list")." agl " ."LEFT JOIN ".$icmsDB->prefix("xf_artifact_counts_agg")." aca USING(group_artifact_id) " ."WHERE agl.group_id='$group_id' " ."AND agl.is_public IN($public_flag) " ."ORDER BY group_artifact_id ASC";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		if (!$result || $rows < 1)
		{
			redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_TRK_NOACCESSTRACKERSFOUND);
			exit;
		}
		else
		{
			$content .= '
				<p>
				'._XF_TRK_CHOOSETRACKER.'
				<p>';
			 
			/*
			Put the result set(list of forums for this group) into a column with folders
			*/
			 
			for($j = 0; $j < $rows; $j++)
			{
				$content .= "<a href='".ICMS_URL."/modules/xfmod/tracker/?atid=".unofficial_getDBResult($result, $j, 'group_artifact_id')."&group_id=".$group_id."&func=browse'>" ."<img src='".ICMS_URL."/modules/xfmod/images/ic/index.png' width='24' height='24' border='0' alt='index'> &nbsp;" .$ts->makeTboxData4Show(unofficial_getDBResult($result, $j, 'name'))."</a> " ."(<strong>".unofficial_getDBResult($result, $j, 'open_count') ." "._XF_TRK_OPEN." / ". unofficial_getDBResult($result, $j, 'count') ." "._XF_TRK_TOTAL."</strong>)<BR>" .$ts->makeTboxData4Show(unofficial_getDBResult($result, $j, 'description'))."<p>";
				 
			}
		}
		$icmsTpl->assign("content", $content);
		 
	}
	else
	{
		//echo "bla";
		// exit_no_group();
		 
	}
	 
	include("../../../footer.php");
	 
?>