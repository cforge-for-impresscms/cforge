# phpMyAdmin MySQL-Dump
# version 2.5.0
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Sep 26, 2003 at 11:22 AM
# Server version: 3.23.49
# PHP Version: 4.3.2
# Database : `usr_web1_1`
# --------------------------------------------------------

#
# Table structure for table `xf_people_job`
#

CREATE TABLE `xf_people_job` (
  `job_id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL default '0',
  `created_by` int(11) NOT NULL default '0',
  `title` text,
  `description` text,
  `date` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`job_id`)
) TYPE=MyISAM;

#
# Dumping data for table `xf_people_job`
#

# --------------------------------------------------------

#
# Table structure for table `xf_people_job_category`
#

CREATE TABLE `xf_people_job_category` (
  `category_id` int(11) NOT NULL auto_increment,
  `name` text,
  `private_flag` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`)
) TYPE=MyISAM;

#
# Dumping data for table `xf_people_job_category`
#

INSERT INTO `xf_people_job_category` VALUES (1, 'Developer', 0);
INSERT INTO `xf_people_job_category` VALUES (2, 'Project Manager', 0);
INSERT INTO `xf_people_job_category` VALUES (3, 'Unix Admin', 0);
INSERT INTO `xf_people_job_category` VALUES (4, 'Doc Writer', 0);
INSERT INTO `xf_people_job_category` VALUES (5, 'Tester', 0);
INSERT INTO `xf_people_job_category` VALUES (6, 'Support Manager', 0);
INSERT INTO `xf_people_job_category` VALUES (7, 'Graphic/Other Designer', 0);
INSERT INTO `xf_people_job_category` VALUES (8, 'Sponsor', 0);
INSERT INTO `xf_people_job_category` VALUES (100, NULL, 0);
# --------------------------------------------------------

#
# Table structure for table `xoops_xf_people_job_inventory`
#

CREATE TABLE `xf_people_job_inventory` (
  `job_inventory_id` int(11) NOT NULL auto_increment,
  `job_id` int(11) NOT NULL default '0',
  `skill_id` int(11) NOT NULL default '0',
  `skill_level_id` int(11) NOT NULL default '0',
  `skill_year_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`job_inventory_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_people_job_status`
#
# Creation: Sep 05, 2003 at 10:11 AM
# Last update: Sep 12, 2003 at 07:47 PM
# Last check: Sep 10, 2003 at 03:27 AM
#

CREATE TABLE `xf_people_job_status` (
  `status_id` int(11) NOT NULL auto_increment,
  `name` text,
  PRIMARY KEY  (`status_id`)
) TYPE=MyISAM;

#
# Dumping data for table `xf_people_job_status`
#

INSERT INTO `xf_people_job_status` VALUES (1, 'Open');
INSERT INTO `xf_people_job_status` VALUES (2, 'Filled');
INSERT INTO `xf_people_job_status` VALUES (3, 'Deleted');
# --------------------------------------------------------

#
# Table structure for table `xf_people_skill`
#

CREATE TABLE `xf_people_skill` (
  `skill_id` int(11) NOT NULL auto_increment,
  `name` text,
  PRIMARY KEY  (`skill_id`)
) TYPE=MyISAM;

#
# Dumping data for table `xf_people_skill`
#

INSERT INTO `xf_people_skill` VALUES (1, '3100 SQL');
INSERT INTO `xf_people_skill` VALUES (2, '3110 C/C++');
INSERT INTO `xf_people_skill` VALUES (3, '3120 Perl');
INSERT INTO `xf_people_skill` VALUES (4, '3130 PHP');
INSERT INTO `xf_people_skill` VALUES (5, '3140 Java');
INSERT INTO `xf_people_skill` VALUES (6, '3150 Python');
INSERT INTO `xf_people_skill` VALUES (7, '3160 Visual Basic');
INSERT INTO `xf_people_skill` VALUES (8, '3170 AppleScript');
INSERT INTO `xf_people_skill` VALUES (9, '3180 UML');
INSERT INTO `xf_people_skill` VALUES (10, '3190 XMI');
INSERT INTO `xf_people_skill` VALUES (11, '3200 HTML/DHTML');
INSERT INTO `xf_people_skill` VALUES (12, '3210 XML/XPath/XLink/XSL/XSLT');
INSERT INTO `xf_people_skill` VALUES (13, '3230 RDF');
INSERT INTO `xf_people_skill` VALUES (14, '3240 LISP');
INSERT INTO `xf_people_skill` VALUES (15, '3250 Delphi');
INSERT INTO `xf_people_skill` VALUES (16, '3260 ASP');
INSERT INTO `xf_people_skill` VALUES (17, '3270 Ada');
INSERT INTO `xf_people_skill` VALUES (18, '3900 Other Prog. Lang.');
INSERT INTO `xf_people_skill` VALUES (19, '5100 Chinese');
INSERT INTO `xf_people_skill` VALUES (20, '5110 Japanese');
INSERT INTO `xf_people_skill` VALUES (21, '5120 Spanish');
INSERT INTO `xf_people_skill` VALUES (22, '5130 French');
INSERT INTO `xf_people_skill` VALUES (23, '5140 German');
INSERT INTO `xf_people_skill` VALUES (24, '5900 Other Spoken Lang.');
INSERT INTO `xf_people_skill` VALUES (25, '7100 UNIX Admin');
INSERT INTO `xf_people_skill` VALUES (26, '7110 Networking');
INSERT INTO `xf_people_skill` VALUES (27, '7120 Security');
INSERT INTO `xf_people_skill` VALUES (28, '7130 Writing');
INSERT INTO `xf_people_skill` VALUES (29, '7140 Editing');
INSERT INTO `xf_people_skill` VALUES (30, '7150 Databases');
INSERT INTO `xf_people_skill` VALUES (31, '7160 Object Oriented Design');
INSERT INTO `xf_people_skill` VALUES (32, '7170 Object Oriented Analysis');
INSERT INTO `xf_people_skill` VALUES (33, '7900 Other Skill Area');
# --------------------------------------------------------

#
# Table structure for table `xf_people_skill_inventory`
#

CREATE TABLE `xf_people_skill_inventory` (
  `skill_inventory_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `skill_id` int(11) NOT NULL default '0',
  `skill_level_id` int(11) NOT NULL default '0',
  `skill_year_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`skill_inventory_id`)
) TYPE=MyISAM;

#
# Dumping data for table `xf_people_skill_inventory`
#

# --------------------------------------------------------

#
# Table structure for table `xf_people_skill_level`
#

CREATE TABLE `xf_people_skill_level` (
  `skill_level_id` int(11) NOT NULL auto_increment,
  `name` text,
  PRIMARY KEY  (`skill_level_id`)
) TYPE=MyISAM;

#
# Dumping data for table `xf_people_skill_level`
#

INSERT INTO `xf_people_skill_level` VALUES (1, 'Want to Learn');
INSERT INTO `xf_people_skill_level` VALUES (2, 'Competent');
INSERT INTO `xf_people_skill_level` VALUES (3, 'Wizard');
INSERT INTO `xf_people_skill_level` VALUES (4, 'Wrote The Book');
INSERT INTO `xf_people_skill_level` VALUES (5, 'Wrote It');
# --------------------------------------------------------

#
# Table structure for table `xf_people_skill_year`
#

CREATE TABLE `xf_people_skill_year` (
  `skill_year_id` int(11) NOT NULL auto_increment,
  `name` text,
  PRIMARY KEY  (`skill_year_id`)
) TYPE=MyISAM;

#
# Dumping data for table `xf_people_skill_year`
#

INSERT INTO `xf_people_skill_year` VALUES (1, '< 6 Months');
INSERT INTO `xf_people_skill_year` VALUES (2, '6 Mo - 2 yr');
INSERT INTO `xf_people_skill_year` VALUES (3, '2 yr - 5 yr');
INSERT INTO `xf_people_skill_year` VALUES (4, '5 yr - 10 yr');
INSERT INTO `xf_people_skill_year` VALUES (5, '> 10 years');

