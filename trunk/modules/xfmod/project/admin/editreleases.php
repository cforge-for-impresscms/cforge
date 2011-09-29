<?php
/**
  *
  * Project Admin: Edit Releases of Packagesn
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: editreleases.php,v 1.16 2004/06/03 21:44:03 devsupaul Exp $
  *
  */

include_once ("../../../../mainfile.php");

$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/frs.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
$xoopsOption['template_main'] = 'project/admin/xfmod_editreleases.html';

$group_id = http_get('group_id');
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$project = &$group;
$perm  =& $group->getPermission( $xoopsUser );
if (!$perm->isReleaseTechnician()) {
        redirect_header(XOOPS_URL."/",4,_XF_G_PERMISSIONDENIED."<br />"._XF_PRJ_NOTRELEASETECHNIC);
        exit;
}
if ($group->isError()) {
	//wasn't found or some other problem
	return;
}
if (!$group->isProject()) {
	return;
}

include ("../../../../header.php");

$xoopsTpl->assign("project_title", project_title($group));
$xoopsTpl->assign("project_tabs", project_tabs('admin', $group_id));
$xoopsTpl->assign("admin_header", project_admin_header($group_id, $perm));
$xoopsTpl->assign("feedback", $feedback);

// Create a new FRS object
$frs = new FRS($group_id);

/*
 * Here's where we do the dirty work based on the step the user has chosen
 */
// Edit release info
if (isset($step1) && $step1) {
	$exec_changes = true;

	// If we haven't encountered any problems so far then save the changes
	if ($exec_changes == true) {
		if(!isset($status_id)) $status_id=1;
		if ($frs->frsChangeRelease($release_date, $release_name, $preformatted, $status_id, $release_notes, $release_changes, $new_package_id, $package_id, $release_id, $release_dependencies)) {
				$feedback .= " "._XF_PRJ_DATASAVED." ";
				$package_id=$new_package_id;
		} else {
				$feedback .= $frs->getErrorMessage();
		}
	}
}

// Add file(s) to the release
if (isset($step2) && $step2) {
		        $frs->frsAddFile(time(), $filename, $file_url, $file_size, time(), $release_id, $package_id);
                if( !$frs->isError() ) {
                        $feedback .= " "._XF_PRJ_FILESADDED." ";
                }else{
                        $feedback .= " "._XF_PRJ_FILEADDFAILED." ";
                }
}

// Edit/Delete files in a release
if (isset($step3) && $step3) {
        // If the user chose to delete the file and he's sure then delete the file
        if( $step3 == "Delete File" && $submit == "Delete File" && $im_sure) {
			// delete the file from the database and file system
			$frs->frsDeleteFile($file_id);
			if( !$frs->isError() ) {
					$feedback .= " "._XF_PRJ_FILEDELETED." ".$frs->getErrorMessage()." ";
			}
        // Otherwise update the file information
        } else if( $step3 == "Delete File" && $submit == "Update File") {
                $frs->frsChangeFile($file_id, $file_url, $filename, $file_size, $release_time, $release_id, $package_id);
                if( !$frs->isError() ) {
                    $feedback .= " "._XF_PRJ_FILEUPDATED." ";
                }else{
					$feedback .= " ".$frs->getErrorMessage();
				}
        }
}

// Send email notice
if (isset($step4) && $step4) {
                $frs->frsSendNotice($group_id, $release_id, $package_id);
                if( !$frs->isError() ) {
                                $feedback .= " "._XF_PRJ_EMAILNOTICESENT." ";
                }
                else {
                                $feedback .= " ".$frs->getErrorMessage();
                }
}

if ($package_id) {
        //narrow the list to just this package's releases
        $pkg_str = "AND p.package_id='$package_id'";
}

if( !$release_id ) {
        $res = $frs->frsGetReleaseList($pkg_str);
        $rows = $xoopsDB->getRowsNum($res);
        if (!$res || $rows < 1) {
          if ($package_id)
                  $content = "<B>"._XF_PRJ_NORELEASESTHISPACKAGEDEFINED."</B>";
                else
                  $content = "<B>"._XF_PRJ_NORELEASESDEFINED."</B>";
                  $content .= $xoopsDB->error();
        } else {
                /*
                        Show a list of releases
                        For this project or package
                */
$content .= "
    <table border='0' width='95%' cellpadding='0' cellspacing='0' align='center' valign='top'><tr><td class='bg2'>
    <table border='0' cellpadding='4' cellspacing='1' width='100%'>
    <tr class='bg3' align='left'>
            <td align='center'><span class='fg2'><b>"._XF_PRJ_RELEASENAME."</b></span></td>
            <td align='center'><span class='fg2'><b>"._XF_PRJ_PACKAGENAME."</b></span></td>
            <td align='center'><span class='fg2'><b>"._XF_PRJ_STATUS."</b></span></td>
    </tr>";



	for ($i=0; $i<$rows; $i++) {

	$content .= "<tr class='".($i%2>0?"bg1":"bg3")."'>"
                        ."<td>"
			."<font size='-1'>"
			.$ts->makeTboxData4Show(unofficial_getDBResult($res,$i,'release_name'))
			."[ <a href='editreleases.php?package_id=".$package_id."&release_id=".unofficial_getDBResult($res,$i,'release_id')."&group_id=".$group_id."'>"._XF_PRJ_EDITTHISRELEASE."</a> ]"
			."</font>"
			."</td>"
			."<td>"
			."<font size='-1'>"
			.$ts->makeTboxData4Show(unofficial_getDBResult($res,$i,'package_name'))
			."[ <a href='editpackages.php?group_id=".$group_id."'>"._XF_PRJ_EDITTHISPACKAGE."</a> ]"
			."</font>"
			."</td>"
			."<td>"
			."<font size='-1'>".unofficial_getDBResult($res,$i,'status_name')."</font>"
			."</td>"
			."</tr>"
			."</form>";
	}
	$content .= "</table></td></tr></table>\n";
  }
}

if($feedback) $content .= "<div class='errorMsg'>".$feedback."</div>";
/*
 * Show the forms for each step
 */
if( $release_id ) {

if (!isset($content))
	$content = '';
$content .= "

<B>"._XF_PRJ_STEP1."</B><p>

<form enctype='multipart/form-data' method='post' action='".$_SERVER['PHP_SELF']."'>
<input type='hidden' name='group_id' value='".$group_id."'>
<input type='hidden' name='package_id' value='".$package_id."'>
<input type='hidden' name='release_id' value='".$release_id."'>
<input type='hidden' name='step1' value='1'>
<table border='0' cellpadding='1' cellspacing='1'>";

        if(!($result = $frs->frsGetRelease($release_id))) {
                $feedback .= $frs->getErrorMessage();
        }

$content .= "

<tr>
        <td><b>"._XF_PRJ_RELEASENAME.":<b></td>
        <td><input type='text' name='release_name' value='".unofficial_getDBResult($result,0,'release_name')."' size='25'></td>
</tr>
<tr>
        <td width='10%'><b>"._XF_PRJ_RELEASEDATE.":<b></td>
        <td><input type='text' name='release_date' value='".date('Y-m-d',unofficial_getDBResult($result,0,'release_date'))."' size='12' maxlength='10'>  yyyy-mm-dd</td>
</tr>
<tr>
        <td><b>"._XF_PRJ_STATUS.":</b></td>
        <td><input type='checkbox' name='status_id' value='3' ".((unofficial_getDBResult($result,0,'status_id')==1)?'':'checked')."> Private
        </td>
</tr>
<tr>
        <td><b>"._XF_PRJ_OFPACKAGE.":</b></td>
        <td>".


		frs_show_package_popup ($group_id, 'new_package_id', unofficial_getDBResult($result,0,'package_id'))




		."</td>
</tr>
<tr>
        <td colspan='2'>
                <br>"._XF_PRJ_STEP1EDITRELEASENOTES."
                <br>
        </td>
</tr>
<tr>
        <td COLSPAN='2'>
                <b>"._XF_PRJ_PASTENOTES.":</b><br>
                <textarea name='release_notes' rows='10' cols='60' wrap='soft'>".$ts->makeTareaData4Edit(unofficial_getDBResult($result,0,'notes'))."</textarea>
        </td>
</TR>
<TR>
        <td COLSPAN=2>
                <b>"._XF_PRJ_PASTELOG.":</b><br>
                <textarea name='release_changes' rows='10' cols='60' wrap='soft'>".$ts->makeTareaData4Edit(unofficial_getDBResult($result,0,'changes'))."</textarea>
        </td>
</tr>
<TR>
        <td COLSPAN=2>
                <b>"._XF_PRJ_PASTEDEPENDENCIES.":</b><br>
                <textarea name='release_dependencies' rows='10' cols='60' wrap='soft'>".$ts->makeTareaData4Edit(unofficial_getDBResult($result,0,'dependencies'))."</textarea>
        </td>
</tr>
<TR>
        <TD COLSPAN=2>
                <br>
                <input type='checkbox' name='preformatted' value='1' ".((unofficial_getDBResult($result,0,'preformatted'))?'checked':'').">"._XF_PRJ_PRESERVEFORMAT."
                <p>
                <input type='submit' name='submit' value='"._XF_PRJ_SUBMITREFRESH."'>
        </td>
</tr>
</table>
</form>

<hr noshade>

<B>"._XF_PRJ_STEP2."</B><p>";

if(!ini_get('file_uploads')){
	$content .= "<p>"._XF_PRJ_UPLOAD_OFF."</p>";
}
else {
	$content .= "
	<form enctype='multipart/form-data' method='post' action='".$_SERVER['PHP_SELF']."'>
	<input type='hidden' name='group_id' value='".$group_id."'>
	<input type='hidden' name='package_id' value='".$package_id."'>
	<input type='hidden' name='release_id' value='".$release_id."'>
	<input type='hidden' name='step2' value='1'>

	<table border='0' cellpadding='3' cellspacing='3'>
	<tr>
		<td colspan='2'>"._XF_PRJ_DISCLAIMER." <a href='#' onclick=\"window.open('".XOOPS_URL."/modules/xfmod/include/contract.php','contract', 'width=750, height=350, resizable=yes, scrollbars=yess, toolbar=no, directories=no, location=no, status=no');\">"._XF_PRJ_DISCLAIMERLINK."</a></td>
	</tr>
	<tr>
			<td colspan=2>".sprintf(_XF_PRJ_MAX_UPLOAD, ini_get('upload_max_filesize'))."</td>
	</tr>
	<tr>
		<td><b>"._XF_PRJ_FILENAME.":<b></td>
		<td><input type='text' name='filename' maxlength=255 size='40'></td>
	</tr>
	<tr>
		<td><b>"._XF_PRJ_FILESIZE.":<b></td>
		<td><input type='text' name='file_size' maxlength='12' size='40'></td>
	</tr>
	<tr>
		<td><b>"._XF_PRJ_LINK1.":<b></td>
		<td><input type='text' name='file_url' maxlength='255' size='40'></td>
	</tr>
	<tr>
		<td></td><td><b>- "._XF_PRJ_OR." -</b></td>
	</tr>
	<tr>
		<td><b>"._XF_PRJ_FILE1.":<b></td>
		<td><input type='file' name='file1' size='40'><BR><BR></td>
	</tr>
	</table>
	<input type='submit' name='submit' value='". _XF_PRJ_ADDFILE."'>
	</form>";
}

$content .= "<hr noshade>

<B>"._XF_PRJ_STEP3."</B><p>";

        // Get a list of files associated with this release
        $res = $frs->frsGetReleaseFiles($release_id);
        if( !$frs->isError() ) {
                $rows = $xoopsDB->getRowsNum($res);
                if($rows < 1) {
                        $content .= "<B>"._XF_PRJ_NOFILESINRELEASE."</B>";
                } else {

$content .= "
    <table border='0' width='95%' cellpadding='0' cellspacing='0' align='center' valign='top'><tr><td class='bg2'>
    <table border='0' cellpadding='4' cellspacing='1' width='100%'>
    <tr class='bg1' align='left'>
            <th align='left'><span class='fg2'>"._XF_PRJ_FILENAME."</span></td>
            <th align='left'><span class='fg2'>"._XF_PRJ_FILESIZE."</span></td>
            <th align='left'><span class='fg2'>"._XF_PRJ_FILEDATE." "._XF_PRJ_FILEDATEFORMAT."</span></td>
    </tr>";

                        for($x=0; $x<$rows; $x++) {
$content .= "
                        <form action='".$_SERVER['PHP_SELF']."' method='post'>
                                <input type='hidden' name='group_id' value='".$group_id."'>
                                <input type='hidden' name='release_id' value='".$release_id."'>
                                <input type='hidden' name='package_id' value='".$package_id."'>
                                <input type='hidden' name='file_id' value='".unofficial_getDBResult($res,$x,'file_id')."'>
                                <input type='hidden' name='step3' value='Delete File'>
                                <tr class='".($x%2>0?"bg1":"bg3")."'>";
					if(strstr(unofficial_getDBResult($res,$x,'file_url'),"://")) {
						$content .= "
						<td><input type='text' name='filename' maxlength='255' value='".unofficial_getDBResult($res,$x,'filename')."'></td>
						<td><input type='text' name='file_size' maxlength='12' value='".unofficial_getDBResult($res,$x,'file_size')."'> bytes</td>
						<td><input type='text' name='release_time' value='".date('Y-m-d',unofficial_getDBResult($res,$x,'release_time'))."'> </td>";
					}else {
					$content .= "
	                                        <td nowrap><font size='-1'>".unofficial_getDBResult($res,$x,'filename')."</td>
						<td><font size='-1'>".unofficial_getDBResult($res,$x,'file_size')." bytes"."</td>
						<td><input type='hidden' name='filename' value='".unofficial_getDBResult($res,$x,'filename')."'>
						    <input type='hidden' name='file_url' value='".unofficial_getDBResult($res,$x,'file_url')."'>
						    <input type='hidden' name='file_size' value='".unofficial_getDBResult($res,$x,'file_size')."'>
						    <input type='text' name='release_time' value='".date('Y-m-d',unofficial_getDBResult($res,$x,'release_time'))."'> </td>";
					 }
				$content .= "
                                </tr>
                                <tr class='".($x%2>0?"bg1":"bg3")."'>
                                        <td colspan='2'>";
					if(strstr(unofficial_getDBResult($res,$x,'file_url'),"://")) {
						$content .= "
						<input type='text' name='file_url' maxlength='255' size='40' value='".unofficial_getDBResult($res,$x,'file_url')."'>";
					}
					$content .= "
								</td>
                     			<td><input type='submit' name='submit' value='"._XF_PRJ_UPDATEFILE."'>";
					if(preg_match("/.*\.rpm$/",unofficial_getDBResult($res,$x,'filename')) && !preg_match("/.*\.src\.rpm$/",unofficial_getDBResult($res,$x,'filename'))){
						$sql = "SELECT count(file_id) FROM ".$xoopsDB->prefix("xf_webservice_publish")." WHERE file_id=".unofficial_getDBResult($res,$x,'file_id')." AND status='succeeded'";
						$myresult = $xoopsDB->query($sql);
						list($mycount) = $xoopsDB->fetchRow($myresult);
						if($mycount == 0)
							$content .= "<input type='button' name='publish' onClick=\"javascript:return openWithSelfMain('publish.php?file_id=".unofficial_getDBResult($res,$x,'file_id')."','publish',300,350);\" value='"._XF_PRJ_PUBLISHFILE."'>";
					}
					$content .= "<input type='submit' name='submit' value='"._XF_PRJ_DELETEFILE."'> <input type='checkbox' name='im_sure' value='1'> "._XF_PRJ_IMSURE."</td>
                                </tr>
                                </form>";
                        }
                        $content .= "</table></td></tr></table>";
                }
        } else {
                $feedback .= $frs->getErrorMessage();
        }

$content .= "
<hr noshade>

<B>"._XF_PRJ_STEP4."</B><p>";


        $mons = $frs->frsGetReleaseMonitors($package_id);
        if( $mons > 0 ) {
		$content .= "
		<form action='".$_SERVER['PHP_SELF']."' method='post'>
			<input type='hidden' name='group_id' value='".$group_id."'>
			<input type='hidden' name='release_id' value='".$release_id."'>
			<input type='hidden' name='package_id' value='".$package_id."'>
			<input type='hidden' name='step4' value='Email Release'>
			".sprintf(_XF_PRJ_USERSAREMONITORING, $mons)."<br>
			<input type='submit' value='"._XF_PRJ_SENDNOTICE."'><input type='checkbox' value='sure'> "._XF_PRJ_IMSURE."
		</form>";
        } else {

          $content .= _XF_PRJ_NOBODYMONITORING;

        }
}
$xoopsTpl->assign("content", $content);
include ("../../../../footer.php");
?>