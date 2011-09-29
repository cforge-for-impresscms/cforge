<?php
	/**
	*
	* SourceForge Code Snippets Repository
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: browse.php,v 1.4 2004/01/30 20:39:22 jcox Exp $
	*
	*/
	include_once ("../../mainfile.php");
	 
	$langfile = "snippet.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");
	 
	include("../../header.php");
	echo snippet_header(_XF_SNP_SNIPPETLIBRARY);
	 
	if (!$cat && !$lang)
	{
		$xoopsForgeErrorHandler->setSystemError("The URL you requested is invalid");
	}
	 
	 
	$sql = "SELECT u.uname,s.description,s.snippet_id,s.name ";
	$sql .= " FROM ".$icmsDB->prefix("xf_snippet")." s,".$icmsDB->prefix("users")." u ";
	if ($text != "") $sql .= ",".$icmsDB->prefix("xf_snippet_version")." v ";
	$sql .= " WHERE u.uid=s.created_by ";
	if ($lang && $lang != 100) $sql .= " AND s.language=$lang";
	if ($cat && $cat != 100) $sql .= " AND s.category=$cat";
	if ($type && $type != 100) $sql .= " AND s.type=$type";
	if ($license && $license != count($SCRIPT_LICENSE)) $sql .= " AND s.license=$license";
	if ($text != "")
	{
		$sql .= " AND v.snippet_id=s.snippet_id";
		$sql .= " AND (s.name LIKE '%".$text."%' || s.description LIKE '%".$text."%' || v.code LIKE '%".$text."%')";
		$sql .= " GROUP BY s.snippet_id";
	}
	 
	 
	$result = $icmsDB->query($sql);
	$rows = $icmsDB->getRowsNum($result);
	 
	if ((!$type || $type == 100) && (!$license || $license == count($SCRIPT_LICENSE)))
	{
		$sql2 = "SELECT u.uname,sp.description,sp.snippet_package_id,sp.name ";
		$sql2 .= " FROM ".$icmsDB->prefix("xf_snippet_package")." sp,".$icmsDB->prefix("users")." u ";
		if ($text != "")
		{
			$sql2 .= ", ".$icmsDB->prefix("xf_snippet_package_item")." pi,".$icmsDB->prefix("xf_snippet_version")." sv ";
			$sql2 .= ", ".$icmsDB->prefix("xf_snippet_package_version")." pv";
		}
		$sql2 .= " WHERE u.uid=sp.created_by ";
		if ($lang && $lang != 100) $sql2 .= " AND sp.language='$lang'";
		if ($cat && $cat != 100) $sql2 .= " AND sp.category='$cat'";
		if ($text != "")
		{
			$sql2 .= " AND sp.snippet_package_id=pv.snippet_package_id";
			$sql2 .= " AND pv.snippet_package_version_id=pi.snippet_package_version_id";
			$sql2 .= " AND sv.snippet_version_id=pi.snippet_version_id";
			$sql2 .= " AND (sp.name LIKE '%".$text."%' || sp.description LIKE '%".$text."%' || sv.code LIKE '%".$text."%')";
			$sql2 .= " GROUP BY sp.snippet_package_id";
		}
		 
		$result2 = $icmsDB->query($sql2);
		$rows2 = $icmsDB->getRowsNum($result2);
	}
	 
	if ((!$result || $rows < 1) && (!$result2 || $rows2 < 1))
	{
		echo '<H4>'._XF_SNP_NOSNIPPETSFOUND.'</H4>';
	}
	else
	{
		 
		echo "<table border='0' width='100%'>" ."<tr class='bg4'>" ."<td><strong>"._XF_SNP_TITLE."</strong></td>" ."<td align=right><strong>"._XF_SNP_CREATOR."</strong></td>" ."</tr>";
		 
		/*
		List packages if there are any
		*/
		if ($rows2 > 0)
		{
			echo '
				<th class="bg2"><td colspan="2"><strong>'._XF_SNP_PACKAGESOFSNIPPETS.'</strong></td>';
		}
		for ($i = 0; $i < $rows2; $i++)
		{
			echo '<th class="'.($i%2 > 0?'bg1':'bg3').'">' .'<td valgin=top><a href="'.ICMS_URL.'/modules/xfsnippet/detail.php?type=package&id='.unofficial_getDBResult($result2, $i, 'snippet_package_id').'"><strong>'.$ts->makeTboxData4Show(unofficial_getDBResult($result2, $i, 'name')).'</strong></a></td>' .'<td valgin=top align=right>'.unofficial_getDBResult($result2, $i, 'uname').'</td></th>';
			echo '<th class="'.($i%2 > 0?'bg1':'bg3').'">' .'<td>'.$ts->makeTareaData4Show(unofficial_getDBResult($result2, $i, 'description')).'</td><td>&nbsp;</td></th>';
		}
		 
		 
		/*
		List snippets if there are any
		*/
		 
		if ($rows > 0)
		{
			echo '<th><td colspan=2>&nbsp;</td></th>';
			echo '<th class="bg2"><td colspan="3"><strong>'._XF_SNP_SNIPPETS.'</strong></td>';
		}
		for ($i = 0; $i < $rows; $i++)
		{
			echo '<th class="'.($i%2 > 0?'bg1':'bg3').'">' .'<td valign=top><a href="'.ICMS_URL.'/modules/xfsnippet/detail.php?type=snippet&id='.unofficial_getDBResult($result, $i, 'snippet_id').'"><strong>'.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'name')).'</strong></a></td>' .'<td valign=top align=right>'.unofficial_getDBResult($result, $i, 'uname').'</td></th>';
			echo '<th class="'.($i%2 > 0?'bg1':'bg3').'">' .'<td>'.$ts->makeTareaData4Show(unofficial_getDBResult($result, $i, 'description')).'</td><td>&nbsp;</td></th>';
		}
		 
		echo '</table>';
		 
	}
	 
	//snippet_footer();
	include("../../footer.php");
	 
?>