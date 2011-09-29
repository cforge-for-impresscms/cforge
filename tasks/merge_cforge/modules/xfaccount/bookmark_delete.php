<?php
	/**
	*
	* SourceForge User's Personal Page
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: bookmark_delete.php,v 1.4 2003/12/09 16:03:55 devsupaul Exp $
	*
	*/
	 
	include_once ("../../mainfile.php");
	 
	$langfile = "my.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/bookmarks.php");
	 
	if (!$icmsUser)
		{
		redirect_header(ICMS_URL."/user.php", 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
	}
	if ($bookmark_id)
	{
		bookmark_delete ($bookmark_id);
		redirect_header(ICMS_URL."/modules/xfaccount/", 4, _XF_MY_BOOKMARKDELETED);
	}
?>