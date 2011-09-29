<?php
/**
  *
  * Project Admin: Edit Packages
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: editpackages.php,v 1.10 2004/06/10 17:54:26 devsupaul Exp $
  *
  */

include_once ("../../../../mainfile.php");

$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/frs.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$xoopsOption['template_main'] = 'project/admin/xfmod_editpackages.html';

$group_id = http_get('group_id');
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isReleaseTechnician()) {
	redirect_header(XOOPS_URL."/",4,_XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTRELEASETECHNIC);
	exit;
}


// only admin can modify packages (vs modifying releases of packages)
if (isset($submit) && $submit) {

	$frs = new FRS($group_id);

	if ($func=='add_package' && $package_name && $perm->isAdmin()){
		$frs->frsAddPackage($package_name, $group->getUnixName());
		$feedback .= ' '.$frs->getErrorMessage().' ';
	}else if ($func=='update_package' && $package_id && $package_name && $status_id && $perm->isAdmin()){
		$frs->frsChangePackage($group, $package_id, $package_name, $status_id);
		$feedback .= ' '.$frs->getErrorMessage().' ';
	}else if($func=='delete_package' && $package_id && $perm->isAdmin()){
		$frs->frsDeletePackage($package_id, $group->getUnixName());
		if($frs->isError()) $feedback .= ' '.$frs->getErrorMessage().' ';
	}else if($func=='delete_release' && $release_id && $perm->isReleaseTechnician()){
		$frs->frsDeleteRelease($release_id, $package_id, $perm);
		if($frs->isError()) $feedback .= ' '.$frs->getErrorMessage().' ';
	}
}

include ("../../../../header.php");
$xoopsTpl->assign("project_title", project_title($group));
$xoopsTpl->assign("project_tabs", project_tabs('admin', $group_id));
$xoopsTpl->assign("admin_header", get_project_admin_header($group_id, $perm));
$xoopsTpl->assign("feedback", $feedback);

/*
	Show a list of existing packages for this project so they can be edited
*/
$res = $xoopsDB->query("SELECT status_id,package_id,name AS package_name "
                      ."FROM ".$xoopsDB->prefix("xf_frs_package")." "
		                  ."WHERE group_id='$group_id'");
$rows = $xoopsDB->getRowsNum($res);

$content = "
<table border='0' width='95%' cellpadding='0' cellspacing='0' align='center' valign='top'><tr><td class='bg2'>
    <table border='0' cellpadding='4' cellspacing='1' width='100%'>
    <tr class='bg3' align='left'>
	    <td align='center'><span class='fg2'><b>"._XF_PRJ_PACKAGENAME."</b></span></td>
	    <td align='center'><span class='fg2'><b>"._XF_PRJ_STATUS."</b></span></td>
		<td align='center'><span class='fg2'><b>"._XF_G_UPDATE."</b></span></td>
		<td align='center'><span class='fg2'><b>"._XF_G_DELETE."</b></span></td>
		</tr>";

	for ($i=0; $i<$rows; $i++)
	{
		//Only admins can update a package, not release tech
		$status_id = unofficial_getDBResult($res,$i,'status_id');
		if($status_id!=2 || $perm->isReleaseAdmin()){
			$package_name = unofficial_getDBResult($res,$i,'package_name');
			$package_id = unofficial_getDBResult($res,$i,'package_id');

			$content .= "<TR class='".($i % 2 > 0 ? "bg1" : "bg3" )."'>";
			if ($perm->isAdmin()){
				$content .= "<FORM ACTION='".$_SERVER['PHP_SELF']."' METHOD='POST'>"
					."<td><INPUT type=text name='package_name' value='$package_name'></td>"
					."<TD>".frs_show_status_popup ('status_id', $status_id)."</TD>";
				$content .= "<TD>"
					."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>"
					."<INPUT TYPE='HIDDEN' NAME='func' VALUE='update_package'>"
					."<INPUT TYPE='HIDDEN' NAME='package_id' VALUE='".$package_id."'>"
					."<INPUT TYPE='SUBMIT' NAME='submit' VALUE='"._XF_G_UPDATE."'>"
					."</TD></FORM><TD>"
					."<FORM ACTION='".$_SERVER['PHP_SELF']."' METHOD='POST'>"
					."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>"
					."<INPUT TYPE='HIDDEN' NAME='func' VALUE='delete_package'>"
					."<INPUT TYPE='HIDDEN' NAME='package_id' VALUE='".$package_id."'>"
					."<INPUT TYPE='SUBMIT' NAME='submit' VALUE='"._XF_G_DELETE."'>"
					."</FORM>"
					."</TD>";
			}else{
				$content .= "<TD><b>".$package_name."</b></TD>"
							."<TD><b>".frs_get_status_name($status_id)."</b></TD>"
							."<TD>&nbsp;</TD>"
							."<TD>&nbsp;</TD>";
			}
			$content .= "</TR>";

			//Now list all of the releases in this package
			// Create a new FRS object
			$frs = new FRS($group_id);
			$relres = $frs->frsGetReleaseList("AND p.package_id='".$package_id."'");
			$relrows = $xoopsDB->getRowsNum($relres);
			if (!$relres || $relrows < 1) {
				$content .= "<TR class='".($i%2>0?"bg1":"bg3")."'><TD colspan=4>&nbsp;&nbsp;&nbsp;"._XF_PRJ_NORELEASESTHISPACKAGEDEFINED."</TD></TR>";
				$content .= $xoopsDB->error();
			} else {
				for($j=0; $j<$relrows; $j++){
					$release_name = unofficial_getDBResult($relres,$j,'release_name');
					$release_id = unofficial_getDBResult($relres,$j,'release_id');
					$content .= "<TR class='".($i%2>0?"bg1":"bg3")."'>"
						."<TD NOWRAP>&nbsp;&nbsp;&nbsp;".$release_name."</TD>"
						."<TD>&nbsp;</TD>"
						."<TD><FORM ACTION='editreleases.php' METHOD='POST'>"
						."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>"
						."<INPUT TYPE='HIDDEN' NAME='release_id' VALUE='".$release_id."'>"
						."<INPUT TYPE='HIDDEN' NAME='package_id' VALUE='".$package_id."'>"
						."<INPUT type='submit' value='Edit Release'></FORM></TD>"
						."<TD><FORM ACTION='".$_SERVER['PHP_SELF']."' METHOD='POST'>"
						."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>"
						."<INPUT TYPE='HIDDEN' NAME='func' VALUE='delete_release'>"
						."<INPUT TYPE='HIDDEN' NAME='release_id' VALUE='".$release_id."'>"
						."<INPUT TYPE='HIDDEN' NAME='package_id' VALUE='".$package_id."'>"
						."<INPUT type='SUBMIT' name='submit' value='"._XF_G_DELETE."'></FORM></TD>"
						."</TR>";
				}
			}
			//Add a button to create a new release
			$content .= "<FORM ACTION='newrelease.php' METHOD='POST'>"
				."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>"
				."<INPUT TYPE='HIDDEN' NAME='package_id' VALUE='".$package_id."'>"
				."<TR class='".($i%2>0?"bg1":"bg3")."'>"
				."<TD>&nbsp;&nbsp;&nbsp;<INPUT type='textbox' name='release_name' value='' size='20' maxlength='25'></TD>"
				."<TD>&nbsp;</TD>"
				."<TD><INPUT TYPE='SUBMIT' NAME='submit' VALUE='"._XF_PRJ_CREATETHISRELEASE."'></TD>"
				."<TD>&nbsp;</TD>"
				."</TR></FORM>";
		}
	}
	//Add a button to create a new package
	if ($perm->isAdmin()){
		$content .= "<FORM ACTION='".$_SERVER['PHP_SELF']."' METHOD='POST'>"
					."<INPUT TYPE='HIDDEN' NAME='group_id' VALUE='".$group_id."'>"
					."<INPUT TYPE='HIDDEN' NAME='func' VALUE='add_package'>"
					."<TR class='".($i%2>0?"bg1":"bg3")."'>"
					."<TD><B><INPUT type='textbox' name='package_name' value='' size=20 maxlength=30></B></TD>"
					."<TD>&nbsp;</TD>"
					."<TD><INPUT TYPE='SUBMIT' NAME='submit' VALUE='"._XF_PRJ_CREATETHISPACKAGE."'></TD>"
					."<TD>&nbsp;</TD>"
					."</TR></FORM>";
	}

	$content .= "</TABLE></TD></TR></TABLE>";

$xoopsTpl->assign("content", $content);
include ("../../../../footer.php");

?>