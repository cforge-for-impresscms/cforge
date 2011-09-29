<?php
include("../../mainfile.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");

echo "<meta name='Robots' content='NOINDEX,FOLLOW'>";

$query = "SELECT d.sampleid, d.data, grp.group_id, grp.unix_group_name"
			." FROM ".$xoopsDB->prefix("xf_sample_data")." AS d"
			.", ".$xoopsDB->prefix("xf_sample_groups")." AS g"
			.", ".$xoopsDB->prefix("xf_groups")." AS grp"
			." WHERE d.sample_group=g.sample_group"
			." AND g.group_id=grp.group_id"
			." AND d.stateid=1";
$result = $xoopsDB->query($query);
while($row = $xoopsDB->fetchArray($result)){
	$url = XOOPS_URL."/modules/websearch/file_proxy.php?group_id=".$row['group_id']."&sampleid=".$row['sampleid'];
	echo "<a href='$url'>$url</a><br/>";
}
$query = "SELECT snippet_version_id, snippet_id FROM ".$xoopsDB->prefix("xf_snippet_version");
$result = $xoopsDB->query($query);
while($row = $xoopsDB->fetchArray($result)){
	$url = XOOPS_URL."/modules/xfsnippet/detail.php?type=snippet&id=".$row['snippet_id']."&version=".$row['snippet_version_id'];
	echo "<a href='$url'>$url</a><br/>";
}

?>