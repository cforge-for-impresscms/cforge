<?php
	/**
	*
	* SourceForge Code Snippets Repository
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: detail.php,v 1.5 2004/01/30 20:39:22 jcox Exp $
	*
	*/
	include_once ("../../mainfile.php");
	 
	$langfile = "snippet.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");
	 
	/*
	Show a detail page for either a snippet or a package
	or a specific version of a package
	*/
	if ($op == 'delete')
	{
		include_once("delete.php");
	}
	include("../../header.php");
	 
	$xoopsForgeErrorHandler->displayFeedback();
	 
	if ($type == 'snippet')
		{
		/*
		View a snippet and show its versions
		Expand and show the code for the latest version
		*/
		 
		 
		if (!snippet_show_snippet_details($id))
		{
			$xoopsForgeErrorHandler->addError("The snippet you requested was not found.");
			$xoopsForgeErrorHandler->displayFeedback();
			snippet_footer();
			include("../../footer.php");
			exit;
		}
		 
		/*
		Get all the versions of this snippet
		*/
		$sql = "SELECT u.uname,sv.snippet_version_id,pi.snippet_version_id as packaged, sv.version,sv.date,sv.changes " ."FROM ".$icmsDB->prefix("users")." as u JOIN ".$icmsDB->prefix("xf_snippet_version")." as sv " ." LEFT JOIN ".$icmsDB->prefix("xf_snippet_package_item")." as pi " ." ON sv.snippet_version_id=pi.snippet_version_id " ." WHERE u.uid=sv.submitted_by " ." AND snippet_id='$id' " ." GROUP BY sv.snippet_version_id" ." ORDER BY sv.snippet_version_id DESC";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		 
		if (!$result || $rows < 1)
		{
			echo '<H4>Error - no versions found</H4>';
		}
		else
		{
			echo '<p><strong>'._XF_SNP_VERSIONSOFSNIPPET.':</strong><BR>';
			echo "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>"._XF_SNP_VERSIONCLICK."</strong></td>" ."<td><strong>"."Packages"."</strong></td>" ."<td><strong>"._XF_SNP_DATEPOSTED."</strong></td>" ."<td><strong>"._XF_SNP_SNIPPETID."</strong></td>" ."<td><strong>"._XF_SNP_AUTHOR."</strong></td>" ."<td></td>" ."</tr>";
			 
			/*
			get the newest version of this snippet, so we can display its code
			*/
			 
			for ($i = 0; $i < $rows; $i++)
			{
				$row = $icmsDB->fetchArray($result);
				if (!$version && $i == 0) $version = $row['snippet_version_id'];
				echo '<th class="'.($i%2 > 0?'bg1':'bg3').'">' .'<td valign=top><a href="'.ICMS_URL.'/modules/xfsnippet/detail.php?type=snippet&id='.$id.'&version='.$row['snippet_version_id'].'">' .'<strong>'.$ts->makeTboxData4Show($row['version']).'</strong></a></td>';
				if ($row['packaged'])
				{
					$rs = $icmsDB->query("SELECT p.name, p.snippet_package_id" ." FROM ".$icmsDB->prefix("xf_snippet_package")." as p" .", ".$icmsDB->prefix("xf_snippet_package_version")." as pv" .", ".$icmsDB->prefix("xf_snippet_package_item")." as pi" ." WHERE p.snippet_package_id=pv.snippet_package_id" ." AND pv.snippet_package_version_id=pi.snippet_package_version_id" ." AND pi.snippet_version_id=".$row['packaged'] ." GROUP BY p.snippet_package_id");
					echo '<td>';
					if ($rs)
					{
						while ($mypackage = $icmsDB->fetchArray($rs))
						{
							echo '<a href="'.ICMS_URL.'/modules/xfsnippet/detail.php?type=package&id='.$mypackage['snippet_package_id'].'">'.$mypackage['name'].'</a><br>';
						}
					}
					echo '</td>';
				}
				else
					{
					echo '<td>&nbsp;</td>';
				}
				echo '<td valign=top>'.date($sys_datefmt, $row['date']).'</td>' .'<td valign=top>'.$row['snippet_version_id'].'</td>' .'<td valign=top>'.$row['uname'].'</td>' .'<td align="center" valign=top>';
				if ($icmsUser && $icmsUser->getVar('uname') == $row['uname'])
				{
					echo '<a href="'.ICMS_URL.'/modules/xfsnippet/detail.php?op=delete&type=snippet&id='.$id.'&snippet_version_id='.$row['snippet_version_id'].'">';
					echo '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/trash.png" width="16" height="16" border="0" alt="delete"></a>';
				}
				echo '</td></th>';
				 
				echo '<th class="'.($i%2 > 0?'bg1':'bg3').'"><td colspan=6>'._XF_SNP_CHANGESSINCELASTVERSION.':<BR>'. $ts->makeTareaData4Show($row['changes']).'</td></th>';
			}
			echo '</table>';
			 
		}
		/*
		Show a link so you can add a new version of this snippet
		*/
		 
		echo '<p><strong><a href="'.ICMS_URL.'/modules/xfsnippet/addversion.php?type=snippet&id='.$id.'">'._XF_SNP_SUBMITNEWSNIPPETVERSION.'</a></strong>' .'<br>'._XF_SNP_YOUCANSUBMITIFMODIFIED.'</p>';
		 
		 
		snippet_show_snippet($id, $version);
		 
		 
		//snippet_footer();
		include("../../footer.php");
		 
	}
	else if ($type == 'package')
	{
		/*
		View a package and show its versions
		Expand and show the snippets for the latest version
		*/
		 
		 
		if (!snippet_show_package_details($id))
		{
			$xoopsForgeErrorHandler->addError("The package you requested was not found.");
			$xoopsForgeErrorHandler->displayFeedback();
			//snippet_footer();
			include("../../footer.php");
			exit;
		}
		 
		/*
		Get all the versions of this package
		*/
		$sql = "SELECT u.uname,spv.snippet_package_version_id,spv.version,spv.date " ."FROM ".$icmsDB->prefix("xf_snippet_package_version")." spv,".$icmsDB->prefix("users")." u " ."WHERE u.uid=spv.submitted_by " ."AND snippet_package_id='$id' " ."ORDER BY spv.snippet_package_version_id DESC";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		if (!$result || $rows < 1)
		{
			$xoopsForgeErrorHandler->addError("There were no versions of this package found.");
			$xoopsForgeErrorHandler->displayFeedback();
			//snippet_footer();
			include("../../footer.php");
			exit;
		}
		else
		{
			echo '
				<H4>'._XF_SNP_VERSIONSOFPACKAGE.':</H4>
				<p>';
			echo "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>"._XF_SNP_PACKAGEVERSION."</strong></td>" ."<td><strong>"._XF_SNP_DATEPOSTED."</strong></td>" ."<td><strong>"._XF_SNP_AUTHOR."</strong></td>" ."<td></td>" ."</tr>";
			 
			 
			if (!$package_version) $package_version = unofficial_getDBResult($result, 0, 'snippet_package_version_id');
			 
			for ($i = 0; $i < $rows; $i++)
			{
				echo '<th class="'.($i%2 > 0?"bg1":"bg3").'"><td>' .'<a href="'.ICMS_URL.'/modules/xfsnippet/detail.php?type=package&id='.$id.'&package_version='.unofficial_getDBResult($result, $i, 'snippet_package_version_id').'">' .'<strong>'.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'version')).'</strong></a></td>' .'<td>'.date($sys_datefmt, unofficial_getDBResult($result, $i, 'date')).'</td>' .'<td>'.unofficial_getDBResult($result, $i, 'uname').'</td>' .'<td align="MIDDLE">' .'<a href="'.ICMS_URL.'/modules/xfsnippet/add_snippet_to_package.php?snippet_package_version_id='.unofficial_getDBResult($result, $i, 'snippet_package_version_id').'">' .'<img src="'.ICMS_URL.'/modules/xfmod/images/ic/pencil.png" width="16" height="16" border="0" alt="delete"></a>&nbsp; &nbsp; &nbsp; ';
				if ($icmsUser && $icmsUser->getVar('uname') == unofficial_getDBResult($result, $i, 'uname'))
				{
					echo '<a href="'.ICMS_URL.'/modules/xfsnippet/detail.php?op=delete&type=package&id='.$id.'&snippet_package_version_id='.unofficial_getDBResult($result, $i, 'snippet_package_version_id').'">' .'<img src="'.ICMS_URL.'/modules/xfmod/images/ic/trash.png" width="16" height="16" border="0" alt="delete"></a>';
				}
				echo '</td></th>';
			}
			echo '</table>';
			 
		}
		/*
		Show a form so you can add a new version of this package
		*/
		echo '
			<p><strong><a href="'.ICMS_URL.'/modules/xfsnippet/addversion.php?type=package&id='.$id.'">'._XF_SNP_SUBMITNEWVERSION.'</a></strong>
			<BR>'._XF_SNP_YOUCANSUBMITIFMODIFIEDPACKAGE.'</p>';
		 
		/*
		show the latest version of the package
		and its snippets
		*/
		 
		echo '
			<p>
			<HR>
			<p>
			<H4>'._XF_SNP_LATESTPACKAGEVERSION.': '.$ts->makeTboxData4Show(unofficial_getDBResult($result, 0, 'version')).'</H4>
			<p>
			<p>';
		snippet_show_package_snippets($id, $package_version);
		 
		 
		snippet_show_snippet($snippet_id, $version);
		 
		 
		//snippet_footer();
		include("../../footer.php");
		 
		/*
		} else if ($type == 'packagever') {
		//  Show a specific version of a package and its specific snippet versions
		 
		 
		include("../../header.php");
		snippet_header(_XF_SNP_SNIPPETLIBRARY);
		 
		snippet_show_package_details($id);
		 
		snippet_show_package_snippets($id);
		 
		snippet_show_snippet($id,$version);
		 
		 
		snippet_footer();
		include("../../footer.php");
		//*/
	}
	else
	{
		 
		$xoopsForgeErrorHandler->setSystemError('Error - Your request was not understood.  Was the URL mangled?');
		 
	}
	 
?>