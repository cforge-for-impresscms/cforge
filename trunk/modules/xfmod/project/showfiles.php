<?php
/**
 * Project File Information/Download Page
 *
 * Copyright 1999-2001 (c) VA Linux Systems
 * Copyright 2003-2004 (c) Novell, Inc.
 *
 * @version $Id: showfiles.php,v 1.67 2004/07/19 16:19:28 danreese Exp $
 */

include_once("../../../mainfile.php");

$langfile = "project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
$xoopsOption['template_main'] = 'project/xfmod_showfiles.html';

function readableSize($bytes)
{
	$decimals = $bytes < 1024 ? 0 : 1;
	$sizes = array('&nbsp;B', '&nbsp;K', '&nbsp;M', '&nbsp;G', '&nbsp;T');
	$count = count($sizes);
	$i = 0;
	while ($i < $count && $bytes >= 1024)
	{
		$bytes /= 1024;
		$i++;
	}
	$locale = localeconv();
	return number_format($bytes, $decimals, $locale['decimal_point'], $locale['thousands_sep']).$sizes[$i];
}

$group_id = http_get('group_id');
$project =& group_get_object($group_id);
$perm =& $project->getPermission($xoopsUser);

// Check if group is private.
if (!$project->isPublic())
{
	// If private, user must be a member of that group.
	if (!$project->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
	{
		redirect_header(XOOPS_URL."/", 4, _XF_PRJ_PROJECTMARKEDASPRIVATE);
		exit;
	}
	$private_group=true;
}
else
{
	$private_group=false;
}

// For dead projects, user must be a member of xoopsforge project.
if (!$project->isActive() && !$perm->isSuperUser())
{
	redirect_header(XOOPS_URL, 4, _XF_PRJ_NOTAUTHORIZEDTOENTER);
	exit;
}

$dl = util_http_track_vars('dl');

if($dl)
{
	include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/download.php");
}

// Include header.
include ("../../../header.php");

// Meta tag information.
$metaTitle = " "._XF_PRJ_PROJECTFILELIST." - ".$project->getPublicName();
$metaKeywords = project_getmetakeywords($group_id);
$metaDescription = str_replace('"', "&quot;", strip_tags($project->getDescription()));

$xoopsTpl->assign("xoops_pagetitle", $metaTitle);
$xoopsTpl->assign("xoops_meta_keywords", $metaKeywords);
$xoopsTpl->assign("xoops_meta_description", $metaDescription);

// Project nav information.
$xoopsTpl->assign("project_title", project_title($project));
$xoopsTpl->assign("project_tabs", project_tabs('downloads', $group_id));
$xoopsTpl->assign("admin_header", project_admin_header($group_id, $perm));

// Get rights of the user to view packages and releases.
$rights = ($perm->isMember() ? 3 : 2);

// Get packages and releases.
$sql = "SELECT p.package_id, p.group_id, p.name, p.status_id, s.name as status"
	." FROM ".$xoopsDB->prefix("xf_frs_package")." AS p"
	.",".$xoopsDB->prefix("xf_frs_status")." AS s"
	." WHERE p.group_id=$group_id"
	." AND p.status_id=s.status_id"
	." AND p.status_id<=$rights"
	." ORDER BY p.name";
$rs_packages = $xoopsDB->query($sql);
$num_packages = $xoopsDB->getRowsNum($rs_packages);
$xoopsTpl->assign("num_packages", $num_packages);

// Return if no packages.
if ($num_packages < 1)
{
	$content = _XF_PRJ_NOFILEPACKAGESDEFINED;
	$xoopsTpl->assign("content", $content);
	include ("../../../footer.php");
	return;
}
$proj_stats['packages'] = $num_packages;

// Display description paragraph.
$content = "<p>"._XF_PRJ_BELOWLISTOFALLFILES." ";
if (isset($_POST['release_id']))
    $highlight_release_id = $_POST['release_id'];
elseif (isset($_GET['release_id']))
	$highlight_release_id = $_GET['release_id'];
else
	$highlight_release_id = null;
//$highlight_release_id = $_REQUEST['release_id'];
if ($highlight_release_id)
{
	$content .= _XF_PRJ_RELEASECHOSENISHIGH." ";
}
$content .= _XF_PRJ_READNOTESBEFOREDOWNLOAD."</p>\n"
	."<table class='bg1' border='0' cellpadding='5' cellspacing='0'>\n";

// Iterate and show the packages.
for ($p = 0; $p < $num_packages; $p++)
{
	$package = $xoopsDB->fetchArray($rs_packages);
	$private_package = ($package['status_id'] == 3);

	$content .= "\n<tr><td colspan='4'><h3>"
		.$ts->makeTboxData4Show($package['name'])
		." - ("
		.$ts->makeTboxData4Show($package['status'])
		.")</h3></td></tr>\n";

	// Get the releases of the package.
	$sql = "SELECT *"
		." FROM ".$xoopsDB->prefix("xf_frs_release")
		." WHERE package_id=".$package['package_id']
		." AND status_id<=$rights"
		." ORDER BY release_date DESC, name ASC";
	$rs_releases = $xoopsDB->query($sql);
	$num_releases = $xoopsDB->getRowsNum($rs_releases);
	if (!isset($proj_stats['releases']))
		$proj_stats['releases'] = $num_releases;
	else
		$proj_stats['releases'] += $num_releases;

	// Check if package has no releases.
	if (!$rs_releases || $num_releases < 1)
	{
		$content .= "<tr><td colspan='4'><i>"._XF_PRJ_NORELEASES."</i></td></tr>\n";
	}
	else
	{
		// Iterate and show the releases of the package.
		for ($r = 0; $r < $num_releases; $r++)
		{
			$release = $xoopsDB->fetchArray($rs_releases);
			$private_release = ($release['status_id'] == 3);

			// Highlight the release, if one was chosen.
			if ($highlight_release_id && $highlight_release_id == $release['release_id'])
			{
				$highlight_name = " name='selected'";
			}
			else
			{
				//unset($highlight_name);
				$highlight_name = null; 
			}
			$content .= "<tr class='bg3'>\n"
				."\t<td colspan='3'".((isset($highlight_name) && $highlight_name) ? " style='border-top:2px solid red;border-left:2px solid red'" : "").">"
				//."&nbsp;<b><a $highlight_name href='shownotes.php?group_id=$group_id&release_id=".$release['release_id']."'>"
				."&nbsp;<b><a href='shownotes.php?group_id=$group_id&release_id=".$release['release_id']."'>"
				.(strlen($release['name']) ? $ts->makeTboxData4Show($release['name']) : ("<i>("._XF_PRJ_VIEWNOTES.")</i>"))
				."</a></b></td>\n"
				."\t<td align='right'".((isset($highlight_name) && $highlight_name) ? " style='border-top:2px solid red;border-right:2px solid red'" : "")."><b>"
				.date('Y-m-d', $release['release_date'])."</b>&nbsp;</td>\n</tr>\n";

			// Get the files in this release.
			$sql = "SELECT filename, file_size, file_url, release_id, ".$xoopsDB->prefix("xf_frs_file").".file_id, release_time, downloads, status"
				." FROM ".$xoopsDB->prefix("xf_frs_file")
				." LEFT JOIN ".$xoopsDB->prefix("xf_webservice_publish")." USING (file_id) "
				." LEFT JOIN ".$xoopsDB->prefix("xf_frs_dlstats_file_agg")
				." ON ".$xoopsDB->prefix("xf_frs_file").".file_id=".$xoopsDB->prefix("xf_frs_dlstats_file_agg").".file_id"
				." WHERE release_id=".$release['release_id']
				." ORDER BY filename";

			$rs_files = $xoopsDB->query($sql);
			$num_files = $xoopsDB->getRowsNum($rs_files);
			if (!isset($proj_stats['files']))
				$proj_stats['files'] = $num_files;
			else
				$proj_stats['files'] += $num_files;
			

			// Check if release has no files.
			if (!$rs_files || $num_files < 1 )
			{
				$content .= "\t<tr><td colspan='4'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>"._XF_PRJ_NOFILES."</i></td></tr>\n";
			}
			else
			{
				// Iterate and show the files in this release.
				for ($f = 0; $f < $num_files; $f++)
				{
					$file = $xoopsDB->fetchArray($rs_files);
					if (!isset($proj_stats['size'])) 
						$proj_stats['size'] = $file['file_size'];
					else
						$proj_stats['size'] += $file['file_size'];
					if (!isset($proj_stats['downloads'])) 
						$proj_stats['downloads'] = $file['downloads']; 
					else
						$proj_stats['downloads'] += $file['downloads'];

					// Construct file download URL.
					$url = XOOPS_URL."/modules/xfmod/project/showfiles.php?group_id=".$group_id."&release_id=".$release['release_id']."&dl=".$file['file_id'];
					if ($private_group || $private_package || $private_release)
					{
						$url.="&private=1";
					}
					$content .= "<tr>\n\t<td".((isset($highlight_name) && $highlight_name) ? " style='border-left:2px solid red".($f+1 == $num_files ? ";border-bottom:2px solid red'" : "'") : "").">"
						."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='$url'>".$file['filename']."</a>";

					// Add Red Carpet image link, if needed.
					if ($file['status'] == 'succeeded')
					{
						$content .= "&nbsp;<a href='#redcarpet'><img src='../images/red-carpet.png' width='16' height='13' border='0' alt='Available in Red-Carpet' align='middle'></a>";
						$usesRedCarpet = true;
					}
					else
					{
						$usesRedCarpet = false;
					}
						
					$content .= "&nbsp;&nbsp;&nbsp;&nbsp;</td>\n"
						."\t<td align='right'".(($highlight_name && $f+1 == $num_files) ? " style='border-bottom:2px solid red'" : "").">"
						.($file['file_size'] ? readableSize($file['file_size']) : '-')."&nbsp;&nbsp;&nbsp;&nbsp;</td>\n"
						."\t<td align='center'".(($highlight_name && $f+1 == $num_files) ? " style='border-bottom:2px solid red'" : "").">"
						.date('Y-m-d', $file['release_time'])."&nbsp;&nbsp;&nbsp;&nbsp;</td>\n"
						."\t<td align='right'".($highlight_name ? " style='border-right:2px solid red".($f+1 == $num_files ? ";border-bottom:2px solid red'" : "'") : "").">"
						.($file['downloads'] ? number_format($file['downloads'], 0) : '0')."&nbsp;"._XF_PRJ_DL."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n"
						."</tr>\n";
				}
			}
		}
	}

	// Add some space before next package.
	$content .= "<tr><td colspan='4'>&nbsp;</td></tr>\n";
}

if ($proj_stats['size'])
{
	$content .= "\n<tr><td colspan='4'><h3>"._XF_PRJ_PROJECTTOTALS."</h3></td></tr>\n<tr>\n\t<td colspan='4'>"
		."<b>Releases:&nbsp;<i>".$proj_stats['releases']."</i></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
		."<b>Files:&nbsp;<i>".$proj_stats['files']."</i></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
		."<b>Downloads:&nbsp;<i>".$proj_stats['downloads']."</i></b>"
		."</td>\n</tr>\n";
}

// Show Red Carpet note.
$url2 = XOOPS_URL."/modules/xfmod/help/projects.php#red_carpet";
$message = ($usesRedCarpet
	? "Indicates that the file is available from <a style='text-decoration:underline' href='/modules/xfmod/help/projects.php#red_carpet'>Forge's Red Carpet server</a>."
	: "No files are available from <a style='text-decoration:underline' href='$url2'>Forge's Red Carpet server</a> for this project.");
$content .= "<tr><td colspan='4'>\n"
	."\t<p><a name='redcarpet'></a><img src='../images/red-carpet.png' width='16' height='13' border='0' alt='Available in Red-Carpet' align='middle'>\n"
	."\t$message\n";
if ($usesRedCarpet)
{
	$content .= "\t</p>\n\tRed Carpet information for this project:\n\t<ul>"
		."\t<li>Service: https://forgerce.novell.com/data</li>"
		."\t<li>Activation Key: ".$project->getUnixName()."-key</li>"
		."\t<li>Channel: ".$project->getUnixName()."</li>"
		."</ul>\n";
}
$content .= "</td></tr></table>";

// Assign content to template.
$xoopsTpl->assign("content", $content);

// Include footer.
include ("../../../footer.php");
?>