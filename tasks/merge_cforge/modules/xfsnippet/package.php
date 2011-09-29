<?php
	/**
	*
	* SourceForge Code Snippets Repository
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: package.php,v 1.3 2004/01/30 20:39:22 jcox Exp $
	*
	*/
	include_once ("../../mainfile.php");
	 
	$langfile = "snippet.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");
	 
	if ($icmsUser)
	{
		 
		if ($post_changes)
		{
			/*
			Create a new snippet entry, then create a new snippet version entry
			*/
			if ($name && $description && $language != 0 && $category != 0 && $version)
			{
				/*
				Create the new package
				*/
				$sql = "INSERT INTO ".$icmsDB->prefix("xf_snippet_package")." (category,created_by,name,description,language) " ."VALUES ('$category','".$icmsUser->getVar("uid")."','".$ts->makeTboxData4Save($name)."','".$ts->makeTboxData4Save($description)."','$language')";
				 
				$result = $icmsDB->queryF($sql);
				 
				if (!$result)
				{
					//error in database
					 
					include("../../header.php");
					echo snippet_header(_XF_SNP_CREATEAPACKAGE);
					 
					echo ' ERROR DOING SNIPPET PACKAGE INSERT! ';
					echo $icmsDB->error();
					 
					//snippet_footer();
					include("../../footer.php");
					exit;
				}
				else
				{
					$feedback .= ' Snippet Package Added Successfully. ';
					$snippet_package_id = $icmsDB->getInsertId();
					/*
					create the snippet package version
					*/
					$sql = "INSERT INTO ".$icmsDB->prefix("xf_snippet_package_version")." " ."(snippet_package_id,changes,version,submitted_by,date) " ."VALUES ('$snippet_package_id','".$ts->makeTareaData4Save($changes)."'," ."'".$ts->makeTboxData4Save($version)."','".$icmsUser->getVar("uid")."','".time()."')";
					 
					$result = $icmsDB->queryF($sql);
					 
					if (!$result)
					{
						//error in database
						 
						include("../../header.php");
						echo snippet_header(_XF_SNP_CREATEAPACKAGE);
						 
						echo ' ERROR DOING SNIPPET PACKAGE VERSION INSERT! ';
						echo $icmsDB->error();
						 
						//snippet_footer();
						include("../../footer.php");
						exit;
					}
					else
					{
						//so far so good - now add snippets to the package
						//id for this snippet_package_version
						$snippet_package_version_id = $icmsDB->getInsertId();
						 
						redirect_header(ICMS_URL."/modules/xfsnippet/add_snippet_to_package.php?snippet_package_version_id=$snippet_package_version_id", 0, "");
						exit;
					}
				}
			}
			else
			{
				echo _XF_SNP_GOBACKFILLALLINFO;
			}
			 
		}
		// sql queries
		$sql_category = $icmsDB->query("SELECT type_id,name FROM ".$icmsDB->prefix("xf_snippet_category"));
		$SCRIPT_CATEGORY_ids = util_result_column_to_array($sql_category, 0);
		$SCRIPT_CATEGORY_val = util_result_column_to_array($sql_category, 1);
		// sql queries
		$sql_language = $icmsDB->query("SELECT type_id,name FROM ".$icmsDB->prefix("xf_snippet_language"));
		$SCRIPT_LANGUAGE_ids = util_result_column_to_array($sql_language, 0);
		$SCRIPT_LANGUAGE_val = util_result_column_to_array($sql_language, 1);
		 
		include("../../header.php");
		echo snippet_header(_XF_SNP_CREATEAPACKAGE);
		 
	?>
<p>
<?php echo _XF_SNP_CANGROUPTOGETHER; ?>
<p>
<OL>
<LI><?php echo _XF_SNP_CREATEPACKAGETHISFORM; ?>
<LI><?php echo _XF_SNP_THENADDSNIPPETSTOIT; ?>
</OL>
<p>
<FONT COLOR="RED"><strong><?php echo _XF_SNP_NOTE; ?>:</strong></FONT> <?php echo _XF_SNP_YOUCANSUBMITPACKAGEBYBROWSE; ?>
<p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<input type="hidden" name="post_changes" value="y">
<input type="hidden" name="changes" value="First Posted Version">

<table>

<th><td colspan="2"><strong><?php echo _XF_SNP_TITLE; ?>:</strong><BR>
<input type="text" name="name" size="60" maxlength="60">
</td></th>

<th><td colspan="2"><strong><?php echo _XF_G_DESCRIPTION; ?>:</strong><BR>
<textarea name="description" rows="5" cols="85" WRAP="SOFT"></textarea>
</td></th>

<th>
<td><strong><?php echo _XF_SNP_LANGUAGE; ?>:</strong><BR>
<?php echo html_build_select_box_from_arrays($SCRIPT_LANGUAGE_ids,$SCRIPT_LANGUAGE_val,'language',100,false); ?>
<BR>
<a href="<?php echo ICMS_URL; ?>/modules/xfmod/tracker/?func=add&group_id=1&atid=102">Suggest a Language</a>
</td>

<td><strong><?php echo _XF_SNP_CATEGORY; ?>:</strong><BR>
<?php echo html_build_select_box_from_arrays($SCRIPT_CATEGORY_ids,$SCRIPT_CATEGORY_val,'category',100,false); ?>
<BR>
<a href="<?php echo ICMS_URL; ?>/modules/xfmod/tracker/?func=add&group_id=1&atid=102">Suggest a Category</a>
</td>
</th>

<th><td colspan="2"><strong><?php echo _XF_SNP_VERSION; ?>:</strong><BR>
<input type="text" name="version" size="10" maxlength="15">
</td></th>

<th><td colspan="2" align="MIDDLE">
<strong><?php echo _XF_SNP_MAKESUREALLCOMPLETE; ?></strong>
<BR>
<input type="submit" name="submit" value="<?php echo _XF_G_SUBMIT; ?>">
</td></th>

</table>
	<?php
		//snippet_footer();
		include("../../footer.php");
		 
	}
	else
	{
		 
		redirect_header(ICMS_URL."/novelllogin.php?ref=/modules/xfsnippet/package.php", 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
		 
	}
	 
?>