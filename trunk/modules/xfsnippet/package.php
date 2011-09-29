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

$langfile="snippet.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");

if ($xoopsUser) {

	if ($post_changes) {
		/*
			Create a new snippet entry, then create a new snippet version entry
		*/
		if ($name && $description && $language != 0 && $category != 0 && $version) {
			/*
				Create the new package
			*/
			$sql = "INSERT INTO ".$xoopsDB->prefix("xf_snippet_package")." (category,created_by,name,description,language) "
			      ."VALUES ('$category','".$xoopsUser->getVar("uid")."','".$ts->makeTboxData4Save($name)."','".$ts->makeTboxData4Save($description)."','$language')";

			$result = $xoopsDB->queryF($sql);

			if (!$result) {
				//error in database

				include("../../header.php");
				echo snippet_header(_XF_SNP_CREATEAPACKAGE);

				echo ' ERROR DOING SNIPPET PACKAGE INSERT! ';
				echo $xoopsDB->error();

				//snippet_footer();
				include("../../footer.php");
				exit;
			} else {
				$feedback .= ' Snippet Package Added Successfully. ';
				$snippet_package_id = $xoopsDB->getInsertId();
				/*
					create the snippet package version
				*/
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_snippet_package_version")." "
				      ."(snippet_package_id,changes,version,submitted_by,date) "
							."VALUES ('$snippet_package_id','".$ts->makeTareaData4Save($changes)."',"
							."'".$ts->makeTboxData4Save($version)."','".$xoopsUser->getVar("uid")."','".time()."')";

				$result = $xoopsDB->queryF($sql);

				if (!$result) {
					//error in database

					include("../../header.php");
					echo snippet_header(_XF_SNP_CREATEAPACKAGE);

					echo ' ERROR DOING SNIPPET PACKAGE VERSION INSERT! ';
					echo $xoopsDB->error();

					//snippet_footer();
					include("../../footer.php");
					exit;
				} else {
					//so far so good - now add snippets to the package
					//id for this snippet_package_version
					$snippet_package_version_id = $xoopsDB->getInsertId();

					redirect_header(XOOPS_URL."/modules/xfsnippet/add_snippet_to_package.php?snippet_package_version_id=$snippet_package_version_id",0,"");
					exit;
				}
			}
		} else {
		  echo _XF_SNP_GOBACKFILLALLINFO;
		}

	}
	// sql queries
	$sql_category = $xoopsDB->query("SELECT type_id,name FROM ".$xoopsDB->prefix("xf_snippet_category"));
	$SCRIPT_CATEGORY_ids = util_result_column_to_array($sql_category, 0);
	$SCRIPT_CATEGORY_val = util_result_column_to_array($sql_category, 1);
	// sql queries
	$sql_language = $xoopsDB->query("SELECT type_id,name FROM ".$xoopsDB->prefix("xf_snippet_language"));
	$SCRIPT_LANGUAGE_ids = util_result_column_to_array($sql_language, 0);
	$SCRIPT_LANGUAGE_val = util_result_column_to_array($sql_language, 1);

	include("../../header.php");
	echo snippet_header(_XF_SNP_CREATEAPACKAGE);

	?>
	<P>
	<?php echo _XF_SNP_CANGROUPTOGETHER; ?>
	<P>
	<OL>
	<LI><?php echo _XF_SNP_CREATEPACKAGETHISFORM; ?>
	<LI><?php echo _XF_SNP_THENADDSNIPPETSTOIT; ?>
	</OL>
	<P>
	<FONT COLOR="RED"><B><?php echo _XF_SNP_NOTE; ?>:</B></FONT> <?php echo _XF_SNP_YOUCANSUBMITPACKAGEBYBROWSE; ?>
	<P>
	<FORM ACTION="<?php echo $_SERVER['PHP_SELF']; ?>" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
	<INPUT TYPE="HIDDEN" NAME="changes" VALUE="First Posted Version">

	<TABLE>

	<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_TITLE; ?>:</B><BR>
		<INPUT TYPE="TEXT" NAME="name" SIZE="60" MAXLENGTH="60">
	</TD></TR>

	<TR><TD COLSPAN="2"><B><?php echo _XF_G_DESCRIPTION; ?>:</B><BR>
		<TEXTAREA NAME="description" ROWS="5" COLS="85" WRAP="SOFT"></TEXTAREA>
	</TD></TR>

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

	<TR><TD COLSPAN="2" ALIGN="MIDDLE">
		<B><?php echo _XF_SNP_MAKESUREALLCOMPLETE; ?></B>
		<BR>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="<?php echo _XF_G_SUBMIT; ?>">
	</TD></TR>

	</TABLE>
	<?php
	//snippet_footer();
	include("../../footer.php");

} else {

  redirect_header(XOOPS_URL."/novelllogin.php?ref=/modules/xfsnippet/package.php", 2, _NOPERM);
  exit;

}

?>
