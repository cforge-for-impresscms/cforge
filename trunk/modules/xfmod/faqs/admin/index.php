<?php
/**
  *
  * SourceForge Forums Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.8 2004/01/09 23:19:32 jcox Exp $
  *
  */

include_once ("../../../../mainfile.php");

$langfile="faqs.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$xoopsOption['template_main'] = 'faqs/admin/xfmod_index.html';

// get current information
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser);

    include ("../../../../header.php");
    $xoopsTpl->assign("project_title", project_title($group));
    $xoopsTpl->assign("project_tabs", project_tabs('faqs', $group_id));
    if($perm->isAdmin()){
	$content .= '<B>';
	$content .= "<a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."'>"._XF_G_ADMIN."</a> | <A HREF='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&add_faq=1'>"._XF_FAQ_ADDAFAQ."</A><BR>";
	$content .= '</B><P/>';
    }
    else {
	redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
	exit();
    }

    $feedback = '';

if ($post_changes) {
	/*
		Update the DB to reflect the changes
	*/
	if ($edit_faq) {
		$sql = "UPDATE ".$xoopsDB->prefix("xoopsfaq_categories")." SET category_title='".$faq_title."' WHERE category_id='".$cat_id."'";
		//$sql = "DELETE FROM ".$xoopsDB->prefix("xf_foundry_faqs")
		//. " WHERE foundry_id='".$group_id."'"
		//. " AND category_id='".$cat_id."'";
		$result = $xoopsDB->queryF($sql);
		if ( ! result ) {
			$feedback = _XF_FAQ_ERRUPFAQ;
		}
		else {
			$feedback = _XF_FAQ_FAQUPDATED;
		}
	}
	else if ($edit_qa) {
		$sql = "UPDATE ".$xoopsDB->prefix("xoopsfaq_contents")." SET contents_title='".$question."', contents_contents='".$answer."' WHERE contents_id='".$contents_id."'";
		$result = $xoopsDB->queryF($sql);
		if ( ! result )	{
			$feedback = "Error updating faq content";
		}
		else {
			$feedback = "Faq Content updated successfully";
		}
	}
	if ($delete_faq) {
		$sql = "DELETE FROM ".$xoopsDB->prefix("xf_foundry_faqs")
		. " WHERE foundry_id='".$group_id."'"
		. " AND category_id='".$cat_id."'";
		$result = $xoopsDB->queryF($sql);
		if ( ! result ) {
			$feedback = _XF_FAQ_ERRDELFAQ;
		}
		else {
			$feedback = _XF_FAQ_FAQDELETED;
		}
	}
	else if ($delete_contents) {
		$sql = "DELETE FROM ".$xoopsDB->prefix("xoopsfaq_contents")
		. " WHERE contents_id='".$delete_contents."'";
		$result = $xoopsDB->queryF($sql);
		if ( ! result )	{
			$feedback = "Error deleted faq content";
		}
		else {
			$feedback = "Faq Content deleted successfully";
		}
	}
	else if ($add_faq) {
		$sql = "INSERT INTO ".$xoopsDB->prefix("xoopsfaq_categories")
		. " (category_title) VALUES ('" . $faq_title . "')";
		$result = $xoopsDB->queryF($sql);
		if ( ! $result ) {
			$feedback = _XF_FAQ_ERRADDINGFAQ;
		}
		else {
			$insert_id = $xoopsDB->getInsertId();
			$sql = "INSERT INTO "
			. $xoopsDB->prefix("xf_foundry_faqs")
			. " (foundry_id, category_id) VALUES "
			. "('" . $group_id . "', '" . $insert_id . "')";
			$result = $xoopsDB->queryF($sql);
			if ( ! $result ) {
				$feedback = _XF_FAQ_ERRADDINGFAQ;
			}
			else {
				$feedback = _XF_FAQ_FAQADDED;
			}
		}
	}
	else if ($link_faq) {
		$sql = "INSERT INTO " . $xoopsDB->prefix("xf_foundry_faqs")
		. " (foundry_id, category_id) VALUES "
		. "('" . $group_id . "', '" . $cat_id . "')";
		$result = $xoopsDB->queryF($sql);
		if ( ! $result ) {
			$feedback = _XF_FAQ_ERRLINKINGFAQ;
		}
		else {
			$feedback = _XF_FAQ_FAQLINKED;
		}
	}
	else if ($add_qa) {
		$sql = "INSERT INTO " . $xoopsDB->prefix("xoopsfaq_contents")
		. " (category_id,contents_title,contents_contents,contents_time)"
		. " VALUES ('" . $cat_id . "', '" . $question
		. "', '" . $answer . "', CURTIME())";
		$result = $xoopsDB->queryF($sql);
		if ( ! $result ) {
			$feedback = _XF_FAQ_ERRADDINGQA;
		}
		else {
			$feedback = _XF_FAQ_QAADDED;
		}
	}
}

	if ($add_faq) {
		/*
			Show the form for adding a faq
		*/
	    $content .= "<p>".$feedback."</p>";

	    $content .= '

				<FORM METHOD="POST" ACTION="'.$_SERVER['PHP_SELF'].'">
				<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
				<INPUT TYPE="HIDDEN" NAME="add_faq" VALUE="y">
				<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
				<B>'._XF_FAQ_FAQTITLE.':</B><BR>
				<INPUT TYPE="TEXT" NAME="faq_title" VALUE="" SIZE="30" MAXLENGTH="30"><BR>
				<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_FAQ_ADDTHISFAQ.'">
				</FORM>';
	}
	else if ($edit_faq) {
		$sql = "SELECT  category_title FROM "
		. $xoopsDB->prefix("xoopsfaq_categories")." WHERE  category_id='".$cat_id."'";;
		$result = $xoopsDB->query($sql);
		$catrow = $xoopsDB->fetchArray($result);

	    $content .= '

				<FORM METHOD="POST" ACTION="'.$_SERVER['PHP_SELF'].'">
				<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
				<INPUT TYPE="HIDDEN" NAME="edit_faq" VALUE="y">
				<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
				<INPUT type="HIDDEN" NAME="cat_id" VALUE="'.$cat_id.'">
				<B>'._XF_FAQ_FAQTITLE.':</B><BR>
				<INPUT TYPE="TEXT" NAME="faq_title" VALUE="'.$catrow['category_title'].'" SIZE="30" MAXLENGTH="30"><BR>
				<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_FAQ_UPDATETHISFAQ.'">
				</FORM>';
	}
	else if ($link_faq) {
		/*
			Show the form for linking to existing forums
		*/
		$content .= "<p>".$feedback."</p>";

		$content .= "<h5>"._XF_FAQ_CURRENTLYAVAILFAQS."</h5>\n";
		$content .= "<table border='0' cellpadding='0' cellspacing='0' width='100%'>\n";

		$sql = "SELECT category_title,category_id FROM "
		. $xoopsDB->prefix("xoopsfaq_categories");
		$result = $xoopsDB->query($sql);
		while ( $catrow = $xoopsDB->fetchArray($result) )
		{
			$sql = "SELECT * FROM " . $xoopsDB->prefix("xf_foundry_faqs")
			. " WHERE foundry_id='".$group_id."'"
			. " AND category_id='".$catrow['category_id']."'";
			$subresult = $xoopsDB->query($sql);
			$numrows = $xoopsDB->getRowsNum($subresult);
			if ( 0 == $numrows )
			{
				// There is currently no link for this FAQ.
				$content .= "<tr><td><a href='".XOOPS_URL."/modules/xoopsfaq/?cat_id=".$catrow['category_id']."'>".$catrow['category_title']."</a></td>\n";
				$content .= "<td>";
				$content .= "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
				$content .= "<input type='hidden' name='post_changes' value='y'>\n";
				$content .= "<input type='hidden' name='link_faq' value='y'>\n";
				$content .= "<input type='hidden' name='group_id' value='".$group_id."'>\n";
				$content .= "<input type='hidden' name='cat_id' value='".$catrow['category_id']."'>\n";
				$content .= "<input type='submit' name='submit' value='"._XF_FAQ_LINKTOTHISFAQ."'>\n";
				$content .= "</form></td></tr>\n";
			}
		}
		$content .= "</table>\n";

	}
	else if ($add_qa) {
		/*
			Change a forum to public/private
		*/
		$content .= "<p>".$feedback."</p>";

		$content .= "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
		$content .= "<input type='hidden' name='post_changes' value='y'>\n";
		$content .= "<input type='hidden' name='add_qa' value='y'>\n";
		$content .= "<input type='hidden' name='group_id' value='".$group_id."'>\n";
		$content .= "<input type='hidden' name='cat_id' value='".$cat_id."'>\n";
		$content .= "<table border='0' cellpadding='0' cellspacing='3'\n";
		$content .= "<tr><td valign='top'><b>"._XF_FAQ_QUESTION."</b><br/><input type='text' name='question' size='50' maxlength'255'></td></tr>\n";
		$content .= "<tr><td valign='top'><b>"._XF_FAQ_ANSWER."</b><br/><textarea name='answer' cols='60' rows='20'></textarea></td></tr>\n";
		$content .= "<tr><td align='center'><input type='submit' name='submit' value='"._XF_FAQ_CREATEQA."'></td></tr></table></form>\n";
	}
	else if ($edit_qa) {
		$sql = "SELECT contents_title, contents_contents FROM "
		. $xoopsDB->prefix("xoopsfaq_contents")." WHERE contents_id='".$contents_id."'";;
		$result = $xoopsDB->query($sql);
		$catrow = $xoopsDB->fetchArray($result);

		$content .= "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
		$content .= "<input type='hidden' name='post_changes' value='y'>\n";
		$content .= "<input type='hidden' name='edit_qa' value='y'>\n";
		$content .= "<input type='hidden' name='group_id' value='".$group_id."'>\n";
		$content .= "<input type='hidden' name='contents_id' value='".$contents_id."'>\n";
		$content .= "<table border='0' cellpadding='0' cellspacing='3'\n";
		$content .= "<tr><td valign='top'><b>"._XF_FAQ_QUESTION."</b><br/><input type='text' name='question' size='50' maxlength'255' value='".$catrow['contents_title']."'></td></tr>\n";
		$content .= "<tr><td valign='top'><b>"._XF_FAQ_ANSWER."</b><br/><textarea name='answer' cols='60' rows='20'>".$catrow['contents_contents']."</textarea></td></tr>\n";
		$content .= "<tr><td align='center'><input type='submit' name='submit' value='"._XF_FAQ_UPDATEQA."'></td></tr></table></form>\n";
	}
	else {
		/*
			Show main page for choosing
			either moderotor or delete
		*/
		$content .= "<p>".$feedback."</p>";

		$content .= '
				<!--A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&link_faq=1">'._XF_FAQ_LINKFAQ.'</A-->';
		$content .= "<h5>"._XF_FAQ_CURRENTFAQS.":</h5>";
		$sql = "SELECT ff.category_id, xf.category_title "
		. "FROM " . $xoopsDB->prefix("xf_foundry_faqs") . " ff, "
		. $xoopsDB->prefix("xoopsfaq_categories") . " xf "
		. "WHERE ff.foundry_id='".$group_id."' "
		. "AND xf.category_id=ff.category_id";
		$result = $xoopsDB->query($sql);

		$content .= "<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$cat_id = $row['category_id'];

			$content .= "<tr><td><A HREF='".XOOPS_URL."/modules/xoopsfaq/?cat_id=".$row['category_id']."'>".$row['category_title']."</a></td><td>"
			. "<A HREF='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&cat_id=".$cat_id."&add_qa=1'>"._XF_FAQ_ADDQA."</A></td><td>"
			. "<A HREF='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&cat_id=".$cat_id."&edit_faq=1'>"._XF_FAQ_EDITAFAQ."</A></td>"
			. "<td><A HREF='".$_SERVER['PHP_SELF']."?post_changes=y&group_id=".$group_id."&cat_id=".$cat_id."&delete_faq=1'>"._XF_FAQ_DELETEAFAQ."</A></td></tr>";

					// No question and answer specified - list the possibles
			$sql = "SELECT contents_id, contents_title"
			. " FROM " . $xoopsDB->prefix("xoopsfaq_contents")
			. " WHERE category_id='".$cat_id."'"
			. " AND contents_visible=1";

			$qaresult = $xoopsDB->query($sql);


			if ( $xoopsDB->getRowsNum($qaresult) > 0 )
			{
				while ( $row = $xoopsDB->fetchArray($qaresult) )
				{
					$content .= "<tr><td colspan='2'>&nbsp; &nbsp;<a href='".XOOPS_URL."/modules/xoopsfaq/?cat_id=".$cat_id."&contents_id=".$row['contents_id']."'>".$row['contents_title']."</a></td><td><A HREF='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&contents_id=".$row['contents_id']."&edit_qa=1'>"._XF_FAQ_EDITAFAQ."</A></td><td><A HREF='".$_SERVER['PHP_SELF']."?post_changes=y&group_id=".$group_id."&cat_id=".$cat_id."&delete_contents=".$row['contents_id']."'>"._XF_FAQ_DELETEAFAQ."</A></td></tr>\n";
				}
			}
			else
			{
				//$content .= "<tr><td></td><td>"._XF_FAQ_NOQUESTIONSDEFINED."</td><td></td></tr>";
			}

		}
		$content .= "</table>";
	}

	$xoopsTpl->assign("content", $content);
        include ( "../../../../footer.php" );
?>