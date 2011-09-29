#!/usr/bin/php -q
<?php
	 
	include "db.php";
	include "header.php";
	/*******************************************************************************
	*                                                                             *
	*                                                                             *
	*                                                                             *
	*                                                                             *
	*                                                                             *
	*                                                                             *
	*                                                                             *
	******************************************************************************/
	/**
	*  Update artifact count
	*/
	$rel = db_query("DELETE FROM ".$dbprefix."_xf_artifact_counts_agg;");
	echo db_error();
	 
	$rel = db_query("SELECT agl.group_artifact_id " ."FROM ".$dbprefix."_xf_artifact_group_list agl " //               ."LEFT JOIN ".$dbprefix."_xf_artifact a USING(group_artifact_id) "
	."GROUP BY agl.group_artifact_id;");
	 
	while ($rel_arr = db_fetch_array($rel))
	{
		$count = db_result(db_query("SELECT COUNT(*) AS count FROM ".$dbprefix."_xf_artifact a1 WHERE status_id<>3 AND a1.group_artifact_id=".$rel_arr['group_artifact_id']), 0, 'count');
		$opencount = db_result(db_query("SELECT COUNT(*) AS count FROM ".$dbprefix."_xf_artifact a1 WHERE status_id=1 AND a1.group_artifact_id=".$rel_arr['group_artifact_id']), 0, 'count');
		 
		$in = db_query("INSERT INTO ".$dbprefix."_xf_artifact_counts_agg VALUES('".$rel_arr['group_artifact_id']."','$count','$opencount')");
		echo db_error();
	}
	 
	db_query("VACUUM ANALYZE ".$dbprefix."_xf_artifact_counts_agg;");
	 
	include "footer.php";
?>