<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.9 2004/04/05 23:11:17 jcox Exp $
include_once ("../../../../mainfile.php");

$langfile="tracker.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/Artifact.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactHtml.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactFile.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactFileHtml.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactType.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactTypeHtml.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactGroup.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactCategory.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactCanned.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactResolution.class");
include(XOOPS_ROOT_PATH."/modules/xfmod/language/english/tracker_text_1.php");
$xoopsOption['template_main'] = 'tracker/admin/xfmod_index.html';

  project_check_access ($group_id);

  // get current information
  $group =& group_get_object($group_id);
  $perm  =& $group->getPermission( $xoopsUser );

if ($group_id && $atid) {
//
//
//		UPDATING A PARTICULAR ARTIFACT TYPE
//
//

	if (!$perm->isArtifactAdmin()) {
	    redirect_header(XOOPS_URL."/",2,_XF_G_PERMISSIONDENIED."<br />"._XF_TRK_NOTTRACKERMANAGER);
	    exit;
	}
	//
	//  Create the ArtifactType object
	//
	$ath = new ArtifactTypeHtml($group,$atid);
	if (!$ath || !is_object($ath)) {
	    redirect_header(XOOPS_URL."/",2,"ERROR<br />ArtifactType could not be created");
	    exit;
	}
	if ($ath->isError()) {
	    redirect_header(XOOPS_URL."/",2,"ERROR<br />".$ath->getErrorMessage());
	    exit;
	}

	$feedback = '';
	if ($post_changes) {
//
//		Update the database
//
		if ($add_cat) {

			$ac = new ArtifactCategory($ath);
			if (!$ac || !is_object($ac)) {
				$feedback .= 'Unable to create ArtifactCategory Object';
			}
			else {
				if (!$ac->create($_POST['name'],$_POST['assign_to'])) {
					$feedback .= ' Error inserting: '.$ac->getErrorMessage();
					$ac->clearError();
				}
				else {
					$feedback .= ' '._XF_TRK_CATEGORYINSERTED.' ';
				}
			}

		} elseif ($add_group) {

			$ag = new ArtifactGroup($ath);
			if (!$ag || !is_object($ag)) {
				$feedback .= 'Unable to create ArtifactGroup Object';
			}
			else {
				if (!$ag->create($_POST['name'])) {
					$feedback .= ' Error inserting: '.$ag->getErrorMessage();
					$ag->clearError();
				}
				else {
					$feedback .= ' '._XF_TRK_GROUPINSERTED.' ';
				}
			}

		} elseif ($add_canned) {

			$acr = new ArtifactCanned($ath);
			if (!$acr || !is_object($acr)) {
				$feedback .= 'Unable to create ArtifactCanned Object';
			}
			else {
				if (!$acr->create($title,$body)) {
					$feedback .= ' Error inserting: '.$acr->getErrorMessage();
					$acr->clearError();
				}
				else {
					$feedback .= ' '._XF_TRK_CANNEDRESPONSEINSERTED.' ';
				}
			}

		} elseif ($add_users) {

			//
			//	if "add all" option, get list of group members
			//	who are not already members of this ArtifactType
			//
			if ($add_all) {
			  $sql = "SELECT u.uid "
				      ."FROM ".$xoopsDB->prefix("users")." u, ".$xoopsDB->prefix("xf_user_group")." ug "
							."LEFT JOIN ".$xoopsDB->prefix("xf_artifact_perm")." ap ON ap.user_id=u.uid AND ap.group_artifact_id='$atid' "
							."WHERE ap.user_id IS NULL "
							."AND ug.group_id='$group_id' "
							."AND u.uid=ug.user_id";

				$addids = util_result_column_to_array($xoopsDB->query($sql));
			}
			$count = count($addids);
			for ($i=0; $i<$count; $i++) {
				$ath->addUser($addids[$i]);
			}
			if ($ath->isError()) {
				$feedback .= $ath->getErrorMessage();
				$ath->clearError();
			}
			else {
				$feedback .= ' '._XF_TRK_USERSADDED.' ';
			}
			//go to the perms page
			$add_users = false;
			$update_users = true;

		} elseif ($update_users) {

			//
			//	Handle the 2-D array of user_id/permission level
			//
			$count = count($updateids);
			for ($i=0; $i<$count; $i++) {
				$ath->updateUser($updateids[$i][0],$updateids[$i][1]);
			}
			if ($ath->isError()) {
				$feedback .= $ath->getErrorMessage();
				$ath->clearError();
			}
			else {
				$feedback .= ' '._XF_TRK_USERSUPDATED.' ';
			}

			//
			//	Delete the checked ids
			//
			$count = count($deleteids);
			for ($i=0; $i<$count; $i++) {
				$ath->deleteUser($deleteids[$i]);
			}
			if ($ath->isError()) {
				$feedback .= $ath->getErrorMessage();
				$ath->clearError();
			}
			else {
				$feedback .= ' '._XF_TRK_USERSDELETED.' ';
			}

		} elseif ($update_canned) {

			$acr = new ArtifactCanned($ath,$id);
			if (!$acr || !is_object($acr)) {
				$feedback .= 'Unable to create ArtifactCanned Object';
			}
			elseif ($acr->isError()) {
				$feedback .= $acr->getErrorMessage();
			}
			else {
				if (!$acr->update($_POST['title'],$_POST['body'])) {
					$feedback .= ' Error updating: '.$acr->getErrorMessage();
					$acr->clearError();
				}
				else {
					$feedback .= ' '._XF_TRK_CANNEDRESPUPDATED.' ';
					$update_canned=false;
					$add_canned=true;
				}
			}

		} elseif ($update_cat) {

			$ac = new ArtifactCategory($ath,$id);
			if (!$ac || !is_object($ac)) {
				$feedback .= 'Unable to create ArtifactCategory Object';
			}
			elseif ($ac->isError()) {
				$feedback .= $ac->getErrorMessage();
			}
			else {
				if (!$ac->update($_POST['name'],$_POST['assign_to'])) {
					$feedback .= ' Error updating: '.$ac->getErrorMessage();
					$ac->clearError();
				}
				else {
					$feedback .= ' '._XF_TRK_CATEGORYUPDATED.' ';
					$update_cat=false;
					$add_cat=true;
				}
			}

		} elseif ($update_group) {

			$ag = new ArtifactGroup($ath,$id);
			if (!$ag || !is_object($ag)) {
				$feedback .= 'Unable to create ArtifactGroup Object';
			}
			elseif ($ag->isError()) {
				$feedback .= $ag->getErrorMessage();
			}
			else {
				if (!$ag->update($_POST['name'])) {
					$feedback .= ' Error updating: '.$ag->getErrorMessage();
					$ag->clearError();
				}
				else {
					$feedback .= ' '._XF_TRK_GROUPUPDATED.' ';
					$update_group=false;
					$add_group=true;
				}
			}
		}
		elseif ($update_type) {
			if (!$ath->update($name,$description,$is_public,$allow_anon,$email_all,$email_address,
				$due_period,$status_timeout,$use_resolution,$submit_instructions,$browse_instructions)) {
				$feedback .= ' Error updating: '.$ath->getErrorMessage();
				$ath->clearError();
			}
			else {
				$feedback .= ' '._XF_TRK_TRACKERUPDATED.' ';
			}

		}
	}


//
//	FORMS TO ADD/UPDATE DATABASE
//
	if ($add_cat) {
//
//      FORM TO ADD CATEGORIES
//
 	        include ("../../../../header.php");
		$adminheader = $ath->adminHeader();

		$xoopsTpl->assign("project_title", $adminheader['title']);
		$xoopsTpl->assign("project_tabs", $adminheader['tabs']);
		$xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$content = "";
		$content .= "<H4>".sprintf(_XF_TRK_ADDCATEGORIESTO, $ath->getName())."</H4>";

		/*
			List of possible categories for this ArtifactType
		*/
		$result = $ath->getCategories();
		$content .= "<P>";
		$rows = $xoopsDB->getRowsNum($result);
		if ($result && $rows > 0) {
		        $content .= "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
			         ."<TR class='bg2'>"
			         ."<TD align='center'><b>"._XF_TRK_ID."</b></td>"
			         ."<TD align='center'><b>"._XF_TRK_TITLE."</b></td>"
			         ."</TR>";

			for ($i=0; $i < $rows; $i++) {
				$content .= "<TR class='".($i%2!=0?"bg2":"bg3")."'>"
				    ."<TD>".unofficial_getDBResult($result, $i, 'id')."</TD>"
						."<TD><A HREF='".$_SERVER['PHP_SELF']."?update_cat=1&id="
						.unofficial_getDBResult($result, $i, 'id')."&group_id=".$group_id."&atid=".$ath->getID()."'>"
						.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'category_name'))."</A></TD></TR>";
			}
			$content .= "</TABLE>";
		}
		else {
			$content .= "\n<H4>"._XF_TRK_NOCATEGORIESDEF."</H4>";
		}

		$content .='
		<P>
		<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="add_cat" VALUE="y">
		<B>'._XF_TRK_NEWCATEGORYNAME.':</B><BR>
		<INPUT TYPE="TEXT" NAME="name" VALUE="" SIZE="15" MAXLENGTH="30"><BR>
		<P>
		<B>'._XF_TRK_AUTOASSIGNTO.':</B><BR>
		'.$ath->technicianBox('assign_to').'
		<P>
		<B><FONT COLOR="RED">'._XF_TRK_ONCEADDCANNOTDELETE.'</FONT></B>
		<P>
		<INPUT TYPE="SUBMIT" NAME="post_changes" VALUE="'._XF_G_SUBMIT.'">
		</FORM>';

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");

	} elseif ($add_group) {
//
//  FORM TO ADD GROUP
//
 	        include ("../../../../header.php");
		$adminheader = $ath->adminHeader();

		$xoopsTpl->assign("project_title", $adminheader['title']);
		$xoopsTpl->assign("project_tabs", $adminheader['tabs']);
		$xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$content = "<H4>".sprintf(_XF_TRK_ADDGROUPSTO, $ath->getName())."</H4>";

		/*
			List of possible groups for this ArtifactType
		*/
		$result = $ath->getGroups();
		$content .= "<P>";
		$rows = $xoopsDB->getRowsNum($result);
		if ($result && $rows > 0) {
		        $content .= "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
			         ."<TR class='bg2'>"
			         ."<TD align='center'><b>"._XF_TRK_ID."</b></td>"
			         ."<TD align='center'><b>"._XF_TRK_TITLE."</b></td>"
			         ."</TR>";

			for ($i=0; $i < $rows; $i++) {
				$content .= "<TR class='".($i%2!=0?"bg2":"bg3")."'>"
				    ."<TD>".unofficial_getDBResult($result, $i, 'id')."</TD>"
						."<TD><A HREF='".$_SERVER['PHP_SELF']."?update_group=1&id="
						.unofficial_getDBResult($result, $i, 'id')."&group_id=".$group_id."&atid=".$ath->getID()."'>"
						.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'group_name'))."</A></TD></TR>";
			}
			$content .= "</TABLE>";
		} else {
			$content .= "\n<H4>"._XF_TRK_NOGROUPSDEF."</H4>";
		}

		$content .= '
		<P>
		<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="add_group" VALUE="y">
		<B>'._XF_TRK_NEWGROUPNAME.':</B><BR>
		<INPUT TYPE="TEXT" NAME="name" VALUE="" SIZE="15" MAXLENGTH="30"><BR>
		<P>
		<B><FONT COLOR="RED">'._XF_TRK_ONCEADDGROUPCANNOTDELETE.'</FONT></B>
		<P>
		<INPUT TYPE="SUBMIT" NAME="post_changes" VALUE="'._XF_G_SUBMIT.'">
		</FORM>';

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");

	} elseif ($add_canned) {
//
//  FORM TO ADD CANNED RESPONSES
//
 	        include ("../../../../header.php");
		$adminheader = $ath->adminHeader();

		$xoopsTpl->assign("project_title", $adminheader['title']);
		$xoopsTpl->assign("project_tabs", $adminheader['tabs']);
		$xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$content = "<H4>".sprintf(_XF_TRK_ADDRESPONSESTO, $ath->getName())."</H4>";

		/*
			List of existing canned responses
		*/
		$result=$ath->getCannedResponses();
		$content .= "<P>";
		$rows = $xoopsDB->getRowsNum($result);

		if ($result && $rows > 0) {
		        $content .= "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
			         ."<TR class='bg2'>"
				 ."<TD align='center'><b>"._XF_TRK_ID."</b></td>"
			  	 ."<TD align='center'><b>"._XF_TRK_TITLE."</b></td>"
				 ."</TR>";

			for ($i=0; $i < $rows; $i++) {
				$content .= "<TR class='".($i%2!=0?"bg2":"bg3")."'>"
				    ."<TD>".unofficial_getDBResult($result, $i, 'id')."</TD>"
						."<TD><A HREF='".$_SERVER['PHP_SELF']."?update_canned=1&id="
						.unofficial_getDBResult($result, $i, 'id')."&group_id=".$group_id."&atid=".$ath->getID()."'>"
						.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'title'))."</A></TD></TR>";
			}
			$content .= "</TABLE>";
		}
		else {
			$content .= "\n<H4>"._XF_TRK_NORESPONSESDEF."</H4>";
		}

		$content .= '
		<P>'._XF_TRK_GENERICMESSAGESISUSEFUL.'
		<P>
		<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="add_canned" VALUE="y">
		<b>'._XF_TRK_TITLE.':</b><BR>
		<INPUT TYPE="TEXT" NAME="title" VALUE="" SIZE="50" MAXLENGTH="50">
		<P>
		<B>'._XF_TRK_MESSAGEBODY.':</B><BR>
		<TEXTAREA NAME="body" ROWS="30" COLS="65" WRAP="HARD"></TEXTAREA>
		<P>
		<INPUT TYPE="SUBMIT" NAME="post_changes" VALUE="'._XF_G_SUBMIT.'">
		</FORM>';

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");

	} elseif ($update_users) {
//
//  FORM TO ADD/UPDATE USERS
//
 	        include ("../../../../header.php");
	        $adminheader = $ath->adminHeader();

	        $xoopsTpl->assign("project_title", $adminheader['title']);
	        $xoopsTpl->assign("project_tabs", $adminheader['tabs']);
	        $xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$content = '';

		$sql = "SELECT ap.id,ap.group_artifact_id,ap.user_id,ap.perm_level,u.uname,u.name "
		      ."FROM ".$xoopsDB->prefix("xf_artifact_perm")." ap, ".$xoopsDB->prefix("users")." u "
					."WHERE u.uid=ap.user_id "
					."AND ap.group_artifact_id='".$ath->getID()."'";
		$res = $xoopsDB->query($sql);

		if (!$res || $xoopsDB->getRowsNum($res) < 1) {
			$content = '<H4>'._XF_TRK_NODEVELOPERSFOUND.'</H4>';
		} else {
			$content = '
			<P>
			'._XF_TRK_TRACKERPERMINFO.'
			<P>
			<dt><B>'._XF_TRK_TECHNICIANS.'</B></dt>
			<dd>'._XF_TRK_TECHNICIANSINFO.'</dd>

			<dt><B>'._XF_TRK_ADMINS.'</B></dt>
			<dd>'._XF_TRK_ADMINSINFO.'</dd>

			<FORM action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="post">
			<INPUT TYPE="HIDDEN" NAME="update_users" VALUE="y">';


  		        $content .= "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
			         ."<TR class='bg2'>"
				 ."<TD align='center'><b>"._XF_G_DELETE."</b></td>"
				 ."<TD align='center'><b>"._XF_TRK_USERNAME."</b></td>"
				 ."<TD align='center'><b>"._XF_TRK_PERMISSION."</b></td>"
				 ."</TR>";

			$i=0;
			//
			//	PHP4 allows multi-dimensional arrays to be passed in from form elements
			//
			while ($row_dev = $xoopsDB->fetchArray($res)) {
				$content .= '
				<INPUT TYPE="HIDDEN" NAME="updateids['.$i.'][0]" VALUE="'.$row_dev['user_id'].'">
				<TR class="'.($i%2!=0?'bg2':'bg3').'">
				<TD><INPUT TYPE="CHECKBOX" NAME="deleteids[]" VALUE="'.$row_dev['user_id'].'"> '._XF_G_DELETE.'</TD>

				<TD>'.$row_dev['name'].' ( '. $row_dev['uname'] .' )</TD>

				<TD><FONT size="-1"><SELECT name="updateids['.$i.'][1]">
				<OPTION value="0"'.(($row_dev['perm_level']==0)?" selected":"").'>-
				<OPTION value="1"'.(($row_dev['perm_level']==1)?" selected":"").'>'._XF_TRK_TECHNICIAN.'
				<OPTION value="2"'.(($row_dev['perm_level']==2)?" selected":"").'>'._XF_TRK_ADMINTECH.'
				<OPTION value="3"'.(($row_dev['perm_level']==3)?" selected":"").'>'._XF_TRK_ADMINONLY.'
				</SELECT></FONT></TD>

				</TR>';
				$i++;
			}
			$content .= '<TR><TD COLSPAN=3 ALIGN=MIDDLE><INPUT type="submit" name="post_changes" value="'._XF_TRK_UPDATEPERMISSIONS.'">
			</FORM></TD></TR>';
			$content .= '</TABLE>';
		}
		$content .= '
		<P>
		<h3>'._XF_TRK_ADDTHESEUSERS.':</H3>
		<P>
		'._XF_TRK_ADDTHESEUSERSINFO.'
		<P>
		<CENTER>
		<FORM action="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" method="post">
		<INPUT TYPE="HIDDEN" NAME="add_users" VALUE="y">';

	        $sql = "SELECT u.uid, u.uname "
		      ."FROM ".$xoopsDB->prefix("users")." u, ".$xoopsDB->prefix("xf_user_group")." ug "
				  ."LEFT JOIN ".$xoopsDB->prefix("xf_artifact_perm")." ap ON ap.user_id=u.uid AND ap.group_artifact_id='$atid' "
					."WHERE ap.user_id IS NULL "
				  ."AND ug.group_id='$group_id' "
					."AND u.uid=ug.user_id";

		$res = $xoopsDB->query($sql);
		$content .= $xoopsDB->error();
		$content .= html_build_multiple_select_box ($res,'addids[]',array(),8,false);
		$content .= '<P>
		<INPUT type="submit" name="post_changes" value="'._XF_TRK_ADDUSERS.'">&nbsp;<INPUT type="checkbox" name="add_all"> '._XF_TRK_ADDALLUSERS.'
		</FORM>
		</CENTER>';

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");

	} elseif ($update_canned) {
//
//	FORM TO UPDATE CANNED MESSAGES
//
 	        include ("../../../../header.php");
		$adminheader = $ath->adminHeader();

		$xoopsTpl->assign("project_title", $adminheader['title']);
		$xoopsTpl->assign("project_tabs", $adminheader['tabs']);
		$xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$content = "<H4>".sprintf(_XF_TRK_UPDATECANNEDRESP, $ath->getName())."</H4>";
		$acr = new ArtifactCanned($ath,$id);
		if (!$acr || !is_object($acr)) {
			$feedback .= 'Unable to create ArtifactCanned Object';
		}
		elseif ($acr->isError()) {
			$feedback .= $acr->getErrorMessage();
		}
		else {
			$content .= '
			<P>
			'._XF_TRK_GENERICMESSAGESISUSEFUL.'
			<P>
			<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">
			<INPUT TYPE="HIDDEN" NAME="update_canned" VALUE="y">
			<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$acr->getID().'">
			<b>'._XF_TRK_TITLE.':</b><BR>
			<INPUT TYPE="TEXT" NAME="title" VALUE="'.$ts->makeTboxData4Edit($acr->getTitle()).'" SIZE="50" MAXLENGTH="50">
			<P>
			<B>'._XF_TRK_MESSAGEBODY.':</B><BR>
			<TEXTAREA NAME="body" ROWS="30" COLS="65" WRAP="HARD">'.$ts->makeTareaData4Edit($acr->getBody()).'</TEXTAREA>
			<P>
			<INPUT TYPE="SUBMIT" NAME="post_changes" VALUE="'._XF_G_SUBMIT.'">
			</FORM>';
		}

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");

	} elseif ($update_cat) {
//
//  FORM TO UPDATE CATEGORIES
//
		/*
			Allow modification of a artifact category
		*/
 	        include ("../../../../header.php");
		$adminheader = $ath->adminHeader();

		$xoopsTpl->assign("project_title", $adminheader['title']);
		$xoopsTpl->assign("project_tabs", $adminheader['tabs']);
		$xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$content = '
			<H4>'.sprintf(_XF_TRK_MODIFYCATEGORYIN, $ath->getName()).'</H4>';

		$ac = new ArtifactCategory($ath,$id);
		if (!$ac || !is_object($ac)) {
			$feedback .= 'Unable to create ArtifactCategory Object';
		} elseif ($ac->isError()) {
			$feedback .= $ac->getErrorMessage();
		} else {
		$content = '
			<P>
			<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">
			<INPUT TYPE="HIDDEN" NAME="update_cat" VALUE="y">
			<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$ac->getID().'">
			<P>
			<B>'. _XF_TRK_CATEGORYNAME.':</B><BR>
			<INPUT TYPE="TEXT" NAME="name" VALUE="'.$ts->makeTboxData4Edit($ac->getName()).'">
			<P>
			<B><'._XF_TRK_AUTOASSIGNTO.':</B><BR>
			'.$ath->technicianBox('assign_to',$ac->getAssignee()).'
			<P>
			<INPUT TYPE="SUBMIT" NAME="post_changes" VALUE="'._XF_G_SUBMIT.'">
			</FORM>';
		}

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");
		//$ath->footer();

	} elseif ($update_group) {
//
//  FORM TO UPDATE GROUPS
//
		/*
			Allow modification of a artifact group
		*/
 	        include ("../../../../header.php");
		$adminheader = $ath->adminHeader();

		$xoopsTpl->assign("project_title", $adminheader['title']);
		$xoopsTpl->assign("project_tabs", $adminheader['tabs']);
	        $xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$ag = new ArtifactGroup($ath,$id);
		if (!$ag || !is_object($ag)) {
			$feedback .= 'Unable to create ArtifactGroup Object';
		}
		elseif ($ag->isError()) {
			$feedback .= $ag->getErrorMessage();
		}
		else {
			$content .= '
			<P>
			<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">
			<INPUT TYPE="HIDDEN" NAME="update_group" VALUE="y">
			<INPUT TYPE="HIDDEN" NAME="id" VALUE="'.$ag->getID().'">
			<P>
			<B>'._XF_TRK_GROUPNAME.':</B><BR>
			<INPUT TYPE="TEXT" NAME="name" VALUE="'.$ts->makeTboxData4Edit($ag->getName()).'">
			<P>
			<INPUT TYPE="SUBMIT" NAME="post_changes" VALUE="'._XF_G_SUBMIT.'">
			</FORM>';
		}

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");

	} elseif ($update_type) {
//
//	FORM TO UPDATE ARTIFACT TYPES
//
 	        include ("../../../../header.php");
		$adminheader = $ath->adminHeader();

		$xoopsTpl->assign("project_title", $adminheader['title']);
		$xoopsTpl->assign("project_tabs", $adminheader['tabs']);
		$xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$content .= '
		<P>
		<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="update_type" VALUE="y">
		<P>
		<B>'._XF_TRK_NAME.':</B> ('._XF_TRK_NAMEEXAMPLES.')<BR>';
		if ($ath->getDataType()) {
			$content .= $ath->getName();
		} else {

			$content .= '<INPUT TYPE="TEXT" NAME="name" VALUE="'.$ts->makeTboxData4Edit($ath->getName()).'">';

		}
		$content .= '
		<P>
		<B>'._XF_G_DESCRIPTION.':</B><BR>';
		if ($ath->getDataType()) {
			$content .= $ath->getDescription();
		} else {

			$content .= '<INPUT TYPE="TEXT" NAME="description" VALUE="'.$ts->makeTareaData4Edit($ath->getDescription()).'" SIZE="50">';
		}

		$content .= '
		<P>
		<INPUT TYPE=CHECKBOX NAME="is_public" VALUE="1" '.(($ath->isPublic())?'CHECKED':'').'> <B>'._XF_TRK_PUBLICLYAVAILABLE.'</B><BR>
		<INPUT TYPE=CHECKBOX NAME="allow_anon" VALUE="1" '.(($ath->allowsAnon())?'CHECKED':'').'> <B>'._XF_TRK_ALLOWNONLOGGEDINPOST.'</B><BR>
		<INPUT TYPE=CHECKBOX NAME="use_resolution" VALUE="1" '.(($ath->useResolution())?'CHECKED':'').'> <B>'._XF_TRK_DISPLAYRESOLUTION.'</B>
		<P>
		<B>'._XF_TRK_SENDMAILONSUBMISSION.':</B><BR>
		<INPUT TYPE="TEXT" NAME="email_address" VALUE="'.$ath->getEmailAddress().'">
		<P>
		<INPUT TYPE=CHECKBOX NAME="email_all" VALUE="1" '.(($ath->emailAll())?'CHECKED':'').'> <B>'._XF_TRK_SENDMAILONCHANGES.'</B><BR>
		<P>
		<B>'._XF_TRK_DAYSTILLOVERDUE.':</B><BR>
		<INPUT TYPE="TEXT" NAME="due_period" VALUE="'.($ath->getDuePeriod() / 86400).'">
		<P>
		<B>'._XF_TRK_DAYSTILLTIMEOUT.':</B><BR>
		<INPUT TYPE="TEXT" NAME="status_timeout"  VALUE="'.($ath->getStatusTimeout() / 86400).'">
		<P>
		<B>'._XF_TRK_FREEFORMSUBMIT.':</B><BR>
		<TEXTAREA NAME="submit_instructions" ROWS="10" COLS="55" WRAP="HARD">'.$ts->makeTareaData4Edit($ath->getSubmitInstructions()).'</TEXTAREA>
		<P>
		<B>'._XF_TRK_FREEFORMBROWSE.':</B><BR>
		<TEXTAREA NAME="browse_instructions" ROWS="10" COLS="55" WRAP="HARD">'.$ts->makeTareaData4Edit($ath->getBrowseInstructions()).'</TEXTAREA>
		<P>
		<INPUT TYPE="SUBMIT" NAME="post_changes" VALUE="'._XF_G_SUBMIT.'">
		</FORM>';

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");

	} else {
//
//  SHOW LINKS TO FEATURES
//
 	        include ("../../../../header.php");
		$adminheader = $ath->adminHeader();

		$xoopsTpl->assign("project_title", $adminheader['title']);
		$xoopsTpl->assign("project_tabs", $adminheader['tabs']);
		$xoopsTpl->assign("adminheader", $adminheader['nav']);

		$xoopsTpl->assign("feedback", $feedback);

		$content .= '<P>
			<A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&add_cat=1"><B>'._XF_TRK_ADDUPDATECATEGORIES.'</B></A><BR>
			'._XF_TRK_ADDUPDATECATEGORIESINFO.'<P>';
		$content .= '<P>
			<A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&add_group=1"><B>'._XF_TRK_ADDUPDATEGROUPS.'</B></A><BR>
			'._XF_TRK_ADDUPDATEGROUPSINFO.'<P>';
		$content .= '<P>
			<A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&add_canned=1"><B>'._XF_TRK_ADDUPDATECANNEDRESP.'</B></A><BR>
			'._XF_TRK_ADDUPDATECANNEDRESPINFO.'<P>';
		$content .= '<P>
			<A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&update_users=1"><B>'._XF_TRK_ADDUPDATEUSERS.'</B></A><BR>
			'._XF_TRK_ADDUPDATEUSERSINFO.'<P>';
		$content .= '<P>
			<A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&atid='.$ath->getID().'&update_type=1"><B>'._XF_TRK_UPDATEPREFERENCES.'</B></A><BR>
			'._XF_TRK_UPDATEPREFERENCESINFO.'<P>';

		$xoopsTpl->assign("content", $content);
		include ("../../../../footer.php");
	}

} elseif ($group_id) {

	if (!$perm->isArtifactAdmin()) {
		redirect_header(XOOPS_URL."/",2,_XF_G_PERMISSIONDENIED."<br />"._XF_TRK_NOTTRACKERMANAGER);
		exit;
	}

	if ($post_changes) {
	//var_dump('post_changes');
		if ($add_at) {
			$res=new ArtifactTypeHtml($group);
			if (!$res->create($name,$description,$is_public,$allow_anon,$email_all,$email_address,
				$due_period,$use_resolution,$submit_instructions,$browse_instructions)) {
				$feedback .= $res->getErrorMessage();
			} else {
				header ("Location: ".XOOPS_URL."/modules/xfmod/tracker/admin/?group_id=$group_id&atid=".$res->getID()."&update_users=1");
			}
		}
	}

	include ("../../../../header.php");

  	$xoopsTpl->assign("project_title", project_title($group));
  	$xoopsTpl->assign("project_tabs", project_tabs ('tracker', $group_id));
	$xoopsTpl->assign("adminheader", $adminheader['nav']);

	$xoopsTpl->assign("feedback", $feedback);

	if($xoopsUser->isAdmin()){
		$content = "<B><A HREF='index.php?group_id=".$group_id."'>"._XF_G_ADMIN."</A></B><P/>";
  	}

	$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_artifact_group_list")." WHERE group_id='$group_id' ORDER BY group_artifact_id";
	$result = $xoopsDB->query ($sql);
	$rows = $xoopsDB->getRowsNum($result);
	if (!$result || $rows < 1) {
		$content .= "<H4>"._XF_TRK_NOTRACKERSFOUND."</H4>";
		$content .= "<P>";
	} else {

		$content .= '<P>'._XF_TRK_CHOOSEDATATYPETOCHANGE.'<P>';

		/*
			Put the result set (list of forums for this group) into a column with folders
		*/

		for ($j = 0; $j < $rows; $j++) {
			$content .= '
			<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/admin/?atid='.unofficial_getDBResult($result, $j, 'group_artifact_id').
			'&group_id='.$group_id.'"> <img src="'.XOOPS_URL.'/modules/xfmod/images/ic/index.png" width="24" height="24" BORDER="0" alt="index"> &nbsp;'.
			$ts->makeTboxData4Show(unofficial_getDBResult($result, $j, 'name')).'</A><BR>'.
			$ts->makeTareaData4Show(unofficial_getDBResult($result, $j, 'description')).'<P>';
		}
	}

	$content .= _TRACKER_TEXT;

	$content .= '
	<P>
	<FORM ACTION="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="add_at" VALUE="y">
	<P>
	<B>'._XF_TRK_NAME.':</B> ('._XF_TRK_NAMEEXAMPLES.')<BR>
	<INPUT TYPE="TEXT" NAME="name" VALUE="">
	<P>
	<B>'._XF_G_DESCRIPTION.':</B><BR>
	<INPUT TYPE="TEXT" NAME="description" VALUE="" SIZE="50">
	<P>
	<INPUT TYPE=CHECKBOX NAME="is_public" VALUE="1"> <B>'._XF_TRK_PUBLICLYAVAILABLE.'</B><BR>
	<INPUT TYPE=CHECKBOX NAME="allow_anon" VALUE="1"> <B>'._XF_TRK_ALLOWNONLOGGEDINPOST.'</B><BR>
	<INPUT TYPE=CHECKBOX NAME="use_resolution" VALUE="1"> <B>'._XF_TRK_DISPLAYRESOLUTION.'</B>
	<P>
	<B>'._XF_TRK_SENDMAILONSUBMISSION.':</B><BR>
	<INPUT TYPE="TEXT" NAME="email_address" VALUE="">
	<P>
	<INPUT TYPE=CHECKBOX NAME="email_all" VALUE="1"> <B>'._XF_TRK_SENDMAILONCHANGES.'</B><BR>
	<P>
	<B>'._XF_TRK_DAYSTILLOVERDUE.':</B><BR>
	<INPUT TYPE="TEXT" NAME="due_period" VALUE="30">
	<P>
	<B>'._XF_TRK_DAYSTILLTIMEOUT.':</B><BR>
	<INPUT TYPE="TEXT" NAME="status_timeout" VALUE="14">
	<P>
	<B>'._XF_TRK_FREEFORMSUBMIT.':</B><BR>
	<TEXTAREA NAME="submit_instructions" ROWS="10" COLS="55" WRAP="HARD"></TEXTAREA>
	<P>
	<B>'._XF_TRK_FREEFORMBROWSE.':</B><BR>
	<TEXTAREA NAME="browse_instructions" ROWS="10" COLS="55" WRAP="HARD"></TEXTAREA>
	<P>
	<INPUT TYPE="SUBMIT" NAME="post_changes" VALUE="'._XF_G_SUBMIT.'">
	</FORM>';

	$xoopsTpl->assign("content", $content);

	include ("../../../../footer.php");

} else {

  //browse for group first message
  redirect_header(XOOPS_URL."/",2,"ERROR<br />No Group");
  exit;
}

?>