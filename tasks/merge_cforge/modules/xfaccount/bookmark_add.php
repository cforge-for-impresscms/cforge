<?php
/**
*
* SourceForge User's Personal Page
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001 (c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: bookmark_add.php,v 1.6 2003/12/09 16:03:55 devsupaul Exp $
*
*/

include_once ("../../mainfile.php");

if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);

$langfile = "my.php";
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/bookmarks.php");

if (!$icmsUser)
{
	redirect_header(ICMS_URL."/user.php", 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
	exit;
}
if ($bookmark_url && $bookmark_title)
{
	if (bookmark_add ($bookmark_url, $bookmark_title) === true)
	{
		redirect_header(ICMS_URL."/modules/xfaccount/", 4, sprintf(_XF_MY_ADDEDBOOKMARK, $bookmark_url, $bookmark_title));
	}
	else
	{
		redirect_header($_SERVER['HTTP_REFERER'], 4, "Could not add bookmark");
	}
}
else
{

$mhandler = icms_gethandler('module');
$icmsModule = $xoopsModule = $mhandler->getByDirname('xfaccount');
global $icmsModule;

	include("../../header.php");
	 
	echo "<h4 style='text-align:left;'>"._XF_MY_ADDNEWBOOKMARK.": ".$icmsUser->getVar("name")."</h4>";
?>
<form method="post">
<?php echo _XF_MY_BOOKMARKURL; ?>:<br>
<input type="text" name="bookmark_url" value="<?php echo $bookmark_url ? $bookmark_url : 'http://'; ?>">
<p>
<?php echo _XF_MY_BOOKMARKTITLE; ?>:<br>
<input type="text" name="bookmark_title" value="<?php echo $bookmark_title ? $bookmark_title : 'New Title'; ?>">
<p>
<input type="submit" value="<?php echo _XF_G_SUBMIT; ?>">
</form>
<?php
	 
	include("../../footer.php");
}
 
?>