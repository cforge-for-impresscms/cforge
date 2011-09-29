<?php
if (!eregi("admin.php", $_SERVER['PHP_SELF'])) { die ("Access Denied"); }

include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
include_once("admin/admin_utils.php");

site_admin_header();

echo "<H4>XoopsForge Site News</H4>";

echo "Each project maintains its own News Items on the project page. "
    ."Every News Item post by a project administrator is also visible "
		."for the XoopsForge Site News Manager. This manager has access to "
		."these news items through the XoopsForge Site News Project page "
		."that is located <a href='".XOOPS_URL."/modules/xfmod/project/?group_id=".$xoopsForge['sysnews']."'>here</a>."
		."<P>You as administrator of this website are responsible for adding "
		."users to this project if you want more people to have control over "
		."those news items."
		."<P>Some News Items posted by other projects could also be very interesting "
		."for every other visitor or user of this website. If a user of the "
		."XoopsForge Site News Project approves such a news item, it becomes also "
		."visible on the front page of the website using the XoopsForge Latest News "
		."Block"
		."<P>You can jump directly to the Administration part of the XoopsForge Site News "
		."project following <a href='".XOOPS_URL."/modules/xfmod/news/admin/'>this link</a>.<br />";

site_admin_footer()
?>