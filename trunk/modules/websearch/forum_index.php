<?php
include("../../mainfile.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
	
echo "<meta name='Robots' content='NOINDEX,FOLLOW'>";

$query = "SELECT group_id"
			." FROM ".$xoopsDB->prefix("xf_groups")
			." WHERE is_public=1"
			." AND status='A'";
$result = $xoopsDB->query($query);
while($row = $xoopsDB->fetchArray($result)){
	$url = XOOPS_URL."/modules/xfmod/newsportal/?group_id=".$row['group_id'];
	echo "<a href='$url'>$url</a><br/>";
}

?>