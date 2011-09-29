<?php
/**
  *
  * SourceForge New Releases Page
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.4 2004/03/22 19:20:26 devsupaul Exp $
  *
  */
include_once ("../../mainfile.php");

$langfile="new.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");

$metaTitle=": "._XF_NEW_NEWFILERELEASES."";
include("../../header.php");

echo "<H4 style='text-align:center;'>"._XF_NEW_NEWFILERELEASES."</H4>";

if ( !$offset || $offset < 0 ) {
	$offset = 0;
}

// For expediancy, list only the filereleases in the past three days.
$start_time = time() - (7 * 86400);

$query = "SELECT g.group_name,"
        ."g.group_id,"
				."g.unix_group_name,"
				."g.short_description,"
				."u.uname,"
				."u.uid,"
				."fr.release_id,"
				."fr.name AS release_version,"
				."fr.release_date,"
				."fr.released_by,"
				."fp.name AS module_name "
//				."frs_dlstats_grouptotal_agg.downloads "
				."FROM ".$xoopsDB->prefix("xf_groups")." g,".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_frs_package")." fp,".$xoopsDB->prefix("xf_frs_release")." fr " //, frs_dlstats_grouptotal_agg "
				."WHERE ( fr.release_date > '$start_time' "
				."AND fr.package_id = fp.package_id "
				."AND fp.group_id = g.group_id "
				."AND fr.released_by = u.uid "
				."AND g.status='A' "
				."AND g.is_public=1 "
				."AND fr.status_id=1 ) "
	      ."ORDER BY fr.release_date DESC";
				
$res_new = $xoopsDB->query($query, 21, $offset);

if (!$res_new || $xoopsDB->getRowsNum($res_new) < 1) {
	echo $xoopsDB->error();
	echo "<H4>"._XF_NEW_NONEWRELEASES."</H4>";
} else {

	if ( $xoopsDB->getRowsNum($res_new) > 20 ) {
		$rows = 20;
	} else {
		$rows = $xoopsDB->getRowsNum($res_new);
	}

	print "\t<TABLE width=100% cellpadding=0 cellspacing=0 border=0>";
	for ($i=0; $i<$rows; $i++) {
		$row_new = $xoopsDB->fetchArray($res_new);
		// avoid dupulicates of different file types
		if (!($G_RELEASE["$row_new[group_id]"])) {
			print "<TR valign=top>";
			print "<TD colspan=2>";
			print "<A href='".XOOPS_URL."/modules/xfmod/project/?".$row_new['unix_group_name']."'><B>".$ts->makeTboxData4Show($row_new['group_name'])."</B></A>"
				   ."\n</TD><TD nowrap><I>"._XF_NEW_RELEASEDBY.": <A href='".XOOPS_URL."/userinfo.php?uid=$row_new[uid]/'>"
				   ."$row_new[uname]</A></I></TD></TR>\n";	

			print "<TR><TD>"._XF_NEW_MODULE.": ".$ts->makeTboxData4Show($row_new['module_name'])."</TD>\n";
			print "<TD>"._XF_NEW_VERSION.": $row_new[release_version]</TD>\n";
			print "<TD>" . date("M d, h:iA",$row_new[release_date]) . "</TD>\n";
			print "</TR>";

			print "<TR valign=top>";
			print "<TD colspan=2>&nbsp;<BR>";
			if ($row_new['short_description']) {
				print "<I>".$ts->makeTareaData4Show($row_new['short_description'])."</I>";
			} else {
				print "<I>"._XF_NEW_NODESCRIPTION."</I>";
			}
			// print "<P>Release rating: ";
			// print vote_show_thumbs($row_new[filerelease_id],2);
			print "</TD>";
			print '<TD align=center nowrap border=1>';
			// print '&nbsp;<BR>Rate this Release!<BR>';
			// print vote_show_release_radios($row_new[filerelease_id],2);
			print "&nbsp;</TD>";
			print "</TR>";

			print '<TR><TD colspan=3>';
			// link to whole file list for downloads
			print "&nbsp;<BR><A href='".XOOPS_URL."/modules/xfmod/project/showfiles.php?group_id=".$row_new[group_id]."&release_id=".$row_new[release_id]."#selected'>";
			print _XF_NEW_DOWNLOAD."</A> ";
			// notes for this release
			print "<A href='".XOOPS_URL."/modules/xfmod/project/shownotes.php?release_id=".$row_new[release_id]."'>";
			print _XF_NEW_NOTESCHANGES."</A>";
			print '<HR></TD></TR>';

			$G_RELEASE["$row_new[group_id]"] = 1;
		}
	}

	echo "<TR BGCOLOR=\"#EEEEEE\"><TD>";
  if ($offset != 0) {
	  echo "<FONT face='Arial, Helvetica' SIZE=3 STYLE='text-decoration:none;'><B>";
		echo "<A HREF='".XOOPS_URL."/modules/xfnewrelease/?offset=".($offset-20)."'><B>"
		    ."<img src='".XOOPS_URL."/modules/xfmod/images/t2.gif' width='15' height='15' border='0' alt='"._XF_NEW_NEWERRELEASES."'>"
				." "._XF_NEW_NEWERRELEASES."</A></B></FONT>";
  } else {
	  echo "&nbsp;";
  }

	echo "</TD><TD COLSPAN=\"2\" ALIGN=\"RIGHT\">";
	if ($xoopsDB->getRowsNum($res_new)>$rows) {
		echo "<FONT face='Arial, Helvetica' SIZE=3 STYLE='text-decoration:none;'><B>";
		echo "<A HREF='".XOOPS_URL."/modules/xfnewrelease/?offset=".($offset+20)."'><B>"._XF_NEW_OLDERRELEASES." "
		    ."<img src='".XOOPS_URL."/modules/xfmod/images/t.gif' width='15' height='15' border='0' alt='"._XF_NEW_OLDERRELEASES."'>"
				."</A></B></FONT>";
	} else {
		echo "&nbsp;";
	}
	echo "</TD></TR></TABLE>";

}
include("../../footer.php");
?>
