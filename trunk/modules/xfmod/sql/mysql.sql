# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Host: savannah.provo.novell.com
# Generation Time: Oct 01, 2003 at 01:45 PM
# Server version: 3.23.56
# PHP Version: 4.3.1
# 
# Database : `xoops2`
# 

# --------------------------------------------------------
#INSERT INTO users (uid, name, uname, email, url, user_avatar, user_regdate, user_icq, user_from, user_sig, user_viewemail, actkey, user_aim, user_yim, user_msnm, pass, posts, attachsig, rank, level, theme, timezone_offset, last_login, umode, uorder, notify_method, notify_mode, user_occ, bio, user_intrest, user_mailok) VALUES (100, 'Anonymous', 'none', 'none@none.net', '', 'blank.gif', 0, '', '', '', 0, '', '', '', '', '*********34343', 0, 0, 0, 0, '', '0.0', 0, '', 0, 1, 0, '', '', '', 1);

#
# Table structure for table `xf_activity_log`
#

CREATE TABLE xf_activity_log (
  day int(11) NOT NULL default '0',
  hour int(11) NOT NULL default '0',
  group_id int(11) NOT NULL default '0',
  browser varchar(8) NOT NULL default 'OTHER',
  ver double NOT NULL default '0',
  platform varchar(8) NOT NULL default 'OTHER',
  time int(11) NOT NULL default '0',
  page text,
  type int(11) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_artifact`
#

CREATE TABLE xf_artifact (
  artifact_id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  status_id int(11) NOT NULL default '1',
  category_id int(11) NOT NULL default '100',
  artifact_group_id int(11) NOT NULL default '0',
  resolution_id int(11) NOT NULL default '100',
  priority int(11) NOT NULL default '5',
  submitted_by int(11) NOT NULL default '100',
  assigned_to int(11) NOT NULL default '100',
  open_date int(11) NOT NULL default '0',
  close_date int(11) NOT NULL default '0',
  summary text NOT NULL,
  details text NOT NULL,
  PRIMARY KEY  (artifact_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_artifact_canned_responses`
#

CREATE TABLE xf_artifact_canned_responses (
  id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  title text NOT NULL,
  body text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_artifact_category`
#

CREATE TABLE xf_artifact_category (
  id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  category_name text NOT NULL,
  auto_assign_to int(11) NOT NULL default '100',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_artifact_category (id, group_artifact_id, category_name, auto_assign_to) VALUES (100, 101, 'None', 100);
INSERT INTO xf_artifact_category (id, group_artifact_id, category_name, auto_assign_to) VALUES (1, 101, 'Project Registration Issue', 100);
INSERT INTO xf_artifact_category (id, group_artifact_id, category_name, auto_assign_to) VALUES (2, 101, 'Project Administration', 100);
INSERT INTO xf_artifact_category (id, group_artifact_id, category_name, auto_assign_to) VALUES (3, 101, 'Offtopic', 100);
INSERT INTO xf_artifact_category (id, group_artifact_id, category_name, auto_assign_to) VALUES (4, 103, 'Layout', 100);
INSERT INTO xf_artifact_category (id, group_artifact_id, category_name, auto_assign_to) VALUES (5, 103, 'Project Registration Process', 100);

#
# Table structure for table `xf_artifact_counts_agg`
#

CREATE TABLE xf_artifact_counts_agg (
  group_artifact_id int(11) NOT NULL default '0',
  count int(11) NOT NULL default '0',
  open_count int(11) NOT NULL default '0',
  PRIMARY KEY  (group_artifact_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_artifact_counts_agg (group_artifact_id, count, open_count) VALUES (100, 0, 0);
INSERT INTO xf_artifact_counts_agg (group_artifact_id, count, open_count) VALUES (101, 0, 0);
INSERT INTO xf_artifact_counts_agg (group_artifact_id, count, open_count) VALUES (102, 0, 0);
INSERT INTO xf_artifact_counts_agg (group_artifact_id, count, open_count) VALUES (103, 0, 0);

#
# Table structure for table `xf_artifact_file`
#

CREATE TABLE xf_artifact_file (
  id int(11) NOT NULL auto_increment,
  artifact_id int(11) NOT NULL default '0',
  description text NOT NULL,
  bin_data longtext NOT NULL,
  filename text NOT NULL,
  filesize int(11) NOT NULL default '0',
  filetype text NOT NULL,
  adddate int(11) NOT NULL default '0',
  submitted_by int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_artifact_group`
#

CREATE TABLE xf_artifact_group (
  id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  group_name text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_artifact_group (id, group_artifact_id, group_name) VALUES (100, 100, 'None');

#
# Table structure for table `xf_artifact_group_list`
#

CREATE TABLE xf_artifact_group_list (
  group_artifact_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  name text,
  description text,
  is_public int(11) NOT NULL default '0',
  allow_anon int(11) NOT NULL default '0',
  email_all_updates int(11) NOT NULL default '0',
  email_address text NOT NULL,
  due_period int(11) NOT NULL default '2592000',
  use_resolution int(11) NOT NULL default '0',
  submit_instructions text,
  browse_instructions text,
  datatype int(11) NOT NULL default '0',
  status_timeout int(11) default NULL,
  PRIMARY KEY  (group_artifact_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_artifact_group_list (group_artifact_id, group_id, name, description, is_public, allow_anon, email_all_updates, email_address, due_period, use_resolution, submit_instructions, browse_instructions, datatype, status_timeout) VALUES (100, 100, NULL, NULL, 0, 0, 0, '', 2592000, 0, NULL, NULL, 0, NULL);
INSERT INTO xf_artifact_group_list (group_artifact_id, group_id, name, description, is_public, allow_anon, email_all_updates, email_address, due_period, use_resolution, submit_instructions, browse_instructions, datatype, status_timeout) VALUES (101, 1, 'Support Requests', 'Tech Support Tracking System', 1, 1, 0, '', 2592000, 0, '', '', 0, 1209600);
INSERT INTO xf_artifact_group_list (group_artifact_id, group_id, name, description, is_public, allow_anon, email_all_updates, email_address, due_period, use_resolution, submit_instructions, browse_instructions, datatype, status_timeout) VALUES (102, 1, 'Feature Requests', 'Feature Request Tracking System', 1, 0, 0, '', 2592000, 0, '', '', 0, 1209600);
INSERT INTO xf_artifact_group_list (group_artifact_id, group_id, name, description, is_public, allow_anon, email_all_updates, email_address, due_period, use_resolution, submit_instructions, browse_instructions, datatype, status_timeout) VALUES (103, 1, 'Bug Tracking', 'Bug Tracking System', 1, 1, 0, '', 2592000, 0, '', '', 0, 1209600);

#
# Table structure for table `xf_artifact_history`
#

CREATE TABLE xf_artifact_history (
  id int(11) NOT NULL auto_increment,
  artifact_id int(11) NOT NULL default '0',
  field_name text NOT NULL,
  old_value text NOT NULL,
  mod_by int(11) NOT NULL default '0',
  entrydate int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_artifact_message`
#

CREATE TABLE xf_artifact_message (
  id int(11) NOT NULL auto_increment,
  artifact_id int(11) NOT NULL default '0',
  submitted_by int(11) NOT NULL default '0',
  from_email text NOT NULL,
  adddate int(11) NOT NULL default '0',
  body text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_artifact_monitor`
#

CREATE TABLE xf_artifact_monitor (
  id int(11) NOT NULL auto_increment,
  artifact_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  email text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_artifact_perm`
#

CREATE TABLE xf_artifact_perm (
  id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  perm_level int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_artifact_perm (id, group_artifact_id, user_id, perm_level) VALUES (1, 101, 1, 2);
INSERT INTO xf_artifact_perm (id, group_artifact_id, user_id, perm_level) VALUES (2, 102, 1, 2);
INSERT INTO xf_artifact_perm (id, group_artifact_id, user_id, perm_level) VALUES (3, 103, 1, 2);

#
# Table structure for table `xf_artifact_resolution`
#

CREATE TABLE xf_artifact_resolution (
  id int(11) NOT NULL auto_increment,
  resolution_name text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_artifact_resolution VALUES (100, 'None');
INSERT INTO xf_artifact_resolution VALUES (102, 'Accepted');
INSERT INTO xf_artifact_resolution VALUES (103, 'Out of Date');
INSERT INTO xf_artifact_resolution VALUES (104, 'Postponed');
INSERT INTO xf_artifact_resolution VALUES (105, 'Rejected');

#
# Table structure for table `xf_artifact_status`
#

CREATE TABLE xf_artifact_status (
  id int(11) NOT NULL auto_increment,
  status_name text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_artifact_status VALUES (1, 'Open');
INSERT INTO xf_artifact_status VALUES (2, 'Closed');
INSERT INTO xf_artifact_status VALUES (3, 'Deleted');
INSERT INTO xf_artifact_status VALUES (4, 'Pending');

#
# Table structure for table `xf_canned_responses`
#

CREATE TABLE xf_canned_responses (
  response_id int(11) NOT NULL auto_increment,
  response_title varchar(25) default NULL,
  response_text text,
  PRIMARY KEY  (response_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_context_sensitive_help`
#

CREATE TABLE xf_context_sensitive_help (
  help_id int(11) unsigned NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  for_page varchar(255) NOT NULL default '',
  help_url varchar(255) NOT NULL default '',
  weight int(5) default '0',
  PRIMARY KEY  (help_id),
  KEY for_page (for_page)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_context_sensitive_help VALUES (1, '', '', '', 0);
INSERT INTO xf_context_sensitive_help VALUES (2, 'Submit News?', '/modules/news', '/modules/xfmod/help/news.php#submit', 0);
INSERT INTO xf_context_sensitive_help VALUES (3, 'Use the Help System?', '/modules/xfmod/help', '/modules/xfmod/help/help.php', 0);
INSERT INTO xf_context_sensitive_help VALUES (4, 'Manage My Account?', '/modules/xfaccount', '/modules/xfmod/help/accounts.php#account_administration', 0);
INSERT INTO xf_context_sensitive_help VALUES (5, 'Create A Project?', '/modules/news/index.php', '/modules/xfmod/help/projects.php#creating_projects', 100);
INSERT INTO xf_context_sensitive_help VALUES (6, 'Find A Project?', '/modules/news/index.php', '/modules/xfmod/help/projects.php#finding_projects', 200);
INSERT INTO xf_context_sensitive_help VALUES (7, 'Find A Community?', '/modules/news/index.php', '/modules/xfmod/help/communities.php#finding_communities', 300);
INSERT INTO xf_context_sensitive_help VALUES (8, 'Become a Member of a Project?', '/modules/xfaccount/index.php', '/modules/xfmod/help/projects.php#contributing_to_projects', 100);
INSERT INTO xf_context_sensitive_help VALUES (9, 'Edit My User Profile?', '/modules/xfaccount/index.php', '/modules/xfmod/help/accounts.php#account_administration', 0);
INSERT INTO xf_context_sensitive_help VALUES (10, 'Become a Member of a Community?', '/modules/xfaccount/index.php', '/modules/xfmod/help/communities.php#contributing_to_communities', 200);
INSERT INTO xf_context_sensitive_help VALUES (11, 'Manage Site Mailing List Subscriptions?', '/modules/xfaccount/index.php', '/modules/xfmod/help/accounts.php#account_administration', 400);
INSERT INTO xf_context_sensitive_help VALUES (12, 'Edit My Profile?', '/userinfo.php', '/modules/xfmod/help/accounts.php#account_administration', 0);
INSERT INTO xf_context_sensitive_help VALUES (13, 'Upload My Resume?', '/modules/xfjobs/editprofile.php', '/modules/xfmod/help/jobs.php#managing_your_skills_profile', 0);
INSERT INTO xf_context_sensitive_help VALUES (16, 'Find Jobs?', '/modules/xfjobs', '/modules/xfmod/help/jobs.php', 0);
INSERT INTO xf_context_sensitive_help VALUES (17, 'Create Jobs?', '/modules/xfjobs/index.php', '/modules/xfmod/help/jobs.php#posting_jobs', 0);
INSERT INTO xf_context_sensitive_help VALUES (15, 'Edit My Skills Profile?', '/modules/xfjobs/editprofile.php', '/modules/xfmod/help/jobs.php#managing_your_skills_profile', 200);
INSERT INTO xf_context_sensitive_help VALUES (18, 'Manage My Skills Profile?', '/modules/xfjobs/index.php', '/modules/xfmod/help/jobs.php#managing_your_skills_profile', 100);
INSERT INTO xf_context_sensitive_help VALUES (19, 'Create An Account?', '/modules/xfjobs', '/modules/xfmod/help/accounts.php#creating', 100);
INSERT INTO xf_context_sensitive_help VALUES (20, 'Apply For a Job?', '/modules/xfjobs/viewjob.php', '/modules/xfmod/help/jobs.php#applying_for_jobs', 0);
INSERT INTO xf_context_sensitive_help VALUES (21, 'Edit a Job?', '/modules/xfjobs/viewjob.php', '/modules/xfmod/help/jobs.php#editing_jobs', 100);
INSERT INTO xf_context_sensitive_help VALUES (22, 'Create a Code Snippet?', '/modules/xfsnippet', '/modules/xfmod/help/code_snippets.php#creating_code_snippets', 0);
INSERT INTO xf_context_sensitive_help VALUES (23, 'Find Code Snippets?', '/modules/xfsnippet/index.php', '/modules/xfmod/help/code_snippets.php#viewing_code_snippets', 0);
INSERT INTO xf_context_sensitive_help VALUES (24, 'Create a Code Snippet Package?', '/modules/xfsnippet', '/modules/xfmod/help/code_snippets.php#managing_code_snippets', 100);
INSERT INTO xf_context_sensitive_help VALUES (25, 'Edit a Code Snippet?', '/modules/xfsnippet/detail.php', '/modules/xfmod/help/code_snippets.php#managing_code_snippets', 0);
INSERT INTO xf_context_sensitive_help VALUES (26, 'Create a New Snippet Version?', '/modules/xfsnippet/detail.php', '/modules/xfmod/help/code_snippets.php#creating_code_snippets', 100);
INSERT INTO xf_context_sensitive_help VALUES (27, 'Create A Project?', '/search.php', '/modules/xfmod/help/projects.php#creating_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (28, 'Use FAQs?', '/search.php', '/modules/xfmod/help/faqs.php', 100);
INSERT INTO xf_context_sensitive_help VALUES (29, 'Use News?', '/search.php', '/modules/xfmod/help/news.php', 200);
INSERT INTO xf_context_sensitive_help VALUES (30, 'Use Forums?', '/search.php', '/modules/xfmod/help/forums.php', 150);
INSERT INTO xf_context_sensitive_help VALUES (31, 'Read FAQs?', '/modules/xoopsfaq', '/modules/xfmod/help/faqs.php#viewing_faqs', 0);
INSERT INTO xf_context_sensitive_help VALUES (32, 'Create FAQs?', '/modules/xoopsfaq', '/modules/xfmod/help/faqs.php#administering_faqs', 100);
INSERT INTO xf_context_sensitive_help VALUES (33, 'Read Forums?', '/modules/newbb', '/modules/xfmod/help/forums.php#viewing_forums', 0);
INSERT INTO xf_context_sensitive_help VALUES (34, 'Post to Forums?', '/modules/newbb/viewforum.php', '/modules/xfmod/help/forums.php#posting_to_forums', 0);
INSERT INTO xf_context_sensitive_help VALUES (35, 'Post to Forums?', '/modules/newbb/viewtopic.php', '/modules/xfmod/help/forums.php#posting_to_forums', 0);
INSERT INTO xf_context_sensitive_help VALUES (36, 'Reply to Forum Postings?', '/modules/newbb/viewtopic.php', '/modules/xfmod/help/forums.php#viewing_forums', 100);
INSERT INTO xf_context_sensitive_help VALUES (37, 'Vote In a Poll?', '/modules/xoopspoll', '/modules/xfmod/help/polls.php#voting_in_polls', 0);
INSERT INTO xf_context_sensitive_help VALUES (38, 'Manage My Project?', '/modules/xfmod/project', '/modules/xfmod/help/projects.php#administering_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (39, 'Create a Project?', '/modules/xfmod/project', '/modules/xfmod/help/projects.php#creating_projects', 100);
INSERT INTO xf_context_sensitive_help VALUES (40, 'Create an Account?', '/modules/xfmod/project', '/modules/xfmod/help/accounts.php#creating', 200);
INSERT INTO xf_context_sensitive_help VALUES (41, 'Release Files?', '/modules/xfmod/project/index.php', '/modules/xfmod/help/projects.php#downloading_projects', 200);
INSERT INTO xf_context_sensitive_help VALUES (42, 'Create News?', '/modules/xfmod/project/index.php', '/modules/xfmod/help/news.php#submitting_news', 300);
INSERT INTO xf_context_sensitive_help VALUES (43, 'Change the Community Homepage?', '/modules/xfmod/community/index.php', '/modules/xfmod/help/communities.php#administering_communities', 400);
INSERT INTO xf_context_sensitive_help VALUES (44, 'Release Files?', '/modules/xfmod/project/admin/index.php', '/modules/xfmod/help/projects.php#downloading_projects', 200);
INSERT INTO xf_context_sensitive_help VALUES (45, 'Create Jobs?', '/modules/xfjobs/createjob.php', '/modules/xfmod/help/jobs.php#posting_jobs', 0);
INSERT INTO xf_context_sensitive_help VALUES (46, 'Edit Jobs?', '/modules/xfmod/project/admin/index.php', '/modules/xfmod/help/jobs.php#editing_jobs', 400);
INSERT INTO xf_context_sensitive_help VALUES (47, 'Create Jobs?', '/modules/xfmod/project/admin/index.php', '/modules/xfmod/help/jobs.php#posting_jobs', 300);
INSERT INTO xf_context_sensitive_help VALUES (48, 'Edit Jobs?', '/modules/xfjobs/index.php', '/modules/xfmod/help/jobs.php#editing_jobs', 0);
INSERT INTO xf_context_sensitive_help VALUES (49, 'Receive Job Applications?', '/modules/xfjobs/createjob.php', '/modules/xfmod/help/jobs.php#applying_for_jobs', 100);
INSERT INTO xf_context_sensitive_help VALUES (50, 'Post To Forums?', '/modules/xfmod/forum', '/modules/xfmod/help/forums.php#posting_to_forums', 0);
INSERT INTO xf_context_sensitive_help VALUES (51, 'Create Forums?', '/modules/xfmod/forum', '/modules/xfmod/help/forums.php#creating_forums', 100);
INSERT INTO xf_context_sensitive_help VALUES (52, 'Monitor Forums?', '/modules/xfmod/forum/forum.php', '/modules/xfmod/help/forums.php#monitoring_forums', 0);
INSERT INTO xf_context_sensitive_help VALUES (53, 'Create Forums?', '/modules/xfmod/forum/admin', '/modules/xfmod/help/forums.php#creating_forums', 0);
INSERT INTO xf_context_sensitive_help VALUES (54, 'Link To External Forums?', '/modules/xfmod/forum/admin', '/modules/xfmod/help/forums.php#creating_forums', 100);
INSERT INTO xf_context_sensitive_help VALUES (55, 'Use Project Trackers?', '/modules/xfmod/tracker', '/modules/xfmod/help/projects.php#trackers', 500);
INSERT INTO xf_context_sensitive_help VALUES (56, 'Vote In Surveys?', '/modules/xfmod/survey', '/modules/xfmod/help/polls.php#voting_in_polls', 0);
INSERT INTO xf_context_sensitive_help VALUES (57, 'View Survey Results?', '/modules/xfmod/survey', '/modules/xfmod/help/polls.php#viewing_polls', 0);
INSERT INTO xf_context_sensitive_help VALUES (58, 'Create Surveys?', '/modules/xfmod/survey', '/modules/xfmod/help/polls.php#creating_polls', 200);
INSERT INTO xf_context_sensitive_help VALUES (59, 'Create Surveys?', '/modules/xfmod/survey/admin', '/modules/xfmod/help/polls.php#creating_polls', 0);
INSERT INTO xf_context_sensitive_help VALUES (60, 'Administer Surveys?', '/modules/xfmod/survey/admin', '/modules/xfmod/help/polls.php#administering_polls', 100);
INSERT INTO xf_context_sensitive_help VALUES (61, 'View Survey Results?', '/modules/xfmod/survey/admin', '/modules/xfmod/help/polls.php#viewing_polls', 200);
INSERT INTO xf_context_sensitive_help VALUES (62, 'Submit News?', '/modules/xfmod/news', '/modules/xfmod/help/news.php#submitting_news', 0);
INSERT INTO xf_context_sensitive_help VALUES (63, 'Administer News?', '/modules/xfmod/news', '/modules/xfmod/help/news.php#administering_news', 100);
INSERT INTO xf_context_sensitive_help VALUES (64, 'Administer News?', '/modules/xfmod/news/admin', '/modules/xfmod/help/news.php#administering_news', 0);
INSERT INTO xf_context_sensitive_help VALUES (65, 'Manage A Community?', '/modules/xfmod/community', '/modules/xfmod/help/communities.php#administering_communities', 0);
INSERT INTO xf_context_sensitive_help VALUES (66, 'Create a Community?', '/modules/xfmod/community', '/modules/xfmod/help/communities.php#creating_communities', 100);
INSERT INTO xf_context_sensitive_help VALUES (67, 'Create an Account?', '/modules/xfmod/community', '/modules/xfmod/help/accounts.php#creating', 200);
INSERT INTO xf_context_sensitive_help VALUES (68, 'Browse A Community?', '/modules/xfmod/community/index.php', '/modules/xfmod/help/communities.php#viewing_communities', 0);
INSERT INTO xf_context_sensitive_help VALUES (69, 'Submit News?', '/modules/xfmod/community/index.php', '/modules/xfmod/help/news.php#submitting_news', 100);
INSERT INTO xf_context_sensitive_help VALUES (70, 'Submit Articles?', '/modules/xfmod/community/index.php', '/modules/xfmod/help/communities.php#articles', 200);
INSERT INTO xf_context_sensitive_help VALUES (71, 'Manage Articles?', '/modules/xfmod/community/index.php', '/modules/xfmod/help/communities.php#articles', 300);
INSERT INTO xf_context_sensitive_help VALUES (72, 'Become a Community Maintainer?', '/modules/xfmod/community/index.php', '/modules/xfmod/help/communities.php#contributing_to_communities', 400);
INSERT INTO xf_context_sensitive_help VALUES (73, 'Manage a Community?', '/modules/xfmod/community/admin', '/modules/xfmod/help/communities.php#administering_communities', 0);
INSERT INTO xf_context_sensitive_help VALUES (74, 'Create a Community?', '/modules/xfmod/community/admin', '/modules/xfmod/help/communities.php#creating_communities', 100);
INSERT INTO xf_context_sensitive_help VALUES (75, 'Create an Account?', '/modules/xfmod/community/admin', '/modules/xfmod/help/accounts.php#creating', 200);
INSERT INTO xf_context_sensitive_help VALUES (76, 'Edit Jobs?', '/modules/xfmod/community/admin/index.php', '/modules/xfmod/help/jobs.php#editing_jobs', 400);
INSERT INTO xf_context_sensitive_help VALUES (77, 'Create Jobs?', '/modules/xfmod/community/admin/index.php', '/modules/xfmod/help/jobs.php#posting_jobs', 300);
INSERT INTO xf_context_sensitive_help VALUES (78, 'Read FAQs?', '/modules/xfmod/faqs', '/modules/xfmod/help/faqs.php#viewing_faqs', 0);
INSERT INTO xf_context_sensitive_help VALUES (79, 'Create FAQs?', '/modules/xfmod/faqs', '/modules/xfmod/help/faqs.php#administering_faqs', 100);
INSERT INTO xf_context_sensitive_help VALUES (80, 'Administer FAQs?', '/modules/xfmod/faqs', '/modules/xfmod/help/faqs.php#administering_faqs', 200);
INSERT INTO xf_context_sensitive_help VALUES (81, 'Create FAQs?', '/modules/xfmod/faqs/admin', '/modules/xfmod/help/faqs.php#administering_faqs', 0);
INSERT INTO xf_context_sensitive_help VALUES (82, 'Administer FAQs?', '/modules/xfmod/faqs/admin', '/modules/xfmod/help/faqs.php#administering_faqs', 200);
INSERT INTO xf_context_sensitive_help VALUES (83, 'Link to Existing FAQs?', '/modules/xfmod/faqs/admin', '/modules/xfmod/help/faqs.php#administering_faqs', 100);
INSERT INTO xf_context_sensitive_help VALUES (84, 'View Another User\'s Profile?', '/userinfo.php', '/modules/xfmod/help/accounts.php#account_administration', 100);
INSERT INTO xf_context_sensitive_help VALUES (85, 'Create Diary Entries?', '/modules/xfaccount/diary.php', '/modules/xfmod/help/accounts.php#account_administration', 0);
INSERT INTO xf_context_sensitive_help VALUES (86, 'Make Diary Entries Public?', '/modules/xfaccount/diary.php', '/modules/xfmod/help/accounts.php#account_administration', 100);
INSERT INTO xf_context_sensitive_help VALUES (87, 'Read Messages?', '/userinfo.php', '/modules/xfmod/help/accounts.php#account_administration', 200);
INSERT INTO xf_context_sensitive_help VALUES (88, 'Send Messages?', '/userinfo.php', '/modules/xfmod/help/accounts.php#account_administration', 300);
INSERT INTO xf_context_sensitive_help VALUES (89, 'View Another User\'s Profile?', '/viewpmsg.php', '/modules/xfmod/help/accounts.php#account_administration', 100);
INSERT INTO xf_context_sensitive_help VALUES (90, 'Read Messages?', '/viewpmsg.php', '/modules/xfmod/help/accounts.php#account_administration', 200);
INSERT INTO xf_context_sensitive_help VALUES (91, 'Send Messages?', '/viewpmsg.php', '/modules/xfmod/help/accounts.php#account_administration', 300);
INSERT INTO xf_context_sensitive_help VALUES (92, 'Create A Signature?', '/userinfo.php', '/modules/xfmod/help/accounts.php#account_administration', 500);
INSERT INTO xf_context_sensitive_help VALUES (93, 'Edit My Profile?', '/edituser.php', '/modules/xfmod/help/accounts.php#account_administration', 0);
INSERT INTO xf_context_sensitive_help VALUES (94, 'Create a Signature?', '/edituser.php', '/modules/xfmod/help/accounts.php#account_administration', 100);
INSERT INTO xf_context_sensitive_help VALUES (95, 'Change My Password?', '/edituser.php', '/modules/xfmod/help/accounts.php#account_administration', 200);
INSERT INTO xf_context_sensitive_help VALUES (96, 'Change My E-Mail Address?', '/edituser.php', '/modules/xfmod/help/accounts.php#account_administration', 300);
INSERT INTO xf_context_sensitive_help VALUES (97, 'Create an Account?', '/modules/xfnewproject', '/modules/xfmod/help/accounts.php#creating', 0);
INSERT INTO xf_context_sensitive_help VALUES (98, 'Learn More About Open Source?', '/modules/xfnewproject', 'http://www.opensource.org/', 100);
INSERT INTO xf_context_sensitive_help VALUES (99, 'Ask About the Terms of Service?', '/modules/xfnewproject/tos.php', '/modules/xfmod/help/projects.php#creating_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (100, 'Choose a License?', '/modules/xfnewproject/projectinfo.php', '/modules/xfmod/help/projects.php#creating_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (101, 'Determine If I Need CVS?', '/modules/xfnewproject/projectinfo.php', '/modules/xfmod/help/projects.php#cvs', 100);
INSERT INTO xf_context_sensitive_help VALUES (102, 'Determine If I Want Anonymous CVS?', '/modules/xfnewproject/projectinfo.php', '/modules/xfmod/help/projects.php#cvs', 200);
INSERT INTO xf_context_sensitive_help VALUES (103, 'Know Whether My Project Is Approved?', '/modules/xfnewproject/projectinfo.php', '/modules/xfmod/help/projects.php#creating_projects', 300);
INSERT INTO xf_context_sensitive_help VALUES (104, 'Suggest a Snippet Language?', '/modules/xfsnippet/index.php', '/modules/xfmod/help/code_snippets.php#snippet_suggestions', 0);
INSERT INTO xf_context_sensitive_help VALUES (105, 'Suggest a Snippet Category?', '/modules/xfsnippet/index.php', '/modules/xfmod/help/code_snippets.php#snippet_suggestions', 100);
INSERT INTO xf_context_sensitive_help VALUES (106, 'Suggest a Snippet Script Type?', '/modules/xfsnippet/index.php', '/modules/xfmod/help/code_snippets.php#snippet_suggestions', 200);
INSERT INTO xf_context_sensitive_help VALUES (107, 'Suggest a Snippet Language?', '/modules/xfsnippet/submit.php', '/modules/xfmod/help/code_snippets.php#snippet_suggestions', 0);
INSERT INTO xf_context_sensitive_help VALUES (108, 'Suggest a Snippet Category?', '/modules/xfsnippet/submit.php', '/modules/xfmod/help/code_snippets.php#snippet_suggestions', 100);
INSERT INTO xf_context_sensitive_help VALUES (109, 'Suggest a Snippet Script Type?', '/modules/xfsnippet/submit.php', '/modules/xfmod/help/code_snippets.php#snippet_suggestions', 200);
INSERT INTO xf_context_sensitive_help VALUES (110, 'Choose a License?', '/modules/xfsnippet/submit.php', '/modules/xfmod/help/code_snippets.php#creating_code_snippets', 300);
INSERT INTO xf_context_sensitive_help VALUES (111, 'Add Snippets to a Package?', '/modules/xfsnippet/package.php', '/modules/xfmod/help/code_snippets.php#managing_code_snippets', 0);
INSERT INTO xf_context_sensitive_help VALUES (112, 'Create a New Snippet Version?', '/modules/xfsnippet', '/modules/xfmod/help/code_snippets.php#managing_code_snippets', 300);
INSERT INTO xf_context_sensitive_help VALUES (113, 'Create a New Snippet Package Version?', '/modules/xfsnippet', '/modules/xfmod/help/code_snippets.php#managing_code_snippets', 400);
INSERT INTO xf_context_sensitive_help VALUES (114, 'Create Forums?', '/modules/newbb', '/modules/xfmod/help/forums.php#creating_forums', 100);
INSERT INTO xf_context_sensitive_help VALUES (115, 'View a Poster\'s Profile?', '/modules/newbb/viewtopic.php', '/modules/xfmod/help/forums.php#viewing_forums', 200);
INSERT INTO xf_context_sensitive_help VALUES (116, 'Send a Message to a Poster?', '/modules/newbb/viewtopic.php', '/modules/xfmod/help/forums.php#viewing_forums', 300);
INSERT INTO xf_context_sensitive_help VALUES (117, 'Edit a Forum Posting?', '/modules/newbb/viewtopic.php', '/modules/xfmod/help/forums.php#viewing_forums', 400);
INSERT INTO xf_context_sensitive_help VALUES (118, 'Create a Poll?', '/modules/xoopspoll', '/modules/xfmod/help/polls.php#creating_polls', 200);
INSERT INTO xf_context_sensitive_help VALUES (119, 'View Poll Results?', '/modules/xoopspoll/index.php', '/modules/xfmod/help/polls.php#viewing_polls', 0);
INSERT INTO xf_context_sensitive_help VALUES (120, 'Comment on a Poll?', '/modules/xoopspoll/pollresults.php', '/modules/xfmod/help/polls.php#viewing_polls', 0);
INSERT INTO xf_context_sensitive_help VALUES (121, 'Contact Poll Participants?', '/modules/xoopspoll/pollresults.php', '/modules/xfmod/help/polls.php#poll_privacy', 100);
INSERT INTO xf_context_sensitive_help VALUES (122, 'Find Projects by Category?', '/modules/xftrove', '/modules/xfmod/help/software_map.php#map_view', 0);
INSERT INTO xf_context_sensitive_help VALUES (123, 'Find Projects by Name?', '/modules/xftrove', '/modules/xfmod/help/software_map.php#list_view', 100);
INSERT INTO xf_context_sensitive_help VALUES (124, 'Categorize My Project?', '/modules/xftrove/trove_list.php', '/modules/xfmod/help/software_map.php#categorizing_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (125, 'Categorize My Project?', '/modules/xfmod/project/index.php', '/modules/xfmod/help/software_map.php#categorizing_projects', 300);
INSERT INTO xf_context_sensitive_help VALUES (126, 'Categorize My Project?', '/modules/xfmod/project/admin/index.php', '/modules/xfmod/help/software_map.php#categorizing_projects', 200);
INSERT INTO xf_context_sensitive_help VALUES (127, 'Add Users To My Project?', '/modules/xfmod/project/admin/index.php', '/modules/xfmod/help/projects.php#administering_projects', 500);
INSERT INTO xf_context_sensitive_help VALUES (128, 'Determine If I Need CVS?', '/modules/xfmod/project/admin/index.php', '/modules/xfmod/help/projects.php#cvs', 600);
INSERT INTO xf_context_sensitive_help VALUES (129, 'Determine If I Want Anonymous CVS?', '/modules/xfmod/project/admin/index.php', '/modules/xfmod/help/projects.php#cvs', 700);
INSERT INTO xf_context_sensitive_help VALUES (130, 'Manage User Permissions?', '/modules/xfmod/project/admin/userperms.php', '/modules/xfmod/help/projects.php#administering_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (131, 'Manage User Permissions?', '/modules/xfmod/project/admin/userpermedit.php', '/modules/xfmod/help/projects.php#administering_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (132, 'Create a File Release Package?', '/modules/xfmod/project/admin/editpackages.php', '/modules/xfmod/help/projects.php#downloading_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (133, 'Create A File Release?', '/modules/xfmod/project/admin/editpackages.php', '/modules/xfmod/help/projects.php#downloading_projects', 100);
INSERT INTO xf_context_sensitive_help VALUES (134, 'Add Files to a Release?', '/modules/xfmod/project/admin/editpackages.php', '/modules/xfmod/help/projects.php#downloading_projects', 200);
INSERT INTO xf_context_sensitive_help VALUES (135, 'Create A File Release?', '/modules/xfmod/project/admin/editreleases.php', '/modules/xfmod/help/projects.php#downloading_projects', 100);
INSERT INTO xf_context_sensitive_help VALUES (136, 'Add Files to a Release?', '/modules/xfmod/project/admin/editreleases.php', '/modules/xfmod/help/projects.php#downloading_projects', 200);
INSERT INTO xf_context_sensitive_help VALUES (137, 'Create a File Release Package?', '/modules/xfmod/project/showfiles.php', '/modules/xfmod/help/projects.php#downloading_projects', 0);
INSERT INTO xf_context_sensitive_help VALUES (138, 'Create A File Release?', '/modules/xfmod/project/showfiles.php', '/modules/xfmod/help/projects.php#downloading_projects', 100);
INSERT INTO xf_context_sensitive_help VALUES (139, 'Add Files to a Release?', '/modules/xfmod/project/showfiles.php', '/modules/xfmod/help/projects.php#downloading_projects', 200);
INSERT INTO xf_context_sensitive_help VALUES (140, 'Create a Tracker?', '/modules/xfmod/tracker', '/modules/xfmod/help/projects.php#trackers', 0);
INSERT INTO xf_context_sensitive_help VALUES (141, 'Disable a Tracker?', '/modules/xfmod/tracker', '/modules/xfmod/help/projects.php#trackers', 100);
INSERT INTO xf_context_sensitive_help VALUES (142, 'Modify a Tracker?', '/modules/xfmod/tracker', '/modules/xfmod/help/projects.php#trackers', 200);
INSERT INTO xf_context_sensitive_help VALUES (143, 'Create a Tracker?', '/modules/xfmod/tracker/admin', '/modules/xfmod/help/projects.php#trackers', 0);
INSERT INTO xf_context_sensitive_help VALUES (144, 'Disable a Tracker?', '/modules/xfmod/tracker/admin', '/modules/xfmod/help/projects.php#trackers', 100);
INSERT INTO xf_context_sensitive_help VALUES (145, 'Modify a Tracker?', '/modules/xfmod/tracker/admin', '/modules/xfmod/help/projects.php#trackers', 200);
INSERT INTO xf_context_sensitive_help VALUES (146, 'Submit a New Tracker Item?', '/modules/xfmod/tracker/index.php', '/modules/xfmod/help/projects.php#trackers', 0);
INSERT INTO xf_context_sensitive_help VALUES (147, 'Assign a Tracker Item to a User?', '/modules/xfmod/tracker/index.php', '/modules/xfmod/help/projects.php#trackers', 100);
INSERT INTO xf_context_sensitive_help VALUES (149, 'Administer Trackers?', '/modules/xfmod/tracker/index.php', '/modules/xfmod/help/projects.php#trackers', 200);
INSERT INTO xf_context_sensitive_help VALUES (150, 'Subscribe to a Mailing List?', '/modules/xfmod/maillist', '/modules/xfmod/help/projects.php#mailing_lists', 0);
INSERT INTO xf_context_sensitive_help VALUES (151, 'Send Mail to a Mailing List?', '/modules/xfmod/maillist', '/modules/xfmod/help/projects.php#mailing_lists', 100);
INSERT INTO xf_context_sensitive_help VALUES (152, 'Administer a Mailing List?', '/modules/xfmod/maillist', '/modules/xfmod/help/projects.php#mailing_lists', 200);
INSERT INTO xf_context_sensitive_help VALUES (153, 'Create a Mailing List?', '/modules/xfmod/maillist', '/modules/xfmod/help/projects.php#mailing_lists', 300);
INSERT INTO xf_context_sensitive_help VALUES (154, 'Delete a Mailing List?', '/modules/xfmod/maillist', '/modules/xfmod/help/projects.php#mailing_lists', 400);
INSERT INTO xf_context_sensitive_help VALUES (155, 'Administer a Mailing List?', '/modules/xfmod/maillist/mlbrowse.php/mailman/admin', '/modules/xfmod/help/projects.php#mailing_lists', 0);
INSERT INTO xf_context_sensitive_help VALUES (156, 'Enable Anonymous CVS Access?', '/modules/xfmod/cvs/cvspage.php', '/modules/xfmod/help/projects.php#cvs', 0);
INSERT INTO xf_context_sensitive_help VALUES (157, 'Configure My CVS Client?', '/modules/xfmod/cvs/cvspage.php', '/modules/xfmod/help/projects.php#cvs', 100);
INSERT INTO xf_context_sensitive_help VALUES (158, 'Learn More About CVS?', '/modules/xfmod/cvs/cvspage.php', 'http://www.cvshome.org/docs/manual/', 200);
INSERT INTO xf_context_sensitive_help VALUES (159, 'Learn More About CVS?', '/modules/xfmod/cvs/cvsbrowse.php', 'http://www.cvshome.org/docs/manual/', 0);
INSERT INTO xf_context_sensitive_help VALUES (160, 'Add My Project to a Community?', '/modules/xfmod/community/index.php', '/modules/xfmod/help/software_map.php#categorizing_projects', 800);
INSERT INTO xf_context_sensitive_help VALUES (161, 'Find Sample Code?', '/modules/news/index.php', '/modules/xfmod/help/code_snippets.php#viewing_code_snippets', 500);
INSERT INTO xf_context_sensitive_help VALUES (162, '', '', '/modules/xfmod/help/news.php#submitting_news', 0);
INSERT INTO xf_context_sensitive_help VALUES (163, 'Start a New Topic?', '/modules/news/submit.php', '/modules/xfmod/help/news.php#submitting_news', 100);
INSERT INTO xf_context_sensitive_help VALUES (164, 'Change My Project\'s License?', '/modules/xfnewproject/projectinfo.php', '/modules/xfmod/help/projects.php#creating_projects', 50);
INSERT INTO xf_context_sensitive_help VALUES (165, 'Change My Project\'s License?', '/modules/xfmod/project/admin', '/modules/xfmod/help/projects.php#creating_projects', 400);

#
# Table structure for table `xf_config`
#

CREATE TABLE xf_config (
  name varchar(20) NOT NULL,
  value varchar(255) NOT NULL default '',
  PRIMARY KEY  (name)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_config VALUES ('manapprove', '1');
INSERT INTO xf_config VALUES ('devsurvey', '100');
INSERT INTO xf_config VALUES ('noreply', 'noreply@forge.novell.com');
INSERT INTO xf_config VALUES ('defaultproject', '0');
INSERT INTO xf_config VALUES ('usemailer', '0');
INSERT INTO xf_config VALUES ('parammail1', 'localhost');
INSERT INTO xf_config VALUES ('parammail2', '');
INSERT INTO xf_config VALUES ('sysnews', '2');
INSERT INTO xf_config VALUES ('virusscan', '0');
INSERT INTO xf_config VALUES ('snippetowner', '0');
INSERT INTO xf_config VALUES ('ftp_server', 'localhost');
INSERT INTO xf_config VALUES ('ftp_internal_server', '');
INSERT INTO xf_config VALUES ('ftp_prefix', 'pub');
INSERT INTO xf_config VALUES ('ftp_path', '/var/ftp/pub');
INSERT INTO xf_config VALUES ('ftp_user', 'username');
INSERT INTO xf_config VALUES ('ftp_password', 'password');
INSERT INTO xf_config VALUES ('validate_email', '1');
INSERT INTO xf_config VALUES ('forum_type','newsportal');
INSERT INTO xf_config VALUES ('nntp_server', 'forum.novell.com');
INSERT INTO xf_config VALUES ('nntp_base', 'novell.forge');
INSERT INTO xf_config VALUES ('max_forums', 5);
INSERT INTO xf_config VALUES ('privkey_path', '/etc/ssl/.private/rsapriv.pem');
INSERT INTO xf_config VALUES ('openssl_path', '/usr/bin/openssl');
INSERT INTO xf_config VALUES ('uuencode_path', '/usr/bin/uuencode');
INSERT INTO xf_config VALUES ('max_maillists', 5);

#
# Table structure for table `xf_cronjob_log`
#

CREATE TABLE xf_cronjob_log (
  cronjob_log_id int(11) NOT NULL auto_increment,
  updatetime int(11) NOT NULL default '0',
  PRIMARY KEY  (cronjob_log_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_cvs_commit_notify`
#

CREATE TABLE xf_cvs_commit_notify (
  group_id int(11) NOT NULL default '0',
  time int(11) NOT NULL default '0',
  email varchar(40) NOT NULL default '',
  KEY group_id (group_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_cvs_commit_tracker`
#

CREATE TABLE xf_cvs_commit_tracker (
  commit_id int(11) NOT NULL auto_increment,
  unix_group_name varchar(30) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  count int(11) NOT NULL default '0',
  PRIMARY KEY  (commit_id),
  KEY unix_group_name (unix_group_name),
  KEY user_id (user_id),
  KEY unix_group_name_user_id (unix_group_name,user_id)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Table structure for table `xf_doc_data`
#

CREATE TABLE xf_doc_data (
  docid int(11) NOT NULL auto_increment,
  stateid int(11) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  data text NOT NULL,
  updatedate int(11) NOT NULL default '0',
  createdate int(11) NOT NULL default '0',
  created_by int(11) NOT NULL default '0',
  doc_group int(11) NOT NULL default '0',
  description text,
  PRIMARY KEY  (docid)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_doc_dl_stats`
#

CREATE TABLE xf_doc_dl_stats (
  id int(11) NOT NULL auto_increment,
  docid int(11) NOT NULL default '0',
  downloads int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY docid (docid)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_doc_feedback`
#

CREATE TABLE xf_doc_feedback (
  feedback_id int(11) NOT NULL auto_increment,
  docid int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  answer int(1) NOT NULL default '0',
  suggestion text NOT NULL,
  entered int(11) NOT NULL default '0',
  PRIMARY KEY  (feedback_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_doc_feedback_agg`
#

CREATE TABLE xf_doc_feedback_agg (
  docid int(11) NOT NULL default '0',
  answer_yes int(11) NOT NULL default '0',
  answer_no int(11) NOT NULL default '0',
  answer_na int(11) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_doc_groups`
#

CREATE TABLE xf_doc_groups (
  doc_group int(11) NOT NULL auto_increment,
  groupname varchar(255) NOT NULL default '',
  group_id int(11) NOT NULL default '0',
  PRIMARY KEY  (doc_group)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_doc_groups (doc_group, groupname, group_id) VALUES (1, 'Uncategorized Submissions', 1);

#
# Table structure for table `xf_doc_states`
#

CREATE TABLE xf_doc_states (
  stateid int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (stateid)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_doc_states VALUES (1, 'active');
INSERT INTO xf_doc_states VALUES (2, 'deleted');
INSERT INTO xf_doc_states VALUES (3, 'pending');
INSERT INTO xf_doc_states VALUES (4, 'hidden');
INSERT INTO xf_doc_states VALUES (5, 'private');

#
# Table structure for table `xf_filemodule_monitor`
#

CREATE TABLE xf_filemodule_monitor (
  id int(11) NOT NULL auto_increment,
  filemodule_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_forum`
#

CREATE TABLE xf_forum (
  msg_id int(11) NOT NULL auto_increment,
  group_forum_id int(11) NOT NULL default '0',
  posted_by int(11) NOT NULL default '0',
  subject text NOT NULL,
  body text NOT NULL,
  date int(11) NOT NULL default '0',
  is_followup_to int(11) NOT NULL default '0',
  thread_id int(11) NOT NULL default '0',
  has_followups int(11) default '0',
  most_recent_date int(11) NOT NULL default '0',
  PRIMARY KEY  (msg_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_forum (msg_id, group_forum_id, posted_by, subject, body, date, is_followup_to, thread_id, has_followups, most_recent_date) VALUES (1, 1, 1, 'Welcome to myXoopsForge Open Discussion', 'Welcome to myXoopsForge Open Discussion', 1017315660, 0, 1, 0, 0);

#
# Table structure for table `xf_forum_agg_msg_count`
#

CREATE TABLE xf_forum_agg_msg_count (
  group_forum_id int(11) NOT NULL default '0',
  count int(11) NOT NULL default '0',
  PRIMARY KEY  (group_forum_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_forum_ext_group_list`
#

CREATE TABLE xf_forum_ext_group_list (
  forum_id int(5) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  forum_name varchar(40) NOT NULL default '',
  forum_url varchar(128) NOT NULL default '',
  PRIMARY KEY  (forum_id),
  KEY indexgroupid (group_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_forum_nntp_list`
#

CREATE TABLE xf_forum_nntp_list (
  group_id int(11) NOT NULL default '0',
  forum_name varchar(60) NOT NULL default '',
  forum_desc_name varchar(128) NOT NULL default '',
  KEY  indexforumname (forum_name),
  KEY indexgroupid (group_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_forum_group_list`
#

CREATE TABLE xf_forum_group_list (
  group_forum_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  forum_name text NOT NULL,
  is_public int(11) NOT NULL default '0',
  description text,
  allow_anonymous int(11) NOT NULL default '0',
  send_all_posts_to text,
  PRIMARY KEY  (group_forum_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_forum_group_list (group_forum_id, group_id, forum_name, is_public, description, allow_anonymous, send_all_posts_to) VALUES (1, 1, 'myXoopsForge Open Discussion', 1, 'Discussion of myXoopsForge Topics.', 0, NULL);
INSERT INTO xf_forum_group_list (group_forum_id, group_id, forum_name, is_public, description, allow_anonymous, send_all_posts_to) VALUES (2, 2, 'myXoopsForge Launched', 1, '', 0, NULL);

#
# Table structure for table `xf_forum_monitored_forums`
#

CREATE TABLE xf_forum_monitored_forums (
  monitor_id int(11) NOT NULL auto_increment,
  forum_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  PRIMARY KEY  (monitor_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_forum_thread_id`
#

CREATE TABLE xf_forum_thread_id (
  thread_id int(11) NOT NULL auto_increment,
  PRIMARY KEY  (thread_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_forum_thread_id (thread_id) VALUES (1);

#
# Table structure for table `xf_foundry_data`
#

CREATE TABLE xf_foundry_data (
  foundry_id int(11) NOT NULL auto_increment,
  freeform1_html text,
  freeform2_html text,
  sponsor1_html text,
  sponsor2_html text,
  guide_image_id int(11) NOT NULL default '0',
  logo_image_id int(11) NOT NULL default '0',
  trove_categories text,
  PRIMARY KEY  (foundry_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_foundry_faqs`
#

CREATE TABLE xf_foundry_faqs (
  id int(11) NOT NULL auto_increment,
  foundry_id int(11) NOT NULL default '0',
  category_id tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY indexfoundryid (foundry_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_foundry_featured_projects`
#

CREATE TABLE xf_foundry_featured_projects (
  foundry_id int(11) NOT NULL default '0',
  project_id int(11) NOT NULL default '0',
  description varchar(255) NOT NULL default '',
  KEY foundry_id_x (foundry_id),
  KEY project_id_x (project_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_foundry_news`
#

CREATE TABLE xf_foundry_news (
  foundry_news_id int(11) NOT NULL auto_increment,
  foundry_id int(11) NOT NULL default '0',
  news_id int(11) NOT NULL default '0',
  approve_date int(11) NOT NULL default '0',
  is_approved int(11) NOT NULL default '0',
  PRIMARY KEY  (foundry_news_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_foundry_projects`
#

CREATE TABLE xf_foundry_projects (
  id int(11) NOT NULL auto_increment,
  foundry_id int(11) NOT NULL default '0',
  project_id int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_frs_dlnames`
#

CREATE TABLE xf_frs_dlnames (
  fileid int(11) NOT NULL default '0',
  uid int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  KEY fileid_index (fileid)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_frs_dlstats_file_agg`
#

CREATE TABLE xf_frs_dlstats_file_agg (
  month int(11) default '1',
  day int(11) default '1',
  file_id int(11) NOT NULL default '0',
  downloads int(11) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_frs_file`
#

CREATE TABLE xf_frs_file (
  file_id int(11) NOT NULL auto_increment,
  filename varchar(255) NOT NULL default '',
  file_url varchar(255) NOT NULL default '',
  release_id int(11) NOT NULL default '0',
  type_id int(11) NOT NULL default '0',
  processor_id int(11) NOT NULL default '0',
  release_time int(11) NOT NULL default '0',
  file_size int(11) NOT NULL default '0',
  post_date int(11) NOT NULL default '0',
  PRIMARY KEY  (file_id),
  KEY release_id (release_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_frs_filetype`
#

CREATE TABLE xf_frs_filetype (
  type_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (type_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_frs_filetype VALUES (1000, '.deb');
INSERT INTO xf_frs_filetype VALUES (2000, '.rpm');
INSERT INTO xf_frs_filetype VALUES (3000, '.zip');
INSERT INTO xf_frs_filetype VALUES (3100, '.bz2');
INSERT INTO xf_frs_filetype VALUES (3110, '.gz');
INSERT INTO xf_frs_filetype VALUES (5000, 'Source .zip');
INSERT INTO xf_frs_filetype VALUES (5010, 'Source .bz2');
INSERT INTO xf_frs_filetype VALUES (5020, 'Source .gz');
INSERT INTO xf_frs_filetype VALUES (5100, 'Source .rpm');
INSERT INTO xf_frs_filetype VALUES (5900, 'Other Source File');
INSERT INTO xf_frs_filetype VALUES (8000, '.jpg');
INSERT INTO xf_frs_filetype VALUES (8100, 'text');
INSERT INTO xf_frs_filetype VALUES (8200, 'html');
INSERT INTO xf_frs_filetype VALUES (8300, 'pdf');
INSERT INTO xf_frs_filetype VALUES (9999, 'Other');
INSERT INTO xf_frs_filetype VALUES (6000, 'Script .php');
INSERT INTO xf_frs_filetype VALUES (6010, 'Script .asp');
INSERT INTO xf_frs_filetype VALUES (3010, '.rar');
INSERT INTO xf_frs_filetype VALUES (6100, 'Script .js');

#
# Table structure for table `xf_frs_package`
#

CREATE TABLE xf_frs_package (
  package_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  status_id int(11) NOT NULL default '0',
  PRIMARY KEY  (package_id),
  KEY group_id (group_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_frs_processor`
#

CREATE TABLE xf_frs_processor (
  processor_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (processor_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_frs_processor VALUES (1000, 'i386');
INSERT INTO xf_frs_processor VALUES (6000, 'IA64');
INSERT INTO xf_frs_processor VALUES (7000, 'Alpha');
INSERT INTO xf_frs_processor VALUES (8000, 'Any');
INSERT INTO xf_frs_processor VALUES (2000, 'PPC');
INSERT INTO xf_frs_processor VALUES (3000, 'MIPS');
INSERT INTO xf_frs_processor VALUES (4000, 'Sparc');
INSERT INTO xf_frs_processor VALUES (5000, 'UltraSparc');
INSERT INTO xf_frs_processor VALUES (9999, 'Other');

#
# Table structure for table `xf_frs_release`
#

CREATE TABLE xf_frs_release (
  release_id int(11) NOT NULL auto_increment,
  package_id int(11) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  notes text NOT NULL,
  changes text NOT NULL,
  status_id int(11) NOT NULL default '0',
  preformatted int(11) NOT NULL default '0',
  release_date int(11) NOT NULL default '0',
  released_by int(11) NOT NULL default '0',
  dependencies text NOT NULL,
  PRIMARY KEY  (release_id),
  KEY package_id (package_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_frs_status`
#

CREATE TABLE xf_frs_status (
  status_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (status_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_frs_status VALUES (1, 'Development');
INSERT INTO xf_frs_status VALUES (2, 'Stable');
INSERT INTO xf_frs_status VALUES (3, 'Private');

#
# Table structure for table `xf_frs_target`
#

CREATE TABLE xoops_xf_frs_target (
  file_id int(11) NOT NULL default '0',
  target varchar(20) NOT NULL default '',
  KEY file_id (file_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_group_history`
#

CREATE TABLE xf_group_history (
  group_history_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  field_name text NOT NULL,
  old_value text NOT NULL,
  mod_by int(11) NOT NULL default '0',
  date int(11) default NULL,
  PRIMARY KEY  (group_history_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_group_type`
#

CREATE TABLE xf_group_type (
  type_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (type_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_groups`
#

CREATE TABLE xf_groups (
  group_id int(11) unsigned NOT NULL auto_increment,
  group_name varchar(60) NOT NULL default '',
  homepage varchar(128) NOT NULL default '',
  is_public int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'A',
  unix_group_name varchar(30) NOT NULL default '',
  unix_box varchar(20) NOT NULL default 'shell1',
  http_domain varchar(80) NOT NULL default '',
  short_description text NOT NULL,
  license varchar(16) NOT NULL default '',
  register_purpose text NOT NULL,
  license_other text NOT NULL,
  register_time int(11) NOT NULL default '0',
  use_bugs int(11) NOT NULL default '1',
  rand_hash text NOT NULL,
  use_mail int(11) NOT NULL default '1',
  use_survey int(11) NOT NULL default '1',
  use_patch int(11) NOT NULL default '1',
  use_forum int(11) NOT NULL default '1',
  use_faq int(11) NOT NULL default '1',
  use_pm int(11) NOT NULL default '1',
  use_cvs int(11) NOT NULL default '1',
  use_news int(11) NOT NULL default '1',
  use_support int(11) NOT NULL default '1',
  anon_cvs int(11) NOT NULL default '1',
  new_bug_address text NOT NULL,
  new_patch_address text NOT NULL,
  new_support_address text NOT NULL,
  type int(11) NOT NULL default '1',
  use_docman int(11) NOT NULL default '1',
  send_all_bugs int(11) NOT NULL default '0',
  send_all_patches int(11) NOT NULL default '0',
  send_all_support int(11) NOT NULL default '0',
  new_task_address text NOT NULL,
  send_all_tasks int(11) NOT NULL default '0',
  use_bug_depend_box int(11) NOT NULL default '1',
  use_pm_depend_box int(11) NOT NULL default '1',
  use_sample int(11) NOT NULL default '1',
  use_tracker int(11) NOT NULL default '1',
  PRIMARY KEY  (group_id),
  UNIQUE KEY unix_group_name (unix_group_name),
  KEY status_index (status)
) TYPE=MyISAM;
# --------------------------------------------------------

INSERT INTO xf_groups (group_id, group_name, homepage, is_public, status, unix_group_name, short_description) VALUES (1, 'myXoopsForge Support', 'myxoopsforge.de', 1, 'A', 'xoopsforge', 'Short Description');
INSERT INTO xf_groups (group_id, group_name, homepage, is_public, status, unix_group_name, short_description) VALUES (2, 'myXoopsForge News', 'myxoopsforge.de', 0, 'A', 'xfnews', 'myXoopsForge News Project');
INSERT INTO xf_groups (group_id, group_name, homepage, is_public, status, unix_group_name, short_description) VALUES (100, 'Default Group', '', 0, 'A', 'default', '');

#
# Table structure for table `xf_maillist_site_subscriptions`
#

CREATE TABLE xf_maillist_site_subscriptions (
  uid int(11) NOT NULL default '0',
  list_id int(5) NOT NULL default '0',
  UNIQUE KEY indexuidlistid (uid,list_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_maillist_sitelists`
#

CREATE TABLE xf_maillist_sitelists (
  list_id int(5) NOT NULL auto_increment,
  list_name varchar(20) NOT NULL default '',
  allow_anon tinyint(1) NOT NULL default '0',
  allow_ru tinyint(1) NOT NULL default '0',
  allow_pa tinyint(1) NOT NULL default '0',
  allow_ca tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (list_id),
  KEY indexlistname (list_name),
  KEY indexallowanon (allow_anon),
  KEY indexallowru (allow_ru),
  KEY indexallowpa (allow_pa),
  KEY indexallowca (allow_ca)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xoops_xf_maillists`
#

CREATE TABLE xoops_xf_maillists (
  id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  name varchar(45) NOT NULL default '',
  description varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY indexgroup_id (group_id),
  KEY indexname (name)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_news_bytes`
#

CREATE TABLE xf_news_bytes (
  id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  submitted_by int(11) NOT NULL default '0',
  is_approved int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  forum_id int(11) NOT NULL default '0',
  summary text,
  details text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_news_bytes (id, group_id, submitted_by, is_approved, date, forum_id, summary, details) VALUES (1, 1, 1, 1, 1017328526, 2, 'myXoopsForge Launched', 'Today we have launched the myXoopsForge Project Management Add-On for the Xoops Content Management System.\n\rPlease report any problems you might have or any Bugs found during usage of this product on the product project page.\n\r\n\rThe myXoopsForge Development Team');

#
# Table structure for table `xf_project_assigned_to`
#

CREATE TABLE xf_project_assigned_to (
  project_assigned_id int(11) NOT NULL auto_increment,
  project_task_id int(11) NOT NULL default '0',
  assigned_to_id int(11) NOT NULL default '0',
  PRIMARY KEY  (project_assigned_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_project_dependencies`
#

CREATE TABLE xf_project_dependencies (
  project_depend_id int(11) NOT NULL auto_increment,
  project_task_id int(11) NOT NULL default '0',
  is_dependent_on_task_id int(11) NOT NULL default '0',
  PRIMARY KEY  (project_depend_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_project_group_list`
#

CREATE TABLE xf_project_group_list (
  group_project_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  project_name text NOT NULL,
  is_public int(11) NOT NULL default '0',
  description text,
  PRIMARY KEY  (group_project_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_project_history`
#

CREATE TABLE xf_project_history (
  project_history_id int(11) NOT NULL auto_increment,
  project_task_id int(11) NOT NULL default '0',
  field_name text NOT NULL,
  old_value text NOT NULL,
  mod_by int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  PRIMARY KEY  (project_history_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_project_status`
#

CREATE TABLE xf_project_status (
  status_id int(11) NOT NULL auto_increment,
  status_name text NOT NULL,
  PRIMARY KEY  (status_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_project_task`
#

CREATE TABLE xf_project_task (
  project_task_id int(11) NOT NULL auto_increment,
  group_project_id int(11) NOT NULL default '0',
  summary text NOT NULL,
  details text NOT NULL,
  percent_complete int(11) NOT NULL default '0',
  priority int(11) NOT NULL default '0',
  hours double NOT NULL default '0',
  start_date int(11) NOT NULL default '0',
  end_date int(11) NOT NULL default '0',
  created_by int(11) NOT NULL default '0',
  status_id int(11) NOT NULL default '0',
  PRIMARY KEY  (project_task_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_project_weekly_metric`
#

CREATE TABLE xf_project_weekly_metric (
  ranking int(11) NOT NULL auto_increment,
  percentile double default NULL,
  group_id int(11) NOT NULL default '0',
  PRIMARY KEY  (ranking)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_pubkeys`
#

CREATE TABLE xf_pubkeys (
  uid int(11) NOT NULL default '0',
  time int(11) NOT NULL default '0',
  pubkey text NOT NULL,
  KEY uid (uid)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_sample_data`
#

CREATE TABLE xf_sample_data (
  sampleid int(11) NOT NULL auto_increment,
  stateid int(11) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  data text NOT NULL,
  updatedate int(11) NOT NULL default '0',
  createdate int(11) NOT NULL default '0',
  created_by int(11) NOT NULL default '0',
  sample_group int(11) NOT NULL default '0',
  description text,
  PRIMARY KEY  (sampleid)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_sample_dl_stats`
#

CREATE TABLE xf_sample_dl_stats (
  id int(11) NOT NULL auto_increment,
  sampleid int(11) NOT NULL default '0',
  downloads int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY sampleid (sampleid)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_sample_feedback`
#

CREATE TABLE xf_sample_feedback (
  feedback_id int(11) NOT NULL auto_increment,
  sampleid int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  answer int(1) NOT NULL default '0',
  suggestion text NOT NULL,
  entered int(11) NOT NULL default '0',
  PRIMARY KEY  (feedback_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_sample_feedback_agg`
#

CREATE TABLE xf_sample_feedback_agg (
  sampleid int(11) NOT NULL default '0',
  answer_yes int(11) NOT NULL default '0',
  answer_no int(11) NOT NULL default '0',
  answer_na int(11) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_sample_groups`
#

CREATE TABLE xf_sample_groups (
  sample_group int(11) NOT NULL auto_increment,
  groupname varchar(255) NOT NULL default '',
  group_id int(11) NOT NULL default '0',
  PRIMARY KEY  (sample_group)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_sample_groups (sample_group, groupname, group_id) VALUES (1, 'Uncategorized Submissions', 1);

#
# Table structure for table `xf_sample_states`
#

CREATE TABLE xf_sample_states (
  stateid int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (stateid)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_sample_states VALUES (1, 'active');
INSERT INTO xf_sample_states VALUES (2, 'deleted');
INSERT INTO xf_sample_states VALUES (3, 'pending');
INSERT INTO xf_sample_states VALUES (4, 'hidden');
INSERT INTO xf_sample_states VALUES (5, 'private');

#
# Table structure for table `xf_survey_question_types`
#

CREATE TABLE xf_survey_question_types (
  id int(11) NOT NULL auto_increment,
  type text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_survey_question_types VALUES (1, 'Radio Buttons 1-5');
INSERT INTO xf_survey_question_types VALUES (2, 'Text Area');
INSERT INTO xf_survey_question_types VALUES (3, 'Radio Buttons Yes/No');
INSERT INTO xf_survey_question_types VALUES (4, 'Comment Only');
INSERT INTO xf_survey_question_types VALUES (5, 'Text Field');
INSERT INTO xf_survey_question_types VALUES (100, 'None');

#
# Table structure for table `xf_survey_questions`
#

CREATE TABLE xf_survey_questions (
  question_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  question text NOT NULL,
  question_type int(11) NOT NULL default '0',
  PRIMARY KEY  (question_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_survey_responses`
#

CREATE TABLE xf_survey_responses (
  user_id int(11) NOT NULL default '0',
  group_id int(11) NOT NULL default '0',
  survey_id int(11) NOT NULL default '0',
  question_id int(11) NOT NULL default '0',
  response text NOT NULL,
  date int(11) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_surveys`
#

CREATE TABLE xf_surveys (
  survey_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  survey_title text NOT NULL,
  survey_questions text NOT NULL,
  is_active int(11) NOT NULL default '1',
  PRIMARY KEY  (survey_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_user_bookmarks`
#

CREATE TABLE xf_user_bookmarks (
  bookmark_id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL default '0',
  bookmark_url text,
  bookmark_title text,
  PRIMARY KEY  (bookmark_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_user_diary`
#

CREATE TABLE xf_user_diary (
  id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL default '0',
  date_posted int(11) NOT NULL default '0',
  summary text,
  details text,
  is_public int(11) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_user_diary_monitor`
#

CREATE TABLE xf_user_diary_monitor (
  monitor_id int(11) NOT NULL auto_increment,
  monitored_user int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  PRIMARY KEY  (monitor_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_user_foundry_groups`
#

CREATE TABLE xf_user_foundry_groups (
  user_id int(11) NOT NULL default '0',
  group_id int(11) NOT NULL default '0',
  join_date int(11) NOT NULL default '0',
  KEY user_id_x (user_id),
  KEY group_id_x (group_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_user_group`
#

CREATE TABLE xf_user_group (
  user_group_id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL default '0',
  group_id int(11) NOT NULL default '0',
  admin_flags char(16) NOT NULL default '',
  bug_flags int(11) NOT NULL default '0',
  forum_flags int(11) NOT NULL default '0',
  project_flags int(11) NOT NULL default '2',
  patch_flags int(11) NOT NULL default '1',
  support_flags int(11) NOT NULL default '1',
  doc_flags int(11) NOT NULL default '0',
  sample_flags int(11) NOT NULL default '0',  
  cvs_flags int(11) NOT NULL default '0',
  member_role int(11) NOT NULL default '100',
  release_flags int(11) NOT NULL default '0',
  artifact_flags int(11) NOT NULL default '0',
  PRIMARY KEY  (user_group_id)
) TYPE=MyISAM;

# --------------------------------------------------------

INSERT INTO xf_user_group (user_group_id, user_id, group_id, admin_flags, bug_flags, forum_flags, project_flags, patch_flags, support_flags, doc_flags, sample_flags, cvs_flags, member_role, release_flags, artifact_flags) VALUES (1, 1, 1, 'A', 0, 2, 2, 1, 1, 0, 0, 1, 100, 0, 2);
INSERT INTO xf_user_group (user_group_id, user_id, group_id, admin_flags, bug_flags, forum_flags, project_flags, patch_flags, support_flags, doc_flags, sample_flags, cvs_flags, member_role, release_flags, artifact_flags) VALUES (2, 1, 2, 'A', 0, 2, 2, 1, 1, 0, 0, 1, 100, 0, 2);

#
# Table structure for table `xf_user_profile`
#

CREATE TABLE xf_user_profile (
  user_id int(11) NOT NULL default '0',
  people_view_skills tinyint(1) NOT NULL default '1',
  resume text,
  UNIQUE KEY user_id (user_id)
) TYPE=MyISAM;
  
