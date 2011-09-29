<?php
	/**
	* projects.php
	*
	* @version   $Id: projects.php,v 1.8 2004/05/03 17:20:30 devsupaul Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "help.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/help/help_utils.php");
	 
	include_once(ICMS_ROOT_PATH."/modules/xftrove/language/english/modinfo.php");
	include_once(ICMS_ROOT_PATH."/modules/xfnewproject/language/english/modinfo.php");
	include_once(ICMS_ROOT_PATH."/modules/xfjobs/language/english/modinfo.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/project.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/pm.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/docman.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/tracker.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/maillist.php");
	 
	//meta tag information
	$metaTitle = ": "._XF_H_HELP;
	 
	include("../../../header.php");
	 
	$site = $icmsConfig['sitename'];
	 
	help_menu("projects");
	 
	begin_help_content();
	 
	$title = "About Projects";
	$content = "<p>$site provides the ability to host and manage projects." . "A project is centered around the creation of a software project and " . "contains tools that allow you to manage the development and distribution " . "of your software project." . "<p>Projects and communities seem very similar(indeed, they are implemented " . "nearly identically and share a lot of code).  The primary difference is that " . "of purpose.  A community is centered around an idea, concept, discipline, " . "technology, market, or other similar topic; whereas a project is centered around " . "the creation, development, and distribution of a software product.";
	themesidebox_help($title, $content, "about");
	 
	echo "<br><br>";
	 
	$title = "How Do I Find A Project?";
	$content = "<p>There are three ways to access a project page." . "<ol><li><strong>Search</strong> - Using the Search capability of the website, you " . "can search for the project you want.  The search function searches the " . "project database by project name and description.  The results of your " . "search will include a link that will take you to the project page." . "<li><strong>"._XF_XFTROVE_NAME."</strong> - This feature presents a tree or list " . "view of all the projects in the project database.  You can use this to " . "navigate and locate the project you want." . "<li>If you know the short name of the project you want, you can type the " . "URL directly into your browser.  " . "The format is \"http://".$_SERVER['SERVER_NAME']."/modules/xfmod/project/?shortname\", " . "replacing \"shortname\" with the short name of the project." . "</ol>";
	themesidebox_help($title, $content, "finding_projects");
	 
	echo "<br><br>";
	 
	$title = "How Do I Navigate A Project?";
	$content = "<p>The project page is the portal for the management of a project.  " . "Through it one can access all the tools needed to manage a project." . "<p>The main project page displays such things as information and statistics about " . "a project, a list of users who are members of the project, current file releases " . "for the project, project news, and a public area with information about bugs, " . "enhancement requests, documentation, and other items." . "<p>The menu bar along the top of each project page is useful for navigating to " . "different portions of project management.  Whether you are browsing a project, " . "contributing to a project, or administering a project, you can find a wealth of " . "information by perusing these areas of the project.";
	themesidebox_help($title, $content, "viewing_projects");
	 
	echo "<br><br>";
	 
	$title = "How Do I Create A Project?";
	$content = "<p>To create a project, start by clicking on the \""._XF_XFNEWPROJECT_NAME . "\" link in the Main Menu.  You will be presented with the first of four pages " . "that you navigate through to create your project." . "<p>The first two pages present information about the project and general " . "guidelines for use.  These outline the primary services provided by $site and " . "describe some of the terms of service in lay terms." . "<br><br>The third page presents the terms of service document.  " . "This is a legal document; you must agree to the terms of service in order " . "to create a project.  Questions about the terms of service document should be " . "directed to the <a href=\"mailto:".$icmsConfig['adminmail']."\">site " . "administrator</a>." . "<br><br>The fourth page allows you to enter information about your project - project name, " . "license, description, etc.  You need to fill out this form in order to complete " . "your project submission." . "<br>It is important to take your time and fill out your project submisison " . "accurately and completely.  The following fields are especially important:" . "<ul><li><strong>Project Purpose</strong> - The project purpose will primarily be used by the Forge administrators to " . "make the determinition whether to approve or reject your project." . "<li><strong>License</strong> - You have your choice of several different types of software " . "licenses, almost all of which are approved by the Open Source Initiative(OSI).  " . "You are strongly encouraged to select a license that is OSI approved, since an " . "OSI approved license ensures that the content covered by the license conforms to " . "the Open Source Definition(meaning that it has the essential characteristics of " . "open source software).  We encourage you to visit the <a href=\"http://www.opensource.org/\">" . "Open Source Initiative</a> website to learn about open source software and the " . "license(s) you are considering before you make a selection." . "<br>At your option, you may submit your own license terms for consideration with " . "your project.  Please consider, however, that $site is an outward demonstration by " . "Novell of its commitment to the advancement of the open source community.  It is " . "likely that a project submitted with a non-OSI approved license will be rejected." . "<br>It is possible for you to change the license of your project in the future - " . "after all, it is <em>your</em> project.  $site allows you to select a license at " . "project creation time, and again when you " . "<a href=\"".ICMS_URL."/modules/xfmod/help/software_map.php#categorizing_projects\">" . "categorize your project</a>, for ease of " . "categorizing and searching projects.  Officially, the license of your project is " . "the license you distribute with your project.  Keep in mind, though, that $site " . "reserves the right to disable your project if you change to a license that is not " . "an approved license.</ul>" . "<p>Once you submit the final form for your project, the information about your " . "new project is submitted to the site administrators for approval.  You will be " . "notified when your project is approved(or rejected).  You should keep the email " . "messages you receive for future reference, as they contain important information " . "about managing your project." . "<p>When your project is created, you should then go to your project page and " . "perform a few administrative tasks, such as categorizing your project in the " . "trove and changing mailing list passwords.  The information that describes how " . "to do this is contained in the e-mail messages you received when your project " . "was approved.  You are then free to add " . "users to your project; create news, trackers, or forums; upload code to your " . "CVS repository; create releases, or anything else!";
	themesidebox_help($title, $content, "creating_projects");
	 
	echo "<br><br>";
	 
	$title = "How Do I Contribute To A Project?";
	$content = "<p>There are many ways you can contribute to a project." . "<ul><li>You can be a <strong>Release Technician</strong> - an individual who is in charge " . "of creating file releases" . "<li>You can be a <strong>Tracker Manager</strong> - an individual who can create and " . "manage the trackers used by the project" . "<li>You can be a <strong>Task Manager Admin</strong> - an individual who can create " . "and manage subprojects and tasks" . "<li>You can be a <strong>Task Manager Tech</strong> - an individual to whom tasks can " . "be assigned" . "<li>You can be a <strong>Forum Moderator</strong> - an individual who can approve and " . "moderate forum postings" . "<li>You can be a <strong>Documentation Editor</strong> - an individual who can edit and " . "approve document submissions, as well as submit documentation" . "<li>You can be a <strong>Sample Code Editor</strong> - an individual who can edit and " . "approve sample code submissions, as well as submit sample code" . "<li>You can be a <strong>Tracker Admin</strong> - an individual who can administer trackers, " . "like Bugs or Enhancement Requests, including approving or submitting tracker items " . "and assigning those items to others" . "<li>You can be a <strong>Tracker Tech</strong> - an individual to whom tracker items, like " . "Bugs or Enhancement Requests, can be assigned" . "<li>And finally, you can be a <strong>Project Admin</strong> - an individual who has full " . "administrative rights to a project" . "</ul>" . "<p>You must become a member of a project in order to contribute.  When you become " . "a member of a project, you will be given permissions to contribute to a project " . "in defined ways as noted above.  You will also have the ability to add to or " . "modify the source code base." . "<p>In order for you to become a member of a project, a project admin must add you " . "to the project.  If you want to contribute to a project, you can always send an " . "email or private message to one of the project admins and request that you be added " . "to the project.  Whether you are actually allowed to become a member is up to the " . "discretion of the project administrators." . "You can also check the \""._XF_XFJOBS_NAME."\" section by clicking on the link in " . "Main Menu.  Projects that are currently looking for contributors may advertise for " . "help in this section.  You can apply for posted jobs and possibly be selected as a " . "project member in this way.";
	themesidebox_help($title, $content, "contributing_to_projects");
	 
	echo "<br><br>";
	 
	$title = "How Do I Administer My Project?";
	$content = "<p>First off, you must be an administrator of a project in order to perform " . "administrative tasks.  You become a project administrator one of two ways:" . "<ol><li>By being the creator of a project" . "<li>By being granted project administrator status by another administrator</ol>" . "<p>Clicking on the \""._XF_G_ADMIN."\" link in the project menu will take you " . "to the primary project administration page.  This page has several subsections." . "<ul><li><strong>"._XF_G_ADMIN."</strong> - This is the main project administrative page.  " . "From this page you can perform the following tasks:" . "<ul><li><a href=\"".ICMS_URL."/modules/xfmod/help/software_map.php#categorizing_projects\">" . "Change the trove categorization</a>" . "<li><a href=\"projects.php#cvs\">Enable and disable CVS access controls</a>" . "<li>Access administrative panels for all project tools" . "<li>Add users to your project - simply enter their username and click \""._XF_PRJ_ADDUSER."\"</ul>" . "<li><strong>"._XF_PRJ_USERPERMISSIONS."</strong> - From within this page you will see an " . "overview of all the members of your project and the permissions they have within " . "the project.  Clicking on a member's username brings up the permissions management " . "page for that member.  Using this page, you can make changes to the permissions " . "of any user, including yourself." . "<br>Note that <strong>Project Roles</strong> exist for the purpose of helping you remember " . "what each person does on your project.  Assigning a user to a role does not have " . "any effect on the permissions that user has within your project." . "<li><strong>"._XF_PRJ_EDITPUBLICINFO."</strong> - This page allows you to modify the " . "information that is publicly available about your project.  You can change your " . "project name, project description, or project homepage within this page.  " . "You can also use this page to activate or deactivate certain project tools or " . "features, like forums, surveys, mailing lists, or tasks." . "<li><strong>"._XF_PRJ_PROJECTHISTORY."</strong> - You can use this page to quickly view a " . "log of the administrative changes that have taken place within your project." . "<li><strong>"._XF_PRJ_EDITRELEASEFILES."</strong> - It is within this page that you create " . "and manage your project releases.  You can create and " . "manage packages, releases for each package, and files for each release here.  " . "View the <a href=\"projects.php#downloading_projects\">Downloading Projects</a> section for more " . "detailed instructions on how to manage your file modules." . "<li><strong>"._XF_PRJ_POSTJOBS."</strong> - You can use this page to post jobs for your " . "project that will appear in the \""._XF_XFJOBS_NAME."\" section.  This is a good " . "way to advertise to get additional help on your project." . "<li><strong>"._XF_PRJ_EDITJOBS."</strong> - From within this page, you will see a view of " . "all the jobs that are currently active for your project.  By clicking on a job, " . "you will be presented with a page where you can modify any information about a " . "job you posted.</ul>" . "<p>All the tools used within the project also have an administrative console for " . "each tool.  To get to the administrative pages for a tool, click on the name of " . "the tool in the project menu, then click on the \""._XF_G_ADMIN."\" link that " . "appears.";
	themesidebox_help($title, $content, "administering_projects");
	 
	echo "<br><br>";
	 
	$title = "How Do I Download A Project Release?";
	$content = "<p>Within a project, the files that you can download are organized as follows:" . "<ul><li>A <strong>Project</strong> can have zero or more <strong>Packages</strong>." . "<li>A <strong>Package</strong> can have zero or more <strong>Releases</strong>." . "<li>A <strong>Release</strong> can have zero or more <strong>Files</strong>.</ul>" . "<p>This concept is perhaps best illustrated with an example." . "<p>Suppose you administer a project that is developing a simple GUI LDAP " . "browsing client, and suppose you have a compiled executable of your project " . "for Linux platforms.  This is a perfect candidate for release.  " . "In order to release your executable, you would first decide what package the " . "executable belongs in.  You may have the packages \"Windows\", \"Linux\", " . "\"Netware\", and \"Macintosh\", so the \"Linux\" package would be the obviously " . "correct choice." . "<p>Next you need a release within the package.  Perhaps the release itself is " . "\"RedHatLinux8.0_v1.1\", to denote the 1.1 release of the software as a compiled " . "executable on Red Hat Linux 8.0." . "<p>Finally, you would create the file within the release by simply uploading the " . "file and assigining it to the release." . "<p>The existence of packages, releases, and files allows you complete control over " . "the organization and management of your file releases." . "<p>If you are the consumer of a project, it is even easier.  On the main project page, " . "the files available for download are listed underneath the heading \""._XF_PRJ_LATESTFILERELEASES . "\".  Simply click on the release you are interested in and you will be taken to " . "a page where you can click on the file(s) you want to download.";
	themesidebox_help($title, $content, "downloading_projects");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use Project Forums?";
	$content = "<p>Forums are available for use within projects to allow a medium of " . "information exchange between participants.  Threaded discussions allow participants " . "to follow topics and resolve issues around those topics of interest." . "<p>Check the <a href=\"forums.php\">Forums</a> help page for more information.";
	themesidebox_help($title, $content, "forums");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use Trackers?";
	$content = "<p>Trackers allow you to create categories and issues within categories for " . "tracking purposes.  Trackers offer similar functionality to that of tasks; however, " . "tasks are primarily for monitoring the planned path of development for your " . "project, whereas trackers are primarily for monitoring other important issues that " . "arise during the development of a project." . "<p>Using the tracking system, you can manage both the categories within which " . "your issues will be placed, and the issues themselves." . "<p>Select \""._XF_G_TRACKERS."\" from the project menu and you will be taken to " . "the trackers page.  You can select here from the available trackers.  Once you " . "select a  tracker, you can:" . "<ul><li><strong>Submit New Tracker Items</strong> by clicking on the \""._XF_TRK_ATHSUBMITNEW . "\" link and filling out the form" . "<li><strong>Browse Open Items</strong> by defining the desired search criteria and then " . "clicking the \""._XF_G_BROWSE."\" button" . "<li><strong>Modify an Item</strong> by selecting the item from the search results and then " . "making changes to the item.  Some of the changes you can make include:" . "<ul><li>Assigning the item to a project member" . "<li>Changing the item priority" . "<li>Changing the item status</ul></ul>" . "To administer this page, click on the \""._XF_G_ADMIN."\" link.  Here you can:" . "<ul><li><strong>Create a new tracker</strong> by filling out and submitting the form at the " . "bottom of the tracker administration page" . "<li><strong>Modify settings on existing trackers</strong> by clicking on the tracker, " . "selecting the appropriate category, and submitting the changes" . "<li><strong>Disable trackers</strong> by clicking on the tracker, selecting the \"" . _XF_TRK_UPDATEPREFERENCES."\" link and deselecting the \"" . _XF_TRK_PUBLICLYAVAILABLE."\" setting</ul>" . "<p>You will notice that the title of each of your default trackers appears in the " . "project menu.  You can get to a specific tracker by selecting it from the project " . "menu, from the main project page, or by clicking on the \""._XF_G_TRACKERS."\" link " . "and then selecting the tracker from this page." . "<p>Once you have selected a tracker, you will be presented with a page that is " . "specific to that tracker.  You can use this page to submit new items to the " . "tracker, such as a bug report.  You also use this page administratively to assign " . "items to members of your project, or to monitor the progress of existing items.";
	themesidebox_help($title, $content, "trackers");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use Tasks?";
	$content = "<p>Tasks provide a means by which a project administrator can assign and " . "delegate responsibilities within a project to individuals.  When using tasks, " . "you first create subprojects.  For example, your software project may have tasks " . "such as \"framework,\" \"user interface,\", \"APIs,\", \"documentation,\" etc.  " . "Once you create subprojects, you then create tasks within each subproject.  You " . "can then assign tasks to members of your project and track the progress of your " . "project." . "<p>To create a subproject, you first click on the \""._XF_PM_TASKS."\" link in " . "the project menu, then click on \""._XF_G_ADMIN."\", \""._XF_PM_SUBPROJECTLIST . "\", and \""._XF_PM_ADDASUBPROJECT."\".  Fill in the simple form and submit to " . "create a subproject." . "<p>To manage a task, go to the tasks window by clicking on the \""._XF_PM_TASKS . "\" link in the project menu, then select a subproject from the list to add the " . "tasks to.  From this point, you can create new tasks, manage existing tasks, " . "or view tasks that are assigned to you within the project." . "<p>Once you have created and assigned tasks, the assignees can keep you informed " . "on their progress by updating the information on their task, including current " . "information or issues, setting or updating estimated completion dates, and " . "maintaining information regarding current state of completion.  Each task also " . "includes dependency information, so you can set up dependencies within your task " . "structure to organize your effort and know what needs to be done and when.";
	themesidebox_help($title, $content, "tasks");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use Project Documentation?";
	$content = "<p>$site offers you the ability to upload and manage all types of documents " . "that pertain to your project or community.  The document management feature is " . "available for both projects and communities." . "<p>To access the documentation page, click on the \""._XF_G_DOCS."\" link " . "in the project or community menu.  You will be presented with all the publicly " . "available documents.  By clicking on the \""._XF_G_ADMIN."\" link, you will go " . "to the document management page where you can submit documentation, add or " . "modify the available document categories, edit information about previously " . "submitted documentation, or set the state of a document.  For example, documents " . "with the state of \"active\" are publicly viewable, whereas documents with the " . "state of \"pending\" are those which have been submitted but not yet approved." . "<p>To submit a document, click on the \""._XF_DOC_SUBMITNEWDOC."\" link in the " . "document management page.  Documents are managed as uploaded files, so all you " . "need do is upload the document and it will be stored and managed on the server.";
	themesidebox_help($title, $content, "documents");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use Mailing Lists?";
	$content = "<p>Mailing lists are offered for both projects and communities.  A set of " . "mailing lists are automatically created whenever a project " . "or community is approved.  You do not have the option of creating additional " . "mailing lists or of deleting existing lists, but you can opt not to use the mailing lists at all." . "<p>When you send e-mail to a mailing list, all the subscribers of the list " . "receive a copy of the e-mail you send.  This is a means by which interested " . "parties can remain informed on a project or community, and collaborate with " . "other subscribers by sending and receiving mail on the list.  To send e-mail to " . "the mailing list, you simply create an e-mail addressed to list-name@" . $_SERVER['SERVER_NAME'].", where \"list-name\" is the name of the list." . "<p>Anybody may subscribe to a mailing list.  To do so, simply navigate to the " . "mailing list page for the project or community in question, and select the \"" . _XF_ML_SUBSCRIBE."\" link.  Then fill out the form and submit.  You will receive " . "an e-mail notification of your subscription request that will tell you what to " . "do next." . "<p>$site uses the GNU Mailman mailing list software, including their subscription " . "and list administration pages, to manage mailing lists.  As the administrator of " . "a project, you have the ability to manage traffic, volume, subscribers, and " . "other aspects of your mailing lists.  For details on how to use Mailman, check " . "their <a href=\"http://www.list.org/\">website</a>.";
	themesidebox_help($title, $content, "mailing_lists");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use Surveys?";
	$content = "<p>You can create your own surveys for your project and use them to gain " . "insight from consumers of your project.  Surveys provide this information in a " . "confidential manner, so that the identity of the participants is not revealed." . "<p>\"Polls\" is another name for surveys.  Check the " . "<a href=\"polls.php\">Polls</a> help page for more information.";
	themesidebox_help($title, $content, "surveys");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use News?";
	$content = "<p>You can create your own news items for your project.  This is a good way " . "to provide summary information about the current state of your project." . "<p>Check the <a href=\"news.php\">News</a> help page for more information.";
	themesidebox_help($title, $content, "news");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use CVS?";
	$content = "<p>CVS is the source code repository control system used by $site.  When " . "you create a project, you are allowed the option of whether you wish to make use " . "of CVS to manage the source code for your project." . "<p>If you want others to be able to contribute to and enhance your project, you " . "should strongly consider using our CVS server to host your project.  Access to " . "the CVS server is limited only to individuals who are members of your project." . "<p>As an option, you can select to enable anonymous CVS access to your source " . "code.  If you enable anonymous CVS access, any user of the site, even users who " . "are not logged in, will be able to view and download a snapshot of your source " . "code.  A user must still be a member of your project in order to make changes " . "to your source code.<br>" . "To enable anonymous CVS access, click the \""._XF_G_ADMIN."\" link in your project " . "page, then make sure the \"Anonymous CVS Access\" property is selected underneath " . "the \"CVS Administration\" heading." . "<p>You can trust the integrity of your source code.  Our CVS server is backed up " . "daily to prevent the loss of your source code.  In addition, the CVS server " . "itself utilizes eDirectory to enforce access rights to projects and source code, " . "but does not allow shell logins to the server itself.  In other words, you can " . "trust that your source code will only be available to the people to whom you wish " . "it available, and only under the terms you provide." . "<p>The CVS link in the project menu takes you to the CVS management page.  Within " . "this page you are shown the information that you need in order to begin managing " . "your source code using our CVS server.  If you have allowed anonymous access to " . "your repository, this page also includes instructions for anonymous download, and " . "a link to view the contents of your repository online." . "<p>There are many CVS clients available that can consume a CVS resource like the " . "one provided at $site.  For details on how to set up your specific client, refer " . "to the documentation provided with the client software." . "<p>For complete documentation on CVS, read the " . "<a href=\"http://www.cvshome.org/docs/manual/\">CVS Manual</a> at " . "<a href=\"http://www.cvshome.org/\">cvshome.org</a>.";
	themesidebox_help($title, $content, "cvs");
	 
	echo "<br><br>";
	 
	$title = "How Do I Use The Forge Red Carpet Service";
	$content = "<p>The Red Carpet clients are available for download from ximian's website,
		<a href='http://www.ximian.com/products/redcarpet/'>http://www.ximian.com/products/redcarpet/</a>.
		After installing a Red Carpet client there are three basic steps that you must perform before you can install RPMs from
		the Novell Forge Red Carpet service.
		<ul>
		<li>Add the Novell Forge Red Carpet service.</li>
		<li>Activate against the project you are interested in by using the project activation key.</li>
		<li>Subscribe to the channel from which you will get files.</li>
		</ul>
		The Forge Red Carpet service is located at https://forgerce.novell.com/data.  The projects
		activation key will be the shortname of the project with '-key' appended to the end(ie. 'forge-key'
		is the activation key for the forge project).  Each project will have a channel with the same name
		as the project's shortname.</p>" ."<p><strong>GUI Client Example:</strong><br>If you were using the red-carpet GUI client and wanted to install files from the forge project you would do the following:
		<ul>
		<li>On the 'Edit' menu select the 'Services' menu item.
		<li>Click the 'Add Services' button.
		<li>Type in the service url(https://forgerce.novell.com/data), click the 'OK' button and close the 'Edit Services' window.
		<li>On the 'File' menu select the 'Activate' menu item.
		<li>Select the Novell Forge service from the drop down menu.
		<li>Type in your email address.
		<li>Type in the activation key('forge-key' in our case) and click the 'Activate' button.
		<li>Click on the 'Channels' button.
		<li>Find the channel you want('forge' in our case), click the check box to select that channel then close the window.
		</ul>Any software published to Red Carpet by the project will now be visable in the 'Available Software' tab.  If you already have the
		software installed and there is a newer file published, you will find it in the 'Updates' tab.
		</p>" ."<p><strong>Command Line Example</strong><br>If you were using the rug command line client and wanted to install files from teh forge project you would do the following:
		<ul>
		<li>rug service-add https://forgerce.novell.com/data
		<li>rug activate --service=https://forgerce.novell.com/data forge-key <i>youremail@yourdomain.com</i>
		<li>rug subscribe forge
		</ul>Using 'rug install' and 'rug update' you can now install/update any files published to Red Carpet by the project
		</p>";
	themesidebox_help($title, $content, "red_carpet");
	 
	echo "<br><br>";
	 
	 
	end_help_content();
	 
	 
	include("../../../footer.php");
?>