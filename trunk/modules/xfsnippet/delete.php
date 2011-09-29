<?php
/**
  *
  * SourceForge Code Snippets Repository
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: delete.php,v 1.2 2003/10/02 15:13:13 devsupaul Exp $
  *
  */
include_once ("../../mainfile.php");

$langfile="snippet.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");
/*
	By Tim Perdue, 2000/01/10

	Delete items from packages, package versions, and snippet versions
*/

if ($xoopsUser) {

	if ($type == 'frompackage' && $snippet_version_id && $snippet_package_version_id) {
		/*
			Delete an item from a package
		*/

		//Check to see if they are the creator of this package_version
		$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_package_version")." "
		                         ."WHERE submitted_by='".$xoopsUser->getVar("uid")."' "
														 ."AND snippet_package_version_id='$snippet_package_version_id'");
															
		if (!$result || $xoopsDB->getRowsNum($result) < 1) {
			echo '<H4>'._XF_SNP_ONLYCREATORCANDELETEFROMPACKAGE.'</H4>';
			snippet_footer();
			include("../../footer.php");
			exit;
		} else {

			//Remove the item from the package
			$result = $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_snippet_package_item")." "
			                          ."WHERE snippet_version_id='$snippet_version_id' "
																."AND snippet_package_version_id='$snippet_package_version_id'");
																
			if (!$result) {
				$xoopsForgeErrorHandler->addError('Error - That snippet does not exist in this package.');
			} else {
				$xoopsForgeErrorHandler->addMessage(_XF_SNP_ITEMREMOVEDFROMPACKAGE);
			}
		}

	} else  if ($type == 'snippet' && $snippet_version_id) {
		/*
			Delete a snippet version
		*/

		//find this snippet id and make sure the current user created it
		$result = $xoopsDB->queryF("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_version")." "
		                          ."WHERE snippet_version_id='$snippet_version_id' "
															."AND submitted_by='".$xoopsUser->getVar("uid")."'");
															
		if (!$result || $xoopsDB->getRowsNum($result) < 1) {
				$xoopsForgeErrorHandler->addError('Error - That snippet does not exist.');
		} else {
			$snippet_id = unofficial_getDBResult($result,0,'snippet_id');

			//do the delete
			$result = $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_snippet_version")." "
			                          ."WHERE snippet_version_id='$snippet_version_id' "
																."AND submitted_by='".$xoopsUser->getVar("uid")."'");

			//see if any versions of this snippet are left
			$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_version")." WHERE snippet_id='$snippet_id'");
			if (!$result || $xoopsDB->getRowsNum($result) < 1) {
				//since no version of this snippet exist, delete the main snippet entry,
				//even if this person is not the creator of the original snippet
				$result = $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_snippet")." WHERE snippet_id='$snippet_id'");
			}

			$xoopsForgeErrorHandler->addMessage(_XF_SNP_SNIPPETREMOVED);
		}

	} else  if ($type == 'package' && $snippet_package_version_id) {
		/*
			Delete a package version

		*/

		//make sure they own this version of the package
		$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_package_version")." "
		                         ."WHERE submitted_by='".$xoopsUser->getVar("uid")."' "
														 ."AND snippet_package_version_id='$snippet_package_version_id'");
															
		if (!$result || $xoopsDB->getRowsNum($result) < 1) {
			//they don't own it or it's not found
			$xoopsForgeErrorHandler->addError(_XF_SNP_ONLYCREATORCANDELETEPACKAGE);
		} else {
			$snippet_package_id = unofficial_getDBResult($result,0,'snippet_package_id');

			//do the version delete
			$result = $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_snippet_package_version")." "
			                          ."WHERE submitted_by='".$xoopsUser->uid()."' "
																."AND snippet_package_version_id='$snippet_package_version_id'");

			//delete snippet_package_items
			$result = $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_snippet_package_item")." "
			                          ."WHERE snippet_package_version_id='$snippet_package_version_id'");

			//see if any versions of this package remain
			$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_package_version")." "
			                         ."WHERE snippet_package_id='$snippet_package_id'");
																
			if (!$result || $xoopsDB->getRowsNum($result) < 1) {
				//since no versions of this package remain,
				//delete the main package even if the user didn't create it
				$result = $xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_snippet_package")." WHERE snippet_package_id='$snippet_package_id'");
				redirect_header(XOOPS_URL."/modules/xfsnippet/");
			}
			$xoopsForgeErrorHandler->addMessage(_XF_SNP_PACKAGEREMOVED);
		}
	} else {
		$xoopsForgeErrorHandler->addError('Error<br />Error - mangled URL?');
	}

} else {

  $xoopsForgeErrorHandler->setSystemError(_NOPERM);
}

?>
