<?php
	/**
	*
	* SourceForge Code Snippets Repository
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: add_snippet_to_package.php,v 1.4 2004/01/26 18:57:01 devsupaul Exp $
	*
	*/
	include_once ("../../mainfile.php");
	 
	$langfile = "snippet.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");
	 
	function handle_add_exit()
	{
		global $suppress_nav;
		if ($suppress_nav)
		{
			echo '';
		}
		else
		{
			snippet_footer();
			include("../../footer.php");
		}
		exit;
	}
	 
	if ($icmsUser)
	{
		 
		include("../../header.php");
		snippet_header(_XF_SNP_SUBMITNEWSNIPPET);
		 
		if (!$snippet_package_version_id)
		{
			//make sure the package id was passed in
			echo '<H4>Error - snippet_package_version_id missing</H4>';
			snippet_footer();
			include("../../footer.php");
			exit;
		}
		 
		if ($op == 'delete')
		{
			include_once("delete.php");
		}
		 
		if ($post_changes)
		{
			if ($xoopsForge['snippetowner'] || 5 == $icmsUser->getVar('level'))
			{
				if ($snippetlist != "")
				{
					$moresnippets = explode(",", $snippetlist);
					$available = array_merge($available, $moresnippets);
				}
			}
			foreach($available as $snippet_version_id)
			{
				/*
				check to see if they are the creator of this version
				*/
				$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_snippet_package_version")." " ."WHERE submitted_by='".$icmsUser->getVar("uid")."' AND " ."snippet_package_version_id='$snippet_package_version_id'");
				 
				if (!$result || $icmsDB->getRowsNum($result) < 1)
				{
					$xoopsForgeErrorHandler->addError(_XF_SNP_ONLYCREATORCANADDTOPACKAGE);
					continue;
				}
				 
				/*
				make sure the snippet_version_id exists
				*/
				$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_snippet_version")." WHERE snippet_version_id='$snippet_version_id'");
				 
				if (!$result || $icmsDB->getRowsNum($result) < 1)
				{
					$xoopsForgeErrorHandler->addError(_XF_SNP_SNIPPETDOESNOTEXIST);
					continue;
				}
				 
				/*
				make sure the snippet_version_id isn't already in this package
				*/
				$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_snippet_package_item")." " ."WHERE snippet_package_version_id='$snippet_package_version_id' " ."AND snippet_version_id='$snippet_version_id'");
				 
				if ($result && $icmsDB->getRowsNum($result) > 0)
				{
					$xoopsForgeErrorHandler->addError(_XF_SNP_SNIPPETALREADYADDEDTOPACKAGE);
					continue;
				}
				 
				/*
				create the snippet version
				*/
				$sql = "INSERT INTO ".$icmsDB->prefix("xf_snippet_package_item")." (snippet_package_version_id,snippet_version_id) " ."VALUES ('$snippet_package_version_id','$snippet_version_id')";
				 
				$result = $icmsDB->queryF($sql);
				 
				if (!$result)
				{
					$xoopsForgeErrorHandler->addError('The snippet could not be inserted into the database. '.$icmsDB->error());
				}
				else
				{
					$xoopsForgeErrorHandler->addMessage(_XF_SNP_SNIPPETVERSIONADDED);
				}
			}
		}
		 
		$result = $icmsDB->query("SELECT sp.name,spv.version " ."FROM ".$icmsDB->prefix("xf_snippet_package")." sp,".$icmsDB->prefix("xf_snippet_package_version")." spv " ."WHERE sp.snippet_package_id=spv.snippet_package_id " ."AND spv.snippet_package_version_id='$snippet_package_version_id'");
		 
	?>
<table border=0><th><td valign=top>
<strong><?php echo _XF_SNP_PACKAGE; ?>:</strong><BR>
<?php echo $ts->makeTboxData4Show(unofficial_getDBResult($result,0,'name')) . ' v' . $ts->makeTboxData4Show(unofficial_getDBResult($result,0,'version')); ?>
<p>
<?php echo _XF_SNP_CANUSEFORMREPEATEDLY; ?>
<p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<input type="hidden" name="post_changes" value="y">
<input type="hidden" name="snippet_package_version_id" value="<?php echo $snippet_package_version_id; ?>">
<input type="hidden" name="suppress_nav" value="<?php echo $suppress_nav; ?>">
	<?php
		//get all my snippets
		$sql = "SELECT sv.snippet_version_id, s.name, sv.version" ." FROM ".$icmsDB->prefix("xf_snippet")." as s" .",".$icmsDB->prefix("xf_snippet_version")." as sv" ." WHERE s.snippet_id=sv.snippet_id " ." AND s.created_by=".$icmsUser->getVar('uid');
		$result = $icmsDB->query($sql);
		 
		while ($row = $icmsDB->fetchArray($result))
		{
			$mysnippets[] = $row;
		}
		//get all the snippets in this package
		$sql = "SELECT spi.snippet_version_id, s.name, sv.version " ."FROM ".$icmsDB->prefix("xf_snippet")." s,".$icmsDB->prefix("xf_snippet_version")." sv,".$icmsDB->prefix("xf_snippet_package_item")." spi " ."WHERE s.snippet_id=sv.snippet_id " ."AND sv.snippet_version_id=spi.snippet_version_id " ."AND spi.snippet_package_version_id='$snippet_package_version_id'";
		$result = $icmsDB->query($sql);
		 
		while ($row = $icmsDB->fetchArray($result))
		{
			$mypackage[] = $row;
			if (false !== ($key = array_search($row, $mysnippets)))
			{
				unset($mysnippets[$key]);
			}
		}
	?>
<select name="available[]" size="10" multiple>
	<?php
		foreach($mysnippets as $mysnippet)
		{
			echo "<option value='".$mysnippet['snippet_version_id']."'>".$mysnippet['name']." v".$mysnippet['version']."</option>";
		}
	?>
</select>
	<?php
		if ($xoopsForge['snippetowner'] || 5 == $icmsUser->getVar('level'))
		{
			echo '<br><br>You may also enter a comma delemated list of snippet id numbers.  Browse the snippets to find each snippet id number.<br>';
			echo '<input type="text" name="snippetlist" size=30>';
		}
	?>

<BR><BR>
<input type="submit" name="submit" value="<?php echo _XF_SNP_ADDSNIPPET; ?>">
<br><br>
</form>
</td>
<td> &nbsp; </td>
<td> &nbsp; </td>
<td valign=top>
<?php $xoopsForgeErrorHandler->displayFeedback(); ?>
</td>
</th></table>
	<?php
		$title = _XF_SNP_SNIPPETSINPACKAGE;
		$content = "<table border='0' width='100%'>" ."<th><td></td><td><strong>Name</strong></td><td><strong>Version</strong></td></th>";
		 
		for ($i = 0; $i < count($mypackage); $i++)
		{
			$content .= '<th class="'.($i%2 > 0?'bg1':'bg3').'">' .'<td align="MIDDLE">' .'<a href="'.ICMS_URL.'/modules/xfsnippet/add_snippet_to_package.php?op=delete&type=frompackage&snippet_version_id='.$mypackage[$i]['snippet_version_id'].'&snippet_package_version_id='.$snippet_package_version_id.'">' .'<img src="'.ICMS_URL.'/modules/xfmod/images/ic/trash.png" width="16" height="16" border="0" alt="delete"></a>' .'</td><td>' .$ts->makeTboxData4Show($mypackage[$i]['name'])
			.'</td><td>' .$ts->makeTboxData4Show($mypackage[$i]['version'])
			."</td></th>";
			 
			//   $last_group = unofficial_getDBResult($result,$i,'group_id');
		}
		$content .= "</table>";
		 
		themesidebox($title, $content);
		 
		snippet_footer();
		include("../../footer.php");
		exit;
	}
	else
	{
		 
		redirect_header(ICMS_URL."/user.php", 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
		 
	}
	//this needs to be removed and have real themimg added instead.
	function themesidebox($title, $content)
	{
		echo"<table width='100%' border='0' cellspacing='1' cellpadding='5'><tr>" ."<td colspan='1'><div class='sidboxtitle'>$title</div></td></tr><tr>" ."<td><font>$content</font></td></tr></table>";
	}
	 
?>