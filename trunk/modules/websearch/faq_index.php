<?php
include("../../mainfile.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");

echo "<meta name='Robots' content='NOINDEX,FOLLOW'>";
	
$query = "SELECT contents_id, category_id FROM ".$xoopsDB->prefix("xoopsfaq_contents")." WHERE contents_visible=1";
$result = $xoopsDB->query($query);
while($row = $xoopsDB->fetchArray($result)){
	$url = XOOPS_URL."/modules/xoopsfaq/?cat_id=".$row['category_id']."&contents_id=".$row['contents_id'];
	echo "<a href='$url'>$url</a><br>";
}

$path = XOOPS_ROOT_PATH."/modules/xfmod/help";
$dir = @opendir($path);
while($file = readdir($dir)){
	if(preg_match("/php/",$file)){
		$url = XOOPS_URL."/modules/xfmod/help/".$file;
		echo "<a href='$url'>$url</a><br/>";
	}	
}
?>