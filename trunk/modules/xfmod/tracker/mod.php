<?php
/**
  *
  * SourceForge Generic Tracker facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: mod.php,v 1.6 2004/06/01 20:22:14 devsupaul Exp $
  *
  */


$xoopsOption['template_main'] = 'tracker/xfmod_mod.html';

include ("../../../header.php");

$header = $ath->header();

$xoopsTpl->assign("project_title", $header['title']);
$xoopsTpl->assign("project_tabs", $header['tabs']);
$xoopsTpl->assign("header", $header['nav']);

        $content = "<H4>[ #".$ah->getID()." ] ".$ts->makeTboxData4Show($ah->getSummary())."</H4>";

     	$sql = "SELECT id FROM ".$xoopsDB->prefix("xf_artifact_monitor")
     		. " WHERE artifact_id='".$aid."' AND user_id='".$xoopsUser->uid()."'";
     	$result = $xoopsDB->query($sql);
    	$content .= "<P><B><A HREF='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&atid=".$ath->getID()."&func=monitor&artifact_id=".$ah->getID()."'>"
        ."<img width='16' height='15' border'0' src='".XOOPS_URL."/modules/xfmod/images/ic/";
        if ( $xoopsDB->getRowsNum($result) < 1 )
       	{
       		$content .= "check.png' alt='"._XF_TRK_MONITOR."'> "._XF_TRK_MONITOR;
       	}
       	else
       	{
       		$content .= "trash.png' alt='"._XF_TRK_STOPMONITOR."'> "._XF_TRK_STOPMONITOR;
       	}
       	$content .= "</A></B></P>";

	$content .= '
	<TABLE WIDTH="100%">

	<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST" enctype="multipart/form-data">
	<INPUT TYPE="HIDDEN" NAME="func" VALUE="postmod">
	<INPUT TYPE="HIDDEN" NAME="artifact_id" VALUE="'.$ah->getID().'">

	<TR>
		<TD><B>'._XF_TRK_SUBMITTEDBY.':</B><BR>'.$ah->getSubmittedRealName().' (<tt>'.$ah->getSubmittedUnixName().'</tt>)</TD>
		<TD><B>'._XF_TRK_DATESUBMITTED.':</B><BR>
		'.date($sys_datefmt, $ah->getOpenDate());

		$close_date = $ah->getCloseDate();
		if ($ah->getStatusID()==2 && $close_date > 1) {
			$content .= '<BR><B>'._XF_TRK_CLOSEDATE.':</B><BR>'
			     .date($sys_datefmt, $close_date);
		}

	$content .= '
		</TD>
	</TR>

	<TR>
		<TD><B>'._XF_TRK_DATATYPE.':</B><BR>';
//
//  kinda messy - but works for now
//  need to get list of data types this person can admin
//
	if ($ath->userIsAdmin()) {
		$res = $group->getArtifactTypes();
	} else {
		$sql = "SELECT agl.group_artifact_id,agl.name "
		      ."FROM ".$xoopsDB->prefix("xf_artifact_group_list")." agl,".$xoopsDB->prefix("xf_artifact_perm")." ap "
					."WHERE agl.group_artifact_id=ap.group_artifact_id "
					."AND ap.user_id='".$xoopsUser->getVar("uid")."' "
					."AND ap.perm_level > 1 "
					."AND agl.group_id='$group_id'";

		$res = $xoopsDB->query($sql);
	}
	$content .= html_build_select_box ($res,'new_artfact_type_id',$ath->getID(),false);

	$content .= '
		</TD>
		<TD>
			<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_G_CHANGE.'">
		</TD>
	</TR>

	<TR>
		<TD><B>'._XF_TRK_CATEGORY.':</B><BR>';


	$content .= $ath->categoryBox('category_id', $ah->getCategoryID() );
	if ($ath->userIsAdmin()) {
	  $content .= '&nbsp;<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='. $ath->getID() .'&add_cat=1">('._XF_TRK_ADMINSMALL.')</A>';
	}

	$content .= '
		</TD>
		<TD><B>'._XF_TRK_GROUP.':</B><BR>';

		$content .= $ath->artifactGroupBox('artifact_group_id', $ah->getArtifactGroupID() );
		if ($ath->userIsAdmin()) {
		  $content .= '&nbsp;<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='. $ath->getID() .'&add_group=1">('._XF_TRK_ADMINSMALL.')</A>';
		}

	$content .= '
		</TD>
	</TR>

	<TR>
		<TD><B>'._XF_G_ASSIGNEDTO.':</B><BR>';

		$content .= $ath->technicianBox('assigned_to', $ah->getAssignedTo() );
		if ($ath->userIsAdmin()) {
		  $content .= '&nbsp;<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='. $ath->getID() .'&update_users=1">('._XF_TRK_ADMINSMALL.')</A>';
		}

	$content .= '
		</TD><TD>
		<B>'._XF_G_PRIORITY.':</B><BR>';

		/*
			Priority of this request
		*/
	$content .= build_priority_select_box('priority',$ah->getPriority());

	$content .= '
		</TD>
	</TR>

	<TR>
		<TD>
		<B>'._XF_TRK_STATUS.':</B><BR>';


		$content .= $ath->statusBox ('status_id', $ah->getStatusID() );

		$content .= '
		</TD>
		<TD>';

		if ($ath->useResolution()) {
			$content .= '
			<B>'._XF_TRK_RESOLUTION.':</B><BR>';
			$content .= $ath->resolutionBox('resolution_id',$ah->getResolutionID());
		} else {
			$content .= '&nbsp;
			<INPUT TYPE="HIDDEN" NAME="resolution_id" VALUE="100">';
		}

	$content .= '
		</TD>
	</TR>

	<TR>
		<TD COLSPAN="2"><B>'._XF_TRK_SUMMARY.':</B><BR>
		<INPUT TYPE="TEXT" NAME="summary" SIZE="45" VALUE="'.$ts->makeTareaData4Edit($ah->getSummary()).'">
		</TD>
	</TR>

	<TR><TD COLSPAN="2">
		'.$ts->makeTareaData4Show($ah->getDetails()).'
	</TD></TR>

	<TR><TD COLSPAN="2">
		<B>'._XF_TRK_USECANNEDRESPONSE.':</B><BR>';

		$content .= $ath->cannedResponseBox('canned_response');
		if ($ath->userIsAdmin()) {
		  $content .= '&nbsp;<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='. $ath->getID() .'&add_canned=1">('._XF_TRK_ADMINSMALL.')</A>';
		}

		$content .= '
		<P>
		<B>'._XF_TRK_ORATTACHCOMMENT.':</B><BR>
		<TEXTAREA NAME="details" ROWS="7" COLS="60" WRAP="HARD"></TEXTAREA>
		<P>
		<H3>'._XF_G_FOLLOWUPS.':</H3>
		<P>';

		$content .= $ah->showMessages();

	$content .= '
	</TD></TR>

	<TR><TD COLSPAN=2>
		<B>'._XF_TRK_CHECKTOUPLOAD.':</B> <input type="checkbox" name="add_file" VALUE="1">
		<BR>
		<P>
		<input type="file" name="input_file" size="30">
		<P>
		<B>'._XF_TRK_FILEDESCRIPTION.':</B><BR>
		<input type="text" name="file_description" size="40" maxlength="255">
		<P>
		<H4>'._XF_TRK_EXISTINGFILES.':</H4>';

		//
		//	print a list of files attached to this Artifact
		//
		$file_list =& $ah->getFiles();

		$count=count($file_list);

		$content .= "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
		    ."<TR class='bg2'>"
				."<TD align='center'><b>"._XF_G_DELETE."</b></td>"
				."<TD align='center'><b>"._XF_TRK_NAME."</b></td>"
				."<td align='center'><b>"._XF_G_DESCRIPTION."</b></td>"
				."<TD align='center'><b>"._XF_TRK_DOWNLOAD."</b></td>"
				."</TR>";

		if ($count > 0) {

			for ($i=0; $i<$count; $i++) {
				$content .= '<TR><TD><INPUT TYPE="CHECKBOX" NAME="delete_file[]" VALUE="'. $file_list[$i]->getID() .'"> Delete</TD>'.
				'<TD>'.$file_list[$i]->getName().'</TD>
				<TD>'.$ts->makeTareaData4Show($file_list[$i]->getDescription()).'</TD>
				<TD><A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/download.php?group_id='.$group_id.'&atid='. $ath->getID() .'&file_id='.$file_list[$i]->getID().'&aid='. $ah->getID() .'">'._XF_TRK_DOWNLOAD.'</A></TD></TR>';
			}

		} else {
			$content .= '<TR><TD COLSPAN=3>'._XF_TRK_NOFILESATTACHED.'</TD></TR>';
		}

		$content .= '
		</TABLE>
	</TD><TR>

	<TR><TD COLSPAN="2">
		<H4>'._XF_TRK_CHANGES.':</H4>';

			$content .= $ah->showHistory();
	$content .= '
	</TD></TR>

	<TR><TD COLSPAN="2" ALIGN="MIDDLE">
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_G_CHANGE.'">
		</FORM>
	</TD></TR>

	</TABLE>';

$xoopsTpl->assign("content", $content);

include ("../../../footer.php");

?>