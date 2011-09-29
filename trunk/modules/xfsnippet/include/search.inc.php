<?php

function snippet_search($queryarray, $andor, $limit, $offset, $userid=0){
	global $xoopsDB;
	$sql = "SELECT  ver.snippet_id, ver.date, ver.submitted_by, snip.description"
			." FROM ".$xoopsDB->prefix("xf_snippet")." AS snip"
			.", ".$xoopsDB->prefix("xf_snippet_version")." AS ver"
			." WHERE snip.snippet_id=ver.snippet_id"
			." AND ver.submitted_by=$userid";

	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((snip.description LIKE '%$queryarray[0]%' OR ver.code LIKE '%$queryarray[0]%')";
		for($i=1; $i<$count; $i++){
			$sql .= " $andor ";
			$sql .= "(snip.description LIKE '%$queryarray[$i]%' OR ver.code LIKE '%$queryarray[$i]%')";
		}
		$sql .= ")";
	}
	$sql .= " GROUP BY snip.snippet_id ";
	
//	echo $sql;
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
 	while($myrow = $xoopsDB->fetchArray($result)){
		$ret[$i]['link'] = "detail.php?type=snippet&id=".$myrow['snippet_id']."";
		$ret[$i]['title'] = $myrow['description'];
		$ret[$i]['time'] = $myrow['date'];
		$ret[$i]['uid'] = $myrow['submitted_by'];
		$i++;
	}
	return $ret;
}
?>