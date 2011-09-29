<?php
	/**
	* communities.php
	*
	* @version   $Id: communities.php,v 1.5 2004/02/24 22:20:32 jcox Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "help.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/help/help_utils.php");
	 
	include_once(ICMS_ROOT_PATH."/modules/xfjobs/language/english/modinfo.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/project.php");
	 
	//meta tag information
	$metaTitle = ": "._XF_H_HELP;
	 
	include("../../../header.php");
	 
	$site = $icmsConfig['sitename'];
	 
	help_menu("communities");
	 
	begin_help_content();
	 
	$title = "About Communities";
	$content = "<p>$site provides several communities for your benefit.  A community is " . "an organization of ideas and concepts around a particular discipline, product, " . "vertical market, technology, development methodology, or other similar concept." . "<p>Projects and communities seem very similar(indeed, they are implemented " . "nearly identically and share a lot of code).  The primary difference is that " . "of purpose.  A community is centered around an idea, concept, discipline, " . "technology, market, or other similar topic; whereas a project is centered around " . "the creation, development, and distribution of a software product.";
	themesidebox_help($title, $content, "about");
	echo "<br><br>";
	 
	$title = "How Do I Find A Community?";
	$content = "<p>The easiest way to find a community is to look for the name of the " . "community in the \"Communities\" block that appears on the right side of the " . "main page.  If you know the short name of the community you want, you can type the " . "URL directly into your browser.  " . "The format is \"http://".$_SERVER['SERVER_NAME']."/modules/xfmod/community/?shortname\", " . "replacing \"shortname\" with the short name of the community.";
	themesidebox_help($title, $content, "finding_communities");
	echo "<br><br>";
	 
	$title = "How Do I Navigate A Community?";
	$content = "<p>The community page is the portal page for consuming and managing a " . "community.  Through it one can access all the tools needed to make use of a " . "community." . "<p>The main community page displays such things as information and statistics about " . "a community, a list of users who are maintainers of the community, community " . "news, and a public area with information about forums, faqs, documentation, " . "and other items." . "<p>The menu bar along the top of each project page is useful for navigating to " . "different portions of project management.  Whether you are browsing a project, " . "contributing to a project, or administering a project, you can find a wealth of " . "information by perusing these areas of the project.";
	themesidebox_help($title, $content, "viewing_communities");
	echo "<br><br>";
	 
	$title = "How Do I Create A Community?";
	$content = "<p>You must be a site administrator in order to create a community." . "<p>If you have a suggestion for a community, you are welcome to e-mail that " . "suggestion to the <a href=\"mailto:".$icmsConfig['adminmail']."\">webmaster</a>.";
	themesidebox_help($title, $content, "creating_communities");
	echo "<br><br>";
	 
	$title = "How Do I Contribute To A Community?";
	$content = "<p>There are a couple of ways you can contribute to a community." . "<ul><li>You can be a <strong>Forum Moderator</strong> - an individual who can approve and " . "moderate forum postings" . "<li>You can be a <strong>Documentation Editor</strong> - an individual who can edit and " . "approve document submissions, as well as submit documentation" . "<li>And finally, you can be a <strong>Community Admin</strong> - an individual who has full " . "administrative rights to a community" . "</ul>" . "<p>You must become a maintainer of a community in order to contribute.  When you " . "become a member of a community, you will be given permissions to contribute to " . "a community in defined ways as noted above." . "<p>In order for you to become a maintainer of a community, a community admin must " . "add you to the community.  If you want to contribute to a community, you can " . "always send an email or private message to one of the community admins and " . "request that you be added to the community.  Whether you are actually allowed " . "to become a member is up to the discretion of the community administrators.";
	themesidebox_help($title, $content, "contributing_to_communities");
	echo "<br><br>";
	 
	$title = "How Do I Administer A Community?";
	$content = "<p>First off, you must be an administrator of a community in order to perform " . "administrative tasks.  You become a community administrator one of two ways:" . "<ol><li>By being the creator of a community" . "<li>By being granted community administrator status by another administrator</ol>" . "<p>Clicking on the \""._XF_G_ADMIN."\" link in the community menu will take you " . "to the primary community administration page.  This page has several subsections." . "<ul><li><strong>"._XF_G_ADMIN."</strong> - This is the main project administrative page.  " . "From this page you can perform the following tasks:" . "<ul><li>Change the trove categorization" . "<li>Access administrative panels for all community tools" . "<li>Add maintainers to your community</ul>" . "<li><strong>"._XF_PRJ_USERPERMISSIONS."</strong> - From within this page you will see an " . "overview of all the maintainers of your community and the permissions they have within " . "the community.  Clicking on a member's username brings up the permissions management " . "page for that member.  Using this page, you can make changes to the permissions " . "of any user, including yourself." . "<br>Note that <strong>Community Roles</strong> exist for the purpose of helping you remember " . "what each person does on your community.  Assigning a user to a role does not have " . "any effect on the permissions that user has within your community." . "<li><strong>"._XF_PRJ_EDITPUBLICINFO."</strong> - This page allows you to modify the " . "information that is publicly available about your community.  You can change your " . "community name, community description, or community homepage within this page.  " . "You can also use this page to activate or deactivate certain community tools or " . "features, like forums, surveys, mailing lists, or news." . "<li><strong>"._XF_PRJ_PROJECTHISTORY."</strong> - You can use this page to quickly view a " . "log of the administrative changes that have taken place within your community." . "<li><strong>"._XF_PRJ_POSTJOBS."</strong> - You can use this page to post jobs for your " . "community that will appear in the \""._XF_XFJOBS_NAME."\" section.  This is a good " . "way to advertise to get additional help on your community." . "<li><strong>"._XF_PRJ_EDITJOBS."</strong> - From within this page, you will see a view of " . "all the jobs that are currently active for your community.  By clicking on a job, " . "you will be presented with a page where you can modify any information about a " . "job you posted.</ul>" . "<p>All the tools used within the community also have an administrative console for " . "each tool.  To get to the administrative pages for a tool, click on the name of " . "the tool in the community menu, then click on the \""._XF_G_ADMIN."\" link that " . "appears.";
	themesidebox_help($title, $content, "administering_communities");
	echo "<br><br>";
	 
	$title = "How Do I Use Community Forums?";
	$content = "<p>Forums are available for use within communities to allow a medium of " . "information exchange between participants.  Threaded discussions allow participants " . "to follow topics and resolve issues around those topics of interest." . "<p>Check the <a href=\"forums.php\">Forums</a> help page for more information.";
	themesidebox_help($title, $content, "forums");
	echo "<br><br>";
	 
	$title = "How Do I Use Community FAQs?";
	$content = "<p>Communities use FAQs to address topics that are frequently asked in " . "forums, articles, mailing lists, or other places.  The FAQs should be the first " . "place you look for answers to the questions you have.  If you feel a question " . "or topic should be addressed in the FAQ but is not, you should feel free to " . "contact the <a href=\"mailto:".$icmsConfig['adminmail']."\">webmaster</a>." . "<p>Check the <a href=\"faqs.php\">FAQs</a> help page for more information.";
	themesidebox_help($title, $content, "faqs");
	echo "<br><br>";
	 
	$title = "How Do I Use Community Documentation?";
	$content = "<p>$site offers you the ability to upload and manage all types of documents " . "that pertain to your community.  The document management feature is " . "available for both projects and communities." . "<p>For more detailed information on documents, view the help page for " . "<a href=\"projects.php#documents\">Project Documents</a>.";
	themesidebox_help($title, $content, "documents");
	echo "<br><br>";
	 
	$title = "How Do I Use Community Articles?";
	$content = "<p>An article is a special type of document.  When articles are enabled for " . "a community, a feature article is prominently displayed within each community " . "page.  Articles allow community admins, maintainers, and other community consumers " . "an opportunity to present a formal document that the community can use as a source " . "of information." . "<p>First, in order to display articles, the feature has to be enabled within the " . "community.  This is done by a community administrator.  Go into the administration " . "panel for documents.  This page will indicate whether articles are enabled or not, " . "and provide the option to switch." . "Once articles are enabled, the main community page will display a section entitled " . "\""._XF_COMM_FEATUREDARTICLE."\".  A link will appear to allow users to submit " . "new articles.  These articles will require approval by the community administrator " . "or by a documentation manager with the community." . "<p>Articles can be submitted in any document format; however, articles that are " . "submitted either in plain text or HTML will be automatically displayed within the " . "site itself.";
	themesidebox_help($title, $content, "articles");
	echo "<br><br>";
	 
	$title = "How Do I Use Community Mailing Lists?";
	$content = "<p>Mailing lists are offered for both projects and communities.  Mailing lists " . "allow you to send and receive communications to all subscribers of the list." . "<p>For more detailed information on mailing lists, view the help page for " . "<a href=\"projects.php#mailing_lists\">Project Mailing Lists</a>.";
	themesidebox_help($title, $content, "mailing_lists");
	echo "<br><br>";
	 
	$title = "How Do I Use Community Surveys?";
	$content = "<p>Communities may have surveys that are specific to the community.  Regular " . "users can vote on community surveys, but only community admins can create or " . "modify surveys." . "<p>\"Polls\" is another name for surveys.  Check the " . "<a href=\"surveys.php\">Polls</a> help page for more information.";
	themesidebox_help($title, $content, "surveys");
	echo "<br><br>";
	 
	$title = "How Do I Use Community News?";
	$content = "<p>Community news indicates the latest developments and information for " . "a community." . "<p>Check the <a href=\"news.php\">News</a> help page for more information.";
	themesidebox_help($title, $content, "news");
	echo "<br><br>";
	 
	 
	end_help_content();
	 
	include("../../../footer.php");
?>