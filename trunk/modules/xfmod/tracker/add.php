<?php
/**
  *
  * SourceForge Generic Tracker facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: add.php,v 1.7 2004/06/01 20:14:23 devsupaul Exp $
  *
  */

include_once ("../../../mainfile.php");

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");

$xoopsOption['template_main'] = 'tracker/xfmod_add.html';

/* http_track_vars */
$group_id = util_http_track_vars('group_id');

include ("../../../header.php");

$header = $ath->header();

//meta tag information
$metaTitle=" Tracker Submit - ".$group->getPublicName();
$metaKeywords=project_getmetakeywords($group_id);
$metaDescription=str_replace('"', "&quot;", strip_tags($group->getDescription()));

$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
$xoopsTpl->assign("xoops_meta_description", $metaDescription);

//project nav information
$xoopsTpl->assign("project_title", $header['title']);
$xoopsTpl->assign("project_tabs", $header['tabs']);
$xoopsTpl->assign("header", $header['nav']);

	$content = '
	<P>';
	/*
	    Show the free-form text submitted by the project admin
	*/
	$content .= $ts->makeTareaData4Show($ath->getSubmitInstructions());
	$content .= '<P>
	<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST" enctype="multipart/form-data">
	<INPUT TYPE="HIDDEN" NAME="func" VALUE="postadd">
	<TABLE>
	<TR><TD VALIGN="TOP"><B>'._XF_TRK_CATEGORY.':</B><BR>';

		$content .= $ath->categoryBox('category_id');
		$content .= '&nbsp;<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='.$ath->getID() .'&add_cat=1">('._XF_TRK_ADMINSMALL.')</A>';
		$content .= '</TD><TD><B>'._XF_TRK_GROUP.':</B><BR>';
		$content .= $ath->artifactGroupBox('artifact_group_id');
		$content .= '&nbsp;<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='.$ath->getID() .'&add_group=1">('._XF_TRK_ADMINSMALL.')</A>';

	$content .= '</TD></TR>';

	if ($ath->userIsAdmin()) {
		$content .= '<TR><TD><B>'._XF_G_ASSIGNEDTO.':</B><BR>';
		$content .= $ath->technicianBox ('assigned_to');
		$content .= '&nbsp;<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/admin/?group_id='.$group_id.'&atid='.$ath->getID() .'&update_users=1">('._XF_TRK_ADMINSMALL.')</A>';

		$content .= '</TD><TD><B>'._XF_G_PRIORITY.':</B><BR>';
		$content .= build_priority_select_box('priority');
		$content .= '</TD></TR>';
	}

	$content .= '<TR><TD COLSPAN="2"><B>'._XF_TRK_SUMMARY.':</B><BR>
		<INPUT TYPE="TEXT" NAME="summary" SIZE="35">
	</TD></TR>

	<TR><TD COLSPAN="2">
		<B>'._XF_TRK_DETAILEDDESCRIPTION.':</B>
		<P>
		<TEXTAREA NAME="details" ROWS="30" COLS="55" WRAP="HARD"></TEXTAREA>
	</TD></TR>

	<TR><TD COLSPAN="2">';

	if (!$xoopsUser) {
		// Make sure to use Novell Login instead of regular
		//$url = XOOPS_URL."/novelllogin.php?ref=".$_SERVER['PHP_SELF'];
		foreach ( $_GET as $key => $value )
		{
			$url .= "&".$key;
			if ( 0 < strlen($key) )
			{
				$url .= "=".$value;
			}
		}
		$content .= "<h4><FONT COLOR='RED'>"._XF_TRK_PLEASE
			 ." <a href='".XOOPS_URL."/user.php?xoops_redirect=".$_SERVER['PHP_SELF']."?".urlencode($_SERVER['QUERY_STRING'])."'>"._XF_TRK_LOGIN."</A></FONT></h4><BR>"
			 ._XF_TRK_IFCANNOTLOGIN.":<P>"
			 ."<INPUT TYPE='TEXT' NAME='user_email' SIZE='30' MAXLENGTH='35'>";

	}

		$content .= '<P>
		<H4><FONT COLOR=RED>'._XF_TRK_DONOTENTERPASSWORDS.'</FONT></H4>
		<P>
	</TD></TR>

	<TR><TD COLSPAN=2>
		<B>'._XF_TRK_CHECKTOUPLOAD.':</B> <input type="checkbox" name="add_file" VALUE="1">
		<P>
		<input type="file" name="input_file" size="30">
		<P>
		<B>'._XF_TRK_FILEDESCRIPTION.':</B><BR>
		<input type="text" name="file_description" size="40" maxlength="255">
		<P>
	</TD><TR>

	<TR><TD COLSPAN=2>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_G_SUBMIT.'">
		</FORM>
		<P>
	</TD></TR>

	</TABLE>';

$xoopsTpl->assign("content", $content);

include ("../../../footer.php");

	//$ath->footer();

?>
