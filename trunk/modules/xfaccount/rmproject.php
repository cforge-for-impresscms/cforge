<?php
/**
  *
  * SourceForge User's Personal Page
  *
  * Confirmation page for users' removing themselves from project.
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: rmproject.php,v 1.3 2004/01/08 16:58:58 devsupaul Exp $
  *
  */
include_once ("../../mainfile.php");

$langfile="my.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");

if (!$xoopsUser)
{
	redirect_header(XOOPS_URL."/user.php",2,_NOPERM);
	exit;
}

$group =& group_get_object($group_id);

if ($confirm) {

	$user_id = $xoopsUser->getVar("uid");

	if (!$group->removeUser($user_id)) {
		echo 'ERROR<br />'.$group->getErrorMessage();
		exit;
	} else {
	  redirect_header(XOOPS_URL."/modules/xfaccount/",0,_XF_MY_TAKINGBACK);
	  exit;
	}
}

/*
	Main code
*/

$perm =& $group->getPermission( $xoopsUser );

if ( $perm->isAdmin() ) {
	redirect_header(XOOPS_URL."/modules/xfaccount/",20,sprintf(_XF_MY_PROJECTADMINERROR, $group_id));
	exit;
}

$metaTitle=": "_XF_MY_QUITTINGPROJECT;
include("../../header.php");

echo "<H4 style='text-align:left;'>"._XF_MY_QUITTINGPROJECT."</H4>"
    ."<P>"
    ."<A HREF='".XOOPS_URL."/modules/xfaccount/'>"._XF_MY_MYPERSONALPAGE."</A> | "
    ."<A HREF='".XOOPS_URL."/modules/xfaccount/diary.php'>"._MY_XF_DIARYNOTES."</A> | "
	  ."<A HREF='".XOOPS_URL."/user.php'>"._XF_MY_MYACCOUNT."</A>"
		."<P>";

echo '
<H4>'._XF_MY_QUITTINGPROJECT.'</H4>
<p>'._XF_MY_ABOUTTOREMOVE.'</p>

<table>
<tr><td>

<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
<input type="hidden" name="confirm" value="1">
<input type="hidden" name="group_id" value="'.$group_id.'">
<input type="submit" value="'._XF_G_REMOVE.'">
</form>

</td><td>

<form action="'.XOOPS_URL.'/modules/xfaccount/" method="GET">
<input type="submit" value="'._XF_G_CANCEL.'">
</form>

</td></tr>
</table>
';

include("../../footer.php");
?>