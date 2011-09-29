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
	 
	$langfile = "new.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
	 
	$metaTitle = ": "._XF_NEW_NEWFILERELEASES."";
	include("../../header.php");
	 
	echo "<H4 style='text-align:center;'>"._XF_NEW_NEWFILERELEASES."</H4>";
	 
	if (!$offset || $offset < 0 )
	{
		$offset = 0;
	}
	 
	// For expediancy, list only the filereleases in the past three days.
	$start_time = time() - (7 * 86400);
	 
	$query = "SELECT g.group_name," ."g.group_id," ."g.unix_group_name," ."g.short_description," ."u.uname," ."u.uid," ."fr.release_id," ."fr.name AS release_version," ."fr.release_date," ."fr.released_by," ."fp.name AS module_name " //    ."frs_dlstats_grouptotal_agg.downloads "
	."FROM ".$icmsDB->prefix("xf_groups")." g,".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_frs_package")." fp,".$icmsDB->prefix("xf_frs_release")." fr " //, frs_dlstats_grouptotal_agg "
	."WHERE ( fr.release_date > '$start_time' " ."AND fr.package_id = fp.package_id " ."AND fp.group_id = g.group_id " ."AND fr.released_by = u.uid " ."AND g.status='A' " ."AND g.is_public=1 " ."AND fr.status_id=1 ) " ."ORDER BY fr.release_date DESC";
	 
	$res_new = $icmsDB->query($query, 21, $offset);
	 
	if (!$res_new || $icmsDB->getRowsNum($res_new) < 1)
	{
		echo $icmsDB->error();
		echo "<H4>"._XF_NEW_NONEWRELEASES."</H4>";
	}
	else
	{
		 
		if ($icmsDB->getRowsNum($res_new) > 20 )
		{
			$rows = 20;
		}
		else
		{
			$rows = $icmsDB->getRowsNum($res_new);
		}
		 
		print "\t<table width=100% cellpadding=0 cellspacing=0 border=0>";
		for ($i = 0; $i < $rows; $i++)
		{
			$row_new = $icmsDB->fetchArray($res_new);
			// avoid dupulicates of different file types
			if (!($G_RELEASE["$row_new[group_id]"]))
			{
				print "<th valign=top>";
				print "<td colspan=2>";
				print "<A href='".ICMS_URL."/modules/xfmod/project/?".$row_new['unix_group_name']."'><strong>".$ts->makeTboxData4Show($row_new['group_name'])."</strong></a>" ."\r\n</td><td nowrap><I>"._XF_NEW_RELEASEDBY.": <A href='".ICMS_URL."/userinfo.php?uid=$row_new[uid]/'>" ."$row_new[uname]</a></I></td></th>\n";
				 
				print "<th><td>"._XF_NEW_MODULE.": ".$ts->makeTboxData4Show($row_new['module_name'])."</td>\n";
				print "<td>"._XF_NEW_VERSION.": $row_new[release_version]</td>\n";
				print "<td>" . date("M d, h:iA", $row_new[release_date]) . "</td>\n";
				print "</th>";
				 
				print "<th valign=top>";
				print "<td colspan=2>&nbsp;<BR>";
				if ($row_new['short_description'])
				{
					print "<I>".$ts->makeTareaData4Show($row_new['short_description'])."</I>";
				}
				else
				{
					print "<I>"._XF_NEW_NODESCRIPTION."</I>";
				}
				// print "<p>Release rating: ";
				// print vote_show_thumbs($row_new[filerelease_id],2);
				print "</td>";
				print '<td align=center nowrap border=1>';
				// print '&nbsp;<BR>Rate this Release!<BR>';
				// print vote_show_release_radios($row_new[filerelease_id],2);
				print "&nbsp;</td>";
				print "</th>";
				 
				print '<th><td colspan=3>';
				// link to whole file list for downloads
				print "&nbsp;<BR><A href='".ICMS_URL."/modules/xfmod/project/showfiles.php?group_id=".$row_new[group_id]."&release_id=".$row_new[release_id]."#selected'>";
				print _XF_NEW_DOWNLOAD."</a> ";
				// notes for this release
				print "<A href='".ICMS_URL."/modules/xfmod/project/shownotes.php?release_id=".$row_new[release_id]."'>";
				print _XF_NEW_NOTESCHANGES."</a>";
				print '<HR></td></th>';
				 
				$G_RELEASE["$row_new[group_id]"] = 1;
			}
		}
		 
		echo "<th BGCOLOR=\"#EEEEEE\"><td>";
		if ($offset != 0)
		{
			echo "<FONT face='Arial, Helvetica' size=3 STYLE='text-decoration:none;'><strong>";
			echo "<a href='".ICMS_URL."/modules/xfnewrelease/?offset=".($offset-20)."'><strong>" ."<img src='".ICMS_URL."/modules/xfmod/images/t2.gif' width='15' height='15' border='0' alt='"._XF_NEW_NEWERRELEASES."'>" ." "._XF_NEW_NEWERRELEASES."</a></strong></FONT>";
		}
		else
		{
			echo "&nbsp;";
		}
		 
		echo "</td><td colspan=\"2\" align=\"RIGHT\">";
		if ($icmsDB->getRowsNum($res_new) > $rows)
		{
			echo "<FONT face='Arial, Helvetica' size=3 STYLE='text-decoration:none;'><strong>";
			echo "<a href='".ICMS_URL."/modules/xfnewrelease/?offset=".($offset+20)."'><strong>"._XF_NEW_OLDERRELEASES." " ."<img src='".ICMS_URL."/modules/xfmod/images/t.gif' width='15' height='15' border='0' alt='"._XF_NEW_OLDERRELEASES."'>" ."</a></strong></FONT>";
		}
		else
		{
			echo "&nbsp;";
		}
		echo "</td></th></table>";
		 
	}
	include("../../footer.php");
?>