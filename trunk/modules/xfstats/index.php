<?php
include_once("../../mainfile.php");

$langfile="stats.php";
//require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfstats/stat_functions.php");
$xoopsOption['template_main'] = 'xfstats_index.html';

include("../../header.php");
	
list ($count_projects) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_groups")." WHERE group_id<>100 AND status<>'D'"));
$result = $xoopsDB->query("SELECT g.unix_group_name from ".$xoopsDB->prefix("users")." AS u, ".$xoopsDB->prefix("xf_user_group")." AS ug, ".$xoopsDB->prefix("xf_groups")." AS g WHERE u.uid=ug.user_id AND g.group_id=ug.group_id AND u.email not like '%novell.com' AND g.is_public=1 AND g.status='A' AND g.group_id<>100 GROUP BY ug.group_id");
$count_non_novell = $xoopsDB->getRowsNum($result);
$result = $xoopsDB->query("SELECT g.unix_group_name from ".$xoopsDB->prefix("users")." AS u, ".$xoopsDB->prefix("xf_user_group")." AS ug, ".$xoopsDB->prefix("xf_groups")." AS g WHERE u.uid=ug.user_id AND g.group_id=ug.group_id AND u.email like '%novell.com' AND g.is_public=1 AND g.status='A' AND g.group_id<>100 GROUP BY ug.group_id");
$count_novell = $xoopsDB->getRowsNum($result);
$result = $xoopsDB->query("SELECT g.unix_group_name from ".$xoopsDB->prefix("users")." AS u, ".$xoopsDB->prefix("xf_user_group")." AS ug, ".$xoopsDB->prefix("xf_groups")." AS g WHERE u.uid=ug.user_id AND g.group_id=ug.group_id AND u.uname='ndkwp' AND is_public=1 AND status='A' GROUP BY ug.group_id");
$count_ndk = $xoopsDB->getRowsNum($result);
list ($count_registered_projects) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_groups")." WHERE status='A' AND group_id<>100"));
list ($count_pending_projects) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_groups")." WHERE status='P' AND group_id<>100"));
list ($count_private_projects) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("xf_groups")." WHERE is_public=0 AND status<>'D' AND group_id<>100"));
//count_novell and count_non_novell are not mutually exclusive
$count_mix_novell = ($count_non_novell + $count_novell) - $count_projects;
$count_only_novell = $count_novell - $count_mix_novell;
$count_no_novell = $count_non_novell - $count_mix_novell;

$title_usage_stats = "Usage Stats";
$usage_stats = "<a href='/usage/'>Usage Stats via Webalizer</a>";
//themesidebox($title, $content);
$xoopsTpl->assign("title_usage_stats",$title_usage_stats);
$xoopsTpl->assign("usage_stats",$usage_stats);

$title_project_stats = "Project Stats";
$project_stats = "<table border='0' cellspacing='4'>"
			."<tr><td>Projects with no Novell employees as members: </td><td align=right><B>".number_format($count_no_novell)."</B></td>"
			."<td rowspan=6 valign=top>&nbsp;&nbsp;&nbsp;&nbsp;<img src='pie.php?total=".($count_projects)."&nn=".($count_no_novell+$count_mix_novell)."'></td></tr>"
			."<tr><td>Projects with only Novell employees as members: </td><td align=right><B>".number_format($count_only_novell)."</B></td></tr>"
			."<tr><td>Projects with both as members: </td><td align=right><B>".number_format($count_mix_novell)."</B></td></tr>"
			."<tr><td>Total Registered Projects: </td><td align=right style=\"border-top:solid; border-top-width:thin\"><B>".number_format($count_projects)."</B></td></tr>"
			."<tr><td>Active projects: </td><td align=right><B>".number_format($count_registered_projects)."</B></td></tr>"
			."<tr><td>Pending projects: </td><td align=right><B>".number_format($count_pending_projects)."</B></td></tr>"
			."<tr><td>NDK Projects: </td><td align=right><B>".number_format($count_ndk)."</B></td></tr>"
			."<tr><td>Private projects: </td><td align=right><B>".number_format($count_private_projects)."</B></td></tr>"
			."</table>";
//themesidebox($title, $content);
$xoopsTpl->assign("title_project_stats",$title_project_stats);
$xoopsTpl->assign("project_stats",$project_stats);

$time = time();
$last_day  = ($time - 86400);  
$last_week = ($time - (86400 * 7));
$last_month  = ($time - (86400 * 30));  
$last_quarter  = ($time - (86400 * 90));  
$sql = "SELECT SUM(IF(register_time>".$last_day.",1,0)) as day"
		.", SUM(IF(register_time>".$last_week.",1,0)) as week"
		.", SUM(IF(register_time>".$last_month.",1,0)) as month"
		.", SUM(IF(register_time>".$last_quarter.",1,0)) as quarter"
		." FROM xoops_xf_groups";
list($day,$week,$month,$quarter) = $xoopsDB->fetchRow($xoopsDB->query($sql));
$new_projects = "<table width=100%><tr><td>".newprojects()."</td><td align=center><img src='newprojects.php?bar[0]=$day&bar[1]=$week&bar[2]=$month&bar[3]=$quarter'></td></tr></table>";
//themesidebox($title, $content);
$xoopsTpl->assign("title_new_projects","New Projects");
$xoopsTpl->assign("new_projects",$new_projects);

$block = mostactiveprojects();
$xoopsTpl->assign("title_most_active_projects","Most Active Projects");
$xoopsTpl->assign("most_active_projects",$block['content']);

$block = topdownloads();
$xoopsTpl->assign("title_top_downloads","Top Downloaded Projects");
$xoopsTpl->assign("top_downloads",$block['content']);


list ($count_users) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("users")." WHERE uid<>100"));
list ($count_novell_users) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(*) AS count FROM ".$xoopsDB->prefix("users")." WHERE uid<>100 AND email LIKE '%novell.com'"));
list ($count_users_project) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(DISTINCT user_id) AS count FROM ".$xoopsDB->prefix("xf_user_group")." WHERE group_id<>100"));
list ($count_novell_users_project) = $xoopsDB->fetchRow($xoopsDB->query("SELECT COUNT(DISTINCT user_id) AS count FROM ".$xoopsDB->prefix("xf_user_group")." AS ug, ".$xoopsDB->prefix("users")." AS u WHERE u.uid=ug.user_id AND u.email LIKE '%novell.com' AND group_id<>100"));

$title_user_stats = "User Stats";
$user_stats = "<table border='0' cellspacing='4'>"
			."<tr><td>Registered users outside Novell</td><td align=right><b>".number_format($count_users-$count_novell_users)."</b></td><td rowspan=3 valign=top>&nbsp;&nbsp;&nbsp;&nbsp;<img src='pie.php?total=$count_users&nn=".($count_users-$count_novell_users)."'></td></tr>"
			."<tr><td>Registered users from Novell</td><td align=right><b>".number_format($count_novell_users)."</b></td></tr>"
			."<tr><td>Total Registered Users: </td><td align=right style=\"border-top:solid; border-top-width:thin\"><B>".number_format($count_users)."</B></td></tr>"
			."<tr><td colspan=2>&nbsp;</td></tr>"
			."<tr><td>Participating Users outside Novell: </td><td align=right><B>".number_format($count_users_project-$count_novell_users_project)."</B></td><td rowspan=3 valign=top>&nbsp;&nbsp;&nbsp;&nbsp;<img src='pie.php?total=$count_users_project&nn=".($count_users_project-$count_novell_users_project)."'></td></tr>"
			."<tr><td>Participating Users from Novell: </td><td align=right><B>".number_format($count_novell_users_project)."</B></td></tr>"
			."<tr><td>Users Participating in at least one project: </td><td align=right style=\"border-top:solid; border-top-width:thin\"><B>".number_format($count_users_project)."</B></td></tr>"
			."</table>";
//themesidebox($title, $content);
$xoopsTpl->assign("title_user_stats",$title_user_stats);
$xoopsTpl->assign("user_stats",$user_stats);

$sql = "SELECT SUM(IF(user_regdate>".$last_day.",1,0)) as day"
		.", SUM(IF(user_regdate>".$last_week.",1,0)) as week"
		.", SUM(IF(user_regdate>".$last_month.",1,0)) as month"
		.", SUM(IF(user_regdate>".$last_quarter.",1,0)) as quarter"
		." FROM xoops_users";
list($day,$week,$month,$quarter) = $xoopsDB->fetchRow($xoopsDB->query($sql));
$title_new_users_in_last_given_tme_period = "New Users in the Last Given Time Period";
$new_users_in_last_given_tme_period = "<table width=100%><tr><td>".newmembers()."</td><td align=center><img src='newprojects.php?bar[0]=$day&bar[1]=$week&bar[2]=$month&bar[3]=$quarter'></td></tr></table>";
//themesidebox($title, $content);
$xoopsTpl->assign("title_new_users_in_last_given_tme_period",$title_new_users_in_last_given_tme_period);
$xoopsTpl->assign("new_users_in_last_given_tme_period",$new_users_in_last_given_tme_period);

$block = mostactiveusers();
$xoopsTpl->assign("title_most_active_users","Most Active Users");
$xoopsTpl->assign("most_active_users",$block['content']);
		
//CloseTable();
include("../../footer.php");
?>