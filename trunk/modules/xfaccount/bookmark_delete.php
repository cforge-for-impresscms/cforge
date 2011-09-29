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

$langfile="my.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/bookmarks.php");

if (!$xoopsUser)
{
	redirect_header(XOOPS_URL."/user.php",2,_NOPERM);
	exit;
}
if ($bookmark_id) {
	bookmark_delete ($bookmark_id);
	redirect_header(XOOPS_URL."/modules/xfaccount/",4,_XF_MY_BOOKMARKDELETED);
}
?>