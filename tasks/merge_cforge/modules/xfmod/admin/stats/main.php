<?php
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	 
	include_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	include_once("admin/admin_utils.php");
	 
	site_admin_header();
	 
	echo "<p><strong>Last Cronjob Updates</strong></p>";
	 
	$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_cronjob_log")." ORDER BY cronjob_log_id DESC", 10);
	echo "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>Log ID</strong></td>" ."<td><strong>Date</strong></td>" ."</tr>";
	 
	$i = 0;
	 
	while ($log = $icmsDB->fetchArray($result))
	{
		echo '<th class="'.($i++%2 > 0?'bg1':'bg3').'">' .'<td>'.$log['cronjob_log_id'].'</td>' .'<td>'.date(_MEDIUMDATESTRING, $log['updatetime']).'</td></th>';
	}
	echo "</table>";
	 
	echo "<p><strong>Quick Site Statistics</strong></p>";
	 
	list($count_projects) = $icmsDB->fetchRow($icmsDB->query("SELECT COUNT(*) AS count FROM ".$icmsDB->prefix("xf_groups")." WHERE group_id<>100"));
	list($count_registered_projects) = $icmsDB->fetchRow($icmsDB->query("SELECT COUNT(*) AS count FROM ".$icmsDB->prefix("xf_groups")." WHERE status='A' AND group_id<>100"));
	list($count_pending_projects) = $icmsDB->fetchRow($icmsDB->query("SELECT COUNT(*) AS count FROM ".$icmsDB->prefix("xf_groups")." WHERE status='P' AND group_id<>100"));
	list($count_users) = $icmsDB->fetchRow($icmsDB->query("SELECT COUNT(*) AS count FROM ".$icmsDB->prefix("users")." WHERE uid<>100"));
	list($count_users_project) = $icmsDB->fetchRow($icmsDB->query("SELECT COUNT(DISTINCT user_id) AS count FROM ".$icmsDB->prefix("xf_user_group")." WHERE group_id<>100"));
	 
	echo "<table border='0' cellspacing='4'>" ."<tr><td>Registered projects: </td><td><strong>".$count_projects."</strong></td></tr>" ."<tr><td>Active projects: </td><td><strong>".$count_registered_projects."</strong></td></tr>" ."<tr><td>Pending projects: </td><td><strong>".$count_pending_projects."</strong></td></tr>" ."<tr><td>Registered Users: </td><td><strong>".$count_users."</strong></td></tr>" ."<tr><td>Participating Users: </td><td><strong>".$count_users_project."</strong></td></tr>" ."</table>";
	 
	site_admin_footer()
?>