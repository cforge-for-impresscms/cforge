<?php 
/**
 * accounts.php
 *
 * @version   $Id: accounts.php,v 1.5 2004/01/26 18:57:04 devsupaul Exp $
 */
include_once("../../../mainfile.php");

$langfile="help.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/help/help_utils.php");

include_once(XOOPS_ROOT_PATH."/modules/system/language/english/blocks.php");
include_once(XOOPS_ROOT_PATH."/modules/xfaccount/language/english/modinfo.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/language/english/my.php");
include_once(XOOPS_ROOT_PATH."/language/english/user.php");
include_once(XOOPS_ROOT_PATH."/modules/xoopsmembers/language/english/modinfo.php");

//meta tag information
$metaTitle=": "._XF_H_HELP;

include ("../../../header.php");

$site = $xoopsConfig['sitename'];

help_menu("accounts");

begin_help_content();	
	
$title = "Why Should I Create An Account?";
$content = "<p>You are not required to create an account to consume the information and services
provided by $site.  This means that you can browse projects or communities,
read news or forum postings, subscribe to mailing lists, and even download software
without having an account.
<p>When you will want an account is when you desire to participate in the site.
For example, an account may be required before you can submit news or articles, or
before you can post information to forums.
<p>An account is also required if you want to be a member of a community or a project.
Being a member of a community or project means that you have some rights on that community
or project.  For example, you may gain the rights to monitor news submissions, manage
the assignment of enhancement requests, or even modify source code for a project.  Of
course, you do not need membership or an account to simply consume the content of any
community or project.";

themesidebox_help( $title, $content, "purpose" );

echo "<br><br>";

$title = "How Do I Create An Account?";
$content = "<p>If you already have an account, you will see the text
\""._MB_SYSTEM_VACNT."\" near the top left corner of your browser.  Otherwise,
you will see the text \""._MB_SYSTEM_RNOW."\"  This indicates that you do not yet have a
$site account.
<p>To create your account, click on the text that says, \""._MB_SYSTEM_RNOW."\"
If you already have a Novell eLogin account, you will see a short form that is already
filled in with the required information as obtained from your eLogin account.  Submit
this form to start the creation of your account.  You will see a confirmation screen to
confirm your choice to create an account.  Click \""._US_FINISH."\" to confirm
your choice to create an account.
<p>If you do not already have an eLogin account, you will need to create an eLogin account
first.  $site uses eLogin so that you use the same credential on all Novell
systems.  In addition, this allows you to manage your identity for all Novell services with
a single credential.";

themesidebox_help( $title, $content, "creating" );

echo "<br><br>";

$title = "How Do I Log In To $site?";
$content = "<p>When you navigate to $site, the iChain servers that front the site will
determine when you need to log in and will present you with a login screen at that time.
You do not need to worry about logging in manually.";

themesidebox_help( $title, $content, "logging_in" );

echo "<br><br>";

$title = "How Do I Log Out?";
$content = "<p>To log out, you need to log completely out of eLogin.  Since eLogin manages your
credential for multiple Novell websites, it is to your advantage to remain logged in,
since your login session for $site will also log you into other 
privileged Novell services.
<p>Closing your browser will always terminate your session and effectively log you out.";

themesidebox_help( $title, $content, "logging_out" );

echo "<br><br>";

$title = "How Do I Manage My Account?";
$content = "<p>$site provides rich account management capabilities.  All account "
	. "management is handled through your personal page.  You can get to your personal "
	. "page by clicking on "._XF_XFACCOUNT_NAME." in the Main Menu."
	. "<p>The menu along the top of the screen allows you to navigate to different areas "
	. "of your account for administrative purposes."
	. "<p><br><b>"._XF_MY_MYPERSONALPAGE."</b><br>This page gives you a quick overview "
	. "of activity within the site that is most pertinent to you.  Some of the key areas "
	. "of this page are:"
	. "<ul><li><b>"._XF_MY_MYPRJCOMM."</b> - Displays a quick list of links to projects "
	. "or communities with which you have membership."
	. "<br><img src=\"".XOOPS_URL."/modules/xfmod/images/ic/trash.png\" width='16' height='16' alt='delete'>  This icon "
	. "appears next to projects or communities of which you are a <u>member</u>.  "
	. "Clicking	on this icon will terminate your membership with that project or community."
	. "<br><img src=\"".XOOPS_URL."/modules/xfmod/images/ic/trash-x.png\" width='16' height='16' alt='can not delete'>  This "
	. "icon appears next to projects or communities of which you are an <u>administrator</u>.  "
	. "You cannot terminate your membership as long as you have administrative status."
	. "<li><b>"._XF_MY_MYASSIGNEDITEMS."</b> - Displays a list of items, such "
	. "as bugs or enhancement requests, that have been assigned to you."
	. "<li><b>"._XF_MY_MYSUBMITTEDITEMS."</b> - Displays a list of items, such "
	. "as bugs or enhancement requests, that you have submitted to other projects."
	. "<li><b>"._XF_MY_MONITOREDFORUMS."</b> - Displays a list of forums that you "
	. "are monitoring.  You can click on the name of the forum and go directly to the page "
	. "for that forum.  Clicking on the <img src=\"".XOOPS_URL."/modules/xfmod/images/ic/trash.png\" width='16' height='16' alt='delete'> "
	. "icon will remove that forum from your monitored forums list."
	. "<li><b>"._XF_MY_MONITOREDFILES."</b> - Displays a list of file modules "
	. "that you are monitoring.  (A file module is another name for a software distribution.)  "
	. "You can click on the name of the file module and go directly to the download page for "
	. "that file module.  Clicking on the <img src=\"".XOOPS_URL."/modules/xfmod/images/ic/trash.png\" width='16' height='16' alt='delete'> "
	. "icon will remove that file module from your monitored file modules list."
	. "<li><b>"._XF_MY_MYTASKS."</b> - Displays a list of project tasks that have "
	. "been assigned to you."
	. "<li><b>"._XF_MY_SITELISTS."</b> - You can use this area of your personal "
	. "page to manage subscriptions to sitewide mailing lists.  By default, you are not subscribed "
	. "to any of the mailing lists.  There are four sitewide mailing lists available:"
		. "<ul><li><b>Site Newsletter</b> - A periodic mailing that includes news "
		. "and information about the site"
		. "<li><b>Registered Users</b> - An infrequent, irregular mailing to all "
		. "registered users of the site	with important site news, such as system "
		. "outage or policy modification notices"
		. "<li><b>Project Admins</b> - An infrequent, irregular mailing to all users "
		. "who have project admin status on at least one project.  If you are not a "
		. "project admin, you will not see the option to subscribe to this list."
		. "<li><b>Community Admins</b> - An infrequent, irregular mailing to all users "
		. "who have	community admin status on at least one community.  If you are not "
		. "a community admin, you will not see the option to subscribe to this list."
		. "</ul>"
	. "To subscribe to any mailing list, enter and confirm a mailing list password in the "
	. "text boxes next to the list you are interested in.  Click the checkbox that says "
	. _XF_MY_SUBSCRIBE.".  When you have done this for all the lists you are interested in, "
	. "click the "._XF_G_SUBMIT." button to complete your subscription.<br>"
	. "<b>Important</b> - Do not use a secure password for the mailing list subscriptions.<br>"
	. "To unsubscribe from any mailing list, enter the mailing list password in the text box next "
	. "to the list from which you wish to unsubscribe.  Click the checkbox that says "
	. _XF_MY_UNSUBSCRIBE.".  When you are ready, click the "._XF_G_SUBMIT
	. " button to complete the unsubscription.</ul>"
	. "<p><br><b>"._XF_MY_DIARYNOTES."</b><br>This page provides you with an area where "
	. "you can keep track of notes or diary/log entries and retrieve them later for use."
	. "<br><br>To create a diary entry, simply fill in the form and submit.  This entry will "
	. "be available for you to view later underneath the heading labeled \""
	. _XF_MY_EXISTINGDIARY."\", and you can view/edit it by clicking on the entry summary."
	. "<br><br>Diary entries can either be public (that is, viewable by other users) or "
	. "private.  By default, all diary entries are private.  To make a diary entry public, "
	. "make sure the \""._XF_G_ISPUBLIC."\" checkbox is checked on the entry in question."
	. "<p><br><b>"._XF_MY_MYACCOUNT."</b><br>Use this page to view specific details "
	. "about your account, such as your personal information (Avatar, website, instant "
	. "messaging ids, and other information), statistics about your account, and links "
	. "to your most recent postings.  This is referred to as your user profile."
	. "<br><br>You can also click "._US_EDITPROFILE." to update your user profile.  It is "
	. "within this page that you can select a different avatar, fill in information for your "
	. "webpage, instant messaging ids, enter your real name, and other things.  You can "
	. "even create a signature - a block of text that can be automatically added to your "
	. "forum postings - by filling in the text area labeled \"Signature\"."
	. "<br><br>Since your account is created using Novell eLogin information, there are "
	. "some aspects of your account - namely, your username, e-mail address, and password - "
	. "that you cannot change within the $site account management page."
	. "<br><br>Every user's user profile is viewable by other users of the system.  To view "
	. "another user's profile, you can search for that user using the \""._MI_MEMBERS_NAME
	. "\" search form, accessible from the main menu.  Once you locate the user you want "
	. "to view, click on the username to view their profile."
	. "<br><br>The "._US_INBOX." button takes you to your message inbox.  You go here to view "
	. "any messages that have been sent to you, and also to send messages between yourself "
	. "and other $site users."
	. "<p><br><b>"._XF_MY_SKILLPROFILE."</b><br>Using this page you can set up a skills "
	. "profile and post your resume.  This allows other users to view your abilities, "
	. "and can be very handy in helping you to obtain membership on a project.  "
	. "See <a href=\"jobs.php#managing_your_skills_profile\">Managing Your Skills Profile</a> "
	. "for more information.";

themesidebox_help( $title, $content, "account_administration" );

end_help_content();

include("../../../footer.php");
?>