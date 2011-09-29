<?php
	 
	include_once("../../../mainfile.php");
	 
	getRandomProject();
	 
	function getRandomProject()
	{
		global $icmsDB;
		$limit = 500;
		$sql = "SELECT unix_group_name FROM " .$icmsDB->prefix("xf_groups")
		." WHERE status='A'" ." AND is_public=1" ." ORDER BY register_time DESC";
		$result = $icmsDB->query($sql, $limit);
		$rows = $icmsDB->getRowsNum($result);
		 
		$rand = rand(1, $rows);
		 
		for($count = 1; $count <= $rand; $count++)
		{
			 
			$row = $icmsDB->fetchArray($result);
			if ($count == $rand)
			{
				redirect_header(ICMS_URL."/modules/xfmod/project/?".$row['unix_group_name'], 0);
				exit();
			}
		}
	}
	 
?>