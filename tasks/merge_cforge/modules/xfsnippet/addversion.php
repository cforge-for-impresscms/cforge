<?php
	/**
	*
	* SourceForge Code Snippets Repository
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: addversion.php,v 1.3 2004/01/30 20:39:22 jcox Exp $
	*
	*/
	include_once ("../../mainfile.php");
	 
	$langfile = "snippet.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");
	 
	if ($icmsUser )
	{
		if ($type == 'snippet')
		{
			/*
			See if the snippet exists first
			*/
			$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_snippet")." WHERE snippet_id='$id'");
			 
			if (!$result || $icmsDB->getRowsNum($result) < 1)
			{
				echo "Error<br />Error - snippet doesn't exist";
				exit;
			}
			 
			/*
			handle inserting a new version of a snippet
			*/
			if ($post_changes)
			{
				/*
				Create a new snippet entry, then create a new snippet version entry
				*/
				if ($changes && $version && $code)
				{
					 
					/*
					create the snippet version
					*/
					$sql = "INSERT INTO ".$icmsDB->prefix("xf_snippet_version")." (snippet_id,changes,version,submitted_by,date,code) " ."VALUES ('$snippet_id','".$ts->makeTareaData4Save($changes)."'," ."'".$ts->makeTboxData4Save($version)."','".$icmsUser->getVar("uid")."'," ."'".time()."','".$ts->makeTareaData4Save($code)."')";
					 
					$result = $icmsDB->queryF($sql);
					if (!$result)
					{
						$feedback .= ' ERROR DOING SNIPPET VERSION INSERT! ';
						echo $icmsDB->error();
					}
					else
					{
						$feedback .= ' '._XF_SNP_SNIPPETVERSIONADDED.' ';
					}
				}
				else
				{
					echo _XF_SNP_GOBACKFILLALLINFO;
					exit;
				}
				 
			}
			include("../../header.php");
			echo snippet_header(_XF_SNP_SUBMITNEWSNIPPETVERSION);
			 
		?>
<p>
<?php echo _XF_SNP_IFMODIFIEDDOSHARE; ?>
<p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<input type="hidden" name="post_changes" value="y">
<input type="hidden" name="type" value="snippet">
<input type="hidden" name="snippet_id" value="<?php echo $id; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">

<table>
<th><td colspan="2"><strong><?php echo _XF_SNP_VERSION; ?>:</strong><BR>
<input type="text" name="version" size="10" maxlength="15">
</td></th>

<th><td colspan="2"><strong><?php echo _XF_SNP_CHANGES; ?>:</strong><BR>
<textarea name="changes" rows="5" cols="45"></textarea>
</td></th>

<th><td colspan="2"><strong><?php echo _XF_SNP_PASTECODEHERE; ?>:</strong><BR>
<textarea name="code" rows="30" cols="85" WRAP="SOFT"></textarea>
</td></th>

<th><td colspan="2" align="MIDDLE">
<strong><?php echo _XF_SNP_MAKESUREALLCOMPLETE; ?></strong>
<BR>
<input type="submit" name="submit" value="<?php echo _XF_G_SUBMIT; ?>">
</td></th>
</form>
</table>
		<?php
			 
			//snippet_footer();
			include("../../footer.php");
			 
		}
		else if ($type == 'package')
		{
			/*
			Handle insertion of a new package version
			*/
			 
			/*
			See if the package exists first
			*/
			$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_snippet_package")." WHERE snippet_package_id='$id'");
			 
			if (!$result || $icmsDB->getRowsNum($result) < 1)
			{
				$xoopsForgeErrorHandler->setSystemError('The snippet package you are trying to access does not exist');
			}
			 
			if ($post_changes)
			{
				/*
				Create a new snippet entry, then create a new snippet version entry
				*/
				if ($changes && $snippet_package_id)
				{
					/*
					create the snippet package version
					*/
					$sql = "INSERT INTO ".$icmsDB->prefix("xf_snippet_package_version")." " ."(snippet_package_id,changes,version,submitted_by,date) " ."VALUES ('$snippet_package_id','".$ts->makeTareaData4Save($changes)."'," ."'".$ts->makeTboxData4Save($version)."','".$icmsUser->getVar("uid")."','".time()."')";
					 
					$result = $icmsDB->queryF($sql);
					 
					if (!$result)
					{
						//error in database
						$xoopsForgeErrorHandler->setSystemError("Error inserting the snippet package version:<br>".$icmsDB->error());
					}
					else
					{
						//so far so good - now go add snippets to the package
						//id for this snippet_package_version
						$snippet_package_version_id = $icmsDB->getInsertId();
						redirect_header(ICMS_URL."/modules/xfsnippet/add_snippet_to_package.php?snippet_package_version_id=$snippet_package_version_id", 0, "");
						exit;
					}
					 
				}
				else
				{
					$xoopsForgeErrorHandler->addError(_XF_SNP_GOBACKFILLALLINFO);
				}
				 
			}
			include("../../header.php");
			echo snippet_header(_XF_SNP_SUBMITNEWSNIPPETVERSION);
			$xoopsForgeErrorHandler->displayFeedback();
		?>
<p>
<?php echo _XF_SNP_IFMODIFIEDPACKAGEDOSHARE; ?>
<p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<input type="hidden" name="post_changes" value="y">
<input type="hidden" name="type" value="package">
<input type="hidden" name="snippet_package_id" value="<?php echo $id; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">

<table>
<th><td colspan="2"><strong><?php echo _XF_SNP_VERSION; ?>:</strong><BR>
<input type="text" name="version" size="10" maxlength="15">
</td></th>

<th><td colspan="2"><strong><?php echo _XF_SNP_CHANGES; ?>:</strong><BR>
<textarea name="changes" rows="5" cols="45"></textarea>
</td></th>

<th><td colspan="2" align="MIDDLE">
<strong><?php echo _XF_SNP_MAKESUREALLCOMPLETE; ?></strong>
<BR>
<input type="submit" name="submit" value="<?php echo _XF_G_SUBMIT; ?>">
</td></th>
</form>
</table>
		<?php
			 
			//snippet_footer();
			include("../../footer.php");
		}
		else
		{
			$xoopsForgeErrorHandler->setSystemError('Was the URL or form mangled??');
		}
	}
	else
	{
		redirect_header(ICMS_URL."/novelllogin.php?ref=/modules/xfsnippet/addversion.php&type=$type&id=$id", 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
	}
	 
?>