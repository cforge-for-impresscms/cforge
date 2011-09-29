<?php
	/**
	* polls.php
	*
	* @version   $Id: polls.php,v 1.3 2004/01/15 20:24:53 devsupaul Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "help.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/help/help_utils.php");
	 
	include_once(ICMS_ROOT_PATH."/modules/xoopspoll/language/english/blocks.php");
	include_once(ICMS_ROOT_PATH."/modules/xoopspoll/language/english/main.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/survey.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/forum.php");
	 
	//meta tag information
	$metaTitle = ": "._XF_H_HELP;
	 
	include("../../../header.php");
	 
	$site = $icmsConfig['sitename'];
	 
	help_menu("polls");
	 
	begin_help_content();
	 
	$title = "About Polls";
	$content = "Polls allow site, project, or community administrators the opportunity to gain " . "valuable, anonymous feedback from consumers of the site.  Polls are one way that " . "you can provide honest feedback in order to make $site more useful to you." . "<p>Sitewide polls as well as project and community polls are all available.  In " . "projects and communities, polls are usually referred to as surveys.";
	themesidebox_help($title, $content, "about");
	echo "<br><br>\n";
	 
	$title = "How Do I View A Poll?";
	$content = "<p>To view a site poll, click on the \""._MB_POLLS_TITLE1."\" link in the " . "main menu.  You will see a list of all the active polls.  You can click on the \"" . _PL_RESULTS."\" link to view the current results of any sitewide poll." . "<p>While viewing the results of a poll, you will also see comments that have " . "been posted regarding the poll.  You can click on the \""._XF_FRM_POSTCOMMENT . "\" link to post your own comment to the poll." . "<p>To view a survey for a project or community, click on the \""._XF_G_SURVEYS . "\" link in the project or community menu, then click on the survey ID of the " . "survey you are interested in.  If you are an administrator of the project or " . "community, you can navigate through the \""._XF_G_ADMIN."\" link to view the " . "results without voting.";
	themesidebox_help($title, $content, "viewing_polls");
	echo "<br><br>\n";
	 
	$title = "How Do I Vote In A Poll?";
	$content = "<p>Voting in polls is easy.  Simply navigate to the poll as described in " . "<a href=\"polls.php#viewing_polls\">Viewing Polls</a> above, then fill out the " . "form and submit.";
	themesidebox_help($title, $content, "voting_in_polls");
	echo "<br><br>\n";
	 
	$title = "How Do I Create A Poll?";
	$content = "<p>If you are a project or community admin, you can create surveys for your " . "project or community.  Click on the \""._XF_G_SURVEYS."\" link in the project or " . "community menu, then click on the \""._XF_G_ADMIN."\" link to go to the " . "administrative page." . "<p>A survey is simply an organization of survey questions.  Survey questions can " . "be used by more than one survey.  So the first thing you need to do is add all of " . "the survey questions.  To add a survey question:" . "<ol><li>Click the \""._XF_SUR_ADDQUESTIONS."\" link" . "<li>Fill in the context of the question" . "<li>Select the type of response allowed for that question" . "<li>Submit the form to add the question</ol>" . "Repeat this process until all your questions have been added.  Now you are ready " . "to add the survey.  To add a survey:" . "<ol><li>Click the \""._XF_SUR_ADDSURVEYS."\" link" . "<li>Provide a name for your survey" . "<li>Select the question(s) you want to be in your survey, and use the left arrow " . "to move those questions into the \""._XF_SUR_SURVEYQUESTIONS."\" box" . "<li>If there are any questions you want to remove from the survey, select them and " . "use the right arrow to move those questions back into the \"Available Questions\"" . "box" . "<li>Select questions and use the up and down arrows to arrange the order of the " . "questions as you require" . "<li>Be sure you indicate to make the survey active, if desired" . "<li>Click \""._XF_SUR_ADDTHISSURVEY."\" to create the survey</ol>" . "<p>You must be a site administrator to create a sitewide poll.";
	themesidebox_help($title, $content, "creating_polls");
	echo "<br><br>\n";
	 
	$title = "How Do I Administer A Poll?";
	$content = "<p>As a project or community administrator, you have the right to create and " . "modify surveys for your project or community.  You can also select whether to " . "make a survey active or inactive.  You can also view the current results of your " . "surveys." . "<p>These features are availble via the administration page for your surveys.";
	themesidebox_help($title, $content, "administering_polls");
	echo "<br><br>\n";
	 
	$title = "How Private Are Polls?";
	$content = "<p>$site has a privacy policy that is available for all users to read before " . "participating in a poll.  Essentially, the privacy policy ensures participants " . "that neither the poll creators nor the general viewing audience will have " . "knowledge of how any specific participant voted in the poll.  For this reason, " . "information about how a participant voted in a poll is not available.";
	themesidebox_help($title, $content, "poll_privacy");
	echo "<br><br>\n";
	 
	end_help_content();
	 
	include("../../../footer.php");
?>