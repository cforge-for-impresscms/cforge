<?php
/**
*
* SourceForge Generic Tracker facility
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001(c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: add.php,v 1.7 2004/06/01 20:14:23 devsupaul Exp $
*
*/
 
include_once("../../../mainfile.php");
 
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
 
$icmsOption['template_main'] = 'tracker/xfmod_add.html';
 
if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
 
/* http_track_vars */
//$group_id = util_http_track_vars('group_id');
 
include("../../../header.php");
 
$header = $ath->header();
 
//meta tag information
$metaTitle = " Tracker Submit - ".$group->getPublicName();
$metaKeywords = project_getmetakeywords($group_id);
$metaDescription = str_replace('"', "&quot;", strip_tags($group->getDescription()));
 
$icmsTpl->assign("icms_pagetitle", $metaTitle);
$icmsTpl->assign("icms_meta_keywords", $metaKeywords);
$icmsTpl->assign("icms_meta_description", $metaDescription);
 
//project nav information
$icmsTpl->assign("project_title", $header['title']);
$icmsTpl->assign("project_tabs", $header['tabs']);
$icmsTpl->assign("header", $header['nav']);
 
$content = '
	<p>';
/*
Show the free-form text submitted by the project admin
*/
$content .= $ts->makeTareaData4Show($ath->getSubmitInstructions());
$content .= '<p>
	<form action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="func" value="postadd">
	<table>
	<tr><td valign="top"><strong>'._XF_TRK_CATEGORY.':</strong><BR>';
 
$content .= $ath->categoryBox('category_id');
$content .= '&nbsp;<a href="'.ICMS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='.$ath->getID() .'&add_cat=1">('._XF_TRK_ADMINSMALL.')</a>';
$content .= '</td><td><strong>'._XF_TRK_GROUP.':</strong><BR>';
$content .= $ath->artifactGroupBox('artifact_group_id');
$content .= '&nbsp;<a href="'.ICMS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='.$ath->getID() .'&add_group=1">('._XF_TRK_ADMINSMALL.')</a>';
 
$content .= '</td></tr>';
 
if ($ath->userIsAdmin())
{
	$content .= '<tr><td><strong>'._XF_G_ASSIGNEDTO.':</strong><BR>';
	$content .= $ath->technicianBox('assigned_to');
	$content .= '&nbsp;<a href="'.ICMS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='.$ath->getID() .'&update_users=1">('._XF_TRK_ADMINSMALL.')</a>';
	 
	$content .= '</td><td><strong>'._XF_G_PRIORITY.':</strong><BR>';
	$content .= build_priority_select_box('priority');
	$content .= '</td></tr>';
}
 
$content .= '<tr><td colspan="2"><strong>'._XF_TRK_SUMMARY.':</strong><BR>
	<input type="text" name="summary" size="35">
	</td></tr>
	 
	<tr><td colspan="2">
	<strong>'._XF_TRK_DETAILEDDESCRIPTION.':</strong>
	<p>
	<textarea name="details" rows="30" cols="55" WRAP="HARD"></textarea>
	</td></tr>
	 
	<tr><td colspan="2">';
 
if (!$icmsUser)
{
	// Make sure to use Novell Login instead of regular
	//$url = ICMS_URL."/novelllogin.php?ref=".$_SERVER['PHP_SELF'];
	foreach($_GET as $key => $value)
	{
		$url .= "&".$key;
		if (0 < strlen($key))
		{
			$url .= "=".$value;
		}
	}
	$content .= "<h4><FONT COLOR='RED'>"._XF_TRK_PLEASE ." <a href='".ICMS_URL."/user.php?icms_redirect=".$_SERVER['PHP_SELF']."?".urlencode($_SERVER['QUERY_STRING'])."'>"._XF_TRK_LOGIN."</a></FONT></h4><BR>" ._XF_TRK_IFCANNOTLOGIN.":<p>" ."<input type='text' name='user_email' size='30' maxlength='35'>";
	 
}
 
$content .= '<p>
	<H4><FONT COLOR=RED>'._XF_TRK_DONOTENTERPASSWORDS.'</FONT></H4>
	<p>
	</td></tr>
	 
	<tr><td colspan=2>
	<strong>'._XF_TRK_CHECKTOUPLOAD.':</strong> <input type="checkbox" name="add_file" value="1">
	<p>
	<input type="file" name="input_file" size="30">
	<p>
	<strong>'._XF_TRK_FILEDESCRIPTION.':</strong><BR>
	<input type="text" name="file_description" size="40" maxlength="255">
	<p>
	</td><tr>
	 
	<tr><td colspan=2>
	<input type="submit" name="submit" value="'._XF_G_SUBMIT.'">
	</form>
	<p>
	</td></tr>
	 
	</table>';
 
$icmsTpl->assign("content", $content);
 
include("../../../footer.php");
 
//$ath->footer();
 
?>