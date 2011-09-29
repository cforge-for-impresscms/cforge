<?php
/**
  *
  * SourceForge Project/Task Manager (PM)
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: detail_task.php,v 1.4 2004/02/06 01:42:06 jcox Exp $
  *
  */
  
$xoopsOption['template_main'] = 'pm/xfmod_detail_task.html';
  
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
$metaTitle=" "._XF_PM_VIEWATASK." - ".$g->getPublicName();
$metaKeywords=project_getmetakeywords($group_id);
$metaDescription=str_replace('"', "&quot;", strip_tags($g->getDescription()));

$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
$xoopsTpl->assign("xoops_meta_description", $metaDescription);

// project nav information  
$xoopsTpl->assign("project_title", project_title($g));
$xoopsTpl->assign("project_tabs", project_tabs ('pm', $group_id));

$header = pm_header($g, $perm, _XF_PM_VIEWATASK, $group_project_id);
$xoopsTpl->assign("pm_header",$header);

$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_project_task")." "
      ."WHERE project_task_id='$project_task_id' "
      ."AND group_project_id='$group_project_id'";

$result = $xoopsDB->query($sql);

$content .= "

<TABLE BORDER='0' WIDTH='100%'>
	<TR>
		<TD><B>"._XF_PM_PERCENTCOMPLETE.":</B>
		<BR>";
		$content .= unofficial_getDBResult($result,0,'percent_complete');
$content .= "	%
		</TD>

		<TD><B>"._XF_G_PRIORITY.":</B>
		<BR>";
		$content .= unofficial_getDBResult($result,0,'priority');
$content .= "
		</TD>
	</TR>

  <TR>
		<TD COLSPAN='2'><B>"._XF_G_SUMMARY.":</B>
		<BR>";
		$content .= $ts->makeTboxData4Show(unofficial_getDBResult($result,0,'summary'));
$content .= "
		</TD>
	</TR>

	<TR>
		<TD COLSPAN='2'>
		<B>"._XF_PM_ORIGINALCOMMENT.":</B>
		<P>";
		$content .= $ts->makeTareaData4Show(unofficial_getDBResult($result,0,'details'));
$content .= "
		</TD>
	</TR>

	<TR>
  	<TD COLSPAN='2'><B>"._XF_PM_STARTDATE.":</B>
		<BR>";
		$content .= date('Y-m-d', unofficial_getDBResult($result,0,'start_date'));
$content .= "
		</TD>
	</TR>

	<TR>
		<TD COLSPAN='2'><B>"._XF_PM_ENDDATE.":</B>
		<BR>";
		$content .= date('Y-m-d', unofficial_getDBResult($result,0,'end_date'));
$content .= "
		</TD>
	</TR>

	<TR>
		<TD VALIGN='TOP'>";

		/*
			Get the list of ids this is assigned to and convert to array
			to pass into multiple select box
		*/

		$result2 = $xoopsDB->query("SELECT u.uname AS User_Name "
		                          ."FROM ".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_project_assigned_to")." pat "
                              ."WHERE u.uid=pat.assigned_to_id "
                              ."AND project_task_id='$project_task_id'");

		$content .= ShowResultSet($result2,_XF_G_ASSIGNEDTO);
$content .= "
		</TD>
		<TD VALIGN='TOP'>";
		
		/*
			Get the list of ids this is dependent on and convert to array
			to pass into multiple select box
		*/
		$result2 = $xoopsDB->query("SELECT pt.summary "
                              ."FROM ".$xoopsDB->prefix("xf_project_dependencies")." pd,".$xoopsDB->prefix("xf_project_task")." pt "
                              ."WHERE is_dependent_on_task_id=pt.project_task_id "
                              ."AND pd.project_task_id='$project_task_id'");

		$content .= ShowResultSet($result2,_XF_PM_DEPENDENTONTASK);
$content .= "
		</TD>
	</TR>

	<TR>
		<TD>
		<B>"._XF_PM_HOURS.":</B>
		<BR>";
		$content .= unofficial_getDBResult($result,0,'hours'); 
$content .= "
		</TD>

		<TD>
		<B>"._XF_PM_STATUS.":</B>
		<BR>";
		
		$content .= pm_data_get_status_name(unofficial_getDBResult($result,0,'status_id'));
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

</table>";

  $xoopsTpl->assign("content",$content);

?>