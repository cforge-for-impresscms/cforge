<?php 
/**
 * code_snippets.php
 *
 * @version   $Id: code_snippets.php,v 1.3 2004/01/15 20:24:53 devsupaul Exp $
 */
include_once("../../../mainfile.php");

$langfile="help.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/help/help_utils.php");

include_once(XOOPS_ROOT_PATH."/modules/xfsnippet/language/english/modinfo.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/language/english/snippet.php");

//meta tag information
$metaTitle=": "._XF_H_HELP;

include ("../../../header.php");

$site = $xoopsConfig['sitename'];

help_menu("code snippets");

begin_help_content();

$title="About Code Snippets";
$content="<p>Code Snippets are essentially code samples.  A code snippet is a piece of "
	. "code that is too small to be its own project, but has intrinsic value of itself "
	. "because it demonstrates how to perform a specific task."
	. "The \""._XF_XFSNIPPET_NAME."\" section contains both code snippets and snippet "
	. "packages.  Packages contain one or more snippets, presumably related.";
themesidebox_help($title,$content,"about");
echo "<br><br>\n";

$title="How Do I View Code Snippets?";
$content="<p>To get to the Code Snippets section, click on the \""._XF_XFSNIPPET_NAME
	. "\" link in the main menu.  There are a couple of ways you can locate snippets "
	. "of interest."
	. "<ul><li>You may search for snippets by keyword, language, category, type, or "
	. "license"
	. "<li>You may browse the snippet libraries by language or category</ul>"
	. "You will see both snippet packages and snippets that match your selection "
	. "criteria.  You can view a snippet by simply clicking on the name of the snippet "
	. "you are interested in.  You can view snippets in a package by first clicking on "
	. "the name of the package, then selecting the name of the snippet in the package "
	. "detail window.";
themesidebox_help($title,$content,"viewing_code_snippets");
echo "<br><br>\n";

$title="How Do I Create A Code Snippet?";
$content="<p>It is easy to create a new code snippet.  Begin by clicking on the \""
	. _XF_SNP_SUBMITNEWSNIPPET."\" link in the \""._XF_XFSNIPPET_NAME."\" section.  "
	. "You will see a form that you fill in to create the snippet.  If you fill in and "
	. "submit this form your snippet will be created."
	. "<p>When you create a snippet, you also license the future use of that snippet "
	. "to other people that may want to consume it.  You may choose from any of the "
	. "licenses provided in the \""._XF_SNP_LICENSE."\" selection box.<br>"
	. "We encourage you to carefully consider the license that you choose for your "
	. "snippet.  For further information on these licenses, including the legal terms "
	. "for each license, visit the website for <a href=\"http://www.opensource.org/\">"
	. "Open Source Initiative</a>."
	. "<p>Another way to create a snippet is to create a new version of an existing "
	. "snippet.  You can create a new version of your own snippets, or of other people's "
	. "snippets.  First, bring up the snippet detail page for the snippet in question.  "
	. "Click on the \""._XF_SNP_SUBMITNEWSNIPPETVERSION."\" link, fill in the form, and "
	. "submit.";
themesidebox_help($title,$content,"creating_code_snippets");
echo "<br><br>\n";

$title="How Do I Manage Code Snippets?";
$content="One way to manage snippets is to create a snippet package of related snippets.  "
	. "You create a package by first clicking the \""._XF_SNP_CREATEAPACKAGE."\" link, "
	. "then completing the form.  You will be presented with a list of all the snippets "
	. "you have created.  Select the snippets and submit to add these snippets to your "
	. "package."
	. "<p>Note that it is not possible to edit snippets once they are created.  In order "
	. "to maintain a history of changes made to a snippet, it is essential to create a "
	. "new version of a snippet if you want to make changes.  Usually, users will view "
	. "only the most current version of a snippet."
	. "<p>Also note that it is not possible to add snippets to a package if you are not "
	. "the creator of the snippets.  This is done to preserve the integrity of each "
	. "snippet, so that a rightful owner of each version is identifiable."
	. "<p>However, it is possible to create new versions of other users' snippets.  "
	. "You will own the new version that you create, and then you can add the new "
	. "version to your packages if you desire.  To create a new version of a code "
	. "snippet or code snippet package, first browse to the page for that snippet or "
	. "package.  Click on the \""._XF_SNP_SUBMITNEWVERSION."\" link, then fill in the "
	. "form to create a new version.";
themesidebox_help($title,$content,"managing_code_snippets");
echo "<br><br>\n";

$title="How Do I Submit Suggestions For Code Snippet Categorization?";
$content="<p>From the main snippet library page, you can suggest new languages, categories, "
	. "or script types that, if approved, will be used to better categorized code snippet "
	. "submissions.  Underneath the selection box for each of these items you will see a "
	. "link.  Clicking on this link will take you to a form where you can submit a support "
	. "request to $site administration.  Use this form to fill in the information to "
	. "describe your suggestion.";
themesidebox_help($title,$content,"snippet_suggestions");
echo "<br><br>\n";

end_help_content();

include("../../../footer.php");
?>