<?php
	/**
	*
	* Module of support routines for Site Admin
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: admin_utils.php,v 1.4 2004/01/07 22:20:11 devsupaul Exp $
	*
	*/
	 
	function site_admin_header()
	{
		global $icmsForge, $feedback;
		xoops_cp_header();
		 
		echo '<p><strong>Version:</strong> '.$icmsForge['version'].'<BR />';
		//  echo '<strong><a href="'.ICMS_URL.'/modules/xfmod/admin.php">Site Admin Home</a> | ';
		//  echo '<a href="'.ICMS_URL.'/modules/xfmod/news/admin/">Site News Admin</a></strong> | ';
		//  echo 'Site Stats</strong>';
		echo '<p><font color="red"><strong>'.$feedback.'</strong></font><br />';
	}
	 
	function site_admin_footer()
	{
		xoops_cp_footer();
	}
	 
	function show_group_type_box($name = 'group_type', $checked_val = 'xzxz')
	{
		global $icmsDB;
		 
		$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_group_type"));
		return html_build_select_box($result, 'group_type', $checked_val, false);
	}
	 
?>