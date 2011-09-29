<?php
	/**
	* news.php
	*
	* @version   $Id: news.php,v 1.4 2004/02/24 22:20:32 jcox Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "help.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/help/help_utils.php");
	 
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/project.php");
	include_once(ICMS_ROOT_PATH."/modules/news/language/english/main.php");
	 
	//meta tag information
	$metaTitle = ": "._XF_H_HELP;
	 
	include("../../../header.php");
	 
	$site = $icmsConfig['sitename'];
	 
	help_menu("news");
	 
	begin_help_content();
	 
	$title = "About News";
	$content = "<p>$site News is basically important and timely information about the site, " . "a project, or community.  News may make mention of a new project or community, " . "a new file release, highlight an influential site member, or notify of an " . "important upcoming event." . "<p>There is a news section for the entire site that concerns itself primarily " . "with sitewide news.  In addition, each project and community has its own news " . "that pertains specifically to that project or community.";
	themesidebox_help($title, $content, "about");
	echo "<br><br>\n";
	 
	$title = "How Do I View News?";
	$content = "<p>Sitewide news is immediately viewable when you first navigate to $site.  " . "You can narrow down the visible news topics by selecting topics and subtopics " . "from the dropdown box.  You can also select how many of the most current news " . "items are displayed.  News archives are also available by selecting them from " . "the main menu." . "<p>The latest project news is displayed on the right side of the main project page.  " . "The latest community news is displayed toward the center of the main community page.  " . "For both projects and communities, you can access all the news by clicking on the \"" . _XF_G_NEWS."\" link in the project or community menu.";
	themesidebox_help($title, $content, "viewing_news");
	echo "<br><br>\n";
	 
	$title = "How Do I Submit News?";
	$content = "<p>To submit sitewide news, click on the \""._NW_SUBMITNEWS."\" link in the " . "main menu.  Fill in the form with the context of your news item.  Before you " . "submit the form, notice that you can choose whether to \"Preview\" the submission " . "or \"Post\" the submission.  Preview allows you the opportunity to look at your " . "submission.  You must eventually select Post, however, in order for your submission " . "to be complete." . "<p>When submitting news, you may select a news topic from the \""._NW_TOPIC . "\" selection box.  Topics are defined by the site administrator.  You do not have " . "the capability to start a new topic, but you can suggest one by sending e-mail to " . "the <a href=\"mailto:".$icmsConfig['adminmail']."\">site administrator</a>." . "<p>After you complete your news submission, the submission will be staged for " . "approval by an administrator.  You will be notified when your submission is " . "approved." . "<p>To submit news for projects or communities, click on the \""._XF_NWS_SUBMITNEWS . "\" link in the news section.  Fill in the form with the context of your news item " . "news item and then submit.";
	themesidebox_help($title, $content, "submitting_news");
	echo "<br><br>\n";
	 
	$title = "How Do I Administer News?";
	$content = "<p>As the administrator of a project or community, you can click on the \"" . _XF_G_ADMIN."\" link in the news section of your project or community to get to " . "the news administrative page.  From here you can view all submitted news.  " . "Clicking on any news summary brings up a form where you can edit the submission " . "or even delete it entirely.";
	themesidebox_help($title, $content, "administering_news");
	echo "<br><br>\n";
	 
	end_help_content();
	 
	include("../../../footer.php");
?>