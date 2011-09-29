<?php

function cmp ($a, $b) {
    if ($a['maxd'] == $b['maxd']) return 0;
    return ($a['maxd'] > $b['maxd']) ? -1 : 1;
}
	
function b_stats_topdownloads (){
	global $xoopsDB;
	$block = array();
	$block['content'] = "<table>";

	$limit = 10;
	$sql = "SELECT max(d.downloads) as maxd, g.group_name,g.group_id FROM "
			.$xoopsDB->prefix("xf_frs_package")." AS p, " 
			.$xoopsDB->prefix("xf_frs_release")." AS r, "
			.$xoopsDB->prefix("xf_frs_file")." AS f, "
			.$xoopsDB->prefix("xf_frs_dlstats_file_agg")." AS d, "
			.$xoopsDB->prefix("xf_groups")." AS g "
			." WHERE p.package_id=r.package_id"
			." AND r.release_id=f.release_id"
			." AND f.file_id=d.file_id"
			." AND p.group_id=g.group_id"
			." AND g.status='A'"
			." AND g.is_public=1"
			." GROUP BY g.group_name"
			." ORDER BY maxd DESC";
	$result = $xoopsDB->query($sql,$limit);
	$rows = $xoopsDB->getRowsNum($result);
	if(!$result || $rows < 1){
		$block['content'] .= "<tr><td>No current results</td></tr>";
	}
	else {
		$rows=array();
		while($row=$xoopsDB->fetchArray($result)){
			$rows[]=$row;
		}
		usort($rows,"cmp");	
		foreach($rows as $row){
			$sql = "SELECT r.release_id FROM "
					.$xoopsDB->prefix("xf_frs_package")." AS p, " 
					.$xoopsDB->prefix("xf_frs_release")." AS r, "
					.$xoopsDB->prefix("xf_frs_file")." AS f, "
					.$xoopsDB->prefix("xf_frs_dlstats_file_agg")." AS d, "
					.$xoopsDB->prefix("xf_groups")." AS g "
					." WHERE p.package_id=r.package_id"
					." AND r.release_id=f.release_id"
					." AND f.file_id=d.file_id"
					." AND p.group_id=g.group_id"
					." AND g.status='A'"
					." AND g.is_public=1"
					." AND g.group_id=".$row['group_id']
					." AND d.downloads=".$row['maxd'];
			$res = $xoopsDB->query($sql,$limit);
			list($release_id) = $xoopsDB->fetchRow($res);
			$block['content'] .= "<tr><td valign='top'><img src='".XOOPS_URL."/modules/xfstats/images/n_arrows_grey.gif' width='7' height='7' alt=''>&nbsp;</td><td><a href='".XOOPS_URL."/modules/xfmod/project/showfiles.php?group_id=".$row['group_id']."&release_id=".$release_id."'>";
			$block['content'] .= $row['group_name'];
			$block['content'] .= "</a> <i>(".$row['maxd'].")</i></td></tr>";
		}
	}
	$block['content'] .= "</table>";
	return $block;
}

function b_stats_mostactive(){
	global $xoopsDB, $xoopsForge;
	
	$block = array();
	$block['content'] = "<table>";

	$limit = 10;
	$sql = "SELECT wm.percentile, g.group_name, g.unix_group_name FROM"
					." ".$xoopsDB->prefix("xf_project_weekly_metric")." AS wm"
					.",".$xoopsDB->prefix("xf_groups")." AS g"
					.",".$xoopsDB->prefix("xf_config")." AS c"
					." WHERE wm.group_id=g.group_id"
					." AND g.status='A'"
					." AND g.is_public=1"
					." AND c.name='sysnews'"
					." AND g.group_id!=c.value"
					." ORDER BY wm.ranking ASC";
	$result = $xoopsDB->query($sql,$limit);
	$rows = $xoopsDB->getRowsNum($result);
	if(!$result || $rows < 1){
		$block['content'] .= "<tr><td>No current results</td></tr>";
	}
	else {
		while($row=$xoopsDB->fetchArray($result)){
			$block['content'] .= "<tr><td valign='top'><img src='".XOOPS_URL."/modules/xfstats/images/n_arrows_grey.gif' width='7' height='7' alt=''>&nbsp;</td><td><a href='".XOOPS_URL."/modules/xfmod/project/?".$row['unix_group_name']."'>";
			$block['content'] .= $row['group_name'];
			$block['content'] .= "</a></td></tr>";
		}
	}
	$block['content'] .= "</table>";
	return $block;
}

function b_stats_active_users(){
	global $xoopsDB;
	$block = array();
	$block['content'] = "<table>";
	$limit = 10;
	$sql = "SELECT users.uid, users.name, users.uname FROM "
			.$xoopsDB->prefix("xf_user_stats"). " AS stats"
			.", ".$xoopsDB->prefix("users")." AS users"
			." WHERE stats.uid=users.uid"
			." ORDER BY (cvs+posts+trackers+news+samples+documents) DESC";
	$result = $xoopsDB->query($sql,$limit);
	$rows = $xoopsDB->getRowsNum($result);
	if(!$result || $rows < 1){
		$block['content'] .= "<tr><td>No current results</td></tr>";
	}
	else {
		while($row=$xoopsDB->fetchArray($result)){
			$block['content'] .= "<tr><td valign='top'><img src='".XOOPS_URL."/modules/xfstats/images/n_arrows_grey.gif' width='7' height='7' alt=''>&nbsp;</td><td><a href='".XOOPS_URL."/userinfo.php?uid=".$row['uid']."'>";
			if($row['name']){
				$block['content'] .= $row['name']." (".$row['uname'].")";
			}
			else {
				$block['content'] .= $row['uname'];	
			}
			$block['content'] .= "</a></td></tr>";
		}
	}
	$block['content'] .= "</table>";
	return $block;
}

function b_stats_new_projects(){
	global $xoopsDB;
	
	$block = array();
	$block['content'] = "<table>";

	$limit = 10;
	$sql = "SELECT group_name, unix_group_name FROM "
			.$xoopsDB->prefix("xf_groups")
			." WHERE status='A'"
			." AND is_public=1"
			." ORDER BY register_time DESC";
	$result = $xoopsDB->query($sql,$limit);
	$rows = $xoopsDB->getRowsNum($result);
	if(!$result || $rows < 1){
		$block['content'] .= "<tr><td>No current results</td></tr>";
	}
	else {
		while($row=$xoopsDB->fetchArray($result)){
			$block['content'] .= "<tr><td valign='top'><img src='".XOOPS_URL."/modules/xfstats/images/n_arrows_grey.gif' width='7' height='7' alt=''>&nbsp;</td><td><a href='".XOOPS_URL."/modules/xfmod/project/?".$row['unix_group_name']."'>";
			$block['content'] .= $row['group_name'];
			$block['content'] .= "</a></td></tr>";
		}
	}
	$block['content'] .= "</table>";
	return $block;
}
?>