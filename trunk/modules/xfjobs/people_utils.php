<?php
/**
 * SourceForge Jobs (aka Help Wanted) Board
 *
 * Job/People finder
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @athor		Tim Perdue <tperdue@valinux.com>
 * @version		$Id: people_utils.php,v 1.9 2004/01/06 16:42:50 jcox Exp $
 *
 */

function people_header($group_id, $job_id, $deljob=false)
{
    global $xoopsTheme, $xoopsForgeErrorHandler, $xoopsConfig;
    $prjheading = "";
    $prjhelpfor = "";

    $content = "";

    if ($group_id) {
	    $group =& group_get_object($group_id);

	    if ( $group->isProject() )
	      {
		$prjheading = _XF_G_PROJECT;
		$prjhelpfor = _XF_PEO_PROJECTHELPFOR;
	      }
	    else
	      {
		$prjheading = _XF_G_COMM;
		$prjhelpfor = _XF_PEO_COMMHELPFOR;
	      }

	    $content .= project_title($group);
	    $content .= project_tabs ('admin', $group_id);
    }
    else {
	    $content .= "<H2>"._XF_PEO_HELPREQUESTS."</H2>";
    }

    if ($group_id && $job_id)
    {
	if($deljob != true)
	{
		//$content .= '<b><a href="'.XOOPS_URL.'/modules/xfjobs/editjob.php?group_id='.
		//$group_id.'&job_id='.$job_id.'">'._XF_PEO_EDITJOB.'</a></b>';
	}
    }

//	var_dump($xoopsForgeErrorHandler);
	$content .= $xoopsForgeErrorHandler->getDisplayFeedback();
	return $content;
}

function people_footer() {
//  CloseTable();
//  include (XOOPS_ROOT_PATH."/footer.php");
}


function people_skill_box($name='skill_id',$checked='xyxy') {
	global $PEOPLE_SKILL, $xoopsDB;

	if (!$PEOPLE_SKILL) {
		//will be used many times potentially on a single page
		$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_people_skill")." ORDER BY name ASC";
		$PEOPLE_SKILL = $xoopsDB->query($sql);
	}
	return html_build_select_box ($PEOPLE_SKILL, $name, $checked);
}

function people_skill_level_box($name='skill_level_id',$checked='xyxy') {
	global $PEOPLE_SKILL_LEVEL, $xoopsDB;

	if (!$PEOPLE_SKILL_LEVEL) {
		//will be used many times potentially on a single page
		$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_people_skill_level");
		$PEOPLE_SKILL_LEVEL = $xoopsDB->query($sql);
	}
	return html_build_select_box ($PEOPLE_SKILL_LEVEL,$name,$checked);
}

function people_skill_year_box($name='skill_year_id',$checked='xyxy') {
	global $PEOPLE_SKILL_YEAR, $xoopsDB;

	if (!$PEOPLE_SKILL_YEAR) {
		//will be used many times potentially on a single page
		$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_people_skill_year");
		$PEOPLE_SKILL_YEAR = $xoopsDB->query($sql);
	}
	return html_build_select_box ($PEOPLE_SKILL_YEAR,$name,$checked);
}

function people_job_status_box($name='status_id',$checked='xyxy') {
	global $xoopsDB;

	$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_people_job_status");
	$result = $xoopsDB->query($sql);

	return html_build_select_box ($result,$name,$checked);
}

function people_job_category_box($name='category_id',$checked='100') {
	global $xoopsDB;

	$sql = "SELECT category_id,name FROM ".$xoopsDB->prefix("xf_people_job_category")." WHERE private_flag=0";
	$result = $xoopsDB->query($sql);

	return html_build_select_box ($result,$name,$checked);
}

function people_add_to_skill_inventory($skill_id,$skill_level_id,$skill_year_id) {
	global $xoopsDB, $xoopsUser, $xoopsForgeErrorHandler;

	if ($xoopsUser)
	{
		//check if they've already added this skill
		$sql = "SELECT * "
		      ."FROM ".$xoopsDB->prefix("xf_people_skill_inventory")." "
					."WHERE user_id='".$xoopsUser->getVar("uid")."' "
					."AND skill_id='$skill_id'";

		$result = $xoopsDB->query($sql);

		if (!$result
			|| $xoopsDB->getRowsNum($result) < 1)
		{
			//skill not already in inventory
			$sql = "INSERT INTO ".$xoopsDB->prefix("xf_people_skill_inventory").
				" (user_id,skill_id,skill_level_id,skill_year_id) ".
				"VALUES ('".$xoopsUser->getVar("uid").
				"','$skill_id','$skill_level_id','$skill_year_id')";

			$result = $xoopsDB->queryF($sql);

			if (!$result)
			{
				$xoopsForgeErrorHandler->addError('ERROR inserting into skill inventory');
				echo $xoopsDB->error();
			}
			else
			{
				$xoopsForgeErrorHandler->addMessage(_XF_PEO_ADDEDTOSKILLINVENTORY);
			}
		}
		else
		{
			$xoopsForgeErrorHandler->addError('ERROR - '._XF_PEO_SKILLALREADYININVENTORY);
		}
	}
	else
	{
		echo '<H4>'._XF_PEO_MUSTBELOGGEDIN.'</H4>';
		$xoopsForgeErrorHandler->addError(_XF_PEO_MUSTBELOGGEDIN);
	}
}

function people_show_skill_inventory($user_id) {
	global $xoopsDB;

	$sql = "SELECT ps.name AS skill_name, psl.name AS level_name, psy.name AS year_name "
	      ."FROM ".$xoopsDB->prefix("xf_people_skill_year")." psy,".$xoopsDB->prefix("xf_people_skill_level")." psl,".$xoopsDB->prefix("xf_people_skill")." ps,".$xoopsDB->prefix("xf_people_skill_inventory")." psi "
				."WHERE psy.skill_year_id=psi.skill_year_id "
				."AND psl.skill_level_id=psi.skill_level_id "
				."AND ps.skill_id=psi.skill_id "
				."AND psi.user_id='$user_id'";

	$result = $xoopsDB->query($sql);

	$content = "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
      ."<TR class='bg2'>"
      ."<TD align='center'><b>"._XF_PEO_SKILL."</b></td>"
      ."<td align='center'><b>"._XF_PEO_LEVEL."</b></td>"
      ."<TD align='center'><b>"._XF_PEO_EXPERIENCE."</b></td>"
      ."</TR>";

	$rows = $xoopsDB->getRowsNum($result);
	if (!$result || $rows < 1) {
		$content .= '<H4>'._XF_PEO_NOSKILLINVENTORYSETUP.'</H4>';
		$content .= $xoopsDB->error();
	} else {
		for ($i=0; $i < $rows; $i++) {
			$content .= '
			<TR class="'.($i%2!=0?'bg2':'bg3').'">
			<TD>'.unofficial_getDBResult($result,$i,'skill_name').'</TD>
			<TD>'.unofficial_getDBResult($result,$i,'level_name').'</TD>
			<TD>'.unofficial_getDBResult($result,$i,'year_name').'</TD></TR>';

		}
	}
	$content .=  '</TABLE>';
	return $content;
}

function people_edit_skill_inventory($user_id)
{
	global $xoopsDB;
	$i = 0;

	$sql="SELECT * FROM ".$xoopsDB->prefix("xf_people_skill_inventory")." WHERE user_id='$user_id'";

	$result = $xoopsDB->query($sql);

	$content = "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
       ."<TR class='bg2'>"
       ."<TD align='center'><b>"._XF_PEO_SKILL."</b></td>"
       ."<td align='center'><b>"._XF_PEO_LEVEL."</b></td>"
       ."<TD align='center'><b>"._XF_PEO_EXPERIENCE."</b></td>"
       ."<TD align='center'><b>"._XF_PEO_ACTION."</b></td>"
       ."</TR>";

	$rows = $xoopsDB->getRowsNum($result);

	if (!$result || $rows < 1)
	{
		$content .= '
			<TR><TD COLSPAN="4"><H4>'._XF_PEO_NOSKILLINVENTORYSETUP.
				'</H4></TD></TR>';
		$content .= $xoopsDB->error();
	}
	else
	{
		for ($i = 0; $i < $rows; $i++)
		{
			$content .= '
			<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
			<INPUT TYPE="HIDDEN" NAME="skill_inventory_id" VALUE="'.
				unofficial_getDBResult($result,$i,'skill_inventory_id').'">
			<TR class="'.($i%2!=0?'bg2':'bg3').'">
				<TD><FONT SIZE="-1">'.
				people_get_skill_name(unofficial_getDBResult($result,$i,
					'skill_id')) .'</TD>
				<TD><FONT SIZE="-1">'. people_skill_level_box('skill_level_id',
					unofficial_getDBResult($result,$i,'skill_level_id')). '</TD>
				<TD><FONT SIZE="-1">'. people_skill_year_box('skill_year_id',
					unofficial_getDBResult($result,$i,'skill_year_id')). '</TD>
				<TD NOWRAP><FONT SIZE="-1"><INPUT TYPE="SUBMIT" NAME="update_skill_inventory" VALUE="Update"> &nbsp;
					<INPUT TYPE="SUBMIT" NAME="delete_from_skill_inventory" VALUE="'._XF_G_DELETE.'"></TD>
				</TR></FORM>';
		}

	}
	//add a new skill
	$i++; //for row coloring

	$content .= '
	<TR><TD COLSPAN="4"><H3>'._XF_PEO_ADDANEWSKILL.'</H3></TD></TR>
	<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
	<TR class="'.($i % 2 != 0 ? 'bg2' : 'bg3').'">
		<TD><FONT SIZE="-1">'. people_skill_box('skill_id'). '</TD>
		<TD><FONT SIZE="-1">'. people_skill_level_box('skill_level_id'). '</TD>
		<TD><FONT SIZE="-1">'. people_skill_year_box('skill_year_id'). '</TD>
		<TD NOWRAP><FONT SIZE="-1"><INPUT TYPE="SUBMIT" NAME="add_to_skill_inventory" VALUE="'._XF_PEO_ADDSKILL.'"></TD>
	</TR></FORM></TABLE>';

	return $content;
}


function people_add_to_job_inventory($job_id,$skill_id,$skill_level_id,$skill_year_id) {
  global $xoopsUser, $xoopsDB, $xoopsForgeErrorHandler;

  if ($xoopsUser) {
    //check if they've already added this skill
    $sql = "SELECT * FROM ".$xoopsDB->prefix("xf_people_job_inventory")." WHERE job_id='$job_id' AND skill_id='$skill_id'";

    $result = $xoopsDB->query($sql);
    if (!$result || $xoopsDB->getRowsNum($result) < 1) {
      //skill isn't already in this inventory
      $sql = "INSERT INTO ".$xoopsDB->prefix("xf_people_job_inventory")." (job_id,skill_id,skill_level_id,skill_year_id) "
            ."VALUES ('$job_id','$skill_id','$skill_level_id','$skill_year_id')";

      $result = $xoopsDB->queryF($sql);

      if (!$result) {
		$xoopsForgeErrorHandler->addError('ERROR inserting into skill inventory - '.$xoopsDB->error());
      } else {
		$xoopsForgeErrorHandler->addMessage(_XF_PEO_ADDEDTOSKILLINVENTORY);
      }
    } else {
		$xoopsForgeErrorHandler->addError('ERROR - '._XF_PEO_SKILLALREADYININVENTORY);
    }
  } else {
    $content = '<H4>'._XF_PEO_MUSTBELOGGEDIN.'</H4>';
  }

}

function people_show_job_inventory($job_id) {
  global $xoopsDB;

	$sql = "SELECT ps.name AS skill_name, psl.name AS level_name, psy.name AS year_name "
        ."FROM ".$xoopsDB->prefix("xf_people_skill_year")." psy,".$xoopsDB->prefix("xf_people_skill_level")." psl,".$xoopsDB->prefix("xf_people_skill")." ps,".$xoopsDB->prefix("xf_people_job_inventory")." pji "
        ."WHERE psy.skill_year_id=pji.skill_year_id "
        ."AND psl.skill_level_id=pji.skill_level_id "
        ."AND ps.skill_id=pji.skill_id "
        ."AND pji.job_id='$job_id'";

	$result = $xoopsDB->query($sql);

	$content = "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
       ."<TR class='bg2'>"
       ."<TD align='center'><b>"._XF_PEO_SKILL."</b></td>"
       ."<td align='center'><b>"._XF_PEO_LEVEL."</b></td>"
       ."<TD align='center'><b>"._XF_PEO_EXPERIENCE."</b></td>"
       ."</TR>";

	$rows = $xoopsDB->getRowsNum($result);
	if (!$result || $rows < 1) {
		$content .= '<H4>No Skill Inventory Set Up</H4>';
		$content .= $xoopsDB->error();
	} else {
		for ($i=0; $i < $rows; $i++) {
		$content .= '<TR class="'.($i%2!=0?'bg2':'bg3').'">
                <TD>'.unofficial_getDBResult($result,$i,'skill_name').'</TD>
                <TD>'.unofficial_getDBResult($result,$i,'level_name').'</TD>
                <TD>'.unofficial_getDBResult($result,$i,'year_name').'</TD></TR>';
		}
	}
	$content .= '</TABLE>';
	return $content;
}

function people_verify_job_group($job_id,$group_id) {
  global $xoopsDB;

	$sql = "SELECT job_id FROM ".$xoopsDB->prefix("xf_people_job")." WHERE job_id='$job_id' AND group_id='$group_id'";

	$result = $xoopsDB->query($sql);

	if (!$result || $xoopsDB->getRowsNum($result) < 1) {
		return false;
	} else {
		return true;
	}
}

function people_get_skill_name($skill_id) {
  global $xoopsDB;

	$sql="SELECT name FROM ".$xoopsDB->prefix("xf_people_skill")." WHERE skill_id='$skill_id'";
	$result = $xoopsDB->query($sql);
	if (!$result || $xoopsDB->getRowsNum($result) < 1) {
		return 'Invalid ID';
	} else {
		return unofficial_getDBResult($result,0,'name');
	}
}

function people_get_category_name($category_id) {
	$sql="SELECT name FROM people_job_category WHERE category_id='$category_id'";
	$result=db_query($sql);
	if (!$result || db_numrows($result) < 1) {
		return 'Invalid ID';
	} else {
		return db_result($result,0,'name');
	}
}

function people_edit_job_inventory($job_id,$group_id) {
	global $xoopsDB;
	$i = 0;

	$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_people_job_inventory")." WHERE job_id='$job_id'";

	$result = $xoopsDB->query($sql);

	$content = "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
       ."<TR class='bg2'>"
       ."<TD align='center'><b>"._XF_PEO_SKILL."</b></td>"
       ."<td align='center'><b>"._XF_PEO_LEVEL."</b></td>"
       ."<TD align='center'><b>"._XF_PEO_EXPERIENCE."</b></td>"
       ."<TD align='center'><b>"._XF_PEO_ACTION."</b></td>"
       ."</TR>";

	$rows = $xoopsDB->getRowsNum($result);
	if (!$result || $rows < 1)
	{
    	$content .= '<TR><TD COLSPAN="4"><H4>'._XF_PEO_NOSKILLINVENTORYSETUP.'</H4></TD></TR>';
    	$content .= $xoopsDB->error();
	}
	else
	{
    	for ($i=0; $i < $rows; $i++)
		{
			$content .= '
			<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
			<INPUT TYPE="HIDDEN" NAME="job_inventory_id" VALUE="'. unofficial_getDBResult($result,$i,'job_inventory_id') .'">
			<INPUT TYPE="HIDDEN" NAME="job_id" VALUE="'. unofficial_getDBResult($result,$i,'job_id') .'">
			<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
			<TR class="'.($i%2!=0?'bg2':'bg3').'">
			<TD><FONT SIZE="-1">'. people_get_skill_name(unofficial_getDBResult($result,$i,'skill_id')) . '</TD>
			<TD><FONT SIZE="-1">'. people_skill_level_box('skill_level_id',unofficial_getDBResult($result,$i,'skill_level_id')). '</TD>
			<TD><FONT SIZE="-1">'. people_skill_year_box('skill_year_id',unofficial_getDBResult($result,$i,'skill_year_id')). '</TD>
			<TD NOWRAP><FONT SIZE="-1">
			<INPUT TYPE="SUBMIT" NAME="update_job_inventory" VALUE="'._XF_G_UPDATE.'"> &nbsp;
			<INPUT TYPE="SUBMIT" NAME="delete_from_job_inventory" VALUE="'._XF_G_DELETE.'"></TD>
			</TR></FORM>';
	  }
	}

	//add a new skill
	$i++; //for row coloring

	$content .= '
	<TR><TD COLSPAN="4"><H3>'._XF_PEO_ADDANEWSKILL.'</H3></TD></TR>
	<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD="POST">
	<TR class="'.($i % 2 != 0 ? 'bg2' : 'bg3').'">
	<INPUT TYPE="HIDDEN" NAME="job_id" VALUE="'. $job_id .'">
	<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
	<TD><FONT SIZE="-1">'. people_skill_box('skill_id'). '</TD>
	<TD><FONT SIZE="-1">'. people_skill_level_box('skill_level_id'). '</TD>
	<TD><FONT SIZE="-1">'. people_skill_year_box('skill_year_id'). '</TD>
	<TD NOWRAP><FONT SIZE="-1"><INPUT TYPE="SUBMIT" NAME="add_to_job_inventory" VALUE="'._XF_PEO_ADDSKILL.'"></TD>
	</TR></FORM></TABLE></TABLE>';

	return $content;
}

function people_show_category_table() {

  global $xoopsDB;

	$content = "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
            ."<TR class='bg2'>"
            ."<TD><b>"._XF_PEO_CATEGORY."</b><p/></td>"
            ."</TR>";

	$sql = "SELECT pjc.category_id, pjc.name, COUNT(pj.category_id) AS total "
	      ."FROM ".$xoopsDB->prefix("xf_people_job_category")." pjc "
				."LEFT JOIN ".$xoopsDB->prefix("xf_people_job")." pj "
				."ON pjc.category_id=pj.category_id "
				."WHERE pjc.private_flag=0 "
				."AND (pj.status_id=1 OR pj.status_id IS NULL) "
				."GROUP BY pjc.category_id, pjc.name";

	$result = $xoopsDB->query($sql);
	$rows = $xoopsDB->getRowsNum($result);
	if (!$result || $rows < 1) {
		$return .= '<TR><TD>'._XF_PEO_NOCATEGORIESFOUND.'</TD></TR>';
	} else {
		$content .= $xoopsDB->error();
		for ($i=0; $i<$rows; $i++) {
			$content .= '<TR class="'.($i%2!=0?'bg2':'bg3').'"><TD><A HREF="'.XOOPS_URL.'/modules/xfjobs/?category_id='
			          .unofficial_getDBResult($result,$i,'category_id') .'">'
								.unofficial_getDBResult($result,$i,'name').'</A> ('.unofficial_getDBResult($result,$i,'total') .')</TD></TR>';
		}
	}
	$content .= '</TABLE>';
	return $content;
}

function people_show_project_jobs($group_id) {
  global $xoopsDB;

	//show open jobs for this project
	$sql = "SELECT pj.group_id,pj.job_id,g.group_name,g.unix_group_name,pj.title,pj.date,pjc.name AS category_name "
		    ."FROM ".$xoopsDB->prefix("xf_people_job")." pj,".$xoopsDB->prefix("xf_people_job_category")." pjc,".$xoopsDB->prefix("xf_groups")." g "
		    ."WHERE pj.group_id='$group_id' "
				."AND pj.group_id=g.group_id "
				."AND pj.category_id=pjc.category_id "
				."AND pj.status_id=1 "
				."AND g.is_public=1 "
				."AND g.status='A' "
				."ORDER BY date DESC";

	$result = $xoopsDB->query($sql);

	return people_show_job_list($result, true);
}

function people_show_category_jobs($category_id) {
  global $xoopsDB;

	//show open jobs for this category
	$sql = "SELECT pj.group_id,pj.job_id,g.group_name,g.unix_group_name,pj.title,pj.date,pjc.name AS category_name "
		    ."FROM ".$xoopsDB->prefix("xf_people_job")." pj,".$xoopsDB->prefix("xf_people_job_category")." pjc,".$xoopsDB->prefix("xf_groups")." g "
				."WHERE pj.category_id='$category_id' "
				."AND pj.group_id=g.group_id "
				."AND pj.category_id=pjc.category_id "
				."AND pj.status_id=1 "
				."AND g.is_public=1 "
				."AND g.status='A' "
				."ORDER BY date DESC";

	$result = $xoopsDB->query($sql);

	return people_show_job_list($result);
}

function people_show_job_list($result, $forProj=false)
{
	global $xoopsDB, $sys_datefmt;

	//takes a result set from a query and shows the jobs

	// query must contain 'group_id', 'job_id', 'title',
	// 'category_name' and 'status_name'
	$content = "
		<table cellspacing='1' cellpadding='5' width='100%' border='0'>
		<tr class='bg2'>
		<td align='center'><b>"._XF_PEO_TITLE."</b></td>
		<td align='center'><b>"._XF_PEO_CATEGORY."</b></td>
		<td align='center'><b>"._XF_PEO_DATEOPENED."</b></td>";

	if($forProj != true)
	{
		$content .= "<td align='center'><b>"._XF_G_PROJECT."</b></td>";
	}
	else
	{
		$content .= "<td align='center'></td>";
	}

	$content .= "</tr>";


	$rows = $xoopsDB->getRowsNum($result);
	if ($rows < 1)
	{
		$content .= '<tr><td colspan="4"><h4><br />'._XF_PEO_NONEFOUND.
			'</h4>'. $xoopsDB->error() .'</td></tr>';
	}
	else
	{
		for ($i=0; $i < $rows; $i++)
		{
      		$group_id = unofficial_getDBResult($result,$i,'group_id');
			$job_id = unofficial_getDBResult($result,$i,'job_id');

      		$content .= "<tr class='".($i%2!=0?'bg2':'bg3')."'>".
                "<td><a href='".XOOPS_URL.
				"/modules/xfjobs/viewjob.php?group_id=$group_id".
                "&job_id=$job_id'>".
                unofficial_getDBResult($result,$i,'title')."</a></td><td>".
                unofficial_getDBResult($result,$i,'category_name')."</td><td>".
                date($sys_datefmt, unofficial_getDBResult($result,$i,'date')).
                "</td><td align='center'>";

			if(!$forProj)
			{
				$content .= "<a href='".XOOPS_URL."/modules/xfmod/project/?".
					unofficial_getDBResult($result,$i,'unix_group_name')."'>".
					unofficial_getDBResult($result,$i,'group_name')."</a>";
			}
			else
			{
				$content .=
					"<a href='".XOOPS_URL."/modules/xfjobs/editjob.php?".
					"group_id=$group_id&job_id=$job_id'>edit</a>/".
					"<a href='".XOOPS_URL."/modules/xfjobs/viewjob.php?".
					"group_id=$group_id&job_id=$job_id&deljob=1'>delete</a>";
			}

			$content .= "</td></tr>";
		}
	}

	$content .= "</table>";

	return $content;
}

?>
