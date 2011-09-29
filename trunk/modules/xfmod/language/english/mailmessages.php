<?php
function frsGetNoticeMessage ($unix_group,$full_group,$package,$date,$group_id,$release_id,$package_id)
{
  global $xoopsConfig;
  $site = $xoopsConfig['sitename'];
	
  $message = array();
	$message['subject'] = "[$site "._XF_FRS_RELEASE."] ".$unix_group." : ".$package;
		
  $message['body'] = "Project: ".$full_group."  (".$unix_group.")\n"
	                  ."Package: ".$package."\n"
										."Date   : ".$date."\n"
										."\n"
										."Project '".$full_group."' ('".$unix_group."') has "
										."released the new version of package '".$package."'. "
										."You can download it from $site "
										."by following this link:\n"
										."<".$xoopsConfig['xoops_url']."/modules/xfmod/project/showfiles.php?group_id=".$group_id."&release_id=".$release_id.">\n"
										."or browse Release Notes and ChangeLog by visiting this link:\n"
										."<".$xoopsConfig['xoops_url']."/modules/xfmod/project/shownotes.php?release_id=".$release_id.">\n"
										."\n"
										."You receive this email because you "
										."requested to be notified when new versions of this package "
										."were released. If you don't wish to be notified in the "
										."future, please login to $site and click this link: "
										."<".$xoopsConfig['xoops_url']."/modules/xfmod/project/filemodule_monitor.php?filemodule_id=".$package_id.">\n";
										
  return $message;
}

function grpGetApprovalMessage($unix_group,$full_group,$group_id,$type=1)
{
  global $xoopsConfig;
  $site = $xoopsConfig['sitename'];

  $prjtype=($type==1?"project":"community");
  $uprjtype=($type==1?"Project":"Community");
  $plprjtype=($type==1?"projects":"communities");
  $message = array();
  $message['subject'] = "Your Novell Forge project (".$full_group.") has been approved";

$message['body'] = "Welcome to Novell Forge!  Thank you for your submission.\n" 
     ."\n"
     .$uprjtype." Full Name: ".$full_group."\n"
     .$uprjtype." Short Name: ".$unix_group."\n"
     ."\n"
     .$xoopsConfig['xoops_url']."/modules/xfmod/project/?".$unix_group."\n"
     ."\n"
     ."Below you'll find frequently asked questions and answers to help you administer your new forge project.  For a complete list of How Do I... project topics (including information on surveys, forums, trackers, and CVS), please visit ".$xoopsConfig['xoops_url']."/modules/xfmod/help/projects.php\n"
     ."\n"
     ."We hope you enjoy Novell Forge and tell others about it.  We welcome your questions, comments and suggestions.  You can contact us at:\n"  
     ."\n"
     .$xoopsConfig['xoops_url']."/modules/contact/\n" 
     ."\n"
     ."-- the ".$site." group\n" 
     ."\n"
     ."All Project Help\n"
     ."\n"
     .$xoopsConfig['xoops_url']."/modules/xfmod/help/projects.php\n"  
     ."\n"
     ."*****************************************************************************************************\n" 
     ."\n"
     ."How do I Release Files for my project ?\n"
     ."\n"
     .$xoopsConfig['xoops_url']."/modules/xfmod/help/projects.php#downloading_projects\n"  
     ."\n"
     ."Within a project, the files that you can download are organized as follows:\n"
     ."\n"
     ."- A Project can have zero or more Packages.\n"
     ."\n"
     ."- A Package can have zero or more Releases.\n"
     ."\n"
     ."- A Release can have zero or more Files.\n"
     ."\n"
     ."This concept is perhaps best illustrated with an example.\n" 
     ."\n"
     ."Package: Linux\n"
     ."\n"
     ."Release: SuSe_v9.0\n"
     ."\n"
     ."File: ldap_gui-1-0.i386.rpm\n"
     ."\n"
     ."Suppose you administer a project that is developing a simple GUI LDAP browsing client, and suppose you have a compiled executable of your project for Linux platforms. This is a perfect candidate for release. In order to release your executable, you would first decide what package the executable belongs in. You may have the packages \"Windows,\" \"Linux,\" \"Netware,\" and \"Macintosh,\" so the \"Linux\" package would be a good choice.\n" 
     ."\n"
     ."Next you need a release within the package. Perhaps the release itself is \"SuSe_v9.0\", to denote the release of the software as a compiled executable on SuSe 9.0.\n"
     ."\n"
     ."Finally, you create the file within the release by uploading the file and assigning it to the release.\n"
     ."\n"
     ."The existence of packages, releases, and files allows you complete control over the organization and management of your file releases.\n"
     ."\n"
     ."If you are the consumer of a project, it is even easier. On the main project page, the files available for download are listed underneath the heading \"Latest File Releases\". Simply click on the release you are interested in and you will be taken to a page where you can click on the file(s) you want to download.\n"
     ."\n"
     ."*****************************************************************************************************\n"
     ."\n"
     ."How can I Categorize My Project ?\n"
     ."\n"
     .$xoopsConfig['xoops_url']."/modules/xfmod/help/software_map.php#categorizing_projects\n"  
     ."\n"
     ."One of the first tasks to complete after your project is approved is that of categorization which makes it very easy for other users to find your project page.\n" 
     ."\n"
     ."To begin categorizing your project, navigate to the administrative page of your project by clicking the \"Admin\" link in your project page. The task you want to undertake is that of editing your trove categorization. When you select this task, you will see a page that allows you to select the appropriate subcategory for every category in the Software Map: Development Status, Intended Audience, License, etc. You can also select up to three communities with which to associate your project.\n"
     ."\n"
     ."A scheduled task runs periodically to update the software map.  This takes about 24 hours, after which time you should find that your project has been categorized.\n"
     ."\n"
     ."*****************************************************************************************************\n"
     ."\n"
     ."How do I Create and Administer News for my Project ?\n"
     ."\n"
     .$xoopsConfig['xoops_url']."/modules/xfmod/help/news.php#submitting_news\n"  
     ."\n"
     ."You can create your own news items for your project. This is a good way to provide summary information about the current state of your project.\n"
     ."\n"
     ."To submit news for projects or communities, click on the \"Submit News\" link in the news section. Fill in the form with the context of your news item and then click on the submit button.\n"
     ."\n"
     ."As the administrator of a project or community, you can click on the \"Admin\" link in the news section of your project or community to get to the news administrative page. From here you can view all submitted news. Clicking on any news summary brings up a form where you can edit the submission or even delete it entirely.  No news item will appear until you, as the Admin, mark it as Displayed.\n"
     ."\n"
     ."*****************************************************************************************************\n"
     ."\n"
     ."How do I Manage my Project ?\n"
     ."\n"
     .$xoopsConfig['xoops_url']."/modules/xfmod/help/projects.php#administering_projects\n"
     ."\n"
     ."First off, you must be an administrator of a project in order to perform administrative tasks. You become a project administrator in one of two ways:\n"
     ."\n"
     ."  - By being the creator of a project\n"
     ."  - By being granted project administrator status by another administrator\n"
     ."\n"
     ."Clicking on the \"Admin\" link in the project menu will take you to the primary project administration page. This page has several subsections.\n"
     ."\n"
     ."Admin - This is the main project administrative page. From this page you can perform the following tasks:\n"
     ."\n"
     ."  - Change the trove categorization\n"
     ."  - Enable and disable CVS access controls\n"
     ."  - Access administrative panels for all project tools\n"
     ."  - Add users to your project - simply enter their username and click \"Add User\"\n" 
     ."\n"
     ."User Permissions - From within this page you will see an overview of all the members of your project and the permissions they have within the project. Clicking on a member's username brings up the permissions management page for that member. Using this page, you can make changes to the permissions of any user, including yourself.\n"
     ."\n"
     ."**Note that Project Roles exist for the purpose of helping you remember what each person does on your project. Assigning a user to a role does not have any effect on the permissions that user has within your project.\n"
     ."\n"
     ."Edit Public Info - This page allows you to modify the information that is publicly available about your project. You can change your project name, project description, or project homepage within this page. You can also use this page to activate or deactivate certain project tools or features, like forums, surveys, mailing lists, or tasks.\n"
     ."\n"
     ."Project History - You can use this page to quickly view a log of the administrative changes that have taken place within your project.\n"
     ."\n"
     ."Edit Release Files - It is within this page that you create and manage your project releases. You can create and manage packages, releases for each package, and files for each release here. View the Downloading Projects section for more detailed instructions on how to manage your file modules.\n"
     ."\n"
     ."Post Jobs - You can use this page to post jobs for your project that will appear in the \"Help Wanted\" section. This is a good way to advertise to get additional help on your project.\n"
     ."\n"
     ."Edit Jobs - From within this page, you will see a view of all the jobs that are currently active for your project. By clicking on a job, you will be presented with a page where you can modify any information about a job you posted.\n"
     ."\n"
     ."**All the tools used within the project also have an administrative console for each tool. To get to the administrative pages for a tool, click on the name of the tool in the project menu, then click on the \"Admin\" link that appears.\n"
     ."\n"
     ."**To know when bugs, support requirements, patches, or feature requirements are entered for your project, add your e-mail address to each classification.  You will receive a notification when requests are made.\n"
     ."\n"
     ."Example:\n"
     ."\n"
     ."  Trackers Tab\n"
     ."\n"
     ."  Click on Admin option under the Trackers Tab\n" 
     ."\n"
     ."  Click on Bugs\n"
     ."\n"
     ."  Click on Update preferences\n"
     ."\n"
     ."  Enter your e-mail address under the \"Send email on new submission to address\" field.\n"
     ."\n"
     ."  Check the send e-mail on all changes box.\n"
     ."\n"
     ."  Click on the submit button.\n"
     ."\n"
     ."Repeat the above steps for Support Requests, Patches, and Feature Requests\n" 
     ."\n"
     ."*****************************************************************************************************\n" 
     ."\n"
     ."How Do I Use CVS ?\n"
     ."\n"
     .$xoopsConfig['xoops_url']."/modules/xfmod/help/projects.php#cvs\n"  
     ."\n"
     ."CVS is the source code repository control system used by Novell Forge. When you create a project, you are allowed the option to make use of CVS to manage the source code for your project.\n" 
     ."\n"
     ."If you want others to be able to contribute to and enhance your project, you should consider using our CVS server to host your project. Access to the CVS server is limited only to individuals who are members of your project.\n" 
     ."\n"
     ."As an option, you can select to enable anonymous CVS access to your source code. If you enable anonymous CVS access, any user of the site, even users who are not logged in, will be able to view and download a snapshot of your source code. A user must still be a member of your project in order to make changes to your source code.\n"
     ."\n"
     ."To enable anonymous CVS access, click the \"Admin\" link in your project page, then make sure the \"Anonymous CVS Access\" property is selected underneath the \"CVS Administration\" heading.\n" 
     ."\n"
     ."The CVS link in the project menu takes you to the CVS management page. Within this page you are shown the information that you need in order to begin managing your source code using our CVS server. If you have allowed anonymous access to your repository, this page also includes instructions for anonymous download, and a link to view the contents of your repository online.\n"
     ."\n"
     ."There are many CVS clients available that can consume a CVS resource like the one provided at Novell Forge. For details on how to set up your specific client, refer to the documentation provided with the client software.\n"
     ."\n"
     ."For complete documentation on CVS, read the CVS Manual at http://www.cvshome.org.\n"
     ."\n";
										
  return $message;
}

function grpGetDeniedMessage($unix_group,$full_group,$type=1)
{
  global $xoopsConfig;
  $site = $xoopsConfig['sitename'];

  $prjtype=($type==1?"project":"community");
  $uprjtype=($type==1?"Project":"Community");
  $plprjtype=($type==1?"projects":"communities");
  $message = array();
	$message['subject'] = "$site ".$uprjtype." Denied";
  
  $message['body'] = "Your ".$prjtype." registration for $site has been denied.\n"
     ."\n"
     .$uprjtype." Full Name: ".$full_group."\n"
     .$uprjtype." Short Name: ".$unix_group."\n"
     ."\n"
     ."Reasons for negative decision:\n";
  
  return $message;
}

function frmGetMonitorMessage($msg_id, $uname, $body, $forum_id, $unix_group_name,$forum_name, $subject)
{
  global $xoopsConfig;
  $site = $xoopsConfig['sitename'];
	
  $message = array();
	$message['subject'] = "[".$unix_group_name." - ".$forum_name."] ".$subject;
		
  $message['body'] = "\n"
	                  ."Read and respond to this message at:\n"
										."<".$xoopsConfig['xoops_url']."/modules/xfmod/forum/message.php?msg_id=".$msg_id.">\n"
										."By: ".$uname."\n"
										."\n"
										.$body."\n"
										."\n"
										."______________________________________________________________________\n"
										."You are receiving this email because you selected to monitor this forum.\n"
										."To stop monitoring this forum, login to $sitee and visit:\n"
										."<".$xoopsConfig['xoops_url']."/modules/xfmod/forum/monitor.php?forum_id=".$forum_id.">";
										
  return $message;
}

function myGetDiaryMessage($uname, $summary, $details, $uid)
{
  global $xoopsConfig;
  $site = $xoopsConfig['sitename'];
	
  $message = array();
	$message['subject'] = "[ XF User Notes: ".$uname."] ".stripslashes($summary);
		
  $message['body'] = "\n"
	                  .stripslashes($details)."\n"
										."\n"
										."______________________________________________________________________\n"
										."You are receiving this email because you selected to monitor this user.\n"
										."To stop monitoring this user, login to $site and visit:\n"
										."<".$xoopsConfig['xoops_url']."/modules/xfmod/developer/monitor.php?diary_user=".$uid.">";
										
  return $message;
}
?>