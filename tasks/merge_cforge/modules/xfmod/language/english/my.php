<?php
// xf/my/index.php
define("_XF_MY_PERSONALPAGEFOR", "Account Page For: %s"); // %s = username
define("_XF_MY_MYPERSONALPAGE", "My Account Page");
define("_XF_MY_DIARYNOTES", "Diary &amp; Notes");
define("_XF_MY_MYACCOUNT", "Account");
define("_XF_MY_MYPUBKEYS", "Public Keys");
define("_XF_MY_SKILLPROFILE", "Skills Profile");
define("_XF_MY_PROFILE", "Profile");
define("_XF_MY_INBOX", "Inbox");
define("_XF_MY_MYASSIGNEDITEMS", "my assigned items");
define("_XF_MY_NOOPENTRACKERITEMS", "There are no items assigned to you.");
define("_XF_MY_MYSUBMITTEDITEMS", "my submitted items");
define("_XF_MY_NOSUBMITTEDTRACKERITEMS", "You have not submitted any tracker items.");
define("_XF_MY_MONITOREDFORUMS", "Monitored Forums");
define("_XF_MY_NOTMONITORFORUMS", "You do not monitor any forums.");
define("_XF_MY_MONITOREDFILES", "Monitored File Modules");
define("_XF_MY_NOTMONITORFILES", "You do not monitor any File Modules.");
define("_XF_MY_MYTASKS", "my tasks");
define("_XF_MY_NOOPENTASKS", "There are no open tasks assigned to you.");
define("_XF_MY_QUICKSURVEY", "Quick Survey");
define("_XF_MY_QUICKSURVEYTAKEN", "You have taken your developer survey.");
define("_XF_MY_MYBOOKMARKS", "my bookmarks");
define("_XF_MY_NOBOOKMARKS", "You do not have any bookmarks stored.");
define("_XF_MY_ADDBOOKMARK", "Add a custom bookmark");
define("_XF_MY_MYPRJCOMM", "My Projects/Communities");
define("_XF_MY_MYPROJECTS", "My Projects");
define("_XF_MY_MYCOMM", "My Communities");
define("_XF_MY_NOPROJECTS", "You are not participating in any project.");
define("_XF_MY_SITELISTS", "site mailing lists");
define("_XF_MY_NOSUBSCRIPTIONS", "You are not currently subscribed to any site lists.");
define("_XF_MY_SUBSCRIPTIONS_HDR", "You are currently subscribed to the following site lists.  Enter mailing list password to unsubscribe.");
define("_XF_MY_TOSUBSCRIBE_HDR", "To Subscribe:");
define("_XF_MY_TOUNSUBSCRIBE_HDR", "To Unsubscribe:");
define("_XF_MY_PASSWD_HDR", "Password");
define("_XF_MY_VERIFY_HDR", "Verify");
define("_XF_MY_SUBMIT_HDR", "Submit");
define("_XF_MY_UNSUBSCRIBE", "Unsubscribe");
define("_XF_MY_SUBSCRIBE", "Subscribe");
define("_XF_MY_AVAILABLE_SUBS", "The following additional subscriptions are available.  Enter and verify a mailing list password to subscribe.  <em>Do not use a secure password</em>.");
define("_XF_MY_LISTNAME_HDR", "List Name");
define("_XF_MY_ENTERPWD", "Enter Password");
define("_XF_MY_CONFIRMPWD", "Confirm Password");
define("_XF_MY_INVALIDOPERATION", "Invalid Operation requested.");
define("_XF_MY_PASSWD_NOMATCH", "The passwords you entered do not match.");
define("_XF_MY_PASSWD_REQD", "You need to enter a password for this mailing list to perform this action.");
define("_XF_MY_NOUNSUB_NODATA", "Unable to process unsubscription request - incomplete data.");
define("_XF_MY_NOSUB_NODATA", "Unable to process subscription request - incomplete data.");
define("_XF_MY_UNSUB_FAIL", "Unable to complete unsubscription request - database error.");
define("_XF_MY_UNSUB_SUCCESS", "Unsubscription request completed successfully.");
define("_XF_MY_SUB_FAIL", "Unable to complete subscription request - database error.");
define("_XF_MY_SUB_SUCCESS", "Subscription request completed successfully.");
 
// xf/my/diary.php
define("_XF_MY_DIARYUPDATED", "Diary Updated");
define("_XF_MY_NOTHINGUPDATED", "Nothing Updated");
define("_XF_MY_ITEMADDED", "Item Added");
define("_XF_MY_MAILSENT", " email sent -(%s) people monitoring "); // %s = number of people monitoring
define("_XF_MY_MAILNOTSENT", " email not sent - no one monitoring ");
define("_XF_MY_ERRORITEMADDED", "Error Adding Item");
define("_XF_MY_ADDENTRY", "Add a New Entry");
define("_XF_MY_UPDATEENTRY", "Update an Entry");
define("_XF_MY_MYDIARY", "My Diary And Notes");
define("_XF_MY_DETAILS", "Details:");
define("_XF_MY_SUBMITONCE", _XF_G_SUBMIT);
define("_XF_MY_IFPUBLIC", "If marked as public, your entry will be mailed to any monitoring users when it is first submitted.");
define("_XF_MY_EXISTINGDIARY", "Previous Entries");
define("_XF_MY_YOUHAVENODIARY", "You Have No Diary Entries");
define("_XF_MY_SUMMARY", "Summary");
define("_XF_MY_CREATIONDATE", 'Creation Date');
 
// xf/my/rmproject.php
define("_XF_MY_TAKINGBACK", "Taking you back...");
define("_XF_MY_PROJECTADMINERROR", "Operation Not Permitted<br />You cannot remove yourself from this project, because " ."you are admin of it. You should ask an other admin to reset " ."your admin privilege first. If you are the only admin of " ."the project, please consider posting availability notice to " ."<a href='".ICMS_URL."/modules/xfjobs/createjob.php?group_id=%s'>Help Wanted Board</a> and be ready to " // %s is group_id
."pass admin privilege to interested party.");
define("_XF_MY_QUITTINGPROJECT", "Quitting Project");
define("_XF_MY_ABOUTTOREMOVE", "You are about to remove yourself from the project. Please confirm your action:");
 
// bookmark_edit.php
define("_XF_MY_EDITBOOKMARK", "Edit Bookmark");
define("_XF_MY_BOOKMARKURL", "Bookmark URL");
define("_XF_MY_BOOKMARKTITLE", "Bookmark Title");
 
// bookmark_delete.php
define("_XF_MY_DELETEBOOKMARK", "Delete Bookmark");
define("_XF_MY_BOOKMARKDELETED", "Bookmark deleted");
 
// bookmark_add.php
define("_XF_MY_ADDNEWBOOKMARK", "Add New Bookmark");
define("_XF_MY_ADDEDBOOKMARK", "Added bookmark for <strong>'%s'</strong> with title <strong>'%s'</strong>."); // 1. Bookmark URL 2. Bookmark Title
define("_XF_MY_VISITBOOKMARK", "Visit the bookmarked page");
define("_XF_MY_BACKTOPAGE", "Back to your personal page");
 
?>