<?php
	 
	function xfmod_public_search($queryarray, $andor, $limit, $offset, $userid = 0)
	{
		global $icmsDB;
		$sql = "SELECT t.group_id,t.unix_group_name,t.group_name,t.short_description,t.register_time ";
		 
		if ($userid != 0)
		{
			$sql .= "FROM ".$icmsDB->prefix("xf_groups")." t";
			$sql .= ",".$icmsDB->prefix("xf_user_group")." ug ";
		}
		else
		{
			$sql .= "FROM ".$icmsDB->prefix("xf_trove_agg")." t";
		}
		 
		$sql .= " WHERE status='A'";
		 
		if ($userid != 0)
		{
			$sql .= " AND ug.user_id=".$userid." AND t.group_id=ug.group_id ";
		}
		 
		// because count() returns 1 even if a supplied variable
		// is not an array, we must check if $querryarray is really an array
		if (is_array($queryarray) && $count = count($queryarray))
		{
			$sql .= " AND((t.group_name LIKE '%$queryarray[0]%' OR t.short_description LIKE '%$queryarray[0]%' OR t.unix_group_name LIKE '%$queryarray[0]%')";
			for($i = 1; $i < $count; $i++)
			{
				$sql .= " $andor ";
				$sql .= "(t.group_name LIKE '%$queryarray[$i]%' OR t.short_description LIKE '%$queryarray[$i]%' OR t.unix_group_name LIKE '%$queryarray[0]%')";
			}
			$sql .= ")";
		}
		$sql .= " GROUP BY t.group_id ";
		 
		if ($userid == 0)
		{
			$sql .= "ORDER BY t.percentile DESC";
		}
		// echo $sql;
		$result = $icmsDB->query($sql, $limit, $offset);
		$ret = array();
		$i = 0;
		while ($myrow = $icmsDB->fetchArray($result))
		{
			$ret[$i]['link'] = "index.php?op=search&unixname=".$myrow['unix_group_name']."";
			$ret[$i]['title'] = $myrow['group_name'];
			$ret[$i]['time'] = $myrow['register_time'];
			$i++;
		}
		return $ret;
	}
?>