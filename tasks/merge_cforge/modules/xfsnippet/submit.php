<?php
	/**
	*
	* SourceForge Code Snippets Repository
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: submit.php,v 1.6 2004/02/27 15:23:33 devsupaul Exp $
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
			if ($snippet_name && $description && $language != 0 && $category != 0 && $type != 0 && $version && $code)
			{
				 
				$sql = "INSERT INTO ".$icmsDB->prefix("xf_snippet")." (category,created_by,name,description,type,language,license) " ."VALUES ('$category','".$icmsUser->getVar("uid")."','". $ts->makeTboxData4Save($snippet_name)."'," ."'".$ts->makeTareaData4Save($description)."','$type','$language','$license')";
				 
				$result = $icmsDB->queryF($sql);
				if (!$result)
				{
					addMsg('ERROR DOING SNIPPET INSERT - '. $icmsDB->error());
				}
				else
				{
					addMsg(_XF_SNP_SNIPPETADDED);
					$snippet_id = $icmsDB->getInsertId();
					/*
					create the snippet version
					*/
					$sql = "INSERT INTO ".$icmsDB->prefix("xf_snippet_version")." (snippet_id,changes,version,submitted_by,date,code) " ."VALUES ('$snippet_id','".$ts->makeTareaData4Save($changes)."'," ."'".$ts->makeTboxData4Save($version)."','".$icmsUser->getVar("uid")."'," ."'".time()."','".$ts->makeTareaData4Save($code)."')";
					 
					$result = $icmsDB->queryF($sql);
					if (!$result)
					{
						addMsg('ERROR DOING SNIPPET VERSION INSERT - ' . $icmsDB->error());
					}
					else
					{
						addMsg(_XF_SNP_SNIPPETVERSIONADDED);
					}
				}
			}
			else
			{
				addMsg(_XF_SNP_GOBACKFILLALLINFO);
			}
			 
		}
		 
		// sql queries
		$sql_type = $icmsDB->query("SELECT type_id,name FROM ".$icmsDB->prefix("xf_snippet_type"));
		$SCRIPT_TYPE_ids = util_result_column_to_array($sql_type, 0);
		$SCRIPT_TYPE_val = util_result_column_to_array($sql_type, 1);
		// sql queries
		$sql_category = $icmsDB->query("SELECT type_id,name FROM ".$icmsDB->prefix("xf_snippet_category"));
		$SCRIPT_CATEGORY_ids = util_result_column_to_array($sql_category, 0);
		$SCRIPT_CATEGORY_val = util_result_column_to_array($sql_category, 1);
		// sql queries
		$sql_language = $icmsDB->query("SELECT type_id,name FROM ".$icmsDB->prefix("xf_snippet_language"));
		$SCRIPT_LANGUAGE_ids = util_result_column_to_array($sql_language, 0);
		$SCRIPT_LANGUAGE_val = util_result_column_to_array($sql_language, 1);
		 
		//$metaTitle=": "._XF_SNP_SUBMITNEWSNIPPET;
		include("../../header.php");
		echo snippet_header(_XF_SNP_SUBMITNEWSNIPPET);
		 
	?>
<p><?php echo _XF_SNP_YOUCANPOSTSNIPPET; ?>
<p>
<FONT COLOR="RED"><strong><?php echo _XF_SNP_NOTE; ?>:</strong></FONT> <?php echo _XF_SNP_YOUCANPOSTSNIPPETBYBROWSE; ?>
<p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<input type="hidden" name="post_changes" value="y">
<input type="hidden" name="changes" value="First Posted Version">

<table>

<th><td colspan="2"><strong><?php echo _XF_SNP_TITLE; ?>:</strong><BR>
<input type="text" name="snippet_name" size="60" maxlength="60">
</td></th>

<th><td colspan="2"><strong><?php echo _XF_G_DESCRIPTION; ?>:</strong><BR>
<textarea name="description" rows="5" cols="85" WRAP="SOFT"></textarea>
</td></th>

<th>
<td><strong><?php echo _XF_SNP_TYPE; ?>:</strong><BR>
<?php echo html_build_select_box_from_arrays($SCRIPT_TYPE_ids,$SCRIPT_TYPE_val,'type',100,false); ?>
<BR>
<a href="<?php echo ICMS_URL; ?>/modules/xfmod/tracker/?func=add&group_id=1&atid=102">Suggest a Script Type</a>
</td>

<td><strong><?php echo _XF_SNP_LICENSE; ?>:</strong><BR>
<?php echo html_build_select_box_from_array($SCRIPT_LICENSE,'license'); ?>
</td>
</th>

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

<th><td colspan="2"><strong><?php echo _XF_SNP_PASTECODEHERE; ?>:</strong><BR>
<textarea name="code" rows="30" cols="85" WRAP="SOFT"></textarea>
</td></th>

<th><td colspan="2" align="MIDDLE">
<strong>Make sure all info is complete and accurate</strong>
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
		 
		redirect_header(ICMS_URL."/user.php?xoops_redirect=/modules/xfsnippet/submit.php", 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
	}
?>