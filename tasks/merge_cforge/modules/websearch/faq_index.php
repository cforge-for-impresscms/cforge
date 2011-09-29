<?php
	include("../../mainfile.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	 
	echo "<meta name='Robots' content='NOINDEX,FOLLOW'>";
	 
	$query = "SELECT contents_id, category_id FROM ".$icmsDB->prefix("xoopsfaq_contents")." WHERE contents_visible=1";
	$result = $icmsDB->query($query);
	while ($row = $icmsDB->fetchArray($result))
	{
		$url = ICMS_URL."/modules/xoopsfaq/?cat_id=".$row['category_id']."&contents_id=".$row['contents_id'];
		echo "<a href='$url'>$url</a><br>";
	}
	 
	$path = ICMS_ROOT_PATH."/modules/xfmod/help";
	$dir = @opendir($path);
	while ($file = readdir($dir))
	{
		if (preg_match("/php/", $file))
		{
			$url = ICMS_URL."/modules/xfmod/help/".$file;
			echo "<a href='$url'>$url</a><br/>";
		}
	}
?>