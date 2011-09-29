<?php
/**
  *
  * SourceForge Documentaion Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: sample_utils.php,v 1.12 2004/03/16 01:41:18 jcox Exp $
  *
  */

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");

function sampleman_header($project,$group_id,$pagehead,$style='xyz') {
  global $xoopsTpl,$xoopsUser;
	
	if (!$project->usesSamples()) {
	  redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_SC_PROJECTTURNEDOFFCODE);
	  exit;
	}
	// get current information if $perm is false
		$group =& group_get_object($group_id);
		$perm  =& $group->getPermission( $xoopsUser );

	//meta tag information
	$metaTitle=" "._XF_SC_PROJECTSAMPLECODE." - ".$project->getPublicName();
	$metaKeywords=project_getmetakeywords($group->getID());
	$metaDescription=strip_tags($group->getDescription());

	$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
	$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
	$xoopsTpl->assign("xoops_meta_description", $metaDescription);

	// nav information
	$xoopsTpl->assign("project_title",project_title($group));
	$xoopsTpl->assign("project_tabs",project_tabs ('sample', $group_id));

  	$header = '<p/>';
  
	if ($perm->isSampleEditor()) {

		$header .= "<b><a href='".XOOPS_URL."/modules/xfmod/sample/admin/index.php?mode=editsamples&group_id=".$group_id."'>"._XF_G_ADMIN."</a></b> | ";
		if ($style == 'admin') {
			$header .= "<b><a href='".XOOPS_URL."/modules/xfmod/sample/admin/index.php?mode=editgroups&group_id=".$group_id."'>"._XF_SC_EDITCODEGROUPS."</a></b> | ";
		}
	}

	$header .= "<b><a href='".XOOPS_URL."/modules/xfmod/sample/index.php?group_id=".$group_id."'>"._XF_SC_VIEWCODE."</a>"
				." | <a href='".XOOPS_URL."/modules/xfmod/sample/new.php?group_id=".$group_id."'>"._XF_SC_SUBMITNEWCODE."</a></b>";

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
		$query = "SELECT sample_group, groupname "
		        ."FROM ".$xoopsDB->prefix("xf_sample_groups")." "
		        ."WHERE group_id='$group_id' "
            ."ORDER BY groupname";
		$result = $xoopsDB->query($query);

		return html_build_select_box ($result,'sample_group',$checkedval, false);
	} //end else
} //end display_groups_option


function display_groups($group_id) {
  global $xoopsDB, $ts;
	// show list of groups to edit.

	$query = "SELECT * "
		      ."FROM ".$xoopsDB->prefix("xf_sample_groups")." "
		      ."WHERE group_id='$group_id'";

	$result = $xoopsDB->queryF($query);
	$content = "";
	if ($xoopsDB->getRowsNum($result) < 1) {
		$content .= "<p>"._XF_SC_NOGROUPSEXIST;
	} else {

    $content .= "<table border='0' width='100%'>"
        ."<tr class='bg2'>"
        ."<td><b>"._XF_SC_GROUPID."</b></td>"
        ."<td><b>"._XF_SC_GROUPNAME."</b></td>"
        ."<td><b>"._XF_SC_CONTROLS."</b></td>"
  	    ."</tr>";

		$i = 0;
		while ($row = $xoopsDB->fetchArray($result)) {
			$content .= "<tr class='".($i%2>0?"bg1":"bg3")."'>"
			    ."<td>".$row['sample_group']."</td>\n"
					."<td>".$ts->makeTboxData4Show($row['groupname'])."</td>\n"
					."<td>[ <a href='".XOOPS_URL."/modules/xfmod/sample/admin/index.php?mode=groupdelete&sample_group=".$row['sample_group']."&group_id=".$group_id."'>"._XF_G_DELETE."</A> ] [ <a href='".XOOPS_URL."/modules/xfmod/sample/admin/index.php?mode=groupedit&sample_group=".$row['sample_group']."&group_id=".$group_id."'>"._XF_G_CHANGE."</a> ]\n</td>"
				  ."</tr>\n";

			$i++;
		}
		$content .= "</table>";
	}
	return $content;
}

function display_sample_feedback ($group_id, $sampleid, $limit)
{
  global $xoopsDB, $ts;
  $answer_arr = array("--",_NO,_YES);
  $content = "";
  
  $res_feedback = $xoopsDB->query("SELECT answer_yes,answer_no,answer_na FROM ".$xoopsDB->prefix("xf_sample_feedback_agg")." WHERE sampleid='".$sampleid."'");

  if ($xoopsDB->getRowsNum ($res_feedback) > 0)
  {
    $query = "SELECT uname,answer,suggestion "
            ."FROM ".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_sample_feedback")." df "
            ."WHERE u.uid=df.user_id "
            ."AND df.sampleid='".$sampleid."' "
            ."ORDER BY entered DESC";

    $res = $xoopsDB->query($query, $limit);
    $numrows = $xoopsDB->getRowsNum ($res);
    $answer_yes = unofficial_getDBResult ($res_feedback, 0, 'answer_yes');
    $answer_no  = unofficial_getDBResult ($res_feedback, 0, 'answer_no');

    $content .= "<table border='0' width='100%'>"
        ."<tr class='bg2'>"
        ."<td colspan='3'>"._XF_SC_DIDITANSWER." "._YES.": ".$answer_yes." "._NO.": ".$answer_no." (".$numrows.")</td>"
        ."</tr>"
        ."<tr class='bg2'>"
        ."<td width='10%'><b>"._XF_SC_USER."</b></td>"
        ."<td width='5%'><b>"._XF_SC_ANSWER."</b></td>"
        ."<td><b>"._XF_SC_SUGGESTION."</b></td>"
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

    $content .= "<tr>"
        ."<td colspan='3'>[ <a href='".XOOPS_URL."/modules/xfmod/sample/admin/index.php?mode=showfeedback&sampleid=".$row['sampleid']."&group_id=".$group_id."'>"._XF_SC_SHOWALL."</A> ]</td>"
        ."</tr></table>";
  }
  else
  {
    $content .= "<H4>"._XF_SC_NOFEEDBACKFORCODE."</H4>";
  }
  return $content;
}

/**
 * get_group_count returns the number of sampleument cateogries that the project has.
 *
 * @author Dominick Bellizzi (dbellizzi@valinux.com)
 * @param $group_id The project group ID
 * @return int The number of sampleument groups for the specified project, or false on an error
 */
function get_group_count($group_id){
  global $xoopsDB;
	// show list of groups to edit.
	$query = "SELECT COUNT(*) AS count "
		      ."FROM ".$xoopsDB->prefix("xf_sample_groups")." "
		      ."WHERE group_id='$group_id'";

	$result = $xoopsDB->queryF($query);

  return unofficial_getDBResult($result, 0, 'count');
}// end function get_group_count

function display_samples($style,$group_id) {
	global $xoopsDB, $ts, $sys_datefmt;
  	$content = "";
	
	$query = "SELECT d1.sampleid,d1.title,d1.updatedate,d1.createdate,d1.data,d2.groupname,d2.sample_group "
		      ."FROM ".$xoopsDB->prefix("xf_sample_data")." d1, ".$xoopsDB->prefix("xf_sample_groups")." d2 "
		      ."WHERE d1.stateid='".$style."' "
		      ."AND d2.group_id='".$group_id."' "
		      ."AND d1.sample_group=d2.sample_group "
					."ORDER BY d2.sample_group ASC";

	$result = $xoopsDB->query($query);

	if ($xoopsDB->getRowsNum($result) < 1) {

		$query = "SELECT name "
			      ."FROM ".$xoopsDB->prefix("xf_sample_states")." "
			      ."WHERE stateid='$style'";

		$result = $xoopsDB->query($query);
		$row = $xoopsDB->fetchArray($result);
		$content .= sprintf (_XF_SC_NONAMECODEAVAILABLE, $row['name']).' <p>';

	} else {

    $content .= "<table border='0' width='100%'>"
        ."<tr class='bg2'>"
        ."<td><b>"._XF_SC_NAME."</b></td>"
        ."<td><b>"._XF_SC_SAMPLETID."</b></td>";
	if($style==2){
		$content .= "<td><b>"._XF_SC_DELETEDBY."</b></td>";
	}
    $content .= "<td><b>"._XF_SC_UPDATEDATE."</b></td>"
        ."<td><b>"._XF_SC_CREATEDATE."</b></td>"
  	    ."</tr>";

		$i = 0;
		$current_sample_group = -1;
		while ($row = $xoopsDB->fetchArray($result)) {
			if ($row['sample_group'] > $current_sample_group) {
				$current_sample_group = $row['sample_group'];
				$content .= "<tr class='".($i++%2>0?"bg1":"bg3")."'>"
					."<td colspan='3'>";
				$content .= "<a href='".XOOPS_URL."/modules/xfmod/sample/admin/index.php?mode=groupedit&sample_group=".$row['sample_group']."&group_id=".$group_id."'>";
				$content .= $row['groupname'];
				$content .= "</a>";
				$content .= "</td><td>&nbsp;</td></tr>";
			}
			$content .= "<tr class='".($i%2>0?"bg1":"bg3")."'>";
			$content .= "<td>";
			if($style!=2)	$content .= "<a href='".XOOPS_URL."/modules/xfmod/sample/admin/index.php?sampleid=".$row['sampleid']."&mode=sampleedit&group_id=".$group_id."'>";
			$content .= $ts->makeTboxData4Show($row['title']);
			if($style!=2) $content .= "</a>";
			$content .= "</td>";
			$content .= "<td>".$row['sampleid']."</td>";
			if($style==2) $content .= "<td>".$row['data']."</td>";
			$content .= "<td>".date($sys_datefmt,$row['updatedate'])."</td>"
				."<td>".date($sys_datefmt,$row['createdate'])."</td></tr>";
			
			$i++;
		}
		$content .= '</table>';
	}//end else
	return $content;
} //end

function sampleman_feedback ()
{
 // TODO: fill in this function
}
?>