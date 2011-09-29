# $Id: mysql.sql,v 1.19 2004/04/08 22:19:05 danreese Exp $
# (c) 2004 Novell, Inc.
#
# Creates tables used by the web service API.
# --------------------------------------------------------

#
# Table structure for table `xf_webservice_build`
#

CREATE TABLE xf_webservice_build (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  unix_group_name varchar(30) NOT NULL,
  target varchar(20) NOT NULL,
  cvs_host varchar(60) NOT NULL,
  cvs_modules varchar(255) NOT NULL,
  start_time int(11) NOT NULL,
  end_time int(11),
  job_id int(11) unsigned,
  status varchar(20) NOT NULL,
  error varchar(255) NOT NULL default '',
  PRIMARY KEY (id),
  KEY (user_id),
  KEY (unix_group_name),
  KEY (job_id)
) type=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `xf_webservice_publish`
#

CREATE TABLE xf_webservice_publish (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  unix_group_name varchar(30) NOT NULL,
  time int(11) NOT NULL,
  file_id int(11) NOT NULL,
  status varchar(20) NOT NULL default 'active',
  error varchar(255) NOT NULL default '',
  PRIMARY KEY (id),
  KEY (user_id),
  KEY (unix_group_name)
) type=MyISAM;