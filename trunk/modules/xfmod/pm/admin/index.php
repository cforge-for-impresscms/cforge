<?php
/**
  *
  * SourceForge Project/Task Manager (PM)
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.4 2004/02/05 23:26:56 jcox Exp $
  *
  */
include_once ("../../../../mainfile.php");

$langfile="pm.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/pm/pm_utils.php");
$xoopsOption['template_main'] = 'pm/admin/xfmod_index.html';

/* http_track_vars */
$group_id = util_http_track_vars('group_id');

/*
	Project / Task Manager Admin
	By Tim Perdue Nov. 1999
*/

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isPMAdmin()) {
   redirect_header($GLOBALS["HTTP_REFERER"],2,_XF_G_PERMISSIONDENIED."<br />"._XF_PM_YOUNOTASKMANAGER);
   exit;
}

if ($post_changes) {
  /*
      Update the database
  */

  if ($projects) {
    /*
      Insert a new project
    */

    $sql = "INSERT INTO ".$xoopsDB->prefix("xf_project_group_list")." (group_id,project_name,is_public,description) "
          ."VALUES ('$group_id','". $ts->makeTboxData4Save($project_name) ."','$is_public','". $ts->makeTboxData4Save($description) ."')";

    $result = $xoopsDB->queryF($sql);
    if (!$result) {
      $feedback .= " Error inserting value ";
      $feedback .= $xoopsDB->error();
    }

    $feedback .= " "._XF_PM_SUBPROJECTINSERTED." ";
  }
  else if ($change_status) {
    /*
      Change a project to public/private
    */
    $sql = "UPDATE ".$xoopsDB->prefix("xf_project_group_list")." SET "
          ."is_public='$is_public',"
          ."project_name='". $ts->makeTboxData4Save($project_name) ."',"
          ."description='". $ts->makeTboxData4Save($description) ."' "
          ."WHERE group_id='$group_id' "
          ."AND group_project_id='$group_project_id'";

    $result = $xoopsDB->queryF($sql);
    if (!$result) {
      $feedback .= " "._XF_PM_ERRORUPDATESTATUS." ";
      $feedback .= $xoopsDB->error();
    }
    else {
      $feedback .= " "._XF_PM_STATUSUPDATED." ";
    }
  }
}

include (XOOPS_ROOT_PATH."/header.php");

  //meta tag information
  $metaTitle=" "._XF_PM_TASKS." - ".$group->getPublicName();
  $metaKeywords=project_getmetakeywords($group_id);
  $metaDescription=str_replace('"', "&quot;", strip_tags($group->getDescription()));

  $xoopsTpl->assign("xoops_pagetitle", $metaTitle);
  $xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
  $xoopsTpl->assign("xoops_meta_description", $metaDescription);

  // project nav information
  $xoopsTpl->assign("project_title", project_title($group));
  $xoopsTpl->assign("project_tabs", project_tabs ('pm', $group_id));

/*
  Show UI forms
*/

if ($projects) {
  /*
    Show categories and blank row
  */

  $header = pm_header($group, $perm, _XF_PM_ADDPROJECTS, $group_project_id);
  $xoopsTpl->assign("pm_header",$header);
  /*
    List of possible categories for this group
  */
  $sql = "SELECT group_project_id,project_name FROM ".$xoopsDB->prefix("xf_project_group_list")." WHERE group_id='$group_id'";
  $result = $xoopsDB->query($sql);
  $content .= "<P>";
  if ($result && $xoopsDB->getRowsNum($result) > 0) {
    ShowResultSet($result, _XF_PM_EXISTINGPROJECTS);
  }
  else {
    //$content .= "\n<H4>"._XF_PM_NOSUBPROJECTSFOUND."</H4>";
  }

  $content .= "
  <P>
	"._XF_PM_ADDPROJECTINFO."
  </P>
  <FORM ACTION='".$_SERVER['PHP_SELF']."' METHOD='POST'>
  <INPUT TYPE='HIDDEN' NAME='projects' VALUE='y'>
  <INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>
  <INPUT TYPE='HIDDEN' NAME='post_changes' VALUE='y'>
  <P>
  <B>"._XF_G_ISPUBLIC."</B><BR>
  <INPUT TYPE='RADIO' NAME='is_public' VALUE='1' CHECKED> "._YES."<BR>
  <INPUT TYPE='RADIO' NAME='is_public' VALUE='0'> "._NO."<P>
  <P>
  <H4 style='text-align:left;'>"._XF_PM_NEWPROJECTNAME." :</H4>
  <P>
  <INPUT TYPE='TEXT' NAME='project_name' VALUE='' SIZE='15' MAXLENGTH='30'>
  <P>
  <B>"._XF_G_DESCRIPTION." :</B><BR>
  <INPUT TYPE='TEXT' NAME='description' VALUE='' SIZE='40' MAXLENGTH='80'>
  <P>
  <INPUT TYPE='SUBMIT' NAME='SUBMIT' VALUE='"._XF_G_SUBMIT."'>
  </FORM>";

} else if ($change_status) {
  /*
    Change a project to public/private
  */

  $header = pm_header($group, $perm, _XF_PM_CHANGEPROJECT, $group_project_id);
  $xoopsTpl->assign("pm_header",$header);

  $sql = "SELECT project_name,group_project_id,is_public,description "
        ."FROM ".$xoopsDB->prefix("xf_project_group_list")." "
        ."WHERE group_id='$group_id'";

  $result = $xoopsDB->query($sql);
  $rows = $xoopsDB->getRowsNum($result);

  if (!$result || $rows < 1) {
    $content .= '
         <H4>'._XF_PM_NOSUBPROJECTSFOUND.'</H4>
         <P>';
         $content .= $xoopsDB->error();
  }
  else {
    $content .= '
    <P>'._XF_PM_MAKEPRIVATEINFO.'<P>';

    $content .= "<table border='0' width='100%'>"
        ."<tr class='bg2'>"
        ."<td><b>"._XF_PM_STATUS."</b></td>"
        ."<td><b>"._XF_PM_NAME."</b></td>"
        ."<td><b>"._XF_G_UPDATE."</b></td>"
        ."</tr>";

    for ($i=0; $i<$rows; $i++) {
      $content .= '<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
            <INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
            <INPUT TYPE="HIDDEN" NAME="change_status" VALUE="y">
            <INPUT TYPE="HIDDEN" NAME="group_project_id" VALUE="'.unofficial_getDBResult($result,$i,'group_project_id').'">
            <INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">';

      $content .= '<TR class="'.($j%2>0?'bg1':'bg3').'"><TD>
              <FONT SIZE="-1">
              <B>'._XF_G_ISPUBLIC.'</B><BR>
              <INPUT TYPE="RADIO" NAME="is_public" VALUE="1"'.((unofficial_getDBResult($result,$i,'is_public')=='1')?' CHECKED':'').'> '._YES.'<BR>
              <INPUT TYPE="RADIO" NAME="is_public" VALUE="0"'.((unofficial_getDBResult($result,$i,'is_public')=='0')?' CHECKED':'').'> '._NO.'<BR>
              <INPUT TYPE="RADIO" NAME="is_public" VALUE="9"'.((unofficial_getDBResult($result,$i,'is_public')=='9')?' CHECKED':'').'> '._XF_G_DELETED.'<BR>
            </TD><TD>
              <INPUT TYPE="TEXT" NAME="project_name" VALUE="'. $ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'project_name')) .'">
            </TD><TD>
              <FONT SIZE="-1">
              <INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_G_UPDATE.'">
            </TD></TR>
            <TR class="'.($j%2>0?'bg1':'bg3').'"><TD COLSPAN="3">
              <B>'._XF_G_DESCRIPTION.':</B><BR>
              <INPUT TYPE="TEXT" NAME="description" VALUE="'.$ts->makeTboxData4Show(unofficial_getDBResult($result,$i,'description')) .'" SIZE="40" MAXLENGTH="80"><BR>
            </TD></TR>
            </FORM>';
    }
    $content .= '</TABLE>';
  }

} else {
  /*
    Show main page
  */
  $header = pm_header($group, $perm, 'Project/Task Manager Administration', $group_project_id);
  $xoopsTpl->assign("pm_header",$header);

  $content .= '<P>
        <A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&projects=1"><B>'._XF_PM_ADDASUBPROJECT.'</B></A>
        <BR>'._XF_PM_ADDASUBPROJECTINFO.'
        <P/>
        <A HREF="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&change_status=1"><B>'._XF_PM_UPDATEINFORMATION.'</B></A>
        <BR>'._XF_PM_UPDATEINFORMATIONINFO;
}

  $xoopsTpl->assign("content",$content);
  include (XOOPS_ROOT_PATH."/footer.php");

?>