<?php
/**
  *
  * SourceForge News Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.5 2003/12/15 18:09:19 devsupaul Exp $
  *
  */
include_once ("../../../../mainfile.php");

$langfile="news.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/news/admin/news_admin_utils.php");
$xoopsOption['template_main'] = 'news/admin/xfmod_index.html';
include (XOOPS_ROOT_PATH."/header.php");

if (isset($_POST['post_changes']))
	$post_changes = $_POST['post_changes'];
elseif (isset($_GET['post_changes']))
	$post_changes = $_GET['post_changes'];
else
	$post_changes = null;

if (isset($_POST['approve']))
	$approve = $_POST['approve'];
elseif (isset($_GET['approve']))
	$approve = $_GET['approve'];
else
	$approve = null;



if (isset($group_id) && $group_id && $group_id != $xoopsForge['sysnews']) {
  project_check_access ($group_id);

	// get current information
	$group =& group_get_object($group_id);
	$perm  =& $group->getPermission( $xoopsUser );

	if(!$perm->isAdmin()){
		redirect_header(XOOPS_URL,4,_XF_SUR_NOTALLOWED);
		exit;
	}

	/*

		Per-project admin pages.

		Shows their own news items so they can edit/update.

		If their news is on the homepage, and they edit, it is removed from
			sf.net homepage.

	*/

	if ($post_changes) {
		if ($approve) {
			/*
				Update the db so the item shows on the home page
			*/
			if ($status != 0 && $status != 4) {
				//may have tampered with HTML to get their item on the home page
				$status=0;
			}

			//foundry stuff - remove this news from the foundry so it has to be re-approved by the admin
			$xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_foundry_news")." WHERE news_id='$id'");

			if (!$summary) {
				$summary='(none)';
			}
			if (!$details) {
				$details='(none)';
			}

			$sql = "UPDATE ".$xoopsDB->prefix("xf_news_bytes")." SET "
			      ."is_approved='$status',"
						."summary='".$ts->makeTboxData4Save($summary)."',"
						."details='".$ts->makeTareaData4Save($details)."' "
						."WHERE id='$id' "
						."AND group_id='$group_id'";
			$result = $xoopsDB->queryF($sql);

			if (!$result) {
				$feedback .= ' ERROR doing group update ';
			} else {
				$feedback .= ' '._XF_NWS_NEWSBYTEUPDATED.' ';
			}
			/*
				Show the list_queue
			*/
			$approve='';
			$list_queue='y';
		}
	}

	$xoopsTpl->assign("news_header",news_header($group,$perm));

	$content = "";
	if ($approve) {
		/*
			Show the submit form
		*/

		$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_news_bytes").",".$xoopsDB->prefix("users")." WHERE submitted_by=uid AND id='$id' AND group_id='$group_id'";
		$result = $xoopsDB->query($sql);
		if ($xoopsDB->getRowsNum($result) < 1) {
		  $xoopsTpl->assign("content",'Error<br />Error - none found');
		  include (XOOPS_ROOT_PATH."/footer.php");
		  exit;
		}

		$content .= '
		<H4>'._XF_NWS_APPROVEANEWSBYTE.' '._XF_NWS_FORPROJECT.': '.$group->getPublicName().'</H4>
		<P>
		<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.unofficial_getDBResult($result,0,'group_id').'">
		<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.unofficial_getDBResult($result,0,'id').'">

		<B>'._XF_NWS_SUBMITTEDBY.':</B> '.unofficial_getDBResult($result,0,'uname').'<BR>
		<INPUT TYPE="HIDDEN" NAME="approve" VALUE="y">
		<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">

		<B>'._XF_NWS_STATUS.':</B><BR>
		<INPUT TYPE="RADIO" NAME="status" VALUE="0" CHECKED> '._XF_NWS_DISPLAYED.'<BR>
		<INPUT TYPE="RADIO" NAME="status" VALUE="4"> '._XF_G_DELETE.'<BR>

		<B>'._XF_G_SUBJECT.':</B><BR>
		<INPUT TYPE="TEXT" NAME="summary" VALUE="'.$ts->makeTboxData4Edit(unofficial_getDBResult($result,0,'summary')).'" SIZE="30" MAXLENGTH="60"><BR>
		<B>'._XF_NWS_DETAILS.':</B><BR>
		<TEXTAREA NAME="details" ROWS="5" COLS="50" WRAP="SOFT">'.$ts->makeTareaData4Edit(unofficial_getDBResult($result,0,'details')).'</TEXTAREA><P>
		<B>'._XF_NWS_IFONFRONTPAGEREMOVED.'</B><BR>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_G_SUBMIT.'">
		</FORM>';

	} else {
		/*
			Show list of waiting news items
		*/

		$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_news_bytes")." WHERE is_approved <> 4 AND group_id='$group_id'";
		$result = $xoopsDB->query($sql);
		$rows = $xoopsDB->getRowsNum($result);
		if ($rows < 1) {
			$content .='
				<H4>'._XF_NWS_NOQUEUEDITEMSFOUND.' '._XF_NWS_FORPROJECT.': '.$group->getPublicName().'</H1>';
		} else {
			$content .= '
				<H4>'._XF_NWS_QUEUEDITEMS.' '._XF_NWS_FORPROJECT.': '.$group->getPublicName().'</H4>
				<P>';
			for ($i=0; $i<$rows; $i++) {
				$content .= '
				<A HREF="'.XOOPS_URL.'/modules/xfmod/news/admin/?approve=1&id='.unofficial_getDBResult($result,$i,'id').'&group_id='.
					unofficial_getDBResult($result,$i,'group_id').'">'.
					$ts->makeTboxData4Show(unofficial_getDBResult($result,$i,'summary')).'</A><BR>';
			}
		}

	}
	$xoopsTpl->assign("content",$content);
	include (XOOPS_ROOT_PATH."/footer.php");

} else {
  $group_id = $xoopsForge['sysnews'];
  project_check_access ($group_id);

  // get current information
  $group =& group_get_object($group_id);
  $perm  =& $group->getPermission( $xoopsUser );

	/*

		News uber-user admin pages

		Show all waiting news items except those already rejected.

		Admin members of $sys_news_group (news project) can edit/change/approve news items

	*/
	if (isset($post_changes) && $post_changes) {
		if ($approve) {
			if ($status == 1) {
				/*
					Update the db so the item shows on the home page
				*/
				$sql = "UPDATE ".$xoopsDB->prefix("xf_news_bytes")." SET "
				      ."is_approved='1',"
							."date='".time()."',"
							."summary='".$ts->makeTboxData4Save($summary)."',"
							."details='".$ts->makeTareaData4Save($details)."' "
							."WHERE id='$id'";

				$result = $xoopsDB->queryF($sql);

				if (!$result) {
					$feedback .= ' ERROR doing update ';
				} else {
					$feedback .= ' '._XF_NWS_NEWSBYTEUPDATED.' ';
				}
			} else if ($status == 2) {
				/*
					Move msg to deleted status
				*/
				$sql = "UPDATE ".$xoopsDB->prefix("xf_news_bytes")." SET is_approved='2' WHERE id='$id'";
				$result = $xoopsDB->queryF($sql);
				if (!$result) {
					$feedback .= ' ERROR doing update ';
					$feedback .= $xoopsDB->error();
				} else {
					$feedback .= ' '._XF_NWS_NEWSBYTEDELETED.' ';
				}
			}

			/*
				Show the list_queue
			*/
			$approve='';
			$list_queue='y';
		} else if ($mass_reject) {
			/*
				Move msg to rejected status
			*/
			$sql = "UPDATE ".$xoopsDB->prefix("xf_news_bytes")." SET "
			      ."is_approved='2' "
						."WHERE id IN ('".implode($news_id,"','")."')";

			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				$feedback .= ' ERROR doing update ';
				$feedback .= $xoopsDB->error();
			} else {
				$feedback .= ' '._XF_NWS_NEWSBYTEREJECTED.' ';
			}
		}
	}

	$xoopsTpl->assign("news_header",news_header($group,$perm));

	if ($approve) {
		/*
			Show the submit form
		*/

		$sql = "SELECT g.unix_group_name,g.group_name,u.uname,nb.* "
		      ."FROM ".$xoopsDB->prefix("xf_news_bytes")." nb,".$xoopsDB->prefix("xf_groups")." g,".$xoopsDB->prefix("users")." u "
					."WHERE id='$id' "
					."AND nb.submitted_by=uid "
					."AND nb.group_id=g.group_id";

		$result = $xoopsDB->query($sql);

		if ($xoopsDB->getRowsNum($result) < 1) {
			$xoopsTpl->assign("content",'Error<br />Error - none found');
			include (XOOPS_ROOT_PATH."/footer.php");
			exit;
		}

		$xoopsTpl->assign("content",'
		<H4>'._XF_NWS_APPROVEANEWSBYTE.'</H4>
		<P>
		<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="for_group" VALUE="'.unofficial_getDBResult($result,0,'group_id').'">
		<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.unofficial_getDBResult($result,0,'id').'">
		<B>'._XF_NWS_SUBMITTEDFOR.':</B> <a href="'.XOOPS_URL.'/modules/xfmod/project/?'.unofficial_getDBResult($result,0,'unix_group_name').'">'.unofficial_getDBResult($result,0,'group_name').'</a><BR>
		<B>'._XF_NWS_SUBMITTEDBY.':</B> '.unofficial_getDBResult($result,0,'uname').'<BR>
		<INPUT TYPE="HIDDEN" NAME="approve" VALUE="y">
		<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
		<INPUT TYPE="RADIO" NAME="status" VALUE="1"> '._XF_NWS_APPROVEFORFRONT.'<BR>
		<INPUT TYPE="RADIO" NAME="status" VALUE="0" CHECKED> '._XF_NWS_DONOTHING.'<BR>
		<INPUT TYPE="RADIO" NAME="status" VALUE="2"> '._XF_G_DELETE.'<BR>
		<B>'._XF_G_SUBJECT.':</B><BR>
		<INPUT TYPE="TEXT" NAME="summary" VALUE="'.$ts->makeTboxData4Edit(unofficial_getDBResult($result,0,'summary')).'" SIZE="30" MAXLENGTH="60"><BR>
		<B>'._XF_NWS_DETAILS.':</B><BR>
		<TEXTAREA NAME="details" ROWS="5" COLS="50" WRAP="SOFT">'.$ts->makeTareaData4Edit(unofficial_getDBResult($result,0,'details')).'</TEXTAREA><BR>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_G_SUBMIT.'">
		</FORM>');

	} else {

		/*
			Show list of waiting news items
		*/

	  $old_date = time()-60*60*24*14;
		$sql_pending = "SELECT g.group_id,id,date,summary,group_name,unix_group_name "
		              ."FROM ".$xoopsDB->prefix("xf_news_bytes")." nb,".$xoopsDB->prefix("xf_groups")." g "
									."WHERE is_approved=0 "
									."AND nb.group_id=g.group_id "
									."AND date > '$old_date' "
									."AND g.is_public=1 "
									."AND g.status='A' "
									."ORDER BY date";

		$old_date = time()-(60*60*24*7);
		$sql_rejected = "SELECT g.group_id,id,date,summary,group_name,unix_group_name "
		               ."FROM ".$xoopsDB->prefix("xf_news_bytes")." nb,".$xoopsDB->prefix("xf_groups")." g "
									 ."WHERE is_approved=2 "
									 ."AND nb.group_id=g.group_id "
									 ."AND date > '$old_date' "
									 ."ORDER BY date";

		$sql_approved = "SELECT g.group_id,id,date,summary,group_name,unix_group_name "
		               ."FROM ".$xoopsDB->prefix("xf_news_bytes")." nb,".$xoopsDB->prefix("xf_groups")." g "
									 ."WHERE is_approved=1 "
									 ."AND nb.group_id=g.group_id "
									 ."AND date > '$old_date' "
									 ."ORDER BY date";

		$xoopsTpl->assign("content",show_news_approve_form(
			$sql_pending,
			$sql_rejected,
			$sql_approved
		));

	}
	include (XOOPS_ROOT_PATH."/footer.php");
}
?>