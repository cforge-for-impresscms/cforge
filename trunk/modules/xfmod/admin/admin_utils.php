<?php
/**
  *
  * Module of support routines for Site Admin
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: admin_utils.php,v 1.4 2004/01/07 22:20:11 devsupaul Exp $
  *
  */

function site_admin_header()
{
  global $xoopsForge, $feedback;
	xoops_cp_header();

  echo '<P><B>Version:</B> '.$xoopsForge['version'].'<BR />';
//  echo '<B><A HREF="'.XOOPS_URL.'/modules/xfmod/admin.php">Site Admin Home</A> | ';
//  echo '<A HREF="'.XOOPS_URL.'/modules/xfmod/news/admin/">Site News Admin</A></B> | ';
//  echo 'Site Stats</B>';
  echo '<P><font color="red"><b>'.$feedback.'</b></font><br />';
}

function site_admin_footer() {
	xoops_cp_footer();
}

function show_group_type_box($name='group_type',$checked_val='xzxz') {
  global $xoopsDB;
	
	$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_group_type"));
	return html_build_select_box ($result,'group_type',$checked_val,false);
}

?>