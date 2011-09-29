<?php
/**
  *
  * SourceForge Project/Task Manager (PM)
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: add_task.php,v 1.5 2004/02/06 01:42:06 jcox Exp $
  *
  */

include_once ("../../../mainfile.php");

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
$xoopsOption['template_main'] = 'pm/xfmod_add_task.html';

/* http_track_vars */
$group_id = util_http_track_vars('group_id');

$g =& group_get_object($group_id);
$perm  =& $g->getPermission( $xoopsUser );

//group is private
if (!$g->isPublic()) {
  //if it's a private group, you must be a member of that group
  if (!$g->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
	{
	  redirect_header(XOOPS_URL."/",4,_XF_PRJ_PROJECTMARKEDASPRIVATE);
	  exit;
	}
}

//meta tag information
$metaTitle=" "._XF_PM_ADDATASK." - ".$g->getPublicName();
$metaKeywords=project_getmetakeywords($group_id);
$metaDescription=str_replace('"', "&quot;", strip_tags($g->getDescription()));

$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
$xoopsTpl->assign("xoops_meta_description", $metaDescription);

// project nav information
$xoopsTpl->assign("project_title", project_title($g));
$xoopsTpl->assign("project_tabs", project_tabs ('pm', $group_id));

$header = pm_header($g, $perm, _XF_PM_ADDATASK, $group_project_id);
$xoopsTpl->assign("pm_header",$header);

$content = "

<FORM ACTION='".$_SERVER['PHP_SELF']."' METHOD='POST'>
<INPUT TYPE='HIDDEN' NAME='func' VALUE='postaddtask'>
<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>
<INPUT TYPE='HIDDEN' NAME='group_project_id' VALUE='".$group_project_id."'>

<TABLE BORDER='0' WIDTH='100%'>
	<TR>
		<TD>
			<B>"._XF_PM_PERCENTCOMPLETE.":</B>
			<BR>";
			$content .= pm_show_percent_complete_box();
$content .= "
		</TD>
		<TD>
			<B>"._XF_G_PRIORITY.":</B>
			<BR>";
			$content .= build_priority_select_box();
$content .= "
		</td>
	</TR>

  	<TR>
		<TD COLSPAN='2'><B>"._XF_G_SUMMARY.":</B>
		<BR>
		<INPUT TYPE='text' name='summary' size='40' MAXLENGTH='65'>
		</td>
	</TR>
	<TR>
		<TD COLSPAN='2'><B>"._XF_PM_TASKDETAILS.":</B>
		<BR>
		<TEXTAREA NAME='details' ROWS='5' COLS='40' WRAP='SOFT'></TEXTAREA></td>
	</TR>
	<TR>
    		<TD COLSPAN='2'><B>"._XF_PM_STARTDATE.":</B>
		<BR>";
		$content .= pm_show_month_box ('start_month',date('m', time()));
		$content .= pm_show_day_box ('start_day',date('d', time()));
		$content .= pm_show_year_box ('start_year',date('Y', time()));
$content .="
                </td>

	</TR>
	<TR>
		<TD COLSPAN='2'><B>"._XF_PM_ENDDATE.":</B>
		<BR>";
		$content .= pm_show_month_box ('end_month',date('m', time()));
		$content .= pm_show_day_box ('end_day',date('d', time()));
		$content .= pm_show_year_box ('end_year',date('Y', time()));
$content .= "
		</td>

	</TR>
	<TR>
		<TD>
		<B>"._XF_G_ASSIGNEDTO.":</B>
		<BR>";
		$content .= pm_multiple_assigned_box ('assigned_to[]',$group_id);
$content .= "
		</td>
		<TD>
		<B>"._XF_PM_DEPENDENTONTASK.":</B>
		<BR>";
		$content .= pm_multiple_task_depend_box ('dependent_on[]',$group_project_id);
$content .= "
		</TD>
	</TR>
	<TR>
		<TD COLSPAN='2'><B>"._XF_PM_HOURS.":</B>
		<BR>
		<INPUT TYPE='text' name='hours' size='5'>
		</td>
	</TR>
	<TR>
		<TD COLSPAN='2'>
		<INPUT TYPE='submit' value='"._XF_G_SUBMIT."' name='submit'>
		</td>
		</form>
	</TR>
</TABLE>";

  $xoopsTpl->assign("content",$content);

?>
