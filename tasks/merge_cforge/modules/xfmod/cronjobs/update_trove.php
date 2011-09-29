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
	/*
	 
	Rebuild the trove_agg table, which saves us
	from doing really expensive queries in trove
	each time of the trove map is viewed
	 
	*/
	 
	db_query("DELETE FROM ".$dbprefix."_xf_trove_agg;");
	 
	$sql = "INSERT INTO ".$dbprefix."_xf_trove_agg(trove_cat_id,group_id,group_name,unix_group_name,status,register_time,short_description,percentile,ranking) " ."SELECT tgl.trove_cat_id,g.group_id,g.group_name,g.unix_group_name,g.status,g.register_time," ."g.short_description " .",pwm.percentile,pwm.ranking " ."FROM ".$dbprefix."_xf_groups g " ."LEFT JOIN ".$dbprefix."_xf_project_weekly_metric pwm USING(group_id)" .",".$dbprefix."_xf_trove_group_link tgl " ."WHERE tgl.group_id=g.group_id " ."AND(g.is_public=1) " ."AND(g.type=1) " ."AND(g.status='A') " ."ORDER BY trove_cat_id ASC" .",ranking ASC ";
	db_query($sql);
	echo db_error();
	 
	/*
	 
	Calculate the number of projects under each category
	 
	Do this by first running an aggregate query in the database,
	then putting that into two associative arrays.
	 
	Start at the top of the trove tree and recursively go down
	the tree, building a third associative array which contains
	the count of projects under each category
	 
	Then iterate through that third array and insert the results into the
	database inside of a transaction
	 
	*/
	 
	$cat_counts = array();
	$parent_list = array();
	 
	$sql = "SELECT tc.trove_cat_id,tc.parent,count(g.group_id) AS count " ."FROM ".$dbprefix."_xf_trove_cat tc " ."LEFT JOIN ".$dbprefix."_xf_trove_group_link tgl ON tc.trove_cat_id=tgl.trove_cat_id " ."LEFT JOIN ".$dbprefix."_xf_groups g ON g.group_id=tgl.group_id " ."WHERE(g.status='A' OR g.status IS NULL) " ."AND(g.type='1' OR g.status IS NULL) " ."AND(g.is_public='1' OR g.is_public IS NULL) " ."GROUP BY tc.trove_cat_id,tc.parent";
	$res = db_query($sql);
	$rows = db_numrows($res);
	 
	for($i = 0; $i < $rows; $i++)
	{
		 
		$cat_counts[db_result($res, $i, 'trove_cat_id')][0] = db_result($res, $i, 'parent');
		$cat_counts[db_result($res, $i, 'trove_cat_id')][1] = db_result($res, $i, 'count');
		 
		$parent_list[db_result($res, $i, 'parent')][] = db_result($res, $i, 'trove_cat_id');
		 
	}
	 
	$sum_totals = array();
	 
	function get_trove_sub_projects($cat_id)
	{
		global $cat_counts, $sum_totals, $parent_list;
		 
		//number of groups that were in this trove_cat
		$count = $cat_counts[$cat_id][1];
		 
		//number of children of this trove_cat
		$rows = count($parent_list[$cat_id]);
		 
		for($i = 0; $i < $rows; $i++)
		{
			$count += get_trove_sub_projects($parent_list[$cat_id][$i]);
		}
		$sum_totals["$cat_id"] = $count;
		return $count;
	}
	 
	//start the recursive function at the top of the trove tree
	$res2 = db_query("SELECT trove_cat_id FROM ".$dbprefix."_xf_trove_cat WHERE parent=0");
	 
	for($i = 0; $i < db_numrows($res2); $i++)
	{
		get_trove_sub_projects(db_result($res2, $i, 0));
	}
	db_query("DELETE FROM ".$dbprefix."_xf_trove_treesums");
	echo db_error();
	 
	while (list($k, $v) = each($sum_totals))
	{
		$res = db_query("INSERT INTO ".$dbprefix."_xf_trove_treesums(trove_cat_id,subprojects) VALUES($k,$v)");
		if (!$res)
		{
			echo db_error();
		}
		 
	}
	 
	if (db_error())
	{
		echo "Error: ".db_error();
	}
	 
	include "footer.php";
?>