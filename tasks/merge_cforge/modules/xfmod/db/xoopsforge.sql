# phpMyAdmin MySQL-Dump
# version 2.2.5-rc1
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/(download page)
#
# Host: localhost
# Generation Time: Mar 28, 2002 at 08:55 AM
# Server version: 3.23.46
# PHP Version: 4.1.1
# Database : `xoops_rc2`
# --------------------------------------------------------

#
# Dumping data for table `v2_users`
#
DELETE FROM v2_users WHERE uid=100;
INSERT INTO v2_users(uid, uname, email, pass) VALUES(100, 'none', 'none@none.net', '*********34343');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_activity_log`
#

DROP TABLE IF EXISTS v2_xf_activity_log;
CREATE TABLE v2_xf_activity_log(
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

#
# Dumping data for table `v2_xf_activity_log`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact`
#

DROP TABLE IF EXISTS v2_xf_artifact;
CREATE TABLE v2_xf_artifact(
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
  PRIMARY KEY (artifact_id)
) TYPE=MyISAM;

#
# Table structure for table `v2_xf_artifact_canned_responses`
#

DROP TABLE IF EXISTS v2_xf_artifact_canned_responses;
CREATE TABLE v2_xf_artifact_canned_responses(
  id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  title text NOT NULL,
  body text NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_canned_responses`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_category`
#

DROP TABLE IF EXISTS v2_xf_artifact_category;
CREATE TABLE v2_xf_artifact_category(
  id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  category_name text NOT NULL,
  auto_assign_to int(11) NOT NULL default '100',
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_category`
#

INSERT INTO v2_xf_artifact_category(id, group_artifact_id, category_name, auto_assign_to) VALUES(1, 101, 'Project Registration Issue', 100);
INSERT INTO v2_xf_artifact_category(id, group_artifact_id, category_name, auto_assign_to) VALUES(2, 101, 'Project Administration', 100);
INSERT INTO v2_xf_artifact_category(id, group_artifact_id, category_name, auto_assign_to) VALUES(3, 101, 'Offtopic', 100);
INSERT INTO v2_xf_artifact_category(id, group_artifact_id, category_name, auto_assign_to) VALUES(4, 103, 'Layout', 100);
INSERT INTO v2_xf_artifact_category(id, group_artifact_id, category_name, auto_assign_to) VALUES(5, 103, 'Project Registration Process', 100);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_counts_agg`
#

DROP TABLE IF EXISTS v2_xf_artifact_counts_agg;
CREATE TABLE v2_xf_artifact_counts_agg(
  group_artifact_id int(11) NOT NULL default '0',
  count int(11) NOT NULL default '0',
  open_count int(11) NOT NULL default '0',
  PRIMARY KEY (group_artifact_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_counts_agg`
#

INSERT INTO v2_xf_artifact_counts_agg(group_artifact_id, count, open_count) VALUES(100, 0, 0);
INSERT INTO v2_xf_artifact_counts_agg(group_artifact_id, count, open_count) VALUES(101, 0, 0);
INSERT INTO v2_xf_artifact_counts_agg(group_artifact_id, count, open_count) VALUES(102, 0, 0);
INSERT INTO v2_xf_artifact_counts_agg(group_artifact_id, count, open_count) VALUES(103, 0, 0);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_file`
#

DROP TABLE IF EXISTS v2_xf_artifact_file;
CREATE TABLE v2_xf_artifact_file(
  id int(11) NOT NULL auto_increment,
  artifact_id int(11) NOT NULL default '0',
  description text NOT NULL,
  bin_data longtext NOT NULL,
  filename text NOT NULL,
  filesize int(11) NOT NULL default '0',
  filetype text NOT NULL,
  adddate int(11) NOT NULL default '0',
  submitted_by int(11) NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_file`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_group`
#

DROP TABLE IF EXISTS v2_xf_artifact_group;
CREATE TABLE v2_xf_artifact_group(
  id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  group_name text NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_group`
#

INSERT INTO v2_xf_artifact_group(id, group_artifact_id, group_name) VALUES(100, 100, 'None');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_group_list`
#

DROP TABLE IF EXISTS v2_xf_artifact_group_list;
CREATE TABLE v2_xf_artifact_group_list(
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
  PRIMARY KEY (group_artifact_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_group_list`
#

INSERT INTO v2_xf_artifact_group_list(group_artifact_id, group_id, name, description, is_public, allow_anon, email_all_updates, email_address, due_period, use_resolution, submit_instructions, browse_instructions, datatype, status_timeout) VALUES(100, 100, NULL, NULL, 0, 0, 0, '', 2592000, 0, NULL, NULL, 0, NULL);
INSERT INTO v2_xf_artifact_group_list(group_artifact_id, group_id, name, description, is_public, allow_anon, email_all_updates, email_address, due_period, use_resolution, submit_instructions, browse_instructions, datatype, status_timeout) VALUES(101, 1, 'Support Requests', 'Tech Support Tracking System', 1, 1, 0, '', 2592000, 0, '', '', 0, 1209600);
INSERT INTO v2_xf_artifact_group_list(group_artifact_id, group_id, name, description, is_public, allow_anon, email_all_updates, email_address, due_period, use_resolution, submit_instructions, browse_instructions, datatype, status_timeout) VALUES(102, 1, 'Feature Requests', 'Feature Request Tracking System', 1, 0, 0, '', 2592000, 0, '', '', 0, 1209600);
INSERT INTO v2_xf_artifact_group_list(group_artifact_id, group_id, name, description, is_public, allow_anon, email_all_updates, email_address, due_period, use_resolution, submit_instructions, browse_instructions, datatype, status_timeout) VALUES(103, 1, 'Bug Tracking', 'Bug Tracking System', 1, 1, 0, '', 2592000, 0, '', '', 0, 1209600);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_history`
#

DROP TABLE IF EXISTS v2_xf_artifact_history;
CREATE TABLE v2_xf_artifact_history(
  id int(11) NOT NULL auto_increment,
  artifact_id int(11) NOT NULL default '0',
  field_name text NOT NULL,
  old_value text NOT NULL,
  mod_by int(11) NOT NULL default '0',
  entrydate int(11) NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_history`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_message`
#

DROP TABLE IF EXISTS v2_xf_artifact_message;
CREATE TABLE v2_xf_artifact_message(
  id int(11) NOT NULL auto_increment,
  artifact_id int(11) NOT NULL default '0',
  submitted_by int(11) NOT NULL default '0',
  from_email text NOT NULL,
  adddate int(11) NOT NULL default '0',
  body text NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_message`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_monitor`
#

DROP TABLE IF EXISTS v2_xf_artifact_monitor;
CREATE TABLE v2_xf_artifact_monitor(
  id int(11) NOT NULL auto_increment,
  artifact_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  email text,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_monitor`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_perm`
#

DROP TABLE IF EXISTS v2_xf_artifact_perm;
CREATE TABLE v2_xf_artifact_perm(
  id int(11) NOT NULL auto_increment,
  group_artifact_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  perm_level int(11) NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_perm`
#

INSERT INTO v2_xf_artifact_perm(id, group_artifact_id, user_id, perm_level) VALUES(1, 101, 1, 2);
INSERT INTO v2_xf_artifact_perm(id, group_artifact_id, user_id, perm_level) VALUES(2, 102, 1, 2);
INSERT INTO v2_xf_artifact_perm(id, group_artifact_id, user_id, perm_level) VALUES(3, 103, 1, 2);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_resolution`
#

DROP TABLE IF EXISTS v2_xf_artifact_resolution;
CREATE TABLE v2_xf_artifact_resolution(
  id int(11) NOT NULL auto_increment,
  resolution_name text,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_resolution`
#

INSERT INTO v2_xf_artifact_resolution(id, resolution_name) VALUES(100, 'None');
INSERT INTO v2_xf_artifact_resolution(id, resolution_name) VALUES(102, 'Accepted');
INSERT INTO v2_xf_artifact_resolution(id, resolution_name) VALUES(103, 'Out of Date');
INSERT INTO v2_xf_artifact_resolution(id, resolution_name) VALUES(104, 'Postponed');
INSERT INTO v2_xf_artifact_resolution(id, resolution_name) VALUES(105, 'Rejected');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_artifact_status`
#

DROP TABLE IF EXISTS v2_xf_artifact_status;
CREATE TABLE v2_xf_artifact_status(
  id int(11) NOT NULL auto_increment,
  status_name text NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_artifact_status`
#

INSERT INTO v2_xf_artifact_status(id, status_name) VALUES(1, 'Open');
INSERT INTO v2_xf_artifact_status(id, status_name) VALUES(2, 'Closed');
INSERT INTO v2_xf_artifact_status(id, status_name) VALUES(3, 'Deleted');
INSERT INTO v2_xf_artifact_status(id, status_name) VALUES(4, 'Pending');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_canned_responses`
#

DROP TABLE IF EXISTS v2_xf_canned_responses;
CREATE TABLE v2_xf_canned_responses(
  response_id int(11) NOT NULL auto_increment,
  response_title varchar(25) default NULL,
  response_text text,
  PRIMARY KEY (response_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_canned_responses`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_doc_data`
#

DROP TABLE IF EXISTS v2_xf_doc_data;
CREATE TABLE v2_xf_doc_data(
  docid int(11) NOT NULL auto_increment,
  stateid int(11) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  data text NOT NULL,
  updatedate int(11) NOT NULL default '0',
  createdate int(11) NOT NULL default '0',
  created_by int(11) NOT NULL default '0',
  doc_group int(11) NOT NULL default '0',
  description text,
  PRIMARY KEY (docid)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_doc_data`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_doc_feedback`
#

DROP TABLE IF EXISTS v2_xf_doc_feedback;
CREATE TABLE v2_xf_doc_feedback(
  feedback_id int(11) NOT NULL auto_increment,
  docid int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  answer int(1) NOT NULL default '0',
  suggestion text NOT NULL,
  entered int(11) NOT NULL default '0',
  PRIMARY KEY (feedback_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_doc_feedback`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_doc_feedback_agg`
#

DROP TABLE IF EXISTS v2_xf_doc_feedback_agg;
CREATE TABLE v2_xf_doc_feedback_agg(
  docid int(11) NOT NULL default '0',
  answer_yes int(11) NOT NULL default '0',
  answer_no int(11) NOT NULL default '0',
  answer_na int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_doc_feedback_agg`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_doc_groups`
#

DROP TABLE IF EXISTS v2_xf_doc_groups;
CREATE TABLE v2_xf_doc_groups(
  doc_group int(11) NOT NULL auto_increment,
  groupname varchar(255) NOT NULL default '',
  group_id int(11) NOT NULL default '0',
  PRIMARY KEY (doc_group)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_doc_groups`
#

INSERT INTO v2_xf_doc_groups(doc_group, groupname, group_id) VALUES(1, 'Uncategorized Submissions', 1);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_doc_states`
#

DROP TABLE IF EXISTS v2_xf_doc_states;
CREATE TABLE v2_xf_doc_states(
  stateid int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY (stateid)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_doc_states`
#

INSERT INTO v2_xf_doc_states(stateid, name) VALUES(1, 'active');
INSERT INTO v2_xf_doc_states(stateid, name) VALUES(2, 'deleted');
INSERT INTO v2_xf_doc_states(stateid, name) VALUES(3, 'pending');
INSERT INTO v2_xf_doc_states(stateid, name) VALUES(4, 'hidden');
INSERT INTO v2_xf_doc_states(stateid, name) VALUES(5, 'private');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_filemodule_monitor`
#

DROP TABLE IF EXISTS v2_xf_filemodule_monitor;
CREATE TABLE v2_xf_filemodule_monitor(
  id int(11) NOT NULL auto_increment,
  filemodule_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_filemodule_monitor`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_forum`
#

DROP TABLE IF EXISTS v2_xf_forum;
CREATE TABLE v2_xf_forum(
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
  PRIMARY KEY (msg_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_forum`
#

INSERT INTO v2_xf_forum(msg_id, group_forum_id, posted_by, subject, body, date, is_followup_to, thread_id, has_followups, most_recent_date) VALUES(1, 1, 1, 'Welcome to XoopsForge Open Discussion', 'Welcome to XoopsForge Open Discussion', 1017315660, 0, 1, 0, 0);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_forum_agg_msg_count`
#

DROP TABLE IF EXISTS v2_xf_forum_agg_msg_count;
CREATE TABLE v2_xf_forum_agg_msg_count(
  group_forum_id int(11) NOT NULL default '0',
  count int(11) NOT NULL default '0',
  PRIMARY KEY (group_forum_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_forum_agg_msg_count`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_forum_group_list`
#

DROP TABLE IF EXISTS v2_xf_forum_group_list;
CREATE TABLE v2_xf_forum_group_list(
  group_forum_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  forum_name text NOT NULL,
  is_public int(11) NOT NULL default '0',
  description text,
  allow_anonymous int(11) NOT NULL default '0',
  send_all_posts_to text,
  PRIMARY KEY (group_forum_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_forum_group_list`
#

INSERT INTO v2_xf_forum_group_list(group_forum_id, group_id, forum_name, is_public, description, allow_anonymous, send_all_posts_to) VALUES(1, 1, 'XoopsForge Open Discussion', 1, 'Discussion of XoopsForge Topics.', 0, NULL);
INSERT INTO v2_xf_forum_group_list(group_forum_id, group_id, forum_name, is_public, description, allow_anonymous, send_all_posts_to) VALUES(2, 2, 'XoopsForge Launched', 1, '', 0, NULL);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_forum_monitored_forums`
#

DROP TABLE IF EXISTS v2_xf_forum_monitored_forums;
CREATE TABLE v2_xf_forum_monitored_forums(
  monitor_id int(11) NOT NULL auto_increment,
  forum_id int(11) NOT NULL default '0',
  user_id int(11) NOT NULL default '0',
  PRIMARY KEY (monitor_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_forum_monitored_forums`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_forum_thread_id`
#

DROP TABLE IF EXISTS v2_xf_forum_thread_id;
CREATE TABLE v2_xf_forum_thread_id(
  thread_id int(11) NOT NULL auto_increment,
  PRIMARY KEY (thread_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_forum_thread_id`
#

INSERT INTO v2_xf_forum_thread_id(thread_id) VALUES(1);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_foundry_data`
#

DROP TABLE IF EXISTS v2_xf_foundry_data;
CREATE TABLE v2_xf_foundry_data(
  foundry_id int(11) NOT NULL auto_increment,
  freeform1_html text,
  freeform2_html text,
  sponsor1_html text,
  sponsor2_html text,
  guide_image_id int(11) NOT NULL default '0',
  logo_image_id int(11) NOT NULL default '0',
  trove_categories text,
  PRIMARY KEY (foundry_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_foundry_data`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_foundry_news`
#

DROP TABLE IF EXISTS v2_xf_foundry_news;
CREATE TABLE v2_xf_foundry_news(
  foundry_news_id int(11) NOT NULL auto_increment,
  foundry_id int(11) NOT NULL default '0',
  news_id int(11) NOT NULL default '0',
  approve_date int(11) NOT NULL default '0',
  is_approved int(11) NOT NULL default '0',
  PRIMARY KEY (foundry_news_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_foundry_news`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_foundry_projects`
#

DROP TABLE IF EXISTS v2_xf_foundry_projects;
CREATE TABLE v2_xf_foundry_projects(
  id int(11) NOT NULL auto_increment,
  foundry_id int(11) NOT NULL default '0',
  project_id int(11) NOT NULL default '0',
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_foundry_projects`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_frs_dlstats_file_agg`
#

DROP TABLE IF EXISTS v2_xf_frs_dlstats_file_agg;
CREATE TABLE v2_xf_frs_dlstats_file_agg(
  month int(11) default NULL,
  day int(11) default NULL,
  file_id int(11) default NULL,
  downloads int(11) default NULL
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_frs_dlstats_file_agg`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_frs_file`
#

DROP TABLE IF EXISTS v2_xf_frs_file;
CREATE TABLE v2_xf_frs_file(
  file_id int(11) NOT NULL auto_increment,
  filename varchar(255) default NULL,
  file_url varchar(255) default NULL,
  release_id int(11) NOT NULL default '0',
  type_id int(11) NOT NULL default '0',
  processor_id int(11) NOT NULL default '0',
  release_time int(11) NOT NULL default '0',
  file_size int(11) NOT NULL default '0',
  post_date int(11) NOT NULL default '0',
  PRIMARY KEY (file_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_frs_file`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_frs_filetype`
#

DROP TABLE IF EXISTS v2_xf_frs_filetype;
CREATE TABLE v2_xf_frs_filetype(
  type_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY (type_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_frs_filetype`
#

INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(1000, '.deb');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(2000, '.rpm');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(3000, '.zip');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(3100, '.bz2');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(3110, '.gz');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(5000, 'Source .zip');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(5010, 'Source .bz2');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(5020, 'Source .gz');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(5100, 'Source .rpm');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(5900, 'Other Source File');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(8000, '.jpg');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(8100, 'text');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(8200, 'html');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(8300, 'pdf');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(9999, 'Other');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(6000, 'Script .php');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(6010, 'Script .asp');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(3010, '.rar');
INSERT INTO v2_xf_frs_filetype(type_id, name) VALUES(6100, 'Script .js');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_frs_package`
#

DROP TABLE IF EXISTS v2_xf_frs_package;
CREATE TABLE v2_xf_frs_package(
  package_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  name text,
  status_id int(11) NOT NULL default '0',
  PRIMARY KEY (package_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_frs_package`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_frs_processor`
#

DROP TABLE IF EXISTS v2_xf_frs_processor;
CREATE TABLE v2_xf_frs_processor(
  processor_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY (processor_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_frs_processor`
#

INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(1000, 'i386');
INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(6000, 'IA64');
INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(7000, 'Alpha');
INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(8000, 'Any');
INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(2000, 'PPC');
INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(3000, 'MIPS');
INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(4000, 'Sparc');
INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(5000, 'UltraSparc');
INSERT INTO v2_xf_frs_processor(processor_id, name) VALUES(9999, 'Other');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_frs_release`
#

DROP TABLE IF EXISTS v2_xf_frs_release;
CREATE TABLE v2_xf_frs_release(
  release_id int(11) NOT NULL auto_increment,
  package_id int(11) NOT NULL default '0',
  name text,
  notes text,
  changes text,
  status_id int(11) NOT NULL default '0',
  preformatted int(11) NOT NULL default '0',
  release_date int(11) NOT NULL default '0',
  released_by int(11) NOT NULL default '0',
  PRIMARY KEY (release_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_frs_release`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_frs_status`
#

DROP TABLE IF EXISTS v2_xf_frs_status;
CREATE TABLE v2_xf_frs_status(
  status_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY (status_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_frs_status`
#

INSERT INTO v2_xf_frs_status(status_id, name) VALUES(1, 'Active');
INSERT INTO v2_xf_frs_status(status_id, name) VALUES(3, 'Hidden');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_group_history`
#

DROP TABLE IF EXISTS v2_xf_group_history;
CREATE TABLE v2_xf_group_history(
  group_history_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  field_name text NOT NULL,
  old_value text NOT NULL,
  mod_by int(11) NOT NULL default '0',
  date int(11) default NULL,
  PRIMARY KEY (group_history_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_group_history`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_groups`
#

DROP TABLE IF EXISTS v2_xf_groups;
CREATE TABLE v2_xf_groups(
  group_id int(11) NOT NULL auto_increment,
  group_name varchar(40) default NULL,
  homepage varchar(128) default NULL,
  is_public int(11) NOT NULL default '0',
  status char(1) NOT NULL default 'A',
  unix_group_name varchar(30) NOT NULL default '',
  unix_box varchar(20) NOT NULL default 'shell1',
  http_domain varchar(80) default NULL,
  short_description varchar(255) default NULL,
  license varchar(16) default NULL,
  register_purpose text,
  license_other text,
  register_time int(11) NOT NULL default '0',
  use_bugs int(11) NOT NULL default '1',
  rand_hash text,
  use_mail int(11) NOT NULL default '1',
  use_survey int(11) NOT NULL default '1',
  use_patch int(11) NOT NULL default '1',
  use_forum int(11) NOT NULL default '1',
  use_pm int(11) NOT NULL default '1',
  use_cvs int(11) NOT NULL default '1',
  use_news int(11) NOT NULL default '1',
  use_support int(11) NOT NULL default '1',
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
  PRIMARY KEY (group_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_groups`
#

INSERT INTO v2_xf_groups(group_id, group_name, homepage, is_public, status, unix_group_name, short_description) VALUES(1, 'XoopsForge Support', 'xoopsforge.mediacom4.net', 1, 'A', 'xoopsforge', 'Short Description');
INSERT INTO v2_xf_groups(group_id, group_name, homepage, is_public, status, unix_group_name, short_description) VALUES(2, 'XoopsForge News', 'xoopsforge.mediacom4.net', 0, 'A', 'xfnews', 'XoopsForge News Project');
INSERT INTO v2_xf_groups(group_id, group_name, homepage, is_public, status, unix_group_name, short_description) VALUES(100, 'Default Group', NULL, 0, 'A', 'default', NULL);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_news_bytes`
#

DROP TABLE IF EXISTS v2_xf_news_bytes;
CREATE TABLE v2_xf_news_bytes(
  id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  submitted_by int(11) NOT NULL default '0',
  is_approved int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  forum_id int(11) NOT NULL default '0',
  summary text,
  details text,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_news_bytes`
#
INSERT INTO v2_xf_news_bytes(id, group_id, submitted_by, is_approved, date, forum_id, summary, details) VALUES(1, 1, 1, 1, 1017328526, 2, 'XoopsForge Launched', 'Today we have launched the XoopsForge Project Management Add-On for the Xoops Content Management System.\n\rPlease report any problems you might have or any Bugs found during usage of this product on the product project page.\n\r\n\rThe XoopsForge Development Team');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_people_job`
#

DROP TABLE IF EXISTS v2_xf_people_job;
CREATE TABLE v2_xf_people_job(
  job_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  created_by int(11) NOT NULL default '0',
  title text,
  description text,
  date int(11) NOT NULL default '0',
  status_id int(11) NOT NULL default '0',
  category_id int(11) NOT NULL default '0',
  PRIMARY KEY (job_id)
) TYPE=MyISAM;

#
# Table structure for table `v2_xf_people_job_category`
#

DROP TABLE IF EXISTS v2_xf_people_job_category;
CREATE TABLE v2_xf_people_job_category(
  category_id int(11) NOT NULL auto_increment,
  name text,
  private_flag int(11) NOT NULL default '0',
  PRIMARY KEY (category_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_people_job_category`
#

INSERT INTO v2_xf_people_job_category(category_id, name, private_flag) VALUES(1, 'Developer', 0);
INSERT INTO v2_xf_people_job_category(category_id, name, private_flag) VALUES(2, 'Project Manager', 0);
INSERT INTO v2_xf_people_job_category(category_id, name, private_flag) VALUES(3, 'Unix Admin', 0);
INSERT INTO v2_xf_people_job_category(category_id, name, private_flag) VALUES(4, 'Doc Writer', 0);
INSERT INTO v2_xf_people_job_category(category_id, name, private_flag) VALUES(5, 'Tester', 0);
INSERT INTO v2_xf_people_job_category(category_id, name, private_flag) VALUES(6, 'Support Manager', 0);
INSERT INTO v2_xf_people_job_category(category_id, name, private_flag) VALUES(7, 'Graphic/Other Designer', 0);
INSERT INTO v2_xf_people_job_category(category_id, name, private_flag) VALUES(100, NULL, 0);
# --------------------------------------------------------

#
# Table structure for table `v2_xf_people_job_status`
#

DROP TABLE IF EXISTS v2_xf_people_job_status;
CREATE TABLE v2_xf_people_job_status(
  status_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY (status_id)
) TYPE=MyISAM;

#
# Table structure for table `v2_xf_people_skill`
#

DROP TABLE IF EXISTS v2_xf_people_skill;
CREATE TABLE v2_xf_people_skill(
  skill_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY (skill_id)
) TYPE=MyISAM;

#
# Table structure for table `v2_xf_people_skill_inventory`
#

DROP TABLE IF EXISTS v2_xf_people_skill_inventory;
CREATE TABLE v2_xf_people_skill_inventory(
  skill_inventory_id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL default '0',
  skill_id int(11) NOT NULL default '0',
  skill_level_id int(11) NOT NULL default '0',
  skill_year_id int(11) NOT NULL default '0',
  PRIMARY KEY (skill_inventory_id)
) TYPE=MyISAM;

#
# Table structure for table v2_xf_people_skill_level
#

DROP TABLE IF EXISTS v2_xf_people_skill_level;
CREATE TABLE v2_xf_people_skill_level(
  skill_level_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY (skill_level_id)
) TYPE=MyISAM;

INSERT INTO v2_xf_people_skill_level VALUES(1,'3100 SQL');
INSERT INTO v2_xf_people_skill_level VALUES(2,'3110 C/C++');
INSERT INTO v2_xf_people_skill_level VALUES(3,'3120 Perl');
INSERT INTO v2_xf_people_skill_level VALUES(4,'3130 PHP');
INSERT INTO v2_xf_people_skill_level VALUES(5,'3140 Java');
INSERT INTO v2_xf_people_skill_level VALUES(6,'3150 Python');
INSERT INTO v2_xf_people_skill_level VALUES(7,'3160 Visual Basic');
INSERT INTO v2_xf_people_skill_level VALUES(8,'3170 AppleScript');
INSERT INTO v2_xf_people_skill_level VALUES(9,'3180 UML');
INSERT INTO v2_xf_people_skill_level VALUES(10,'3190 XMI');
INSERT INTO v2_xf_people_skill_level VALUES(11,'3200 HTML/DHTML');
INSERT INTO v2_xf_people_skill_level VALUES(12,'3210 XML/XPath/XLink/XSL/XSLT');
INSERT INTO v2_xf_people_skill_level VALUES(13,'3230 RDF');
INSERT INTO v2_xf_people_skill_level VALUES(14,'3240 LISP');
INSERT INTO v2_xf_people_skill_level VALUES(15,'3250 Delphi');
INSERT INTO v2_xf_people_skill_level VALUES(16,'3260 ASP');
INSERT INTO v2_xf_people_skill_level VALUES(17,'3270 Ada');
INSERT INTO v2_xf_people_skill_level VALUES(18,'3900 Other Prog. Lang.');
INSERT INTO v2_xf_people_skill_level VALUES(19,'5100 Chinese');
INSERT INTO v2_xf_people_skill_level VALUES(20,'5110 Japanese');
INSERT INTO v2_xf_people_skill_level VALUES(21,'5120 Spanish');
INSERT INTO v2_xf_people_skill_level VALUES(22,'5130 French');
INSERT INTO v2_xf_people_skill_level VALUES(23,'5140 German');
INSERT INTO v2_xf_people_skill_level VALUES(24,'5900 Other Spoken Lang.');
INSERT INTO v2_xf_people_skill_level VALUES(25,'7100 UNIX Admin');
INSERT INTO v2_xf_people_skill_level VALUES(26,'7110 Networking');
INSERT INTO v2_xf_people_skill_level VALUES(27,'7120 Security');
INSERT INTO v2_xf_people_skill_level VALUES(28,'7130 Writing');
INSERT INTO v2_xf_people_skill_level VALUES(29,'7140 Editing');
INSERT INTO v2_xf_people_skill_level VALUES(30,'7150 Databases');
INSERT INTO v2_xf_people_skill_level VALUES(31,'7160 Object Oriented Design');
INSERT INTO v2_xf_people_skill_level VALUES(32,'7170 Object Oriented Analysis');
INSERT INTO v2_xf_people_skill_level VALUES(33,'7900 Other Skill Area');

#
# Table structure for table v2_xf_people_skill_year
#

DROP TABLE IF EXISTS v2_xf_people_skill_year;
CREATE TABLE v2_xf_people_skill_year(
  skill_year_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY (skill_year_id)
) TYPE=MyISAM;

#
# Table structure for table `v2_xf_project_assigned_to`
#

DROP TABLE IF EXISTS v2_xf_project_assigned_to;
CREATE TABLE v2_xf_project_assigned_to(
  project_assigned_id int(11) NOT NULL auto_increment,
  project_task_id int(11) NOT NULL default '0',
  assigned_to_id int(11) NOT NULL default '0',
  PRIMARY KEY (project_assigned_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_project_assigned_to`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_project_group_list`
#

DROP TABLE IF EXISTS v2_xf_project_group_list;
CREATE TABLE v2_xf_project_group_list(
  group_project_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  project_name text NOT NULL,
  is_public int(11) NOT NULL default '0',
  description text,
  PRIMARY KEY (group_project_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_project_group_list`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_project_task`
#

DROP TABLE IF EXISTS v2_xf_project_task;
CREATE TABLE v2_xf_project_task(
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
  PRIMARY KEY (project_task_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_project_task`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_project_weekly_metric`
#

DROP TABLE IF EXISTS v2_xf_project_weekly_metric;
CREATE TABLE v2_xf_project_weekly_metric(
  ranking int(11) NOT NULL auto_increment,
  percentile double default NULL,
  group_id int(11) NOT NULL default '0',
  PRIMARY KEY (ranking)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_project_weekly_metric`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_survey_question_types`
#

DROP TABLE IF EXISTS v2_xf_survey_question_types;
CREATE TABLE v2_xf_survey_question_types(
  id int(11) NOT NULL auto_increment,
  type text NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_survey_question_types`
#

INSERT INTO v2_xf_survey_question_types(id, type) VALUES(1, 'Radio Buttons 1-5');
INSERT INTO v2_xf_survey_question_types(id, type) VALUES(2, 'Text Area');
INSERT INTO v2_xf_survey_question_types(id, type) VALUES(3, 'Radio Buttons Yes/No');
INSERT INTO v2_xf_survey_question_types(id, type) VALUES(4, 'Comment Only');
INSERT INTO v2_xf_survey_question_types(id, type) VALUES(5, 'Text Field');
INSERT INTO v2_xf_survey_question_types(id, type) VALUES(100, 'None');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_survey_questions`
#

DROP TABLE IF EXISTS v2_xf_survey_questions;
CREATE TABLE v2_xf_survey_questions(
  question_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  question text NOT NULL,
  question_type int(11) NOT NULL default '0',
  PRIMARY KEY (question_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_survey_questions`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_survey_responses`
#

DROP TABLE IF EXISTS v2_xf_survey_responses;
CREATE TABLE v2_xf_survey_responses(
  user_id int(11) NOT NULL default '0',
  group_id int(11) NOT NULL default '0',
  survey_id int(11) NOT NULL default '0',
  question_id int(11) NOT NULL default '0',
  response text NOT NULL,
  date int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_survey_responses`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_surveys`
#

DROP TABLE IF EXISTS v2_xf_surveys;
CREATE TABLE v2_xf_surveys(
  survey_id int(11) NOT NULL auto_increment,
  group_id int(11) NOT NULL default '0',
  survey_title text NOT NULL,
  survey_questions text NOT NULL,
  is_active int(11) NOT NULL default '1',
  PRIMARY KEY (survey_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_surveys`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_trove_agg`
#

DROP TABLE IF EXISTS v2_xf_trove_agg;
CREATE TABLE v2_xf_trove_agg(
  trove_cat_id int(11) default NULL,
  group_id int(11) default NULL,
  group_name varchar(40) default NULL,
  unix_group_name varchar(30) default NULL,
  status char(1) default NULL,
  register_time int(11) default NULL,
  short_description varchar(255) default NULL,
  percentile double default NULL,
  ranking int(11) default NULL
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_trove_agg`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_trove_cat`
#

DROP TABLE IF EXISTS v2_xf_trove_cat;
CREATE TABLE v2_xf_trove_cat(
  trove_cat_id int(11) NOT NULL auto_increment,
  version int(11) NOT NULL default '0',
  parent int(11) NOT NULL default '0',
  root_parent int(11) NOT NULL default '0',
  shortname varchar(80) default NULL,
  fullname varchar(80) default NULL,
  description varchar(255) default NULL,
  count_subcat int(11) NOT NULL default '0',
  count_subproj int(11) NOT NULL default '0',
  fullpath text NOT NULL,
  fullpath_ids text,
  PRIMARY KEY (trove_cat_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_trove_cat`
#

INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(1, 2000031601, 0, 0, 'audience', 'Intended Audience', 'The main class of people likely to be interested in this resource.', 0, 0, 'Intended Audience', '1');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(2, 2000032401, 1, 1, 'endusers', 'End Users/Desktop', 'Programs and resources for software end users. Software for the desktop.', 0, 0, 'Intended Audience :: End Users/Desktop', '1 :: 2');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(3, 2000041101, 1, 1, 'developers', 'Developers', 'Programs and resources for software developers, to include libraries.', 0, 0, 'Intended Audience :: Developers', '1 :: 3');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(4, 2000031601, 1, 1, 'sysadmins', 'System Administrators', 'Programs and resources for people who administer computers and networks.', 0, 0, 'Intended Audience :: System Administrators', '1 :: 4');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(5, 2000040701, 1, 1, 'other', 'Other Audience', 'Programs and resources for an unlisted audience.', 0, 0, 'Intended Audience :: Other Audience', '1 :: 5');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(6, 2000031601, 0, 0, 'developmentstatus', 'Development Status', 'An indication of the development status of the software or resource.', 0, 0, 'Development Status', '6');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(7, 2000040701, 6, 6, 'planning', '1 - Planning', 'This resource is in the planning stages only. There is no code.', 0, 0, 'Development Status :: 1 - Planning', '6 :: 7');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(8, 2000040701, 6, 6, 'prealpha', '2 - Pre-Alpha', 'There is code for this project, but it is not usable except for further development.', 0, 0, 'Development Status :: 2 - Pre-Alpha', '6 :: 8');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(9, 2000041101, 6, 6, 'alpha', '3 - Alpha', 'Resource is in early development, and probably incomplete and/or extremely buggy.', 0, 0, 'Development Status :: 3 - Alpha', '6 :: 9');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(10, 2000040701, 6, 6, 'beta', '4 - Beta', 'Resource is in late phases of development. Deliverables are essentially complete, but may still have significant bugs.', 0, 0, 'Development Status :: 4 - Beta', '6 :: 10');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(11, 2000040701, 6, 6, 'production', '5 - Production/Stable', 'Deliverables are complete and usable by the intended audience.', 0, 0, 'Development Status :: 5 - Production/Stable', '6 :: 11');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(12, 2000040701, 6, 6, 'mature', '6 - Mature', 'This resource has an extensive history of successful use and has probably undergone several stable revisions.', 0, 0, 'Development Status :: 6 - Mature', '6 :: 12');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(13, 2000031601, 0, 0, 'license', 'License', 'License terms under which the resource is distributed.', 0, 0, 'License', '13');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(14, 2000032401, 13, 13, 'osi', 'OSI Approved', 'Licenses that have been approved by OSI as approved', 0, 0, 'License :: OSI Approved', '13 :: 14');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(15, 2000032001, 14, 13, 'gpl', 'GNU General Public License(GPL)', 'GNU General Public License.', 0, 0, 'License :: OSI Approved :: GNU General Public License(GPL)', '13 :: 14 :: 15');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(16, 2000050801, 14, 13, 'lgpl', 'GNU Lesser General Public License(LGPL)', 'GNU Lesser General Public License', 0, 0, 'License :: OSI Approved :: GNU Lesser General Public License(LGPL)', '13 :: 14 :: 16');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(17, 2000032001, 14, 13, 'artistic', 'Artistic License', 'The Perl Artistic License', 0, 0, 'License :: OSI Approved :: Artistic License', '13 :: 14 :: 17');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(18, 2000031601, 0, 0, 'topic', 'Topic', 'Topic categorization.', 0, 0, 'Topic', '18');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(19, 2000032001, 136, 18, 'archiving', 'Archiving', 'Tools for maintaining and searching software or document archives.', 0, 0, 'Topic :: System :: Archiving', '18 :: 136 :: 19');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(20, 2000032401, 18, 18, 'communications', 'Communications', 'Programs intended to facilitate communication between people.', 0, 0, 'Topic :: Communications', '18 :: 20');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(21, 2000031601, 20, 18, 'bbs', 'BBS', 'Bulletin Board systems.', 0, 0, 'Topic :: Communications :: BBS', '18 :: 20 :: 21');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(22, 2000031601, 20, 18, 'chat', 'Chat', 'Programs to support real-time communication over the Internet.', 0, 0, 'Topic :: Communications :: Chat', '18 :: 20 :: 22');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(23, 2000031601, 22, 18, 'icq', 'ICQ', 'Programs to support ICQ.', 0, 0, 'Topic :: Communications :: Chat :: ICQ', '18 :: 20 :: 22 :: 23');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(24, 2000041101, 22, 18, 'irc', 'Internet Relay Chat', 'Programs to support Internet Relay Chat.', 0, 0, 'Topic :: Communications :: Chat :: Internet Relay Chat', '18 :: 20 :: 22 :: 24');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(25, 2000031601, 22, 18, 'talk', 'Unix Talk', 'Programs to support Unix Talk protocol.', 0, 0, 'Topic :: Communications :: Chat :: Unix Talk', '18 :: 20 :: 22 :: 25');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(26, 2000031601, 22, 18, 'aim', 'AOL Instant Messanger', 'Programs to support AOL Instant Messanger.', 0, 0, 'Topic :: Communications :: Chat :: AOL Instant Messanger', '18 :: 20 :: 22 :: 26');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(27, 2000031601, 20, 18, 'conferencing', 'Conferencing', 'Software to support real-time conferencing over the Internet.', 0, 0, 'Topic :: Communications :: Conferencing', '18 :: 20 :: 27');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(28, 2000031601, 20, 18, 'email', 'Email', 'Programs for sending, processing, and handling electronic mail.', 0, 0, 'Topic :: Communications :: Email', '18 :: 20 :: 28');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(29, 2000031601, 28, 18, 'filters', 'Filters', 'Content-driven filters and dispatchers for Email.', 0, 0, 'Topic :: Communications :: Email :: Filters', '18 :: 20 :: 28 :: 29');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(30, 2000031601, 28, 18, 'listservers', 'Mailing List Servers', 'Tools for managing electronic mailing lists.', 0, 0, 'Topic :: Communications :: Email :: Mailing List Servers', '18 :: 20 :: 28 :: 30');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(31, 2000031601, 28, 18, 'mua', 'Email Clients(MUA)', 'Programs for interactively reading and sending Email.', 0, 0, 'Topic :: Communications :: Email :: Email Clients(MUA)', '18 :: 20 :: 28 :: 31');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(32, 2000031601, 28, 18, 'mta', 'Mail Transport Agents', 'Email transport and gatewaying software.', 0, 0, 'Topic :: Communications :: Email :: Mail Transport Agents', '18 :: 20 :: 28 :: 32');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(33, 2000031601, 28, 18, 'postoffice', 'Post-Office', 'Programs to support post-office protocols, including POP and IMAP.', 0, 0, 'Topic :: Communications :: Email :: Post-Office', '18 :: 20 :: 28 :: 33');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(34, 2000031601, 33, 18, 'pop3', 'POP3', 'Programs to support POP3(Post-Office Protocol, version 3).', 0, 0, 'Topic :: Communications :: Email :: Post-Office :: POP3', '18 :: 20 :: 28 :: 33 :: 34');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(35, 2000031601, 33, 18, 'imap', 'IMAP', 'Programs to support IMAP protocol(Internet Message Access Protocol).', 0, 0, 'Topic :: Communications :: Email :: Post-Office :: IMAP', '18 :: 20 :: 28 :: 33 :: 35');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(36, 2000031601, 20, 18, 'fax', 'Fax', 'Tools for sending and receiving facsimile messages.', 0, 0, 'Topic :: Communications :: Fax', '18 :: 20 :: 36');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(37, 2000031601, 20, 18, 'fido', 'FIDO', 'Tools for FIDOnet mail and echoes.', 0, 0, 'Topic :: Communications :: FIDO', '18 :: 20 :: 37');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(38, 2000031601, 20, 18, 'hamradio', 'Ham Radio', 'Tools and resources for amateur radio.', 0, 0, 'Topic :: Communications :: Ham Radio', '18 :: 20 :: 38');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(39, 2000031601, 20, 18, 'usenet', 'Usenet News', 'Software to support USENET news.', 0, 0, 'Topic :: Communications :: Usenet News', '18 :: 20 :: 39');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(40, 2000031601, 20, 18, 'internetphone', 'Internet Phone', 'Software to support real-time speech communication over the Internet.', 0, 0, 'Topic :: Communications :: Internet Phone', '18 :: 20 :: 40');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(41, 2000031601, 19, 18, 'packaging', 'Packaging', 'Tools for packing and unpacking multi-file formats. Includes data-only formats and software package systems.', 0, 0, 'Topic :: System :: Archiving :: Packaging', '18 :: 136 :: 19 :: 41');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(42, 2000031601, 19, 18, 'compression', 'Compression', 'Tools and libraries for data compression.', 0, 0, 'Topic :: System :: Archiving :: Compression', '18 :: 136 :: 19 :: 42');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(43, 2000031601, 18, 18, 'security', 'Security', 'Security-related software, to include system administration and cryptography.', 0, 0, 'Topic :: Security', '18 :: 43');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(44, 2000031601, 43, 18, 'cryptography', 'Cryptography', 'Cryptography programs, algorithms, and libraries.', 0, 0, 'Topic :: Security :: Cryptography', '18 :: 43 :: 44');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(45, 2000031601, 18, 18, 'development', 'Software Development', 'Software used to aid software development.', 0, 0, 'Topic :: Software Development', '18 :: 45');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(46, 2000031601, 45, 18, 'build', 'Build Tools', 'Software for the build process.', 0, 0, 'Topic :: Software Development :: Build Tools', '18 :: 45 :: 46');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(47, 2000031601, 45, 18, 'debuggers', 'Debuggers', 'Programs for controlling and monitoring the execution of compiled binaries.', 0, 0, 'Topic :: Software Development :: Debuggers', '18 :: 45 :: 47');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(48, 2000031601, 45, 18, 'compilers', 'Compilers', 'Programs for compiling high-level languges into machine code.', 0, 0, 'Topic :: Software Development :: Compilers', '18 :: 45 :: 48');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(49, 2000031601, 45, 18, 'interpreters', 'Interpreters', 'Programs for interpreting and executing high-level languages directly.', 0, 0, 'Topic :: Software Development :: Interpreters', '18 :: 45 :: 49');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(50, 2000031601, 45, 18, 'objectbrokering', 'Object Brokering', 'Object brokering libraries and tools.', 0, 0, 'Topic :: Software Development :: Object Brokering', '18 :: 45 :: 50');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(51, 2000031601, 50, 18, 'corba', 'CORBA', 'Tools for implementation and use of CORBA.', 0, 0, 'Topic :: Software Development :: Object Brokering :: CORBA', '18 :: 45 :: 50 :: 51');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(52, 2000031601, 45, 18, 'versioncontrol', 'Version Control', 'Tools for managing multiple versions of evolving sources or documents.', 0, 0, 'Topic :: Software Development :: Version Control', '18 :: 45 :: 52');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(53, 2000031601, 52, 18, 'cvs', 'CVS', 'Tools for CVS(Concurrent Versioning System).', 0, 0, 'Topic :: Software Development :: Version Control :: CVS', '18 :: 45 :: 52 :: 53');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(54, 2000031601, 52, 18, 'rcs', 'RCS', 'Tools for RCS(Revision Control System).', 0, 0, 'Topic :: Software Development :: Version Control :: RCS', '18 :: 45 :: 52 :: 54');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(55, 2000031601, 18, 18, 'desktop', 'Desktop Environment', 'Accessories, managers, and utilities for your GUI desktop.', 0, 0, 'Topic :: Desktop Environment', '18 :: 55');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(56, 2000031601, 55, 18, 'windowmanagers', 'Window Managers', 'Programs that provide window control and application launching.', 0, 0, 'Topic :: Desktop Environment :: Window Managers', '18 :: 55 :: 56');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(57, 2000031601, 55, 18, 'kde', 'K Desktop Environment(KDE)', 'Software for the KDE desktop.', 0, 0, 'Topic :: Desktop Environment :: K Desktop Environment(KDE)', '18 :: 55 :: 57');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(58, 2000031601, 55, 18, 'gnome', 'Gnome', 'Software for the Gnome desktop.', 0, 0, 'Topic :: Desktop Environment :: Gnome', '18 :: 55 :: 58');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(59, 2000031601, 56, 18, 'enlightenment', 'Enlightenment', 'Software for the Enlightenment window manager.', 0, 0, 'Topic :: Desktop Environment :: Window Managers :: Enlightenment', '18 :: 55 :: 56 :: 59');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(60, 2000031601, 59, 18, 'themes', 'Themes', 'Themes for the Enlightenment window manager.', 0, 0, 'Topic :: Desktop Environment :: Window Managers :: Enlightenment :: Themes', '18 :: 55 :: 56 :: 59 :: 60');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(61, 2000031601, 57, 18, 'themes', 'Themes', 'Themes for KDE.', 0, 0, 'Topic :: Desktop Environment :: K Desktop Environment(KDE) :: Themes', '18 :: 55 :: 57 :: 61');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(62, 2000031601, 55, 18, 'screensavers', 'Screen Savers', 'Screen savers and lockers.', 0, 0, 'Topic :: Desktop Environment :: Screen Savers', '18 :: 55 :: 62');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(63, 2000032001, 18, 18, 'editors', 'Text Editors', 'Programs for editing code and documents.', 0, 0, 'Topic :: Text Editors', '18 :: 63');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(64, 2000031601, 63, 18, 'emacs', 'Emacs', 'GNU Emacs and its imitators and tools.', 0, 0, 'Topic :: Text Editors :: Emacs', '18 :: 63 :: 64');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(65, 2000031601, 63, 18, 'ide', 'Integrated Development Environments(IDE)', 'Complete editing environments for code, including cababilities such as compilation and code building assistance.', 0, 0, 'Topic :: Text Editors :: Integrated Development Environments(IDE)', '18 :: 63 :: 65');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(66, 2000031601, 18, 18, 'database', 'Database', 'Front ends, engines, and tools for database work.', 0, 0, 'Topic :: Database', '18 :: 66');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(67, 2000031601, 66, 18, 'engines', 'Database Engines/Servers', 'Programs that manage data and provide control via some query language.', 0, 0, 'Topic :: Database :: Database Engines/Servers', '18 :: 66 :: 67');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(68, 2000031601, 66, 18, 'frontends', 'Front-Ends', 'Clients and front-ends for generating queries to database engines.', 0, 0, 'Topic :: Database :: Front-Ends', '18 :: 66 :: 68');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(69, 2000031601, 63, 18, 'documentation', 'Documentation', 'Tools for the creation and use of documentation.', 0, 0, 'Topic :: Text Editors :: Documentation', '18 :: 63 :: 69');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(70, 2000031601, 63, 18, 'wordprocessors', 'Word Processors', 'WYSIWYG word processors.', 0, 0, 'Topic :: Text Editors :: Word Processors', '18 :: 63 :: 70');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(71, 2000031601, 18, 18, 'education', 'Education', 'Programs and tools for educating yourself or others.', 0, 0, 'Topic :: Education', '18 :: 71');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(72, 2000031601, 71, 18, 'cai', 'Computer Aided Instruction(CAI)', 'Programs for authoring or using Computer Aided Instrution courses.', 0, 0, 'Topic :: Education :: Computer Aided Instruction(CAI)', '18 :: 71 :: 72');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(73, 2000031601, 71, 18, 'testing', 'Testing', 'Tools for testing someone\'s knowledge on a subject.', 0, 0, 'Topic :: Education :: Testing', '18 :: 71 :: 73');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(74, 2000042701, 136, 18, 'emulators', 'Emulators', 'Emulations of foreign operating systme and machines.', 0, 0, 'Topic :: System :: Emulators', '18 :: 136 :: 74');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(75, 2000031701, 129, 18, 'financial', 'Financial', 'Programs related to finance.', 0, 0, 'Topic :: Office/Business :: Financial', '18 :: 129 :: 75');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(76, 2000031601, 75, 18, 'accounting', 'Accounting', 'Checkbook balancers and accounting programs.', 0, 0, 'Topic :: Office/Business :: Financial :: Accounting', '18 :: 129 :: 75 :: 76');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(77, 2000031601, 75, 18, 'investment', 'Investment', 'Programs for assisting in financial investment.', 0, 0, 'Topic :: Office/Business :: Financial :: Investment', '18 :: 129 :: 75 :: 77');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(78, 2000031601, 75, 18, 'spreadsheet', 'Spreadsheet', 'Spreadsheet applications.', 0, 0, 'Topic :: Office/Business :: Financial :: Spreadsheet', '18 :: 129 :: 75 :: 78');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(79, 2000031601, 75, 18, 'pointofsale', 'Point-Of-Sale', 'Point-Of-Sale applications.', 0, 0, 'Topic :: Office/Business :: Financial :: Point-Of-Sale', '18 :: 129 :: 75 :: 79');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(80, 2000031601, 18, 18, 'games', 'Games/Entertainment', 'Games and Entertainment software.', 0, 0, 'Topic :: Games/Entertainment', '18 :: 80');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(81, 2000031601, 80, 18, 'realtimestrategy', 'Real Time Strategy', 'Real Time strategy games', 0, 0, 'Topic :: Games/Entertainment :: Real Time Strategy', '18 :: 80 :: 81');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(82, 2000031601, 80, 18, 'firstpersonshooters', 'First Person Shooters', 'First Person Shooters.', 0, 0, 'Topic :: Games/Entertainment :: First Person Shooters', '18 :: 80 :: 82');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(83, 2000032401, 80, 18, 'turnbasedstrategy', 'Turn Based Strategy', 'Turn Based Strategy', 0, 0, 'Topic :: Games/Entertainment :: Turn Based Strategy', '18 :: 80 :: 83');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(84, 2000031601, 80, 18, 'rpg', 'Role-Playing', 'Role-Playing games', 0, 0, 'Topic :: Games/Entertainment :: Role-Playing', '18 :: 80 :: 84');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(85, 2000031601, 80, 18, 'simulation', 'Simulation', 'Simulation games', 0, 0, 'Topic :: Games/Entertainment :: Simulation', '18 :: 80 :: 85');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(86, 2000031601, 80, 18, 'mud', 'Multi-User Dungeons(MUD)', 'Massively-multiplayer text based games.', 0, 0, 'Topic :: Games/Entertainment :: Multi-User Dungeons(MUD)', '18 :: 80 :: 86');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(87, 2000031601, 18, 18, 'internet', 'Internet', 'Tools to assist human access to the Internet.', 0, 0, 'Topic :: Internet', '18 :: 87');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(88, 2000031601, 87, 18, 'finger', 'Finger', 'The Finger protocol for getting information about users.', 0, 0, 'Topic :: Internet :: Finger', '18 :: 87 :: 88');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(89, 2000031601, 87, 18, 'ftp', 'File Transfer Protocol(FTP)', 'Programs and tools for file transfer via FTP.', 0, 0, 'Topic :: Internet :: File Transfer Protocol(FTP)', '18 :: 87 :: 89');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(90, 2000031601, 87, 18, 'www', 'WWW/HTTP', 'Programs and tools for the World Wide Web.', 0, 0, 'Topic :: Internet :: WWW/HTTP', '18 :: 87 :: 90');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(91, 2000031601, 90, 18, 'browsers', 'Browsers', 'Web Browsers', 0, 0, 'Topic :: Internet :: WWW/HTTP :: Browsers', '18 :: 87 :: 90 :: 91');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(92, 2000031601, 90, 18, 'dynamic', 'Dynamic Content', 'Common Gateway Interface scripting and server-side parsing.', 0, 0, 'Topic :: Internet :: WWW/HTTP :: Dynamic Content', '18 :: 87 :: 90 :: 92');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(93, 2000031601, 90, 18, 'indexing', 'Indexing/Search', 'Indexing and search tools for the Web.', 0, 0, 'Topic :: Internet :: WWW/HTTP :: Indexing/Search', '18 :: 87 :: 90 :: 93');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(94, 2000031601, 92, 18, 'counters', 'Page Counters', 'Scripts to count numbers of pageviews.', 0, 0, 'Topic :: Internet :: WWW/HTTP :: Dynamic Content :: Page Counters', '18 :: 87 :: 90 :: 92 :: 94');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(95, 2000031601, 92, 18, 'messageboards', 'Message Boards', 'Online message boards', 0, 0, 'Topic :: Internet :: WWW/HTTP :: Dynamic Content :: Message Boards', '18 :: 87 :: 90 :: 92 :: 95');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(96, 2000031601, 92, 18, 'cgi', 'CGI Tools/Libraries', 'Tools for the Common Gateway Interface', 0, 0, 'Topic :: Internet :: WWW/HTTP :: Dynamic Content :: CGI Tools/Libraries', '18 :: 87 :: 90 :: 92 :: 96');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(97, 2000042701, 18, 18, 'scientific', 'Scientific/Engineering', 'Scientific applications, to include research, applied and pure mathematics and sciences.', 0, 0, 'Topic :: Scientific/Engineering', '18 :: 97');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(98, 2000031601, 97, 18, 'mathematics', 'Mathematics', 'Software to support pure and applied mathematics.', 0, 0, 'Topic :: Scientific/Engineering :: Mathematics', '18 :: 97 :: 98');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(99, 2000031601, 18, 18, 'multimedia', 'Multimedia', 'Graphics, sound, video, and multimedia.', 0, 0, 'Topic :: Multimedia', '18 :: 99');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(100, 2000031601, 99, 18, 'graphics', 'Graphics', 'Tools and resources for computer graphics.', 0, 0, 'Topic :: Multimedia :: Graphics', '18 :: 99 :: 100');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(101, 2000031601, 100, 18, 'capture', 'Capture', 'Support for scanners, cameras, and screen capture.', 0, 0, 'Topic :: Multimedia :: Graphics :: Capture', '18 :: 99 :: 100 :: 101');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(102, 2000031601, 101, 18, 'scanners', 'Scanners', 'Support for graphic scanners.', 0, 0, 'Topic :: Multimedia :: Graphics :: Capture :: Scanners', '18 :: 99 :: 100 :: 101 :: 102');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(103, 2000031601, 101, 18, 'cameras', 'Digital Camera', 'Digital Camera', 0, 0, 'Topic :: Multimedia :: Graphics :: Capture :: Digital Camera', '18 :: 99 :: 100 :: 101 :: 103');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(104, 2000031601, 101, 18, 'screencapture', 'Screen Capture', 'Screen capture tools and processors.', 0, 0, 'Topic :: Multimedia :: Graphics :: Capture :: Screen Capture', '18 :: 99 :: 100 :: 101 :: 104');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(105, 2000031701, 100, 18, 'conversion', 'Graphics Conversion', 'Programs which convert between graphics formats.', 0, 0, 'Topic :: Multimedia :: Graphics :: Graphics Conversion', '18 :: 99 :: 100 :: 105');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(106, 2000031701, 100, 18, 'editors', 'Editors', 'Drawing, painting, and structured editing programs.', 0, 0, 'Topic :: Multimedia :: Graphics :: Editors', '18 :: 99 :: 100 :: 106');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(107, 2000031701, 106, 18, 'vector', 'Vector-Based', 'Vector-Based drawing programs.', 0, 0, 'Topic :: Multimedia :: Graphics :: Editors :: Vector-Based', '18 :: 99 :: 100 :: 106 :: 107');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(108, 2000031701, 106, 18, 'raster', 'Raster-Based', 'Raster/Bitmap based drawing programs.', 0, 0, 'Topic :: Multimedia :: Graphics :: Editors :: Raster-Based', '18 :: 99 :: 100 :: 106 :: 108');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(109, 2000031701, 100, 18, '3dmodeling', '3D Modeling', 'Programs for working with 3D Models.', 0, 0, 'Topic :: Multimedia :: Graphics :: 3D Modeling', '18 :: 99 :: 100 :: 109');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(110, 2000031701, 100, 18, '3drendering', '3D Rendering', 'Programs which render 3D models.', 0, 0, 'Topic :: Multimedia :: Graphics :: 3D Rendering', '18 :: 99 :: 100 :: 110');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(111, 2000031701, 100, 18, 'presentation', 'Presentation', 'Tools for generating presentation graphics and slides.', 0, 0, 'Topic :: Multimedia :: Graphics :: Presentation', '18 :: 99 :: 100 :: 111');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(112, 2000031701, 100, 18, 'viewers', 'Viewers', 'Programs that can display various graphics formats.', 0, 0, 'Topic :: Multimedia :: Graphics :: Viewers', '18 :: 99 :: 100 :: 112');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(113, 2000031701, 99, 18, 'sound', 'Sound/Audio', 'Tools for generating, editing, analyzing, and playing sound.', 0, 0, 'Topic :: Multimedia :: Sound/Audio', '18 :: 99 :: 113');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(114, 2000031701, 113, 18, 'analysis', 'Analysis', 'Sound analysis tools, to include frequency analysis.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Analysis', '18 :: 99 :: 113 :: 114');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(115, 2000031701, 113, 18, 'capture', 'Capture/Recording', 'Sound capture and recording.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Capture/Recording', '18 :: 99 :: 113 :: 115');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(116, 2000031701, 113, 18, 'cdaudio', 'CD Audio', 'Programs to play and manipulate audio CDs.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: CD Audio', '18 :: 99 :: 113 :: 116');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(117, 2000031701, 116, 18, 'cdplay', 'CD Playing', 'CD Playing software, to include jukebox software.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: CD Audio :: CD Playing', '18 :: 99 :: 113 :: 116 :: 117');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(118, 2000031701, 116, 18, 'cdripping', 'CD Ripping', 'Software to convert CD Audio to other digital formats.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: CD Audio :: CD Ripping', '18 :: 99 :: 113 :: 116 :: 118');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(119, 2000031701, 113, 18, 'conversion', 'Conversion', 'Programs to convert between audio formats.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Conversion', '18 :: 99 :: 113 :: 119');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(120, 2000031701, 113, 18, 'editors', 'Editors', 'Programs to edit/manipulate sound data.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Editors', '18 :: 99 :: 113 :: 120');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(121, 2000031701, 113, 18, 'mixers', 'Mixers', 'Programs to mix audio.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Mixers', '18 :: 99 :: 113 :: 121');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(122, 2000031701, 113, 18, 'players', 'Players', 'Programs to play audio files to a sound device.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Players', '18 :: 99 :: 113 :: 122');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(123, 2000031701, 122, 18, 'mp3', 'MP3', 'Programs to play MP3 audio files.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Players :: MP3', '18 :: 99 :: 113 :: 122 :: 123');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(124, 2000031701, 113, 18, 'speech', 'Speech', 'Speech manipulation and intepretation tools.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Speech', '18 :: 99 :: 113 :: 124');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(125, 2000031701, 99, 18, 'video', 'Video', 'Video capture, editing, and playback.', 0, 0, 'Topic :: Multimedia :: Video', '18 :: 99 :: 125');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(126, 2000031701, 125, 18, 'capture', 'Capture', 'Video capture tools.', 0, 0, 'Topic :: Multimedia :: Video :: Capture', '18 :: 99 :: 125 :: 126');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(127, 2000031701, 125, 18, 'conversion', 'Conversion', 'Programs which convert between video formats.', 0, 0, 'Topic :: Multimedia :: Video :: Conversion', '18 :: 99 :: 125 :: 127');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(128, 2000031701, 125, 18, 'display', 'Display', 'Programs which display various video formats.', 0, 0, 'Topic :: Multimedia :: Video :: Display', '18 :: 99 :: 125 :: 128');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(129, 2000031701, 18, 18, 'office', 'Office/Business', 'Software for assisting and organizing work at your desk.', 0, 0, 'Topic :: Office/Business', '18 :: 129');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(130, 2000031701, 129, 18, 'scheduling', 'Scheduling', 'Projects for scheduling time, to include project management.', 0, 0, 'Topic :: Office/Business :: Scheduling', '18 :: 129 :: 130');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(131, 2000032001, 129, 18, 'suites', 'Office Suites', 'Integrated office suites(word processing, presentation, spreadsheet, database, etc).', 0, 0, 'Topic :: Office/Business :: Office Suites', '18 :: 129 :: 131');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(132, 2000032001, 18, 18, 'religion', 'Religion', 'Programs relating to religion and sacred texts.', 0, 0, 'Topic :: Religion', '18 :: 132');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(133, 2000032001, 97, 18, 'ai', 'Artificial Intelligence', 'Artificial Intelligence.', 0, 0, 'Topic :: Scientific/Engineering :: Artificial Intelligence', '18 :: 97 :: 133');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(134, 2000032001, 97, 18, 'astronomy', 'Astronomy', 'Software and tools related to astronomy.', 0, 0, 'Topic :: Scientific/Engineering :: Astronomy', '18 :: 97 :: 134');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(135, 2000032001, 97, 18, 'visualization', 'Visualization', 'Software for scientific visualization.', 0, 0, 'Topic :: Scientific/Engineering :: Visualization', '18 :: 97 :: 135');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(136, 2000032001, 18, 18, 'system', 'System', 'Operating system core and administration utilities.', 0, 0, 'Topic :: System', '18 :: 136');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(137, 2000032001, 19, 18, 'backup', 'Backup', 'Programs to manage and sequence system backup.', 0, 0, 'Topic :: System :: Archiving :: Backup', '18 :: 136 :: 19 :: 137');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(138, 2000032001, 136, 18, 'benchmark', 'Benchmark', 'Programs for benchmarking system performance.', 0, 0, 'Topic :: System :: Benchmark', '18 :: 136 :: 138');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(139, 2000032001, 136, 18, 'boot', 'Boot', 'Programs for bootstrapping your OS.', 0, 0, 'Topic :: System :: Boot', '18 :: 136 :: 139');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(140, 2000032001, 139, 18, 'init', 'Init', 'Init-time programs to start system services after boot.', 0, 0, 'Topic :: System :: Boot :: Init', '18 :: 136 :: 139 :: 140');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(141, 2000032001, 136, 18, 'clustering', 'Clustering/Distributed Networks', 'Tools for automatically distributing computation across a network.', 0, 0, 'Topic :: System :: Clustering/Distributed Networks', '18 :: 136 :: 141');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(142, 2000032001, 136, 18, 'filesystems', 'Filesystems', 'Support for creating, editing, reading, and writing file systems.', 0, 0, 'Topic :: System :: Filesystems', '18 :: 136 :: 142');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(143, 2000032001, 144, 18, 'linux', 'Linux', 'The Linux kernel, patches, and modules.', 0, 0, 'Topic :: System :: Operating System Kernels :: Linux', '18 :: 136 :: 144 :: 143');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(144, 2000032001, 136, 18, 'kernels', 'Operating System Kernels', 'OS Kernels, patches, modules, and tools.', 0, 0, 'Topic :: System :: Operating System Kernels', '18 :: 136 :: 144');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(145, 2000032001, 144, 18, 'bsd', 'BSD', 'Code relating to any of the BSD kernels.', 0, 0, 'Topic :: System :: Operating System Kernels :: BSD', '18 :: 136 :: 144 :: 145');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(146, 2000032001, 136, 18, 'hardware', 'Hardware', 'Tools for direct, non-kernel control and configuration of hardware.', 0, 0, 'Topic :: System :: Hardware', '18 :: 136 :: 146');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(147, 2000032001, 136, 18, 'setup', 'Installation/Setup', 'Tools for installation and setup of the operating system and other programs.', 0, 0, 'Topic :: System :: Installation/Setup', '18 :: 136 :: 147');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(148, 2000032001, 136, 18, 'logging', 'Logging', 'Utilities for clearing, rotating, and digesting system logs.', 0, 0, 'Topic :: System :: Logging', '18 :: 136 :: 148');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(149, 2000032001, 87, 18, 'dns', 'Name Service(DNS)', 'Domain name system servers and utilities.', 0, 0, 'Topic :: Internet :: Name Service(DNS)', '18 :: 87 :: 149');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(150, 2000032001, 136, 18, 'networking', 'Networking', 'Network configuration and administration.', 0, 0, 'Topic :: System :: Networking', '18 :: 136 :: 150');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(151, 2000032001, 150, 18, 'firewalls', 'Firewalls', 'Firewalls and filtering systems.', 0, 0, 'Topic :: System :: Networking :: Firewalls', '18 :: 136 :: 150 :: 151');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(152, 2000032001, 150, 18, 'monitoring', 'Monitoring', 'System monitoring, traffic analysis, and sniffers.', 0, 0, 'Topic :: System :: Networking :: Monitoring', '18 :: 136 :: 150 :: 152');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(153, 2000032001, 136, 18, 'power', 'Power(UPS)', 'Code for communication with uninterruptible power supplies.', 0, 0, 'Topic :: System :: Power(UPS)', '18 :: 136 :: 153');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(154, 2000032001, 18, 18, 'printing', 'Printing', 'Tools, daemons, and utilities for printer control.', 0, 0, 'Topic :: Printing', '18 :: 154');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(155, 2000032001, 152, 18, 'watchdog', 'Hardware Watchdog', 'Software to monitor and perform actions or shutdown on hardware trouble detection.', 0, 0, 'Topic :: System :: Networking :: Monitoring :: Hardware Watchdog', '18 :: 136 :: 150 :: 152 :: 155');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(156, 2000032001, 18, 18, 'terminals', 'Terminals', 'Terminal emulators, terminal programs, and terminal session utilities.', 0, 0, 'Topic :: Terminals', '18 :: 156');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(157, 2000032001, 156, 18, 'serial', 'Serial', 'Dialup, terminal emulation, and file transfer over serial lines.', 0, 0, 'Topic :: Terminals :: Serial', '18 :: 156 :: 157');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(158, 2000032001, 156, 18, 'virtual', 'Terminal Emulators/X Terminals', 'Programs to handle multiple terminal sessions. Includes terminal emulations for X and other window systems.', 0, 0, 'Topic :: Terminals :: Terminal Emulators/X Terminals', '18 :: 156 :: 158');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(159, 2000032001, 156, 18, 'telnet', 'Telnet', 'Support for telnet; terminal sessions across Internet links.', 0, 0, 'Topic :: Terminals :: Telnet', '18 :: 156 :: 159');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(160, 2000032001, 0, 0, 'language', 'Programming Language', 'Language in which this program was written, or was meant to support.', 0, 0, 'Programming Language', '160');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(161, 2000032001, 160, 160, 'apl', 'APL', 'APL', 0, 0, 'Programming Language :: APL', '160 :: 161');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(164, 2000032001, 160, 160, 'c', 'C', 'C', 0, 0, 'Programming Language :: C', '160 :: 164');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(162, 2000032001, 160, 160, 'assembly', 'Assembly', 'Assembly-level programs. Platform specific.', 0, 0, 'Programming Language :: Assembly', '160 :: 162');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(163, 2000051001, 160, 160, 'ada', 'Ada', 'Ada', 0, 0, 'Programming Language :: Ada', '160 :: 163');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(165, 2000032001, 160, 160, 'cpp', 'C++', 'C++', 0, 0, 'Programming Language :: C++', '160 :: 165');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(166, 2000032401, 160, 160, 'eiffel', 'Eiffel', 'Eiffel', 0, 0, 'Programming Language :: Eiffel', '160 :: 166');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(167, 2000032001, 160, 160, 'euler', 'Euler', 'Euler', 0, 0, 'Programming Language :: Euler', '160 :: 167');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(168, 2000032001, 160, 160, 'forth', 'Forth', 'Forth', 0, 0, 'Programming Language :: Forth', '160 :: 168');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(169, 2000032001, 160, 160, 'fortran', 'Fortran', 'Fortran', 0, 0, 'Programming Language :: Fortran', '160 :: 169');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(170, 2000032001, 160, 160, 'lisp', 'Lisp', 'Lisp', 0, 0, 'Programming Language :: Lisp', '160 :: 170');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(171, 2000041101, 160, 160, 'logo', 'Logo', 'Logo', 0, 0, 'Programming Language :: Logo', '160 :: 171');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(172, 2000032001, 160, 160, 'ml', 'ML', 'ML', 0, 0, 'Programming Language :: ML', '160 :: 172');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(173, 2000032001, 160, 160, 'modula', 'Modula', 'Modula-2 or Modula-3', 0, 0, 'Programming Language :: Modula', '160 :: 173');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(174, 2000032001, 160, 160, 'objectivec', 'Objective C', 'Objective C', 0, 0, 'Programming Language :: Objective C', '160 :: 174');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(175, 2000032001, 160, 160, 'pascal', 'Pascal', 'Pascal', 0, 0, 'Programming Language :: Pascal', '160 :: 175');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(176, 2000032001, 160, 160, 'perl', 'Perl', 'Perl', 0, 0, 'Programming Language :: Perl', '160 :: 176');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(177, 2000032001, 160, 160, 'prolog', 'Prolog', 'Prolog', 0, 0, 'Programming Language :: Prolog', '160 :: 177');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(178, 2000032001, 160, 160, 'python', 'Python', 'Python', 0, 0, 'Programming Language :: Python', '160 :: 178');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(179, 2000032001, 160, 160, 'rexx', 'Rexx', 'Rexx', 0, 0, 'Programming Language :: Rexx', '160 :: 179');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(180, 2000032001, 160, 160, 'simula', 'Simula', 'Simula', 0, 0, 'Programming Language :: Simula', '160 :: 180');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(181, 2000032001, 160, 160, 'smalltalk', 'Smalltalk', 'Smalltalk', 0, 0, 'Programming Language :: Smalltalk', '160 :: 181');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(182, 2000032001, 160, 160, 'tcl', 'Tcl', 'Tcl', 0, 0, 'Programming Language :: Tcl', '160 :: 182');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(183, 2000032001, 160, 160, 'php', 'PHP', 'PHP', 0, 0, 'Programming Language :: PHP', '160 :: 183');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(184, 2000032001, 160, 160, 'asp', 'ASP', 'Active Server Pages', 0, 0, 'Programming Language :: ASP', '160 :: 184');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(185, 2000032001, 160, 160, 'shell', 'Unix Shell', 'Unix Shell', 0, 0, 'Programming Language :: Unix Shell', '160 :: 185');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(186, 2000032001, 160, 160, 'visualbasic', 'Visual Basic', 'Visual Basic', 0, 0, 'Programming Language :: Visual Basic', '160 :: 186');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(187, 2000032001, 14, 13, 'bsd', 'BSD License', 'BSD License', 0, 0, 'License :: OSI Approved :: BSD License', '13 :: 14 :: 187');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(188, 2000032001, 14, 13, 'mit', 'MIT/X Consortium License', 'MIT License, also the X Consortium License.', 0, 0, 'License :: OSI Approved :: MIT/X Consortium License', '13 :: 14 :: 188');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(189, 2000032001, 14, 13, 'mpl', 'Mozilla Public License(MPL)', 'Mozilla Public License(MPL)', 0, 0, 'License :: OSI Approved :: Mozilla Public License(MPL)', '13 :: 14 :: 189');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(190, 2000032001, 14, 13, 'qpl', 'QT Public License(QPL)', 'QT Public License', 0, 0, 'License :: OSI Approved :: QT Public License(QPL)', '13 :: 14 :: 190');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(191, 2000032001, 14, 13, 'ibm', 'IBM Public License', 'IBM Public License', 0, 0, 'License :: OSI Approved :: IBM Public License', '13 :: 14 :: 191');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(192, 2000032001, 14, 13, 'cvw', 'MITRE Collaborative Virtual Workspace License(CVW)', 'MITRE Collaborative Virtual Workspace License(CVW)', 0, 0, 'License :: OSI Approved :: MITRE Collaborative Virtual Workspace License(CVW)', '13 :: 14 :: 192');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(193, 2000032001, 14, 13, 'ricoh', 'Ricoh Source Code Public License', 'Ricoh Source Code Public License', 0, 0, 'License :: OSI Approved :: Ricoh Source Code Public License', '13 :: 14 :: 193');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(194, 2000032001, 14, 13, 'python', 'Python License', 'Python License', 0, 0, 'License :: OSI Approved :: Python License', '13 :: 14 :: 194');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(195, 2000032001, 14, 13, 'zlib', 'zlib/libpng License', 'zlib/libpng License', 0, 0, 'License :: OSI Approved :: zlib/libpng License', '13 :: 14 :: 195');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(196, 2000040701, 13, 13, 'other', 'Other/Proprietary License', 'Non OSI-Approved/Proprietary license.', 0, 0, 'License :: Other/Proprietary License', '13 :: 196');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(197, 2000032001, 13, 13, 'publicdomain', 'Public Domain', 'Public Domain. No author-retained rights.', 0, 0, 'License :: Public Domain', '13 :: 197');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(198, 2000032001, 160, 160, 'java', 'Java', 'Java', 0, 0, 'Programming Language :: Java', '160 :: 198');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(199, 2000032101, 0, 0, 'os', 'Operating System', 'What operating system the program requires to run, if any.', 0, 0, 'Operating System', '199');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(200, 2000032101, 199, 199, 'posix', 'POSIX', 'POSIX plus standard Berkeley socket facilities. Don\'t list a more specific OS unless your program requires it.', 0, 0, 'Operating System :: POSIX', '199 :: 200');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(201, 2000032101, 200, 199, 'linux', 'Linux', 'Any version of Linux. Don\'t specify a subcategory unless the program requires a particular distribution.', 0, 0, 'Operating System :: POSIX :: Linux', '199 :: 200 :: 201');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(202, 2000032101, 200, 199, 'bsd', 'BSD', 'Any variant of BSD. Don\'t specify a subcategory unless the program requires a particular BSD flavor.', 0, 0, 'Operating System :: POSIX :: BSD', '199 :: 200 :: 202');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(203, 2000041101, 202, 199, 'freebsd', 'FreeBSD', 'FreeBSD', 0, 0, 'Operating System :: POSIX :: BSD :: FreeBSD', '199 :: 200 :: 202 :: 203');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(204, 2000032101, 202, 199, 'netbsd', 'NetBSD', 'NetBSD', 0, 0, 'Operating System :: POSIX :: BSD :: NetBSD', '199 :: 200 :: 202 :: 204');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(205, 2000032101, 202, 199, 'openbsd', 'OpenBSD', 'OpenBSD', 0, 0, 'Operating System :: POSIX :: BSD :: OpenBSD', '199 :: 200 :: 202 :: 205');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(206, 2000032101, 202, 199, 'bsdos', 'BSD/OS', 'BSD/OS', 0, 0, 'Operating System :: POSIX :: BSD :: BSD/OS', '199 :: 200 :: 202 :: 206');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(207, 2000032101, 200, 199, 'sun', 'SunOS/Solaris', 'Any Sun Microsystems OS.', 0, 0, 'Operating System :: POSIX :: SunOS/Solaris', '199 :: 200 :: 207');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(208, 2000032101, 200, 199, 'sco', 'SCO', 'SCO', 0, 0, 'Operating System :: POSIX :: SCO', '199 :: 200 :: 208');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(209, 2000032101, 200, 199, 'hpux', 'HP-UX', 'HP-UX', 0, 0, 'Operating System :: POSIX :: HP-UX', '199 :: 200 :: 209');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(210, 2000032101, 200, 199, 'aix', 'AIX', 'AIX', 0, 0, 'Operating System :: POSIX :: AIX', '199 :: 200 :: 210');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(211, 2000032101, 200, 199, 'irix', 'IRIX', 'IRIX', 0, 0, 'Operating System :: POSIX :: IRIX', '199 :: 200 :: 211');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(212, 2000032101, 200, 199, 'other', 'Other', 'Other specific POSIX OS, specified in description.', 0, 0, 'Operating System :: POSIX :: Other', '199 :: 200 :: 212');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(213, 2000032101, 160, 160, 'other', 'Other', 'Other programming language, specified in description.', 0, 0, 'Programming Language :: Other', '160 :: 213');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(214, 2000032101, 199, 199, 'microsoft', 'Microsoft', 'Microsoft operating systems.', 0, 0, 'Operating System :: Microsoft', '199 :: 214');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(215, 2000032101, 214, 199, 'msdos', 'MS-DOS', 'Microsoft Disk Operating System(DOS)', 0, 0, 'Operating System :: Microsoft :: MS-DOS', '199 :: 214 :: 215');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(216, 2000032101, 214, 199, 'windows', 'Windows', 'Windows software, not specific to any particular version of Windows.', 0, 0, 'Operating System :: Microsoft :: Windows', '199 :: 214 :: 216');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(217, 2000032101, 216, 199, 'win31', 'Windows 3.1 or Earlier', 'Windows 3.1 or Earlier', 0, 0, 'Operating System :: Microsoft :: Windows :: Windows 3.1 or Earlier', '199 :: 214 :: 216 :: 217');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(218, 2000032101, 216, 199, 'win95', 'Windows 95/98/2000', 'Windows 95, Windows 98, and Windows 2000.', 0, 0, 'Operating System :: Microsoft :: Windows :: Windows 95/98/2000', '199 :: 214 :: 216 :: 218');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(219, 2000041101, 216, 199, 'winnt', 'Windows NT/2000', 'Windows NT and Windows 2000.', 0, 0, 'Operating System :: Microsoft :: Windows :: Windows NT/2000', '199 :: 214 :: 216 :: 219');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(220, 2000032101, 199, 199, 'os2', 'OS/2', 'OS/2', 0, 0, 'Operating System :: OS/2', '199 :: 220');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(221, 2000032101, 199, 199, 'macos', 'MacOS', 'MacOS', 0, 0, 'Operating System :: MacOS', '199 :: 221');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(222, 2000032101, 216, 199, 'wince', 'Windows CE', 'Windows CE', 0, 0, 'Operating System :: Microsoft :: Windows :: Windows CE', '199 :: 214 :: 216 :: 222');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(223, 2000032101, 199, 199, 'palmos', 'PalmOS', 'PalmOS(for Palm Pilot)', 0, 0, 'Operating System :: PalmOS', '199 :: 223');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(224, 2000032101, 199, 199, 'beos', 'BeOS', 'BeOS', 0, 0, 'Operating System :: BeOS', '199 :: 224');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(225, 2000032101, 0, 0, 'environment', 'Environment', 'Run-time environment required for this program.', 0, 0, 'Environment', '225');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(226, 2000041101, 225, 225, 'console', 'Console(Text Based)', 'Console-based programs.', 0, 0, 'Environment :: Console(Text Based)', '225 :: 226');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(227, 2000032401, 226, 225, 'curses', 'Curses', 'Curses-based software.', 0, 0, 'Environment :: Console(Text Based) :: Curses', '225 :: 226 :: 227');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(228, 2002031201, 226, 225, 'newt', 'Newt', 'Newt', 0, 0, 'Environment :: Console(Text Based) :: Newt', '225 :: 226 :: 228');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(229, 2000040701, 225, 225, 'x11', 'X11 Applications', 'Programs that run in an X windowing environment.', 0, 0, 'Environment :: X11 Applications', '225 :: 229');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(230, 2000040701, 225, 225, 'win32', 'Win32(MS Windows)', 'Programs designed to run in a graphical Microsoft Windows environment.', 0, 0, 'Environment :: Win32(MS Windows)', '225 :: 230');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(231, 2000040701, 229, 225, 'gnome', 'Gnome', 'Programs designed to run in a Gnome environment.', 0, 0, 'Environment :: X11 Applications :: Gnome', '225 :: 229 :: 231');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(232, 2000040701, 229, 225, 'kde', 'KDE', 'Programs designed to run in a KDE environment.', 0, 0, 'Environment :: X11 Applications :: KDE', '225 :: 229 :: 232');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(233, 2000040701, 225, 225, 'other', 'Other Environment', 'Programs designed to run in an environment other than one listed.', 0, 0, 'Environment :: Other Environment', '225 :: 233');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(234, 2000040701, 18, 18, 'other', 'Other/Nonlisted Topic', 'Topic does not fit into any listed category.', 0, 0, 'Topic :: Other/Nonlisted Topic', '18 :: 234');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(235, 2000041001, 199, 199, 'independent', 'OS Independent', 'This software does not depend on any particular operating system.', 0, 0, 'Operating System :: OS Independent', '199 :: 235');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(236, 2000040701, 199, 199, 'other', 'Other OS', 'Program is designe for a nonlisted operating system.', 0, 0, 'Operating System :: Other OS', '199 :: 236');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(237, 2000041001, 225, 225, 'web', 'Web Environment', 'This software is designed for a web environment.', 0, 0, 'Environment :: Web Environment', '225 :: 237');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(238, 2000041101, 225, 225, 'daemon', 'No Input/Output(Daemon)', 'This program has no input or output, but is intended to run in the background as a daemon.', 0, 0, 'Environment :: No Input/Output(Daemon)', '225 :: 238');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(239, 2000041301, 144, 18, 'gnuhurd', 'GNU Hurd', 'Kernel code and modules for GNU Hurd.', 0, 0, 'Topic :: System :: Operating System Kernels :: GNU Hurd', '18 :: 136 :: 144 :: 239');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(240, 2000041301, 200, 199, 'gnuhurd', 'GNU Hurd', 'GNU Hurd', 0, 0, 'Operating System :: POSIX :: GNU Hurd', '199 :: 200 :: 240');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(241, 2000050101, 251, 18, 'napster', 'Napster', 'Clients and servers for the Napster file sharing protocol.', 0, 0, 'Topic :: Communications :: File Sharing :: Napster', '18 :: 20 :: 251 :: 241');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(242, 2000042701, 160, 160, 'scheme', 'Scheme', 'Scheme programming language.', 0, 0, 'Programming Language :: Scheme', '160 :: 242');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(243, 2000042701, 90, 18, 'sitemanagement', 'Site Management', 'Tools for maintanance and management of web sites.', 0, 0, 'Topic :: Internet :: WWW/HTTP :: Site Management', '18 :: 87 :: 90 :: 243');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(244, 2000042701, 243, 18, 'linkchecking', 'Link Checking', 'Tools to assist in checking for broken links.', 0, 0, 'Topic :: Internet :: WWW/HTTP :: Site Management :: Link Checking', '18 :: 87 :: 90 :: 243 :: 244');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(245, 2000042701, 87, 18, 'loganalysis', 'Log Analysis', 'Software to help analyze various log files.', 0, 0, 'Topic :: Internet :: Log Analysis', '18 :: 87 :: 245');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(246, 2000042701, 97, 18, 'eda', 'Electronic Design Automation(EDA)', 'Tools for circuit design, schematics, board layout, and more.', 0, 0, 'Topic :: Scientific/Engineering :: Electronic Design Automation(EDA)', '18 :: 97 :: 246');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(247, 2000042701, 20, 18, 'telephony', 'Telephony', 'Telephony related applications, to include automated voice response systems.', 0, 0, 'Topic :: Communications :: Telephony', '18 :: 20 :: 247');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(248, 2000042801, 113, 18, 'midi', 'MIDI', 'Software related to MIDI synthesis and playback.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: MIDI', '18 :: 99 :: 113 :: 248');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(249, 2000042801, 113, 18, 'synthesis', 'Sound Synthesis', 'Software for creation and synthesis of sound.', 0, 0, 'Topic :: Multimedia :: Sound/Audio :: Sound Synthesis', '18 :: 99 :: 113 :: 249');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(250, 2000042801, 90, 18, 'httpservers', 'HTTP Servers', 'Software designed to serve content via the HTTP protocol.', 0, 0, 'Topic :: Internet :: WWW/HTTP :: HTTP Servers', '18 :: 87 :: 90 :: 250');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(251, 2000050101, 20, 18, 'filesharing', 'File Sharing', 'Software for person-to-person online file sharing.', 0, 0, 'Topic :: Communications :: File Sharing', '18 :: 20 :: 251');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(252, 2000071101, 97, 18, 'bioinformatics', 'Bio-Informatics', 'Category for gene software(e.g. Gene Ontology)', 0, 0, 'Topic :: Scientific/Engineering :: Bio-Informatics', '18 :: 97 :: 252');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(253, 2000071101, 136, 18, 'sysadministration', 'Systems Administration', 'Systems Administration Software(e.g. configuration apps.)', 0, 0, 'Topic :: System :: Systems Administration', '18 :: 136 :: 253');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(254, 2000071101, 160, 160, 'plsql', 'PL/SQL', 'PL/SQL Programming Language', 0, 0, 'Programming Language :: PL/SQL', '160 :: 254');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(255, 2000071101, 160, 160, 'progress', 'PROGRESS', 'PROGRESS Programming Language', 0, 0, 'Programming Language :: PROGRESS', '160 :: 255');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(256, 2000071101, 125, 18, 'nonlineareditor', 'Non-Linear Editor', 'Video Non-Linear Editors', 0, 0, 'Topic :: Multimedia :: Video :: Non-Linear Editor', '18 :: 99 :: 125 :: 256');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(257, 2000071101, 136, 18, 'softwaredist', 'Software Distribution', 'Systems software for distributing other software.', 0, 0, 'Topic :: System :: Software Distribution', '18 :: 136 :: 257');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(258, 2000071101, 160, 160, 'objectpascal', 'Object Pascal', 'Object Pascal', 0, 0, 'Programming Language :: Object Pascal', '160 :: 258');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(259, 2000071401, 45, 18, 'codegen', 'Code Generators', 'Code Generators', 0, 0, 'Topic :: Software Development :: Code Generators', '18 :: 45 :: 259');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(260, 2000071401, 52, 18, 'SCCS', 'SCCS', 'SCCS', 0, 0, 'Topic :: Software Development :: Version Control :: SCCS', '18 :: 45 :: 52 :: 260');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(261, 2000072501, 160, 160, 'xbasic', 'XBasic', 'XBasic programming language', 0, 0, 'Programming Language :: XBasic', '160 :: 261');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(262, 2000073101, 160, 160, 'coldfusion', 'Cold Fusion', 'Cold Fusion Language', 0, 0, 'Programming Language :: Cold Fusion', '160 :: 262');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(263, 2000080401, 160, 160, 'euphoria', 'Euphoria', 'Euphoria programming language - http://www.rapideuphoria.com/', 0, 0, 'Programming Language :: Euphoria', '160 :: 263');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(264, 2000080701, 160, 160, 'erlang', 'Erlang', 'Erlang - developed by Ericsson - http://www.erlang.org/', 0, 0, 'Programming Language :: Erlang', '160 :: 264');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(265, 2000080801, 160, 160, 'Delphi', 'Delphi', 'Borland/Inprise Delphi', 0, 0, 'Programming Language :: Delphi', '160 :: 265');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(266, 2000081601, 97, 18, 'medical', 'Medical Science Apps.', 'Medical / BioMedical Science Apps.', 0, 0, 'Topic :: Scientific/Engineering :: Medical Science Apps.', '18 :: 97 :: 266');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(267, 2000082001, 160, 160, 'zope', 'Zope', 'Zope Object Publishing', 0, 0, 'Programming Language :: Zope', '160 :: 267');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(268, 2000082101, 80, 18, 'Puzzles', 'Puzzle Games', 'Puzzle Games', 0, 0, 'Topic :: Games/Entertainment :: Puzzle Games', '18 :: 80 :: 268');
INSERT INTO v2_xf_trove_cat(trove_cat_id, version, parent, root_parent, shortname, fullname, description, count_subcat, count_subproj, fullpath, fullpath_ids) VALUES(269, 2000082801, 160, 160, 'asm', 'Assembly', 'ASM programming', 0, 0, 'Programming Language :: Assembly', '160 :: 269');
# --------------------------------------------------------

#
# Table structure for table `v2_xf_trove_group_link`
#

DROP TABLE IF EXISTS v2_xf_trove_group_link;
CREATE TABLE v2_xf_trove_group_link(
  trove_group_id int(11) NOT NULL auto_increment,
  trove_cat_id int(11) NOT NULL default '0',
  trove_cat_version int(11) NOT NULL default '0',
  group_id int(11) NOT NULL default '0',
  trove_cat_root int(11) NOT NULL default '0',
  PRIMARY KEY (trove_group_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_trove_group_link`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_trove_treesums`
#

DROP TABLE IF EXISTS v2_xf_trove_treesums;
CREATE TABLE v2_xf_trove_treesums(
  trove_treesums_id int(11) NOT NULL auto_increment,
  trove_cat_id int(11) NOT NULL default '0',
  limit_1 int(11) NOT NULL default '0',
  subprojects int(11) NOT NULL default '0',
  PRIMARY KEY (trove_treesums_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_trove_treesums`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_user_bookmarks`
#

DROP TABLE IF EXISTS v2_xf_user_bookmarks;
CREATE TABLE v2_xf_user_bookmarks(
  bookmark_id int(11) NOT NULL auto_increment,
  user_id int(11) NOT NULL default '0',
  bookmark_url text,
  bookmark_title text,
  PRIMARY KEY (bookmark_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_user_bookmarks`
#

# --------------------------------------------------------

#
# Table structure for table `v2_xf_user_group`
#

DROP TABLE IF EXISTS v2_xf_user_group;
CREATE TABLE v2_xf_user_group(
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
  cvs_flags int(11) NOT NULL default '1',
  member_role int(11) NOT NULL default '100',
  release_flags int(11) NOT NULL default '0',
  artifact_flags int(11) NOT NULL default '0',
  PRIMARY KEY (user_group_id)
) TYPE=MyISAM;

#
# Dumping data for table `v2_xf_user_group`
#

INSERT INTO v2_xf_user_group(user_group_id, user_id, group_id, admin_flags, bug_flags, forum_flags, project_flags, patch_flags, support_flags, doc_flags, cvs_flags, member_role, release_flags, artifact_flags) VALUES(1, 1, 1, 'A', 0, 2, 2, 1, 1, 0, 1, 100, 0, 2);
INSERT INTO v2_xf_user_group(user_group_id, user_id, group_id, admin_flags, bug_flags, forum_flags, project_flags, patch_flags, support_flags, doc_flags, cvs_flags, member_role, release_flags, artifact_flags) VALUES(2, 1, 2, 'A', 0, 2, 2, 1, 1, 0, 1, 100, 0, 2);

