<?php
/**
  *
  * SourceForge Documentaion Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: doc_utils.php,v 1.13 2004/03/16 01:42:12 jcox Exp $
  *
  */

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");

function docman_header($project,$group_id,$pagehead,$style='xyz') {
	global $xoopsTpl,$xoopsUser;
	
	if (!$project->usesDocman()) {
	redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_DOC_PROJECTTURNEDOFFDOC);
	exit;
	}
	// get current information if $perm is false
	$group =& group_get_object($group_id);
	$perm  =& $group->getPermission( $xoopsUser );

	//meta tag information
	$metaTitle=" "._XF_DOC_PROJECTDOCUMENTATION." - ".$project->getPublicName();
	$metaKeywords=project_getmetakeywords($group->getID());
	$metaDescription=strip_tags($group->getDescription());

	$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
	$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
	$xoopsTpl->assign("xoops_meta_description", $metaDescription);

	// nav information
	$xoopsTpl->assign("project_title",project_title($group));
	$xoopsTpl->assign("project_tabs",project_tabs ('docman', $group_id));
	
	$header = '<p/>';
	
	if ($perm->isDocEditor()) {

		$header .= "<b><a href='".XOOPS_URL."/modules/xfmod/docman/admin/index.php?mode=editdocs&group_id=".$group_id."'>"._XF_G_ADMIN."</a></b> | ";
		
		if ($style == 'admin') {
			$header .= "<b><a href='".XOOPS_URL."/modules/xfmod/docman/admin/index.php?mode=editgroups&group_id=".$group_id."'>"._XF_DOC_EDITDOCGROUPS."</a></b> | ";
		}

	}
	
	$header .= "<b><a href='".XOOPS_URL."/modules/xfmod/docman/index.php?group_id=".$group_id."'>"._XF_DOC_VIEWDOC."</a>"
				." | <a href='".XOOPS_URL."/modules/xfmod/docman/new.php?group_id=".$group_id."'>"._XF_DOC_SUBMITNEWDOC."</a></b>";
		
	return $header;
}

/*
	by Quentin Cregan, SourceForge 06/2000
*/
function display_groups_option($group_id=false,$checkedval='xzxz') {
  global $xoopsDB;
	
	if (!$group_id) {
		redirect_header($GLOBALS["HTTP_REFERER"],2,"ERROR<br />No Group.");
		exit;
	} else {
		$query = "SELECT doc_group, groupname "
		        ."FROM ".$xoopsDB->prefix("xf_doc_groups")." "
		        ."WHERE group_id='$group_id' "
            ."ORDER BY groupname";
		$result = $xoopsDB->query($query);

		return html_build_select_box ($result,'doc_group',$checkedval, false);

	} //end else

} //end display_groups_option


function display_groups($group_id) {
  global $xoopsDB, $ts;
	// show list of groups to edit.
	$content = "";
	
	$query = "SELECT * "
		      ."FROM ".$xoopsDB->prefix("xf_doc_groups")." "
		      ."WHERE group_id='$group_id'";

	$result = $xoopsDB->queryF($query);

	if ($xoopsDB->getRowsNum($result) < 1) {
		$content .= "<p>"._XF_DOC_NOGROUPSEXIST;
	} else {

    $content .= "<table border='0' width='100%'>"
        ."<tr class='bg2'>"
        ."<td><b>"._XF_DOC_GROUPID."</b></td>"
        ."<td><b>"._XF_DOC_GROUPNAME."</b></td>"
        ."<td><b>"._XF_DOC_CONTROLS."</b></td>"
  	    ."</tr>";

		$i = 0;
		while ($row = $xoopsDB->fetchArray($result)) {
			$content .= "<tr class='".($i%2>0?"bg1":"bg3")."'>"
			    ."<td>".$row['doc_group']."</td>\n"
					."<td>".$ts->makeTboxData4Show($row['groupname'])."</td>\n"
					."<td>[ <a href='".XOOPS_URL."/modules/xfmod/docman/admin/index.php?mode=groupdelete&doc_group=".$row['doc_group']."&group_id=".$group_id."'>"._XF_G_DELETE."</A> ] [ <a href='".XOOPS_URL."/modules/xfmod/docman/admin/index.php?mode=groupedit&doc_group=".$row['doc_group']."&group_id=".$group_id."'>"._XF_G_CHANGE."</a> ]\n</td>"
				  ."</tr>\n";

			$i++;
		}
		$content .= "</table>";
	}
	return $content;
}

function display_doc_feedback($group_id, $docid, $limit)
{
  global $xoopsDB, $ts;
  $answer_arr = array("--",_NO,_YES);
  $content = "";
  
  $res_feedback = $xoopsDB->query("SELECT answer_yes,answer_no,answer_na FROM ".$xoopsDB->prefix("xf_doc_feedback_agg")." WHERE docid='".$docid."'");

  if ($xoopsDB->getRowsNum ($res_feedback) > 0)
  {
    $query = "SELECT uname,answer,suggestion "
            ."FROM ".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_doc_feedback")." df "
            ."WHERE u.uid=df.user_id "
            ."AND df.docid='".$docid."' "
            ."ORDER BY entered DESC";

    $res = $xoopsDB->query($query, $limit);
    $numrows = $xoopsDB->getRowsNum ($res);
    $answer_yes = unofficial_getDBResult ($res_feedback, 0, 'answer_yes');
    $answer_no  = unofficial_getDBResult ($res_feedback, 0, 'answer_no');

    $content .= "<table border='0' width='100%'>"
        ."<tr class='bg2'>"
        ."<td colspan='3'>"._XF_DOC_DIDITANSWER." "._YES.": ".$answer_yes." "._NO.": ".$answer_no." (".$numrows.")</td>"
        ."</tr>"
        ."<tr class='bg2'>"
        ."<td width='10%'><b>"._XF_DOC_USER."</b></td>"
        ."<td width='5%'><b>"._XF_DOC_ANSWER."</b></td>"
        ."<td><b>"._XF_DOC_SUGGESTION."</b></td>"
        ."</tr>";

    $i = 0;
    while ($row = $xoopsDB->fetchArray($res)) {
      $content .= "<tr class='".($i%2>0?"bg1":"bg3")."'>"
          ."<td>".$row['uname']."</td>\n"
          ."<td>".$answer_arr[$row['answer']]."</td>"
          ."<td>".$ts->makeTareaData4Show($row['suggestion'])."</td>\n"
          ."</tr>\n";
      $i++;
    }
		// TODO: Add ShowAll Function to DOCMAN
    $content .= "<tr>"
        ."<td colspan='3'>[ <a href='".XOOPS_URL."/modules/xfmod/docman/admin/index.php?mode=showfeedback&docid=".$row['docid']."&group_id=".$group_id."'>"._XF_DOC_SHOWALL."</A> ]</td>"
        ."</tr></table>";
  }
  else
  {
    $content .= "<H4>"._XF_DOC_NOFEEDBACKFORDOCUMENT."</H4>";
  }
  return $content;
}

/**
 * get_group_count returns the number of document cateogries that the project has.
 *
 * @author Dominick Bellizzi (dbellizzi@valinux.com)
 * @param $group_id The project group ID
 * @return int The number of document groups for the specified project, or false on an error
 */
function get_group_count($group_id){
  global $xoopsDB;
	// show list of groups to edit.
	$query = "SELECT COUNT(*) AS count "
		      ."FROM ".$xoopsDB->prefix("xf_doc_groups")." "
		      ."WHERE group_id='$group_id'";

	$result = $xoopsDB->queryF($query);

  return unofficial_getDBResult($result, 0, 'count');
}// end function get_group_count

function display_docs($style,$group_id) {
	global $xoopsDB, $ts, $sys_datefmt;
	$content = "";
  
	$query = "SELECT d1.docid,d1.title,d1.updatedate,d1.createdate,d1.data,d2.groupname,d2.doc_group "
		      ."FROM ".$xoopsDB->prefix("xf_doc_data")." d1, ".$xoopsDB->prefix("xf_doc_groups")." d2 "
		      ."WHERE d1.stateid='".$style."' "
		      ."AND d2.group_id='".$group_id."' "
		      ."AND d1.doc_group=d2.doc_group "
					."ORDER BY d2.doc_group ASC";

	$result = $xoopsDB->query($query);

	if ($xoopsDB->getRowsNum($result) < 1) {

		$query = "SELECT name "
			      ."FROM ".$xoopsDB->prefix("xf_doc_states")." "
			      ."WHERE stateid='$style'";

		$result = $xoopsDB->query($query);
		$row = $xoopsDB->fetchArray($result);
		$content .= sprintf (_XF_DOC_NONAMEDOCSAVAILABLE, $row['name']).' <p>';

	} else {

    $content .= "<table border='0' width='100%'>"
        ."<tr class='bg2'>"
        ."<td><b>"._XF_DOC_NAME."</b></td>"
        ."<td><b>"._XF_DOC_DOCUMENTID."</b></td>";
	if($style==2){
		$content .= "<td><b>"._XF_DOC_DELETEDBY."</b></td>";
	}
    $content .= "<td><b>"._XF_DOC_UPDATEDATE."</b></td>"
        ."<td><b>"._XF_DOC_CREATEDATE."</b></td>"
  	    ."</tr>";

		$i = 0;
		$current_doc_group = -1;
		while ($row = $xoopsDB->fetchArray($result)) {
			if ($row['doc_group'] > $current_doc_group) {
				$current_doc_group = $row['doc_group'];
				$content .= "<tr class='".($i++%2>0?"bg1":"bg3")."'>"
					."<td colspan='3'>";
				$content .= "<a href='".XOOPS_URL."/modules/xfmod/docman/admin/index.php?mode=groupedit&doc_group=".$row['doc_group']."&group_id=".$group_id."'>";
				$content .= "<b>".$row['groupname']."</b>";
				$content .= "</a>";
				$content .= "</td><td>&nbsp;</td></tr>";
			}
			$content .= "<tr class='".($i%2>0?"bg1":"bg3")."'>";
			$content .= "<td>";
			if($style!=2) $content .= "<a href='".XOOPS_URL."/modules/xfmod/docman/admin/index.php?docid=".$row['docid']."&mode=docedit&group_id=".$group_id."'>";
			$content .= $ts->makeTboxData4Show($row['title']);
			if($style!=2) $content .= "</a>";
			$content .= "</td>";
			$content .= "<td>".$row['docid']."</td>";
			if($style==2) $content .= "<td>".$row['data']."</td>";
			$content .= "<td>".date($sys_datefmt,$row['updatedate'])."</td>"
				."<td>".date($sys_datefmt,$row['createdate'])."</td></tr>";
			
			$i++;
		}
		$content .= '</table>';
	}//end else
	return $content;

} //end

function docman_feedback ()
{
 // TODO: fill in this function
}
?>