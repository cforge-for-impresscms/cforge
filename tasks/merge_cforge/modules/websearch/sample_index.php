<?php
	include("../../mainfile.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	 
	echo "<meta name='Robots' content='NOINDEX,FOLLOW'>";
	 
	$query = "SELECT d.sampleid, d.data, grp.group_id, grp.unix_group_name" ." FROM ".$icmsDB->prefix("xf_sample_data")." AS d" .", ".$icmsDB->prefix("xf_sample_groups")." AS g" .", ".$icmsDB->prefix("xf_groups")." AS grp" ." WHERE d.sample_group=g.sample_group" ." AND g.group_id=grp.group_id" ." AND d.stateid=1";
	$result = $icmsDB->query($query);
	while ($row = $icmsDB->fetchArray($result))
	{
		$url = ICMS_URL."/modules/websearch/file_proxy.php?group_id=".$row['group_id']."&sampleid=".$row['sampleid'];
		echo "<a href='$url'>$url</a><br/>";
	}
	$query = "SELECT snippet_version_id, snippet_id FROM ".$icmsDB->prefix("xf_snippet_version");
	$result = $icmsDB->query($query);
	while ($row = $icmsDB->fetchArray($result))
	{
		$url = ICMS_URL."/modules/xfsnippet/detail.php?type=snippet&id=".$row['snippet_id']."&version=".$row['snippet_version_id'];
		echo "<a href='$url'>$url</a><br/>";
	}
	 
?>