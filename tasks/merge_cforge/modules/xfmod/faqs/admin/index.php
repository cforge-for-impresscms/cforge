<?php
	/**
	*
	* SourceForge Forums Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.8 2004/01/09 23:19:32 jcox Exp $
	*
	*/
	 
	include_once("../../../../mainfile.php");
	 
	$langfile = "faqs.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	$icmsOption['template_main'] = 'faqs/admin/xfmod_index.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	// get current information
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	include("../../../../header.php");
	$icmsTpl->assign("project_title", project_title($group));
	$icmsTpl->assign("project_tabs", project_tabs('faqs', $group_id));
	if ($perm->isAdmin())
	{
		$content .= '<strong>';
		$content .= "<a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."'>"._XF_G_ADMIN."</a> | <a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&add_faq=1'>"._XF_FAQ_ADDAFAQ."</a><BR>";
		$content .= '</strong><P/>';
	}
	else
	{
		redirect_header($_SERVER["HTTP_REFERER"], 4, _XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTADMINTHISPROJECT);
		exit();
	}
	 
	$feedback = '';
	 
	if ($post_changes)
	{
		/*
		Update the DB to reflect the changes
		*/
		if ($edit_faq)
		{
			$sql = "UPDATE ".$icmsDB->prefix("xoopsfaq_categories")." SET category_title='".$faq_title."' WHERE category_id='".$cat_id."'";
			//$sql = "DELETE FROM ".$icmsDB->prefix("xf_foundry_faqs")
			//. " WHERE foundry_id='".$group_id."'"
			//. " AND category_id='".$cat_id."'";
			$result = $icmsDB->queryF($sql);
			if (! result)
			{
				$feedback = _XF_FAQ_ERRUPFAQ;
			}
			else
			{
				$feedback = _XF_FAQ_FAQUPDATED;
			}
		}
		else if($edit_qa)
		{
			$sql = "UPDATE ".$icmsDB->prefix("xoopsfaq_contents")." SET contents_title='".$question."', contents_contents='".$answer."' WHERE contents_id='".$contents_id."'";
			$result = $icmsDB->queryF($sql);
			if (! result)
			{
				$feedback = "Error updating faq content";
			}
			else
			{
				$feedback = "Faq Content updated successfully";
			}
		}
		if ($delete_faq)
		{
			$sql = "DELETE FROM ".$icmsDB->prefix("xf_foundry_faqs")
			. " WHERE foundry_id='".$group_id."'" . " AND category_id='".$cat_id."'";
			$result = $icmsDB->queryF($sql);
			if (! result)
			{
				$feedback = _XF_FAQ_ERRDELFAQ;
			}
			else
			{
				$feedback = _XF_FAQ_FAQDELETED;
			}
		}
		else if($delete_contents)
		{
			$sql = "DELETE FROM ".$icmsDB->prefix("xoopsfaq_contents")
			. " WHERE contents_id='".$delete_contents."'";
			$result = $icmsDB->queryF($sql);
			if (! result)
			{
				$feedback = "Error deleted faq content";
			}
			else
			{
				$feedback = "Faq Content deleted successfully";
			}
		}
		else if($add_faq)
		{
			$sql = "INSERT INTO ".$icmsDB->prefix("xoopsfaq_categories")
			. "(category_title) VALUES('" . $faq_title . "')";
			$result = $icmsDB->queryF($sql);
			if (! $result)
			{
				$feedback = _XF_FAQ_ERRADDINGFAQ;
			}
			else
			{
				$insert_id = $icmsDB->getInsertId();
				$sql = "INSERT INTO " . $icmsDB->prefix("xf_foundry_faqs")
				. "(foundry_id, category_id) VALUES " . "('" . $group_id . "', '" . $insert_id . "')";
				$result = $icmsDB->queryF($sql);
				if (! $result)
				{
					$feedback = _XF_FAQ_ERRADDINGFAQ;
				}
				else
				{
					$feedback = _XF_FAQ_FAQADDED;
				}
			}
		}
		else if($link_faq)
		{
			$sql = "INSERT INTO " . $icmsDB->prefix("xf_foundry_faqs")
			. "(foundry_id, category_id) VALUES " . "('" . $group_id . "', '" . $cat_id . "')";
			$result = $icmsDB->queryF($sql);
			if (! $result)
			{
				$feedback = _XF_FAQ_ERRLINKINGFAQ;
			}
			else
			{
				$feedback = _XF_FAQ_FAQLINKED;
			}
		}
		else if($add_qa)
		{
			$sql = "INSERT INTO " . $icmsDB->prefix("xoopsfaq_contents")
			. "(category_id,contents_title,contents_contents,contents_time)" . " VALUES('" . $cat_id . "', '" . $question . "', '" . $answer . "', CURTIME())";
			$result = $icmsDB->queryF($sql);
			if (! $result)
			{
				$feedback = _XF_FAQ_ERRADDINGQA;
			}
			else
			{
				$feedback = _XF_FAQ_QAADDED;
			}
		}
	}
	 
	if ($add_faq)
	{
		/*
		Show the form for adding a faq
		*/
		$content .= "<p>".$feedback."</p>";
		 
		$content .= '
			 
			<form method="POST" ACTION="'.$_SERVER['PHP_SELF'].'">
			<input type="hidden" name="post_changes" value="y">
			<input type="hidden" name="add_faq" value="y">
			<input type="hidden" name="group_id" value="'.$group_id.'">
			<strong>'._XF_FAQ_FAQTITLE.':</strong><BR>
			<input type="text" name="faq_title" value="" size="30" maxlength="30"><BR>
			<input type="submit" name="submit" value="'._XF_FAQ_ADDTHISFAQ.'">
			</form>';
	}
	else if($edit_faq)
	{
		$sql = "SELECT  category_title FROM " . $icmsDB->prefix("xoopsfaq_categories")." WHERE  category_id='".$cat_id."'";
		;
		$result = $icmsDB->query($sql);
		$catrow = $icmsDB->fetchArray($result);
		 
		$content .= '
			 
			<form method="POST" ACTION="'.$_SERVER['PHP_SELF'].'">
			<input type="hidden" name="post_changes" value="y">
			<input type="hidden" name="edit_faq" value="y">
			<input type="hidden" name="group_id" value="'.$group_id.'">
			<input type="HIDDEN" name="cat_id" value="'.$cat_id.'">
			<strong>'._XF_FAQ_FAQTITLE.':</strong><BR>
			<input type="text" name="faq_title" value="'.$catrow['category_title'].'" size="30" maxlength="30"><BR>
			<input type="submit" name="submit" value="'._XF_FAQ_UPDATETHISFAQ.'">
			</form>';
	}
	else if($link_faq)
	{
		/*
		Show the form for linking to existing forums
		*/
		$content .= "<p>".$feedback."</p>";
		 
		$content .= "<h5>"._XF_FAQ_CURRENTLYAVAILFAQS."</h5>\n";
		$content .= "<table border='0' cellpadding='0' cellspacing='0' width='100%'>\n";
		 
		$sql = "SELECT category_title,category_id FROM " . $icmsDB->prefix("xoopsfaq_categories");
		$result = $icmsDB->query($sql);
		while ($catrow = $icmsDB->fetchArray($result))
		{
			$sql = "SELECT * FROM " . $icmsDB->prefix("xf_foundry_faqs")
			. " WHERE foundry_id='".$group_id."'" . " AND category_id='".$catrow['category_id']."'";
			$subresult = $icmsDB->query($sql);
			$numrows = $icmsDB->getRowsNum($subresult);
			if (0 == $numrows)
			{
				// There is currently no link for this FAQ.
				$content .= "<tr><td><a href='".ICMS_URL."/modules/xoopsfaq/?cat_id=".$catrow['category_id']."'>".$catrow['category_title']."</a></td>\n";
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
	else if($add_qa)
	{
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
		$content .= "<tr><td valign='top'><strong>"._XF_FAQ_QUESTION."</strong><br/><input type='text' name='question' size='50' maxlength'255'></td></tr>\n";
		$content .= "<tr><td valign='top'><strong>"._XF_FAQ_ANSWER."</strong><br/><textarea name='answer' cols='60' rows='20'></textarea></td></tr>\n";
		$content .= "<tr><td align='center'><input type='submit' name='submit' value='"._XF_FAQ_CREATEQA."'></td></tr></table></form>\n";
	}
	else if($edit_qa)
	{
		$sql = "SELECT contents_title, contents_contents FROM " . $icmsDB->prefix("xoopsfaq_contents")." WHERE contents_id='".$contents_id."'";
		;
		$result = $icmsDB->query($sql);
		$catrow = $icmsDB->fetchArray($result);
		 
		$content .= "<form action='".$_SERVER['PHP_SELF']."' method='POST'>\n";
		$content .= "<input type='hidden' name='post_changes' value='y'>\n";
		$content .= "<input type='hidden' name='edit_qa' value='y'>\n";
		$content .= "<input type='hidden' name='group_id' value='".$group_id."'>\n";
		$content .= "<input type='hidden' name='contents_id' value='".$contents_id."'>\n";
		$content .= "<table border='0' cellpadding='0' cellspacing='3'\n";
		$content .= "<tr><td valign='top'><strong>"._XF_FAQ_QUESTION."</strong><br/><input type='text' name='question' size='50' maxlength'255' value='".$catrow['contents_title']."'></td></tr>\n";
		$content .= "<tr><td valign='top'><strong>"._XF_FAQ_ANSWER."</strong><br/><textarea name='answer' cols='60' rows='20'>".$catrow['contents_contents']."</textarea></td></tr>\n";
		$content .= "<tr><td align='center'><input type='submit' name='submit' value='"._XF_FAQ_UPDATEQA."'></td></tr></table></form>\n";
	}
	else
	{
		/*
		Show main page for choosing
		either moderotor or delete
		*/
		$content .= "<p>".$feedback."</p>";
		 
		$content .= '
			<!--A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&link_faq=1">'._XF_FAQ_LINKFAQ.'</A-->';
		$content .= "<h5>"._XF_FAQ_CURRENTFAQS.":</h5>";
		$sql = "SELECT ff.category_id, xf.category_title " . "FROM " . $icmsDB->prefix("xf_foundry_faqs") . " ff, " . $icmsDB->prefix("xoopsfaq_categories") . " xf " . "WHERE ff.foundry_id='".$group_id."' " . "AND xf.category_id=ff.category_id";
		$result = $icmsDB->query($sql);
		 
		$content .= "<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
		while ($row = $icmsDB->fetchArray($result))
		{
			$cat_id = $row['category_id'];
			 
			$content .= "<tr><td><a href='".ICMS_URL."/modules/xoopsfaq/?cat_id=".$row['category_id']."'>".$row['category_title']."</a></td><td>" . "<a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&cat_id=".$cat_id."&add_qa=1'>"._XF_FAQ_ADDQA."</a></td><td>" . "<a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&cat_id=".$cat_id."&edit_faq=1'>"._XF_FAQ_EDITAFAQ."</a></td>" . "<td><a href='".$_SERVER['PHP_SELF']."?post_changes=y&group_id=".$group_id."&cat_id=".$cat_id."&delete_faq=1'>"._XF_FAQ_DELETEAFAQ."</a></td></tr>";
			 
			// No question and answer specified - list the possibles
			$sql = "SELECT contents_id, contents_title" . " FROM " . $icmsDB->prefix("xoopsfaq_contents")
			. " WHERE category_id='".$cat_id."'" . " AND contents_visible=1";
			 
			$qaresult = $icmsDB->query($sql);
			 
			 
			if ($icmsDB->getRowsNum($qaresult) > 0)
			{
				while ($row = $icmsDB->fetchArray($qaresult))
				{
					$content .= "<tr><td colspan='2'>&nbsp; &nbsp;<a href='".ICMS_URL."/modules/xoopsfaq/?cat_id=".$cat_id."&contents_id=".$row['contents_id']."'>".$row['contents_title']."</a></td><td><a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."&contents_id=".$row['contents_id']."&edit_qa=1'>"._XF_FAQ_EDITAFAQ."</a></td><td><a href='".$_SERVER['PHP_SELF']."?post_changes=y&group_id=".$group_id."&cat_id=".$cat_id."&delete_contents=".$row['contents_id']."'>"._XF_FAQ_DELETEAFAQ."</a></td></tr>\n";
				}
			}
			else
				{
				//$content .= "<tr><td></td><td>"._XF_FAQ_NOQUESTIONSDEFINED."</td><td></td></tr>";
			}
			 
		}
		$content .= "</table>";
	}
	 
	$icmsTpl->assign("content", $content);
	include("../../../../footer.php");
?>