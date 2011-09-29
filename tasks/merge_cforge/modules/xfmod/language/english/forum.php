<?php
	define("_XF_FRM_FORUMS", "Forums");
	define("_XF_FRM_FORUMSFORGROUP", "Forums for %s"); // %s = group name
	define("_XF_FRM_NOFORUMSFOUNDFORGROUP", "No forums found for %s"); // %s = group name
	define("_XF_FRM_CHOOSEFORUMTOBROWSE", "Choose a forum and you can browse, search, and post messages.");
	 
	define("_XF_FRM_FORUMNOTFOUND", "Forum not found");
	define("_XF_FRM_FORUMISRESTRICTED", "Forum is restricted to members of this group");
	define("_XF_FRM_MESSAGEPOSTED", "Message Posted Successfully");
	define("_XF_FRM_SHOW", "Show");
	define("_XF_FRM_CHANGEVIEW", "Change View");
	define("_XF_FRM_TOPIC", "Topic");
	define("_XF_FRM_TOPICSTARTER", "Topic Starter");
	define("_XF_FRM_REPLIES", "Replies");
	define("_XF_FRM_LASTPOST", "Last Post");
	define("_XF_FRM_PREVIOUSMESSAGES", "Previous Messages");
	define("_XF_FRM_NEXTMESSAGES", "Next Messages");
	define("_XF_FRM_POSTMESSAGETOTHREAD", "Post A Message To This Thread");
	define("_XF_FRM_STARTNEWTHREAD", "Start a New Thread");
	define("_XF_FRM_MUSTSPECIFYFORUM", "You must specify a forum first");
	 
	define("_XF_FRM_NEWS", "News");
	define("_XF_FRM_NEWSITEMNOTFOUND", "This news item was not found");
	define("_XF_FRM_LATESTNEWS", "Latest News");
	define("_XF_FRM_DISCUSSIONFORUMS", "Discussion Forums");
	define("_XF_FRM_MONITORFORUM", "Monitor Forum");
	define("_XF_FRM_STOPMONITORFORUM", "Stop Monitoring Forum");
	 
	define("_XF_FRM_ERRORADDINGFORUM", "Error Adding Forum");
	define("_XF_FRM_BROKENTHREAD", "Broken Thread");
	define("_XF_FRM_THREAD", "Thread");
	define("_XF_FRM_AUTHOR", "Author");
	define("_XF_FRM_DATE", "Date");
	 
	define("_XF_FRM_COULDPOSTIFLOGGEDIN", "You could post if you were logged in");
	define("_XF_FRM_TRYINGTOPOSTWITHOUTID", "Trying to post without a forum ID");
	define("_XF_FRM_MUSTINCLUDEBODYANDSUB", "Must include a message body and subject");
	define("_XF_FRM_GETTINGNEXTIDFAILED", "Getting next thread_id failed");
	define("_XF_FRM_COULDNOTUPDATEPARENTTIME", "Could not Update Master Thread parent with current time");
	define("_XF_FRM_COULDNOTUPDATEPARENT", "Could not Update parent");
	define("_XF_FRM_TRYINGTOFOLLOWUPNOTEXIST", "Trying to followup to a message that does not exist.");
	define("_XF_FRM_NOFOLLOWUPIDPRESENT", "No followup ID present when trying to post to an existing thread.");
	define("_XF_FRM_POSTINGFAILED", "Posting Failed");
	define("_XF_FRM_FAILEDTOGETINSERTID", "Failed to get insertid()");
	define("_XF_FRM_HTMLDISPLAYSASTEXT", "HTML tags will display in your post as text");
	define("_XF_FRM_POSTCOMMENT", "Post Comment");
	 
	define("_XF_FRM_EMAILSENT", "email sent");
	define("_XF_FRM_EMAILNOTSENT", "email not sent");
	define("_XF_FRM_PEOPLEMONITORING", "people monitoring");
	define("_XF_FRM_NOONEMONITORING", "no one monitoring");
	 
	// message.php
	define("_XF_FRM_MESSAGENOTFOUND", "Message Not Found");
	define("_XF_FRM_MESSAGENOTEXIST", "This message does not(any longer) exist.");
	define("_XF_FRM_THREADVIEW", "Thread View");
	define("_XF_FRM_POSTFOLLOWUP", "Post a followup to this message");
	 
	// monitor.php
	define("_XF_FRM_COULDNOTINSERTMONITOR", "ERROR - could not insert monitor info into database");
	define("_XF_FRM_FORUMISMONITORED", "Forum Is Now Being Monitored");
	define("_XF_FRM_FORUMISNOTMONITORED", "Forum Monitoring Deactivated");
	define("_XF_FRM_MUSTLOGGEDINTOMONITOR", "You must be logged in to monitor a forum");
	 
	// admin/index.php
	define("_XF_FRM_MESSAGESDELETED", "messages deleted");
	define("_XF_FRM_MESSAGESNOTFOUNDINYOURGROUP", "Message not found or message is not in your group");
	define("_XF_FRM_EMAILADDRESSINVALID", "The Email Address You Provided Was Invalid");
	define("_XF_FRM_ERRORUPDATINGFORUMINFO", "Error Updating Forum Info");
	define("_XF_FRM_FORUMINFOUPDATED", "Forum Info Updated Successfully");
	define("_XF_FRM_FORUMADMIN", "Forum Administration");
	define("_XF_FRM_DELETEAMESSAGE", "Delete a message");
	define("_XF_FRM_WARNINGABOUTTODELETEMESSAGE", "WARNING! You are about to permanently delete a message and all of its followups!!");
	define("_XF_FRM_ENTERMESSAGEID", "Enter the Message ID");
	define("_XF_FRM_ADDAFORUM", "Add a Forum");
	define("_XF_FRM_LINKFORUM", "Link/Unlink External Forums/Newsgroups");
	define("_XF_FRM_EXISTINGFORUMS", "Existing Forums");
	define("_XF_FRM_EXISTINGEXTFORUMS", "Existing External Forums");
	define("_XF_FRM_LINKINSTRUCTIONS", "To link to an external Novell forum, enter the forum name in the box below.  The forum name of the format \"novell.devsup.(name)\", where(name) is some identifier, like \"activex\" or \"consoleone\".  You can see the forum names by browsing to the online forums at <a href=\"http://developer-forums.novell.com/\">developer-forums.novell.com</a>.");
	define("_XF_FRM_FORUMNAME", "Forum Name");
	define("_XF_FRM_NAMEREQD", "Forum name is required to link to an external forum");
	define("_XF_FRM_LINKTO", "Link to external forum");
	define("_XF_FRM_CANTREMOVELINK", "Unable to remove link to forum");
	define("_XF_FRM_REMOVELINKS", "Unlink the Selected External Forum(s)");
	define("_XF_FRM_SELECTFORUM", "Please select a form to unlink from.");
	define("_XF_FRM_FORUMMUSTBEGINWITH", "External forum names must begin with");
	define("_XF_FRM_ADDEDFORUMCANNOTBEMODIFIED", "Once you add a forum, it cannot be modified or deleted!");
	define("_XF_FRM_ADDTHISFORUM", "Add This Forum");
	define("_XF_FRM_CHANGEFORUMSTATUS", "Change Forum Status");
	define("_XF_FRM_NOFORUMSFOUNDFORTHISPROJECT", "No forums found for this project");
	define("_XF_FRM_CHANGEFORUMINFO", "You can adjust forum features from here. Please note that private forums can still be viewed by members of your project, not the general public.");
	define("_XF_FRM_FORUM", "Forum");
	define("_XF_FRM_STATUS", "Status");
	define("_XF_FRM_ALLOWANONYMOUS", "Allow Anonymous Posts?");
	define("_XF_FRM_EMAILALLTO", "Email All Posts To");
	define("_XF_FRM_YOUARENOTFORUMADMIN", "You're not a forum administrator");
	 
	// admin/forum/main.php
	define("_XF_FRM_SERVERS", "Configured External Newsgroup Servers");
	define("_XF_FRM_NOSERVERSCONFIGURED", "There are currently no external servers configured");
	define("_XF_FRM_SERVERNAME", "Server Name");
	define("_XF_FRM_ADDSERVER", "Add An External Newsgroup Server");
	define("_XF_FRM_SERVERHOST", "Hostname:");
	define("_XF_FRM_SERVERPORT", "Port:");
	define("_XF_FRM_SERVERLOGIN", "Login:");
	define("_XF_FRM_SERVERPWD", "Password:");
	define("_XF_FRM_SUBMITSERVER", "Create Server");
	define("_XF_FRM_ADDSERVER_FAILED", "Unable to add new newsgroup server");
	define("_XF_FRM_NOHOST", "Host not defined");
	define("_XF_FRM_NOPORT", "Port not defined");
	define("_XF_FRM_DELSERVER", "Delete Server");
	define("_XF_FRM_AVAILGROUPS", "Available Newsgroups on Server");
	define("_XF_FRM_UPDSERVER", "Update Server");
	 
	define("_XF_PRJ_PROJECTMARKEDASPRIVATE", "Sorry, this project is marked as private and you are not a member of this project");
	 
?>