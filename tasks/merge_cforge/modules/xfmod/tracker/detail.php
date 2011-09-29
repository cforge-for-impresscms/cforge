<?php
/**
*
* SourceForge Generic Tracker facility
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001(c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: detail.php,v 1.8 2004/02/05 23:28:29 jcox Exp $
*
*/
 
$icmsOption['template_main'] = 'tracker/xfmod_detail.html';
 
if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
 
include("../../../header.php");
 
$header = $ath->header();
$group = group_get_object($group_id);
 
//meta tag information
$metaTitle = " Tracker Detail - ".$group->getPublicName();
$metaKeywords = project_getmetakeywords($group_id);
$metaDescription = str_replace('"', "&quot;", strip_tags($group->getDescription()));
 
$icmsTpl->assign("icms_pagetitle", $metaTitle);
$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
$icmsTpl->assign("icms_meta_description", $metaDescription);
 
//project nav information
$icmsTpl->assign("project_title", $header['title']);
$icmsTpl->assign("project_tabs", $header['tabs']);
$icmsTpl->assign("header", $header['nav']);
 
$content = "<H4>[ #".$ah->getID()." ] ".$ts->makeTareaData4Show($ah->getSummary())."</H4>";
 
if ($icmsUser)
{
	$sql = "SELECT id FROM ".$icmsDB->prefix("xf_artifact_monitor")
	. " WHERE artifact_id='".$aid."' AND user_id='".$icmsUser->uid()."'";
	$result = $icmsDB->query($sql);
	$content .= "<p><strong><a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&atid=".$ath->getID()."&func=monitor&artifact_id=".$ah->getID()."'>" ."<img width='16' height='15' border='0' src='".ICMS_URL."/modules/xfmod/images/ic/";
	if ($icmsDB->getRowsNum($result) < 1)
	{
		$content .= "check.png' alt='"._XF_TRK_MONITOR."'>"." "._XF_TRK_MONITOR;
	}
	else
	{
		$content .= "trash.png' alt='"._XF_TRK_STOPMONITOR."'>"." "._XF_TRK_STOPMONITOR;
	}
	$content .= "</a></strong></p>";
}
 
$content .= "<table CELLPADDING='0' width='100%'>";
 
if (!$icmsUser)
{
	$content .= '
		<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
		<input type="hidden" name="func" value="monitor">
		<input type="hidden" name="artifact_id" value="'.$ah->getID().'">
		<th>
		<td colspan=2">
		<strong>'._XF_TRK_EMAIL.':</strong> &nbsp;
		<input type="text" name="user_email" size="20" maxlength="40">
		<input type="submit" name="submit" value="'._XF_TRK_MONITOR.'">
		</form>
		</td>
		</th>';
}
 
$content .= '
	<th>
	<td><strong>'._XF_G_DATE.':</strong><BR>'.date($sys_datefmt, $ah->getOpenDate()).'</td>
	<td><strong>'._XF_G_PRIORITY.':</strong><BR>'.$ah->getPriority().'</td>
	</th>
	 
	<th>
	<td><strong>'._XF_G_SUBMITTEDBY.':</strong><BR>'.$ah->getSubmittedRealName().'('.$ah->getSubmittedUnixName().')</td>
	<td><strong>'._XF_G_ASSIGNEDTO.':</strong><BR>'.$ah->getAssignedRealName().'('.$ah->getAssignedUnixName().')</td>
	</th>
	 
	<th>
	<td><strong>'._XF_TRK_CATEGORY.':</strong><BR>'.$ah->getCategoryName().'</td>
	<td><strong>'._XF_TRK_STATUS.':</strong><BR>'.$ah->getStatusName().'</td>
	</th>
	 
	<th><td colspan="2"><strong>'._XF_TRK_SUMMARY.':<BR><I>'.$ts->makeTareaData4Show($ah->getSummary()).'</I></strong></td></th>
	 
	<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST">
	 
	<th><td colspan="2">
	'.$ts->makeTareaData4Show($ah->getDetails()).'
	<input type="hidden" name="func" value="postaddcomment">
	<input type="hidden" name="artifact_id" value="'.$ah->getID().'">
	<p>
	<strong>'._XF_TRK_ADDACOMMENT.':</strong><BR>
	<textarea name="details" rows="10" cols="60" WRAP="HARD"></textarea>
	</td></th>
	 
	<th><td colspan="2">';
 
if (!$icmsUser)
{
	//$loginurl = getLoginURL();
	 
	$content .= '
		<h4><FONT COLOR="RED">Please log in!</FONT></h4><BR>
		'._XF_TRK_IFCANNOTLOGIN.':<p>
		<input type="text" name="user_email" size="20" maxlength="40">';
	 
}
 
$content .= '
	<p>
	<H4>'._XF_TRK_DONOTENTERPASSWORDS.'</H4>
	<p>
	<input type="submit" name="submit" value="'._XF_G_SUBMIT.'">
	</form>
	</td></th>
	 
	<th><td colspan="2">
	<H4>'._XF_G_FOLLOWUPS.':</H4>
	<p>'.  
$ah->showMessages()
 
.'</td></th>
	 
	<th><td colspan=2>
	<H4>'._XF_TRK_ATTACHEDFILES.':</H4>';
 
//
//  print a list of files attached to this Artifact
//
$file_list = $ah->getFiles();
 
$count = count($file_list);
 
$content .= "<table border='0' width='100%' cellpadding='5' cellspacing='1'>
	<th class='bg2'>
	<td align='center'><strong>"._XF_TRK_NAME."</strong></td>
	<td align='center'><strong>"._XF_G_DESCRIPTION."</strong></td>
	<td align='center'><strong>"._XF_TRK_DOWNLOAD."</strong></td>
	</th>";
 
if ($count > 0)
{
	 
	for($i = 0; $i < $count; $i++)
	{
		$content .= '<th>
			<td>'.$file_list[$i]->getName().'</td>
			<td>'.$ts->makeTareaData4Show($file_list[$i]->getDescription()) .'</td>
			<td><a href="'.ICMS_URL.'/modules/xfmod/tracker/download.php?group_id='.$group_id.'&atid='. $ath->getID().'&file_id='.$file_list[$i]->getID().'&aid='. $ah->getID() .'">'._XF_TRK_DOWNLOAD.'</a></td>
			</th>';
	}
	 
}
else
{
	$content .= '<th><td colspan=3>'._XF_TRK_NOFILESATTACHED.'</td></th>';
}
 
$content .= '</table>
	 
	</td></th>
	 
	<th>
	<td colspan="2">
	<H3>'._XF_TRK_CHANGES.':</H3>
	<p>'.  
$ah->showHistory()
 
.'</td>
	</th>
	</table>';
 
$icmsTpl->assign("content", $content);
 
include("../../../footer.php");
 
?>