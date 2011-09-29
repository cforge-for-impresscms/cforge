<?php
/**
  *
  * SourceForge User's Personal Page
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: bookmark_edit.php,v 1.4 2004/01/08 16:58:58 devsupaul Exp $
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

include("../../header.php");

echo "<h4 style='text-align:left;'>"._XF_MY_EDITBOOKMARK.": ".$xoopsUser->getVar("name")."</h4>";

if ($bookmark_url && $bookmark_title)
{
	bookmark_edit($bookmark_id, $bookmark_url, $bookmark_title);
}

$sql = "SELECT * FROM ".$xoopsDB->prefix("xf_user_bookmarks")
	." WHERE bookmark_id='".$bookmark_id
	."' AND user_id='".$xoopsUser->getVar("uid")."'";
$result = $xoopsDB->query($sql);
if ($result)
{
	$bookmark_url = unofficial_getDBResult($result, 0, 'bookmark_url');
	$bookmark_title = unofficial_getDBResult($result, 0, 'bookmark_title');
}
?>
<form method="post">
	<?php echo _XF_MY_BOOKMARKURL; ?>:<br>
	<input type="text" name="bookmark_url" value="<?php echo $bookmark_url; ?>">
	<p>
	<?php echo _XF_MY_BOOKMARKTITLE; ?>:<br>
	<input type="text" name="bookmark_title" value="<?php echo $ts->makeTboxData4Edit($bookmark_title); ?>">
	<p>
	<input type="submit" value="<?php echo _XF_G_SUBMIT; ?>">
</form>
<?php

print "<p><a href='".XOOPS_URL."/modules/xfaccount/'>"._XF_G_BACK."</a>";

include("../../footer.php");
?>