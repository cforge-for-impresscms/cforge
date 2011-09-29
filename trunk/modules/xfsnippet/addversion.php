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

$langfile="snippet.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");

if ( $xoopsUser ) {
	if ($type == 'snippet') {
		/*
			See if the snippet exists first
		*/
		$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet")." WHERE snippet_id='$id'");

		if (!$result || $xoopsDB->getRowsNum($result) < 1) {
		  echo "Error<br />Error - snippet doesn't exist";
		  exit;
		}

		/*
			handle inserting a new version of a snippet
		*/
		if ($post_changes) {
			/*
				Create a new snippet entry, then create a new snippet version entry
			*/
			if ($changes && $version && $code) {

				/*
					create the snippet version
				*/
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_snippet_version")." (snippet_id,changes,version,submitted_by,date,code) "
				      ."VALUES ('$snippet_id','".$ts->makeTareaData4Save($changes)."',"
							."'".$ts->makeTboxData4Save($version)."','".$xoopsUser->getVar("uid")."',"
							."'".time()."','".$ts->makeTareaData4Save($code)."')";

				$result = $xoopsDB->queryF($sql);
				if (!$result) {
					$feedback .= ' ERROR DOING SNIPPET VERSION INSERT! ';
					echo $xoopsDB->error();
				} else {
					$feedback .= ' '._XF_SNP_SNIPPETVERSIONADDED.' ';
				}
			} else {
				echo _XF_SNP_GOBACKFILLALLINFO;
				exit;
			}

		}
		include("../../header.php");
		echo snippet_header(_XF_SNP_SUBMITNEWSNIPPETVERSION);

		?>
		<P>
		<?php echo _XF_SNP_IFMODIFIEDDOSHARE; ?>
		<P>
		<FORM ACTION="<?php echo $_SERVER['PHP_SELF']; ?>" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
		<INPUT TYPE="HIDDEN" NAME="type" VALUE="snippet">
		<INPUT TYPE="HIDDEN" NAME="snippet_id" VALUE="<?php echo $id; ?>">
		<INPUT TYPE="HIDDEN" NAME="id" VALUE="<?php echo $id; ?>">

		<TABLE>
		<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_VERSION; ?>:</B><BR>
			<INPUT TYPE="TEXT" NAME="version" SIZE="10" MAXLENGTH="15">
		</TD></TR>

		<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_CHANGES; ?>:</B><BR>
			<TEXTAREA NAME="changes" ROWS="5" COLS="45"></TEXTAREA>
		</TD></TR>

		<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_PASTECODEHERE; ?>:</B><BR>
			<TEXTAREA NAME="code" ROWS="30" COLS="85" WRAP="SOFT"></TEXTAREA>
		</TD></TR>

		<TR><TD COLSPAN="2" ALIGN="MIDDLE">
			<B><?php echo _XF_SNP_MAKESUREALLCOMPLETE; ?></B>
			<BR>
			<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="<?php echo _XF_G_SUBMIT; ?>">
		</TD></TR>
		</FORM>
		</TABLE>
		<?php

		//snippet_footer();
		include("../../footer.php");

	} else if ($type=='package') {
		/*
			Handle insertion of a new package version
		*/

		/*
			See if the package exists first
		*/
		$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_package")." WHERE snippet_package_id='$id'");

		if (!$result || $xoopsDB->getRowsNum($result) < 1) {
			$xoopsForgeErrorHandler->setSystemError('The snippet package you are trying to access does not exist');
		}

		if ($post_changes) {
			/*
				Create a new snippet entry, then create a new snippet version entry
			*/
			if ($changes && $snippet_package_id) {
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
					$xoopsForgeErrorHandler->setSystemError("Error inserting the snippet package version:<br>".$xoopsDB->error());
				} else {
					//so far so good - now go add snippets to the package
					//id for this snippet_package_version
					$snippet_package_version_id = $xoopsDB->getInsertId();
					redirect_header(XOOPS_URL."/modules/xfsnippet/add_snippet_to_package.php?snippet_package_version_id=$snippet_package_version_id",0,"");
					exit;
				}

			} else {
				$xoopsForgeErrorHandler->addError(_XF_SNP_GOBACKFILLALLINFO);
			}

		}
		include("../../header.php");
		echo snippet_header(_XF_SNP_SUBMITNEWSNIPPETVERSION);
		$xoopsForgeErrorHandler->displayFeedback();
		?>
		<P>
		<?php echo _XF_SNP_IFMODIFIEDPACKAGEDOSHARE; ?>
		<P>
		<FORM ACTION="<?php echo $_SERVER['PHP_SELF']; ?>" METHOD="POST">
		<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
		<INPUT TYPE="HIDDEN" NAME="type" VALUE="package">
		<INPUT TYPE="HIDDEN" NAME="snippet_package_id" VALUE="<?php echo $id; ?>">
		<INPUT TYPE="HIDDEN" NAME="id" VALUE="<?php echo $id; ?>">

		<TABLE>
		<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_VERSION; ?>:</B><BR>
			<INPUT TYPE="TEXT" NAME="version" SIZE="10" MAXLENGTH="15">
		</TD></TR>

		<TR><TD COLSPAN="2"><B><?php echo _XF_SNP_CHANGES; ?>:</B><BR>
			<TEXTAREA NAME="changes" ROWS="5" COLS="45"></TEXTAREA>
		</TD></TR>

		<TR><TD COLSPAN="2" ALIGN="MIDDLE">
			<B><?php echo _XF_SNP_MAKESUREALLCOMPLETE; ?></B>
			<BR>
			<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="<?php echo _XF_G_SUBMIT; ?>">
		</TD></TR>
		</FORM>
		</TABLE>
		<?php

		//snippet_footer();
		include("../../footer.php");
	} else {
		$xoopsForgeErrorHandler->setSystemError('Was the URL or form mangled??');
	}
} else {
  redirect_header(XOOPS_URL."/novelllogin.php?ref=/modules/xfsnippet/addversion.php&type=$type&id=$id", 2, _NOPERM);
  exit;
}

?>
