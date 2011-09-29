<?php
	/**
	* forums.php
	*
	* @version   $Id: forums.php,v 1.7 2004/01/29 20:43:22 jcox Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "help.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/help/help_utils.php");
	 
	include_once(ICMS_ROOT_PATH."/modules/newbb/language/english/blocks.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/project.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/forum.php");
	 
	//meta tag information
	$metaTitle = ": "._XF_H_HELP;
	 
	include("../../../header.php");
	 
	$site = $icmsConfig['sitename'];
	 
	help_menu("forums");
	 
	begin_help_content();
	 
	$title = "about forums";
	$content = "<p>Forums are useful for providing threaded discussions around a specific " . "topic.  $site provides two different types of forums." . "<ul><li><strong>Project Forums</strong> - Forums that pertain to a specific project" . "<li><strong>Community Forums</strong> - Forums that pertain to a specific community</ul>" . "<p>All forums are public.  Anyone may post any information.  Novell is not responsible" . " for any content posted to these forums.  If you find something offensive or irrelevant" . " the project or community admin has the ability to remove the content in question.";
	themesidebox_help($title, $content, "about");
	echo "<br><br>\n";
	 
	$title = "How Do I View Forums?";
	$content = "<ul><li>To view a <strong>project</strong> forum, click on either the \""._XF_G_FORUMS."\" link " . "in the project menu, or the \""._XF_PRJ_PUBLICFORUMS."\" link in the project " . "public area.  Click on the forum of interest to view the postings in the forum." . "<li>To view a <strong>community</strong> forum, click on either the \""._XF_G_FORUMS."\" link " . "in the community menu, or the \""._XF_COMM_PUBLICFORUMS."\" link in the community " . "public area.  Click on the forum of interest to view the postings in the forum.</ul>" . "<p>Once you locate a forum you are interested in, you have three options for viewing" . " the content of the forum.  You may use the web interface to the forum by clicking on" . " <strong>[HTTP]</strong>.  You may also use a news client to access the forum.  All forge forums" . " are hosted at <strong>forums.novell.com</strong>.  If you click on <strong>[NNTP]</strong> your default news" . " reader will open and display the forum that the link corrisponds to.  We also offer an RSS feed." . " You can access the xml document by clicking on the <img src='".ICMS_URL."/modules/xfmod/newsportal/img/xml.gif' width='36 height='14' alt='RSS Feed'> image." . " It is also possible to recieve new posts via email if the forum admin has associated a mailing list with the forum" . " you are interested in viewing.";
	themesidebox_help($title, $content, "viewing_forums");
	echo "<br><br>\n";
	 
	$title = "How do I post to forums?";
	$content = "<p>You may post to the forum either through you news reader or through the web" . " interface.  Short description for using the web interface follow." . "<ul><li>To post a <strong>new thread</strong>, first browse to the forum to which you wish " . "to post.  Near the bottom of the page will be a form with the label \"" . _XF_FRM_STARTNEWTHREAD."\".  Fill in the form and click on the \""._XF_FRM_POSTCOMMENT . "\" button to create a new thread." . "<li>To post to an <strong>existing thread</strong>, first browse to the forum, and then to " . "the thread that you want to respond to.  Near the bottom of the page you will see " . "a form with the label \""._XF_FRM_POSTFOLLOWUP."\".  Fill in the form and click " . "on the \""._XF_FRM_POSTCOMMENT."\" button to post a follow-up message to the thread." . "</ul>If the forum is associated with a mailing list you could also post to the forum by" . " sending mail to the proper mailing list.";
	themesidebox_help($title, $content, "posting_to_forums");
	echo "<br><br>\n";
	 
	$title = "How do I create forums?";
	$content = "<p>Project or community forums can only be created by an administrator of " . "that project or community.</p>" . "<p>To create a project or community forum, first click on the \""._XF_G_FORUMS . "\" link in the project or community menu, then click on the \""._XF_G_ADMIN . "\" link to get to the administrative panel.  Fill in a Descriptive name " . "and a unique shortname for your forum.  Then click on \"Add Forum\"." . "This will submit a request to have a new forum added.  Your request will be granted " . "within one hour.</p>";
	themesidebox_help($title, $content, "creating_forums");
	echo "<br><br>\n";
	 
	$title = "How do I link a forum and a mailing list together?";
	$content = "<p>Log in to the forge site.  Select the \"Mailing Lists\" tab for your project." . " Select the \"Admin\" link for the list you are interested in linking to your forum." . " Enter your list password when prompted.(By default your password is the name of the list with " . "\"-passwd\" appended to the end.)  After logging in, click on the \"Mail-News and News-Mail gateways\"" . " configuration category.  The internet address of the news server is <strong>forums.novell.com</strong>." . " The usenet group is <strong>novell.forge.<i>[project short name]</i>.<i>[forum short name]</i></strong>." . " Then select yes or no to the next three questions on the page to allow posts to pass from the list to the forum" . " and vice versa, depending on your desires.</p>";
	themesidebox_help($title, $content, "linking_forums_and_mailing_lists");
	echo "<br><br>\n";
	 
	end_help_content();
	 
	include("../../../footer.php");
?>