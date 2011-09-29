<?php
	/**
	*
	* SourceForge Generic Tracker facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: mod.php,v 1.6 2004/06/01 20:22:14 devsupaul Exp $
	*
	*/
	 
	 
	$icmsOption['template_main'] = 'tracker/xfmod_mod.html';
	 
	include("../../../header.php");
	 
	$header = $ath->header();
	 
	$icmsTpl->assign("project_title", $header['title']);
	$icmsTpl->assign("project_tabs", $header['tabs']);
	$icmsTpl->assign("header", $header['nav']);
	 
	$content = "<H4>[ #".$ah->getID()." ] ".$ts->makeTboxData4Show($ah->getSummary())."</H4>";
	 
	$sql = "SELECT id FROM ".$icmsDB->prefix("xf_artifact_monitor")
	. " WHERE artifact_id='".$aid."' AND user_id='".$icmsUser->uid()."'";
	$result = $icmsDB->query($sql);
	$content .= "<p><strong><a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&atid=".$ath->getID()."&func=monitor&artifact_id=".$ah->getID()."'>" ."<img width='16' height='15' border'0' src='".ICMS_URL."/modules/xfmod/images/ic/";
	if ($icmsDB->getRowsNum($result) < 1)
	{
		$content .= "check.png' alt='"._XF_TRK_MONITOR."'> "._XF_TRK_MONITOR;
	}
	else
	{
		$content .= "trash.png' alt='"._XF_TRK_STOPMONITOR."'> "._XF_TRK_STOPMONITOR;
	}
	$content .= "</a></strong></p>";
	 
	$content .= '
		<table width="100%">
		 
		<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="func" value="postmod">
		<input type="hidden" name="artifact_id" value="'.$ah->getID().'">
		 
		<th>
		<td><strong>'._XF_TRK_SUBMITTEDBY.':</strong><BR>'.$ah->getSubmittedRealName().'(<tt>'.$ah->getSubmittedUnixName().'</tt>)</td>
		<td><strong>'._XF_TRK_DATESUBMITTED.':</strong><BR>
		'.date($sys_datefmt, $ah->getOpenDate());
	 
	$close_date = $ah->getCloseDate();
	if ($ah->getStatusID() == 2 && $close_date > 1)
	{
		$content .= '<BR><strong>'._XF_TRK_CLOSEDATE.':</strong><BR>' .date($sys_datefmt, $close_date);
	}
	 
	$content .= '
		</td>
		</th>
		 
		<th>
		<td><strong>'._XF_TRK_DATATYPE.':</strong><BR>';
	//
	//  kinda messy - but works for now
	//  need to get list of data types this person can admin
	//
	if ($ath->userIsAdmin())
	{
		$res = $group->getArtifactTypes();
	}
	else
	{
		$sql = "SELECT agl.group_artifact_id,agl.name " ."FROM ".$icmsDB->prefix("xf_artifact_group_list")." agl,".$icmsDB->prefix("xf_artifact_perm")." ap " ."WHERE agl.group_artifact_id=ap.group_artifact_id " ."AND ap.user_id='".$icmsUser->getVar("uid")."' " ."AND ap.perm_level > 1 " ."AND agl.group_id='$group_id'";
		 
		$res = $icmsDB->query($sql);
	}
	$content .= html_build_select_box($res, 'new_artfact_type_id', $ath->getID(), false);
	 
	$content .= '
		</td>
		<td>
		<input type="submit" name="submit" value="'._XF_G_CHANGE.'">
		</td>
		</th>
		 
		<th>
		<td><strong>'._XF_TRK_CATEGORY.':</strong><BR>';
	 
	 
	$content .= $ath->categoryBox('category_id', $ah->getCategoryID());
	if ($ath->userIsAdmin())
	{
		$content .= '&nbsp;<a href="'.ICMS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='. $ath->getID() .'&add_cat=1">('._XF_TRK_ADMINSMALL.')</a>';
	}
	 
	$content .= '
		</td>
		<td><strong>'._XF_TRK_GROUP.':</strong><BR>';
	 
	$content .= $ath->artifactGroupBox('artifact_group_id', $ah->getArtifactGroupID());
	if ($ath->userIsAdmin())
	{
		$content .= '&nbsp;<a href="'.ICMS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='. $ath->getID() .'&add_group=1">('._XF_TRK_ADMINSMALL.')</a>';
	}
	 
	$content .= '
		</td>
		</th>
		 
		<th>
		<td><strong>'._XF_G_ASSIGNEDTO.':</strong><BR>';
	 
	$content .= $ath->technicianBox('assigned_to', $ah->getAssignedTo());
	if ($ath->userIsAdmin())
	{
		$content .= '&nbsp;<a href="'.ICMS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='. $ath->getID() .'&update_users=1">('._XF_TRK_ADMINSMALL.')</a>';
	}
	 
	$content .= '
		</td><td>
		<strong>'._XF_G_PRIORITY.':</strong><BR>';
	 
	/*
	Priority of this request
	*/
	$content .= build_priority_select_box('priority', $ah->getPriority());
	 
	$content .= '
		</td>
		</th>
		 
		<th>
		<td>
		<strong>'._XF_TRK_STATUS.':</strong><BR>';
	 
	 
	$content .= $ath->statusBox('status_id', $ah->getStatusID());
	 
	$content .= '
		</td>
		<td>';
	 
	if ($ath->useResolution())
	{
		$content .= '
			<strong>'._XF_TRK_RESOLUTION.':</strong><BR>';
		$content .= $ath->resolutionBox('resolution_id', $ah->getResolutionID());
	}
	else
	{
		$content .= '&nbsp;
			<input type="hidden" name="resolution_id" value="100">';
	}
	 
	$content .= '
		</td>
		</th>
		 
		<th>
		<td colspan="2"><strong>'._XF_TRK_SUMMARY.':</strong><BR>
		<input type="text" name="summary" size="45" value="'.$ts->makeTareaData4Edit($ah->getSummary()).'">
		</td>
		</th>
		 
		<th><td colspan="2">
		'.$ts->makeTareaData4Show($ah->getDetails()).'
		</td></th>
		 
		<th><td colspan="2">
		<strong>'._XF_TRK_USECANNEDRESPONSE.':</strong><BR>';
	 
	$content .= $ath->cannedResponseBox('canned_response');
	if ($ath->userIsAdmin())
	{
		$content .= '&nbsp;<a href="'.ICMS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='. $ath->getID() .'&add_canned=1">('._XF_TRK_ADMINSMALL.')</a>';
	}
	 
	$content .= '
		<p>
		<strong>'._XF_TRK_ORATTACHCOMMENT.':</strong><BR>
		<textarea name="details" rows="7" cols="60" WRAP="HARD"></textarea>
		<p>
		<H3>'._XF_G_FOLLOWUPS.':</H3>
		<p>';
	 
	$content .= $ah->showMessages();
	 
	$content .= '
		</td></th>
		 
		<th><td colspan=2>
		<strong>'._XF_TRK_CHECKTOUPLOAD.':</strong> <input type="checkbox" name="add_file" value="1">
		<BR>
		<p>
		<input type="file" name="input_file" size="30">
		<p>
		<strong>'._XF_TRK_FILEDESCRIPTION.':</strong><BR>
		<input type="text" name="file_description" size="40" maxlength="255">
		<p>
		<H4>'._XF_TRK_EXISTINGFILES.':</H4>';
	 
	//
	// print a list of files attached to this Artifact
	//
	$file_list = $ah->getFiles();
	 
	$count = count($file_list);
	 
	$content .= "<table border='0' width='100%' cellpadding='5' cellspacing='1'>" ."<th class='bg2'>" ."<td align='center'><strong>"._XF_G_DELETE."</strong></td>" ."<td align='center'><strong>"._XF_TRK_NAME."</strong></td>" ."<td align='center'><strong>"._XF_G_DESCRIPTION."</strong></td>" ."<td align='center'><strong>"._XF_TRK_DOWNLOAD."</strong></td>" ."</th>";
	 
	if ($count > 0)
	{
		 
		for($i = 0; $i < $count; $i++)
		{
			$content .= '<th><td><input type="CHECKBOX" name="delete_file[]" value="'. $file_list[$i]->getID() .'"> Delete</td>'. '<td>'.$file_list[$i]->getName().'</td>
				<td>'.$ts->makeTareaData4Show($file_list[$i]->getDescription()).'</td>
				<td><a href="'.ICMS_URL.'/modules/xfmod/tracker/download.php?group_id='.$group_id.'&atid='. $ath->getID() .'&file_id='.$file_list[$i]->getID().'&aid='. $ah->getID() .'">'._XF_TRK_DOWNLOAD.'</a></td></th>';
		}
		 
	}
	else
	{
		$content .= '<th><td colspan=3>'._XF_TRK_NOFILESATTACHED.'</td></th>';
	}
	 
	$content .= '
		</table>
		</td><th>
		 
		<th><td colspan="2">
		<H4>'._XF_TRK_CHANGES.':</H4>';
	 
	$content .= $ah->showHistory();
	$content .= '
		</td></th>
		 
		<th><td colspan="2" align="MIDDLE">
		<input type="submit" name="submit" value="'._XF_G_CHANGE.'">
		</form>
		</td></th>
		 
		</table>';
	 
	$icmsTpl->assign("content", $content);
	 
	include("../../../footer.php");
	 
?>