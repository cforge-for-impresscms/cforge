#
# Table structure for table `xf_snippet`
#

CREATE TABLE xf_snippet (
  snippet_id int(11) NOT NULL auto_increment,
  created_by int(11) NOT NULL default '0',
  name text,
  description text,
  type int(11) NOT NULL default '0',
  language int(11) NOT NULL default '0',
  license text NOT NULL,
  category int(11) NOT NULL default '0',
  PRIMARY KEY  (snippet_id)
) type=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `xf_snippet_category`
#

CREATE TABLE xf_snippet_category (
  type_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (type_id)
) type=MyISAM;
# --------------------------------------------------------

INSERT INTO xf_snippet_category VALUES (100, 'Choose One');
INSERT INTO xf_snippet_category VALUES (101, 'UNIX Admin');
INSERT INTO xf_snippet_category VALUES (102, 'HTML Manipulation');
INSERT INTO xf_snippet_category VALUES (103, 'BBS Systems');
INSERT INTO xf_snippet_category VALUES (104, 'Auctions');
INSERT INTO xf_snippet_category VALUES (105, 'Calendars');
INSERT INTO xf_snippet_category VALUES (106, 'Database Manipulation');
INSERT INTO xf_snippet_category VALUES (107, 'Searching');
INSERT INTO xf_snippet_category VALUES (108, 'File Management');
INSERT INTO xf_snippet_category VALUES (109, 'Games');
INSERT INTO xf_snippet_category VALUES (110, 'Voting');
INSERT INTO xf_snippet_category VALUES (111, 'Shopping Carts');
INSERT INTO xf_snippet_category VALUES (112, 'Other');
INSERT INTO xf_snippet_category VALUES (113, 'Math Functions');

#
# Table structure for table `xf_snippet_language`
#

CREATE TABLE xf_snippet_language (
  type_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (type_id)
) type=MyISAM;
# --------------------------------------------------------

INSERT INTO xf_snippet_language VALUES (100, 'Choose One');
INSERT INTO xf_snippet_language VALUES (101, 'Other Language');
INSERT INTO xf_snippet_language VALUES (102, 'C');
INSERT INTO xf_snippet_language VALUES (103, 'C++');
INSERT INTO xf_snippet_language VALUES (104, 'Perl');
INSERT INTO xf_snippet_language VALUES (105, 'PHP');
INSERT INTO xf_snippet_language VALUES (106, 'Python');
INSERT INTO xf_snippet_language VALUES (107, 'Unix Shell');
INSERT INTO xf_snippet_language VALUES (108, 'Java');
INSERT INTO xf_snippet_language VALUES (109, 'AppleScript');
INSERT INTO xf_snippet_language VALUES (110, 'Visual Basic');
INSERT INTO xf_snippet_language VALUES (111, 'TCL');
INSERT INTO xf_snippet_language VALUES (112, 'Lisp');
INSERT INTO xf_snippet_language VALUES (113, 'Mixed');
INSERT INTO xf_snippet_language VALUES (114, 'Javascript');
INSERT INTO xf_snippet_language VALUES (115, 'SQL');
INSERT INTO xf_snippet_language VALUES (116, 'C#');

#
# Table structure for table `xf_snippet_package`
#

CREATE TABLE xf_snippet_package (
  snippet_package_id int(11) NOT NULL auto_increment,
  created_by int(11) NOT NULL default '0',
  name text,
  description text,
  category int(11) NOT NULL default '0',
  language int(11) NOT NULL default '0',
  PRIMARY KEY  (snippet_package_id)
) type=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `xf_snippet_package_item`
#

CREATE TABLE xf_snippet_package_item (
  snippet_package_item_id int(11) NOT NULL auto_increment,
  snippet_package_version_id int(11) NOT NULL default '0',
  snippet_version_id int(11) NOT NULL default '0',
  PRIMARY KEY  (snippet_package_item_id)
) type=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `xf_snippet_package_version`
#

CREATE TABLE xf_snippet_package_version (
  snippet_package_version_id int(11) NOT NULL auto_increment,
  snippet_package_id int(11) NOT NULL default '0',
  changes text,
  version text,
  submitted_by int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  PRIMARY KEY  (snippet_package_version_id)
) type=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `xf_snippet_type`
#

CREATE TABLE xf_snippet_type (
  type_id int(11) NOT NULL auto_increment,
  name text,
  PRIMARY KEY  (type_id)
) type=MyISAM;
# --------------------------------------------------------

INSERT INTO xf_snippet_type VALUES (100, 'Choose One');
INSERT INTO xf_snippet_type VALUES (101, 'Hack');
INSERT INTO xf_snippet_type VALUES (102, 'Function');
INSERT INTO xf_snippet_type VALUES (103, 'Full Script');
INSERT INTO xf_snippet_type VALUES (104, 'Sample Code (HOWTO)');
INSERT INTO xf_snippet_type VALUES (105, 'README');
INSERT INTO xf_snippet_type VALUES (106, 'Class');
INSERT INTO xf_snippet_type VALUES (107, 'Core Hack');

#
# Table structure for table `xf_snippet_version`
#
# Creation: Sep 12, 2003 at 02:04 PM
# Last update: Sep 12, 2003 at 02:10 PM
#

CREATE TABLE xf_snippet_version (
  snippet_version_id int(11) NOT NULL auto_increment,
  snippet_id int(11) NOT NULL default '0',
  changes text,
  version text,
  submitted_by int(11) NOT NULL default '0',
  date int(11) NOT NULL default '0',
  code text,
  PRIMARY KEY  (snippet_version_id)
) type=MyISAM;
