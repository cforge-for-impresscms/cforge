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

$langfile="snippet.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");



if ($xoopsUser) {

	if ($post_changes) {
		/*
			Create a new snippet entry, then create a new snippet version entry
		*/
		if ($snippet_name && $description && $language != 0 && $category != 0 && $type != 0 && $version && $code) {

			$sql = "INSERT INTO ".$xoopsDB->prefix("xf_snippet")." (category,created_by,name,description,type,language,license) "
			      ."VALUES ('$category','".$xoopsUser->getVar("uid")."','". $ts->makeTboxData4Save($snippet_name)."',"
						."'".$ts->makeTareaData4Save($description)."','$type','$language','$license')";

			$result = $xoopsDB->queryF($sql);
			if (!$result) {
				addMsg('ERROR DOING SNIPPET INSERT - '. $xoopsDB->error());
			} else {
				addMsg(_XF_SNP_SNIPPETADDED);
				$snippet_id = $xoopsDB->getInsertId();
				/*
					create the snippet version
				*/
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_snippet_version")." (snippet_id,changes,version,submitted_by,date,code) "
				      ."VALUES ('$snippet_id','".$ts->makeTareaData4Save($changes)."',"
							."'".$ts->makeTboxData4Save($version)."','".$xoopsUser->getVar("uid")."',"
							."'".time()."','".$ts->makeTareaData4Save($code)."')";

				$result = $xoopsDB->queryF($sql);
				if (!$result) {
					addMsg('ERROR DOING SNIPPET VERSION INSERT - ' . $xoopsDB->error());
				} else {
					addMsg(_XF_SNP_SNIPPETVERSIONADDED);
				}
			}
		} else {
		  addMsg(_XF_SNP_GOBACKFILLALLINFO);
		}

	}

	// sql queries
	$sql_type = $xoopsDB->query("SELECT type_id,name FROM ".$xoopsDB->prefix("xf_snippet_type"));
	$SCRIPT_TYPE_ids = util_result_column_to_array($sql_type, 0);
	$SCRIPT_TYPE_val = util_result_column_to_array($sql_type, 1);
	// sql queries
	$sql_category = $xoopsDB->query("SELECT type_id,name FROM ".$xoopsDB->prefix("xf_snippet_category"));
	$SCRIPT_CATEGORY_ids = util_result_column_to_array($sql_category, 0);
	$SCRIPT_CATEGORY_val = util_result_column_to_array($sql_category, 1);
	// sql queries
	$sql_language = $xoopsDB->query("SELECT type_id,name FROM ".$xoopsDB->prefix("xf_snippet_language"));
	$SCRIPT_LANGUAGE_ids = util_result_column_to_array($sql_language, 0);
	$SCRIPT_LANGUAGE_val = util_result_column_to_array($sql_language, 1);

	//$metaTitle=": "._XF_SNP_SUBMITNEWSNIPPET;
	include("../../header.php");
	echo snippet_header(_XF_SNP_SUBMITNEWSNIPPET);

	?>
	<P><?php echo _XF_SNP_YOUCANPOSTSNIPPET; ?>
	<P>
	<FONT COLOR="RED"><B><?php echo _XF_SNP_NOTE; ?>:</B></FONT> <?php echo _XF_SNP_YOUCANPOSTSNIPPETBYBROWSE; ?>
	<P>
	<FORM ACTION="<?php echo $_SERVER['PHP_SELF']; ?>" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
	<INPUT TYPE="HIDDEN" NAME="changes" VALUE="First Posted Version">

	<TABLE>

	<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_TITLE; ?>:</B><BR>
		<INPUT TYPE="TEXT" NAME="snippet_name" SIZE="60" MAXLENGTH="60">
	</TD></TR>

	<TR><TD COLSPAN="2"><B><?php echo _XF_G_DESCRIPTION; ?>:</B><BR>
		<TEXTAREA NAME="description" ROWS="5" COLS="85" WRAP="SOFT"></TEXTAREA>
	</TD></TR>

	<TR>
	<TD><B><?php echo _XF_SNP_TYPE; ?>:</B><BR>
		<?php echo html_build_select_box_from_arrays($SCRIPT_TYPE_ids,$SCRIPT_TYPE_val,'type',100,false); ?>
		<BR>
		<A HREF="<?php echo XOOPS_URL; ?>/modules/xfmod/tracker/?func=add&group_id=1&atid=102">Suggest a Script Type</A>
	</TD>

	<TD><B><?php echo _XF_SNP_LICENSE; ?>:</B><BR>
		<?php echo html_build_select_box_from_array($SCRIPT_LICENSE,'license'); ?>
	</TD>
	</TR>

	<TR>
	<TD><B><?php echo _XF_SNP_LANGUAGE; ?>:</B><BR>
		<?php echo html_build_select_box_from_arrays($SCRIPT_LANGUAGE_ids,$SCRIPT_LANGUAGE_val,'language',100,false); ?>
		<BR>
  	<A HREF="<?php echo XOOPS_URL; ?>/modules/xfmod/tracker/?func=add&group_id=1&atid=102">Suggest a Language</A>
	</TD>

	<TD><B><?php echo _XF_SNP_CATEGORY; ?>:</B><BR>
		<?php echo html_build_select_box_from_arrays($SCRIPT_CATEGORY_ids,$SCRIPT_CATEGORY_val,'category',100,false); ?>
    <BR>
    <A HREF="<?php echo XOOPS_URL; ?>/modules/xfmod/tracker/?func=add&group_id=1&atid=102">Suggest a Category</A>
	</TD>
	</TR>

	<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_VERSION; ?>:</B><BR>
		<INPUT TYPE="TEXT" NAME="version" SIZE="10" MAXLENGTH="15">
	</TD></TR>

	<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_PASTECODEHERE; ?>:</B><BR>
		<TEXTAREA NAME="code" ROWS="30" COLS="85" WRAP="SOFT"></TEXTAREA>
	</TD></TR>

	<TR><TD COLSPAN="2" ALIGN="MIDDLE">
		<B>Make sure all info is complete and accurate</B>
		<BR>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="<?php echo _XF_G_SUBMIT; ?>">
	</TD></TR>
	</FORM>
	</TABLE>
	<?php
	//snippet_footer();
	include("../../footer.php");
} else {

  redirect_header(XOOPS_URL."/user.php?xoops_redirect=/modules/xfsnippet/submit.php", 2, _NOPERM);
  exit;
}
?>
