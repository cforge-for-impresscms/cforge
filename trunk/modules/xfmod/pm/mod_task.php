<?php
/**
  *
  * SourceForge Project/Task Manager (PM)
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: mod_task.php,v 1.4 2004/02/06 01:42:06 jcox Exp $
  *
  */

$xoopsOption['template_main'] = 'pm/xfmod_mod_task.html';

$project_task_id = util_http_track_vars('project_task_id');

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
$metaTitle=" "._XF_PM_MODIFYATASK." - ".$g->getPublicName();
$metaKeywords=project_getmetakeywords($group_id);
$metaDescription=str_replace('"', "&quot;", strip_tags($g->getDescription()));

$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
$xoopsTpl->assign("xoops_meta_description", $metaDescription);

// project nav information
$xoopsTpl->assign("project_title", project_title($g));
$xoopsTpl->assign("project_tabs", project_tabs ('pm', $group_id));

$header = pm_header($g, $perm, _XF_PM_MODIFYATASK, $group_project_id);
$xoopsTpl->assign("pm_header",$header);

$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_project_task")." "
      ."WHERE project_task_id='$project_task_id' "
      ."AND group_project_id='$group_project_id'";

$result = $xoopsDB->query($sql);

$content = "

<FORM ACTION='".$_SERVER['PHP_SELF']."' METHOD='POST'>
<INPUT TYPE='HIDDEN' NAME='func' VALUE='postmodtask'>
<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>
<INPUT TYPE='HIDDEN' NAME='group_project_id' VALUE='".$group_project_id."'>
<INPUT TYPE='HIDDEN' NAME='project_task_id' VALUE='".$project_task_id."'>

<TABLE BORDER='0' WIDTH='100%'>
	<TR>
		<TD><B>"._XF_PM_SUBPROJECT.":</B>
		<BR>";
		$content .= pm_show_subprojects_box('new_group_project_id',$group_id,$group_project_id);
$content .= "
		</TD>

		<TD>
		<INPUT TYPE='submit' value='"._XF_G_CHANGE."' name='submit'>
		</TD>
	</TR>

	<TR>
		<TD><B>"._XF_PM_PERCENTCOMPLETE.":</B>
		<BR>";
		$content .= pm_show_percent_complete_box('percent_complete',unofficial_getDBResult($result,0,'percent_complete'));
$content .= "
		</TD>

		<TD><B>"._XF_G_PRIORITY.":</B>
		<BR>";
		$content .= build_priority_select_box('priority',unofficial_getDBResult($result,0,'priority'));
$content .= "
		</TD>
	</TR>

  	<TR>
		<TD COLSPAN='2'><B>"._XF_G_SUMMARY.":</B>
		<BR>
		<INPUT TYPE='text' name='summary' size='40' MAXLENGTH='65' VALUE='".$ts->makeTboxData4Edit(unofficial_getDBResult($result,0,'summary'))."'>
		</TD>
	</TR>

	<TR>
		<TD COLSPAN='2'>
		<B>"._XF_PM_ORIGINALCOMMENT.":</B>
		<P>";
		$content .= $ts->makeTareaData4Show(unofficial_getDBResult($result,0,'details'));
$content .= "
		<P>
		<B>"._XF_PM_ADDACOMMENT.":</B>
		<BR>
		<TEXTAREA NAME='details' ROWS='5' COLS='40' WRAP='SOFT'></TEXTAREA>
		</TD>
	</TR>

	<TR>
    		<TD COLSPAN='2'><B>"._XF_PM_STARTDATE.":</B>
		<BR>";
		$content .= pm_show_month_box ('start_month',date('m', unofficial_getDBResult($result,0,'start_date')));
		$content .= pm_show_day_box ('start_day',date('d', unofficial_getDBResult($result,0,'start_date')));
		$content .= pm_show_year_box ('start_year',date('Y', unofficial_getDBResult($result,0,'start_date')));
$content .= "
		</TD>
	</TR>

	<TR>
		<TD COLSPAN='2'><B>"._XF_PM_ENDDATE.":</B>
		<BR>";
		$content .= pm_show_month_box ('end_month',date('m', unofficial_getDBResult($result,0,'end_date')));
		$content .= pm_show_day_box ('end_day',date('d', unofficial_getDBResult($result,0,'end_date')));
		$content .= pm_show_year_box ('end_year',date('Y', unofficial_getDBResult($result,0,'end_date')));
$content .= "
		</TD>
	</TR>

	<TR>
		<TD>
		<B>"._XF_G_ASSIGNEDTO.":</B>
		<BR>";
		/*
			List of possible users that this one could be assigned to
		*/
		$content .= pm_multiple_assigned_box ('assigned_to[]',$group_id,$project_task_id);
$content .= "
		</TD>

		<TD>
		<B>"._XF_PM_DEPENDENTONTASK.":</B>
		<BR>";
		/*
			List of possible tasks that this one could depend on
		*/
		$content .= pm_multiple_task_depend_box ('dependent_on[]',$group_project_id,$project_task_id);
$content .= "
		</TD>
	</TR>

	<TR>
		<TD>
		<B>"._XF_PM_HOURS.":</B>
		<BR>
		<INPUT TYPE='text' name='hours' size='5' VALUE='".unofficial_getDBResult($result,0,'hours')."'>
		</TD>

		<TD>
		<B>"._XF_PM_STATUS.":</B>
		<BR>";
		$content .= pm_status_box ('status_id',unofficial_getDBResult($result,0,'status_id'));
$content .= "
		</TD>
	</TR>

	<TR>
		<TD COLSPAN='2'>";
			$content .= pm_show_dependent_tasks ($project_task_id,$group_id,$group_project_id);
$content .= "
		</TD>
	</TR>

	<TR>
		<TD COLSPAN='2'>";
			$content .= pm_show_task_details ($project_task_id);
$content .= "
		</TD>
	</TR>

	<TR>
		<TD COLSPAN='2'>";
			$content .= pm_show_task_history ($project_task_id);
$content .= "
		</TD>
	</TR>

	<TR>
		<TD COLSPAN='2' ALIGN='MIDDLE'>
		<INPUT TYPE='submit' value='"._XF_G_CHANGE."' name='submit'>
		</TD>
		</form>
	</TR>

</table>";

  $xoopsTpl->assign("content",$content);

?>