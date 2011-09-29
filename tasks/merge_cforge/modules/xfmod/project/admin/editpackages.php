<?php
	/**
	*
	* Project Admin: Edit Packages
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: editpackages.php,v 1.10 2004/06/10 17:54:26 devsupaul Exp $
	*
	*/
	 
	include_once("../../../../mainfile.php");
	 
	$langfile = "project.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/frs.class.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	$icmsOption['template_main'] = 'project/admin/xfmod_editpackages.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isReleaseTechnician())
	{
		redirect_header(ICMS_URL."/", 4, _XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTRELEASETECHNIC);
		exit;
	}
	 
	 
	// only admin can modify packages(vs modifying releases of packages)
	if (isset($submit) && $submit)
	{
		 
		$frs = new FRS($group_id);
		 
		if ($func == 'add_package' && $package_name && $perm->isAdmin())
		{
			$frs->frsAddPackage($package_name, $group->getUnixName());
			$feedback .= ' '.$frs->getErrorMessage().' ';
		}
		else if($func == 'update_package' && $package_id && $package_name && $status_id && $perm->isAdmin())
		{
			$frs->frsChangePackage($group, $package_id, $package_name, $status_id);
			$feedback .= ' '.$frs->getErrorMessage().' ';
		}
		else if($func == 'delete_package' && $package_id && $perm->isAdmin())
		{
			$frs->frsDeletePackage($package_id, $group->getUnixName());
			if ($frs->isError()) $feedback .= ' '.$frs->getErrorMessage().' ';
		}
		else if($func == 'delete_release' && $release_id && $perm->isReleaseTechnician())
		{
			$frs->frsDeleteRelease($release_id, $package_id, $perm);
			if ($frs->isError()) $feedback .= ' '.$frs->getErrorMessage().' ';
		}
	}
	 
	include("../../../../header.php");
	$icmsTpl->assign("project_title", project_title($group));
	$icmsTpl->assign("project_tabs", project_tabs('admin', $group_id));
	$icmsTpl->assign("admin_header", get_project_admin_header($group_id, $perm));
	$icmsTpl->assign("feedback", $feedback);
	 
	/*
	Show a list of existing packages for this project so they can be edited
	*/
	$res = $icmsDB->query("SELECT status_id,package_id,name AS package_name " ."FROM ".$icmsDB->prefix("xf_frs_package")." " ."WHERE group_id='$group_id'");
	$rows = $icmsDB->getRowsNum($res);
	 
	$content = "
		<table border='0' width='95%' cellpadding='0' cellspacing='0' align='center' valign='top'><tr><td class='bg2'>
		<table border='0' cellpadding='4' cellspacing='1' width='100%'>
		<tr class='bg3' align='left'>
		<td align='center'><span class='fg2'><strong>"._XF_PRJ_PACKAGENAME."</strong></span></td>
		<td align='center'><span class='fg2'><strong>"._XF_PRJ_STATUS."</strong></span></td>
		<td align='center'><span class='fg2'><strong>"._XF_G_UPDATE."</strong></span></td>
		<td align='center'><span class='fg2'><strong>"._XF_G_DELETE."</strong></span></td>
		</tr>";
	 
	for($i = 0; $i < $rows; $i++)
	{
		//Only admins can update a package, not release tech
		$status_id = unofficial_getDBResult($res, $i, 'status_id');
		if ($status_id != 2 || $perm->isReleaseAdmin())
		{
			$package_name = unofficial_getDBResult($res, $i, 'package_name');
			$package_id = unofficial_getDBResult($res, $i, 'package_id');
			 
			$content .= "<th class='".($i % 2 > 0 ? "bg1" : "bg3")."'>";
			if ($perm->isAdmin())
			{
				$content .= "<form action='".$_SERVER['PHP_SELF']."' METHOD='POST'>" ."<td><input type=text name='package_name' value='$package_name'></td>" ."<td>".frs_show_status_popup('status_id', $status_id)."</td>";
				$content .= "<td>" ."<input type='hidden' name='group_id' value='".$group_id."'>" ."<input type='hidden' name='func' value='update_package'>" ."<input type='hidden' name='package_id' value='".$package_id."'>" ."<input type='submit' name='submit' value='"._XF_G_UPDATE."'>" ."</td></form><td>" ."<form action='".$_SERVER['PHP_SELF']."' METHOD='POST'>" ."<input type='hidden' name='group_id' value='".$group_id."'>" ."<input type='hidden' name='func' value='delete_package'>" ."<input type='hidden' name='package_id' value='".$package_id."'>" ."<input type='submit' name='submit' value='"._XF_G_DELETE."'>" ."</form>" ."</td>";
			}
			else
				{
				$content .= "<td><strong>".$package_name."</strong></td>" ."<td><strong>".frs_get_status_name($status_id)."</strong></td>" ."<td>&nbsp;</td>" ."<td>&nbsp;</td>";
			}
			$content .= "</th>";
			 
			//Now list all of the releases in this package
			// Create a new FRS object
			$frs = new FRS($group_id);
			$relres = $frs->frsGetReleaseList("AND p.package_id='".$package_id."'");
			$relrows = $icmsDB->getRowsNum($relres);
			if (!$relres || $relrows < 1)
			{
				$content .= "<th class='".($i%2 > 0?"bg1":"bg3")."'><td colspan=4>&nbsp;&nbsp;&nbsp;"._XF_PRJ_NORELEASESTHISPACKAGEDEFINED."</td></th>";
				$content .= $icmsDB->error();
			}
			else
			{
				for($j = 0; $j < $relrows; $j++)
				{
					$release_name = unofficial_getDBResult($relres, $j, 'release_name');
					$release_id = unofficial_getDBResult($relres, $j, 'release_id');
					$content .= "<th class='".($i%2 > 0?"bg1":"bg3")."'>" ."<td NOWRAP>&nbsp;&nbsp;&nbsp;".$release_name."</td>" ."<td>&nbsp;</td>" ."<td><form action='editreleases.php' METHOD='POST'>" ."<input type='hidden' name='group_id' value='".$group_id."'>" ."<input type='hidden' name='release_id' value='".$release_id."'>" ."<input type='hidden' name='package_id' value='".$package_id."'>" ."<input type='submit' value='Edit Release'></form></td>" ."<td><form action='".$_SERVER['PHP_SELF']."' METHOD='POST'>" ."<input type='hidden' name='group_id' value='".$group_id."'>" ."<input type='hidden' name='func' value='delete_release'>" ."<input type='hidden' name='release_id' value='".$release_id."'>" ."<input type='hidden' name='package_id' value='".$package_id."'>" ."<input type='submit' name='submit' value='"._XF_G_DELETE."'></form></td>" ."</th>";
				}
			}
			//Add a button to create a new release
			$content .= "<form action='newrelease.php' METHOD='POST'>" ."<input type='hidden' name='group_id' value='".$group_id."'>" ."<input type='hidden' name='package_id' value='".$package_id."'>" ."<th class='".($i%2 > 0?"bg1":"bg3")."'>" ."<td>&nbsp;&nbsp;&nbsp;<input type='textbox' name='release_name' value='' size='20' maxlength='25'></td>" ."<td>&nbsp;</td>" ."<td><input type='submit' name='submit' value='"._XF_PRJ_CREATETHISRELEASE."'></td>" ."<td>&nbsp;</td>" ."</th></form>";
		}
	}
	//Add a button to create a new package
	if ($perm->isAdmin())
	{
		$content .= "<form action='".$_SERVER['PHP_SELF']."' METHOD='POST'>" ."<input type='hidden' name='group_id' value='".$group_id."'>" ."<input type='hidden' name='func' value='add_package'>" ."<th class='".($i%2 > 0?"bg1":"bg3")."'>" ."<td><strong><input type='textbox' name='package_name' value='' size=20 maxlength=30></strong></td>" ."<td>&nbsp;</td>" ."<td><input type='submit' name='submit' value='"._XF_PRJ_CREATETHISPACKAGE."'></td>" ."<td>&nbsp;</td>" ."</th></form>";
	}
	 
	$content .= "</table></td></th></table>";
	 
	$icmsTpl->assign("content", $content);
	include("../../../../footer.php");
	 
?>