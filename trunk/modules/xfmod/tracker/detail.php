<?php
/**
  *
  * SourceForge Generic Tracker facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: detail.php,v 1.8 2004/02/05 23:28:29 jcox Exp $
  *
  */

$xoopsOption['template_main'] = 'tracker/xfmod_detail.html';

include ("../../../header.php");

$header = $ath->header();
$group =& group_get_object($group_id);

//meta tag information
$metaTitle=" Tracker Detail - ".$group->getPublicName();
$metaKeywords=project_getmetakeywords($group_id);
$metaDescription=str_replace('"', "&quot;", strip_tags($group->getDescription()));

$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
$xoopsTpl->assign("xoops_meta_description", $metaDescription);

//project nav information
$xoopsTpl->assign("project_title", $header['title']);
$xoopsTpl->assign("project_tabs", $header['tabs']);
$xoopsTpl->assign("header", $header['nav']);

    $content =	"<H4>[ #".$ah->getID()." ] ".$ts->makeTareaData4Show($ah->getSummary())."</H4>";

     if ($xoopsUser)
     {
     	$sql = "SELECT id FROM ".$xoopsDB->prefix("xf_artifact_monitor")
     		. " WHERE artifact_id='".$aid."' AND user_id='".$xoopsUser->uid()."'";
     	$result = $xoopsDB->query($sql);
        $content .= "<P><B><A HREF='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&atid=".$ath->getID()."&func=monitor&artifact_id=".$ah->getID()."'>"
           ."<img width='16' height='15' border='0' src='".XOOPS_URL."/modules/xfmod/images/ic/";
        if ( $xoopsDB->getRowsNum($result) < 1 )
        {
        	$content .= "check.png' alt='"._XF_TRK_MONITOR."'>"." "._XF_TRK_MONITOR;
        }
        else
        {
        	$content .= "trash.png' alt='"._XF_TRK_STOPMONITOR."'>"." "._XF_TRK_STOPMONITOR;
        }
        $content .= "</A></B></P>";
     }

     $content .= "<TABLE CELLPADDING='0' WIDTH='100%'>";

     if (!$xoopsUser)
     {
	     $content .= '
			<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">
			<INPUT TYPE="HIDDEN" NAME="func" VALUE="monitor">
			<INPUT TYPE="HIDDEN" NAME="artifact_id" VALUE="'.$ah->getID().'">
		<TR>
			<TD COLSPAN=2">
			<B>'._XF_TRK_EMAIL.':</B> &nbsp;
			<INPUT TYPE="TEXT" NAME="user_email" SIZE="20" MAXLENGTH="40">
			<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_TRK_MONITOR.'">
			</FORM>
			</TD>
		</TR>';
     }

     $content .= '
	<TR>
		<TD><B>'._XF_G_DATE.':</B><BR>'.date( $sys_datefmt, $ah->getOpenDate() ).'</TD>
		<TD><B>'._XF_G_PRIORITY.':</B><BR>'.$ah->getPriority().'</TD>
	</TR>

	<TR>
		<TD><B>'._XF_G_SUBMITTEDBY.':</B><BR>'.$ah->getSubmittedRealName().' ('.$ah->getSubmittedUnixName().')</TD>
		<TD><B>'._XF_G_ASSIGNEDTO.':</B><BR>'.$ah->getAssignedRealName().' ('.$ah->getAssignedUnixName().')</TD>
	</TR>

	<TR>
		<TD><B>'._XF_TRK_CATEGORY.':</B><BR>'.$ah->getCategoryName().'</TD>
		<TD><B>'._XF_TRK_STATUS.':</B><BR>'.$ah->getStatusName().'</TD>
	</TR>

	<TR><TD COLSPAN="2"><B>'._XF_TRK_SUMMARY.':<BR><I>'.$ts->makeTareaData4Show($ah->getSummary()).'</I></B></TD></TR>

	<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">

	<TR><TD COLSPAN="2">
		'.$ts->makeTareaData4Show( $ah->getDetails() ).'
		<INPUT TYPE="HIDDEN" NAME="func" VALUE="postaddcomment">
		<INPUT TYPE="HIDDEN" NAME="artifact_id" VALUE="'.$ah->getID().'">
		<P>
		<B>'._XF_TRK_ADDACOMMENT.':</B><BR>
		<TEXTAREA NAME="details" ROWS="10" COLS="60" WRAP="HARD"></TEXTAREA>
	</TD></TR>

	<TR><TD COLSPAN="2">';

	if (!$xoopsUser) {
		//$loginurl = getLoginURL();

		$content .= '
		<h4><FONT COLOR="RED">Please log in!</FONT></h4><BR>
		'._XF_TRK_IFCANNOTLOGIN.':<P>
		<INPUT TYPE="TEXT" NAME="user_email" SIZE="20" MAXLENGTH="40">';

	}

	$content .= '
		<P>
		<H4>'._XF_TRK_DONOTENTERPASSWORDS.'</H4>
		<P>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_G_SUBMIT.'">
		</FORM>
	</TD></TR>

	<TR><TD COLSPAN="2">
	<H4>'._XF_G_FOLLOWUPS.':</H4>
	<P>'.

	$ah->showMessages()

	.'</TD></TR>

	<TR><TD COLSPAN=2>
	<H4>'._XF_TRK_ATTACHEDFILES.':</H4>';

	//
	//  print a list of files attached to this Artifact
	//
	$file_list =& $ah->getFiles();

	$count=count($file_list);

		$content .= "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>
		    		<TR class='bg2'>
		    		  <TD align='center'><b>"._XF_TRK_NAME."</b></TD>
				  <TD align='center'><b>"._XF_G_DESCRIPTION."</b></TD>
				  <TD align='center'><b>"._XF_TRK_DOWNLOAD."</b></TD>
				</TR>";

	if ($count > 0) {

		for ($i=0; $i<$count; $i++) {
			$content .= '<TR>
			<TD>'.$file_list[$i]->getName().'</TD>
			<TD>'.$ts->makeTareaData4Show($file_list[$i]->getDescription()) .'</TD>
			<TD><A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/download.php?group_id='.$group_id.'&atid='. $ath->getID().'&file_id='.$file_list[$i]->getID().'&aid='. $ah->getID() .'">'._XF_TRK_DOWNLOAD.'</A></TD>
			</TR>';
		}

	} else {
		$content .= '<TR><TD COLSPAN=3>'._XF_TRK_NOFILESATTACHED.'</TD></TR>';
	}

	$content .= '</TABLE>

	</TD></TR>

	<TR>
	<TD COLSPAN="2">
	<H3>'._XF_TRK_CHANGES.':</H3>
	<P>'.

	$ah->showHistory()

	.'</TD>
	</TR>
</TABLE>';

$xoopsTpl->assign("content", $content);

include ("../../../footer.php");

?>