<?php
	/**
	* jobs.php
	*
	* @version   $Id: jobs.php,v 1.3 2004/01/15 20:24:53 devsupaul Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "help.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/help/help_utils.php");
	 
	include_once(ICMS_ROOT_PATH."/modules/xfjobs/language/english/modinfo.php");
	include_once(ICMS_ROOT_PATH."/modules/xfaccount/language/english/modinfo.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/my.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/people.php");
	 
	//meta tag information
	$metaTitle = ": "._XF_H_HELP;
	 
	include("../../../header.php");
	 
	$site = $icmsConfig['sitename'];
	 
	help_menu("jobs");
	 
	begin_help_content();
	 
	$title = "About Jobs";
	$content = "Jobs allow projects or communities looking for help with a specific area " . "to advertise for help, and they allow users wishing to contribute a means to " . "find a project or community to which they can contribute." . "<p>Jobs are helpful to projects and communities because they provide a medium for " . "advertising positions that are available.  Project or community administrators " . "can post jobs to a sitewide location where they are available for viewing by all " . "users.  Users can then apply for the jobs if they are interested." . "<p>Jobs are helpful to users because they provide an easy means for getting " . "involved.  Users can search for open positions that they are interested in and " . "apply for those positions.  Users can upload their resume and create a skills " . "profile to describe their ability to contribute to interested parties.";
	themesidebox_help($title, $content, "about");
	echo "<br><br>\n";
	 
	$title = "How Do I View Job Postings?";
	$content = "<p>To view open job postings, click on the \""._XF_XFJOBS_NAME."\" link in the " . "main menu.  You will see a categorized list of all the open positions by role, " . "along with a list of the latest job postings.  Clicking on a job title will " . "display a page with detailed information about the job.";
	themesidebox_help($title, $content, "viewing_jobs");
	echo "<br><br>\n";
	 
	$title = "How Do I Manage My Skills Profile?";
	$content = "<p>If you want to contribute to a project or a community, it is a good idea " . "to set up your skills profile.  When you apply for a job, it is likely that " . "the project or community administrator will look at your skills profile to " . "make a determination about your ability to contribute." . "<p>Your skills profile is maintained with your personal page.  Click on the \"" . _XF_XFACCOUNT_NAME."\" link in the main menu to get to your personal page, then " . "click on the \""._XF_MY_SKILLPROFILE."\" link to go to your skills profile page." . "<p>Within the skills profile page, you will see a text area where you can paste " . "a copy of your resume or a description of your experience.  Below that is the " . "skills list.  If you are updating your skills list, you may already have skills " . "in the list; otherwise, the list will be empty." . "<p>Under the \""._XF_PEO_ADDANEWSKILL."\" heading, you will see a form where you " . "can add your skills, as follows:" . "<ol><li>From the leftmost dropdown box, select the skill" . "<li>From the centermost dropdown box, select your skill level" . "<li>From the rightmost dropdown box, select the amount of experience you have" . "<li>Click the \""._XF_PEO_ADDSKILL."\" button to add the skill</ol>" . "Repeat this process to add all the skills you have." . "<p>Once you have added a skill, you can modify the details of the skill by " . "changing the values in the dropdown box for that skill and then clicking on the \"" . _XF_G_UPDATE."\" button to the right of that skill.  You may also delete a skill " . "by clicking on the \""._XF_G_DELETE."\" button to the right of the skill." . "<p>You are free at any time to modify your skills profile and keep it current.";
	themesidebox_help($title, $content, "managing_your_skills_profile");
	echo "<br><br>\n";
	 
	$title = "How Do I Apply For A Job?";
	$content = "<p>Once you have set up your skills profile, you are ready to apply for any " . "jobs you may be interested in." . "<p>First, locate the job you want to apply for by navigating to the job as " . "described in <a href=\"jobs.php#viewing_jobs\">Viewing Jobs</a> above.  In the " . "job detail page, toward the bottom you will see the \""._XF_PEO_APPLY."\" button.  " . "Click this button, and an e-mail with a summary of your skills profile will be " . "sent to the administrator(s) of the project or community." . "<p>You can also apply for a job by simply contacting the administrator(s) of the " . "project or community and sending them a message indicating your interest in the " . "job.";
	themesidebox_help($title, $content, "applying_for_jobs");
	echo "<br><br>\n";
	 
	$title = "How Do I Post A Job?";
	$content = "<p>Administrators of projects and communities can post jobs for their project " . "or community.  To do so, click the \""._XF_G_ADMIN."\" link in the project or " . "community menu, then select the \""._XF_PRJ_POSTJOBS."\" link.  You will see a " . "form asking for the job category, and a short and long description of the job.  " . "The short description is the information that will be displayed in the list of " . "jobs in the \""._XF_XFJOBS_NAME."\" section.  The long description will be " . "displayed on the job detail page." . "<p>After clicking the \""._XF_PEO_CONTINUE."\" button, your job is posted.  You " . "now have the option of adding a list of skills to the job.  This is done in the " . "same way as by a user when updating the skills profile.  See " . "<a href=\"jobs.php#managing_your_skills_profile\">Managing Your Skills Profile</a> " . "for more information on setting up a skills profile.";
	themesidebox_help($title, $content, "posting_jobs");
	echo "<br><br>\n";
	 
	$title = "How Do I Edit A Job?";
	$content = "<p>Administrators of projects and communities can edit jobs they have posted " . "for their project or community at any time.  Begin by clicking the \""._XF_G_ADMIN . "\" link in the project or community menu, and then clicking the \"" . _XF_PRJ_EDITJOBS."\" link.  You will see all the open jobs for your project or " . "community.  Click on the job you want to edit to view the job detail page, then " . "click the \""._XF_PEO_EDITJOB."\" link.  You will be presented with the same " . "form used to create the job." . "<p>Simply make any changes you want to make within the form.  When you are done " . "making changes, click on the \""._XF_PEO_FINISHED."\" button to save your changes.";
	themesidebox_help($title, $content, "editing_jobs");
	echo "<br><br>\n";
	 
	end_help_content();
	 
	include("../../../footer.php");
?>