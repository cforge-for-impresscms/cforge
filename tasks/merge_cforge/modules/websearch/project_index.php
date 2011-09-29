<?php
	include("../../mainfile.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	 
	echo "<meta name='Robots' content='NOINDEX,FOLLOW'>";
	 
	$query = "SELECT group_id,unix_group_name FROM ".$icmsDB->prefix("xf_groups")." WHERE is_public=1 AND status='A'";
	$result = $icmsDB->query($query);
	while ($row = $icmsDB->fetchArray($result))
	{
		$url = ICMS_URL."/modules/xfmod/project/?".$row['unix_group_name'];
		echo "<a href='$url'>$url</a><br/>";
	}
	 
	 
?>