<?php
include("../../mainfile.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");

echo "<meta name='Robots' content='NOINDEX,FOLLOW'>";

$query = "SELECT d.docid, d.data, grp.group_id, grp.unix_group_name"
			." FROM ".$xoopsDB->prefix("xf_doc_data")." AS d"
			.", ".$xoopsDB->prefix("xf_doc_groups")." AS g"
			.", ".$xoopsDB->prefix("xf_groups")." AS grp"
			." WHERE d.doc_group=g.doc_group"
			." AND g.group_id=grp.group_id"
			." AND d.stateid=1";
$result = $xoopsDB->query($query);
while($row = $xoopsDB->fetchArray($result)){
	$url = XOOPS_URL."/modules/websearch/file_proxy.php?group_id=".$row['group_id']."&docid=".$row['docid'];
	echo "<a href='$url'>$url</a><br/>";
}


?>