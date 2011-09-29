<?php
if (!eregi("admin.php", $_SERVER['PHP_SELF'])) { die ("Access Denied"); }

include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
include_once("admin/admin_utils.php");

site_admin_header();

echo "<P><B>Last Cronjob Updates</B></P>";

$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_cronjob_log")." ORDER BY cronjob_log_id DESC", 10);
echo "<table border='0' width='100%'>"
    ."<tr class='bg2'>"
	  ."<td><b>Log ID</b></td>"
	  ."<td><b>Date</b></td>"
    ."</tr>";

$i = 0;

while ($log = $xoopsDB->fetchArray($result))
{
  echo '<TR class="'.($i++%2>0?'bg1':'bg3').'">'
	    .'<TD>'.$log['cronjob_log_id'].'</TD>'
		  .'<TD>'.date(_MEDIUMDATESTRING, $log['updatetime']).'</TD></TR>';
}
echo "</table>";

echo "<P><B>Quick Site Statistics</B></P>";

list ($count_projects) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_groups")." WHERE group_id<>100"));
list ($count_registered_projects) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_groups")." WHERE status='A' AND group_id<>100"));
list ($count_pending_projects) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_groups")." WHERE status='P' AND group_id<>100"));
list ($count_users) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("users")." WHERE uid<>100"));
list ($count_users_project) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(DISTINCT user_id) AS count FROM ".$xoopsDB->prefix("xf_user_group")." WHERE group_id<>100"));

echo "<table border='0' cellspacing='4'>"
    ."<tr><td>Registered projects: </td><td><B>".$count_projects."</B></td></tr>"
    ."<tr><td>Active projects: </td><td><B>".$count_registered_projects."</B></td></tr>"
    ."<tr><td>Pending projects: </td><td><B>".$count_pending_projects."</B></td></tr>"
    ."<tr><td>Registered Users: </td><td><B>".$count_users."</B></td></tr>"
    ."<tr><td>Participating Users: </td><td><B>".$count_users_project."</B></td></tr>"
		."</table>";

site_admin_footer()
?>