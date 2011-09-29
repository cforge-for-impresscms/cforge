<?php
	include("../../mainfile.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	 
	echo "<meta name='Robots' content='NOINDEX,FOLLOW'>";
	 
	$query = "SELECT d.docid, d.data, grp.group_id, grp.unix_group_name" ." FROM ".$icmsDB->prefix("xf_doc_data")." AS d" .", ".$icmsDB->prefix("xf_doc_groups")." AS g" .", ".$icmsDB->prefix("xf_groups")." AS grp" ." WHERE d.doc_group=g.doc_group" ." AND g.group_id=grp.group_id" ." AND d.stateid=1";
	$result = $icmsDB->query($query);
	while ($row = $icmsDB->fetchArray($result))
	{
		$url = ICMS_URL."/modules/websearch/file_proxy.php?group_id=".$row['group_id']."&docid=".$row['docid'];
		echo "<a href='$url'>$url</a><br/>";
	}
	 
	 
?>