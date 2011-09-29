<?php
	// xf/include/tracker/Artifact.class
	define("_XF_TRK_A_ONLYMEMBERSCANVIEW", "Only Group Members Can View Private ArtifactTypes");
	define("_XF_TRK_A_ONLYADMINSCANMODIFY", "Only Artifact Admins Can Modify Private ArtifactTypes");
	define("_XF_TRK_A_ANONSUBMISSIONSNOTALLOWED", "This ArtifactType Does Not Allow Anonymous Submissions. Please Login.");
	define("_XF_TRK_A_SUMMARYREQUIRED", "Message Summary Is Required");
	define("_XF_TRK_A_BODYREQUIRED", "Message Body Is Required");
	define("_XF_TRK_A_ATTEMPTEDDOUBLESUBMIT", "You Attempted To Double-submit this item. Please avoid double-clicking.");
	define("_XF_TRK_A_VALIDEMAILREQUIRED", "Valid Email Address Required");
	define("_XF_TRK_A_NOWMONITORING", "Now Monitoring");
	define("_XF_TRK_A_MONITORINGDEACTIVATED", "Monitoring Deactivated");
	define("_XF_TRK_A_MISSINGPARAMETERS", "Missing Parameters");
	define("_XF_TRK_A_MISSINGMAILADDRESS", "Missing Email Address");
	define("_XF_TRK_A_UPDATEPERMISSIONDENIED", "Update Permission Denied");
	 
	define("_XF_TRK_A_ITEMSMALL", "item");
	define("_XF_TRK_A_WASOPENEDAT", "was opened at %s"); // %s = date
	define("_XF_TRK_A_YOUCANRESPOND", "You can respond by visiting:");
	define("_XF_TRK_A_INITIALCOMMENT", "Initial Comment");
	define("_XF_TRK_A_COMMENTBY", "Comment By");
	 
	// xf/include/tracker/ArtifactCanned.class
	define("_XF_TRK_AC_NAMEASSIGNEEREQUIRED", "Name and assignee are Required");
	 
	// xf/include/tracker/ArtifactFile.class
	define("_XF_TRK_AF_FILEADDED", "File Added");
	define("_XF_TRK_AF_FILEDELETED", "File Deleted");
	 
	// xf/include/tracker/ArtifactGroup.class
	define("_XF_TRK_AG_NAMEREQUIRED", "Name is Required");
	 
	// xf/include/tracker/ArtifactType.class
	define("_XF_TRK_AT_NAMEDESCDUEREQUIRED", "Name, Description, and Due Period are required");
	define("_XF_TRK_AT_STATUSNAMENOTFOUND", "Statusname not found");
	define("_XF_TRK_AT_NAMEDESCDUESTATUSREQUIRED", "Name, Description, Due Period, and Status Timeout are required");
	 
	// xf/include/pm/pm_data.php
	define("_XF_PM_NOTFOUND", "Not Found");
	define("_XF_PM_TASKBEENUPDATED", "Task #%s has been updated."); // %s = number of task
	define("_XF_PM_SUBPROJECT", "Subproject");
	define("_XF_PM_COMPLETE", "Complete");
	define("_XF_PM_AUTHORITY", "Authority");
	define("_XF_PM_FORMOREINFO", "For more info, visit:");
	define("_XF_PM_TASK", "Task");
	define("_XF_PM_TASKUPDATESENT", "Task Update Sent");
	define("_XF_PM_TASKUPDATENOTSENT", "Could Not Send Task Update");
	 
	// xf/include/canned_responses.php
	define("_XF_CRSELECTRESPONSE", "Select Response");
	 
	// xf/include/frs.class
	define("_XF_FRS_ADDCHANGELOGFAILED", "Add Change Log Failed");
	define("_XF_FRS_ADDFILEFAILED", "Add File Failed");
	define("_XF_FRS_VIRUSSCANFAILED", "The uploaded file could not be scanned for viruses");
	define("_XF_FRS_VIRUSSCANFAILEDNOFILE", "The uploaded file could not be scanned for viruses: There was no file.");
	define("_XF_FRS_VIRUSFOUND", "The uploaded file contained a virus and was deleted.  Please clean the file and upload it again.");
	define("_XF_FRS_ADDRELEASEFAILED", "Add Release Failed");
	define("_XF_FRS_ADDNOTESFAILED", "Add Notes Failed");
	define("_XF_FRS_CHANGEPACKAGENAMEFAILED", "Change Package Name Failed");
	define("_XF_FRS_CHANGEFILEFAILED", "Change File Failed");
	define("_XF_FRS_CHANGEFILERELEASEFAILED", "Change File Release Failed");
	define("_XF_FRS_CHANGERELEASEFAILED", "Change Release Failed");
	define("_XF_FRS_GETRELEASELISTFAILED", "Get Release List Failed");
	define("_XF_FRS_GETRELEASEFAILED", "Get Release Failed");
	define("_XF_FRS_GETRELEASEFILESFAILED", "Get Release Files Failed");
	define("_XF_FRS_VERIFYFILEOWNERSHIPFAILED", "Verify File Ownership Failed");
	define("_XF_FRS_VERIFYRELEASEFAILED", "Verify Release Failed");
	define("_XF_FRS_VERIFYPACKAGEFAILED", "Verify Package Failed");
	define("_XF_FRS_VERIFYRELEASENAMEFAILED", "Release Name Already Exists");
	define("_XF_FRS_VERIFYPACKAGENAMEFAILED", "Package Name Already Exists");
	define("_XF_FRS_CANNOTHIDEPACKAGE", "You cannot hide a package that contains active releases.");
	define("_XF_FRS_DELETERELEASEFIRST", "You must delete all of the releases in this package before deleting the package itself.");
	define("_XF_FRS_VERIFYFILEFAILED", "Verify File Failed");
	define("_XF_FRS_VERIFYFILERELEASEFAILED", "Verify File Release Failed");
	define("_XF_FRS_FILEALREADYEXISTS", "File Already Exists");
	define("_XF_FRS_VERIFYPROJECTFAILED", "Verify Project Failed");
	define("_XF_FRS_VERIFYRELEASEDATEFAILED", "Verify Release Date Failed: invalid date format");
	define("_XF_FRS_CREATERELEASEFAILED", "Create Release Failed");
	define("_XF_FRS_CREATEPACKAGEFAILED", "Create Package Failed");
	define("_XF_FRS_SENDNOTICEFAILED", "Send Notice Failed");
	define("_XF_FRS_RESOLVERELEASEFAILED", "Resolve Release Failed");
	define("_XF_FRS_MAKEDIRFAILED", "Failed to make directory");
	define("_XF_FRS_RMDIRFAILED", "Failed to remove directory");
	define("_XF_FRS_CHMODFAILED", "Could not set directory rights.  The directory will be publicly available.");
	 
	// xf/language/%language%/mailmessages.php
	define("_XF_FRS_RELEASE", "Release");
	 
	// xf/include/Group.class
	define("_XF_GRP_GROUPOBJECTALREADYEXISTS", "Group object already exists");
	define("_XF_GRP_COULDNOTCREATEGROUP", "Could not create group");
	define("_XF_GRP_COULDNOTADDADMIN", "Could not add admin to newly created group");
	define("_XF_GRP_COULDNOTGETPERMISSION", "Could not get permission");
	define("_XF_GRP_COULDNOTCHANGEGROUP", "Could not change group properties");
	define("_XF_GRP_INVALIDGROUPNAME", "Invalid Group Name");
	define("_XF_GRP_ERRORUPDATINGPROJECT", "Error updating project information");
	define("_XF_GRP_CHANGEDPUBLICINFO", "Changed Public Info");
	define("_XF_GRP_INVALIDSTATUSCHANGE", "Invalid Status Change");
	define("_XF_GRP_COULDNOTCHANGEGROUPSTATUS", "Could not change group status");
	define("_XF_GRP_NOTADMINTHISGROUP", "You Are Not An Admin For This Group");
	define("_XF_GRP_USERNOTACTIVE", "User is not active. Only active users can be added");
	define("_XF_GRP_COULDNOTADDUSERTOGROUP", "Could Not Add User To Group");
	define("_XF_GRP_ADDEDUSER", "Added User");
	define("_XF_GRP_REMOVEDUSER", "Removed User");
	define("_XF_GRP_APPROVEDPROJECT", "Approved");
	define("_XF_GRP_CANNOTREMOVEADMIN", "Cannot remove admin");
	define("_XF_GRP_USERNOTREMOVED", "User not removed");
	define("_XF_GRP_COULDNOTCHANGEDMEMBER", "Could Not Change Member Permissions");
	define("_XF_GRP_GROUPALREADYACTIVE", "Group already active");
	define("_XF_GRP_GROUPHASNOADMINS", "Group does not have any administrators");
	define("_XF_GRP_COULDNOTCREATEFOUNDRYDATA", "Could not create supplemental community database entries");
	 
	define("_XF_FND_COULDNOTINSERTFOUNDRY", "Could not insert community_data row");
	define("_XF_TSK_TASKADDRESSINVALID", "Task Address Appeared Invalid");
	define("_XF_ART_ERRORCREATINGARTIFACTTYPES", "Error creating ArtifactTypes object");
	 
	// xf/include/Project.class
	define("_XF_PRJ_NOTADMINTHISPROJECT", "You are not an administrator for this project");
	define("_XF_COMM_NOTADMINTHISCOMM", "You are not an administrator for this community");
	define("_XF_PRJ_NOTMEMBEROFPROJECT", "You're not a member of this project");
	 
	// xf/include/Permission.class
	define("_XF_PER_NOVALIDGROUPOBJECT", "No Valid Group Object");
	define("_XF_PER_NOVALIDUSEROBJECT", "No Valid User Object");
	define("_XF_PER_USERNOTFOUND", "User Not Found");
	 
	// xf/include/trove.php
	define("_XF_TRV_NONESELECTED", "None Selected");
	define("_XF_TRV_NONYETCATEGORIZED", "This project has not yet categorized itself in the");
	define("_XF_TRV_NONYETCATEGORIZEDCOMM", "This community has not yet categorized itself in the ");
	define("_XF_TRV_NOWFILTERING", "Now Filtering");
	define("_XF_TRV_FILTER", "Filter");
	 
	// xf/forum/forum_utils.php
	define("_XF_FRM_FORUMADDED", "Forum Added");
	 
	// xf/news/news_utils.php
	define("_XF_NWS_NONEWSITEMSFOUND", "No News Items Found");
	define("_XF_NWS_COMMENT", "Comment");
	define("_XF_NWS_COMMENTS", "Comments");
	define("_XF_NWS_READMORECOMMENT", "Read More/Comment");
	define("_XF_NWS_NEWSARCHIVE", "News archive");
	define("_XF_NWS_SUBMITNEWS", "Submit News");
	 
	// xf/include/vote_function.php
	define("_XF_LOW", "low");
	define("_XF_HIGH", "high");
	define("_XF_SURVEYPRIVACY", "Survey Privacy");
	define("_XF_SURVEYNOTFOUND", "Survey Not Found");
	 
	// Default names for forums,documentation,trackers and stuff like that
	define("_XF_FRM_OPENDISCUSSION", "Open Discussion");
	define("_XF_FRM_OPENDISCUSSIONDESC", "General Discussion");
	define("_XF_FRM_HELP", "Help");
	define("_XF_FRM_HELPDESC", "Get Help");
	define("_XF_FRM_DEVELOPERS", "Developers");
	define("_XF_FRM_DEVELOPERSDESC", "Project Developer Discussion");
	define("_XF_DOC_UNCATEGORIZEDSUBS", "Uncategorized Submissions");
	define("_XF_DOC_DEVELOPER", "Developer");
	define("_XF_DOC_PROJECT", "Project");
	define("_XF_DOC_RELATEDRESOURCES", "Related Resources");
	define("_XF_TRK_BUGS", "Bugs");
	define("_XF_TRK_BUGSDESC", "Bug Tracking System");
	define("_XF_TRK_SUPPORTREQUESTS", "Support Requests");
	define("_XF_TRK_SUPPORTREQUESTSDESC", "Tech Support Tracking System");
	define("_XF_TRK_PATCHES", "Patches");
	define("_XF_TRK_PATCHESDESC", "Patch Tracking System");
	define("_XF_TRK_FEATUREREQUESTS", "Feature Requests");
	define("_XF_TRK_FEATUREREQUESTSDESC", "Feature Request Tracking System");
	 
	// xf/include/html.php
	define("_XF_HTM_LOWEST", "Lowest");
	define("_XF_HTM_MEDIUM", "Medium");
	define("_XF_HTM_HIGHEST", "Highest");
	 
	// xf/include/utils.php
	define("_XF_PRIORITYCOLORS", "Priority Colors");
	 
	// xf/include/pm/pm_data.php
	define("_XF_PM_MISSINGREQPARAMETERS", "Missing Required Parameters");
	define("_XF_PM_ENDDATEMUSTBEGREATER", "End Date Must Be Greater Than Begin Date");
	define("_XF_PM_TASKDOESNOTEXIST", "Task Doesn't Exist In This Subproject");
	define("_XF_PM_CANNOTPUTTASKINOTHERGROUP", "You can not put this task into the subproject of another group.");
	define("_XF_PM_MODIFIEDTASK", "Successfully Modified Task");
	 
	// xf/tracker/include/ArtifactFileHtml.class
	define("_XF_TRK_AFHINVALIDFILENAME", "ArtifactFile: Invalid filename");
	define("_XF_TRK_AFHFILESIZEINCORRECT", "ArtifactFile: File must be > 20 bytes and < 256000 bytes in length");
	 
	// xf/tracker/include/ArtifactTypeHtml.class
	define("_XF_TRK_ATHSUBMITNEW", "Submit New");
	define("_XF_TRK_ATHADMINFUNCTIONS", "Admin Functions");
	define("_XF_TRK_ATHADDBROWSETYPES", "Add/Browse Artifact Types");
	define("_XF_TRK_ATHEDITUPDATEOPTIONS", "Edit/Update Options in: %s"); // %s = name of ArtifactType
	define("_XF_TRK_ATHREQID", "Req. ID");
	define("_XF_TRK_ATHRESOLUTION", "Resolution");
	define("_XF_TRK_ATHIFAPPLYTOALL", "If you wish to apply changes to all items selected above, use these controls to change their properties and click once on 'Mass Update'.");
	define("_XF_TRK_ATHCATEGORY", "Category");
	define("_XF_TRK_ATHGROUP", "Group");
	define("_XF_TRK_ATHSTATUS", "Status");
	define("_XF_TRK_ATHSELECTALL", "Select all items or undo all selections");
	define("_XF_TRK_ATHCANNEDRESP", "Canned Response");
	define("_XF_TRK_ATHMASSUPDATE", "Mass Update");
	 
	// xf/project/admin/project_admin_utils.php
	define("_XF_PRJ_USERPERMISSIONS", "User Permissions");
	define("_XF_PRJ_EDITRELEASEFILES", "Edit Release Files");
	define("_XF_PRJ_EDITPUBLICINFO", "Edit Public Info");
	define("_XF_PRJ_POSTJOBS", "Post Jobs");
	define("_XF_PRJ_EDITJOBS", "Edit Jobs");
	define("_XF_PRJ_NOCHANGESMADETHISGROUP", "No Changes Have Been Made to This Group");
	define("_XF_PRJ_PROJECTHISTORY", "Project History");
	define("_XF_COMM_COMMHISTORY", "Community History");
	 
	// global
	define("_XF_G_NONE", "None");
	define("_XF_G_UNDEFINED", "Undefined");
	define("_XF_G_NOCHANGE", "No change");
	define("_XF_G_PERMISSIONDENIED", "Permission Denied");
	// Months
	define("_XF_G_JANUARY", "January");
	define("_XF_G_FEBRUARY", "February");
	define("_XF_G_MARCH", "March");
	define("_XF_G_APRIL", "April");
	define("_XF_G_MAY", "May");
	define("_XF_G_JUNE", "June");
	define("_XF_G_JULY", "July");
	define("_XF_G_AUGUST", "August");
	define("_XF_G_SEPTEMBER", "September");
	define("_XF_G_OCTOBER", "October");
	define("_XF_G_NOVEMBER", "November");
	define("_XF_G_DECEMBER", "December");
	// Menu Item
	define("_XF_G_SUMMARY", "Summary");
	define("_XF_G_ADMIN", "Admin");
	define("_XF_G_SITEADMIN", "Site Admin Home");
	define("_XF_G_HOMEPAGE", "Homepage");
	define("_XF_G_MEMBERS", "Members");
	define("_XF_G_FORUMS", "Forums");
	define("_XF_G_FAQS", "FAQs");
	define("_XF_G_TRACKERS", "Trackers");
	define("_XF_G_BUGS", "Bugs");
	define("_XF_G_SUPPORT", "Support");
	define("_XF_G_PATCHES", "Patches");
	define("_XF_G_TASKS", "Tasks");
	define("_XF_G_DOCS", "Docs");
	define("_XF_G_LISTS", "Mailing Lists");
	define("_XF_G_SURVEYS", "Surveys");
	define("_XF_G_NEWS", "News");
	define("_XF_G_FILES", "Files");
	define("_XF_G_CVS", "CVS");
	define("_XF_G_SAMPLE", "Sample Code");
	 
	// navigation
	define("_XF_G_PREVIOUS", "Previous");
	define("_XF_G_NEXT", "Next");
	define("_XF_G_BACK", "Back");
	define("_XF_G_REPLY", "Reply");
	define("_XF_G_SUBMIT", "Submit");
	define("_XF_G_BROWSE", "Browse");
	define("_XF_G_CANCEL", "Cancel");
	define("_XF_G_ADD", "Add");
	define("_XF_G_EDIT", "Edit");
	define("_XF_G_DELETE", "Delete");
	define("_XF_G_REMOVE", "Remove");
	define("_XF_G_DELETED", "Deleted");
	define("_XF_G_ANY", "Any");
	define("_XF_G_CHANGE", "Change");
	define("_XF_G_UPDATE", "Update");
	define("_XF_G_ISPUBLIC", "Is Public?");
	 
	define("_XF_G_DATE", "Date");
	define("_XF_G_POSTEDBY", "Posted By");
	define("_XF_G_SUBMITTEDBY", "Submitted By");
	define("_XF_G_SENDER", "Sender");
	define("_XF_G_ASSIGNEDTO", "Assigned To");
	define("_XF_G_UNASSIGNED", "Unassigned");
	define("_XF_G_FOLLOWUPS", "Followups");
	define("_XF_G_NOFOLLOWUPS", "No Followups Have Been Posted");
	define("_XF_G_BY", "By");
	define("_XF_G_FOR", "For");
	define("_XF_G_IN", "In");
	define("_XF_G_DESCRIPTION", "Description");
	define("_XF_G_SUBJECT", "Subject");
	define("_XF_G_COMMENT", "Comment");
	define("_XF_G_NOCOMMENTSADDED", "No Comments Have Been Added");
	define("_XF_G_MESSAGE", "Message");
	define("_XF_G_PROJECT", "Project");
	define("_XF_G_COMM", "Community");
	define("_XF_G_PRIORITY", "Priority");
	define("_XF_G_SEARCH", "Search");
	 
	// History
	define("_XF_G_FIELD", "Field");
	define("_XF_G_PROPERTY", "Property");
	define("_XF_G_OLDVALUE", "Old Value");
	define("_XF_G_VALUE", "Value");
	define("_XF_G_NOCHANGES", "No Changes Have Been Made to This Item");
	 
?>